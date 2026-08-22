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
                <p><strong>{{ trans('lang.payment_methods') }}:</strong> <span id="payment_label"></span></p>
                <p id="hourly_rate_wrap" class="d-none"><strong>{{ trans('lang.hourly_rate') }}:</strong> <span id="hourly_rate"></span></p>
                <p id="billed_hours_wrap" class="d-none"><strong>{{ trans('lang.billed_hours') }}:</strong> <span id="billed_hours"></span></p>
                <p><strong>{{ trans('lang.date') }}:</strong> <span id="schedule_date"></span></p>
                <p><strong>{{ trans('lang.vendor_address') }}:</strong> <span id="address"></span></p>
                <div id="map_card" class="d-none mt-3">
                    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
                    <div id="booking_map" style="height:220px;border-radius:8px;overflow:hidden;"></div>
                    <a id="como_chegar" class="btn btn-outline-primary btn-sm mt-2" target="_blank" rel="noopener">
                        <i class="mdi mdi-directions"></i> {{ trans('lang.como_chegar') }}
                    </a>
                </div>
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
                <div class="mt-4 d-none" id="chat_card">
                    <h4>{{ trans('lang.provider_chat') }}</h4>
                    <div id="chat_box" class="border rounded p-3 mb-2" style="max-height:280px;overflow-y:auto;background:#f8f9fa;">
                        <p class="text-muted mb-0" id="chat_empty">{{ trans('lang.provider_chat_empty') }}</p>
                    </div>
                    <div class="input-group">
                        <input type="text" class="form-control" id="chat_input" placeholder="{{ trans('lang.type_your_message') }}">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary" id="chat_send">{{ trans('lang.provider_chat_send') }}</button>
                        </div>
                    </div>
                </div>
                <div class="mt-4" id="safety_card">
                    <p class="text-muted">{{ trans('lang.report_hint') }}</p>
                    <button type="button" class="btn btn-outline-secondary d-none" id="report_btn">{{ trans('lang.report_problem') }}</button>
                </div>
                <div class="modal fade" id="early_start_modal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ trans('lang.early_start_title') }}</h5>
                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <p id="early_start_message">{{ trans('lang.early_start_confirm') }}</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('lang.cancel') }}</button>
                                <button type="button" class="btn btn-primary" id="early_start_confirm">{{ trans('lang.early_start_go') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="report_modal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="report_modal_title">{{ trans('lang.report_problem') }}</h5>
                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <p class="text-muted">{{ trans('lang.report_hint') }}</p>
                                <div class="form-group" id="report_categories">
                                    <label class="d-block"><input type="radio" name="report_category" value="abuse" checked> {{ trans('lang.report_category_abuse') }}</label>
                                    <label class="d-block"><input type="radio" name="report_category" value="harassment"> {{ trans('lang.report_category_harassment') }}</label>
                                    <label class="d-block"><input type="radio" name="report_category" value="no_show"> {{ trans('lang.report_category_no_show_customer') }}</label>
                                    <label class="d-block"><input type="radio" name="report_category" value="unsafe_situation"> {{ trans('lang.report_category_unsafe_situation') }}</label>
                                    <label class="d-block"><input type="radio" name="report_category" value="payment_dispute"> {{ trans('lang.report_category_payment_dispute') }}</label>
                                    <label class="d-block"><input type="radio" name="report_category" value="other"> {{ trans('lang.report_category_other') }}</label>
                                </div>
                                <div class="form-group">
                                    <label>{{ trans('lang.report_describe') }}</label>
                                    <textarea class="form-control" id="report_description" rows="4"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('lang.cancel') }}</button>
                                <button type="button" class="btn btn-danger" id="report_submit">{{ trans('lang.report_submit') }}</button>
                            </div>
                        </div>
                    </div>
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
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var database = firebase.firestore();
    var id = "{{ $id }}";
    var providerId = "{{ $providerId }}";
    var order = null;
    var providerUser = null;
    var myServices = [];
    var customerId = '';
    var customerName = '';
    var bookingMap = null;
    var chatUnsubs = [];
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
        "In Transit": "{{ trans('lang.in_transit') }}",
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
            if (!isHourly(priceUnit) || storedEndTime) {
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

    function isHourly(unit) {
        var u = (unit || '').toString().trim().toLowerCase();
        return u === 'hourly' || u === 'hour' || u === 'por hora';
    }
    function isCod(method) {
        var m = (method || '').toString().trim().toLowerCase();
        return m === 'cod' || m === 'cash on delivery' || m === 'dinheiro';
    }
    function unitPrice(price, disPrice) {
        var discounted = parseFloat(disPrice);
        if (discounted > 0) return discounted;
        return parseFloat(price) || 0;
    }
    function billableHours(start, end) {
        var hours = Math.abs(end - start) / (1000 * 60 * 60);
        if (hours <= 0 || hours < 1) return 1;
        return parseFloat(hours.toFixed(2));
    }
    function formatBrl(value) {
        var n = Number(value) || 0;
        return 'R$ ' + n.toFixed(2).replace('.', ',');
    }
    function paymentLabel(method, paid) {
        if (isCod(method)) return paid === true ? "{{ trans('lang.cod_paid') }}" : "{{ trans('lang.cod_unpaid') }}";
        var gateway = (method || '').toString().trim();
        if (paid === true) return gateway ? ('{{ trans("lang.paid") }} · ' + gateway) : '{{ trans("lang.paid") }}';
        return gateway ? ('{{ trans("lang.pending") }} · ' + gateway) : '{{ trans("lang.pending") }}';
    }
    function isEarlyStart() {
        var when = order && (order.newScheduleDateTime || order.scheduleDateTime);
        if (!when || !when.toDate) return false;
        return when.toDate().getTime() > Date.now();
    }
    function newId() {
        if (window.crypto && crypto.randomUUID) return crypto.randomUUID();
        return invoiceFileId();
    }
    function isBroadcastOpen(o) {
        if (!o || o.dispatchMode !== 'broadcast' || o.status !== 'Order Placed') return false;
        var author = o.provider && o.provider.author;
        return !author;
    }
    function isAssignedToMe(o) {
        return !!(o && o.provider && o.provider.author === providerId);
    }
    function rejectedByMe(o) {
        return !!(o && Array.isArray(o.rejectedBy) && o.rejectedBy.indexOf(providerId) >= 0);
    }
    function canReportOrder(status) {
        return ['Order Assigned', 'Order Ongoing', 'Order Completed', 'Order Cancelled', 'Order Accepted', 'In Transit'].indexOf(status) >= 0;
    }
    function isValidCoord(lat, lng) {
        if (lat == null || lng == null || isNaN(lat) || isNaN(lng)) return false;
        if (Math.abs(lat) < 0.2 && Math.abs(lng) < 0.2) return false;
        return lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180;
    }
    function customerLatLng(o) {
        var addr = (o && o.address) || {};
        var loc = addr.location || {};
        var lat = parseFloat(loc.latitude != null ? loc.latitude : addr.latitude);
        var lng = parseFloat(loc.longitude != null ? loc.longitude : addr.longitude);
        return { lat: lat, lng: lng };
    }
    function addressLine(o) {
        var addr = (o && o.address) || {};
        return addr.address || addr.locality || addr.addressAsString || '';
    }
    function resolveCustomerId(o) {
        if (o.authorID) return o.authorID;
        if (o.author && o.author.id) return o.author.id;
        return '';
    }
    function resolveCustomerName(o) {
        if (o.author && (o.author.firstName || o.author.lastName)) {
            return ((o.author.firstName || '') + ' ' + (o.author.lastName || '')).trim();
        }
        return '{{ trans("lang.user") }}';
    }
    function directionsUrl(lat, lng, address) {
        if (isValidCoord(lat, lng)) {
            return 'https://www.openstreetmap.org/directions?engine=fossgis_osrm_car&route=;' + lat + '%2C' + lng + '#map=16/' + lat + '/' + lng;
        }
        var q = encodeURIComponent(address || '');
        return 'https://www.openstreetmap.org/search?query=' + q;
    }
    function renderMap() {
        var coords = customerLatLng(order);
        var line = addressLine(order);
        if (!isValidCoord(coords.lat, coords.lng) && !line) return;
        $('#map_card').removeClass('d-none');
        $('#como_chegar').attr('href', directionsUrl(coords.lat, coords.lng, line));
        if (!isValidCoord(coords.lat, coords.lng) || typeof L === 'undefined') return;
        if (bookingMap) {
            bookingMap.remove();
            bookingMap = null;
        }
        bookingMap = L.map('booking_map').setView([coords.lat, coords.lng], 15);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(bookingMap);
        L.marker([coords.lat, coords.lng]).addTo(bookingMap);
        setTimeout(function () { bookingMap.invalidateSize(); }, 200);
    }
    function renderSafety() {
        if (canReportOrder(order.status)) $('#report_btn').removeClass('d-none');
        else $('#report_btn').addClass('d-none');
    }
    function chatTime(ts) {
        if (!ts || !ts.toDate) return '';
        var d = ts.toDate();
        return ArrowDateTime.formatTime ? ArrowDateTime.formatTime(d) : d.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
    }
    function renderChatMessages(docs) {
        var box = $('#chat_box');
        box.empty();
        if (!docs.length) {
            box.html('<p class="text-muted mb-0" id="chat_empty">{{ trans("lang.provider_chat_empty") }}</p>');
            return;
        }
        docs.forEach(function (item) {
            var mine = item.senderId === providerId;
            var wrap = $('<div class="mb-2"></div>').css('text-align', mine ? 'right' : 'left');
            var bubble = $('<div class="d-inline-block p-2 rounded"></div>')
                .css({
                    'max-width': '75%',
                    'background': mine ? '#00A1F1' : '#e9ecef',
                    'color': mine ? '#fff' : '#212529'
                })
                .text(item.message || '');
            wrap.append(bubble);
            if (item.createdAt) {
                wrap.append($('<div class="small text-muted"></div>').text(chatTime(item.createdAt)));
            }
            box.append(wrap);
        });
        box.scrollTop(box[0].scrollHeight);
    }
    function listenChat() {
        chatUnsubs.forEach(function (fn) { try { fn(); } catch (e) {} });
        chatUnsubs = [];
        if (!customerId || order.status === 'Order Placed') {
            $('#chat_card').addClass('d-none');
            return;
        }
        $('#chat_card').removeClass('d-none');
        var merged = {};
        function ingest(snap) {
            snap.docs.forEach(function (doc) {
                var data = doc.data() || {};
                merged[doc.id] = {
                    id: doc.id,
                    senderId: data.senderId,
                    message: data.message,
                    createdAt: data.createdAt
                };
            });
            var list = Object.keys(merged).map(function (k) { return merged[k]; });
            list.sort(function (a, b) {
                var ta = a.createdAt && a.createdAt.toDate ? a.createdAt.toDate().getTime() : 0;
                var tb = b.createdAt && b.createdAt.toDate ? b.createdAt.toDate().getTime() : 0;
                return ta - tb;
            });
            renderChatMessages(list);
        }
        chatUnsubs.push(database.collection('chat').doc(id).collection('thread').orderBy('createdAt').onSnapshot(ingest, function () {}));
        chatUnsubs.push(database.collection('chat_provider').doc(id).collection('thread').orderBy('createdAt').onSnapshot(ingest, function () {}));
    }
    function sendChat() {
        var text = ($('#chat_input').val() || '').trim();
        if (!text || !customerId) return;
        $('#chat_input').val('');
        var threadId = newId();
        var inbox = {
            senderId: providerId,
            receiverId: customerId,
            lastSenderId: providerId,
            lastMessage: text,
            lastMessageType: 'text',
            orderId: id,
            createdAt: firebase.firestore.Timestamp.now(),
            chatType: 'provider',
            type: 'orderChat',
            sender_receiver_id: [providerId, customerId]
        };
        database.collection('chat').doc(id).set(inbox, { merge: true }).then(function () {
            return database.collection('chat').doc(id).collection('thread').doc(threadId).set({
                id: threadId,
                senderId: providerId,
                receiverId: customerId,
                orderId: id,
                message: text,
                messageType: 'text',
                createdAt: firebase.firestore.Timestamp.now(),
                seen: false
            });
        }).catch(function (err) {
            $('.error_top').html('<p>' + (err && err.message ? err.message : err) + '</p>').show();
        });
    }
    function matchingService(orderDoc) {
        var requested = orderDoc.requestedCategoryId || (orderDoc.provider && orderDoc.provider.categoryId) || '';
        var published = myServices.filter(function (s) { return s.publish === true; });
        if (requested) {
            var match = published.filter(function (s) { return s.categoryId === requested; });
            if (match.length) return match[0];
        }
        return published[0] || null;
    }
    function acceptBroadcast() {
        var service = matchingService(order);
        if (!service || !providerUser) {
            $('.error_top').html('<p>{{ trans("lang.provider_nearby_need_service") }}</p>').show();
            return;
        }
        var snapshot = Object.assign({}, service);
        snapshot.author = providerId;
        snapshot.authorName = ((providerUser.firstName || '') + ' ' + (providerUser.lastName || '')).trim();
        snapshot.authorProfilePic = providerUser.profilePictureURL || '';
        snapshot.phoneNumber = providerUser.phoneNumber || service.phoneNumber || '';
        var ref = database.collection('provider_orders').doc(id);
        $("#data-table_processing").show();
        database.runTransaction(function (tx) {
            return tx.get(ref).then(function (snap) {
                var data = snap.data();
                if (!data || data.status !== 'Order Placed' || (data.provider && data.provider.author)) {
                    throw new Error('taken');
                }
                var rejected = data.rejectedBy || [];
                if (rejected.indexOf(providerId) >= 0) throw new Error('rejected');
                tx.update(ref, {
                    provider: snapshot,
                    status: 'Order Accepted',
                    dispatchAcceptedAt: firebase.firestore.Timestamp.now()
                });
            });
        }).then(function () {
            location.reload();
        }).catch(function (err) {
            $("#data-table_processing").hide();
            var msg = (err && err.message === 'taken') ? '{{ trans("lang.provider_nearby_taken") }}' : (err && err.message ? err.message : err);
            $('.error_top').html('<p>' + msg + '</p>').show();
        });
    }
    function declineBroadcast() {
        database.collection('provider_orders').doc(id).update({
            rejectedBy: firebase.firestore.FieldValue.arrayUnion([providerId])
        }).then(function () {
            window.location = "{{ route('provider.bookings') }}";
        });
    }
    function submitReport() {
        var description = ($('#report_description').val() || '').trim();
        if (!description) return;
        var category = $('input[name="report_category"]:checked').val() || 'other';
        var complaintId = id + '_' + providerId;
        $("#data-table_processing").show();
        database.collection('complaints').doc(complaintId).get().then(function (existing) {
            if (existing.exists) {
                throw new Error('already');
            }
            var createdAt = firebase.firestore.Timestamp.now();
            return database.collection('complaints').doc(complaintId).set({
                id: complaintId,
                createdAt: createdAt,
                orderId: id,
                serviceType: 'ondemand-service',
                reporterId: providerId,
                reporterRole: 'provider',
                reportedId: customerId,
                reportedRole: 'customer',
                category: category,
                description: description,
                title: category,
                status: 'Initiated',
                priority: 'normal',
                evidenceUrls: [],
                customerId: customerId,
                customerName: customerName,
                driverId: providerId,
                driverName: ((providerUser && providerUser.firstName) || '') + ' ' + ((providerUser && providerUser.lastName) || '')
            }, { merge: true });
        }).then(function () {
            $('#report_modal').modal('hide');
            $('#report_description').val('');
            $("#data-table_processing").hide();
            $('.error_top').html('<p class="text-success">{{ trans("lang.report_sent") }}</p>').show();
        }).catch(function (err) {
            $("#data-table_processing").hide();
            var msg = (err && err.message === 'already') ? '{{ trans("lang.report_already") }}' : (err && err.message ? err.message : err);
            $('.error_top').html('<p>' + msg + '</p>').show();
        });
    }
    function creditCodOnComplete() {
        if (!isCod(order.paymentMethod)) return Promise.resolve();
        return database.collection('wallet').where('user_id', '==', providerId).where('order_id', '==', id).get().then(function (snap) {
            var already = snap.docs.some(function (d) {
                var data = d.data();
                if (data.transactionUser !== 'provider' || data.isTopUp !== true) return false;
                return String(data.note || '').toLowerCase().indexOf('extra') < 0;
            });
            if (already) return;
            var rate = unitPrice((order.provider && order.provider.price) || 0, (order.provider && order.provider.disPrice) || 0);
            var hours = isHourly(priceUnit) ? ((order.quantity > 0) ? Number(order.quantity) : 1) : 1;
            var amount = rate * hours;
            var extra = parseFloat(order.extraCharges) || 0;
            if (extra > 0) {
                var extraAlready = snap.docs.some(function (d) {
                    var data = d.data();
                    if (data.transactionUser !== 'provider' || data.isTopUp !== true) return false;
                    return String(data.note || '').toLowerCase().indexOf('extra') >= 0;
                });
                if (!extraAlready) amount += extra;
            }
            if (amount <= 0) return;
            var txId = newId();
            return database.collection('wallet').doc(txId).set({
                id: txId,
                user_id: providerId,
                payment_method: 'wallet',
                amount: amount,
                isTopUp: true,
                order_id: id,
                payment_status: 'success',
                date: firebase.firestore.Timestamp.now(),
                transactionUser: 'provider',
                note: 'On-demand booking credited',
                serviceType: 'ondemand-service'
            }).then(function () {
                return database.collection('users').doc(providerId).get();
            }).then(function (userSnap) {
                var wallet = parseFloat((userSnap.data() || {}).wallet_amount) || 0;
                return database.collection('users').doc(providerId).update({ wallet_amount: wallet + amount });
            });
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
        if (!isHourly(priceUnit) || order.status !== 'Order Ongoing' || !storedStartTime) {
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
        Promise.all([
            database.collection('provider_orders').doc(id).get(),
            database.collection('users').doc(providerId).get(),
            database.collection('providers_services').where('author', '==', providerId).get()
        ]).then(function (snaps) {
            order = snaps[0].data();
            providerUser = snaps[1].data() || {};
            myServices = snaps[2].docs.map(function (d) { return d.data(); });
            if (!order || (!isAssignedToMe(order) && !isBroadcastOpen(order)) || (isBroadcastOpen(order) && rejectedByMe(order))) {
                window.location = "{{ route('provider.bookings') }}";
                return;
            }
            customerId = resolveCustomerId(order);
            customerName = resolveCustomerName(order);
            $('#status_label').text(statusLabel[order.status] || order.status);
            $('#service_title').text((order.provider && order.provider.title) || '');
            $('#customer_name').text(customerName);
            $('#customer_phone').text((order.author && order.author.phoneNumber) || '');
            var sched = order.newScheduleDateTime || order.scheduleDateTime;
            $('#schedule_date').text(sched && sched.toDate ? ArrowDateTime.formatDate(sched.toDate()) + ' ' + ArrowDateTime.formatTime(sched.toDate()) : '');
            $('#address').text(addressLine(order));
            $('#otp_code').text(order.otp || '');
            $('#payment_label').text(paymentLabel(order.paymentMethod, order.paymentStatus));
            priceUnit = (order.provider && order.provider.priceUnit) ? order.provider.priceUnit : '';
            if (isHourly(priceUnit) && order.provider) {
                var rate = unitPrice(order.provider.price, order.provider.disPrice);
                $('#hourly_rate').text(formatBrl(rate));
                $('#hourly_rate_wrap').removeClass('d-none');
                if (order.quantity) {
                    $('#billed_hours').text(Number(order.quantity).toFixed(2));
                    $('#billed_hours_wrap').removeClass('d-none');
                }
            }
            storedStartTime = (order.startTime && order.startTime.toDate) ? order.startTime.toDate() : null;
            storedEndTime = (order.endTime && order.endTime.toDate) ? order.endTime.toDate() : null;
            renderActions();
            renderInvoices();
            renderTimer();
            renderMap();
            renderSafety();
            listenChat();
            $("#data-table_processing").hide();
        }).catch(function () {
            $("#data-table_processing").hide();
        });

        $('#chat_send').on('click', sendChat);
        $('#chat_input').on('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); sendChat(); }
        });
        $('#report_btn').on('click', function () {
            $('#report_modal_title').text("{{ trans('lang.report_problem') }}");
            $('#report_modal').modal('show');
        });
        $('#report_submit').on('click', submitReport);

        $(document).on('click', '#accept_btn', function () {
            if (isBroadcastOpen(order)) {
                acceptBroadcast();
                return;
            }
            database.collection('provider_orders').doc(id).update({
                status: 'Order Accepted',
                newScheduleDateTime: order.scheduleDateTime || firebase.firestore.FieldValue.serverTimestamp()
            }).then(function () { location.reload(); });
        });
        $(document).on('click', '#reject_btn', function () {
            if (isBroadcastOpen(order)) {
                declineBroadcast();
                return;
            }
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
        function startOrder(early) {
            var payload = {
                status: 'Order Ongoing',
                startTime: firebase.firestore.FieldValue.serverTimestamp()
            };
            if (isHourly(priceUnit)) {
                payload.endTime = null;
            }
            if (early) {
                payload.startedEarly = true;
                payload.earlyStartAt = firebase.firestore.FieldValue.serverTimestamp();
            }
            database.collection('provider_orders').doc(id).update(payload).then(function () { location.reload(); });
        }
        $(document).on('click', '#start_btn', function () {
            if (isEarlyStart()) {
                var when = order.newScheduleDateTime || order.scheduleDateTime;
                var whenText = when && when.toDate ? ArrowDateTime.formatDate(when.toDate()) + ' ' + ArrowDateTime.formatTime(when.toDate()) : '';
                var base = '{{ trans("lang.early_start_confirm") }}';
                $('#early_start_message').text(whenText ? (base + ' (' + whenText + ')') : base);
                $('#early_start_modal').modal('show');
                return;
            }
            startOrder(false);
        });
        $('#early_start_confirm').on('click', function () {
            $('#early_start_modal').modal('hide');
            startOrder(true);
        });
        $(document).on('click', '#stop_timer_btn', function () {
            if (!storedStartTime) { return; }
            var hours = billableHours(storedStartTime, new Date());
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
            var hourly = isHourly(priceUnit);
            if (hourly && !storedEndTime) {
                $('.error_top').html('<p>{{ trans("lang.stop_the_timer") }}</p>').show();
                return;
            }
            if (hourly && order.paymentStatus !== true && !isCod(order.paymentMethod)) {
                $('.error_top').html('<p>{{ trans("lang.hourly_wait_payment") }}</p>').show();
                return;
            }
            var payload = {
                status: 'Order Completed',
                extraPaymentStatus: true,
                paymentStatus: hourly ? (order.paymentStatus === true || isCod(order.paymentMethod)) : true
            };
            if (hourly) {
                if (storedStartTime) {
                    payload.endTime = order.endTime || firebase.firestore.FieldValue.serverTimestamp();
                    payload.quantity = billableHours(storedStartTime, storedEndTime || new Date());
                    order.quantity = payload.quantity;
                }
            } else {
                payload.endTime = firebase.firestore.FieldValue.serverTimestamp();
            }
            database.collection('provider_orders').doc(id).update(payload).then(function () {
                return creditCodOnComplete();
            }).then(function () { location.reload(); });
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
