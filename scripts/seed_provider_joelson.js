/**
 * Seed de demonstração para o prestador joelson@joelson.com.
 *
 * Só grava docs deste prestador (author/providerId = uid) e IDs prefixados
 * com demo_joelson_. Não apaga pedidos de outros usuários.
 *
 * Uso:
 *   cd firebase/import-export && npm i
 *   node ../../scripts/seed_provider_joelson.js
 *   node ../../scripts/seed_provider_joelson.js --dry-run
 *
 * Credenciais: GOOGLE_APPLICATION_CREDENTIALS, firebase/import-export/credentials.json
 * ou j-arrow-firebase-adminsdk-*.json na raiz do repo (nunca commitar).
 */
const { initFirestore, parseArgs } = require('./lib/firestore-admin');

const PROVIDER_EMAIL = 'joelson@joelson.com';
const CUSTOMER_EMAIL = 'cliente.demo.joelson@arrow.test';
const PREFIX = 'demo_joelson_';
const FALLBACK_LAT = -23.5505;
const FALLBACK_LNG = -46.6333;
const PLACES = {
  rio: {
    address: 'Estrada do Camboatá, 2500 — Bangu, Rio de Janeiro — RJ',
    locality: 'Rio de Janeiro',
  },
  sp: {
    address: 'Av. Paulista, 1578 — Bela Vista, São Paulo — SP',
    locality: 'São Paulo',
  },
};
const FALLBACK_ADDRESS = PLACES.sp.address;

function cityFromCoords(lat, lng) {
  if (!Number.isFinite(lat) || !Number.isFinite(lng)) return null;
  if (lat >= -23.15 && lat <= -22.6 && lng >= -43.8 && lng <= -43.05) return 'rio';
  if (lat >= -23.9 && lat <= -23.3 && lng >= -46.95 && lng <= -46.3) return 'sp';
  return null;
}

function cityFromAddress(text) {
  const s = String(text || '');
  if (!s) return null;
  const rio = /rio de janeiro/i.test(s);
  const sp = /s[aã]o paulo/i.test(s);
  if (rio && !sp) return 'rio';
  if (sp && !rio) return 'sp';
  return null;
}

/** Endereço e localidade da mesma cidade das coords (não mistura SP com pin no Rio). */
function placeForCoords(lat, lng, existingAddress) {
  const city = cityFromCoords(lat, lng) || cityFromAddress(existingAddress) || 'sp';
  const catalog = PLACES[city];
  const existing = String(existingAddress || '').trim();
  return {
    address: cityFromAddress(existing) === city && existing ? existing : catalog.address,
    locality: catalog.locality,
  };
}

function printHelp() {
  console.log(`Usage: node scripts/seed_provider_joelson.js [--dry-run]

Cria serviços publicados, pedidos, carteira, trabalhador e avaliação
para o prestador ${PROVIDER_EMAIL}.
`);
}

function encodeGeohash(latitude, longitude, precision) {
  precision = precision || 9;
  const BASE32 = '0123456789bcdefghjkmnpqrstuvwxyz';
  let idx = 0;
  let bit = 0;
  let even = true;
  let geohash = '';
  let latMin = -90;
  let latMax = 90;
  let lonMin = -180;
  let lonMax = 180;
  while (geohash.length < precision) {
    if (even) {
      const mid = (lonMin + lonMax) / 2;
      if (longitude > mid) {
        idx = idx * 2 + 1;
        lonMin = mid;
      } else {
        idx = idx * 2;
        lonMax = mid;
      }
    } else {
      const mid = (latMin + latMax) / 2;
      if (latitude > mid) {
        idx = idx * 2 + 1;
        latMin = mid;
      } else {
        idx = idx * 2;
        latMax = mid;
      }
    }
    even = !even;
    if (++bit === 5) {
      geohash += BASE32.charAt(idx);
      bit = 0;
      idx = 0;
    }
  }
  return geohash;
}

