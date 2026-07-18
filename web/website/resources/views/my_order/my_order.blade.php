@include('layouts.app')
@include('layouts.header')
@php
    $cityToCountry = file_get_contents(public_path('tz-cities-to-countries.json'));
    $cityToCountry = json_decode($cityToCountry, true);
    $countriesJs = [];
    foreach ($cityToCountry as $key => $value) {
        $countriesJs[$key] = $value;
    }
@endphp
<div class="d-none">
    <div class="bg-primary border-bottom p-3 d-flex align-items-center">
        <a class="toggle togglew toggle-2" href="#"><span></span></a>
        <h4 class="font-weight-bold m-0 text-white">{{ trans('lang.my_orders') }}</h4>
    </div>
</div>
<section class="py-4 siddhi-main-body">
    <div class="container">
        <div class="row">
            <div class="col-md-12 top-nav mb-3">
                <ul class="nav nav-tabsa custom-tabsa border-0 bg-white rounded overflow-hidden shadow-sm p-2 c-t-order" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link border-0 text-dark py-3 active" id="completed-tab" data-toggle="tab" href="#completed" role="tab" aria-controls="completed" aria-selected="true">
                            <i class="feather-check mr-2 text-success mb-0"></i> {{ trans('lang.completed') }}</a>
                    </li>
                    <li class="nav-item border-top" role="presentation">
                        <a class="nav-link border-0 text-dark py-3" id="progress-tab" data-toggle="tab" href="#progress" role="tab" aria-controls="progress" aria-selected="false">
                            <i class="feather-clock mr-2 text-warning mb-0"></i> {{ trans('lang.on_progress') }}</a>
                    </li>
                    <li class="nav-item border-top" role="presentation">
                        <a class="nav-link border-0 text-dark py-3" id="canceled-tab" data-toggle="tab" href="#canceled" role="tab" aria-controls="canceled" aria-selected="false">
                            <i class="feather-x-circle mr-2 text-danger mb-0"></i> {{ trans('lang.canceled') }}</a>
                    </li>
                    <li class="nav-item border-top" role="presentation">
                        <a class="nav-link border-0 text-dark py-3" id="rejected-tab" data-toggle="tab" href="#rejected" role="tab" aria-controls="rejected" aria-selected="false">
                            <i class="feather-slash mr-2 text-danger mb-0"></i> {{ trans('lang.rejected') }}</a>
                    </li>
                </ul>
            </div>
            <div class="tab-content col-md-12" id="myTabContent">
                <div class="tab-pane fade show active" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                    <div class="order-body">
                        <div id="completed_orders"></div>
                    </div>
                </div>
                <div class="tab-pane fade" id="progress" role="tabpanel" aria-labelledby="progress-tab">
                    <div class="order-body">
                        <div id="pending_orders"></div>
                    </div>
                </div>
                <div class="tab-pane fade" id="canceled" role="tabpanel" aria-labelledby="canceled-tab">
                    <div class="order-body">
                        <div id="canceled_orders"></div>
                    </div>
                </div>
                <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
                    <div class="order-body">
                        <div id="rejected_orders"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('layouts.footer')
@include('layouts.nav')

