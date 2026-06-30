@extends('layouts.app')

@section('content')
    <div class="page-wrapper">

        <div class="row page-titles">

            <div class="col-md-5 align-self-center">

                <h3 class="text-themecolor">{{ trans('lang.order_plural') }}</h3>

            </div>

            <div class="col-md-7 align-self-center">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>

                    <li class="breadcrumb-item active">{{ trans('lang.order_table') }}</li>

                </ol>

            </div>

            <div>

            </div>

        </div>

        <div class="container-fluid">

            <div class="admin-top-section">

                <div class="row">

                    <div class="col-12">

                        <div class="d-flex top-title-section pb-4 justify-content-between">

                            <div class="d-flex top-title-left align-self-center">

                                <span class="icon mr-3"><img src="{{ asset('images/order.png') }}"></span>

                                <h3 class="mb-0">{{ trans('lang.order_plural') }}</h3>

                                <span class="counter ml-3 total_count"></span>

                            </div>

                            <div class="d-flex top-title-right align-self-center">

                                <div class="select-box pl-3">

                                    <select class="form-control status_selector">

                                        <option value="" selected>{{ trans('lang.status') }}</option>

                                        <option value="Order Placed">{{ trans('lang.order_placed') }}</option>

                                        <option value="Order Accepted">{{ trans('lang.order_accepted') }}</option>

                                        <option value="Order Rejected">{{ trans('lang.order_rejected') }}</option>

                                        <option value="Driver Pending">{{ trans('lang.driver_pending') }}</option>

                                        <option value="Driver Rejected">{{ trans('lang.driver_rejected') }}</option>

                                        <option value="Order Shipped">{{ trans('lang.order_shipped') }}</option>

                                        <option value="In Transit">{{ trans('lang.in_transit') }}</option>

                                        <option value="Order Completed">{{ trans('lang.order_completed') }}</option>

                                    </select>

                                </div>

                                <div class="select-box pl-3">

                                    <select class="form-control order_type_selector">

                                        <option value="" selected>{{ trans('lang.order_type') }}</option>

                                        <option value="takeaway">{{ trans('lang.order_takeaway') }}</option>

                                        <option value="delivery">{{ trans('lang.delivery') }}</option>

                                    </select>

                                </div>

                                <div class="select-box pl-3">

                                    <div id="daterange"><i class="fa fa-calendar"></i>&nbsp;

                                        <span></span>&nbsp; <i class="fa fa-caret-down"></i>

                                    </div>

                                </div>

                                <div class="select-box pl-3">

                                    <select class="form-control filteredRecords allModules" id="section_id" onchange="clickLink(this.value)">

                                        <option value="" selected>{{ trans('lang.select') }} {{ trans('lang.section_plural') }}

                                    </select>

                                    <p style="color: red;font-size: 13px;"> {{ trans('lang.rental_parcel_cab_service_are_not') }}</p>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="table-list">

                <div class="row">

                    <div class="col-12">

                        <?php if (isset($_GET['driverId'])) { ?>

                        <div class="menu-tab">

                            <ul>

                                <li>

                                    <a href="{{ route('drivers.view', $_GET['driverId']) }}">{{ trans('lang.tab_basic') }}</a>

                                </li>

                                <li>

                                    <a href="{{ route('drivers.vehicle', $_GET['driverId']) }}">{{ trans('lang.vehicle') }}</a>

                                </li>

                                <li class="active">

                                    <a href="{{ route('orders', 'driverId=' . $_GET['driverId']) }}">{{ trans('lang.order_plural') }}</a>

                                </li>

                                <li>

                                    <a href="{{ route('driver.payouts', $_GET['driverId']) }}">{{ trans('lang.tab_payouts') }}</a>

                                </li>

                                <li>

                                    <a href="{{ route('payoutRequests.drivers.view', $_GET['driverId']) }}" class="vendor_payout">{{ trans('lang.tab_payout_request') }}</a>

                                </li>

                                <li>

                                    <a href="{{ route('users.walletstransaction', $_GET['driverId']) }}" class="wallet_transaction">{{ trans('lang.wallet_transaction') }}</a>

                                </li>

                            </ul>

                        </div>

                        <?php } ?>

                        <?php if (isset($_GET['userId'])) { ?>

                        <div class="menu-tab">

                            <ul>

                                <li><a href="{{ route('users.view', $_GET['userId']) }}">{{ trans('lang.tab_basic') }}</a>

                                </li>

                                <li class="active"><a href="{{ route('orders', 'userId=' . $_GET['userId']) }}">{{ trans('lang.tab_orders') }}</a>

                                </li>

                                <li>

                                    <a href="{{ route('users.walletstransaction', $_GET['userId']) }}">{{ trans('lang.wallet_transaction') }}</a>

                                </li>

                            </ul>

                        </div>

                        <?php } ?>

                        <div class="menu-tab vendorMenuTab">

                            <ul>

                                <li>

                                    <a href="{{ route('vendors.view', $id) }}">{{ trans('lang.tab_basic') }}</a>

                                </li>

                                <li>

                                    <a href="{{ route('vendors.items', $id) }}">{{ trans('lang.tab_items') }}</a>

                                </li>

                                <li class="active">

                                    <a href="{{ route('vendors.orders', $id) }}">{{ trans('lang.tab_orders') }}</a>

                                </li>

                                <li>

                                    <a href="{{ route('vendors.reviews', $id) }}">{{ trans('lang.tab_reviews') }}</a>

                                </li>

                                <li>

                                    <a href="{{ route('vendors.coupons', $id) }}">{{ trans('lang.tab_promos') }}</a>

                                <li>

                                    <a href="{{ route('vendors.payout', $id) }}">{{ trans('lang.tab_payouts') }}</a>

                                </li>

                                <li>

                                    <a href="{{ route('payoutRequests.vendor.view', $id) }}">{{ trans('lang.tab_payout_request') }}</a>

                                </li>

                                <li>

                                    <a class="wallet_transaction_vendor">{{ trans('lang.wallet_transaction') }}</a>

                                </li>

                                <li class="dine_in_future" style="display:none;">

                                    <a href="{{ route('vendors.booktable', $id) }}">{{ trans('lang.dine_in_future') }}</a>

                                </li>

                                <?php
                                
                                $subscription = route('subscription.subscriptionPlanHistory', ':id');
                                
                                $subscription = str_replace(':id', 'storeID=' . $id, $subscription);
                                
                                ?>

                                <li>

                                    <a href="{{ $subscription }}">{{ trans('lang.subscription_history') }}</a>

                                </li>

                            </ul>

                        </div>

                        <div class="row">

                            <div class="col-12">

                                <div class="card border">

                                    <div class="card-body">

                                        <div class="row">

                                            <div class="col-md-3">

                                                <div class="card card-box-with-icon bg--1">

                                                    <div class="card-body d-flex justify-content-between align-items-center">

                                                        <div class="card-box-with-content">

                                                            <h4 class="text-dark-2 mb-1 h4 order_count" id="order_count"></h4>

                                                            <p class="mb-0 small text-dark-2">{{ trans('lang.dashboard_total_orders') }}</p>

                                                        </div>

                                                        <span class="box-icon ab"><img src="{{ asset('images/total_orders.png') }}"></span>

                                                    </div>

                                                </div>

                                            </div>

                                            <div class="col-md-3">

                                                <div class="card card-box-with-icon bg--5">

                                                    <div class="card-body d-flex justify-content-between align-items-center">

                                                        <div class="card-box-with-content">

                                                            <h4 class="text-dark-2 mb-1 h4 placed_count" id="placed_count"></h4>

                                                            <p class="mb-0 small text-dark-2">{{ trans('lang.dashboard_order_placed') }}</p>

                                                        </div>

                                                        <span class="box-icon ab"><img src="{{ asset('images/order_placed.png') }}"></span>

                                                    </div>

                                                </div>

                                            </div>

                                            <div class="col-md-3">

                                                <div class="card card-box-with-icon bg--6">

                                                    <div class="card-body d-flex justify-content-between align-items-center">

                                                        <div class="card-box-with-content">

                                                            <h4 class="text-dark-2 mb-1 h4 accepted_count" id="accepted_count"></h4>

                                                            <p class="mb-0 small text-dark-2">{{ trans('lang.dashboard_order_accepted') }}</p>

                                                        </div>

                                                        <span class="box-icon ab"><img src="{{ asset('images/order_accepted.png') }}"></span>

                                                    </div>

                                                </div>

                                            </div>

                                            <div class="col-md-3">

                                                <div class="card card-box-with-icon bg--8">

                                                    <div class="card-body d-flex justify-content-between align-items-center">

                                                        <div class="card-box-with-content">

                                                            <h4 class="text-dark-2 mb-1 h4 order_completed" id="order_completed"></h4>

                                                            <p class="mb-0 small text-dark-2">{{ trans('lang.dashboard_order_completed') }}</p>

                                                        </div>

                                                        <span class="box-icon ab"><img src="{{ asset('images/order_completed.png') }}"></span>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="card border">

                            <div class="card-header d-flex justify-content-between align-items-center border-0">

                                <div class="card-header-title">

                                    <h3 class="text-dark-2 mb-2 h4">{{ trans('lang.order_table') }}</h3>

                                    <p class="mb-0 text-dark-2">{{ trans('lang.order_table_text') }}</p>

                                </div>

                                <div class="card-header-right d-flex align-items-center">

                                    <div class="card-header-btn mr-3">

                                    </div>

                                </div>

                            </div>

                            <div class="card-body">

                                <div class="table-responsive m-t-10">

                                    <table id="orderTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">

                                        <thead>

                                            <tr>

                                                <?php if (in_array('orders.delete', json_decode(@session('user_permissions'), true))) { ?>

                                                <th class="delete-all"><input type="checkbox" id="is_active"><label class="col-3 control-label" for="is_active"><a id="deleteAll" class="do_not_delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i> {{ trans('lang.all') }}</a></label></th>

                                                <?php } ?>

                                                <th>{{ trans('lang.order_id') }}</th>

                                                @if ($id == '')
                                                    <th>{{ trans('lang.vendor') }}</th>

                                                    <th>{{ trans('lang.section') }}</th>
                                                @endif

                                                @if (isset($_GET['userId']))
                                                    <th class="driverClass">{{ trans('lang.driver_plural') }}</th>
                                                @elseif (isset($_GET['driverId']))
                                                    <th>{{ trans('lang.order_user_id') }}</th>
                                                @else
                                                    <th class="driverClass">{{ trans('lang.driver_plural') }}</th>

                                                    <th>{{ trans('lang.order_user_id') }}</th>
                                                @endif

                                                <th>{{ trans('lang.date') }}</th>

                                                <th>{{ trans('lang.vendors_payout_amount') }}</th>

                                                <th>{{ trans('lang.order_type') }}</th>

                                                <th>{{ trans('lang.order_order_status_id') }}</th>

                                                <th>{{ trans('lang.actions') }}</th>

                                            </tr>

                                        </thead>

                                        <tbody id="append_list1">

                                        </tbody>

                                    </table>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        var user_permissions = '<?php echo @session('user_permissions'); ?>';

        user_permissions = JSON.parse(user_permissions);

        var checkDeletePermission = false;

        if ($.inArray('orders.delete', user_permissions) >= 0) {

            checkDeletePermission = true;

        }



        var database = firebase.firestore();

        var vendor_id = '<?php echo $id; ?>';

        var append_list = '';

        var refData = "";

        var currentCurrency = '';

        var currencyAtRight = false;

        var refCurrency = database.collection('currencies').where('isActive', '==', true);

        var ref_sections = database.collection('sections');



        var decimal_degits = 0;

        refCurrency.get().then(async function(snapshots) {



            var currencyData = snapshots.docs[0].data();

            currentCurrency = currencyData.symbol;

            currencyAtRight = currencyData.symbolAtRight;

            if (currencyData.decimal_degits) {



                decimal_degits = currencyData.decimal_degits;



            }



        });

        var user_permissions = '<?php echo @session('user_permissions'); ?>';

        user_permissions = JSON.parse(user_permissions);

        var checkPrintPermission = false;

        if ($.inArray('orders.print', user_permissions) >= 0) {

            checkPrintPermission = true;

        }



        var order_status = jQuery('#order_status').val();

        var search = jQuery("#search").val();

        refData = database.collection('vendor_orders');

        $('.vendorMenuTab').hide();

        var getId = '<?php echo $id; ?>';



        if (getId != "") {

            $('.sectionDiv').hide();

            refData = database.collection('vendor_orders');

        } else {

            $('.sectionDiv').show();

            var section_id = getCookie('section_id');

            if (section_id != '') {

                refData = database.collection('vendor_orders').where('vendor.section_id', '==', section_id);

            } else {

                refData = database.collection('vendor_orders');

            }



        }

        $('.userClass').hide();

        $('.vendorClass').hide();

        $('.driverClass').hide();

        var ref = '';

        $(document.body).on('change', '#order_status', function() {

            order_status = jQuery(this).val();

        });

        $(document.body).on('keyup', '#search', function() {

            search = jQuery(this).val();

        });

        var orderStatus = '<?php if (isset($_GET['status'])) {
            echo $_GET['status'];
        } else {
            echo '';
        } ?>';



        var getId = '<?php echo $id; ?>';

        var userID = '<?php if (isset($_GET['userId'])) {
            echo $_GET['userId'];
        } else {
            echo '';
        } ?>';



        var driverID = '<?php if (isset($_GET['driverId'])) {
            echo $_GET['driverId'];
        } else {
            echo '';
        } ?>';

        var orderStatus = '<?php if (isset($_GET['status'])) {
            echo $_GET['status'];
        } else {
            echo '';
        } ?>';



        if (userID) {

            const getUserName = getUserNameFunction(userID);

            if ((order_status == 'All' || order_status != '') && search != '') {

                ref = refData.orderBy('createdAt', 'desc').where('authorID', '==', userID);

            } else {

                ref = refData.orderBy('createdAt', 'desc').where('authorID', '==', userID);



            }



        } else if (driverID) {

            const getUserName = getUserNameFunction(driverID);

            var wallet_route = "{{ route('users.walletstransaction', 'id') }}";

            $(".wallet_transaction").attr("href", wallet_route.replace('id', 'driverID=' + driverID));

            if ((order_status == 'All' || order_status != '') && search != '') {

                ref = refData.orderBy('createdAt', 'desc').where('driverID', '==', driverID);



            } else {

                ref = refData.orderBy('createdAt', 'desc').where('driverID', '==', driverID);

            }

        } else if (orderStatus) {

            if (orderStatus == 'order-placed') {

                ref = refData.orderBy('createdAt', 'desc').where('status', '==', 'Order Placed');

                $("ol.breadcrumb ").append("<li class='breadcrumb-item active'>{{ trans('lang.order_placed') }}</li>");



            } else if (orderStatus == 'order-confirmed') {

                ref = refData.orderBy('createdAt', 'desc').where('status', 'in', ['Order Accepted', 'Driver Accepted']);

                $("ol.breadcrumb ").append("<li class='breadcrumb-item active'>{{ trans('lang.order_accepted') }}</li>");



            } else if (orderStatus == 'order-shipped') {

                ref = refData.orderBy('createdAt', 'desc').where('status', 'in', ['Order Shipped', 'In Transit']);

                $("ol.breadcrumb ").append("<li class='breadcrumb-item active'>{{ trans('lang.order_shipped') }}</li>");

            } else if (orderStatus == 'order-completed') {

                ref = refData.orderBy('createdAt', 'desc').where('status', '==', 'Order Completed');

                $("ol.breadcrumb ").append("<li class='breadcrumb-item active'>{{ trans('lang.order_completed') }}</li>");



            } else if (orderStatus == 'order-canceled') {



                ref = refData.orderBy('createdAt', 'desc').where('status', '==', 'Order Rejected');



                $("ol.breadcrumb ").append("<li class='breadcrumb-item active'>{{ trans('lang.order_rejected') }}</li>");

            } else if (orderStatus == 'order-failed') {



                ref = refData.orderBy('createdAt', 'desc').where('status', '==', 'Driver Rejected');



                $("ol.breadcrumb ").append("<li class='breadcrumb-item active'>{{ trans('lang.driver_rejected') }}</li>");

            } else if (orderStatus == 'order-pending') {



                ref = refData.orderBy('createdAt', 'desc').where('status', '==', 'Driver Pending');



                $("ol.breadcrumb ").append("<li class='breadcrumb-item active'>{{ trans('lang.driver_pending') }}</li>");



            } else {

                ref = refData.orderBy('createdAt', 'desc');



            }

        } else if (getId != '') {

            $('.vendorMenuTab').show();

            const getStoreName = getStoreNameFunction(getId);



            if ((order_status == 'All' || order_status != '') && search != '') {

                ref = refData.orderBy('createdAt', 'desc').where('vendorID', '==', getId);



            } else {

                ref = refData.orderBy('createdAt', 'desc').where('vendorID', '==', getId);



            }



        } else {

            if ((order_status == 'All' || order_status != '') && search != '') {

                ref = refData.orderBy('createdAt', 'desc');



            } else {

                ref = refData.orderBy('createdAt', 'desc');



            }
        }



        const sectionsRef = firebase.firestore().collection('sections');



        $('.status_selector').select2({

            placeholder: '{{ trans('lang.status') }}',

            minimumResultsForSearch: Infinity,

            allowClear: true

        });



        $('.order_type_selector').select2({

            placeholder: '{{ trans('lang.order_type') }}',

            minimumResultsForSearch: Infinity,

            allowClear: true

        });

        $('.allModules').select2({

            placeholder: "{{ trans('lang.select') }} {{ trans('lang.section_plural') }}",

            minimumResultsForSearch: Infinity,

            allowClear: true

        });

        $('select').on("select2:unselecting", function(e) {

            var self = $(this);

            setTimeout(function() {

                self.select2('close');

            }, 0);

        });



        function setDate() {

            $('#daterange span').html('{{ trans('lang.select_range') }}');

            $('#daterange').daterangepicker({

                autoUpdateInput: false,

            }, function(start, end) {

                $('#daterange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

                $('.filteredRecords').trigger('change');

            });

            $('#daterange').on('apply.daterangepicker', function(ev, picker) {

                $('#daterange span').html(picker.startDate.format('MMMM D, YYYY') + ' - ' + picker.endDate.format('MMMM D, YYYY'));

                $('.filteredRecords').trigger('change');

            });

            $('#daterange').on('cancel.daterangepicker', function(ev, picker) {

                $('#daterange span').html('{{ trans('lang.select_range') }}');

                $('.filteredRecords').trigger('change');

            });

        }

        setDate();

        var initialRef = ref;

        $('select').change(async function() {

            var status = $('.status_selector').val();

            var orderType = $('.order_type_selector').val();

            var daterangepicker = $('#daterange').data('daterangepicker');

            var refData = initialRef;



            if (status) {

                refData = refData.where('status', '==', status);

            }

            if (orderType) {

                refData = (orderType == 'takeaway') ? refData.where('takeAway', '==', true) : refData.where('takeAway', '==', false);

            }

            if ($('#daterange span').html() != '{{ trans('lang.select_range') }}' && daterangepicker) {

                var from = moment(daterangepicker.startDate).toDate();

                var to = moment(daterangepicker.endDate).toDate();

                if (from && to) {

                    var fromDate = firebase.firestore.Timestamp.fromDate(new Date(from));

                    refData = refData.where('createdAt', '>=', fromDate);

                    var toDate = firebase.firestore.Timestamp.fromDate(new Date(to));

                    refData = refData.where('createdAt', '<=', toDate);

                }

            }

            ref = refData;

            $('#orderTable').DataTable().ajax.reload(null, false);

        });





        $(document).ready(function() {


            var refTotalOrder = database.collection('vendor_orders');
            if ("{{$id}}" != '') {
                refTotalOrder = refTotalOrder.where('vendorID', '==', vendor_id)
            }
            refTotalOrder.get().then((snapshot) => {

                jQuery("#order_count").empty();

                jQuery("#order_count").text(snapshot.docs.length);

            });


            var refPlacedOrder = database.collection('vendor_orders').where('status', 'in', ["Order Placed"]);
            if ("{{$id}}" != '') {
                refPlacedOrder = refPlacedOrder.where('vendorID', '==', vendor_id)
            }
            refPlacedOrder.get().then((snapshot) => {

                jQuery("#placed_count").empty();

                jQuery("#placed_count").text(snapshot.docs.length);

            });


            var refAcceptedOrder = database.collection('vendor_orders').where('status', 'in', ["Order Accepted"]);
            if ("{{$id}}" != '') {
                refAcceptedOrder = refAcceptedOrder.where('vendorID', '==', vendor_id)
            }
            refAcceptedOrder.get().then((snapshot) => {

                jQuery("#accepted_count").empty();

                jQuery("#accepted_count").text(snapshot.docs.length);

            });


            var refCompletedOrder = database.collection('vendor_orders').where('status', 'in', ["Order Completed"]);
            if ("{{$id}}" != '') {
                refCompletedOrder = refCompletedOrder.where('vendorID', '==', vendor_id)
            }
            refCompletedOrder.get().then((snapshot) => {

                jQuery("#order_completed").empty();

                jQuery("#order_completed").text(snapshot.docs.length);

            });





            $(document.body).on('click', '.redirecttopage', function() {



                var url = $(this).attr('data-url');



                window.location.href = url;



            });



            ref_sections.get().then(async function(snapshots) {



                snapshots.docs.forEach((listval) => {



                    var data = listval.data();

                    if (data.serviceTypeFlag != "cab-service" && data.serviceTypeFlag != "parcel_delivery" && data.serviceTypeFlag != "rental-service" && data.serviceTypeFlag != "ondemand-service") {



                        $('#section_id').append($("<option></option>")



                            .attr("value", data.id)



                            .text(data.name));



                    }



                })



                $('#section_id').val(section_id);



            })

            $(document.body).on('change', '#selected_search', function() {



                if (jQuery(this).val() == 'status') {



                    jQuery('#order_status').show();



                } else {

                    jQuery('#order_status').hide();



                }



            });



            jQuery("#data-table_processing").show();



            append_list = document.getElementById('append_list1');



            append_list.innerHTML = '';



            ref.get().then(async function(snapshots) {



                jQuery("#data-table_processing").hide();





                $(document).on('click', '.dt-button-collection .dt-button', function() {

                    $('.dt-button-collection').hide();

                    $('.dt-button-background').hide();

                });

                $(document).on('click', function(event) {

                    if (!$(event.target).closest('.dt-button-collection, .dt-buttons').length) {

                        $('.dt-button-collection').hide();

                        $('.dt-button-background').hide();

                    }

                });

                var columns = [];

                if (getId != '') {

                    columns = [

                        {
                            key: 'id',
                            header: "{{ trans('lang.order_id') }}"
                        },

                        {
                            key: 'driverName',
                            header: "{{ trans('lang.driver_plural') }}"
                        },

                        {
                            key: 'clientName',
                            header: "{{ trans('lang.order_user_id') }}"
                        },

                        {
                            key: 'createdAt',
                            header: "{{ trans('lang.date') }}"
                        },

                        {
                            key: 'amount',
                            header: "{{ trans('lang.vendors_payout_amount') }}"
                        },

                        {
                            key: 'orderType',
                            header: "{{ trans('lang.order_type') }}"
                        },

                        {
                            key: 'status',
                            header: "{{ trans('lang.order_order_status_id') }}"
                        },

                    ];

                } else if (driverID) {

                    columns = [

                        {
                            key: 'id',
                            header: "{{ trans('lang.order_id') }}"
                        },

                        {
                            key: 'storeName',
                            header: "{{ trans('lang.vendor') }}"
                        },

                        {
                            key: 'sectionName',
                            header: "{{ trans('lang.section') }}"
                        },

                        {
                            key: 'clientName',
                            header: "{{ trans('lang.order_user_id') }}"
                        },

                        {
                            key: 'createdAt',
                            header: "{{ trans('lang.date') }}"
                        },

                        {
                            key: 'amount',
                            header: "{{ trans('lang.vendors_payout_amount') }}"
                        },

                        {
                            key: 'orderType',
                            header: "{{ trans('lang.order_type') }}"
                        },

                        {
                            key: 'status',
                            header: "{{ trans('lang.order_order_status_id') }}"
                        },

                    ];

                } else if (userID) {

                    columns = [

                        {
                            key: 'id',
                            header: "{{ trans('lang.order_id') }}"
                        },

                        {
                            key: 'storeName',
                            header: "{{ trans('lang.vendor') }}"
                        },

                        {
                            key: 'sectionName',
                            header: "{{ trans('lang.section') }}"
                        },

                        {
                            key: 'driverName',
                            header: "{{ trans('lang.driver_plural') }}"
                        },

                        {
                            key: 'createdAt',
                            header: "{{ trans('lang.date') }}"
                        },

                        {
                            key: 'amount',
                            header: "{{ trans('lang.vendors_payout_amount') }}"
                        },

                        {
                            key: 'orderType',
                            header: "{{ trans('lang.order_type') }}"
                        },

                        {
                            key: 'status',
                            header: "{{ trans('lang.order_order_status_id') }}"
                        },

                    ];

                } else {

                    columns = [

                        {
                            key: 'id',
                            header: "{{ trans('lang.order_id') }}"
                        },

                        {
                            key: 'storeName',
                            header: "{{ trans('lang.vendor') }}"
                        },

                        {
                            key: 'sectionName',
                            header: "{{ trans('lang.section') }}"
                        },

                        {
                            key: 'driverName',
                            header: "{{ trans('lang.driver_plural') }}"
                        },

                        {
                            key: 'clientName',
                            header: "{{ trans('lang.order_user_id') }}"
                        },

                        {
                            key: 'createdAt',
                            header: "{{ trans('lang.date') }}"
                        },

                        {
                            key: 'amount',
                            header: "{{ trans('lang.vendors_payout_amount') }}"
                        },

                        {
                            key: 'orderType',
                            header: "{{ trans('lang.order_type') }}"
                        },

                        {
                            key: 'status',
                            header: "{{ trans('lang.order_order_status_id') }}"
                        },

                    ];

                }



                var fieldConfig = {

                    columns: columns, // Assign the dynamically generated array here

                    fileName: "{{ trans('lang.order_table') }}",

                };



                const table = $('#orderTable').DataTable({

                    pageLength: 10, // Number of rows per page

                    processing: false, // Show processing indicator

                    serverSide: true, // Enable server-side processing

                    responsive: true,

                    ajax: function(data, callback, settings) {

                        const start = data.start;

                        const length = data.length;

                        const searchValue = data.search.value.toLowerCase();

                        const orderColumnIndex = data.order[0].column;

                        const orderDirection = data.order[0].dir;



                        if (getId != '') {

                            var orderableColumns = (checkDeletePermission) ? ['', 'id', 'driverName', 'clientName', 'createdAt', 'amount', 'orderType', 'status', ''] : ['id', 'driverName', 'clientName', 'createdAt', 'amount', 'orderType', 'status', '']; // Ensure this matches the actual column names



                        } else if (driverID) {

                            var orderableColumns = (checkDeletePermission) ? ['', 'id', 'storeName', 'sectionName', 'clientName', 'createdAt', 'amount', 'orderType', 'status', ''] : ['id', 'storeName', 'sectionName', 'clientName', 'createdAt', 'amount', 'orderType', 'status', '']; // Ensure this matches the actual column names





                        } else if (userID) {

                            var orderableColumns = (checkDeletePermission) ? ['', 'id', 'storeName', 'sectionName', 'driverName', 'createdAt', 'amount', 'orderType', 'status', ''] : ['id', 'storeName', 'sectionName', 'driverName', 'createdAt', 'amount', 'orderType', 'status', ''];; // Ensure this matches the actual column names



                        } else {

                            var orderableColumns = (checkDeletePermission) ? ['', 'id', 'storeName', 'sectionName', 'driverName', 'clientName', 'createdAt', 'amount', 'orderType', 'status', ''] : ['id', 'storeName', 'sectionName', 'driverName', 'clientName', 'createdAt', 'amount', 'orderType', 'status', '']; // Ensure this matches the actual column names



                        }



                        const orderByField = orderableColumns[orderColumnIndex]; // Adjust the index to match your table



                        if (searchValue.length >= 3 || searchValue.length === 0) {



                            $('#data-table_processing').show();



                        }







                        ref.get().then(async function(querySnapshot) {



                            if (querySnapshot.empty) {

                                $('.total_count').text(0);



                                console.error("No data found in Firestore.");



                                $('#data-table_processing').hide(); // Hide loader



                                callback({



                                    draw: data.draw,



                                    recordsTotal: 0,



                                    recordsFiltered: 0,



                                    data: [] // No data



                                });



                                return;



                            }







                            let records = [];



                            let filteredRecords = [];



                            let sectionNames = {};



                            // Fetch section names



                            const sectionDocs = await sectionsRef.get();



                            sectionDocs.forEach(doc => {



                                sectionNames[doc.id] = doc.data().name;



                            });





                            querySnapshot.docs.map(async doc => {







                                let childData = doc.data();



                                childData.id = doc.id; // Ensure the document ID is included in the data



                                childData.sectionName = sectionNames[childData.section_id] || 'N/A';



                                var driverName = '';



                                if (childData.hasOwnProperty("driver") && childData.driver != null) {



                                    var driverId = childData.driver.id;



                                    driverName = childData.driver.firstName + ' ' + childData.driver.lastName;



                                    childData.driverName = driverName;



                                }



                                if (childData.hasOwnProperty('vendor') && childData.vendor != null) {



                                    childData.storeName = childData.vendor.title;



                                }



                                if (childData.hasOwnProperty('author') && childData.author != null) {



                                    childData.clientName = childData.author.firstName + ' ' + childData.author.lastName;



                                }



                                if (childData.hasOwnProperty('takeAway') && childData.takeAway) {

                                    childData.orderType = "{{ trans('lang.order_takeaway') }}"

                                } else {

                                    childData.orderType = "{{ trans('lang.order_delivery') }}";

                                }



                                var price = 0;







                                price = buildHTMLProductstotal(childData);



                                childData.amount = price;







                                if (searchValue) {



                                    var date = '';



                                    var time = '';



                                    if (childData.hasOwnProperty("createdAt")) {



                                        try {



                                            date = childData.createdAt.toDate().toDateString();



                                            time = childData.createdAt.toDate().toLocaleTimeString('en-US');



                                        } catch (err) {



                                        }



                                    }



                                    var createdAt = date + ' ' + time;



                                    if (



                                        (childData.id && childData.id.toString().includes(searchValue)) ||



                                        (childData.storeName && childData.storeName.toLowerCase().toString().includes(searchValue)) ||



                                        (childData.sectionName && childData.sectionName.toString().includes(searchValue)) ||



                                        (childData.driverName && childData.driverName.toString().includes(searchValue)) ||



                                        (childData.clientName && childData.clientName.toString().includes(searchValue)) ||



                                        (createdAt && createdAt.toString().toLowerCase().indexOf(searchValue) > -1) ||



                                        (childData.orderType && childData.orderType.toLowerCase().toString().includes(searchValue)) ||



                                        (childData.status && childData.status.toLowerCase().toString().includes(searchValue)) ||



                                        (childData.amount && childData.amount.toString().toLowerCase().includes(searchValue))



                                    ) {



                                        filteredRecords.push(childData);



                                    }



                                } else {



                                    filteredRecords.push(childData);



                                }



                            });



                            filteredRecords.sort((a, b) => {



                                let aValue = a[orderByField];



                                let bValue = b[orderByField];







                                if (orderByField === 'createdAt' && a[orderByField] != '' && b[orderByField] != '' && a[orderByField] != null && b[orderByField] != null) {







                                    aValue = a[orderByField] ? new Date(a[orderByField].toDate()).getTime() : 0;



                                    bValue = b[orderByField] ? new Date(b[orderByField].toDate()).getTime() : 0;



                                } else if (orderByField === 'amount') {



                                    aValue = a[orderByField] ? parseFloat(String(a[orderByField]).replace(/[^0-9.]/g, '')) || 0 : 0;



                                    bValue = b[orderByField] ? parseFloat(String(b[orderByField]).replace(/[^0-9.]/g, '')) || 0 : 0;



                                } else {



                                    aValue = a[orderByField] ? a[orderByField].toString().toLowerCase().trim() : '';



                                    bValue = b[orderByField] ? b[orderByField].toString().toLowerCase().trim() : ''



                                }







                                if (orderDirection === 'asc') {



                                    return (aValue > bValue) ? 1 : -1;



                                } else {



                                    return (aValue < bValue) ? 1 : -1;



                                }







                            });



                            const totalRecords = filteredRecords.length;

                            $('.total_count').text(totalRecords);



                            const paginatedRecords = filteredRecords.slice(start, start + length);



                            await Promise.all(paginatedRecords.map(async (childData) => {



                                var getData = await buildHTML(childData);

                                records.push(getData);



                            }));



                            console.log("Records fetched:", records.length);



                            $('#data-table_processing').hide(); // Hide loader



                            callback({



                                draw: data.draw,



                                recordsTotal: totalRecords, // Total number of records in Firestore



                                recordsFiltered: totalRecords, // Number of records after filtering (if any)

                                filteredData: filteredRecords,



                                data: records // The actual data to display in the table



                            });



                        }).catch(function(error) {



                            console.error("Error fetching data from Firestore:", error);



                            $('#data-table_processing').hide(); // Hide loader



                            callback({



                                draw: data.draw,



                                recordsTotal: 0,



                                recordsFiltered: 0,



                                data: [] // No data due to error



                            });



                        });



                    },







                    order: (getId != '') ? ((checkDeletePermission) ? [
                        [4, 'desc']
                    ] : [
                        [3, 'desc']
                    ]) : ((driverID || userID) ? ((checkDeletePermission) ? [
                        [5, 'desc']
                    ] : [
                        [4, 'desc']
                    ]) : (checkDeletePermission) ? [
                        [6, 'desc']
                    ] : [
                        [5, 'desc']
                    ]),



                    columnDefs: [



                        {



                            orderable: false,



                            targets: (getId != '') ? ((checkDeletePermission) ? [0, 8] : [7]) : ((driverID || userID) ? ((checkDeletePermission == true) ? [0, 9] : [8]) : ((checkDeletePermission == true) ? [0, 10] : [9])),



                        },



                        {



                            type: 'date',



                            render: function(data) {



                                return data;



                            },



                            targets: (getId != '') ? ((checkDeletePermission) ? [4] : [3]) : ((driverID || userID) ? ((checkDeletePermission == true) ? 5 : 4) : ((checkDeletePermission == true) ? 6 : 5)),



                        }







                    ],



                    "language": {



                        "zeroRecords": "{{ trans("lang.no_record_found") }}",



                        "emptyTable": "{{ trans("lang.no_record_found") }}",



                        "processing": "" // Remove default loader



                    },

                    dom: 'lfrtipB',

                    buttons: [

                        {

                            extend: 'collection',

                            text: '<i class="mdi mdi-cloud-download"></i> Export as',

                            className: 'btn btn-info',

                            buttons: [

                                {

                                    extend: 'excelHtml5',

                                    text: 'Export Excel',

                                    action: function(e, dt, button, config) {

                                        exportData(dt, 'excel', fieldConfig);

                                    }

                                },

                                {

                                    extend: 'pdfHtml5',

                                    text: 'Export PDF',

                                    action: function(e, dt, button, config) {

                                        exportData(dt, 'pdf', fieldConfig);

                                    }

                                },

                                {

                                    extend: 'csvHtml5',

                                    text: 'Export CSV',

                                    action: function(e, dt, button, config) {

                                        exportData(dt, 'csv', fieldConfig);

                                    }

                                }

                            ]

                        }

                    ],

                    initComplete: function() {

                        $(".dataTables_filter").append($(".dt-buttons").detach());

                        $('.dataTables_filter input').attr('placeholder', 'Search here...').attr('autocomplete', 'new-password').val('');

                        $('.dataTables_filter label').contents().filter(function() {

                            return this.nodeType === 3;

                        }).remove();

                    }



                });





                function debounce(func, wait) {



                    let timeout;



                    const context = this;



                    return function(...args) {



                        clearTimeout(timeout);



                        timeout = setTimeout(() => func.apply(context, args), wait);



                    };



                }



                $('#search-input').on('input', debounce(function() {



                    const searchValue = $(this).val();



                    if (searchValue.length >= 3) {



                        $('#data-table_processing').show();



                        table.search(searchValue).draw();



                    } else if (searchValue.length === 0) {



                        $('#data-table_processing').show();



                        table.search('').draw();



                    }



                }, 300));



            });



        });



        async function buildHTML(val) {

            var html = [];



            var id = val.id;



            var vendorID = val.vendorID;



            var user_id = val.authorID;



            var route1 = '{{ route('orders.edit', ':id') }}';

            route1 = route1.replace(':id', id);



            var printRoute = '{{ route('vendors.orderprint', ':id') }}';



            printRoute = printRoute.replace(':id', id);



            <?php if ($id != '') { ?>



            route1 = route1 + '?eid={{ $id }}';



            printRoute = printRoute + '?eid={{ $id }}';



            <?php } ?>



            var route_view = '{{ route('vendors.view', ':id') }}';



            route_view = route_view.replace(':id', vendorID);







            var customer_view = '{{ route('users.view', ':id') }}';



            customer_view = customer_view.replace(':id', user_id);



            if (checkDeletePermission) {



                html.push('<td class="delete-all"><input type="checkbox" id="is_open_' + id + '" class="is_open" dataId="' + id + '"><label class="col-3 control-label"\n' +



                    'for="is_open_' + id + '" ></label></td>');



            }







            html.push('<a data-url="' + route1 + '" href="' + route1 + '" class="redirecttopage">' + val.id + '</a>');



            if (userID) {



                var title = '';



                if (val.hasOwnProperty('vendor') && val.vendor.title != undefined) {



                    title = val.vendor.title;



                }



                html.push('<td  data-url="' + route_view + '" href="' + route_view + '" class="redirecttopage">' + title + '</td>');



                if (val.section_id) {



                    html.push('<td class="sectionName_' + val.section_id + '">' + val.sectionName + '</td>');



                } else {



                    html.push('');



                }







                if (val.hasOwnProperty("driver") && val.driver != null) {



                    var driverId = val.driver.id;



                    var diverRoute = '{{ route('drivers.view', ':id') }}';



                    diverRoute = diverRoute.replace(':id', driverId);



                    html.push('<td  data-url="' + diverRoute + '" class="redirecttopage">' + val.driver.firstName + ' ' + val.driver.lastName + '</td>');







                } else {



                    html.push('');



                }







            } else if (driverID) {



                if (val.hasOwnProperty('vendor') && val.vendor.title != undefined) {



                    html.push('<a  data-url="' + route_view + '" href="' + route_view + '" class="redirecttopage">' + val.storeName + '</a>');



                }



                if (val.section_id) {



                    html.push('<td class="sectionName_' + val.section_id + '">' + val.sectionName + '</td>');



                } else {



                    html.push('');



                }



                if (val.hasOwnProperty("author") && val.author != null) {



                    html.push('<a  data-url="' + customer_view + '" href="' + customer_view + '" class="redirecttopage">' + val.author.firstName + ' ' + val.author.lastName + '</a>');







                } else {



                    html.push('');



                }







            } else if (getId != '') {







                if (val.hasOwnProperty("driver") && val.driver != null) {



                    var driverId = val.driver.id;



                    var diverRoute = '{{ route('drivers.view', ':id') }}';



                    diverRoute = diverRoute.replace(':id', driverId);



                    html.push('<a  data-url="' + diverRoute + '" href="' + diverRoute + '"  class="redirecttopage">' + val.driver.firstName + ' ' + val.driver.lastName + '</a>');







                } else {



                    html.push('');







                }











                if (val.hasOwnProperty("author") && val.author != null) {



                    var driverId = val.author.id;







                    html.push('<a  data-url="' + customer_view + '" href="' + customer_view + '" class="redirecttopage">' + val.author.firstName + ' ' + val.author.lastName + '</a>');







                } else {



                    html.push('');



                }







            } else {



                var title = '';



                if (val.hasOwnProperty('vendor') && val.vendor.title != undefined) {



                    title = val.vendor.title;



                }







                html.push('<a  data-url="' + route_view + '" href="' + route_view + '" class="redirecttopage">' + title + '</a>');



                if (val.section_id) {



                    html.push('<td class="sectionName_' + val.section_id + '">' + val.sectionName + '</td>');







                } else {



                    html.push('');







                }







                if (val.hasOwnProperty("driver") && val.driver != null) {



                    var driverId = val.driver.id;



                    var diverRoute = '{{ route('drivers.view', ':id') }}';



                    diverRoute = diverRoute.replace(':id', driverId);



                    html.push('<a  data-url="' + diverRoute + '" href="' + diverRoute + '" class="redirecttopage">' + val.driver.firstName + ' ' + val.driver.lastName + '</a>');







                } else {



                    html.push('');



                }











                if (val.hasOwnProperty("author") && val.author != null) {



                    var driverId = val.author.id;







                    html.push('<a  data-url="' + customer_view + '" href="' + customer_view + '" class="redirecttopage">' + val.author.firstName + ' ' + val.author.lastName + '</a>');







                } else {



                    html.push('');



                }







            }











            var date = '';



            var time = '';



            if (val.hasOwnProperty("createdAt")) {







                try {



                    date = val.createdAt.toDate().toDateString();



                    time = val.createdAt.toDate().toLocaleTimeString('en-US');



                } catch (err) {







                }



                html.push('<td class="dt-time">' + date + ' ' + time + '</td>');



            } else {



                html.push('');



            }



            html.push('<td class="text-green">' + val.amount + '</td>');



            if (val.hasOwnProperty('takeAway') && val.takeAway) {



                html.push('<td>{{ trans('lang.order_takeaway') }}</td>');



            } else {



                html.push('<td>{{ trans('lang.order_delivery') }}</td>');



            }











            if (val.status == 'Order Placed') {



                html.push('<td class="order_placed"><span>' + val.status + '</span></td>');







            } else if (val.status == 'Order Accepted') {



                html.push('<td class="order_accepted"><span>' + val.status + '</span></td>');







            } else if (val.status == 'Order Rejected') {



                html.push('<td class="order_rejected"><span>' + val.status + '</span></td>');







            } else if (val.status == 'Driver Pending') {



                html.push('<td class="driver_pending"><span>' + val.status + '</span></td>');







            } else if (val.status == 'Driver Rejected') {



                html.push('<td class="driver_rejected"><span>' + val.status + '</span></td>');







            } else if (val.status == 'Order Shipped') {



                html.push('<td class="order_shipped"><span>' + val.status + '</span></td>');







            } else if (val.status == 'In Transit') {



                html.push('<td class="in_transit"><span>' + val.status + '</span></td>');







            } else if (val.status == 'Order Completed') {



                html.push('<td class="order_completed"><span>' + val.status + '</span></td>');







            } else {



                html.push('<td class="order_completed"><span>' + val.status + '</span></td>');







            }



            var actionHtml = '';



            actionHtml = actionHtml + '<span class="action-btn"><?php if (in_array('orders.print', json_decode(@session('user_permissions')))) { ?><a href="' + printRoute + '"><i class="mdi mdi-printer" style="font-size:20px;"></i></a><?php } ?><a href="' + route1 + '"><i class="mdi mdi-lead-pencil"></i></a> ';



            if (checkDeletePermission) {



                actionHtml = actionHtml + '<a id="' + val.id + '" class="delete-btn" name="order-delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a>';



            }



            actionHtml = actionHtml + '</span>';



            html.push(actionHtml);







            return html;



        }







        $("#is_active").click(function() {



            $("#orderTable .is_open").prop('checked', $(this).prop('checked'));







        });







        $("#deleteAll").click(function() {



            if ($('#orderTable .is_open:checked').length) {



                if (confirm("{{ trans('lang.selected_delete_alert') }}")) {



                    jQuery("#data-table_processing").show();



                    $('#orderTable .is_open:checked').each(function() {



                        var dataId = $(this).attr('dataId');



                        database.collection('vendor_orders').doc(dataId).delete().then(function() {



                            setTimeout(function() {



                                window.location.reload();



                            }, 5000);



                        });







                    });







                }



            } else {



                alert("{{ trans('lang.select_delete_alert') }}");



            }



        });







        async function getSectionName(sectionId) {



            var sectionName = '';



            await database.collection('sections').where("id", "==", sectionId).get().then(async function(snapshots) {







                if (snapshots.docs.length > 0) {



                    var data = snapshots.docs[0].data();



                    sectionName = data.name;







                }



            });



            return sectionName;



        }







        async function getStoreNameFunction(vendorId) {



            var vendorName = '';



            await database.collection('vendors').where('id', '==', vendorId).get().then(async function(snapshots) {



                var vendorData = snapshots.docs[0].data();







                vendorName = vendorData.title;



                $('.orderTitle').html("{{ trans('lang.order_plural') }} - " + vendorName);







                if (vendorData.dine_in_active == true) {



                    $(".dine_in_future").show();



                }

                var wallet_route = "{{ route('users.walletstransaction', 'id') }}";



                $(".wallet_transaction_vendor").attr("href", wallet_route.replace('id', 'storeID=' + vendorData.author));









            });







            return vendorName;







        }











        async function getUserNameFunction(userId) {



            var userName = '';



            await database.collection('users').where('id', '==', userId).get().then(async function(snapshots) {







                var userData = snapshots.docs[0].data();







                userName = userData.firstName + " " + userData.lastName;



                $('.orderTitle').html('{{ trans('lang.order_plural') }} - ' + userName);







            });







            return userName;







        }



        $(document).on("click", "a[name='order-delete']", function(e) {







            var id = this.id;



            jQuery("#data-table_processing").show();



            database.collection('vendor_orders').doc(id).delete().then(function(result) {







                window.location.href = '{{ url()->current() }}';



            });







        });







        function clickpage(value) {



            setCookie('pagesizes', value, 30);



            location.reload();



        }







        function buildHTMLProductstotal(snapshotsProducts) {







            var adminCommission = snapshotsProducts.adminCommission;



            var discount = snapshotsProducts.discount;



            var couponCode = snapshotsProducts.couponCode;



            var extras = snapshotsProducts.extras;



            var extras_price = snapshotsProducts.extras_price;



            var rejectedByDrivers = snapshotsProducts.rejectedByDrivers;



            var takeAway = snapshotsProducts.takeAway;



            var tip_amount = snapshotsProducts.tip_amount;



            var status = snapshotsProducts.status;



            var products = snapshotsProducts.products;



            var deliveryCharge = snapshotsProducts.deliveryCharge;



            var totalProductPrice = 0;



            var total_price = 0;







            var intRegex = /^\d+$/;



            var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;







            if (products) {







                products.forEach((product) => {







                    var val = product;







                    price_item = parseFloat(val.price).toFixed(decimal_degits);







                    extras_price_item = (parseFloat(val.extras_price) * parseInt(val.quantity)).toFixed(decimal_degits);







                    totalProductPrice = parseFloat(price_item) * parseInt(val.quantity);



                    var extras_price = 0;



                    if (parseFloat(extras_price_item) != NaN && val.extras_price != undefined) {



                        extras_price = extras_price_item;



                    }



                    totalProductPrice = parseFloat(extras_price) + parseFloat(totalProductPrice);



                    totalProductPrice = parseFloat(totalProductPrice).toFixed(decimal_degits);







                    total_price += parseFloat(totalProductPrice);







                });



            }







            if (intRegex.test(discount) || floatRegex.test(discount)) {







                discount = parseFloat(discount).toFixed(decimal_degits);



                total_price -= parseFloat(discount);







                if (currencyAtRight) {



                    discount_val = discount + "" + currentCurrency;



                } else {



                    discount_val = currentCurrency + "" + discount;



                }







            }







            var tax = 0;



            taxlabel = '';



            taxlabeltype = '';







            if (snapshotsProducts.hasOwnProperty('taxSetting')) {



                var total_tax_amount = 0;



                for (var i = 0; i < snapshotsProducts.taxSetting.length; i++) {



                    var data = snapshotsProducts.taxSetting[i];







                    if (data.type && data.tax) {



                        if (data.type == "percentage") {



                            tax = (data.tax * total_price) / 100;



                            taxlabeltype = "%";



                        } else {



                            tax = data.tax;



                            taxlabeltype = "fix";



                        }



                        taxlabel = data.title;



                    }



                    total_tax_amount += parseFloat(tax);



                }



                total_price = parseFloat(total_price) + parseFloat(total_tax_amount);



            }







            if ((intRegex.test(deliveryCharge) || floatRegex.test(deliveryCharge)) && !isNaN(deliveryCharge)) {







                deliveryCharge = parseFloat(deliveryCharge).toFixed(decimal_degits);



                total_price += parseFloat(deliveryCharge);







                if (currencyAtRight) {



                    deliveryCharge_val = deliveryCharge + "" + currentCurrency;



                } else {



                    deliveryCharge_val = currentCurrency + "" + deliveryCharge;



                }



            }











            if (intRegex.test(tip_amount) || floatRegex.test(tip_amount) && !isNaN(tip_amount)) {







                tip_amount = parseFloat(tip_amount).toFixed(decimal_degits);



                total_price += parseFloat(tip_amount);



                total_price = parseFloat(total_price).toFixed(decimal_degits);







                if (currencyAtRight) {



                    tip_amount_val = tip_amount + "" + currentCurrency;



                } else {



                    tip_amount_val = currentCurrency + "" + tip_amount;



                }



            }











            if (currencyAtRight) {



                var total_price_val = parseFloat(total_price).toFixed(decimal_degits) + "" + currentCurrency;



            } else {



                var total_price_val = currentCurrency + "" + parseFloat(total_price).toFixed(decimal_degits);



            }







            return total_price_val;



        }







        function clickLink(value) {



            setCookie('section_id', value, 30);



            location.reload();



        }
    </script>
@endsection
