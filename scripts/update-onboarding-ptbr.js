/**
 * Atualiza títulos/descrições da collection Firestore `on_boarding` para pt-BR (marca Arrow).
 * Uso:
 *   cd firebase/import-export
 *   npm i firebase-admin
 *   node ../../scripts/update-onboarding-ptbr.js
 *
 * Requer credentials.json válido (service account) em firebase/import-export/.
 */
const fs = require('fs');
const path = require('path');

const credPath = path.join(__dirname, '..', 'firebase', 'import-export', 'credentials.json');
const seedPath = path.join(__dirname, 'onboarding-ptbr-seed.json');

if (!fs.existsSync(credPath)) {
  console.error('credentials.json não encontrado em firebase/import-export/');
  process.exit(1);
}

let admin;
try {
  admin = require('firebase-admin');
} catch (e) {
  console.error('Instale firebase-admin: npm i firebase-admin');
  process.exit(1);
}

const serviceAccount = require(credPath);
const seed = JSON.parse(fs.readFileSync(seedPath, 'utf8'));
const byTitle = seed.updates_by_english_title || {};

if (!admin.apps.length) {
  admin.initializeApp({ credential: admin.credential.cert(serviceAccount) });
}

const db = admin.firestore();

(async () => {
  const snap = await db.collection('on_boarding').get();
  let updated = 0;
  for (const doc of snap.docs) {
    const data = doc.data() || {};
    const title = data.title || '';
    const patch = byTitle[title];
    if (!patch) continue;
    await doc.ref.update({
      title: patch.title,
      description: patch.description,
    });
    updated++;
    console.log('Updated', doc.id, '=>', patch.title);
  }
  console.log('Done. Updated', updated, 'of', snap.size, 'docs.');
  // Also match already-partial EN descriptions for known titles if title already PT
  process.exit(0);
})().catch((err) => {
  console.error(err);
  process.exit(1);
});
