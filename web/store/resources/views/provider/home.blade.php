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
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">{{ trans('lang.provider_nearby') }}</h4>
                <a href="{{ route('provider.bookings') }}">{{ trans('lang.view_details') }}</a>
            </div>
            <div class="card-body table-responsive">
                <p class="text-muted mb-2" id="nearby_hint"></p>
                <div class="error_top"></div>
                <table class="table table-striped mb-0" id="nearbyBookings">
                    <thead>
                        <tr>
                            <th>{{ trans('lang.service_plural') }}</th>
                            <th>{{ trans('lang.user') }}</th>
                            <th>{{ trans('lang.date') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
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
    var providerUser = null;
    var myServices = [];
    var providerOnline = false;
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
    function isValidCoord(lat, lng) {
        if (lat == null || lng == null || isNaN(lat) || isNaN(lng)) return false;
        if (Math.abs(lat) < 0.2 && Math.abs(lng) < 0.2) return false;
        return lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180;
    }
    function haversineKm(fromLat, fromLng, toLat, toLng) {
        if (!isValidCoord(fromLat, fromLng) || !isValidCoord(toLat, toLng)) return null;
        var R = 6371.0088;
        var dLat = (toLat - fromLat) * Math.PI / 180;
        var dLng = (toLng - fromLng) * Math.PI / 180;
        var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(fromLat * Math.PI / 180) * Math.cos(toLat * Math.PI / 180) * Math.sin(dLng / 2) * Math.sin(dLng / 2);
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }
    function customerLat(order) {
        var addr = order.address || {};
        var loc = addr.location || {};
        return parseFloat(loc.latitude != null ? loc.latitude : addr.latitude);
    }
    function customerLng(order) {
        var addr = order.address || {};
        var loc = addr.location || {};
        return parseFloat(loc.longitude != null ? loc.longitude : addr.longitude);
    }
    function addressLine(order) {
        var addr = order.address || {};
        return addr.address || addr.locality || addr.addressAsString || '';
    }
    function providerLatLng() {
        var loc = (providerUser && providerUser.location) || {};
        var lat = parseFloat(loc.latitude != null ? loc.latitude : (providerUser && providerUser.latitude));
        var lng = parseFloat(loc.longitude != null ? loc.longitude : (providerUser && providerUser.longitude));
        return { lat: lat, lng: lng };
    }
    function matchingService(order) {
        var requested = order.requestedCategoryId || (order.provider && order.provider.categoryId) || '';
        var published = myServices.filter(function (s) { return s.publish === true; });
        if (requested) {
            var match = published.filter(function (s) { return s.categoryId === requested; });
            if (match.length) return match[0];
        }
        return published[0] || null;
    }
    function filterNearby(docs) {
        var now = Date.now();
        var lookback = 6 * 60 * 60 * 1000;
        var categories = myServices.filter(function (s) { return s.publish; }).map(function (s) { return s.categoryId; }).filter(Boolean);
        var me = providerLatLng();
        return docs.filter(function (order) {
            if (order.status !== 'Order Placed') return false;
            if (order.provider && order.provider.author) return false;
            if (Array.isArray(order.rejectedBy) && order.rejectedBy.indexOf(providerId) >= 0) return false;
            if (order.createdAt && order.createdAt.toDate && (now - order.createdAt.toDate().getTime()) > lookback) return false;
            var requested = order.requestedCategoryId || (order.provider && order.provider.categoryId) || '';
            if (requested && categories.length && categories.indexOf(requested) < 0) return false;
            var radius = parseFloat(order.radiusKm) || 25;
            var distance = haversineKm(me.lat, me.lng, customerLat(order), customerLng(order));
            if (distance != null && distance > radius) return false;
            return true;
        });
    }
    function renderNearby(list) {
        if (!providerOnline) {
            $('#nearby_hint').text("{{ trans('lang.provider_nearby_offline') }}");
        } else {
            $('#nearby_hint').text('');
        }
        var html = '';
        list.forEach(function (order) {
            var when = order.createdAt && order.createdAt.toDate ? ArrowDateTime.formatDate(order.createdAt.toDate()) : '';
            var title = (order.provider && order.provider.title) ? order.provider.title : '';
            var customer = order.author ? ((order.author.firstName || '') + ' ' + (order.author.lastName || '')) : '{{ trans("lang.user") }}';
            var edit = "{{ url('provider/bookings/edit') }}/" + order.id;
            html += '<tr><td>' + title + '</td><td>' + customer + '</td><td>' + when + '</td><td>';
            html += '<button type="button" class="btn btn-sm btn-success nearby-accept mr-1" data-id="' + order.id + '">{{ trans("lang.accept") }}</button>';
            html += '<button type="button" class="btn btn-sm btn-danger nearby-reject mr-1" data-id="' + order.id + '">{{ trans("lang.reject") }}</button>';
            html += '<a href="' + edit + '">{{ trans("lang.view_details") }}</a></td></tr>';
        });
        $('#nearbyBookings tbody').html(html || '<tr><td colspan="4">{{ trans("lang.provider_nearby_empty") }}</td></tr>');
    }
    function acceptBroadcast(orderId, order) {
        var service = matchingService(order || {});
        if (!service || !providerUser) {
            $('.error_top').html('<p>{{ trans("lang.provider_nearby_need_service") }}</p>').show();
            return;
        }
        var snapshot = Object.assign({}, service);
        snapshot.author = providerId;
        snapshot.authorName = ((providerUser.firstName || '') + ' ' + (providerUser.lastName || '')).trim();
        snapshot.authorProfilePic = providerUser.profilePictureURL || '';
        snapshot.phoneNumber = providerUser.phoneNumber || service.phoneNumber || '';
        var ref = database.collection('provider_orders').doc(orderId);
        database.runTransaction(function (tx) {
            return tx.get(ref).then(function (snap) {
                var data = snap.data();
                if (!data || data.status !== 'Order Placed' || (data.provider && data.provider.author)) {
                    throw new Error('taken');
                }
                tx.update(ref, {
                    provider: snapshot,
                    status: 'Order Accepted',
                    dispatchAcceptedAt: firebase.firestore.Timestamp.now()
                });
            });
        }).then(function () {
            window.location = "{{ url('provider/bookings/edit') }}/" + orderId;
        }).catch(function (err) {
            var msg = (err && err.message === 'taken') ? '{{ trans("lang.provider_nearby_taken") }}' : (err && err.message ? err.message : err);
            $('.error_top').html('<p>' + msg + '</p>').show();
        });
    }

    $(function () {
        $("#data-table_processing").show();
        var nearbyCache = [];
        Promise.all([
            database.collection('users').doc(providerId).get(),
            database.collection('documents_verify').doc(providerId).get(),
            database.collection('providers_services').where('author', '==', providerId).get()
        ]).then(function (snaps) {
            providerUser = snaps[0].data() || {};
            myServices = snaps[2].docs.map(function (d) { return d.data(); });
            var verified = providerUser.isDocumentVerify === true;
            var auto = providerUser.isAutoVerify === true;
            var verifyData = snaps[1].data() || {};
            var docs = Array.isArray(verifyData.documents) ? verifyData.documents : [];
            var rejected = docs.some(function (d) { return (d.status || '').toLowerCase() === 'rejected'; }) || !!verifyData.rejectReason;
            if (!verified && !auto) {
                $('#kyc_banner').removeClass('d-none');
                if (rejected) $('#kyc_banner_text').text("{{ trans('lang.provider_kyc_rejected') }}");
            }
            providerOnline = providerUser.online === true;
            $('#provider_online').prop('checked', providerOnline);
            $('#online_help').text(providerOnline ? "{{ trans('lang.provider_online_on') }}" : "{{ trans('lang.provider_online_off') }}");
            $('#wallet_balance').text(formatBrl(providerUser.wallet_amount));
            database.collection('provider_orders').where('dispatchMode', '==', 'broadcast').onSnapshot(function (snap) {
                nearbyCache = snap.docs.map(function (d) {
                    var data = d.data() || {};
                    if (!data.id) data.id = d.id;
                    return data;
                });
                renderNearby(providerOnline ? filterNearby(nearbyCache) : []);
            });
        });
        $('#provider_online').on('change', function () {
            providerOnline = $(this).is(':checked');
            $('#online_help').text(providerOnline ? "{{ trans('lang.provider_online_on') }}" : "{{ trans('lang.provider_online_off') }}");
            database.collection('users').doc(providerId).set({ online: providerOnline }, { merge: true });
            renderNearby(providerOnline ? filterNearby(nearbyCache) : []);
        });
        $(document).on('click', '.nearby-accept', function () {
            var oid = $(this).data('id');
            var order = nearbyCache.filter(function (o) { return o.id === oid; })[0];
            acceptBroadcast(oid, order);
        });
        $(document).on('click', '.nearby-reject', function () {
            database.collection('provider_orders').doc($(this).data('id')).update({
                rejectedBy: firebase.firestore.FieldValue.arrayUnion([providerId])
            });
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
