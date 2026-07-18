@extends('layouts.app')

@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ trans('lang.item_plural') }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('lang.item_plural') }}</li>
                </ol>
            </div>
            <div>
            </div>
        </div>
        <div class="row px-5 mb-2">
            <div class="col-12">
                <span class="font-weight-bold text-danger food-limit-note"></span>
            </div>
        </div>
        <div class="container-fluid  page-menu">
            <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">
                {{ trans('lang.processing') }}
            </div>
            <div class="admin-top-section">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex top-title-section pb-4 justify-content-between">
                            <div class="d-flex top-title-left align-self-center">
                                <span class="icon mr-3"><img src="{{ asset('images/item_image.png') }}"></span>
                                <h3 class="mb-0">{{ trans('lang.item_plural') }}</h3>
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
                                    <h3 class="text-dark-2 mb-2 h4">{{ trans('lang.item_plural') }}</h3>
                                    <p class="mb-0 text-dark-2">{{ trans('lang.item_table_text') }}</p>
                                </div>
                                <div class="card-header-right d-flex align-items-center">
                                    <div class="card-header-btn mr-3">

                                        <a class="btn-primary btn rounded-full" href="{!! route('items.create') !!}"><i class="mdi mdi-plus mr-2"></i>{{ trans('lang.item_create') }}</a>

                                    </div>
                                    <div class="card-header-btn mr-3">
                                        <a class="btn-secondary btn rounded-full" href="{!! route('items.global') !!}"><i class="mdi mdi-plus mr-2"></i>{{ trans('lang.global_item') }}</a>    
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-none" id="assign-tax-container" style="display: inline-block;margin: 12px;">
                                    <button type="button" class="btn btn-sm btn-warning" id="assign-taxes-btn">
                                        {{ trans('lang.assign_taxes') }}
                                    </button>
                                </div>
                                <div class="table-responsive m-t-10">
                                    <table id="itemTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th class="delete-all"><input type="checkbox" id="is_active"><label
                                                    class="col-3 control-label" for="is_active">
                                                    <a id="deleteAll" class="do_not_delete" href="javascript:void(0)"><i
                                                            class="fa fa-trash"></i> {{ trans('lang.all') }}</a></label>
                                                <th>{{ trans('lang.item_info') }}</th>
                                                <th>{{ trans('lang.item_price') }}</th>
                                                <th>{{ trans('lang.item_category_id') }}</th>
                                                <th>{{ trans('lang.item_publish') }}</th>
                                                <th>{{ trans('lang.date_created') }}</th>
                                                <th>{{ trans('lang.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="append_list1">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="taxModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ trans('lang.select_taxes') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="product-details mb-3">
                        <div class="box border p-3">
                            <div id="product_taxes"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button type="button" class="btn btn-primary" id="modal-add-taxes-btn">
                        {{ trans('lang.add_taxes') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        var database = firebase.firestore();
        var vendorUserId = "<?php echo $id; ?>";
        var vendorId;
        var ref;
        var append_list = '';
        var placeholderImage = '';
        var activeCurrencyref = database.collection('currencies').where('isActive', "==", true);
        var activeCurrency = '';
        var currencyAtRight = false;
        var decimal_degits = 0;
        var subscriptionModel = false;
        var commissionModel = false;
        var section_id = '';
        var subscriptionBusinessModel = database.collection('settings').doc("vendor");
        var date = '';
        var time = '';
        var authRole = "{{ $authRole }}";
        var empVendorId = "{{ $empVendorId }}";
        subscriptionBusinessModel.get().then(async function(snapshots) {
            var subscriptionSetting = snapshots.data();
            if (subscriptionSetting.subscription_model == true) {
                subscriptionModel = true;
            }
        });
        let globalTaxScope = null;
        let selectedProductIds = [];
        var vendorLatitude = '';
        var vendorLongitude = '';
        (async function() {
            let globalTaxSnapshot = await database.collection('settings').doc('globalSettings').get();
            let globalTax = globalTaxSnapshot.data();
            globalTaxScope = globalTax.taxScope;
        })();
        let countryName = getCookie('countryName') ?? '';        
        async function fetchSectionId() {

            const snapshots = await database.collection('users').where('id', '==', vendorUserId).get();

            if (snapshots.empty) {
                console.error('No user found');
                return;
            }

            var data = snapshots.docs[0].data();
            section_id = data.sectionId;

            await fetchSectionData();
        }

        async function fetchSectionData() {

            const section = database.collection('sections').where('id', '==', section_id);
            const sectionSnapshot = await section.get();

            if (sectionSnapshot.empty) {
                console.error('No section found');
                return;
            }

            var section_data = sectionSnapshot.docs[0].data();

            if (section_data.adminCommision != null && section_data.adminCommision != '') {
                if (section_data.adminCommision.enable) {
                    commissionModel = true;
                }
            }

        }        

        activeCurrencyref.get().then(async function(currencySnapshots) {
            currencySnapshotsdata = currencySnapshots.docs[0].data();
            activeCurrency = currencySnapshotsdata.symbol;
            currencyAtRight = currencySnapshotsdata.symbolAtRight;

            if (currencySnapshotsdata.decimal_degits) {
                decimal_degits = currencySnapshotsdata.decimal_degits;
            }
        })
        document.addEventListener("DOMContentLoaded", async function() {       
            if (authRole === 'employee') {               
                const perm = await getEmployeePermissionForTitle(vendorUserId, "Manage Products");

                currentPermissions = {
                    isActive: perm.isActive ?? false
                };           

                if (!currentPermissions.isActive) {
                    alert('{{ trans("lang.no_permission") }}');
                    $('#itemTable').hide();
                    $('.page-menu').html('<p class="text-center text-danger font-weight-bold">{{ trans("lang.no_permission") }}</p>');
                    return;
                }
            }
            await fetchSectionId();   
            getVendorId(vendorUserId).then(async data => {
                if (!countryName && (vendorLatitude && vendorLongitude)) {
                    countryName = await getCountryFromLatLng(vendorLatitude,vendorLongitude);                   
                    setCookie('countryName', countryName, 365);
                }  
            })
            if(globalTaxScope == "product" && countryName){
                const taxSnapshots = await database.collection('tax').where('country', '==', countryName).where('sectionId','==',section_id).where('enable', '==', true).where('scope', '==', 'product').get();
                if (taxSnapshots.docs.length > 0) {
                    window.taxSnapshots = taxSnapshots;
                    $('#assign-tax-container').removeClass('d-none');
                }
            }
        });
        getVendorId(vendorUserId).then(data => {
            vendorId = data;
            ref = database.collection('vendor_products').where('vendorID', "==", vendorId);
            $(document).ready(function() {                    
                    $(document.body).on('click', '.redirecttopage', function() {
                        var url = $(this).attr('data-url');
                        window.location.href = url;
                    });

                    jQuery("#data-table_processing").show();

                    var placeholder = database.collection('settings').doc('placeHolderImage');
                    placeholder.get().then(async function(snapshotsimage) {
                        var placeholderImageData = snapshotsimage.data();
                        placeholderImage = placeholderImageData.image;
                    })

                    var fieldConfig = {
                        columns: [{
                                key: 'name',
                                header: "{{ trans('lang.item_info') }}"
                            },
                            {
                                key: 'finalPrice',
                                header: "{{ trans('lang.item_price') }}"
                            },
                            {
                                key: 'category',
                                header: "{{ trans('lang.item_category_id') }}"
                            },
                            {
                                key: 'publish',
                                header: "{{ trans('lang.item_publish') }}"
                            },
                        ],

                        fileName: "{{ trans('lang.item_table') }}",
                    };

                    const table = $('#itemTable').DataTable({
                            pageLength: 10, // Number of rows per page
                            processing: false, // Show processing indicator
                            serverSide: true, // Enable server-side processing
                            responsive: true,
                            ajax: async function(data, callback, settings) {
                                const start = data.start;
                                const length = data.length;
                                const searchValue = data.search.value.toLowerCase();
                                const orderColumnIndex = data.order[0].column;
                                const orderDirection = data.order[0].dir;
                                const orderableColumns = ['', 'name', 'finalPrice', 'category', '', 'createdDate', '']; // Ensure this matches the actual column names

                                const orderByField = orderableColumns[
                                    orderColumnIndex]; // Adjust the index to match your table

                                if (searchValue.length >= 3 || searchValue.length === 0) {
                                    $('#data-table_processing').show();
                                }

                                await ref.get().then(async function(querySnapshot) {
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

                                        await Promise.all(querySnapshot.docs.map(async (
                                                doc) => {
                                                let childData = doc.data();
                                                childData.id = doc
                                                    .id; // Ensure the document ID is included in the data
                                                var finalPrice = 0;
                                                if (childData.hasOwnProperty(
                                                        'disPrice') && childData
                                                    .disPrice != '' && childData
                                                    .disPrice != '0') {
                                                    finalPrice = childData.disPrice;
                                                } else {
                                                    finalPrice = childData.price;
                                                }
                                                if (childData.hasOwnProperty("createdAt") && childData.createdAt != '' && childData.createdAt != null) {
                                                    try {
                                                        date = childData.createdAt.toDate().toDateString();
                                                        time = childData.createdAt.toDate()
                                                            .toLocaleTimeString('en-US');
                                                    } catch (err) {}
                                                }
                                                var createdAt = date + ' ' + time;
                                                childData.createdDate=createdAt;
                                                childData.foodName = childData.name;
                                                childData.finalPrice = parseInt(
                                                    finalPrice);
                                                var category =
                                                    await productCategory(childData
                                                        .categoryID);
                                                if (category == '') {
                                                    category =
                                                        '{{ trans('lang.unknown') }}';
                                                }
                                                childData.category = category;
                                                childData.publish = childData
                                                    .publish ? 'Yes' : 'No';
                                                if (searchValue) {
                                                    if (
                                                        (childData.name && childData
                                                            .name.toString()
                                                            .toLowerCase().includes(
                                                                searchValue)) ||
                                                        (childData.finalPrice &&
                                                            childData.finalPrice
                                                            .toString().includes(
                                                                searchValue)) ||
                                                        (childData.category &&
                                                            childData.category
                                                            .toString()
                                                            .toLowerCase().includes(
                                                                searchValue)) || (
                                                            childData.publish &&
                                                            childData.publish
                                                            .toString()
                                                            .toLowerCase().includes(
                                                                searchValue)) ||
                                                        (createdAt && createdAt.toString().toLowerCase()
                                                            .indexOf(searchValue) > -1)
                                                ) {
                                                    filteredRecords.push(
                                                        childData);
                                                }
                                            } else {
                                                filteredRecords.push(childData);
                                            }
                                        }));

                                    filteredRecords.sort((a, b) => {
                                        let aValue = a[orderByField];
                                        let bValue = b[orderByField];

                                        if (orderByField === 'finalPrice') {
                                            aValue = a[orderByField] ? parseFloat(a[
                                                orderByField]) : 0.0;
                                            bValue = b[orderByField] ? parseFloat(b[
                                                orderByField]) : 0.0;
                                        }else if (orderByField === 'createdDate' && a[orderByField] != '' && b[orderByField] != '' && a[orderByField] != null && b[orderByField] != null) {
                                            aValue = a[orderByField] ? new Date(a[orderByField]).getTime() : 0;
                                            bValue = b[orderByField] ? new Date(b[orderByField]).getTime() : 0;
                                        }  else {
                                            aValue = a[orderByField] ? a[
                                                    orderByField].toString()
                                                .toLowerCase() : '';
                                            bValue = b[orderByField] ? b[
                                                    orderByField].toString()
                                                .toLowerCase() : ''
                                        }

                                        if (orderDirection === 'asc') {
                                            return (aValue > bValue) ? 1 : -1;
                                        } else {
                                            return (aValue < bValue) ? 1 : -1;
                                        }

                                    });

                                    const totalRecords = filteredRecords.length; $('.total_count').text(totalRecords);
                                    const paginatedRecords = filteredRecords.slice(start,
                                        start + length);

                                    await Promise.all(paginatedRecords.map(async (
                                        childData) => {
                                        var getData = await buildHTML(
                                            childData);

                                        records.push(getData);
                                    }));

                                    $('#data-table_processing').hide(); // Hide loader
                                    callback({
                                        draw: data.draw,
                                        recordsTotal: totalRecords, // Total number of records in Firestore
                                        recordsFiltered: totalRecords, // Number of records after filtering (if any)
                                        filteredData: filteredRecords,
                                        data: records // The actual data to display in the table
                                    });
                                }).catch(function(error) {
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
                        columnDefs: [{
                            orderable: false,
                            targets: [0, 4, 6]
                        }, ],
                        order: [5, 'asc'],
                        "language": datatableLang,
                        dom: 'lfrtipB',
                        buttons: [{
                            extend: 'collection',
                            text: '<i class="mdi mdi-cloud-download"></i> {{trans("lang.export_as")}}',
                            className: 'btn btn-info',
                            buttons: [{
                                    extend: 'excelHtml5',
                                    text: '{{trans("lang.export_excel")}}',
                                    action: function(e, dt, button, config) {
                                        exportData(dt, 'excel', fieldConfig);
                                    }
                                },
                                {
                                    extend: 'pdfHtml5',
                                    text: '{{trans("lang.export_pdf")}}',
                                    action: function(e, dt, button, config) {
                                        exportData(dt, 'pdf', fieldConfig);
                                    }
                                },
                                {
                                    extend: 'csvHtml5',
                                    text: '{{trans("lang.export_csv")}}',
                                    action: function(e, dt, button, config) {
                                        exportData(dt, 'csv', fieldConfig);
                                    }
                                }
                            ]
                        }],
                        initComplete: function() {
                            $(".dataTables_filter").append($(".dt-buttons").detach());
                            $('.dataTables_filter input').attr('placeholder', '{{ trans('lang.search_here') }}')
                                .attr('autocomplete', 'new-password').val('');
                            $('.dataTables_filter label').contents().filter(function() {
                                return this.nodeType === 3;
                            }).remove();
                        }
                    });
            });
            $('#assign-tax-container').insertAfter($('#itemTable_length'));
        })

        async function buildHTML(val) {
            var html = [];

            var id = val.id;
            var route1 = '{{ route('items.edit', ':id') }}';
            route1 = route1.replace(':id', id);
            var price_val = 0;
            var price_s = '';
            const tax = val.taxSetting || [];

            let tax_titles = '';            
            if (globalTaxScope === "product" && tax.length > 0) {
                const taxDisplay = tax
                    .map(t => {
                        const value = t.type === 'percentage'
                            ? `${t.tax}%`
                            : currencyAtRight
                                ? `${t.tax} ${activeCurrency}`
                                : `${activeCurrency} ${t.tax}`;

                        return `${t.title}(${value})`;
                    })
                    .join(', ');

                tax_titles = `<p class="d-block text-muted">Taxes: ${taxDisplay}</p>`;
            }
            html.push('<td class="delete-all"><input type="checkbox" id="is_open_' + id + '" class="is_open" dataId="' +
                id + '"><label class="col-3 control-label"\n' +
                'for="is_open_' + id + '" ></label></td>');

            if (val.photo == '') {
                html.push('<img class="rounded" style="width:50px" src="' + placeholderImage +
                    '" alt="image" ><a data-url="' + route1 + '" href="' + route1 +
                    '" class="left_space redirecttopage">' + val.name + tax_titles + '</a>');
            } else {
                html.push('<img class="rounded" style="width:50px" src="' + val.photo +
                    '" alt="image" onerror="this.onerror=null;this.src=\'' + placeholderImage +
                    '\'"><a data-url="' + route1 + '" href="' + route1 + '" class="left_space redirecttopage">' +
                    val.name + tax_titles + '</a>');
            }

            if (val.item_attribute && val.item_attribute.variants && val.item_attribute.variants.length > 0) {

                let originalVariantPrices = val.item_attribute.variants
                    .map(v => parseFloat(v.variant_price))
                    .filter(price => !isNaN(price) && price > 0);

                if (originalVariantPrices.length > 0) {

                    const displayMin = Math.min(...originalVariantPrices);
                    const displayMax = Math.max(...originalVariantPrices);

                    let minPriceFormatted = '';
                    let maxPriceFormatted = '';

                    if (currencyAtRight) {
                        minPriceFormatted = parseFloat(displayMin).toFixed(decimal_degits) + '' + activeCurrency;
                        maxPriceFormatted = parseFloat(displayMax).toFixed(decimal_degits) + '' + activeCurrency;
                    } else {
                        minPriceFormatted = activeCurrency + '' + parseFloat(displayMin).toFixed(decimal_degits);
                        maxPriceFormatted = activeCurrency + '' + parseFloat(displayMax).toFixed(decimal_degits);
                    }

                    if (displayMin === displayMax) {
                        html.push(minPriceFormatted);
                    } else {
                        html.push(minPriceFormatted + ' - ' + maxPriceFormatted);
                    }
                }
            }        
            else if (val.hasOwnProperty('disPrice') && val.disPrice != '' && val.disPrice != '0') {
                if (currencyAtRight) {
                    price_val = parseFloat(val.disPrice).toFixed(decimal_degits) + '' + activeCurrency;
                    price_s = parseFloat(val.price).toFixed(decimal_degits) + '' + activeCurrency;

                } else {
                    price_val = activeCurrency + '' + parseFloat(val.disPrice).toFixed(decimal_degits);
                    price_s = activeCurrency + '' + parseFloat(val.price).toFixed(decimal_degits);
                }
                html.push(price_val + " " + '<s>' + price_s + '</s>');
            } else {
                if (currencyAtRight) {
                    price_val = parseFloat(val.price).toFixed(decimal_degits) + '' + activeCurrency;
                } else {
                    price_val = activeCurrency + '' + parseFloat(val.price).toFixed(decimal_degits);
                }
                html.push(price_val);
            }
            html.push('<span class="category_' + val.categoryID + '">' + val.category + '</span>');
            
            if (val.publish == "Yes") {
                html.push('<span class="badge badge-success">Yes</span>');
            } else {
                html.push('<span class="badge badge-danger">No</span>');
            }
            html.push(val.createdDate);
            html.push('<span class="action-btn"><a href="' + route1 +
                '"><i class="mdi mdi-lead-pencil"></i></a><a id="' + val.id +
                '" class="do_not_delete" name="item-delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a></span>'
            );
            return html;
        }

        async function productCategory(category) {
            var productCategory = '';
            await database.collection('vendor_categories').where("id", "==", category).get().then(async function(
                snapshotss) {

                if (snapshotss.docs[0]) {
                    var category_data = snapshotss.docs[0].data();
                    productCategory = category_data.title;
                }
            });
            return productCategory;
        }

        $(document).on("click", "a[name='item-delete']", async function(e) {
            const id = this.id;
            await deleteDocumentWithImage('vendor_products', id, 'photo', 'photos');
            window.location = "{{ !url()->current() }}";
        });

        async function getVendorId(vendorUser) {
            var vendorId = '';
            var ref;
            let vendorSnapshots;
            if (authRole === 'vendor') {
                vendorSnapshots = await database.collection('vendors').where('author', '==', vendorUser).get();
            } else {
                vendorSnapshots = await database.collection('vendors').where('id', '==', empVendorId).get();
            }
            if (vendorSnapshots.empty) {
                console.error('Vendor not found');
                return '';
            }
           
                var vendorData = vendorSnapshots.docs[0].data();
                vendorId = vendorData.id;
                vendorLatitude = vendorData.latitude;
                vendorLongitude = vendorData.longitude;
                if (subscriptionModel || commissionModel) {
                    if (vendorData.hasOwnProperty('subscription_plan') && vendorData.subscription_plan != null && vendorData.subscription_plan != '') {
                        itemLimit = vendorData.subscription_plan.itemLimit;
                        if (itemLimit != '-1') {
                            $('.food-limit-note').html(
                                '{{ trans('lang.note') }} : {{ trans('lang.your_item_limit_is') }} ' +
                                itemLimit + ' {{ trans('lang.so_only_first') }} ' + itemLimit +
                                ' {{ trans('lang.items_will_visible_to_customer') }}')
                        }
                    }
                }
           

            return vendorId;
        }
        $("#deleteAll").click(function() {
            if ($('#itemTable .is_open:checked').length) {
                if (confirm('{{trans("lang.are_you_sure_want_to_delete_selected_data")}}')) {
                    jQuery("#data-table_processing").show();
                    $('#itemTable .is_open:checked').each(async function() {
                        var dataId = $(this).attr('dataId');
                        await deleteDocumentWithImage('vendor_products', dataId, 'photo', 'photos');
                        window.location.reload();
                    });
                }
            } else {
                alert('{{trans("lang.please_select_any_one_record")}}');
            }
        });
        $('#assign-taxes-btn').on('click', async function() {

            $('#data-table_processing').show();

            $("#itemTable .is_open:checked").each(function () {
                selectedProductIds.push($(this).attr('dataId'));
            });

            if (!selectedProductIds.length) {
                Swal.fire({ icon: 'warning', text: "{{trans('lang.no_products_selected')}}" });
                $('#data-table_processing').hide();
                return;
            }

            let taxesHtml = '';
            window.taxSnapshots.docs.forEach((doc) => {
                let data = doc.data();
                let taxText = data.title + ' (';
                if (data.type === 'percentage') {
                    taxText += data.tax + '%';
                } else {
                    if (currencyAtRight) {
                        taxText += parseFloat(data.tax).toFixed(decimal_degits) + ' ' + activeCurrency;
                    } else {
                        taxText += activeCurrency + parseFloat(data.tax).toFixed(decimal_degits);
                    }
                }
                taxText += ')';
                let taxData = encodeURIComponent(JSON.stringify(data));
                taxesHtml += `
                    <div class="form-check mb-2">
                        <input class="form-check-input product-tax"
                            type="checkbox"
                            data-tax='${taxData}'
                            id="tax_${doc.id}">
                        <label class="form-check-label" for="tax_${doc.id}">${taxText}</label>
                    </div>
                `;
            });

            $('#product_taxes').html(taxesHtml);
            $('.product-tax').prop('checked', false);
            $('#taxModal').modal('show');
            $('#data-table_processing').hide();
        });

        $('#modal-add-taxes-btn').on('click', async function() {
            $('#taxModal').modal('hide');
            let selectedTaxes = [];
            $('.product-tax:checked').each(function () {
                let taxObj = $(this).data('tax');
                taxObj = JSON.parse(decodeURIComponent(taxObj));
                selectedTaxes.push(taxObj);
            });
            Swal.fire({
                title: '{{trans("lang.assigning_taxes")}}',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            try {
                for (let productId of selectedProductIds) {
                    const productRef = database.collection('vendor_products').doc(productId);
                    await productRef.update({ taxSetting: selectedTaxes });
                }
                Swal.fire({
                    icon: 'success',
                    title: "{{trans('lang.taxes_assigned_successfully')}}",
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    location.reload();
                });
            } catch (error) {
                console.error(error);
                Swal.fire({ icon: 'error', text: "{{trans('lang.error_assigning_taxes')}}" });
            }
        });
        $("#is_active").click(function () {
            $("#itemTable .is_open").prop('checked', $(this).prop('checked'));

        });
    </script>
@endsection
