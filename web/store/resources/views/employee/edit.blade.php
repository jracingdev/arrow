@extends('layouts.app')
@section('content')
    <?php
    $countries = file_get_contents(public_path('countriesdata.json'));
    $countries = json_decode($countries);
    $countries = (array) $countries;
    $newcountries = [];
    $newcountriesjs = [];
    foreach ($countries as $keycountry => $valuecountry) {
        $newcountries[$valuecountry->phoneCode] = $valuecountry;
        $newcountriesjs[$valuecountry->phoneCode] = $valuecountry->code;
    }
    ?>
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ trans('lang.edit_employee') }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{!! route('employee.index') !!}">{{ trans('lang.employee_plural') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ trans('lang.edit_employee') }}</li>
                </ol>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="resttab-sec">
                        <div class="error_top"></div>
                        <div class="row restaurant_payout_create">
                            <div class="restaurant_payout_create-inner">
                                <fieldset>
                                    <legend>{{ trans('lang.basic_details') }}</legend>
                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{trans('lang.employee_role')}}</label>
                                        <div class="col-7">
                                            <select class="form-control" id="employee_role" required>
                                                <option value="">{{trans('lang.select_role')}}</option>
                                            </select>
                                            <div class="form-text text-muted">{{trans('lang.employee_role_help')}}</div>
                                        </div>
                                    </div>
                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{ trans('lang.first_name') }}</label>
                                        <div class="col-7">
                                            <input type="text" class="form-control user_first_name" required>
                                            <div class="form-text text-muted">
                                                {{ trans('lang.user_first_name_help') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{ trans('lang.last_name') }}</label>
                                        <div class="col-7">
                                            <input type="text" class="form-control user_last_name">
                                            <div class="form-text text-muted">
                                                {{ trans('lang.user_last_name_help') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{ trans('lang.email') }}</label>
                                        <div class="col-7">
                                            <input type="email" class="form-control user_email" required disabled>
                                            <div class="form-text text-muted">
                                                {{ trans('lang.user_email_help') }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group form-material">
                                        <label class="col-3 control-label">{{ trans('lang.user_phone') }}</label>
                                        <div class="col-12">
                                            <div class="phone-box position-relative" id="phone-box">
                                                <select name="country" id="country_selector" class="form-control country_code" disabled>
                                                    <option value="">{{ trans("lang.select_country") }}</option>
                                                    @foreach($countries as $country)
                                                    <option value="{{ $country->code }}"  data-phonecode="{{ $country->phoneCode }}" data-iso="{{ $country->code }}">{{$country->countryName}}
                                                        (+{{$country->phoneCode}})
                                                    </option>
                                                    @endforeach
                                                </select>
                                                <input class="form-control user_phone" placeholder="{{trans('lang.phone')}}" id="phone" type="phone" name="phone" value="{{ old('phone') }}" required autocomplete="phone" autofocus disabled>
                                                <div id="error2" class="err"></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label class="col-3 control-label">{{ trans('lang.profile_pic') }}</label>
                                        <div class="col-9">
                                            <input type="file" onChange="handleFileSelectProfile(event,'vendor')">
                                            <div id="uploding_image_owner"></div>
                                            <div class="uploaded_image_owner mt-3">
                                            </div>
                                            <div class="form-text text-muted">
                                                {{ trans('lang.image_help') }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="form-group row width-50">
                                            <div class="form-check width-100">
                                                <input type="checkbox" id="is_active">
                                                <label class="col-3 control-label" for="is_active">{{ trans('lang.active') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>                                

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group col-12 text-center btm-btn">
                <button type="button" class="btn btn-primary  save_from_btn"><i class="fa fa-save"></i> {{ trans('lang.save') }}</button>
                <a href="{!! route('employee.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{ trans('lang.cancel') }}</a>
            </div>
        </div>
    </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/compressorjs/1.1.1/compressor.min.js" integrity="sha512-VaRptAfSxXFAv+vx33XixtIVT9A/9unb1Q8fp63y1ljF+Sbka+eMJWoDAArdm7jOYuLQHVx5v60TQ+t3EA8weA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js"></script>
    <script>
        var database = firebase.firestore();
        var geoFirestore = new GeoFirestore(database);
        var storageRef = firebase.storage().ref('images');
        var storage = firebase.storage();
        var photo = "";
        var profilephoto = '';
        var oldProfile = '';
        var profileFileName = '';  
        var placeholderImage = '';
        var id = "{{ $id }}";
        var vendorUserId = "{{ $vendorUserId }}";
        var authRole = "{{ $authRole }}";
        var empVendorId = "{{ $empVendorId }}";
        let currentPermissions = {
            isActive: true   
        };
        var ref = database.collection('users').where("id", "==", id);
        var user = '';
        var placeholder = database.collection('settings').doc('placeHolderImage');
        placeholder.get().then(async function(snapshotsimage) {
            var placeholderImageData = snapshotsimage.data();
            placeholderImage = placeholderImageData.image;
        })       
        $(document).ready(function() {
            jQuery("#country_selector").select2({
                templateResult: formatState,
                templateSelection: formatState2,
                placeholder: "{{ trans('lang.select_country') }}",
                allowClear: true
            });
            ref.get().then(async function(snapshots) {
                if (!snapshots.empty) {
                    user = snapshots.docs[0].data();
                    $(".user_first_name").val(user.firstName);
                    $(".user_last_name").val(user.lastName);
                    if (user.email != "") {
                        $(".user_email").val(user.email);
                    } else {
                        $(".user_email").val("");
                    }
                    if (user.phoneNumber != "") {
                        $(".user_phone").val(user.phoneNumber);
                    } else {
                        $(".user_phone").val("");
                    }                   
                    if (user.countryISOCode) {        
                        const isoCode = user.countryISOCode;

                        if ($("#country_selector option[value='" + isoCode + "']").length > 0) {
                            $("#country_selector").val(isoCode).trigger('change');
                        } else {
                            fallbackToGlobal();
                        }

                    } else if (user.countryCode) {

                        let phoneCode = user.countryCode;
                        if (phoneCode.startsWith('+')) phoneCode = phoneCode.substring(1);

                        if ($("#country_selector option[phoneCode='" + phoneCode + "']").length > 0) {
                            $("#country_selector")
                                .val($("#country_selector option[phoneCode='" + phoneCode + "']").val())
                                .trigger('change');
                        } else {
                            fallbackToGlobal();
                        }

                    } else {
                        fallbackToGlobal();
                    }
                    function fallbackToGlobal() {
                        database.collection('settings').doc("globalSettings").get()
                            .then(function(snapshot) {
                                let settings = snapshot.data();
                                if (!settings || !settings.defaultCountryCode) return;

                                let defaultCode = settings.defaultCountryCode.toString().trim();

                                // ISO first
                                if ($("#country_selector option[value='" + defaultCode.toUpperCase() + "']").length > 0) {
                                    $("#country_selector").val(defaultCode.toUpperCase()).trigger('change');
                                }
                                // phone fallback
                                else {
                                    let phoneCode = defaultCode.replace('+', '');
                                    let $opt = $("#country_selector option[phoneCode='" + phoneCode + "']");
                                    if ($opt.length > 0) {
                                        $("#country_selector").val($opt.val()).trigger('change');
                                    }
                                }
                            })
                            .catch(function(error) {
                                console.error("Error loading global settings:", error);
                            });
                    }
                
                    if (user.active) {
                        $("#is_active").prop("checked", true);
                        user_active_deactivate = true;
                    }
                    if (user.profilePictureURL != '' && user.profilePictureURL != null) {
                        profilephoto = user.profilePictureURL;
                        oldProfile = user.profilePictureURL;
                        $(".uploaded_image_owner").append('<img onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" class="rounded" style="width:50px" src="' + profilephoto + '" alt="image">');
                    } else {
                        $(".uploaded_image_owner").append('<img class="rounded" style="width:50px" src="' + placeholderImage + '" alt="image">');
                    }



                    jQuery("#data-table_processing").hide();
                }
            })
            let vendorQuery = database.collection('vendors');

            if (authRole === 'employee') {           
                vendorQuery = vendorQuery.where('id', '==', empVendorId);
            } else {           
                vendorQuery = vendorQuery.where('author', '==', vendorUserId);
            }  
            vendorQuery.get().then(async function(vendorSnapshots) {
                if (vendorSnapshots.docs.length > 0) {
                    vendorData = vendorSnapshots.docs[0].data();
                    restaurant_id = vendorData.id;
                    zoneId = vendorData.zoneId;


                    database.collection('vendor_employee_roles').where('vendorId', '==', restaurant_id).where('isEnable', '==' ,true).get().then(function(roleSnapshot) {
                        var select = document.getElementById('employee_role');
                        select.innerHTML = '<option value="">{{trans("lang.select_role")}}</option>'; 

                        if (roleSnapshot.empty) {
                            console.warn("No roles found for vendor:", restaurant_id);                      
                            return;
                        }

                        roleSnapshot.forEach(function(doc) {
                            var role = doc.data();
                            var option = document.createElement('option');                        option.value = doc.id;                  
                            option.textContent = role.title || "Unnamed Role (" + doc.id + ")";                                                  
                            select.appendChild(option);
                        });
                        if (user.employeePermissionId) { 
                            $("#employee_role").val(user.employeePermissionId).trigger('change');

                        }
                    })
                    .catch(function(err) {
                        console.error("Error loading roles:", err);
                    });
                }
            });
            

        });
       
       

        $(".save_from_btn").click(async function() {
            var userFirstName = $(".user_first_name").val();
            var userLastName = $(".user_last_name").val();
            var email = $(".user_email").val().toLowerCase().trim();
            var userPhone = $(".user_phone").val();
            var countryCode = '+' + $("#country_selector option:selected").data('phonecode');
            let isoCode = $("#country_selector option:selected").data('iso');
            var selectedRoleId = $("#employee_role").val();
            var isActive = $("#is_active").is(':checked') ? true : false;
            if (userFirstName == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{ trans('lang.user_first_name_help') }}</p>");
                window.scrollTo(0, 0);
            } else if (email == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{ trans('lang.user_email_help') }}</p>");
                window.scrollTo(0, 0);
            } else if (userPhone == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{ trans('lang.user_phone_help') }}</p>");
                window.scrollTo(0, 0);
            } else {
                jQuery("#data-table_processing").show();
                await storeImageData().then(async (IMG) => {
                 

                    database.collection('users').doc(id).update({
                        'firstName': userFirstName,
                        'lastName': userLastName,                       
                        'profilePictureURL': IMG && IMG.profileImage ? IMG.profileImage : "",  
                        'active': isActive,
                        'isActive': false,
                        'employeePermissionId': selectedRoleId,                      


                    }).then(function(result) {
                        window.location.href = '{{ route('employee.index') }}';
                    });
                }).catch(err => {
                    jQuery("#data-table_processing").hide();
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>" + err + "</p>");
                    window.scrollTo(0, 0);
                });

            }
        })
        async function storeImageData() {
            var newPhoto = [];
            newPhoto['profileImage'] = profilephoto;
            try {
                if (profilephoto != '' && profilephoto != null) {
                    if (oldProfile != "" && profilephoto != oldProfile) {
                        var oldImageUrlRef = await storage.refFromURL(oldProfile);
                        imageBucket = oldImageUrlRef.bucket;
                        var envBucket = "<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";
                        if (imageBucket == envBucket) {
                            await oldImageUrlRef.delete().then(() => {
                                console.log("Old file deleted!")
                            }).catch((error) => {
                                console.log("ERR File delete ===", error);
                            });
                        } else {
                            console.log('Bucket not matched');
                        }
                    }
                    if (profilephoto != oldProfile) {
                        profilephoto = profilephoto.replace(/^data:image\/[a-z]+;base64,/, "")
                        var uploadTask = await storageRef.child(profileFileName).putString(profilephoto, 'base64', {
                            contentType: 'image/jpg'
                        });
                        var downloadURL = await uploadTask.ref.getDownloadURL();
                        newPhoto['profileImage'] = downloadURL;
                        profilephoto = downloadURL;
                    }

                }
            } catch (error) {
                console.log("ERR ===", error);
            }
            return newPhoto;
        }

        function handleFileSelectProfile(evt) {
            var f = evt.target.files[0];
            var reader = new FileReader();
            new Compressor(f, {
                quality: <?php echo env('IMAGE_COMPRESSOR_QUALITY', 0.8); ?>,
                success(result) {
                    f = result;
                    reader.onload = (function(theFile) {
                        return function(e) {
                            var filePayload = e.target.result;
                            var hash = CryptoJS.SHA256(Math.random() + CryptoJS.SHA256(filePayload));
                            var val = f.name;
                            var ext = val.split('.')[1];
                            var docName = val.split('fakepath')[1];
                            var filename = (f.name).replace(/C:\\fakepath\\/i, '');
                            var timestamp = Number(new Date());
                            var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;
                            profilephoto = filePayload;
                            profileFileName = filename;
                            $(".uploaded_image_owner").html('<img class="rounded" style="width:50px" src="' + profilephoto + '" alt="image">');

                        };
                    })(f);
                    reader.readAsDataURL(f);
                },
                error(err) {
                    console.log(err.message);
                },
            });
        }

      
        var newcountriesjs = '<?php echo json_encode($newcountriesjs); ?>';
        var newcountriesjs = JSON.parse(newcountriesjs);
        function formatState(state) {
            if (!state.id) {
                return state.text;
            }
            var countryCode = state.element.value.toLowerCase(); // "GB" → "gb"
            var baseUrl = "<?php echo URL::to('/'); ?>/scss/icons/flag-icon-css/flags";
            var $state = $(
                '<span><img src="' + baseUrl + '/' + countryCode + '.svg' + '" class="img-flag" /> ' + state.text + '</span>'
            );
            return $state;
        }
        function formatState2(state) {
            if (!state.id) {
                return state.text;
            }
            var countryCode = state.element.value.toLowerCase();
            var baseUrl = "<?php echo URL::to('/'); ?>/scss/icons/flag-icon-css/flags";
            var $state = $(
                '<span><img class="img-flag" src="' + baseUrl + '/' + countryCode + '.svg' + '" /> <span>' + state.text + '</span></span>'
            );
            return $state;
        }	
        
    </script>
@endsection
