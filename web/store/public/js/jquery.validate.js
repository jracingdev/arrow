if (typeof firebase !== 'undefined' && window.__firebaseConfig && window.__firebaseConfig.apiKey && !firebase.apps.length) {
    firebase.initializeApp(window.__firebaseConfig);
}
