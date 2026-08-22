/**
 * Testes de contrato Firestore: falha se um vertical do seed e2e estiver quebrado.
 *
 * Uso:
 *   cd firebase/import-export && npm i
 *   node ../../scripts/seed_platform_e2e.js
 *   node ../../scripts/firestore_contract_test.js
 *   node ../../scripts/firestore_contract_test.js --help
 *
 * Sem service account: sai 0 com SKIP (não quebra CI local).
 * Credenciais: GOOGLE_APPLICATION_CREDENTIALS, firebase/import-export/credentials.json
 * ou j-arrow-firebase-adminsdk-*.json na raiz (nunca commitar).
 */
const { tryInitFirestore, parseArgs } = require('./lib/firestore-admin');
const { E2E_PREFIX, JOELSON_PREFIX } = require('./lib/demo-ids');

function printHelp() {
  console.log(`Usage: node scripts/firestore_contract_test.js [--help]

Depois do seed (scripts/seed_platform_e2e.js), verifica:
  • providers_services publicados (demo_joelson_* ou demo_e2e_*)
  • vendor_products publicados demo_e2e_*
  • vendor_orders nos status placed/accepted/completed
  • wallet com transações demo_e2e_*
  • payouts com campos PIX (pixKey, pixKeyType)
  • cliente demo_e2e_customer com endereço no Rio e carteira

Sem credenciais: SKIP (exit 0). Sem seed: FAIL (exit 1).
`);
}

function fail(msg) {
  console.error('FAIL:', msg);
  process.exitCode = 1;
}

function ok(msg) {
  console.log('OK:', msg);
}

