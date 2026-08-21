@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{ trans('lang.ondemand_reports') }}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ondemand.reports.index') }}">{{ trans('lang.ondemand_reports') }}</a></li>
                <li class="breadcrumb-item">{{ trans('lang.edit_complaints') }}</li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h4 id="title"></h4>
                        <p><strong>{{ trans('lang.order_id') }}:</strong> <a href="#" id="order-link"></a></p>
                        <p><strong>{{ trans('lang.report_category') }}:</strong> <span id="category"></span></p>
                        <p><strong>{{ trans('lang.report_priority') }}:</strong> <span id="priority"></span></p>
                        <p><strong>{{ trans('lang.status') }}:</strong> <span id="status"></span></p>
                        <p><strong>{{ trans('lang.description') }}:</strong></p>
                        <p id="description"></p>
                        <div class="form-group">
                            <label>{{ trans('lang.admin_note') }}</label>
                            <textarea id="admin_note" class="form-control" rows="3"></textarea>
                        </div>
                        <button type="button" class="btn btn-secondary" id="btn-dismiss">{{ trans('lang.dismiss_report') }}</button>
                        <button type="button" class="btn btn-warning" id="btn-warn">{{ trans('lang.warn_user') }}</button>
                        <button type="button" class="btn btn-danger" id="btn-ban">{{ trans('lang.ban_reported_user') }}</button>
                    </div>
                    <div class="col-md-4">
                        <h5>{{ trans('lang.reporter') }}</h5>
                        <p id="reporter"></p>
                        <h5>{{ trans('lang.reported_user') }}</h5>
                        <p id="reported"></p>
                        <p id="strikes"></p>
                        <p id="repeat"></p>
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
    var id = @json($id);
    var report = {};

    $(document).ready(function () {
        database.collection('complaints').doc(id).get().then(async function (snap) {
            if (!snap.exists) return;
            report = snap.data() || {};
            $('#title').text(report.title || report.category || '');
            $('#category').text(report.category || '');
            $('#priority').text(report.priority || 'normal');
            $('#status').text(report.status || '');
            $('#description').text(report.description || '');
            $('#admin_note').val(report.adminNote || '');
            $('#reporter').text((report.reporterRole || '') + ' · ' + (report.reporterId || ''));
            $('#reported').text((report.reportedRole || '') + ' · ' + (report.reportedId || ''));
            var orderRoute = '{{ route('ondemand.bookings.edit', ':id') }}'.replace(':id', report.orderId || '');
            $('#order-link').attr('href', orderRoute).text(report.orderId || '');
            if (report.reportedId) {
                var user = await database.collection('users').doc(report.reportedId).get();
                if (user.exists) {
                    var data = user.data();
                    var strikes = data.reportStrikes || 0;
                    $('#strikes').text('Strikes: ' + strikes);
                    if (data.banRecommended === true || strikes >= 3) {
                        $('#repeat').html('<span class="badge badge-warning">{{ trans('lang.repeat_offender') }}</span>');
                    }
                    if (data.active === false && data.isActive === false) {
                        $('#repeat').append(' <span class="badge badge-danger">ban</span>');
                    }
                }
            }
        });
    });

    async function incrementStrikes(uid) {
        if (!uid) return;
        var user = await database.collection('users').doc(uid).get();
        var data = user.exists ? user.data() : {};
        var current = parseInt(data.reportStrikes || 0, 10);
        if (isNaN(current) || current < 0) current = 0;
        var next = current + 1;
        var count = parseInt(data.reportCount || 0, 10);
        if (isNaN(count) || count < 0) count = 0;
        await database.collection('users').doc(uid).update({
            reportStrikes: next,
            reportCount: count + 1,
            banRecommended: next >= 3
        });
    }

    $('#btn-dismiss').click(async function () {
        await database.collection('complaints').doc(id).update({
            status: 'Dismissed',
            adminNote: $('#admin_note').val() || ''
        });
        window.location.href = '{{ route('ondemand.reports.index') }}';
    });

    $('#btn-warn').click(async function () {
        await incrementStrikes(report.reportedId);
        await database.collection('complaints').doc(id).update({
            status: 'Resolved',
            adminNote: $('#admin_note').val() || '',
            action: 'warn'
        });
        window.location.href = '{{ route('ondemand.reports.index') }}';
    });

    $('#btn-ban').click(async function () {
        if (!report.reportedId) return;
        await incrementStrikes(report.reportedId);
        await database.collection('users').doc(report.reportedId).update({
            active: false,
            isActive: false
        });
        await database.collection('complaints').doc(id).update({
            status: 'Resolved',
            adminNote: $('#admin_note').val() || '',
            action: 'ban'
        });
        window.location.href = '{{ route('ondemand.reports.index') }}';
    });
</script>
@endsection
