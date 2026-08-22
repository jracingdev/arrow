/**
 * Helper compartilhado: inicializa firebase-admin com credentials do monorepo Arrow.
 *
 * Credenciais esperadas em:
 *   firebase/import-export/credentials.json
 * (no servidor de produção o arquivo já existe; no repo local pode estar vazio/placeholder)
 *
 * Uso típico no servidor:
 *   cd /caminho/arrow-repo/firebase/import-export
 *   npm i
 *   node ../../scripts/<script>.js
 *
 * Ou com NODE_PATH apontando para node_modules deste diretório.
 */
const fs = require('fs');
const path = require('path');

function looksUsableServiceAccount(filePath) {
  try {
    const sa = JSON.parse(fs.readFileSync(filePath, 'utf8'));
    return Boolean(sa.private_key && String(sa.private_key).trim() && sa.client_email && String(sa.client_email).trim());
  } catch (e) {
    return false;
  }
}

function resolveCredentialsPath() {
  const fromEnv = process.env.GOOGLE_APPLICATION_CREDENTIALS || process.env.FIREBASE_CREDENTIALS;
  if (fromEnv && fs.existsSync(fromEnv) && looksUsableServiceAccount(fromEnv)) return path.resolve(fromEnv);
  const root = path.join(__dirname, '..', '..');
  const candidates = [path.join(root, 'firebase', 'import-export', 'credentials.json')];
  try {
    fs.readdirSync(root).forEach((name) => {
      if (/firebase-adminsdk.*\.json$/i.test(name)) candidates.unshift(path.join(root, name));
    });
  } catch (e) {
    // ignore
  }
  for (const candidate of candidates) {
    if (fs.existsSync(candidate) && looksUsableServiceAccount(candidate)) return candidate;
  }
  return candidates[candidates.length - 1];
}

function loadServiceAccount(credPath) {
  if (!fs.existsSync(credPath)) {
    throw new Error(`credentials.json não encontrado em: ${credPath}`);
  }
  const raw = fs.readFileSync(credPath, 'utf8');
  let sa;
  try {
    sa = JSON.parse(raw);
  } catch (e) {
    throw new Error(`credentials.json inválido (JSON): ${credPath}`);
  }
  const looksEmpty =
    !sa.private_key ||
    String(sa.private_key).trim() === '' ||
    !sa.client_email ||
    String(sa.client_email).trim() === '';
  if (looksEmpty) {
    throw new Error(
      'credentials.json está vazio/placeholder. No servidor, use o service account real ' +
        'em firebase/import-export/credentials.json (não commitar credenciais).'
    );
  }
  return sa;
}

function loadFirebaseAdmin() {
  try {
    return require('firebase-admin');
  } catch (e) {
    const local = path.join(__dirname, '..', '..', 'firebase', 'import-export', 'node_modules', 'firebase-admin');
    try {
      return require(local);
    } catch (e2) {
      throw new Error(
        'firebase-admin não encontrado. Rode: cd firebase/import-export && npm i'
      );
    }
  }
}

function initFirestore() {
  const admin = loadFirebaseAdmin();
  const credPath = resolveCredentialsPath();
  const serviceAccount = loadServiceAccount(credPath);
  if (!admin.apps.length) {
    admin.initializeApp({ credential: admin.credential.cert(serviceAccount) });
  }
  return { admin, db: admin.firestore(), credPath };
}

/** Igual a initFirestore, mas não lança se faltar service account (testes de contrato). */
function tryInitFirestore() {
  try {
    return { ok: true, ...initFirestore() };
  } catch (err) {
    return { ok: false, error: err, message: err && err.message ? err.message : String(err) };
  }
}

function encodeGeohash(latitude, longitude, precision) {
  precision = precision || 9;
  const BASE32 = '0123456789bcdefghjkmnpqrstuvwxyz';
  let idx = 0;
  let bit = 0;
  let even = true;
  let geohash = '';
  let latMin = -90;
  let latMax = 90;
  let lonMin = -180;
  let lonMax = 180;
  while (geohash.length < precision) {
    if (even) {
      const mid = (lonMin + lonMax) / 2;
      if (longitude > mid) {
        idx = idx * 2 + 1;
        lonMin = mid;
      } else {
        idx = idx * 2;
        lonMax = mid;
      }
    } else {
      const mid = (latMin + latMax) / 2;
      if (latitude > mid) {
        idx = idx * 2 + 1;
        latMin = mid;
      } else {
        idx = idx * 2;
        latMax = mid;
      }
    }
    even = !even;
    if (++bit === 5) {
      geohash += BASE32.charAt(idx);
      bit = 0;
      idx = 0;
    }
  }
  return geohash;
}

function parseArgs(argv = process.argv.slice(2)) {
  const flags = new Set();
  const opts = {};
  for (let i = 0; i < argv.length; i++) {
    const a = argv[i];
    if (a.startsWith('--')) {
      const eq = a.indexOf('=');
      if (eq !== -1) {
        opts[a.slice(2, eq)] = a.slice(eq + 1);
      } else if (argv[i + 1] && !argv[i + 1].startsWith('--')) {
        const key = a.slice(2);
        // boolean flags without value
        if (
          [
            'dry-run',
            'activate-br',
            'deactivate-others',
            'deactivate-usd',
            'force',
            'list',
            'help',
            'h',
            'yes',
            'include-joelson',
          ].includes(key)
        ) {
          flags.add(key);
        } else {
          opts[key] = argv[++i];
        }
      } else {
        flags.add(a.slice(2));
      }
    }
  }
  return { flags, opts, has: (k) => flags.has(k) || opts[k] === 'true' || opts[k] === true };
}

module.exports = {
  initFirestore,
  tryInitFirestore,
  parseArgs,
  resolveCredentialsPath,
  looksUsableServiceAccount,
  encodeGeohash,
};
