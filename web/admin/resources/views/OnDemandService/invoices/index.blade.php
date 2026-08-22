@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{ trans('lang.nfse_queue') }}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ondemand.bookings.index') }}">{{ trans('lang.booking_plural') }}</a></li>
                <li class="breadcrumb-item active">{{ trans('lang.nfse_queue') }}</li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div class="table-list">
            <div class="row">
                <div class="col-12">
                    <div class="card border">
                        <div class="card-header d-flex justify-content-between align-items-center border-0 top-title-section">
                            <div class="top-title-left">
                                <div class="d-flex align-items-center">
                                    <span class="icon mr-3"><img src="{{ asset('images/booking.png') }}"></span>
                                    <h3 class="mb-0">{{ trans('lang.nfse_queue') }}</h3>
                                    <span class="counter ml-3 total_count"></span>
                                </div>
                                <p class="mb-0 text-dark-2">{{ trans('lang.nfse_queue_text') }}</p>
                            </div>
                            <div class="select-box">
                                <select id="nfse_filter" class="form-control" onchange="reloadNfseTable()">
                                    <option value="with" selected>{{ trans('lang.nfse_filter_with') }}</option>
                                    <option value="without">{{ trans('lang.nfse_filter_without') }}</option>
                                    <option value="all">{{ trans('lang.nfse_filter_all') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive m-t-10">
                                <table id="nfseTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('lang.booking_id') }}</th>
                                            <th>{{ trans('lang.provider') }}</th>
                                            <th>{{ trans('lang.order_user_id') }}</th>
                                            <th>{{ trans('lang.status') }}</th>
                                            <th>{{ trans('lang.amount') }}</th>
                                            <th>{{ trans('lang.nfse_files') }}</th>
                                            <th>{{ trans('lang.created_at') }}</th>
                                            <th>{{ trans('lang.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
    var database = firebase.firestore();
    var id = "{{ $id }}";
    var currencyData = { symbol: 'R$', decimal_degits: 2, symbolAtRight: false };
    var table = null;
    var active_id = getCookie('section_id') || '';
    database.collection('currencies').where('isActive', '==', true).get().then(function (snapshots) {
        if (snapshots.docs.length) {
            currencyData = snapshots.docs[0].data() || currencyData;
        }
    });

    $(document).ready(function () {
        table = $('#nfseTable').DataTable({
            pageLength: 10,
            processing: false,
            serverSide: true,
            ajax: async function (data, callback) {
                $('#data-table_processing').show();
                var filter = $('#nfse_filter').val();
                var ref = database.collection('provider_orders');
                if (id) {
                    ref = ref.where('provider.author', '==', id);
                }
                try {
                    var snap = await ref.get();
                    var records = [];
                    snap.docs.forEach(function (doc) {
                        var row = doc.data();
                        var invoices = Array.isArray(row.invoices) ? row.invoices.filter(function (inv) { return inv && inv.url; }) : [];
                        if (filter === 'with' && !invoices.length) {
                            return;
                        }
                        if (filter === 'without' && invoices.length) {
                            return;
                        }
                        row.invoices = invoices;
                        records.push(row);
                    });
                    records.sort(function (a, b) {
                        var aTime = a.createdAt && a.createdAt.toDate ? a.createdAt.toDate().getTime() : 0;
                        var bTime = b.createdAt && b.createdAt.toDate ? b.createdAt.toDate().getTime() : 0;
                        return bTime - aTime;
                    });
                    $('.total_count').text(records.length);
                    var start = data.start;
                    var length = data.length;
                    var page = records.slice(start, start + length);
                    var rows = page.map(function (val) {
                        var editRoute = '{{ route('ondemand.bookings.edit', ':id') }}'.replace(':id', val.id);
                        var files = nfseInvoiceLinks(val.invoices);
                        if (!files) {
                            files = '<span class="text-muted">{{ trans('lang.nfse_missing') }}</span>';
                        }
                        var amount = formatCurrency((val.price != null ? val.price : 0), currencyData);
                        var created = '';
                        if (val.createdAt && val.createdAt.toDate) {
                            created = ArrowDateTime.formatDate(val.createdAt.toDate()) + '<br>' + ArrowDateTime.formatTime(val.createdAt.toDate());
                        }
                        var providerName = (val.provider && (val.provider.authorName || val.provider.firstName)) || val.authorName || '-';
                        return [
                            '<a href="' + editRoute + '">' + (val.id && val.id.length > 8 ? val.id.substring(0, 8) + '...' : val.id) + '</a>',
                            providerName,
                            val.authorName || '-',
                            val.status || '-',
                            amount,
                            files,
                            created,
                            '<span class="action-btn"><a href="' + editRoute + '" data-toggle="tooltip" title="{{ trans('lang.view') }}"><i class="mdi mdi-eye"></i></a></span>'
                        ];
                    });
                    $('#data-table_processing').hide();
                    callback({
                        draw: data.draw,
                        recordsTotal: records.length,
                        recordsFiltered: records.length,
                        data: rows
                    });
                } catch (err) {
                    console.error(err);
                    $('#data-table_processing').hide();
                    callback({ draw: data.draw, recordsTotal: 0, recordsFiltered: 0, data: [] });
                }
            },
            language: datatableLang,
            order: [[6, 'desc']]
        });
    });

    function reloadNfseTable() {
        if (table) {
            table.ajax.reload();
        }
    }
</script>
@endsection
