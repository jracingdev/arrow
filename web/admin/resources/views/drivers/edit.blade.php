@extends('layouts.app')


@section('content')
    <div class="page-wrapper">

        <div class="row page-titles">

            <div class="col-md-5 align-self-center">

                <h3 class="text-themecolor">{{trans('lang.driver_plural')}}</h3>

            </div>



            <div class="col-md-7 align-self-center">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>

                    <li class="breadcrumb-item"><a href="{!! route('drivers') !!}">{{trans('lang.driver_plural')}}</a>

                    </li>

                    <li class="breadcrumb-item active">{{trans('lang.driver_edit')}}</li>

                </ol>

            </div>

        </div>

        <div>

            <div class="card-body">


                <div class="row daes-top-sec mb-3">



                    <div class="col-lg-6 col-md-6">

                        <a href="Javascript:void(0)" class="driver_orders_url">

                            <div class="card">

                                <div class="flex-row">

                                    <div class="p-10 bg-info col-md-12 text-center">

                                        <h3 class="text-white box m-b-0"><i class="mdi mdi-cart"></i></h3>

                                    </div>



                                    <div class="align-self-center pt-3 col-md-12 text-center">



                                        <h3 class="m-b-0 text-info" id="total_orders">0</h3>



                                        <h5 class="text-muted m-b-0 driver_order_text">

                                            {{trans('lang.dashboard_total_orders')}}</h5>



                                    </div>



                                </div>



                            </div>

                        </a>

                    </div>



                    <div class="col-lg-6 col-md-6">

                        <a href="{{route('payoutRequests.drivers.view',$id)}}">

                            <div class="card">



                                <div class="flex-row">



                                    <div class="p-10 bg-info col-md-12 text-center">



                                        <h3 class="text-white box m-b-0"><i class="mdi mdi-bank"></i></h3>

                                    </div>



                                    <div class="align-self-center pt-3 col-md-12 text-center">



                                        <h3 class="m-b-0 text-info" id="wallet_amount"></h3>



                                        <h5 class="text-muted m-b-0">{{trans('lang.wallet_Balance')}}</h5>



                                    </div>



                                </div>



                            </div>

                        </a>

                    </div>



                </div>



                <div class="error_top"></div>

                <div class="row vendor_payout_create">

                    <div class="vendor_payout_create-inner">

                        <fieldset>

                            <legend>{{trans('lang.driver_details')}}</legend>

                            <div class="form-group row width-50">

                                <label class="col-3 control-label">{{trans('lang.first_name')}}</label>

                                <div class="col-7">

                                    <input type="text" class="form-control user_first_name"

                                           onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode == 32)">

                                    <div class="form-text text-muted">{{trans('lang.first_name_help')}}</div>

                                </div>

                            </div>



                            <div class="form-group row width-50">

                                <label class="col-3 control-label">{{trans('lang.last_name')}}</label>

                                <div class="col-7">

                                    <input type="text" class="form-control user_last_name"

                                           onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode == 32)">

                                    <div class="form-text text-muted">{{trans('lang.last_name_help')}}</div>

                                </div>

                            </div>



                            <div class="form-group row width-50">

                                <label class="col-3 control-label">{{trans('lang.email')}}</label>

                                <div class="col-7">

                                    <input type="text" class="form-control user_email">

                                    <div class="form-text text-muted">{{trans('lang.user_email_help')}}</div>

                                </div>

                            </div>

                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.user_phone')}}</label>
                                <div class="col-7">
                                    <input type="text" class="form-control user_phone"
                                        onkeypress="return chkAlphabets2(event,'error2')" readonly>
                                    <div id="error2" class="err"></div>
                                    <div class="form-text text-muted">
                                        {{ trans("lang.user_phone_help") }}
                                    </div>
                                </div>
                            </div>



                            <div class="form-group row width-50">

                                <label class="col-3 control-label">{{trans('lang.user_latitude')}}</label>

                                <div class="col-7">

                                    <input type="number" class="form-control user_latitude"

                                           onkeypress="return chkAlphabets3(event,'error2')">

                                    <div id="error2" class="err"></div>

                                    <div class="form-text text-muted">{{trans('lang.user_latitude_help')}}</div>

                                </div>

                            </div>



                            <div class="form-group row width-50">

                                <label class="col-3 control-label">{{trans('lang.user_longitude')}}</label>

                                <div class="col-7">

                                    <input type="number" class="form-control user_longitude"

                                           onkeypress="return chkAlphabets3(event,'error3')">

                                    <div id="error3" class="err"></div>

                                    <div class="form-text text-muted">{{trans('lang.user_longitude_help')}}</div>

                                </div>

                            </div>



                            <div class="form-group row width-50">

                                <label class="col-3 control-label">{{trans('lang.profile_image')}}</label>

                                <div class="col-7">

                                    <input type="file" onChange="handleFileSelect(event)" class="">

                                    <div class="form-text text-muted">{{trans('lang.profile_image_help')}}</div>

                                </div>

                                <div class="placeholder_img_thumb user_image">

                                </div>



                                <div id="uploding_image"></div>

                            </div>



                            <div class="form-group row width-50">

                                <label class="col-3 control-label">{{trans('lang.car_image')}}</label>

                                <div class="col-7">

                                    <input type="file" onChange="handleFileSelectcar(event)" class="">

                                    <div class="form-text text-muted">{{trans('lang.car_image_help')}}</div>

                                </div>

                                <div class="placeholder_img_thumb car_image">

                                </div>

                                <div id="uploding_image_car"></div>

                            </div>



                            <div class="form-group row width-50 individualDiv">

                                <label class="col-3 control-label">{{trans('lang.car_proof')}}</label>

                                <div class="col-7">

                                    <input type="file" onChange="handleFileSelectCarProof(event)" class="">

                                    <div class="form-text text-muted">{{trans('lang.car_proof_help')}}</div>

                                </div>

                                <div class="placeholder_img_thumb car_proof">

                                </div>

                                <div id="uploding_car_proof"></div>

                            </div>



                            <div class="form-group row width-50 individualDiv">

                                <label class="col-3 control-label">{{trans('lang.driver_proof')}}</label>

                                <div class="col-7">

                                    <input type="file" onChange="handleFileSelectDriverProof(event)" class="">

                                    <div class="form-text text-muted">{{trans('lang.driver_proof_help')}}</div>

                                </div>

                                <div class="placeholder_img_thumb driver_proof">

                                </div>

                                <div id="uploding_driver_proof"></div>

                            </div>



                            <div class="form-check width-100">



                                <input type="checkbox" class="col-7 form-check-inline user_active" id="user_active">

                                <label class="col-3 control-label" for="user_active">{{trans('lang.active')}}</label>



                            </div>

                            <div class="form-check width-100">

                                <input type="checkbox" class="col-7 form-check-inline" id="reset_password">



                                <label class="col-3 control-label"

                                       for="reset_password">{{trans('lang.reset_driver_password')}}</label>



                            </div>

                            <div class="form-group row width-100">

                                <div class="form-text text-muted w-100 col-12">

                                    {{ trans("lang.note_reset_driver_password_email") }}

                                </div>

                            </div>

                            <div class="form-group row width-50">

                                <div class="col-3 control-label" style="margin-top: 16px;">

                                    <button type="button" class="btn btn-primary"

                                            id="send_mail">{{trans('lang.send_mail')}}

                                    </button>

                                </div>

                            </div>

                            <br>



                            <div class="form-group row width-50 individualDiv driverRate" style="display: none">

                                <label class="col-3 control-label">{{trans('lang.driver_rate')}}</label>

                                <div class="col-7">

                                    <input type="text" class="form-control driver_rate">

                                    <div class="form-text text-muted">{{trans('lang.driver_rate_help')}}</div>

                                </div>

                            </div>

                        </fieldset>





                        <fieldset>

                            <legend>{{trans('lang.car_details')}}</legend>



                            <div class="form-group row width-50">

                                <label class="col-3 control-label ">{{trans('lang.service_type')}}</label>

                                <div class="col-12">

                                    <select name="service_type" id="service_type" class="form-control service_type"

                                            disabled>

                                        <option value="">{{trans('lang.select')}} {{trans('lang.service_type')}}</option>



                                    </select>

                                </div>

                            </div>





                            <div class="form-group row width-50 car_div">

                                <label class="col-3 control-label">{{trans('lang.car_name')}}</label>

                                <div class="col-7">

                                    <input type="text" class="form-control car_name">

                                    <div class="form-text text-muted">{{trans('lang.car_name_help')}}</div>

                                </div>

                            </div>



                            <div class="form-group row width-50 " id="car_model">

                                <label class="col-3 control-label">{{trans('lang.car_model')}}</label>

                                <div class="col-7">

                                    <input type="text" class="form-control carmodel">

                                    <div class="form-text text-muted">{{trans('lang.car_model_help')}}</div>

                                </div>

                            </div>





                            <div class="form-group row width-50 car_number_field">

                                <label class="col-3 control-label">{{trans('lang.car_number')}}</label>

                                <div class="col-7">

                                    <input type="text" class="form-control car_number">

                                    <div class="form-text text-muted">{{trans('lang.car_number_help')}}</div>

                                </div>

                            </div>





                            <div class="cab_service" style="display: none">

                                <div class="form-group row width-50 individualDiv">

                                    <label class="col-3 control-label ">{{trans('lang.select_section')}}</label>

                                    <div class="col-12">

                                        <select name="cab_section_id" id="cab_section_id"

                                                class="form-control cab_section_id">

                                        </select>

                                    </div>

                                </div>

                                <div class="form-group row width-50 individualDiv">

                                    <label class="col-3 control-label">{{trans('lang.vehicle_type')}}</label>

                                    <div class="col-7">

                                        <select name="vehicle_type" class="form-control vehicle_type">



                                        </select>

                                    </div>

                                </div>



                                <div class="form-group row width-50 individualDiv">

                                    <label class="col-3 control-label">{{trans('lang.car_make')}}</label>

                                    <div class="col-7">

                                        <select name="car_make" class="form-control car_make">

                                            <option value="">{{trans('lang.select')}} {{trans('lang.car_make')}}</option>

                                        </select>

                                    </div>

                                </div>



                                <div class="form-group row width-50 individualDiv">

                                    <label class="col-3 control-label">{{trans('lang.car_model')}}</label>

                                    <div class="col-7">

                                        <select name="car_model" class="form-control car_model">

                                            <option value="">{{trans('lang.select')}} {{trans('lang.car_model')}}</option>

                                        </select>

                                    </div>

                                </div>



                                <div class="form-group row width-50 individualDiv">

                                    <label class="col-3 control-label">{{trans('lang.car_color')}}</label>

                                    <div class="col-7">

                                        <input type="text" class="form-control car_color">

                                        <div class="form-text text-muted">{{trans('lang.car_color_help')}}</div>

                                    </div>

                                </div>



                                <div class="form-group row width-50 chooseRideType">

                                    <label class="col-3 control-label">{{trans('lang.choose_ride_type')}}</label>

                                    <div class="col-7">

                                        <input type="checkbox" class="col-7 form-check-inline" id="is_ride"

                                        >

                                        <label class="control-label" for="is_ride">{{trans('lang.ride')}}</label>

                                    </div>

                                    <div class="col-3">

                                        <input type="checkbox" class="col-7 form-check-inline" id="is_intercity">

                                        <label class="control-label"

                                               for="is_intercity">{{trans('lang.intercity')}}</label>

                                    </div>

                                </div>



                            </div>



                            <div class="rental_service" style="display: none">





                                <div class="form-group row width-50 individualDiv" style="display: none">

                                    <label class="col-3 control-label">{{trans('lang.vehicle_type')}}</label>

                                    <div class="col-7">

                                        <select name="rental_vehicle_type" class="form-control rental_vehicle_type">

                                            <option value="">{{trans('lang.select')}} {{trans('lang.vehicle_type')}}

                                            </option>

                                        </select>

                                    </div>

                                </div>



                                <div class="form-group row width-50 individualDiv" style="display: none">

                                    <label class="col-3 control-label">{{trans('lang.car_rate')}}</label>

                                    <div class="col-7">

                                        <input type="text" class="form-control car_rate">

                                        <div class="form-text text-muted">{{trans('lang.car_rate_help')}}</div>

                                    </div>

                                </div>





                                <div class="form-group row width-50 individualDiv" style="display: none">

                                    <label class="col-3 control-label">{{trans('lang.passengers')}}</label>

                                    <div class="col-7">

                                        <input type="text" class="form-control passenger">

                                        <div class="form-text text-muted">{{trans('lang.passengers_help')}}</div>

                                    </div>

                                </div>



                                <div class="form-group row width-50 individualDiv" style="display: none">

                                    <label class="col-3 control-label">{{trans('lang.doors')}}</label>

                                    <div class="col-7">

                                        <input type="text" class="form-control doors">

                                        <div class="form-text text-muted">{{trans('lang.doors_help')}}</div>

                                    </div>

                                </div>



                                <div class="form-group row width-50 individualDiv" style="display: none">

                                    <label class="col-3 control-label">{{trans('lang.air_conditioning')}}</label>

                                    <div class="col-7">

                                        <select name="rental_vehicle_type" class="form-control air_conditioning">

                                            <option value="Yes">{{trans('lang.yes')}}</option>

                                            <option value="No">{{trans('lang.no')}}</option>

                                        </select>



                                    </div>

                                </div>



                                <div class="form-group row width-50 individualDiv" style="display: none">

                                    <label class="col-3 control-label">{{trans('lang.gear')}}</label>

                                    <div class="col-7">

                                        <select name="rental_vehicle_type" class="form-control gear">

                                            <option value="Manual">{{trans('lang.manual')}}</option>

                                            <option value="Auto">{{trans('lang.auto')}}</option>

                                        </select>



                                    </div>

                                </div>



                                <div class="form-group row width-50 individualDiv" style="display: none">

                                    <label class="col-3 control-label">{{trans('lang.mileage')}}</label>

                                    <div class="col-7">

                                        <select name="rental_vehicle_type" class="form-control mileage">

                                            <option value="Average">{{trans('lang.average')}}</option>

                                            <option value="Ultimated">{{trans('lang.ultimated')}}</option>

                                        </select>



                                    </div>

                                </div>



                                <div class="form-group row width-50 individualDiv" style="display: none">

                                    <label class="col-3 control-label">{{trans('lang.fuel_filling')}}</label>

                                    <div class="col-7">

                                        <select name="rental_vehicle_type" class="form-control fuel_filling">

                                            <option value="Full to Full">{{trans('lang.full_to_full')}}</option>

                                            <option value="Half">{{trans('lang.half')}}</option>

                                        </select>



                                    </div>

                                </div>



                                <div class="form-group row width-50 individualDiv" style="display: none">

                                    <label class="col-3 control-label">{{trans('lang.fuel_type')}}</label>

                                    <div class="col-7">

                                        <select name="rental_vehicle_type" class="form-control fuel_type">

                                            <option value="Petrol">{{trans('lang.petrol')}}</option>

                                            <option value="Diesel">{{trans('lang.diesel')}}</option>

                                        </select>



                                    </div>

                                </div>



                                <div class="form-group row width-50 individualDiv" style="display: none">

                                    <label class="col-3 control-label">{{trans('lang.max_power')}}</label>

                                    <div class="col-7">

                                        <input type="text" class="form-control max_power">

                                        <div class="form-text text-muted">{{trans('lang.max_power_help')}}</div>

                                    </div>

                                </div>



                                <div class="form-group row width-50 individualDiv" style="display: none">

                                    <label class="col-3 control-label">{{trans('lang.mph')}}</label>

                                    <div class="col-7">

                                        <input type="text" class="form-control mph">

                                        <div class="form-text text-muted">{{trans('lang.mph_help')}}</div>

                                    </div>

                                </div>

                                <div class="form-group row width-50 individualDiv" style="display: none">

                                    <label class="col-3 control-label">{{trans('lang.top_speed')}}</label>

                                    <div class="col-7">

                                        <input type="text" class="form-control top_speed">

                                        <div class="form-text text-muted">{{trans('lang.top_speed_help')}}</div>

                                    </div>

                                </div>





                                <div class="form-group row width-100 individualDiv" style="display: none">

                                    <label class="col-3 control-label">{{trans('lang.vehicle_images')}}</label>

                                    <div class="col-7">

                                        <input type="file" onChange="handleFileSelectVehicleImages(event)" class="">

                                        <div class="form-text text-muted">{{trans('lang.vehicle_images_help')}}</div>

                                    </div>



                                    <div class="uploding_vehicle_images"></div>



                                    <div class="placeholder_img_thumb vendor_image">



                                        <div id="photos"></div>

                                    </div>

                                </div>







                            </div>





                        </fieldset>

                        <fieldset>

                            <legend>{{trans('lang.bankdetails')}}</legend>

                            <div class="form-group row" id="companyDriverHideDiv">



                                <div class="form-group row width-100">

                                    <label class="col-4 control-label">{{trans('lang.bank_name')}}</label>

                                    <div class="col-7">

                                        <input type="text" name="bank_name" class="form-control" id="bankName"

                                               onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode == 32)">

                                    </div>

                                </div>



                                <div class="form-group row width-100">

                                    <label class="col-4 control-label">{{trans('lang.branch_name')}}</label>

                                    <div class="col-7">

                                        <input type="text" name="branch_name" class="form-control" id="branchName"

                                               onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode == 32)">

                                    </div>

                                </div>





                                <div class="form-group row width-100">

                                    <label class="col-4 control-label">{{trans('lang.holer_name')}}</label>

                                    <div class="col-7">

                                        <input type="text" name="holer_name" class="form-control" id="holderName"

                                               onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode == 32)">

                                    </div>

                                </div>



                                <div class="form-group row width-100">

                                    <label class="col-4 control-label">{{trans('lang.account_number')}}</label>

                                    <div class="col-7">

                                        <input type="text" name="account_number" class="form-control" id="accountNumber"

                                               onkeypress="return event.charCode >= 48 && event.charCode <= 57">

                                    </div>

                                </div>



                                <div class="form-group row width-100">

                                    <label class="col-4 control-label">{{trans('lang.other_information')}}</label>

                                    <div class="col-7">

                                        <input type="text" name="other_information" class="form-control"

                                               id="otherDetails">

                                    </div>

                                </div>



                            </div>

                        </fieldset>



                    </div>

                </div>

            </div>

            <div class="form-group col-12 text-center btm-btn">

                <button type="button" class="btn btn-primary edit-form-btn"><i class="fa fa-save"></i> {{

                trans('lang.save')}}

                </button>

                <a href="{!! route('drivers') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{

                trans('lang.cancel')}}</a>

            </div>



        </div>

    </div>



