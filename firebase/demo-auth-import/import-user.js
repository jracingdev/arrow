let admin = require("firebase-admin");
let email = "";
let uid = "";
let serviceAccountData = require("./serviceAccountKey.json");
let adminConfig = {
    credential: admin.credential.cert(serviceAccountData),
    databaseURL: "YOUR_DATABASE_URL",
};
let newUserOverrides=[];
let users_update_list=[{'email':'customer02@emart.com','uid':'eMIxZi024oXntpRxo7Sz895n5E93','password':'12345678'},{'email':'customer03@emart.com','uid':'ncxVDiCldHW3bnQU1bDCJVjc2B42','password':'123456'},{'email':'store03@emart.com','uid':'HthbaL49TgRkf2ZtPLhwSlBYjGI2','password':'123456'},{'email':'store04@emart.com','uid':'EmFlJfSWqDQLjYsOTPF2Zfusx3w1','password':'123456'},{'email':'driver01@emart.com','uid':'EJ4N3Nn5ZxXnscQkVjLKH1OF9Qs2','password':'123456'}];
Start();
async function Start() {
    console.log("Initializing firebase. databaseURL:", adminConfig.databaseURL);
    admin.initializeApp(adminConfig);
    for (let user of users_update_list) {
        uid = user.uid;
        email = user.email;
        password = user.password;
        newUserOverrides = {
            uid: uid,
        };    
        console.log("Starting update for user with email:", email);
        let oldUser = [];
        await admin.auth().getUserByEmail(email).then(async(oldUser) => {
            
            console.log("Old user found:", oldUser);
            await admin.auth().deleteUser(oldUser.uid);
            console.log("Old user deleted.");
            let dataToTransfer_keys = ["disabled", "displayName", "email", "emailVerified", "phoneNumber", "photoURL", "uid"];
            let newUserData = {};
            for (let key of dataToTransfer_keys) {
                newUserData[key] = oldUser[key];
            }
            
            Object.assign(newUserData, newUserOverrides);
            console.log("New user data ready: ", newUserData);
            let newUser = await admin.auth().createUser(newUserData);
            console.log("New user created: ", newUser);

          })
          .catch(async(error) => {
                await admin.auth().createUser({email:email,password:password});    
                let oldUser = await admin.auth().getUserByEmail(email);
                console.log("Old user found:", oldUser);
                await admin.auth().deleteUser(oldUser.uid);
                console.log("Old user deleted.");
                let dataToTransfer_keys = ["disabled", "displayName", "email", "emailVerified", "phoneNumber", "photoURL", "uid"];
                let newUserData = {};
                for (let key of dataToTransfer_keys) {
                    newUserData[key] = oldUser[key];
                }
                
                Object.assign(newUserData, newUserOverrides);
                console.log("New user data ready: ", newUserData);
                let newUser = await admin.auth().createUser(newUserData);
                console.log("New user created: ", newUser);

          });
    }
}