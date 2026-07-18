@extends('layouts.app')

@section('content')

<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor restaurantTitle">{{trans('lang.admin_food_items')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.admin_food_items')}}</li>
            </ol>
        </div>
        <div>
        </div>
    </div>

    <div class="container-fluid">
        <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">
            {{trans('lang.processing')}}
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card pos-card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <div class="pos-search w-50 me-2">
                                <div class="form-group mb-0" id="search-box">
                                    <input type="text" name="search-product" id="search-product" class="form-control" placeholder="{{trans('lang.search_products')}}">
                                </div>
                            </div>
                            <div class="ml-2">
                                <a href="{!! route('items') !!}" class="btn btn-sm btn-primary">{{trans('lang.back_to_foods_menu')}}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="pos-contant-wrap">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="shop-card">
                                        <div class="row d-flex" id="product-list">
                                        </div>
                                        <div class="pagination-container mt-5">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="d-flex justify-content-center">
                                                        <nav aria-label="Page navigation example">
                                                            <ul class="pagination" id="pagination-numbers">

                                                            </ul>
                                                        </nav>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="section_id" value="">

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
                <span id="tax-error" class="text-danger">{{trans('lang.note_tax_selection_optional')}}</span>
                <div class="product-details mb-3">
                    <div class="box border p-3">
                        <div id="product_taxes"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button type="button" class="btn btn-primary" id="modal-add-taxes-btn">
                    {{ trans('lang.add_restaurant') }}
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')

