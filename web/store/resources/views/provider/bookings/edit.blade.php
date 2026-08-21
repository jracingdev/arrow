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
                <div class="mt-3 d-none" id="timer_wrap">
                    <p><strong>{{ trans('lang.total_time') }}:</strong> <span id="timer" class="text-danger font-weight-bold">00:00:00</span></p>
                    <button type="button" class="btn btn-sm btn-danger" id="stop_timer_btn">{{ trans('lang.stop_time') }}</button>
                </div>
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
                <hr>
                <h4>{{ trans('lang.nfse_documents') }}</h4>
                <p class="text-muted mb-2">{{ trans('lang.nfse_invoice') }}</p>
                <div id="invoices_empty" class="text-muted">{{ trans('lang.nfse_empty') }}</div>
                <ul id="invoices_list" class="list-unstyled"></ul>
                <div class="form-group mt-2" id="invoice_upload_wrap" style="display:none;">
                    <label for="invoice_file">{{ trans('lang.nfse_attach') }}</label>
                    <input type="file" class="form-control-file" id="invoice_file" accept=".pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/*">
                    <small class="form-text text-muted">{{ trans('lang.nfse_hint') }}</small>
                    <button type="button" class="btn btn-outline-primary mt-2" id="invoice_upload_btn">{{ trans('lang.nfse_attach') }}</button>
                </div>
                <p id="invoice_status_hint" class="text-muted mt-2" style="display:none;">{{ trans('lang.nfse_status_hint') }}</p>
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
    var replaceInvoiceIndex = null;
    var timerInterval = null;
    var storedStartTime = null;
    var storedEndTime = null;
    var priceUnit = '';
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
            html += '<button type="button" class="btn btn-info ml-2" id="start_btn">{{ trans("lang.start_service") }}</button>';
        } else if (order.status === 'Order Assigned') {
            html += '<button type="button" class="btn btn-info" id="start_btn">{{ trans("lang.start_service") }}</button>';
        } else if (order.status === 'Order Ongoing' || order.status === 'In Transit') {
            if (priceUnit !== 'Hourly' || storedEndTime) {
                $('#complete_wrap').show();
            }
        }
        $('#actions').html(html);
    }

    function canUploadInvoice(status) {
        return status === 'Order Ongoing' || status === 'In Transit' || status === 'Order Completed';
    }

    function invoiceFileId() {
        if (window.crypto && crypto.randomUUID) return crypto.randomUUID();
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
            var r = Math.random() * 16 | 0, v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }

    function invoiceExtension(name) {
        var parts = (name || '').split('.');
        return parts.length > 1 ? parts.pop().toLowerCase() : '';
    }

    function renderInvoices() {
        var list = (order && Array.isArray(order.invoices)) ? order.invoices : [];
        var html = '';
        list.forEach(function (inv, index) {
            if (!inv || !inv.url) return;
            var name = inv.fileName || 'NFS-e';
            html += '<li class="mb-2 d-flex align-items-center">';
            html += '<a href="' + inv.url + '" target="_blank" rel="noopener">' + name + '</a>';
            if (canUploadInvoice(order.status)) {
                html += ' <button type="button" class="btn btn-sm btn-link invoice-replace" data-index="' + index + '">{{ trans("lang.nfse_replace") }}</button>';
            }
            html += '</li>';
        });
        $('#invoices_list').html(html);
        if (html) {
            $('#invoices_empty').hide();
        } else {
            $('#invoices_empty').show();
        }
        if (order && canUploadInvoice(order.status)) {
            $('#invoice_upload_wrap').show();
            $('#invoice_status_hint').hide();
        } else {
            $('#invoice_upload_wrap').hide();
            $('#invoice_status_hint').show();
        }
    }

    function uploadInvoiceFile(file, replaceIndex) {
        var allowed = ['pdf', 'jpg', 'jpeg', 'png', 'webp'];
        var ext = invoiceExtension(file.name);
        if (allowed.indexOf(ext) === -1) {
            $('.error_top').html('<p>{{ trans("lang.nfse_invalid_type") }}</p>').show();
            return;
        }
        if (file.size > 10 * 1024 * 1024) {
            $('.error_top').html('<p>{{ trans("lang.nfse_too_large") }}</p>').show();
            return;
        }
        $("#data-table_processing").show();
        var path = 'provider_orders/' + id + '/invoices/' + invoiceFileId() + '.' + ext;
        firebase.storage().ref(path).put(file).then(function (snap) {
            return snap.ref.getDownloadURL();
        }).then(function (url) {
            var invoices = Array.isArray(order.invoices) ? order.invoices.slice() : [];
            var item = {
                type: 'nfs-e',
                url: url,
                fileName: file.name,
                uploadedAt: firebase.firestore.Timestamp.now(),
                uploadedBy: providerId
            };
            if (replaceIndex !== null && replaceIndex !== undefined && invoices[replaceIndex]) {
                invoices[replaceIndex] = item;
            } else {
                invoices.push(item);
            }
            return database.collection('provider_orders').doc(id).update({ invoices: invoices }).then(function () {
                order.invoices = invoices;
                replaceInvoiceIndex = null;
                renderInvoices();
                $('#invoice_file').val('');
                $("#data-table_processing").hide();
            });
        }).catch(function (err) {
            $("#data-table_processing").hide();
            $('.error_top').html('<p>' + (err && err.message ? err.message : '{{ trans("lang.nfse_upload_failed") }}') + '</p>').show();
        });
    }

    function pad(n) {
        return n < 10 ? '0' + n : '' + n;
    }

    function formatElapsed(ms) {
        if (ms < 0) ms = 0;
        var s = Math.floor(ms / 1000);
        var h = Math.floor(s / 3600);
        var m = Math.floor((s % 3600) / 60);
        var sec = s % 60;
        return pad(h) + ':' + pad(m) + ':' + pad(sec);
    }

    function renderTimer() {
        if (priceUnit !== 'Hourly' || order.status !== 'Order Ongoing' || !storedStartTime) {
            $('#timer_wrap').addClass('d-none');
            return;
        }
        $('#timer_wrap').removeClass('d-none');
        if (storedEndTime) {
            $('#timer').text(formatElapsed(storedEndTime - storedStartTime));
            $('#stop_timer_btn').hide();
            return;
        }
        $('#stop_timer_btn').show();
        $('#timer').text(formatElapsed(new Date() - storedStartTime));
        if (timerInterval) clearInterval(timerInterval);
        timerInterval = setInterval(function () {
            $('#timer').text(formatElapsed(new Date() - storedStartTime));
        }, 1000);
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
            priceUnit = (order.provider && order.provider.priceUnit) ? order.provider.priceUnit : '';
            storedStartTime = (order.startTime && order.startTime.toDate) ? order.startTime.toDate() : null;
            storedEndTime = (order.endTime && order.endTime.toDate) ? order.endTime.toDate() : null;
            renderActions();
            renderInvoices();
            renderTimer();
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
            var payload = {
                status: 'Order Ongoing',
                startTime: firebase.firestore.FieldValue.serverTimestamp()
            };
            if (priceUnit === 'Hourly') {
                payload.endTime = null;
            }
            database.collection('provider_orders').doc(id).update(payload).then(function () { location.reload(); });
        });
        $(document).on('click', '#stop_timer_btn', function () {
            if (!storedStartTime) { return; }
            var now = new Date();
            var hours = Math.abs(now - storedStartTime) / (1000 * 60 * 60);
            hours = parseFloat(hours.toFixed(2));
            if (hours < 1) hours = 1;
            database.collection('provider_orders').doc(id).update({
                endTime: firebase.firestore.FieldValue.serverTimestamp(),
                quantity: hours
            }).then(function () { location.reload(); });
        });
        $(document).on('click', '#complete_btn', function () {
            var otp = $('#otp_input').val();
            if (otp !== String(order.otp || '')) {
                $('.error_top').html('<p>{{ trans("lang.please_enter_otp") }}</p>').show();
                return;
            }
            if (priceUnit === 'Hourly' && !storedEndTime) {
                $('.error_top').html('<p>{{ trans("lang.stop_the_timer") }}</p>').show();
                return;
            }
            var payload = {
                status: 'Order Completed',
                extraPaymentStatus: true,
                paymentStatus: priceUnit === 'Hourly' ? (order.paymentStatus === true) : true
            };
            if (priceUnit !== 'Hourly') {
                payload.endTime = firebase.firestore.FieldValue.serverTimestamp();
            }
            database.collection('provider_orders').doc(id).update(payload).then(function () { location.reload(); });
        });

        $(document).on('click', '.invoice-replace', function () {
            replaceInvoiceIndex = parseInt($(this).data('index'), 10);
            $('#invoice_file').trigger('click');
        });
        $('#invoice_upload_btn').on('click', function () {
            replaceInvoiceIndex = null;
            var file = document.getElementById('invoice_file').files[0];
            if (!file) {
                $('#invoice_file').trigger('click');
                return;
            }
            uploadInvoiceFile(file, null);
        });
        $('#invoice_file').on('change', function () {
            var file = this.files[0];
            if (!file) return;
            if (!order || !canUploadInvoice(order.status)) return;
            uploadInvoiceFile(file, replaceInvoiceIndex);
        });
    });
</script>
@endsection
