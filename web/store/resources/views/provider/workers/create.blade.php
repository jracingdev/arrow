@extends('layouts.app')
<style>
.autocomplete-item { padding: 8px 10px; cursor: pointer; border-bottom: 1px solid #eee; background: #fff; }
.autocomplete-item:hover { background: #f5f5f5; }
</style>

@section('content')
<?php
$countries = file_get_contents(public_path('countriesdata.json'));
$countries = json_decode($countries);
$countries = (array) $countries;
$newcountries = array();
foreach ($countries as $valuecountry) {
    $newcountries[$valuecountry->phoneCode] = $valuecountry;
}
?>
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
                        <label class="col-3 control-label">{{ trans('lang.email') }}</label>
                        <div class="col-7"><input type="email" class="form-control" id="email"></div>
                    </div>
                    <div class="form-group row width-50">
                        <label class="col-3 control-label">{{ trans('lang.password') }}</label>
                        <div class="col-7"><input type="password" class="form-control" id="password"></div>
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
                        <label class="col-3 control-label">{{ trans('lang.vendor_address') }}</label>
                        <div class="col-7">
                            <input type="text" class="form-control" id="address">
                            <input type="hidden" id="latitude">
                            <input type="hidden" id="longitude">
                            <div id="autocomplete-list"></div>
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
    var geoFirestore = new GeoFirestore(database);
    var providerId = "<?php echo $id; ?>";
    var createdAt = firebase.firestore.FieldValue.serverTimestamp();

    $('#address').on('input', function () {
        var query = this.value;
        if (query.length < 3) { $('#autocomplete-list').empty(); return; }
        fetch('https://nominatim.openstreetmap.org/search?q=' + encodeURIComponent(query) + '&format=json')
            .then(function (r) { return r.json(); })
            .then(function (data) {
                $('#autocomplete-list').empty();
                data.slice(0, 5).forEach(function (place) {
                    var item = $('<div class="autocomplete-item"></div>').text(place.display_name);
                    item.on('click', function () {
                        $('#address').val(place.display_name);
                        $('#latitude').val(place.lat);
                        $('#longitude').val(place.lon);
                        $('#autocomplete-list').empty();
                    });
                    $('#autocomplete-list').append(item);
                });
            });
    });

    $('#save_btn').on('click', function () {
        var firstName = $('#firstName').val();
        var lastName = $('#lastName').val();
        var email = $('#email').val();
        var password = $('#password').val();
        var phone = $('#phone').val();
        var salary = $('#salary').val();
        var lat = parseFloat($('#latitude').val());
        var lng = parseFloat($('#longitude').val());
        if (!firstName || !email || !password || !phone || !salary || isNaN(lat) || isNaN(lng)) {
            $('.error_top').html('<p>{{ trans("lang.please_enter_details") }}</p>').show();
            return;
        }
        $("#data-table_processing").show();
        firebase.auth().createUserWithEmailAndPassword(email, password).then(function (cred) {
            var user_id = cred.user.uid;
            return geoFirestore.collection('providers_workers').doc(user_id).set({
                firstName: firstName,
                lastName: lastName,
                email: email,
                phoneNumber: phone,
                salary: salary,
                address: $('#address').val(),
                profilePictureURL: '',
                active: true,
                reviewsCount: 0,
                reviewsSum: 0,
                id: user_id,
                createdAt: createdAt,
                latitude: lat,
                longitude: lng,
                online: false,
                providerId: providerId,
                coordinates: new firebase.firestore.GeoPoint(lat, lng)
            });
        }).then(function () {
            window.location = "{{ route('provider.workers') }}";
        }).catch(function (err) {
            $("#data-table_processing").hide();
            $('.error_top').html('<p>' + (err.message || err) + '</p>').show();
        });
    });
</script>
@endsection
