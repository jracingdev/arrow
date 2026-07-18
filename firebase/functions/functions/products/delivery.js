const { onDocumentWritten } = require("firebase-functions/v2/firestore");
const { defineString } = require('firebase-functions/params');
const { getFirestore } = require("firebase-admin/firestore");
const admin = require("firebase-admin");

// Initialize Admin SDK once
if (admin.apps.length === 0) {
    admin.initializeApp();
}

/**
 * Get the correct Firestore instance based on the DATABASE parameter
 */
const getDb = () => getFirestore();

exports.dispatch = onDocumentWritten({
    document: "vendor_orders/{orderID}"
}, async (event) => {

    const firestore = getDb();
    
    const orderData = event.data.after.data();
    const beforeData = event.data.before.data();
    const orderId = event.params.orderID;
    const documentRef = event.data.after.ref;

    // 1. Guard Clauses & Skip Logic
    if (!orderData) {
        console.log("No order data found for ID:", orderId);
        return null;
    }

    if (beforeData && orderData) {
        const keysChanged = Object.keys(orderData).filter(
            key => JSON.stringify(orderData[key]) !== JSON.stringify(beforeData[key])
        );
        if (keysChanged.length === 1 && keysChanged.includes('orderAutoCancelAt')) {
            console.log("orderAutoCancelAt update detected, skipping dispatch logic.");
            return null;
        }
    }

    if (orderData.status === "Order Cancelled") {
        console.log(`Order #${orderId} was cancelled.`);
        return null;
    }

    if (orderData.status === "Order Placed") {
        console.log(`Order #${orderId} was sent to vendor for approval.`);
        return null;
    }

    if (orderData.takeAway === true) {
        console.log(`Order #${orderId} was sent as takeAway to vendor for approval.`);
        return null;
    }

    // 2. Dispatch Logic
    if (orderData.status === "Order Accepted" || orderData.status === "Driver Rejected") {
        console.log("Finding a driver for order #" + orderId + " ---");

        const rejectedByDrivers = orderData.rejectedByDrivers || [];
        const driverNearByData = await getDriverNearByData(firestore);
        
        let minimumDepositToRideAccept = 0;
        let orderAcceptRejectDuration = 0;
        let orderAutoCancelDuration = 0;
        let kDistanceRadiusForDispatch = 50;
        let singleOrderReceive = false;
        
        let zone_id = null;
        if (orderData.address?.location?.longitude && orderData.address?.location?.latitude) {
            zone_id = await getUserZoneId(firestore, orderData.address.location.longitude, orderData.address.location.latitude);
            console.log('Zone id by address:', zone_id);
        }
        
        if (driverNearByData !== undefined) {
            minimumDepositToRideAccept = parseInt(driverNearByData.minimumDepositToRideAccept || 0);
            orderAcceptRejectDuration = parseInt(driverNearByData.driverOrderAcceptRejectDuration || 0);
            orderAutoCancelDuration = parseInt(driverNearByData.orderAutoCancelDuration || 0);
            kDistanceRadiusForDispatch = parseInt(driverNearByData.driverRadios || 50);
            if (driverNearByData.distanceType === 'miles') {
                kDistanceRadiusForDispatch = Math.round(kDistanceRadiusForDispatch * 1.60934);
            }
            singleOrderReceive = Boolean(driverNearByData.singleOrderReceive);
        }

        console.log('Config: minDeposit:', minimumDepositToRideAccept, 'acceptDuration:', orderAcceptRejectDuration);

        const snapshot = await firestore.collection("users")
            .where('role', '==', "driver")
            .where('isActive', '==', true)
            .where('serviceTypes', 'array-contains', 'delivery-service')
            .where('wallet_amount', '>=', minimumDepositToRideAccept)
            .get();

        console.log(`Found ${snapshot.docs.length} drivers matching initial criteria.`);

        let found = false;

        for (const doc of snapshot.docs) {  
            if (found) break;

            const driver = doc.data();
            const driverId = doc.id;

            if (driver.vendorID || !driver.fcmToken) continue;

            // Section Check
            if (!driver.sectionIds?.includes(orderData.section_id)) {
                console.log(`Driver ${driver.id} skipped (section mismatch)`);
                continue;
            }

            // Check Zone
            if (driver.zoneId && zone_id !== null && driver.zoneId !== zone_id) {
                continue;
            }

            // Check Rejections and Proximity
            if (driver.location && !rejectedByDrivers.includes(driverId)) {
                const vendor = orderData.vendor;
                if (vendor) {
                    const distance = distanceRadius(driver.location.latitude, driver.location.longitude, vendor.latitude, vendor.longitude);
                    
                    // RESTORED LOGS
                    console.log(`Checking Driver: ${driver.email}`);
                    console.log(`Driver Location: lat ${driver.location.latitude} long ${driver.location.longitude}`);
                    console.log(`Vendor Location: lat ${vendor.latitude} long ${vendor.longitude}`);
                    console.log(`Calculated Distance: ${distance.toFixed(2)} (Limit: ${kDistanceRadiusForDispatch})`);

                    if (distance < kDistanceRadiusForDispatch) {
                        
                        if (singleOrderReceive === true) {
                            const hasPendingOrder = Array.isArray(driver.orderRequestData) && driver.orderRequestData.length > 0;
                            const hasAcceptedOrder = Array.isArray(driver.inProgressOrderID) && driver.inProgressOrderID.length > 0;
                            if (hasPendingOrder || hasAcceptedOrder) {
                                console.log(`Driver ${driver.email} is currently busy.`);
                                continue;
                            }
                        }
                        
                        found = true;

                        console.log(`Match Found: Driver ${driver.email} assigned to order #${orderId}`);

                        // Notify Driver
                        const timeLabel = Math.floor(orderAcceptRejectDuration / 60) + ":" + (orderAcceptRejectDuration % 60 || '00');
                        const message = {
                            notification: {
                                title: 'New order received',
                                body: 'You have a new order, please accept in ' + timeLabel + ' mins'
                            },
                            token: driver.fcmToken
                        };
                        admin.messaging().send(message).catch(e => console.log("FCM Error", e));

                        // Update Order Status
                        // eslint-disable-next-line no-await-in-loop
                        await documentRef.set({ status: "Driver Pending" }, { merge: true });

                        // Handle timeout logic
                        if (orderAcceptRejectDuration > 0) {
                            setTimeout(async () => { 
                                const snap = await firestore.collection("vendor_orders").doc(orderId).get();
                                const latestOrder = snap.data();

                                if (latestOrder && latestOrder.status === "Driver Pending") {
                                    console.log(`Timeout: Driver ${driver.email} failed to accept. Resetting order.`);
                                    const dSnap = await firestore.collection("users").doc(driverId).get();
                                    const dData = dSnap.data();
                                    
                                    if (dData?.orderRequestData) {
                                        const filteredRequests = dData.orderRequestData.filter(oid => oid !== orderId);
                                        await firestore.collection('users').doc(driverId).update({ orderRequestData: filteredRequests });
                                    }

                                    rejectedByDrivers.push(driverId);
                                    await firestore.collection('vendor_orders').doc(orderId).update({
                                        status: 'Order Accepted',
                                        rejectedByDrivers: rejectedByDrivers
                                    });
                                }
                            }, orderAcceptRejectDuration * 1000);
                        }

                        // Assign order to driver request list
                        let currentRequests = driver.orderRequestData || [];
                        if (!currentRequests.includes(orderId)) {
                            currentRequests.push(orderId);
                        }
                        // eslint-disable-next-line no-await-in-loop
                        await firestore.collection('users').doc(driverId).update({ orderRequestData: currentRequests });
                    }
                }
            }
        }

        if (!found) {
            const futureTime = new Date(Date.now() + orderAutoCancelDuration * 60 * 1000);
            await firestore.collection('vendor_orders').doc(orderId).update({
                orderAutoCancelAt: admin.firestore.Timestamp.fromDate(futureTime)
            });
            console.log("No driver found for order #" + orderId);
        }
    }

    if (orderData.status === "Driver Accepted") {
        await documentRef.set({ status: "Order Shipped" }, { merge: true });
        console.log("Order #" + orderId + " shipped");
    }

    return null;
});

