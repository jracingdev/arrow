@extends('layouts.app')

@section('content')

<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.order_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.order_plural')}}</li>
            </ol>
        </div>
        <div>
        </div>
    </div>
    <div class="container-fluid page-menu">
        <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">
        {{trans('lang.processing')}}
        </div>
       <div class="admin-top-section"> 
        <div class="row">
            <div class="col-12">
                <div class="d-flex top-title-section pb-4 justify-content-between">
                    <div class="d-flex top-title-left align-self-center">
                        <span class="icon mr-3"><img src="{{ asset('images/order.png') }}"></span>
                        <h3 class="mb-0">{{trans('lang.order_plural')}}</h3>
                        <span class="counter ml-3 total_count"></span>
                    </div>  
                  
                </div>
            </div>
        </div> 
    
       </div>
       <div class="table-list">
       <div class="row">
           <div class="col-12">
               <div class="card border">
                 <div class="card-header d-flex justify-content-between align-items-center border-0">
                   <div class="card-header-title">
                    <h3 class="text-dark-2 mb-2 h4">{{trans('lang.order_plural')}}</h3>
                    <p class="mb-0 text-dark-2">{{trans('lang.order_table_text')}}</p>
                   </div>              
                 </div>
                 <div class="card-body">
                         <div class="table-responsive m-t-10">
                            <table id="orderTable"
                                   class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                   cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>{{trans('lang.order_id')}}</th>
                                    <th>{{trans('lang.order_user_id')}}</th>
                                    <th>{{trans('lang.order_order_status_id')}}</th>
                                    <th>{{trans('lang.amount')}}</th>
                                    <th>{{trans('lang.order_type')}}</th>
                                    <th>{{trans('lang.date')}}</th>
                                    <th>{{trans('lang.actions')}}</th>
                                </tr>
                                </thead>                         
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>


@endsection


