@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-12"><h3 class="text-themecolor">{{ trans('lang.worker_plural') }}</h3></div>
    </div>
    <div class="card-body">
        <div class="error_top"></div>
        <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display:none;">{{ trans('lang.processing') }}</div>
        <div class="row vendor_payout_create">
            <div class="vendor_payout_create-inner">
                <fieldset>
                    <legend>{{ trans('lang.worker_plural') }}</legend>
                    <div class="form-group row width-50">
                        <label class="col-3 control-label">{{ trans('lang.first_name') }}</label>
                        <div class="col-7"><input type="text" class="form-control" id="firstName"></div>
                    </div>
                    <div class="form-group row width-50">
                        <label class="col-3 control-label">{{ trans('lang.last_name') }}</label>
                        <div class="col-7"><input type="text" class="form-control" id="lastName"></div>
                    </div>
                    <div class="form-group row width-50">
                        <label class="col-3 control-label">{{ trans('lang.phone') }}</label>
                        <div class="col-7"><input type="text" class="form-control" id="phone"></div>
                    </div>
                    <div class="form-group row width-50">
                        <label class="col-3 control-label">{{ trans('lang.vendors_payout_amount') }}</label>
                        <div class="col-7"><input type="number" class="form-control" id="salary" min="0"></div>
                    </div>
                    <div class="form-group row width-100">
                        <div class="form-check">
                            <input type="checkbox" id="online">
                            <label for="online">{{ trans('lang.active') }}</label>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="form-group col-12 text-center btm-btn">
            <button type="button" class="btn btn-primary" id="save_btn"><i class="fa fa-save"></i> {{ trans('lang.save') }}</button>
            <a href="{{ route('provider.workers') }}" class="btn btn-default">{{ trans('lang.cancel') }}</a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    var database = firebase.firestore();
    var id = "{{ $id }}";
    var providerId = "{{ $providerId }}";
    $(function () {
        $("#data-table_processing").show();
        database.collection('providers_workers').doc(id).get().then(function (snap) {
            var w = snap.data();
            if (!w || w.providerId !== providerId) {
                window.location = "{{ route('provider.workers') }}";
                return;
            }
            $('#firstName').val(w.firstName || '');
            $('#lastName').val(w.lastName || '');
            $('#phone').val(w.phoneNumber || '');
            $('#salary').val(w.salary || '');
            $('#online').prop('checked', w.online === true);
            $("#data-table_processing").hide();
        });
        $('#save_btn').on('click', function () {
            database.collection('providers_workers').doc(id).update({
                firstName: $('#firstName').val(),
                lastName: $('#lastName').val(),
                phoneNumber: $('#phone').val(),
                salary: $('#salary').val(),
                online: $('#online').is(':checked'),
                active: $('#online').is(':checked')
            }).then(function () {
                window.location = "{{ route('provider.workers') }}";
            });
        });
    });
</script>
@endsection
