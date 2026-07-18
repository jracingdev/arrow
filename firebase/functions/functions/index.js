const functions = require('firebase-functions');
const admin = require('firebase-admin');

const serviceAccount = require("./serviceAccountKey.json");
admin.initializeApp({
  credential: admin.credential.cert(serviceAccount),
  databaseURL: "YOUR_DATABASE_URL"
});

const delivery = require('./products/delivery')

//Multivendor service function
exports.deliveryDispatch = delivery.dispatch

//Delete auth user function
exports.deleteUser = functions.https.onCall(async (data, context) => {
    try {
        await admin.auth().deleteUser(data.uid);
        return { result: 'user successfully deleted'};
    } catch (error) {
        throw new functionsGlobal.https.HttpsError('failed-precondition','The function must be called while authenticated.');
    }
});