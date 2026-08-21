
@extends('layouts.app')
<style>
    .business-analytics .card-box {
    background-color: #fff;
    border-radius: 10px;
    padding: 40px 30px 20px;
    position: relative;
    border: 1px solid rgba(180, 208, 224, .5);
    box-shadow: 0 5px 10px rgb(0 0 0 / 5%);
    height: 100%;
    transition: all .3s ease;
    cursor: pointer;
}
.order-status {
    background-color: rgba(110, 137, 175, .0509803922);
    border-radius: 10px;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    height: 100%;
    transition: all .3s ease;
}

element.style {
}
.business-analytics_list > div {
    padding-bottom: 10px;
}
@media (min-width: 992px){
.col-lg-3 {
    -webkit-box-flex: 0;
    -ms-flex: 0 0 25%;
    flex: 0 0 25%;
    max-width: 25%;
}
}
</style>

@section('content')

<div id="main-wrapper" class="page-wrapper" style="min-height: 207px;">

	<div class="container-fluid">

		<div id="data-table_processing" class="dataTables_processing panel panel-default"
             style="display: none;margin-top:20px;">{{trans('lang.processing')}}
        </div>

		<div class="card mb-3 business-analytics" style="display:none">

			<div class="card-body">

				<div class="row flex-between align-items-center g-2 mb-3 order_stats_header">
					<div class="col-sm-6">
						<h4 class="d-flex align-items-center text-capitalize gap-10 mb-0">{{trans('lang.dashboard_business_analytics')}}</h4>
					</div>
				</div>

				<div class="row business-analytics_list">

                        <div class="col-sm-6 col-lg-4 mb-3">
                            <div class="card card-box-with-icon bg--8 redirectionCheck" onclick="location.href='{!! route('payments') !!}'">
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

                        <div class="col-sm-6 col-lg-4 mb-3">
                            <div class="card card-box-with-icon bg--5 redirectionCheck" onclick="location.href='{!! route('orders') !!}'">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div class="card-box-with-content">
                                        <h2 class="text-dark-2 mb-1 h4 order_count" id="order_count"></h2>
                                        <p class="mb-0 small text-dark-2">{{trans('lang.dashboard_total_orders')}}</p>
                                    </div>
                                
                                    <span class="box-icon ab">
                                        <img src="{{asset('images/active_restaurant.png')}}"/>
                                    </span>
                                </div>
                            </div>
                        </div>


                        <div class="col-sm-6 col-lg-4 mb-3">
                            <div class="card card-box-with-icon bg--24 redirectionCheck" onclick="location.href='{!! route('items') !!}'">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div class="card-box-with-content">
                                        <h2 class="text-dark-2 mb-1 h4 product_count" id="product_count"></h2>
                                        <p class="mb-0 small text-dark-2">{{trans('lang.dashboard_total_products')}}</p>
                                    </div>
                                
                                    <span class="box-icon ab">
                                        <img src="{{asset('images/dproduct.png')}}"/>
                                    </span>
                                </div>
                            </div>
                        </div>

					<div class="col-sm-6 col-lg-3">
						<a class="order-status pending redirectionCheck" href="{!! route('orders') !!}">
						<div class="data">
							<i class="mdi mdi-lan-pending"></i>
							<h6 class="status">{{trans('lang.dashboard_order_placed')}}</h6>
						</div> <span class="count" id="placed_count"></span> </a>
					</div>

					<div class="col-sm-6 col-lg-3">
						<a class="order-status confirmed redirectionCheck" href="{!! route('orders') !!}">
						<div class="data">
							<i class="mdi mdi-check-circle"></i>
							<h6 class="status">{{trans('lang.dashboard_order_confirmed')}}</h6>
						</div> <span class="count" id="confirmed_count"></span> </a>
					</div>

					<div class="col-sm-6 col-lg-3">
						<a class="order-status packaging redirectionCheck" href="{!! route('orders') !!}">
						<div class="data">
							<i class="mdi mdi-clipboard-outline"></i>
							<h6 class="status">{{trans('lang.dashboard_order_shipped')}}</h6>
						</div> <span class="count" id="shipped_count"></span> </a>
					</div>

					<div class="col-sm-6 col-lg-3">
						<a class="order-status delivered redirectionCheck" href="{!! route('orders') !!}">
							<div class="data">
								<i class="mdi mdi-check-circle-outline"></i>
								<h6 class="status">{{trans('lang.dashboard_order_completed')}}</h6>
							</div>
							<span class="count" id="completed_count"></span>
						</a>
					</div>

					<div class="col-sm-6 col-lg-3">
						<a class="order-status canceled redirectionCheck" href="{!! route('orders') !!}">
							<div class="data">
								<i class="mdi mdi-window-close"></i>
								<h6 class="status">{{trans('lang.dashboard_order_canceled')}}</h6>
							</div>
							<span class="count" id="canceled_count"></span>
						</a>
					</div>

					<div class="col-sm-6 col-lg-3">
						<a class="order-status failed redirectionCheck" href="{!! route('orders') !!}">
							<div class="data">
								<i class="mdi mdi-alert-circle-outline"></i>
								<h6 class="status">{{trans('lang.dashboard_order_failed')}}</h6>
							</div>
							<span class="count" id="failed_count"></span>
						</a>
					</div>

					<div class="col-sm-6 col-lg-3">
						<a class="order-status failed redirectionCheck" href="{!! route('orders') !!}">
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

		<div class="row business-analytics-graph" style="display:none">

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
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header no-border d-flex justify-content-between">
                        <h3 class="card-title">{{trans('lang.recent_orders')}}</h3>
                        <div class="card-tools">
                            <a href="{{route('orders')}}" class="btn btn-tool btn-sm redirectionCheck"><i class="fa fa-bars"></i> </a>
                        </div>
                    </div>
                    <div class="card-body p-2">
                        <div class="table-responsive m-t-10">
                            <table class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                   cellspacing="0" width="100%" id="orderTable">
                                <thead>
                                <tr>
                                    <th>{{trans('lang.order_id')}}</th>
                                    <th>{{trans('lang.order_user_id')}}</th>
                                    <th>{{trans('lang.order_type')}}</th>
                                    <th>{{trans('lang.total_amount')}}</th>
                                    <th>{{trans('lang.quantity')}}</th>
                                    <th>{{trans('lang.order_date')}}</th>
                                    <th>{{trans('lang.order_order_status_id')}}</th>
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

