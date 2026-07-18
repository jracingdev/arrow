@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor headerText"></h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{!! route('dashboard') !!}">{{ trans('lang.dashboard') }}</a>
                    </li>
                    <li class="breadcrumb-item active headerRedirectionText"></li>
                </ol>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="resttab-sec">
                        <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">{{ trans('lang.processing') }}
                        </div>
                        <div class="error_top"></div>
                        <div id="noPermissionMsg" class="text-center text-danger font-weight-bold mb-3" style="display:none;">
                            <p>{{ trans("lang.no_permission") }}</p>
                        </div>
                        <div class="row vendor_payout_create">
                            <div class="vendor_payout_create-inner">
                                <fieldset class="profile_fieldset" style="display:none">
                                    <legend>{{ trans('lang.admin_area') }}</legend>
                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{ trans('lang.first_name') }}</label>
                                        <div class="col-7">
                                            <input type="text" class="form-control user_first_name" required onkeypress="return chkAlphabets(event,'error1')">
                                            <div id="error1" class="err"></div>
                                            <div class="form-text text-muted">
                                                {{ trans('lang.user_first_name_help') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{ trans('lang.last_name') }}</label>
                                        <div class="col-7">
                                            <input type="text" class="form-control user_last_name" onkeypress="return chkAlphabets(event,'error2')">
                                            <div id="error2" class="err"></div>
                                            <div class="form-text text-muted">
                                                {{ trans('lang.user_last_name_help') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{ trans('lang.email') }}</label>
                                        <div class="col-7">
                                            <input type="email" class="form-control user_email" required>
                                            <div class="form-text text-muted">
                                                {{ trans('lang.user_email_help') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{ trans('lang.user_phone') }}</label>
                                        <div class="col-7">
                                            <input type="text" class="form-control user_phone" onkeypress="return chkAlphabets2(event,'error3')" readonly>
                                            <div id="error3" class="err"></div>
                                            <div class="form-text text-muted w-50">
                                                {{ trans('lang.user_phone_help') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-3 control-label">{{ trans('lang.user_profile_picture') }}</label>
                                        <div class="col-9">
                                            <input type="file" onChange="handleFileSelectowner(event,'vendor')">
                                            <div id="uploding_image_owner"></div>
                                            <div class="uploaded_image_owner" style="display:none;">
                                                <!-- <img id="uploaded_image_owner" src="" width="150px" height="150px;"> -->
                                            </div>
                                            <div class="form-text text-muted">
                                                {{ trans('lang.vendor_image_help') }}
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset class="profile_fieldset" style="display:none">
                                    <legend>{{ trans('lang.password') }}</legend>
                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{ trans('lang.old_password') }}</label>
                                        <div class="col-7">
                                            <input type="password" class="form-control user_old_password" required>
                                            <div class="form-text text-muted">
                                                {{ trans('lang.user_password_help') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{ trans('lang.new_password') }}</label>
                                        <div class="col-7">
                                            <input type="password" class="form-control user_new_password" required>
                                            <div class="form-text text-muted">
                                                {{ trans('lang.user_password_help') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-12 text-center">
                                        <button type="button" class="btn btn-primary  change_user_password"><i class="fa fa-save"></i>{{ trans('lang.change_password') }}
                                        </button>
                                    </div>
                                </fieldset>
                                <div class="vendor_fieldset" style="display:none">
                                    <fieldset>
                                        <legend>{{ trans('lang.vendor_details') }}</legend>
                                        <div class="form-group row width-50">
                                            <label class="col-3 control-label">{{ trans('lang.vendor_name') }}</label>
                                            <div class="col-7">
                                                <input type="text" class="form-control vendor_name">
                                                <div class="form-text text-muted">
                                                    {{ trans('lang.vendor_name_help') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row width-50">
                                            <label class="col-3 control-label">{{ trans('lang.wallet_amount') }}</label>
                                            <h5 class="col-3 control-label text-primary user_wallet"><a href="#"></a></h5>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-3 control-label ">{{ trans('lang.select_section') }}</label>
                                            <div class="col-9">
                                                <select name="section_id" id="section_id" class="form-control">
                                                    <option value="">{{ trans('lang.select') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-3 control-label">{{ trans('lang.category_plural') }}</label>
                                            <div class="col-7">
                                                <select id='vendor_cuisines' class="form-control chosen-select" multiple="multiple">
                                                </select>
                                                <div class="form-text text-muted">
                                                    {{ trans('lang.vendor_category_help') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-3 control-label">{{ trans('lang.vendor_phone') }}</label>
                                            <div class="col-9">
                                                <input type="text" class="form-control vendor_phone" onkeypress="return chkAlphabets2(event,'error4')">
                                                <div id="error4" class="err"></div>
                                                <div class="form-text text-muted">
                                                    {{ trans('lang.vendor_phone_help') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-3 control-label">{{ trans('lang.zone') }}<span class="required-field"></span></label>
                                            <div class="col-9">
                                                <select id='zone' class="form-control">
                                                    <option value="">{{ trans('lang.select_zone') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-3 control-label">{{ trans('lang.vendor_address') }}</label>
                                            <div class="col-9">
                                                <input type="text" class="form-control vendor_address">
                                                <div class="form-text text-muted">
                                                    {{ trans('lang.vendor_address_help') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-9">
                                                <h6>{{ trans('lang.cordinates') }} <a target="_blank" href="https://www.latlong.net/"></a>{{ trans('lang.lat_long') }}
                                                </h6>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-3 control-label">{{ trans('lang.vendor_latitude') }}</label>
                                            <div class="col-9">
                                                <input type="text" class="form-control vendor_latitude">
                                                <div class="form-text text-muted">
                                                    {{ trans('lang.vendor_latitude_help') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-3 control-label">{{ trans('lang.vendor_longitude') }}</label>
                                            <div class="col-9">
                                                <input type="text" class="form-control vendor_longitude">
                                                <div class="form-text text-muted">
                                                    {{ trans('lang.vendor_longitude_help') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-3 control-label ">{{ trans('lang.vendor_description') }}</label>
                                            <div class="col-7">
                                                <textarea rows="7" class="vendor_description form-control" id="vendor_description"></textarea>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <fieldset style="display:none;" id="showhidedinein">
                                        <legend>{{ trans('lang.dine-in-feature') }}</legend>
                                        <div class="form-group row">
                                            <div class="form-group row width-50">
                                                <div class="form-check width-100">
                                                    <input type="checkbox" id="dine_in_feature" class="">
                                                    <label class="col-3 control-label" for="dine_in_feature">{{ trans('lang.dine-in-feature') }}</label>
                                                </div>
                                            </div>                                           
                                            <div class="divein_div" style="display:none">
                                                <div class="form-group row width-50">
                                                    <label class="col-3 control-label">{{ trans('lang.Opening_Time') }}</label>
                                                    <div class="col-7">
                                                        <input type="time" class="form-control" id="openDineTime" required>
                                                    </div>
                                                </div>
                                                <div class="form-group row width-50">
                                                    <label class="col-3 control-label">{{ trans('lang.Closing_Time') }}</label>
                                                    <div class="col-7">
                                                        <input type="time" class="form-control" id="closeDineTime" required>
                                                    </div>
                                                </div>
                                                <div class="form-group row width-50">
                                                    <label class="col-3 control-label">{{ trans('lang.cost') }}</label>
                                                    <div class="col-7">
                                                        <input type="number" class="form-control vendor_cost" required>
                                                    </div>
                                                </div>
                                                <div class="form-group row width-100 vendor_image">
                                                    <label class="col-3 control-label">{{ trans('lang.menu_card') }}</label>
                                                    <div class="col-7">
                                                        <div id="photos_menu_card"></div>
                                                    </div>
                                                </div>
                                                <div class="form-group row width-100">
                                                    <label class="col-3 control-label"></label> <!-- empty label for alignment -->
                                                    <div class="col-7">
                                                        <input type="file" onChange="handleFileSelectMenuCard(event)">
                                                        <div id="uploaded_image_menu"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <fieldset>
                                        <legend>{{ trans('lang.gallery') }}</legend>
                                        <div class="form-group row width-50 vendor_image">
                                            <div class="">
                                                <div id="photos"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div>
                                                <input type="file" onChange="handleFileSelect(event,'photos')">
                                                <div id="uploding_image_photos"></div>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <fieldset id="working_hour_section" class="d-none">
                                        <legend>{{ trans('lang.working_hours') }}</legend>
                                        <div class="form-group row">
                                            <label class="col-12 control-label" style="color:red;font-size:15px;">{{ trans('lang.working_hour_note') }}</label>
                                            <div class="form-group row width-100">
                                                <div class="col-7">
                                                    <button type="button" class="btn btn-primary  add_working_hours_restaurant_btn">
                                                        <i></i>{{ trans('lang.add_working_hours') }}
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="working_hours_div" style="display:none">
                                                <div class="form-group row">
                                                    <label class="col-1 control-label">{{ trans('lang.sunday') }}</label>
                                                    <div class="col-12">
                                                        <button type="button" class="btn btn-primary add_more_sunday" onclick="addMorehour('Sunday','sunday', '1')">
                                                            {{ trans('lang.add_more') }}
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="restaurant_discount_options_Sunday_div restaurant_discount" style="display:none">
                                                    <table class="booking-table" id="working_hour_table_Sunday">
                                                        <tr>
                                                            <th>
                                                                <label class="col-3 control-label">{{ trans('lang.from') }}</label>
                                                            </th>
                                                            <th>
                                                                <label class="col-3 control-label">{{ trans('lang.to') }}</label>
                                                            </th>
                                                            <th>
                                                                <label class="col-3 control-label">{{ trans('lang.actions') }}</label>
                                                            </th>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-1 control-label">{{ trans('lang.monday') }}</label>
                                                    <div class="col-12">
                                                        <button type="button" class="btn btn-primary add_more_sunday" onclick="addMorehour('Monday','monday', '1')">
                                                            {{ trans('lang.add_more') }}
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="restaurant_discount_options_Monday_div restaurant_discount" style="display:none">
                                                    <table class="booking-table" id="working_hour_table_Monday">
                                                        <tr>
                                                            <th>
                                                                <label class="col-3 control-label">{{ trans('lang.from') }}</label>
                                                            </th>
                                                            <th>
                                                                <label class="col-3 control-label">{{ trans('lang.to') }}</label>
                                                            </th>
                                                            <th>
                                                                <label class="col-3 control-label">{{ trans('lang.actions') }}</label>
                                                            </th>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-1 control-label">{{ trans('lang.tuesday') }}</label>
                                                    <div class="col-12">
                                                        <button type="button" class="btn btn-primary" onclick="addMorehour('Tuesday','tuesday', '1')">
                                                            {{ trans('lang.add_more') }}
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="restaurant_discount_options_Tuesday_div restaurant_discount" style="display:none">
                                                    <table class="booking-table" id="working_hour_table_Tuesday">
                                                        <tr>
                                                            <th>
                                                                <label class="col-3 control-label">{{ trans('lang.from') }}</label>
                                                            </th>
                                                            <th>
                                                                <label class="col-3 control-label">{{ trans('lang.to') }}</label>
                                                            </th>
                                                            <th>
                                                                <label class="col-3 control-label">{{ trans('lang.actions') }}</label>
                                                            </th>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-1 control-label">{{ trans('lang.wednesday') }}</label>
                                                    <div class="col-12">
                                                        <button type="button" class="btn btn-primary" onclick="addMorehour('Wednesday','wednesday', '1')">
                                                            {{ trans('lang.add_more') }}
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="restaurant_discount_options_Wednesday_div restaurant_discount" style="display:none">
                                                    <table class="booking-table" id="working_hour_table_Wednesday">
                                                        <tr>
                                                            <th>
                                                                <label class="col-3 control-label">{{ trans('lang.from') }}</label>
                                                            </th>
                                                            <th>
                                                                <label class="col-3 control-label">{{ trans('lang.to') }}</label>
                                                            </th>
                                                            <th>
                                                                <label class="col-3 control-label">{{ trans('lang.actions') }}</label>
                                                            </th>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-1 control-label">{{ trans('lang.thursday') }}</label>
                                                    <div class="col-12">
                                                        <button type="button" class="btn btn-primary" onclick="addMorehour('Thursday','thursday', '1')">
                                                            {{ trans('lang.add_more') }}
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="restaurant_discount_options_Thursday_div restaurant_discount" style="display:none">
                                                    <table class="booking-table" id="working_hour_table_Thursday">
                                                        <tr>
                                                            <th>
                                                                <label class="col-3 control-label">{{ trans('lang.from') }}</label>
                                                            </th>
                                                            <th>
                                                                <label class="col-3 control-label">{{ trans('lang.to') }}</label>
                                                            </th>
                                                            <th>
                                                                <label class="col-3 control-label">{{ trans('lang.actions') }}</label>
                                                            </th>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-1 control-label">{{ trans('lang.friday') }}</label>
                                                    <div class="col-12">
                                                        <button type="button" class="btn btn-primary" onclick="addMorehour('Friday','friday', '1')">
                                                            {{ trans('lang.add_more') }}
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="restaurant_discount_options_Friday_div restaurant_discount" style="display:none">
                                                    <table class="booking-table" id="working_hour_table_Friday">
                                                        <tr>
                                                            <th>
                                                                <label class="col-3 control-label">{{ trans('lang.from') }}</label>
                                                            </th>
                                                            <th>
                                                                <label class="col-3 control-label">{{ trans('lang.to') }}</label>
                                                            </th>
                                                            <th>
                                                                <label class="col-3 control-label">{{ trans('lang.actions') }}</label>
                                                            </th>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-1 control-label">{{ trans('lang.satuarday') }}</label>
                                                    <div class="col-12">
                                                        <button type="button" class="btn btn-primary" onclick="addMorehour('Satuarday','satuarday','1')">
                                                            {{ trans('lang.add_more') }}
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="restaurant_discount_options_Satuarday_div restaurant_discount" style="display:none">
                                                    <table class="booking-table" id="working_hour_table_Satuarday">
                                                        <tr>
                                                            <th>
                                                                <label class="col-3 control-label">{{ trans('lang.from') }}</label>
                                                            </th>
                                                            <th>
                                                                <label class="col-3 control-label">{{ trans('lang.to') }}</label>
                                                            </th>
                                                            <th>
                                                                <label class="col-3 control-label">{{ trans('lang.actions') }}</label>
                                                            </th>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <fieldset style="display: none;" id="services_feature">
                                        <legend>{{ trans('lang.services') }}</legend>
                                        <div class="form-group row">
                                            <div class="form-check width-100">
                                                <input type="checkbox" id="Free_Wi_Fi">
                                                <label class="col-3 control-label" for="Free_Wi_Fi">{{ trans('lang.wifi') }}</label>
                                            </div>
                                            <div class="form-check width-100">
                                                <input type="checkbox" id="Good_for_Breakfast">
                                                <label class="col-3 control-label" for="Good_for_Breakfast">{{ trans('lang.breakfast') }}</label>
                                            </div>
                                            <div class="form-check width-100">
                                                <input type="checkbox" id="Good_for_Dinner">
                                                <label class="col-3 control-label" for="Good_for_Dinner">{{ trans('lang.dinner') }}</label>
                                            </div>
                                            <div class="form-check width-100">
                                                <input type="checkbox" id="Good_for_Lunch">
                                                <label class="col-3 control-label" for="Good_for_Lunch">{{ trans('lang.lunch') }}</label>
                                            </div>
                                            <div class="form-check width-100">
                                                <input type="checkbox" id="Live_Music">
                                                <label class="col-3 control-label" for="Live_Music">{{ trans('lang.live_music') }}</label>
                                            </div>
                                            <div class="form-check width-100">
                                                <input type="checkbox" id="Outdoor_Seating">
                                                <label class="col-3 control-label" for="Outdoor_Seating">{{ trans('lang.outdoor_seating') }}</label>
                                            </div>
                                            <div class="form-check width-100">
                                                <input type="checkbox" id="Takes_Reservations">
                                                <label class="col-3 control-label" for="Takes_Reservations">{{ trans('lang.reservations') }}</label>
                                            </div>
                                            <div class="form-check width-100">
                                                <input type="checkbox" id="Vegetarian_Friendly">
                                                <label class="col-3 control-label" for="Vegetarian_Friendly">{{ trans('lang.vegetarian_friendly') }}</label>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <fieldset class="selfDeliveryOption d-none">
                                        <legend>{{ trans('lang.self_delivery_setting') }}</legend>
                                        <div class="form-group row">
                                            <div class="form-group row width-100">
                                                <div class="form-check width-100">
                                                    <input type="checkbox" id="enable_self_delivery" class="">
                                                    <label class="col-3 control-label" for="enable_self_delivery">{{ trans('lang.enable_self_delivery') }}</label>
                                                    <div class="form-text text-muted">
                                                        {{ trans('lang.enable_self_delivery_help') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <fieldset id="delivery_charges_div">
                                        <legend>{{ trans('lang.deliveryCharge') }}</legend>
                                        <div class="form-group row">
                                            <div class="form-group row width-100">
                                                <label class="col-4 control-label">{{ trans('lang.delivery_charges_per_km') }}</label>
                                                <div class="col-7">
                                                    <input type="number" class="form-control" id="delivery_charges_per_km">
                                                </div>
                                            </div>
                                            <div class="form-group row width-100">
                                                <label class="col-4 control-label">{{ trans('lang.minimum_delivery_charges') }}</label>
                                                <div class="col-7">
                                                    <input type="number" class="form-control" id="minimum_delivery_charges">
                                                </div>
                                            </div>
                                            <div class="form-group row width-100">
                                                <label class="col-4 control-label">{{ trans('lang.minimum_delivery_charges_within_km') }}</label>
                                                <div class="col-7">
                                                    <input type="number" class="form-control" id="minimum_delivery_charges_within_km">
                                                </div>
                                            </div>                                            
                                        </div>
                                    </fieldset>
                                    <fieldset id="packagingChargeDiv" class='d-none'>
                                        <legend>{{ trans('lang.packaging_charge') }}</legend>
                                        <div class="form-group row width-100 packagingChargeEnable d-none">
                                            <label class="col-4 control-label">{{ trans('lang.packaging_charge') }}</label>
                                            <div class="col-7">
                                                <input type="number" class="form-control" id="packagingCharge">
                                            </div>
                                        </div>
                                    </fieldset>
                                    <fieldset>
                                        <legend>{{ trans('lang.bankdetails') }}</legend>
                                        <div class="form-group row">
                                            <div class="form-group row width-100">
                                                <label class="col-4 control-label">{{ trans('lang.bank_name') }}</label>
                                                <div class="col-7">
                                                    <input type="text" name="bank_name" class="form-control" id="bankName">
                                                </div>
                                            </div>
                                            <div class="form-group row width-100">
                                                <label class="col-4 control-label">{{ trans('lang.branch_name') }}</label>
                                                <div class="col-7">
                                                    <input type="text" name="branch_name" class="form-control" id="branchName">
                                                </div>
                                            </div>
                                            <div class="form-group row width-100">
                                                <label class="col-4 control-label">{{ trans('lang.holder_name') }}</label>
                                                <div class="col-7">
                                                    <input type="text" name="holer_name" class="form-control" id="holderName">
                                                </div>
                                            </div>
                                            <div class="form-group row width-100">
                                                <label class="col-4 control-label">{{ trans('lang.account_number') }}</label>
                                                <div class="col-7">
                                                    <input type="text" name="account_number" class="form-control" id="accountNumber" onkeypress="return chkAlphabets2(event,'error5')">
                                                    <div id="error5" class="err"></div>
                                                </div>
                                            </div>
                                            <div class="form-group row width-100">
                                                <label class="col-4 control-label">{{ trans('lang.other_information') }}</label>
                                                <div class="col-7">
                                                    <input type="text" name="other_information" class="form-control" id="otherDetails">
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <fieldset id="story_upload_div" style="display: none;">
                                        <legend>{{trans('lang.story_plural')}}</legend>
                                        <div class="form-group row vendor_image">
                                            <label class="col-3 control-label">{{trans('lang.choose_humbling_gif_image')}}</label>
                                            <div class="">
                                                <div id="story_thumbnail"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-12">
                                                <input type="file" id="file" onChange="handleStoryThumbnailFileSelect(event)">
                                                <div id="uploding_story_thumbnail"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row vendor_image">
                                            <label class="col-3 control-label">{{trans('lang.select_story_video')}}</label>
                                            <div class="col-md-12">
                                                <div id="story_vedios" class="row"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-12">
                                                <input type="file" id="video_file" onChange="handleStoryFileSelect(event)">
                                                <div id="uploding_story_video"></div>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group col-12 text-center btm-btn page-btn">
                <button type="button" class="btn btn-primary  save_vendor_btn"><i class="fa fa-save"></i>
                    {{ trans('lang.save') }}
                </button>
                <a href="{!! route('dashboard') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{ trans('lang.cancel') }}</a>
            </div>
        </div>
    </div>
    </div>
    <div class="dataTables_paginate paging_simple_numbers" id="data-table_paginate">
        <ul class="pagination">
            <li class="paginate_button previous" id="users-table_previous">
                <a href="javascript:void(0);" id="users_table_previous_btn" onclick="prev()" aria-controls="users-table" data-dt-idx="0" tabindex="0">{{trans('lang.previous')}}</a>
            </li>
            <li class="paginate_button">
                <a href="javascript:void(0);" id="users_table_next_btn" onclick="next()" aria-controls="users-table" data-dt-idx="2" tabindex="0">{{trans('lang.next')}}</a>
            </li>
        </ul>
    </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js"></script>
    <script>
        var database = firebase.firestore();
        var geoFirestore = new GeoFirestore(database);
        var photo = "";
        var restaurnt_photos = "";
        var vendorOwnerId = "";
        var vendorOwnerOnline = false;
        var photocount = 0;
        var ownerId = '';
        var vendorUserId = "<?php echo $id; ?>";
        var id = '';
        var vendorOwnerPhoto = '';
        var menuPhotoCount = 0;
        var restaurantMenuPhotos = "";
        var placeholderImage = '';
        var ref_sections = database.collection('sections');
        var commissionObj = '';
        var placeholder = database.collection('settings').doc('placeHolderImage');
        var ref_deliverycharge = database.collection('settings').doc("DeliveryCharge");
        var deliveryChargeFlag = false;
        var storageRef = firebase.storage().ref('images');
        var storage = firebase.storage();
        var subscriptionData = null;
        var workingHours = [];
        var timeslotworkSunday = [];
        var timeslotworkMonday = [];
        var timeslotworkTuesday = [];
        var timeslotworkWednesday = [];
        var timeslotworkFriday = [];
        var timeslotworkSatuarday = [];
        var timeslotworkThursday = [];
        var ownerPhoto = '';
        var ownerFileName = '';
        var ownerOldImageFile = '';
        var vendor_photos = [];
        var new_added_vendor_photos_filename = [];
        var new_added_vendor_photos = [];
        var galleryImageToDelete = [];
        var vendor_menu_photos = [];
        var new_added_vendor_menu_filename = [];
        var new_added_vendor_menu = [];
        var menuImageToDelete = [];
        var isSelfDelivery = false;
        var selfDeliveryRef = database.collection('settings').doc("globalSettings");
        var storevideoDuration = 0;
        var story_vedios = [];
        var story_thumbnail = '';
        var story_thumbnail_filename = '';
        var story_thumbnail_oldfile = '';
        var story_isEnabled = false;
        var storyCount = 0;
        var storyRef = firebase.storage().ref('Story');
        var storyImagesRef = firebase.storage().ref('Story/images');
        var isStory = database.collection('settings').doc('story');
        isStory.get().then(async function(snapshots) {
            var story_data = snapshots.data();
            if (story_data.isEnabled) {
                story_isEnabled = true;
            }
            storevideoDuration = story_data.videoDuration;
        });
        var section_data = [];
        var is_dine_in_active = false;
        var authRole = "{{ $authRole }}";
        var empVendorId = "{{ $empVendorId }}";
        let currentPermissions = {
            isActive: true   
        };
        var section_id = getCookie('section_id') || null;
        var packagingChargeEnable = false;      
        var currentRoute = "{{ Route::currentRouteName() }}";
        var isStorePage = currentRoute === 'store';
        placeholder.get().then(async function(snapshotsimage) {
            var placeholderImageData = snapshotsimage.data();
            placeholderImage = placeholderImageData.image;
        })
        selfDeliveryRef.get().then(async function(settingSnapshots) {
            if (settingSnapshots.data()) {
                var settingData = settingSnapshots.data();
                if (settingData.isSelfDelivery) {
                    isSelfDelivery = true;
                }
            }
        })
        document.addEventListener("DOMContentLoaded", async function() {
            jQuery("#data-table_processing").show();
           
            ref_sections.get().then(async function(snapshots) {
                snapshots.docs.forEach((listval) => {
                    var data = listval.data();
                    if (data.serviceTypeFlag == "delivery-service" || data.serviceTypeFlag == "ecommerce-service") {
                        section_data.push(data);
                        $('#section_id').append(
                            $("<option></option>")
                                .attr("value", data.id)
                                .attr("data-type", data.serviceTypeFlag)
                                .attr("data-commission", JSON.stringify(data.adminCommision))
                                .text(data.name + ' (' + data.serviceType + ')')
                        );
                    }
                });
                if(authRole === 'vendor'){
                    database.collection("users").doc(vendorUserId).get().then(async function(vendorSnap) {
                        if (vendorSnap.exists) {
                            var vendorData = vendorSnap.data();
                            $("#section_id").val(vendorData.sectionId);                       
                            var selectedSection = section_data.find(sec => sec.id == vendorData.sectionId);
                            if (selectedSection && selectedSection.dine_in_active === true) {
                                $("#services_feature").show();
                            } else {
                                $("#services_feature").hide();
                            }
                            await isPackagingEnable(vendorData.sectionId);
                        }
                    });
                }else{
                    database.collection("vendors").doc(empVendorId).get().then(async function(vendorSnap) {
                        if (vendorSnap.exists) {
                            var vendorData = vendorSnap.data();
                            $("#section_id").val(vendorData.section_id);                       
                            var selectedSection = section_data.find(sec => sec.id == vendorData.section_id);
                            if (selectedSection && selectedSection.dine_in_active === true) {
                                $("#services_feature").show();
                            } else {
                                $("#services_feature").hide();
                            }
                            await isPackagingEnable(vendorData.section_id);
                        }
                    });
                }
            });
        database.collection('users').doc(vendorUserId).get().then(async function(userSnapshots) {
            jQuery("#data-table_processing").show();
            var userData = userSnapshots.data();
            if (userData.sectionId != undefined && userData.sectionId != null && userData.sectionId != '') {
                database.collection('sections').doc(userData.sectionId).get().then(async function(snapshots) {
                    var data = snapshots.data();
                    if (data.serviceTypeFlag == "ecommerce-service") {
                        $("#delivery_charges_div").hide();
                        $('#working_hour_section').addClass('d-none');
                    }
                    if (data.serviceTypeFlag == "delivery-service" && isSelfDelivery) {
                        $('.selfDeliveryOption').removeClass('d-none');
                    }
                    if (data.serviceTypeFlag == "delivery-service") {
                        if(authRole == 'vendor'){
                            $("#story_upload_div").show();
                        }else{
                            const perm = await getEmployeePermissionForTitle(vendorUserId, "Add Story");                    

                            if (perm.isActive) {
                                $("#story_upload_div").show();
                            } else{
                                $("#story_upload_div").hide();
                            }        
                        }
                    }
                });
                $("#section_id").val(userData.sectionId);
                $("#section_id").prop("disabled", true);
                await getSectionsCategory(userData.sectionId);
            }
            if (userData.hasOwnProperty('subscription_plan') && userData.subscription_plan != null && userData.subscription_plan != '') {
                subscriptionData = userData.subscription_plan;
                subscriptionData.subscriptionExpiryDate = userData.subscriptionExpiryDate;
            }
            ownerId = userData.id;
            ownerPhoto = userData.profilePictureURL
            vendorOwnerPhoto = userData.profilePictureURL;
            $(".user_first_name").val(userData.firstName);
            $(".user_last_name").val(userData.lastName);
            if (userData.hasOwnProperty('email') && userData.email != null && userData.email != '') {
                $(".user_email").val(userData.email).attr('readonly', true);
            }
            $(".user_phone").val(userData.phoneNumber);
            if (userData.profilePictureURL != '') {
                ownerPhoto = userData.profilePictureURL;
                ownerOldImageFile = userData.profilePictureURL;
                if (userData.profilePictureURL) {
                    photo = userData.profilePictureURL;
                } else {
                    photo = placeholderImage;
                }
                $(".uploaded_image_owner").html(
                    '<img id="uploaded_image_owner" src="' +
                    photo +
                    '" onerror="this.onerror=null;this.src=\'' +
                    placeholderImage +
                    '\'" width="150px" height="150px;">'
                );
            } else {
                $(".uploaded_image_owner").html(
                    '<img id="uploaded_image_owner" src="' +
                    placeholderImage +
                    '" width="150px" height="150px;">'
                );
            }
            $(".uploaded_image_owner").show();           
            if (userData.userBankDetails) {
                if (userData.userBankDetails.bankName != undefined) {
                    $("#bankName").val(userData.userBankDetails.bankName);
                }
                if (userData.userBankDetails.branchName != undefined) {
                    $("#branchName").val(userData.userBankDetails.branchName);
                }
                if (userData.userBankDetails.holderName != undefined) {
                    $("#holderName").val(userData.userBankDetails.holderName);
                }
                if (userData.userBankDetails.accountNumber != undefined) {
                    $("#accountNumber").val(userData.userBankDetails.accountNumber);
                }
                if (userData.userBankDetails.otherDetails != undefined) {
                    $("#otherDetails").val(userData.userBankDetails.otherDetails);
                }
            }
            var ref;
            if(authRole === 'vendor'){
                vendorId = userData.vendorID;
                id = vendorId;
                ref = database.collection('vendors').where("id", "==", vendorId);
            }else{
               
                vendorId = empVendorId;
                id = empVendorId;
                ref = database.collection('vendors').where("id", "==", empVendorId);
            }
                ref.get().then(async function(snapshots) {
                   
                     if (snapshots.empty) {                      

                        vendor = null; 
                        return;
                    }
                    var vendor = snapshots.docs[0].data();
                    $(".vendor_name").val(vendor.title);
                    var selected_category = [];
                    if (vendor.hasOwnProperty('categoryID') && Array.isArray(vendor.categoryID) && vendor.categoryID != null && vendor.categoryID != '' && vendor.categoryID.length > 0) {
                        $.each(vendor.categoryID, function(index, catId) {
                            selected_category.push(catId);
                        });
                    }
                    await database.collection('vendor_categories').where('publish', '==', true).where('section_id', '==', vendor.section_id).get().then(async function(snapshots) {
                        if ($("#vendor_cuisines").data("chosen")) {
                            $("#vendor_cuisines").chosen("destroy");
                        }
                        $('#vendor_cuisines').empty();
                        snapshots.docs.forEach((listval) => {
                            var data = listval.data();
                            var selected = '';
                            if ($.inArray(data.id, selected_category) !== -1) {
                                var selected = 'selected="selected"';
                            }
                            var option = '<option value="' + data.id + '" ' + selected + '>' + data.title + '</option>';
                            $('#vendor_cuisines').append(option);
                        })
                        $("#vendor_cuisines").show().chosen({
                            "placeholder_text": "{{ trans('lang.select_cuisines') }}"
                        });
                    });
                    if (vendor.hasOwnProperty('zoneId') && vendor.zoneId != '') {
                        $("#zone").val(vendor.zoneId);
                    }
                    if (vendor.hasOwnProperty('adminCommission') && vendor.adminCommission !=
                        null && vendor.adminCommission != '') {
                        commissionObj = vendor.adminCommission;
                    }
                    if (vendor.section_id != undefined && vendor.section_id != null &&
                        vendor.section_id != '') {                        
                        $("#section_id").val(vendor.section_id);
                        section_id = vendor.section_id;
                        await isPackagingEnable(section_id);
                        is_dine_in_active = false;                      
                        $.each(section_data, function(index, value) {                            
                            if (value.id == vendor.section_id) {
                                if (value.dine_in_active) {
                                    is_dine_in_active = true;
                                }
                            }
                            if (value.id == vendor.section_id && value
                                .serviceTypeFlag == "ecommerce-service") {
                                $("#delivery_charges_div").hide();
                                $('#working_hour_section').addClass('d-none');
                            }
                            if (value.id == vendor.section_id && value.serviceTypeFlag == "delivery-service") {
                                if(authRole ==='vendor'){
                                    $('#working_hour_section').removeClass('d-none');
                                }
                                if(authRole ==='employee'){
                                    getEmployeePermissionForTitle(vendorUserId, "Working Hours")
                                    .then(perm => {
                                        if (perm && perm.isActive) {
                                            $('#working_hour_section').removeClass('d-none');
                                        } else {
                                            $('#working_hour_section').addClass('d-none');
                                        }
                                    })
                                    .catch(err => {
                                        console.error("Permission check failed:", err);
                                        $('#working_hour_section').addClass('d-none');
                                    });
                                }
                            }
                            if (value.id == vendor.section_id && value
                                .serviceTypeFlag == "delivery-service" &&
                                story_isEnabled == true) {
                                if(authRole == 'vendor'){  
                                     $("#story_upload_div").show();
                                }else{
                                    getEmployeePermissionForTitle(vendorUserId, "Add Story")
                                    .then(perm => {
                                        if (perm && perm.isActive) {
                                            $("#story_upload_div").show();
                                        } else {
                                            $("#story_upload_div").hide();
                                        }
                                    })
                                    .catch(err => {
                                        console.error("Story permission check failed:", err);
                                        $("#story_upload_div").hide();
                                    });       
                                }
                            }
                        });
                        showhidedinein();
                    }
                    $(".vendor_address").val(vendor.location);
                    $(".vendor_latitude").val(vendor.latitude);
                    $(".vendor_longitude").val(vendor.longitude);
                    $(".vendor_description").val(vendor.description);
                    if (packagingChargeEnable) {
                        $('.packagingChargeEnable').removeClass('d-none');
                        $('#packagingChargeDiv').removeClass('d-none');
                        $('#packagingCharge').val(vendor.packagingCharge);
                    }else{
                        $('.packagingChargeEnable').addClass('d-none');
                        $('#packagingChargeDiv').addClass('d-none');
                        $('#packagingCharge').val(vendor.packagingCharge);
                    }
                    if (vendor.opentime) {
                        vendor.opentime = moment(vendor.opentime, 'hh:mm A').format(
                            'HH:mm');
                    }
                    if (vendor.closetime) {
                        vendor.closetime = moment(vendor.closetime, 'hh:mm A').format(
                            'HH:mm');
                    }
                    $("#opentime").val(vendor.opentime);
                    $("#closetime").val(vendor.closetime);
                    if (vendor.hasOwnProperty('restaurantMenuPhotos')) {
                        restaurantMenuPhotos = vendor.restaurantMenuPhotos;
                    }
                    if (vendor.hasOwnProperty('restaurantCost')) {
                        $(".vendor_cost").val(vendor.restaurantCost);
                    }
                    if (vendor.openDineTime) {
                        vendor.openDineTime = moment(vendor.openDineTime, 'hh:mm A').format('HH:mm');
                    }
                    if (vendor.closeDineTime) {
                        vendor.closeDineTime = moment(vendor.closeDineTime, 'hh:mm A').format('HH:mm');
                    }
                    $("#openDineTime").val(vendor.openDineTime);
                    $("#closeDineTime").val(vendor.closeDineTime);
                    var menuCardPhotos = ''
                    if (vendor.hasOwnProperty('restaurantMenuPhotos')) {
                        vendor_menu_photos = vendor.restaurantMenuPhotos;
                        vendor.restaurantMenuPhotos.forEach((photo) => {
                            menuPhotoCount++;
                            if (photo) {
                                photo5 = photo;
                            } else {
                                photo5 = placeholderImage;
                            }
                            menuCardPhotos = menuCardPhotos +
                                '<span class="image-item" id="photo_menu_' +
                                menuPhotoCount +
                                '"><span class="remove-menu-btn" data-id="' +
                                menuPhotoCount + '" data-img="' + photo5 +
                                '" data-status="old"><i class="fa fa-remove"></i></span><img width="100px" id="" height="auto" src="' +
                                photo5 +
                                '" onerror="this.onerror=null;this.src=\'' +
                                placeholderImage + '\'"></span>';
                        })
                    }
                    if (menuCardPhotos) {
                        $("#photos_menu_card").html(menuCardPhotos);
                    } else {
                        $("#photos_menu_card").html('<p><?php echo trans('lang.menu_card_photos_not_available'); ?></p>');
                    }
                    if (vendor.hasOwnProperty('enabledDiveInFuture') && vendor
                        .enabledDiveInFuture == true) {
                        $(".divein_div").show();
                    }
                    if (vendor.hasOwnProperty('enabledDiveInFuture')) {
                        if (vendor.enabledDiveInFuture) {
                            $("#dine_in_feature").prop("checked", true);
                        }
                    }
                    if (vendor.hasOwnProperty('workingHours')) {
                        for (i = 0; i < vendor.workingHours.length; i++) {
                            var day = vendor.workingHours[i]['day'];
                            if (vendor.workingHours[i]['timeslot'].length != 0) {
                                for (j = 0; j < vendor.workingHours[i]['timeslot']
                                    .length; j++) {
                                    $(".restaurant_discount_options_" + day + "_div")
                                        .show();
                                    var timeslot = vendor.workingHours[i]['timeslot'][j];
                                    var discount = vendor.workingHours[i]['timeslot'][j][
                                        'discount'
                                    ];
                                    var TimeslotHourVar = {
                                        'from': timeslot[`from`],
                                        'to': timeslot[`to`]
                                    };
                                    if (day == 'Sunday') {
                                        timeslotworkSunday.push(TimeslotHourVar);
                                    } else if (day == 'Monday') {
                                        timeslotworkMonday.push(TimeslotHourVar);
                                    } else if (day == 'Tuesday') {
                                        timeslotworkTuesday.push(TimeslotHourVar);
                                    } else if (day == 'Wednesday') {
                                        timeslotworkWednesday.push(TimeslotHourVar);
                                    } else if (day == 'Thursday') {
                                        timeslotworkThursday.push(TimeslotHourVar);
                                    } else if (day == 'Friday') {
                                        timeslotworkFriday.push(TimeslotHourVar);
                                    } else if (day == 'Satuarday') {
                                        timeslotworkSatuarday.push(TimeslotHourVar);
                                    }
                                    $('#working_hour_table_' + day + ' tr:last').after(
                                        '<tr>' +
                                        '<td class="" style="width:50%;"><input type="time" class="form-control ' +
                                        i + '_' + j + '_row" value="' + timeslot[
                                            `from`] + '" id="from' + day + j + i +
                                        '" onchange="replaceText(`' + i + '`,`' + j +
                                    '`,`workingHours`)"></td>' +
                                        '<td class="" style="width:50%;"><input type="time" class="form-control ' +
                                        i + '_' + j + '_row" value="' + timeslot[`to`] +
                                        '" id="to' + day + j + i +
                                        '" onchange="replaceText(`' + i + '`,`' + j +
                                    '`,`workingHours`)"></td>' +
                                        '<td class="action-btn" style="width:20%;">' +
                                        '<button type="button" class="btn btn-primary ' +
                                        i + '_' + j + '_row workingHours_' + i + '_' +
                                        j + '"  onclick="updatehoursFunctionButton(`' +
                                    day + '`,`' + j + '`,`' + i +
                                    '`)" ><i class="fa fa-edit"></i></button>' +
                                        '&nbsp;&nbsp;<button type="button" class="btn btn-primary ' +
                                        i + '_' + j +
                                        '_row" onclick="deleteWorkingHour(`' + day +
                                    '`,`' + j + '`,`' + i +
                                    '`)" ><i class="fa fa-trash"></i></button>' +
                                        '</td></tr>');
                                }
                            }
                        }
                    }
                    vendor_photos = vendor.photos;
                    var photos = '';
                    if (vendor.photos.length > 0) {
                        vendor.photos.forEach((photo) => {
                            photocount++;
                            if (photo) {
                                photo4 = photo;
                            } else {
                                photo4 = placeholderImage;
                            }
                            photos = photos +
                                '<span class="image-item" id="photo_' + photocount +
                                '"><span class="remove-btn" data-id="' +
                                photocount + '" data-img="' + photo +
                                '" data-status="old"><i class="fa fa-remove"></i></span><img width="100px" id="" height="auto" src="' +
                                photo4 +
                                '" onerror="this.onerror=null;this.src=\'' +
                                placeholderImage + '\'"></span>';
                        })
                    }
                    if (photos) {
                        $("#photos").html(photos);
                    } else {
                        $("#photos").html('<p>photos not available.</p>');
                    }                

                    if (vendor.hasOwnProperty('filters') && vendor.filters != null) {
                       
                        if (vendor.filters["Free Wi-Fi"] === "Yes") {
                            $("#Free_Wi_Fi").prop("checked", true);
                        } else {
                            $("#Free_Wi_Fi").prop("checked", false);
                        }                        
                        
                        if (vendor.filters["Good for Breakfast"] === "Yes") {
                            $("#Good_for_Breakfast").prop("checked", true);
                        } else {
                            $("#Good_for_Breakfast").prop("checked", false);
                        }
                        
                        if (vendor.filters["Good for Dinner"] === "Yes") {
                            $("#Good_for_Dinner").prop("checked", true);
                        } else {
                            $("#Good_for_Dinner").prop("checked", false);
                        }
                        
                        if (vendor.filters["Good for Lunch"] === "Yes") {
                            $("#Good_for_Lunch").prop("checked", true);
                        } else {
                            $("#Good_for_Lunch").prop("checked", false);
                        }
                        if (vendor.filters["Live Music"] === "Yes") {
                            $("#Live_Music").prop("checked", true);
                        } else {
                            $("#Live_Music").prop("checked", false);
                        }
                        
                        if (vendor.filters["Outdoor Seating"] === "Yes") {
                            $("#Outdoor_Seating").prop("checked", true);
                        } else {
                            $("#Outdoor_Seating").prop("checked", false);
                        }
                        if (vendor.filters["Takes Reservations"] === "Yes") {
                            $("#Takes_Reservations").prop("checked", true);
                        } else {
                            $("#Takes_Reservations").prop("checked", false);
                        }
                        
                        if (vendor.filters["Vegetarian Friendly"] === "Yes") {
                            $("#Vegetarian_Friendly").prop("checked", true);
                        } else {
                            $("#Vegetarian_Friendly").prop("checked", false);
                        }
                    }
                    vendorOwnerOnline = vendor.isActive;
                    photo = vendor.photo;
                    vendorOwnerId = vendor.author;
                    if (vendor.hasOwnProperty('phonenumber')) {
                        $(".vendor_phone").val(vendor.phonenumber);
                    }
                    if (vendor.deliveryCharge && deliveryChargeFlag) {
                        $("#delivery_charges_per_km").val(vendor.deliveryCharge
                            .delivery_charges_per_km);
                        $("#minimum_delivery_charges").val(vendor.deliveryCharge
                            .minimum_delivery_charges);
                        $("#minimum_delivery_charges_within_km").val(vendor.deliveryCharge
                            .minimum_delivery_charges_within_km);
                    }
                    if (vendor.hasOwnProperty('isSelfDelivery') && vendor.isSelfDelivery != null && vendor.isSelfDelivery != '' && vendor.isSelfDelivery) {
                        $('#enable_self_delivery').prop('checked', true);
                    }
                    await getRestaurantStory(vendor.id);
                    if (story_vedios.length > 0) {
                        var html = '';
                        for (var i = 0; i < story_vedios.length; i++) {
                            html += '<div class="col-md-3" id="story_div_' + i + '">\n' +
                                '<div class="video-inner"><video width="320px" height="240px"\n' +
                                '                                   controls="controls">\n' +
                                '                            <source src="' + story_vedios[
                                    i] + '"\n' +
                                '            type="video/mp4"></video><span class="remove-story-video" data-id="' +
                                i + '" data-img="' + story_vedios[i] +
                                '"><i class="fa fa-remove"></i></span></div></div>';
                        }
                        jQuery("#story_vedios").append(html);
                    }
                    if (story_thumbnail) {
                        if (story_thumbnail) {
                            photo3 = story_thumbnail;
                        } else {
                            photo3 = placeholderImage;
                        }
                        html =
                            '<div class="col-md-3"><div class="thumbnail-inner"><span class="remove-story-thumbnail" data-img="' +
                            story_thumbnail +
                            '"><i class="fa fa-remove"></i></span><img id="story_thumbnail_image" src="' +
                            photo3 + '" onerror="this.onerror=null;this.src=\'' +
                            placeholderImage +
                            '\'" width="150px" height="150px;"></div></div>';
                        jQuery("#story_thumbnail").html(html);
                    }
                    jQuery("#data-table_processing").hide();
                })
            
            if (userData.wallet_amount != undefined) {
                var wallet = userData.wallet_amount;
            } else {
                var wallet = 0;
            }
            if (currencyAtRight) {
                var price_val = parseFloat(wallet).toFixed(decimal_degits) + "" + currentCurrency;
            } else {
                var price_val = currentCurrency + "" + parseFloat(wallet).toFixed(decimal_degits);
            }
            $('.user_wallet a').html(price_val);
            jQuery("#data-table_processing").hide();
        })
            <?php if(Route::is('user.profile')): ?>
            $(".profile_fieldset").show();
            $(".headerText").text("{{ trans('lang.user_profile') }}");
            $(".headerRedirectionText").text("{{ trans('lang.user_profile_edit') }}");
            <?php endif; ?>
            <?php if(Route::is('store')): ?>
            $(".vendor_fieldset").show();
            $(".headerText").text("{{ trans('lang.mystore_plural') }}");
            $(".headerRedirectionText").text("{{ trans('lang.mystore_plural') }}");
            <?php endif; ?>
            jQuery("#data-table_paginate").show();
            database.collection('zone').where('publish', '==', true).orderBy('name', 'asc').get().then(async function(snapshots) {
                snapshots.docs.forEach((listval) => {
                    var data = listval.data();
                    var area = [];
                    data.area.forEach((location) => {
                        area.push({
                            'latitude': location.latitude,
                            'longitude': location.longitude
                        });
                    });
                    $('#zone').append($("<option></option>")
                        .attr("value", data.id)
                        .attr("data-area", JSON.stringify(area))
                        .text(data.name));
                })
            });
        });
        $('#section_id').on('change', async function() {
            var sectionVal = $('#section_id').val();
            ref_sections.doc(sectionVal).get().then(async function(snapshots) {
                var data = snapshots.data();
                if (data.serviceTypeFlag == "ecommerce-service") {
                    $("#delivery_charges_div").hide();
                    $('#working_hour_section').addClass('d-none');
                } else {
                    $("#delivery_charges_div").show();
                    (id != '') ? $('#working_hour_section').removeClass('d-none'): $('#working_hour_section').addClass('d-none');
                }
                if (data.serviceTypeFlag == "delivery-service" && story_isEnabled == true) {
                    if(authRole == 'vendor'){
                        $("#story_upload_div").show();
                    }else{
                        const perm = await getEmployeePermissionForTitle(vendorUserId, "Add Story");                   

                        if (perm.isActive) {
                            $("#story_upload_div").show();
                        } else{
                            $("#story_upload_div").hide();
                        }        
                    }
                } else {
                    $("#story_upload_div").hide();
                }
                if (data.dine_in_active == true) {                   
                    $("#services_feature").show();                    
                }else{
                    $("#services_feature").hide();
                }
            })
            getSectionsCategory(sectionVal);
        });
        async function getSectionsCategory(sectionId) {
            if ($("#vendor_cuisines").data('chosen')) {
                $('#vendor_cuisines').chosen('destroy');
            }
            $('#vendor_cuisines').empty();
            await database.collection('vendor_categories').where('section_id', '==', sectionId).get().then(
                async function(snapshots) {
                    snapshots.docs.forEach((listval) => {
                        var data = listval.data();
                        $('#vendor_cuisines').append($("<option></option>")
                            .attr("value", data.id)
                            .text(data.title));
                    })
                    $("#vendor_cuisines").show().chosen({
                        "placeholder_text": "{{ trans('lang.select_category') }}"
                    });
                });
        }
        ref_deliverycharge.get().then(async function(snapshots_charge) {
            var deliveryChargeSettings = snapshots_charge.data();
            try {
                if (deliveryChargeSettings.vendor_can_modify) {
                    deliveryChargeFlag = true;
                    $("#delivery_charges_per_km").val(deliveryChargeSettings.delivery_charges_per_km);
                    $("#minimum_delivery_charges").val(deliveryChargeSettings.minimum_delivery_charges);
                    $("#minimum_delivery_charges_within_km").val(deliveryChargeSettings
                        .minimum_delivery_charges_within_km);
                } else {
                    deliveryChargeFlag = false;
                    $("#delivery_charges_per_km").val(deliveryChargeSettings.delivery_charges_per_km);
                    $("#minimum_delivery_charges").val(deliveryChargeSettings.minimum_delivery_charges);
                    $("#minimum_delivery_charges_within_km").val(deliveryChargeSettings
                        .minimum_delivery_charges_within_km);
                    $("#delivery_charges_per_km").prop('disabled', true);
                    $("#minimum_delivery_charges").prop('disabled', true);
                    $("#minimum_delivery_charges_within_km").prop('disabled', true);
                }
            } catch (error) {
            }
        });
        var currentCurrency = '';
        var currencyAtRight = false;
        var decimal_degits = 0;
        var refCurrency = database.collection('currencies').where('isActive', '==', true);
        refCurrency.get().then(async function(snapshots) {
            var currencyData = snapshots.docs[0].data();
            currentCurrency = currencyData.symbol;
            currencyAtRight = currencyData.symbolAtRight;
            if (currencyData.decimal_degits) {
                decimal_degits = currencyData.decimal_degits;
            }
        });
       
        async function getRestaurantStory(vendorId) {
            await database.collection('story').where('vendorID', '==', vendorId).get().then(
                async function(snapshots) {
                    if (snapshots.docs.length > 0) {
                        var story_data = snapshots.docs[0].data();
                        story_vedios = story_data.videoUrl;
                        story_thumbnail = story_data.videoThumbnail;
                        story_thumbnail_oldfile = story_data.videoThumbnail;
                    }
                });
        }
        $(".change_user_password").click(function() {
            var userOldPassword = $(".user_old_password").val();
            var userNewPassword = $(".user_new_password").val();
            var userEmail = $(".user_email").val();
            if (userOldPassword == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{ trans('lang.old_password_error') }}</p>");
                window.scrollTo(0, 0);
            } else if (userNewPassword == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{ trans('lang.new_password_error') }}</p>");
                window.scrollTo(0, 0);
            } else {
                var user = firebase.auth().currentUser;
                firebase.auth().signInWithEmailAndPassword(userEmail, userOldPassword)
                    .then((userCredential) => {
                        var user = userCredential.user;
                        user.updatePassword(userNewPassword).then(() => {
                            $(".error_top").show();
                            $(".error_top").html("");
                            $(".error_top").append(
                                "<p class='text-success'>{{ trans('lang.password_updated_successfully') }}</p>"
                            );
                            window.scrollTo(0, 0);
                        }).catch((error) => {
                            $(".error_top").show();
                            $(".error_top").html("");
                            $(".error_top").append("<p>" + error + "</p>");
                            window.scrollTo(0, 0);
                        });
                    })
                    .catch((error) => {
                        let message = '';

                        switch (error.code) {
                            case 'auth/wrong-password':
                                message = "{{ trans('lang.current_password_incorrect') }}";
                                break;

                            case 'auth/user-not-found':
                                message = "{{ trans('lang.user_not_found_check_email') }}";
                                break;

                            case 'auth/invalid-email':
                                message = "{{ trans('lang.invalid_email_address') }}";
                                break;

                            case 'auth/too-many-requests':
                                message = "{{ trans('lang.too_many_attempts_try_later') }}";
                                break;

                            default:
                                message = "{{ trans('lang.something_went_wrong') }}";
                        }

                        $(".error_top").show();
                        $(".error_top").html("");
                        $(".error_top").append("<p>" + message + "</p>");
                        window.scrollTo(0, 0);
                    });
            }
        })
      
        $(".save_vendor_btn").click(async function(e) {
            e.preventDefault();            
            $(".error_top").hide();
            $(".error_top").html("");
            
            var vendorname = $(".vendor_name").val();
            var cuisines = $("#vendor_cuisines").val();
            var categoryTitle = $("#vendor_cuisines option:selected").map(function() {
                return $(this).text();
            }).get();
            var address = $(".vendor_address").val();
            var latitude = parseFloat($(".vendor_latitude").val());
            var longitude = parseFloat($(".vendor_longitude").val());
            var description = $(".vendor_description").val();
            var phonenumber = $(".vendor_phone").val();
            var userFirstName = $(".user_first_name").val();
            var userLastName = $(".user_last_name").val();
            var email = $(".user_email").val();
            var userPhone = $(".user_phone").val();
            var section_id = $("#section_id").val();
            var selectedCommission = $("#section_id option:selected").attr("data-commission");
            var vendorCommission = null;
            var packagingCharge = $('#packagingCharge').val();
            var zoneId = $('#zone option:selected').val();
            var zoneArea = $('#zone option:selected').data('area');
            var isInZone = false;            
            
            if (zoneId && zoneArea) {
                isInZone = checkLocationInZone(zoneArea, longitude, latitude);
            }
            
            if (commissionObj != '') {
                vendorCommission = commissionObj;
            } else if (selectedCommission) {
                vendorCommission = JSON.parse(selectedCommission);
            }
            
            var enabledDiveInFuture = $("#dine_in_feature").is(':checked');
            var enable_self_delivery = $("#enable_self_delivery").is(':checked');
            var restaurantCost = $(".vendor_cost").val();
            var openDineTime = $("#openDineTime").val();
            
            if (openDineTime) {
                openDineTime = new Date('1970-01-01T' + openDineTime + 'Z')
                    .toLocaleTimeString('en-US', {
                        timeZone: 'UTC',
                        hour12: true,
                        hour: 'numeric',
                        minute: 'numeric'
                    });
            }
            
            var closeDineTime = $("#closeDineTime").val();
            
            if (closeDineTime) {
                closeDineTime = new Date('1970-01-01T' + closeDineTime + 'Z')
                    .toLocaleTimeString('en-US', {
                        timeZone: 'UTC',
                        hour12: true,
                        hour: 'numeric',
                        minute: 'numeric'
                    });
            }
            
            if (is_dine_in_active == false) {
                enabledDiveInFuture = false;
                restaurantCost = "";
                openDineTime = "";
                closeDineTime = "";
                vendor_menu_photos = [];
            }
            
            var workingHours = [];
            var timeSlot = [];
            var timeSlotObj = {
                'from': '00:00',
                'to': '23:59'
            };
            timeSlot.push(timeSlotObj);
            
            var sunday = {
                'day': 'Sunday',
                'timeslot': (id != '') ? timeslotworkSunday : timeSlot
            };
            var monday = {
                'day': 'Monday',
                'timeslot': (id != '') ? timeslotworkMonday : timeSlot
            };
            var tuesday = {
                'day': 'Tuesday',
                'timeslot': (id != '') ? timeslotworkTuesday : timeSlot
            };
            var wednesday = {
                'day': 'Wednesday',
                'timeslot': (id != '') ? timeslotworkWednesday : timeSlot
            };
            var thursday = {
                'day': 'Thursday',
                'timeslot': (id != '') ? timeslotworkThursday : timeSlot
            };
            var friday = {
                'day': 'Friday',
                'timeslot': (id != '') ? timeslotworkFriday : timeSlot
            };
            var satuarday = {
                'day': 'Satuarday',
                'timeslot': (id != '') ? timeslotworkSatuarday : timeSlot
            };
            
            workingHours.push(monday);
            workingHours.push(tuesday);
            workingHours.push(wednesday);
            workingHours.push(thursday);
            workingHours.push(friday);
            workingHours.push(satuarday);
            workingHours.push(sunday);
            
            var Free_Wi_Fi = "No";
            if ($("#Free_Wi_Fi").is(":checked")) {
                Free_Wi_Fi = "Yes";
            }
            var Good_for_Breakfast = "No";
            if ($("#Good_for_Breakfast").is(':checked')) {
                Good_for_Breakfast = "Yes";
            }
            var Good_for_Dinner = "No";
            if ($("#Good_for_Dinner").is(':checked')) {
                Good_for_Dinner = "Yes";
            }
            var Good_for_Lunch = "No";
            if ($("#Good_for_Lunch").is(':checked')) {
                Good_for_Lunch = "Yes";
            }
            var Live_Music = "No";
            if ($("#Live_Music").is(':checked')) {
                Live_Music = "Yes";
            }
            var Outdoor_Seating = "No";
            if ($("#Outdoor_Seating").is(':checked')) {
                Outdoor_Seating = "Yes";
            }
            var Takes_Reservations = "No";
            if ($("#Takes_Reservations").is(':checked')) {
                Takes_Reservations = "Yes";
            }
            var Vegetarian_Friendly = "No";
            if ($("#Vegetarian_Friendly").is(':checked')) {
                Vegetarian_Friendly = "Yes";
            }
            
            var filters_new = {
                "Free Wi-Fi": Free_Wi_Fi,
                "Good for Breakfast": Good_for_Breakfast,
                "Good for Dinner": Good_for_Dinner,
                "Good for Lunch": Good_for_Lunch,
                "Live Music": Live_Music,
                "Outdoor Seating": Outdoor_Seating,
                "Takes Reservations": Takes_Reservations,
                "Vegetarian Friendly": Vegetarian_Friendly
            };
            
            // Validation
            if (userFirstName == '') {
                jQuery("#data-table_processing").hide();
                $(".error_top").show();
                $(".error_top").html("<p>{{ trans('lang.enter_owners_name_error') }}</p>");
                window.scrollTo(0, 0);
                return false;
            } else if (userLastName == '') {
                jQuery("#data-table_processing").hide();
                $(".error_top").show();
                $(".error_top").html("<p>{{ trans('lang.enter_owners_lastname_error') }}</p>");
                window.scrollTo(0, 0);
                return false;
            } else if (email == '') {
                jQuery("#data-table_processing").hide();
                $(".error_top").show();
                $(".error_top").html("<p>{{ trans('lang.enter_owners_email') }}</p>");
                window.scrollTo(0, 0);
                return false;
            } else if (userPhone == '') {
                jQuery("#data-table_processing").hide();
                $(".error_top").show();
                $(".error_top").html("<p>{{ trans('lang.enter_owners_phone') }}</p>");
                window.scrollTo(0, 0);
                return false;
            } else if (section_id == '') {
                jQuery("#data-table_processing").hide();
                $(".error_top").show();
                $(".error_top").html("<p>{{ trans('lang.select_section_error') }}</p>");
                window.scrollTo(0, 0);
                return false;
            }
            
            <?php if(Route::is('store')): ?>
            else if (vendorname == '') {
                jQuery("#data-table_processing").hide();
                $(".error_top").show();
                $(".error_top").html("<p>{{ trans('lang.vendor_name_error') }}</p>");
                window.scrollTo(0, 0);
                return false;
            } else if (cuisines == '' || cuisines.length == 0) {
                jQuery("#data-table_processing").hide();
                $(".error_top").show();
                $(".error_top").html("<p>{{ trans('lang.vendor_cuisine_error') }}</p>");
                window.scrollTo(0, 0);
                return false;
            } else if (phonenumber == '') {
                jQuery("#data-table_processing").hide();
                $(".error_top").show();
                $(".error_top").html("<p>{{ trans('lang.vendor_phone_error') }}</p>");
                window.scrollTo(0, 0);
                return false;
            } else if (zoneId == '') {
                jQuery("#data-table_processing").hide();
                $(".error_top").show();
                $(".error_top").html("<p>{{ trans('lang.select_zone_help') }}</p>");
                window.scrollTo(0, 0);
                return false;
            } else if (address == '') {
                jQuery("#data-table_processing").hide();
                $(".error_top").show();
                $(".error_top").html("<p>{{ trans('lang.vendor_address_error') }}</p>");
                window.scrollTo(0, 0);
                return false;
            } else if (isNaN(latitude)) {
                jQuery("#data-table_processing").hide();
                $(".error_top").show();
                $(".error_top").html("<p>{{ trans('lang.vendor_lattitude_error') }}</p>");
                window.scrollTo(0, 0);
                return false;
            } else if (latitude < -90 || latitude > 90) {
                jQuery("#data-table_processing").hide();
                $(".error_top").show();
                $(".error_top").html("<p>{{ trans('lang.vendor_lattitude_limit_error') }}</p>");
                window.scrollTo(0, 0);
                return false;
            } else if (isNaN(longitude)) {
                jQuery("#data-table_processing").hide();
                $(".error_top").show();
                $(".error_top").html("<p>{{ trans('lang.vendor_longitude_error') }}</p>");
                window.scrollTo(0, 0);
                return false;
            } else if (longitude < -180 || longitude > 180) {
                jQuery("#data-table_processing").hide();
                $(".error_top").show();
                $(".error_top").html("<p>{{ trans('lang.vendor_longitude_limit_error') }}</p>");
                window.scrollTo(0, 0);
                return false;
            } else if (isInZone == false) {
                jQuery("#data-table_processing").hide();
                $(".error_top").show();
                $(".error_top").html("<p>{{ trans('lang.invalid_location_zone') }}</p>");
                window.scrollTo(0, 0);
                return false;
            } else if (description == '') {
                jQuery("#data-table_processing").hide();
                $(".error_top").show();
                $(".error_top").html("<p>{{ trans('lang.vendor_description_error') }}</p>");
                window.scrollTo(0, 0);
                return false;
            } else if (packagingChargeEnable && (packagingCharge < 0 || packagingCharge === '')) {
                jQuery("#data-table_processing").hide();
                $(".error_top").show();
                $(".error_top").html("<p>{{ trans('lang.packagingCharge_error') }}</p>");
                window.scrollTo(0, 0);
                return false;
            }
            <?php endif; ?>
            
            jQuery("#data-table_processing").show();
            
            try {
                
                <?php if(Route::is('store')): ?>
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
                    'otherDetails': otherDetails,
                };
                
                var tempId = id;
                if (tempId == '' || tempId == null) {
                    tempId = database.collection("tmp").doc().id;                   
                } 
                <?php else: ?>
                var userBankDetails = null;
                var tempId = id;
                <?php endif; ?>
                
                if (!tempId && tempId !== null) {
                    throw new Error("No vendor ID available");
                }
                
                const imageData = await Promise.race([
                    storeImageData(),
                    new Promise((_, reject) => setTimeout(() => reject(new Error("Image upload timeout")), 30000))
                ]);
                
                const galleryImages = await Promise.race([
                    storeGalleryImageData(),
                    new Promise((_, reject) => setTimeout(() => reject(new Error("Gallery upload timeout")), 30000))
                ]);
               
                const menuImages = await Promise.race([
                    storeMenuImageData(),
                    new Promise((_, reject) => setTimeout(() => reject(new Error("Menu upload timeout")), 30000))
                ]);
                
                await database.collection('users').doc(ownerId).update({
                    'firstName': userFirstName,
                    'lastName': userLastName,
                    'email': email,
                    'phoneNumber': userPhone,
                    'profilePictureURL': imageData.ownerImage || null,
                    'userBankDetails': userBankDetails,
                    'section_id': section_id,
                    'vendorID': tempId,
                });
                
                <?php if(Route::is('store')): ?>
                // Only save vendor data if we're on the store page
                var delivery_charges_per_km = parseFloat($("#delivery_charges_per_km").val()) || 0;
                var minimum_delivery_charges = parseFloat($("#minimum_delivery_charges").val()) || 0;
                var minimum_delivery_charges_within_km = parseFloat($("#minimum_delivery_charges_within_km").val()) || 0;
                
                var deliveryCharge = {
                    'delivery_charges_per_km': delivery_charges_per_km,
                    'minimum_delivery_charges': minimum_delivery_charges,
                    'minimum_delivery_charges_within_km': minimum_delivery_charges_within_km
                };
                
                var coordinates = new firebase.firestore.GeoPoint(latitude, longitude);
                var vendorData = {
                    'title': vendorname,
                    'description': description,
                    'latitude': latitude,
                    'longitude': longitude,
                    'location': address,
                    'photo': (galleryImages && galleryImages.length > 0) ? galleryImages[0] : null,
                    'photos': galleryImages && galleryImages.length > 0 ? galleryImages : [],
                    'section_id': section_id,
                    'categoryID': cuisines,
                    'phonenumber': phonenumber,
                    'categoryTitle': categoryTitle,
                    'coordinates': coordinates,
                    'authorName': userFirstName,
                    'enabledDiveInFuture': enabledDiveInFuture,
                    'restaurantMenuPhotos': menuImages && menuImages.length > 0 ? menuImages : [],
                    'restaurantCost': restaurantCost,
                    'deliveryCharge': deliveryCharge,
                    'workingHours': workingHours,
                    'adminCommission': vendorCommission,
                    'isSelfDelivery': enable_self_delivery,
                    'zoneId': zoneId,
                    'filters': filters_new,
                    'openDineTime': openDineTime,
                    'closeDineTime': closeDineTime,
                    'packagingCharge': packagingCharge ? packagingCharge : '0',
                };                
                
                const vendorDoc = await geoFirestore.collection('vendors').doc(tempId).get();
                
                if (vendorDoc.exists) {
                    await geoFirestore.collection('vendors').doc(tempId).update(vendorData);
                } else {
                    vendorData.createdAt = firebase.firestore.FieldValue.serverTimestamp();
                    vendorData.id = tempId;
                    vendorData.author = ownerId;
                    vendorData.authorProfilePic = imageData.ownerImage || null;
                    vendorData.subscriptionExpiryDate = (subscriptionData != null) ? subscriptionData.subscriptionExpiryDate : null;
                    vendorData.subscription_plan = (subscriptionData != null) ? subscriptionData : null;
                    vendorData.subscriptionPlanId = (subscriptionData != null) ? subscriptionData.id : null;
                    vendorData.subscriptionTotalOrders = (subscriptionData != null) ? subscriptionData.orderLimit : null;
                    await geoFirestore.collection('vendors').doc(tempId).set(vendorData);
                }
                
                var storyVendorId = tempId;
                
                await database.collection('users').doc(ownerId).update({
                    'sectionId': section_id
                });
                
                // Handle story uploads
                if (story_vedios && story_vedios.length > 0 || story_thumbnail != '') {
                    if (story_vedios.length > 0 && story_thumbnail == '') {
                        throw new Error("{{ trans('lang.story_error') }}");
                    } else if (story_thumbnail && story_vedios.length == 0) {
                        throw new Error("{{ trans('lang.story_error') }}");
                    } else if (story_vedios.length > 0 && story_thumbnail != '') {
                        await database.collection('story').doc(storyVendorId).set({
                            'createdAt': new Date(),
                            'vendorID': storyVendorId,
                            'videoThumbnail': imageData.storyThumbnailImage || null,
                            'videoUrl': story_vedios,
                            'sectionID': section_id
                        });
                    }
                }
                <?php endif; ?>
               
                jQuery("#data-table_processing").hide();
                window.location.reload();
                
            } catch (err) {
                console.error("Save error:", err);
                jQuery("#data-table_processing").hide();
                $(".error_top").show();
                $(".error_top").html("<p>Error: " + (err.message || JSON.stringify(err)) + "</p>");
                window.scrollTo(0, 0);
            }
        });
        function replaceText(i, j, type) {
            $('.' + type + '_' + i + '_' + j).text("Save");
        }
        function handleStoryFileSelect(evt) {
            var f = evt.target.files[0];
            var reader = new FileReader();
            var story_video_duration = $("#story_video_duration").val();
            var isVideo = document.getElementById('video_file');
            var videoValue = isVideo.value;
            var allowedExtensions = /(\.mp4)$/i;;
            if (!allowedExtensions.exec(videoValue)) {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.error_invalid_video_type')}}</p>");
                window.scrollTo(0, 0);
                isVideo.value = '';
                return false;
            }
            var video = document.createElement('video');
            video.preload = 'metadata';
            video.onloadedmetadata = function() {
                window.URL.revokeObjectURL(video.src);
                var videoDurationTime = Math.trunc(video.duration)
                if (videoDurationTime > storevideoDuration) {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{trans('lang.error_story_video_duration_maximum_allow')}}" + storevideoDuration +
                        " {{trans('lang.seconds')}}</p>");
                    window.scrollTo(0, 0);
                    evt.target.value = '';
                    return false;
                }
                $(".error_top").html("");
                reader.onload = (function(theFile) {
                    return function(e) {
                        var filePayload = e.target.result;
                        var hash = CryptoJS.SHA256(Math.random() + CryptoJS.SHA256(filePayload));
                        var val = f.name;
                        var ext = val.split('.')[1];
                        var docName = val.split('fakepath')[1];
                        var filename = (f.name).replace(/C:\\fakepath\\/i, '')
                        var timestamp = Number(new Date());
                        var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;
                        var uploadTask = storyRef.child(filename).put(theFile);
                        uploadTask.on('state_changed', function(snapshot) {
                            var progress = (snapshot.bytesTransferred / snapshot.totalBytes) * 100;
                            jQuery("#uploding_story_video").text("{{trans('lang.video_is_uploading')}}");
                        }, function(error) {}, function() {
                            uploadTask.snapshot.ref.getDownloadURL().then(function(downloadURL) {
                                jQuery("#uploding_story_video").text("{{trans('lang.upload_is_completed')}}");
                                setTimeout(function() {
                                    jQuery("#uploding_story_video").empty();
                                }, 3000);
                                var nextCount = $("#story_vedios").children().length;
                                html = '<div class="col-md-3" id="story_div_' + nextCount +
                                    '">\n' +
                                    '<div class="video-inner"><video width="320px" height="240px"\n' +
                                    '                                   controls="controls">\n' +
                                    '                            <source src="' +
                                    downloadURL + '"\n' +
                                    '            type="video/mp4"></video><span class="remove-story-video" data-id="' +
                                    nextCount + '" data-img="' + downloadURL +
                                    '"><i class="fa fa-remove"></i></span></div></div>';
                                jQuery("#story_vedios").append(html);
                                story_vedios.push(downloadURL);
                                $("#video_file").val('');
                            });
                        });
                    };
                })(f);
                reader.readAsDataURL(f);
            }
            video.src = URL.createObjectURL(f);
        }
        $(document).on("click", ".remove-story-video", function() {
            var id = $(this).attr('data-id');
            var photo_remove = $(this).attr('data-img');
            firebase.storage().refFromURL(photo_remove).delete();
            $("#story_div_" + id).remove();
            index = story_vedios.indexOf(photo_remove);
            $("#video_file").val('');
            if (index > -1) {
                story_vedios.splice(index, 1); // 2nd parameter means remove one item only
            }
            var newhtml = '';
            if (story_vedios.length > 0) {
                for (var i = 0; i < story_vedios.length; i++) {
                    newhtml += '<div class="col-md-3" id="story_div_' + i + '">\n' +
                        '<div class="video-inner"><video width="320px" height="240px"\n' +
                        'controls="controls">\n' +
                        '<source src="' + story_vedios[i] + '"\n' +
                        'type="video/mp4"></video><span class="remove-story-video" data-id="' + i + '" data-img="' +
                        story_vedios[i] + '"><i class="fa fa-remove"></i></span></div></div>';
                }
            }
            jQuery("#story_vedios").html(newhtml);
            deleteStoryfromCollection();
        });
        $(document).on("click", ".remove-story-thumbnail", function() {
            var photo_remove = $(this).attr('data-img');
            $("#story_thumbnail").empty();
            story_thumbnail = '';
            deleteStoryfromCollection();
        });
        function deleteStoryfromCollection() {
            if (story_vedios.length == 0 && story_thumbnail == '') {
                database.collection('story').where('vendorID', '==', id).get().then(async function(snapshot) {
                    if (snapshot.docs.length > 0) {
                        database.collection('story').doc(id).delete();
                    }
                });
            }
        }
        function handleStoryThumbnailFileSelect(evt) {
            var f = evt.target.files[0];
            var reader = new FileReader();
            var fileInput =
                document.getElementById('file');
            var filePath = fileInput.value;
            // Allowing file type
            var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif)$/i;;
            if (!allowedExtensions.exec(filePath)) {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.error_invalid_file_type')}}</p>");
                window.scrollTo(0, 0);
                fileInput.value = '';
                return false;
            }
            reader.onload = (function(theFile) {
                return function(e) {
                    var filePayload = e.target.result;
                    var hash = CryptoJS.SHA256(Math.random() + CryptoJS.SHA256(filePayload));
                    var val = f.name;
                    var ext = val.split('.')[1];
                    var docName = val.split('fakepath')[1];
                    var filename = (f.name).replace(/C:\\fakepath\\/i, '')
                    var timestamp = Number(new Date());
                    var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;
                    story_thumbnail = filePayload;
                    story_thumbnail_filename = filename;
                    if (story_thumbnail) {
                        photo = story_thumbnail;
                    } else {
                        photo = placeholderImage;
                    }
                    var html =
                        '<div class="col-md-3"><div class="thumbnail-inner"><span class="remove-story-thumbnail" data-img="' +
                        story_thumbnail +
                        '"><i class="fa fa-remove"></i></span><img id="story_thumbnail_image" src="' + photo +
                        '" onerror="this.onerror=null;this.src=\'' + placeholderImage +
                        '\'" width="150px" height="150px;"></div></div>';
                    jQuery("#story_thumbnail").html(html);
                };
            })(f);
            reader.readAsDataURL(f);
        }
        $(document).on("click", ".remove-btn", function() {
            var id = $(this).attr('data-id');
            var photo_remove = $(this).attr('data-img');
            $("#photo_" + id).remove();
            var status = $(this).attr('data-status');
            if (status == "old") {
                galleryImageToDelete.push(firebase.storage().refFromURL(photo_remove));
            }
            index = vendor_photos.indexOf(photo_remove);
            if (index > -1) {
                vendor_photos.splice(index, 1);
            }
            index = new_added_vendor_photos.indexOf(photo_remove);
            if (index > -1) {
                new_added_vendor_photos.splice(index, 1); // 2nd parameter means remove one item only
                new_added_vendor_photos_filename.splice(index, 1);
            }
        });
        function handleFileSelectowner(evt) {
            var f = evt.target.files[0];
            var reader = new FileReader();
            reader.onload = (function(theFile) {
                return function(e) {
                    var filePayload = e.target.result;
                    var hash = CryptoJS.SHA256(Math.random() + CryptoJS.SHA256(filePayload));
                    var val = f.name;
                    var ext = val.split('.')[1];
                    var docName = val.split('fakepath')[1];
                    var filename = (f.name).replace(/C:\\fakepath\\/i, '')
                    var timestamp = Number(new Date());
                    var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;
                    ownerPhoto = filePayload;
                    ownerFileName = filename;
                    if (ownerPhoto) {
                        photo = ownerPhoto;
                    } else {
                        photo = placeholderImage;
                    }
                    $(".uploaded_image_owner").html('<img id="uploaded_image_owner" src="' + photo +
                        '" onerror="this.onerror=null;this.src=\'' + placeholderImage +
                        '\'" width="150px" height="150px;">');
                    $(".uploaded_image_owner").show();
                };
            })(f);
            reader.readAsDataURL(f);
        }
        function handleFileSelect(evt, type) {
            var f = evt.target.files[0];
            var reader = new FileReader();
            reader.onload = (function(theFile) {
                return function(e) {
                    var filePayload = e.target.result;
                    var hash = CryptoJS.SHA256(Math.random() + CryptoJS.SHA256(filePayload));
                    var val = f.name;
                    var ext = val.split('.')[1];
                    var docName = val.split('fakepath')[1];
                    var filename = (f.name).replace(/C:\\fakepath\\/i, '')
                    var timestamp = Number(new Date());
                    var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;
                    photo = filePayload;
                    if (photo) {
                        if (type == 'photos') {
                            photocount++;
                            if (photo) {
                                photo = photo;
                            } else {
                                photo = placeholderImage;
                            }
                            photos_html = '<span class="image-item" id="photo_' + photocount +
                                '"><span class="remove-btn" data-id="' + photocount + '" data-img="' + photo +
                                '" data-status="new"><i class="fa fa-remove"></i></span><img width="100px" id="" height="auto" src="' +
                                photo + '" onerror="this.onerror=null;this.src=\'' + placeholderImage +
                                '\'"></span>';
                            $("#photos").append(photos_html);
                            new_added_vendor_photos.push(photo);
                            new_added_vendor_photos_filename.push(filename);
                        }
                    }
                };
            })(f);
            reader.readAsDataURL(f);
        }
        async function getVendorId(vendorUser) {
            var vendorId = '';
            var ref;
            if (authRole === 'vendor') {
                await database.collection('vendors').where('author', "==", vendorUser).get().then(async function(
                    vendorSnapshots) {
                    if (vendorSnapshots.docs && vendorSnapshots.docs.length > 0) {
                        var vendorData = vendorSnapshots.docs[0].data();
                        vendorId = vendorData.id;
                    }
                })
            }else{                
                await database.collection('vendors').where('id', "==", empVendorId).get().then(async function(
                    vendorSnapshots) {
                    if (vendorSnapshots.docs && vendorSnapshots.docs.length > 0) {
                        var vendorData = vendorSnapshots.docs[0].data();
                        vendorId = vendorData.id;
                    }
                })
            }
            return vendorId;
        }
        $(document).on("change", "#section_id", async function(e) {
            var selected_id = this.value;
            section_id = selected_id;
            is_dine_in_active = false;
            $.each(section_data, function(index, value) {
                if (value.id == selected_id) {
                    if (value.dine_in_active) {
                        is_dine_in_active = true;
                    }
                }
            });
            var serice_type = $("#section_id option:selected").data('type');
            if (serice_type == "ecommerce-service") {
                $("#delivery_charges_div").hide();
            } else {
                $("#delivery_charges_div").show();
            }
            if (serice_type == "delivery-service" && story_isEnabled == true) {
                $('#story_upload_div').show();
            } else {
                $('#story_upload_div').hide();
            }
            await isPackagingEnable(section_id);
            showhidedinein();
        });
        async function showhidedinein() {
            if(authRole=='vendor'){
                if (is_dine_in_active == true) {
                    $("#showhidedinein").show();
                } else {
                    $("#showhidedinein").hide();
                }
            }else{
                if (authRole === 'employee' && isStorePage){        
                    const perm = await getEmployeePermissionForTitle(vendorUserId, "Store Information's");
                    currentPermissions = {
                        isActive: perm.isActive ?? false
                    };

                    if (!currentPermissions.isActive) {
                        alert('{{ trans("lang.no_permission") }}');
                        $('.vendor_payout_create').hide();
                        $('.page-btn').hide();
                        $('#noPermissionMsg').show();                 
                        return;
                    } 
                    
                    // const permDine = await getEmployeePermissionForTitle(vendorUserId, "Dine in Request"); 
                    const permDineAdd = await getEmployeePermissionForTitle(vendorUserId, "Add Dine in");                
                    
                    if (/* permDine.isActive ||  */permDineAdd.isActive && (is_dine_in_active == true)) {
                        $('#showhidedinein').show(); 
                    } else{
                        $('#showhidedinein').hide();
                    } 
                }
            
            }
        }
        function handleFileSelectMenuCard(evt) {
            var f = evt.target.files[0];
            var reader = new FileReader();
            reader.onload = (function(theFile) {
                return function(e) {
                    var filePayload = e.target.result;
                    var hash = CryptoJS.SHA256(Math.random() + CryptoJS.SHA256(filePayload));
                    var val = f.name;
                    var ext = val.split('.')[1];
                    var docName = val.split('fakepath')[1];
                    var filename = (f.name).replace(/C:\\fakepath\\/i, '')
                    var timestamp = Number(new Date());
                    var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;
                    photo = filePayload;
                    if (photo) {
                        menuPhotoCount++;
                        if (photo) {
                            photo = photo;
                        } else {
                            photo = placeholderImage;
                        }
                        photos_html = '<span class="image-item" id="photo_menu_' + menuPhotoCount +
                            '"><span class="remove-menu-btn" data-id="' + menuPhotoCount + '" data-img="' +
                            photo +
                            '" data-status="new"><i class="fa fa-remove"></i></span><img width="100px" id="" height="auto" src="' +
                            photo + '" onerror="this.onerror=null;this.src=\'' + placeholderImage +
                            '\'"></span>';
                        $("#photos_menu_card").append(photos_html);
                        new_added_vendor_menu.push(photo);
                        new_added_vendor_menu_filename.push(filename);
                    }
                };
            })(f);
            reader.readAsDataURL(f);
        }
        $("#dine_in_feature").change(function() {
            if (this.checked) {
                $(".divein_div").show();
            } else {
                $(".divein_div").hide();
            }
        });
        $(".add_working_hours_restaurant_btn").click(function() {
            $(".working_hours_div").show();
        })
        var countAddhours = 1;
        function addMorehour(day, day2, count) {
            count = countAddhours;
            $(".restaurant_discount_options_" + day + "_div").show();
            $('#working_hour_table_' + day + ' tr:last').after('<tr>' +
                '<td class="" style="width:50%;"><input type="time" class="form-control" id="from' + day + count +
                '"></td>' +
                '<td class="" style="width:50%;"><input type="time" class="form-control" id="to' + day + count +
                '"></td>' +
                '<td><button type="button" class="btn btn-primary save_option_day_button' + day + count +
                '" onclick="addMoreFunctionhour(`' + day2 + '`,`' + day + '`,' + countAddhours +
                ')" style="width:62%;">{{trans('lang.save')}}</button>' +
                '</td></tr>');
            countAddhours++;
        }
        function addMoreFunctionhour(day1, day2, count) {
            var to = $("#to" + day2 + count).val();
            var from = $("#from" + day2 + count).val();
            if (to == '' && from == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.please_enter_valid_time')}}</p>");
                window.scrollTo(0, 0);
            } else if (from > to) {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.to_time_can_not_be_less_than_from_time')}}</p>");
                window.scrollTo(0, 0);
            } else {
                var timeslotworkVar = {
                    'from': from,
                    'to': to,
                };
                if (day1 == 'sunday') {
                    timeslotworkSunday.push(timeslotworkVar);
                } else if (day1 == 'monday') {
                    timeslotworkMonday.push(timeslotworkVar);
                } else if (day1 == 'tuesday') {
                    timeslotworkTuesday.push(timeslotworkVar);
                } else if (day1 == 'wednesday') {
                    timeslotworkWednesday.push(timeslotworkVar);
                } else if (day1 == 'thursday') {
                    timeslotworkThursday.push(timeslotworkVar);
                } else if (day1 == 'friday') {
                    timeslotworkFriday.push(timeslotworkVar);
                } else if (day1 == 'satuarday') {
                    timeslotworkSatuarday.push(timeslotworkVar);
                }
                $(".save_option_day_button" + day2 + count).hide();
                $("#to" + day2 + count).attr('disabled', "true");
                $("#from" + day2 + count).attr('disabled', "true");
            }
        }
        function deleteWorkingHour(day, count, i) {
            $('.' + i + '_' + count + '_row').hide();
            if (day == 'Sunday') {
                timeslotworkSunday.splice(count, 1);
            } else if (day == 'Monday') {
                timeslotworkMonday.splice(count, 1);
            } else if (day == 'Tuesday') {
                timeslotworkTuesday.splice(count, 1);
            } else if (day == 'Wednesday') {
                timeslotworkWednesday.splice(count, 1);
            } else if (day == 'Thursday') {
                timeslotworkThursday.splice(count, 1);
            } else if (day == 'Friday') {
                timeslotworkFriday.splice(count, 1);
            } else if (day == 'Satuarday') {
                timeslotworkSatuarday.splice(count, 1);
            }
            var workingHours = [];
            var sunday = {
                'day': 'Sunday',
                'timeslot': timeslotworkSunday
            };
            var monday = {
                'day': 'Monday',
                'timeslot': timeslotworkMonday
            };
            var tuesday = {
                'day': 'Tuesday',
                'timeslot': timeslotworkTuesday
            };
            var wednesday = {
                'day': 'Wednesday',
                'timeslot': timeslotworkWednesday
            };
            var thursday = {
                'day': 'Thursday',
                'timeslot': timeslotworkThursday
            };
            var friday = {
                'day': 'Friday',
                'timeslot': timeslotworkFriday
            };
            var satuarday = {
                'day': 'Satuarday',
                'timeslot': timeslotworkSatuarday
            };
            workingHours.push(monday);
            workingHours.push(tuesday);
            workingHours.push(wednesday);
            workingHours.push(thursday);
            workingHours.push(friday);
            workingHours.push(satuarday);
            workingHours.push(sunday);
            database.collection('vendors').doc(id).update({
                'workingHours': workingHours
            }).then(function(result) {
            });
        }
        function updatehoursFunctionButton(day, rowCount, dayCount) {
            var to = $("#to" + day + rowCount + dayCount + "").val();
            var from = $("#from" + day + rowCount + dayCount + "").val();
            if (to == '' && from == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.please_enter_valid_time')}}</p>");
                window.scrollTo(0, 0);
            } else if (from > to) {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.to_ time_can_not_be_less_than_from_time')}}</p>");
                window.scrollTo(0, 0);
            } else {
                var timeslotworkVar = {
                    'from': from,
                    'to': to
                };
                if (day == 'Sunday') {
                    timeslotworkSunday[rowCount] = timeslotworkVar;
                } else if (day == 'Monday') {
                    timeslotworkMonday[rowCount] = timeslotworkVar;
                } else if (day == 'Tuesday') {
                    timeslotworkTuesday[rowCount] = timeslotworkVar;
                } else if (day == 'Wednesday') {
                    timeslotworkWednesday[rowCount] = timeslotworkVar;
                } else if (day == 'Thursday') {
                    timeslotworkThursday[rowCount] = timeslotworkVar;
                } else if (day == 'Friday') {
                    timeslotworkFriday[rowCount] = timeslotworkVar;
                } else if (day == 'Satuarday') {
                    timeslotworkSatuarday[rowCount] = timeslotworkVar;
                }
            }
        }
        function chkAlphabets(event, msg) {
            if (!(event.which >= 97 && event.which <= 122) && !(event.which >= 65 && event.which <= 90)) {
                document.getElementById(msg).innerHTML = "{{trans('lang.accept_only_alphabets')}}";
                return false;
            } else {
                document.getElementById(msg).innerHTML = "";
                return true;
            }
        }
        function chkAlphabets2(event, msg) {
            if (!(event.which >= 48 && event.which <= 57)) {
                document.getElementById(msg).innerHTML = "{{trans('lang.accept_only_number')}}";
                return false;
            } else {
                document.getElementById(msg).innerHTML = "";
                return true;
            }
        }
        function chkAlphabets3(event, msg) {
            if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                document.getElementById(msg).innerHTML = "{{trans('lang.accept_number')}}";
                return false;
            } else {
                document.getElementById(msg).innerHTML = "";
                return true;
            }
        }        
        async function storeImageData() {
            var result = {
                ownerImage: '',
                storyThumbnailImage: ''
            };
            
            try {
                // Handle owner image
                if (ownerPhoto) {
                    if (ownerOldImageFile && ownerOldImageFile !== "" && ownerPhoto !== ownerOldImageFile) {
                        try {
                            var ownerOldImageUrlRef = await storage.refFromURL(ownerOldImageFile);
                            var imageBucket = ownerOldImageUrlRef.bucket;
                            var envBucket = "<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";
                            if (imageBucket === envBucket) {
                                await ownerOldImageUrlRef.delete();
                                console.log("Old owner image deleted!");
                            }
                        } catch (error) {
                            console.log("Error deleting old owner image:", error);
                        }
                    }
                    
                    if (ownerPhoto !== ownerOldImageFile && ownerFileName) {
                        var base64String = ownerPhoto.replace(/^data:image\/[a-z]+;base64,/, "");
                        var uploadTask = await storageRef.child(ownerFileName).putString(base64String, 'base64', {
                            contentType: 'image/jpg'
                        });
                        var downloadURL = await uploadTask.ref.getDownloadURL();
                        result.ownerImage = downloadURL;
                    } else {
                        result.ownerImage = ownerPhoto;
                    }
                }
                
                // Handle story thumbnail
                if (story_thumbnail) {
                    if (story_thumbnail_oldfile && story_thumbnail_oldfile !== "" && story_thumbnail !== story_thumbnail_oldfile) {
                        try {
                            var thumbnailOldImageUrlRef = await storage.refFromURL(story_thumbnail_oldfile);
                            var imageBucket = thumbnailOldImageUrlRef.bucket;
                            var envBucket = "<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";
                            if (imageBucket === envBucket) {
                                await thumbnailOldImageUrlRef.delete();
                                console.log("Old story thumbnail deleted!");
                            }
                        } catch (error) {
                            console.log("Error deleting old story thumbnail:", error);
                        }
                    }
                    
                    if (story_thumbnail !== story_thumbnail_oldfile && story_thumbnail_filename) {
                        var base64String = story_thumbnail.replace(/^data:image\/[a-z]+;base64,/, "");
                        var uploadTask = await storageRef.child(story_thumbnail_filename).putString(base64String, 'base64', {
                            contentType: 'image/jpg'
                        });
                        var downloadURL = await uploadTask.ref.getDownloadURL();
                        result.storyThumbnailImage = downloadURL;
                    } else {
                        result.storyThumbnailImage = story_thumbnail;
                    }
                }
                
            } catch (error) {
                console.log("ERR in storeImageData ===", error);
                throw error;
            }
            
            return result;
        }
       
        async function storeGalleryImageData() {           
            var newPhoto = [];
            
            // Start with existing photos if any
            if (vendor_photos && vendor_photos.length > 0) {
                newPhoto = [...vendor_photos];
            }
            
            if (new_added_vendor_photos && new_added_vendor_photos.length > 0) {
                const validExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                const photoPromises = new_added_vendor_photos.map(async (resPhoto, index) => {
                    const fileName = new_added_vendor_photos_filename[index];
                    if (!fileName) {
                        throw new Error("Invalid file name for uploaded image");
                    }
                    
                    const extension = fileName.split('.').pop().toLowerCase();
                    if (!validExtensions.includes(extension)) {
                        throw new Error(`Invalid file type (${extension}). Only JPG, JPEG, PNG, GIF allowed.`);
                    }
                    
                    if (!resPhoto || !resPhoto.startsWith("data:image/")) {
                        throw new Error(`Invalid image format: ${fileName}`);
                    }
                    
                    // Strip base64 header
                    const base64String = resPhoto.replace(/^data:image\/[a-z]+;base64,/, "");
                    
                    // Upload to Firebase
                    const uploadTask = await storageRef
                        .child(fileName)
                        .putString(base64String, 'base64', { contentType: 'image/jpg' });
                    const downloadURL = await uploadTask.ref.getDownloadURL();
                    return downloadURL;
                });
                
                let uploadedPhotos;
                try {
                    uploadedPhotos = await Promise.all(photoPromises);
                } catch (err) {
                    jQuery("#data-table_processing").hide();
                    $(".error_top").show();
                    $(".error_top").html("<p>" + err.message + "</p>");
                    window.scrollTo(0, 0);
                    throw err;
                }
                
                newPhoto = [...newPhoto, ...uploadedPhotos];
            }
            
            // Handle deletions
            if (galleryImageToDelete && galleryImageToDelete.length > 0) {
                for (const delImage of galleryImageToDelete) {
                    try {
                        const imageBucket = delImage.bucket;
                        const envBucket = "<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";
                        if (imageBucket === envBucket) {
                            await delImage.delete();
                            console.log("Old file deleted!");
                        }
                    } catch (error) {
                        console.log("Error deleting file:", error);
                    }
                }
            }
            
            return newPhoto;
        }       
        async function storeMenuImageData() {
            var newPhoto = [];
            
            // Start with existing menu photos if any
            if (vendor_menu_photos && vendor_menu_photos.length > 0) {
                newPhoto = [...vendor_menu_photos];
            }
            
            if (new_added_vendor_menu && new_added_vendor_menu.length > 0) {
                const menuPromises = new_added_vendor_menu.map(async (menuPhoto, index) => {
                    const fileName = new_added_vendor_menu_filename[index];
                    if (!fileName) {
                        throw new Error("Invalid file name for menu image");
                    }
                    
                    const base64String = menuPhoto.replace(/^data:image\/[a-z]+;base64,/, "");
                    const uploadTask = await storageRef.child(fileName)
                        .putString(base64String, 'base64', { contentType: 'image/jpg' });
                    const downloadURL = await uploadTask.ref.getDownloadURL();
                    return downloadURL;
                });
                
                const uploadedPhotos = await Promise.all(menuPromises);
                newPhoto = [...newPhoto, ...uploadedPhotos];
            }
            
            // Handle deletions
            if (menuImageToDelete && menuImageToDelete.length > 0) {
                for (const delImage of menuImageToDelete) {
                    try {
                        const imageBucket = delImage.bucket;
                        const envBucket = "<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";
                        if (imageBucket === envBucket) {
                            await delImage.delete();
                            console.log("Old menu file deleted!");
                        }
                    } catch (error) {
                        console.log("Error deleting menu file:", error);
                    }
                }
            }
            
            return newPhoto;
        }
        $(document).on("click", ".remove-menu-btn", function() {
            var id = $(this).attr('data-id');
            var photo_remove = $(this).attr('data-img');
            var status = $(this).attr('data-status');
            if (status == "old") {
                menuImageToDelete.push(firebase.storage().refFromURL(photo_remove));
            }
            $("#photo_menu_" + id).remove();
            index = vendor_menu_photos.indexOf(photo_remove);
            if (index > -1) {
                vendor_menu_photos.splice(index, 1); // 2nd parameter means remove one item only
            }
            index = new_added_vendor_menu.indexOf(photo_remove);
            if (index > -1) {
                new_added_vendor_menu.splice(index, 1); // 2nd parameter means remove one item only
                new_added_vendor_menu_filename.splice(index, 1);
            }
        });
        function checkLocationInZone(area, address_lng, address_lat) {
            var vertices_x = [];
            var vertices_y = [];
            for (j = 0; j < area.length; j++) {
                var geopoint = area[j];
                vertices_x.push(geopoint.longitude);
                vertices_y.push(geopoint.latitude);
            }
            var points_polygon = (vertices_x.length) - 1;
            if (is_in_polygon(points_polygon, vertices_x, vertices_y, address_lng, address_lat)) {
                return true;
            } else {
                return false;
            }
        }
        function is_in_polygon($points_polygon, $vertices_x, $vertices_y, $longitude_x, $latitude_y) {
            $i = $j = $c = $point = 0;
            for ($i = 0, $j = $points_polygon; $i < $points_polygon; $j = $i++) {
                $point = $i;
                if ($point == $points_polygon)
                    $point = 0;
                if ((($vertices_y[$point] > $latitude_y != ($vertices_y[$j] > $latitude_y)) && ($longitude_x < ($vertices_x[$j] - $vertices_x[$point]) * ($latitude_y - $vertices_y[$point]) / ($vertices_y[$j] - $vertices_y[$point]) + $vertices_x[$point])))
                    $c = !$c;
            }
            return $c;
        }
        async function isPackagingEnable(section_id){
            var sectionRef = database.collection('sections').doc(section_id);
            await sectionRef.get().then(function(snapshots) {
                var sectionData = snapshots.data();
                if (sectionData.packagingChargeEnable) {
                    packagingChargeEnable = true;
                    $('.packagingChargeEnable').removeClass('d-none');                    
                    $('#packagingChargeDiv').removeClass('d-none');
                }else{
                    packagingChargeEnable = false;
                    $('.packagingChargeEnable').addClass('d-none');
                    $('#packagingChargeDiv').addClass('d-none');
                }
            });
        }
    </script>
@endsection
