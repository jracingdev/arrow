/**
 * Atualiza dynamic_notification e email_templates no Firestore para pt-BR (marca Arrow).
 *
 * Uso:
 *   cd firebase/import-export && npm i
 *   export NODE_PATH=$PWD/node_modules
 *   node ../../scripts/seed-notifications-ptbr.js
 *   node ../../scripts/seed-notifications-ptbr.js --dry-run
 *   node ../../scripts/seed-notifications-ptbr.js --list
 *
 * Requer credentials.json válido em firebase/import-export/.
 * Não altera campos de status de domínio.
 */
const fs = require('fs');
const path = require('path');
const { initFirestore, parseArgs } = require('./lib/firestore-admin');

const seedPath = path.join(__dirname, 'notifications-ptbr-seed.json');

function patchEmailBody(html) {
  if (!html || typeof html !== 'string') return html;
  let out = html;
  out = out.replace(/Emart/g, 'Arrow');
  out = out.replace(/eMart/g, 'Arrow');
  out = out.replace(/\bDear\b/g, 'Olá,');
  out = out.replace(/\bBest regards\b/gi, 'Atenciosamente');
  out = out.replace(/\bWarm regards\b/gi, 'Atenciosamente');
  return out;
}

(async () => {
  const { has } = parseArgs();
  if (has('help') || has('h')) {
    console.log('Usage: node scripts/seed-notifications-ptbr.js [--dry-run] [--list]');
    process.exit(0);
  }
  const dryRun = has('dry-run');
  const listOnly = has('list');
  const seed = JSON.parse(fs.readFileSync(seedPath, 'utf8'));
  const byNotifType = seed.dynamic_notification || {};
  const byEmailType = seed.email_templates || {};

  const { db } = initFirestore();

  const notifSnap = await db.collection('dynamic_notification').get();
  const emailSnap = await db.collection('email_templates').get();

  if (listOnly) {
    console.log('Total dynamic_notification:', notifSnap.size);
    notifSnap.docs.forEach((doc) => {
      const d = doc.data() || {};
      console.log({
        id: doc.id,
        type: d.type,
        subject: d.subject,
        message: (d.message || '').slice(0, 80),
      });
    });
    console.log('Total email_templates:', emailSnap.size);
    emailSnap.docs.forEach((doc) => {
      const d = doc.data() || {};
      console.log({
        id: doc.id,
        type: d.type,
        subject: d.subject,
      });
    });
    process.exit(0);
  }

  let updated = 0;
  let skipped = 0;
  let already = 0;

  for (const doc of notifSnap.docs) {
    const data = doc.data() || {};
    const type = data.type;
    const patch = type ? byNotifType[type] : null;
    if (!patch) {
      skipped++;
      console.log('Skip notif:', doc.id, '|', type || '(no type)');
      continue;
    }
    const next = {
      subject: patch.subject,
      message: patch.message,
    };
    const changed = next.subject !== data.subject || next.message !== data.message;
    if (!changed) {
      already++;
      console.log('Already OK notif:', doc.id, type, '=>', next.subject);
      continue;
    }
    console.log(dryRun ? 'Would update notif' : 'Update notif', doc.id, type, '=>', next.subject);
    if (!dryRun) {
      await doc.ref.update(next);
    }
    updated++;
  }

  for (const doc of emailSnap.docs) {
    const data = doc.data() || {};
    const type = data.type;
    const patch = type ? byEmailType[type] : null;
    if (!patch) {
      skipped++;
      console.log('Skip email:', doc.id, '|', type || '(no type)');
      continue;
    }

    const next = { subject: patch.subject };
    // Safe body/html branding + greeting/closing when fields exist
    for (const field of ['message', 'body', 'html', 'html_body']) {
      if (typeof data[field] === 'string' && data[field].length) {
        const patched = patchEmailBody(data[field]);
        if (patched !== data[field]) {
          next[field] = patched;
        }
      }
    }

    const changed = Object.keys(next).some((k) => next[k] !== data[k]);
    if (!changed) {
      already++;
      console.log('Already OK email:', doc.id, type, '=>', next.subject);
      continue;
    }
    console.log(dryRun ? 'Would update email' : 'Update email', doc.id, type, '=>', next.subject);
    if (!dryRun) {
      await doc.ref.update(next);
    }
    updated++;
  }

  console.log(
    dryRun
      ? `Dry-run. Would update ${updated} (skipped ${skipped}, already ${already}).`
      : `Done. Updated ${updated} (skipped ${skipped}, already ${already}).`
  );
  process.exit(0);
})().catch((err) => {
  console.error(err.message || err);
  process.exit(1);
});