<script>

	jQuery("#data-table_processing").show();

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

        if(currencyData.decimal_degits){
            decimal_degits = currencyData.decimal_degits;
        }
    });


	var vendorUserId = "<?php echo $id; ?>";
	
	var vendorId = '';
    var authRole = "{{ $authRole }}";
    if(authRole == 'vendor'){
        $('.business-analytics').show();
        $('.business-analytics-graph').show();
    }else{
        $('.business-analytics').hide();
        $('.business-analytics-graph').hide();
    }
    database.collection('users').doc(vendorUserId).get().then(async function(snapshots) {
        var userData = snapshots.data();
        if (userData.hasOwnProperty('vendorID') && userData.vendorID && userData.vendorID != '' && userData.vendorID != null) {
        } else {
            const divs = document.querySelectorAll('.redirectionCheck');
            divs.forEach(div => {
                div.removeAttribute('onclick');
                div.style.cursor = "default";
            });

            const links = document.querySelectorAll('.redirectionCheck');
            links.forEach(link => {
                link.setAttribute('href', '#');
                link.style.pointerEvents = "none";
                link.style.cursor = "default";
            });
        }
    });

	getVendorId(vendorUserId).then(data => {

		vendorId= data;

    	jQuery("#data-table_processing").show();

		  $(document).ready(function () {

	    	db.collection('vendor_orders').where('vendorID',"==",vendorId).get().then(
	        (snapshot) => {
	            jQuery("#order_count").empty();
	            jQuery("#order_count").text(snapshot.docs.length);
	        });

	        db.collection('vendor_products').where('vendorID',"==",vendorId).get().then(
	        (snapshot) => {
	            jQuery("#product_count").empty();
	            jQuery("#product_count").text(snapshot.docs.length);
	            setVisitors();
	        });

			getTotalEarnings();

	        db.collection('vendor_orders').where('vendorID',"==",vendorId).where('status', 'in', ["Order Placed"]).get().then(
	        (snapshot) => {
	            jQuery("#placed_count").empty();
	            jQuery("#placed_count").text(snapshot.docs.length);
	        });

	        db.collection('vendor_orders').where('vendorID',"==",vendorId).where('status', 'in', ["Order Accepted", "Driver Accepted"]).get().then(
	        (snapshot) => {
	            jQuery("#confirmed_count").empty();
	            jQuery("#confirmed_count").text(snapshot.docs.length);
	        });

	        db.collection('vendor_orders').where('vendorID',"==",vendorId).where('status', 'in', ["Order Shipped", "In Transit"]).get().then(
	        (snapshot) => {
	            jQuery("#shipped_count").empty();
	            jQuery("#shipped_count").text(snapshot.docs.length);
	        });

	        db.collection('vendor_orders').where('vendorID',"==",vendorId).where('status', 'in', ["Order Completed"]).get().then(
	        (snapshot) => {
	            jQuery("#completed_count").empty();
	            jQuery("#completed_count").text(snapshot.docs.length);
	        });

	        db.collection('vendor_orders').where('vendorID',"==",vendorId).where('status', 'in', ["Order Rejected"]).get().then(
	        (snapshot) => {
	            jQuery("#canceled_count").empty();
	            jQuery("#canceled_count").text(snapshot.docs.length);
	        });

	        db.collection('vendor_orders').where('vendorID',"==",vendorId).where('status', 'in', ["Driver Rejected"]).get().then(
	        (snapshot) => {
	            jQuery("#failed_count").empty();
	            jQuery("#failed_count").text(snapshot.docs.length);
	        });

	        db.collection('vendor_orders').where('vendorID',"==",vendorId).where('status', 'in', ["Driver Pending"]).get().then(
	        (snapshot) => {
	            jQuery("#pending_count").empty();
	            jQuery("#pending_count").text(snapshot.docs.length);
	        });

	        var placeholder = db.collection('settings').doc('placeHolderImage');
	        placeholder.get().then(async function (snapshotsimage) {
	            var placeholderImageData = snapshotsimage.data();
	            placeholderImage = placeholderImageData.image;

	        })

	        var offest = 1;
	        var pagesize = 10;
	        var start = null;
	        var end = null;
	        var endarray = [];
	        var inx = parseInt(offest) * parseInt(pagesize);
	        var append_listrecent_order = document.getElementById('append_list_recent_order');
	        append_listrecent_order.innerHTML = '';

	        ref = db.collection('vendor_orders');
	        ref.orderBy('createdAt', 'desc').where('status','in',["Order Placed","Order Accepted","Driver Pending","Driver Accepted","Order Shipped","In Transit"]).where('vendorID',"==",vendorId).limit(inx).get().then(async (snapshots) => {
	            html = '';
	            html = await buildOrderHTML(snapshots);
	            if (html != '') {
	                append_listrecent_order.innerHTML = html;
	                start = snapshots.docs[snapshots.docs.length - 1];
	                endarray.push(snapshots.docs[0]);
	            }
                    $('#orderTable').DataTable({
                        order: [],
                        columnDefs: [
                            {
                                targets: 5,
                                type: 'date',
                                render: function (data) {

                                    return data;
                                }
                            },
                            {orderable: false, targets: [6]},
                        ],
                        order: [['5', 'desc']],
                        "language": datatableLang,
                        responsive: true,
                        paging: false,
                        info: false
                    });
	        });

	    })
	})

	async function getVendorId(vendorUser){
	    var vendorId = '';
	    var ref;
	    await db.collection('vendors').where('author',"==",vendorUser).get().then(async function(vendorSnapshots){
            if(vendorSnapshots.docs.length>0){
                var vendorData = vendorSnapshots.docs[0].data();
                vendorId = vendorData.id;
            }
	        
	    })
	    return vendorId;
	}

    async function getTotalEarnings() {

        const months = Array(12).fill(0);
        const currentYear = new Date().getFullYear();

        let totalEarning = 0;
        let adminCommissionTotal = 0;

        await database.collection('vendor_orders').where('vendorID', "==", vendorId).where('status', 'in', ["Order Completed"]).get().then((orderSnapshots) => {
            orderSnapshots.docs.forEach((doc) => {

                let order = doc.data();
                
                let order_subtotal = 0;
                let total_discount = 0;
                let total_tax_amount = 0;
                let tip_amount = parseFloat(order.tip_amount || 0);
                let deliveryCharge = parseFloat(order.deliveryCharge || 0);
                let platformFee = parseFloat(order.platformFee || 0);
                let packagingCharge = parseFloat(order.vendor.packagingCharge || 0);
                let packagingChargeEnable = order.packagingChargeEnable;

                //  Calculate subtotal and product extras
                for (let i = 0; i < order.products.length; i++) {
                    let product = order.products[i];
                    let basePrice = (product.discountPrice && parseFloat(product.discountPrice) > 0) ? parseFloat(product.discountPrice) : parseFloat(product.price);
                    let itemGross = (basePrice + parseFloat(product.extras_price || 0)) * parseInt(product.quantity);
                    order_subtotal += itemGross;
                }

                // Total discounts
                let order_discount = parseFloat(order.discount || 0);
                let special_discount = parseFloat(order.specialDiscount?.special_discount || 0);
                    total_discount = order_discount + special_discount;

                // Calculate item-level taxes (if product-level)
                if (order.taxScope === "product") {
                    let itemSubtotal = order_subtotal;
                    order.products.forEach(product => {
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
                                    taxAmount = tax.tax;
                                }
                                total_tax_amount += parseFloat(taxAmount);
                            }
                        });
                    });
                } 

                // Order-level taxes (if order-level)
                if (order.taxScope === "order") {
                    let orderTaxable = Math.max(0, order_subtotal - total_discount);
                    (order.taxSetting || []).forEach(tax => {
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

                // Packaging taxes
                if(packagingChargeEnable){
                    let extraCharges = [
                        {amount: packagingCharge, taxes: order.packagingTax || []},
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
                }

                //Final subtotal after discounts
                order_subtotal = order_subtotal - total_discount;

                // Final total
                let order_total = order_subtotal + (packagingChargeEnable ? packagingCharge : 0) + total_tax_amount;
                
                // Total earning
                totalEarning += order_total;

                // Monthly graph
                if (order.createdAt) {
                    let date = order.createdAt.toDate();
                    if (date.getFullYear() === currentYear) {
                        months[date.getMonth()] += order_total;
                    }
                }
            });
        });

        let formattedTotal = currencyAtRight
            ? totalEarning.toFixed(decimal_degits) + currentCurrency
            : currentCurrency + totalEarning.toFixed(decimal_degits);
    
        $("#earnings_count, #earnings_count_graph, .earnings_over_time, #total_earnings_header").text(formattedTotal);

        let labels = ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'];
        
        renderChart($('#sales-chart'), months, labels);

        setCommision();
        
        jQuery("#data-table_processing").hide();
    }
    
    async function buildOrderHTML(snapshots) {
        var html = '';
        var count = 1;
        snapshots.docs.forEach(async (listval) => {
            val = listval.data();
            val.id = listval.id;
            if(authRole == 'vendor'){
                route = '<?php echo route("orders.edit", ":id"); ?>';
                route = route.replace(':id', val.id);
            }else{
                const perm = await getEmployeePermissionForTitle(vendorUserId, "Manage Order");                 

                if (perm.isActive) {
                    route = '<?php echo route("orders.edit", ":id"); ?>';
                    route = route.replace(':id', val.id);
                } else{
                    route = 'javascript:void(0)';
                }
            }

            html = html + '<tr>';

            html = html + '<td data-url="' + route + '" class="redirecttopage">' + val.id + '</td>';

            html = html + '<td data-url="' + route + '" class="redirecttopage">' + val.author.firstName +' '+val.author.lastName+'</td>';

            if(val.takeAway == true){
            	html = html + '<td data-url="' + route + '" class="redirecttopage">Take away</td>';
            }else{
            	html = html + '<td data-url="' + route + '" class="redirecttopage">Order Delivery</td>';
            }

            var price = 0;
            if (val.deliveryCharge != undefined) {
                price = parseInt(val.deliveryCharge) + price;
            }
            if (val.tip_amount != undefined) {
                price = parseInt(val.tip_amount) + price;
            }

            var date = ArrowDateTime.formatDate(val.createdAt.toDate());
            var time = ArrowDateTime.formatTime(val.createdAt.toDate());

            var price = buildHTMLProductstotal(val);

            html = html + '<td data-url="' + route + '" class="redirecttopage">' + price + '</td>';
            html = html + '<td data-url="' + route + '" class="redirecttopage"><i class="mdi mdi-cart"></i> ' + val.products[0].quantity + '</td>';
            html = html + '<td data-url="' + route + '" class="redirecttopage">' + date + ' ' + time + '</td>';
              if (val.status == 'Order Placed') {
                html = html + '<td class="order_placed"><span>' + ArrowI18n.status(val.status) + '</span></td>';

            } else if (val.status == 'Order Accepted') {
                html = html + '<td data-url="' + route + '" class="order_accepted redirecttopage"><span>' + ArrowI18n.status(val.status) + '</span></td>';

            } else if (val.status == 'Order Rejected') {
                html = html + '<td data-url="' + route + '" class="order_rejected redirecttopage"><span>' + ArrowI18n.status(val.status) + '</span></td>';

            } else if (val.status == 'Driver Pending') {
                html = html + '<td data-url="' + route + '" class="driver_pending redirecttopage"><span>' + ArrowI18n.status(val.status) + '</span></td>';

            } else if (val.status == 'Driver Rejected') {
                html = html + '<td data-url="' + route + '" class="driver_rejected redirecttopage"><span>' + ArrowI18n.status(val.status) + '</span></td>';

            } else if (val.status == 'Order Shipped') {
                html = html + '<td data-url="' + route + '" class="order_shipped redirecttopage"><span>' + ArrowI18n.status(val.status) + '</span></td>';

            } else if (val.status == 'In Transit') {
                html = html + '<td data-url="' + route + '" class="in_transit redirecttopage"><span>' + ArrowI18n.status(val.status) + '</span></td>';

            } else if (val.status == 'Order Completed') {
                html = html + '<td data-url="' + route + '" class="order_completed redirecttopage"><span>' + ArrowI18n.status(val.status) + '</span></td>';

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
                    intersect: intersect
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
                            	return currentCurrency + value.toFixed(decimal_degits);
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
        let order_subtotal = 0;
        let total_discount = 0;
        let total_tax_amount = 0;
        let tip_amount = parseFloat(snapshotsProducts.tip_amount || 0);
        let deliveryCharge = parseFloat(snapshotsProducts.deliveryCharge || 0);
        let platformFee = parseFloat(snapshotsProducts.platformFee || 0);
        let packagingCharge = parseFloat(snapshotsProducts.vendor.packagingCharge || 0);
        let packagingChargeEnable = snapshotsProducts.packagingChargeEnable;
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
                    }
                });
            });
        } 

        // Order-level taxes (if order-level)
        if (snapshotsProducts.taxScope === "order") {
            let orderTaxable = Math.max(0, order_subtotal - total_discount);
            (snapshotsProducts.taxSetting || []).forEach(tax => {
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
            {key: 'packaging', amount: packagingCharge, taxes: snapshotsProducts.packagingTax || []},
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
                    total_tax_amount += parseFloat(taxAmount);
                }
            });
        });

        //Final subtotal after discounts
        order_subtotal = order_subtotal - total_discount;

        // Final total
        let order_total = order_subtotal + (packagingChargeEnable ? packagingCharge : 0) + total_tax_amount;

        if (currencyAtRight) {
            order_total_val = parseFloat(order_total).toFixed(decimal_degits) + '' + currentCurrency;
        } else {
            order_total_val = currentCurrency + '' + parseFloat(order_total).toFixed(decimal_degits);
        }

        return order_total_val;
    }

    function setVisitors(){

    	const data = {
		  labels: [
		    "{{trans('lang.dashboard_total_orders')}}",
		    "{{trans('lang.dashboard_total_products')}}",
		  ],
		  datasets: [{
		    data: [jQuery("#order_count").text(), jQuery("#product_count").text()],
		    backgroundColor: [
		      '#B1DB6F',
		      '#7360ed',
		    ],
		    hoverOffset: 4
		  }]
		};

        return new Chart('visitors',{
            type: 'doughnut',
            data: data,
            options: {
            	maintainAspectRatio: false,
            }
        })
    }

    function setCommision(){

    	const data = {
		  labels: [
		    "{{trans('lang.dashboard_total_earnings')}}"
		  ],
		  datasets: [{
		    data: [jQuery("#earnings_count").text().replace(currentCurrency,"")],
		    backgroundColor: [
		      '#feb84d',
		      '#9b77f8',
		      '#fe95d3'
		    ],
		    hoverOffset: 4
		  }]
		};
        return new Chart('commissions',{
            type: 'doughnut',
            data: data,
            options: {
            	maintainAspectRatio: false,
        		tooltips: {
		            callbacks: {
		                label: function(tooltipItems, data) {
		                	return data.labels[tooltipItems.index] +': '+ currentCurrency + data.datasets[0].data[tooltipItems.index];
		                }
		           }
			   }
			}
        })
    }

</script>

@endsection
