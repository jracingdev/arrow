/**
 * Garante settings/document_verification_settings no Firestore.
 *
 * Defaults alinhados a firebase/import-export/collections.json (Arrow):
 *   isStoreVerification: false
 *   isDriverVerification: false
 *   isOwnerVerification: false
 *
 * Seguro: verificação de documentos desligada até o Admin habilitar.
 * Por padrão só cria se o doc não existir; use --force para sobrescrever.
 *
 * Uso no servidor:
 *   cd firebase/import-export && npm i
 *   node ../../scripts/seed-document-verification-settings.js
 *   node ../../scripts/seed-document-verification-settings.js --dry-run
 *   node ../../scripts/seed-document-verification-settings.js --force
 */
const { initFirestore, parseArgs } = require('./lib/firestore-admin');

const DOC_ID = 'document_verification_settings';

/** Mesmos campos/valores de collections.json → settings.document_verification_settings */
const DEFAULTS = {
  isStoreVerification: false,
  isDriverVerification: false,
  isOwnerVerification: false,
};

function printHelp() {
  console.log(`Usage: node scripts/seed-document-verification-settings.js [options]

Options:
  --force    Sobrescreve o doc mesmo se já existir
  --dry-run  Só imprime; não grava
  --help
`);
}

(async () => {
  const { has } = parseArgs();
  if (has('help') || has('h')) {
    printHelp();
    process.exit(0);
  }
  const dryRun = has('dry-run');
  const force = has('force');

  const { db } = initFirestore();
  const ref = db.collection('settings').doc(DOC_ID);
  const snap = await ref.get();

  console.log('Target: settings/' + DOC_ID);
  console.log('Defaults (collections.json):', DEFAULTS);
  console.log('Exists:', snap.exists, force ? '(--force)' : '');

  if (snap.exists && !force) {
    console.log('Doc já existe; nada a fazer. Use --force para sobrescrever.');
    process.exit(0);
  }

  if (dryRun) {
    console.log('Dry-run: não gravou.', force || !snap.exists ? 'Gravaria:' : '', DEFAULTS);
    process.exit(0);
  }

  await ref.set(DEFAULTS, { merge: true });
  console.log('Done. settings/' + DOC_ID, force ? 'atualizado (--force).' : 'criado.');
  process.exit(0);
})().catch((err) => {
  console.error(err);
  process.exit(1);
});
