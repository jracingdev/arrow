/**
 * Traduz valores persistidos em inglês (Firestore/API) apenas para exibição.
 * Nunca altere o valor gravado — use ArrowI18n.status() / serviceTypeLabel() só na UI.
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

  /** name/flag canônicos em inglês — o que o backend e os ifs já comparam. */
  var SERVICE_TYPE_CATALOG = [
    { flag: 'rental-service', name: 'Rental Service' },
    { flag: 'delivery-service', name: 'Multivendor Delivery Service' },
    { flag: 'ondemand-service', name: 'On Demand Service' },
    { flag: 'ecommerce-service', name: 'Ecommerce Service' },
    { flag: 'parcel_delivery', name: 'Parcel Delivery Service' },
    { flag: 'cab-service', name: 'Cab Service' }
  ];

  w.ArrowI18n = w.ArrowI18n || {};
  w.ArrowI18n.statusMap = Object.assign({}, DEFAULT_STATUS, w.ArrowI18n.statusMap || {});
  w.ArrowI18n.serviceTypeMap = Object.assign({}, DEFAULT_SERVICE_TYPE, w.ArrowI18n.serviceTypeMap || {});
  w.ArrowI18n.searchPlaceholder = w.ArrowI18n.searchPlaceholder || 'Buscar aqui...';
  w.ArrowI18n.perHourSuffix = w.ArrowI18n.perHourSuffix || '/hora';
  w.ArrowI18n.viewDetails = w.ArrowI18n.viewDetails || 'Ver detalhes';

  function serviceTypeMapOf(ctx) {
    return (ctx && ctx.serviceTypeMap) || (w.ArrowI18n && w.ArrowI18n.serviceTypeMap) || DEFAULT_SERVICE_TYPE || {};
  }

  function canonicalServiceType(flag, name) {
    var i;
    var f = flag ? String(flag).trim() : '';
    var n = name ? String(name).trim() : '';
    for (i = 0; i < SERVICE_TYPE_CATALOG.length; i++) {
      if ((f && SERVICE_TYPE_CATALOG[i].flag === f) || (n && SERVICE_TYPE_CATALOG[i].name === n)) {
        return { flag: SERVICE_TYPE_CATALOG[i].flag, name: SERVICE_TYPE_CATALOG[i].name };
      }
    }
    return { flag: f, name: n };
  }

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
    var map = serviceTypeMapOf(this);
    return map[key] || key;
  };

  w.ArrowI18n.serviceTypeLabel = function (flag, name) {
    try {
      var map = serviceTypeMapOf(this);
      if (flag && map[flag]) return map[flag];
      if (name && map[name]) return map[name];
      return name || flag || '';
    } catch (e) {
      return name || flag || '';
    }
  };

  w.ArrowI18n.serviceTypeCatalog = function () {
    return SERVICE_TYPE_CATALOG.slice();
  };

  /**
   * Preenche um <select> de tipo de serviço.
   * Mantém o placeholder (option com value vazio). Não usa innerHTML.
   * value = name em inglês; atributo flag = flag; textContent = label PT-BR.
   * docs: array opcional de {flag, name} (ex.: Firestore `services`).
   */
  w.ArrowI18n.fillServiceTypeSelect = function (select, docs) {
    var el = select;
    if (!el) return;
    if (typeof el === 'string') {
      el = document.querySelector(el);
    } else if (el.jquery) {
      el = el[0];
    }
    if (!el || !el.options) return;

    var i;
    for (i = el.options.length - 1; i >= 0; i--) {
      if (el.options[i].value) {
        el.remove(i);
      }
    }

    var seenFlag = {};
    var seenName = {};
    var items = [];

    function addItem(name, flag) {
      var canonical = canonicalServiceType(flag, name);
      name = canonical.name;
      flag = canonical.flag;
      if (!name && !flag) return;
      if ((flag && seenFlag[flag]) || (name && seenName[name])) return;
      if (flag) seenFlag[flag] = true;
      if (name) seenName[name] = true;
      items.push({ name: name, flag: flag });
    }

    SERVICE_TYPE_CATALOG.forEach(function (d) {
      addItem(d.name, d.flag);
    });

    if (Array.isArray(docs)) {
      docs.forEach(function (d) {
        if (!d) return;
        addItem(d.name, d.flag);
      });
    }

    items.forEach(function (d) {
      var opt = document.createElement('option');
      opt.value = d.name || d.flag;
      if (d.flag) {
        opt.setAttribute('flag', d.flag);
      }
      opt.textContent = w.ArrowI18n.serviceTypeLabel(d.flag, d.name);
      el.appendChild(opt);
    });
  };
})(window);
