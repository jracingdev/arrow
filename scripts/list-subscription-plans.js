/**
 * Diagnóstico: sections + subscription_plans no Firestore j-arrow.
 * Uso (com firebase-admin instalado):
 *   cd firebase/import-export && npm i
 *   node ../../scripts/list-subscription-plans.js
 */
const path = require('path');
const fs = require('fs');

const credPath = path.join(__dirname, '..', 'firebase', 'import-export', 'credentials.json');
if (!fs.existsSync(credPath)) {
  console.error('credentials.json não encontrado em firebase/import-export/');
  process.exit(1);
}

const adminPaths = [
  path.join(__dirname, '..', 'firebase', 'import-export', 'node_modules', 'firebase-admin'),
  path.join(__dirname, '..', 'firebase', 'functions', 'functions', 'node_modules', 'firebase-admin'),
  'firebase-admin',
];

let admin;
for (const p of adminPaths) {
  try {
    admin = require(p);
    break;
  } catch (_) {}
}
if (!admin) {
  console.error('firebase-admin não encontrado. Rode: cd firebase/import-export && npm i');
  process.exit(1);
}

const serviceAccount = require(credPath);
if (!admin.apps.length) {
  admin.initializeApp({ credential: admin.credential.cert(serviceAccount) });
}
const db = admin.firestore();

(async () => {
  const vendor = await db.collection('settings').doc('vendor').get();
  console.log('settings/vendor:', vendor.exists ? vendor.data() : 'MISSING');

  const sections = await db.collection('sections').get();
  console.log('\nsections total:', sections.size);
  sections.docs.forEach((doc) => {
    const d = doc.data() || {};
    console.log({
      docId: doc.id,
      id: d.id,
      name: d.name,
      isActive: d.isActive,
      serviceTypeFlag: d.serviceTypeFlag,
      hasPlatformFee: d.platformFee != null,
      adminCommissionEnabled: d.adminCommision?.enable,
    });
  });

  const plans = await db.collection('subscription_plans').get();
  console.log('\nsubscription_plans total:', plans.size);
  plans.docs.forEach((doc) => {
    const d = doc.data() || {};
    console.log({
      docId: doc.id,
      id: d.id,
      name: d.name,
      sectionId: d.sectionId,
      isEnable: d.isEnable,
      isCommissionPlan: d.isCommissionPlan,
      price: d.price,
    });
  });

  process.exit(0);
})().catch((err) => {
  console.error(err);
  process.exit(1);
});
