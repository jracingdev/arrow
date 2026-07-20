/**
 * Backfill do campo `order` em todas as docs da collection Firestore `sections`
 * (valores estáveis 0..n-1) + opção de ativar módulos úteis para o Brasil.
 *
 * ============================================================================ulos BR (ativados com --activate-br):
 *   Restaurants, Food Grocery, Cab, Delivery Express (parcel), Rental,
 *   E-commerce, On Demand
 *
 * Uso no servidor (credentials em firebase/import-export/credentials.json):
 *   cd firebase/import-export && npm i
 *   node ../../scripts/backfill-sections-br.js
 *   node ../../scripts/backfill-sections-br.js --activate-br
 *   node ../../scripts/backfill-sections-br.js --activate-br --deactivate-others
 *   node ../../scripts/backfill-sections-br.js --dry-run
 *
 * Parâmetros CLI:
 *   --activate-br         Ativa (isActive=true) os módulos BR listados acima
 *                         e reordena: BR primeiro (ordem preferida), demais depois.
 *   --deactivate-others   Com --activate-br, desativa seções que não forem BR.
 *   --dry-run             Só imprime o plano, sem gravar.
 *   --help                Ajuda.
 *
 * Ordenação estável (sem --activate-br):
 *   order atual (nulls por último) → name → docId → atribui 0..n-1.
 */
const { initFirestore, parseArgs } = require('./lib/firestore-admin');

/** Ordem preferida dos módulos BR (nome canônico → matchers). */
const BR_MODULE_DEFS = [
  {
    key: 'restaurants',
    label: 'Restaurants',
    match: (d) => /restaurant/i.test(d.name || ''),
  },
  {
    key: 'food_grocery',
    label: 'Food Grocery',
    match: (d) => /food\s*grocery|grocery|mercado/i.test(d.name || ''),
  },
  {
    key: 'cab',
    label: 'Cab',
    match: (d) =>
      d.serviceTypeFlag === 'cab-service' || /^(cab|taxi)/i.test(d.name || '') || /\bcab\b/i.test(d.name || ''),
  },
  {
    key: 'delivery_express',
    label: 'Delivery Express',
    match: (d) =>
      d.serviceTypeFlag === 'parcel_delivery' ||
      /parcel|delivery\s*express|encomenda/i.test(d.name || ''),
  },
  {
    key: 'rental',
    label: 'Rental',
    match: (d) =>
      d.serviceTypeFlag === 'rental-service' || /rental|aluguel/i.test(d.name || ''),
  },
  {
    key: 'ecommerce',
    label: 'E-commerce',
    match: (d) =>
      d.serviceTypeFlag === 'ecommerce-service' ||
      /e-?commerce|fashion|loja online/i.test(d.name || ''),
  },
  {
    key: 'ondemand',
    label: 'On Demand',
    match: (d) =>
      d.serviceTypeFlag === 'ondemand-service' || /on\s*demand|sob demanda/i.test(d.name || ''),
  },
];

function classifyBr(d) {
  for (let i = 0; i < BR_MODULE_DEFS.length; i++) {
    if (BR_MODULE_DEFS[i].match(d)) return { index: i, def: BR_MODULE_DEFS[i] };
  }
  return null;
}

function printHelp() {
  console.log(`Usage: node scripts/backfill-sections-br.js [options]

Options:
  --activate-br          Activate BR modules and put them first in order
  --deactivate-others    With --activate-br, set isActive=false on non-BR sections
  --dry-run              Print plan only
  --help                 Show this help
`);
}

(async () => {
  const { has } = parseArgs();
  if (has('help') || has('h')) {
    printHelp();
    process.exit(0);
  }
  const dryRun = has('dry-run');
  const activateBr = has('activate-br');
  const deactivateOthers = has('deactivate-others');

  const { db } = initFirestore();
  const snap = await db.collection('sections').get();
  if (snap.empty) {
    console.log('Collection sections está vazia. Nada a fazer.');
    process.exit(0);
  }

  const docs = snap.docs.map((doc) => {
    const data = doc.data() || {};
    return {
      ref: doc.ref,
      id: doc.id,
      data,
      br: classifyBr(data),
    };
  });

  let ordered;
  if (activateBr) {
    const br = docs
      .filter((d) => d.br)
      .sort((a, b) => a.br.index - b.br.index || String(a.data.name || '').localeCompare(String(b.data.name || '')) || a.id.localeCompare(b.id));
    const rest = docs
      .filter((d) => !d.br)
      .sort((a, b) => {
        const ao = a.data.order;
        const bo = b.data.order;
        const aNull = ao === undefined || ao === null;
        const bNull = bo === undefined || bo === null;
        if (aNull !== bNull) return aNull ? 1 : -1;
        if (!aNull && !bNull && ao !== bo) return Number(ao) - Number(bo);
        return String(a.data.name || '').localeCompare(String(b.data.name || '')) || a.id.localeCompare(b.id);
      });
    ordered = [...br, ...rest];
  } else {
    ordered = [...docs].sort((a, b) => {
      const ao = a.data.order;
      const bo = b.data.order;
      const aNull = ao === undefined || ao === null;
      const bNull = bo === undefined || bo === null;
      if (aNull !== bNull) return aNull ? 1 : -1;
      if (!aNull && !bNull && ao !== bo) return Number(ao) - Number(bo);
      return String(a.data.name || '').localeCompare(String(b.data.name || '')) || a.id.localeCompare(b.id);
    });
  }

  console.log(`sections: ${ordered.length} | activateBr=${activateBr} | deactivateOthers=${deactivateOthers} | dryRun=${dryRun}`);
  let writes = 0;
  for (let i = 0; i < ordered.length; i++) {
    const item = ordered[i];
    const patch = { order: i };
    if (activateBr && item.br) {
      patch.isActive = true;
      // Nome amigável BR para parcel
      if (item.br.key === 'delivery_express' && /parcel/i.test(item.data.name || '')) {
        patch.name = 'Delivery Express';
      }
    }
    if (activateBr && deactivateOthers && !item.br) {
      patch.isActive = false;
    }

    const changed =
      item.data.order !== patch.order ||
      (patch.isActive !== undefined && item.data.isActive !== patch.isActive) ||
      (patch.name !== undefined && item.data.name !== patch.name);

    console.log({
      docId: item.id,
      name: patch.name || item.data.name,
      serviceTypeFlag: item.data.serviceTypeFlag,
      br: item.br ? item.br.label : null,
      fromOrder: item.data.order,
      toOrder: patch.order,
      isActive: patch.isActive !== undefined ? patch.isActive : item.data.isActive,
      changed,
    });

    if (changed && !dryRun) {
      await item.ref.set(patch, { merge: true });
      writes++;
    }
  }

  if (activateBr) {
    const missing = BR_MODULE_DEFS.filter((def) => !docs.some((d) => d.br && d.br.key === def.key));
    if (missing.length) {
      console.log(
        'AVISO: módulos BR não encontrados no Firestore (crie/renomeie no Admin):',
        missing.map((m) => m.label).join(', ')
      );
    }
  }

  console.log(dryRun ? `Dry-run ok. Escritas planejadas possíveis: revise changed=true.` : `Done. Writes: ${writes}`);
  process.exit(0);
})().catch((err) => {
  console.error(err.message || err);
  process.exit(1);
});
