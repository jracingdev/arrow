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
                <li class="breadcrumb-item"><a href="{{ route('provider.bookings') }}">{{ trans('lang.booking_plural') }}</a></li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display:none;">{{ trans('lang.processing') }}</div>
        <div class="error_top"></div>
        <div class="card">
            <div class="card-body">
                <p><strong>{{ trans('lang.status') }}:</strong> <span id="status_label"></span></p>
                <p><strong>{{ trans('lang.service_plural') }}:</strong> <span id="service_title"></span></p>
                <p><strong>{{ trans('lang.user') }}:</strong> <span id="customer_name"></span></p>
                <p><strong>{{ trans('lang.phone') }}:</strong> <span id="customer_phone"></span></p>
                <p><strong>{{ trans('lang.date') }}:</strong> <span id="schedule_date"></span></p>
                <p><strong>{{ trans('lang.vendor_address') }}:</strong> <span id="address"></span></p>
                <p><strong>OTP:</strong> <span id="otp_code"></span></p>
                <div class="mt-3" id="actions"></div>
                <div class="form-group mt-3" id="worker_wrap" style="display:none;">
                    <label>{{ trans('lang.select_worker') }}</label>
                    <select class="form-control" id="worker_list"></select>
                    <button type="button" class="btn btn-primary mt-2" id="assign_btn">{{ trans('lang.assign_worker') }}</button>
                    <button type="button" class="btn btn-secondary mt-2" id="self_assign_btn">{{ trans('lang.self_assign') }}</button>
                </div>
                <div class="form-group mt-3" id="complete_wrap" style="display:none;">
                    <label>{{ trans('lang.booking_otp') }}</label>
                    <input type="text" class="form-control" id="otp_input">
                    <button type="button" class="btn btn-success mt-2" id="complete_btn">{{ trans('lang.complete_booking') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    var database = firebase.firestore();
    var id = "{{ $id }}";
    var providerId = "{{ $providerId }}";
    var order = null;
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

    function renderActions() {
        var html = '';
        if (order.status === 'Order Placed') {
            html += '<button type="button" class="btn btn-success mr-2" id="accept_btn">{{ trans("lang.accept") }}</button>';
            html += '<button type="button" class="btn btn-danger" id="reject_btn">{{ trans("lang.reject") }}</button>';
        } else if (order.status === 'Order Accepted') {
            html += '<button type="button" class="btn btn-primary" id="show_assign">{{ trans("lang.assign_worker") }}</button>';
        } else if (order.status === 'Order Assigned') {
            html += '<button type="button" class="btn btn-info" id="start_btn">{{ trans("lang.start_service") }}</button>';
        } else if (order.status === 'Order Ongoing' || order.status === 'In Transit') {
            $('#complete_wrap').show();
        }
        $('#actions').html(html);
    }

    function loadWorkers() {
        database.collection('providers_workers').where('providerId', '==', providerId).where('online', '==', true).get().then(function (snap) {
            $('#worker_list').html('<option value="">{{ trans("lang.select_worker") }}</option>');
            snap.docs.forEach(function (doc) {
                var w = doc.data();
                $('#worker_list').append($('<option></option>').attr('value', w.id).text((w.firstName || '') + ' ' + (w.lastName || '')));
            });
        });
    }

    $(function () {
        $("#data-table_processing").show();
        database.collection('provider_orders').doc(id).get().then(function (snap) {
            order = snap.data();
            if (!order || !order.provider || order.provider.author !== providerId) {
                window.location = "{{ route('provider.bookings') }}";
                return;
            }
            $('#status_label').text(statusLabel[order.status] || order.status);
            $('#service_title').text((order.provider && order.provider.title) || '');
            $('#customer_name').text(order.author ? ((order.author.firstName || '') + ' ' + (order.author.lastName || '')) : '');
            $('#customer_phone').text((order.author && order.author.phoneNumber) || '');
            var sched = order.newScheduleDateTime || order.scheduleDateTime;
            $('#schedule_date').text(sched && sched.toDate ? ArrowDateTime.formatDate(sched.toDate()) + ' ' + ArrowDateTime.formatTime(sched.toDate()) : '');
            $('#address').text((order.address && (order.address.address || order.address.locality)) || '');
            $('#otp_code').text(order.otp || '');
            renderActions();
            $("#data-table_processing").hide();
        });

        $(document).on('click', '#accept_btn', function () {
            database.collection('provider_orders').doc(id).update({
                status: 'Order Accepted',
                newScheduleDateTime: order.scheduleDateTime || firebase.firestore.FieldValue.serverTimestamp()
            }).then(function () { location.reload(); });
        });
        $(document).on('click', '#reject_btn', function () {
            database.collection('provider_orders').doc(id).update({ status: 'Order Rejected' }).then(function () { location.reload(); });
        });
        $(document).on('click', '#show_assign', function () {
            $('#worker_wrap').show();
            loadWorkers();
        });
        $(document).on('click', '#self_assign_btn', function () {
            database.collection('provider_orders').doc(id).update({ status: 'Order Assigned', workerId: '' }).then(function () { location.reload(); });
        });
        $(document).on('click', '#assign_btn', function () {
            var worker = $('#worker_list').val();
            if (!worker) { return; }
            database.collection('provider_orders').doc(id).update({ status: 'Order Assigned', workerId: worker }).then(function () { location.reload(); });
        });
        $(document).on('click', '#start_btn', function () {
            database.collection('provider_orders').doc(id).update({
                status: 'Order Ongoing',
                startTime: firebase.firestore.FieldValue.serverTimestamp()
            }).then(function () { location.reload(); });
        });
        $(document).on('click', '#complete_btn', function () {
            var otp = $('#otp_input').val();
            if (otp !== String(order.otp || '')) {
                $('.error_top').html('<p>{{ trans("lang.please_enter_otp") }}</p>').show();
                return;
            }
            database.collection('provider_orders').doc(id).update({
                status: 'Order Completed',
                extraPaymentStatus: true,
                paymentStatus: true,
                endTime: firebase.firestore.FieldValue.serverTimestamp()
            }).then(function () { location.reload(); });
        });
    });
</script>
@endsection
