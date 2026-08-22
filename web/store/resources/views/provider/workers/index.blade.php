@extends('layouts.app')

@section('content')
<style>
.worker-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; background: #e9ecef; display: inline-flex; align-items: center; justify-content: center; font-weight: 600; color: #555; }
.worker-name { font-weight: 600; }
.worker-meta { font-size: 12px; color: #6c757d; }
</style>
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{ trans('lang.worker_plural') }}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                <li class="breadcrumb-item active">{{ trans('lang.worker_plural') }}</li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display:none;">{{ trans('lang.processing') }}</div>
        <div class="mb-3">
            <a class="btn btn-primary" href="{{ route('provider.workers.create') }}"><i class="mdi mdi-plus"></i> {{ trans('lang.worker_create') }}</a>
        </div>
        <p class="text-muted">{{ trans('lang.worker_online_help') }}</p>
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-striped" id="workerTable">
                    <thead>
                        <tr>
                            <th>{{ trans('lang.worker_photo') }}</th>
                            <th>{{ trans('lang.first_name') }}</th>
                            <th>{{ trans('lang.phone') }}</th>
                            <th>{{ trans('lang.rating') }}</th>
                            <th>{{ trans('lang.worker_online') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="worker_body"></tbody>
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
    var editBase = "{{ url('provider/workers/edit') }}";

    function escapeHtml(value) {
        return $('<div>').text(value == null ? '' : String(value)).html();
    }

    function formatRating(sum, count) {
        count = Number(count) || 0;
        if (count <= 0) return '—';
        var avg = (Number(sum) || 0) / count;
        return avg.toFixed(1) + ' (' + count + ' {{ trans("lang.worker_reviews") }})';
    }

    function avatarHtml(worker) {
        var url = worker.profilePictureURL || '';
        var name = ((worker.firstName || '') + ' ' + (worker.lastName || '')).trim();
        var initial = name ? escapeHtml(name.charAt(0).toUpperCase()) : '?';
        if (url) {
            return '<img class="worker-avatar" src="' + escapeHtml(url) + '" alt="" onerror="this.style.display=\'none\';this.nextSibling.style.display=\'inline-flex\';"><span class="worker-avatar" style="display:none;">' + initial + '</span>';
        }
        return '<span class="worker-avatar">' + initial + '</span>';
    }

    function renderWorkers(docs) {
        if (!docs.length) {
            $('#worker_body').html('<tr><td colspan="6">{{ trans("lang.worker_empty") }}</td></tr>');
            return;
        }
        var html = '';
        docs.forEach(function (doc) {
            var w = doc.data() || {};
            var id = w.id || doc.id;
            var name = ((w.firstName || '') + ' ' + (w.lastName || '')).trim() || '—';
            var phone = w.phoneNumber || w.phone || '';
            var email = w.email || '';
            var checked = w.online === true ? ' checked' : '';
            html += '<tr data-id="' + escapeHtml(id) + '">';
            html += '<td>' + avatarHtml(w) + '</td>';
            html += '<td><div class="worker-name">' + escapeHtml(name) + '</div>';
            if (email) html += '<div class="worker-meta">' + escapeHtml(email) + '</div>';
            html += '</td>';
            html += '<td>' + escapeHtml(phone) + '</td>';
            html += '<td>' + formatRating(w.reviewsSum, w.reviewsCount) + '</td>';
            html += '<td><label class="switch"><input type="checkbox" class="worker-online"' + checked + ' data-id="' + escapeHtml(id) + '"><span class="slider round"></span></label></td>';
            html += '<td><a href="' + editBase + '/' + encodeURIComponent(id) + '">{{ trans("lang.edit") }}</a></td>';
            html += '</tr>';
        });
        $('#worker_body').html(html);
    }

    $(function () {
        $("#data-table_processing").show();
        database.collection('providers_workers').where('providerId', '==', providerId).get().then(function (snap) {
            renderWorkers(snap.docs);
            $("#data-table_processing").hide();
        }).catch(function () {
            $("#data-table_processing").hide();
            $('#worker_body').html('<tr><td colspan="6">{{ trans("lang.no_record_found") }}</td></tr>');
        });

        $(document).on('change', '.worker-online', function () {
            var id = $(this).data('id');
            var online = $(this).is(':checked');
            var $input = $(this);
            $input.prop('disabled', true);
            database.collection('providers_workers').doc(id).update({ online: online }).catch(function () {
                $input.prop('checked', !online);
            }).then(function () {
                $input.prop('disabled', false);
            });
        });
    });
</script>
@endsection