@endsection



@section('scripts')



    <script type="text/javascript">



        var id = "<?php echo $id; ?>";

        var database = firebase.firestore();

        var ref = database.collection('users').where("id", "==", id);



        var photo = "";

        var fileName = '';

        var oldProfileFile = '';



        var photoCar = "";

        var carfileName = '';

        var oldCarFile = '';



        var photoCarProof = '';

        var carProofFileName = '';

        var oldCarProofFile = '';



        var photoDriverProof = '';

        var driverProofFileName = '';

        var oldDriverProofFile = '';



        var rentalImages = [];

        var newAddedRentalImages = [];

        var newAddedRentalFileName = [];

        var rentalImageToDelete = [];



        var placeholderImage = '';

        var rentalImagesCount = 0;



        var storage = firebase.storage();

        var storageRef = firebase.storage().ref('images');



        var refCarMake = database.collection('car_make');

        var refCarModel = database.collection('car_model');

        var refVehicleType = database.collection('vehicle_type');

        var refRentalVehicleType = database.collection('rental_vehicle_type');

         var services = database.collection('services').where('flag','in',["rental-service","delivery-service","parcel_delivery","cab-service"]);

        var cab_sections = database.collection('sections').where('serviceTypeFlag', '==', 'cab-service').where('isActive', '==', true);

        var serviceType = "";

        var isCompany = false;

        var placeholder = database.collection('settings').doc('placeHolderImage');



        placeholder.get().then(async function (snapshotsimage) {

            var placeholderImageData = snapshotsimage.data();

            placeholderImage = placeholderImageData.image;

        })





        var currency = database.collection('settings');



        var currentCurrency = '';

        var currencyAtRight = false;

        var decimal_degits = 0;



        var refCurrency = database.collection('currencies').where('isActive', '==', true);

        refCurrency.get().then(async function (snapshots) {

            var currencyData = snapshots.docs[0].data();

            currentCurrency = currencyData.symbol;

            currencyAtRight = currencyData.symbolAtRight;



            if (currencyData.decimal_degits) {

                decimal_degits = currencyData.decimal_degits;

            }

        });



        $("#send_mail").click(function () {

            if ($("#reset_password").is(":checked")) {

                var email = $(".user_email").val();

                firebase.auth().sendPasswordResetEmail(email)

                    .then((res) => {

                        alert('{{trans("lang.driver_mail_sent")}}');

                    })

                    .catch((error) => {

                        console.log('Error password reset: ', error);

                    });

            } else {

                alert('{{trans("lang.error_reset_driver_password")}}');

            }

        });



        refCarMake.get().then(async function (snapshots) {

            snapshots.docs.forEach((listval) => {

                var data = listval.data();



                $('.car_make').append($("<option></option>")

                    .attr("value", data.name)

                    .text(data.name));

            })



        });



        refCarModel.get().then(async function (snapshots) {

            snapshots.docs.forEach((listval) => {

                var data = listval.data();



                $('.car_model').append($("<option></option>")

                    .attr("value", data.name)

                    .text(data.name));

            })



        });



        refVehicleType.get().then(async function (snapshots) {

            $('.vehicle_type').append('<option value="">{{trans("lang.select")}} {{trans("lang.vehicle_type")}}</option>');

            snapshots.docs.forEach((listval) => {

                var data = listval.data();

                $('.vehicle_type').append($("<option></option>")

                    .attr("value", data.name)

                    .attr("data-id", data.id)

                    .text(data.name));

            })

        });



        refRentalVehicleType.get().then(async function (snapshots) {

            snapshots.docs.forEach((listval) => {

                var data = listval.data();



                $('.rental_vehicle_type').append($("<option></option>")

                    .attr("value", data.name)

                    .attr("data-id", data.id)

                    .text(data.name));

            })



        });



        services.get().then(async function (snapshots) {

            snapshots.docs.forEach((listval) => {

                var data = listval.data();



                $('.service_type').append($("<option></option>")

                    .attr("value", data.flag)

                    .text(data.name));

            });

        });



        cab_sections.get().then(async function (snapshots) {

            snapshots.docs.forEach((listval) => {

                var data = listval.data();

                $('.cab_section_id').append($("<option></option>")

                    .attr("value", data.id)

                    .text(data.name));

            });

        });





        $(document).ready(function () {


            jQuery("#data-table_processing").show();

           
            ref.get().then(async function (snapshots) {

                var user = snapshots.docs[0].data();



                $(".user_first_name").val(user.firstName);

                $(".user_last_name").val(user.lastName);

                $(".user_email").val(shortEmail(user.email)).prop('disabled',true);

                $(".carmodel").val(user.carMakes);
                $(".car_name").val(user.carName);

                $(".car_number").val(user.carNumber);

                if(user.phoneNumber.includes('+')){

                    $(".user_phone").val('+' + EditPhoneNumber(user.phoneNumber.slice(1))).prop('disabled',true);

                }else{

                    $(".user_phone").val(EditPhoneNumber(user.phoneNumber)).prop('disabled',true);

                }

                if (user.hasOwnProperty('carMakes')) {

                    $('.car_make').val(user.carMakes).trigger('change');

                }


                if (user.hasOwnProperty('carName') && user.serviceType == "cab-service") {

                    $('.car_model').val(user.carName).trigger('change');

                }



                if (user.hasOwnProperty('carColor')) {

                    $('.car_color').val(user.carColor);

                }



                if (user.hasOwnProperty('serviceType')) {

                    $('.service_type').val(user.serviceType).trigger('change');

                    serviceType = user.serviceType;



                }

                if (serviceType == "rental-service") {

                    $('.rental_service').show();

                    $('.cab_service').hide();



                } else if (serviceType == "parcel_delivery") {

                } else if (serviceType == "cab-service") {

                    $('.cab_service').show();

                    $('.rental_service').hide();



                } else {

                    $('.cab_service').hide();

                    $('.rental_service').hide();

                }



                if (user.hasOwnProperty('driverRate')) {

                    $('.driver_rate').val(user.driverRate);



                    if (user.hasOwnProperty('serviceType') && user.serviceType == "cab-service") {

                        $('.driverRate').hide();

                    }

                }



                if (user.hasOwnProperty('vehicleType')) {

                    $('.rental_vehicle_type').val(user.vehicleType);

                }



                if (user.hasOwnProperty('sectionId') && user.sectionId != '') {

                    $('.cab_section_id').val(user.sectionId);



                    var options = '<option value="">{{trans("lang.select")}} {{trans("lang.vehicle_type")}}</option>';

                    refVehicleType.where('sectionId', '==', user.sectionId).get().then(async function (snapshots) {

                        snapshots.docs.forEach((listval) => {

                            var data = listval.data();

                            if (user.hasOwnProperty('vehicleType') && user.vehicleType != '' && data.name == user.vehicleType) {

                                options += '<option value="' + data.name + '" data-id="' + data.id + '" selected>' + data.name + '</option>';

                            } else {

                                options += '<option value="' + data.name + '" data-id="' + data.id + '">' + data.name + '</option>';

                            }

                        })

                        $(".vehicle_type").html(options);

                    });

                }



                if (user.hasOwnProperty('rideType')) {

                    if (user.rideType == "both") {

                        $("#is_intercity").prop('checked', true);

                    } else {

                        $("#is_ride").prop('checked', true);

                    }

                }



                if (user.hasOwnProperty('serviceType') && user.serviceType == "cab-service") {

                    $(".chooseRideType").show();

                } else {

                    $(".chooseRideType").hide();

                }



                if (user.hasOwnProperty('carRate')) {

                    $('.car_rate').val(user.carRate);

                }



                var carPhotoes = '';



                if (user.hasOwnProperty('carInfo')) {

                    var carInfo = user.carInfo;



                    if (user.carName) {

                        $('.car_name').val(user.carName);

                    }



                    if (carInfo.passenger) {

                        $('.passenger').val(carInfo.passenger);

                    }



                    if (carInfo.doors) {

                        $('.doors').val(carInfo.doors);

                    }

                    if (carInfo.air_conditioning) {

                        $('.air_conditioning').val(carInfo.air_conditioning).trigger('change');



                    }



                    if (carInfo.gear) {

                        $('.gear').val(carInfo.gear).trigger('change');



                    }

                    if (carInfo.mileage) {

                        $('.mileage').val(carInfo.mileage).trigger('change');

                    }

                    if (carInfo.fuel_filling) {

                        $('.fuel_filling').val(carInfo.fuel_filling).trigger('change');

                    }

                    if (carInfo.fuel_type) {

                        $('.fuel_type').val(carInfo.fuel_type).trigger('change');

                    }

                    if (carInfo.maxPower) {

                        $('.max_power').val(carInfo.maxPower);

                    }

                    if (carInfo.mph) {

                        $('.mph').val(carInfo.mph);

                    }

                    if (carInfo.topSpeed) {

                        $('.top_speed').val(carInfo.topSpeed);

                    }



                    if (carInfo.car_image) {

                        if (carInfo.car_image.length > 0) {

                            rentalImages = carInfo.car_image;

                            carInfo.car_image.forEach((photoImg) => {

                                rentalImagesCount++;

                                carPhotoes = carPhotoes + '<span class="image-item" id="photo_' + rentalImagesCount + '"><span class="remove-btn" data-id="' + rentalImagesCount + '" data-img="' + photoImg + '" data-status="old"><i class="fa fa-remove"></i></span><img width="100px" id="" height="auto" src="' + photoImg + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'"></span>';

                            })

                        }

                    }



                }



                if (carPhotoes) {

                    $("#photos").html(carPhotoes);

                } else {

                    $("#photos").html('<p>photos not available.</p>');

                }



                if (user.hasOwnProperty('carProofPictureURL')) {

                    carProofPictureURL = user.carProofPictureURL;

                    if (user.carProofPictureURL != '' && user.carProofPictureURL != null) {

                        oldCarProofFile = user.carProofPictureURL;

                        photoCarProof = user.carProofPictureURL;

                        $(".car_proof").append('<img class="rounded" style="width:50px" src="' + user.carProofPictureURL + '" alt="image" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'">');

                    } else {



                        $(".car_proof").append('<img class="rounded" style="width:50px" src="' + placeholderImage + '" alt="image">');

                    }

                }

                if (user.hasOwnProperty('driverProofPictureURL')) {

                    driverProofPictureURL = user.driverProofPictureURL;



                    if (user.driverProofPictureURL != '' && user.driverProofPictureURL != null) {

                        oldDriverProofFile = user.driverProofPictureURL;

                        photoDriverProof = user.driverProofPictureURL;

                        $(".driver_proof").append('<img class="rounded" style="width:50px" src="' + user.driverProofPictureURL + '" alt="image" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'">');

                    } else {



                        $(".driver_proof").append('<img class="rounded" style="width:50px" src="' + placeholderImage + '" alt="image">');

                    }

                }

                if (user.hasOwnProperty('vehicleType')) {

                    $('.vehicle_type').val(user.vehicleType).trigger('change');

                }





                if (user.hasOwnProperty('location')) {

                    $(".user_latitude").val(user.location.latitude);

                    $(".user_longitude").val(user.location.longitude);

                }



                oldProfileFile = user.profilePictureURL;

                oldCarFile = user.carPictureURL;



                if (user.active) {

                    $(".user_active").prop('checked', true);

                }

                if (oldCarFile != '' && oldCarFile != null) {

                    $(".car_image").append('<img class="rounded" style="width:50px" src="' + oldCarFile + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" alt="image">');

                } else {

                    $(".car_image").append('<img class="rounded" style="width:50px" src="' + placeholderImage + '" alt="image">');

                }

                if (oldProfileFile != '' && oldProfileFile != null) {

                    $(".user_image").append('<img class="rounded" style="width:50px" src="' + oldProfileFile + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" alt="image">');

                } else {

                    $(".user_image").append('<img class="rounded" style="width:50px" src="' + placeholderImage + '" alt="image">');

                }





                var wallet = 0;



                if (user.wallet_amount) {

                    wallet = user.wallet_amount;

                }

                if (currencyAtRight) {

                    wallet = parseFloat(wallet).toFixed(decimal_degits) + "" + currentCurrency;

                } else {

                    wallet = currentCurrency + "" + parseFloat(wallet).toFixed(decimal_degits);

                }



                $("#wallet_amount").text(wallet);



                getTotalOrders(id, user.serviceType);



                if (user.userBankDetails) {

                    if (user.userBankDetails.bankName != undefined) {

                        $("#bankName").val(user.userBankDetails.bankName);

                    }

                    if (user.userBankDetails.branchName != undefined) {

                        $("#branchName").val(user.userBankDetails.branchName);

                    }

                    if (user.userBankDetails.holderName != undefined) {

                        $("#holderName").val(user.userBankDetails.holderName);

                    }

                    if (user.userBankDetails.accountNumber != undefined) {

                        $("#accountNumber").val(user.userBankDetails.accountNumber);

                    }

                    if (user.userBankDetails.otherDetails != undefined) {

                        $("#otherDetails").val(user.userBankDetails.otherDetails);

                    }

                }



                jQuery("#data-table_processing").hide();



            })



            $('.cab_section_id').on('change', function () {

                var cab_section_id = $(this).val();

                var options = '<option value="">{{trans("lang.select")}} {{trans("lang.vehicle_type")}}</option>';

                refVehicleType.where('sectionId', '==', cab_section_id).get().then(async function (snapshots) {

                    snapshots.docs.forEach((listval) => {

                        var data = listval.data();

                        options += '<option value="' + data.name + '" data-id="' + data.id + '">' + data.name + '</option>';

                    })

                    $(".vehicle_type").html(options);

                });

            })



            $('.service_type').on('change', function () {



                var service_type = $(this).val();



                if (service_type == "rental-service") {

                    $('.rental_service').show();

                    $('.individualDiv').show();

                    $('.cab_service').hide();

                    $('#car_model').show();

                    $('.car_number_field').show();
                    $('.car_name').show();

                } else if (service_type == "parcel_delivery") {

                    $('#car_model').show();

                    $('.car_number_field').show();

                    $('.cab_service').hide();

                    $('.rental_service').hide();

                } else if (service_type == "cab-service") {

                    $('.cab_service').show();

                    $('.individualDiv').show();

                    $('#car_model').hide();

                    $('.car_div').hide();

                    $('.car_number_field').show();

                    $('.rental_service').hide();

                    $('.driverRate').hide();

                } else {

                    $('.cab_service').hide();

                    $('.rental_service').hide();

                    $('#car_model').show();

                    $('.car_number_field').show();



                }

            });





            $(".edit-form-btn").click(function () {



                var userFirstName = $(".user_first_name").val();

                var userLastName = $(".user_last_name").val();

                var email = $(".user_email").val();
               
                var userPhone = $(".user_phone").val();

                var active = $(".user_active").is(":checked");

                var car_model = $(".carmodel").val();

                var carName = $(".car_name").val();

                var carNumber = $(".car_number").val();

                var latitude = parseFloat($(".user_latitude").val());

                var longitude = parseFloat($(".user_longitude").val());

                var carMakeId = $('.car_make').val();

                var carMakeName = $('.car_make option:selected').text();

                var carModelId = $('.car_model').val();

                var carModelName = $('.car_model option:selected').text();





                var vehicleTypeName = $('.vehicle_type option:selected').text();

                var cabSectionId = $('.cab_section_id').val();

                var carColor = $('.car_color').val();



                var rideType = 'ride';

                if ($("#is_intercity").is(":checked") == true) {

                    rideType = 'both';

                }



                var driverRate = "";

                var carRate = "";

                var vehicleType = "";

                var vehicleTypeId = "";

                var carInfo = {};

                var air_conditioning = "";

                var doors = "";

                var fuel_filling = "";

                var fuel_type = "";

                var gear = "";

                var maxPower = "";

                var mileage = "";

                var mph = "";

                var passenger = "";

                var topSpeed = "";

                var carName = "";

                if (serviceType == "rental-service") {



                    vehicleType = $('.rental_vehicle_type').val();

                    vehicleTypeId = $('.rental_vehicle_type option:selected').data('id');

                    air_conditioning = $('.air_conditioning').val();

                    doors = $('.doors').val();

                    fuel_filling = $('.fuel_filling').val();

                    fuel_type = $('.fuel_type').val();

                    gear = $('.gear').val();

                    maxPower = $('.max_power').val();

                    mileage = $('.mileage').val();

                    mph = $('.mph').val();

                    passenger = $('.passenger').val();

                    topSpeed = $('.top_speed').val();

                    driverRate = $('.driver_rate').val();

                    carRate = $('.car_rate').val();
                    carName = $('.car_name').val();



                } else if (serviceType == "cab-service") {

                    vehicleType = $('.vehicle_type').val();

                    vehicleTypeId = $('.vehicle_type option:selected').data('id');

                }



                if (userFirstName == '') {

                    $(".error_top").show();

                    $(".error_top").html("");

                    $(".error_top").append("<p>{{trans('lang.enter_owners_name_error')}}</p>");

                    window.scrollTo(0, 0);



                } else if (userPhone == '') {

                    $(".error_top").show();

                    $(".error_top").html("");

                    $(".error_top").append("<p>{{trans('lang.enter_owners_phone')}}</p>");

                    window.scrollTo(0, 0);

                }else if(isNaN(latitude)) {

                    $(".error_top").show();

                    $(".error_top").html("");

                    $(".error_top").append("<p>{{trans('lang.driver_lattitude_error')}}</p>");

                    window.scrollTo(0, 0);

                } else if (latitude < -90 || latitude > 90) {

                    $(".error_top").show();

                    $(".error_top").html("");

                    $(".error_top").append("<p>{{trans('lang.driver_lattitude_limit_error')}}</p>");

                    window.scrollTo(0, 0);

                } else if (isNaN(longitude)) {

                    $(".error_top").show();

                    $(".error_top").html("");

                    $(".error_top").append("<p>{{trans('lang.driver_longitude_error')}}</p>");

                    window.scrollTo(0, 0);

                } else if (longitude < -180 || longitude > 180) {

                    $(".error_top").show();

                    $(".error_top").html("");

                    $(".error_top").append("<p>{{trans('lang.driver_longitude_limit_error')}}</p>");

                    window.scrollTo(0, 0);

                } else if ((vehicleType == '' || vehicleTypeId == '')  && (serviceType == "rental-service" || serviceType == "cab-service")) {

                    $(".error_top").show();

                    $(".error_top").html("");

                    $(".error_top").append("<p>{{trans('lang.vehicle_type_error')}}</p>");

                    window.scrollTo(0, 0);

                } else if (carNumber == '') {

                    $(".error_top").show();

                    $(".error_top").html("");

                    $(".error_top").append("<p>{{trans('lang.car_number_error')}}</p>");

                    window.scrollTo(0, 0);

                }

                else {



                    var bankName = $("#bankName").val();

                    var branchName = $("#branchName").val();

                    var holderName = $("#holderName").val();

                    var accountNumber = $("#accountNumber").val();

                    var otherDetails = $("#otherDetails").val();

                    var userBankDetails = {

                        'bankName': bankName,

                        'branchName': branchName,

                        'holderName': holderName,

                        'accountNumber': accountNumber,

                        'accountNumber': accountNumber,

                        'otherDetails': otherDetails,

                    };



                    if (serviceType != "cab-service") {

                        carMakeId = car_model;

                        carModelId = carColor = carProofPictureURL = driverProofPictureURL = "";

                    }

                    storeImageData().then(IMG => {

                        storeVehicleImageData().then(IMGVEH => {



                            carInfo = {

                                "air_conditioning": air_conditioning,

                                "car_image": IMGVEH,

                                "doors": doors,

                                "fuel_filling": fuel_filling,

                                "fuel_type": fuel_type,

                                "gear": gear,

                                "maxPower": maxPower,

                                "mileage": mileage,

                                "mph": mph,

                                "passenger": passenger,

                                "topSpeed": topSpeed,

                            };

                            database.collection('users').doc(id).update({

                                'firstName': userFirstName,

                                'lastName': userLastName,

                                'email': email,
                                
                                'phoneNumber': userPhone,

                                'active': active,

                                'profilePictureURL': IMG.profile,

                                'carName': carName,

                                'carNumber': carNumber,

                                // 'carMakes': carMakeId,
                                'carMakes': car_model,

                                'carModelName': carModelName,

                                'vehicleId': vehicleTypeId,

                                'sectionId': cabSectionId,

                                'rideType': rideType,

                                'carColor': carColor,

                                'carProofPictureURL': IMG.photoCarProof,

                                'driverProofPictureURL': IMG.photoDriverProof,

                                'location.latitude': latitude,

                                'location.longitude': longitude,

                                'carPictureURL': IMG.photoCar,

                                'role': 'driver',

                                'userBankDetails': userBankDetails,

                                'driverRate': driverRate,

                                'vehicleType': vehicleType,

                                'carRate': carRate,

                                'carInfo': carInfo,



                            }).then(function (result) {



                                window.location.href = '{{ route("drivers")}}';



                            });

                        }).catch(err => {

                            jQuery("#data-table_processing").hide();

                            $(".error_top").show();

                            $(".error_top").html("");

                            $(".error_top").append("<p>" + err + "</p>");

                            window.scrollTo(0, 0);

                        });



                    }).catch(function (error) {

                        jQuery("#data-table_processing").hide();



                        $(".error_top").show();

                        $(".error_top").html("");

                        $(".error_top").append("<p>" + error + "</p>");

                        window.scrollTo(0, 0);

                    });

                }

            })





        })



        async function getTotalOrders(id, type) {



            var count_order_complete = 0;



            var url = "Javascript:void(0)";



            var order_text = '';



            if (type == "cab-service") {

                url = "{{route('drivers.rides','driverId')}}";

                url = url.replace('driverId', id);

                await database.collection('rides').where('driverID', '==', id).get().then(async function (orderSnapshots) {

                    count_order_complete = orderSnapshots.docs.length;



                });



                order_text = "{{trans('lang.rides')}}";





            } else if (type == "rental-service") {



                url = "{{route('rental_orders.driver','id')}}";

                url = url.replace("id", id);

                await database.collection('rental_orders').where('driverID', '==', id).get().then(async function (orderSnapshots) {

                    count_order_complete = orderSnapshots.docs.length;



                });



                order_text = "{{trans('lang.rental_orders')}}";





            } else if (type == "delivery-service" || type == "ecommerce-service") {

                url = "{{route('orders','id')}}";

                url = url.replace("id", 'driverId=' + id);



                await database.collection('vendor_orders').where('driverID', '==', id).get().then(async function (orderSnapshots) {

                    count_order_complete = orderSnapshots.docs.length;



                });



                order_text = "{{trans('lang.order_plural')}}";





            } else if (type == "parcel_delivery") {



                url = "{{route('parcel_orders.driver','id')}}";

                url = url.replace("id", id);

                await database.collection('parcel_orders').where('driverID', '==', id).get().then(async function (orderSnapshots) {

                    count_order_complete = orderSnapshots.docs.length;



                });



                order_text = "{{trans('lang.parcel_orders')}}";





            }

            $("#total_orders").text(count_order_complete);

            $('.driver_order_text').html(order_text);

            $('.driver_orders_url').attr('href', url);

        }



        function openLoadingPanel(title, text, icon, isButtonTrue = false) {



            swal({

                title: title,

                text: text,

                icon: icon,

                closeOnClickOutside: false,

                buttons: isButtonTrue,

                html: false,

            });

        }





        function handleFileSelect(evt) {

            var f = evt.target.files[0];

            var reader = new FileReader();



            reader.onload = (function (theFile) {

                return function (e) {



                    var filePayload = e.target.result;

                    var hash = CryptoJS.SHA256(Math.random() + CryptoJS.SHA256(filePayload));

                    var val = f.name;

                    var ext = val.split('.')[1];

                    var docName = val.split('fakepath')[1];

                    var filename = (f.name).replace(/C:\\fakepath\\/i, '')



                    var timestamp = Number(new Date());

                    var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;

                    photo = filePayload;

                    fileName = filename;

                    $(".user_image").empty();

                    $(".user_image").append('<img class="rounded" style="width:50px" src="' + photo + '" alt="image" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'">');



                };

            })(f);

            reader.readAsDataURL(f);

        }



        var storageRefcar = firebase.storage().ref('images');



        function handleFileSelectcar(evt) {

            var f = evt.target.files[0];

            var reader = new FileReader();



            reader.onload = (function (theFile) {

                return function (e) {



                    var filePayload = e.target.result;

                    var hash = CryptoJS.SHA256(Math.random() + CryptoJS.SHA256(filePayload));

                    var val = f.name;

                    var ext = val.split('.')[1];

                    var docName = val.split('fakepath')[1];

                    var filename = (f.name).replace(/C:\\fakepath\\/i, '')



                    var timestamp = Number(new Date());

                    var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;

                    photoCar = filePayload;

                    carfileName = filename;

                    $(".car_image").empty();

                    $(".car_image").append('<img class="rounded" style="width:50px" src="' + filePayload + '" alt="image" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'">');



                };

            })(f);

            reader.readAsDataURL(f);

        }



        function handleFileSelectCarProof(evt) {

            var f = evt.target.files[0];

            var reader = new FileReader();



            reader.onload = (function (theFile) {

                return function (e) {



                    var filePayload = e.target.result;

                    var hash = CryptoJS.SHA256(Math.random() + CryptoJS.SHA256(filePayload));



                    var val = f.name;

                    var ext = val.split('.')[1];

                    var docName = val.split('fakepath')[1];

                    var filename = (f.name).replace(/C:\\fakepath\\/i, '')



                    var timestamp = Number(new Date());

                    var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;

                    carProofFileName = filename;

                    photoCarProof = filePayload;

                    $(".car_proof").empty();

                    $(".car_proof").append('<img class="rounded" style="width:50px" src="' + filePayload + '" alt="image" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'">');



                };

            })(f);

            reader.readAsDataURL(f);

        }



        function handleFileSelectDriverProof(evt) {

            var f = evt.target.files[0];

            var reader = new FileReader();



            reader.onload = (function (theFile) {

                return function (e) {



                    var filePayload = e.target.result;

                    var hash = CryptoJS.SHA256(Math.random() + CryptoJS.SHA256(filePayload));



                    var val = f.name;

                    var ext = val.split('.')[1];

                    var docName = val.split('fakepath')[1];

                    var filename = (f.name).replace(/C:\\fakepath\\/i, '')



                    var timestamp = Number(new Date());

                    var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;

                    driverProofFileName = filename;

                    photoDriverProof = filePayload;

                    $(".driver_proof").empty();

                    $(".driver_proof").append('<img class="rounded" style="width:50px" src="' + filePayload + '" alt="image" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'">');



                };

            })(f);

            reader.readAsDataURL(f);

        }





        function handleFileSelectVehicleImages(evt) {

            var f = evt.target.files[0];

            var reader = new FileReader();

            reader.onload = (function (theFile) {

                return function (e) {



                    var filePayload = e.target.result;

                    var hash = CryptoJS.SHA256(Math.random() + CryptoJS.SHA256(filePayload));

                    var val = f.name;

                    var ext = val.split('.')[1];

                    var docName = val.split('fakepath')[1];

                    var filename = (f.name).replace(/C:\\fakepath\\/i, '')



                    var timestamp = Number(new Date());

                    var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;

                    rentalImagesCount++;

                    photos_html = '<span class="image-item" id="photo_' + rentalImagesCount + '"><span class="remove-btn" data-id="' + rentalImagesCount + '" data-img="' + filePayload + '" data-status="new"><i class="fa fa-remove"></i></span><img width="100px" id="" height="auto" src="' + filePayload + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'"></span>';

                    $("#photos").append(photos_html);

                    newAddedRentalImages.push(filePayload);

                    newAddedRentalFileName.push(filename);

                };

            })(f);

            reader.readAsDataURL(f);

        }



        $(document).on("click", ".remove-btn", function () {

            var id = $(this).attr('data-id');

            var photo_remove = $(this).attr('data-img');

            $("#photo_" + id).remove();

            var status = $(this).attr('data-status');

            if (status == "old") {

                rentalImageToDelete.push(firebase.storage().refFromURL(photo_remove));

            }

            index = rentalImages.indexOf(photo_remove);

            if (index > -1) {

                rentalImages.splice(index, 1); // 2nd parameter means remove one item only

            }

            index = newAddedRentalImages.indexOf(photo_remove);

            if (index > -1) {

                newAddedRentalImages.splice(index, 1); // 2nd parameter means remove one item only

                newAddedRentalFileName.splice(index, 1);

            }



        });



        async function storeImageData() {

            var newPhoto = [];

            newPhoto['photoDriverProof'] = '';

            newPhoto['photoCarProof'] = '';

            newPhoto['photoCar'] = '';

            newPhoto['profile'] = '';



            if (photo != '' && photo != oldProfileFile) {



                    if (oldProfileFile != "" && oldProfileFile != null) {



                        var oldImageUrlRef = await storage.refFromURL(oldProfileFile);

                        imageBucket = oldImageUrlRef.bucket;

                        var envBucket = "<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";



                        if (imageBucket == envBucket) {

                            if (oldImageUrlRef) {

                                await oldImageUrlRef.delete().then(() => {

                                    console.log("Old file deleted!")

                                }).catch((error) => {

                                    console.log("ERR File delete ===", error);

                                });

                            }

                        } else {

                            console.log('Bucket not matched');

                        }

                    }



                    try {

                        photo = photo.replace(/^data:image\/[a-z]+;base64,/, "")

                        var uploadTask = await storageRef.child(fileName).putString(photo, 'base64', {contentType: 'image/jpg'});

                        var downloadURL = await uploadTask.ref.getDownloadURL();

                        newPhoto['profile'] = downloadURL;

                        photo = downloadURL;

                    } catch (error) {

                        console.log("ERR ===", error);

                    }



            } else

                newPhoto['profile'] = oldProfileFile;





            if (photoCar != '' && photoCar != oldCarFile) {



                    if (oldCarFile != "" && oldCarFile != null) {

                        var oldCarImageUrlRef = await storage.refFromURL(oldCarFile);

                        imageBucket = oldCarImageUrlRef.bucket;

                        var envBucket = "<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";



                        if (imageBucket == envBucket) {

                            if (oldCarImageUrlRef != '') {

                                await oldCarImageUrlRef.delete().then(() => {

                                    console.log("Old file deleted!")

                                }).catch((error) => {

                                    console.log("ERR File delete ===", error);

                                });

                            }

                        } else {

                            console.log('Bucket not matched');

                        }

                    }



                    try {

                        photoCar = photoCar.replace(/^data:image\/[a-z]+;base64,/, "")

                        var uploadTask = await storageRef.child(carfileName).putString(photoCar, 'base64', {contentType: 'image/jpg'});

                        var downloadURL = await uploadTask.ref.getDownloadURL();

                        newPhoto['photoCar'] = downloadURL;

                        photoCar = downloadURL;

                    } catch (error) {

                        console.log("ERR ===", error);

                    }



            } else

                newPhoto['photoCar'] = oldCarFile;







            if (photoCarProof != '' && photoCarProof != oldCarProofFile) {



                    if (oldCarProofFile != "" && oldCarProofFile != null) {

                        var oldCarProofImageUrlRef = await storage.refFromURL(oldCarProofFile);

                        imageBucket = oldCarProofImageUrlRef.bucket;

                        var envBucket = "<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";



                        if (imageBucket == envBucket) {

                            if (oldCarProofImageUrlRef) {

                                await oldCarProofImageUrlRef.delete().then(() => {

                                    console.log("Old file deleted!")

                                }).catch((error) => {

                                    console.log("ERR File delete ===", error);

                                });

                            }

                        } else {

                            console.log('Bucket not matched');

                        }

                    }

                    try {

                        photoCarProof = photoCarProof.replace(/^data:image\/[a-z]+;base64,/, "")

                        var uploadTask = await storageRef.child(carProofFileName).putString(photoCarProof, 'base64', {contentType: 'image/jpg'});

                        var downloadURL = await uploadTask.ref.getDownloadURL();

                        newPhoto['photoCarProof'] = downloadURL;

                        photoCarProof = downloadURL;

                    } catch (error) {

                        console.log("ERR ===", error);

                    }



            }else

                newPhoto['photoCarProof'] = oldCarProofFile;





            if (photoDriverProof != '' && photoDriverProof != oldDriverProofFile) {



                if (oldDriverProofFile != "" && oldDriverProofFile != null) {

                    var oldDriverProofImageUrlRef = await storage.refFromURL(oldDriverProofFile);

                    imageBucket = oldDriverProofImageUrlRef.bucket;

                    var envBucket = "<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";



                    if (imageBucket == envBucket) {

                        if (oldDriverProofImageUrlRef) {

                            await oldDriverProofImageUrlRef.delete().then(() => {

                                console.log("Old file deleted!")

                            }).catch((error) => {

                                console.log("ERR File delete ===", error);

                            });

                        }

                    } else {

                        console.log('Bucket not matched');

                    }

                }



                    try {

                        photoDriverProof = photoDriverProof.replace(/^data:image\/[a-z]+;base64,/, "")

                        var uploadTask = await storageRef.child(driverProofFileName).putString(photoDriverProof, 'base64', {contentType: 'image/jpg'});

                        var downloadURL = await uploadTask.ref.getDownloadURL();

                        newPhoto['photoDriverProof'] = downloadURL;

                        photoDriverProof = downloadURL;

                    } catch (error) {

                        console.log("ERR ===", error);

                    }



            }else

                newPhoto['photoDriverProof'] = oldDriverProofFile;



            return newPhoto;

        }



        async function storeVehicleImageData() {

            var newPhoto = [];

            if (rentalImages.length > 0) {

                newPhoto = rentalImages;

            }

            if (newAddedRentalImages.length > 0) {

                await Promise.all(newAddedRentalImages.map(async (vehPhoto, index) => {



                    vehPhoto = vehPhoto.replace(/^data:image\/[a-z]+;base64,/, "");

                    var uploadTask = await storageRef.child(newAddedRentalFileName[index]).putString(vehPhoto, 'base64', {contentType: 'image/jpg'});

                    var downloadURL = await uploadTask.ref.getDownloadURL();

                    newPhoto.push(downloadURL);

                }));

            }

            if (rentalImageToDelete.length > 0) {

                await Promise.all(rentalImageToDelete.map(async (delImage) => {



                    imageBucket = delImage.bucket;

                    var envBucket = "<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";

                    if (imageBucket == envBucket) {

                        await delImage.delete().then(() => {

                            console.log("Old file deleted!")

                        }).catch((error) => {

                            console.log("ERR File delete ===", error);

                        });

                    } else {

                        console.log('Bucket not matched');

                    }



                }));



            }

            return newPhoto;

        }



        function chkAlphabets3(event, msg) {

            if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {

                document.getElementById(msg).innerHTML = "Accept only Number and Dot(.)";

                return false;

            } else {

                document.getElementById(msg).innerHTML = "";

                return true;

            }

        }


    </script>

@endsection

