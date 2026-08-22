/**
 * Seed e2e da plataforma Arrow (Brasil / BRL / pt-BR).
 *
 * Cria dados de caminhada em todos os verticais: cliente no Rio, e-commerce/food,
 * encomenda, táxi, on-demand (reusa serviços joelson), motorista, loja, carteira e PIX.
 * IDs sempre prefixados com demo_e2e_ — o cleanup só apaga estes docs.
 *
 * Uso:
 *   cd firebase/import-export && npm i
 *   node ../../scripts/seed_platform_e2e.js
 *   node ../../scripts/seed_platform_e2e.js --dry-run
 *   node ../../scripts/seed_platform_e2e.js --help
 *
 * Credenciais: GOOGLE_APPLICATION_CREDENTIALS, firebase/import-export/credentials.json
 * ou j-arrow-firebase-adminsdk-*.json na raiz (nunca commitar).
 *
 * Depois do seed: node ../../scripts/firestore_contract_test.js
 * Apagar: node ../../scripts/cleanup_demo_data.js --yes
 */
const { initFirestore, parseArgs, encodeGeohash } = require('./lib/firestore-admin');
const { E2E_PREFIX, JOELSON_PREFIX } = require('./lib/demo-ids');

const PREFIX = E2E_PREFIX;
const CUSTOMER_EMAIL = 'cliente.e2e.rio@arrow.test';
const VENDOR_EMAIL = 'loja.e2e.rio@arrow.test';
const DRIVER_EMAIL = 'motorista.e2e.rio@arrow.test';
const PROVIDER_EMAIL = 'joelson@joelson.com';

const RIO = {
  lat: -22.9847,
  lng: -43.1986,
  address: 'Rua Visconde de Pirajá, 414 — Ipanema, Rio de Janeiro — RJ',
  locality: 'Rio de Janeiro',
  cep: '22410-002',
  vendorAddress: 'Rua Barata Ribeiro, 111 — Copacabana, Rio de Janeiro — RJ',
  vendorLat: -22.9678,
  vendorLng: -43.1844,
  destAddress: 'Av. Atlântica, 1702 — Copacabana, Rio de Janeiro — RJ',
  destLat: -22.9711,
  destLng: -43.1822,
};

function printHelp() {
  console.log(`Usage: node scripts/seed_platform_e2e.js [--dry-run] [--help]

Cria casos reais (prefixo ${PREFIX}) para caminhar a plataforma:
  • cliente no Rio + carteira
  • loja/vendor + produtos + pedidos (realizado / aceito / concluído)
  • encomenda (parcel_orders) se a coleção ou seção existir
  • corrida de táxi (rides) se a coleção ou seção existir
  • on-demand: +1 concluído e +1 broadcast se faltarem (reusa serviços joelson)
  • motorista atribuído nos pedidos que exigem
  • carteira + payout PIX

Não apaga dados de produção. Para limpar: node scripts/cleanup_demo_data.js --yes
`);
}

function userSnippet(user, fallbackId) {
  return {
    id: user.id || user.userID || fallbackId,
    userID: user.id || user.userID || fallbackId,
    firstName: user.firstName || 'Cliente',
    lastName: user.lastName || 'E2E',
    email: user.email || '',
    phoneNumber: user.phoneNumber || '',
    profilePictureURL: user.profilePictureURL || '',
    role: user.role || 'customer',
    active: true,
  };
}

async function findSection(db, flags, nameRe) {
  const snap = await db.collection('sections').get();
  const docs = snap.docs.map((d) => ({ id: d.id, ...d.data() }));
  const flagSet = new Set(flags);
  return (
    docs.find((s) => s && s.isActive !== false && flagSet.has(s.serviceTypeFlag)) ||
    docs.find((s) => s && flagSet.has(s.serviceTypeFlag)) ||
    docs.find((s) => s && nameRe && nameRe.test(String(s.name || ''))) ||
    null
  );
}

async function collectionHasDocs(db, name) {
  try {
    const snap = await db.collection(name).limit(1).get();
    return !snap.empty;
  } catch (e) {
    return false;
  }
}

