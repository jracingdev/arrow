/**
 * Seed/atualização LGPD Arrow: settings/termsAndConditions e settings/privacyPolicy.
 *
 * HTML em português (pt-BR), marca Arrow. Placeholders obrigatórios (NÃO inventar CNPJ):
 *   [RAZAO_SOCIAL]  [EMAIL_CONTATO]  [CNPJ]
 *
 * Uso no servidor:
 *   cd firebase/import-export && npm i
 *   node ../../scripts/seed-terms-privacy-lgpd.js
 *   node ../../scripts/seed-terms-privacy-lgpd.js --dry-run
 *   node ../../scripts/seed-terms-privacy-lgpd.js --razao="Minha Empresa LTDA" --email="privacidade@dominio.com" --cnpj="00.000.000/0001-00"
 *
 * Sem --razao/--email/--cnpj, grava o HTML com placeholders literais para edição posterior no Admin.
 */
const fs = require('fs');
const path = require('path');
const { initFirestore, parseArgs } = require('./lib/firestore-admin');

const dataPath = path.join(__dirname, 'data', 'terms-privacy-lgpd-ptbr.json');

function applyPlaceholders(html, { razao, email, cnpj }) {
  let out = html;
  if (razao) out = out.split('[RAZAO_SOCIAL]').join(razao);
  if (email) out = out.split('[EMAIL_CONTATO]').join(email);
  if (cnpj) out = out.split('[CNPJ]').join(cnpj);
  return out;
}

function printHelp() {
  console.log(`Usage: node scripts/seed-terms-privacy-lgpd.js [options]

Options:
  --razao=...    Substitui [RAZAO_SOCIAL]
  --email=...    Substitui [EMAIL_CONTATO]
  --cnpj=...     Substitui [CNPJ] (use o CNPJ real; não invente)
  --dry-run      Só imprime resumo
  --help
`);
}

(async () => {
  const { has, opts } = parseArgs();
  if (has('help') || has('h')) {
    printHelp();
    process.exit(0);
  }
  const dryRun = has('dry-run');
  const payload = JSON.parse(fs.readFileSync(dataPath, 'utf8'));
  const vars = {
    razao: opts.razao || null,
    email: opts.email || null,
    cnpj: opts.cnpj || null,
  };

  const terms = applyPlaceholders(payload.terms_and_condition, vars);
  const privacy = applyPlaceholders(payload.privacy_policy, vars);

  console.log('terms length=', terms.length, '| privacy length=', privacy.length);
  console.log('placeholders substituídos:', {
    razao: Boolean(vars.razao),
    email: Boolean(vars.email),
    cnpj: Boolean(vars.cnpj),
  });

  if (dryRun) {
    console.log('Dry-run: não gravou. Docs alvo: settings/termsAndConditions, settings/privacyPolicy');
    process.exit(0);
  }

  const { db } = initFirestore();
  await db.collection('settings').doc('termsAndConditions').set(
    { terms_and_condition: terms },
    { merge: true }
  );
  await db.collection('settings').doc('privacyPolicy').set(
    { privacy_policy: privacy },
    { merge: true }
  );
  console.log('Done. Atualizados settings/termsAndConditions e settings/privacyPolicy');
  process.exit(0);
})().catch((err) => {
  console.error(err.message || err);
  process.exit(1);
});
