window.ArrowDateTime = {
  formatDate: function (d) {
    if (!d) return '';
    if (!(d instanceof Date)) d = new Date(d);
    if (isNaN(d.getTime())) return '';
    var day = String(d.getDate()).padStart(2, '0');
    var month = String(d.getMonth() + 1).padStart(2, '0');
    return day + '-' + month + '-' + d.getFullYear();
  },
  formatTime: function (d) {
    if (!d) return '';
    if (!(d instanceof Date)) d = new Date(d);
    if (isNaN(d.getTime())) return '';
    return d.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit', hour12: false });
  },
  formatDateTime: function (d) {
    return this.formatDate(d) + ' ' + this.formatTime(d);
  },
  formatClock: function (timeStr) {
    if (timeStr == null || timeStr === '') return '';
    if (timeStr instanceof Date) return this.formatTime(timeStr);
    var s = String(timeStr).trim();
    var m = s.match(/^(\d{1,2}):(\d{2})(?::\d{2})?\s*(AM|PM)?$/i);
    if (!m) return s;
    var h = parseInt(m[1], 10);
    var mer = m[3] ? m[3].toUpperCase() : null;
    if (mer === 'PM' && h !== 12) h += 12;
    if (mer === 'AM' && h === 12) h = 0;
    return String(h).padStart(2, '0') + ':' + m[2];
  }
};
window.ArrowCep = {
  mask: function (value) {
    var digits = String(value == null ? '' : value).replace(/\D/g, '').slice(0, 8);
    if (digits.length > 5) {
      return digits.slice(0, 5) + '-' + digits.slice(5);
    }
    return digits;
  },
  bind: function (root) {
    var scope = root && root.querySelectorAll ? root : document;
    var nodes = scope.querySelectorAll('#address_zipcode, input[name="address_zipcode"], input[data-cep]');
    Array.prototype.forEach.call(nodes, function (el) {
      if (!el || el.dataset.cepBound === '1') return;
      el.dataset.cepBound = '1';
      el.setAttribute('inputmode', 'numeric');
      el.setAttribute('maxlength', '9');
      el.setAttribute('autocomplete', 'postal-code');
      if (!el.getAttribute('placeholder')) {
        el.setAttribute('placeholder', '00000-000');
      }
      el.addEventListener('input', function () {
        var start = el.selectionStart;
        var before = el.value;
        el.value = window.ArrowCep.mask(el.value);
        if (document.activeElement === el && start != null) {
          var delta = el.value.length - before.length;
          try { el.setSelectionRange(start + delta, start + delta); } catch (e) {}
        }
      });
      if (el.value) {
        el.value = window.ArrowCep.mask(el.value);
      }
    });
  }
};
if (typeof moment !== 'undefined') {
  moment.locale('pt-br');
}