@extends('layouts.app')
@section('content')

<?php
$countries = file_get_contents(public_path('countriesdata.json'));
$countries = json_decode($countries);
$countries = (array)$countries;
$newcountries = array();
$newcountriesjs = array();
foreach ($countries as $keycountry => $valuecountry) {
    $newcountries[$valuecountry->phoneCode] = $valuecountry;
    $newcountriesjs[$valuecountry->phoneCode] = $valuecountry->code;
}
?>

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

        <div>

            <div class="card-body">


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

                                    <input type="email" class="form-control user_email">

                                    <div class="form-text text-muted">{{trans('lang.user_email_help')}}</div>

                                </div>

                            </div>



                            <div class="form-group row width-50">

                                <label class="col-3 control-label">{{trans('lang.password')}}</label>

                                <div class="col-7">

                                    <input type="password" class="form-control user_password">

                                    <div class="form-text text-muted">{{trans('lang.user_password_help')}}</div>

                                </div>

                            </div>


                            <div class="form-group row"> 
                                <label class="col-3 control-label">{{trans('lang.user_phone')}}</label>
                                <div class="col-md-6">
                                <div class="phone-box position-relative" id="phone-box">
                                        <select name="country" id="country_selector">
                                        <?php foreach ($newcountries as $keycy => $valuecy) { ?>
                                        <?php $selected = ""; ?>
                                        <option <?php echo $selected; ?> code="<?php echo $valuecy->code; ?>"
                                            value="<?php echo $keycy; ?>">
                                            +<?php echo $valuecy->phoneCode; ?> {{$valuecy->countryName}}</option>
                                        <?php } ?>
                                        </select>
                                        <input type="text" class="form-control user_phone"  onkeypress="return chkAlphabets2(event,'error2')">
                                        <div id="error2" class="err"></div>
                                                    </div>
                                                </div>
                                <div class="form-text text-muted">
                                    {{trans('lang.user_phone_help')}}
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

                                <div class="placeholder_img_thumb user_image"></div>

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

                                    <select name="service_type" id="service_type" class="form-control service_type">

                                        <option value="">{{trans('lang.select')}} {{trans('lang.service_type')}}

                                        </option>



                                    </select>

                                </div>

                            </div>



                            <div class="form-group row width-50 rental_service" style="display:none">

                                <label class="col-3 control-label">{{trans('lang.car_name')}}</label>

                                <div class="col-7">

                                    <input type="text" class="form-control car_name">

                                    <div class="form-text text-muted">{{trans('lang.car_name_help')}}</div>

                                </div>

                            </div>



                            <div class="form-group row width-50" id="car_model">

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

                                            <option value="">{{trans('lang.select_section')}}</option>

                                        </select>

                                    </div>

                                </div>

                                <div class="form-group row width-50 individualDiv">

                                    <label class="col-3 control-label">{{trans('lang.vehicle_type')}}</label>

                                    <div class="col-7">

                                        <select name="vehicle_type" class="form-control vehicle_type">

                                            <option value="">{{trans('lang.select')}} {{trans('lang.vehicle_type')}}

                                            </option>

                                        </select>

                                    </div>

                                </div>

                                <div class="form-group row width-50 individualDiv">

                                    <label class="col-3 control-label">{{trans('lang.car_make')}}</label>

                                    <div class="col-7">

                                        <select name="car_make" class="form-control car_make">

                                            <option value="">{{trans('lang.select')}} {{trans('lang.car_make')}}

                                            </option>

                                        </select>

                                    </div>

                                </div>

                                <div class="form-group row width-50 individualDiv">

                                    <label class="col-3 control-label">{{trans('lang.car_model')}}</label>

                                    <div class="col-7">

                                        <select name="car_model" class="form-control car_model">

                                            <option value="">{{trans('lang.select')}} {{trans('lang.car_model')}}

                                            </option>

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



                                <div class="form-group row width-100 chooseRideType">

                                    <label class="col-3 control-label">{{trans('lang.choose_ride_type')}}</label>

                                    <div class="col-3">

                                        <input type="checkbox" class="col-7 form-check-inline" id="is_ride" checked

                                            disabled>

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




                                <div class="form-group row width-50 individualDiv" {{-- style="display: none" --}}>

                                    <label class="col-3 control-label">{{trans('lang.car_rate')}}</label>

                                    <div class="col-7">

                                        <input type="text" class="form-control car_rate">

                                        <div class="form-text text-muted">{{trans('lang.car_rate_help')}}</div>

                                    </div>

                                </div>





                                <div class="form-group row width-50 individualDiv" {{--style="display: none" --}}>

                                    <label class="col-3 control-label">{{trans('lang.passengers')}}</label>

                                    <div class="col-7">

                                        <input type="text" class="form-control passenger">

                                        <div class="form-text text-muted">{{trans('lang.passengers_help')}}</div>

                                    </div>

                                </div>



                                <div class="form-group row width-50 individualDiv" {{--style="display: none" --}}>

                                    <label class="col-3 control-label">{{trans('lang.doors')}}</label>

                                    <div class="col-7">

                                        <input type="text" class="form-control doors">

                                        <div class="form-text text-muted">{{trans('lang.doors_help')}}</div>

                                    </div>

                                </div>



                                <div class="form-group row width-50 individualDiv" {{-- style="display: none" --}}>

                                    <label class="col-3 control-label">{{trans('lang.air_conditioning')}}</label>

                                    <div class="col-7">

                                        <select name="rental_vehicle_type" class="form-control air_conditioning">

                                            <option value="Yes">{{trans('lang.yes')}}</option>

                                            <option value="No">{{trans('lang.no')}}</option>

                                        </select>



                                    </div>

                                </div>



                                <div class="form-group row width-50 individualDiv" {{-- style="display: none" --}}>

                                    <label class="col-3 control-label">{{trans('lang.gear')}}</label>

                                    <div class="col-7">

                                        <select name="rental_vehicle_type" class="form-control gear">

                                            <option value="Manual">{{trans('lang.manual')}}</option>

                                            <option value="Auto">{{trans('lang.auto')}}</option>

                                        </select>



                                    </div>

                                </div>



                                <div class="form-group row width-50 individualDiv" {{--style="display: none" --}}>

                                    <label class="col-3 control-label">{{trans('lang.mileage')}}</label>

                                    <div class="col-7">

                                        <select name="rental_vehicle_type" class="form-control mileage">

                                            <option value="Average">{{trans('lang.average')}}</option>

                                            <option value="Ultimated">{{trans('lang.ultimated')}}</option>

                                        </select>



                                    </div>

                                </div>



                                <div class="form-group row width-50 individualDiv" {{--style="display: none" --}}>

                                    <label class="col-3 control-label">{{trans('lang.fuel_filling')}}</label>

                                    <div class="col-7">

                                        <select name="rental_vehicle_type" class="form-control fuel_filling">

                                            <option value="Full to Full">{{trans('lang.full_to_full')}}</option>

                                            <option value="Half">{{trans('lang.half')}}</option>

                                        </select>



                                    </div>

                                </div>



                                <div class="form-group row width-50 individualDiv" {{--style="display: none" --}}>

                                    <label class="col-3 control-label">{{trans('lang.fuel_type')}}</label>

                                    <div class="col-7">

                                        <select name="rental_vehicle_type" class="form-control fuel_type">

                                            <option value="Petrol">{{trans('lang.petrol')}}</option>

                                            <option value="Diesel">{{trans('lang.diesel')}}</option>

                                        </select>



                                    </div>

                                </div>



                                <div class="form-group row width-50 individualDiv" {{--style="display: none" --}}>

                                    <label class="col-3 control-label">{{trans('lang.max_power')}}</label>

                                    <div class="col-7">

                                        <input type="text" class="form-control max_power">

                                        <div class="form-text text-muted">{{trans('lang.max_power_help')}}</div>

                                    </div>

                                </div>



                                <div class="form-group row width-50 individualDiv" {{--style="display: none" --}}>

                                    <label class="col-3 control-label">{{trans('lang.mph')}}</label>

                                    <div class="col-7">

                                        <input type="text" class="form-control mph">

                                        <div class="form-text text-muted">{{trans('lang.mph_help')}}</div>

                                    </div>

                                </div>

                                <div class="form-group row width-50 individualDiv" {{-- style="display: none" --}}>

                                    <label class="col-3 control-label">{{trans('lang.top_speed')}}</label>

                                    <div class="col-7">

                                        <input type="text" class="form-control top_speed">

                                        <div class="form-text text-muted">{{trans('lang.top_speed_help')}}</div>

                                    </div>

                                </div>





                                <div class="form-group row width-100 individualDiv" {{-- style="display: none" --}}>

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

                            <div class="form-group row width-100" style="display: none;" id="companyDriverShowDiv">

                                <div class="col-12">

                                    <h6><a href="#">{{ trans("lang.driver_add_by_company_info") }}</a>

                                    </h6>

                                </div>

                            </div>

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

                <button type="button" class="btn btn-primary save-form-btn"><i class="fa fa-save"></i> {{

                    trans('lang.save')}}</button>

                <a href="{!! route('drivers') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{

                    trans('lang.cancel')}}</a>

            </div>

        </div>

    </div>

