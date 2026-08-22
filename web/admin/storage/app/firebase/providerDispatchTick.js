const admin = require('firebase-admin');
const serviceAccount = require('./credentials.json');

if (!admin.apps.length) {
    admin.initializeApp({
        credential: admin.credential.cert(serviceAccount),
    });
}

const db = admin.firestore();
const OFFER_SECONDS = 25;
const JOB_MINUTES = 10;
const ORDER_PLACED = 'Order Placed';

function toDate(value) {
    if (!value) return null;
    if (typeof value.toDate === 'function') return value.toDate();
    if (value instanceof Date) return value;
    return null;
}

function haversineKm(lat1, lon1, lat2, lon2) {
    const nums = [lat1, lon1, lat2, lon2].map(Number);
    if (nums.some((n) => !Number.isFinite(n))) return null;
    if (Math.abs(nums[0]) < 0.2 && Math.abs(nums[1]) < 0.2) return null;
    if (Math.abs(nums[2]) < 0.2 && Math.abs(nums[3]) < 0.2) return null;
    const R = 6371.0088;
    const toRad = (d) => (d * Math.PI) / 180;
    const dLat = toRad(nums[2] - nums[0]);
    const dLon = toRad(nums[3] - nums[1]);
    const a = Math.sin(dLat / 2) ** 2 + Math.cos(toRad(nums[0])) * Math.cos(toRad(nums[2])) * Math.sin(dLon / 2) ** 2;
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}

function coordsFrom(user, service) {
    const loc = user && user.location && typeof user.location === 'object' ? user.location : {};
    const lat = Number(service && service.latitude != null ? service.latitude : (user && (user.latitude != null ? user.latitude : loc.latitude)));
    const lng = Number(service && service.longitude != null ? service.longitude : (user && (user.longitude != null ? user.longitude : loc.longitude)));
    return { lat, lng };
}

function customerCoords(order) {
    const address = order.address || {};
    const loc = address.location || {};
    if (loc.latitude != null && loc.longitude != null) {
        return { lat: Number(loc.latitude), lng: Number(loc.longitude) };
    }
    if (typeof loc.latitude === 'number') {
        return { lat: loc.latitude, lng: loc.longitude };
    }
    return { lat: Number(address.latitude), lng: Number(address.longitude) };
}

function assignedAuthor(order) {
    return (((order.provider || {}).author) || '').toString().trim();
}

async function sendFcm(token, title, body, data) {
    if (!token) return;
    try {
        await admin.messaging().send({
            token: token,
            notification: { title: title, body: body },
            data: data,
            android: {
                priority: 'high',
                notification: {
                    channelId: data.type === 'provider_dispatch_offer' ? 'provider_dispatch_offer' : 'arrow-provider',
                    sound: 'default',
                },
            },
        });
    } catch (error) {
        console.log('FCM failed', error.message || error);
    }
}

async function listCandidates(categoryId, excluded) {
    let query = db.collection('providers_services').where('publish', '==', true);
    if (categoryId) {
        query = query.where('categoryId', '==', categoryId);
    }
    const snap = await query.get();
    const byAuthor = {};
    snap.forEach((doc) => {
        const data = doc.data();
        const author = (data.author || '').toString();
        if (author && !byAuthor[author] && !excluded.has(author)) {
            byAuthor[author] = data;
        }
    });
    const candidates = [];
    const uids = Object.keys(byAuthor);
    for (let i = 0; i < uids.length; i += 10) {
        const chunk = uids.slice(i, i + 10);
        const users = await Promise.all(chunk.map((id) => db.collection('users').doc(id).get()));
        users.forEach((userSnap, idx) => {
            if (!userSnap.exists) return;
            const user = userSnap.data() || {};
            if (user.online !== true) return;
            const service = byAuthor[chunk[idx]] || {};
            const coords = coordsFrom(user, service);
            candidates.push({
                uid: chunk[idx],
                lat: coords.lat,
                lng: coords.lng,
                fcmToken: user.fcmToken || '',
            });
        });
    }
    return candidates;
}

