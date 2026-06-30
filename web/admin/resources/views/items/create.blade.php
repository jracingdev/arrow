@extends('layouts.app')



@section('content')

    <div class="page-wrapper">

        <div class="row page-titles">


            <div class="col-md-5 align-self-center"> 

                <?php if ($id != '') { ?>

                <h3 class="text-themecolor vendor_name_heading"></h3>

                <?php } else { ?>

                <h3 class="text-themecolor">{{trans('lang.item_plural')}}</h3>

                <?php } ?>

            </div>

            <div class="col-md-7 align-self-center">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="{!! route('dashboard') !!}">{{trans('lang.dashboard')}}</a>

                    </li>


                    <?php if ($id != '') { ?>

                    <li class="breadcrumb-item"><a
                            href="{{route('vendors.items',$id)}}">{{trans('lang.item_plural')}}</a>

                    </li>

                    <?php } else { ?>

                    <li class="breadcrumb-item"><a href="{!! route('items') !!}">{{trans('lang.item_plural')}}</a></li>

                    <?php } ?>

                    <li class="breadcrumb-item active">{{trans('lang.item_create')}}</li>

                </ol>

            </div>

        </div>


        <div>


            <div class="card-body">


                <div class="error_top" style="display:none"></div>


                <div class="row vendor_payout_create">

                    <div class="vendor_payout_create-inner">


                        <fieldset>

                            <legend>{{trans('lang.item_information')}}</legend>

                            <div class="form-group row width-100" id="admin_commision_info">
                                <div class="m-3">
                                    <div class="form-text font-weight-bold text-danger h6">
                                        {{trans('lang.price_instruction')}}</div>
                                    <div class="form-text font-weight-bold text-danger h6" id="admin_commision"></div>
                                </div>
                            </div>

                            <div class="form-group row width-50">

                                <label class="col-3 control-label">{{trans('lang.item_name')}}</label>

                                <div class="col-7">

                                    <input type="text" class="form-control item_name" required>

                                    <div class="form-text text-muted">

                                        {{ trans("lang.item_name_help") }}

                                    </div>

                                </div>

                            </div>


                            <div class="form-group row width-50">

                                <label class="col-3 control-label">{{trans('lang.item_price')}}</label>

                                <div class="col-7">

                                    <input type="number" class="form-control item_price" required>

                                    <div class="form-text text-muted">

                                        {{ trans("lang.item_price_help") }}

                                    </div>

                                </div>

                            </div>


                            <div class="form-group row width-50">

                                <label class="col-3 control-label">{{trans('lang.item_discount')}}</label>

                                <div class="col-7">

                                    <input type="number" class="form-control item_discount">

                                    <div class="form-text text-muted">

                                        {{ trans("lang.item_discount_help") }}

                                    </div>

                                </div>

                            </div>

                            <?php if ($id == '') { ?>

                            <div class="form-group row width-50">

                                <label class="col-3 control-label">{{trans('lang.item_vendor_id')}}</label>

                                <div class="col-7">

                                    <select id="item_vendor" class="form-control" required>

                                        <option value="">{{trans('lang.select_vendor')}}</option>

                                    </select>

                                    <div class="form-text text-muted">

                                        {{ trans("lang.item_vendor_id_help") }}

                                    </div>

                                </div>

                            </div>

                            <?php } ?>


                            <div class="form-check row width-50 mb-3" id="is_digital_div" style="display: none;">

                                <input type="checkbox" class="is_digital_product" id="is_digital_product">

                                <label class="col-3 control-label"

                                       for="item_publish">{{trans('lang.item_is_digital')}}</label>

                            </div>


                            <div class="form-group row width-50" id="upload_file_div" style="display: none;">

                                <label class="col-3 control-label">{{trans('lang.item_upload_file')}}</label>

                                <div class="col-7">

                                    <input type="file" onChange="handleZipUpload(event)" id="digital_product_file">

                                    <div id="uploding_zip" class="placeholder_img_thumb mt-2"></div>

                                    <div class="form-text text-muted max_file_size"></div>

                                    <div class="form-text text-muted">{{ trans("lang.item_upload_file_ext") }}</div>

                                </div>

                            </div>


                            <div class="form-group row width-100">

                                <label class="col-3 control-label">{{trans('lang.item_category_id')}}</label>

                                <div class="col-7">

                                    <select id='item_category' class="form-control" required>

                                        <option value="">{{trans('lang.select_category')}}</option>

                                    </select>

                                    <div class="form-text text-muted">

                                        {{ trans("lang.item_category_id_help") }}

                                    </div>

                                </div>


                            </div>


                            <div class="form-group row width-50">

                                <label class="col-3 control-label">{{trans('lang.item_quantity')}}</label>

                                <div class="col-7">

                                    <input type="number" class="form-control item_quantity" value="-1">

                                    <div class="form-text text-muted">

                                        {{ trans("lang.item_quantity_help") }}

                                    </div>

                                </div>

                            </div>


                            <div class="form-group row width-50 brandDiv" style="display: none;">

                                <label class="col-3 control-label">{{trans('lang.brand')}}</label>

                                <div class="col-7">

                                    <select id='brand' class="form-control" required>

                                        <option value="">{{trans('lang.select_brand')}}</option>

                                    </select>

                                    <div class="form-text text-muted">

                                        {{ trans("lang.brand_help") }}

                                    </div>

                                </div>

                            </div>


                            <div class="form-group row width-100" id="attributes_div" style="display:none">

                                <label class="col-3 control-label">{{trans('lang.item_attribute_id')}}</label>

                                <div class="col-7">

                                    <select id='item_attribute' class="form-control chosen-select" required

                                            multiple="multiple" style="display: none;"
                                            onchange="selectAttribute();"></select>

                                </div>

                            </div>


                            <div class="form-group row width-100">

                                <div class="item_attributes" id="item_attributes"></div>

                                <div class="item_variants" id="item_variants"></div>

                                <input type="hidden" id="attributes" value=""/>

                                <input type="hidden" id="variants" value=""/>

                            </div>


                            <div class="form-group row width-100">

                                <label class="col-3 control-label">{{trans('lang.item_image')}}</label>

                                <div class="col-7">

                                    <input type="file" id="product_image">

                                    <div class="placeholder_img_thumb product_image"></div>

                                    <div id="uploding_image"></div>

                                    <div class="form-text text-muted">

                                        {{ trans("lang.item_image_help") }}

                                    </div>

                                </div>

                            </div>


                            <div class="form-group row width-100">

                                <label class="col-3 control-label">{{trans('lang.item_description')}}</label>

                                <div class="col-7">

                                <textarea rows="8" class="form-control item_description"

                                          id="item_description"></textarea>

                                </div>

                            </div>

                            <div class="form-check width-100">

                                <input type="checkbox" class="item_publish" id="item_publish">

                                <label class="col-3 control-label"
                                       for="item_publish">{{trans('lang.item_publish')}}</label>

                            </div>


                            <div class="form-check width-100 food_delivery_div d-none">

                                <input type="checkbox" class="item_nonveg" id="item_nonveg">

                                <label class="col-3 control-label" for="item_nonveg">{{ trans('lang.non_veg')}}</label>

                            </div>


                            <div class="form-check width-100 food_delivery_take_away d-none">

                                <input type="checkbox" class="item_take_away_option" id="item_take_away_option">

                                <label class="col-3 control-label"

                                       for="item_take_away_option">{{trans('lang.item_take_away')}}</label>

                            </div>


                        </fieldset>


                        <fieldset class="food_delivery_div d-none">


                            <legend>{{trans('lang.ingredients')}}</legend>


                            <div class="form-group row width-50">

                                <label class="col-3 control-label">{{trans('lang.calories')}}</label>

                                <div class="col-7">

                                    <input type="number" class="form-control item_calories">

                                </div>

                            </div>


                            <div class="form-group row width-50">

                                <label class="col-3 control-label">{{trans('lang.grams')}}</label>

                                <div class="col-7">

                                    <input type="number" class="form-control item_grams">

                                </div>

                            </div>


                            <div class="form-group row width-50">

                                <label class="col-3 control-label">{{trans('lang.fats')}}</label>

                                <div class="col-7">

                                    <input type="number" class="form-control item_fats">

                                </div>

                            </div>


                            <div class="form-group row width-50">

                                <label class="col-3 control-label">{{trans('lang.proteins')}}</label>

                                <div class="col-7">

                                    <input type="number" class="form-control item_proteins">

                                </div>

                            </div>


                        </fieldset>


                        <fieldset>

                            <legend>{{trans('lang.item_add_one')}}</legend>


                            <div class="form-group add_ons_list extra-row">

                            </div>


                            <div class="form-group row width-100">

                                <div class="col-7">

                                    <button type="button" onclick="addOneFunction()" class="btn btn-primary"

                                            id="add_one_btn">{{trans('lang.item_add_one')}}

                                    </button>

                                </div>

                            </div>


                            <div class="form-group row width-100" id="add_ones_div" style="display:none">

                                <div class="row">

                                    <div class="col-6">

                                        <label class="col-3 control-label">{{trans('lang.item_title')}}</label>

                                        <div class="col-7">

                                            <input type="text" class="form-control add_ons_title">

                                        </div>

                                    </div>

                                    <div class="col-6">

                                        <label class="col-3 control-label">{{trans('lang.item_price')}}</label>

                                        <div class="col-7">

                                            <input type="number" class="form-control add_ons_price">

                                        </div>

                                    </div>

                                </div>

                            </div>


                            <div class="form-group row save_add_one_btn width-100" style="display:none">

                                <div class="col-7">

                                    <button type="button" onclick="saveAddOneFunction()"

                                            class="btn btn-primary">{{trans('lang.save_add_ones')}}

                                    </button>

                                </div>

                            </div>


                        </fieldset>

                        <fieldset>


                            <legend>{{trans('lang.product_specification')}}</legend>


                            <div class="form-group product_specification extra-row">

                            </div>


                            <div class="form-group row width-100">

                                <div class="col-7">

                                    <button type="button" onclick="addProductSpecificationFunction()"

                                            class="btn btn-primary" id="add_one_btn">

                                        {{trans('lang.add_product_specification')}}

                                    </button>

                                </div>

                            </div>

                            <div class="form-group row width-100" id="add_product_specification_div"
                                 style="display:none">

                                <div class="row">

                                    <div class="col-6">

                                        <label class="col-2 control-label">{{trans('lang.lable')}}</label>

                                        <div class="col-7">

                                            <input type="text" class="form-control add_label">

                                        </div>

                                    </div>

                                    <div class="col-6">

                                        <label class="col-3 control-label">{{trans('lang.value')}}</label>

                                        <div class="col-7">

                                            <input type="text" class="form-control add_value">

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="form-group row save_product_specification_btn width-100" style="display:none">

                                <div class="col-7">

                                    <button type="button" onclick="saveProductSpecificationFunction()"

                                            class="btn btn-primary">{{trans('lang.save_product_specification')}}

                                    </button>

                                </div>

                            </div>


                        </fieldset>


                    </div>

                </div>


                <div class="form-group col-12 text-center btm-btn">

                    <button type="button" class="btn btn-primary  save-form-btn"><i class="fa fa-save"></i>

                        {{trans('lang.save')}}

                    </button>

                    <?php if ($id != '') { ?>

                    <a href="{{route('vendors.items',$id)}}" class="btn btn-default"><i

                            class="fa fa-undo"></i>{{trans('lang.cancel')}}</a>

                    <?php } else { ?>

                    <a href="{!! route('items') !!}" class="btn btn-default"><i

                            class="fa fa-undo"></i>{{trans('lang.cancel')}}</a>

                    <?php } ?>


                </div>

            </div>

        </div>

    </div>