</div>





@endsection



@section('scripts')



<script type="text/javascript">



    var database = firebase.firestore();

    var geoFirestore = new GeoFirestore(database);

    var createdAt = firebase.firestore.FieldValue.serverTimestamp();



    var photo = "";

    var fileName = '';

    var photocar = "";

    var carfileName = '';

    var photoCarProof = '';

    var carProofFileName = '';

    var photoDriverProof = '';

    var driverProofFileName = '';

    var refCarMake = database.collection('car_make');

    var refCarModel = database.collection('car_model');

    var refVehicleType = database.collection('vehicle_type');

    var refRentalVehicleType = database.collection('rental_vehicle_type');

    var services = database.collection('services').where('flag','in',["rental-service","delivery-service","parcel_delivery","cab-service"]);

    var cab_sections = database.collection('sections').where('serviceTypeFlag', '==', 'cab-service').where('isActive', '==', true);

    var rentalImagesCount = 0;

    var rentalImages = [];

    var rentalImagesFileName = [];



    var getCompanyId = '<?php echo @$_GET['companyId'] ?>';



    $(document).ready(function () {



        jQuery("#data-table_processing").show();

        jQuery("#country_selector").select2({
			templateResult: formatState,
			templateSelection: formatState2,
			placeholder: "Select Country",
			allowClear: true
		});

        refCarMake.get().then(async function (snapshots) {

            snapshots.docs.forEach((listval) => {

                var data = listval.data();



                $('.car_make').append($("<option></option>")

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

            getComapnyDetails();

        });



        cab_sections.get().then(async function (snapshots) {

            snapshots.docs.forEach((listval) => {

                var data = listval.data();

                $('.cab_section_id').append($("<option></option>")

                    .attr("value", data.id)

                    .text(data.name));

            });

        });





        jQuery("#data-table_processing").hide();



    });



    function getComapnyDetails() {



        if (getCompanyId) {



            database.collection('users').where('id', '==', getCompanyId).get().then(async function (snapshots) {

                var data = snapshots.docs[0].data();



                if (data.serviceType == "rental-service") {

                    $('.service_type').val('rental-service').trigger('change');

                    $('.service_type').attr('disabled', true);



                }

                if (data.serviceType == "cab-service") {

                    $('.service_type').val('cab-service').trigger('change');

                    $('.service_type').attr('disabled', true);

                }

            });





        }

    }



    $(".save-form-btn").click(function () {



        var userFirstName = $(".user_first_name").val();

        var userLastName = $(".user_last_name").val();

        var email = $(".user_email").val();

        var password = $(".user_password").val();

        var country_code = '+' + $("#country_selector").val();
        var userPhone = $(".user_phone").val();

        var active = $(".user_active").is(":checked");

        var carName = $(".carmodel").val();

        var car_model = $(".carmodel").val();

        var carNumber = $(".car_number").val();



        var latitude = parseFloat($(".user_latitude").val());

        var longitude = parseFloat($(".user_longitude").val());

        var location = { 'latitude': latitude, 'longitude': longitude };

        var id = "<?php echo uniqid(); ?>";

        var carMakeId = $('.car_make').val();

        var carMakeName = $('.car_make option:selected').text();

        var carModelId = $('.car_model').val();

        var carModelName = $('.car_model').val();



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

        var service_type = $('.service_type').val();



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



        if (service_type == "rental-service") {

            vehicleType = $('.rental_vehicle_type').val();
            vehicleTypeId = $('.rental_vehicle_type option:selected').data('id');

            carName = $(".car_name").val();
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



        } else if (service_type == "cab-service") {





            vehicleType = $('.vehicle_type').val();

            vehicleTypeId = $('.vehicle_type option:selected').data('id');





        }



        if (userFirstName == '') {

            $(".error_top").show();

            $(".error_top").html("");

            $(".error_top").append("<p>{{trans('lang.enter_owners_name_error')}}</p>");

            window.scrollTo(0, 0);



        } else if (email == '') {

            $(".error_top").show();

            $(".error_top").html("");

            $(".error_top").append("<p>{{trans('lang.enter_owners_email')}}</p>");

            window.scrollTo(0, 0);

        }else if(!country_code) {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.select_country_code')}}</p>");
            window.scrollTo(0,0);
        } else if (userPhone == '') {

            $(".error_top").show();

            $(".error_top").html("");

            $(".error_top").append("<p>{{trans('lang.enter_owners_phone')}}</p>");

            window.scrollTo(0, 0);

        } else if(isNaN(latitude)) {

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

        } else if ((vehicleType === '' || vehicleType === undefined || vehicleTypeId === '' || vehicleTypeId === undefined) &&
        (service_type === "rental-service" || service_type === "cab-service"))/* ((vehicleType == '' || vehicleTypeId == '')  && (service_type == "rental-service" || service_type == "cab-service")) */ {

            $(".error_top").show();

            $(".error_top").html("");

            $(".error_top").append("<p>{{trans('lang.vehicle_type_error')}}</p>");

            window.scrollTo(0, 0);

        } else if (service_type == "") {

            $(".error_top").show();

            $(".error_top").html("");

            $(".error_top").append("<p>{{trans('lang.service_type_error')}}</p>");

            window.scrollTo(0, 0);

        }



        else if (carNumber == '') {

            $(".error_top").show();

            $(".error_top").html("");

            $(".error_top").append("<p>{{trans('lang.car_number_error')}}</p>");

            window.scrollTo(0, 0);

        } else if (cabSectionId == '' && service_type == "cab-service") {

            $(".error_top").show();

            $(".error_top").html("");

            $(".error_top").append("<p>{{trans('lang.select_section')}}</p>");

            window.scrollTo(0, 0);

        } else if (carMakeId == '' && service_type == "cab-service") {

            $(".error_top").show();

            $(".error_top").html("");

            $(".error_top").append("<p>{{trans('lang.car_make_error')}}</p>");

            window.scrollTo(0, 0);

        } else if (carModelId == '' && service_type == "cab-service") {

            $(".error_top").show();

            $(".error_top").html("");

            $(".error_top").append("<p>{{trans('lang.car_model_error')}}</p>");

            window.scrollTo(0, 0);

        } else if (carColor == '' && service_type == "cab-service") {

            $(".error_top").show();

            $(".error_top").html("");

            $(".error_top").append("<p>{{trans('lang.car_color_error')}}</p>");

            window.scrollTo(0, 0);



        } else {

            jQuery("#data-table_processing").show();

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



            if (service_type != "cab-service") {

                carMakeId = car_model;

                carColor = carProofPictureURL = driverProofPictureURL = "";

            }





            firebase.auth().createUserWithEmailAndPassword(email, password)

                .then(function (firebaseUser) {

                    id = firebaseUser.user.uid;

                    coordinates = new firebase.firestore.GeoPoint(latitude, longitude);

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

                            geoFirestore.collection('users').doc(id).set({

                                'id': id,

                                'firstName': userFirstName,

                                'lastName': userLastName,

                                'email': email,
                                
                                'phoneNumber': country_code+userPhone,
                
                                'active': active,

                                'profilePictureURL': IMG.profile,

                                'carName': carName,

                                'carNumber': carNumber,

                                'carMakes': carMakeId,

                                'carModelName': carModelName,

                                'vehicleId': vehicleTypeId,

                                'sectionId': cabSectionId,

                                'rideType': rideType,

                                'carColor': carColor,

                                'carProofPictureURL': IMG.photoCarProof,

                                'driverProofPictureURL': IMG.photoDriverProof,

                                'location': location,

                                'carPictureURL': IMG.photoCar,

                                'role': 'driver',

                                'serviceType': service_type,

                                'driverRate': driverRate,

                                'vehicleType': vehicleType,

                                'carRate': carRate,

                                'carInfo': carInfo,

                                'userBankDetails': userBankDetails,

                                'coordinates': coordinates,

                                'createdAt': createdAt

                            }).then(function (result) {

                                window.location.href = '{{ route("drivers")}}';

                            });



                        }).catch(function (error) {



                            $(".error_top").show();

                            $(".error_top").html("");

                            $(".error_top").append("<p>" + error + "</p>");

                            window.scrollTo(0, 0);

                        })

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



    });



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

            $('.car_name').show();

            $('.rental_service').show();

            $('.individualDiv').show();

            $('.cab_service').hide();

            $('#car_model').show();

            $('.car_number_field').show();

        } else if (service_type == "parcel_delivery") {

            $('#car_model').show();

            $('.car_number_field').show();

            $('.cab_service').hide();

            $('.rental_service').hide();

            $('.driverRate').hide();

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

            $('.driverRate').hide();

        }

    });



    $('.car_make').on('change', function () {

        var cab_make_name = $(this).val();

        var options = '<option value="">{{trans("lang.select")}} {{trans("lang.car_model")}}</option>';

        refCarModel.where('car_make_name', '==', cab_make_name).get().then(async function (snapshots) {

            snapshots.docs.forEach((listval) => {

                var data = listval.data();

                options += '<option value="' + data.name + '" data-id="' + data.id + '">' + data.name + '</option>';

            })

            $(".car_model").html(options);

        });

    })



    var storageRef = firebase.storage().ref('images');



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

                $(".user_image").append('<img class="rounded" style="width:50px" src="' + photo + '" alt="image">');



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

                // Generate a location that can't be guessed using the file's contents and a random number

                var hash = CryptoJS.SHA256(Math.random() + CryptoJS.SHA256(filePayload));



                var val = f.name;

                var ext = val.split('.')[1];

                var docName = val.split('fakepath')[1];

                var filename = (f.name).replace(/C:\\fakepath\\/i, '')



                var timestamp = Number(new Date());

                var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;

                photocar = filePayload;

                carfileName = filename;



                $(".car_image").empty();

                $(".car_image").append('<img class="rounded" style="width:50px" src="' + photocar + '" alt="image">');



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

                photoCarProof = filePayload;

                carProofFileName = filename;

                $(".car_proof").empty();

                $(".car_proof").append('<img class="rounded" style="width:50px" src="' + filePayload + '" alt="image">');



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

                photoDriverProof = filePayload;

                driverProofFileName = filename;

                $(".driver_proof").empty();

                $(".driver_proof").append('<img class="rounded" style="width:50px" src="' + filePayload + '" alt="image" >');



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

                photos_html = '<span class="image-item" id="photo_' + rentalImagesCount + '"><span class="remove-btn" data-id="' + rentalImagesCount + '" data-img="' + filePayload + '"><i class="fa fa-remove"></i></span><img width="100px" id="" height="auto" src="' + filePayload + '"></span>';

                $("#photos").append(photos_html);

                rentalImages.push(filePayload);

                rentalImagesFileName.push(filename);

            };

        })(f);

        reader.readAsDataURL(f);

    }



    $(document).on("click", ".remove-btn", function () {

        var id = $(this).attr('data-id');

        var photo_remove = $(this).attr('data-img');

        $("#photo_" + id).remove();

        index = rentalImages.indexOf(photo_remove);

        if (index > -1) {

            rentalImages.splice(index, 1);

        }



    });



    function chkAlphabets3(event, msg) {

        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {

            document.getElementById(msg).innerHTML = "Accept only Number and Dot(.)";

            return false;

        } else {

            document.getElementById(msg).innerHTML = "";

            return true;

        }

    }

    async function storeImageData() {

        var newPhoto = [];

        newPhoto['photoDriverProof'] = '';

        newPhoto['photoCarProof'] = '';

        newPhoto['photoCar'] = '';

        newPhoto['profile'] = '';

        try {

            if (photo != "") {

                photo = photo.replace(/^data:image\/[a-z]+;base64,/, "")

                var uploadTask = await storageRef.child(fileName).putString(photo, 'base64', { contentType: 'image/jpg' });

                var downloadURL = await uploadTask.ref.getDownloadURL();

                newPhoto['profile'] = downloadURL;

                photo = downloadURL;

            }

            if (photocar != "") {

                photocar = photocar.replace(/^data:image\/[a-z]+;base64,/, "")

                var uploadTask = await storageRef.child(carfileName).putString(photocar, 'base64', { contentType: 'image/jpg' });

                var downloadURL = await uploadTask.ref.getDownloadURL();

                newPhoto['photoCar'] = downloadURL;

                photocar = downloadURL;

            }

            if (photoCarProof != "") {

                photoCarProof = photoCarProof.replace(/^data:image\/[a-z]+;base64,/, "")

                var uploadTask = await storageRef.child(carProofFileName).putString(photoCarProof, 'base64', { contentType: 'image/jpg' });

                var downloadURL = await uploadTask.ref.getDownloadURL();

                newPhoto['photoCarProof'] = downloadURL;

                photoCarProof = downloadURL;

            }

            if (photoDriverProof != "") {

                photoDriverProof = photoDriverProof.replace(/^data:image\/[a-z]+;base64,/, "")

                var uploadTask = await storageRef.child(driverProofFileName).putString(photoDriverProof, 'base64', { contentType: 'image/jpg' });

                var downloadURL = await uploadTask.ref.getDownloadURL();

                newPhoto['photoDriverProof'] = downloadURL;

                photoDriverProof = downloadURL;

            }

        } catch (error) {

            console.log("ERR ===", error);

        }

        return newPhoto;

    }

    async function storeVehicleImageData() {

        var newPhoto = [];

        if (rentalImages.length > 0) {

        try{

            await Promise.all(rentalImages.map(async (vehPhoto, index) => {

                vehPhoto = vehPhoto.replace(/^data:image\/[a-z]+;base64,/, "");

                var uploadTask = await storageRef.child(rentalImagesFileName[index]).putString(vehPhoto, 'base64', { contentType: 'image/jpg' });

                var downloadURL = await uploadTask.ref.getDownloadURL();

                newPhoto.push(downloadURL);

            }));

            }catch (error) {

            console.log("ERR ===", error);

        }

        }

        return newPhoto;

    }
    var newcountriesjs = '<?php echo json_encode($newcountriesjs); ?>';
    var newcountriesjs = JSON.parse(newcountriesjs);
    function formatState(state) {
        if (!state.id) {
            return state.text;
        }
        var baseUrl = "<?php echo URL::to('/');?>/scss/icons/flag-icon-css/flags";
        var $state = $(
            '<span><img src="' + baseUrl + '/' + newcountriesjs[state.element.value].toLowerCase() + '.svg" class="img-flag" /> ' + state.text + '</span>'
        );
        return $state;
    }
    function formatState2(state) {
        if (!state.id) {
            return state.text;
        }
        var baseUrl = "<?php echo URL::to('/');?>/scss/icons/flag-icon-css/flags"
        var $state = $(
            '<span><img class="img-flag" /> <span></span></span>'
        );
        $state.find("span").text(state.text);
        $state.find("img").attr("src", baseUrl + "/" + newcountriesjs[state.element.value].toLowerCase() + ".svg");
        return $state;
    }

    function chkAlphabets2(event,msg)
    {
        if(!(event.which>=48  && event.which<=57)
        )
        {
        document.getElementById(msg).innerHTML="Accept only Number";
        return false;
        }
        else
        {
        document.getElementById(msg).innerHTML="";
        return true;
        }
    }

</script>

@endsection

