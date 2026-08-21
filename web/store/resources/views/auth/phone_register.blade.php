<!doctype html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1">


        <!-- CSRF Token -->

        <meta name="csrf-token" content="{{ csrf_token() }}">


        <title id="app_name"><?php echo @$_COOKIE['meta_title']; ?></title>
        <!-- <link rel="icon" id="favicon" type="image/x-icon" href="<?php echo str_replace('images/', 'images%2F', @$_COOKIE['favicon']); ?>"> -->
        <link rel="icon" type="image/x-icon" href="{{ asset('images/logo-light-icon.png')}}">

        <!-- Fonts -->

        <link rel="dns-prefetch" href="//fonts.gstatic.com">

        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

        <!-- Styles -->

        <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

        <link href="{{ asset('css/style.css') }}" rel="stylesheet">

        <link href="{{ asset('assets/plugins/toast-master/css/jquery.toast.css') }}" rel="stylesheet">

        <link href="{{ asset('css/colors/blue.css') }}" rel="stylesheet">

        <link href="{{ asset('assets/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">

        <!--  @yield('style')-->

        <?php if (isset($_COOKIE['store_panel_color'])){ ?>
        <style type="text/css">
            a,
            a:hover,
            a:focus {
                color: <?php echo $_COOKIE['store_panel_color']; ?>;
            }

            .form-group.default-admin {
                padding: 10px;
                font-size: 14px;
                color: #000;
                font-weight: 600;
                border-radius: 10px;
                box-shadow: 0 0px 6px 0px rgba(0, 0, 0, 0.5);
                margin: 20px 10px 10px 10px;
            }

            .form-group.default-admin .crediantials-field {
                position: relative;
                padding-right: 15px;
                text-align: left;
                padding-top: 5px;
                padding-bottom: 5px;
            }

            .form-group.default-admin .crediantials-field>a {
                position: absolute;
                right: 0;
                top: 0;
                bottom: 0;
                margin: auto;
                height: 20px;
            }

            .btn-primary,
            .btn-primary.disabled,
            .btn-primary:hover,
            .btn-primary.disabled:hover {
                background: <?php echo $_COOKIE['store_panel_color']; ?>;
                border: 1px solid<?php echo $_COOKIE['store_panel_color']; ?>;
            }

            [type="checkbox"]:checked+label::before {
                border-right: 2px solid<?php echo $_COOKIE['store_panel_color']; ?>;
                border-bottom: 2px solid<?php echo $_COOKIE['store_panel_color']; ?>;
            }

            .form-material .form-control,
            .form-material .form-control.focus,
            .form-material .form-control:focus {
                background-image: linear-gradient(<?php echo $_COOKIE['store_panel_color']; ?>, <?php echo $_COOKIE['store_panel_color']; ?>), linear-gradient(rgba(120, 130, 140, 0.13), rgba(120, 130, 140, 0.13));
            }

            .btn-primary.active,
            .btn-primary:active,
            .btn-primary:focus,
            .btn-primary.disabled.active,
            .btn-primary.disabled:active,
            .btn-primary.disabled:focus,
            .btn-primary.active.focus,
            .btn-primary.active:focus,
            .btn-primary.active:hover,
            .btn-primary.focus:active,
            .btn-primary:active:focus,
            .btn-primary:active:hover,
            .open>.dropdown-toggle.btn-primary.focus,
            .open>.dropdown-toggle.btn-primary:focus,
            .open>.dropdown-toggle.btn-primary:hover,
            .btn-primary.focus,
            .btn-primary:focus,
            .btn-primary:not(:disabled):not(.disabled).active:focus,
            .btn-primary:not(:disabled):not(.disabled):active:focus,
            .show>.btn-primary.dropdown-toggle:focus {
                background: <?php echo $_COOKIE['store_panel_color']; ?>;
                border-color: <?php echo $_COOKIE['store_panel_color']; ?>;
                box-shadow: 0 0 0 0.2rem<?php echo $_COOKIE['store_panel_color']; ?>;
            }

            <?php } ?>
        </style>


    </head>

    <body>

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


        <section id="wrapper">

            <?php if (isset($_COOKIE['store_panel_color'])){ ?>

            <div class="login-register" style="background-color:<?php echo $_COOKIE['store_panel_color']; ?>;">
                <?php } else{ ?>

                <div class="login-register" style="background-color:#FF683A;">
                    <?php } ?>


                    <div class="login-logo text-center py-3" style="margin-top:5%;">

                        <a href="#"
                            style="display: inline-block;background: #fff ; padding: 10px;border-radius: 5px;"><img
                                src="{{ asset('images/logo_web.png') }}" onerror="this.onerror=null; this.src='{{ asset('images/logo_web.png') }}';"> </a>

                    </div>

                    <div class="login-box card" style="margin-bottom:0%;">


                        <div class="card-body">
                            <div class="error_top"></div>
                            <div class="alert alert-success" style="display:none;"></div>

                            <form class="form-horizontal form-material" name="loginwithphon" id="login-with-phone-box"
                                action="#">
                                @csrf
                                <div class="box-title m-b-20">{{ trans('lang.sign_up_with_us') }}</div>
                                <div class="form-group" id="firstName_div">

                                    <label for="firstName" class="text-dark">{{ trans('lang.first_name') }}</label>

                                    <input type="text" placeholder="{{trans('lang.enter_first_name')}}" class="form-control"
                                        id="firstName" required>
                                    <input type="hidden" id="hidden_fName" />
                                </div>

                                <div class="form-group" id="lastName_div">

                                    <label for="lastName" class="text-dark">{{ trans('lang.last_name') }}</label>

                                    <input type="text" placeholder="{{trans('lang.enter_last_name')}}" class="form-control"
                                        id="lastName" required>
                                    <input type="hidden" id="hidden_lName" />
                                </div>

                                <div class="form-group" id="email_div">
                                    <label class="text-dark">{{trans('lang.email')}}</label>                                    
                                        <input type="email" placeholder="{{trans('lang.enter_email')}}" class="form-control user_email" id="email" required>
                                        <input type="hidden" id="hidden_email" />
                                </div>

                                <div class="form-group">
                                    <label class="text-dark">{{ trans('lang.login_as') }}</label>
                                    <div class="d-flex align-items-center" style="gap:18px;">
                                        <div class="radio radio-info radio-inline">
                                            <input type="radio" name="signup_role" id="signup_role_vendor" value="vendor" checked>
                                            <label for="signup_role_vendor"> {{ trans('lang.owner') }} </label>
                                        </div>
                                        <div class="radio radio-info radio-inline">
                                            <input type="radio" name="signup_role" id="signup_role_provider" value="provider">
                                            <label for="signup_role_provider"> {{ trans('lang.provider') }} </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" id="provider_section_wrap" style="display:none;">
                                    <label class="text-dark">{{ trans('lang.section') }}</label>
                                    <select class="form-control" id="provider_section_id">
                                        <option value="">{{ trans('lang.select') }}</option>
                                    </select>
                                </div>

                                <div class="form-group " id="phone-box">
                                    <div class="col-xs-12">
                                        <select name="country" id="country_selector">
                                            <?php foreach ($newcountries as $keycy => $valuecy) { ?>
                                            <?php $selected = ''; ?>
                                            <option <?php echo $selected; ?> code="<?php echo $valuecy->code; ?>"
                                                value="<?php echo $keycy; ?>">
                                                +<?php echo $valuecy->phoneCode; ?> {{ $valuecy->countryName }}</option>
                                            <?php } ?>
                                        </select>
                                        <input class="form-control" placeholder="{{trans('lang.user_phone')}}" id="phone" type="text"
                                            class="form-control" name="phone" value="{{ old('phone') }}" required
                                            autocomplete="phone" autofocus>
                                    </div>
                                    @error('phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group " id="otp-box" style="display:none;">
                                    <input class="form-control" placeholder="Código OTP" id="verificationcode" type="text"
                                        class="form-control" name="otp" value="{{ old('otp') }}" required
                                        autocomplete="otp" autofocus>
                                    <p id="arrow-otp-display" class="mt-2 mb-0" style="display:none;"></p>
                                    <div class="otp_error">

                                    </div>
                                </div>
                                <div id="recaptcha-container" style="display:none;"></div>

                                <div class="form-group text-center m-t-20">
                                    <div class="col-xs-12">
                                        <button type="button" style="display:none;" onclick="applicationVerifier()"
                                            id="verify_btn"
                                            class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">
                                            {{ trans('lang.otp_verify') }}
                                        </button>
                                        <button type="button" onclick="sendOTP()" id="send-code"
                                            class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">
                                            {{ trans('lang.otp_send') }}
                                        </button>


                                    </div>
                                </div>
                            </form>
                            <div class="new-acc d-flex align-items-center justify-content-center mt-4 mb-3">

                                <a href="{{ url('login') }}">

                                    <p class="text-center m-0"> {{ trans('lang.already_an_account') }}
                                        {{ trans('lang.sign_in') }}</p>

                                </a>

                            </div>


                        </div>

                        <!-- </div>

            </div>

        </div> -->

                    </div>

                </div>
            </div>

        </section>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="{{ asset('assets/plugins/bootstrap/js/popper.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/select2/dist/js/select2.min.js') }}"></script>
        <script src="{{ asset('js/jquery.slimscroll.js') }}"></script>
        <script src="{{ asset('js/waves.js') }}"></script>
        <script src="{{ asset('js/sidebarmenu.js') }}"></script>
        <script src="{{ asset('assets/plugins/sticky-kit-master/dist/sticky-kit.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/sparkline/jquery.sparkline.min.js') }}"></script>
        <script src="{{ asset('js/custom.min.js') }}"></script>
        <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
        <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-firestore-compat.js"></script>
        <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-storage-compat.js"></script>
        <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-auth-compat.js"></script>
        <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-database-compat.js"></script>
        <script src="https://unpkg.com/geofirestore/dist/geofirestore.js"></script>
        <script src="https://cdn.firebase.com/libs/geofire/5.0.1/geofire.min.js"></script>
        <script src="{{ asset('js/crypto-js.js') }}"></script>
        <script src="{{ asset('js/jquery.cookie.js') }}"></script>
        @include('partials.firebase-init')
        <script src="{{ asset('js/firestore-safe.js') }}"></script>
        <script src="{{ asset('js/jquery.validate.js') }}"></script>
        <script src="{{ asset('js/arrow-phone-otp.js') }}?v=5"></script>

        <script type="text/javascript">
            var database = firebase.firestore();
            var geoFirestore = new GeoFirestore(database);

            var createdAt = firebase.firestore.FieldValue.serverTimestamp();

            var vendor_active = false;
            var autoAprroveVendor = database.collection('settings').doc("vendor");
            var autoApproveProvider = database.collection('settings').doc("provider");
            function refreshAutoApprove() {
                var role = $('input[name="signup_role"]:checked').val() || 'vendor';
                vendor_active = false;
                if (role === 'provider') {
                    autoApproveProvider.get().then(function(snapshots) {
                        var data = snapshots.exists ? (snapshots.data() || {}) : {};
                        vendor_active = data.auto_approve_provider == true;
                    }).catch(function() {});
                } else {
                    autoAprroveVendor.get().then(function(snapshots) {
                        var vendordata = snapshots.exists ? (snapshots.data() || {}) : {};
                        vendor_active = vendordata.auto_approve_vendor == true;
                    }).catch(function() {});
                }
            }
            refreshAutoApprove();
            $(document).on('change', 'input[name="signup_role"]', function() {
                refreshAutoApprove();
                if ($(this).val() === 'provider') {
                    $('#provider_section_wrap').show();
                } else {
                    $('#provider_section_wrap').hide();
                }
            });
            database.collection('sections').where('serviceTypeFlag', '==', 'ondemand-service').where('isActive', '==', true).orderBy('order').get().then(function (snapshots) {
                snapshots.docs.forEach(function (doc) {
                    var data = doc.data();
                    $('#provider_section_id').append($('<option></option>').attr('value', data.id).text(data.name));
                });
            }).catch(function() {});

            var adminEmail = '';

            var emailSetting = database.collection('settings').doc('emailSetting');
            var email_templates = database.collection('email_templates').where('type', '==', 'new_vendor_signup');

            var emailTemplatesData = null;

            var newcountriesjs = '<?php echo json_encode($newcountriesjs); ?>';
            var newcountriesjs = JSON.parse(newcountriesjs);

            let isStoreVerification = false;

            function formatState(state) {

                if (!state.id) {
                    return state.text;
                }
                var baseUrl = "<?php echo URL::to('/'); ?>/flags/120/";
                var $state = $(
                    '<span><img src="' + baseUrl + '/' + newcountriesjs[state.element.value].toLowerCase() +
                    '.png" class="img-flag" /> ' + state.text + '</span>'
                );
                return $state;
            }

            function formatState2(state) {
                if (!state.id) {
                    return state.text;
                }

                var baseUrl = "<?php echo URL::to('/'); ?>/flags/120/"
                var $state = $(
                    '<span><img class="img-flag" /> <span></span></span>'
                );
                $state.find("span").text(state.text);
                $state.find("img").attr("src", baseUrl + "/" + newcountriesjs[state.element.value].toLowerCase() + ".png");

                return $state;
            }

            jQuery(document).ready(async function() {

                try {
                    let docRef = await database.collection('settings').doc('document_verification_settings').get();
                    let docData = docRef.exists ? (docRef.data() || {}) : {};
                    isStoreVerification = docData.isStoreVerification === true;
                } catch (settingsError) {
                    console.error("Error loading document_verification_settings:", settingsError);
                    isStoreVerification = false;
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>Não foi possível carregar as configurações. Tente novamente em instantes.</p>");
                }

                await email_templates.get().then(async function(snapshots) {
                    emailTemplatesData = snapshots.docs[0].data();
                });

                await emailSetting.get().then(async function(snapshots) {
                    var emailSettingData = snapshots.data();

                    adminEmail = emailSettingData.userName;
                });

                jQuery("#country_selector").select2({
                    templateResult: formatState,
                    templateSelection: formatState2,
                    placeholder: "{{trans('lang.select_country')}}",
                    allowClear: true
                });
            });


            function sendOTP() {
                var firstName = $('#firstName').val();
                var lastName = $('#lastName').val();
                var email = $('#email').val();

                if (firstName == '') {

                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.enter_owners_name_error') }}</p>");
                    window.scrollTo(0, 0);
                } else if (jQuery("#phone").val() == "") {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.enter_owners_phone') }}</p>");
                    window.scrollTo(0, 0);
                } else if (email == "") {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.enter_owners_email') }}</p>");
                    window.scrollTo(0, 0);
                } else {
                    var phoneNumber = '+' + jQuery("#country_selector").val() + jQuery("#phone").val();

                    database.collection("users").where('phoneNumber', '==', phoneNumber).get().then(async function(snapshots) {
                        if (snapshots.docs.length > 0) {
                            alert('{{ trans('lang.you_already_have_account_with_this_phone_number') }}')
                            return false;
                        } else {
                            $('#hidden_fName').val(firstName);
                            $('#hidden_lName').val(lastName);
                            $('#hidden_email').val(email);


                            window.arrowOtpSession = ArrowPhoneOtp.newSessionId();
                            ArrowPhoneOtp.send(phoneNumber, window.arrowOtpSession)
                                .then(function() {
                                    $('#firstName_div').hide();
                                    $('#lastName_div').hide();
                                    $('#email_div').hide();
                                    $('#phone-box').hide();
                                    jQuery("#verify_btn").show();
                                    jQuery("#otp-box").show();
                                })
                                .catch(function(error) {
                                    $('.otp_error').html(error.message || error);
                                });
                        }
                    })


                }


            }

            function applicationVerifier() {
                var code = $('#verificationcode').val();
                if (code == "") {
                    $('.otp_error').html("{{ trans('lang.please_enter_otp') }}")
                } else {
                    var e164 = '+' + jQuery("#country_selector").val() + jQuery("#phone").val();
                    ArrowPhoneOtp.verify(e164, window.arrowOtpSession, document.getElementById("verificationcode").value)
                        .then(function(session) {
                            return ArrowPhoneOtp.signIn(session);
                        })
                        .then(async function(result) {
                            var phoneNumber = e164;
                            var firstName = $('#hidden_fName').val();
                            var lastName = $('#hidden_lName').val();
                            var email = $('#hidden_email').val();

                            var password = "";

                            var uuid = result.user.uid;

                            
                            coordinates = new firebase.firestore.GeoPoint(0, 0);
                            var signupRole = $('input[name="signup_role"]:checked').val() || 'vendor';
                            var providerSectionId = $('#provider_section_id').val() || '';
                            if (signupRole === 'provider' && !providerSectionId) {
                                $('.error_top').show().html("<p>{{trans('lang.select_ondemand_section')}}</p>");
                                return;
                            }
                            var userDoc = {
                                'email': email,
                                'firstName': firstName,
                                'lastName': lastName,
                                'id': uuid,
                                'phoneNumber': phoneNumber,
                                'role': signupRole,
                                'profilePictureURL': "",
                                'active': vendor_active,
                                'isActive': vendor_active,
                                'coordinates': coordinates,
                                'createdAt': createdAt,
                            };
                            if (signupRole === 'provider') {
                                userDoc.section_id = providerSectionId;
                                userDoc.sectionId = providerSectionId;
                                userDoc.wallet_amount = 0;
                                userDoc.reviewsCount = 0;
                                userDoc.reviewsSum = 0;
                            } else {
                                userDoc.vendorID = '';
                                userDoc.sectionId = '';
                                userDoc.isDocumentVerify = isStoreVerification === true ? false : true;
                                userDoc.isAutoVerify = isStoreVerification === false ? true : false;
                            }
                            geoFirestore.collection("users").doc(uuid).set(userDoc).then(async function(result) {
                                autoAprroveVendor.get().then(async function(snapshots) {
                                    var formattedDate = new Date();
                                    var month = formattedDate.getMonth() + 1;
                                    var day = formattedDate.getDate();
                                    var year = formattedDate.getFullYear();

                                    month = month < 10 ? '0' + month : month;
                                    day = day < 10 ? '0' + day : day;

                                    formattedDate = day + '-' + month + '-' + year;

                                    var message = emailTemplatesData.message;
                                    message = message.replace(/{userid}/g, uuid);
                                    message = message.replace(/{username}/g, firstName + ' ' +
                                        lastName);
                                    message = message.replace(/{useremail}/g, "");
                                    message = message.replace(/{userphone}/g, phoneNumber);
                                    message = message.replace(/{date}/g, formattedDate);

                                    emailTemplatesData.message = message;

                                    var url = "{{ url('send-email') }}";

                                    var sendEmailStatus = await sendEmail(url,
                                        emailTemplatesData.subject, emailTemplatesData
                                        .message, [adminEmail]);

                                    if (sendEmailStatus) {

                                        var vendordata = snapshots.data();
                                        // if (vendordata.auto_approve_vendor == false) {
                                        if (vendor_active == false) {                                        
                                            $(".alert-success").show();
                                            $(".alert-success").html("");
                                            $(".alert-success").append(
                                                "<p>{{ trans('lang.signup_waiting_approval') }}</p>"
                                                );
                                            window.scrollTo(0, 0);
                                            setTimeout(function() {
                                                window.location.href =
                                                    '{{ route('login') }}';
                                            }, 5000);
                                        } else {
                                            $(".alert-success").show();
                                            $(".alert-success").html("");
                                            $(".alert-success").append(
                                                "<p>{{ trans('lang.thank_you_signup_msg') }}</p>"
                                                );
                                            window.scrollTo(0, 0);
                                            setTimeout(function() {
                                                window.location.href =
                                                    '{{ route('login') }}';
                                            }, 5000);
                                        }

                                    }


                                }).catch((error) => {

                                    console.error("Error writing document: ", error);
                                    $("#field_error").html(error);

                                });
                            });


                        }).catch((error) => {
                            $(".otp_error").html("{{ trans('lang.otp_verification_failed') }}");
                        });
                }
            }

            async function sendEmail(url, subject, message, recipients) {

                var checkFlag = false;

                await $.ajax({

                    type: 'POST',
                    data: {
                        subject: subject,
                        message: message,
                        recipients: recipients
                    },
                    url: url,
                    headers: {

                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        checkFlag = true;
                    },
                    error: function(xhr, status, error) {
                        checkFlag = true;
                    }
                });

                return checkFlag;

            }
        </script>
