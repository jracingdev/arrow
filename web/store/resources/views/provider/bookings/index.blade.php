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
        <ul class="nav nav-pills mb-3" id="bookingTabs">
            <li class="nav-item"><a class="nav-link active" href="#" data-tab="placed">{{ trans('lang.provider_new_bookings') }}</a></li>
            <li class="nav-item"><a class="nav-link" href="#" data-tab="active">{{ trans('lang.order_ongoing') }}</a></li>
            <li class="nav-item"><a class="nav-link" href="#" data-tab="completed">{{ trans('lang.provider_completed_bookings') }}</a></li>
            <li class="nav-item"><a class="nav-link" href="#" data-tab="cancelled">{{ trans('lang.provider_cancelled_bookings') }}</a></li>
        </ul>
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
    var currentTab = 'placed';
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
        "In Transit": "In Transit",
        "Order Completed": "{{ trans('lang.provider_completed_bookings') }}",
        "Order Cancelled": "{{ trans('lang.provider_cancelled_bookings') }}",
        "Order Rejected": "{{ trans('lang.reject') }}",
        "Driver Rejected": "{{ trans('lang.reject') }}"
    };

    function render() {
        var list = allOrders.filter(function (o) { return (tabs[currentTab] || []).indexOf(o.status) >= 0; });
        var html = '';
        list.forEach(function (order) {
            var when = order.createdAt && order.createdAt.toDate ? ArrowDateTime.formatDate(order.createdAt.toDate()) : '';
            var title = (order.provider && order.provider.title) ? order.provider.title : '';
            var customer = order.author ? ((order.author.firstName || '') + ' ' + (order.author.lastName || '')) : '';
            var edit = "{{ url('provider/bookings/edit') }}/" + order.id;
            html += '<tr><td>' + (statusLabel[order.status] || order.status) + '</td><td>' + title + '</td><td>' + customer + '</td><td>' + when + '</td><td><a class="btn btn-sm btn-primary" href="' + edit + '">{{ trans("lang.view_details") }}</a></td></tr>';
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
        $("#data-table_processing").show();
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
    });
</script>
@endsection