<script>
    var database = firebase.firestore();
    var refUsers = database.collection('users');
    var pageSize = 12;
    var currentPage = 1;
    var totalPages = 1;
    var pageCursors = {};

    var activeVendorId = "{{ $vendorUserId }}";

    var searchText = "";
    var isCommissionEnabled;
    var commissionType;
    var commissionValue;
    let selectedProductId = null;
    let selectedProductTaxes = [];

    var placeholderImage = '';
    var placeholder = database.collection('settings').doc('placeHolderImage');
    var refCurrency = database.collection('currencies').where('isActive', '==', true);

    var subscriptionModel = false;
    var createdItem = 0;
    var itemLimit = '-1';
    var section_id = $("#section_id").val();
    var refProducts = null;
    
   database.collection("users").doc(activeVendorId).get().then(function(vendorSnap) {
        if (vendorSnap.exists) {
            var vendorData = vendorSnap.data();
            $("#section_id").val(vendorData.sectionId || "");
            if (vendorData.sectionId) {
                refProducts = database.collection('admin_products')
                    .where('publish', '==', true)
                    .where('sectionId', '==', vendorData.sectionId);
                
            } else {
                console.error("sectionId not found in vendor document");
            }
        } else {
            console.error("Vendor document not found");
        }
    });

    document.addEventListener('DOMContentLoaded', async function() {

        refUsers.doc(activeVendorId).get().then(async function(userSnap) {
            var userData = userSnap.data();
            activeVendorId = userData.vendorID;

            var subscriptionBusinessModel = database.collection('settings').doc("vendor");
            await subscriptionBusinessModel.get().then(async function(snapshots) {
                var subscriptionSetting = snapshots.data();
                if (subscriptionSetting.subscription_model == true) {
                    subscriptionModel = true;
                }
            });
            if (subscriptionModel) {
                if (userData.hasOwnProperty('subscription_plan') && userData.subscription_plan != null && userData.subscription_plan != '') {
                    itemLimit = userData.subscription_plan.itemLimit;
                }
                itemCountRef = await database.collection('vendor_products').where('vendorID', '==', activeVendorId).get();
                createdItem = itemCountRef.size;
            }
        })

        placeholder.get().then(async function(snapshotsimage) {
            var placeholderImageData = snapshotsimage.data();
            placeholderImage = placeholderImageData.image;
        });

        refCurrency.get().then(async function(snapshots) {
            var currencyData = snapshots.docs[0].data();
            currentCurrency = currencyData.symbol;
            currencyAtRight = currencyData.symbolAtRight;
            if (currencyData.decimal_degits) {
                decimal_degits = currencyData.decimal_degits;
            }
        });

        $('#data-table_processing').show();

        setTimeout(async () => {
            if (refProducts) {
                await loadProducts();
            } else {
                console.warn("refProducts not ready yet, retrying...");
                setTimeout(() => loadProducts(), 800); 
            }
        }, 600);

        function resetPagination() {
            currentPage = 1;
            pageCursors = {};
            lastVisible = null;
            isLastPage = false;
        }

        let debounceTimeout;

        $('#search-product').on('input', function() {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(async () => {
                searchText = $(this).val().trim().toLowerCase();
                currentPage = 1;
                pageCursors = {};
                await loadProducts(1);
            }, 300);
        });

    });

    async function loadProducts(page = 1) {

        $('#data-table_processing').show();

        if (!activeVendorId) {
            $('#product-list').html(`
                <div class="col-12 d-flex justify-content-center align-items-center" style="min-height:150px;">
                    <p class="text-center w-100 text-muted">
                        {{ trans('lang.select_restaurant_to_browse') }}
                    </p>
                </div>
            `);
            $('#pagination-numbers').html('');
            $('#data-table_processing').hide();
            return;
        }

        currentPage = page;

        try {
            let query = refProducts;

            let docs = [];

            if (searchText) {
                const snapshot = await query.get();
                const allDocs = snapshot.docs;

                const matched = allDocs.filter((doc) => {
                    const data = doc.data();
                    return data.name && data.name.toLowerCase().includes(searchText.toLowerCase());
                });

                totalPages = Math.ceil(matched.length / pageSize);
                docs = matched.slice((page - 1) * pageSize, page * pageSize);
            } else {
                query = query.orderBy('name').limit(pageSize + 1);

                if (page > 1 && pageCursors[page - 1]) {
                    query = query.startAfter(pageCursors[page - 1]);
                }

                const snapshot = await query.get();
                docs = snapshot.docs;

                if (docs.length > pageSize) {
                    pageCursors[page] = docs[pageSize - 1];
                    docs = docs.slice(0, pageSize);
                } else {
                    pageCursors[page] = null;
                }

                totalPages = pageCursors[page] ? currentPage + 1 : currentPage;
            }

            let html = '';
            if (docs.length === 0) {
                $('#product-list').html(`
                    <div class="col-12 d-flex justify-content-center align-items-center" style="min-height:150px;">
                        <p class="text-center w-100">{{ trans("lang.no_products_found") }}</p>
                    </div>
                `);
                $('#pagination-numbers').html('');
                $('#data-table_processing').hide();
                return;
            }

            // Helper to format price
            function formatPrice(amount) {
                const formatted = parseFloat(amount).toFixed(decimal_degits);
                return currencyAtRight ?
                    `${formatted}${currentCurrency}` :
                    `${currentCurrency}${formatted}`;
            }

            docs.forEach((doc) => {
                const data = doc.data();
                const id = doc.id;
                const name = data.name || 'Unnamed';
                const photo = data.photo || placeholderImage;

                let priceHtmlContent = '';
                let originalCartPrice = 0; // ← This is what goes to hidden input (original price)                

                // Case 1: Product with disPrice (no variants)
                if (data.disPrice && data.disPrice !== '0' && data.disPrice !== "" && !data.item_attribute) {
                    let originalPrice = parseFloat(data.price || 0);
                    let discountedPrice = parseFloat(data.disPrice);

                    originalCartPrice = discountedPrice; // ← Store original discounted price

                    let displayOriginal = originalPrice;
                    let displayDiscounted = discountedPrice;

                    priceHtmlContent = `${formatPrice(displayDiscounted)} <s>${formatPrice(displayOriginal)}</s>`;
                }
                // Case 2: Product with variants
                else if (data.item_attribute && data.item_attribute.variants && data.item_attribute.variants.length > 0) {
                    let originalVariantPrices = data.item_attribute.variants.map(v => parseFloat(v.variant_price || 0));
                    let displayVariantPrices = originalVariantPrices.map(price =>
                        // isCommissionEnabled ? applyCommission(price) : price
                        isCommissionEnabled ? price : price
                    );

                    const displayMin = Math.min(...displayVariantPrices);
                    const displayMax = Math.max(...displayVariantPrices);

                    if (displayMin === displayMax) {
                        priceHtmlContent = formatPrice(displayMax);
                    } else {
                        priceHtmlContent = `${formatPrice(displayMin)} - ${formatPrice(displayMax)}`;
                    }

                    // Store the ORIGINAL lowest price (without commission)
                    originalCartPrice = Math.min(...originalVariantPrices);
                }
                // Case 3: Regular product
                else {
                    let originalPrice = parseFloat(data.price || 0);
                    originalCartPrice = originalPrice;

                    let displayPrice = isCommissionEnabled ? originalPrice : originalPrice;
                    priceHtmlContent = formatPrice(displayPrice);
                }

                let route1 = '{{route("items.edit",":id")}}'.replace(':id', id);

                html += `
                    <div class="text-center col-md-3">
                        <div class="shop-item m-3">
                            <h3 class="shop-item-title mb-2 h6">
                                <a href="#" class="text text-dark">${name}</a>
                            </h3>
                            <div class="shop-item-image d-flex justify-content-center align-items-center mb-3">
                                <a href="#">
                                    <img class="item-image" src="${photo}" alt="${name}" 
                                        onerror="this.onerror=null;this.src='${placeholderImage}';">
                                </a>
                            </div>
                            <div class="shop-price mb-3">
                                <span class="form-control text-center text-dark">${priceHtmlContent}</span>
                            </div>
                            <div class="shop-item-btn justify-content-center">
                                <div class="form-check width-100">
                                    <button class="btn btn-sm btn-purchase btn-primary mb-2 add_to_store_btn" data-id="${id}">{{trans('lang.add_restaurant')}}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            $('#product-list').html(html);
            renderPagination();

        } catch (error) {
            console.error("Error loading products:", error);
        } finally {
            $('#data-table_processing').hide();
        }
        $('#data-table_processing').hide();
    }


    function renderPagination() {
        let paginationHtml = '';
        // Previous button
        paginationHtml += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadProducts(${currentPage - 1})">{{trans('lang.previous')}}</a>
        </li>`;
        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            paginationHtml += `<li class="page-item ${currentPage === i ? 'active' : ''}">
                <a class="page-link" href="#" onclick="loadProducts(${i})">${i}</a>
            </li>`;
        }
        // Next button
        paginationHtml += `<li class="page-item ${currentPage >= totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadProducts(${currentPage + 1})">{{trans('lang.next')}}</a>
        </li>`;
        $('#pagination-numbers').html(paginationHtml);
    }


    $(document).on('click', '.add_to_store_btn', async function() {

        $('#data-table_processing').show();

        if (!activeVendorId) {
            $('#data-table_processing').hide();
            Swal.fire({
                icon: 'error',
                text: '{{trans('lang.vendor_not_found ')}}'
            });
            return;
        }

        if (parseInt(itemLimit) != -1 && parseInt(createdItem) >= parseInt(itemLimit)) {
            $('#data-table_processing').hide();
            Swal.fire({
                icon: 'error',
                text: '{{ trans('lang.create_food_limit_exceed ') }}'
            });
            return;
        }

        selectedProductId = $(this).data('id');
        section_id = $("#section_id").val();

        let globalTaxSnapshot = await database.collection('settings').doc('globalSettings').get();
        let globalTax = globalTaxSnapshot.data();
        let countryName = getCookie('countryName') ?? '';       
        if (globalTax.taxScope == "product" && countryName) {

            let taxSnapshots = await database.collection('tax').where('enable', '==', true).where('scope', '==', 'product').where('country', '==', countryName).where('sectionId','==',section_id).get();
            let taxesHtml = '';
            if (taxSnapshots.docs.length > 0) {
                taxSnapshots.docs.forEach((doc) => {
                    let data = doc.data();
                    let taxText = data.title + ' (';
                    if (data.type === 'percentage') {
                        taxText += data.tax + '%';
                    } else {
                        let formattedAmount = formatAmount(data.tax);

                        if (currencyAtRight) {
                            taxText += formattedAmount + ' ' + currentCurrency;
                        } else {
                            taxText += currentCurrency + formattedAmount;
                        }
                    }
                    taxText += ')';
                    taxesHtml += `
                        <div class="form-check mb-2">
                            <input class="form-check-input product-tax" type="checkbox" data-tax="${encodeURIComponent(JSON.stringify(data))}" id="tax_${doc.id}">
                            <label class="form-check-label" for="tax_${doc.id}">${taxText}</label>
                        </div>
                    `;
                });
                $('#product_taxes').html(taxesHtml);
                $('#data-table_processing').hide();
                $('#taxModal').modal('show');
                return false;
            } else {
                addProductToVendor(selectedProductId,section_id, []);
                return false;
            }
        }

        addProductToVendor(selectedProductId, section_id, []);
    });

    $(document).on('click', '#modal-add-taxes-btn', function() {
        $('#data-table_processing').show();
        selectedProductTaxes = [];
        $('.product-tax:checked').each(function() {
            let taxObj = $(this).data('tax');
            taxObj = JSON.parse(decodeURIComponent(taxObj));
            selectedProductTaxes.push(taxObj);
        });
        $('#taxModal').modal('hide');
        addProductToVendor(selectedProductId, section_id, selectedProductTaxes);
    });

    async function addProductToVendor(productId, section_id, taxes) {

        try {

            let docSnap = await database.collection('admin_products').doc(productId).get();
            let productData = docSnap.data();

            let vendorProductRef = database.collection('vendor_products').doc();

            delete productData.sectionId;
            productData.id = vendorProductRef.id;
            productData.vendorID = activeVendorId;
            productData.adminProductId = productId;
            productData.createdAt = firebase.firestore.FieldValue.serverTimestamp();
            productData.taxSetting = taxes;
            productData.hasTax = taxes.length > 0;
            productData.section_id = section_id;

            await vendorProductRef.set(productData);

            $('#data-table_processing').hide();
            Swal.fire({
                icon: 'success',
                title: '{{ trans("lang.product_added_success") }}',
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                location.reload();
            });

        } catch (error) {
            console.error(error);
            Swal.fire({
                icon: 'error',
                text: '{{ trans("lang.something_went_wrong") }}'
            });
        }
    }

    function formatAmount(amount) {
        let num = parseFloat(amount) || 0;
        return num.toFixed(decimal_degits);
    }
</script>

@endsection