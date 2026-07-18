@include('layouts.app')
@include('layouts.header')
<div class="d-none">
    <div class="bg-primary p-3 d-flex align-items-center">
        <a class="toggle togglew toggle-2" href="#"><span></span></a>
        <h4 class="font-weight-bold m-0 text-white">{{ trans('lang.my_orders') }}</h4>
    </div>
</div>
<section class="py-4 siddhi-main-body">
    <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">{{ trans('lang.processing') }}...
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12 top-nav mb-3">
                <ul class="nav nav-tabsa custom-tabsa border-0 bg-white rounded overflow-hidden shadow-sm p-2 c-t-order" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link border-0 text-dark py-3" href="{{ route('my_order') }}?activeTab=completed"> <i class="feather-check mr-2 text-success mb-0"></i>
                            {{ trans('lang.completed') }}</a>
                    </li>
                    <li class="nav-item border-top" role="presentation">
                        <a class="nav-link border-0 text-dark py-3" href="{{ route('my_order') }}?activeTab=progress"> <i class="feather-clock mr-2 text-warning mb-0"></i>
                            {{ trans('lang.on_progress') }}</a>
                    </li>
                    <li class="nav-item border-top" role="presentation">
                        <a class="nav-link border-0 text-dark py-3" href="{{ route('my_order') }}?activeTab=canceled"> <i class="feather-x-circle mr-2 text-danger mb-0"></i>
                            {{ trans('lang.canceled') }}</a>
                    </li>
                    <li class="nav-item border-top" role="presentation">
                         <a class="nav-link border-0 text-dark py-3 active" href="{{ route('my_order') }}?activeTab=rejected">
                            <i class="feather-slash mr-2 text-danger mb-0"></i> {{ trans('lang.rejected') }}</a>
                    </li>
                </ul>
            </div>
            <div class="col-md-12">
                <section class="bg-white siddhi-main-body rounded shadow-sm overflow-hidden">
                    <div class="container p-0">
                        <div class="p-3 border-bottom gendetail-row">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="card p-3">
                                        <h3>{{ trans('lang.general_details') }}</h3>
                                        <div class="form-group widt-100 gendetail-col">
                                            <label class="control-label"><strong>{{ trans('lang.date_created') }}
                                                    : </strong><span id="order-date"></span></label>
                                        </div>
                                        <div class="form-group widt-100 gendetail-col">
                                            <label class="control-label"><strong>{{ trans('lang.order_number') }}
                                                    : </strong><span id="order-number"></span></label>
                                        </div>
                                        <div class="form-group widt-100 gendetail-col">
                                            <label class="control-label"><strong>{{ trans('lang.status') }}
                                                    : </strong><span id="order-status"></span></label>
                                        </div>
                                        <div class="form-group widt-100 gendetail-col">
                                            <label class="control-label"><strong>{{ trans('lang.order_type') }}
                                                    : </strong><span id="order-type"></span></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="card p-3">
                                        <h3>{{ trans('lang.billing_details') }}</h3>
                                        <div class="form-group widt-100 gendetail-col">
                                            <div class="bill-address">
                                                <p><strong>{{ trans('lang.name') }} : </strong><span id="billing_name"></span></p>
                                                <p id="billing_adrs"><strong>{{ trans('lang.address') }} : </strong><span id="billing_line1"></span><br>
                                                    <span id="billing_line2"></span><br>
                                                    <span id="billing_country"></span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="clear-both ml-auto addreview-btn">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-3 border-bottom order-secdetail">
                            <div class="row">
                                <div class="col-6">
                                    <div class=" order-deta-btm-right">
                                        <div class="resturant-detail">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-header-title">{{ trans('lang.vendor') }}</h4>
                                                </div>
                                                <div class="card-body">
                                                    <a href="#" class="row redirecttopage" id="resturant-view">
                                                        <div class="col-4">
                                                            <img src="" class="resturant-img rounded-circle" alt="vendor" width="70px" height="70px">
                                                        </div>
                                                        <div class="col-8">
                                                            <h4 class="vendor-title"></h4>
                                                        </div>
                                                    </a>
                                                    <h5 class="contact-info">{{ trans('lang.contact_info') }}:</h5>
                                                    <p><strong>{{ trans('lang.phone') }}:</strong>
                                                        <span id="vendor_phone"></span>
                                                    </p>
                                                    <p><strong>{{ trans('lang.address') }}:</strong>
                                                        <span id="vendor_address"></span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                </div>
                            </div>
                        </div>
                        <div class="row" id="order-note-box" style="display: none;">
                            <div class="col-lg-12">
                                <div class="p-3 border-bottom">
                                    <h6 class="font-weight-bold">{{ trans('lang.order_notes') }}</h6>
                                    <div id="order-note" class="order-note"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="p-3 border-bottom">
                                    <h6 class="font-weight-bold">{{ trans('lang.order_items') }}</h6>
                                    <div id="order-items"></div>
                                </div>
                                <div class="p-3 border-bottom">
                                    <div class="d-flex align-items-center mb-2">
                                        <h6 class="font-weight-bold mb-1">{{ trans('lang.order_subtotal') }}</h6>
                                        <h6 class="font-weight-bold ml-auto mb-1" id="order-subtotal"></h6>
                                    </div>
                                </div>
                                <div class="p-3 border-bottom">
                                    <div class="d-flex align-items-center mb-2">
                                        <h6 class="font-weight-bold mb-1">{{ trans('lang.order_discount') }}</h6>
                                        <h6 class="font-weight-bold ml-auto mb-1" style="color:red" ; id="order-discount"></h6>
                                    </div>
                                </div>
                                <div class="p-3 border-bottom">
                                    <div class="d-flex align-items-center mb-2">
                                        <h6 class="font-weight-bold mb-1">{{ trans('lang.special') }}
                                            {{ trans('lang.offer') }} {{ trans('lang.discount') }} <span
                                                id="special_offer_text"></span></h6>
                                        <h6 class="font-weight-bold ml-auto mb-1" id="special_offer_discount"
                                            style="color:red"></h6>
                                    </div>
                                </div>
                                <div class="p-3 border-bottom order_shopping_div">
                                    <div class="d-flex align-items-center mb-2">
                                        <h6 class="font-weight-bold mb-1">{{ trans('lang.delivery_charge') }}</h6>
                                        <h6 class="font-weight-bold ml-auto mb-1" id="order-shipping"></h6>
                                    </div>
                                </div>
                                <div class="p-3 border-bottom">
                                    <div class="d-flex align-items-center mb-2">
                                        <h6 class="font-weight-bold mb-1">{{ trans('lang.packaging_charge') }}</h6>
                                        <h6 class="font-weight-bold ml-auto mb-1" id="packaging_charge"></h6>
                                    </div>
                                </div>
                                <div class="p-3 border-bottom">
                                    <div class="d-flex align-items-center mb-2">
                                        <h6 class="font-weight-bold mb-1">{{ trans('lang.platform_charge') }}</h6>
                                        <h6 class="font-weight-bold ml-auto mb-1" id="platform_charge"></h6>
                                    </div>
                                </div>
                                <div class="p-3 border-bottom used_coupon_code_div" style="display:none">
                                    <div class="d-flex align-items-center mb-2">
                                        <h6 class="font-weight-bold mb-1">{{ trans('lang.used_coupon') }}</h6>
                                        <h6 class="font-weight-bold ml-auto mb-1" id="used_coupon_code"></h6>
                                    </div>
                                </div>
                                <div class="p-3 border-bottom order_tip_div">
                                    <div class="d-flex align-items-center mb-2">
                                        <h6 class="font-weight-bold mb-1">{{ trans('lang.tip_amount') }}</h6>
                                        <h6 class="font-weight-bold ml-auto mb-1" id="order-tip-amount"></h6>
                                    </div>
                                </div>
                                <div class="p-3 border-bottom order_tax_div">
                                    <div id="order-tax"></div>
                                    <div class="d-flex align-items-center mb-2">
                                        <h6 class="font-weight-bold mb-1">{{ trans('lang.order_total_tax') }}</h6>
                                        <h6 class="font-weight-bold mb-1 ml-auto" id="total_tax_amount"></h6>
                                    </div>
                                </div>
                                <div class="p-3 bg-white">
                                    <div class="d-flex align-items-center mb-2">
                                        <h6 class="font-weight-bold mb-1">{{ trans('lang.order_total') }}</h6>
                                        <h6 class="font-weight-bold ml-auto mb-1" id="order-total"></h6>
                                    </div>
                                    <p class="m-0 small text-muted">
                                        <br>
                                        {{ trans('lang.thank_you_for_order') }}.
                                    </p>
                                </div>
                                <div class="p-3 border-bottom">
                                    <p class="font-weight-bold small mb-1">
                                        {{ trans('lang.courier') }}
                                    </p>
                                    <img alt="#" src="img/logo_web.png" class="img-fluid sc-siddhi-logo mr-2"><span class="small text-primary font-weight-bold">{{ trans('lang.grocery_courier') }} </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
