/**
 * Atualiza títulos/descrições da collection Firestore `on_boarding` para pt-BR (marca Arrow).
 *
 * Cobre o onboarding residual (títulos ainda em inglês), inclusive os 6 docs comuns:
 *   Simplify Your Workflow, Simple Business Setup, Shop from Your Favorite Stores,
 *   Fast & Reliable Parcel Delivery, Build Your Dream Team, All Your Needs in One App!
 *
 * Uso:
 *   cd firebase/import-export && npm i
 *   node ../../scripts/update-onboarding-ptbr.js
 *   node ../../scripts/update-onboarding-ptbr.js --dry-run
 *
 * Requer credentials.json válido (service account) em firebase/import-export/.
 */
const fs = require('fs');
const path = require('path');
const { initFirestore, parseArgs } = require('./lib/firestore-admin');

const seedPath = path.join(__dirname, 'onboarding-ptbr-seed.json');

(async () => {
  const { has } = parseArgs();
  if (has('help') || has('h')) {
    console.log('Usage: node scripts/update-onboarding-ptbr.js [--dry-run]');
    process.exit(0);
  }
  const dryRun = has('dry-run');
  const seed = JSON.parse(fs.readFileSync(seedPath, 'utf8'));
  const byTitle = seed.updates_by_english_title || {};
  const byDescription = seed.updates_by_english_description || {};

  const { db } = initFirestore();
  const snap = await db.collection('on_boarding').get();
  let updated = 0;
  let skipped = 0;

  for (const doc of snap.docs) {
    const data = doc.data() || {};
    const title = data.title || '';
    const description = data.description || '';

    let patch = byTitle[title] || byDescription[description] || null;

    // Fallback: título já em PT mas descrição ainda em inglês conhecida
    if (!patch && byDescription[description]) {
      patch = byDescription[description];
    }
    // Fallback: descrição contém trecho EN mapeado como chave de título (caso Rack typo)
    if (!patch && byTitle[description]) {
      patch = byTitle[description];
    }

    if (!patch) {
      skipped++;
      console.log('Skip (sem mapeamento):', doc.id, '|', title);
      continue;
    }

    const next = {
      title: patch.title,
      description: patch.description,
    };
    const changed = next.title !== title || next.description !== description;
    console.log(changed ? 'Update' : 'Already OK', doc.id, '=>', next.title);
    if (changed && !dryRun) {
      await doc.ref.update(next);
      updated++;
    } else if (changed) {
      updated++;
    }
  }

  console.log(
    dryRun
      ? `Dry-run. Would update ${updated} of ${snap.size} (skipped ${skipped}).`
      : `Done. Updated ${updated} of ${snap.size} docs (skipped ${skipped}).`
  );
  process.exit(0);
})().catch((err) => {
  console.error(err.message || err);
  process.exit(1);
});
