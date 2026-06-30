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
                        <div class="col-sm-6" id="intercity_enabled" style="display:none">
                            <h5 class="align-items-center text-capitalize gap-10 mb-0">
                                <i class="mdi mdi-check-circle"></i>
                                {{trans('lang.dashboard_intercity_enabled')}}
                            </h5>
                        </div>
                    </div>

                    <div class="row business-analytics_list">

                        <div class="col-sm-6 col-lg-3 mb-3">
                            <div class="card card-box-with-icon bg--8" onclick="location.href='{!! route('payments') !!}'">
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
                            <div class="card card-box-with-icon bg--1" onclick="location.href='{!! route('rides') !!}'">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div class="card-box-with-content">
                                            <h2 class="text-dark-2 mb-1 h4 total_rides" id="total_rides"></h2>
                                            <h6 id="total_rides_intercity"></h6>
                                            <p class="mb-0 small text-dark-2">{{trans('lang.dashboard_total_rides')}}</p>
                                        </div>
                                        <span class="box-icon ab">
                                            <img src="{{asset('images/total_rides.png')}}"/>
                                        </span>
                                    </div>
                                </div>
                        
                        </div>

                        <div class="col-sm-6 col-lg-3 mb-3">
                            <div class="card card-box-with-icon bg--6" onclick="location.href='{!! route('users') !!}'">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div class="card-box-with-content">
                                        <h2 class="text-dark-2 mb-1 h4 users_count" id="users_count"></h2>
                                        <p class="mb-0 small text-dark-2">{{trans('lang.dashboard_total_clients')}}</p>
                                    </div>
                                
                                    <span class="box-icon ab">
                                        <img src="{{asset('images/dcustomer.png')}}"/>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3 mb-3">
                            <div class="card card-box-with-icon bg--15" onclick="location.href='{!! route('drivers') !!}'">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div class="card-box-with-content">
                                        <h2 class="text-dark-2 mb-1 h4 driver_count" id="driver_count"></h2>
                                        <p class="mb-0 small text-dark-2">{{trans('lang.dashboard_total_drivers')}}</p>
                                    </div>
                                
                                    <span class="box-icon ab">
                                        <img src="{{asset('images/total_driver.png')}}"/>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3 mb-3">
                            <div class="card card-box-with-icon bg--14" onclick="location.href='{!! route('payments') !!}'">
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
                            <div class="card card-box-with-icon bg--11" onclick="location.href='{!! route('rides') !!}'">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div class="card-box-with-content">
                                        <h2 class="text-dark-2 mb-1 h4 confirmed_count" id="confirmed_count"></h2>
                                        <h6 id="confirmed_count_intercity"></h6>
                                        <p class="mb-0 small text-dark-2">{{trans('lang.dashboard_ride_confirmed')}}</p>
                                    </div>
                                
                                    <span class="box-icon ab">
                                        <img src="{{asset('images/dconfirmed_ride.png')}}"/>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3 mb-3">
                            <div class="card card-box-with-icon bg--24" onclick="location.href='{!! route('rides') !!}'">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div class="card-box-with-content">
                                        <h2 class="text-dark-2 mb-1 h4 completed_count" id="completed_count"></h2>
                                        <h6 id="completed_count_intercity"></h6>
                                        <p class="mb-0 small text-dark-2">{{trans('lang.dashboard_ride_completed')}}</p>
                                    </div>
                                
                                    <span class="box-icon ab">
                                        <img src="{{asset('images/dcompleted_ride.png')}}"/>
                                    </span>
                                </div>
                            </div>
                            
                        </div>

                        <div class="col-sm-6 col-lg-3 mb-3">
                        <div class="card card-box-with-icon bg--19" onclick="location.href='{!! route('rides') !!}'">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div class="card-box-with-content">
                                        <h2 class="text-dark-2 mb-1 h4 canceled_count" id="canceled_count"></h2>
                                        <h6 id="canceled_count_intercity"></h6>
                                        <p class="mb-0 small text-dark-2">{{trans('lang.dashboard_ride_canceled')}}</p>
                                    </div>
                                
                                    <span class="box-icon ab">
                                        <img src="{{asset('images/dcanceled_ride.png')}}"/>
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

            <div class="row daes-sec-sec">
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
                                <canvas id="visitors" height="200"></canvas>

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
                                <canvas id="commissions" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row daes-sec-sec pt-3">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header no-border d-flex justify-content-between">
                            <h3 class="card-title">{{trans('lang.recent_rides')}}</h3>
                            <div class="card-tools">
                                <a href="{{route('orders')}}" class="btn btn-tool btn-sm"><i class="fa fa-bars"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-2">
                        <div class="table-responsive px-3">  
                            <table class="table table-striped table-valign-middle" id="orderTable">
                                <thead>
                                <tr>
                                    <th style="text-align:center">{{trans('lang.ride_id')}}</th>
                                    <th>{{trans('lang.ridetype')}}</th>
                                    <th>{{trans('lang.dashboard_user')}}</th>
                                    <th>{{trans('lang.dashboard_driver')}}</th>
                                    <th>{{trans('lang.address')}}</th>
                                </tr>
                                </thead>
                                <tbody id="append_list_recent_rides">

                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header no-border d-flex justify-content-between">
                            <h3 class="card-title">{{trans('lang.top_drivers')}}</h3>
                            <div class="card-tools">
                                <a href="{{route('drivers')}}" class="btn btn-tool btn-sm"><i class="fa fa-bars"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-2">
                        <div class="table-responsive px-3">  
                            <table class="table table-striped table-valign-middle" id="driverTable">
                                <thead>
                                <tr>
                                    <th style="text-align:center">{{trans('lang.vendor_image')}}</th>
                                    <th>{{trans('lang.driver')}}</th>
                                    <th>{{trans('lang.ride_completed')}}</th>
                                    <th>{{trans('lang.actions')}}</th>
                                </tr>
                                </thead>
                                <tbody id="append_list_top_drivers">

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
        setCookie('section_id', active_id, 30);
        var active_type = "<?php echo @$_REQUEST['type'] ?>";
        var db = firebase.firestore();
        var currency = db.collection('settings');
        var intercity_enabled = false;

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

        async function getSectionData() {
            var section = await database.collection('sections').doc(active_id).get();
            var data = section.data();
            if (data.rideType == "intercity" || data.rideType == "both") {

                $("#intercity_enabled").show();

                db.collection('users').where('rideType', 'in', ['intercity', 'both']).where("role", "==", "driver").where('serviceType', '==', active_type).get().then((snapshot) => {
                    jQuery("#driver_count_intercity").empty();
                    jQuery("#driver_count_intercity").append("{{trans('lang.intercity')}} / " + snapshot.docs.length);
                });

                db.collection('rides').where('rideType', '==', 'intercity').get().then((snapshot) => {
                    jQuery("#total_rides_intercity").empty();
                    jQuery("#total_rides_intercity").text("{{trans('lang.intercity')}} / " + snapshot.docs.length);
                });

                db.collection('rides').where('rideType', '==', 'intercity').where('status', 'in', ["Order Placed", "Order Accepted", "In Transit"]).get().then((snapshot) => {
                    jQuery("#confirmed_count_intercity").empty();
                    jQuery("#confirmed_count_intercity").text("{{trans('lang.intercity')}} / " + snapshot.docs.length);
                });

                db.collection('rides').where('rideType', '==', 'intercity').where('status', 'in', ["Order Completed"]).get().then(
                    (snapshot) => {
                        jQuery("#completed_count_intercity").empty();
                        jQuery("#completed_count_intercity").text("{{trans('lang.intercity')}} / " + snapshot.docs.length);
                    });

                db.collection('rides').where('rideType', '==', 'intercity').where('status', 'in', ["Order Rejected"]).get().then(
                    (snapshot) => {
                        jQuery("#canceled_count_intercity").empty();
                        jQuery("#canceled_count_intercity").text("{{trans('lang.intercity')}} / " + snapshot.docs.length);
                    });

                getTotalEarningsIntercity();
            }
        }

        $(document).ready(function () {

            jQuery("#data-table_processing").show();
            getSectionData();

            getSections();

            db.collection('users').where("role", "==", "customer").orderBy('createdAt').get().then((snapshot) => {
                jQuery("#users_count").empty();
                jQuery("#users_count").append(snapshot.docs.length);
            });
 
            db.collection('users').where("role", "==", "driver").where('serviceType', '==', active_type).orderBy('createdAt').get().then((snapshot) => {
                jQuery("#driver_count").empty();
                jQuery("#driver_count").append(snapshot.docs.length);
            });

            db.collection('rides').where('rideType','==','ride').get().then((snapshot) => {
                jQuery("#total_rides").empty();
                jQuery("#total_rides").text(snapshot.docs.length);
            });

            db.collection('rides').where('status', 'in', ["Order Placed", "Order Accepted", "In Transit"]).get().then(
                (snapshot) => {
                    jQuery("#confirmed_count").empty();
                    jQuery("#confirmed_count").text(snapshot.docs.length);
                });

            db.collection('rides').where('status', 'in', ["Order Completed"]).get().then(
                (snapshot) => {
                    jQuery("#completed_count").empty();
                    jQuery("#completed_count").text(snapshot.docs.length);
                });

            db.collection('rides').where('status', 'in', ["Order Rejected"]).get().then(
                (snapshot) => {
                    jQuery("#canceled_count").empty();
                    jQuery("#canceled_count").text(snapshot.docs.length);
                    setVisitors();
                });

            getTotalEarnings();

            var offest = 1;
            var pagesize = 10;
            var start = null;
            var end = null;
            var endarray = [];
            var inx = parseInt(offest) * parseInt(pagesize);
            var append_list_recent_rides = document.getElementById('append_list_recent_rides');
            append_list_recent_rides.innerHTML = '';

            ref = db.collection('rides');
            ref.orderBy('createdAt', 'desc').where('status', 'in', ["Order Placed", "Order Accepted", "Driver Pending", "Driver Accepted", "Order Shipped", "In Transit"]).limit(inx).get().then((snapshots) => {
                html = '';
                html = buildRidesHTML(snapshots);
                if (html != '') {
                    append_list_recent_rides.innerHTML = html;
                    start = snapshots.docs[snapshots.docs.length - 1];
                    endarray.push(snapshots.docs[0]);
                }
                $('#orderTable').DataTable({
                    order: [],
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
            var pagesize = 5;
            var start = null;
            var end = null;
            var endarray = [];
            var inx = parseInt(offest) * parseInt(pagesize);
            var append_listtop_drivers = document.getElementById('append_list_top_drivers');
            append_listtop_drivers.innerHTML = '';

            ref = db.collection('users');
            ref.where('role', '==', 'driver').where('serviceType', '==', active_type).orderBy('orderCompleted', 'desc').limit(inx).get().then((snapshots) => {
                html = '';
                html = buildDriverHTML(snapshots);
                if (html != '') {
                    append_listtop_drivers.innerHTML = html;
                    start = snapshots.docs[snapshots.docs.length - 1];
                    endarray.push(snapshots.docs[0]);
                }
                $('#driverTable').DataTable({
                    order: [],
                    columnDefs: [
                        {orderable: false, targets: [0, 3]},
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
            await db.collection('rides').where('status', 'in', ["Order Completed"]).get().then(async function (orderSnapshots) {
                var paymentData = orderSnapshots.docs;
                var totalEarning = 0;
                var adminCommission = 0;
                paymentData.forEach((order) => {
                    var orderData = order.data();
                    var price = 0;
                    var minprice = 0;

                    price = orderData.subTotal;
                    minprice = orderData.subTotal;
                    discount = orderData.discount;

                    if ((intRegex.test(discount) || floatRegex.test(discount)) && !isNaN(discount)) {
                        discount = parseFloat(discount).toFixed(decimal_degits);
                        price = price - parseFloat(discount);
                        minprice = minprice - parseFloat(discount);
                    }

                    tax = 0;
                    if (orderData.taxType != undefined && orderData.tax != undefined) {
                        if (orderData.taxType == "percentage") {
                            tax = (parseFloat(orderData.tax) * minprice) / 100;
                        } else {
                            tax = parseFloat(orderData.tax);
                        }
                    }

                    if (!isNaN(tax)) {
                        price = price + tax;
                    }

                    if (orderData.deliveryCharge != undefined && orderData.deliveryCharge != "" && orderData.deliveryCharge > 0) {
                        price = price + parseFloat(orderData.deliveryCharge);
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

                        db.collection('vendor_orders').doc(orderData.id).update({'createdAt': dates}).then(() => {

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
            })
            jQuery("#data-table_processing").hide();

        }

        async function getTotalEarningsIntercity() {
            var intRegex = /^\d+$/;
            var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;

            await db.collection('rides').where('rideType', '==', 'intercity').where('status', 'in', ["Order Completed"]).get().then(async function (orderSnapshots) {
                var paymentData = orderSnapshots.docs;
                var totalEarning = 0;
                var adminCommission = 0;

                paymentData.forEach((order) => {
                    var orderData = order.data();
                    var price = 0;
                    var minprice = 0;

                    price = orderData.subTotal;
                    minprice = orderData.subTotal;
                    discount = orderData.discount;

                    if ((intRegex.test(discount) || floatRegex.test(discount)) && !isNaN(discount)) {
                        discount = parseFloat(discount).toFixed(decimal_degits);
                        price = price - parseFloat(discount);
                        minprice = minprice - parseFloat(discount);
                    }

                    tax = 0;
                    if (orderData.taxType != undefined && orderData.tax != undefined) {
                        if (orderData.taxType == "percentage") {
                            tax = (parseFloat(orderData.tax) * minprice) / 100;
                        } else {
                            tax = parseFloat(orderData.tax);
                        }
                    }

                    if (!isNaN(tax)) {
                        price = price + tax;
                    }

                    if (orderData.deliveryCharge != undefined && orderData.deliveryCharge != "" && orderData.deliveryCharge > 0) {
                        price = price + parseFloat(orderData.deliveryCharge);
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
                })

                if (currencyAtRight) {
                    totalEarning = parseFloat(totalEarning).toFixed(decimal_degits) + "" + currentCurrency;
                    adminCommission = parseFloat(adminCommission).toFixed(decimal_degits) + "" + currentCurrency;
                } else {
                    totalEarning = currentCurrency + "" + parseFloat(totalEarning).toFixed(decimal_degits);
                    adminCommission = currentCurrency + "" + parseFloat(adminCommission).toFixed(decimal_degits);
                }

                $("#earnings_count_intercity").append("{{trans('lang.intercity')}} / " + totalEarning);
                $("#admincommission_count_intercity").append("{{trans('lang.intercity')}} / " + adminCommission);
            })

        }

        function buildHTML(snapshots) {
            var html = '';
            var count = 1;
            var rating = 0;
            snapshots.docs.forEach((listval) => {
                val = listval.data();
                val.id = listval.id;
                var route = '<?php echo route("vendors.edit", ":id");?>';
                route = route.replace(':id', val.id);

                var routeview = '<?php echo route("vendors.view", ":id");?>';
                routeview = routeview.replace(':id', val.id);

                html = html + '<tr>';
                if (val.photo == '') {

                    html = html + '<td class="text-center"><img class="img-circle img-size-32 mr-2" style="width:60px;height:60px;" src="' + placeholderImage + '" alt="image"></td>';
                } else {
                    html = html + '<td class="text-center"><img class="img-circle img-size-32 mr-2" style="width:60px;height:60px;" src="' + val.photo + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" alt="image"></td>';
                }

                html = html + '<td data-url="' + routeview + '" class="redirecttopage">' + val.title + '</td>';

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


        function buildDriverHTML(snapshots) {
            var html = '';
            var count = 1;
            snapshots.docs.forEach((listval) => {
                val = listval.data();
                val.id = listval.id;
                var driverroute = '<?php echo route("drivers.edit", ":id");?>';
                driverroute = driverroute.replace(':id', val.id);
                var driverView = '<?php echo route("drivers.view", ":id");?>';
                driverView = driverView.replace(':id', val.id);

                html = html + '<tr>';
                if (val.profilePictureURL == '') {

                    html = html + '<td class="text-center"><img class="img-circle img-size-32 mr-2" style="width:60px;height:60px;" src="' + placeholderImage + '" alt="image"></td>';
                } else {
                    html = html + '<td class="text-center"><img class="img-circle img-size-32 mr-2" style="width:60px;height:60px;" src="' + val.profilePictureURL + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" alt="image"></td>';
                }
                html = html + '<td data-url="' + driverView + '" class="redirecttopage">' + val.firstName + ' ' + val.lastName + '</td>';
                html = html + '<td data-url="' + driverView + '" class="redirecttopage">' + val.orderCompleted + '</td>';
                html = html + '<td data-url="' + driverroute + '" class="redirecttopage"><span class="mdi mdi-lead-pencil"></span></td>';
                html = html + '</tr>';
                count++;
            });
            return html;
        }

        function buildRidesHTML(snapshots) {
            var html = '';
            var count = 1;
            snapshots.docs.forEach((listval) => {
                val = listval.data();
                val.id = listval.id;
                var route = '<?php echo route("rides.edit", ":id"); ?>';
                route = route.replace(':id', val.id);

                var user_id = val.author.id;
                var customer_view = '{{route("users.edit",":id")}}';
                customer_view = customer_view.replace(':id', user_id);
                var user_view = '{{route("users.view",":id")}}';
                user_view = user_view.replace(':id', user_id);

                html = html + '<tr>';

                html = html + '<td data-url="' + route + '" class="redirecttopage">' + val.id + '</td>';

                if (val.rideType != undefined) {
                    html = html + '<td>' + val.rideType + '</td>';
                } else {
                    html = html + '<td></td>';
                }

                if (val.hasOwnProperty("author")) {
                    html = html + '<td data-url="' + user_view + '" class="redirecttopage">' + val.author.firstName + '</td>';
                } else {
                    html = html + '<td></td>';
                }

                if (val.hasOwnProperty("driver")) {
                    var driverId = val.driver.id;
                    var diverRoute = '{{route("drivers.edit",":id")}}';
                    diverRoute = diverRoute.replace(':id', driverId);
                    var driverView = '{{route("drivers.view",":id")}}';
                    driverView = driverView.replace(':id', driverId);
                    html = html + '<td data-url="' + driverView + '" class="redirecttopage">' + val.driver.firstName + '</td>';
                } else {
                    html = html + '<td></td>';
                }

                html = html + '<td>' + val.destinationLocationName + '</td>';

                html = html + '</tr>';
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

            if (intRegex.test(deliveryCharge) || floatRegex.test(deliveryCharge)) {

                deliveryCharge = parseFloat(deliveryCharge).toFixed(decimal_degits);
                total_price += parseFloat(deliveryCharge);

                if (currencyAtRight) {
                    deliveryCharge_val = deliveryCharge + "" + currentCurrency;
                } else {
                    deliveryCharge_val = currentCurrency + "" + deliveryCharge;
                }
            }


            if (intRegex.test(tip_amount) || floatRegex.test(tip_amount)) {

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
            var active_section = ''
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
                    "{{trans('lang.dashboard_total_rides')}}",
                    "{{trans('lang.dashboard_total_clients')}}",
                    "{{trans('lang.dashboard_total_drivers')}}",
                    "{{trans('lang.dashboard_ride_confirmed')}}",
                    "{{trans('lang.dashboard_ride_completed')}}",
                    "{{trans('lang.dashboard_ride_canceled')}}",
                ],
                datasets: [{
                    data: [jQuery("#total_rides").text(), jQuery("#users_count").text(), jQuery("#driver_count").text(), jQuery("#confirmed_count").text(), jQuery("#completed_count").text(), jQuery("#canceled_count").text()],
                    backgroundColor: [
                        '#218be1',
                        '#B1DB6F',
                        '#7360ed',
                        '#FFAB2E',
                        '#FF683A',
                        '#000000',
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

