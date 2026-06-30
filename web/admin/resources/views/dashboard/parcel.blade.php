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

                        <div class="col-sm-6 col-lg-3 mb-3" onclick="location.href='{!! route('driver.driverpayments') !!}'">
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
                            <div class="card card-box-with-icon bg--5" onclick="location.href='{!! route('parcel_orders') !!}'">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div class="card-box-with-content">
                                        <h2 class="text-dark-2 mb-1 h4 order_count" id="order_count"></h2>
                                        <p class="mb-0 small text-dark-2">{{trans('lang.dashboard_total_orders')}}</p>
                                    </div>
                                
                                    <span class="box-icon ab">
                                        <img src="{{asset('images/dparcel.png')}}"/>
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

                        <div class="col-sm-6 col-lg-3 mb-3" onclick="location.href='{!! route('drivers') !!}'">
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

                        <div class="col-sm-6 col-lg-3 mb-3" onclick="location.href='{!! route('payments') !!}'">
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

                        </div>
                        <div class="col-sm-6 col-lg-3 mb-3">

                        </div>
                        <div class="col-sm-6 col-lg-3 mb-3">

                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <a class="order-status pending" href="{!! route('parcel_orders','status=order-placed') !!}">
                                <div class="data">
                                    <i class="mdi mdi-lan-pending"></i>
                                    <h6 class="status">{{trans('lang.dashboard_order_placed')}}</h6>
                                </div>
                                <span class="count" id="placed_count"></span> </a>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <a class="order-status confirmed"
                               href="{!! route('parcel_orders','status=order-confirmed') !!}">
                                <div class="data">
                                    <i class="mdi mdi-check-circle"></i>
                                    <h6 class="status">{{trans('lang.dashboard_order_confirmed')}}</h6>
                                </div>
                                <span class="count" id="confirmed_count"></span> </a>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <a class="order-status packaging"
                               href="{!! route('parcel_orders','status=order-shipped') !!}">
                                <div class="data">
                                    <i class="mdi mdi-clipboard-outline"></i>
                                    <h6 class="status">{{trans('lang.dashboard_order_shipped')}}</h6>
                                </div>
                                <span class="count" id="shipped_count"></span> </a>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <a class="order-status delivered"
                               href="{!! route('parcel_orders','status=order-completed') !!}">
                                <div class="data">
                                    <i class="mdi mdi-check-circle-outline"></i>
                                    <h6 class="status">{{trans('lang.dashboard_order_completed')}}</h6>
                                </div>
                                <span class="count" id="completed_count"></span>
                            </a>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <a class="order-status canceled"
                               href="{!! route('parcel_orders','status=order-canceled') !!}">
                                <div class="data">
                                    <i class="mdi mdi-window-close"></i>
                                    <h6 class="status">{{trans('lang.dashboard_order_canceled')}}</h6>
                                </div>
                                <span class="count" id="canceled_count"></span>
                            </a>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <a class="order-status failed" href="{!! route('parcel_orders','status=order-failed') !!}">
                                <div class="data">
                                    <i class="mdi mdi-alert-circle-outline"></i>
                                    <h6 class="status">{{trans('lang.dashboard_order_failed')}}</h6>
                                </div>
                                <span class="count" id="failed_count"></span>
                            </a>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <a class="order-status failed" href="{!! route('parcel_orders','status=order-pending') !!}">
                                <div class="data">
                                    <i class="mdi mdi-car-connected"></i>
                                    <h6 class="status">{{trans('lang.dashboard_order_pending')}}</h6>
                                </div>
                                <span class="count" id="pending_count"></span>
                            </a>
                        </div>


                    </div>

                </div>

            </div>

            <div class="row pt-3">

                <div class="col-lg-4 col-md-4">
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

                <div class="col-lg-4 col-md-4">
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

                <div class="col-lg-4 col-md-4">
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

            <div class="row daes-sec-sec pt-3">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header no-border d-flex justify-content-between">
                            <h3 class="card-title">{{trans('lang.recent_orders')}}</h3>
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
                                    <th style="text-align:center">{{trans('lang.order_id')}}</th>
                                    <th>{{trans('lang.dashboard_user')}}</th>
                                    <th>{{trans('lang.total_amount')}}</th>
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
                                    <th>{{trans('lang.order_completed')}}</th>
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

            jQuery("#data-table_processing").show();
            getSections();

            db.collection('parcel_orders').get().then((snapshot) => {
                jQuery("#order_count").empty();
                jQuery("#order_count").text(snapshot.docs.length);
            });

            db.collection('users').orderBy('createdAt').where("role", "==", "customer").get().then((snapshot) => {
                jQuery("#users_count").empty();
                jQuery("#users_count").append(snapshot.docs.length);
            });

            db.collection('users').orderBy('createdAt').where("role", "==", "driver").where('serviceType', '==', active_type).get().then((snapshot) => {
                jQuery("#driver_count").empty();
                jQuery("#driver_count").append(snapshot.docs.length);
                setVisitors();
            });

            getTotalEarnings();

            db.collection('parcel_orders').where('status', 'in', ["Order Placed"]).get().then(
                (snapshot) => {
                    jQuery("#placed_count").empty();
                    jQuery("#placed_count").text(snapshot.docs.length);
                });

            db.collection('parcel_orders').where('status', 'in', ["Order Accepted", "Driver Accepted"]).get().then(
                (snapshot) => {
                    jQuery("#confirmed_count").empty();
                    jQuery("#confirmed_count").text(snapshot.docs.length);
                });

            db.collection('parcel_orders').where('status', 'in', ["Order Shipped", "In Transit"]).get().then(
                (snapshot) => {
                    jQuery("#shipped_count").empty();
                    jQuery("#shipped_count").text(snapshot.docs.length);
                });

            db.collection('parcel_orders').where('status', 'in', ["Order Completed"]).get().then(
                (snapshot) => {
                    jQuery("#completed_count").empty();
                    jQuery("#completed_count").text(snapshot.docs.length);
                });

            db.collection('parcel_orders').where('status', 'in', ["Order Rejected"]).get().then(
                (snapshot) => {
                    jQuery("#canceled_count").empty();
                    jQuery("#canceled_count").text(snapshot.docs.length);
                });

            db.collection('parcel_orders').where('status', 'in', ["Driver Rejected"]).get().then(
                (snapshot) => {
                    jQuery("#failed_count").empty();
                    jQuery("#failed_count").text(snapshot.docs.length);
                });

            db.collection('parcel_orders').where('status', 'in', ["Driver Pending"]).get().then(
                (snapshot) => {
                    jQuery("#pending_count").empty();
                    jQuery("#pending_count").text(snapshot.docs.length);
                });

            var offest = 1;
            var pagesize = 10;
            var start = null;
            var end = null;
            var endarray = [];
            var inx = parseInt(offest) * parseInt(pagesize);
            var append_listrecent_order = document.getElementById('append_list_recent_order');
            append_listrecent_order.innerHTML = '';

            ref = db.collection('parcel_orders');
            ref.orderBy('createdAt', 'desc').where('status', 'in', ["Order Placed", "Order Accepted", "Driver Pending", "Driver Accepted", "Order Shipped", "In Transit"]).limit(inx).get().then((snapshots) => {
                html = '';
                html = buildOrderHTML(snapshots);
                if (html != '') {
                    append_listrecent_order.innerHTML = html;
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
            await db.collection('parcel_orders').where('status', 'in', ["Order Completed"]).get().then(async function (orderSnapshots) {
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
                html = html + '<td data-url="' + driverroute + '" class="redirecttopage">' + val.orderCompleted + '</td>';
                html = html + '<td data-url="' + driverroute + '" class="redirecttopage"><span class="mdi mdi-lead-pencil"></span></td>';
                html = html + '</tr>';
                count++;
            });
            return html;
        }

        function buildOrderHTML(snapshots) {
            var html = '';
            var count = 1;
            snapshots.docs.forEach((listval) => {
                val = listval.data();
                val.id = listval.id;
                var user_id = val.authorID;
                var route = '<?php echo route("parcel_orders.edit", ":id"); ?>';
                route = route.replace(':id', val.id);

                var vendorroute = '<?php echo route("vendors.view", ":id");?>';
                vendorroute = vendorroute.replace(':id', val.vendorID);

                var user_view = '<?php echo route("users.view", ":id");?>';
                user_view = user_view.replace(':id', user_id);
                var route1 = '<?php echo route("users.edit", ":id");?>';
                route1 = route1.replace(':id', user_id);

                html = html + '<tr>';

                html = html + '<td data-url="' + route + '" class="redirecttopage">' + val.id + '</td>';

                var price = 0;
                if (val.deliveryCharge != undefined) {
                    price = parseInt(val.deliveryCharge) + price;
                }
                if (val.tip_amount != undefined) {
                    price = parseInt(val.tip_amount) + price;
                }

                html = html + '<td data-url="' + user_view + '" class="redirecttopage">' + val.author.firstName + ' ' + val.author.lastName + '</td>';

                var price = buildParcelTotal(val);

                html = html + '<td data-url="' + route + '" class="redirecttopage">' + price + '</td>';
                if (val.status == 'Order Placed') {
                    html = html + '<td data-url="' + route + '" class="redirecttopage order_placed"><span>' + val.status + '</span></td>';

                } else if (val.status == 'Order Accepted') {
                    html = html + '<td data-url="' + route + '" class="redirecttopage order_accepted"><span>' + val.status + '</span></td>';

                } else if (val.status == 'Order Rejected') {
                    html = html + '<td data-url="' + route + '" class="redirecttopage order_rejected"><span>' + val.status + '</span></td>';

                } else if (val.status == 'Driver Pending') {
                    html = html + '<td data-url="' + route + '" class="redirecttopage driver_pending"><span>' + val.status + '</span></td>';

                } else if (val.status == 'Driver Rejected') {
                    html = html + '<td data-url="' + route + '" class="redirecttopage driver_rejected"><span>' + val.status + '</span></td>';

                } else if (val.status == 'Order Shipped') {
                    html = html + '<td data-url="' + route + '" class="redirecttopage order_shipped"><span>' + val.status + '</span></td>';

                } else if (val.status == 'In Transit') {
                    html = html + '<td data-url="' + route + '" class="redirecttopage in_transit"><span>' + val.status + '</span></td>';

                } else if (val.status == 'Order Completed') {
                    html = html + '<td data-url="' + route + '" class="redirecttopage order_completed"><span>' + val.status + '</span></td>';

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


        function buildParcelTotal(snapshotsProducts) {

            var adminCommission = snapshotsProducts.adminCommission;
            var adminCommissionType = snapshotsProducts.adminCommissionType;
            var discount = snapshotsProducts.discount;
            var subTotal = snapshotsProducts.subTotal;


            var total_price = subTotal;

            var intRegex = /^\d+$/;
            var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;

            if (intRegex.test(discount) || floatRegex.test(discount)) {

                discount = parseFloat(discount).toFixed(2);
                total_price -= parseFloat(discount);

            }

            var total_tax_amount = 0;

            if (snapshotsProducts.hasOwnProperty('taxSetting') && snapshotsProducts.taxSetting != '' && snapshotsProducts.taxSetting != null) {
                for (var i = 0; i < snapshotsProducts.taxSetting.length; i++) {
                    var data = snapshotsProducts.taxSetting[i];

                    var tax = 0;

                    if (data.type && data.tax) {
                        if (data.type == "percentageage") {

                            tax = (data.tax * total_price) / 100;
                        } else {
                            tax = data.tax;
                        }
                    }
                    total_tax_amount += parseFloat(tax);
                }
            }

            total_price += parseFloat(total_tax_amount);

            if (currencyAtRight) {

                var total_price_val = total_price.toFixed(decimal_degits) + "" + currentCurrency;
            } else {
                var total_price_val = currentCurrency + "" + total_price.toFixed(decimal_degits);
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
            html = html + '<div class="cat-item px-2 py-1 select_section ' + active_section + '"><a href="' + all_route + '" class="bg-white d-block p-2 text-center shadow-sm cat-link"><img alt="#" src="' + img_url + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'"  class="img-fluid mb-2"><p class="m-0 small">All</p></a></div>';

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
                    "{{trans('lang.dashboard_total_orders')}}",
                    "{{trans('lang.dashboard_total_clients')}}",
                    "{{trans('lang.dashboard_total_drivers')}}",
                ],
                datasets: [{
                    data: [jQuery("#order_count").text(), jQuery("#users_count").text(), jQuery("#driver_count").text()],
                    backgroundColor: [
                        '#FFAB2E',
                        '#B1DB6F',
                        '#7360ed'
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