@endsection



@section('scripts')



    <script type="text/javascript">


        var database = firebase.firestore();


        var photo = "";

        var digital_product_file = '';

        var digital_product_file_name = '';

        var digital_product_ext = '';

        var addOnesTitle = [];

        var addOnesPrice = [];

        var categories_list = [];

        var brand_list = [];

        var attributes_list = [];

        var vendor_list = [];

        var product_specification = {};

        var photos = [];

        var product_image_filename = [];

        var variant_photos = [];

        var variant_filename = [];

        var variant_vIds = [];

        var productImagesCount = 0;

        var allowed_file_size = '';

        var vendor_section_id = '';

        $('#item_vendor').select2();

        $('#item_category').select2();

        var sections_list = [];

        var itemLimit = '-1';
        var createdItem = 0;
        var subscriptionModel = false;
        var commissionModel = false;
        var vendorId = '';
        var sectionId = '';
        var userId = '';
        var section_id = getCookie('section_id');
    
    
        var ref_sections = database.collection('sections');
       

        ref_sections.get().then(async function (snapshots) {

            await snapshots.docs.forEach((listval) => {

                var data = listval.data();

                sections_list.push(data);

            });

            if (getCookie('section_id') != "") {

                var result = sections_list.find(function (e) {
                    return e.id === getCookie('section_id');
                });

                if (result && result.serviceTypeFlag == "delivery-service") {
                    $('.food_delivery_take_away').removeClass('d-none');

                    if (result && result.is_product_details) {
                        $('.food_delivery_div').removeClass('d-none');
                    }
                }
            }

        });


        var subscriptionBusinessModel = database.collection('settings').doc("vendor");
        subscriptionBusinessModel.get().then(async function(snapshots) {
            var subscriptionSetting = snapshots.data();
            if (subscriptionSetting.subscription_model == true) {
                subscriptionModel = true;
            }
        });

        var reataurantIDDirec = "<?php echo $id; ?>";        

        var sectionn_id = '';
        $(document).ready(function () {


            jQuery(document).on("click", ".mdi-cloud-upload", function () {

                var variant = jQuery(this).data('variant');

                var photo_remove = $(this).attr('data-img');

                index = variant_photos.indexOf(photo_remove);

                if (index > -1) {

                    variant_photos.splice(index, 1); // 2nd parameter means remove one item only

                }

                var file_remove = $(this).attr('data-file');

                fileindex = variant_filename.indexOf(file_remove);

                if (fileindex > -1) {

                    variant_filename.splice(fileindex, 1); // 2nd parameter means remove one item only

                }

                variantindex = variant_vIds.indexOf(variant);

                if (variantindex > -1) {

                    variant_vIds.splice(variantindex, 1); // 2nd parameter means remove one item only

                }


                $('[id="file_' + variant + '"]').click();

            });


            jQuery(document).on("click", ".mdi-delete", function () {

                var variant = jQuery(this).data('variant');

                $('[id="variant_' + variant + '_image"]').empty();

                var photo_remove = $(this).attr('data-img');

                index = variant_photos.indexOf(photo_remove);

                if (index > -1) {

                    variant_photos.splice(index, 1); // 2nd parameter means remove one item only

                }

                var file_remove = $(this).attr('data-file');

                fileindex = variant_filename.indexOf(file_remove);

                if (fileindex > -1) {

                    variant_filename.splice(fileindex, 1); // 2nd parameter means remove one item only

                }

                variantindex = variant_vIds.indexOf(variant);

                if (variantindex > -1) {

                    variant_vIds.splice(variantindex, 1); // 2nd parameter means remove one item only

                }

            });


            jQuery(document).on("click", "#is_digital_product", function () {

                var selected_section = $('#item_vendor').find('option:selected').attr('data-section-id');

                if (selected_section) {

                    selected_section = selected_section;

                } else {

                    selected_section = vendor_section_id;

                }

                var section_info = $.map(sections_list, function (section, i) {

                    if (section.id == selected_section) {

                        return section;

                    }

                });

                if (jQuery(this).is(':checked') && section_info.length > 0 && (section_info[0].serviceTypeFlag == "ecommerce-service")) {

                    $("#upload_file_div").show();

                } else {

                    $("#upload_file_div").hide();

                }

            });


            jQuery("#data-table_processing").show();


            database.collection('vendors').orderBy('title').where('title', '!=', '').get().then(async function (snapshots) {


                snapshots.docs.forEach((listval) => {

                    var data = listval.data();

                    vendor_list.push(data);
                    
                    $('#item_vendor').append($("<option></option>")

                        .attr("value", data.id)

                        .attr("data-section-id", data.section_id)
                        .attr("data-user-id", data.author)
                        .text(data.title));

                    if (reataurantIDDirec == data.id) {

                        vendor_section_id = data.section_id;
                        localStorage.setItem('vendor_section_id', vendor_section_id);
                        $(".vendor_name_heading").html(data.title);


                        var section_info = $.map(sections_list, function (section, i) {

                            if (section.id == data.section_id) {

                                return section;

                            }

                        });

                        if (section_info.length > 0 && (section_info[0].serviceTypeFlag == "ecommerce-service" || section_info[0].serviceTypeFlag == "delivery-service")) {

                            $("#attributes_div").show();

                            $("#item_attribute_chosen").css({'width': '100%'});

                        }


                        if (section_info.length > 0 && (section_info[0].serviceTypeFlag == "ecommerce-service")) {

                            $("#is_digital_div").show();

                        }

                    }

                })


            });

            if (getCookie('section_id') != "") {
                var vendors = database.collection('vendor_categories').where('section_id', '==', getCookie('section_id'));

                var brand = database.collection('brands').where('sectionId', '==', getCookie('section_id'));

            } else {

                var vendors = database.collection('vendor_categories');

                var brand = database.collection('brands');


            }


            vendors.where('publish', '==', true).get().then(async function (snapshots) {


                snapshots.docs.forEach((listval) => {

                    var data = listval.data();
                  
                    categories_list.push(data);

                    var section_id = "";

                    if (data.section_id) {

                        section_id = data.section_id;

                    }
                    if(localStorage.getItem('vendor_section_id') == data.section_id) {
                        $('#item_category').append($("<option></option>")

                            .attr("value", data.id)

                            .attr("section_id", section_id)

                            .text(data.title));
                    }


                })

            });


            brand.get().then(async function (snapshots) {

                snapshots.docs.forEach((listval) => {

                    var data = listval.data();

                    brand_list.push(data);

                    $('#brand').append($("<option></option>")

                        .attr("value", data.id)

                        .text(data.title));

                })

            });


            var digitalProductRef = database.collection('settings').doc("digitalProduct");

            digitalProductRef.get().then(async function (snapshots) {

                var digitalProductData = snapshots.data();

                allowed_file_size = digitalProductData.fileSize;

                $(".max_file_size").text('{{ trans("lang.item_upload_file_max") }}' + allowed_file_size + 'Mb');

            })


            var attributes = database.collection('vendor_attributes');


            attributes.get().then(async function (snapshots) {

                snapshots.docs.forEach((listval) => {

                    var data = listval.data();

                    attributes_list.push(data);

                    $('#item_attribute').append($("<option></option>")

                        .attr("value", data.id)

                        .text(data.title));

                })

                $("#item_attribute").show().chosen({"placeholder_text": "{{trans('lang.select_attribute')}}"});

            });


            jQuery("#data-table_processing").hide();


            $(".save-form-btn").click(async function () {
                var vendor = '';
              <?php if ($id == '') { ?>

                vendor = $("#item_vendor option:selected").val();

                    <?php } else { ?>

                    vendor = "<?php echo $id;?>";
                        
                    <?php } ?>
                   
                    await database.collection('vendors').where('id', '==', vendor).get().then(async function (snapshots) {

                        var data = snapshots.docs[0].data();
                        sectionn_id = data.section_id;
                        userId = data.author;
                    });

                    database.collection('vendor_products').where('vendorID','==',vendor).get().then(async function(snapshot) {
                        createdItem=snapshot.docs.length;
                    })
                
                var commissionBusinessModel=database.collection('sections').where('id','==', sectionn_id);
                await commissionBusinessModel.get().then(async function(querySnapshots) {
                    var commissionSetting=querySnapshots.docs[0].data();                        
                    if(commissionSetting.adminCommision.enable==true) {
                        commisionModel=true;
                    }
                })                
                
                await database.collection('users').where('id', '==', userId).get().then(async function(snapshots) {
                    var data = snapshots.docs[0].data();
                    if (subscriptionModel || commisionModel) {
                        if (data.hasOwnProperty('subscription_plan') && data.subscription_plan != null && data
                            .subscription_plan != '') {
                            itemLimit = data.subscription_plan.itemLimit;
                        }
                    }

                });
               
                if (parseInt(itemLimit) == -1 || parseInt(createdItem) < parseInt(itemLimit)) {
                    var name = $(".item_name").val();

                    var price = $(".item_price").val();

                    var item_quantity = $(".item_quantity").val();

                        <?php if ($id == '') { ?>

                    var vendor = $("#item_vendor option:selected").val();

                        <?php } else { ?>

                    var vendor = "<?php echo $id; ?>";

                        <?php } ?>

                    var category = $("#item_category").val();

                    var section_id = $('#item_category').find('option:selected').attr('section_id');

                    var brand = $("#brand").val();

                    var itemCalories = parseInt($(".item_calories").val());

                    var itemGrams = parseInt($(".item_grams").val());

                    var itemProteins = parseInt($(".item_proteins").val());

                    var itemFats = parseInt($(".item_fats").val());

                    var description = $("#item_description").val();

                    var itemPublish = $(".item_publish").is(":checked");

                    var nonveg = $(".item_nonveg").is(":checked");


                    var veg = !nonveg;

                    var itemTakeaway = $(".item_take_away_option").is(":checked");

                    var discount = $(".item_discount").val();

                    var is_digital_product = $("#is_digital_product").is(":checked");


                    if (discount == '') {

                        discount = "0";

                    }

                    if (!itemCalories) {

                        itemCalories = 0;

                    }

                    if (!itemGrams) {

                        itemGrams = 0;

                    }

                    if (!itemFats) {

                        itemFats = 0;

                    }

                    if (!itemProteins) {

                        itemProteins = 0;

                    }

                    if (photos != '') {

                        photo = photos[0]

                    }


                    var id = "<?php echo uniqid(); ?>";


                    if (name == '') {

                        $(".error_top").show();

                        $(".error_top").html("");

                        $(".error_top").append("<p>{{trans('lang.enter_item_name_error')}}</p>");

                        window.scrollTo(0, 0);

                    } else if (price == '') {

                        $(".error_top").show();

                        $(".error_top").html("");

                        $(".error_top").append("<p>{{trans('lang.enter_item_price_error')}}</p>");

                        window.scrollTo(0, 0);

                    }else if (price <= 0) {

                        $(".error_top").show();

                        $(".error_top").html("");

                        $(".error_top").append("<p>{{trans('lang.enter_positive_price_error')}}</p>");

                        window.scrollTo(0, 0);

                    } else if (item_quantity == '' || item_quantity < -1) {

                        $(".error_top").show();

                        $(".error_top").html("");

                        if (item_quantity == '') {

                            $(".error_top").append("<p>{{trans('lang.enter_item_quantity_error')}}</p>");

                        } else {

                            $(".error_top").append("<p>{{trans('lang.invalid_item_quantity_error')}}</p>");

                        }

                        window.scrollTo(0, 0);

                    } else if (vendor == '') {

                        $(".error_top").show();

                        $(".error_top").html("");

                        $(".error_top").append("<p>{{trans('lang.select_vendor_error')}}</p>");

                        window.scrollTo(0, 0);

                    } else if (category == '') {

                        $(".error_top").show();

                        $(".error_top").html("");

                        $(".error_top").append("<p>{{trans('lang.select_item_category_error')}}</p>");

                        window.scrollTo(0, 0);

                    } else if (brand == '' && $('.brandDiv').is(':visible') == true) {

                        $(".error_top").show();

                        $(".error_top").html("");

                        $(".error_top").append("<p>{{trans('lang.select_brand_error')}}</p>");

                        window.scrollTo(0, 0);

                    } else if (parseInt(price) < parseInt(discount)) {

                        $(".error_top").show();

                        $(".error_top").html("");

                        $(".error_top").append("<p>{{trans('lang.price_should_not_less_then_discount_error')}}</p>");

                        window.scrollTo(0, 0);


                    } else if (description == '') {

                        $(".error_top").show();

                        $(".error_top").html("");

                        $(".error_top").append("<p>{{trans('lang.enter_item_description_error')}}</p>");

                        window.scrollTo(0, 0);

                    } else if (is_digital_product == true && digital_product_file == '') {

                        $(".error_top").show();

                        $(".error_top").html("");

                        $(".error_top").append("<p>{{trans('lang.upload_digital_file_error')}}</p>");

                        window.scrollTo(0, 0);

                    } else {


                        $(".error_top").hide();


                        var attributes = [];

                        var variants = [];
                        var quantityerror=0;
                        var priceerror=0;

                        if($("#item_attribute").val().length>0) {
                            if($('#attributes').val().length>0) {
                                var attributes=$.parseJSON($('#attributes').val());
                            } else {
                                alert('Please add your attribute value');
                                return false; 
                            }
                            if($("#item_attribute").val().length!==attributes.length) {
                                alert('Please add your attribute value');
                                return false;
                            }
                        }

                        if ($('#variants').val().length > 0) {

                            var variantsSet = $.parseJSON($('#variants').val());

                            await storeVariantImageData().then(async (vIMG) => {


                                $.each(variantsSet, function (key, variant) {

                                    var variant_id = uniqid();

                                    var variant_sku = variant;

                                    var variant_price = $('#price_' + variant).val();

                                    var variant_quantity = $('#qty_' + variant).val();

                                    var variant_image = $('#variant_' + variant + '_url').val();

                                    if (variant_image) {

                                        variants.push({

                                            'variant_id': variant_id,

                                            'variant_sku': variant_sku,

                                            'variant_price': variant_price,

                                            'variant_quantity': variant_quantity,

                                            'variant_image': variant_image

                                        });

                                    } else {

                                        variants.push({

                                            'variant_id': variant_id,

                                            'variant_sku': variant_sku,

                                            'variant_price': variant_price,

                                            'variant_quantity': variant_quantity

                                        });

                                    }

                                    if(variant_quantity=''||
                                        variant_quantity<-1||
                                        variant_quantity==0) {
                                        quantityerror++;
                                    }
                                    if(variant_price==""||variant_price<=
                                        0) {
                                        priceerror++;
                                    }

                                });

                            }).catch(err => {

                                jQuery("#data-table_processing").hide();

                                $(".error_top").show();

                                $(".error_top").html("");

                                $(".error_top").append("<p>" + err + "</p>");

                                window.scrollTo(0, 0);

                            });

                        }


                        var item_attribute = null;

                        if (attributes.length > 0 && variants.length > 0) {

                                if(quantityerror>0) {
                                    alert(
                                        'Please add your variants quantity it should be -1 or greater than -1'
                                    );
                                    return false;
                                }
                                if(priceerror>0) {
                                    alert('Please add your variants  Price');
                                    return false;
                                }

                                var item_attribute = {'attributes': attributes, 'variants': variants};

                        }

                        jQuery("#data-table_processing").show();

                        await storeDigitalImageData().then(async (DigitalImg) => {

                            await storeProductImageData().then(async (IMG) => {

                                if (IMG.length > 0) {

                                    photo = IMG[0];

                                }

                                var objects = {

                                    'name': name,

                                    'price': price.toString(),

                                    'quantity': parseInt(item_quantity),

                                    'disPrice': discount.toString(),

                                    'vendorID': vendor,

                                    'categoryID': category,

                                    'brandID': brand,

                                    'section_id': section_id,

                                    'photo': photo,

                                    'calories': itemCalories,

                                    "grams": itemGrams,

                                    'proteins': itemProteins,

                                    'fats': itemFats,

                                    'description': description,

                                    'publish': itemPublish,

                                    'nonveg': nonveg,

                                    'veg': veg,

                                    'addOnsTitle': addOnesTitle,

                                    'addOnsPrice': addOnesPrice,

                                    'takeawayOption': itemTakeaway,

                                    'product_specification': product_specification,

                                    'id': id,

                                    'item_attribute': item_attribute,

                                    'photos': IMG,

                                    'isDigitalProduct': is_digital_product,

                                    'digitalProduct': DigitalImg,
                                    'createdAt': firebase.firestore.FieldValue.serverTimestamp() 

                                };


                                database.collection('vendor_products').doc(id).set(objects).then(function (result) {

                                    if (reataurantIDDirec) {

                                        window.location.href = "{{route('vendors.items',$id)}}";

                                    } else {

                                        window.location.href = '{{ route("items")}}';

                                    }


                                });

                            }).catch(err => {

                                jQuery("#data-table_processing").hide();

                                $(".error_top").show();

                                $(".error_top").html("");

                                $(".error_top").append("<p>" + err + "</p>");

                                window.scrollTo(0, 0);

                            });

                        }).catch(err => {

                            jQuery("#data-table_processing").hide();

                            $(".error_top").show();

                            $(".error_top").html("");

                            $(".error_top").append("<p>" + err + "</p>");

                            window.scrollTo(0, 0);

                        });

                    }
                }else{

                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append(
                        "<p>{{ trans('lang.create_item_limit_exceed') }}</p>"
                    );
                    window.scrollTo(0, 0);
                }


            })


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

                    var uploadTask = storageRef.child(filename).put(theFile);

                    uploadTask.on('state_changed', function (snapshot) {

                        var progress = (snapshot.bytesTransferred / snapshot.totalBytes) * 100;

                        console.log('Upload is ' + progress + '% done');

                        jQuery("#uploding_image").text("Image is uploading...");


                    }, function (error) {

                    }, function () {

                        uploadTask.snapshot.ref.getDownloadURL().then(function (downloadURL) {

                            jQuery("#uploding_image").text("Upload is completed");

                            photo = downloadURL;

                            $(".item_image").empty()

                            $(".item_image").append('<img class="rounded" style="width:50px" src="' + photo + '" alt="image">');


                        });

                    });


                };

            })(f);

            reader.readAsDataURL(f);

        }


        function handleFileSelectProduct(evt) {

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

                    var uploadTask = storageRef.child(filename).put(theFile);

                    uploadTask.on('state_changed', function (snapshot) {


                        var progress = (snapshot.bytesTransferred / snapshot.totalBytes) * 100;

                        console.log('Upload is ' + progress + '% done');


                        $('.product_image').find(".uploding_image_photos").text("Image is uploading...");


                    }, function (error) {

                    }, function () {

                        uploadTask.snapshot.ref.getDownloadURL().then(function (downloadURL) {

                            jQuery("#uploding_image").text("Upload is completed");

                            if (downloadURL) {


                                productImagesCount++;

                                photos_html = '<span class="image-item" id="photo_' + productImagesCount + '"><span class="remove-btn" data-id="' + productImagesCount + '" data-img="' + downloadURL + '"><i class="fa fa-remove"></i></span><img width="100px" id="" height="auto" src="' + downloadURL + '"></span>';

                                $(".product_image").append(photos_html);

                                photos.push(downloadURL);


                            }


                        });

                    });


                };

            })(f);

            reader.readAsDataURL(f);

        }


        function handleZipUpload(evt) {

            var f = evt.target.files[0];

            var reader = new FileReader();


            reader.onload = (function (theFile) {

                return function (e) {


                    var filePayload = e.target.result;

                    var hash = CryptoJS.SHA256(Math.random() + CryptoJS.SHA256(filePayload));

                    var val = f.name;

                    var ext = val.split('.')[1];

                    var size = f.size;


                    var max_file_size = parseInt(allowed_file_size) * 1000000;

                    if (size > max_file_size) {

                        $("#digital_product_file").val('');

                        alert('{{trans("lang.max_file_limit_error")}}' + allowed_file_size + 'Mb');

                        return false;

                    }


                    if (ext == "jpg" || ext == "jpeg" || ext == "png" || ext == "gif" || ext == "zip" || ext == "pdf") {


                        var docName = val.split('fakepath')[1];

                        var filename = (f.name).replace(/C:\\fakepath\\/i, '')


                        var timestamp = Number(new Date());

                        var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;

                        digital_product_file_name = filename;

                        digital_product_file = filePayload;


                        if (ext == "zip") {

                            digital_product_ext = 'zip';

                            $("#uploding_zip").html('<span class="image-item zip-file"><span class=""   data-file="' + filePayload + '"></span><a href="' + filePayload + '" download><i class="fa fa-file-text" style="font-size:45px"></i></a></span>');

                        } else if (ext == 'pdf') {

                            digital_product_ext = 'pdf';

                            $("#uploding_zip").html('<span class="image-item zip-file"><span class=""   data-file="' + filePayload + '"></span><a href="' + filePayload + '" target="_blank"><i class="fa fa-file-text" style="font-size:45px"></i></a></span>');

                        } else {

                            digital_product_ext = 'image';

                            $("#uploding_zip").html('<span class="image-item zip-file"><span class=""  data-file="' + filePayload + '"></span><img width="100px" id="" height="auto" src="' + filePayload + '"></span>');

                        }

                        $("#digital_product_file").val('');


                    } else {

                        $("#digital_product_file").val('');

                        alert('{{trans("lang.enter_valid_file_ext")}}')

                        return false;

                    }

                };

            })(f);

            reader.readAsDataURL(f);

        }


        $(document).on("click", ".remove-btn", function () {

            var id = $(this).attr('data-id');

            var photo_remove = $(this).attr('data-img');

            $("#photo_" + id).remove();

            index = photos.indexOf(photo_remove);

            if (index > -1) {

                photos.splice(index, 1);

            }

        });


        function handleVariantFileSelect(evt, vid) {

            var f = evt.target.files[0];

            var reader = new FileReader();


            reader.onload = (function (theFile) {

                return function (e) {


                    var filePayload = e.target.result;

                    var hash = CryptoJS.SHA256(Math.random() + CryptoJS.SHA256(filePayload));

                    var val = f.name;

                    var ext = val.split('.')[1];

                    var docName = val.split('fakepath')[1];

                    var timestamp = Number(new Date());

                    var filename = (f.name).replace(/C:\\fakepath\\/i, '')

                    var filename = 'variant_' + vid + '_' + timestamp + '.' + ext;

                    variant_filename.push(filename);

                    variant_photos.push(filePayload);

                    variant_vIds.push(vid);

                    $('[id="variant_' + vid + '_image"]').empty();

                    $('[id="variant_' + vid + '_image"]').html('<img class="rounded" style="width:50px" src="' + filePayload + '" alt="image"><i class="mdi mdi-delete" data-variant="' + vid + '" data-img="' + filePayload + '" data-file="' + filename + '"></i>');

                    $('#upload_' + vid).attr('data-img', filePayload);

                    $('#upload_' + vid).attr('data-file', filename);


                };

            })(f);

            reader.readAsDataURL(f);

        }


        function addOneFunction() {

            $("#add_ones_div").show();

            $(".save_add_one_btn").show();

        }


        function addProductSpecificationFunction() {

            $("#add_product_specification_div").show();

            $(".save_product_specification_btn").show();

        }


        function addProductimagesFunction() {

            $("#add_product_images_div").show();

            $(".save_product_images_btn").show();

        }


        function saveAddOneFunction() {

            var optiontitle = $(".add_ons_title").val();

            var optionPrice = $(".add_ons_price").val();

            $(".add_ons_price").val('');

            $(".add_ons_title").val('');

            if (optiontitle != '' && optionPrice != '') {

                addOnesPrice.push(optionPrice.toString());

                addOnesTitle.push(optiontitle);

                var index = addOnesTitle.length - 1;

                $(".add_ons_list").append('<div class="row" style="margin-top:5px;" id="add_ones_list_iteam_' + index + '"><div class="col-5"><input class="form-control" type="text" value="' + optiontitle + '" disabled ></div><div class="col-5"><input class="form-control" type="text" value="' + optionPrice + '" disabled ></div><div class="col-2"><button class="btn" type="button" onclick="deleteAddOnesSingle(' + index + ')"><span class="fa fa-trash"></span></button></div></div>');

            } else {

                alert("Please enter Title and Price");

            }

        }


        function saveProductSpecificationFunction() {

            var optionlabel = $(".add_label").val();

            var optionvalue = $(".add_value").val();

            $(".add_label").val('');

            $(".add_value").val('');

            if (optionlabel != '' && optionlabel != '') {


                product_specification[optionlabel] = optionvalue;


                $(".product_specification").append('<div class="row" style="margin-top:5px;" id="add_product_specification_iteam_' + optionlabel + '"><div class="col-5"><input class="form-control" type="text" value="' + optionlabel + '" disabled ></div><div class="col-5"><input class="form-control" type="text" value="' + optionvalue + '" disabled ></div><div class="col-2"><button class="btn" type="button" onclick=deleteProductSpecificationSingle("' + optionlabel + '")><span class="fa fa-trash"></span></button></div></div>');

            } else {

                alert("Please enter Label and Value");

            }

        }


        function deleteAddOnesSingle(index) {

            addOnesTitle.splice(index, 1);

            addOnesPrice.splice(index, 1);

            $("#add_ones_list_iteam_" + index).hide();

        }


        function deleteProductSpecificationSingle(index) {


            delete product_specification[index];

            $("#add_product_specification_iteam_" + index).hide();

        }


        $("#item_vendor").change(function () {


            var section_id = $(this).find('option:selected').data('section-id');
            vendorId = $(this).find('option:selected').val();
            sectionId = $(this).find('option:selected').data('section-id');
            userId = $('#item_vendor option:selected').attr("data-user-id");
            if (section_id != undefined) {


                var section_info = $.map(sections_list, function (section, i) {

                    if (section.id == section_id) {

                        return section;

                    }

                });


                if (section_info.length > 0 && (section_info[0].serviceTypeFlag == "ecommerce-service" || section_info[0].serviceTypeFlag == "delivery-service")) {

                    $("#attributes_div").show();

                    $("#item_attribute_chosen").css({'width': '100%'});


                    if (section_info[0].serviceTypeFlag == "delivery-service") {
                        $('.food_delivery_take_away').removeClass('d-none');
                    } else {
                        $('.food_delivery_take_away').addClass('d-none');
                    }

                } else {

                    $("#item_attribute").val('').trigger("chosen:updated");

                    $("#attributes_div").hide();

                    $("#item_attributes").html('');

                    $("#item_variants").html('');

                    $("#attributes").val('');

                    $("#variants").val('');

                }


                if (section_info.length > 0 && section_info[0].is_product_details) {

                    $(".food_delivery_div").removeClass('d-none');

                } else {

                    $(".food_delivery_div").addClass('d-none');

                }


                if (section_info.length > 0 && (section_info[0].serviceTypeFlag == "ecommerce-service")) {

                    $("#is_digital_div").show();

                } else {

                    $("#is_digital_div").hide();

                    $("#upload_file_div").hide();

                    $("#is_digital_product").prop('checked', false);

                }


            } else {

                $("#item_attribute").val('').trigger("chosen:updated");

                $("#attributes_div").hide();

                $("#item_attributes").html('');

                $("#item_variants").html('');

                $("#attributes").val('');

                $("#variants").val('');

                $("#is_digital_div").hide();

                $("#upload_file_div").hide();

                $("#is_digital_product").prop('checked', false);

            }


            var selected_vendor = this.value;

            change_categories(selected_vendor);

            $('.brandDiv').hide();

            $("#brand").val('');

        });


        function change_categories(selected_vendor) {

            vendor_list.forEach((vendor) => {

                if (vendor.id == selected_vendor) {

                    if (vendor.section_id) {

                        $('#item_category').html('');

                        $('#item_category').append($('<option value="">{{trans("lang.select_category")}}</option>'));


                        categories_list.forEach((data) => {

                            var section_id = "";

                            if (data.section_id) {

                                section_id = data.section_id;

                            }

                            if (vendor.section_id == data.section_id) {

                                $('#item_category').append($("<option></option>")

                                    .attr("value", data.id)

                                    .attr("section_id", section_id)

                                    .text(data.title));


                            }

                        })


                    }

                }

            });

        }


        $('#item_category').change(function () {


            var section_id = $(this).find('option:selected').attr('section_id');


            if (section_id) {

                var section = database.collection('sections').where('id', '==', section_id);


                section.get().then(async function (snapshots) {

                    var section_data = snapshots.docs[0].data();

                    if (section_data.serviceTypeFlag == "ecommerce-service") {

                        $('.brandDiv').show();

                    }


                });

            } else {

                $('.brandDiv').hide();

                $("#brand").val('');


            }


        });


        function selectAttribute() {

            var html = '';

            $("#item_attribute").find('option:selected').each(function () {

                html += '<div class="row">';

                html += '<div class="col-md-3">';

                html += '<label>' + $(this).text() + '</label>';

                html += '</div>';

                html += '<div class="col-lg-9">';

                html += '<input type="text" class="form-control" id="attribute_options_' + $(this).val() + '" placeholder="Add attribute values" data-role="tagsinput" onchange="variants_update()">';

                html += '</div>';

                html += '</div>';

            });

            $("#item_attributes").html(html);

            $("#item_attributes input[data-role=tagsinput]").tagsinput();

            $("#attributes").val('');

            $("#variants").val('');

            $("#item_variants").html('');

        }


        function variants_update() {

            var html = '';

            variant_photos = [];

            variant_vIds = [];

            variant_filename = [];

            var item_attribute = $("#item_attribute").map(function (idx, ele) {

                return $(ele).val();

            }).get();


            if (item_attribute.length > 0) {


                var attributes = [];

                var attributeSet = [];

                $.each(item_attribute, function (index, attribute) {

                    var attribute_options = $("#attribute_options_" + attribute).val();

                    if (attribute_options) {

                        var attribute_options = attribute_options.split(',');

                        attribute_options = $.map(attribute_options, function (value) {

                            return value.replace(/[^a-zA-Z0-9]/g, '');

                        });

                        attributeSet.push(attribute_options);

                        attributes.push({'attribute_id': attribute, 'attribute_options': attribute_options});

                    }

                });


                if (attributeSet.length > 0) {


                    $('#attributes').val(JSON.stringify(attributes));


                    var variants = getCombinations(attributeSet);

                    $('#variants').val(JSON.stringify(variants));


                    html += '<table class="table table-bordered">';

                    html += '<thead class="thead-light">';

                    html += '<tr>';

                    html += '<th class="text-center"><span class="control-label">Variant</span></th>';

                    html += '<th class="text-center"><span class="control-label">Variant Price</span></th>';

                    html += '<th class="text-center"><span class="control-label">Variant Quantity</span></th>';

                    html += '<th class="text-center"><span class="control-label">Variant Image</span></th>';

                    html += '</tr>';

                    html += '</thead>';

                    html += '<tbody>';

                    $.each(variants, function (index, variant) {

                        html += '<tr>';

                        html += '<td><label for="" class="control-label">' + variant + '</label></td>';

                        html += '<td>';

                        var check_variant_price = $('#price_' + variant).val() ? $('#price_' + variant).val() : 1;

                        html += '<input type="number" id="price_' + variant + '" value="' + check_variant_price + '" min="0" class="form-control">';

                        html += '</td>';

                        html += '<td>';

                        var check_variant_qty = $('#price_' + variant).val() ? $('#price_' + variant).val() : -1;

                        html += '<input type="number" id="qty_' + variant + '" value="' + check_variant_qty + '" min="-1" class="form-control">';

                        html += '</td>';

                        html += '<td>';

                        html += '<div class="variant-image">';

                        html += '<div class="upload">';

                        html += '<div class="image" id="variant_' + variant + '_image"></div>';

                        html += '<div class="icon"><i class="mdi mdi-cloud-upload" data-variant="' + variant + '"></i></div>';

                        html += '</div>';

                        html += '<div id="variant_' + variant + '_process"></div>';

                        html += '<div class="input-file">';

                        html += '<input type="file" id="file_' + variant + '" onChange="handleVariantFileSelect(event,\'' + variant + '\')" class="form-control" style="display:none;">';

                        html += '<input type="hidden" id="variant_' + variant + '_url" value="">';

                        html += '</div>';

                        html += '</div>';

                        html += '</td>';

                        html += '</tr>';

                    });

                    html += '</tbody>';

                    html += '</table>';

                }

            }

            $("#item_variants").html(html);

        }


        function getCombinations(arr) {

            if (arr.length) {

                if (arr.length == 1) {

                    return arr[0];

                } else {

                    var result = [];

                    var allCasesOfRest = getCombinations(arr.slice(1));

                    for (var i = 0; i < allCasesOfRest.length; i++) {

                        for (var j = 0; j < arr[0].length; j++) {

                            result.push(arr[0][j] + '-' + allCasesOfRest[i]);

                        }

                    }

                    return result;

                }

            }

        }


        function uniqid(prefix = "", random = false) {

            const sec = Date.now() * 1000 + Math.random() * 1000;

            const id = sec.toString(16).replace(/\./g, "").padEnd(14, "0");

            return `${prefix}${id}${random ? `.${Math.trunc(Math.random() * 100000000)}` : ""}`;

        }


        $("#product_image").resizeImg({


            callback: function (base64str) {


                var val = $('#product_image').val().toLowerCase();

                var ext = val.split('.')[1];

                var docName = val.split('fakepath')[1];

                var filename = $('#product_image').val().replace(/C:\\fakepath\\/i, '')

                var timestamp = Number(new Date());

                var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;

                product_image_filename.push(filename);

                productImagesCount++;

                photos_html = '<span class="image-item" id="photo_' + productImagesCount + '"><span class="remove-btn" data-id="' + productImagesCount + '" data-img="' + base64str + '"><i class="fa fa-remove"></i></span><img class="rounded" width="50px" id="" height="auto" src="' + base64str + '"></span>'

                $(".product_image").append(photos_html);

                photos.push(base64str);

                $("#product_image").val('');


            }

        });

        async function storeProductImageData() {

            var newPhoto = [];

            if (photos.length > 0) {

                await Promise.all(photos.map(async (productPhoto, index) => {

                    productPhoto = productPhoto.replace(/^data:image\/[a-z]+;base64,/, "");

                    var uploadTask = await storageRef.child(product_image_filename[index]).putString(productPhoto, 'base64', {contentType: 'image/jpg'});

                    var downloadURL = await uploadTask.ref.getDownloadURL();

                    newPhoto.push(downloadURL);

                }));

            }

            return newPhoto;

        }

        async function storeDigitalImageData() {

            var newPhoto = '';

            try {

                if (digital_product_file != '') {

                    digital_product_file = digital_product_file.replace(/^data:image\/[a-z]+;base64,/, "")

                    if (digital_product_ext == 'zip' || digital_product_ext == "pdf") {

                        var uploadTask = await storageRef.child(digital_product_file_name).put(digital_product_file);

                    } else {

                        var uploadTask = await storageRef.child(digital_product_file_name).putString(digital_product_file, 'base64', {contentType: 'image/jpg'});

                    }

                    var downloadURL = await uploadTask.ref.getDownloadURL();

                    newPhoto = downloadURL;

                    digital_product_file = downloadURL;

                }

            } catch (error) {

                console.log("ERR ===", error);

            }

            return newPhoto;

        }

        async function storeVariantImageData() {

            var newPhoto = [];

            if (variant_photos.length > 0) {

                await Promise.all(variant_photos.map(async (variantPhoto, index) => {

                    variantPhoto = variantPhoto.replace(/^data:image\/[a-z]+;base64,/, "");

                    var uploadTask = await storageRef.child(variant_filename[index]).putString(variantPhoto, 'base64', {contentType: 'image/jpg'});

                    var downloadURL = await uploadTask.ref.getDownloadURL();

                    $('[id="variant_' + variant_vIds[index] + '_url"]').val(downloadURL);

                    newPhoto.push(downloadURL);

                }));

            }

            return newPhoto;

        }


    </script>

@endsection
