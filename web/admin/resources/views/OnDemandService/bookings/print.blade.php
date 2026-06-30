@extends('layouts.app')

@section('content')

<div class="page-wrapper">
    <div class="row page-titles">

        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.print_booking')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('ondemand.bookings.index') !!}">{{trans('lang.booking_plural')}}</a>
                </li>
                <li class="breadcrumb-item">{{trans('lang.print_booking')}}</li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div class="card" id="printableArea" style="font-family: emoji;">
            <div class="col-md-12">
                <div class="print-top non-printable mt-3">
                   
                    <div class="text-right print-btn non-printable">
                        <button type="button" class="fa fa-print non-printable" onclick="printDiv('printableArea')"></button>
                    </div>
                </div>

                <hr class="non-printable">
            </div>
            <div class="col-12" id="printableArea">
                <div class="text-left pt-4 mb-5" style="text-align:center;">
                     <h5 style="font-weight: bold" class="provider_div">{{trans('lang.provider_name')}} : <label class="providerName"></label></h5>
                     
                     <h5 style="font-weight: bold" class="provider_div">
                            {{trans('lang.provider_phone')}} :
                            <label class="providerPhone"></label>
                    </h5>
                </div>
                <span><hr style="border-top: 2px dashed;"></span>
                <div class="row mt-3">
                    <div class="col-6">
                        <h5>{{trans('lang.order_id')}} : <label class="orderId"></label></h5>
                    </div>
                    <div class="col-6">
                        <h5 style="font-weight: bold">
                                <label class="orderDate"></label>

                        </h5>
                    </div>
                    <div class="col-12">
                        <h5>
                            {{trans('lang.customer_name')}} :
                            <label class="customerName"></label>
                        </h5>
                        <h5>
                            {{trans('lang.phone')}} :

                            <label class="customerPhone"></label>
                        </h5>
                        <h5 class="text-break">
                            {{trans('lang.address')}} :

                            <label class="customerAddress"></label>
                        </h5>
                    </div>
                </div>
                <h5 class="text-uppercase"></h5>
                <span><hr style="border-top: 2px dashed;"></span>
                <table class="table table-bordered mt-5 mb-5 table-product-list" style="width: 100%">
                        <thead>
                        <tr>
                            <th>{{trans('lang.service')}}</th>
                            <th>{{trans('lang.price')}}</th>
                            <th>{{trans('lang.qty')}}</th>
                            <th>{{trans('lang.total')}}</th>
                        </tr>
                        </thead>
                        <tbody id="order_products">
                            <td id="service_name"></td>
                            <td id="price"></td>
                            <td id="qty"></td>
                            <td class="total_price"></td>
                        </tbody>
                    </table>
                    
                <span><hr style="border-top: 2px dashed;"></span>
                <div class="row justify-content-md-end mb-5" style="width: 100%;">
                <div class="col-md-3 col-lg-3">
                            <table class="table-subtotal" cellpadding="5" cellspacing="5">
                                <tbody>
                                    <tr>
                                        <td>{{trans('lang.sub_total')}} :</td>
                                        <td><label class="total_price"></label></td>
                                    </tr>
                                    <tr>
                                        <td>{{trans('lang.coupon_discount')}} :</td>
                                         <td>-<label class="total_discount_amount"></label></td>
                                    </tr>
                                    
                                    
                                    <tr>
                                        <td>{{trans('lang.total')}} :</td>
                                        <td><label class="total_amount"></label></td>
                                    </tr>
                                    <tr><td><span class="admin_commission row w-100 m-0"></span></td></tr>
                                </tbody>
                            </table>
                </div>             
                    
                </div>
                 <div class="clearfix" style="text-align:center;width: 100%;display:table">
                        <span><hr style="border-top: 2px dashed;"></span>
                        <h5 class="text-center pt-3" style="font-size: 18px;font-weight: bold">{{trans('lang.thank_you')}}</h5>
                        <span><hr style="border-top: 2px dashed;"></span>
                </div>
            </div>
        </div>
    </div>
