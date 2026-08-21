@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.document_pending_queue')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('documents') !!}">{{trans('lang.document_plural')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.document_pending_queue')}}</li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div class="table-list">
            <div class="row">
                <div class="col-12">
                    <div class="card border">
                        <div class="card-header d-flex justify-content-between align-items-center border-0">
                            <div class="card-header-title">
                                <h3 class="text-dark-2 mb-2 h4">{{trans('lang.document_pending_queue')}}</h3>
                                <p class="mb-0 text-dark-2">{{trans('lang.document_pending_queue_text')}}</p>
                            </div>
                            <div class="card-header-right">
                                <a class="btn btn-default btn-sm" href="{!! route('documents') !!}">{{trans('lang.document_plural')}}</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive m-t-10">
                                <table id="pendingDocsTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>{{trans('lang.document_applicant')}}</th>
                                            <th>{{trans('lang.document_role')}}</th>
                                            <th>{{trans('lang.title')}}</th>
                                            <th>{{trans('lang.document_files')}}</th>
                                            <th>{{trans('lang.status')}}</th>
                                            <th>{{trans('lang.document_reject_reason')}}</th>
                                            <th>{{trans('lang.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="pending_docs_body"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="docPreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document" style="max-width: 50%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <embed id="docPreviewImage" src="" frameBorder="0" height="100%" width="100%" style="height: 540px;"></embed>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    var database = firebase.firestore();
    var roleLabels = {
        driver: "{{trans('lang.document_driver')}}",
        vendor: "{{trans('lang.document_vendor')}}",
        store: "{{trans('lang.document_store')}}",
        owner: "{{trans('lang.document_owner')}}",
        provider: "{{trans('lang.document_provider')}}",
        ondemand: "{{trans('lang.document_ondemand')}}",
        car: "{{trans('lang.document_car')}}"
    };
    function roleLabel(role) {
        return roleLabels[role] || role || '-';
    }
    function reviewUrl(role, uid) {
        if (role === 'driver') return "{{ route('drivers.document', ':id') }}".replace(':id', uid);
        if (role === 'vendor' || role === 'store') return "{{ route('vendors.document', ':id') }}".replace(':id', uid);
        if (role === 'owner') return "{{ route('owners.document', ':id') }}".replace(':id', uid);
        return "{{ route('providers.document', ':id') }}".replace(':id', uid);
    }
    function catalogTypesFor(role, verifyType) {
        var type = verifyType || role || 'driver';
        if (type === 'car') return ['driver', 'car'];
        if (type === 'store') return ['vendor', 'store'];
        if (type === 'ondemand') return ['provider', 'ondemand'];
        if (type === 'provider') return ['provider', 'ondemand'];
        if (type === 'driver') return ['driver', 'car'];
        if (type === 'vendor') return ['vendor', 'store'];
        return [type];
    }
    function promptRejectReason() {
        var reason = prompt("{{trans('lang.document_reject_reason_prompt')}}");
        if (reason === null) return null;
        reason = (reason || '').trim();
        if (!reason) {
            alert("{{trans('lang.document_reject_reason_required')}}");
            return null;
        }
        return reason;
    }
    async function getEnableDocIds(types) {
        var ids = [];
        for (var i = 0; i < types.length; i++) {
            var snaps = await database.collection('documents').where('type', '==', types[i]).where('enable', '==', true).get();
            snaps.forEach(function (doc) { ids.push(doc.data().id); });
        }
        return ids;
    }
    async function applyUserVerifyFlag(uid, role, types) {
        var enableDocIds = await getEnableDocIds(types);
        var verifySnap = await database.collection('documents_verify').doc(uid).get();
        var uploaded = (verifySnap.data() && verifySnap.data().documents) ? verifySnap.data().documents : [];
        var approvedIds = uploaded.filter(function (d) { return d.status === 'approved'; }).map(function (d) { return d.documentId; });
        var allApproved = enableDocIds.length > 0 && enableDocIds.every(function (id) { return approvedIds.indexOf(id) !== -1; });
        var patch = { isDocumentVerify: allApproved };
        if (role === 'driver') {
            patch.isActive = allApproved;
        }
        await database.collection('users').doc(uid).update(patch);
        await database.collection('documents_verify').doc(uid).set({
            pending: !allApproved
        }, { merge: true });
        return allApproved;
    }
    $(document).ready(async function () {
        $('#docPreviewModal').on('show.bs.modal', function (event) {
            var img = $(event.relatedTarget).data('image');
            $(this).find('#docPreviewImage').attr('src', img);
        });
        jQuery("#data-table_processing").show();
        var catalog = {};
        var catalogSnap = await database.collection('documents').get();
        catalogSnap.forEach(function (doc) { catalog[doc.id] = doc.data(); });
        var verifySnap = await database.collection('documents_verify').get();
        var rows = [];
        await Promise.all(verifySnap.docs.map(async function (snap) {
            var data = snap.data() || {};
            var docs = data.documents || [];
            var needsReview = data.pending === true || docs.some(function (d) {
                return d.status === 'pending' || d.status === 'uploaded' || d.status === 'rejected';
            });
            if (!needsReview || !docs.length) return;
            var uid = data.id || snap.id;
            var userSnap = await database.collection('users').doc(uid).get();
            var user = userSnap.exists ? (userSnap.data() || {}) : {};
            var role = data.type || user.role || '';
            var name = ((user.firstName || '') + ' ' + (user.lastName || '')).trim() || user.email || uid;
            docs.forEach(function (item) {
                var status = item.status || 'pending';
                if (status !== 'pending' && status !== 'uploaded' && status !== 'rejected' && status !== 'approved') {
                    status = 'pending';
                }
                if (status === 'approved' && data.pending !== true) return;
                var meta = catalog[item.documentId] || {};
                rows.push({
                    uid: uid,
                    name: name,
                    role: role,
                    userRole: user.role || role,
                    documentId: item.documentId,
                    title: meta.title || item.documentId || '-',
                    status: status,
                    frontImage: item.frontImage || '',
                    backImage: item.backImage || '',
                    rejectReason: item.rejectReason || data.rejectReason || '',
                    verifyType: data.type
                });
            });
        }));
        rows.sort(function (a, b) { return (a.name || '').localeCompare(b.name || ''); });
        var html = '';
        rows.forEach(function (row, idx) {
            var files = '';
            if (row.frontImage) {
                files += '<a href="#" class="badge badge-info" data-toggle="modal" data-target="#docPreviewModal" data-image="' + row.frontImage + '">{{trans("lang.view_front_image")}}</a> ';
            }
            if (row.backImage) {
                files += '<a href="#" class="badge badge-info" data-toggle="modal" data-target="#docPreviewModal" data-image="' + row.backImage + '">{{trans("lang.view_back_image")}}</a>';
            }
            if (!files) files = '-';
            var badgeClass = row.status === 'approved' ? 'success' : (row.status === 'rejected' ? 'danger' : (row.status === 'uploaded' ? 'primary' : 'warning'));
            var actions = '<a href="' + reviewUrl(row.userRole || row.role, row.uid) + '" class="btn btn-sm btn-default">{{trans("lang.view")}}</a> ';
            if (row.status !== 'approved') {
                actions += '<button type="button" class="btn btn-sm btn-success queue-approve" data-idx="' + idx + '">{{trans("lang.approve")}}</button> ';
            }
            if (row.status !== 'rejected') {
                actions += '<button type="button" class="btn btn-sm btn-danger queue-reject" data-idx="' + idx + '">{{trans("lang.reject")}}</button>';
            }
            html += '<tr data-idx="' + idx + '">';
            html += '<td>' + $('<div>').text(row.name).html() + '</td>';
            html += '<td>' + roleLabel(row.userRole || row.role) + '</td>';
            html += '<td>' + $('<div>').text(row.title).html() + '</td>';
            html += '<td>' + files + '</td>';
            html += '<td><span class="badge badge-' + badgeClass + ' py-2 px-3">' + row.status + '</span></td>';
            html += '<td>' + $('<div>').text(row.rejectReason || '').html() + '</td>';
            html += '<td class="action-btn">' + actions + '</td>';
            html += '</tr>';
        });
        $('#pending_docs_body').html(html);
        window._pendingDocRows = rows;
        $('#pendingDocsTable').DataTable({
            order: [[0, 'asc']],
            columnDefs: [{ orderable: false, targets: [3, 6] }],
            language: datatableLang
        });
        jQuery("#data-table_processing").hide();
    });
    async function updateQueueStatus(idx, status, reason) {
        var row = window._pendingDocRows[idx];
        if (!row) return;
        jQuery("#data-table_processing").show();
        var ref = database.collection('documents_verify').doc(row.uid);
        var snap = await ref.get();
        var objects = (snap.data() && snap.data().documents) ? snap.data().documents.slice() : [];
        var found = false;
        objects = objects.map(function (doc) {
            if (doc.documentId !== row.documentId) return doc;
            found = true;
            var next = Object.assign({}, doc, { status: status });
            if (status === 'rejected') {
                next.rejectReason = reason;
            } else {
                next.rejectReason = '';
            }
            return next;
        });
        if (!found) {
            objects.push({ documentId: row.documentId, status: status, frontImage: row.frontImage, backImage: row.backImage, rejectReason: reason || '' });
        }
        var parentReason = status === 'rejected' ? reason : '';
        await ref.set({
            id: row.uid,
            type: row.verifyType || row.role || row.userRole,
            documents: objects,
            rejectReason: parentReason
        }, { merge: true });
        await applyUserVerifyFlag(row.uid, row.userRole || row.role, catalogTypesFor(row.userRole || row.role, row.verifyType));
        window.location.reload();
    }
    $(document).on('click', '.queue-approve', function () {
        updateQueueStatus($(this).data('idx'), 'approved', '');
    });
    $(document).on('click', '.queue-reject', function () {
        var reason = promptRejectReason();
        if (reason === null) return;
        updateQueueStatus($(this).data('idx'), 'rejected', reason);
    });
</script>
@endsection
