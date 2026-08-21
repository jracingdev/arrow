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

  w.ArrowI18n = w.ArrowI18n || {};
  w.ArrowI18n.statusMap = Object.assign({}, DEFAULT_STATUS, w.ArrowI18n.statusMap || {});
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
})(window);
