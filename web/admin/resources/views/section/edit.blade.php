@extends('layouts.app')



@section('content')

<div class="page-wrapper">

    <div class="row page-titles">

        <div class="col-md-5 align-self-center">

            <h3 class="text-themecolor">{{trans('lang.section_plural')}}</h3>

        </div>

        <div class="col-md-7 align-self-center">

            <ol class="breadcrumb">

                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>

                <li class="breadcrumb-item"><a href="{!! route('section') !!}">{{trans('lang.section_plural')}}</a>

                </li>

                <li class="breadcrumb-item active">{{trans('lang.section_edit')}}</li>

            </ol>

        </div>

    </div>





    <div class="card-body">

        <div class="error_top" style="display:none"></div>

        <div class="row vendor_payout_create">

            <div class="vendor_payout_create-inner">

                <fieldset>

                    <legend>{{trans('lang.section_edit')}}</legend>

                    <div class="form-group row width-100">

                        <label class="col-3 control-label">{{trans('lang.section_name')}}</label>

                        <div class="col-7">

                            <input type="text" class="form-control" id="name">

                            <div class="form-text text-muted">{{ trans("lang.section_name_help") }}</div>

                        </div>

                    </div>



                    <div class="form-group row width-100">

                        <label class="col-3 control-label ">{{trans('lang.section_color')}}</label>

                        <div class="col-7">

                            <input type="color" id="color" value="#0000ff">

                            <div class="form-text text-muted">{{ trans("lang.section_color_help") }}</div>

                        </div>

                    </div>



                    <div class="form-group row width-100">

                        <label class="col-3 control-label">{{trans('lang.section_image')}}</label>

                        <div class="col-7">

                            <input type="file" id="sectionImage" onChange="handleFileSelect(event)">

                            <div class="placeholder_img_thumb cat_image"></div>

                            <div id="uploding_image"></div>

                            <div class="form-text text-muted w-50">{{ trans("lang.section_image_help") }}</div>

                        </div>

                    </div>



                    <div class="form-group row width-100">

                        <label class="col-3 control-label ">{{trans('lang.service_type')}}</label>

                        <div class="col-12">

                            <select name="service_type" id="service_type" class="form-control service_type">

                                <option value="">{{trans('lang.select')}} {{trans('lang.service_type')}}</option>



                            </select>

                        </div>

                    </div>



                    <div class="form-group row width-100" id="div_ride_type" style="display: none;">

                        <label class="col-3 control-label" for="user_active">{{trans('lang.choose_ride_type')}}</label>

                        <div class="col-7">

                            <input type="radio" class="form-check-inline" name="ride_type" id="ride" value="ride">

                            <label for="ride">{{trans('lang.ride')}}</label>

                            <input type="radio" class="form-check-inline" name="ride_type" id="intercity"

                                value="intercity">

                            <label for="intercity">{{trans('lang.intercity')}}</label>

                            <input type="radio" class="form-check-inline" name="ride_type" id="both" value="both">

                            <label for="both">{{trans('lang.both')}}</label>

                        </div>

                    </div>



                    <div class="form-group row width-100">



                        <div class="form-check">

                            <input type="checkbox" class="section_active" id="section_active">

                            <label class="col-3 control-label" for="section_active">{{trans('lang.active')}}</label>

                        </div>

                    </div>



                </fieldset>



                <fieldset id="" class="diliverychargeDiv" style="display: none">

                    <legend>{{trans('lang.deliveryCharge')}}</legend>

                    <div class="form-group row width-50">

                        <label class="col-4 control-label">{{ trans('lang.deliveryCharge')}}</label>

                        <div class="col-7">

                            <input type="number" id="deliveryCharge" class="form-control ">

                        </div>

                    </div>

                </fieldset>



                <fieldset>

                    <legend><i class="mr-3 mdi mdi-share"></i>{{trans('lang.referral_settings')}}</legend>

                    <div class="form-group row width-100">

                        <label class="col-4 control-label">{{ trans('lang.referral_amount')}}</label>

                        <div class="col-7">

                            <div class="control-inner">

                                <input type="number" class="form-control" id="referral_amount">

                                <span class="currentCurrency"></span>

                                <div class="form-text text-muted">

                                    {{ trans("lang.referral_amount_help") }}

                                </div>

                            </div>

                        </div>

                    </div>

                </fieldset>



                <fieldset id="food_delivery_set" style="display:none">

                    <legend>{{trans('lang.food_delivery_feature')}}</legend>

                    <div class="form-group row width-100">

                        <div class="form-check">

                            <input type="checkbox" class="section_dine_in_active" id="section_dine_in_active">

                            <label class="col-3 control-label"

                                for="section_dine_in_active">{{trans('lang.dine_in_feature')}}</label>

                            <span style="font-size: 15px;">{{trans('lang.dine_in_feature_note')}}</span>

                        </div>

                    </div>

                    <div class="form-group row width-100">

                        <div class="form-check">

                            <input type="checkbox" class="is_product_details" id="is_product_details">

                            <label class="col-3 control-label"

                                for="is_product_details">{{trans('lang.product_detail_feature')}}</label>

                            <span style="font-size: 15px;">{{trans('lang.product_detail_feature_note')}}</span>

                        </div>

                    </div>

                </fieldset>



                <fieldset id="radios_set" style="display:none">

                    <legend>{{trans('lang.radios_configuration')}}</legend>

                    <div class="form-group row width-100">

                        <label class="col-4 control-label"><span id="stype"></span> {{ trans('lang.nearby_radios')}}</label>

                        <div class="col-7">

                        <div class="control-inner">

                            <input type="number" class="form-control" id="vendor_nearby_radius" min="1">

                            <span id="set_distance_type"></span>

                        </div>

                        </div>

                    </div>

                </fieldset>



                <fieldset class="adminCommisitionDiv">

                    <legend>{{trans('lang.admin_commission')}}</legend>

                    <div class="form-check width-100">

                        <label style="font-size: 15px;">{{trans('lang.admin_commision_note_section')}}</label>

                    </div>

                    <div class="form-check width-100">

                        <input type="checkbox" class="form-check-inline" id="enable_commission">

                        <label class="col-5 control-label" for="enable_commission">{{trans('lang.enable_adminCommission')}}</label>

                    </div>

                    <div class="form-fields" id="show_admin_commision_type_value" style="display:none">

                        <div class="form-group row width-50">

                            <label class="col-4 control-label">{{ trans('lang.commission_type')}}</label>

                            <div class="col-7">

                                <select class="form-control" id="commission_type">

                                    <option value="percentage">{{trans('lang.admin_commission_percentage')}}</option>

                                    <option value="fixed">{{trans('lang.admin_commission_fixed')}}</option>

                                </select>

                            </div>

                        </div>

                        <div class="form-group row width-50">

                            <label class="col-4 control-label">{{ trans('lang.admin_commission_value')}}</label>

                            <div class="col-7">

                                <input type="number" class="form-control" id="commission_value"  min="0">

                            </div>

                        </div>

                    </div>

                </fieldset>



                <fieldset class="htmlTemplateDiv" style="display:none">

                    <legend>{{trans('lang.html_template')}}</legend>

                    <div class="form-group width-100">

                        <textarea class="form-control col-7" name="html_template" id="html_template"></textarea>

                    </div>

                </fieldset>

            </div>

        </div>

    </div>

    <div class="form-group col-12 text-center btm-btn">

        <button type="button" class="btn btn-primary edit-setting-btn"><i class="fa fa-save"></i> {{trans('lang.save')}}

        </button>

        <a href="{!! route('section') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{trans('lang.cancel')}}</a>

    </div>