// --- HELPER FUNCTIONS ---

const distanceRadius = (lat1, lon1, lat2, lon2) => {
    if (lat1 === lat2 && lon1 === lon2) return 0;
    const radlat1 = Math.PI * lat1/180;
    const radlat2 = Math.PI * lat2/180;
    const theta = lon1-lon2;
    const radtheta = Math.PI * theta/180;
    let dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
    if (dist > 1) dist = 1;
    dist = Math.acos(dist) * 180 / Math.PI * 60 * 1.1515 * 1.60934;
    return dist;
};

async function getDriverNearByData(firestore) {
    const snapshot = await firestore.collection("settings").doc('DriverNearBy').get();
    return snapshot.data();
}

async function getUserZoneId(firestore, address_lng, address_lat) {
    let zone_id = null;
    const snapshots = await firestore.collection('zone').where("publish", "==", true).get();
    
    for (const doc of snapshots.docs) {
        const zone = doc.data();
        const vertices_x = zone.area.map(p => p.longitude);
        const vertices_y = zone.area.map(p => p.latitude);
        
        if (is_in_polygon(vertices_x.length, vertices_x, vertices_y, address_lng, address_lat)) {
            zone_id = zone.id;
            break;
        }
    }
    return zone_id;
}

function is_in_polygon(nvert, vertx, verty, testx, testy) {
    let c = false;
    for (let i = 0, j = nvert - 1; i < nvert; j = i++) {
        if (((verty[i] > testy) !== (verty[j] > testy)) &&
            (testx < (vertx[j] - vertx[i]) * (testy - verty[i]) / (verty[j] - verty[i]) + vertx[i])) {
            c = !c;
        }
    }
    return c;
}