@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{ trans('lang.service_plural') }}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                <li class="breadcrumb-item active">{{ trans('lang.service_plural') }}</li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display:none;">{{ trans('lang.processing') }}</div>
        <div class="mb-3">
            <a class="btn btn-primary" href="{{ route('provider.services.create') }}"><i class="mdi mdi-plus"></i> {{ trans('lang.service_plural') }}</a>
        </div>
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-striped" id="serviceTable">
                    <thead>
                        <tr>
                            <th>{{ trans('lang.title') }}</th>
                            <th>{{ trans('lang.price') }}</th>
                            <th>{{ trans('lang.status') }}</th>
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
    $(function () {
        $("#data-table_processing").show();
        database.collection('providers_services').where('author', '==', providerId).get().then(function (snap) {
            var html = '';
            snap.docs.forEach(function (doc) {
                var s = doc.data();
                var price = s.disPrice && parseFloat(s.disPrice) > 0 ? s.disPrice : s.price;
                var st = s.publish ? '{{ trans("lang.active") }}' : '{{ trans("lang.inactive") }}';
                html += '<tr><td>' + (s.title || '') + '</td><td>' + (price || '') + '</td><td>' + st + '</td><td><a href="{{ url("provider/services/edit") }}/' + s.id + '">{{ trans("lang.edit") }}</a></td></tr>';
            });
            $('#serviceTable tbody').html(html || '<tr><td colspan="4">{{ trans("lang.no_record_found") }}</td></tr>');
            $("#data-table_processing").hide();
        }).catch(function () { $("#data-table_processing").hide(); });
    });
</script>
@endsection