function num(value, fallback) {
  const n = Number(value);
  return Number.isFinite(n) ? n : fallback;
}

function pickCategory(categories, needles, used) {
  const lower = (title) => String(title || '').toLowerCase();
  const match = categories.find((c) => {
    if (used.has(c.id)) return false;
    const title = lower(c.title);
    return needles.some((n) => title.includes(n));
  });
  if (match) {
    used.add(match.id);
    return match;
  }
  const next = categories.find((c) => !used.has(c.id));
  if (next) used.add(next.id);
  return next || categories[0] || { id: '', title: 'Serviço' };
}

(async () => {
  const { has } = parseArgs();
  if (has('help') || has('h')) {
    printHelp();
    process.exit(0);
  }
  const dryRun = has('dry-run');
  const { admin, db, credPath } = initFirestore();
  const FieldValue = admin.firestore.FieldValue;
  const Timestamp = admin.firestore.Timestamp;
  const GeoPoint = admin.firestore.GeoPoint;

  console.log('Credentials:', credPath);
  console.log(dryRun ? 'Dry-run: não grava.' : 'Gravando seed de demonstração...');

  const providerSnap = await db.collection('users').where('email', '==', PROVIDER_EMAIL).limit(5).get();
  if (providerSnap.empty) {
    throw new Error(`Usuário ${PROVIDER_EMAIL} não encontrado em users.`);
  }
  const providerDoc = providerSnap.docs.find((d) => (d.data().role || '') === 'provider') || providerSnap.docs[0];
  const provider = providerDoc.data() || {};
  const uid = provider.id || provider.userID || providerDoc.id;
  if (!uid) throw new Error('Prestador sem id.');
  if (provider.role && provider.role !== 'provider') {
    console.warn('Aviso: role atual é', provider.role, '— o seed assume prestador.');
  }
  console.log('Provider uid:', uid, provider.firstName, provider.lastName);

  const sectionsSnap = await db.collection('sections').where('serviceTypeFlag', '==', 'ondemand-service').get();
  let section =
    sectionsSnap.docs
      .map((d) => d.data())
      .find((s) => s && s.isActive !== false && (s.id === provider.section_id || s.id === provider.sectionId)) ||
    sectionsSnap.docs.map((d) => d.data()).find((s) => s && s.isActive !== false) ||
    sectionsSnap.docs[0]?.data();
  if (!section || !section.id) {
    throw new Error('Nenhuma seção ondemand-service encontrada. Crie em Admin → Seções.');
  }
  const sectionId = section.id;
  console.log('Section:', sectionId, section.name);

  const catsSnap = await db.collection('provider_categories').where('sectionId', '==', sectionId).where('publish', '==', true).get();
  const parents = catsSnap.docs
    .map((d) => d.data())
    .filter((c) => c && c.id && !c.parentCategoryId);
  if (parents.length === 0) {
    throw new Error(`Nenhuma categoria publicada em provider_categories para sectionId=${sectionId}.`);
  }
  const usedCats = new Set();
  const catLimpeza = pickCategory(parents, ['limp', 'faxina', 'casa'], usedCats);
  const catEletrica = pickCategory(parents, ['elétr', 'eletr', 'eletro'], usedCats);
  const catHidraulica = pickCategory(parents, ['hidr', 'encan', 'água', 'agua', 'cano'], usedCats);
  console.log('Categories:', catLimpeza.title, catEletrica.title, catHidraulica.title);

  const lat = num(provider.latitude ?? provider.location?.latitude, FALLBACK_LAT);
  const lng = num(provider.longitude ?? provider.location?.longitude, FALLBACK_LNG);
  const hash = encodeGeohash(lat, lng);
  const point = new GeoPoint(lat, lng);
  const destLat = lat + 0.004;
  const destLng = lng + 0.003;
  const existingAddr = provider.shippingAddress?.[0]?.address || provider.address || '';
  const place = placeForCoords(lat, lng, existingAddr);
  const address = place.address;
  const authorName = `${provider.firstName || ''} ${provider.lastName || ''}`.trim() || 'Joelson Justino';
  console.log('Place:', place.locality, address, lat, lng);

  const customerAddr = {
    id: `${PREFIX}addr`,
    address,
    addressAs: 'Casa',
    locality: place.locality,
    landmark: '',
    isDefault: true,
    latitude: destLat,
    longitude: destLng,
    location: { latitude: destLat, longitude: destLng },
  };

  let customerSnap = await db.collection('users').where('email', '==', CUSTOMER_EMAIL).limit(1).get();
  let customerId = customerSnap.empty ? `${PREFIX}customer` : customerSnap.docs[0].id;
  let customer = customerSnap.empty ? null : customerSnap.docs[0].data();
  let createDummyCustomer = false;
  if (!customer) {
    const existingCustomers = await db.collection('users').where('role', '==', 'customer').limit(15).get();
    const testish = existingCustomers.docs.find((d) => {
      const e = String(d.data().email || '').toLowerCase();
      return e.includes('test') || e.includes('demo') || e.includes('seed');
    });
    if (testish) {
      customerId = testish.data().id || testish.id;
      customer = testish.data();
      console.log('Cliente de teste existente:', customerId, customer.email);
    }
  }
  if (!customer) {
    createDummyCustomer = true;
    customer = {
      id: customerId,
      userID: customerId,
      email: CUSTOMER_EMAIL,
      firstName: 'Cliente',
      lastName: 'Demo Joelson',
      role: 'customer',
      active: true,
      isActive: true,
      wallet_amount: 80,
      phoneNumber: '+5511999990001',
      countryCode: '+55',
      countryISOCode: 'BR',
      createdAt: FieldValue.serverTimestamp(),
      location: { latitude: destLat, longitude: destLng },
      latitude: destLat,
      longitude: destLng,
      address,
      shippingAddress: [customerAddr],
    };
    console.log('Criará cliente dummy:', customerId, CUSTOMER_EMAIL);
  }

  const customerAuthor = {
    id: customer.id || customerId,
    userID: customer.id || customerId,
    firstName: customer.firstName || 'Cliente',
    lastName: customer.lastName || 'Demo',
    email: customer.email || CUSTOMER_EMAIL,
    phoneNumber: customer.phoneNumber || '',
    profilePictureURL: customer.profilePictureURL || '',
    role: 'customer',
    active: true,
  };

  const serviceBase = (id, title, price, priceUnit, category, reviewsCount, reviewsSum) => ({
    id,
    author: uid,
    authorName,
    authorProfilePic: provider.profilePictureURL || '',
    phoneNumber: provider.phoneNumber || '',
    title,
    description:
      priceUnit === 'Hourly'
        ? `Serviço de ${title.toLowerCase()} cobrado por hora. Atendimento residencial e comercial.`
        : `Serviço de ${title.toLowerCase()} com preço fechado, material básico incluso.`,
    price: String(price),
    disPrice: '0',
    priceUnit,
    publish: true,
    photos: [],
    address,
    sectionId,
    categoryId: category.id,
    subCategoryId: '',
    reviewsCount,
    reviewsSum,
    latitude: lat,
    longitude: lng,
    days: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
    startTime: '08:00',
    endTime: '18:00',
    createdAt: FieldValue.serverTimestamp(),
    coordinates: point,
    g: { geohash: hash, geopoint: point },
    subscription_plan: provider.subscription_plan || null,
    subscriptionPlanId: provider.subscriptionPlanId || null,
    subscriptionExpiryDate: provider.subscriptionExpiryDate || null,
    subscriptionTotalOrders: provider.subscriptionTotalOrders || '-1',
  });

  const services = [
    serviceBase(`${PREFIX}svc_limpeza`, 'Limpeza residencial', '85', 'Hourly', catLimpeza, 6, 28),
    serviceBase(`${PREFIX}svc_eletrica`, 'Instalação elétrica', '220', 'Fixed', catEletrica, 4, 18),
    serviceBase(`${PREFIX}svc_hidraulica`, 'Reparo hidráulico', '160', 'Fixed', catHidraulica, 3, 14),
  ];

  const workerId = `${PREFIX}worker_1`;
  const worker = {
    id: workerId,
    providerId: uid,
    firstName: 'Marcos',
    lastName: 'Oliveira',
    email: 'marcos.equipe.joelson@arrow.test',
    phoneNumber: '+5511988880002',
    salary: '2200',
    address,
    profilePictureURL: '',
    fcmToken: '',
    active: true,
    online: true,
    reviewsCount: 5,
    reviewsSum: 23,
    createdAt: FieldValue.serverTimestamp(),
    latitude: lat,
    longitude: lng,
    coordinates: point,
  };

  const now = Date.now();
  const ts = (offsetMs) => Timestamp.fromMillis(now + offsetMs);
  const hour = 60 * 60 * 1000;
  const limpeza = services[0];
  const eletrica = services[1];

  const orderDoc = (id, extra) => ({
    id,
    authorID: customerAuthor.id,
    author: customerAuthor,
    sectionId,
    address: customerAddr,
    createdAt: ts(-2 * hour),
    notes: extra.notes || '',
    quantity: extra.quantity ?? 1,
    payment_method: extra.payment_method || 'cod',
    status: extra.status,
    provider: extra.provider,
    scheduleDateTime: extra.scheduleDateTime || ts(2 * hour),
    newScheduleDateTime: extra.newScheduleDateTime || extra.scheduleDateTime || ts(2 * hour),
    startTime: extra.startTime || null,
    endTime: extra.endTime || null,
    discount: '0',
    discountType: '',
    discountLabel: '0',
    couponCode: '',
    reason: extra.reason || '',
    otp: extra.otp || '4821',
    extraCharges: extra.extraCharges || '0',
    extraChargesDescription: extra.extraChargesDescription || '',
    paymentStatus: extra.paymentStatus ?? false,
    extraPaymentStatus: true,
    workerId: extra.workerId || '',
    adminCommission: '0',
    adminCommissionType: 'fixed',
    platformFee: '0',
    taxSetting: [],
    invoices: [],
    dispatchMode: extra.dispatchMode || 'direct',
    requestedCategoryId: extra.requestedCategoryId || extra.provider.categoryId || '',
    radiusKm: extra.radiusKm || 25,
    rejectedBy: extra.rejectedBy || [],
  });

  const broadcastProvider = { ...limpeza, author: '', authorName: '', phoneNumber: '' };
  const orders = [
    orderDoc(`${PREFIX}order_placed`, {
      status: 'Order Placed',
      provider: broadcastProvider,
      dispatchMode: 'broadcast',
      requestedCategoryId: limpeza.categoryId,
      notes: 'Chamado próximo (broadcast) — aceitar no app Prestador.',
      scheduleDateTime: ts(20 * 60 * 1000),
      createdAt: ts(-10 * 60 * 1000),
    }),
    orderDoc(`${PREFIX}order_accepted`, {
      status: 'Order Accepted',
      provider: limpeza,
      notes: 'Agendado para amanhã de manhã.',
      scheduleDateTime: ts(20 * hour),
    }),
    orderDoc(`${PREFIX}order_assigned`, {
      status: 'Order Assigned',
      provider: eletrica,
      workerId,
      notes: 'Equipe atribuída para instalação.',
      scheduleDateTime: ts(4 * hour),
    }),
    orderDoc(`${PREFIX}order_ongoing`, {
      status: 'Order Ongoing',
      provider: limpeza,
      workerId,
      quantity: 2,
      notes: 'Serviço por hora em andamento.',
      startTime: ts(-45 * 60 * 1000),
      scheduleDateTime: ts(-hour),
    }),
    orderDoc(`${PREFIX}order_completed`, {
      status: 'Order Completed',
      provider: eletrica,
      workerId,
      payment_method: 'cod',
      paymentStatus: true,
      notes: 'Concluído e pago.',
      startTime: ts(-26 * hour),
      endTime: ts(-24 * hour),
      scheduleDateTime: ts(-26 * hour),
    }),
    orderDoc(`${PREFIX}order_cancelled`, {
      status: 'Order Cancelled',
      provider: limpeza,
      reason: 'Cliente pediu cancelamento de teste.',
      notes: 'Cancelado para demonstrar a aba.',
      scheduleDateTime: ts(-5 * hour),
    }),
  ];

  const walletTx = [
    {
      id: `${PREFIX}wallet_completed`,
      user_id: uid,
      amount: 220,
      date: ts(-24 * hour),
      payment_method: 'wallet',
      transactionUser: 'provider',
      isTopUp: true,
      order_id: `${PREFIX}order_completed`,
      note: 'On-demand booking credited',
      payment_status: 'success',
      serviceType: 'ondemand-service',
    },
    {
      id: `${PREFIX}wallet_topup`,
      user_id: uid,
      amount: 30,
      date: ts(-12 * hour),
      payment_method: 'wallet',
      transactionUser: 'provider',
      isTopUp: true,
      order_id: `${PREFIX}order_completed`,
      note: 'Extra Charge Amount Credited',
      payment_status: 'success',
      serviceType: 'ondemand-service',
    },
  ];

  const review = {
    Id: `${PREFIX}review_1`,
    id: `${PREFIX}review_1`,
    comment: 'Serviço pontual e caprichado. Recomendo.',
    rating: 5,
    orderid: `${PREFIX}order_completed`,
    VendorId: uid,
    productId: eletrica.id,
    CustomerId: customerAuthor.id,
    uname: `${customerAuthor.firstName} ${customerAuthor.lastName}`.trim(),
    profile: customerAuthor.profilePictureURL || '',
    photos: [],
    reviewAttributes: {},
    createdAt: ts(-23 * hour),
  };

  const userPatch = {
    role: 'provider',
    active: true,
    isActive: true,
    online: true,
    isDocumentVerify: true,
    wallet_amount: 250,
    reviewsCount: 8,
    reviewsSum: 36,
    section_id: provider.section_id || provider.sectionId || sectionId,
    sectionId: provider.sectionId || provider.section_id || sectionId,
    latitude: lat,
    longitude: lng,
    location: { latitude: lat, longitude: lng },
    g: { geohash: hash, geopoint: point },
  };

  const written = {
    user: uid,
    services: services.map((s) => s.id),
    worker: workerId,
    customer: customerId,
    orders: orders.map((o) => o.id),
    wallet: walletTx.map((t) => t.id),
    review: review.Id,
  };

  if (dryRun) {
    console.log('Dry-run payload ids:', JSON.stringify(written, null, 2));
    process.exit(0);
  }

  const batch = db.batch();
  batch.set(db.collection('users').doc(uid), userPatch, { merge: true });
  if (createDummyCustomer) {
    batch.set(db.collection('users').doc(customerId), customer, { merge: true });
  }
  services.forEach((svc) => batch.set(db.collection('providers_services').doc(svc.id), svc, { merge: true }));
  batch.set(db.collection('providers_workers').doc(workerId), worker, { merge: true });
  orders.forEach((ord) => {
    const payload = { ...ord };
    if (ord.id.endsWith('placed')) payload.createdAt = FieldValue.serverTimestamp();
    batch.set(db.collection('provider_orders').doc(ord.id), payload, { merge: true });
  });
  walletTx.forEach((tx) => batch.set(db.collection('wallet').doc(tx.id), tx, { merge: true }));
  batch.set(db.collection('items_review').doc(review.Id), review, { merge: true });
  await batch.commit();

  console.log('Seed ok.');
  console.log(JSON.stringify(written, null, 2));
  process.exit(0);
})().catch((err) => {
  console.error(err);
  process.exit(1);
});
