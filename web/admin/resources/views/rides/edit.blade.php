@extends('layouts.app')

@section('content')

<div class="page-wrapper">

    <div class="row page-titles">

        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.rides')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>

                <?php if (isset($_GET['eid']) && $_GET['eid'] != '') { ?>
                    <li class="breadcrumb-item"><a href="{{route('drivers.ride',$_GET['eid'])}}">{{trans('lang.order_plural')}}</a>
                    </li>
                <?php } else { ?>
                    <li class="breadcrumb-item"><a href="javascript:window.history.go(-1);">{{trans('lang.rides')}}</a>
                    </li>
                <?php } ?>

                <li class="breadcrumb-item">{{trans('lang.ride_edit')}}</li>
            </ol>
        </div>
    </div>

    <div class="card-body">
    

        <div class="order_detail" id="order_detail">
            <div class="order_detail-top">
                <div class="row">
                    <div class="order_edit-genrl col-md-7">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-header-title">{{trans('lang.general_details')}}</h4>
                            </div>
                            <div class="card-body">
                                <div class="order_detail-top-box">

                                    <div class="form-group row widt-100 gendetail-col">
                                        <label class="col-12 control-label"><strong>{{trans('lang.date_created')}}
                                                : </strong><span
                                                    id="createdAt"></span></label>
                                    </div>

                                    <div class="form-group row widt-100 gendetail-col payment_method">
                                        <label class="col-12 control-label"><strong>{{trans('lang.payment_methods')}}: </strong><span
                                                    id="payment_method"></span></label>
                                    </div>


                                    <div class="form-group row widt-100 gendetail-col">
                                        <label class="col-12 control-label"><strong>{{trans('lang.ridetype')}}:</strong>
                                            <span
                                                    id="rideType"></span></label>
                                    </div>

                                    <div class="form-group row width-100 ">
                                        <label class="col-3 control-label">{{trans('lang.status')}}:</label>
                                        <div class="col-7">
                                            <select id="order_status" class="form-control">
                                                <option value="Order Placed" id="order_placed">{{
                                                    trans('lang.order_placed')}}
                                                </option>
                                                <option value="Order Accepted" id="order_accepted">{{
                                                    trans('lang.order_accepted')}}
                                                </option>
                                                <option value="Order Rejected" id="order_rejected">{{
                                                    trans('lang.order_rejected')}}
                                                </option>
                                                <option value="Driver Pending" id="driver_pending">{{
                                                    trans('lang.driver_pending')}}
                                                </option>
                                                <option value="Driver Rejected" id="driver_rejected">{{
                                                    trans('lang.driver_rejected')}}
                                                </option>
                                                <option value="Order Shipped" id="order_shipped">{{
                                                    trans('lang.order_shipped')}}
                                                </option>
                                                <option value="In Transit" id="in_transit">{{
                                                    trans('lang.in_transit')}}
                                                </option>
                                                <option value="Order Completed" id="order_completed">{{
                                                    trans('lang.order_completed')}}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row width-100">
                                        <label class="col-3 control-label"></label>
                                        <div class="col-7 text-right">
                                            <button type="button" class="btn btn-primary edit-form-btn"><i
                                                        class="fa fa-save"></i>
                                                {{trans('lang.update')}}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="order-items-list mt-4 ">
                            <div class="card">
                                <div class="card-body">
                                    <table cellpadding="0" cellspacing="0"
                                           class="table table-striped table-valign-middle">

                                        <thead>
                                        <tr>
                                            <th>{{trans('lang.from')}}</th>
                                            <th>{{trans('lang.to')}}</th>
                                            <th>{{trans('lang.price')}}</th>
                                            <th>{{trans('lang.total')}}</th>
                                        </tr>

                                        </thead>

                                        <tbody id="order_products">

                                        </tbody>

                                    </table>

                                    <div class="order-data-row order-totals-items">
                                        <div class="card">
                                            <div class="card-body">
                                                <table class="order-totals">
                                                    <tbody id="order_products_total">

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="order_edit-genrl col-md-5">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-header-title">{{ trans('lang.billing_details')}}</h4>
                            </div>
                            <div class="card-body">
                                <div class="address order_detail-top-box">
                                    <div class="address order_detail-top-box">
                                        <div class="form-group row widt-100 gendetail-col">
                                            <label class="col-12 control-label"><strong>{{trans('lang.name')}}
                                                    : </strong><span id="billing_name"></span></label>

                                        </div>
                                        <div class="form-group row widt-100 gendetail-col">
                                            <label class="col-12 control-label"><strong>{{trans('lang.address')}}
                                                    : </strong><span id="billing_line1"></span> <span
                                                        id="billing_line2"></span><span
                                                        id="billing_country"></span></label>

                                        </div>

                                        <div class="form-group row widt-100 gendetail-col">
                                            <label class="col-12 control-label"><strong>{{trans('lang.email_address')}}
                                                    : </strong><span id="billing_email"></span></label>

                                        </div>

                                        <p><strong>{{trans('lang.phone')}}:</strong>
                                            <span id="billing_phone"></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="order_addre-edit col-md-4 driver_details_hide">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-header-title">{{ trans('lang.driver_detail')}}</h4>
                                </div>
                                <div class="card-body">
                                    <div class="address order_detail-top-box">
                                        <p>
                                            <span id="driver_firstName"></span> <span id="driver_lastName"></span><br>
                                        </p>
                                        <p><strong>{{trans('lang.email_address')}}:</strong>
                                            <span id="driver_email"></span>
                                        </p>
                                        <p><strong>{{trans('lang.phone')}}:</strong>
                                            <span id="driver_phone"></span>
                                        </p>
                                        <p><strong>{{trans('lang.car_name')}}:</strong>
                                            <span id="driver_carName"></span>
                                        </p>
                                        <p><strong>{{trans('lang.car_number')}}:</strong>
                                            <span id="driver_carNumber"></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="resturant-detail mt-4">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-header-title">{{trans('lang.driver_detail')}}</h4>
                                </div>

                                <div class="card-body">
                                    <a href="" class="row redirecttopage" id="resturant-view">
                                        <div class="col-4">
                                            <img src="" class="resturant-img rounded-circle" alt="driver" width="70px"
                                                 height="70px">
                                        </div>
                                        <div class="col-8">
                                            <h4 class="vendor-title"></h4>
                                        </div>
                                    </a>

                                    <h5 class="contact-info">{{trans('lang.contact_info')}}:</h5>

                                    <p><strong id="vendor_phone1">{{trans('lang.phone')}}:</strong>
                                        <span id="vendor_phone"></span>
                                    </p>
                                    <h5 class="contact-info">{{trans('lang.car_info')}}:</h5>
                                    <a href="" class="row redirecttopage" id="car-view">
                                        <div class="col-4">
                                            <img src="" class="car-img rounded-circle" alt="car" width="70px"
                                                 height="70px">
                                        </div>

                                    </a>
                                    <br>
                                    <p><strong id="driver_carName1" style="width:auto !important;">{{trans('lang.car_name')}}:</strong>
                                        <span id="driver_carName"></span>
                                    </p> <br>
                                    <p><strong id="driver_carNumber1" style="width:auto !important;">{{trans('lang.car_number')}}:</strong>
                                        <span id="driver_carNumber"></span>
                                    </p> <br>
                                    <p><strong id="driver_car_make1" style="width:auto !important;">{{trans('lang.car_make')}}:</strong>
                                        <span id="driver_car_make"></span>
                                    </p>


                                </div>
                            </div>
                        </div>

                    </div>

                </div>


            </div>

        </div>


        <div class="form-group col-12 text-center btm-btn">
            <button type="button" class="btn btn-primary edit-form-btn"><i class="fa fa-save"></i>
                {{trans('lang.save')}}
            </button>

            <?php if (isset($_GET['eid']) && $_GET['eid'] != '') { ?>
                <a href="{{route('vendors.orders',$_GET['eid'])}}" class="btn btn-default"><i
                            class="fa fa-undo"></i>{{trans('lang.cancel')}}</a>
            <?php } else { ?>
                <a href="javascript:window.history.go(-1);" class="btn btn-default"><i
                            class="fa fa-undo"></i>{{trans('lang.cancel')}}</a>
            <?php } ?>

        </div>

    </div>


