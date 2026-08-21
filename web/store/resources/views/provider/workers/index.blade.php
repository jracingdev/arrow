@extends('layouts.app')

@section('content')
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
            <a class="btn btn-primary" href="{{ route('provider.workers.create') }}"><i class="mdi mdi-plus"></i> {{ trans('lang.worker_plural') }}</a>
        </div>
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{ trans('lang.first_name') }}</th>
                            <th>{{ trans('lang.email') }}</th>
                            <th>{{ trans('lang.status') }}</th>
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
    $(function () {
        $("#data-table_processing").show();
        database.collection('providers_workers').where('providerId', '==', providerId).get().then(function (snap) {
            var html = '';
            snap.docs.forEach(function (doc) {
                var w = doc.data();
                var st = w.online ? '{{ trans("lang.active") }}' : '{{ trans("lang.inactive") }}';
                html += '<tr><td>' + (w.firstName || '') + ' ' + (w.lastName || '') + '</td><td>' + (w.email || '') + '</td><td>' + st + '</td><td><a href="{{ url("provider/workers/edit") }}/' + w.id + '">{{ trans("lang.edit") }}</a></td></tr>';
            });
            $('#worker_body').html(html || '<tr><td colspan="4">{{ trans("lang.no_record_found") }}</td></tr>');
            $("#data-table_processing").hide();
        }).catch(function () { $("#data-table_processing").hide(); });
    });
</script>
@endsection
