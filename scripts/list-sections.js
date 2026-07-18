/**
 * Lista seções do Firestore (diagnóstico do dropdown "Selecionar seção").
 * Uso:
 *   export NODE_PATH=/www/wwwroot/arrow-repo/firebase/import-export/node_modules
 *   node scripts/list-sections.js
 */
const path = require('path');
const fs = require('fs');

const credPath = path.join(__dirname, '..', 'firebase', 'import-export', 'credentials.json');
if (!fs.existsSync(credPath)) {
  console.error('credentials.json não encontrado em firebase/import-export/');
  process.exit(1);
}

let admin;
try {
  admin = require('firebase-admin');
} catch (e) {
  console.error('Instale firebase-admin (NODE_PATH ou npm i).');
  process.exit(1);
}

const serviceAccount = require(credPath);
if (!admin.apps.length) {
  admin.initializeApp({ credential: admin.credential.cert(serviceAccount) });
}

const db = admin.firestore();

(async () => {
  const snap = await db.collection('sections').get();
  console.log('Total docs em sections:', snap.size);
  if (snap.empty) {
    console.log('VAZIO: crie módulos em Admin → Seções → Criar, ou importe o seed.');
    process.exit(0);
  }
  let active = 0;
  snap.docs.forEach((doc) => {
    const d = doc.data() || {};
    const isActive = d.isActive === true;
    if (isActive) active++;
    console.log({
      docId: doc.id,
      id: d.id,
      name: d.name,
      serviceTypeFlag: d.serviceTypeFlag,
      isActive: d.isActive,
      order: d.order,
    });
  });
  console.log('Ativas (isActive === true):', active);
  if (active === 0) {
    console.log('Nenhuma ativa: no Admin → Seções, ative pelo menos um módulo.');
  }
  process.exit(0);
})().catch((err) => {
  console.error(err);
  process.exit(1);
});
