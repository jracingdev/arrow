/**
 * Contato/país BR + pagamentos BR no Firestore (merge) e garantia de currency BRL.
 *
 * Contato:
 *   settings/ContactUs → defaultCountry = Brazil (demais campos só se vazios/placeholder)
 *   settings/globalSettings → defaultCountryCode = +55
 *
 * Pagamentos (desabilita demos estrangeiros; Mercado Pago permanece habilitável):
 *   razorpaySettings.isEnabled = false (+ isWithdrawEnabled false)
 *   payStack.isEnable = false
 *   xendit_settings.enable = false
 *   stripeSettings.isEnabled = false (+ isWithdrawEnabled false)
 *   MercadoPago.isEnabled = true (não apaga chaves; apenas garante enableável)
 *
 * Currency:
 *   collection currencies → cria/atualiza BRL (isActive=true); opcionalmente desativa USD demo
 *
 * Uso no servidor:
 *   cd firebase/import-export && npm i
 *   node ../../scripts/seed-contact-payments-br.js
 *   node ../../scripts/seed-contact-payments-br.js --deactivate-usd
 *   node ../../scripts/seed-contact-payments-br.js --dry-run
 */
const { initFirestore, parseArgs } = require('./lib/firestore-admin');

const PAYMENT_DISABLE = [
  { doc: 'razorpaySettings', fields: { isEnabled: false, isWithdrawEnabled: false } },
  { doc: 'payStack', fields: { isEnable: false } },
  { doc: 'rentalPayStack', fields: { isEnable: false } },
  { doc: 'xendit_settings', fields: { enable: false } },
  { doc: 'stripeSettings', fields: { isEnabled: false, isWithdrawEnabled: false } },
];

const BRL_DOC_ID = 'brl_arrow_seed';
const BRL_PAYLOAD = {
  id: BRL_DOC_ID,
  code: 'BRL',
  name: 'Real Brasileiro',
  symbol: 'R$',
  country: 'Brazil',
  decimal_degits: 2,
  symbolAtRight: false,
  isActive: true,
};

function printHelp() {
  console.log(`Usage: node scripts/seed-contact-payments-br.js [options]

Options:
  --deactivate-usd   Define isActive=false nas currencies com code=USD
  --dry-run
  --help
`);
}

function isPlaceholderContact(value) {
  if (value == null) return true;
  const s = String(value).trim().toLowerCase();
  if (!s) return true;
  return (
    s === 'your address' ||
    s === 'india' ||
    s === 'info@yourdomain.com' ||
    s === '1234567890' ||
    s.includes('yourdomain')
  );
}

(async () => {
  const { has } = parseArgs();
  if (has('help') || has('h')) {
    printHelp();
    process.exit(0);
  }
  const dryRun = has('dry-run');
  const deactivateUsd = has('deactivate-usd');

  const { db } = initFirestore();
  const settings = db.collection('settings');

  // --- Contact / país ---
  const contactRef = settings.doc('ContactUs');
  const contactSnap = await contactRef.get();
  const contact = contactSnap.exists ? contactSnap.data() || {} : {};
  const contactPatch = { defaultCountry: 'Brazil' };
  if (isPlaceholderContact(contact.Email)) contactPatch.Email = '[EMAIL_CONTATO]';
  if (isPlaceholderContact(contact.Address) || isPlaceholderContact(contact.defaultAddress)) {
    contactPatch.Address = '[ENDERECO]';
    contactPatch.defaultAddress = '[ENDERECO]';
  }
  if (isPlaceholderContact(contact.Phone)) contactPatch.Phone = '[TELEFONE]';

  const globalRef = settings.doc('globalSettings');
  const globalPatch = { defaultCountryCode: '+55' };

  console.log('ContactUs patch:', contactPatch);
  console.log('globalSettings patch:', globalPatch);

  if (!dryRun) {
    await contactRef.set(contactPatch, { merge: true });
    await globalRef.set(globalPatch, { merge: true });
  }

  // --- Pagamentos ---
  for (const item of PAYMENT_DISABLE) {
    console.log('Disable', item.doc, item.fields);
    if (!dryRun) {
      await settings.doc(item.doc).set(item.fields, { merge: true });
    }
  }
  const mpPatch = { isEnabled: true };
  console.log('MercadoPago patch:', mpPatch, '(habilitável; preencha AccessToken/PublicKey no Admin)');
  if (!dryRun) {
    await settings.doc('MercadoPago').set(mpPatch, { merge: true });
  }

  // --- Currency BRL ---
  const currencies = db.collection('currencies');
  const existing = await currencies.where('code', '==', 'BRL').limit(5).get();
  if (existing.empty) {
    console.log('Criando currency BRL:', BRL_DOC_ID);
    if (!dryRun) {
      await currencies.doc(BRL_DOC_ID).set(BRL_PAYLOAD, { merge: true });
    }
  } else {
    for (const doc of existing.docs) {
      console.log('Ativando BRL existente:', doc.id);
      if (!dryRun) {
        await doc.ref.set(
          {
            isActive: true,
            code: 'BRL',
            name: doc.data().name || BRL_PAYLOAD.name,
            symbol: doc.data().symbol || 'R$',
            country: doc.data().country || 'Brazil',
            decimal_degits: doc.data().decimal_degits ?? 2,
            symbolAtRight: doc.data().symbolAtRight ?? false,
          },
          { merge: true }
        );
      }
    }
  }

  if (deactivateUsd) {
    const usd = await currencies.where('code', '==', 'USD').get();
    for (const doc of usd.docs) {
      console.log('Desativando USD:', doc.id);
      if (!dryRun) await doc.ref.set({ isActive: false }, { merge: true });
    }
  }

  console.log(dryRun ? 'Dry-run ok.' : 'Done. Contato/país + pagamentos + BRL atualizados.');
  process.exit(0);
})().catch((err) => {
  console.error(err.message || err);
  process.exit(1);
});
