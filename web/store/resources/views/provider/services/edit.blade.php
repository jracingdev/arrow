@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-12">
            <h3 class="text-themecolor">{{ trans('lang.service_plural') }}</h3>
        </div>
    </div>
    <div class="card-body">
        <div class="error_top"></div>
        <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display:none;">{{ trans('lang.processing') }}</div>
        <div class="row vendor_payout_create">
            <div class="vendor_payout_create-inner">
                <fieldset>
                    <legend>{{ trans('lang.service_plural') }}</legend>
                    <div class="form-group row width-50">
                        <label class="col-3 control-label">{{ trans('lang.title') }}</label>
                        <div class="col-7"><input type="text" class="form-control" id="title"></div>
                    </div>
                    <div class="form-group row width-50">
                        <label class="col-3 control-label">{{ trans('lang.price') }}</label>
                        <div class="col-7"><input type="number" class="form-control" id="price" min="0"></div>
                    </div>
                    <div class="form-group row width-50">
                        <label class="col-3 control-label">{{ trans('lang.price_unit') }}</label>
                        <div class="col-7">
                            <select class="form-control" id="priceUnit">
                                <option value="Fixed">{{ trans('lang.fixed') }}</option>
                                <option value="Hourly">{{ trans('lang.hourly') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row width-100">
                        <label class="col-3 control-label">{{ trans('lang.vendor_description') }}</label>
                        <div class="col-7"><textarea class="form-control" id="description" rows="3"></textarea></div>
                    </div>
                    <div class="form-group row width-100">
                        <div class="form-check">
                            <input type="checkbox" id="publish">
                            <label for="publish">{{ trans('lang.active') }}</label>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="form-group col-12 text-center btm-btn">
            <button type="button" class="btn btn-primary" id="save_btn"><i class="fa fa-save"></i> {{ trans('lang.save') }}</button>
            <a href="{{ route('provider.services') }}" class="btn btn-default">{{ trans('lang.cancel') }}</a>
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
        database.collection('providers_services').doc(id).get().then(function (snap) {
            var s = snap.data();
            if (!s || s.author !== providerId) {
                window.location = "{{ route('provider.services') }}";
                return;
            }
            $('#title').val(s.title || '');
            $('#price').val(s.price || '');
            $('#priceUnit').val(s.priceUnit || 'Fixed');
            $('#description').val(s.description || '');
            $('#publish').prop('checked', s.publish === true);
            $("#data-table_processing").hide();
        });
        $('#save_btn').on('click', function () {
            database.collection('providers_services').doc(id).update({
                title: $('#title').val(),
                price: String($('#price').val()),
                priceUnit: $('#priceUnit').val(),
                description: $('#description').val(),
                publish: $('#publish').is(':checked')
            }).then(function () {
                window.location = "{{ route('provider.services') }}";
            });
        });
    });
</script>
@endsection