<script type="text/javascript">
    
    var section_id = getCookie('section_id');
    let userCountry = getCookie('userCountryName');
   
    var append_categories = '';
    var completedorsersref = database.collection('vendor_orders').where("author.id", "==", user_uuid).where('section_id', '==', section_id).orderBy('createdAt', 'desc');
    
    var currentCurrency = '';
    var currencyAtRight = false;
    var decimal_degits = 0;
    var products_info = {};

    var refCurrency = database.collection('currencies').where('isActive', '==', true);
    refCurrency.get().then(async function(snapshots) {
        var currencyData = snapshots.docs[0].data();
        currentCurrency = currencyData.symbol;
        currencyAtRight = currencyData.symbolAtRight;
        if (currencyData.decimal_degits) {
            decimal_degits = currencyData.decimal_degits;
        }
    });
    
    var deliveryCharge = 0;
    var deliveryChargeRef = database.collection('settings').doc('DeliveryCharge');
    deliveryChargeRef.get().then(async function(deliveryChargeSnapshots) {
        var deliveryChargeData = deliveryChargeSnapshots.data();
        deliveryCharge = deliveryChargeData.amount;
    });

    var distanceType = 'km';
    var driverRef = database.collection('settings').doc('DriverNearBy');
    driverRef.get().then(async function(snapshot) {
        var driverNearByData = snapshot.data();
        distanceType = driverNearByData.distanceType;
    });

    var taxScope = '';
    var isSelfDeliveryGlobally = false;
    var refGlobal = database.collection('settings').doc("globalSettings");
    refGlobal.get().then(async function(
        settingSnapshots) {
        if (settingSnapshots.data()) {
            var settingData = settingSnapshots.data();
            if (settingData.isSelfDelivery) {
                isSelfDeliveryGlobally = true;
            }
            taxScope = settingData.taxScope;
        }
    });

    var taxSetting = [];
    const scopes = ['delivery', 'order', 'packaging', 'platform', 'product'];
    const taxesByScope = {};
    database.collection('tax').where('country', '==', userCountry).where('enable', '==', true).where('scope', 'in', scopes).where('sectionId', '==', section_id).get().then(snapshot => {
        snapshot.forEach(doc => {
            const tax = doc.data();
            (taxesByScope[tax.scope] ??= []).push(tax);
        });
    });

    var platformCharge = '0';
    let platformFeeSettings = JSON.parse(localStorage.getItem('platformFeeSettings'));
    if (platformFeeSettings && platformFeeSettings.enable) {
        platformCharge = platformFeeSettings.fee;
    }

    var packagingCharge = '0';
    let packagingChargeEnable = localStorage.getItem('packagingChargeEnable') === 'true' || false;

    var place_holder_image = '';
    var ref_placeholder_image = database.collection('settings').doc("placeHolderImage");
    ref_placeholder_image.get().then(async function(snapshots) {
        var placeHolderImage = snapshots.data();
        place_holder_image = placeHolderImage.image;
    });
    
    async function productInfo(id) {
        var doc = await database.collection('vendor_products').doc(id).get();
        products_info[id] = doc.data();
    }
    
    $(document).ready(async function() {

        jQuery("#overlay").show();
        inValidVendors = await getInvaidUserIds();
        getOrders();
        getActiveTab();
        
        $(document).on("click", '.reorder-add-to-cart', async function(event) {
            
            var order_id = $(this).attr('data-id');
            
            var item = [];
            jQuery(".order_" + order_id).each(function() {
                var category_id = jQuery(this).find('.category_id').val();
                var id = jQuery(this).find('.product_id').val();
                var name = jQuery(this).find('.name').val();
                var price = jQuery(this).find('.price').val();
                var image = jQuery(this).find('.image').val();
                var quantity = jQuery(this).find('.quantity').val();
                var extra_price = jQuery(this).find('.extra_price').val();
                var extra = jQuery(this).find('.extra').val();
                var item_price = jQuery(this).find('.item_price').val();
                var taxSetting = JSON.parse(jQuery(this).find('.taxSetting').val());

                var stock_quantity = undefined;
                if (products_info[id] != undefined) {
                    stock_quantity = products_info[id].quantity;
                }
                var variant_info = null;
                if (jQuery(this).find('.variant_info').val()) {
                    variant_info = jQuery(this).find('.variant_info').val()
                    variant_info = JSON.parse(atob(variant_info));
                    if (products_info[id].item_attribute != null) {
                        $.each(products_info[id].item_attribute.variants, function(key, value) {
                            if (value.variant_sku == variant_info.variant_sku) {
                                variant_info.variant_qty = value.variant_quantity;
                                return false;
                            }
                        });
                    }
                    id = id + 'PV' + variant_info.variant_id;
                }
                
                var item_arr = {
                    'id': id,
                    'name': name,
                    'image': image,
                    'price': price,
                    'quantity': quantity,
                    'stock_quantity': stock_quantity,
                    'extra_price': extra_price,
                    'extra': extra,
                    'item_price': item_price,
                    'variant_info': variant_info,
                    'category_id': category_id,
                    'taxSetting': taxSetting,
                }
                item.push(item_arr);
            });

            var vendor_id = jQuery(".restid_" + order_id).val();
            var isSelfDeliveryByVendor = false;
            await database.collection('vendors').doc(vendor_id).get().then(async function(snapshot) {
                if (snapshot.exists) {
                    data = snapshot.data();
                    if (data.hasOwnProperty('isSelfDelivery') && data.isSelfDelivery) {
                        isSelfDeliveryByVendor = true;
                    }
                    if(packagingChargeEnable){
                        packagingCharge = data.packagingCharge ?? '0';
                    }
                }
            });

            var vendor_name = jQuery(".resttitle_" + order_id).val();
            var vendor_location = jQuery(".restlocation_" + order_id).val();
            var vendor_latitude = jQuery(".restlatitude_" + order_id).val();
            var vendor_longitude = jQuery(".restlongitude_" + order_id).val();
            var vendor_image = jQuery(".restphoto_" + order_id).val();

            const payload = {
                _token: '<?php echo csrf_token(); ?>',
                vendor_id,
                vendor_location,
                vendor_name,
                vendor_image,
                vendor_latitude,
                vendor_longitude,
                item,
                deliveryCharge,
                taxSetting,
                decimal_degits,
                distanceType,
                taxScope,
                taxesByScope,
                packagingCharge,
                platformCharge,
                packagingChargeEnable,
                currencyData,
                isSelfDelivery:(isSelfDeliveryByVendor && isSelfDeliveryGlobally) ? true : false
            };
            
            $.ajax({
                type: 'POST',
                url: "<?php echo route('reorder-add-to-cart'); ?>",
                contentType: 'application/json',
                dataType: 'json',
                data: JSON.stringify(payload),
                success: function(data) {
                    window.location.href = '{{ route('checkout') }}';
                }
            });
        });
    });

    function getActiveTab() {
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('activeTab');
        const newUrl = window.location.href.replace(/[?&]activeTab=[^&]+/, '').replace(/&$/, '').replace(/\?$/, '');
        history.replaceState(null, null, newUrl);
        if (activeTab) {
            const defaultActiveTab = document.querySelector('.tab-pane.fade.show.active');
            const defaultActiveTabClass = document.querySelector('.nav-link.border-0.text-dark.py-3.active');
            if (defaultActiveTab) {
                defaultActiveTab.classList.remove('show', 'active');
                defaultActiveTabClass.classList.remove('show', 'active');
            }
            const tabElement = document.querySelector(`#${activeTab}-tab`);
            if (tabElement) {
                tabElement.classList.add('active');
                const tabContentElement = document.querySelector(`#${activeTab}`);
                if (tabContentElement) {
                    tabContentElement.classList.add('show', 'active');
                }
            }
        }
    }
    
    async function getOrders() {
        completedorsersref.get().then(async function(completedorderSnapshots) {
            completed_orders = document.getElementById('completed_orders');
            pending_orders = document.getElementById('pending_orders');
            rejected_orders = document.getElementById('rejected_orders');
            canceled_orders = document.getElementById('canceled_orders');
            completed_orders.innerHTML = '';
            pending_orders.innerHTML = '';
            rejected_orders.innerHTML = '';
            canceled_orders.innerHTML = '';
            completedOrderHtml = await buildHTMLCompletedOrders(completedorderSnapshots);
            pendingOrderHtml = await buildHTMLPendingOrders(completedorderSnapshots);
            rejectedOrdersHtml = await buildHTMLRejectedOrders(completedorderSnapshots);
            canceledOrdersHtml = await buildHTMLCanceledOrders(completedorderSnapshots);
            completed_orders.innerHTML = completedOrderHtml;
            pending_orders.innerHTML = pendingOrderHtml;
            rejected_orders.innerHTML = rejectedOrdersHtml;
            canceled_orders.innerHTML = canceledOrdersHtml;
        })
    }

    async function buildHTMLCompletedOrders(completedorderSnapshots) {
        jQuery("#overlay").show();
        var html = '';
        var alldata = [];
        var number = [];
        if (completedorderSnapshots.docs.length > 0) {
            completedorderSnapshots.docs.forEach((listval) => {
                var datas = listval.data();
                datas.id = listval.id;
                alldata.push(datas);
            });
            for (const listval of alldata) {

                var val = listval;
                
                if (val.status == "Order Completed") {

                    var order_id = val.id;
                    var view_details = "{{ route('completed_order', ':id') }}";
                    view_details = view_details.replace(':id', 'id=' + order_id);
                    var orderDetails = "{{ route('orderDetails', ':id') }}";
                    orderDetails = orderDetails.replace(':id', 'id=' + order_id);
                    var view_contact = "{{ route('contact_us') }}";
                    var view_checkout = "{{ route('checkout') }}";
                    
                    vendorActiveCheck(val.vendorID);

                    let showReorder = false;
                    try {
                        const productSnap = await database
                            .collection('vendor_products')
                            .where("vendorID", "==", val.vendorID)
                            .where("categoryID", "==", val.products[0].category_id)
                            .limit(1)
                            .get();

                        if (!productSnap.empty) {
                            showReorder = productSnap.docs[0].data().publish === true;
                        }
                    } catch (e) {
                        jQuery("#overlay").hide();
                        console.error("Reorder check failed:", e);
                    }  

                    var orderRestaurantImage = '';
                    if (val.vendor.hasOwnProperty('photo') && val.vendor.photo != '') {
                        orderRestaurantImage = val.vendor.photo;
                    } else {
                        orderRestaurantImage = place_holder_image;
                    }
                    html = html + '<div class="pb-3"><div class="p-3 rounded shadow-sm bg-white"><div class="d-flex border-bottom pb-3 m-d-flex"><div class="text-muted mr-3"><a href="javascript:void(0)" class="text-dark check_vendor_' + val.vendorID + '"><img alt="#" src="' + orderRestaurantImage + '" onerror="this.onerror=null;this.src=\'' + place_holder_image +
                        '\'" class="img-fluid order_img rounded"></a></div><div><p class="mb-0 font-weight-bold"><a href="javascript:void(0)" class="text-dark check_vendor_' + val.vendorID + '">' + val.vendor.title + '</a></p><p class="mb-0"><span class="fa fa-map-marker"></span> ' + val.vendor.location + '</p><p>ORDER ' + val.id + '</p><p class="mb-0 small view-det"><a href="' + view_details +
                        '">View Details</a></p></div><div class="ml-auto ord-com-btn"><p class="bg-success text-white py-1 px-2 rounded small mb-1">' + val.status + '</p><p class="small font-weight-bold text-center"><i class="feather-clock"></i> ' + val.createdAt.toDate().toDateString() + '</p></div></div><div class="d-flex pt-3 m-d-flex"><div class="small">';

                    
                    let order_subtotal = 0;
                    let total_discount = 0;
                    let total_tax_amount = 0;
                    let tip_amount = parseFloat(val.tip_amount || 0);
                    let deliveryCharge = parseFloat(val.deliveryCharge || 0);
                    let platformFee = parseFloat(val.platformFee || 0);
                    let packagingCharge = val.packagingChargeEnable ? parseFloat(val.vendor.packagingCharge || 0) : 0;

                    //  Calculate subtotal and product extras
                    for (let i = 0; i < val.products.length; i++) {
                        let product = val.products[i];
                        let basePrice = (product.discountPrice && parseFloat(product.discountPrice) > 0) ? parseFloat(product.discountPrice) : parseFloat(product.price);
                        let itemGross = (basePrice + parseFloat(product.extras_price || 0)) * parseInt(product.quantity);
                        order_subtotal += itemGross;
                    }

                    // Total discounts
                    let order_discount = parseFloat(val.discount || 0);
                    let special_discount = parseFloat(val.specialDiscount?.special_discount || 0);
                        total_discount = order_discount + special_discount;

                    // Calculate item-level taxes (if product-level)
                    if (val.taxScope === "product") {
                        let adminTaxes = taxesByScope['product'] || [];
                        let itemSubtotal = order_subtotal;
                        val.products.forEach(product => {
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
                                }
                            });
                        });
                    } 

                    // Order-level taxes (if order-level)
                    if (val.taxScope === "order") {
                        let orderTaxable = Math.max(0, order_subtotal - total_discount);
                        (val.taxSetting || []).forEach(tax => {
                            if (tax.enable) {
                                let taxAmount = 0;
                                if (tax.type === "percentage") {
                                    taxAmount = (tax.tax / 100) * orderTaxable;
                                } else {
                                    taxAmount = tax.tax;
                                }
                                total_tax_amount += parseFloat(taxAmount);
                            }
                        });
                    }

                    // Delivery, packaging, platform taxes
                    let extraCharges = [
                        {amount: deliveryCharge, taxes: val.driverDeliveryTax || []},
                        {amount: packagingCharge, taxes: val.packagingTax || []},
                        {amount: platformFee, taxes: val.platformTax || []},
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
                            }
                        });
                    });

                    //Final subtotal after discounts
                    order_subtotal = order_subtotal - total_discount;

                    // Final total
                    let order_total = order_subtotal + deliveryCharge + tip_amount + packagingCharge + platformFee + total_tax_amount;

                    if (currencyAtRight) {
                        order_total_val = parseFloat(order_total).toFixed(decimal_degits) + '' + currentCurrency;
                    } else {
                        order_total_val = currentCurrency + '' + parseFloat(order_total).toFixed(decimal_degits);
                    }

                    for (let i = 0; i < val.products.length; i++) {
                        productInfo(val.products[i]['id']);
                        order_subtotal = order_subtotal + parseFloat(val.products[i]['price']) * parseFloat(val.products[i]['quantity']);
                        var productPriceTotal = parseFloat(val.products[i]['price']) * parseFloat(val.products[i]['quantity']);
                        var productExtras = 0;
                        if (val.products[i].hasOwnProperty('extras_price') && val.products[i].hasOwnProperty('extras')) {
                            if (val.products[i].extras_price) {
                                productPriceTotal += parseFloat(val.products[i].extras_price);
                                order_subtotal += parseFloat(val.products[i].extras_price);
                                productExtras = val.products[i].extras_price;
                            }
                        }
                        var extras = '';
                        if (val.products[i].hasOwnProperty('extras') && val.products[i].extras != null) {
                            extras = val.products[i].extras;
                        }
                        var size = '';
                        if (val.products[i].hasOwnProperty('size') && val.products[i].size != '') {
                            size = val.products[i].size;
                        }

                        html = html + '<p class="text- font-weight-bold mb-0">' + val.products[i]['name'] + ' x ' + val.products[i]['quantity'] + '</p>';
                        if (val.products[i]['variant_info']) {
                            html = html + '<div class="variant-info">';
                            html = html + '<ul>';
                            $.each(val.products[i]['variant_info']['variant_options'], function(label, value) {
                                html = html + '<li class="variant"><span class="label">' + label + '</span><span class="value">' + value + '</span></li>';
                            });
                            html = html + '</ul>';
                            html = html + '</div>';
                        }
                        
                        html = html + '<div class="order_' + String(order_id) + '">';
                        html = html + '<input type="hidden" class="product_id" value="' + String(val.products[i]['id']) + '">';
                        html = html + '<input type="hidden" class="name" value="' + String(val.products[i]['name']) + '">';
                        html = html + '<input type="hidden" class="image" value="' + String(val.products[i]['photo']) + '">';
                        html = html + '<input type="hidden" class="price" value="' + parseFloat(val.products[i]['price']) + '">';
                        html = html + '<input type="hidden" class="quantity" value="' + parseFloat(val.products[i]['quantity']) + '">';
                        html = html + '<input type="hidden" class="extra_price" value="' + parseFloat(productExtras) + '">';
                        html = html + '<input type="hidden" class="item_price" value="' + parseFloat(val.products[i]['price']) + '">';
                        if (extras && extras != "null" && extras != null && extras != "") {
                            html = html + '<input type="hidden" class="extra" value="' + extras + '">';
                        }
                        html = html + '<input type="hidden" class="size" value="' + size + '">';
                        html = html + '<input type="hidden" class="taxSetting" value=\'' + JSON.stringify(val.products[i].taxSetting) + '\'>';
                        if (val.products[i]['variant_info']) {
                            html = html + '<input type="hidden" class="variant_info" value="' + btoa(JSON.stringify(val.products[i]['variant_info'])) + '">';
                        }
                        html = html + '<input type="hidden" class="category_id" value="' + val.products[i]['category_id'] + '">';
                        html = html + '</div>';
                    }
                    
                    html = html + '<input type="hidden" class="restid_' + String(order_id) + '" value="' + val.vendor.id + '">';
                    html = html + '<input type="hidden" class="resttitle_' + String(order_id) + '" value="' + val.vendor.title + '">';
                    html = html + '<input type="hidden" class="restlocation_' + String(order_id) + '" value="' + val.vendor.location + '">';
                    html = html + '<input type="hidden" class="restlatitude_' + String(order_id) + '" value="' + val.vendor.latitude + '">';
                    html = html + '<input type="hidden" class="restlongitude_' + String(order_id) + '" value="' +val.vendor.longitude + '">';
                    html = html + '<input type="hidden" class="restphoto_' + String(order_id) + '" value="' + val.vendor.photo + '">';
                    html = html + '<input type="hidden" class="deliveryCharge_' + String(order_id) + '" value="' + deliveryCharge + '">';
                    html = html + '<input type="hidden" class="specialDiscount_' + String(order_id) + '" value="' + special_discount + '">';
                    html = html + '</div><div class="text-muted m-0 ml-auto mr-3 small">Total Payment<br><span class="text-dark font-weight-bold">' + order_total_val + '</span></div> <div class="text-right">';
                    
                    if (showReorder && !inValidVendors.includes(val.vendor.author)) {
                        html = html + '<a href="javascript:void(0);" class="btn btn-primary px-3 reorder-add-to-cart" data-id="' + String(order_id) + '">Reorder</a>';
                    }
                    html = html + '<a href="' + view_contact + '" class="btn btn-outline-primary px-3">Help</a> </div></div></div></div></div></div>';
                }
            }
        }
        if (html == '') {
            html = html + "<p class='text-center font-weight-bold h5 mt-3'>{{ trans('lang.no_results') }}</p>";
        }
        jQuery("#overlay").hide();
        return html;
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
                        } else {
                            view_vendor_details = "javascript:void(0)";
                        }
                        $('.check_vendor_' + vendorId).attr('href', view_vendor_details)
                    } else {
                        $('.check_vendor_' + vendorId).attr('href', 'javascript:void(0)')
                    }
                })
            } else {
                $('.check_vendor_' + vendorId).attr('href', 'javascript:void(0)')
            }
        });
    }

    async function buildHTMLPendingOrders(completedorderSnapshots) {
        jQuery("#overlay").show();

        var html = '';
        var alldata = [];
        var number = [];
        
        if (completedorderSnapshots.docs.length > 0) {

            completedorderSnapshots.docs.forEach((listval) => {
                var datas = listval.data();
                datas.id = listval.id;
                alldata.push(datas);
            });
            
            for (const listval of alldata) {

                var val = listval;
                var order_id = val.id;
                var view_details = "{{ route('pending_order', ':id') }}";
                view_details = view_details.replace(':id', 'id=' + order_id);
                var view_checkout = "{{ route('checkout') }}";
                var view_contact = "{{ route('contact_us') }}";

                vendorActiveCheck(val.vendorID);

                let showReorder = false;
                try {
                    const productSnap = await database
                        .collection('vendor_products')
                        .where("vendorID", "==", val.vendorID)
                        .where("categoryID", "==", val.products[0].category_id)
                        .limit(1)
                        .get();
                    if (!productSnap.empty) {
                        showReorder = productSnap.docs[0].data().publish === true;
                    }
                } catch (e) {
                    jQuery("#overlay").hide();
                    console.error("Reorder check failed:", e);
                }    
                
                if (val.status == "Order Placed" || val.status == "Order Accepted" || val.status == "Driver Pending" || val.status == "Order Shipped" || val.status == "In Transit") {

                    var orderRestaurantImage = '';
                    if (val.vendor.hasOwnProperty('photo') && val.vendor.photo != '') {
                        orderRestaurantImage = val.vendor.photo;
                    } else {
                        orderRestaurantImage = place_holder_image;
                    }

                    html = html + '<div class="pb-3"><div class="p-3 rounded shadow-sm bg-white"><div class="d-flex border-bottom pb-3 m-d-flex"><div class="text-muted mr-3"><a href="javascript:void(0)" class="text-dark check_vendor_' + val.vendorID + '"><img alt="#" src="' + orderRestaurantImage + '" onerror="this.onerror=null;this.src=\'' + place_holder_image +
                        '\'" class="img-fluid order_img rounded"></a></div><div><p class="mb-0 font-weight-bold"><a href="javascript:void(0)" class="text-dark check_vendor_' + val.vendorID + '">' + val.vendor.title + '</a></p><p class="mb-0"><span class="fa fa-map-marker"></span> ' + val.vendor.location + '</p><p>ORDER ' + val.id + '</p><p class="mb-0 small view-det"><a href="' + view_details +
                        '">View Details</a></p></div><div class="ml-auto ord-com-btn"><p class="bg-pending text-white py-1 px-2 rounded small mb-1">' + val.status + '</p><p class="small font-weight-bold text-center"><i class="feather-clock"></i> ' + val.createdAt.toDate().toDateString() + '</p></div></div><div class="d-flex pt-3 m-d-flex"><div class="small">';

                    let order_subtotal = 0;
                    let total_discount = 0;
                    let total_tax_amount = 0;
                    let tip_amount = parseFloat(val.tip_amount || 0);
                    let deliveryCharge = parseFloat(val.deliveryCharge || 0);
                    let platformFee = parseFloat(val.platformFee || 0);
                    let packagingCharge = val.packagingChargeEnable ? parseFloat(val.vendor.packagingCharge || 0) : 0;

                    for (let i = 0; i < val.products.length; i++) {
                        let product = val.products[i];
                        let basePrice = (product.discountPrice && parseFloat(product.discountPrice) > 0) ? parseFloat(product.discountPrice) : parseFloat(product.price);
                        let itemGross = (basePrice + parseFloat(product.extras_price || 0)) * parseInt(product.quantity);
                        order_subtotal += itemGross;
                    }

                    let order_discount = parseFloat(val.discount || 0);
                    let special_discount = parseFloat(val.specialDiscount?.special_discount || 0);
                    total_discount = order_discount + special_discount;

                    // Calculate item-level taxes (if product-level)
                    if (val.taxScope === "product") {
                        let adminTaxes = taxesByScope['product'] || [];
                        let itemSubtotal = order_subtotal;
                        val.products.forEach(product => {
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
                                }
                            });
                        });
                    } 

                    // Order-level taxes (if order-level)
                    if (val.taxScope === "order") {
                        let orderTaxable = Math.max(0, order_subtotal - total_discount);
                        (val.taxSetting || []).forEach(tax => {
                            if (tax.enable) {
                                let taxAmount = 0;
                                if (tax.type === "percentage") {
                                    taxAmount = (tax.tax / 100) * orderTaxable;
                                } else {
                                    taxAmount = tax.tax;
                                }
                                total_tax_amount += parseFloat(taxAmount);
                            }
                        });
                    }

                    // Delivery, packaging, platform taxes
                    let extraCharges = [
                        {amount: deliveryCharge, taxes: val.driverDeliveryTax || []},
                        {amount: packagingCharge, taxes: val.packagingTax || []},
                        {amount: platformFee, taxes: val.platformTax || []},
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
                            }
                        });
                    });

                    order_subtotal = order_subtotal - total_discount;

                    let order_total = order_subtotal + deliveryCharge + tip_amount + packagingCharge + platformFee + total_tax_amount;

                    if (currencyAtRight) {
                        order_total_val = parseFloat(order_total).toFixed(decimal_degits) + '' + currentCurrency;
                    } else {
                        order_total_val = currentCurrency + '' + parseFloat(order_total).toFixed(decimal_degits);
                    }

                    for (let i = 0; i < val.products.length; i++) {
                        productInfo(val.products[i]['id']);
                        order_subtotal = order_subtotal + parseFloat(val.products[i]['price']) * parseFloat(val.products[i]['quantity']);
                        var productPriceTotal = parseFloat(val.products[i]['price']) * parseFloat(val.products[i]['quantity']);
                        var productExtras = 0;
                        if (val.products[i].hasOwnProperty('extras_price') && val.products[i].hasOwnProperty('extras')) {
                            if (val.products[i].extras_price) {
                                productPriceTotal += (parseFloat(val.products[i].extras_price) * parseInt(val.products[i]['quantity']));
                                order_subtotal += (parseFloat(val.products[i].extras_price) * parseInt(val.products[i]['quantity']));
                                productExtras = (parseFloat(val.products[i].extras_price) * parseInt(val.products[i]['quantity']));
                            }
                        }
                        var extras = '';
                        if (val.products[i].hasOwnProperty('extras') && val.products[i].extras != '') {
                            extras = val.products[i].extras;
                        }
                        var size = '';
                        if (val.products[i].hasOwnProperty('size') && val.products[i].size != '') {
                            size = val.products[i].size;
                        }
                        html = html + '<p class="text- font-weight-bold mb-0">' + val.products[i]['name'] + ' x ' + val.products[i]['quantity'] + '</p>';
                        if (val.products[i]['variant_info']) {
                            html = html + '<div class="variant-info">';
                            html = html + '<ul>';
                            $.each(val.products[i]['variant_info']['variant_options'], function(label, value) {
                                html = html + '<li class="variant"><span class="label">' + label + '</span><span class="value">' + value + '</span></li>';
                            });
                            html = html + '</ul>';
                            html = html + '</div>';
                        }
                        
                        html = html + '<div class="order_' + String(order_id) + '">';
                        html = html + '<input type="hidden" class="product_id" value="' + String(val.products[i]['id']) + '">';
                        html = html + '<input type="hidden" class="name" value="' + String(val.products[i]['name']) + '">';
                        html = html + '<input type="hidden" class="image" value="' + String(val.products[i]['photo']) + '">';
                        html = html + '<input type="hidden" class="price" value="' + parseFloat(val.products[i]['price']) + '">';
                        html = html + '<input type="hidden" class="quantity" value="' + parseFloat(val.products[i]['quantity']) + '">';
                        html = html + '<input type="hidden" class="extra_price" value="' + parseFloat(productExtras) + '">';
                        html = html + '<input type="hidden" class="item_price" value="' + parseFloat(val.products[i]['price']) + '">';
                        html = html + '<input type="hidden" class="extra" value="' + extras + '">';
                        html = html + '<input type="hidden" class="size" value="' + size + '">';
                        html = html + '<input type="hidden" class="taxSetting" value=\'' + JSON.stringify(val.products[i].taxSetting) + '\'>';
                        if (val.products[i]['variant_info']) {
                            html = html + '<input type="hidden" class="variant_info" value="' + btoa(JSON.stringify(val.products[i]['variant_info'])) + '">';
                        }
                        html = html + '<input type="hidden" class="category_id" value="' + val.products[i]['category_id'] + '">';
                        html = html + '</div>';
                    }

                    html = html + '<input type="hidden" class="restid_' + String(order_id) + '" value="' + val.vendor.id + '">';
                    html = html + '<input type="hidden" class="resttitle_' + String(order_id) + '" value="' + val.vendor.title + '">';
                    html = html + '<input type="hidden" class="restlocation_' + String(order_id) + '" value="' + val.vendor.location + '">';
                    html = html + '<input type="hidden" class="restlatitude_' + String(order_id) + '" value="' + val.vendor.latitude + '">';
                    html = html + '<input type="hidden" class="restlongitude_' + String(order_id) + '" value="' +val.vendor.longitude + '">';
                    html = html + '<input type="hidden" class="restphoto_' + String(order_id) + '" value="' + val.vendor.photo + '">';
                    html = html + '<input type="hidden" class="deliveryCharge_' + String(order_id) + '" value="' + deliveryCharge + '">';
                    html = html + '<input type="hidden" class="specialDiscount_' + String(order_id) + '" value="' + special_discount + '">';
                    html = html + '</div><div class="text-muted m-0 ml-auto mr-3 small">Total Payment<br><span class="text-dark font-weight-bold">' + order_total_val + '</span></div> <div class="text-right">';
                    if (showReorder && !inValidVendors.includes(val.vendor.author)) {
                        html = html + '<a href="javascript:void(0);" class="btn btn-primary px-3 reorder-add-to-cart" data-id="' + String(order_id) + '">Reorder</a>';
                    }
                    html = html + '<a href="' + view_contact + '" class="btn btn-outline-primary px-3">Help</a> </div></div></div></div></div></div>';
                }
            }
        }
        if (html == '') {
            html = html + "<p class='text-center font-weight-bold h5 mt-3'>{{ trans('lang.no_results') }}</p>";
        }
        jQuery("#overlay").hide();
        return html;
    }

    async function buildHTMLRejectedOrders(completedorderSnapshots) {
        jQuery("#overlay").show();
        var html = '';
        var alldata = [];
        var number = [];
        if (completedorderSnapshots.docs.length > 0) {

            completedorderSnapshots.docs.forEach((listval) => {
                var datas = listval.data();
                datas.id = listval.id;
                alldata.push(datas);
            });
            
            for (const listval of alldata) {
                var val = listval;
                var order_id = val.id;
                var view_details = "{{ route('rejected_order', ':id') }}";
                view_details = view_details.replace(':id', 'id=' + order_id);
                var view_contact = "{{ route('contact_us') }}";
                var view_checkout = "{{ route('checkout') }}";
                vendorActiveCheck(val.vendorID);

                let showReorder = false;
                try {
                    const productSnap = await database
                        .collection('vendor_products')
                        .where("vendorID", "==", val.vendorID)
                        .where("categoryID", "==", val.products[0].category_id)
                        .limit(1)
                        .get();
                    if (!productSnap.empty) {
                        showReorder = productSnap.docs[0].data().publish === true;
                    }
                } catch (e) {
                    jQuery("#overlay").hide();
                    console.error("Reorder check failed:", e);
                }    

                if (val.status == "Driver Rejected" || val.status == "Order Rejected") {               
                    var orderRestaurantImage = '';
                    if (val.vendor.hasOwnProperty('photo') && val.vendor.photo != '') {
                        orderRestaurantImage = val.vendor.photo;
                    } else {
                        orderRestaurantImage = place_holder_image;
                    }
                    html = html + '<div class="pb-3"><div class="p-3 rounded shadow-sm bg-white"><div class="d-flex border-bottom pb-3 m-d-flex"><div class="text-muted mr-3"><a href="javascript:void(0)" class="text-dark check_vendor_' + val.vendorID + '"><img alt="#" src="' + orderRestaurantImage + '" onerror="this.onerror=null;this.src=\'' + place_holder_image +
                        '\'" class="img-fluid order_img rounded"></a></div><div><p class="mb-0 font-weight-bold"><a href="javascript:void(0)" class="text-dark check_vendor_' + val.vendorID + '">' + val.vendor.title + '</a></p><p class="mb-0"><span class="fa fa-map-marker"></span> ' + val.vendor.location + '</p><p>ORDER ' + val.id + '</p><p class="mb-0 small view-det"><a href="' + view_details +
                        '">View Details</a></p></div><div class="ml-auto ord-com-btn"><p class="bg-rejected text-white py-1 px-2 rounded small mb-1">' + val.status + '</p><p class="small font-weight-bold text-center"><i class="feather-clock"></i> ' + val.createdAt.toDate().toDateString() + '</p></div></div><div class="d-flex pt-3 m-d-flex"><div class="small">';
                    
                    let order_subtotal = 0;
                    let total_discount = 0;
                    let total_tax_amount = 0;
                    let tip_amount = parseFloat(val.tip_amount || 0);
                    let deliveryCharge = parseFloat(val.deliveryCharge || 0);
                    let platformFee = parseFloat(val.platformFee || 0);
                    let packagingCharge = val.packagingChargeEnable ? parseFloat(val.vendor.packagingCharge || 0) : 0;

                    //  Calculate subtotal and product extras
                    for (let i = 0; i < val.products.length; i++) {
                        let product = val.products[i];
                        let basePrice = (product.discountPrice && parseFloat(product.discountPrice) > 0) ? parseFloat(product.discountPrice) : parseFloat(product.price);
                        let itemGross = (basePrice + parseFloat(product.extras_price || 0)) * parseInt(product.quantity);
                        order_subtotal += itemGross;
                    }

                    // Total discounts
                    let order_discount = parseFloat(val.discount || 0);
                    let special_discount = parseFloat(val.specialDiscount?.special_discount || 0);
                        total_discount = order_discount + special_discount;

                    // Calculate item-level taxes (if product-level)
                    if (val.taxScope === "product") {
                        let adminTaxes = taxesByScope['product'] || [];
                        let itemSubtotal = order_subtotal;
                        val.products.forEach(product => {
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
                                }
                            });
                        });
                    } 

                    // Order-level taxes (if order-level)
                    if (val.taxScope === "order") {
                        let orderTaxable = Math.max(0, order_subtotal - total_discount);
                        (val.taxSetting || []).forEach(tax => {
                            if (tax.enable) {
                                let taxAmount = 0;
                                if (tax.type === "percentage") {
                                    taxAmount = (tax.tax / 100) * orderTaxable;
                                } else {
                                    taxAmount = tax.tax;
                                }
                                total_tax_amount += parseFloat(taxAmount);
                            }
                        });
                    }

                    // Delivery, packaging, platform taxes
                    let extraCharges = [
                        {amount: deliveryCharge, taxes: val.driverDeliveryTax || []},
                        {amount: packagingCharge, taxes: val.packagingTax || []},
                        {amount: platformFee, taxes: val.platformTax || []},
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
                            }
                        });
                    });
                    //Final subtotal after discounts
                    order_subtotal = order_subtotal - total_discount;

                    // Final total
                    let order_total = order_subtotal + deliveryCharge + tip_amount + packagingCharge + platformFee + total_tax_amount;

                    if (currencyAtRight) {
                        order_total_val = parseFloat(order_total).toFixed(decimal_degits) + '' + currentCurrency;
                    } else {
                        order_total_val = currentCurrency + '' + parseFloat(order_total).toFixed(decimal_degits);
                    }

                    for (let i = 0; i < val.products.length; i++) {
                        productInfo(val.products[i]['id']);
                        order_subtotal = order_subtotal + parseFloat(val.products[i]['price']) * parseFloat(val.products[i]['quantity']);
                        var productPriceTotal = parseFloat(val.products[i]['price']) * parseFloat(val.products[i]['quantity']);
                        var productExtras = 0;
                        if (val.products[i].hasOwnProperty('extras_price') && val.products[i].hasOwnProperty('extras')) {
                            if (val.products[i].extras_price) {
                                productPriceTotal += parseFloat(val.products[i].extras_price);
                                order_subtotal += parseFloat(val.products[i].extras_price);
                                productExtras = val.products[i].extras_price;
                            }
                        }
                        var extras = '';
                        if (val.products[i].hasOwnProperty('extras') && val.products[i].extras != '') {
                            extras = val.products[i].extras;
                        }
                        var size = '';
                        if (val.products[i].hasOwnProperty('size') && val.products[i].size != '') {
                            size = val.products[i].size;
                        }
                        html = html + '<p class="text- font-weight-bold mb-0">' + val.products[i]['name'] + ' x ' + val.products[i]['quantity'] + '</p>';
                        if (val.products[i]['variant_info']) {
                            html = html + '<div class="variant-info">';
                            html = html + '<ul>';
                            $.each(val.products[i]['variant_info']['variant_options'], function(label, value) {
                                html = html + '<li class="variant"><span class="label">' + label + '</span><span class="value">' + value + '</span></li>';
                            });
                            html = html + '</ul>';
                            html = html + '</div>';
                        }
                        
                        html = html + '<div class="order_' + String(order_id) + '">';
                        html = html + '<input type="hidden" class="product_id" value="' + String(val.products[i]['id']) + '">';
                        html = html + '<input type="hidden" class="name" value="' + String(val.products[i]['name']) + '">';
                        html = html + '<input type="hidden" class="image" value="' + String(val.products[i]['photo']) + '">';
                        html = html + '<input type="hidden" class="price" value="' + parseFloat(val.products[i]['price']) + '">';
                        html = html + '<input type="hidden" class="quantity" value="' + parseFloat(val.products[i]['quantity']) + '">';
                        html = html + '<input type="hidden" class="extra_price" value="' + parseFloat(productExtras) + '">';
                        html = html + '<input type="hidden" class="item_price" value="' + parseFloat(val.products[i]['price']) + '">';
                        html = html + '<input type="hidden" class="extra" value="' + extras + '">';
                        html = html + '<input type="hidden" class="size" value="' + size + '">';
                        html = html + '<input type="hidden" class="taxSetting" value=\'' + JSON.stringify(val.products[i].taxSetting) + '\'>';
                        if (val.products[i]['variant_info']) {
                            html = html + '<input type="hidden" class="variant_info" value="' + btoa(JSON.stringify(val.products[i]['variant_info'])) + '">';
                        }
                        html = html + '<input type="hidden" class="category_id" value="' + val.products[i]['category_id'] + '">';
                        html = html + '</div>';
                    }
                    
                    html = html + '<input type="hidden" class="restid_' + String(order_id) + '" value="' + val.vendor.id + '">';
                    html = html + '<input type="hidden" class="resttitle_' + String(order_id) + '" value="' + val.vendor.title + '">';
                    html = html + '<input type="hidden" class="restlocation_' + String(order_id) + '" value="' + val.vendor.location + '">';
                    html = html + '<input type="hidden" class="restlatitude_' + String(order_id) + '" value="' + val.vendor.latitude + '">';
                    html = html + '<input type="hidden" class="restlongitude_' + String(order_id) + '" value="' +val.vendor.longitude + '">';
                    html = html + '<input type="hidden" class="restphoto_' + String(order_id) + '" value="' + val.vendor.photo + '">';
                    html = html + '<input type="hidden" class="deliveryCharge_' + String(order_id) + '" value="' + deliveryCharge + '">';
                    html = html + '<input type="hidden" class="specialDiscount_' + String(order_id) + '" value="' + special_discount + '">';
                    html = html + '</div><div class="text-muted m-0 ml-auto mr-3 small">Total Payment<br><span class="text-dark font-weight-bold">' + order_total_val + '</span></div> <div class="text-right">';
                    if (showReorder && !inValidVendors.includes(val.vendor.author)) {
                        html = html + '<a href="javascript:void(0);" class="btn btn-primary px-3 reorder-add-to-cart" data-id="' + String(order_id) + '">Reorder</a>';
                    }
                    html = html + '<a href="' + view_contact + '" class="btn btn-outline-primary px-3">Help</a> </div></div></div></div></div></div>';
                }
            }
        }
        if (html == '') {
            html = html + "<p class='text-center font-weight-bold h5 mt-3'>{{ trans('lang.no_results') }}</p>";
        }
        jQuery("#overlay").hide();
        return html;
    }

    async function buildHTMLCanceledOrders(completedorderSnapshots) {
        jQuery("#overlay").show();
        var html = '';
        var alldata = [];
        var number = [];

        if (completedorderSnapshots.docs.length > 0) {
            completedorderSnapshots.docs.forEach((listval) => {
                var datas = listval.data();
                datas.id = listval.id;
                alldata.push(datas);
            });
            for (const listval of alldata) {
                var val = listval;
                var order_id = val.id;
                var view_details = "{{ route('cancelled_order', ':id') }}";
                view_details = view_details.replace(':id', 'id=' + order_id);
                var view_contact = "{{ route('contact_us') }}";
                var view_checkout = "{{ route('checkout') }}";
                vendorActiveCheck(val.vendorID);

                let showReorder = false;
                try {
                    const productSnap = await database
                        .collection('vendor_products')
                        .where("vendorID", "==", val.vendorID)
                        .where("categoryID", "==", val.products[0].category_id)
                        .limit(1)
                        .get();
                    if (!productSnap.empty) {
                        showReorder = productSnap.docs[0].data().publish === true;
                    }
                } catch (e) {
                    jQuery("#overlay").hide();
                    console.error("Reorder check failed:", e);
                }    

                if (val.status == "Order Cancelled") {
                    var orderRestaurantImage = '';
                    if (val.vendor.hasOwnProperty('photo') && val.vendor.photo != '') {
                        orderRestaurantImage = val.vendor.photo;
                    } else {
                        orderRestaurantImage = place_holder_image;
                    }
                    html = html + '<div class="pb-3"><div class="p-3 rounded shadow-sm bg-white"><div class="d-flex border-bottom pb-3 m-d-flex"><div class="text-muted mr-3"><a href="javascript:void(0)" class="text-dark check_vendor_' + val.vendorID + '"><img alt="#" src="' + orderRestaurantImage + '" onerror="this.onerror=null;this.src=\'' + place_holder_image +
                        '\'" class="img-fluid order_img rounded"></a></div><div><p class="mb-0 font-weight-bold"><a href="javascript:void(0)" class="text-dark check_vendor_' + val.vendorID + '">' + val.vendor.title + '</a></p><p class="mb-0"><span class="fa fa-map-marker"></span> ' + val.vendor.location + '</p><p>ORDER ' + val.id + '</p><p class="mb-0 small view-det"><a href="' + view_details +
                        '">View Details</a></p></div><div class="ml-auto ord-com-btn"><p class="bg-rejected text-white py-1 px-2 rounded small mb-1">' + val.status + '</p><p class="small font-weight-bold text-center"><i class="feather-clock"></i> ' + val.createdAt.toDate().toDateString() + '</p></div></div><div class="d-flex pt-3 m-d-flex"><div class="small">';
                    
                    let order_subtotal = 0;
                    let total_discount = 0;
                    let total_tax_amount = 0;
                    let tip_amount = parseFloat(val.tip_amount || 0);
                    let deliveryCharge = parseFloat(val.deliveryCharge || 0);
                    let platformFee = parseFloat(val.platformFee || 0);
                    let packagingCharge = val.packagingChargeEnable ? parseFloat(val.vendor.packagingCharge || 0) : 0;

                    //  Calculate subtotal and product extras
                    for (let i = 0; i < val.products.length; i++) {
                        let product = val.products[i];
                        let basePrice = (product.discountPrice && parseFloat(product.discountPrice) > 0) ? parseFloat(product.discountPrice) : parseFloat(product.price);
                        let itemGross = (basePrice + parseFloat(product.extras_price || 0)) * parseInt(product.quantity);
                        order_subtotal += itemGross;
                    }

                    // Total discounts
                    let order_discount = parseFloat(val.discount || 0);
                    let special_discount = parseFloat(val.specialDiscount?.special_discount || 0);
                        total_discount = order_discount + special_discount;

                    // Calculate item-level taxes (if product-level)
                    if (val.taxScope === "product") {
                        let adminTaxes = taxesByScope['product'] || [];
                        let itemSubtotal = order_subtotal;
                        val.products.forEach(product => {
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
                                }
                            });
                        });
                    } 

                    // Order-level taxes (if order-level)
                    if (val.taxScope === "order") {
                        let orderTaxable = Math.max(0, order_subtotal - total_discount);
                        (val.taxSetting || []).forEach(tax => {
                            if (tax.enable) {
                                let taxAmount = 0;
                                if (tax.type === "percentage") {
                                    taxAmount = (tax.tax / 100) * orderTaxable;
                                } else {
                                    taxAmount = tax.tax;
                                }
                                total_tax_amount += parseFloat(taxAmount);
                            }
                        });
                    }

                    // Delivery, packaging, platform taxes
                    let extraCharges = [
                        {amount: deliveryCharge, taxes: val.driverDeliveryTax || []},
                        {amount: packagingCharge, taxes: val.packagingTax || []},
                        {amount: platformFee, taxes: val.platformTax || []},
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
                            }
                        });
                    });

                    //Final subtotal after discounts
                    order_subtotal = order_subtotal - total_discount;

                    // Final total
                    let order_total = order_subtotal + deliveryCharge + tip_amount + packagingCharge + platformFee + total_tax_amount;
                
                    if (currencyAtRight) {
                        order_total_val = parseFloat(order_total).toFixed(decimal_degits) + '' + currentCurrency;
                    } else {
                        order_total_val = currentCurrency + '' + parseFloat(order_total).toFixed(decimal_degits);
                    }

                    for (let i = 0; i < val.products.length; i++) {
                        productInfo(val.products[i]['id']);
                        order_subtotal = order_subtotal + parseFloat(val.products[i]['price']) * parseFloat(val.products[i]['quantity']);
                        var productPriceTotal = parseFloat(val.products[i]['price']) * parseFloat(val.products[i]['quantity']);
                        var productExtras = 0;
                        if (val.products[i].hasOwnProperty('extras_price') && val.products[i].hasOwnProperty('extras')) {
                            if (val.products[i].extras_price) {
                                productPriceTotal += parseFloat(val.products[i].extras_price);
                                order_subtotal += parseFloat(val.products[i].extras_price);
                                productExtras = val.products[i].extras_price;
                            }
                        }
                        var extras = '';
                        if (val.products[i].hasOwnProperty('extras') && val.products[i].extras != '') {
                            extras = val.products[i].extras;
                        }
                        var size = '';
                        if (val.products[i].hasOwnProperty('size') && val.products[i].size != '') {
                            size = val.products[i].size;
                        }
                        html = html + '<p class="text- font-weight-bold mb-0">' + val.products[i]['name'] + ' x ' + val.products[i]['quantity'] + '</p>';
                        if (val.products[i]['variant_info']) {
                            html = html + '<div class="variant-info">';
                            html = html + '<ul>';
                            $.each(val.products[i]['variant_info']['variant_options'], function(label, value) {
                                html = html + '<li class="variant"><span class="label">' + label + '</span><span class="value">' + value + '</span></li>';
                            });
                            html = html + '</ul>';
                            html = html + '</div>';
                        }
                        html = html + '<div class="order_' + String(order_id) + '">';
                        html = html + '<input type="hidden" class="product_id" value="' + String(val.products[i]['id']) + '">';
                        html = html + '<input type="hidden" class="name" value="' + String(val.products[i]['name']) + '">';
                        html = html + '<input type="hidden" class="image" value="' + String(val.products[i]['photo']) + '">';
                        html = html + '<input type="hidden" class="price" value="' + parseFloat(val.products[i]['price']) + '">';
                        html = html + '<input type="hidden" class="quantity" value="' + parseFloat(val.products[i]['quantity']) + '">';
                        html = html + '<input type="hidden" class="extra_price" value="' + parseFloat(productExtras) + '">';
                        html = html + '<input type="hidden" class="item_price" value="' + parseFloat(val.products[i]['price']) + '">';
                        html = html + '<input type="hidden" class="extra" value="' + extras + '">';
                        html = html + '<input type="hidden" class="size" value="' + size + '">';
                        html = html + '<input type="hidden" class="taxSetting" value=\'' + JSON.stringify(val.products[i].taxSetting) + '\'>';
                        if (val.products[i]['variant_info']) {
                            html = html + '<input type="hidden" class="variant_info" value="' + btoa(JSON.stringify(val.products[i]['variant_info'])) + '">';
                        }
                        html = html + '<input type="hidden" class="category_id" value="' + val.products[i]['category_id'] + '">';
                        html = html + '</div>';
                    }
                    
                    html = html + '<input type="hidden" class="restid_' + String(order_id) + '" value="' + val.vendor.id + '">';
                    html = html + '<input type="hidden" class="resttitle_' + String(order_id) + '" value="' + val.vendor.title + '">';
                    html = html + '<input type="hidden" class="restlocation_' + String(order_id) + '" value="' + val.vendor.location + '">';
                    html = html + '<input type="hidden" class="restlatitude_' + String(order_id) + '" value="' + val.vendor.latitude + '">';
                    html = html + '<input type="hidden" class="restlongitude_' + String(order_id) + '" value="' +val.vendor.longitude + '">';
                    html = html + '<input type="hidden" class="restphoto_' + String(order_id) + '" value="' + val.vendor.photo + '">';
                    html = html + '<input type="hidden" class="deliveryCharge_' + String(order_id) + '" value="' + deliveryCharge + '">';
                    html = html + '<input type="hidden" class="specialDiscount_' + String(order_id) + '" value="' + special_discount + '">';
                    html = html + '</div><div class="text-muted m-0 ml-auto mr-3 small">Total Payment<br><span class="text-dark font-weight-bold">' + order_total_val + '</span></div> <div class="text-right">';
                    if (showReorder && !inValidVendors.includes(val.vendor.author)) {
                        html = html + '<a href="javascript:void(0);" class="btn btn-primary px-3 reorder-add-to-cart" data-id="' + String(order_id) + '">Reorder</a>';
                    }
                    html = html + '<a href="' + view_contact + '" class="btn btn-outline-primary px-3">Help</a> </div></div></div></div></div></div>';
                }
            }
        }
        if (html == '') {
            html = html + "<p class='text-center font-weight-bold h5 mt-3'>{{ trans('lang.no_results') }}</p>";
        }
        jQuery("#overlay").hide();
        return html;
    }
    
</script>
