/**
 * Prefixos e coleções do seed/cleanup e2e.
 * Só docs com estes IDs podem ser apagados pelo cleanup.
 */
const E2E_PREFIX = 'demo_e2e_';
const JOELSON_PREFIX = 'demo_joelson_';

const DEMO_COLLECTIONS = [
  'users',
  'vendors',
  'vendor_products',
  'vendor_orders',
  'vendor_categories',
  'providers_services',
  'provider_orders',
  'providers_workers',
  'wallet',
  'payouts',
  'withdraw_method',
  'items_review',
  'parcel_orders',
  'rides',
  'rental_orders',
  'documents_verify',
  'notifications',
  'chat',
];

function isDemoId(id, { includeJoelson = false } = {}) {
  const s = String(id || '');
  if (s.startsWith(E2E_PREFIX)) return true;
  if (includeJoelson && s.startsWith(JOELSON_PREFIX)) return true;
  return false;
}

module.exports = {
  E2E_PREFIX,
  JOELSON_PREFIX,
  DEMO_COLLECTIONS,
  isDemoId,
};
