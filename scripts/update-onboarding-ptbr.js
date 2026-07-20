/**
 * Atualiza títulos/descrições da collection Firestore `on_boarding` para pt-BR (marca Arrow).
 *
 * Uso:
 *   cd firebase/import-export && npm i
 *   export NODE_PATH=$PWD/node_modules
 *   node ../../scripts/update-onboarding-ptbr.js
 *   node ../../scripts/update-onboarding-ptbr.js --dry-run
 *   node ../../scripts/update-onboarding-ptbr.js --list
 *
 * Requer credentials.json válido em firebase/import-export/.
 */
const fs = require('fs');
const path = require('path');
const { initFirestore, parseArgs } = require('./lib/firestore-admin');

const seedPath = path.join(__dirname, 'onboarding-ptbr-seed.json');

function norm(s) {
  return String(s || '')
    .replace(/[\u2018\u2019\u201A\u2032]/g, "'")
    .replace(/[\u201C\u201D\u201E\u2033]/g, '"')
    .replace(/\s+/g, ' ')
    .trim();
}

function looksEnglish(title) {
  const t = norm(title);
  if (!t) return false;
  // Já em PT comum
  if (/[áàâãéêíóôõúçÁÀÂÃÉÊÍÓÔÕÚÇ]/.test(t)) return false;
  if (/^(Bem-vindo|Comece|Seu |Configuração|Trabalho|Monte |Compre |Entrega |Simplifique|Das compras|App multi|Experiência|Tudo o que)/i.test(t)) {
    return false;
  }
  return /\b(Your|Welcome|Start|Quick|Work|From|All|Convenient|Shop|Fast|Build|Simplify|Simple|Manage|See|Partner|Provide|Get|Join)\b/i.test(t);
}

(async () => {
  const { has } = parseArgs();
  if (has('help') || has('h')) {
    console.log('Usage: node scripts/update-onboarding-ptbr.js [--dry-run] [--list]');
    process.exit(0);
  }
  const dryRun = has('dry-run');
  const listOnly = has('list');
  const seed = JSON.parse(fs.readFileSync(seedPath, 'utf8'));
  const byId = seed.updates_by_doc_id || {};
  const byTitle = seed.updates_by_english_title || {};
  const byDescription = seed.updates_by_english_description || {};

  // Índice normalizado de títulos
  const byTitleNorm = {};
  for (const [k, v] of Object.entries(byTitle)) {
    byTitleNorm[norm(k)] = v;
  }
  const byDescNorm = {};
  for (const [k, v] of Object.entries(byDescription)) {
    byDescNorm[norm(k)] = v;
  }

  const { db } = initFirestore();
  const snap = await db.collection('on_boarding').get();

  if (listOnly) {
    console.log('Total on_boarding:', snap.size);
    snap.docs.forEach((doc) => {
      const d = doc.data() || {};
      const en = looksEnglish(d.title);
      console.log({
        id: doc.id,
        title: d.title,
        englishLike: en,
        description: (d.description || '').slice(0, 80),
      });
    });
    process.exit(0);
  }

  let updated = 0;
  let skipped = 0;
  let already = 0;

  for (const doc of snap.docs) {
    const data = doc.data() || {};
    const title = data.title || '';
    const description = data.description || '';

    let patch =
      byId[doc.id] ||
      byTitle[title] ||
      byTitleNorm[norm(title)] ||
      byDescription[description] ||
      byDescNorm[norm(description)] ||
      null;

    if (!patch) {
      skipped++;
      const flag = looksEnglish(title) ? ' [AINDA EM INGLÊS?]' : '';
      console.log('Skip' + flag + ':', doc.id, '|', title);
      continue;
    }

    const next = {
      title: patch.title,
      description: patch.description,
    };
    const changed = next.title !== title || next.description !== description;
    if (!changed) {
      already++;
      console.log('Already OK:', doc.id, '=>', next.title);
      continue;
    }

    console.log(dryRun ? 'Would update' : 'Update', doc.id, '=>', next.title);
    if (!dryRun) {
      await doc.ref.update(next);
    }
    updated++;
  }

  console.log(
    dryRun
      ? `Dry-run. Would update ${updated} of ${snap.size} (skipped ${skipped}, already ${already}).`
      : `Done. Updated ${updated} of ${snap.size} docs (skipped ${skipped}, already ${already}).`
  );
  process.exit(0);
})().catch((err) => {
  console.error(err.message || err);
  process.exit(1);
});
