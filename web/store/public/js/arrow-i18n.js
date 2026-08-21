/**
 * Traduz valores persistidos em inglês (Firestore/API) apenas para exibição.
 * Nunca altere o valor gravado — use ArrowI18n.status() só na UI.
 */
(function (w) {
  var DEFAULT_STATUS = {
    'Order Placed': 'Pedido realizado',
    'Order Accepted': 'Pedido aceito',
    'Order Assigned': 'Pedido atribuído',
    'Order Ongoing': 'Em andamento',
    'Order Completed': 'Pedido concluído',
    'Order Cancelled': 'Pedido cancelado',
    'Order Canceled': 'Pedido cancelado',
    'Order Rejected': 'Pedido recusado',
    'Driver Pending': 'Motorista pendente',
    'Driver Rejected': 'Motorista recusou',
    'Driver Accepted': 'Motorista aceitou',
    'Order Shipped': 'Pedido enviado',
    'In Transit': 'Em trânsito',
    'InTransit': 'Em trânsito',
    'InProcess': 'Em andamento',
    'Delivered': 'Entregue',
    'Completed': 'Concluído',
    'Processing': 'Em processamento',
    'Pending': 'Pendente',
    'Order Delivered': 'Pedido entregue',
    'Hourly': 'Por hora',
    'Fixed': 'Fixo'
  };

  var DEFAULT_SERVICE_TYPE = {
    'rental-service': 'Aluguel',
    'Rental Service': 'Aluguel',
    'delivery-service': 'Entrega marketplace (várias lojas)',
    'Multivendor Delivery Service': 'Entrega marketplace (várias lojas)',
    'ondemand-service': 'Serviço sob demanda',
    'On Demand Service': 'Serviço sob demanda',
    'ecommerce-service': 'E-commerce',
    'Ecommerce Service': 'E-commerce',
    'parcel_delivery': 'Encomendas',
    'parcel-service': 'Encomendas',
    'parcel-delivery': 'Encomendas',
    'Parcel Delivery Service': 'Encomendas',
    'Parcel Service': 'Encomendas',
    'cab-service': 'Corridas',
    'Cab Service': 'Corridas'
  };

  w.ArrowI18n = w.ArrowI18n || {};
  w.ArrowI18n.statusMap = Object.assign({}, DEFAULT_STATUS, w.ArrowI18n.statusMap || {});
  w.ArrowI18n.serviceTypeMap = Object.assign({}, DEFAULT_SERVICE_TYPE, w.ArrowI18n.serviceTypeMap || {});
  w.ArrowI18n.searchPlaceholder = w.ArrowI18n.searchPlaceholder || 'Buscar aqui...';
  w.ArrowI18n.perHourSuffix = w.ArrowI18n.perHourSuffix || '/hora';
  w.ArrowI18n.viewDetails = w.ArrowI18n.viewDetails || 'Ver detalhes';

  w.ArrowI18n.status = function (s) {
    if (s == null || s === '') return '';
    var key = String(s).trim();
    return (this.statusMap && this.statusMap[key]) || key;
  };

  w.ArrowI18n.priceUnit = function (u) {
    return this.status(u);
  };

  /** Traduz flag ou name do tipo de serviço só para exibição. Não altere flags gravadas. */
  w.ArrowI18n.serviceType = function (s) {
    if (s == null || s === '') return '';
    var key = String(s).trim();
    return (this.serviceTypeMap && this.serviceTypeMap[key]) || key;
  };

  w.ArrowI18n.serviceTypeLabel = function (flag, name) {
    var map = this.serviceTypeMap || {};
    if (flag && map[flag]) return map[flag];
    if (name && map[name]) return map[name];
    return name || flag || '';
  };
})(window);