(async () => {
  const { has } = parseArgs();
  if (has('help') || has('h')) {
    printHelp();
    process.exit(0);
  }

  const init = tryInitFirestore();
  if (!init.ok) {
    console.log('SKIP: sem service account Firestore.');
    console.log(init.message);
    console.log('Como rodar: defina GOOGLE_APPLICATION_CREDENTIALS ou coloque credentials.json em firebase/import-export/');
    console.log('Depois: node scripts/seed_platform_e2e.js && node scripts/firestore_contract_test.js');
    process.exit(0);
  }

  const { db } = init;
  let failed = false;
  const mark = (cond, msgOk, msgFail) => {
    if (cond) ok(msgOk);
    else {
      failed = true;
      fail(msgFail);
    }
  };

  const customer = await db.collection('users').doc(`${E2E_PREFIX}customer`).get();
  mark(
    customer.exists,
    'cliente demo_e2e_customer existe',
    'cliente demo_e2e_customer ausente — rode seed_platform_e2e.js'
  );
  if (customer.exists) {
    const c = customer.data() || {};
    const addr = (c.shippingAddress && c.shippingAddress[0] && c.shippingAddress[0].address) || c.address || '';
    mark(/rio/i.test(String(addr)), 'endereço do cliente no Rio', `endereço inesperado: ${addr}`);
    mark(Number(c.wallet_amount) > 0, `carteira do cliente = ${c.wallet_amount}`, 'cliente sem saldo na carteira');
  }

  const vendorProducts = await db
    .collection('vendor_products')
    .where(init.admin.firestore.FieldPath.documentId(), '>=', E2E_PREFIX)
    .where(init.admin.firestore.FieldPath.documentId(), '<', E2E_PREFIX + '\uf8ff')
    .get();
  const publishedProducts = vendorProducts.docs.filter((d) => (d.data() || {}).publish === true);
  mark(
    publishedProducts.length >= 1,
    `${publishedProducts.length} produto(s) vendor publicados`,
    'nenhum vendor_products demo_e2e_* publicado'
  );

  const vendorOrders = {};
  for (const suffix of ['vendor_order_placed', 'vendor_order_accepted', 'vendor_order_completed']) {
    const doc = await db.collection('vendor_orders').doc(`${E2E_PREFIX}${suffix}`).get();
    vendorOrders[suffix] = doc.exists ? doc.data() : null;
    mark(doc.exists, `vendor_orders/${E2E_PREFIX}${suffix}`, `falta vendor_orders/${E2E_PREFIX}${suffix}`);
  }
  if (vendorOrders.vendor_order_placed) {
    mark(
      vendorOrders.vendor_order_placed.status === 'Order Placed',
      'pedido e-commerce em Order Placed',
      `status placed = ${vendorOrders.vendor_order_placed.status}`
    );
  }
  if (vendorOrders.vendor_order_completed) {
    mark(
      vendorOrders.vendor_order_completed.status === 'Order Completed',
      'pedido e-commerce Order Completed',
      `status completed = ${vendorOrders.vendor_order_completed.status}`
    );
  }

  const svcJoelson = await db.collection('providers_services').doc(`${JOELSON_PREFIX}svc_limpeza`).get();
  const svcAlt = await db
    .collection('providers_services')
    .where(init.admin.firestore.FieldPath.documentId(), '>=', E2E_PREFIX)
    .where(init.admin.firestore.FieldPath.documentId(), '<', E2E_PREFIX + '\uf8ff')
    .limit(5)
    .get();
  const publishedSvc =
    (svcJoelson.exists && (svcJoelson.data() || {}).publish === true) ||
    svcAlt.docs.some((d) => (d.data() || {}).publish === true);
  mark(publishedSvc, 'há providers_services publicado (joelson ou e2e)', 'nenhum providers_services publicado');

  const ondemandDone = await db.collection('provider_orders').doc(`${E2E_PREFIX}ondemand_completed`).get();
  const ondemandBroadcast = await db.collection('provider_orders').doc(`${E2E_PREFIX}ondemand_broadcast`).get();
  const joelsonDone = await db.collection('provider_orders').doc(`${JOELSON_PREFIX}order_completed`).get();
  const joelsonBroadcast = await db.collection('provider_orders').doc(`${JOELSON_PREFIX}order_placed`).get();
  mark(
    ondemandDone.exists || joelsonDone.exists,
    'pedido on-demand concluído presente',
    'falta pedido on-demand concluído (rode seed_provider_joelson.js e seed_platform_e2e.js)'
  );
  mark(
    ondemandBroadcast.exists || joelsonBroadcast.exists,
    'broadcast on-demand presente',
    'falta broadcast on-demand'
  );

  const wallet = await db
    .collection('wallet')
    .where(init.admin.firestore.FieldPath.documentId(), '>=', E2E_PREFIX)
    .where(init.admin.firestore.FieldPath.documentId(), '<', E2E_PREFIX + '\uf8ff')
    .get();
  mark(wallet.size >= 1, `${wallet.size} transação(ões) wallet demo_e2e_*`, 'nenhuma transação wallet demo_e2e_*');

  const payout = await db.collection('payouts').doc(`${E2E_PREFIX}payout_vendor_pix`).get();
  mark(payout.exists, 'payout PIX demo existe', 'falta payouts/demo_e2e_payout_vendor_pix');
  if (payout.exists) {
    const p = payout.data() || {};
    mark(Boolean(p.pixKey), `payout.pixKey = ${p.pixKey}`, 'payout sem pixKey');
    mark(Boolean(p.pixKeyType), `payout.pixKeyType = ${p.pixKeyType}`, 'payout sem pixKeyType');
    mark(String(p.withdrawMethod || '').toLowerCase() === 'pix', 'payout.withdrawMethod = pix', 'payout sem withdrawMethod pix');
    mark(String(p.currency || '').toUpperCase() === 'BRL', 'payout.currency = BRL', `payout.currency = ${p.currency}`);
  }

  const parcel = await db.collection('parcel_orders').doc(`${E2E_PREFIX}parcel_completed`).get();
  if (parcel.exists) ok('parcel_orders demo presente');
  else console.log('SKIP vertical encomenda: sem demo_e2e_parcel_completed (coleção/seção ausente no seed)');

  const ride = await db.collection('rides').doc(`${E2E_PREFIX}ride_completed`).get();
  if (ride.exists) ok('rides demo presente');
  else console.log('SKIP vertical táxi: sem demo_e2e_ride_completed (coleção/seção ausente no seed)');

  if (failed) {
    console.error('Contrato Firestore falhou. Rode o seed e confira as coleções acima.');
    process.exit(1);
  }
  console.log('Contrato Firestore ok.');
  process.exit(0);
})().catch((err) => {
  console.error(err);
  process.exit(1);
});
