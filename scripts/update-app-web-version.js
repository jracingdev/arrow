/**
 * Atualiza app_version e web_version em Firestore settings/Version (ex.: 6.3 → 6.8).
 *
 * Uso:
 *   node scripts/update-app-web-version.js
 *   node scripts/update-app-web-version.js 6.8
 *
 * Requer credentials.json válido (service account) em firebase/import-export/
 * e firebase-admin instalado (ex.: em firebase/functions/functions).
 *
 * Alternativa sem script: Admin → Settings → App → Globals → App Version / Web Version → salvar 6.8.
 */
const path = require('path');
const fs = require('fs');

const VERSION = process.argv[2] || '6.8';
const credPath = path.join(__dirname, '..', 'firebase', 'import-export', 'credentials.json');

if (!fs.existsSync(credPath)) {
  console.error('credentials.json não encontrado em firebase/import-export/');
  process.exit(1);
}

const serviceAccount = require(credPath);
if (!serviceAccount.project_id || !serviceAccount.private_key) {
  console.error(
    'credentials.json está vazio/placeholder. Coloque a service account do Firebase e rode de novo,\n' +
      'ou altere App Version / Web Version para ' +
      VERSION +
      ' em Admin → Settings → App → Globals e salve.'
  );
  process.exit(1);
}

let admin;
const candidates = [
  path.join(__dirname, '..', 'firebase', 'functions', 'functions', 'node_modules', 'firebase-admin'),
  'firebase-admin',
];
for (const mod of candidates) {
  try {
    admin = require(mod);
    break;
  } catch (_) {
    /* try next */
  }
}
if (!admin) {
  console.error('Instale firebase-admin (ex.: cd firebase/functions/functions && npm i)');
  process.exit(1);
}

if (!admin.apps.length) {
  admin.initializeApp({ credential: admin.credential.cert(serviceAccount) });
}

const db = admin.firestore();
const ref = db.collection('settings').doc('Version');

(async () => {
  const before = (await ref.get()).data() || {};
  console.log('Antes:', {
    app_version: before.app_version,
    web_version: before.web_version,
  });

  await ref.set(
    {
      app_version: VERSION,
      web_version: VERSION,
    },
    { merge: true }
  );

  const after = (await ref.get()).data() || {};
  console.log('Depois:', {
    app_version: after.app_version,
    web_version: after.web_version,
  });
  console.log('OK. Recarregue o admin — sidebar deve mostrar V:' + VERSION);
  process.exit(0);
})().catch((err) => {
  console.error(err);
  process.exit(1);
});