function pickNext(candidates, origin, radiusKm) {
    const scored = candidates
        .map((c) => ({ ...c, km: haversineKm(origin.lat, origin.lng, c.lat, c.lng) }))
        .filter((c) => c.km == null || !(radiusKm > 0) || c.km <= radiusKm)
        .sort((a, b) => {
            if (a.km == null && b.km == null) return 0;
            if (a.km == null) return 1;
            if (b.km == null) return -1;
            return a.km - b.km;
        });
    return scored[0] || null;
}

async function tick() {
    const now = new Date();
    const snap = await db.collection('provider_orders').where('dispatchMode', '==', 'broadcast').get();
    for (const doc of snap.docs) {
        const order = doc.data() || {};
        if ((order.status || '') !== ORDER_PLACED) continue;
        if (assignedAuthor(order)) continue;
        const createdAt = toDate(order.createdAt) || now;
        const expiresAt = toDate(order.dispatchExpiresAt) || new Date(createdAt.getTime() + JOB_MINUTES * 60 * 1000);
        if (now >= expiresAt) {
            await doc.ref.update({
                status: 'Order Cancelled',
                reason: 'Nenhum prestador disponível',
                cancelReason: 'no_provider',
            });
            const customerId = (order.authorID || (order.author && order.author.id) || '').toString();
            if (customerId) {
                const customer = await db.collection('users').doc(customerId).get();
                const token = customer.exists ? (customer.data() || {}).fcmToken : '';
                await sendFcm(token, 'Nenhum prestador disponível', 'Ninguém aceitou seu pedido em 10 minutos.', {
                    type: 'provider_order',
                    orderId: (order.id || doc.id).toString(),
                });
            }
            console.log('Closed broadcast ' + doc.id + ' (no_provider)');
            continue;
        }

        const offeredTo = (order.dispatchOfferedTo || '').toString().trim();
        const offerExpires = toDate(order.dispatchOfferExpiresAt);
        const offerStale = !offeredTo || !offerExpires || now >= offerExpires;
        if (!offerStale) continue;

        const rejected = new Set([].concat(order.rejectedBy || [], order.offeredTo || []).map((e) => e.toString()));
        if (offeredTo) rejected.add(offeredTo);
        const categoryId = (order.requestedCategoryId || (order.provider && order.provider.categoryId) || '').toString();
        const candidates = await listCandidates(categoryId, rejected);
        const next = pickNext(candidates, customerCoords(order), Number(order.radiusKm || 25));
        const patch = {};
        if (offeredTo) {
            patch.rejectedBy = admin.firestore.FieldValue.arrayUnion(offeredTo);
            patch.offeredTo = admin.firestore.FieldValue.arrayUnion(offeredTo);
        }
        if (!next) {
            patch.dispatchOfferedTo = '';
            patch.dispatchOfferExpiresAt = admin.firestore.FieldValue.delete();
            await doc.ref.update(patch);
            continue;
        }
        patch.dispatchOfferedTo = next.uid;
        patch.dispatchOfferExpiresAt = admin.firestore.Timestamp.fromDate(new Date(now.getTime() + OFFER_SECONDS * 1000));
        patch.offeredTo = admin.firestore.FieldValue.arrayUnion(next.uid, ...(offeredTo ? [offeredTo] : []));
        await doc.ref.update(patch);
        const title = ((order.provider && order.provider.title) || 'Serviço').toString();
        await sendFcm(next.fcmToken, 'Novo pedido próximo', title, {
            type: 'provider_dispatch_offer',
            orderId: (order.id || doc.id).toString(),
        });
        console.log('Offered ' + doc.id + ' to ' + next.uid);
    }
}

tick()
    .then(() => process.exit(0))
    .catch((error) => {
        console.error(error);
        process.exit(1);
    });
