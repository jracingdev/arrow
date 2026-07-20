/**
 * Orquestra os patches CMS BR no Firestore (servidor).
 *
 * Ordem:
 *   1) backfill sections order (+ --activate-br opcional)
 *   2) terms + privacy LGPD
 *   3) contato/país + pagamentos + BRL
 *   4) onboarding pt-BR residual
 *   5) notificações + email templates pt-BR
 *   6) document_verification_settings (se ausente)
 *
 * Uso:
 *   cd firebase/import-export && npm i
 *   node ../../scripts/run-cms-br-all.js
 *   node ../../scripts/run-cms-br-all.js --activate-br --deactivate-others
 *   node ../../scripts/run-cms-br-all.js --dry-run
 *
 * Flags extras são repassadas aos scripts filhos quando aplicável.
 */
const { spawnSync } = require('child_process');
const path = require('path');

const scriptsDir = __dirname;
const args = process.argv.slice(2);
const dry = args.includes('--dry-run');

function run(script, extra = []) {
  const cmdArgs = [path.join(scriptsDir, script), ...args, ...extra];
  console.log('\n===', script, cmdArgs.slice(1).join(' '), '===');
  const r = spawnSync(process.execPath, cmdArgs, { stdio: 'inherit' });
  if (r.status !== 0) {
    process.exit(r.status || 1);
  }
}

run('backfill-sections-br.js');
run('seed-terms-privacy-lgpd.js');
run('seed-contact-payments-br.js');
run('update-onboarding-ptbr.js');
run('seed-notifications-ptbr.js');
run('seed-document-verification-settings.js');

console.log(dry ? '\nCMS BR dry-run finalizado.' : '\nCMS BR concluído.');
