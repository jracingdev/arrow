@extends('layouts.app')

@section('content')

    <div id="main-wrapper" class="page-wrapper" style="min-height: 207px;">

        <div class="row cat-slider mb-4 mt-3" id="sections">

        </div>

        <div class="container-fluid">
            <div class="card mb-3 business-analytics">

                <div class="card-body">

                    <div class="row flex-between align-items-center g-2 mb-3 order_stats_header">
                        <div class="col-sm-6">
                            <h4 class="d-flex align-items-center text-capitalize gap-10 mb-0">{{trans('lang.dashboard_business_analytics')}}</h4>
                        </div>
                    </div>

                    <div class="row business-analytics_list">

                        <div class="col-sm-6 col-lg-3 mb-3">
                            <div class="card card-box-with-icon bg--8" onclick="location.href='{!! route('providerpayments') !!}'">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div class="card-box-with-content">
                                        <h2 class="text-dark-2 mb-1 h4 earnings_count" id="earnings_count"></h2>
                                        <p class="mb-0 small text-dark-2">{{trans('lang.dashboard_total_earnings')}}</p>
                                    </div>
                                
                                    <span class="box-icon ab">
                                        <img src="{{asset('images/total_earning.png')}}"/>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3 mb-3">
                            <div class="card card-box-with-icon bg--1" onclick="location.href='{!! url('ondemand-bookings') !!}'">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div class="card-box-with-content">
                                        <h2 class="text-dark-2 mb-1 h4 booking_count" id="booking_count"></h2>
                                        <p class="mb-0 small text-dark-2">{{trans('lang.dashboard_total_bookings')}}</p>
                                    </div>
                                
                                    <span class="box-icon ab">
                                        <img src="{{asset('images/dbooking_total.png')}}"/>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3 mb-3">
                            <div class="card card-box-with-icon bg--5" onclick="location.href='{!! url('ondemand-services') !!}'">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div class="card-box-with-content">
                                        <h2 class="text-dark-2 mb-1 h4 service_count" id="service_count"></h2>
                                        <p class="mb-0 small text-dark-2">{{trans('lang.dashboard_total_service')}}</p>
                                    </div>
                                
                                    <span class="box-icon ab">
                                        <img src="{{asset('images/dservices_total.png')}}"/>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3 mb-3">
                            <div class="card card-box-with-icon bg--6" onclick="location.href='{!! url('ondemand-workers') !!}'">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div class="card-box-with-content">
                                        <h2 class="text-dark-2 mb-1 h4 worker_count" id="worker_count"></h2>
                                        <p class="mb-0 small text-dark-2">{{trans('lang.dashboard_total_worker')}}</p>
                                    </div>
                                
                                    <span class="box-icon ab">
                                        <img src="{{asset('images/dworker_total.png')}}"/>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3 mb-3">
                            <div class="card card-box-with-icon bg--14" onclick="location.href='{!! url('providerpayments') !!}'">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div class="card-box-with-content">
                                        <h2 class="text-dark-2 mb-1 h4 admincommission_count" id="admincommission_count"></h2>
                                        <p class="mb-0 small text-dark-2">{{trans('lang.admin_commission')}}</p>
                                    </div>
                                
                                    <span class="box-icon ab">
                                        <img src="{{asset('images/total_payment.png')}}"/>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3 mb-3">
                            <div class="card card-box-with-icon bg--15" onclick="location.href='{!! url('providers') !!}'">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div class="card-box-with-content">
                                        <h2 class="text-dark-2 mb-1 h4 provider_count" id="provider_count"></h2>
                                        <p class="mb-0 small text-dark-2">{{trans('lang.dashboard_total_providers')}}</p>
                                    </div>
                                
                                    <span class="box-icon ab">
                                        <img src="{{asset('images/dprovider_total.png')}}"/>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3 mb-3">

                        </div>
                        <div class="col-sm-6 col-lg-3 mb-3">

                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <a class="order-status pending" href="{!!url('ondemand-bookings?status=order-placed') !!}">
                                <div class="data">
                                    <i class="mdi mdi-lan-pending"></i>
                                    <h6 class="status">{{trans('lang.dashboard_ondemand_order_placed')}}</h6>
                                </div>
                                <span class="count" id="placed_count"></span> </a>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <a class="order-status confirmed" href="{!! url('ondemand-bookings') !!}">
                                <div class="data">
                                    <i class="mdi mdi-check-circle"></i>
                                    <h6 class="status">{{trans('lang.dashboard_ondemand_order_accepted')}}</h6>
                                </div>
                                <span class="count" id="accepted_count"></span> </a>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <a class="order-status packaging" href="{!! url('ondemand-bookings') !!}">
                                <div class="data">
                                    <i class="mdi mdi-arrow-right-bold-circle-outline"></i>
                                    <h6 class="status">{{trans('lang.dashboard_ondemand_order_assigned')}}</h6>
                                </div>
                                <span class="count" id="assigned_count"></span> </a>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <a class="order-status delivered" href="{!! url('ondemand-bookings?status=order-ongoing') !!}">
                                <div class="data">
                                    <i class="mdi mdi-arrow-down-bold-circle-outline"></i>
                                    <h6 class="status">{{trans('lang.dashboard_ondemand_order_ongoing')}}</h6>
                                </div>
                                <span class="count" id="ongoing_count"></span>
                            </a>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <a class="order-status canceled" href="{!! url('ondemand-bookings?status=order-completed') !!}">
                                <div class="data">
                                    <i class="mdi mdi-check-circle-outline"></i>
                                    <h6 class="status">{{trans('lang.dashboard_ondemand_order_completed')}}</h6>
                                </div>
                                <span class="count" id="completed_count"></span>
                            </a>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <a class="order-status failed" href="{!! url('ondemand-bookings?status=order-canceled') !!}">
                                <div class="data">
                                    <i class="mdi mdi-window-close"></i>
                                    <h6 class="status">{{trans('lang.dashboard_ondemand_order_canceled')}}</h6>
                                </div>
                                <span class="count" id="canceled_count"></span>
                            </a>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <a class="order-status today" href="{!! url('ondemand-bookings?status=order-today') !!}">
                                <div class="data">
                                    <i class="mdi mdi-calendar-today"></i>
                                    <h6 class="status">{{trans('lang.dashboard_ondemand_order_today')}}</h6>
                                </div>
                                <span class="count" id="today_count"></span>
                            </a>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <a class="order-status upcoming" href="{!! url('ondemand-bookings?status=order-upcoming') !!}">
                                <div class="data">
                                    <i class="mdi mdi-calendar-clock"></i>
                                    <h6 class="status">{{trans('lang.dashboard_ondemand_order_upcoming')}}</h6>
                                </div>
                                <span class="count" id="upcoming_count"></span>
                            </a>
                        </div>

                    </div>

                </div>

            </div>

            <div class="row">

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header no-border">
                            <div class="d-flex justify-content-between">
                                <h3 class="card-title">{{trans('lang.total_sales')}}</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="position-relative">
                                <canvas id="sales-chart" height="200"></canvas>
                            </div>

                            <div class="d-flex flex-row justify-content-end">
                                <span class="mr-2"> <i class="fa fa-square" style="color:#2EC7D9"></i> {{trans('lang.dashboard_this_year')}} </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header no-border">
                            <div class="d-flex justify-content-between">
                                <h3 class="card-title">{{trans('lang.service_overview')}}</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="flex-row">
                                <canvas id="visitors" height="222"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header no-border">
                            <div class="d-flex justify-content-between">
                                <h3 class="card-title">{{trans('lang.sales_overview')}}</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="flex-row">
                                <canvas id="commissions" height="222"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row daes-sec-sec mb-3">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header no-border d-flex justify-content-between">
                            <h3 class="card-title">{{trans('lang.dashboard_ondemand_top_services')}}</h3>
                            <div class="card-tools">
                                <a href="{{url('ondemand-services')}}" class="btn btn-tool btn-sm"><i class="fa fa-bars"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-2">
                        <div class="table-responsive px-3">  
                            <table class="table table-striped table-valign-middle" id="servicesTable">
                                <thead>
                                <tr>
                                    <th style="text-align:center">{{trans('lang.vendor_image')}}</th>
                                    <th>{{trans('lang.service')}}</th>
                                    <th>{{trans('lang.tab_reviews')}}</th>
                                    <th>{{trans('lang.actions')}}</th>
                                </tr>
                                </thead>
                                <tbody id="append_list_services">

                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header no-border d-flex justify-content-between">
                            <h3 class="card-title">{{trans('lang.dashboard_ondemand_top_providers')}}</h3>
                            <div class="card-tools">
                                <a href="{{url('providers')}}" class="btn btn-tool btn-sm"><i class="fa fa-bars"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-2">
                        <div class="table-responsive px-3">  
                            <table class="table table-striped table-valign-middle" id="providersTable">
                                <thead>
                                <tr>
                                    <th style="text-align:center">{{trans('lang.vendor_image')}}</th>
                                    <th>{{trans('lang.provider')}}</th>
                                    <th>{{trans('lang.services')}}</th>
                                    <th>{{trans('lang.actions')}}</th>
                                </tr>
                                </thead>
                                <tbody id="append_list_top_providers">

                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row daes-sec-sec">

                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header no-border d-flex justify-content-between">
                            <h3 class="card-title">{{trans('lang.dashboard_ondemand_recent_orders')}}</h3>
                            <div class="card-tools">
                                <a href="{{url('ondemand-bookings')}}" class="btn btn-tool btn-sm"><i class="fa fa-bars"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-2">
                        <div class="table-responsive px-3">  
                            <table class="table table-striped table-valign-middle" id="orderTable">
                                <thead>
                                <tr>
                                    <th style="text-align:center">{{trans('lang.order_id')}}</th>
                                    <th>{{trans('lang.order_user_id')}}</th>
                                    <th>{{trans('lang.service')}}</th>
                                    <th>{{trans('lang.provider')}}</th>
                                    <th>{{trans('lang.total_amount')}}</th>
                                    <th>{{trans('lang.quantity')}}</th>
                                    <th>{{trans('lang.booking_date')}}</th>
                                    <th>{{trans('lang.status')}}</th>
                                </tr>
                                </thead>
                                <tbody id="append_list_recent_order">

                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>

        </div>

    </div>

