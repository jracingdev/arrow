@extends('layouts.app')
@section('content')
<div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor restaurantTitle">{{trans('lang.document_verification')}}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                    <li class="breadcrumb-item active">{{trans('lang.document_verification')}}</li>
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
                                    <a class="nav-link active vendor-name"
                                       href="{!! url()->current() !!}">{{trans('lang.document_details')}}</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">{{ trans('lang.document_upload_help') }}</p>
                            <div class="table-responsive m-t-10 doc-body"></div>
                            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
                                 aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document" style="max-width: 50%;">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close"
                                                    data-dismiss="modal"
                                                    aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <embed id="docImage"
                                                       src=""
                                                       frameBorder="0"
                                                       scrolling="auto"
                                                       height="100%"
                                                       width="100%"
                                                       style="height: 540px;"
                                                ></embed>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">{{trans('lang.close')}}</button>
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
    </div>
@endsection
@section('scripts')
<script>
    var id = "<?php echo $id;?>";
    var authRole = "{{ $authRole }}";
    var database = firebase.firestore();
    var uploadBase = "{{ url('document/upload') }}";
    window.arrowUserRole = authRole || '';

    function allowedDocumentTypes(role, includeDriverFallback) {
        if (role === 'provider') {
            return includeDriverFallback ? ['provider', 'ondemand', 'driver'] : ['provider', 'ondemand'];
        }
        if (role === 'vendor' || role === 'employee') {
            return ['vendor', 'store', 'restaurant'];
        }
        return ['provider', 'ondemand', 'driver', 'vendor', 'store', 'restaurant'];
    }

    function isEnabledDoc(doc) {
        return doc.enable === true || doc.enable === 'true' || doc.enable === 1;
    }

    function statusLabel(status, hasFile) {
        if (status === 'approved') return { text: "{{ trans('lang.document_status_approved') }}", cls: 'success' };
        if (status === 'rejected') return { text: "{{ trans('lang.document_status_rejected') }}", cls: 'danger' };
        if (status === 'uploaded' || hasFile) return { text: "{{ trans('lang.document_status_pending') }}", cls: 'warning' };
        return { text: "{{ trans('lang.document_status_not_sent') }}", cls: 'secondary' };
    }

    function escapeHtml(value) {
        return $('<div>').text(value || '').html();
    }

    async function loadCatalog(role) {
        var types = allowedDocumentTypes(role);
        var collected = [];
        var seen = {};
        try {
            var enabledSnap = await database.collection('documents').where('enable', '==', true).get();
            enabledSnap.docs.forEach(function (ele) {
                var doc = ele.data() || {};
                doc.id = doc.id || ele.id;
                collected.push(doc);
            });
        } catch (err) {
            console.warn('documents enable query failed', err);
        }
        if (!collected.length) {
            try {
                var allSnap = await database.collection('documents').get();
                allSnap.docs.forEach(function (ele) {
                    var doc = ele.data() || {};
                    doc.id = doc.id || ele.id;
                    collected.push(doc);
                });
            } catch (err) {
                console.warn('documents full query failed', err);
            }
        }
        function matchTypes(allowed) {
            var matched = [];
            var used = {};
            collected.forEach(function (doc) {
                var docId = (doc.id || '').toString();
                if (!docId || seen[docId] || used[docId]) return;
                var docType = (doc.type || '').toString().toLowerCase().replace(/[\s_-]/g, '');
                var aliases = {
                    ondemand: 'ondemand',
                    ondemandervice: 'ondemand',
                    provider: 'provider',
                    prestador: 'provider',
                    driver: 'driver',
                    vendor: 'vendor',
                    store: 'store',
                    restaurant: 'restaurant'
                };
                var normalized = aliases[docType] || docType;
                if (allowed.indexOf(normalized) < 0) return;
                if (!isEnabledDoc(doc) && collected.some(isEnabledDoc)) return;
                used[docId] = true;
                matched.push(doc);
            });
            return matched;
        }
        var filtered = matchTypes(types);
        if (role === 'provider' && !filtered.length) {
            filtered = matchTypes(allowedDocumentTypes(role, true));
        }
        filtered.forEach(function (doc) {
            seen[(doc.id || '').toString()] = true;
        });
        return filtered;
    }

    function findUpload(uploaded, docId) {
        if (!Array.isArray(uploaded)) return null;
        return uploaded.filter(function (item) {
            return item && String(item.documentId || '').trim() === String(docId || '').trim();
        })[0] || null;
    }

    $(document).ready(function () {
        jQuery("#data-table_processing").show();
        $('#exampleModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            $(this).find('#docImage').attr('src', button.data('image'));
        });

        database.collection('users').doc(id).get().then(function (snap) {
            if (snap.exists) {
                window.arrowUserRole = (snap.data() || {}).role || window.arrowUserRole;
            }
            return loadDocumentTable();
        }).catch(function () {
            return loadDocumentTable();
        });
    });

    async function loadDocumentTable() {
        var role = window.arrowUserRole || authRole || '';
        var documents = await loadCatalog(role);
        var html = '<table id="taxTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">';
        html += '<thead><tr>';
        html += '<th>{{trans('lang.name')}}</th>';
        html += '<th>{{trans('lang.status')}}</th>';
        html += '<th>{{trans('lang.action')}}</th>';
        html += '</tr></thead><tbody id="document-list-body"></tbody></table>';

        if (!documents.length) {
            $(".doc-body").html('<p class="text-muted">' + (role === 'provider'
                ? "{{ trans('lang.provider_documents_empty') }}"
                : "{{ trans('lang.no_record_found') }}") + '</p>');
            jQuery("#data-table_processing").hide();
            return;
        }

        var verifySnap = await database.collection('documents_verify').doc(id).get();
        var uploaded = (verifySnap.data() && verifySnap.data().documents) ? verifySnap.data().documents : [];
        $(".doc-body").html(html);

        documents.forEach(function (doc) {
            var docRef = findUpload(uploaded, doc.id);
            var hasFront = !!(docRef && docRef.frontImage);
            var hasBack = !!(docRef && docRef.backImage);
            var hasFile = hasFront || hasBack;
            var rawStatus = ((docRef && docRef.status) || '').toString().toLowerCase();
            if (rawStatus !== 'approved' && rawStatus !== 'rejected' && rawStatus !== 'uploaded') {
                rawStatus = hasFile ? 'pending' : 'pending';
            }
            var label = statusLabel(rawStatus, hasFile);
            var title = escapeHtml(doc.title || '');
            if (hasFront && doc.frontSide) {
                title += ' <a href="#" class="badge badge-info py-2 px-3 font-weight-bold" data-toggle="modal" data-target="#exampleModal" data-image="' + escapeHtml(docRef.frontImage) + '">{{trans('lang.view_front_image')}}</a>';
            }
            if (hasBack && doc.backSide) {
                title += ' <a href="#" class="badge badge-info py-2 px-3 font-weight-bold" data-toggle="modal" data-target="#exampleModal" data-image="' + escapeHtml(docRef.backImage) + '">{{trans('lang.view_back_image')}}</a>';
            }
            var statusHtml = '<span class="badge badge-' + label.cls + ' py-2 px-3 font-weight-bold">' + label.text + '</span>';
            if (rawStatus === 'rejected' && docRef && docRef.rejectReason) {
                statusHtml += '<div class="small text-danger mt-1">{{ trans('lang.document_reject_reason') }}: ' + escapeHtml(docRef.rejectReason) + '</div>';
            }
            var actionLabel = hasFile ? "{{ trans('lang.document_replace_action') }}" : "{{ trans('lang.document_upload_action') }}";
            var actionHtml = '';
            if (rawStatus !== 'approved') {
                actionHtml = '<a class="btn btn-sm btn-primary" href="' + uploadBase + '/' + encodeURIComponent(String(doc.id).trim()) + '"><i class="fa fa-upload"></i> ' + actionLabel + '</a>';
            } else {
                actionHtml = '<span class="text-muted">{{ trans('lang.document_status_approved') }}</span>';
            }
            $('#document-list-body').append('<tr><td>' + title + '</td><td>' + statusHtml + '</td><td class="action-btn">' + actionHtml + '</td></tr>');
        });

        $('#taxTable').DataTable({
            order: [[0, 'asc']],
            columnDefs: [{ orderable: false, targets: [1, 2] }],
            language: (typeof datatableLang !== 'undefined') ? datatableLang : { emptyTable: "{{ trans('lang.no_record_found') }}" }
        });
        jQuery("#data-table_processing").hide();
    }
</script>
@endsection
