@include('layouts.app')
@include('layouts.header')
<style type="text/css">
    .order_assigned {
        background: #9b59b6;
    }

    .order_accepted {
        background: #008080;
    }
</style>
<div class="d-none">
    <div class="bg-primary border-bottom p-3 d-flex align-items-center">
        <a class="toggle togglew toggle-2" href="#"><span></span></a>
        <h4 class="font-weight-bold m-0 text-white">{{ trans('lang.my_bookings') }}</h4>
    </div>
</div>
<section class="py-4 siddhi-main-body">
    <div class="container">
        <div class="row">
            <div class="col-md-12 top-nav mb-3">
                <ul class="nav nav-tabsa custom-tabsa border-0 bg-white rounded overflow-hidden shadow-sm p-2 c-t-order" id="myTab" role="tablist">
                    <li class="nav-item border-top" role="presentation">
                        <a class="nav-link border-0 text-dark py-3 active" id="pending-tab" data-toggle="tab" href="#pending" role="tab" aria-controls="pending" aria-selected="false">
                            <i class="feather-clock mr-2 text-warning mb-0"></i> {{ trans('lang.pending') }}</a>
                    </li>
                    <li class="nav-item border-top" role="presentation">
                        <a class="nav-link border-0 text-dark py-3" id="accepted-tab" data-toggle="tab" href="#accepted" role="tab" aria-controls="accepted" aria-selected="false">
                            <i class="feather-corner-left-down mr-2 text-warning mb-0"></i> {{ trans('lang.accepted') }}
                        </a>
                    </li>
                    <li class="nav-item border-top" role="presentation">
                        <a class="nav-link border-0 text-dark py-3" id="ongoing-tab" data-toggle="tab" href="#ongoing" role="tab" aria-controls="ongoing" aria-selected="false">
                            <i class="feather-arrow-right-circle mr-2 text-info mb-0"></i> {{ trans('lang.ongoing') }}</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link border-0 text-dark py-3 " id="completed-tab" data-toggle="tab" href="#completed" role="tab" aria-controls="completed" aria-selected="true">
                            <i class="feather-check mr-2 text-success mb-0"></i> {{ trans('lang.completed') }}</a>
                    </li>
                    <li class="nav-item border-top" role="presentation">
                        <a class="nav-link border-0 text-dark py-3" id="canceled-tab" data-toggle="tab" href="#canceled" role="tab" aria-controls="canceled" aria-selected="false">
                            <i class="feather-x-circle mr-2 text-danger mb-0"></i> {{ trans('lang.canceled') }}</a>
                    </li>
                </ul>
            </div>
            <div class="tab-content col-md-12" id="myTabContent">
                <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                    <div class="order-body">
                        <div id="pending_orders"></div>
                    </div>
                </div>
                <div class="tab-pane fade" id="accepted" role="tabpanel" aria-labelledby="accepted-tab">
                    <div class="order-body">
                        <div id="accepted_orders"></div>
                    </div>
                </div>
                <div class="tab-pane fade" id="ongoing" role="tabpanel" aria-labelledby="ongoing-tab">
                    <div class="order-body">
                        <div id="ongoing_orders"></div>
                    </div>
                </div>
                <div class="tab-pane fade " id="completed" role="tabpanel" aria-labelledby="completed-tab">
                    <div class="order-body">
                        <div id="completed_orders"></div>
                    </div>
                </div>
                <div class="tab-pane fade" id="canceled" role="tabpanel" aria-labelledby="canceled-tab">
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

    var append_categories = '';
    var bookingRef = database.collection('provider_orders').where("authorID", "==", user_uuid).where('sectionId', '==', section_id).orderBy('createdAt', 'desc');
    
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

    var place_holder_image = '';
    var ref_placeholder_image = database.collection('settings').doc("placeHolderImage");
    ref_placeholder_image.get().then(async function(snapshots) {
        var placeHolderImage = snapshots.data();
        place_holder_image = placeHolderImage.image;
    });
    
    $(document).ready(function() {
        getBookings();
        getActiveTab();
    });

    async function getBookings() {
        bookingRef.get().then(async function(bookingSnapshots) {
            completed_orders = document.getElementById('completed_orders');
            pending_orders = document.getElementById('pending_orders');
            rejected_orders = document.getElementById('rejected_orders');
            accepted_orders = document.getElementById('accepted_orders');
            ongoing_orders = document.getElementById('ongoing_orders');
            completed_orders.innerHTML = '';
            pending_orders.innerHTML = '';
            rejected_orders.innerHTML = '';
            accepted_orders.innerHTML = '';
            ongoing_orders.innerHTML = '';
            completedOrderHtml = buildHTMLCompletedOrders(bookingSnapshots);
            pendingOrderHtml = buildHTMLPendingOrders(bookingSnapshots);
            rejectedOrdersHtml = buildHTMLRejectedOrders(bookingSnapshots);
            acceptedOrdersHtml = buildHTMLAcceptedOrders(bookingSnapshots);
            ongoingOrdersHtml = buildHTMLOngoingOrders(bookingSnapshots);
            completed_orders.innerHTML = completedOrderHtml;
            pending_orders.innerHTML = pendingOrderHtml;
            rejected_orders.innerHTML = rejectedOrdersHtml;
            accepted_orders.innerHTML = acceptedOrdersHtml;
            ongoing_orders.innerHTML = ongoingOrdersHtml;
        })
    }

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

    function buildHTMLCompletedOrders(bookingSnapshots) {
        var html = '';
        var alldata = [];
        var number = [];
        bookingSnapshots.docs.forEach((listval) => {
            var val = listval.data();
            var order_id = val.id;
            var view_details = "{{ route('completed-booking', ':id') }}";
            view_details = view_details.replace(':id', 'id=' + order_id);
            var view_checkout = "{{ route('ondemand-checkout') }}";
            var view_contact = "{{ route('contact_us') }}";
            var view_service_details = "{{ route('service', ':id') }}";
            view_service_details = view_service_details.replace(':id', val.provider.id);
            checkServiceExist(val.provider.id);
            if (val.status == "Order Completed") {
                var ServiceImage = '';
                if (val.provider.hasOwnProperty('photos') && val.provider.photos.length > 0) {
                    ServiceImage = val.provider.photos[0];
                } else {
                    ServiceImage = place_holder_image;
                }
                html = html + '<div class="pb-3"><div class="p-3 rounded shadow-sm bg-white"><div class="d-flex border-bottom pb-3 m-d-flex"><div class="text-muted mr-3"><a href="' + view_service_details + '" class="text-dark check_service_' + val.provider.id + '"><img alt="#" src="' + ServiceImage + '" onerror="this.onerror=null;this.src=\'' + place_holder_image + '\'" class="img-fluid order_img rounded"></a></div><div><p class="mb-0 font-weight-bold"><a href="' + view_service_details +
                    '" class="text-dark check_service_' + val.provider.id + '">' + val.provider.title + '</a></p><p class="mb-0"><span class="fa fa-map-marker"></span> ' + val.provider.address + '</p><p>{{ trans('lang.booking_id') }} : ' + val.id + '</p><p class="mb-0 small view-det"><a href="' + view_details + '">View Details</a></p></div><div class="ml-auto ord-com-btn"><p class="order_completed text-white py-1 px-2 rounded small mb-1">' + val.status +
                    '</p><p class="small font-weight-bold text-center"><i class="feather-clock"></i> ' + (val.newScheduleDateTime ? ArrowDateTime.formatDate(val.newScheduleDateTime.toDate()) : '') + ' ' + (val.newScheduleDateTime ? ArrowDateTime.formatTime(val.newScheduleDateTime.toDate()) : '') + '</p></div></div><div class="d-flex pt-3 m-d-flex"><div class="small">';
                html = html + '<p class="text- font-weight-bold mb-0">{{ trans('lang.provider') }}: ' + val.provider.authorName + '</p>';
                var totalHours = 0;
                if (val.hasOwnProperty('startTime') && val.hasOwnProperty('endTime')) {
                    if (val.startTime != '' && val.endTime != '' && val.startTime != null && val.endTime != null) {
                        var orderStartTime = val.startTime.toDate();
                        var orderEndTime = val.endTime.toDate();
                        var timeDiff = Math.abs(orderEndTime - orderStartTime);
                        totalHours = timeDiff / 1000;
                        totalHours = parseFloat(totalHours / 3600).toFixed(2);
                    }
                }
                var price = val.provider.price;
                var booking_subtotal = booking_total = 0;
                if (val.provider.hasOwnProperty('disPrice') && val.provider.disPrice != '0') {
                    price = val.provider.disPrice;
                }
                booking_subtotal = booking_subtotal + parseFloat(price) * parseFloat(val.quantity);
                html = html + '<div class="order_' + String(order_id) + '">';
                html = html + '<input type="hidden" class="service_id" value="' + val.provider.id + '">';
                html = html + '<input type="hidden" name="provider_id_' + String(order_id) + '" class="provider_id" value="' + val.authorID + '">';
                html = html + '<input type="hidden" name="category_id_' + String(order_id) + '" class="category_id" value="' + val.provider.categoryId + '">';
                html = html + '<input type="hidden" class="name_' + String(order_id) + '" value="' + val.provider.title + '">';
                html = html + '<input type="hidden" class="image_' + String(order_id) + '" value="' + ServiceImage + '">';
                html = html + '<input type="hidden" name="price_' + String(order_id) + '" class="price" value="' + price + '">';
                html = html + '<input type="hidden" name="dis_price_' + String(order_id) + '" class="dis_price" value="' + val.provider.disPrice + '">';
                html = html + '<input type="hidden" name="quantity_' + String(order_id) + '" class="quantity" value="' + parseFloat(val.quantity) + '">';
                html = html + '</div>';
                if (val.hasOwnProperty('discount') && val.discount) {
                    if (val.discount) {
                        booking_discount = val.discount;
                    } else {
                        booking_discount = 0;
                    }
                } else {
                    booking_discount = 0;
                }
                booking_subtotal = (parseFloat(booking_subtotal) - parseFloat(booking_discount));
                
                let platformFee = parseFloat(val.platformFee || 0);
                let total_tax_amount = 0;
                (val.taxSetting || []).forEach(tax => {
                    if (tax.enable) {
                        let taxAmount = 0;
                        if (tax.type === "percentage") {
                            taxAmount = (tax.tax / 100) * booking_subtotal;
                        } else {
                            taxAmount = tax.tax;
                        }
                        total_tax_amount += parseFloat(taxAmount);
                    }
                });
                
                // Platform taxes
                let extraCharges = [
                    {key: 'platform', amount: platformFee, taxes: val.platformTax || []},
                ];

                extraCharges.forEach(scope => {
                    scope.taxes?.forEach(tax => {
                        if (tax.enable) {
                            let taxAmount = 0;
                            if (tax.type === "percentage") {
                                taxAmount = (tax.tax / 100) * scope.amount;
                            } else {
                                taxAmount = tax.tax;
                            }
                            total_tax_amount += parseFloat(taxAmount);
                        }
                    });
                });

                booking_total = booking_subtotal + platformFee + parseFloat(total_tax_amount);
                var booking_total_val = '';
                if (currencyAtRight) {
                    booking_total_val = booking_total.toFixed(decimal_degits) + '' + currentCurrency;
                } else {
                    booking_total_val = currentCurrency + '' + booking_total.toFixed(decimal_degits);
                }
                html = html + '</div>';
                html = html + '<div class="text-muted m-0 ml-auto mr-3 small total_payment">Total Payment<br><span class="text-dark font-weight-bold">' + booking_total_val + '</span></div> ';
                html = html + '<div class="text-right"><a href="' + view_contact + '" class="btn btn-outline-primary px-3">Help</a></div></div></div></div></div></div>';
            }
        });
        if (html == '') {
            html = html + "<p class='font-weight-bold text-center h5'>{{ trans('lang.no_results') }}</p>";
        }
        return html;
    }

    function buildHTMLOngoingOrders(bookingSnapshots) {
        var html = '';
        var alldata = [];
        var number = [];
        bookingSnapshots.docs.forEach((listval) => {
            var val = listval.data();
            var order_id = val.id;
            var view_details = "{{ route('ongoing-booking', ':id') }}";
            view_details = view_details.replace(':id', 'id=' + order_id);
            var view_checkout = "{{ route('ondemand-checkout') }}";
            var view_contact = "{{ route('contact_us') }}";
            var view_service_details = "{{ route('service', ':id') }}";
            view_service_details = view_service_details.replace(':id', val.provider.id);
            checkServiceExist(val.provider.id);
            if (val.status == "Order Ongoing") {
                var ServiceImage = '';
                if (val.provider.hasOwnProperty('photos') && val.provider.photos.length > 0) {
                    ServiceImage = val.provider.photos[0];
                } else {
                    ServiceImage = place_holder_image;
                }
                html = html + '<div class="pb-3"><div class="p-3 rounded shadow-sm bg-white"><div class="d-flex border-bottom pb-3 m-d-flex"><div class="text-muted mr-3"><a href="' + view_service_details + '" class="text-dark check_service_' + val.provider.id + '"><img alt="#" src="' + ServiceImage + '" onerror="this.onerror=null;this.src=\'' + place_holder_image + '\'" class="img-fluid order_img rounded"></a></div><div><p class="mb-0 font-weight-bold"><a href="' + view_service_details +
                    '" class="text-dark check_service_' + val.provider.id + '">' + val.provider.title + '</a></p><p class="mb-0"><span class="fa fa-map-marker"></span> ' + val.provider.address + '</p><p class="mb-0">{{ trans('lang.booking_id') }} : ' + val.id + '</p><p>{{ trans('lang.otp') }} : ' + val.otp + '</p><p class="mb-0 small view-det"><a href="' + view_details +
                    '">View Details</a></p></div><div class="ml-auto ord-com-btn"><p class="order_ongoing text-white py-1 px-2 rounded small mb-1">' + val.status + '</p><p class="small font-weight-bold text-center"><i class="feather-clock"></i> ' + (val.newScheduleDateTime ? ArrowDateTime.formatDate(val.newScheduleDateTime.toDate()) : '' ) + ' ' + ( val.newScheduleDateTime ? ArrowDateTime.formatTime(val.newScheduleDateTime.toDate()) : '') + '</p></div></div><div class="d-flex pt-3 m-d-flex"><div class="small">';
                html = html + '<p class="text- font-weight-bold mb-0">{{ trans('lang.provider') }}: ' + val.provider.authorName + '</p>';
                var price = val.provider.price;
                var booking_subtotal = booking_total = 0;
                if (val.provider.hasOwnProperty('disPrice') && val.provider.disPrice != '0') {
                    price = val.provider.disPrice;
                }
                booking_subtotal = booking_subtotal + parseFloat(price) * parseFloat(val.quantity);
                html = html + '<div class="order_' + String(order_id) + '">';
                html = html + '<input type="hidden" class="service_id" value="' + val.provider.id + '">';
                html = html + '<input type="hidden" name="provider_id_' + String(order_id) + '" class="provider_id" value="' + val.authorID + '">';
                html = html + '<input type="hidden" name="category_id_' + String(order_id) + '" class="category_id" value="' + val.provider.categoryId + '">';
                html = html + '<input type="hidden" class="name_' + String(order_id) + '" value="' + val.provider.title + '">';
                html = html + '<input type="hidden" class="image_' + String(order_id) + '" value="' + ServiceImage + '">';
                html = html + '<input type="hidden" name="price_' + String(order_id) + '" class="price" value="' + price + '">';
                html = html + '<input type="hidden" name="dis_price_' + String(order_id) + '" class="dis_price" value="' + val.provider.disPrice + '">';
                html = html + '<input type="hidden" name="quantity_' + String(order_id) + '" class="quantity" value="' + parseFloat(val.quantity) + '">';
                html = html + '</div>';
                if (val.hasOwnProperty('discount') && val.discount) {
                    if (val.discount) {
                        booking_discount = val.discount;
                    } else {
                        booking_discount = 0;
                    }
                } else {
                    booking_discount = 0;
                }
                booking_subtotal = (parseFloat(booking_subtotal) - parseFloat(booking_discount));

                let platformFee = parseFloat(val.platformFee || 0);
                let total_tax_amount = 0;
                (val.taxSetting || []).forEach(tax => {
                    if (tax.enable) {
                        let taxAmount = 0;
                        if (tax.type === "percentage") {
                            taxAmount = (tax.tax / 100) * booking_subtotal;
                        } else {
                            taxAmount = tax.tax;
                        }
                        total_tax_amount += parseFloat(taxAmount);
                    }
                });
                
                // Platform taxes
                let extraCharges = [
                    {key: 'platform', amount: platformFee, taxes: val.platformTax || []},
                ];

                extraCharges.forEach(scope => {
                    scope.taxes?.forEach(tax => {
                        if (tax.enable) {
                            let taxAmount = 0;
                            if (tax.type === "percentage") {
                                taxAmount = (tax.tax / 100) * scope.amount;
                            } else {
                                taxAmount = tax.tax;
                            }
                            total_tax_amount += parseFloat(taxAmount);
                        }
                    });
                });

                booking_total = booking_subtotal + platformFee + parseFloat(total_tax_amount);
                var booking_total_val = '';
                if (currencyAtRight) {
                    booking_total_val = booking_total.toFixed(decimal_degits) + '' + currentCurrency;
                } else {
                    booking_total_val = currentCurrency + '' + booking_total.toFixed(decimal_degits);
                }
                html = html + '</div>';
                var noDisplayClass = '';
                var mlAutoClass = '';
                if (val.provider.priceUnit == 'Hourly') {
                    noDisplayClass = 'd-none';
                    mlAutoClass = 'ml-auto';
                }
                html = html + '<div class="text-muted m-0 ml-auto mr-3 small total_payment ' + noDisplayClass + '">Total Payment<br><span class="text-dark font-weight-bold">' + booking_total_val + '</span></div> ';
                html = html + '<div class="text-right ' + mlAutoClass + '"><a href="' + view_contact + '" class="btn btn-outline-primary px-3">Help</a></div></div></div></div></div></div>';
            }
        });
        if (html == '') {
            html = html + "<p class='font-weight-bold text-center h5'>{{ trans('lang.no_results') }}</p>";
        }
        return html;
    }

    function buildHTMLAcceptedOrders(bookingSnapshots) {
        var html = '';
        var alldata = [];
        var number = [];
        bookingSnapshots.docs.forEach((listval) => {
            var val = listval.data();
            var order_id = val.id;
            var view_details = "{{ route('accepted-booking', ':id') }}";
            view_details = view_details.replace(':id', 'id=' + order_id);
            var view_checkout = "{{ route('ondemand-checkout') }}";
            var view_contact = "{{ route('contact_us') }}";
            var view_service_details = "{{ route('service', ':id') }}";
            view_service_details = view_service_details.replace(':id', val.provider.id);
            checkServiceExist(val.provider.id);
            if (val.status == "Order Accepted" || val.status == "Order Assigned") {
                var ServiceImage = '';
                if (val.provider.hasOwnProperty('photos') && val.provider.photos != '') {
                    ServiceImage = val.provider.photos;
                } else {
                    ServiceImage = place_holder_image;
                }
                var stus_clr = '';
                if (val.status == "Order Accepted") {
                    stus_clr = 'order_accepted';
                } else {
                    stus_clr = 'order_assigned';
                }
                html = html + '<div class="pb-3"><div class="p-3 rounded shadow-sm bg-white"><div class="d-flex border-bottom pb-3 m-d-flex"><div class="text-muted mr-3"><a href="' + view_service_details + '" class="text-dark check_service_' + val.provider.id + '"><img alt="#" src="' + ServiceImage + '" onerror="this.onerror=null;this.src=\'' + place_holder_image + '\'" class="img-fluid order_img rounded"></a></div><div><p class="mb-0 font-weight-bold"><a href="' + view_service_details +
                    '" class="text-dark check_service_' + val.provider.id + '">' + val.provider.title + '</a></p><p class="mb-0"><span class="fa fa-map-marker"></span> ' + val.provider.address + '</p><p>{{ trans('lang.booking_id') }} : ' + val.id + '</p><p class="mb-0 small view-det"><a href="' + view_details + '">View Details</a></p></div><div class="ml-auto ord-com-btn"><p class="text-white py-1 px-2 rounded small mb-1 ' + stus_clr + '">' + val.status +
                    '</p><p class="small font-weight-bold text-center"><i class="feather-clock"></i> ' + (val.newScheduleDateTime ? ArrowDateTime.formatDate(val.newScheduleDateTime.toDate()) : '') + ' ' + ( val.newScheduleDateTime ? ArrowDateTime.formatTime(val.newScheduleDateTime.toDate()) : '' )+ '</p></div></div><div class="d-flex pt-3 m-d-flex"><div class="small">';
                html = html + '<p class="text- font-weight-bold mb-0">{{ trans('lang.provider') }}: ' + val.provider.authorName + '</p>';
                var price = val.provider.price;
                if (val.provider.hasOwnProperty('disPrice') && val.provider.disPrice != '0') {
                    price = val.provider.disPrice;
                }
                var booking_subtotal = booking_total = 0;
                booking_subtotal = booking_subtotal + parseFloat(price) * parseFloat(val.quantity);
                html = html + '<div class="order_' + String(order_id) + '">';
                html = html + '<input type="hidden" class="service_id" value="' + val.provider.id + '">';
                html = html + '<input type="hidden" name="provider_id_' + String(order_id) + '" class="provider_id" value="' + val.authorID + '">';
                html = html + '<input type="hidden" name="category_id_' + String(order_id) + '" class="category_id" value="' + val.provider.categoryId + '">';
                html = html + '<input type="hidden" class="name_' + String(order_id) + '" value="' + val.provider.title + '">';
                html = html + '<input type="hidden" class="image_' + String(order_id) + '" value="' + ServiceImage + '">';
                html = html + '<input type="hidden" name="price_' + String(order_id) + '" class="price" value="' + price + '">';
                html = html + '<input type="hidden" name="dis_price_' + String(order_id) + '" class="dis_price" value="' + val.provider.disPrice + '">';
                html = html + '<input type="hidden" name="quantity_' + String(order_id) + '" class="quantity" value="' + parseFloat(val.quantity) + '">';
                html = html + '</div>';
                if (val.hasOwnProperty('discount') && val.discount) {
                    if (val.discount) {
                        booking_discount = val.discount;
                    } else {
                        booking_discount = 0;
                    }
                } else {
                    booking_discount = 0;
                }
                booking_subtotal = (parseFloat(booking_subtotal) - parseFloat(booking_discount));
                
                let platformFee = parseFloat(val.platformFee || 0);
                let total_tax_amount = 0;
                (val.taxSetting || []).forEach(tax => {
                    if (tax.enable) {
                        let taxAmount = 0;
                        if (tax.type === "percentage") {
                            taxAmount = (tax.tax / 100) * booking_subtotal;
                        } else {
                            taxAmount = tax.tax;
                        }
                        total_tax_amount += parseFloat(taxAmount);
                    }
                });
                
                // Platform taxes
                let extraCharges = [
                    {key: 'platform', amount: platformFee, taxes: val.platformTax || []},
                ];

                extraCharges.forEach(scope => {
                    scope.taxes?.forEach(tax => {
                        if (tax.enable) {
                            let taxAmount = 0;
                            if (tax.type === "percentage") {
                                taxAmount = (tax.tax / 100) * scope.amount;
                            } else {
                                taxAmount = tax.tax;
                            }
                            total_tax_amount += parseFloat(taxAmount);
                        }
                    });
                });

                booking_total = booking_subtotal + platformFee + parseFloat(total_tax_amount);
                var booking_total_val = '';
                if (currencyAtRight) {
                    booking_total_val = booking_total.toFixed(decimal_degits) + '' + currentCurrency;
                } else {
                    booking_total_val = currentCurrency + '' + booking_total.toFixed(decimal_degits);
                }
                html = html + '</div>';
                var noDisplayClass = '';
                var mlAutoClass = '';
                if (val.provider.priceUnit == 'Hourly') {
                    noDisplayClass = 'd-none';
                    mlAutoClass = 'ml-auto';
                }
                html = html + '<div class="text-muted m-0 ml-auto mr-3 small total_payment ' + noDisplayClass + '">Total Payment<br><span class="text-dark font-weight-bold">' + booking_total_val + '</span></div> ';
                html = html + '<div class="text-right ' + mlAutoClass + '"><a href="' + view_contact + '" class="btn btn-outline-primary px-3">Help</a></div></div></div></div></div></div>';
            }
        });
        if (html == '') {
            html = html + "<p class=' font-weight-bold text-center h5'>{{ trans('lang.no_results') }}</p>";
        }
        return html;
    }

    function buildHTMLPendingOrders(bookingSnapshots) {
        var html = '';
        var alldata = [];
        var number = [];
        bookingSnapshots.docs.forEach((listval) => {
            var val = listval.data();
            var order_id = val.id;
            var view_details = "{{ route('pending-booking', ':id') }}";
            view_details = view_details.replace(':id', 'id=' + order_id);
            var view_checkout = "{{ route('ondemand-checkout') }}";
            var view_contact = "{{ route('contact_us') }}";
            var view_service_details = "{{ route('service', ':id') }}";
            view_service_details = view_service_details.replace(':id', val.provider.id);
            checkServiceExist(val.provider.id);
            
            if (val.status == "Order Placed") {
                var ServiceImage = '';
                if (val.provider.hasOwnProperty('photos') && val.provider.photos != '') {
                    ServiceImage = val.provider.photos;
                } else {
                    ServiceImage = place_holder_image;
                }
                html = html + '<div class="pb-3"><div class="p-3 rounded shadow-sm bg-white"><div class="d-flex border-bottom pb-3 m-d-flex"><div class="text-muted mr-3"><a href="' + view_service_details + '" class="text-dark check_service_' + val.provider.id + '"><img alt="#" src="' + ServiceImage + '" onerror="this.onerror=null;this.src=\'' + place_holder_image + '\'" class="img-fluid order_img rounded"></a></div><div><p class="mb-0 font-weight-bold"><a href="' + view_service_details +
                    '" class="text-dark check_service_' + val.provider.id + '">' + val.provider.title + '</a></p><p class="mb-0"><span class="fa fa-map-marker"></span> ' + val.provider.address + '</p><p>{{ trans('lang.booking_id') }} :' + val.id + '</p><p class="mb-0 small view-det"><a href="' + view_details + '">View Details</a></p></div><div class="ml-auto ord-com-btn"><p class="bg-pending text-white py-1 px-2 rounded small mb-1">' + val.status +
                    '</p><p class="small font-weight-bold text-center"><i class="feather-clock"></i> ' + ArrowDateTime.formatDate(val.scheduleDateTime.toDate()) + ' ' + ArrowDateTime.formatTime(val.scheduleDateTime.toDate()) + '</p></div></div><div class="d-flex pt-3 m-d-flex"><div class="small">';
                html = html + '<p class="text- font-weight-bold mb-0">{{ trans('lang.provider') }}: ' + val.provider.authorName + '</p>';
                var price = val.provider.price;
                if (val.provider.hasOwnProperty('disPrice') && val.provider.disPrice != '0') {
                    price = val.provider.disPrice;
                }
                var booking_subtotal = booking_total = 0;
                booking_subtotal = booking_subtotal + (parseFloat(price) * parseFloat(val.quantity));
                html = html + '<div class="order_' + String(order_id) + '">';
                html = html + '<input type="hidden" class="service_id" value="' + val.provider.id + '">';
                html = html + '<input type="hidden" name="provider_id_' + String(order_id) + '" class="provider_id" value="' + val.authorID + '">';
                html = html + '<input type="hidden" name="category_id_' + String(order_id) + '" class="category_id" value="' + val.provider.categoryId + '">';
                html = html + '<input type="hidden" class="name_' + String(order_id) + '" value="' + val.provider.title + '">';
                html = html + '<input type="hidden" class="image_' + String(order_id) + '" value="' + ServiceImage + '">';
                html = html + '<input type="hidden" name="price_' + String(order_id) + '" class="price_' + String(order_id) + '" value="' + price + '">';
                html = html + '<input type="hidden" name="dis_price_' + String(order_id) + '" class="dis_price_' + String(order_id) + '" value="' + val.provider.disPrice + '">';
                html = html + '<input type="hidden" name="quantity_' + String(order_id) + '" class="quantity_' + String(order_id) + '" value="' + parseFloat(val.quantity) + '">';
                html = html + '</div>';
                if (val.hasOwnProperty('discount') && val.discount) {
                    if (val.discount) {
                        booking_discount = val.discount;
                    } else {
                        booking_discount = 0;
                    }
                } else {
                    booking_discount = 0;
                }
                booking_subtotal = (parseFloat(booking_subtotal) - parseFloat(booking_discount));

                let platformFee = parseFloat(val.platformFee || 0);
                let total_tax_amount = 0;
                (val.taxSetting || []).forEach(tax => {
                    if (tax.enable) {
                        let taxAmount = 0;
                        if (tax.type === "percentage") {
                            taxAmount = (tax.tax / 100) * booking_subtotal;
                        } else {
                            taxAmount = tax.tax;
                        }
                        total_tax_amount += parseFloat(taxAmount);
                    }
                });
                
                // Platform taxes
                let extraCharges = [
                    {key: 'platform', amount: platformFee, taxes: val.platformTax || []},
                ];

                extraCharges.forEach(scope => {
                    scope.taxes?.forEach(tax => {
                        if (tax.enable) {
                            let taxAmount = 0;
                            if (tax.type === "percentage") {
                                taxAmount = (tax.tax / 100) * scope.amount;
                            } else {
                                taxAmount = tax.tax;
                            }
                            total_tax_amount += parseFloat(taxAmount);
                        }
                    });
                });

                booking_total = booking_subtotal + platformFee + parseFloat(total_tax_amount);

                var booking_total_val = '';
                if (currencyAtRight) {
                    booking_total_val = booking_total.toFixed(decimal_degits) + '' + currentCurrency;
                } else {
                    booking_total_val = currentCurrency + '' + booking_total.toFixed(decimal_degits);
                }
                html = html + '</div>';
                var noDisplayClass = '';
                var mlAutoClass = '';
                if (val.provider.priceUnit == 'Hourly') {
                    noDisplayClass = 'd-none';
                    mlAutoClass = 'ml-auto';
                }
                html = html + '<div class="text-muted m-0 ml-auto mr-3 small total_payment ' + noDisplayClass + '">Total Payment<br><span class="text-dark font-weight-bold">' + booking_total_val + '</span></div> ';
                html = html + '<div class="text-right ' + mlAutoClass + '"><a href="' + view_contact + '" class="btn btn-outline-primary px-3">Help</a></div></div></div></div></div></div>';
            }
        });
        if (html == '') {
            html = html + "<p class=' font-weight-bold text-center h5'>{{ trans('lang.no_results') }}</p>";
        }
        return html;
    }

    function buildHTMLRejectedOrders(bookingSnapshots) {
        var html = '';
        var alldata = [];
        var number = [];
        bookingSnapshots.docs.forEach((listval) => {
            var val = listval.data();
            var order_id = val.id;
            var view_details = "{{ route('cancelled-booking', ':id') }}";
            view_details = view_details.replace(':id', 'id=' + order_id);
            var view_checkout = "{{ route('ondemand-checkout') }}";
            var view_contact = "{{ route('contact_us') }}";
            var view_service_details = "{{ route('service', ':id') }}";
            view_service_details = view_service_details.replace(':id', val.provider.id);
            checkServiceExist(val.provider.id);
            if (val.status == "Order Cancelled" || val.status == "Order Rejected") {
                var ServiceImage = '';
                if (val.provider.hasOwnProperty('photos') && val.provider.photos != '') {
                    ServiceImage = val.provider.photos;
                } else {
                    ServiceImage = place_holder_image;
                }
                html = html + '<div class="pb-3"><div class="p-3 rounded shadow-sm bg-white"><div class="d-flex border-bottom pb-3 m-d-flex"><div class="text-muted mr-3"><a href="' + view_service_details + '" class="text-dark check_service_' + val.provider.id + '"><img alt="#" src="' + ServiceImage + '" onerror="this.onerror=null;this.src=\'' + place_holder_image + '\'" class="img-fluid order_img rounded"></a></div><div><p class="mb-0 font-weight-bold"><a href="' + view_service_details +
                    '" class="text-dark check_service_' + val.provider.id + '">' + val.provider.title + '</a></p><p class="mb-0"><span class="fa fa-map-marker"></span> ' + val.provider.address + '</p><p>{{ trans('lang.booking_id') }} : ' + val.id + '</p><p class="mb-0 small view-det"><a href="' + view_details + '">View Details</a></p></div><div class="ml-auto ord-com-btn"><p class="order_rejected text-white py-1 px-2 rounded small mb-1">' + val.status +
                    '</p><p class="small font-weight-bold text-center"><i class="feather-clock"></i> ' + ArrowDateTime.formatDate(val.scheduleDateTime.toDate()) + ' ' + ArrowDateTime.formatTime(val.scheduleDateTime.toDate()) + '</p></div></div><div class="d-flex pt-3 m-d-flex"><div class="small">';
                html = html + '<p class="text- font-weight-bold mb-0">{{ trans('lang.provider') }}: ' + val.provider.authorName + '</p>';
                var price = val.provider.price;
                if (val.provider.hasOwnProperty('disPrice') && val.provider.disPrice != '0') {
                    price = val.provider.disPrice;
                }
                var booking_subtotal = booking_total = 0;
                booking_subtotal = booking_subtotal + parseFloat(price) * parseFloat(val.quantity);
                html = html + '<div class="order_' + String(order_id) + '">';
                html = html + '<input type="hidden" class="service_id" value="' + val.provider.id + '">';
                html = html + '<input type="hidden" name="provider_id_' + String(order_id) + '" class="provider_id" value="' + val.authorID + '">';
                html = html + '<input type="hidden" name="category_id_' + String(order_id) + '" class="category_id" value="' + val.provider.categoryId + '">';
                html = html + '<input type="hidden" class="name_' + String(order_id) + '" value="' + val.provider.title + '">';
                html = html + '<input type="hidden" class="image_' + String(order_id) + '" value="' + ServiceImage + '">';
                html = html + '<input type="hidden" name="price_' + String(order_id) + '" class="price" value="' + price + '">';
                html = html + '<input type="hidden" name="dis_price_' + String(order_id) + '" class="dis_price" value="' + val.provider.disPrice + '">';
                html = html + '<input type="hidden" name="quantity_' + String(order_id) + '" class="quantity" value="' + parseFloat(val.quantity) + '">';
                html = html + '</div>';
                if (val.hasOwnProperty('discount') && val.discount) {
                    if (val.discount) {
                        booking_discount = val.discount;
                    } else {
                        booking_discount = 0;
                    }
                } else {
                    booking_discount = 0;
                }
                booking_subtotal = (parseFloat(booking_subtotal) - parseFloat(booking_discount));
                
                let platformFee = parseFloat(val.platformFee || 0);
                let total_tax_amount = 0;
                (val.taxSetting || []).forEach(tax => {
                    if (tax.enable) {
                        let taxAmount = 0;
                        if (tax.type === "percentage") {
                            taxAmount = (tax.tax / 100) * booking_subtotal;
                        } else {
                            taxAmount = tax.tax;
                        }
                        total_tax_amount += parseFloat(taxAmount);
                    }
                });
                
                // Platform taxes
                let extraCharges = [
                    {key: 'platform', amount: platformFee, taxes: val.platformTax || []},
                ];

                extraCharges.forEach(scope => {
                    scope.taxes?.forEach(tax => {
                        if (tax.enable) {
                            let taxAmount = 0;
                            if (tax.type === "percentage") {
                                taxAmount = (tax.tax / 100) * scope.amount;
                            } else {
                                taxAmount = tax.tax;
                            }
                            total_tax_amount += parseFloat(taxAmount);
                        }
                    });
                });

                booking_total = booking_subtotal + platformFee + parseFloat(total_tax_amount);
                var booking_total_val = '';
                if (currencyAtRight) {
                    booking_total_val = booking_total.toFixed(decimal_degits) + '' + currentCurrency;
                } else {
                    booking_total_val = currentCurrency + '' + booking_total.toFixed(decimal_degits);
                }
                html = html + '</div>';
                var noDisplayClass = '';
                var mlAutoClass = '';
                if (val.provider.priceUnit == 'Hourly') {
                    noDisplayClass = 'd-none';
                    mlAutoClass = 'ml-auto';
                }
                html = html + '<div class="text-muted m-0 ml-auto mr-3 small total_payment ' + noDisplayClass + '">Total Payment<br><span class="text-dark font-weight-bold">' + booking_total_val + '</span></div> ';
                html = html + '<div class="text-right ' + mlAutoClass + '"><a href="' + view_contact + '" class="btn btn-outline-primary px-3">Help</a></div></div></div></div></div></div>';
            }
        });
        if (html == '') {
            html = html + "<p class=' font-weight-bold text-center h5'>{{ trans('lang.no_results') }}</p>";
        }
        return html;
    }

    async function checkServiceExist(serviceId) {
        await database.collection('providers_services').where('id', '==', serviceId).get().then(async function(snapshot) {
            if (snapshot.docs.length > 0) {
                var data = snapshot.docs[0].data();
                var inValidServiceIds = await getProviderServiceLimit(data.author);
                if (inValidServiceIds.length == 0 || !inValidServiceIds.includes(data.id)) {
                    var view_service_details = "{{ route('service', ':id') }}";
                    view_service_details = view_service_details.replace(':id', serviceId);
                    $('.check_service_' + serviceId).attr('href', view_service_details);
                } else {
                    $('.check_service_' + serviceId).attr('href', 'javascript:void(0)');
                }
            } else {
                $('.check_service_' + serviceId).attr('href', 'javascript:void(0)');
            }
        })
    }
</script>
