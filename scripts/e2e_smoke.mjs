/**
 * Smoke HTTP das URLs públicas (sem login, sem secrets).
 *
 * Uso:
 *   node scripts/e2e_smoke.mjs
 *   node scripts/e2e_smoke.mjs --help
 *
 * Variáveis opcionais:
 *   ARROW_WEB_HOME     default https://arrow.app.br
 *   ARROW_STORE_URL    default https://store.arrow.app.br
 *   ARROW_ADMIN_URL    default https://admin.arrow.app.br
 *
 * Aceita 200–399. Falha em 500+ ou rede. Não cobre fluxos autenticados.
 */
const HOME = process.env.ARROW_WEB_HOME || 'https://arrow.app.br';
const STORE = (process.env.ARROW_STORE_URL || 'https://store.arrow.app.br').replace(/\/$/, '');
const ADMIN = (process.env.ARROW_ADMIN_URL || 'https://admin.arrow.app.br').replace(/\/$/, '');

const TARGETS = [
  { name: 'home', url: HOME },
  { name: 'store login', url: `${STORE}/login` },
  { name: 'admin login', url: `${ADMIN}/login` },
];

function printHelp() {
  console.log(`Usage: node scripts/e2e_smoke.mjs [--help]

GET nas URLs públicas (home, store/login, admin/login).
Espera 200–399 e corpo sem "500 Internal Server Error" óbvio.

ARROW_WEB_HOME / ARROW_STORE_URL / ARROW_ADMIN_URL sobrescrevem os hosts.
Não há suíte logada — isso exigiria secrets de conta.
`);
}

async function fetchUrl(url) {
  const controller = new AbortController();
  const timer = setTimeout(() => controller.abort(), 20000);
  try {
    const res = await fetch(url, {
      method: 'GET',
      redirect: 'follow',
      signal: controller.signal,
      headers: { 'user-agent': 'arrow-e2e-smoke/1.0' },
    });
    const text = await res.text();
    return { status: res.status, finalUrl: res.url, text };
  } finally {
    clearTimeout(timer);
  }
}

function looksLike500(text) {
  if (!text) return false;
  const sample = text.slice(0, 4000).toLowerCase();
  return (
    sample.includes('500 internal server error') ||
    sample.includes('server error') && sample.includes('whoops') ||
    sample.includes('uncaught exception')
  );
}

const args = process.argv.slice(2);
if (args.includes('--help') || args.includes('-h')) {
  printHelp();
  process.exit(0);
}

let failed = 0;
for (const target of TARGETS) {
  try {
    const { status, finalUrl, text } = await fetchUrl(target.url);
    const bodyBad = looksLike500(text);
    if (status >= 500 || bodyBad) {
      console.error(`FAIL ${target.name}: ${target.url} → ${status} ${finalUrl}${bodyBad ? ' (corpo parece 500)' : ''}`);
      failed += 1;
    } else if (status >= 400) {
      console.error(`FAIL ${target.name}: ${target.url} → ${status} ${finalUrl}`);
      failed += 1;
    } else {
      console.log(`OK   ${target.name}: ${status} ${finalUrl}`);
    }
  } catch (err) {
    console.error(`FAIL ${target.name}: ${target.url} → ${err.message || err}`);
    failed += 1;
  }
}

if (failed) {
  console.error(`Smoke falhou em ${failed} URL(s).`);
  process.exit(1);
}
console.log('Smoke web ok.');
process.exit(0);
