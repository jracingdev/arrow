@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor restaurantTitle">{{trans('lang.provider_document_details')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('providers') !!}">{{trans('lang.provider_plural')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('documents.pending') !!}">{{trans('lang.document_pending_queue')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.provider_document_details')}}</li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
                            <li class="nav-item">
                                <a class="nav-link active vendor-name" href="{!! url()->current() !!}">{{trans('lang.provider_document_details')}}</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive m-t-10 doc-body"></div>
                        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document" style="max-width: 50%;">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <embed id="docImage" src="" frameBorder="0" height="100%" width="100%" style="height: 540px;"></embed>
                                    </div>
                                </div>
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
<script>
    var id = "<?php echo $id;?>";
    var database = firebase.firestore();
    var ref = database.collection('users').where("id", "==", id);
    var fcmToken = "";
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
    $(document).ready(async function () {
        jQuery("#data-table_processing").show();
        $('#exampleModal').on('show.bs.modal', function (event) {
            $(this).find('#docImage').attr('src', $(event.relatedTarget).data('image'));
        });
        ref.get().then(function (snapshots) {
            if (!snapshots.docs.length) return;
            var vendor = snapshots.docs[0].data();
            if (vendor.fcmToken) fcmToken = vendor.fcmToken;
            $(".vendor-name").text((vendor.firstName || '') + ' ' + (vendor.lastName || '') + " — {{trans('lang.provider_document_details')}}");
        });
        var html = '<table id="documentTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">';
        html += "<thead><tr><th>{{trans('lang.name')}}</th><th>{{trans('lang.status')}}</th><th>{{trans('lang.document_reject_reason')}}</th><th>{{trans('lang.action')}}</th></tr></thead><tbody></tbody></table>";
        $(".doc-body").append(html);
        var docsSnap = await database.collection('documents').where('enable', '==', true).get();
        var documents = docsSnap.docs.map(function (ele) { return ele.data(); }).filter(function (doc) {
            return doc.type === 'provider' || doc.type === 'ondemand';
        });
        var verifySnap = await database.collection('documents_verify').doc(id).get();
        var uploaded = (verifySnap.data() && verifySnap.data().documents) ? verifySnap.data().documents : [];
        documents.forEach(function (doc) {
            var docRef = uploaded.filter(function (item) { return item.documentId == doc.id; })[0] || null;
            var trhtml = '<tr>';
            var title = doc.title || '';
            if (docRef && ((docRef.frontImage && doc.frontSide) || (docRef.backImage && doc.backSide))) {
                title += '&nbsp;';
                if (docRef.frontImage && doc.frontSide) {
                    title += '<a href="#" class="badge badge-info" data-toggle="modal" data-target="#exampleModal" data-image="' + docRef.frontImage + '">{{trans("lang.view_front_image")}}</a>&nbsp;';
                }
                if (docRef.backImage && doc.backSide) {
                    title += '<a href="#" class="badge badge-info" data-toggle="modal" data-target="#exampleModal" data-image="' + docRef.backImage + '">{{trans("lang.view_back_image")}}</a>';
                }
            }
            trhtml += '<td>' + title + '</td>';
            var status = docRef && docRef.status == "approved" ? 'approved' : ((docRef && docRef.status == "rejected") ? "rejected" : ((docRef && docRef.status == "uploaded") ? 'uploaded' : 'pending'));
            var badge = status == "approved" ? 'success' : (status == "rejected" ? 'danger' : (status == "uploaded" ? 'primary' : 'warning'));
            trhtml += '<td><span class="badge badge-' + badge + ' py-2 px-3">' + status + '</span></td>';
            trhtml += '<td>' + $('<div>').text((docRef && docRef.rejectReason) ? docRef.rejectReason : '').html() + '</td>';
            trhtml += '<td class="action-btn">';
            if (status !== 'approved') {
                trhtml += '<a href="javascript:void(0);" class="btn btn-sm btn-success verify-doc" id="approve-doc" data-title="' + doc.title + '" data-id="' + doc.id + '">{{trans("lang.approve")}}</a>&nbsp;';
            }
            if (status !== 'rejected') {
                trhtml += '<a href="javascript:void(0);" class="btn btn-sm btn-danger verify-doc" id="disapprove-doc" data-title="' + doc.title + '" data-id="' + doc.id + '">{{trans("lang.reject")}}</a>';
            }
            trhtml += '</td></tr>';
            $("tbody").append(trhtml);
        });
        $('#documentTable').DataTable({
            order: [[0, 'asc']],
            columnDefs: [{ orderable: false, targets: [1, 2, 3] }],
        });
        jQuery("#data-table_processing").hide();
    });
    $(document).on('click', '.verify-doc', function () {
        var status = $(this).attr('id') == "approve-doc" ? "approved" : "rejected";
        var reason = '';
        if (status === 'rejected') {
            reason = promptRejectReason();
            if (reason === null) return;
        }
        jQuery("#data-table_processing").show();
        var docId = $(this).attr('data-id');
        var docTitle = $(this).attr('data-title');
        database.collection('documents_verify').doc(id).get().then(async function (doc) {
            var objects = (doc.data() && doc.data().documents) ? doc.data().documents.slice() : [];
            var keydataId = objects.findIndex(function (d) { return d.documentId == docId; });
            if (keydataId >= 0) {
                objects[keydataId].status = status;
                objects[keydataId].rejectReason = status === 'rejected' ? reason : '';
            } else {
                objects.push({ documentId: docId, status: status, frontImage: '', backImage: '', rejectReason: status === 'rejected' ? reason : '' });
            }
            await database.collection('documents_verify').doc(id).set({
                id: id,
                type: 'provider',
                documents: objects,
                rejectReason: status === 'rejected' ? reason : '',
                pending: status !== 'approved'
            }, { merge: true });
            var enableDocIds = await getDocId();
            var snapshotsVendor = await ref.get();
            if (snapshotsVendor.docs.length > 0) {
                await vendorDocVerification(enableDocIds, snapshotsVendor);
            }
            jQuery("#data-table_processing").hide();
            window.location.reload();
        });
    });
    async function getDocId() {
        var enableDocIds = [];
        var snaps = await database.collection('documents').where('enable', "==", true).get();
        snaps.forEach(function (doc) {
            var type = doc.data().type;
            if (type === 'provider' || type === 'ondemand') enableDocIds.push(doc.data().id);
        });
        return enableDocIds;
    }
    async function vendorDocVerification(enableDocIds, snapshotsVendor) {
        await Promise.all(snapshotsVendor.docs.map(async function (vendor) {
            var docrefSnapshot = await database.collection('documents_verify').doc(vendor.id).get();
            var uploaded = (docrefSnapshot.data() && docrefSnapshot.data().documents) ? docrefSnapshot.data().documents : [];
            var approvedIds = uploaded.filter(function (d) { return d.status == 'approved'; }).map(function (d) { return d.documentId; });
            var allApproved = enableDocIds.length > 0 && enableDocIds.every(function (docId) { return approvedIds.indexOf(docId) !== -1; });
            await database.collection('users').doc(vendor.id).update({ isDocumentVerify: allApproved });
            await database.collection('documents_verify').doc(vendor.id).set({ pending: !allApproved }, { merge: true });
        }));
        return true;
    }
</script>
@endsection
