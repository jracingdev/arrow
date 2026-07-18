@extends('layouts.app')

@section('content')

<div class="page-wrapper">
    <div class="row page-titles">

        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.print_order')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{trans('lang.dashboard')}}</a></li>


                <li class="breadcrumb-item"><a href="{!! route('orders') !!}">{{trans('lang.order_plural')}}</a>
                </li>


                <li class="breadcrumb-item">{{trans('lang.print_order')}}</li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div class="card" id="printableArea" style="font-family: emoji;">
            <div class="col-md-12">
                <div class="print-top non-printable mt-3">
                    <div id="data-table_processing" class="dataTables_processing panel panel-default non-printable" style="display: none;">{{trans('lang.processing')}}
                    </div>
                    <div class="text-right print-btn non-printable">
                        <button type="button" class="fa fa-print non-printable" onclick="printDiv('printableArea')"></button>
                    </div>
                </div>

                <hr class="non-printable">
            </div>
            <div class="col-12">
                <div class="text-center pt-4 mb-3">
                    <h2 style="line-height: 1"><label class="storeName"></label></h2>
                    <h5 style="font-size: 20px;font-weight: lighter;line-height: 1">
                        <label class="storeAddress"></label>
                    </h5>
                    <h5 style="font-size: 16px;font-weight: lighter;line-height: 1">
                        {{trans('lang.phone')}} :
                        <label class="storePhone"></label>
                    </h5>
                </div>
                <span class="dashed-line"></span>
                <div class="row mt-3">
                    <div class="col-6">
                        <h5>{{trans('lang.order_id')}} : <label class="orderId"></label></h5>
                    </div>
                    <div class="col-6">
                        <h5 style="font-weight: lighter">
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
                <span class="dashed-line"></span>
                <table class="table table-bordered mt-3" style="width: 95%">
                    <thead>
                        <tr>
                            <th>{{trans('lang.item')}}</th>
                            <th>{{trans('lang.price')}}</th>
                            <th>{{trans('lang.qty')}}</th>
                            <th>{{trans('lang.extras')}}</th>
                            <th>{{trans('lang.total')}}</th>
                        </tr>
                    </thead>
                    <tbody id="order_products">

                    </tbody>
                </table>
                <span class="dashed-line"></span>
                <div class="row justify-content-md-end mb-3" style="width: 97%">
                    <div class="col-md-7 col-lg-7">
                        <dl class="row text-right" id="price-breakdown">
                            <dt class="col-6">{{trans('lang.sub_total')}} :</dt>
                            <dd class="col-6">
                                <label class="total_price"></label>
                            </dd>
                            <dt class="col-6">{{trans('lang.coupon_discount')}} :</dt>
                            <dd class="col-6">
                                -
                                <label class="total_discount_amount"></label>
                            </dd>
                            <dt class="col-6">{{trans('lang.special_offer')}} {{trans('lang.coupon_discount')}}:
                            </dt>
                            <dd class="col-6">
                                -
                                <label class="total_special_discount_amount"></label>
                            </dd>

                            <span class="taxes row w-100 m-0"></span>

                            <dt class="col-6 packagingChargeDiv d-none">{{trans('lang.packaging_charge')}} :</dt>
                            <dd class="col-6 packagingChargeDiv d-none">
                                <label class="packaging_charge">+ $ 0</label>
                            </dd>

                            <dt class="col-6" style="font-size: 20px">{{trans('lang.total')}} :
                            </dt>
                            <dd class="col-6" style="font-size: 20px">
                                <label class="total_amount"></label>
                            </dd>
                        </dl>
                    </div>
                </div>
                <span class="dashed-line"></span>
                <h5 class="text-center pt-3">
                    {{trans('lang.thank_you')}}
                </h5>
                <span class="dashed-line"></span>
            </div>
        </div>
    </div>

    @endsection

    @section('style')
    <style type="text/css">
        #printableArea * {
            color: black !important;
        }

        @media print {
            @page {
                size: portrait;
            }

            .non-printable {
                display: none;
            }

            .printable {
                display: block;
                font-family: emoji !important;
            }

            #printableArea {
                width: 400px;
            }

            body {
                -webkit-print-color-adjust: exact !important;
                /* Chrome, Safari */
                color-adjust: exact !important;
                font-family: emoji !important;
            }

        }
    </style>
    <style type="text/css" media="print">
        @page {
            size: portrait;
        }

        @page {
            size: auto;
            /* auto is the initial value */
            margin: 2px;
            /* this affects the margin in the printer settings */
            font-family: emoji !important;
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
        
        var vendorname = '';
        var database = firebase.firestore();
        var ref = database.collection('vendor_orders').where("id", "==", id);
        var currentCurrency = '';
        var currencyAtRight = false;
        var refCurrency = database.collection('currencies').where('isActive', '==', true);
        var decimal_degits = 0;
        var currencyData = '';
        var total_tax_amount = 0;
        var placeholderImage = '';
        var packagingChargeEnable = false;

        let taxBreakdownGrouped = {
            item: {},
            order: {},
            delivery: {},
            packaging: {},
            platform: {}
        };
        
        let taxHeaderInserted = false;
        let taxFooterInserted = false;

        var placeholder = database.collection('settings').doc('placeHolderImage');
        placeholder.get().then(async function (snapshotsimage) {
            var placeholderImageData = snapshotsimage.data();
            placeholderImage = placeholderImageData.image;
        })

        refCurrency.get().then(async function(snapshots) {
            currencyData = snapshots.docs[0].data();
            currentCurrency = currencyData.symbol;
            currencyAtRight = currencyData.symbolAtRight;

            if (currencyData.decimal_degits) {
                decimal_degits = currencyData.decimal_degits;
            }
        });

        ref.get().then(async function(snapshots) {

            jQuery("#data-table_processing").show();
            var order = snapshots.docs[0].data();
            packagingChargeEnable = order.packagingChargeEnable;
            if(packagingChargeEnable){
                $('.packagingChargeDiv').removeClass('d-none');
            }else{
                $('.packagingChargeDiv').addClass('d-none');
            }
            $(".customerName").text(order.author.firstName + " " + order.author.lastName);
            var billingAddressstring = '';

            $(".orderId").text(id);

            var date = ArrowDateTime.formatDate(order.createdAt.toDate());
        var time = ArrowDateTime.formatTime(order.createdAt.toDate());
        $(".orderDate").text(date + " " + time);

            var billingAddressstring = '';

            if (order.address.hasOwnProperty('address')) {
            billingAddressstring = billingAddressstring + order.address.address;
            }

            if (order.address.hasOwnProperty('locality')) {
            billingAddressstring = billingAddressstring +","+ order.address.locality;
            }
            if (order.address.hasOwnProperty('landmark')) {
            billingAddressstring = billingAddressstring + " " + order.address.landmark;
            }
            if(order.takeAway==false){
               $(".customerAddress").text(billingAddressstring);  
            }
        if (order.author.hasOwnProperty('phoneNumber')) {
            $(".customerPhone").text(order.author.phoneNumber);
        }


        if (order.address.hasOwnProperty('country')) {

            $("#billing_country").text(order.address.country);

        }

        if (order.address.hasOwnProperty('email')) {
            $("#billing_email").html('<a href="mailto:' + order.address.email + '">' + order.address.email +
                '</a>');
        }

            if (order.createdAt) {
                var date1 = ArrowDateTime.formatDate(order.createdAt.toDate());
                var date = new Date(date1);
                var dd = String(date.getDate()).padStart(2, '0');
                var mm = String(date.getMonth() + 1).padStart(2, '0'); //January is 0!
                var yyyy = date.getFullYear();
                var createdAt_val = yyyy + '-' + mm + '-' + dd;
                var time = ArrowDateTime.formatTime(order.createdAt.toDate());
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
            if (order.hasOwnProperty('takeAway') && order.takeAway) {
                $('#driver_pending').hide();
                $('#driver_rejected').hide();
                $('#order_shipped').hide();
                $('#in_transit').hide();
                $('#order_type').text('{{trans("lang.order_takeaway")}}');
                $('.payment_method').hide();
                orderTakeAwayOption = true;

            } else {
                $('#order_type').text('{{trans("lang.order_delivery")}}');
                $('.payment_method').show();

            }

            if ((order.driver != '' && order.driver != undefined) && (order.takeAway)) {

                $('#driver_carName').text(order.driver.carName);
                $('#driver_carNumber').text(order.driver.carNumber);
                $('#driver_email').html('<a href="mailto:' + order.driver.email + '">' + order.driver.email + '</a>');
                $('#driver_firstName').text(order.driver.firstName);
                $('#driver_lastName').text(order.driver.lastName);
                $('#driver_phone').text(order.driver.phoneNumber);

            } else {
                $('.order_edit-genrl').removeClass('col-md-4').addClass('col-md-6');
                $('.order_addre-edit').removeClass('col-md-4').addClass('col-md-6');
                $('.driver_details_hide').empty();

            }

            if (order.driverID != '' && order.driverID != undefined) {
                driverId = order.driverID;
            }
            if (order.vendor && order.vendor.author != '' && order.vendor.author != undefined) {
                vendorAuthor = order.vendor.author;
            }
            fcmToken = order.author.fcmToken;
            vendorname = order.vendor.title;

            fcmTokenVendor = order.vendor.fcmToken;
            customername = order.author.firstName;

            vendorId = order.vendor.id;
            old_order_status = order.status;
            if (order.payment_shared != undefined) {
                payment_shared = order.payment_shared;
            }
            append_procucts_list = document.getElementById('order_products');
            append_procucts_list.innerHTML = '';

            var productsListHTML = buildHTMLProductsList(order.products);
            var productstotalHTML = buildHTMLProductstotal(order);

            if (productsListHTML != '') {
                append_procucts_list.innerHTML = productsListHTML;
            }

            orderPreviousStatus = order.status;
            if (order.hasOwnProperty('payment_method')) {
                orderPaymentMethod = order.payment_method;
            }

            $("#order_status option[value='" + order.status + "']").attr("selected", "selected");
            if (order.status == "Order Rejected" || order.status == "Driver Rejected") {
                $("#order_status").prop("disabled", true);
            }
            var price = 0;


            if (order.vendorID) {
                var vendor = database.collection('vendors').where("id", "==", order.vendorID);

                vendor.get().then(async function(snapshotsnew) {
                    var vendordata = snapshotsnew.docs[0].data();

                    if (vendordata.id) {
                        var route_view = '';
                        route_view = route_view.replace(':id', vendordata.id);

                        $('#resturant-view').attr('data-url', route_view);
                    }

                    if (vendordata.photo) {
                        $('.resturant-img').attr('src', vendordata.photo);
                    } else {
                        $('.resturant-img').attr('src', placeholderImage);
                    }
                    if (vendordata.title) {
                        $('.storeName').html(vendordata.title);
                    }

                 
                    if (vendordata.phonenumber) {
                        $('.storePhone').text(vendordata.phonenumber);
                    }
                    if (vendordata.location) {
                        $('.storeAddress').text(vendordata.location);
                    }

                });

            }


            jQuery("#data-table_processing").hide();
        })

        function buildHTMLProductsList(snapshotsProducts) {
            var html = '';
            var alldata = [];
            var number = [];
            var totalProductPrice = 0;

            snapshotsProducts.forEach((product) => {

                var val = product;

                html = html + '<tr>';

                var extra_html = '';
                if (product.extras != undefined && product.extras != '' && product.extras.length > 0) {
                    extra_html = extra_html + '<span>';
                    var extra_count = 1;
                    try {
                        product.extras.forEach((extra) => {

                            if (extra_count > 1) {
                                extra_html = extra_html + ',' + extra;
                            } else {
                                extra_html = extra_html + extra;
                            }
                            extra_count++;
                        })
                    } catch (error) {

                    }

                    extra_html = extra_html + '</span>';
                }

                html = html + '<td class="order-product"><div class="order-product-box">';


                if (val.photo != '') {
                    if(val.photo){
                        photo=val.photo;
                    }else{
                        photo=placeholderImage;
                    }
                    html = html + '<img class="img-circle img-size-32 mr-2" style="width:60px;height:60px;" src="' + photo + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" alt="image">';
                } else {
                    html = html + '<img class="img-circle img-size-32 mr-2" style="width:60px;height:60px;" src="' + placeholderImage + '" alt="image">';
                }

                html = html + '</div><div class="orders-tracking"><h6>' + val.name + '</h6><div class="orders-tracking-item-details">';
                if (extra_count > 1 || product.size) {
                    html = html + '<strong>{{trans("lang.extras")}} :</strong>';
                }
                if (extra_count > 1) {
                    html = html + '<div class="extra"><span>{{trans("lang.extras")}} :</span><span class="ext-item">' + extra_html + '</span></div>';
                }
                if (product.size) {
                    html = html + '<div class="type"><span>{{trans("lang.type")}} :</span><span class="ext-size">' + product.size + '</span></div>';
                }

                var final_price = '';
                if (val.discountPrice != 0 && val.discountPrice != "" && val.discountPrice != null && !isNaN(val.discountPrice)) {
                    final_price = parseFloat(val.discountPrice);
                } else {
                    final_price = parseFloat(val.price);
                }
                price_item = parseFloat(final_price).toFixed(decimal_degits);
                
                totalProductPrice = parseFloat(price_item) * parseInt(val.quantity);
                var extras_price = 0;
                if (product.extras != undefined && product.extras != '' && product.extras.length > 0) {
                    extras_price_item = (parseFloat(val.extras_price) * parseInt(val.quantity)).toFixed(decimal_degits);
                    if (parseFloat(extras_price_item) != NaN && val.extras_price != undefined) {
                        extras_price = extras_price_item;
                    }
                    totalProductPrice = parseFloat(extras_price) + parseFloat(totalProductPrice);
                }
                totalProductPrice = parseFloat(totalProductPrice).toFixed(decimal_degits);

                if (currencyAtRight) {
                    price_val = parseFloat(price_item).toFixed(decimal_degits) + "" + currentCurrency;
                    extras_price_val = parseFloat(extras_price).toFixed(decimal_degits) + "" + currentCurrency;
                    totalProductPrice_val = parseFloat(totalProductPrice).toFixed(decimal_degits) + "" + currentCurrency;
                } else {
                    price_val = currentCurrency + "" + parseFloat(price_item).toFixed(decimal_degits);
                    extras_price_val = currentCurrency + "" + parseFloat(extras_price).toFixed(decimal_degits);
                    totalProductPrice_val = currentCurrency + "" + parseFloat(totalProductPrice).toFixed(decimal_degits);
                }

                html = html + '</div></div></td>';
                html = html + '<td>' + price_val + '</td><td>' + val.quantity + '</td><td> + ' + extras_price_val + '</td><td>  ' + totalProductPrice_val + '</td>';

                html = html + '</tr>';
                total_price += parseFloat(totalProductPrice);
            });

            if (currencyAtRight) {
                $('.total_price').text(parseFloat(total_price).toFixed(decimal_degits) + "" + currentCurrency);
            } else {
                $('.total_price').text(currentCurrency + "" + parseFloat(total_price).toFixed(decimal_degits));
            }
            
            return html;
        }

        function buildHTMLProductstotal(snapshotsProducts) {
            var html = '';
            
            var adminCommission = snapshotsProducts.adminCommission;
            var adminCommissionType = snapshotsProducts.adminCommissionType;
            var discount = snapshotsProducts.discount;
            var couponCode = snapshotsProducts.couponCode;
            var takeAway = snapshotsProducts.takeAway;
            var notes = snapshotsProducts.notes;
            var specialDiscount = snapshotsProducts.specialDiscount;
            var intRegex = /^\d+$/;
            var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
            
            let order_subtotal = 0;
            let total_discount = 0;
            let tip_amount = parseFloat(snapshotsProducts.tip_amount || 0);
            let deliveryCharge = parseFloat(snapshotsProducts.deliveryCharge || 0);
            let platformFee = parseFloat(snapshotsProducts.platformFee || 0);
            let packagingCharge = parseFloat(snapshotsProducts.vendor.packagingCharge || 0);

            //  Calculate subtotal and product extras
            for (let i = 0; i < snapshotsProducts.products.length; i++) {
                let product = snapshotsProducts.products[i];
                let basePrice = (product.discountPrice && parseFloat(product.discountPrice) > 0) ? parseFloat(product.discountPrice) : parseFloat(product.price);
                let itemGross = (basePrice + parseFloat(product.extras_price || 0)) * parseInt(product.quantity);
                order_subtotal += itemGross;
            }

            // Total discounts
            let order_discount = parseFloat(snapshotsProducts.discount || 0);
            let special_discount = parseFloat(snapshotsProducts.specialDiscount?.special_discount || 0);
                total_discount = order_discount + special_discount;

            // Calculate item-level taxes (if product-level)
            if (snapshotsProducts.taxScope === "product") {
                let itemSubtotal = order_subtotal;
                let itemCombinedTax = 0;
                snapshotsProducts.products.forEach(product => {
                    let basePrice = (product.discountPrice && parseFloat(product.discountPrice) > 0) ? parseFloat(product.discountPrice) : parseFloat(product.price);
                    let itemGross = (basePrice + parseFloat(product.extras_price || 0)) * parseInt(product.quantity);
                    let itemDiscount = (itemSubtotal > 0) ? (itemGross / itemSubtotal) * total_discount : 0;
                    let itemTaxable = Math.max(0, itemGross - itemDiscount);
                    let itemTaxes = product.taxSetting || [];
                    itemTaxes.forEach(tax => {
                        if (tax.enable) {
                            let taxAmount = 0;
                            if (tax.type === "percentage") {
                                taxAmount = (tax.tax / 100) * itemTaxable;
                            } else {
                                taxAmount = tax.tax * product.quantity;
                            }
                            total_tax_amount += parseFloat(taxAmount);
                            itemCombinedTax += parseFloat(taxAmount);
                        }
                    });
                });
                if(itemCombinedTax > 0){
                    taxBreakdownGrouped.item[''] = itemCombinedTax;
                }
            } 

            // Order-level taxes (if order-level)
            if (snapshotsProducts.taxScope === "order") {
                let orderTaxable = Math.max(0, order_subtotal - total_discount);
                let orderCombinedTax = 0;
                (snapshotsProducts.taxSetting || []).forEach(tax => {
                    if (tax.enable) {
                        let taxAmount = 0;
                        if (tax.type === "percentage") {
                            taxAmount = (tax.tax / 100) * orderTaxable;
                        } else {
                            taxAmount = tax.tax;
                        }
                        total_tax_amount += parseFloat(taxAmount);
                        orderCombinedTax += parseFloat(taxAmount);
                    }
                });
                if(orderCombinedTax > 0){
                    taxBreakdownGrouped.order[''] = orderCombinedTax;
                }
            }

            orderTaxAmountVendor = total_tax_amount;
            orderTaxAmountCustomer = total_tax_amount;
            
            // Delivery, packaging, platform taxes
            let extraCharges = [
                {key: 'delivery', amount: deliveryCharge, taxes: snapshotsProducts.driverDeliveryTax || []},
                {key: 'packaging', amount: packagingCharge, taxes: snapshotsProducts.packagingTax || []},
                {key: 'platform', amount: platformFee, taxes: snapshotsProducts.platformTax || []},
            ];

            
            extraCharges.forEach(scope => {
                if (scope.key === "packaging" && !packagingChargeEnable) {
                    return;
                }
                scope.taxes?.forEach(tax => {
                    if (tax.enable) {
                        let taxAmount = 0;
                        if (tax.type === "percentage") {
                            taxAmount = (tax.tax / 100) * scope.amount;
                        } else {
                            taxAmount = tax.tax;
                        }
                        if(scope.key == "packaging"){
                            total_tax_amount += parseFloat(taxAmount);
                            orderTaxAmountVendor += parseFloat(taxAmount);
                        }
                        orderTaxAmountCustomer += parseFloat(taxAmount);
                        taxBreakdownGrouped[scope.key][tax.title] = (taxBreakdownGrouped[scope.key][tax.title] || 0) + parseFloat(taxAmount);
                    }
                });
            });

            // Final total
            let order_total = (order_subtotal - total_discount) + (packagingChargeEnable ? packagingCharge : 0) + total_tax_amount;
            if(packagingChargeEnable){
                $(".packaging_charge").text('+'+formatCurrency(packagingCharge, currencyData));
            }
            
            $('.total_amount').text(formatCurrency(order_total, currencyData));
            $(".total_tax_amount").text(formatCurrency(total_tax_amount, currencyData));
            
            if (intRegex.test(discount) || floatRegex.test(discount)) {
                discount = parseFloat(discount).toFixed(2);
                if (currencyAtRight) {
                    discount_val = parseFloat(discount).toFixed(decimal_degits) + "" + currentCurrency;
                } else {
                    discount_val = currentCurrency + "" + parseFloat(discount).toFixed(decimal_degits);
                }
                couponCode_html = '';
                if (couponCode) {
                    couponCode_html = '</br><small>{{trans("lang.coupon_codes")}} :' + couponCode + '</small>';
                }
                html = html + '<tr><td class="label">{{trans("lang.discount")}}' + couponCode_html +
                    '</td><td class="discount">-' + discount_val + '</td></tr>';
                    $('.total_discount_amount').text(discount_val);
            }

            $('.total_special_discount_amount').text(formatCurrency(special_discount, currencyData));
            
            html = html + '<tr><td class="seprater" colspan="2"><hr><span>{{ trans('lang.sub_total') }}</span></td></tr>';
            html = html +
                '<tr class="final-rate"><td class="label">{{ trans('lang.sub_total') }}</td><td class="sub_total" style="color:green">(' +
                formatCurrency(order_subtotal, currencyData) + ')</td></tr>';

            renderTaxSection('item', 'Tax on Item Total');
            renderTaxSection('order', 'Tax on Order Total');
            renderTaxSection('packaging', 'Tax on Packaging Fee');
            
            let totalTaxHtml = `
                  <dt class="col-6"><strong>{{ trans('lang.total_tax') }} :</strong></dt>
                  <dd class="col-6"><label class="total_tax_amount">${formatCurrency(total_tax_amount, currencyData)}</label></dd>
                  <dt class="col-12"><hr></dt>`;
            $('#price-breakdown dt').last().before(totalTaxHtml); 
            
            html += '<tr><td class="seprater" colspan="2"><hr></td></tr>';
            
            html = html + '<tr><td class="label">{{trans("lang.total_amount")}}</td><td class="total_amount">' +formatCurrency(order_total, currencyData) + '</td></tr>';

            if (adminCommission != undefined && adminCommissionType != undefined) {
                var commission = 0;
                if (adminCommissionType == "percentage") {
                    commission = (order_subtotal * parseFloat(adminCommission)) / 100;
                } else {
                    commission = parseFloat(adminCommission);
                }
                adminCommission = commission;
            } else if (adminCommission != undefined) {
                var commission = parseFloat(adminCommission);
                adminCommission = commission;
            }
            if (adminCommission) {
                adminCommission = parseFloat(adminCommission).toFixed(2);
                if (currencyAtRight) {
                    adminCommission_val = adminCommission + "" + currentCurrency;
                } else {
                    adminCommission_val = currentCurrency + "" + adminCommission;
                }
                html = html +
                    '<tr><td class="label"><small>( {{trans("lang.admin_commission")}} </small></td><td class="adminCommission_val"><small>' +
                    adminCommission_val + ')</small></td></tr>';
            }
            
            if (notes) {
                html = html + '<tr><td class="label">{{ trans('lang.notes') }}</td><td class="adminCommission_val">' +
                    notes + '</td></tr>';
            }

            return html;
        }

        function printDiv(divName) {

            var css = '@page { size: portrait; }',
                head = document.head || document.getElementsByTagName('head')[0],
                style = document.createElement('style');

            style.type = 'text/css';
            style.media = 'print';

            if (style.styleSheet) {
                style.styleSheet.cssText = css;
            } else {
                style.appendChild(document.createTextNode(css));
            }

            head.appendChild(style);

            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
        
        function renderTaxSection(section, labelSuffix) {
            if (!taxBreakdownGrouped[section]) return;
            let html = '';
            if (!taxHeaderInserted) {
                html += `<dt class="col-12"><hr></dt>`;
                taxHeaderInserted = true;
            }
            for (let title in taxBreakdownGrouped[section]) {
                let taxAmount = parseFloat(taxBreakdownGrouped[section][title]);
                html += `
                    <dt class="col-6">${title} ${labelSuffix} :</dt>
                    <dd class="col-6">
                        ${formatCurrency(taxAmount, currencyData)}
                    </dd>
                `;
            }
            if (!taxFooterInserted && section === 'platform') {
                html += `<dt class="col-12"><hr></dt>`; 
                taxFooterInserted = true;
            }
            $('#price-breakdown dt:last').before(html);
        }
    </script>

    @endsection