</section>
@include('layouts.footer')
@include('layouts.nav')

<script type="text/javascript">

    var orderId = "<?php echo $_GET['id']; ?>";
    var append_categories = '';
    var completedorsersref = database.collection('vendor_orders').where('id', "==", orderId);
    
    $(document).ready(async function() {
        $(".dataTables_processing").show();
        inValidVendors = await getInvaidUserIds();
        getOrderDetails();
    });
    
    var place_image = '';
    var ref_place = database.collection('settings').doc("placeHolderImage");
    ref_place.get().then(async function(snapshots) {
        var placeHolderImage = snapshots.data();
        place_image = placeHolderImage.image;
    });
    
    var currencyData = '';
    var currentCurrency = '';
    var currencyAtRight = false;
    var decimal_degits = 0;
    var refCurrency = database.collection('currencies').where('isActive', '==', true);
    refCurrency.get().then(async function(snapshots) {
        currencyData = snapshots.docs[0].data();
        currentCurrency = currencyData.symbol;
        currencyAtRight = currencyData.symbolAtRight;
        if (currencyData.decimal_degits) {
            decimal_degits = currencyData.decimal_degits;
        }
    });

    let userCountry = getCookie('userCountryName');
    const scopes = ['delivery', 'order', 'packaging', 'platform', 'product'];
    const taxesByScope = {};
    database.collection('tax').where('country', '==', userCountry).where('enable', '==', true).where('scope', 'in', scopes).where('sectionId', '==', section_id).get().then(snapshot => {
        snapshot.forEach(doc => {
            const tax = doc.data();
            (taxesByScope[tax.scope] ??= []).push(tax);
        });
    });

    async function getOrderDetails() {

        completedorsersref.get().then(async function(completedorderSnapshots) {

            var orderDetails = completedorderSnapshots.docs[0].data();
            if (orderDetails.author.id != user_uuid) {
                window.location.href = '{{ route('login') }}';
            } else {
                var orderDate = orderDetails.createdAt.toDate().toDateString();
                var time = orderDetails.createdAt.toDate().toLocaleTimeString('en-US');
                $("#order-date").html(orderDate + ' ' + time);
                var billingName = '';
                if (orderDetails.author.hasOwnProperty('firstName')) {
                    billingName = orderDetails.author.firstName;
                }
                if (orderDetails.author.hasOwnProperty('lastName')) {
                    billingName += " " + orderDetails.author.lastName;
                }
                $("#billing_name").text(billingName);
                var billingAddressstring = '';
                if (orderDetails.address.hasOwnProperty('address')) {
                    $("#billing_line1").text(orderDetails.address.address);
                }
                if (orderDetails.address.hasOwnProperty('locality')) {
                    billingAddressstring = billingAddressstring + orderDetails.address.locality;
                }
                if (orderDetails.address.hasOwnProperty('landmark') && orderDetails.address.landmark != null && orderDetails.address.landmark != '') {
                    billingAddressstring = billingAddressstring + " " + orderDetails.address.landmark;
                }
                if (orderDetails.takeAway == true) {
                    billingAddressstring = '';
                    $('#billing_adrs').hide();
                }
                $("#billing_line2").text(billingAddressstring);
                var order_items = order_status = '';
                
                let order_subtotal = 0;
                let total_discount = 0;
                let total_tax_amount = 0;
                let tip_amount = parseFloat(orderDetails.tip_amount || 0);
                let deliveryCharge = parseFloat(orderDetails.deliveryCharge || 0);
                let platformFee = parseFloat(orderDetails.platformFee || 0);
                let packagingCharge = orderDetails.packagingChargeEnable ? parseFloat(orderDetails.vendor.packagingCharge || 0) : 0;

                //  Calculate subtotal and product extras
                for (let i = 0; i < orderDetails.products.length; i++) {
                    let product = orderDetails.products[i];
                    let basePrice = (product.discountPrice && parseFloat(product.discountPrice) > 0) ? parseFloat(product.discountPrice) : parseFloat(product.price);
                    let itemGross = (basePrice + parseFloat(product.extras_price || 0)) * parseInt(product.quantity);
                    order_subtotal += itemGross;
                }

                // Total discounts
                let order_discount = parseFloat(orderDetails.discount || 0);
                let special_discount = parseFloat(orderDetails.specialDiscount?.special_discount || 0);
                    total_discount = order_discount + special_discount;

                // Calculate item-level taxes (if product-level)
                if (orderDetails.taxScope === "product") {
                    let adminTaxes = taxesByScope['product'] || [];
                    let itemSubtotal = order_subtotal;
                    let itemCombinedTax = 0;
                    orderDetails.products.forEach(product => {
                        let basePrice = (product.discountPrice && parseFloat(product.discountPrice) > 0) ? parseFloat(product.discountPrice) : parseFloat(product.price);
                        let itemGross = (basePrice + parseFloat(product.extras_price || 0)) * parseInt(product.quantity);
                        let itemDiscount = (itemSubtotal > 0) ? (itemGross / itemSubtotal) * total_discount : 0;
                        let itemTaxable = Math.max(0, itemGross - itemDiscount);
                        let itemTaxes = product.taxSetting || [];
                        itemTaxes.forEach(tax => {
                            let match = adminTaxes.find(t => t.id === tax.id);
                            if (tax.enable && match) {
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
                    taxBreakdownGrouped.item[''] = itemCombinedTax;
                } 

                // Order-level taxes (if order-level)
                if (orderDetails.taxScope === "order") {
                    let orderTaxable = Math.max(0, order_subtotal - total_discount);
                    let orderCombinedTax = 0;
                    (orderDetails.taxSetting || []).forEach(tax => {
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
                    taxBreakdownGrouped.order[''] = orderCombinedTax;
                }

                // Delivery, packaging, platform taxes
                let extraCharges = [
                    {key: 'delivery', amount: deliveryCharge, taxes: orderDetails.driverDeliveryTax || []},
                    {key: 'packaging', amount: packagingCharge, taxes: orderDetails.packagingTax || []},
                    {key: 'platform', amount: platformFee, taxes: orderDetails.platformTax || []},
                ];

                extraCharges.forEach(scope => {
                    scope.taxes?.forEach(tax => {
                        if (tax.enable) {
                            let taxAmount = 0;
                            if(scope.amount > 0){
                                if (tax.type === "percentage") {
                                    taxAmount = (tax.tax / 100) * scope.amount;
                                } else {
                                    taxAmount = tax.tax;
                                }
                            }
                            total_tax_amount += parseFloat(taxAmount);
                            taxBreakdownGrouped[scope.key][tax.title] = (taxBreakdownGrouped[scope.key][tax.title] || 0) + parseFloat(taxAmount);
                        }
                    });
                });

                $("#total_tax_amount").text(formatCurrency(total_tax_amount,currencyData));
                if(total_tax_amount > 0){
                    $("#order-tax").after("<hr>");
                    renderTaxSection('item', 'Tax on Item Total');
                    renderTaxSection('order', 'Tax on Order Total');
                    renderTaxSection('delivery', 'Tax on Delivery Fee');
                    renderTaxSection('packaging', 'Tax on Packaging Fee');
                    renderTaxSection('platform', 'Tax on Platform Fee');
                }

                //Final subtotal after discounts
                order_subtotal_main = order_subtotal;
                order_subtotal = order_subtotal - total_discount;

                // Final total
                let order_total = order_subtotal + deliveryCharge + tip_amount + packagingCharge + platformFee + total_tax_amount;

                order_items += '<tr>';
                order_items += '<th></th>';
                order_items += '<th class="prod-name">{{ trans('lang.item_name') }}</th>';
                order_items += '<th class="qunt">{{ trans('lang.quantity') }}</th>';
                order_items += '<th class="qunt">{{ trans('lang.extras') }}</th>';
                order_items += '<th class="price">{{ trans('lang.price') }}</th>';
                order_items += '<th class="price text-right">{{ trans('lang.total') }}</th>';
                order_items += '</tr>';
                
                for (let i = 0; i < orderDetails.products.length; i++) {

                    let basePrice = (orderDetails.products[i]['discountPrice'] && parseFloat(orderDetails.products[i]['discountPrice']) > 0) ? parseFloat(orderDetails.products[i]['discountPrice']) : parseFloat(orderDetails.products[i]['price']);

                    var extra_html = '';
                    var productPriceTotal = basePrice * parseInt( orderDetails.products[i]['quantity']);
                    var productExtras = 0;
                    if (orderDetails.products[i].hasOwnProperty('extras_price') && orderDetails.products[i].hasOwnProperty('extras')) {
                        productPriceTotal += parseFloat(orderDetails.products[i].extras_price) * parseInt(orderDetails.products[i]['quantity']);
                        productExtras = parseFloat(orderDetails.products[i].extras_price) * parseInt(orderDetails.products[i]['quantity']);
                    }

                    products_price = "";
                    productPriceTotal_val = "";
                    productExtras_val = "";

                    if (currencyAtRight) {
                        products_price = basePrice.toFixed(decimal_degits) + "" + currentCurrency;
                        productPriceTotal_val = parseFloat(productPriceTotal).toFixed(decimal_degits) +"" + currentCurrency;
                        productExtras_val = parseFloat(productExtras).toFixed(decimal_degits) + "" +currentCurrency;
                    } else {
                        products_price = currentCurrency + "" + basePrice.toFixed(decimal_degits);
                        productPriceTotal_val = currentCurrency + "" + parseFloat(productPriceTotal).toFixed(decimal_degits);
                        productExtras_val = currentCurrency + "" + parseFloat(productExtras).toFixed(decimal_degits);
                    }
                    
                    var extra_html = '';
                    if (orderDetails.products[i].extras != undefined && orderDetails.products[i].extras != '' && orderDetails.products[i].extras.length > 0) {
                        extra_html = extra_html + '<span>';
                        var extra_count = 1;
                        try {
                            orderDetails.products[i].extras.forEach((extra) => {
                                if (extra_count > 1) {
                                    extra_html = extra_html + ',' + extra;
                                } else {
                                    extra_html = extra_html + extra;
                                }
                                extra_count++;
                            });
                        } catch (error) {}
                        extra_html = extra_html + '</span>';
                    }
                    order_items += '<tr>';
                    if (orderDetails.products[i]['photo'] != '') {
                        order_items += '<td class="ord-photo"><img alt="#" src="' + orderDetails.products[i]['photo'] + '" class="img-fluid order_img rounded"></td>';
                    } else {
                        order_items += '<td class="ord-photo"><img alt="#" src="' + place_image + '" class="img-fluid order_img rounded"></td>';
                    }
                    var variant_info = '';
                    if (orderDetails.products[i]['variant_info']) {
                        variant_info += '<div class="variant-info">';
                        variant_info += '<ul>';
                        $.each(orderDetails.products[i]['variant_info']['variant_options'], function(label, value) {
                            variant_info += '<li class="variant"><span class="label">' + label + '</span><span class="value">' + value + '</span></li>';
                        });
                        variant_info += '</ul>';
                        variant_info += '</div>';
                    }
                    order_items += '<td class="prod-name">' + orderDetails.products[i]['name'] + '<div class="extra"><span>{{ trans('lang.extras') }} :</span><span class="ext-item">' + extra_html + variant_info + '</span></div></td>';
                    order_items += '<td class="qunt">x ' + orderDetails.products[i]['quantity'] + '</td>';
                    order_items += '<td class="extras_price">+ ' + productExtras_val + '</td>';
                    order_items += '<td class="product_price">' + products_price + '</td>';
                    order_items += '<td class="total_product_price text-right">' + productPriceTotal_val + '</td>';
                    order_items += '</tr>';
                }
                
                var special_discount_html = "";
                if (orderDetails.hasOwnProperty('specialDiscount')) {
                    if (orderDetails.specialDiscount != null) {
                        if (orderDetails.specialDiscount.specialType != "" && orderDetails.specialDiscount.specialType != null) {
                            if (orderDetails.specialDiscount.specialType == "percentage") {
                                special_discount_html = "(" + orderDetails.specialDiscount.special_discount_label + "%)";
                            }
                            special_discount = orderDetails.specialDiscount.special_discount;
                        }
                    }
                }
                
                order_number = orderDetails['id'];
                order_status = orderDetails['status'];
                order_total_val = formatCurrency(order_total, currencyData);
                order_subtotal_main = formatCurrency(order_subtotal_main, currencyData);
                order_discount_val = formatCurrency(order_discount, currencyData);
                order_shipping_val = formatCurrency(deliveryCharge, currencyData);
                order_tip_amount_val = formatCurrency(tip_amount, currencyData);
                order_special_discount = formatCurrency(special_discount, currencyData);
                order_packaging_charge = formatCurrency(packagingCharge, currencyData);
                order_platform_charge = formatCurrency(platformFee, currencyData);
                
                $("#order-number").html(order_number);
                $("#order-status").html(order_status);
                $("#order-items").html('<table class="order-list">' + order_items + '</table>');
                $("#order-subtotal").html(order_subtotal_main);
                $("#order-shipping").html(order_shipping_val);
                $("#packaging_charge").html(order_packaging_charge);
                $("#platform_charge").html(order_platform_charge);
                $("#order-discount").html("(-" + order_discount_val + ")");
                $('#special_offer_text').text(special_discount_html);
                $('#special_offer_discount').html("(-" + order_special_discount + ")");
                $("#order-tip-amount").html(order_tip_amount_val);
                $("#order-total").append(order_total_val);
                if (orderDetails.hasOwnProperty('couponCode') && orderDetails.couponCode != '') {
                    $('.used_coupon_code_div').show();
                    $("#used_coupon_code").html(orderDetails.couponCode);
                }
                if (orderDetails.hasOwnProperty('takeAway') && orderDetails.takeAway == true) {
                    $("#order-type").html("Take Away");
                } else {
                    $("#order-type").html("Delivery");
                }
                var order_vendor = '<tr>';
                var vendorImage = orderDetails.vendor.photo;
                vendorActiveCheck(orderDetails.vendorID);
                if (vendorImage == '') {
                    vendorImage = place_image;
                }
                $('.resturant-img').attr('src', vendorImage);
                if (orderDetails.vendor.title) {
                    $('.vendor-title').html('<a class="row redirecttopage check_vendor_' + orderDetails.vendorID + '" id="resturant-view">' + orderDetails.vendor.title + '</a>');
                }
                if (orderDetails.vendor.phonenumber) {
                    $('#vendor_phone').text(orderDetails.vendor.phonenumber);
                }
                if (orderDetails.vendor.location) {
                    $('#vendor_address').text(orderDetails.vendor.location);
                }
                if (orderDetails.hasOwnProperty('takeAway') && orderDetails.takeAway == true) {
                    $(".order_driver_details").hide();
                    $(".order_shopping_div").hide();
                    $(".order_tip_div").hide();
                } else if (orderDetails.hasOwnProperty('driver')) {
                    var driverImage = orderDetails.driver.profilePictureURL;
                    if (driverImage == '') {
                        driverImage = place_image;
                    }
                    var name = orderDetails.driver.firstName + " " + orderDetails.driver.lastName;
                    $('.driver-img').attr('src', driverImage);
                    if (name) {
                        $('.driver-name-title').html(name);
                    }
                    if (orderDetails.driver.phoneNumber) {
                        $('#driver_phone').text(orderDetails.driver.phoneNumber);
                    }
                    if (orderDetails.driver.carNumber) {
                        $('#driver_car_number').text(orderDetails.driver.carNumber);
                    }
                }
                if (!orderDetails.driver) {
                    $("#order_driver_details").hide();
                }
                if (orderDetails.notes) {
                    $("#order-note-box").show();
                    $("#order-note").html(orderDetails.notes);
                }
                jQuery("#data-table_processing").hide();
            }
        })
    }
    
    async function vendorActiveCheck(vendorId) {
        await database.collection('vendors').where('id', '==', vendorId).get().then(async function(resultCheckVendor) {
            if (resultCheckVendor.docs.length > 0) {
                var vendorData = resultCheckVendor.docs[0].data();
                await database.collection('users').where('id', '==', vendorData.author).get().then(async function(resultCheckUser) {
                    if (resultCheckUser.docs.length > 0) {
                        if (!inValidVendors.includes(vendorData.author)) {
                            var view_vendor_details = "{{ route('vendor', ':id') }}";
                            view_vendor_details = view_vendor_details.replace(':id', vendorId);
                            $('.check_vendor_' + vendorId).attr('href', view_vendor_details)
                        } else {
                            $('.check_vendor_' + vendorId).attr('href', 'javascript:void(0)')
                        }
                    } else {
                        $('.check_vendor_' + vendorId).attr('href', 'javascript:void(0)')
                    }
                })
            } else {
                $('.check_vendor_' + vendorId).attr('href', 'javascript:void(0)')
            }
        });
    }

    function renderTaxSection(section, labelSuffix) {
        if (!taxBreakdownGrouped[section]) return;
        for (let title in taxBreakdownGrouped[section]) {
            let taxlabel = title;
            let taxAmount = parseFloat(taxBreakdownGrouped[section][title]);
            $("#order-tax").append(
                "<div class='d-flex align-items-center mb-2'>" +
                "<h6 class='font-weight-bold mb-1'>" + taxlabel + " " + labelSuffix + "</h6>" +
                "<h6 class='font-weight-bold mb-1 ml-auto'>" + formatCurrency(taxAmount, currencyData) + "</h6>" +
                "</div>"
            );
        }
    }
</script>