@section('scripts')
<script type="text/javascript">

    var database = firebase.firestore();    
    var user_id = "<?php echo $id; ?>";
    var user_number = [];
   
    var ref;
    var currentCurrency = '';
    var currencyAtRight = false;
    var decimal_degits = 0;
    var authRole = "{{ $authRole }}";
    var empVendorId = "{{ $empVendorId }}";
    let currentPermissions = {
        isActive: true   
    };
    var refCurrency = database.collection('currencies').where('isActive', '==', true);
    refCurrency.get().then(async function (snapshots) {
        var currencyData = snapshots.docs[0].data();
        currentCurrency = currencyData.symbol;
        currencyAtRight = currencyData.symbolAtRight;

        if (currencyData.decimal_degits) {
            decimal_degits = currencyData.decimal_degits;
        }
    });

    document.addEventListener("DOMContentLoaded", async function() {
        if (authRole === 'employee') {               
            const perm = await getEmployeePermissionForTitle(user_id, "Manage Order");
            currentPermissions = {
                isActive: perm.isActive ?? false
            };  

            if (!currentPermissions.isActive) {
                alert('{{ trans("lang.no_permission") }}');
                $('#orderTable').hide();
                $('.page-menu').html('<p class="text-center text-danger font-weight-bold">{{ trans("lang.no_permission") }}</p>');                   
                return;
            }
            const vendorSnap = await database
                .collection('vendors')
                .where('id', '==', empVendorId)
                .limit(1)
                .get();

            if (vendorSnap.empty) {
                console.error('Vendor not found for employee');
                return;
            }

            const authorId = vendorSnap.docs[0].data().author;
            ref = database
                .collection('vendor_orders')
                .where('isPosOrder','==',false)
                .where('vendor.author','==',authorId)
                .orderBy('createdAt','desc');
        }else{
            ref = database
            .collection('vendor_orders')
            .where('isPosOrder','==',false)
            .where('vendor.author','==',user_id)
            .orderBy('createdAt','desc');
        }
        $(document.body).on('click', '.redirecttopage', function () {
            var url = $(this).attr('data-url');
            window.location.href = url;
        });


        jQuery("#data-table_processing").show();
       
        var fieldConfig = {
                columns: [
                    { key: 'id', header: "{{trans('lang.order_id')}}" },
                    { key: 'clientName', header: "{{trans('lang.order_user_id')}}" },                            
                    { key: 'status', header: "{{trans('lang.order_order_status_id')}}" }, 
                    { key: 'amount', header: "{{trans('lang.amount')}}" },
                    { key: 'orderType', header: "{{trans('lang.order_type')}}" },
                    { key: 'createdAt', header: "{{trans('lang.date')}}" },
                    
                ],
                
                fileName: "{{trans('lang.order_list')}}",
            };
       
        const table = $('#orderTable').DataTable({
            pageLength: 10, // Number of rows per page
            processing: false, // Show processing indicator
            serverSide: true, // Enable server-side processing
            responsive: true,
            ajax: async function (data, callback, settings) {
                const start = data.start;
                const length = data.length;
                const searchValue = data.search.value.toLowerCase();
                const orderColumnIndex = data.order[0].column;
                const orderDirection = data.order[0].dir;                    
                const orderableColumns = ['id', 'clientName', 'status', 'amount', 'orderType', 'createdAt','']; // Ensure this matches the actual column names
                
                const orderByField = orderableColumns[orderColumnIndex]; // Adjust the index to match your table

                if (searchValue.length >= 3 || searchValue.length === 0) {
                    $('#data-table_processing').show();
                }

                await ref.get().then(async function (querySnapshot) {
                    if (querySnapshot.empty) {
                        $('.total_count').text(0);
                        $('#data-table_processing').hide(); // Hide loader
                        callback({
                            draw: data.draw,
                            recordsTotal: 0,
                            recordsFiltered: 0,
                            data: [] // No data
                        });
                        return;
                    }

                    let records = [];
                    let filteredRecords = [];

                    await Promise.all(querySnapshot.docs.map(async (doc) => {
                        let childData = doc.data();
                        childData.id = doc.id; // Ensure the document ID is included in the data
                        if(childData.hasOwnProperty('author') && childData.author != ''){
                            childData.clientName = childData.author.firstName + ' ' + childData.author.lastName;                            
                        }else{
                            childData.clientName = '{{trans("lang.unknown")}}';
                        }
                        var price =  await buildHTMLProductstotal(childData);
                        childData.amount = price;
                        
                        if (childData.hasOwnProperty('takeAway') && childData.takeAway) {
                            childData.orderType = '{{trans("lang.order_takeaway")}}';
                        } else {
                            childData.orderType = '{{trans("lang.order_delivery")}}';
                        }  
                        
                        if (searchValue) {
                            var date = '';
                            var time = '';
                            if (childData.hasOwnProperty("createdAt")) {
                                try {
                                    date = ArrowDateTime.formatDate(childData.createdAt.toDate());
                                    time = ArrowDateTime.formatTime(childData.createdAt.toDate());
                                } catch (err) {
                                }                                
                            }
                            var createdAt = date + ' ' + time;
                            if (
                                (childData.clientName && childData.clientName.toString().toLowerCase().includes(searchValue)) ||
                                (childData.amount && childData.amount.toString().includes(searchValue))
                                || (childData.orderType && childData.orderType.toString().toLowerCase().includes(searchValue)) || (childData.amount && childData.amount.toString().toLowerCase().includes(searchValue)) || (childData.id && childData.id.toString().toLowerCase().includes(searchValue)) || (createdAt && createdAt.toString().toLowerCase().includes(searchValue)) || (childData.status && childData.status.toString().toLowerCase().includes(searchValue))
                            ) {
                                filteredRecords.push(childData);
                            }
                        } else {
                            filteredRecords.push(childData);
                        }
                    }));
                    
                    filteredRecords.sort((a, b) => {
                        let aValue = a[orderByField] ;
                        let bValue = b[orderByField] ;                       
                        if (orderByField === 'amount') {  
                            aValue = a[orderByField] ? parseFloat(String(a[orderByField]).replace(/[^0-9.]/g, '')) || 0 : 0;
                            bValue = b[orderByField] ? parseFloat(String(b[orderByField]).replace(/[^0-9.]/g, '')) || 0 : 0;
                        }else{
                            aValue = a[orderByField] ? a[orderByField].toString().toLowerCase() : '';
                            bValue = b[orderByField] ? b[orderByField].toString().toLowerCase() : ''
                        }
                    
                        if (orderDirection === 'asc') {
                            return (aValue > bValue) ? 1 : -1;
                        } else {
                            return (aValue < bValue) ? 1 : -1;
                        }
                    
                    });
                    
                    const totalRecords = filteredRecords.length;
                    $('.total_count').text(totalRecords);
                    const paginatedRecords = filteredRecords.slice(start, start + length);
                
                    await Promise.all(paginatedRecords.map(async (childData) => {
                        var getData = await buildHTML(childData);
                        
                        records.push(getData);
                    }));

                    $('#data-table_processing').hide(); // Hide loader
                    callback({
                        draw: data.draw,
                        recordsTotal: totalRecords, // Total number of records in Firestore
                        recordsFiltered: totalRecords, // Number of records after filtering (if any)
                        recordsFiltered: totalRecords, 
                        filteredData: filteredRecords,
                        data: records // The actual data to display in the table
                    });
                }).catch(function (error) {
                    console.error("Error fetching data from Firestore:", error);
                    $('#data-table_processing').hide(); // Hide loader
                    callback({
                        draw: data.draw,
                        recordsTotal: 0,
                        recordsFiltered: 0,
                        data: [] // No data due to error
                    });
                });
            },
            columnDefs: [
                {orderable: false, targets: [6]},
            ],
            order: [5, 'desc'],
           "language": datatableLang,
            dom: 'lfrtipB',
                buttons: [
                    {
                        extend: 'collection',
                        text: '<i class="mdi mdi-cloud-download"></i> {{trans("lang.export_as")}}',
                        className: 'btn btn-info',
                        buttons: [
                            {
                                extend: 'excelHtml5',
                                text: '{{trans("lang.export_excel")}}',
                                action: function (e, dt, button, config) {
                                    exportData(dt, 'excel',fieldConfig);
                                }
                            },
                            {
                                extend: 'pdfHtml5',
                                text: '{{trans("lang.export_pdf")}}',
                                action: function (e, dt, button, config) {
                                    exportData(dt, 'pdf',fieldConfig);
                                }
                            },   
                            {
                                extend: 'csvHtml5',
                                text: '{{trans("lang.export_csv")}}',
                                action: function (e, dt, button, config) {
                                    exportData(dt, 'csv',fieldConfig);
                                }
                            }
                        ]
                    }
                ],
                initComplete: function() {
                    $(".dataTables_filter").append($(".dt-buttons").detach());
                    $('.dataTables_filter input').attr('placeholder', '{{trans("lang.search_here")}}').attr('autocomplete','new-password').val('');
                    $('.dataTables_filter label').contents().filter(function() {
                        return this.nodeType === 3; 
                    }).remove();
                }
        });

    });

    async function buildHTML(val) {
        html=[];
        var id = val.id;
        var route1 = '{{route("orders.edit",":id")}}';
        route1 = route1.replace(':id', id);

        var printRoute = '{{route("vendors.orderprint",":id")}}';
        printRoute = printRoute.replace(':id', id);


        html.push('<a href="'+route1+'">' + val.id + '</a>');
        html.push(val.clientName);

        if (val.status == 'Order Placed') {
            html.push('<span class="badge badge-warning ">' + val.status + '</span>');

        } else if (val.status == 'Order Accepted') {
            html.push('<span class="badge badge-info ">' + val.status + '</span>');

        } else if (val.status == 'Order Rejected') {
            html.push('<span class="badge badge-danger ">' + val.status + '</span>');

        } else if (val.status == 'Driver Pending') {
            html.push('<span class="badge badge-secondary ">' + val.status + '</span>');

        } else if (val.status == 'Driver Rejected') {
            html.push('<span class="badge badge-danger ">' + val.status + '</span>');

        } else if (val.status == 'Order Shipped') {
            html.push('<span class="badge badge-primary ">' + val.status + '</span>');

        } else if (val.status == 'In Transit') {
            html.push('<span class="badge badge-info ">' + val.status + '</span>');

        } else if (val.status == 'Order Completed') {
            html.push('<span class="badge badge-success ">' + val.status + '</span>');

        }else if(val.status == 'Order Cancelled') {
            html.push('<span class="badge badge-dark ">' + val.status + '</span>');

        }else{
            html.push('');
        }

        html.push(val.amount);
        html.push(val.orderType);

        var date = '';
        var time = '';
        if (val.hasOwnProperty("createdAt")) {
            try {
                date = ArrowDateTime.formatDate(val.createdAt.toDate());
                time = ArrowDateTime.formatTime(val.createdAt.toDate());
            } catch (err) {

            }
            html.push('<span class="dt-time">' + date + ' ' + time + '</span>');
        } else {
            html.push('');
        }

        html.push('<span class="action-btn"><a href="' + printRoute + '"><i class="mdi mdi-printer" style="font-size:20px;"></i></a><a href="' + route1 + '"><i class="mdi mdi-lead-pencil"></i></a><a id="' + val.id + '" class="do_not_delete" name="order-delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a></span>');
        return html;
    }

    $(document).on("click", "a[name='order-delete']", function (e) {
        var id = this.id;
        database.collection('vendor_orders').doc(id).delete().then(function (result) {
            window.location.href = '{{ url()->current() }}';
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

</script>


@endsection
