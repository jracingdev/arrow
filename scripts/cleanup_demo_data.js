/**
 * Apaga somente documentos de demonstração (prefixos demo_e2e_ e, opcional, demo_joelson_).
 * Nunca apaga IDs de produção.
 *
 * Uso:
 *   cd firebase/import-export && npm i
 *   node ../../scripts/cleanup_demo_data.js              # dry-run (padrão)
 *   node ../../scripts/cleanup_demo_data.js --yes        # apaga demo_e2e_*
 *   node ../../scripts/cleanup_demo_data.js --yes --include-joelson
 *   node ../../scripts/cleanup_demo_data.js --help
 *
 * Credenciais: GOOGLE_APPLICATION_CREDENTIALS, firebase/import-export/credentials.json
 * ou j-arrow-firebase-adminsdk-*.json na raiz (nunca commitar).
 */
const { initFirestore, parseArgs } = require('./lib/firestore-admin');
const { E2E_PREFIX, JOELSON_PREFIX, DEMO_COLLECTIONS } = require('./lib/demo-ids');

function printHelp() {
  console.log(`Usage: node scripts/cleanup_demo_data.js [--yes] [--include-joelson] [--help]

Remove só docs cujo ID começa com:
  ${E2E_PREFIX}*
  ${JOELSON_PREFIX}*   (somente com --include-joelson)

Padrão é dry-run (lista o que seria apagado, não grava).
Passe --yes para executar. Não mexe em dados de produção.
`);
}

async function listByPrefix(db, admin, collection, prefix) {
  try {
    const snap = await db
      .collection(collection)
      .where(admin.firestore.FieldPath.documentId(), '>=', prefix)
      .where(admin.firestore.FieldPath.documentId(), '<', prefix + '\uf8ff')
      .get();
    return snap.docs.map((d) => ({ col: collection, id: d.id }));
  } catch (err) {
    console.warn(`Aviso: não foi possível listar ${collection}:`, err.message || err);
    return [];
  }
}

(async () => {
  const { has } = parseArgs();
  if (has('help') || has('h')) {
    printHelp();
    process.exit(0);
  }
  const includeJoelson = has('include-joelson');
  const yes = has('yes');
  const { admin, db, credPath } = initFirestore();
  console.log('Credentials:', credPath);
  console.log(yes ? 'Executando exclusão…' : 'Dry-run: nada será apagado (passe --yes para confirmar).');
  if (includeJoelson) console.log('Inclui prefixo', JOELSON_PREFIX);

  const prefixes = [E2E_PREFIX];
  if (includeJoelson) prefixes.push(JOELSON_PREFIX);

  const found = [];
  for (const col of DEMO_COLLECTIONS) {
    for (const prefix of prefixes) {
      const rows = await listByPrefix(db, admin, col, prefix);
      found.push(...rows);
    }
  }

  const unique = [];
  const seen = new Set();
  for (const row of found) {
    const key = `${row.col}/${row.id}`;
    if (seen.has(key)) continue;
    seen.add(key);
    unique.push(row);
  }

  console.log(`Encontrados ${unique.length} docs demo.`);
  unique.forEach((r) => console.log(`  ${r.col}/${r.id}`));

  if (!yes) {
    console.log('Dry-run ok. Para apagar: node scripts/cleanup_demo_data.js --yes');
    process.exit(0);
  }

  const CHUNK = 400;
  for (let i = 0; i < unique.length; i += CHUNK) {
    const batch = db.batch();
    unique.slice(i, i + CHUNK).forEach((r) => {
      batch.delete(db.collection(r.col).doc(r.id));
    });
    await batch.commit();
  }
  console.log(`Apagados ${unique.length} docs demo.`);
  process.exit(0);
})().catch((err) => {
  console.error(err);
  process.exit(1);
});
