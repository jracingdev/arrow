const functions = require('firebase-functions');
const admin = require ("firebase-admin");
const firestore = admin.firestore();

/*
** Dispatch parcel orders to vendors and drivers
*/
exports.dispatch = functions.firestore
.document("parcel_orders/{orderID}")
.onWrite(async (change, context) => {
    const orderData = change.after.data();
    if (!orderData) {
        console.log("No order data");
        return;
    }

    if (orderData.status === "Order Cancelled") {
        console.log("Order #" + change.after.ref.id + " was cancelled.")
        return null
    }
    
    if (orderData.sendToDriver === undefined || orderData.sendToDriver === false) {
        console.log("Order #" + change.after.ref.id + " is sheduled.")
        return null
    }
    if (orderData.status === "Order Placed" || orderData.status === "Order Accepted" || orderData.status === "Driver Rejected") {
        // the vendor accepted the order, so we need to find an available driver
        console.log("Finding a driver for order #" + change.after.ref.id)

        const rejectedByDrivers = orderData.rejectedByDrivers ? orderData.rejectedByDrivers : []

        var orderId = change.after.ref.id;
        var driverNearByData = await getDriverNearByData();
        var minimumDepositToRideAccept = 0;
        var orderAcceptRejectDuration = 0;
        var kDistanceRadiusForDispatchInMiles = 50;
    
        if(driverNearByData !== undefined){
            if(driverNearByData.minimumDepositToRideAccept !== undefined){
                minimumDepositToRideAccept = parseInt(driverNearByData.minimumDepositToRideAccept);
            }
            if(driverNearByData.driverOrderAcceptRejectDuration !== undefined){
                 orderAcceptRejectDuration = parseInt(driverNearByData.driverOrderAcceptRejectDuration);
            }
            if(driverNearByData.driverRadios !== undefined){
                 kDistanceRadiusForDispatchInMiles = parseInt(driverNearByData.driverRadios);
            }
        }

        console.log('minimumDepositToRideAccept',minimumDepositToRideAccept);
        console.log('orderAcceptRejectDuration',orderAcceptRejectDuration);

        // change.after.ref.set({ status: "Pending Driver" }, {merge: true})
        return firestore
            .collection("users")
            .where('role', '==', "driver")
            .where('serviceType', '==', "parcel_delivery")
            .where('isActive', '==', true)
            .where('wallet_amount', '>=', minimumDepositToRideAccept)
            .get()
            .then(snapshot => {
                var found = false
                snapshot.forEach(doc => {
                    if (!found) {
                        // We simply assign the first available driver who's within a reasonable distance from the vendor and who did not reject the order and who is not delivering already
                        const driver = doc.data();
                        console.log(driver)

                        if (driver.location
                            && rejectedByDrivers.indexOf(driver.id) === -1
                            && (driver.inProgressOrderID === undefined || driver.inProgressOrderID === null)
                            && (driver.orderRequestData === undefined || driver.orderRequestData === null)) {
                            /*const vendor = orderData.vendor*/
                            if (orderData.senderLatLong) {
                                const distance = distanceRadiusride(driver.location.latitude, driver.location.longitude, orderData.senderLatLong.latitude, orderData.senderLatLong.longitude)
                                console.log("Driver (" + driver.email + " Location: ")
                                console.log(driver.location)
                                /*console.log("Vendor Location: lat " + vendor.latitude + " long" + vendor.longitude)*/
                                console.log(distance)
                                if (distance < kDistanceRadiusForDispatchInMiles) {
                                    found = true

                                    //set data for notification
                                    var time = Math.floor(orderAcceptRejectDuration / 60) + ":" + (orderAcceptRejectDuration % 60 ? orderAcceptRejectDuration % 60 : '00');
                                    var message = {
                                        notification:{
                                          title: 'New order received',
                                          body: 'You have a new order, please accept the order in '+time+' mins'
                                        },
                                        token: driver.fcmToken
                                    };
                                    //send notification to driver
                                    admin.messaging().send(message).then((response) => {
                                        console.log('Notification Success:',response);
                                        return null
                                    }).catch((error) => {
                                        console.log('Notification Error:',error);
                                        return null
                                    });

                                    // We update the order status
                                    change.after.ref.set({ status: "Driver Pending" }, {merge: true})
                                    .then(async function (result) {
                                        // After update the order status get new updated status
                                         firestore.collection("parcel_orders").doc(orderId).get().then((querySnapshot) => {	
                                            var newOrderData = querySnapshot.data();
                                            // Check if driver is accepting the order within defined time or not
                                            if(orderAcceptRejectDuration > 0 && newOrderData.status === "Driver Pending"){
                                                setTimeout(function(){ 
                                                    // Re-check order status after time limit exceed before find out other driver
                                                    firestore.collection("parcel_orders").doc(orderId).get().then((querySnapshot) => {
                                                        var newOrderData2 = querySnapshot.data();
                                                        // If order status is driver pending then and only we will find new driver and current driver will add to rejected list
                                                        if(newOrderData2.status === "Driver Pending"){
                                                            firestore.collection('users').doc(driver.id).update({
                                                                'orderRequestData': null,
                                                            });
                                                            // Current driver is adding to rejected list so they will not receive order again and update status to find new driver
                                                            rejectedByDrivers.push(driver.id);
                                                            firestore.collection('parcel_orders').doc(orderId).update({
                                                                'status': 'Order Accepted',
                                                                'rejectedByDrivers': rejectedByDrivers
                                                            })
                                                            console.log("Order not accepted by driver #" + driver.id + " for order #" + orderId + " within " + orderAcceptRejectDuration + " seconds, searching for next driver.")
                                                            return null
                                                        }
                                                        return null
                                                    })
                                                    .catch(error => {
                                                        console.log(error)
                                                    })
                                                },orderAcceptRejectDuration*1000);
                                            }
                                            return null
                                        })
                                        .catch(error => {
                                            console.log(error)
                                        })
                                        return null
                                    })
                                    .catch(error => {
                                        console.log(error)
                                    })

                                    // We send the order to the driver, by appending orderRequestData to the driver's user model in the users table
                                    firestore.collection('users').doc(driver.id).update({
                                        orderParcelRequestData: orderData,
                                    });
                                    console.log("Order sent to driver #" + driver.id + " for order #" + change.after.ref.id + " with distance at " + distance)
                                }
                            }
                        }
                    }
                })
                if (!found) {
                    // We did not find an available driver
                    console.log("Could not find an available driver for order #" + change.after.ref.id)
                }
                return null
            })
            .catch(error => {
                console.log(error)
            })
    }

    if (orderData.status === "Driver Accepted") {
        // Vendor accepted, driver accepted, so we update the delivery status
        change.after.ref.set({ status: "Order Shipped" }, {merge: true})
        console.log("Order #" + change.after.ref.id + " was shipped")
        return null
    }
    return null
});

const distanceRadiusride = (lat1, lon1, lat2, lon2) => {
	if ((lat1 === lat2) && (lon1 === lon2)) {
		return 0;
	}
	else {
		var radlat1 = Math.PI * lat1/180;
		var radlat2 = Math.PI * lat2/180;
		var theta = lon1-lon2;
		var radtheta = Math.PI * theta/180;
		var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
		if (dist > 1) {
			dist = 1;
		}
		dist = Math.acos(dist);
		dist = dist * 180/Math.PI;
		dist = dist * 60 * 1.1515;
		return dist;
	}
}

async function getDriverNearByData(){
    var snapshot =  await firestore.collection("settings").doc('DriverNearBy').get();
    return snapshot.data();
}