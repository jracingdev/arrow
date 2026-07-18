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
  }
};
if (typeof moment !== 'undefined') {
  moment.locale('pt-br');
}