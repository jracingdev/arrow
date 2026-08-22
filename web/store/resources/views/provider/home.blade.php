@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="container-fluid">
        <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display:none;">{{trans('lang.processing')}}</div>
        <div class="row page-titles">
            <div class="col-md-12">
                <h3 class="text-themecolor">{{ trans('lang.provider_dashboard') }}</h3>
            </div>
        </div>
        <div id="kyc_banner" class="alert alert-warning d-none">
            <a href="{{ route('vendors.document') }}" class="text-dark d-block">
                <strong>{{ trans('lang.document_verification') }}.</strong>
                <span id="kyc_banner_text">{{ trans('lang.provider_kyc_banner') }}</span>
            </a>
        </div>
        <div class="card mb-3">
            <div class="card-body d-flex flex-wrap align-items-center justify-content-between">
                <div>
                    <div class="font-weight-bold">{{ trans('lang.provider_online') }}</div>
                    <small class="text-muted" id="online_help">{{ trans('lang.provider_online_off') }}</small>
                </div>
                <label class="switch mb-0">
                    <input type="checkbox" id="provider_online">
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
        <div class="row business-analytics_list">
            <div class="col-sm-6 col-lg-3 mb-3">
                <a href="{{ route('provider.bookings') }}">
                    <div class="card"><div class="card-body">
                        <h2 class="h4 mb-1" id="count_placed">0</h2>
                        <p class="mb-0">{{ trans('lang.provider_new_bookings') }}</p>
                    </div></div>
                </a>
            </div>
            <div class="col-sm-6 col-lg-3 mb-3">
                <a href="{{ route('provider.bookings') }}">
                    <div class="card"><div class="card-body">
                        <h2 class="h4 mb-1" id="count_active">0</h2>
                        <p class="mb-0">{{ trans('lang.provider_today_bookings') }}</p>
                    </div></div>
                </a>
            </div>
            <div class="col-sm-6 col-lg-3 mb-3">
                <a href="{{ route('provider.services') }}">
                    <div class="card"><div class="card-body">
                        <h2 class="h4 mb-1" id="count_services">0</h2>
                        <p class="mb-0">{{ trans('lang.service_plural') }}</p>
                    </div></div>
                </a>
            </div>
            <div class="col-sm-6 col-lg-3 mb-3">
                <a href="{{ route('provider.workers') }}">
                    <div class="card"><div class="card-body">
                        <h2 class="h4 mb-1" id="count_workers">0</h2>
                        <p class="mb-0">{{ trans('lang.worker_plural') }}</p>
                    </div></div>
                </a>
            </div>
            <div class="col-sm-6 col-lg-3 mb-3">
                <a href="{{ route('wallettransaction.index') }}">
                    <div class="card"><div class="card-body">
                        <h2 class="h4 mb-1" id="wallet_balance">R$ 0,00</h2>
                        <p class="mb-0">{{ trans('lang.my_wallet') }}</p>
                    </div></div>
                </a>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h4 class="mb-0">{{ trans('lang.recent_orders') }}</h4></div>
            <div class="card-body table-responsive">
                <table class="table table-striped" id="recentBookings">
                    <thead>
                        <tr>
                            <th>{{ trans('lang.status') }}</th>
                            <th>{{ trans('lang.title') }}</th>
                            <th>{{ trans('lang.user') }}</th>
                            <th>{{ trans('lang.date') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    var database = firebase.firestore();
    var providerId = "<?php echo $id; ?>";
    var placed = ["Order Placed"];
    var active = ["Order Accepted", "Order Assigned", "Order Ongoing", "In Transit"];
    var statusLabel = {
        "Order Placed": "{{ trans('lang.placed_orders') }}",
        "Order Accepted": "{{ trans('lang.accept') }}",
        "Order Assigned": "{{ trans('lang.order_assigned') }}",
        "Order Ongoing": "{{ trans('lang.order_ongoing') }}",
        "In Transit": "{{ trans('lang.in_transit') }}",
        "Order Completed": "{{ trans('lang.provider_completed_bookings') }}",
        "Order Cancelled": "{{ trans('lang.provider_cancelled_bookings') }}",
        "Order Rejected": "{{ trans('lang.reject') }}",
        "Driver Rejected": "{{ trans('lang.reject') }}"
    };

    function formatBrl(value) {
        var n = Number(value) || 0;
        return 'R$ ' + n.toFixed(2).replace('.', ',');
    }

    $(function () {
        $("#data-table_processing").show();
        Promise.all([
            database.collection('users').doc(providerId).get(),
            database.collection('documents_verify').doc(providerId).get()
        ]).then(function (snaps) {
            var user = snaps[0].data() || {};
            var verified = user.isDocumentVerify === true;
            var auto = user.isAutoVerify === true;
            var verifyData = snaps[1].data() || {};
            var docs = Array.isArray(verifyData.documents) ? verifyData.documents : [];
            var rejected = docs.some(function (d) { return (d.status || '').toLowerCase() === 'rejected'; }) || !!verifyData.rejectReason;
            if (!verified && !auto) {
                $('#kyc_banner').removeClass('d-none');
                if (rejected) $('#kyc_banner_text').text("{{ trans('lang.provider_kyc_rejected') }}");
            }
            $('#provider_online').prop('checked', user.online === true);
            $('#online_help').text(user.online === true ? "{{ trans('lang.provider_online_on') }}" : "{{ trans('lang.provider_online_off') }}");
            $('#wallet_balance').text(formatBrl(user.wallet_amount));
        });
        $('#provider_online').on('change', function () {
            var online = $(this).is(':checked');
            $('#online_help').text(online ? "{{ trans('lang.provider_online_on') }}" : "{{ trans('lang.provider_online_off') }}");
            database.collection('users').doc(providerId).set({ online: online }, { merge: true });
        });
        Promise.all([
            database.collection('provider_orders').where('provider.author', '==', providerId).get(),
            database.collection('providers_services').where('author', '==', providerId).get(),
            database.collection('providers_workers').where('providerId', '==', providerId).get()
        ]).then(function (results) {
            var orders = results[0].docs.map(function (d) { return d.data(); });
            $('#count_placed').text(orders.filter(function (o) { return placed.indexOf(o.status) >= 0; }).length);
            $('#count_active').text(orders.filter(function (o) { return active.indexOf(o.status) >= 0; }).length);
            $('#count_services').text(results[1].size);
            $('#count_workers').text(results[2].size);
            orders.sort(function (a, b) {
                var ta = a.createdAt && a.createdAt.toDate ? a.createdAt.toDate().getTime() : 0;
                var tb = b.createdAt && b.createdAt.toDate ? b.createdAt.toDate().getTime() : 0;
                return tb - ta;
            });
            var html = '';
            orders.slice(0, 10).forEach(function (order) {
                var when = order.createdAt && order.createdAt.toDate ? ArrowDateTime.formatDate(order.createdAt.toDate()) : '';
                var title = (order.provider && order.provider.title) ? order.provider.title : '';
                var customer = order.author ? ((order.author.firstName || '') + ' ' + (order.author.lastName || '')) : '';
                var edit = "{{ url('provider/bookings/edit') }}/" + order.id;
                html += '<tr><td>' + (statusLabel[order.status] || order.status) + '</td><td>' + title + '</td><td>' + customer + '</td><td>' + when + '</td><td><a href="' + edit + '">{{ trans("lang.view_details") }}</a></td></tr>';
            });
            $('#recentBookings tbody').html(html || '<tr><td colspan="5">{{ trans("lang.no_record_found") }}</td></tr>');
            $("#data-table_processing").hide();
        }).catch(function () {
            $("#data-table_processing").hide();
        });
    });
</script>
@endsection
