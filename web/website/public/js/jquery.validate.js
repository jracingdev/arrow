
var firebaseConfig;
if (window.__firebaseConfig && window.__firebaseConfig.apiKey) {
    firebaseConfig = window.__firebaseConfig;
} else {
    firebaseConfig = {
        apiKey: $.decrypt($.cookie('XSRF-TOKEN-AK')),
        authDomain: $.decrypt($.cookie('XSRF-TOKEN-AD')),
        databaseURL: $.decrypt($.cookie('XSRF-TOKEN-DU')),
        projectId: $.decrypt($.cookie('XSRF-TOKEN-PI')),
        storageBucket: $.decrypt($.cookie('XSRF-TOKEN-SB')),
        messagingSenderId: $.decrypt($.cookie('XSRF-TOKEN-MS')),
        appId: $.decrypt($.cookie('XSRF-TOKEN-AI')),
        measurementId: $.decrypt($.cookie('XSRF-TOKEN-MI'))
    };
}

if (firebaseConfig.apiKey && typeof firebase !== 'undefined' && !firebase.apps.length) {
    firebase.initializeApp(firebaseConfig);
}
