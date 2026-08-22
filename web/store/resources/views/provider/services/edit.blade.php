@extends('layouts.app')
<style>
.autocomplete-item { padding: 8px 10px; cursor: pointer; border-bottom: 1px solid #eee; background: #fff; }
.autocomplete-item:hover { background: #f5f5f5; }
#autocomplete-list { position: relative; z-index: 9; }
.service-photo-preview { max-width: 160px; max-height: 120px; display: none; margin-top: 8px; border-radius: 6px; }
</style>

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
                        <label class="col-3 control-label">{{ trans('lang.select_section') }}</label>
                        <div class="col-7"><select class="form-control" id="sectionId"><option value="">{{ trans('lang.select_section') }}</option></select></div>
                    </div>
                    <div class="form-group row width-50">
                        <label class="col-3 control-label">{{ trans('lang.category') }}</label>
                        <div class="col-7"><select class="form-control" id="categoryId"><option value="">{{ trans('lang.select') }}</option></select></div>
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
                        <label class="col-3 control-label">{{ trans('lang.image') }}</label>
                        <div class="col-7">
                            <input type="file" id="photo" accept="image/*">
                            <img id="photo_preview" class="service-photo-preview" alt="">
                        </div>
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
                    <div class="form-group row width-50">
                        <label class="col-3 control-label">{{ trans('lang.start_time') }}</label>
                        <div class="col-7"><input type="time" class="form-control" id="startTime" value="08:00"></div>
                    </div>
                    <div class="form-group row width-50">
                        <label class="col-3 control-label">{{ trans('lang.end_time') }}</label>
                        <div class="col-7"><input type="time" class="form-control" id="endTime" value="18:00"></div>
                    </div>
                    <div class="form-group row width-100">
                        <div class="form-check">
                            <input type="checkbox" id="publish">
                            <label for="publish">{{ trans('lang.active') }}</label>
                        </div>
                        <small class="form-text text-muted ml-4">{{ trans('lang.service_publish_help') }}</small>
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
    var storageRef = firebase.storage().ref('images');
    var id = "{{ $id }}";
    var providerId = "{{ $providerId }}";
    var providerUser = null;
    var serviceDoc = null;
    var photoUrl = '';

    function encodeGeohash(latitude, longitude, precision) {
        precision = precision || 10;
        var BASE32 = "0123456789bcdefghjkmnpqrstuvwxyz";
        var idx = 0, bit = 0, even = true, geohash = "";
        var latMin = -90, latMax = 90, lonMin = -180, lonMax = 180;
        while (geohash.length < precision) {
            if (even) {
                var mid = (lonMin + lonMax) / 2;
                if (longitude > mid) { idx = idx * 2 + 1; lonMin = mid; } else { idx = idx * 2; lonMax = mid; }
            } else {
                var mid = (latMin + latMax) / 2;
                if (latitude > mid) { idx = idx * 2 + 1; latMin = mid; } else { idx = idx * 2; latMax = mid; }
            }
            even = !even;
            if (++bit == 5) { geohash += BASE32.charAt(idx); bit = 0; idx = 0; }
        }
        return geohash;
    }

    function loadCategories(sectionId, selected) {
        $('#categoryId').html('<option value="">{{ trans('lang.select') }}</option>');
        if (!sectionId) return Promise.resolve();
        return database.collection('provider_categories').where('sectionId', '==', sectionId).where('publish', '==', true).get().then(function (cats) {
            cats.docs.forEach(function (doc) {
                var c = doc.data();
                if (!c.parentCategoryId) {
                    $('#categoryId').append($('<option></option>').attr('value', c.id).text(c.title));
                }
            });
            if (selected) $('#categoryId').val(selected);
        });
    }

    function showPhoto(url) {
        photoUrl = url || '';
        if (photoUrl) {
            $('#photo_preview').attr('src', photoUrl).show();
        } else {
            $('#photo_preview').hide();
        }
    }

    $(function () {
        $("#data-table_processing").show();
        Promise.all([
            database.collection('users').doc(providerId).get(),
            database.collection('providers_services').doc(id).get(),
            database.collection('sections').where('serviceTypeFlag', '==', 'ondemand-service').get()
        ]).then(function (snaps) {
            providerUser = snaps[0].data() || {};
            serviceDoc = snaps[1].data();
            if (!serviceDoc || serviceDoc.author !== providerId) {
                window.location = "{{ route('provider.services') }}";
                return;
            }
            snaps[2].docs.forEach(function (doc) {
                var s = doc.data() || {};
                if (s.isActive === false) return;
                $('#sectionId').append($('<option></option>').attr('value', s.id).text(s.name || s.id));
            });
            $('#title').val(serviceDoc.title || '');
            $('#price').val(serviceDoc.price || '');
            $('#priceUnit').val(serviceDoc.priceUnit || 'Fixed');
            $('#description').val(serviceDoc.description || '');
            $('#publish').prop('checked', serviceDoc.publish === true);
            $('#address').val(serviceDoc.address || '');
            $('#startTime').val(serviceDoc.startTime || '08:00');
            $('#endTime').val(serviceDoc.endTime || '18:00');
            var lat = serviceDoc.latitude != null ? serviceDoc.latitude : (providerUser.location && providerUser.location.latitude);
            var lng = serviceDoc.longitude != null ? serviceDoc.longitude : (providerUser.location && providerUser.location.longitude);
            if (lat != null) $('#latitude').val(lat);
            if (lng != null) $('#longitude').val(lng);
            var photos = Array.isArray(serviceDoc.photos) ? serviceDoc.photos : [];
            showPhoto(photos[0] || '');
            var sectionId = serviceDoc.sectionId || providerUser.sectionId || providerUser.section_id || '';
            if (sectionId) $('#sectionId').val(sectionId);
            return loadCategories(sectionId, serviceDoc.categoryId);
        }).then(function () {
            $("#data-table_processing").hide();
        }).catch(function () {
            $("#data-table_processing").hide();
        });
    });

    $('#sectionId').on('change', function () {
        loadCategories($(this).val());
    });

    $('#address').on('input', function () {
        var query = this.value;
        if (query.length < 3) { $('#autocomplete-list').empty(); return; }
        fetch('https://nominatim.openstreetmap.org/search?q=' + encodeURIComponent(query) + '&format=json&addressdetails=1&countrycodes=br&accept-language=pt-BR')
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
        var filename = Date.now() + '_' + file.name;
        storageRef.child(filename).put(file).then(function (snap) {
            return snap.ref.getDownloadURL();
        }).then(function (url) { showPhoto(url); });
    });

    $('#save_btn').on('click', function () {
        var title = $('#title').val();
        var categoryId = $('#categoryId').val();
        var price = $('#price').val();
        var lat = parseFloat($('#latitude').val());
        var lng = parseFloat($('#longitude').val());
        var sectionId = $('#sectionId').val() || (serviceDoc && serviceDoc.sectionId) || providerUser.sectionId || providerUser.section_id || '';
        if (!title || !sectionId || !categoryId || !price || isNaN(lat) || isNaN(lng)) {
            $('.error_top').html('<p>{{ trans("lang.please_enter_details") }}</p>').show();
            window.scrollTo(0, 0);
            return;
        }
        var payload = {
            address: $('#address').val(),
            author: providerId,
            authorName: ((providerUser.firstName || '') + ' ' + (providerUser.lastName || '')).trim(),
            authorProfilePic: providerUser.profilePictureURL || (serviceDoc && serviceDoc.authorProfilePic) || '',
            categoryId: categoryId,
            description: $('#description').val(),
            latitude: lat,
            longitude: lng,
            phoneNumber: providerUser.phoneNumber || (serviceDoc && serviceDoc.phoneNumber) || '',
            photos: photoUrl ? [photoUrl] : ((serviceDoc && serviceDoc.photos) || []),
            price: String(price),
            priceUnit: $('#priceUnit').val(),
            publish: $('#publish').is(':checked'),
            sectionId: sectionId,
            startTime: $('#startTime').val(),
            endTime: $('#endTime').val(),
            title: title,
            coordinates: new firebase.firestore.GeoPoint(lat, lng),
            g: {
                geohash: encodeGeohash(lat, lng),
                geopoint: new firebase.firestore.GeoPoint(lat, lng)
            }
        };
        if (providerUser.subscription_plan) {
            payload.subscription_plan = providerUser.subscription_plan;
            payload.subscriptionPlanId = providerUser.subscriptionPlanId || (providerUser.subscription_plan.id || '');
            payload.subscriptionExpiryDate = providerUser.subscriptionExpiryDate || null;
            payload.subscriptionTotalOrders = providerUser.subscriptionTotalOrders || providerUser.subscription_plan.orderLimit || '-1';
        }
        $("#data-table_processing").show();
        database.collection('providers_services').doc(id).update(payload).then(function () {
            if (sectionId && (!providerUser.section_id || !providerUser.sectionId)) {
                database.collection('users').doc(providerId).update({ section_id: sectionId, sectionId: sectionId });
            }
            window.location = "{{ route('provider.services') }}";
        }).catch(function (err) {
            $("#data-table_processing").hide();
            $('.error_top').html('<p>' + err + '</p>').show();
        });
    });
</script>
@endsection
