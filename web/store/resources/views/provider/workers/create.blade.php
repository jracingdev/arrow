@extends('layouts.app')
<style>
.autocomplete-item { padding: 8px 10px; cursor: pointer; border-bottom: 1px solid #eee; background: #fff; }
.autocomplete-item:hover { background: #f5f5f5; }
#autocomplete-list { position: relative; z-index: 9; }
.worker-photo-preview { width: 96px; height: 96px; border-radius: 50%; object-fit: cover; display: none; margin-top: 8px; }
</style>

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{ trans('lang.worker_create') }}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('provider.workers') }}">{{ trans('lang.worker_plural') }}</a></li>
                <li class="breadcrumb-item active">{{ trans('lang.worker_create') }}</li>
            </ol>
        </div>
    </div>
    <div class="card-body">
        <div class="error_top"></div>
        <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display:none;">{{ trans('lang.processing') }}</div>
        <div class="row vendor_payout_create">
            <div class="vendor_payout_create-inner">
                <fieldset>
                    <legend>{{ trans('lang.worker_create') }}</legend>
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
                        <label class="col-3 control-label">{{ trans('lang.worker_salary') }}</label>
                        <div class="col-7"><input type="number" class="form-control" id="salary" min="0"></div>
                    </div>
                    <div class="form-group row width-100">
                        <label class="col-3 control-label">{{ trans('lang.address') }}</label>
                        <div class="col-7">
                            <input type="text" class="form-control" id="address" autocomplete="off">
                            <input type="hidden" id="latitude">
                            <input type="hidden" id="longitude">
                            <div id="autocomplete-list"></div>
                        </div>
                    </div>
                    <div class="form-group row width-100">
                        <label class="col-3 control-label">{{ trans('lang.user_profile_picture') }}</label>
                        <div class="col-7">
                            <input type="file" id="photo" accept="image/*">
                            <img id="photo_preview" class="worker-photo-preview" alt="">
                        </div>
                    </div>
                    <div class="form-group row width-100">
                        <div class="form-check">
                            <input type="checkbox" id="online">
                            <label for="online">{{ trans('lang.worker_online') }}</label>
                        </div>
                        <div class="form-text text-muted">{{ trans('lang.worker_online_help') }}</div>
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
    var storageRef = firebase.storage().ref('images');
    var providerId = "<?php echo $id; ?>";
    var createdAt = firebase.firestore.FieldValue.serverTimestamp();
    var photoUrl = '';

    function showError(message) {
        $('.error_top').html('<p>' + message + '</p>').show();
        window.scrollTo(0, 0);
    }

    function createWorkerAuth(email, password) {
        var cfg = firebase.app().options;
        var secondary = null;
        firebase.apps.forEach(function (app) {
            if (app.name === 'WorkerCreate') secondary = app;
        });
        if (!secondary) secondary = firebase.initializeApp(cfg, 'WorkerCreate');
        return secondary.auth().createUserWithEmailAndPassword(email, password).then(function (cred) {
            var uid = cred.user.uid;
            return secondary.auth().signOut().then(function () { return uid; });
        });
    }

    $('#address').on('input', function () {
        var query = this.value;
        if (query.length < 3) { $('#autocomplete-list').empty(); return; }
        fetch('https://nominatim.openstreetmap.org/search?q=' + encodeURIComponent(query) + '&format=json&addressdetails=1')
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

    $('#photo').on('change', function (e) {
        var file = e.target.files[0];
        if (!file) return;
        if (file.type && file.type.indexOf('image/') !== 0) {
            showError('{{ trans("lang.please_enter_details") }}');
            return;
        }
        var preview = URL.createObjectURL(file);
        $('#photo_preview').attr('src', preview).show();
        var filename = Date.now() + '_' + file.name;
        storageRef.child(filename).put(file).then(function (snap) {
            return snap.ref.getDownloadURL();
        }).then(function (url) {
            photoUrl = url;
        }).catch(function (err) {
            showError(err.message || err);
        });
    });

    $('#save_btn').on('click', function () {
        var firstName = $.trim($('#firstName').val());
        var lastName = $.trim($('#lastName').val());
        var email = $.trim($('#email').val());
        var password = $('#password').val();
        var phone = $.trim($('#phone').val());
        var salary = $.trim($('#salary').val());
        var address = $.trim($('#address').val());
        var lat = parseFloat($('#latitude').val());
        var lng = parseFloat($('#longitude').val());
        var online = $('#online').is(':checked');
        if (!firstName || !lastName || !email || !password || !phone || !salary || isNaN(lat) || isNaN(lng)) {
            showError('{{ trans("lang.please_enter_details") }}');
            return;
        }
        $("#data-table_processing").show();
        createWorkerAuth(email, password).then(function (userId) {
            return geoFirestore.collection('providers_workers').doc(userId).set({
                id: userId,
                providerId: providerId,
                firstName: firstName,
                lastName: lastName,
                email: email,
                phoneNumber: phone,
                salary: salary,
                address: address,
                profilePictureURL: photoUrl || '',
                fcmToken: '',
                active: true,
                online: online,
                reviewsCount: 0,
                reviewsSum: 0,
                createdAt: createdAt,
                latitude: lat,
                longitude: lng,
                coordinates: new firebase.firestore.GeoPoint(lat, lng)
            });
        }).then(function () {
            window.location = "{{ route('provider.workers') }}";
        }).catch(function (err) {
            $("#data-table_processing").hide();
            showError(err.message || err);
        });
    });
</script>
@endsection