@endsection

@section('scripts')

    <script src="{{asset('js/chart.js')}}"></script>
    <script src="{{asset('js/highcharts.js')}}"></script>

    <script>

        var active_id = "<?php echo @$_REQUEST['id'] ?>";
        setCookie('ondemand_section_id', active_id, 30);

        var active_type = "<?php echo @$_REQUEST['type'] ?>";
        var db = firebase.firestore();
        var currency = db.collection('settings');

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

        var placeholderImage = '';    
        var placeholder = db.collection('settings').doc('placeHolderImage');
        placeholder.get().then(async function (snapshotsimage) {
            var placeholderImageData = snapshotsimage.data();
            placeholderImage = placeholderImageData.image;
        })

        $(document).ready(function () {
            var  currentDateTime = new Date();
            var startOfToday = new Date(currentDateTime);
            startOfToday.setHours(0, 0, 0, 0);
            var endOfToday = new Date(currentDateTime);
            endOfToday.setHours(23, 59, 59, 999);
            var startTimestamp = firebase.firestore.Timestamp.fromDate(startOfToday);
            var endTimestamp = firebase.firestore.Timestamp.fromDate(endOfToday);

            jQuery("#data-table_processing").show();
            getSections();

            db.collection('provider_orders').where('sectionId', '==', active_id).get().then(
                (snapshot) => {
                    jQuery("#booking_count").empty();
                    jQuery("#booking_count").text(snapshot.docs.length);
                });

            db.collection('providers_services').where('sectionId', '==', active_id).get().then(
                (snapshot) => {
                    jQuery("#service_count").empty();
                    jQuery("#service_count").text(snapshot.docs.length);
                });

            db.collection('providers_workers').get().then((snapshot) => {
                jQuery("#worker_count").empty();
                jQuery("#worker_count").append(snapshot.docs.length);
            });

            db.collection('users').where("role", "==", "provider").get().then((snapshot) => {
                jQuery("#provider_count").empty();
                jQuery("#provider_count").append(snapshot.docs.length);
            });

            getTotalEarnings();
            setTimeout(function(){
                setVisitors();
            },1000);

            db.collection('provider_orders').where('status', '==', "Order Placed").where('sectionId', '==', active_id).get().then(
                (snapshot) => {
                    jQuery("#placed_count").empty();
                    jQuery("#placed_count").text(snapshot.docs.length);
                });

            db.collection('provider_orders').where('status', '==', "Order Accepted").where('sectionId', '==', active_id).get().then(
                (snapshot) => {
                    jQuery("#accepted_count").empty();
                    jQuery("#accepted_count").text(snapshot.docs.length);
                });

            db.collection('provider_orders').where('status', '==', "Order Assigned").where('sectionId', '==', active_id).get().then(
                (snapshot) => {
                    jQuery("#assigned_count").empty();
                    jQuery("#assigned_count").text(snapshot.docs.length);
                });

            db.collection('provider_orders').where('status', '==', "Order Ongoing").where('newScheduleDateTime', '>=',startTimestamp).where('newScheduleDateTime', '<=',endTimestamp).where('sectionId', '==', active_id).get().then(
                (snapshot) => {
                    jQuery("#ongoing_count").empty();
                    jQuery("#ongoing_count").text(snapshot.docs.length);
                });

            db.collection('provider_orders').where('status', '==', "Order Completed").where('sectionId', '==', active_id).get().then(
                (snapshot) => {
                    jQuery("#completed_count").empty();
                    jQuery("#completed_count").text(snapshot.docs.length);
                });

            db.collection('provider_orders').where('status', 'in', ["Order Rejected","Order Cancelled"]).where('sectionId', '==', active_id).get().then(
                (snapshot) => {
                    jQuery("#canceled_count").empty();
                    jQuery("#canceled_count").text(snapshot.docs.length);
                });
            db.collection('provider_orders').where('newScheduleDateTime', '>=',startTimestamp).where('newScheduleDateTime', '<=',endTimestamp).where('status','in',['Order Accepted','Order Assigned','Order Ongoing']).where('sectionId', '==', active_id).get().then(
                (snapshot) => {
                    jQuery("#today_count").empty();
                    jQuery("#today_count").text(snapshot.docs.length);
                });
            db.collection('provider_orders').where('status','in',['Order Accepted','Order Assigned']).where('newScheduleDateTime', '>=',endTimestamp).where('sectionId', '==', active_id).get().then(
                (snapshot) => {
                    jQuery("#upcoming_count").empty();
                    jQuery("#upcoming_count").text(snapshot.docs.length);
                });

            var offest = 1;
            var pagesize = 10;
            var start = null;
            var end = null;
            var endarray = [];
            var inx = parseInt(offest) * parseInt(pagesize);
            var append_list_services = document.getElementById('append_list_services');
            
            let ref = db.collection('providers_services').where('sectionId', '==', active_id);
            ref.orderBy('reviewsCount', 'desc').limit(inx).get().then((snapshots) => {
                html = '';
                html = buildServicesHTML(snapshots);
                if (html != '') {
                    append_list_services.innerHTML = html;
                    start = snapshots.docs[snapshots.docs.length - 1];
                    endarray.push(snapshots.docs[0]);
                }
                $('#servicesTable').DataTable({
                    order: [],
                    columnDefs: [
                        {orderable: false, targets: [0, 2, 3]},
                    ],
                    "language": {
                        "zeroRecords": "{{trans("lang.no_record_found")}}",
                        "emptyTable": "{{trans("lang.no_record_found")}}"
                    },
                    responsive: true,
                    paging: false,
                    info: false
                });
            });

            var offest = 1;
            var pagesize = 10;
            var start = null;
            var end = null;
            var endarray = [];
            var inx = parseInt(offest) * parseInt(pagesize);
            var append_list_recent_order = document.getElementById('append_list_recent_order');

            ref = db.collection('provider_orders').where('sectionId', '==', active_id);
            ref.orderBy('createdAt', 'desc').where('status', 'in', ["Order Placed", "Order Accepted", "Order Assigned", "Order Ongoing", "Order Completed", "Order Cancelled"]).limit(inx).get().then((snapshots) => {
                html = '';
                html = buildOrderHTML(snapshots);
                if (html != '') {
                    append_list_recent_order.innerHTML = html;
                    start = snapshots.docs[snapshots.docs.length - 1];
                    endarray.push(snapshots.docs[0]);
                }

                $('#orderTable').DataTable({
                    order: [],
                    columnDefs: [
                        {
                            targets: 6,
                            type: 'date',
                            render: function (data) {

                                return data;
                            }
                        },
                    ],
                    order: [['3', 'desc']],
                    "language": {
                        "zeroRecords": "{{trans("lang.no_record_found")}}",
                        "emptyTable": "{{trans("lang.no_record_found")}}"
                    },
                    responsive: true,
                    paging: false,
                    info: false
                });
            });

            var offest = 1;
            var pagesize = 10;
            var start = null;
            var end = null;
            var endarray = [];
            var inx = parseInt(offest) * parseInt(pagesize);
            var append_list_top_providers = document.getElementById('append_list_top_providers');
            append_list_top_providers.innerHTML = '';

            ref = db.collection('users');
            ref.where('role', '==', 'provider').orderBy('firstName', 'asc').limit(inx).get().then(async (snapshots) => {
                html = '';
                html = await buildProvidersHTML(snapshots);
                if (html != '') {
                    append_list_top_providers.innerHTML = html;
                    start = snapshots.docs[snapshots.docs.length - 1];
                    endarray.push(snapshots.docs[0]);
                    
                }
                $('#providersTable').DataTable({
                    order: [],
                    columnDefs: [
                        {orderable: false, targets: [0, 3]},
                        {targets: 2, type: "html-num-fmt"}
                    ],
                    "language": {
                        "zeroRecords": "{{trans("lang.no_record_found")}}",
                        "emptyTable": "{{trans("lang.no_record_found")}}"
                    },
                    responsive: true,
                    paging: false,
                    info: false
                });
            });
        })

        async function getTotalEarnings() {
            var intRegex = /^\d+$/;
            var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
            var v01 = 0;
            var v02 = 0;
            var v03 = 0;
            var v04 = 0;
            var v05 = 0;
            var v06 = 0;
            var v07 = 0;
            var v08 = 0;
            var v09 = 0;
            var v10 = 0;
            var v11 = 0;
            var v12 = 0;
            var currentYear = new Date().getFullYear();
            await db.collection('provider_orders').where('status', '==', "Order Completed").where('sectionId', '==', active_id).get().then(async function (orderSnapshots) {
                var paymentData = orderSnapshots.docs;
                var totalEarning = 0;
                var adminCommission = 0;
                paymentData.forEach((order) => {
                    var orderData = order.data();
                    var price = 0;
                    var minprice = 0;
                    var minprice = parseFloat(orderData.provider.price);

                    if (orderData.provider.disPrice != null && orderData.provider.disPrice != undefined && orderData.provider.disPrice != '' && orderData.provider.disPrice != '0') {
                        minprice = parseFloat(orderData.provider.disPrice)
                    }
                    var price=minprice;
                    minprice=parseFloat(orderData.quantity)*minprice;               
                   
                    discount = orderData.discount;
                    if ((intRegex.test(discount) || floatRegex.test(discount)) && !isNaN(discount)) {
                        discount = parseFloat(discount).toFixed(decimal_degits);
                        price = price - parseFloat(discount);
                        minprice = minprice - parseFloat(discount);
                    }

                    tax = 0;
                    totalTaxAmount=0;
                     totalTaxAmount=0;
                        if (orderData.hasOwnProperty('taxSetting') && orderData.taxSetting.length>0) {
                            for (var i = 0; i < orderData.taxSetting.length; i++) {
                                var data = orderData.taxSetting[i];
                                if (data.type && data.tax) {
                                    if (data.type == "percentage") {
                                        tax = (parseFloat(data.tax) * minprice) / 100;
                                    }else {
                                        tax = parseFloat(data.tax);
                                        }
                                    totalTaxAmount=totalTaxAmount+parseFloat(tax);     
                                }
                            }
                        }

                    if (!isNaN(totalTaxAmount)) {
                        price = price + totalTaxAmount;
                    }

                    if (orderData.adminCommission != undefined && orderData.adminCommissionType != undefined && orderData.adminCommission > 0 && price > 0) {
                        var commission = 0;
                        if (orderData.adminCommissionType == "percentage") {
                            commission = (price * parseFloat(orderData.adminCommission)) / 100;

                        } else {
                            commission = parseFloat(orderData.adminCommission);
                        }

                        adminCommission = commission + adminCommission;
                    } else if (orderData.adminCommission != undefined && orderData.adminCommission > 0 && price > 0) {
                        var commission = parseFloat(orderData.adminCommission);
                        adminCommission = commission + adminCommission;
                    }

                    totalEarning = parseFloat(totalEarning) + parseFloat(price);

                    try {

                        if (orderData.createdAt) {
                            var orderMonth = orderData.createdAt.toDate().getMonth() + 1;
                            var orderYear = orderData.createdAt.toDate().getFullYear();
                            if (currentYear == orderYear) {
                                switch (parseInt(orderMonth)) {
                                    case 1:
                                        v01 = parseInt(v01) + price;
                                        break;
                                    case 2:
                                        v02 = parseInt(v02) + price;
                                        break;
                                    case 3:
                                        v03 = parseInt(v03) + price;
                                        break;
                                    case 4:
                                        v04 = parseInt(v04) + price;
                                        break;
                                    case 5:
                                        v05 = parseInt(v05) + price;
                                        break;
                                    case 6:
                                        v06 = parseInt(v06) + price;
                                        break;
                                    case 7:
                                        v07 = parseInt(v07) + price;
                                        break;
                                    case 8:
                                        v08 = parseInt(v08) + price;
                                        break;
                                    case 9:
                                        v09 = parseInt(v09) + price;
                                        break;
                                    case 10:
                                        v10 = parseInt(v10) + price;
                                        break;
                                    case 11:
                                        v11 = parseInt(v11) + price;
                                        break;
                                    default :
                                        v12 = parseInt(v12) + price;
                                        break;
                                }
                            }
                        }

                    } catch (err) {


                        var datas = new Date(orderData.createdAt._seconds * 1000);

                        var dates = firebase.firestore.Timestamp.fromDate(datas);

                        db.collection('provider_orders').doc(orderData.id).update({'createdAt': dates}).then(() => {

                            console.log('Provided document has been updated in Firestore');

                        }, (error) => {

                            console.log('Error: ' + error);

                        });

                    }


                })

                if (currencyAtRight) {
                    totalEarning = parseFloat(totalEarning).toFixed(decimal_degits) + "" + currentCurrency;
                    adminCommission = parseFloat(adminCommission).toFixed(decimal_degits) + "" + currentCurrency;
                } else {
                    totalEarning = currentCurrency + "" + parseFloat(totalEarning).toFixed(decimal_degits);
                    adminCommission = currentCurrency + "" + parseFloat(adminCommission).toFixed(decimal_degits);
                }

                $("#earnings_count").append(totalEarning);
                $("#earnings_count_graph").append(totalEarning);
                $("#admincommission_count_graph").append(adminCommission);
                $("#admincommission_count").append(adminCommission);
                $("#total_earnings_header").text(totalEarning);
                $(".earnings_over_time").append(totalEarning);
                var data = [v01, v02, v03, v04, v05, v06, v07, v08, v09, v10, v11, v12];
                var labels = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
                var $salesChart = $('#sales-chart');
                var salesChart = renderChart($salesChart, data, labels);
                setCommision();
                jQuery("#data-table_processing").hide();
            })
            
        }

        function buildServicesHTML(snapshots) {
            var html = '';
            var count = 1;
            var rating = 0;
            snapshots.docs.forEach((listval) => {
                val = listval.data();
                
                val.id = listval.id;

                var route = '<?php echo url("ondemand-services/edit/{id}");?>';
                route = route.replace('{id}', val.id);

                html = html + '<tr>';
                if (val.photos.length == 0) {

                    html = html + '<td class="text-center"><img class="img-circle img-size-32 mr-2" style="width:60px;height:60px;" src="' + placeholderImage + '" alt="image"></td>';
                } else {
                    html = html + '<td class="text-center"><img class="img-circle img-size-32 mr-2" style="width:60px;height:60px;" src="' + val.photos[0] + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" alt="image"></td>';
                }

                html = html + '<td data-url="' + route + '" class="redirecttopage">' + val.title + '</td>';
                

                if (val.hasOwnProperty('reviewsCount') && val.reviewsCount != 0) {
                    rating = Math.round(parseFloat(val.reviewsSum) / parseInt(val.reviewsCount));
                } else {
                    rating = 0;
                }

                html = html + '<td><ul class="rating" data-rating="' + rating + '">';
                html = html + '<li class="rating__item"></li>';
                html = html + '<li class="rating__item"></li>';
                html = html + '<li class="rating__item"></li>';
                html = html + '<li class="rating__item"></li>';
                html = html + '<li class="rating__item"></li>';
                html = html + '</ul></td>';
                html = html + '<td><a href="' + route + '" > <span class="mdi mdi-lead-pencil"></span></a></td>';
                html = html + '</tr>';

                rating = 0;
                count++;
            });
            return html;
        }

       async function getProviderServices(providerId) {
            var total = 0;
           await database.collection('providers_services').where('author', '==',providerId).get().then(async function (snapshots) {
                
                total = snapshots.docs.length;
            });
            return total;
        }
    async function buildProvidersHTML(snapshots) {

        var html = '';
        await Promise.all(snapshots.docs.map(async (listval) => {
            var val = listval.data();
            var getData = await getListData(val);
            
            html += getData;
        }));
        return html;
    }
   async function getListData(val) {
            var html = '';
            var count = 1;

                var provider_route = '<?php echo url("providers/edit/{id}");?>';
                provider_route = provider_route.replace('{id}', val.id);

                html = html + '<tr>';
                if (val.profilePictureURL == '') {

                    html = html + '<td class="text-center"><img class="img-circle img-size-32 mr-2" style="width:60px;height:60px;" src="' + placeholderImage + '" alt="image"></td>';
                } else {
                    html = html + '<td class="text-center"><img class="img-circle img-size-32 mr-2" style="width:60px;height:60px;" src="' + val.profilePictureURL + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" alt="image"></td>';
                }
                
                html = html + '<td data-url="' + provider_route + '" class="redirecttopage">' + val.firstName + ' ' + val.lastName + '</td>';

                serviceTotal=await getProviderServices(val.id);     

                html = html + '<td data-url="' + provider_route + '" class="total_services_'+val.id+' redirecttopage">'+serviceTotal+'</td>';

                html = html + '<td data-url="' + provider_route + '" class="redirecttopage"><span class="mdi mdi-lead-pencil"></span></td>';
                html = html + '</tr>';
                count++;
            return html;
        }

        function buildOrderHTML(snapshots) {
            var html = '';
            var count = 1;
            snapshots.docs.forEach((listval) => {
                val = listval.data();
                val.id = listval.id;
                var route = '<?php echo url("ondemand-bookings/edit/{id}"); ?>';
                route = route.replace('{id}', val.id);

                var service_route = '<?php echo url("ondemand-services/edit/{id}");?>';
                service_route = service_route.replace('{id}', val.provider.id);

                var provider_route = '<?php echo url("providers/edit/{id}");?>';
                provider_route = provider_route.replace('{id}', val.provider.author);

                html = html + '<tr>';

                html = html + '<td data-url="' + route + '" class="redirecttopage">' + val.id + '</td>';

                html = html + '<td>' + val.author.firstName +' '+val.author.lastName+'</td>';

                var price = 0;
                if (val.deliveryCharge != undefined) {
                    price = parseInt(val.deliveryCharge) + price;
                }
                if (val.tip_amount != undefined) {
                    price = parseInt(val.tip_amount) + price;
                }

                html = html + '<td data-url="' + service_route + '" class="redirecttopage">' + val.provider.title + '</td>';

                html = html + '<td data-url="' + provider_route + '" class="redirecttopage">' + val.provider.authorName + '</td>';

                var price = buildHTMLProductstotal(val);

                html = html + '<td data-url="' + route + '" class="redirecttopage">' + price + '</td>';
                
                html = html + '<td data-url="' + route + '" class="redirecttopage"><i class="fa fa-shopping-cart"></i> ' + val.quantity + '</td>';

                var date = val.createdAt.toDate().toDateString();
                var time = val.createdAt.toDate().toLocaleTimeString('en-US');  
                html = html + '<td>' + date + ' ' + time + '</td>';
                
                if (val.status == 'Order Placed') {
                    html = html + '<td class="order_placed"><span>' + val.status + '</span></td>';

                }else if (val.status == 'Order Assigned') {
                    html = html + '<td class="order_assigned"><span>' + val.status + '</span></td>';
                }
                else if (val.status == 'Order Ongoing') {
                    html = html + '<td class="order_ongoing"><span>' + val.status + '</span></td>';

                }
                else if (val.status == 'Order Accepted') {
                    html = html + '<td class="order_accept"><span>' + val.status + '</span></td>';

                }else if (val.status == 'Order Rejected') {
                    html = html + '<td class="order_rejected"><span>' + val.status + '</span></td>';

                }else if (val.status == 'Order Completed') {
                    html = html + '<td class="order_completed"><span>' + val.status + '</span></td>';

                }
                else if (val.status == 'Order Cancelled') {
                    html = html + '<td class="order_rejected"><span>' + val.status + '</span></td>';
                }else{
                    html = html + '<td class="order_completed"><span>' + val.status + '</span></td>';
        
                }

                html = html + '</a></tr>';
                count++;
            });
            return html;
        }


        function renderChart(chartNode, data, labels) {
            var ticksStyle = {
                fontColor: '#495057',
                fontStyle: 'bold'
            };

            var mode = 'index';
            var intersect = true;
            return new Chart(chartNode, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            backgroundColor: '#2EC7D9',
                            borderColor: '#2EC7D9',
                            data: data
                        }
                    ]
                },
                options: {
                    maintainAspectRatio: false,
                    tooltips: {
                        mode: mode,
                        intersect: intersect,
                        callbacks: {
                            label: function (tooltipItems, data) {

                                if (currencyAtRight) {
                                    return (data.datasets[0].data[tooltipItems.index]).toFixed(decimal_degits) + currentCurrency;

                                } else {
                                    return currentCurrency + (data.datasets[0].data[tooltipItems.index]).toFixed(decimal_degits);

                                }
                            }
                        }
                    },
                    hover: {
                        mode: mode,
                        intersect: intersect
                    },
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            gridLines: {
                                display: true,
                                lineWidth: '4px',
                                color: 'rgba(0, 0, 0, .2)',
                                zeroLineColor: 'transparent'
                            },
                            ticks: $.extend({
                                beginAtZero: true,
                                callback: function (value, index, values) {
                                    if (currencyAtRight) {
                                        return value.toFixed(decimal_degits) + currentCurrency;

                                    } else {
                                        return currentCurrency + value.toFixed(decimal_degits);

                                    }
                                }


                            }, ticksStyle)
                        }],
                        xAxes: [{
                            display: true,
                            gridLines: {
                                display: false
                            },
                            ticks: ticksStyle
                        }]
                    }
                }
            })
        }

        $(document).ready(function () {
            $(document.body).on('click', '.redirecttopage', function () {
                var url = $(this).attr('data-url');
                window.location.href = url;
            });
        });


        function buildHTMLProductstotal(snapshotsProducts) {

            var adminCommission = snapshotsProducts.adminCommission;
            var discount = snapshotsProducts.discount;
            var couponCode = snapshotsProducts.couponCode;
            var status = snapshotsProducts.status;

            var totalProductPrice = 0;
            var total_price = 0;

            var intRegex = /^\d+$/;
            var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
            var total_price = parseFloat(val.provider.price);

            if (val.provider.disPrice != null && val.provider.disPrice != undefined && val.provider.disPrice != '' && val.provider.disPrice != '0') {
                total_price = parseFloat(val.provider.disPrice)
            }
            var price=total_price;
            total_price=parseFloat(val.quantity)*total_price;

            if (intRegex.test(discount) || floatRegex.test(discount)) {
                discount = parseFloat(discount).toFixed(decimal_degits);
                total_price -= parseFloat(discount);
                if (currencyAtRight) {
                    discount_val = discount + "" + currentCurrency;
                } else {
                    discount_val = currentCurrency + "" + discount;
                }
            }

            tax = 0;
            if(snapshotsProducts.hasOwnProperty('taxSetting')){
                if(snapshotsProducts.taxSetting.type && snapshotsProducts.taxSetting.tax){
                    if(snapshotsProducts.taxSetting.type=="percentage"){
                        tax=(snapshotsProducts.taxSetting.tax*total_price)/100;
                    }else{
                        tax=snapshotsProducts.taxSetting.tax;
                    }
                }
            }

            if(!isNaN(tax)){
                total_price = parseFloat(total_price) + parseFloat(tax);
            }

            if (currencyAtRight) {
                var total_price_val = total_price + "" + currentCurrency;
            } else {
                var total_price_val = currentCurrency + "" + total_price;
            }

            return total_price_val;
        }

        async function getSections() {
            var sections = database.collection('sections').where('isActive', '==', true);
        
            sections.get().then(async function (sectionsSnapshot) {
                sections = document.getElementById('sections');
                sections.innerHTML = '';
                sectionshtml = buildHTMLSections(sectionsSnapshot);
                sections.innerHTML = sectionshtml;
            })
        }

        function buildHTMLSections(sectionsSnapshot) {
            var html = '';
            var alldata = [];
            sectionsSnapshot.docs.forEach((listval) => {
                var datas = listval.data();
                datas.id = listval.id;
                alldata.push(datas);
            });

            var all_route = "{{ route('dashboard')}}";
            var img_url = "{{asset('images/shopping_cart.png')}}";
            var active_section = '';
            if (active_id == '') {
                active_section = 'section-selected';
            }
            html = html + '<div class="cat-item px-2 py-1 select_section ' + active_section + '"><a href="' + all_route + '" class="bg-white d-block p-2 text-center shadow-sm cat-link"><img alt="#" src="' + img_url + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" class="img-fluid mb-2"><p class="m-0 small">All</p></a></div>';

            alldata.forEach((listval) => {
                var val = listval;
                var section_id = val.id;

                if (val.sectionImage) {
                    photo = val.sectionImage;
                } else {
                    photo = placeholderImage;
                }

                var active_section = '';
                if (active_id != undefined && active_id == section_id) {
                    active_section = 'section-selected';
                }

                var section_route = "{{ route('dashboard')}}?id=" + val.id + "&type=" + val.serviceTypeFlag;

                html = html + '<div class="cat-item px-2 py-1 select_section ' + active_section + '"><a href="' + section_route + '" class="bg-white d-block p-2 text-center shadow-sm cat-link"><img alt="#" src="' + photo + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" class="img-fluid mb-2"><p class="m-0 small">' + val.name + '</p></a></div>';
            });
            return html;
        }

        function setVisitors() {

            const data = {
                labels: [
                    "{{trans('lang.dashboard_total_bookings')}}",
                    "{{trans('lang.dashboard_total_service')}}",
                    "{{trans('lang.dashboard_total_workers')}}",
                    "{{trans('lang.dashboard_total_providers')}}",
                ],
                datasets: [{
                    data: [jQuery("#booking_count").text(), jQuery("#service_count").text(), jQuery("#worker_count").text(), jQuery("#provider_count").text()],
                    backgroundColor: [
                        '#218be1',
                        '#B1DB6F',
                        '#7360ed',
                        '#FFAB2E',
                    ],
                    hoverOffset: 4
                }]
            };

            return new Chart('visitors', {
                type: 'doughnut',
                data: data,
                options: {
                    maintainAspectRatio: false,
                }
            })
        }

        function setCommision() {

            const data = {
                labels: [
                    "{{trans('lang.dashboard_total_earnings')}}",
                    "{{trans('lang.admin_commission')}}"
                ],
                datasets: [{
                    data: [jQuery("#earnings_count").text().replace(currentCurrency, ""), jQuery("#admincommission_count").text().replace(currentCurrency, "")],
                    backgroundColor: [
                        '#feb84d',
                        '#9b77f8',
                        '#fe95d3'
                    ],
                    hoverOffset: 4
                }]
            };
            return new Chart('commissions', {
                type: 'doughnut',
                data: data,
                options: {
                    maintainAspectRatio: false,
                    tooltips: {
                        callbacks: {
                            label: function (tooltipItems, data) {
                                if (currencyAtRight) {
                                    return data.labels[tooltipItems.index] + ': ' + (data.datasets[0].data[tooltipItems.index]) + currentCurrency;

                                } else {
                                    return data.labels[tooltipItems.index] + ': ' + currentCurrency + (data.datasets[0].data[tooltipItems.index]);

                                }
                            }
                        }
                    }
                }
            })
        }

    </script>
@endsection