</div>
    @endsection

    @section('style')
    <style type="text/css">
        #printableArea * {
            color: black !important;
            font-weight: bold;
        }
        body {
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
        }

        .table-subtotal td{
            text-align:right;
        }
        
    </style>
    <style type="text/css" media="print">
         @page {
            size: portrait;
        }

        @page {
            size: auto;
            margin: 2px;
        }
    </style>
    @section('scripts')
    <script>
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
        var total_price = 0;
        var total_item_price = 0;
        var total_addon_price = 0;
        var vendorname = '';
        var database = firebase.firestore();

        var ref = database.collection('provider_orders').where("id", "==", id);
        var currentCurrency = '';
        var currencyAtRight = false;
        var refCurrency = database.collection('currencies').where('isActive', '==', true);
        var decimal_degits = 0;

        refCurrency.get().then(async function(snapshots) {
            var currencyData = snapshots.docs[0].data();
            currentCurrency = currencyData.symbol;
            currencyAtRight = currencyData.symbolAtRight;

            if (currencyData.decimal_degits) {
                decimal_degits = currencyData.decimal_degits;
            }
        });

        ref.get().then(async function(snapshots) {

            jQuery("#data-table_processing").show();
            var order = snapshots.docs[0].data();

            $(".customerName").text(order.author.firstName + " " + order.author.lastName);
            var billingAddressstring = '';

            $(".orderId").text(id);

            var date = order.createdAt.toDate().toDateString();
            var time = order.createdAt.toDate().toLocaleTimeString('en-US');
            $(".orderDate").text(date + " " + time);

            var billingAddressstring = '';

            if (order.address.hasOwnProperty('address')) {
                billingAddressstring = billingAddressstring + order.address.address;
            }

            if (order.address.hasOwnProperty('locality')) {
                billingAddressstring = billingAddressstring + "," + order.address.locality;
            }
            if (order.address.hasOwnProperty('landmark')) {
                billingAddressstring = billingAddressstring + " " + order.address.landmark;
            }
            $(".customerAddress").text(billingAddressstring);

            if (order.author.hasOwnProperty('phoneNumber')) {
                if(order.author.phoneNumber.includes('+')){
                    $(".customerPhone").text('+' + EditPhoneNumber(order.author.phoneNumber.slice(1)));
                }else{
                    $(".customerPhone").text(EditPhoneNumber(order.author.phoneNumber));
                }
            }


            if (order.address.hasOwnProperty('country')) {

                $("#billing_country").text(order.address.country);

            }

            if (order.address.hasOwnProperty('email')) {
                $("#billing_email").html('<a href="mailto:' + order.address.email + '">' + shortEmail(order.address.email) +
                    '</a>');
            }

            if (order.createdAt) {
                var date1 = order.createdAt.toDate().toDateString();
                var date = new Date(date1);
                var dd = String(date.getDate()).padStart(2, '0');
                var mm = String(date.getMonth() + 1).padStart(2, '0'); //January is 0!
                var yyyy = date.getFullYear();
                var createdAt_val = yyyy + '-' + mm + '-' + dd;
                var time = order.createdAt.toDate().toLocaleTimeString('en-US');
                $('#createdAt').text(createdAt_val + ' ' + time);
            }

            if (order.payment_method) {

                if (order.payment_method == 'cod') {
                    $('#payment_method').text('{{trans("lang.cash_on_delivery")}}');
                } else if (order.payment_method == 'paypal') {
                    $('#payment_method').text('{{trans("lang.paypal")}}');
                } else {
                    $('#payment_method').text(order.payment_method);
                }

            }

            if (order.provider && order.provider.author != '' && order.provider.author != undefined) {
                vendorAuthor = order.provider.author;
            }
            fcmToken = order.author.fcmToken;
            vendorname = order.provider.title;

            fcmTokenVendor = order.provider.fcmToken;
            customername = order.author.firstName;

            vendorId = order.provider.id;
            old_order_status = order.status;
            if (order.payment_shared != undefined) {
                payment_shared = order.payment_shared;
            }
            var productstotalHTML = buildHTMLProductstotal(order);

            orderPreviousStatus = order.status;
            if (order.hasOwnProperty('payment_method')) {
                orderPaymentMethod = order.payment_method;
            }

            $("#order_status option[value='" + order.status + "']").attr("selected", "selected");
            if (order.status == "Order Rejected" || order.status == "Driver Rejected") {
                $("#order_status").prop("disabled", true);
            }
            var price = 0;

            if (order.provider.author) {
                var provider = database.collection('users').where("id", "==",order.provider.author);
               
                provider.get().then(async function(snapshotsnew) {
                    if(!snapshotsnew.empty){
                    var providerData = snapshotsnew.docs[0].data();

                    $('.providerName').html(providerData.firstName+' '+providerData.lastName);
                    
                    if (providerData.phoneNumber) {
                        if(providerData.phoneNumber.includes('+')){
                            $('.providerPhone').text('+' + EditPhoneNumber(providerData.phoneNumber.slice(1)));
                        }else{
                            $('.providerPhone').text(EditPhoneNumber(providerData.phoneNumber));
                        }
                    }
                    
                }else{
                    $(".provider_div").hide();
                }
                });

            }
            $('#service_name').text(order.provider.title);
            $('#qty').text(order.quantity);
           
            
            jQuery("#data-table_processing").hide();
        })

        

        function buildHTMLProductstotal(snapshotsProducts) {
            var html = '';
            var alldata = [];
            var number = [];

            var adminCommission = snapshotsProducts.adminCommission;
            var adminCommissionType = snapshotsProducts.adminCommissionType;
            var discount = snapshotsProducts.discount;
            var couponCode = snapshotsProducts.couponCode;
          
            var notes = snapshotsProducts.notes;
            var status = snapshotsProducts.status;
            var products = snapshotsProducts;

            var intRegex = /^\d+$/;
            var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;


           var val = products;
         
           var sub_total = parseFloat(val.provider.price);

            if (val.provider.disPrice != null && val.provider.disPrice != undefined && val.provider.disPrice != '' && val.provider.disPrice != '0') {
                sub_total = parseFloat(val.provider.disPrice)
            }
         
            var price=sub_total;
            
            var priceUnit='';
            sub_total=parseFloat(val.quantity)*sub_total;
            if(val.provider.priceUnit=='Hourly'){
                priceUnit=' /hr';
            }
            
            if (currencyAtRight) {
                $('.total_price').text(parseFloat(sub_total).toFixed(decimal_degits) + "" + currentCurrency);
                $('#price').text(parseFloat(price).toFixed(decimal_degits) + "" + currentCurrency+priceUnit);

            } else {
                $('.total_price').text(currentCurrency + "" + parseFloat(sub_total).toFixed(decimal_degits));
                $('#price').text(currentCurrency + "" + parseFloat(price).toFixed(decimal_degits)+priceUnit);

            }

            total_price += parseFloat(sub_total);
            var discount_val = 0;
        if((val.endTime!=null && val.provider.priceUnit=='Hourly') || val.provider.priceUnit!='Hourly' ){

            if (intRegex.test(discount) || floatRegex.test(discount)) {

                discount = parseFloat(discount).toFixed(decimal_degits);
                total_price -= parseFloat(discount);

                if (currencyAtRight) {
                    discount_val = parseFloat(discount).toFixed(decimal_degits) + "" + currentCurrency;
                } else {
                    discount_val = currentCurrency + "" + parseFloat(discount).toFixed(decimal_degits);
                }

                couponCode_html = '';
                if (couponCode) {
                    couponCode_html = '</br><small>{{trans("lang.coupon_codes")}} :' + couponCode + '</small>';
                }

                $('.total_discount_amount').text(discount_val);


            } else {

                if (currencyAtRight) {
                    discount_val = parseFloat(discount_val).toFixed(decimal_degits) + "" + currentCurrency;
                } else {
                    discount_val = currentCurrency + "" + parseFloat(discount_val).toFixed(decimal_degits);
                }
                $('.total_discount_amount').text(discount_val);

            }

            var total_item_price = total_price;

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
                            var taxvalue = data.tax;

                        } else {
                            tax = data.tax;
                            taxlabeltype = " ";
                            if (currencyAtRight) {
                                var taxvalue = parseFloat(data.tax).toFixed(decimal_degits) + "" + currentCurrency;
                            } else {
                                var taxvalue = currentCurrency + "" + parseFloat(data.tax).toFixed(decimal_degits);

                            }

                        }
                        taxlabel = data.title;
                    }
                    total_tax_amount += parseFloat(tax);
                    if (currencyAtRight) {
                    $('.table-subtotal tr:eq(1)').after("<tr><td> " + taxlabel + " (" + taxvalue + taxlabeltype + ")</td><td><label>" + parseFloat(tax).toFixed(decimal_degits) + " " + currentCurrency + "</label></td></tr>");
                    } else {
                        $('.table-subtotal tr:eq(1)').after("<tr><td> " + taxlabel + " (" + taxvalue + taxlabeltype + ")</td><td><label>" + currentCurrency + " " + parseFloat(tax).toFixed(decimal_degits) + "</label></td></tr>");
                    }


                }
                total_price = parseFloat(total_price) + parseFloat(total_tax_amount);


            }
            if (currencyAtRight) {
                total_price_val = parseFloat(total_price).toFixed(decimal_degits) + "" + currentCurrency;
            } else {
                total_price_val = currentCurrency + "" + parseFloat(total_price).toFixed(decimal_degits);
            }

            $('.total_amount').text(total_price_val);
            html = html + '<tr><td class="label">{{trans("lang.total_amount")}}</td><td class="total_price_val">' + total_price_val + '</td></tr>';
            if (intRegex.test(adminCommission) || floatRegex.test(adminCommission)) {
                var adminCommHtml = "";

                if (adminCommissionType == "percentage") {
                    adminCommHtml = "(" + adminCommission + "%)";
                    var adminCommission_val = parseFloat(parseFloat(total_item_price * adminCommission) / 100).toFixed(decimal_degits);
                } else {
                    var adminCommission_val = parseFloat(adminCommission).toFixed(decimal_degits);
                }

                if (currencyAtRight) {
                    adminCommHtml = "(" + adminCommission + ")";
                    adminCommission = parseFloat(adminCommission_val).toFixed(decimal_degits) + "" + currentCurrency;
                } else {
                    adminCommission = currentCurrency + "" + parseFloat(adminCommission_val).toFixed(decimal_degits);
                }

                $('.table-subtotal tr:eq(4)').after("<tr><td>{{trans('lang.admin_commission')}}<label>"+adminCommHtml + "</label></td><td>"+adminCommission+"</td></tr>");
            }
        }

            if (notes) {

                html = html + '<tr><td class="label">{{trans("lang.notes")}}</td><td class="adminCommission_val">' + notes + '</td></tr>';
            }


            return html;
        }

        function printDiv(divName) {

            var printContents = document.getElementById(divName).innerHTML;
            printContents = printContents.replace(/<img[^>]*>/g,"");
            var printWindow = window.open();
            var style = `<style type="text/css"> 
                .table-subtotal{ 
                    width: auto; float: right;
                } 
                .table-subtotal td{
                    text-align:right;
                }
                .table-product-list td{
                    font-size: 12px;
                    text-align:center;
                }
                </style>`;
            
            printWindow.document.write(style);
            printWindow.document.write(printContents);
            printWindow.document.close();
            printWindow.print();
        }
    </script>

    @endsection