</div>



@endsection



@section('scripts')



<script type="text/javascript">



    var id = "<?php echo $id;?>";

    var database = firebase.firestore();

    var ref = database.collection('sections').where("id", "==", id);

    

    var services = database.collection('services');

  



    var htmlTemplate = ""

    var sectionImage = "";

    var placeholderImage = '';

    var placeholder = database.collection('settings').doc('placeHolderImage');

    var storageRef = firebase.storage().ref('images');

    var storage = firebase.storage();

    var photo = "";

    var fileName = "";

    var oldImageFile = '';

    placeholder.get().then(async function (snapshotsimage) {

        var placeholderImageData = snapshotsimage.data();

        placeholderImage = placeholderImageData.image;

    })



    var refDriver = database.collection('settings').doc("DriverNearBy");

    refDriver.get().then( async function(snapshots){  

        var radios = snapshots.data();

        if(radios.hasOwnProperty('distanceType')){

            $("#set_distance_type").text(radios.distanceType);

          }

    });



    $('#html_template').summernote({

        height: 400,

        width: 1000,

        toolbar: [

            ['style', ['bold', 'italic', 'underline', 'clear']],

            ['font', ['strikethrough', 'superscript', 'subscript']],

            ['fontsize', ['fontsize']],

            ['color', ['color']],

            ['forecolor', ['forecolor']],

            ['backcolor', ['backcolor']],

            ['para', ['ul', 'ol', 'paragraph']],

            ['height', ['height']],

            ['view', ['fullscreen', 'codeview', 'help']],

        ]

    });



    $(document).ready(function () {

        jQuery("#data-table_processing").show();

        services.get().then(async function (snapshots) {

            snapshots.docs.forEach((listval) => {

                var data = listval.data();

                $('#service_type').append($("<option></option>")

                    .attr("value", data.name).attr("flag", data.flag)

                    .text(data.name));

            })

        })



        ref.get().then(async function (snapshots) {



            if (snapshots.docs) {



                var section = snapshots.docs[0].data();

                $("#name").val(section.name);

                $("#color").val(section.color);

                

                if(section.isActive) {

                    $("#section_active").prop('checked', true);

                }



                if(section.hasOwnProperty('nearByRadius')) {

                    $("#vendor_nearby_radius").val(section.nearByRadius);

                }

                

                if (section.hasOwnProperty('adminCommision') && section.adminCommision != null && section.adminCommision.enable == true) {

                    $("#enable_commission").prop('checked', true);

                    $("#show_admin_commision_type_value").show();

                    $("#commission_type").val(section.adminCommision.type);

                    $("#commission_value").val(section.adminCommision.commission);

                }



                if (section.dine_in_active) {

                    $("#section_dine_in_active").prop('checked', true);

                }



                if (section.is_product_details) {

                    $("#is_product_details").prop('checked', true);

                }



                if (section.serviceType) {

                    

                    $('#service_type').val(section.serviceType).trigger('change');



                    if (section.serviceType == "Cab Service") {



                        $('.diliverychargeDiv').hide();



                        if (section.hasOwnProperty('rideType')) {

                            if (section.rideType == "ride") {

                                $("#ride").prop('checked', true);

                            } else if (section.rideType == "intercity") {

                                $("#intercity").prop('checked', true);

                            } else if (section.rideType == "both") {

                                $("#both").prop('checked', true);

                            }

                        }



                        if (section.hasOwnProperty('cab_service_template')) {

                            $('#html_template').summernote("code", section.cab_service_template);

                        }

                    }

                    

                    if (section.serviceType == "Ecommerce Service") {

                        $('.diliverychargeDiv').show();

                        if (section.delivery_charge != '') {

                            $('#deliveryCharge').val(section.delivery_charge);

                        }

                    }

                }



                $("#referral_amount").val(section.referralAmount);



                sectionImage = section.sectionImage;



                if (sectionImage != '' && sectionImage != null) {

                    photo = sectionImage;

                    oldImageFile = sectionImage;

                    if(sectionImage){

                        photo=sectionImage;

                    }else{

                        photo=placeholderImage;

                    }

                    $(".cat_image").append('<span class="image-item"><span class="remove-btn"><i class="fa fa-remove"></i></span><img class="rounded" style="width:50px" src="' + photo + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" alt="image"></span>');

                } else {

                    $(".cat_image").append('<span class="image-item"><span class="remove-btn"><i class="fa fa-remove"></i></span><img class="rounded" style="width:50px" src="' + placeholderImage + '" alt="image"></span>');

                }

            }

        })

        jQuery("#data-table_processing").hide();



        $(".edit-setting-btn").click(function () {



            var name = $("#name").val();

            var color = $("#color").val();

            var active = $("#section_active").is(":checked");

            var section_dine_in_active = $("#section_dine_in_active").is(":checked");

            var is_product_details = $("#is_product_details").is(":checked");

            var service_type = $('#service_type').val();

            var service_type_flag = $('#service_type option:selected').attr('flag');

            var referralAmount = $("#referral_amount").val();

            var enable_commission = $("#enable_commission").is(":checked");

            var commission_type = $("#commission_type").val();

            var commission_value = parseInt($("#commission_value").val());

            var vendor_nearby_radius = parseInt($("#vendor_nearby_radius").val());

            

            if (service_type == "Ecommerce Service") {

                var delivery_charge = $('#deliveryCharge').val();

            } else {

                var delivery_charge = '';

            }



            if (service_type == "Cab Service") {

                var htmlTemplate = $('#html_template').summernote('code');

                var rideType = $('input[name="ride_type"]:checked').val();

            } else {

                var htmlTemplate = '';

                var rideType = '';

            }



            if (enable_commission == true) {

                var adminCommision = {

                    'commission' : commission_value,

                    'enable' : true,

                    'type' : commission_type,

                };

            } else {

                var adminCommision = {

                    'commission' : 0,

                    'enable' : false,

                    'type' : null,

                };

            }



            if (name == '') {

                $(".error_top").show();

                $(".error_top").html("");

                $(".error_top").append("<p>{{trans('lang.enter_cat_name_error')}}</p>");

                window.scrollTo(0, 0);

            } else if (service_type == "") {

                $(".error_top").show();

                $(".error_top").html("");

                $(".error_top").append("<p>{{trans('lang.service_type_error')}}</p>");

                window.scrollTo(0, 0);

            } else if (referralAmount == '') {

                $(".error_top").show();

                $(".error_top").html("");

                $(".error_top").append("<p>{{trans('lang.enter_referral_amount_error')}}</p>");

                window.scrollTo(0, 0);

                window.scrollTo(0, 0);

            } else if (enable_commission == true && (isNaN(commission_value) || commission_value <= 0)) {

                $(".error_top").show();

                $(".error_top").html("");

                $(".error_top").append("<p>{{trans('lang.commission_fix_error')}}</p>");

                window.scrollTo(0, 0);

            } else if (isNaN(vendor_nearby_radius)) {

                $(".error_top").show();

                $(".error_top").html("");

                $(".error_top").append("<p>{{trans('lang.enter_vendor_nearby_error')}}</p>");

                window.scrollTo(0, 0);

            } else {

                jQuery("#data-table_processing").show();



                storeImageData().then(IMG => {



                    database.collection('sections').doc(id).update({

                        'name': name,

                        'color': color,

                        'sectionImage': IMG,

                        'isActive': active,

                        'dine_in_active': section_dine_in_active,

                        'is_product_details': is_product_details,

                        'rideType': rideType,

                        'serviceType': service_type,

                        'serviceTypeFlag': service_type_flag,

                        'delivery_charge': delivery_charge,

                        'cab_service_template': htmlTemplate,

                        'referralAmount': referralAmount,

                        'adminCommision': adminCommision,

                        'nearByRadius': vendor_nearby_radius,



                    }).then(async function (result) {
                      
                        window.location.href = '{{ route("section")}}';

                    })

                }).catch(err => {

                     jQuery("#data-table_processing").hide();

                    $(".error_top").show();

                    $(".error_top").html("");

                    $(".error_top").append("<p>" + err + "</p>");

                    window.scrollTo(0, 0);

                });

            }

        });



    });



    function handleFileSelect(evt) {

        var f = evt.target.files[0];

        var reader = new FileReader();

        reader.onload = (function (theFile) {

            return function (e) {



                var filePayload = e.target.result;

                var hash = CryptoJS.SHA256(Math.random() + CryptoJS.SHA256(filePayload));

                var val = $('#sectionImage').val().toLowerCase();

                var ext = val.split('.')[1];

                var docName = val.split('fakepath')[1];

                var filename = $('#sectionImage').val().replace(/C:\\fakepath\\/i, '')

                var timestamp = Number(new Date());

                var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;

                photo = filePayload;

                fileName = filename;

                $(".cat_image").empty();

                if(filePayload){

                    photo1=filePayload;

                }else{

                    photo1=placeholderImage;

                }

                $(".cat_image").append('<span class="image-item" id="photo_user"><span class="remove-btn" data-id="user-remove" data-img="' + photo + '"><i class="fa fa-remove"></i></span><img class="rounded" style="width:50px" src="' + photo1 + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" alt="image"></span>');

            };

        })(f);

        reader.readAsDataURL(f);

    }

    async function storeImageData() {

        var newPhoto = '';

        try {

            if (oldImageFile != "" && photo != oldImageFile) {

                var oldImageUrl = await storage.refFromURL(oldImageFile);

                imageBucket = oldImageUrl.bucket;

                var envBucket = "<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";



                if (imageBucket == envBucket) {

                    await oldImageUrl.delete().then(() => {

                        console.log("Old file deleted!")

                    }).catch((error) => {

                        console.log("ERR File delete ===", error);

                    });

                } else {

                    console.log('Bucket not matched');

                }

            }

            if (photo != oldImageFile) {

                photo = photo.replace(/^data:image\/[a-z]+;base64,/, "")

                var uploadTask = await storageRef.child(fileName).putString(photo, 'base64', { contentType: 'image/jpg' });

                var downloadURL = await uploadTask.ref.getDownloadURL();

                newPhoto = downloadURL;

                photo = downloadURL;



            } else {

                newPhoto = photo;

            }

        } catch (error) {

            console.log("ERR ===", error);

        }

        return newPhoto;

    }



    $(document).on("click", ".remove-btn", function () {

        $(".image-item").remove();

        $('#sectionImage').val('');

    });



    $('#enable_commission').click(function () {

        var checkboxValue = $(this).is(":checked");

        if (checkboxValue) {

            $("#show_admin_commision_type_value").show();

        } else {

            $("#show_admin_commision_type_value").hide();



        }

    });



    $('.service_type').change(function () {

        var serviceType = $(this).val();



        if (serviceType == "Cab Service") {

            $('.diliverychargeDiv').hide();

            $('.htmlTemplateDiv').show();

            $('#div_ride_type').show();

            $('#food_delivery_set').hide();

            $('#radios_set').hide();

        } else if (serviceType == "Parcel Delivery Service") {

            $('.htmlTemplateDiv').hide();

            $('#div_ride_type').hide();

            $('#food_delivery_set').hide();

            $('#radios_set').hide();

        } else if (serviceType == "Ecommerce Service") {

            $('.diliverychargeDiv').show();

            $('.htmlTemplateDiv').hide();

            $('#div_ride_type').hide();

            $('#food_delivery_set').hide();

            $('#radios_set').show();

            $('#stype').text("{{trans('lang.store')}}");

        } else if (serviceType == "Rental Service") {

            $('.diliverychargeDiv').hide();

            $('.htmlTemplateDiv').hide();

            $('#div_ride_type').hide();

            $('#food_delivery_set').hide();

            $('#radios_set').hide();

        } else if (serviceType == "On Demand Service") {

            $('.diliverychargeDiv').hide();

            $('.htmlTemplateDiv').hide();

            $('#div_ride_type').hide();

            $('#food_delivery_set').hide();

            $('#radios_set').show();

            $('#stype').text("{{trans('lang.provider_services')}}");

        } else if (serviceType == "Multivendor Delivery Service") {

            $('.diliverychargeDiv').hide();

            $('.htmlTemplateDiv').hide();

            $('#div_ride_type').hide();

            $('#food_delivery_set').show();

            $('#radios_set').show();

            $('#stype').text("{{trans('lang.store')}}");

        } else {

            $('.diliverychargeDiv').hide();

            $('.htmlTemplateDiv').hide();

            $('#div_ride_type').hide();

            $('#food_delivery_set').hide();

            $('#radios_set').hide();

        } 

    })



</script>

@endsection