async function firstPublishedCategory(db, collection, sectionId, field) {
  if (!sectionId) return null;
  let snap = await db.collection(collection).where(field, '==', sectionId).limit(20).get();
  if (snap.empty) snap = await db.collection(collection).limit(20).get();
  const published = snap.docs
    .map((d) => ({ id: d.id, ...d.data() }))
    .filter((c) => c && c.id && c.publish !== false && !c.parentCategoryId);
  return published[0] || (snap.empty ? null : { id: snap.docs[0].id, ...snap.docs[0].data() });
}

(async () => {
  const { has } = parseArgs();
  if (has('help') || has('h')) {
    printHelp();
    process.exit(0);
  }
  const dryRun = has('dry-run');
  const { admin, db, credPath } = initFirestore();
  const FieldValue = admin.firestore.FieldValue;
  const Timestamp = admin.firestore.Timestamp;
  const GeoPoint = admin.firestore.GeoPoint;

  console.log('Credentials:', credPath);
  console.log(dryRun ? 'Dry-run: não grava.' : 'Gravando seed e2e da plataforma...');

  const now = Date.now();
  const hour = 60 * 60 * 1000;
  const ts = (offsetMs) => Timestamp.fromMillis(now + offsetMs);

  const rioPoint = new GeoPoint(RIO.lat, RIO.lng);
  const rioHash = encodeGeohash(RIO.lat, RIO.lng);
  const vendorPoint = new GeoPoint(RIO.vendorLat, RIO.vendorLng);
  const vendorHash = encodeGeohash(RIO.vendorLat, RIO.vendorLng);

  const customerId = `${PREFIX}customer`;
  const vendorUserId = `${PREFIX}vendor_user`;
  const vendorId = `${PREFIX}store`;
  const driverId = `${PREFIX}driver`;
  const productAcai = `${PREFIX}prod_acai`;
  const productFeijoada = `${PREFIX}prod_feijoada`;
  const orderPlaced = `${PREFIX}vendor_order_placed`;
  const orderAccepted = `${PREFIX}vendor_order_accepted`;
  const orderCompleted = `${PREFIX}vendor_order_completed`;
  const parcelId = `${PREFIX}parcel_completed`;
  const rideId = `${PREFIX}ride_completed`;
  const ondemandCompleted = `${PREFIX}ondemand_completed`;
  const ondemandBroadcast = `${PREFIX}ondemand_broadcast`;
  const walletCustomer = `${PREFIX}wallet_customer_topup`;
  const walletVendor = `${PREFIX}wallet_vendor_sale`;
  const walletDriver = `${PREFIX}wallet_driver_fare`;
  const payoutVendor = `${PREFIX}payout_vendor_pix`;
  const withdrawVendor = `${PREFIX}withdraw_vendor`;
  const withdrawDriver = `${PREFIX}withdraw_driver`;

  let joelsonCustomerSnap = await db.collection('users').doc(`${JOELSON_PREFIX}customer`).get();
  if (!joelsonCustomerSnap.exists) {
    const byEmail = await db.collection('users').where('email', '==', 'cliente.demo.joelson@arrow.test').limit(1).get();
    if (!byEmail.empty) joelsonCustomerSnap = byEmail.docs[0];
  }

  const customer = {
    id: customerId,
    userID: customerId,
    email: CUSTOMER_EMAIL,
    firstName: 'Ana',
    lastName: 'Ribeiro',
    role: 'customer',
    active: true,
    isActive: true,
    wallet_amount: 180,
    phoneNumber: '+5521987654321',
    countryCode: '+55',
    countryISOCode: 'BR',
    createdAt: FieldValue.serverTimestamp(),
    location: { latitude: RIO.lat, longitude: RIO.lng },
    latitude: RIO.lat,
    longitude: RIO.lng,
    address: RIO.address,
    cep: RIO.cep,
    shippingAddress: [
      {
        id: `${PREFIX}addr_casa`,
        address: RIO.address,
        addressAs: 'Casa',
        locality: RIO.locality,
        landmark: 'Perto da Praça Nossa Senhora da Paz',
        isDefault: true,
        cep: RIO.cep,
        location: { latitude: RIO.lat, longitude: RIO.lng },
      },
    ],
  };

  const vendorUser = {
    id: vendorUserId,
    userID: vendorUserId,
    email: VENDOR_EMAIL,
    firstName: 'Carla',
    lastName: 'Souza',
    role: 'vendor',
    vendorID: vendorId,
    active: true,
    isActive: true,
    isDocumentVerify: true,
    wallet_amount: 420,
    phoneNumber: '+5521977770001',
    countryCode: '+55',
    countryISOCode: 'BR',
    createdAt: FieldValue.serverTimestamp(),
    location: { latitude: RIO.vendorLat, longitude: RIO.vendorLng },
    latitude: RIO.vendorLat,
    longitude: RIO.vendorLng,
    userBankDetails: {
      pixKey: 'loja.e2e.rio@arrow.test',
      pixKeyType: 'email',
    },
  };

  const driver = {
    id: driverId,
    userID: driverId,
    email: DRIVER_EMAIL,
    firstName: 'Bruno',
    lastName: 'Costa',
    role: 'driver',
    active: true,
    isActive: true,
    isDocumentVerify: true,
    online: true,
    wallet_amount: 95,
    phoneNumber: '+5521966660002',
    countryCode: '+55',
    countryISOCode: 'BR',
    carName: 'Onix',
    carNumber: 'RJO2E26',
    vehicleType: 'Sedan',
    createdAt: FieldValue.serverTimestamp(),
    location: { latitude: RIO.vendorLat + 0.002, longitude: RIO.vendorLng + 0.001 },
    latitude: RIO.vendorLat + 0.002,
    longitude: RIO.vendorLng + 0.001,
    userBankDetails: {
      pixKey: '11988887777',
      pixKeyType: 'telefone',
    },
  };

  const ecomSection =
    (await findSection(db, ['ecommerce-service', 'delivery-service'], /e-?commerce|restaurant|food|comida/i)) || null;
  const parcelSection =
    (await findSection(db, ['parcel_delivery', 'parcel-service'], /parcel|encomenda|delivery\s*express/i)) || null;
  const cabSection = (await findSection(db, ['cab-service'], /^(cab|taxi)|táxi|taxi/i)) || null;
  const ondemandSection = (await findSection(db, ['ondemand-service'], /on\s*demand|sob demanda/i)) || null;

  const hasParcelCol = await collectionHasDocs(db, 'parcel_orders');
  const hasRidesCol = await collectionHasDocs(db, 'rides');
  const seedParcel = Boolean(parcelSection || hasParcelCol);
  const seedCab = Boolean(cabSection || hasRidesCol);

  const vendorCat = ecomSection
    ? await firstPublishedCategory(db, 'vendor_categories', ecomSection.id, 'section_id')
    : await firstPublishedCategory(db, 'vendor_categories', '', 'section_id');

  const vendor = {
    id: vendorId,
    author: vendorUserId,
    authorName: 'Carla Souza',
    title: 'Sabores do Rio — Ipanema',
    description: 'Comida carioca para delivery e retirada. Açaí, feijoada e pratos do dia.',
    phonenumber: vendorUser.phoneNumber,
    location: RIO.vendorAddress,
    latitude: RIO.vendorLat,
    longitude: RIO.vendorLng,
    coordinates: vendorPoint,
    g: { geohash: vendorHash, geopoint: vendorPoint },
    reststatus: true,
    publish: true,
    hidephotos: false,
    photos: [],
    photo: '',
    reviewsCount: 12,
    reviewsSum: 56,
    walletAmount: 420,
    section_id: (ecomSection && ecomSection.id) || '',
    categoryID: vendorCat && vendorCat.id ? [vendorCat.id] : [],
    categoryTitle: vendorCat && vendorCat.title ? [vendorCat.title] : ['Comida'],
    dine_in_active: false,
    isSelfDelivery: false,
    createdAt: FieldValue.serverTimestamp(),
    filters: { GoodforDineIn: 'No', GoodforBreakfast: 'Yes', VegetarianFriendly: 'Yes' },
  };

  const products = [
    {
      id: productAcai,
      vendorID: vendorId,
      name: 'Açaí 500ml com granola',
      description: 'Açaí batido na hora, granola e banana. Entrega em Ipanema e Copacabana.',
      price: '28',
      disPrice: '0',
      publish: true,
      veg: true,
      nonveg: false,
      quantity: 40,
      photos: [],
      photo: '',
      reviewsCount: 8,
      reviewsSum: 38,
      section_id: vendor.section_id,
      categoryID: vendorCat && vendorCat.id ? vendorCat.id : '',
      takeawayOption: true,
      createdAt: FieldValue.serverTimestamp(),
    },
    {
      id: productFeijoada,
      vendorID: vendorId,
      name: 'Feijoada completa',
      description: 'Feijoada carioca com arroz, couve, farofa e laranja. Serve 1 pessoa.',
      price: '52',
      disPrice: '0',
      publish: true,
      veg: false,
      nonveg: true,
      quantity: 18,
      photos: [],
      photo: '',
      reviewsCount: 5,
      reviewsSum: 23,
      section_id: vendor.section_id,
      categoryID: vendorCat && vendorCat.id ? vendorCat.id : '',
      takeawayOption: true,
      createdAt: FieldValue.serverTimestamp(),
    },
  ];

  const customerAuthor = userSnippet(customer, customerId);
  const vendorEmbed = {
    id: vendorId,
    author: vendorUserId,
    authorName: vendor.authorName,
    title: vendor.title,
    location: vendor.location,
    latitude: vendor.latitude,
    longitude: vendor.longitude,
    phonenumber: vendor.phonenumber,
    photo: '',
    section_id: vendor.section_id,
  };
  const driverEmbed = userSnippet(driver, driverId);
  const addr = {
    id: `${PREFIX}addr_casa`,
    address: RIO.address,
    addressAs: 'Casa',
    locality: RIO.locality,
    landmark: 'Perto da Praça Nossa Senhora da Paz',
    isDefault: true,
    cep: RIO.cep,
    location: { latitude: RIO.lat, longitude: RIO.lng },
  };
  const cartAcai = {
    id: productAcai,
    name: products[0].name,
    photo: '',
    price: '28',
    discountPrice: '0',
    vendorID: vendorId,
    quantity: 1,
    extras: [],
    extras_price: '0',
    category_id: products[0].categoryID,
  };
  const cartFeijoada = {
    id: productFeijoada,
    name: products[1].name,
    photo: '',
    price: '52',
    discountPrice: '0',
    vendorID: vendorId,
    quantity: 1,
    extras: [],
    extras_price: '0',
    category_id: products[1].categoryID,
  };

  const vendorOrders = [
    {
      id: orderPlaced,
      address: addr,
      author: customerAuthor,
      authorID: customerId,
      vendor: vendorEmbed,
      vendorID: vendorId,
      products: [cartAcai],
      status: 'Order Placed',
      payment_method: 'pix',
      createdAt: ts(-20 * 60 * 1000),
      notes: 'Sem granola extra — caso e2e (pedido realizado).',
      takeAway: false,
      discount: 0,
      deliveryCharge: '8',
      tip_amount: '0',
      adminCommission: '0',
      adminCommissionType: 'fixed',
      section_id: vendor.section_id,
      driverID: '',
      rejectedByDrivers: [],
    },
    {
      id: orderAccepted,
      address: addr,
      author: customerAuthor,
      authorID: customerId,
      vendor: vendorEmbed,
      vendorID: vendorId,
      products: [cartFeijoada],
      status: 'Order Accepted',
      payment_method: 'wallet',
      createdAt: ts(-2 * hour),
      notes: 'Feijoada aceita pela loja — caso e2e.',
      takeAway: false,
      discount: 0,
      deliveryCharge: '8',
      tip_amount: '5',
      adminCommission: '0',
      adminCommissionType: 'fixed',
      section_id: vendor.section_id,
      driverID: driverId,
      driver: driverEmbed,
      rejectedByDrivers: [],
    },
    {
      id: orderCompleted,
      address: addr,
      author: customerAuthor,
      authorID: customerId,
      vendor: vendorEmbed,
      vendorID: vendorId,
      products: [cartAcai, cartFeijoada],
      status: 'Order Completed',
      payment_method: 'cod',
      createdAt: ts(-26 * hour),
      notes: 'Pedido concluído e pago em dinheiro — caso e2e.',
      takeAway: false,
      discount: 0,
      deliveryCharge: '8',
      tip_amount: '10',
      adminCommission: '0',
      adminCommissionType: 'fixed',
      section_id: vendor.section_id,
      driverID: driverId,
      driver: driverEmbed,
      rejectedByDrivers: [],
    },
  ];

  const parcelOrder = seedParcel
    ? {
        id: parcelId,
        author: customerAuthor,
        authorID: customerId,
        createdAt: ts(-8 * hour),
        status: 'Order Completed',
        payment_method: 'pix',
        subTotal: '35',
        discount: '0',
        discountType: '',
        discountLabel: '0',
        distance: '4.2',
        note: 'Envelope com documentos — caso e2e.',
        receiverNote: 'Deixar na portaria.',
        parcelWeight: 'Até 1 kg',
        parcelWeightCharge: '12',
        parcelType: 'Documento',
        paymentCollectByReceiver: false,
        sender: { address: RIO.address, name: 'Ana Ribeiro', phone: customer.phoneNumber },
        receiver: { address: RIO.destAddress, name: 'Carla Souza', phone: vendorUser.phoneNumber },
        senderLatLong: { latitude: RIO.lat, longitude: RIO.lng },
        receiverLatLong: { latitude: RIO.destLat, longitude: RIO.destLng },
        senderPickupDateTime: ts(-8 * hour),
        receiverPickupDateTime: ts(-7 * hour),
        sectionId: (parcelSection && parcelSection.id) || '',
        parcelCategoryID: '',
        parcelImages: [],
        driverId: driverId,
        driver: driverEmbed,
        rejectedByDrivers: [],
        sourcePoint: { geohash: rioHash, geopoint: rioPoint },
        destinationPoint: {
          geohash: encodeGeohash(RIO.destLat, RIO.destLng),
          geopoint: new GeoPoint(RIO.destLat, RIO.destLng),
        },
        adminCommission: '0',
        adminCommissionType: 'fixed',
        platformFee: '0',
        isSchedule: false,
      }
    : null;

  const rideOrder = seedCab
    ? {
        id: rideId,
        status: 'Order Completed',
        author: customerAuthor,
        authorID: customerId,
        driver: driverEmbed,
        driverId: driverId,
        createdAt: ts(-5 * hour),
        scheduleDateTime: ts(-5 * hour),
        sourceLocationName: RIO.address,
        destinationLocationName: RIO.destAddress,
        sourceLocation: { latitude: RIO.lat, longitude: RIO.lng },
        destinationLocation: { latitude: RIO.destLat, longitude: RIO.destLng },
        distance: '3.8',
        duration: '18',
        subTotal: '28',
        paymentMethod: 'pix',
        paymentStatus: true,
        tip_amount: '5',
        rideType: 'city',
        otpCode: '3142',
        sectionId: (cabSection && cabSection.id) || '',
        rejectedByDrivers: [],
        adminCommission: '0',
        adminCommissionType: 'fixed',
        platformFee: '0',
        roundTrip: false,
        discount: '0',
        vehicleType: { name: 'Sedan', id: `${PREFIX}vehicle_sedan`, isActive: true, capacity: '4' },
      }
    : null;

  const providerSnap = await db.collection('users').where('email', '==', PROVIDER_EMAIL).limit(5).get();
  const providerDoc =
    providerSnap.docs.find((d) => (d.data().role || '') === 'provider') || providerSnap.docs[0] || null;
  const provider = providerDoc ? providerDoc.data() || {} : null;
  const providerUid = provider ? provider.id || provider.userID || providerDoc.id : null;

  const existingJoelsonCompleted = await db.collection('provider_orders').doc(`${JOELSON_PREFIX}order_completed`).get();
  const existingJoelsonBroadcast = await db.collection('provider_orders').doc(`${JOELSON_PREFIX}order_placed`).get();

  let limpezaService = null;
  let eletricaService = null;
  if (providerUid) {
    const limpezaSnap = await db.collection('providers_services').doc(`${JOELSON_PREFIX}svc_limpeza`).get();
    const eletricaSnap = await db.collection('providers_services').doc(`${JOELSON_PREFIX}svc_eletrica`).get();
    limpezaService = limpezaSnap.exists ? limpezaSnap.data() : null;
    eletricaService = eletricaSnap.exists ? eletricaSnap.data() : null;
  }

  const ondemandOrders = [];
  if (providerUid && (limpezaService || eletricaService)) {
    const svcCompleted = eletricaService || limpezaService;
    const svcBroadcast = limpezaService || eletricaService;
    const customerAddr = {
      id: `${PREFIX}addr_casa`,
      address: RIO.address,
      addressAs: 'Casa',
      locality: RIO.locality,
      isDefault: true,
      location: { latitude: RIO.lat, longitude: RIO.lng },
    };
    ondemandOrders.push({
      id: ondemandCompleted,
      authorID: customerId,
      author: customerAuthor,
      sectionId: (ondemandSection && ondemandSection.id) || svcCompleted.sectionId || '',
      address: customerAddr,
      createdAt: ts(-30 * hour),
      notes: 'Reparo hidráulico concluído no Rio — caso e2e extra.',
      quantity: 1,
      payment_method: 'pix',
      status: 'Order Completed',
      provider: svcCompleted,
      scheduleDateTime: ts(-30 * hour),
      startTime: ts(-31 * hour),
      endTime: ts(-30 * hour),
      paymentStatus: true,
      extraPaymentStatus: true,
      workerId: `${JOELSON_PREFIX}worker_1`,
      dispatchMode: 'direct',
      requestedCategoryId: svcCompleted.categoryId || '',
      radiusKm: 25,
      rejectedBy: [],
      otp: '5521',
      discount: '0',
      extraCharges: '0',
      adminCommission: '0',
      adminCommissionType: 'fixed',
      platformFee: '0',
    });
    ondemandOrders.push({
      id: ondemandBroadcast,
      authorID: customerId,
      author: customerAuthor,
      sectionId: (ondemandSection && ondemandSection.id) || svcBroadcast.sectionId || '',
      address: customerAddr,
      createdAt: FieldValue.serverTimestamp(),
      notes: 'Chamado broadcast Ipanema — aceitar no app Prestador (caso e2e).',
      quantity: 1,
      payment_method: 'cod',
      status: 'Order Placed',
      provider: { ...svcBroadcast, author: '', authorName: '', phoneNumber: '' },
      scheduleDateTime: ts(25 * 60 * 1000),
      paymentStatus: false,
      extraPaymentStatus: true,
      workerId: '',
      dispatchMode: 'broadcast',
      requestedCategoryId: svcBroadcast.categoryId || '',
      radiusKm: 25,
      rejectedBy: [],
      offeredTo: [],
      dispatchOfferedTo: '',
      dispatchExpiresAt: ts(10 * 60 * 1000),
      otp: '8821',
      discount: '0',
      extraCharges: '0',
      adminCommission: '0',
      adminCommissionType: 'fixed',
      platformFee: '0',
    });
  }

  const walletTx = [
    {
      id: walletCustomer,
      user_id: customerId,
      amount: 180,
      date: ts(-48 * hour),
      payment_method: 'pix',
      transactionUser: 'customer',
      isTopUp: true,
      order_id: '',
      note: 'Recarga PIX — caso e2e',
      payment_status: 'success',
      serviceType: 'wallet',
    },
    {
      id: walletVendor,
      user_id: vendorUserId,
      amount: 80,
      date: ts(-24 * hour),
      payment_method: 'wallet',
      transactionUser: 'vendor',
      isTopUp: true,
      order_id: orderCompleted,
      note: 'Venda e-commerce creditada',
      payment_status: 'success',
      serviceType: 'ecommerce-service',
    },
    {
      id: walletDriver,
      user_id: driverId,
      amount: 28,
      date: ts(-5 * hour),
      payment_method: 'wallet',
      transactionUser: 'driver',
      isTopUp: true,
      order_id: rideId,
      note: 'Corrida táxi creditada',
      payment_status: 'success',
      serviceType: 'cab-service',
    },
  ];

  const payout = {
    id: payoutVendor,
    vendorID: vendorUserId,
    amount: '50',
    note: 'Repasse PIX e2e — Sabores do Rio',
    paidDate: ts(-3 * hour),
    paymentStatus: 'Pending',
    role: 'vendor',
    withdrawMethod: 'pix',
    pixKey: 'loja.e2e.rio@arrow.test',
    pixKeyType: 'email',
    currency: 'BRL',
  };

  const withdraws = [
    {
      id: withdrawVendor,
      userId: vendorUserId,
      pix: { name: 'PIX', chave: 'loja.e2e.rio@arrow.test', tipo: 'email', enable: true },
      pixKey: 'loja.e2e.rio@arrow.test',
      pixKeyType: 'email',
    },
    {
      id: withdrawDriver,
      userId: driverId,
      pix: { name: 'PIX', chave: '11988887777', tipo: 'telefone', enable: true },
      pixKey: '11988887777',
      pixKeyType: 'telefone',
    },
  ];

  const written = {
    customer: customerId,
    vendorUser: vendorUserId,
    vendor: vendorId,
    driver: driverId,
    products: products.map((p) => p.id),
    vendorOrders: vendorOrders.map((o) => o.id),
    parcel: parcelOrder ? parcelId : null,
    cab: rideOrder ? rideId : null,
    ondemand: ondemandOrders.map((o) => o.id),
    wallet: walletTx.map((t) => t.id),
    payout: payoutVendor,
    skipped: {
      parcel: !seedParcel,
      cab: !seedCab,
      ondemand: ondemandOrders.length === 0,
      joelsonCompletedExists: existingJoelsonCompleted.exists,
      joelsonBroadcastExists: existingJoelsonBroadcast.exists,
    },
    sections: {
      ecommerce: ecomSection && ecomSection.id,
      parcel: parcelSection && parcelSection.id,
      cab: cabSection && cabSection.id,
      ondemand: ondemandSection && ondemandSection.id,
    },
  };

  if (dryRun) {
    console.log('Dry-run payload ids:', JSON.stringify(written, null, 2));
    process.exit(0);
  }

  const writes = [];
  const set = (col, id, data) => writes.push({ col, id, data });

  set('users', customerId, customer);
  set('users', vendorUserId, vendorUser);
  set('users', driverId, driver);
  set('vendors', vendorId, vendor);
  products.forEach((p) => set('vendor_products', p.id, p));
  vendorOrders.forEach((o) => set('vendor_orders', o.id, o));
  if (parcelOrder) set('parcel_orders', parcelId, parcelOrder);
  if (rideOrder) set('rides', rideId, rideOrder);
  ondemandOrders.forEach((o) => set('provider_orders', o.id, o));
  walletTx.forEach((t) => set('wallet', t.id, t));
  set('payouts', payoutVendor, payout);
  withdraws.forEach((w) => set('withdraw_method', w.id, w));

  const CHUNK = 400;
  for (let i = 0; i < writes.length; i += CHUNK) {
    const batch = db.batch();
    writes.slice(i, i + CHUNK).forEach((w) => {
      batch.set(db.collection(w.col).doc(w.id), w.data, { merge: true });
    });
    await batch.commit();
  }

  console.log('Seed e2e ok.');
  console.log(JSON.stringify(written, null, 2));
  if (!seedParcel) console.log('Aviso: parcel_orders / seção encomenda ausentes — vertical pulado.');
  if (!seedCab) console.log('Aviso: rides / seção táxi ausentes — vertical pulado.');
  if (ondemandOrders.length === 0) {
    console.log('Aviso: prestador joelson ou serviços demo_joelson_* ausentes — rode seed_provider_joelson.js.');
  }
  process.exit(0);
})().catch((err) => {
  console.error(err);
  process.exit(1);
});
