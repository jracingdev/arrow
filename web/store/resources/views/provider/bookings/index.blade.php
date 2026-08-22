@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{ trans('lang.booking_plural') }}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                <li class="breadcrumb-item active">{{ trans('lang.booking_plural') }}</li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display:none;">{{ trans('lang.processing') }}</div>
        <div class="error_top"></div>
        <ul class="nav nav-pills mb-3" id="bookingTabs">
            <li class="nav-item"><a class="nav-link active" href="#" data-tab="nearby">{{ trans('lang.provider_nearby') }} <span class="badge badge-info" id="nearby_badge">0</span></a></li>
            <li class="nav-item"><a class="nav-link" href="#" data-tab="placed">{{ trans('lang.provider_new_bookings') }}</a></li>
            <li class="nav-item"><a class="nav-link" href="#" data-tab="active">{{ trans('lang.order_ongoing') }}</a></li>
            <li class="nav-item"><a class="nav-link" href="#" data-tab="completed">{{ trans('lang.provider_completed_bookings') }}</a></li>
            <li class="nav-item"><a class="nav-link" href="#" data-tab="cancelled">{{ trans('lang.provider_cancelled_bookings') }}</a></li>
        </ul>
        <p class="text-muted" id="nearby_hint"></p>
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-striped" id="bookingTable">
                    <thead>
                        <tr>
                            <th>{{ trans('lang.status') }}</th>
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
    </div>
</div>
@endsection

@section('scripts')
<script>
    var database = firebase.firestore();
    var providerId = "<?php echo $id; ?>";
    var allOrders = [];
    var nearbyOrders = [];
    var currentTab = 'nearby';
    var providerUser = null;
    var myServices = [];
    var providerOnline = false;
    var tabs = {
        placed: ["Order Placed"],
        active: ["Order Accepted", "Order Assigned", "Order Ongoing", "In Transit"],
        completed: ["Order Completed"],
        cancelled: ["Order Rejected", "Order Cancelled", "Driver Rejected"]
    };
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
    function customerName(order) {
        if (order.author && (order.author.firstName || order.author.lastName)) {
            return ((order.author.firstName || '') + ' ' + (order.author.lastName || '')).trim();
        }
        return '{{ trans("lang.user") }}';
    }
    function directionsUrl(order) {
        var lat = customerLat(order);
        var lng = customerLng(order);
        if (isValidCoord(lat, lng)) {
            return 'https://www.openstreetmap.org/directions?engine=fossgis_osrm_car&route=;' + lat + '%2C' + lng + '#map=16/' + lat + '/' + lng;
        }
        return 'https://www.openstreetmap.org/search?query=' + encodeURIComponent(addressLine(order));
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
    function acceptBroadcast(orderId) {
        var order = nearbyOrders.filter(function (o) { return o.id === orderId; })[0];
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
    function declineBroadcast(orderId) {
        database.collection('provider_orders').doc(orderId).update({
            rejectedBy: firebase.firestore.FieldValue.arrayUnion([providerId])
        });
    }

    function render() {
        $('#nearby_badge').text(nearbyOrders.length);
        $('#nearby_hint').text('');
        var html = '';
        if (currentTab === 'nearby') {
            if (!providerOnline) {
                $('#nearby_hint').text("{{ trans('lang.provider_nearby_offline') }}");
            }
            nearbyOrders.forEach(function (order) {
                var when = order.createdAt && order.createdAt.toDate ? ArrowDateTime.formatDate(order.createdAt.toDate()) : '';
                var title = (order.provider && order.provider.title) ? order.provider.title : '';
                var edit = "{{ url('provider/bookings/edit') }}/" + order.id;
                var dir = directionsUrl(order);
                html += '<tr><td>{{ trans("lang.provider_nearby") }}</td><td>' + title + '</td><td>' + customerName(order) + '</td><td>' + when + '</td>';
                html += '<td>';
                html += '<a class="btn btn-sm btn-outline-secondary mr-1" href="' + dir + '" target="_blank" rel="noopener">{{ trans("lang.como_chegar") }}</a>';
                html += '<button type="button" class="btn btn-sm btn-success nearby-accept mr-1" data-id="' + order.id + '">{{ trans("lang.accept") }}</button>';
                html += '<button type="button" class="btn btn-sm btn-danger nearby-reject mr-1" data-id="' + order.id + '">{{ trans("lang.reject") }}</button>';
                html += '<a class="btn btn-sm btn-primary" href="' + edit + '">{{ trans("lang.view_details") }}</a>';
                html += '</td></tr>';
            });
            $('#bookingTable tbody').html(html || '<tr><td colspan="5">{{ trans("lang.provider_nearby_empty") }}</td></tr>');
            return;
        }
        var list = allOrders.filter(function (o) { return (tabs[currentTab] || []).indexOf(o.status) >= 0; });
        list.forEach(function (order) {
            var when = order.createdAt && order.createdAt.toDate ? ArrowDateTime.formatDate(order.createdAt.toDate()) : '';
            var title = (order.provider && order.provider.title) ? order.provider.title : '';
            var edit = "{{ url('provider/bookings/edit') }}/" + order.id;
            html += '<tr><td>' + (statusLabel[order.status] || order.status) + '</td><td>' + title + '</td><td>' + customerName(order) + '</td><td>' + when + '</td><td><a class="btn btn-sm btn-primary" href="' + edit + '">{{ trans("lang.view_details") }}</a></td></tr>';
        });
        $('#bookingTable tbody').html(html || '<tr><td colspan="5">{{ trans("lang.no_record_found") }}</td></tr>');
    }

    $(function () {
        $('#bookingTabs a').on('click', function (e) {
            e.preventDefault();
            $('#bookingTabs a').removeClass('active');
            $(this).addClass('active');
            currentTab = $(this).data('tab');
            render();
        });
        $(document).on('click', '.nearby-accept', function () { acceptBroadcast($(this).data('id')); });
        $(document).on('click', '.nearby-reject', function () { declineBroadcast($(this).data('id')); });
        $("#data-table_processing").show();
        Promise.all([
            database.collection('users').doc(providerId).get(),
            database.collection('providers_services').where('author', '==', providerId).get()
        ]).then(function (snaps) {
            providerUser = snaps[0].data() || {};
            providerOnline = providerUser.online === true;
            myServices = snaps[1].docs.map(function (d) { return d.data(); });
            database.collection('provider_orders').where('provider.author', '==', providerId).onSnapshot(function (snap) {
                allOrders = snap.docs.map(function (d) { return d.data(); });
                allOrders.sort(function (a, b) {
                    var ta = a.createdAt && a.createdAt.toDate ? a.createdAt.toDate().getTime() : 0;
                    var tb = b.createdAt && b.createdAt.toDate ? b.createdAt.toDate().getTime() : 0;
                    return tb - ta;
                });
                render();
                $("#data-table_processing").hide();
            }, function () {
                $("#data-table_processing").hide();
            });
            database.collection('provider_orders').where('dispatchMode', '==', 'broadcast').onSnapshot(function (snap) {
                var docs = snap.docs.map(function (d) {
                    var data = d.data() || {};
                    if (!data.id) data.id = d.id;
                    return data;
                });
                nearbyOrders = providerOnline ? filterNearby(docs) : [];
                render();
            });
        }).catch(function () {
            $("#data-table_processing").hide();
        });
    });
</script>
@endsection
