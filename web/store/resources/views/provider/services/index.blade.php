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
        <p class="text-muted">{{ trans('lang.service_publish_help') }}</p>
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-striped" id="serviceTable">
                    <thead>
                        <tr>
                            <th>{{ trans('lang.title') }}</th>
                            <th>{{ trans('lang.price') }}</th>
                            <th>{{ trans('lang.item_publish') }}</th>
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

    function isHourly(unit) {
        var u = (unit || '').toString().trim().toLowerCase();
        return u === 'hourly' || u === 'hour' || u === 'por hora';
    }
    function formatBrl(value) {
        var n = parseFloat(value);
        if (isNaN(n)) return value || '';
        return 'R$ ' + n.toFixed(2).replace('.', ',');
    }

    $(function () {
        $("#data-table_processing").show();
        database.collection('providers_services').where('author', '==', providerId).get().then(function (snap) {
            var html = '';
            snap.docs.forEach(function (doc) {
                var s = doc.data();
                var id = s.id || doc.id;
                var price = s.disPrice && parseFloat(s.disPrice) > 0 ? s.disPrice : s.price;
                var unit = isHourly(s.priceUnit) ? ' {{ trans("lang.per_hour_suffix") }}' : '';
                var checked = s.publish === true ? ' checked' : '';
                html += '<tr><td>' + (s.title || '') + '</td><td>' + formatBrl(price) + unit + '</td>';
                html += '<td><label class="switch"><input type="checkbox" class="service-publish"' + checked + ' data-id="' + id + '"><span class="slider round"></span></label></td>';
                html += '<td><a href="{{ url("provider/services/edit") }}/' + id + '">{{ trans("lang.edit") }}</a></td></tr>';
            });
            $('#serviceTable tbody').html(html || '<tr><td colspan="4">{{ trans("lang.no_record_found") }}</td></tr>');
            $("#data-table_processing").hide();
        }).catch(function () { $("#data-table_processing").hide(); });

        $(document).on('change', '.service-publish', function () {
            var id = $(this).data('id');
            var publish = $(this).is(':checked');
            var $input = $(this);
            $input.prop('disabled', true);
            database.collection('providers_services').doc(id).update({ publish: publish }).catch(function () {
                $input.prop('checked', !publish);
            }).then(function () {
                $input.prop('disabled', false);
            });
        });
    });
</script>
@endsection