</div>
</div>

@endsection

@section('style')

@section('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/printThis/1.15.0/printThis.js"></script>

<script type="text/javascript">

    var adminCommission = 0;
    var id_rendom = "<?php echo uniqid(); ?>";
    var id = "<?php echo $id; ?>";
    var driverId = '';
    var fcmToken = '';
    var old_order_status = '';
    var payment_shared = false;
    var deliveryChargeVal = 0;
    var tip_amount_val = 0;
    var tip_amount = 0;
    var vendorname = '';
    var database = firebase.firestore();
    var ref = database.collection('rides').where("id", "==", id);
    var append_procucts_list = '';
    var append_procucts_total = '';
    var total_price = 0;
    var currentCurrency = '';
    var currencyAtRight = false;
    var refCurrency = database.collection('currencies').where('isActive', '==', true);
    var orderPreviousStatus = '';
    var orderTakeAwayOption = false;
    var manfcmTokenVendor = '';
    var manname = '';
    var decimal_degits = 0;

    refCurrency.get().then(async function (snapshots) {
        var currencyData = snapshots.docs[0].data();
        currentCurrency = currencyData.symbol;
        currencyAtRight = currencyData.symbolAtRight;

        if (currencyData.decimal_degits) {
            decimal_degits = currencyData.decimal_degits;
        }
    });
    refCurrency.get().then(async function (snapshots) {
        var currencyData = snapshots.docs[0].data();
        currentCurrency = currencyData.symbol;
        currencyAtRight = currencyData.symbolAtRight;
    });


    var geoFirestore = new GeoFirestore(database);
    var place_image = '';
    var ref_place = database.collection('settings').doc("placeHolderImage");
    ref_place.get().then(async function (snapshots) {
        var placeHolderImage = snapshots.data();
        place_image = placeHolderImage.image;
    });

    $(document).ready(function () {

        //hide this status for admin
        $('#order_placed').hide();
        $('#driver_pending').hide();
        $('#driver_rejected').hide();
        $('#order_shipped').hide();
        $('#in_transit').hide();
        $('#order_completed').hide();

        var alovelaceDocumentRef = database.collection('vendor_orders').doc();
        if (alovelaceDocumentRef.id) {
            id_rendom = alovelaceDocumentRef.id;
        }
        $(document.body).on('click', '.redirecttopage', function () {
            var url = $(this).attr('data-url');
            window.location.href = url;
        });

        jQuery("#data-table_processing").show();

        ref.get().then(async function (snapshots) {
            var ride = snapshots.docs[0].data();

            append_procucts_list = document.getElementById('order_products');
            append_procucts_list.innerHTML = '';

            append_procucts_total = document.getElementById('order_products_total');
            append_procucts_total.innerHTML = '';


            $("#billing_name").text(ride.author.firstName + " " + ride.author.lastName);


            var billingAddressstring = '';

            $("#trackng_number").text(id);
            if (ride.author.shippingAddress.hasOwnProperty('line1')) {
                $("#billing_line1").text(ride.author.shippingAddress.line1);
            }
            if (ride.author.shippingAddress.hasOwnProperty('line2')) {
                billingAddressstring = billingAddressstring + ride.author.shippingAddress.line2;
            }
            if (ride.author.shippingAddress.hasOwnProperty('city')) {
                billingAddressstring = billingAddressstring + ", " + ride.author.shippingAddress.city;
            }

            if (ride.author.shippingAddress.hasOwnProperty('postalCode')) {
                billingAddressstring = billingAddressstring + ", " + ride.author.shippingAddress.postalCode;
            }

            if (ride.author.hasOwnProperty('phoneNumber')) {
                if(ride.author.phoneNumber.includes('+')){
                    $("#billing_phone").text('+' + EditPhoneNumber(ride.author.phoneNumber.slice(1)));
                }else{
                    $("#billing_phone").text(EditPhoneNumber(ride.author.phoneNumber));
                }
            }

            $("#billing_line2").text(billingAddressstring);

            if (ride.author.shippingAddress.hasOwnProperty('country')) {

                $("#billing_country").text(ride.author.shippingAddress.country);

            }

            if (ride.author.shippingAddress.hasOwnProperty('email')) {
                $("#billing_email").html('<a href="mailto:' + ride.author.email + '">' + shortEmail(ride.author.email) + '</a>');
            }

            if (ride.createdAt) {
                var date1 = ride.createdAt.toDate().toDateString();
                var date = new Date(date1);
                var dd = String(date.getDate()).padStart(2, '0');
                var mm = String(date.getMonth() + 1).padStart(2, '0'); //January is 0!
                var yyyy = date.getFullYear();
                var createdAt_val = yyyy + '-' + mm + '-' + dd;
                var time = ride.createdAt.toDate().toLocaleTimeString('en-US');

                $('#createdAt').text(createdAt_val + ' ' + time);
            }

            var paymentMethod = '';
            if (ride.paymentMethod) {

                if (ride.paymentMethod == "stripe") {
                    image = '{{asset("images/payment/stripe.png")}}';
                    paymentMethod = '<img alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + place_image + '\'" width="30%" height="30%">';

                } else if (ride.paymentMethod == "cod") {
                    image = '{{asset("images/payment/cashondelivery.png")}}';
                    paymentMethod = '<img alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + place_image + '\'" width="30%" height="30%">';

                } else if (ride.paymentMethod == "razorpay") {
                    image = '{{asset("images/payment/razorepay.png")}}';
                    paymentMethod = '<img alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + place_image + '\'" width="30%" height="30%">';

                } else if (ride.paymentMethod == "paypal") {
                    image = '{{asset("images/payment/paypal.png")}}';
                    paymentMethod = '<img alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + place_image + '\'"  width="30%" height="30%">';

                } else if (ride.paymentMethod == "payfast") {
                    image = '{{asset("images/payfast.png")}}';
                    paymentMethod = '<img alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + place_image + '\'" width="30%" height="30%">';

                } else if (ride.paymentMethod == "paystack") {
                    image = '{{asset("images/payment/paystack.png")}}';
                    paymentMethod = '<img alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + place_image + '\'"  width="30%" height="30%">';

                } else if (ride.paymentMethod == "flutterwave") {
                    image = '{{asset("images/payment/flutter_wave.png")}}';
                    paymentMethod = '<img alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + place_image + '\'" width="30%" height="30%">';

                } else if (ride.paymentMethod == "mercadoPago" || ride.paymentMethod == "mercado pago" || ride.paymentMethod == "mercadopago") {
                    image = '{{asset("images/payment/marcado_pago.png")}}';
                    paymentMethod = '<img alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + place_image + '\'" width="30%" height="30%">';

                } else if (ride.paymentMethod == "wallet") {
                    image = '{{asset("images/payment/emart_wallet.png")}}';
                    paymentMethod = '<img alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + place_image + '\'" width="30%" height="30%" >';

                } else if (ride.paymentMethod == "paytm") {
                    image = '{{asset("images/payment/paytm.png")}}';
                    paymentMethod = '<img alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + place_image + '\'" width="30%" height="30%">';

                } else if (ride.paymentMethod == "cancelled order payment") {
                    image = '{{asset("images/payment/cancel_order.png")}}';
                    paymentMethod = '<img alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + place_image + '\'" width="30%" height="30%">';

                } else if (ride.paymentMethod == "refund amount") {
                    image = '{{asset("images/payment/refund_amount.png")}}';
                    paymentMethod = '<img alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + place_image + '\'" width="30%" height="30%">';
                } else if (ride.paymentMethod == "referral amount") {
                    image = '{{asset("images/payment/reffral_amount.png")}}';
                    paymentMethod = '<img alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + place_image + '\'" width="30%" height="30%">';
                } else {
                    paymentMethod = ride.paymentMethod;
                }
            }
            $('#payment_method').html('<span>' + paymentMethod + '</span>');


            if (ride.hasOwnProperty('takeAway') && ride.takeAway) {
                $('#driver_pending').hide();
                $('#driver_rejected').hide();
                $('#order_shipped').hide();
                $('#in_transit').hide();
                $('.payment_method').hide();
                orderTakeAwayOption = true;

            } else {
                $('.payment_method').show();

            }

            if (ride.hasOwnProperty('rideType')) {
                $('#rideType').text(ride.rideType);
            } else {

            }

            if ((ride.driver != '' && ride.driver != undefined) && (ride.takeAway)) {

                $('#driver_carName').text(ride.driver.carName);
                $('#driver_carNumber').text(ride.driver.carNumber);
                $('#driver_email').html('<a href="mailto:' + ride.driver.email + '">' + shortEmail(ride.driver.email) + '</a>');
                $('#driver_firstName').text(ride.driver.firstName);
                $('#driver_lastName').text(ride.driver.lastName);
                if(ride.driver.phoneNumber.includes('+')){
                    $('#driver_phone').text('+' + EditPhoneNumber(ride.driver.phoneNumber.slice(1)));
                }else{
                    $('#driver_phone').text(EditPhoneNumber(ride.driver.phoneNumber));
                }

            } else {
                $('.order_edit-genrl').removeClass('col-md-4').addClass('col-md-5');
                $('.order_addre-edit').removeClass('col-md-4').addClass('col-md-7');
                $('.driver_details_hide').empty();

            }

            if (ride.driverID != '' && ride.driverID != undefined) {
                driverId = ride.driverID;
            }
            if (ride.vendor && ride.vendor.author != '' && ride.vendor.author != undefined) {
                vendorAuthor = ride.vendor.author;
            }
            fcmToken = ride.author.fcmToken;
            if (ride.driver != undefined) {
                drivername = ride.driver.firstName;
            } else {
                drivername = '';
            }


            customername = ride.author.firstName;

            driverID = ride.driverID;
            old_order_status = ride.status;
            if (ride.payment_shared != undefined) {
                payment_shared = ride.payment_shared;
            }
            var productsListHTML = buildHTMLProductsList(snapshots);
            var productstotalHTML = buildHTMLProductstotal(ride);

            if (productsListHTML != '') {
                append_procucts_list.innerHTML = productsListHTML;
            }

            if (productstotalHTML != '') {
                append_procucts_total.innerHTML = productstotalHTML;
            }

            orderPreviousStatus = ride.status;
            if (ride.hasOwnProperty('paymentMethod')) {
                orderPaymentMethod = ride.paymentMethod;
            }

            $("#order_status option[value='" + ride.status + "']").attr("selected", "selected");
            if (ride.status == "Order Rejected" || ride.status == "Driver Rejected") {
                $("#order_status").prop("disabled", true);
            }
            var price = 0;

            $('.resturant-img').attr('src', place_image);
            $('.car-img').attr('src', place_image);

            if (ride.driverID) {
                var driver = database.collection('users').where("id", "==", ride.driverID);

                driver.get().then(async function (snapshotsnew) {
                    if (!snapshotsnew.empty) {
                        var driverdata = snapshotsnew.docs[0].data();


                        if (driverdata.id) {
                            var route_view = '{{route("drivers.view",":id")}}';
                            route_view = route_view.replace(':id', driverdata.id);

                            $('#resturant-view').attr('data-url', route_view);
                        }
                        if (driverdata.profilePictureURL) {
                            $('.resturant-img').attr('src', driverdata.profilePictureURL);
                        } else {
                            $('.resturant-img').attr('src', place_image);
                        }
                        if (driverdata.firstName) {
                            $('.vendor-title').html(driverdata.firstName + ' ' + driverdata.lastName);
                        }

                        if (driverdata.email) {
                            $('#vendor_email').html(shortEmail(driverdata.email));
                        }
                        if (driverdata.phoneNumber) {
                            if(driverdata.phoneNumber.includes('+')){
                                $('#vendor_phone').text('+' + EditPhoneNumber(driverdata.phoneNumber.slice(1)));
                            }else{
                                $('#vendor_phone').text(EditPhoneNumber(driverdata.phoneNumber));
                            }
                        }
                        if (driverdata.id) {
                            var route_view = '{{route("drivers.view",":id")}}';
                            route_view = route_view.replace(':id', driverdata.id);

                            $('#resturant-car').attr('data-url', route_view);
                        }
                        if (driverdata.carPictureURL) {
                            $('.car-img').attr('src', driverdata.carPictureURL);
                        } else {
                            $('.car-img').attr('src', place_image);
                        }
                        if (driverdata.carName) {
                            $('#driver_carName').html(driverdata.carName);
                        }

                        if (driverdata.carNumber) {
                            $('#driver_carNumber').html(driverdata.carNumber);
                        }
                        if (driverdata.carMakes) {
                            $('#driver_car_make').text(driverdata.carMakes);
                        }
                    } else {


                        $('.resturant-img').hide();
                        $('.vendor-title').text('Driver deleted');
                        $('#vendor_email').hide();
                        $('#vendor_phone').hide();

                        $('#resturant-car').hide();
                        $('.car-img').hide();
                        $('#driver_carName').hide();
                        $('#driver_carNumber').hide();
                        $('#driver_car_make').hide();

                        $('#vendor_phone1').hide();


                        $('#driver_carName1').hide();
                        $('#driver_carNumber1').hide();
                        $('#driver_car_make1').hide();

                        $('.contact-info').hide();
                    }
                });

            }

            ref.get().then(async function (snapshotsride) {

                snapshotsride.docs.forEach((listval) => {
                    database.collection('rides').where('id', '==', listval.id).where("status", "in", ["Order Completed"]).get().then(async function (orderSnapshots) {
                        var count_order_complete = orderSnapshots.docs.length;
                    });

                });
            });

            jQuery("#data-table_processing").hide();
        })


        $(".edit-form-btn").click(async function () {

            var clientName = $(".client_name").val();
            var orderStatus = $("#order_status").val();
            if (old_order_status != orderStatus) {
                database.collection('rides').doc(id).update({'status': orderStatus}).then(async function (result) {

                    if (orderStatus == "Order Completed") {
                        manname = customername;
                    } else {
                        manfcmTokenRide = fcmToken;
                        manname = drivername;
                    }
                    if (orderStatus != orderPreviousStatus && payment_shared == false) {
                        if (orderStatus == 'Order Completed') {

                            vendorAmount = parseFloat(total_price) + (parseFloat(adminCommission));
                            driverAmount = parseFloat(deliveryChargeVal) - parseFloat(tip_amount);
                            var vendor = database.collection('users').where("driverID", "==", driverID);
                            var vendorWallet = 0;

                            if (driverId && driverAmount) {
                                var driver = database.collection('users').where("id", "==", driverId);
                                await driver.get().then(async function (snapshotsdriver) {
                                    var driverdata = snapshotsdriver.docs[0].data();
                                    if (driverdata) {
                                        if (isNaN(driverdata.wallet_amount) || driverdata.wallet_amount == undefined) {
                                            driverWallet = 0;
                                        } else {
                                            driverWallet = driverdata.wallet_amount;
                                        }
                                        if (orderPaymentMethod == 'cod' && orderTakeAwayOption == true) {
                                            driverWallet = driverWallet - parseFloat(total_price) - parseFloat(driverAmount);
                                        } else {
                                            driverWallet = driverWallet + driverAmount;
                                        }
                                        if (!isNaN(vendorWallet)) {
                                            await database.collection('users').doc(driverdata.id).update({'wallet_amount': driverWallet}).then(async function (result) {

                                            });
                                        }

                                    }
                                })
                            }

                            await database.collection('rides').doc(id).update({'payment_shared': true}).then(async function (result) {
                            });
                        }

                        await $.ajax({
                            type: 'POST',
                            url: "<?php echo route('order-status-notification'); ?>",
                            data: {
                                _token: '<?php echo csrf_token() ?>',
                                'fcm': manfcmTokenVendor,
                                'drivername': manname,
                                'orderStatus': orderStatus
                            },
                            success: function (data) {

                                if (orderPreviousStatus != 'Order Rejected' && orderPreviousStatus != 'Driver Rejected' && orderPaymentMethod != 'cod' && orderTakeAwayOption == false) {
                                    if (orderStatus == 'Order Rejected' || orderStatus == 'Driver Rejected') {
                                        var walletId = "<?php echo uniqid(); ?>";
                                        var canceldateNew = new Date();
                                        var orderCancelDate = new Date(canceldateNew.setHours(23, 59, 59, 999));
                                        database.collection('wallet').doc(walletId).set({
                                            'amount': parseFloat(orderPaytableAmount),
                                            'date': orderStatus,
                                            'id': walletId,
                                            'payment_status': 'success',
                                            'user_id': orderCustomerId,
                                            'paymentMethod': 'Cancelled Order Payment'
                                        }).then(function (result) {
                                            window.location.href = '{{ route("rides")}}';
                                        })
                                    } else {

                                        window.location.href = '{{ route("rides")}}';
                                    }
                                } else {
                                    window.location.href = '{{ route("rides")}}';
                                }

                            }
                        });

                    }

                    await $.ajax({
                        type: 'POST',
                        url: "<?php echo route('order-status-notification'); ?>",
                        data: {
                            _token: '<?php echo csrf_token() ?>',
                            'fcm': fcmToken,
                            'drivername': drivername,
                            'orderStatus': orderStatus
                        },
                        success: function (data) {
                            <?php if (isset($_GET['eid']) && $_GET['eid'] != '') { ?>
                            window.location.href = "{{ route('driver.ride',$_GET['eid']) }}";
                            <?php } else { ?>
                            window.location.href = '{{ route("rides")}}';
                            <?php } ?>
                        }
                    });

                });
            }

        })

    })

    function buildHTMLProductsList(snapshots) {
        var html = '';
        var alldata = [];
        var number = [];
        snapshots.docs.forEach((listval) => {
            var datas = listval.data();
            datas.id = listval.id;
            alldata.push(datas);
        });


        var count = 0;
        alldata.forEach((listval) => {

            var val = listval;

            html = html + '<tr>';

            if (val.size) {
                html = html + '<div class="type"><span>{{trans("lang.type")}} :</span><span class="ext-size">' + val.size + '</span></div>';
            }

            price_item = parseFloat(val.subTotal).toFixed(2);

            totalProductPrice = price_item;
            var extras_price = 0;

            totalProductPrice = parseFloat(totalProductPrice).toFixed(2);

            if (currencyAtRight) {
                price_val = price_item + "" + currentCurrency;
                extras_price_val = extras_price + "" + currentCurrency;
                totalProductPrice_val = totalProductPrice + "" + currentCurrency;
            } else {
                price_val = currentCurrency + "" + price_item;
                extras_price_val = currentCurrency + "" + extras_price;
                totalProductPrice_val = currentCurrency + "" + totalProductPrice;
            }

            html = html + '</div></div></td>';
            html = html + '<td>' + val.sourceLocationName + '</td><td>' + val.destinationLocationName + '</td><td>' + price_val + '</td><td>  ' + totalProductPrice_val + '</td>';

            html = html + '</tr>';
            total_price += parseFloat(totalProductPrice);
        });
        totalProductPrice = 0;

        return html;
    }

    function buildHTMLProductstotal(snapshotsProducts) {
        var html = '';
        var alldata = [];
        var number = [];
        var adminCommission = snapshotsProducts.adminCommission;
        var adminCommissionType = snapshotsProducts.adminCommissionType;
        var discount = snapshotsProducts.discount;
        var couponCode = snapshotsProducts.couponCode;
        var extras = snapshotsProducts.extras;
        var extras_price = snapshotsProducts.extras_price;
        var rejectedByDrivers = snapshotsProducts.rejectedByDrivers;
        var tip_amount = snapshotsProducts.tip_amount;
        var notes = snapshotsProducts.notes;
        var status = snapshotsProducts.status;
        var products = snapshotsProducts.products;
        var deliveryCharge = snapshotsProducts.vehicleType.delivery_charges_per_km;
        var subTotal = snapshotsProducts.subTotal;

        var intRegex = /^\d+$/;
        var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;

        if (products) {

            products.forEach((product) => {
                var val = product;
            });
        }

        if (currencyAtRight) {
            total_price_val = parseFloat(total_price).toFixed(decimal_degits) + "" + currentCurrency;
        } else {
            total_price_val = currentCurrency + "" + parseFloat(total_price).toFixed(decimal_degits);
        }
        html = html + '<tr><td class="seprater" colspan="2"><hr><span>{{trans("lang.sub_total")}}</span></td></tr>';
        html = html + '<tr class="final-rate"><td class="label">{{trans("lang.sub_total")}}</td><td class="sub_total" style="color:green">(' + total_price_val + ')</td></tr>';

        var discount_price = subTotal;
        if (intRegex.test(discount) || floatRegex.test(discount)) {
            html = html + '<tr><td class="seprater" colspan="2"><hr><span>{{trans("lang.discount")}}</span></td></tr>';
            discount = parseFloat(discount).toFixed(2);
            total_price = subTotal - parseFloat(discount);
            discount_price = subTotal - parseFloat(discount);

            if (currencyAtRight) {
                discount_val = discount + "" + currentCurrency;
            } else {
                discount_val = currentCurrency + "" + discount;
            }

            couponCode_html = '';
            if (couponCode) {
                couponCode_html = '</br><small>{{trans("lang.coupon_codes")}} :' + couponCode + '</small>';
            }
            html = html + '<tr><td class="label">{{trans("lang.discount")}}' + couponCode_html + '</td><td class="discount" style="color:red">(-' + discount_val + ')</td></tr>';
        }

        var tax = 0;
        var taxlabel = '';
        var taxlabeltype = '';
        var total_tax_amount = 0;

        var taxHtml = '';

        if (snapshotsProducts.hasOwnProperty('taxSetting') && snapshotsProducts.taxSetting != null) {
            html = html + '<tr><td class="seprater" colspan="2"><hr><span>{{trans("lang.tax_calculation")}}</span></td></tr>';

            var total_tax_amount = 0;
            for (var i = 0; i < snapshotsProducts.taxSetting.length; i++) {
                var data = snapshotsProducts.taxSetting[i];

                if (data.type && data.tax) {
                    if (data.type == "percentage") {
                        tax = (data.tax * total_price) / 100;
                        taxlabeltype = "%";
                        var taxvalue = data.tax;

                    } else {
                        tax = data.tax;
                        taxlabeltype = "";
                        if (currencyAtRight) {
                            var taxvalue = parseFloat(data.tax).toFixed(decimal_degits) + "" + currentCurrency;
                        } else {
                            var taxvalue = currentCurrency + "" + parseFloat(data.tax).toFixed(decimal_degits);

                        }

                    }
                    taxlabel = data.title;
                }
                total_tax_amount += parseFloat(tax);
                if (!isNaN(tax) && tax != 0) {
                    if (currencyAtRight) {
                        html = html + '<tr><td class="label">' + taxlabel + " (" + taxvalue + taxlabeltype + ')</td><td class="tax_amount" id="greenColor" style="color:green">+' + parseFloat(tax).toFixed(decimal_degits) + '' + currentCurrency + '</td></tr>';
                    } else {
                        html = html + '<tr><td class="label">' + taxlabel + " (" + taxvalue + taxlabeltype + ')</td><td class="tax_amount" id="greenColor" style="color:green">+' + currentCurrency + parseFloat(tax).toFixed(decimal_degits) + '</td></tr>';
                    }


                }
            }
            total_price = parseFloat(total_price) + parseFloat(total_tax_amount);
        }

        var total_item_price = total_price;
        if (intRegex.test(tip_amount) || floatRegex.test(tip_amount)) {
            html = html + '<tr><td class="seprater" colspan="2"><hr><span>{{trans("lang.tip")}}</span></td></tr>';
            tip_amount = parseFloat(tip_amount).toFixed(2);
            total_price += parseFloat(tip_amount);
            total_price = parseFloat(total_price).toFixed(2);

            if (currencyAtRight) {
                tip_amount_val = tip_amount + "" + currentCurrency;
            } else {
                tip_amount_val = currentCurrency + "" + tip_amount;
            }
            if (tip_amount) {
                html = html + '<tr><td class="label">{{trans("lang.tip_amount")}}</td><td class="tip_amount_val">+' + tip_amount_val + '</td></tr>';
            }
        }

        if (currencyAtRight) {
            total_price_val = parseFloat(total_price).toFixed(2) + "" + currentCurrency;
        } else {
            total_price_val = currentCurrency + "" + parseFloat(total_price).toFixed(2);
        }
        html += '<tr><td class="seprater" colspan="2"><hr></td></tr>';
        html = html + '<tr class="grand-total"><td class="label">{{trans("lang.total_amount")}}</td><td class="total_price_val">' + total_price_val + '</td></tr>';

        if (intRegex.test(adminCommission) || floatRegex.test(adminCommission)) {
            var adminCommHtml = "";

            if (adminCommissionType == "percentage") {
                adminCommHtml = "(" + adminCommission + "%)";
                var adminCommission_val = parseFloat(parseFloat(discount_price * adminCommission) / 100).toFixed(decimal_degits);
            } else {
                var adminCommission_val = parseFloat(adminCommission).toFixed(decimal_degits);
            }

            if (currencyAtRight) {

                adminCommission = parseFloat(adminCommission_val).toFixed(decimal_degits) + "" + currentCurrency;
            } else {
                adminCommission = currentCurrency + "" + parseFloat(adminCommission_val).toFixed(decimal_degits);
            }

            html = html + '<tr><td class="label"><small>{{trans("lang.admin_commission")}} ' + adminCommHtml + '</small> </td><td style="color:red"><small>( ' + adminCommission + ' )</small></td></tr>';

        }


        if (notes) {


            html = html + '<tr><td class="label">{{trans("lang.notes")}}</td><td class="adminCommission_val">' + notes + '</td></tr>';
        }


        return html;
    }

</script>

@endsection