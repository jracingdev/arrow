@extends('layouts.app')

@section('content')

<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor restaurantTitle">{{trans('lang.point_of_sale')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.point_of_sale')}}</li>
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
                    <div class="pos-search d-flex align-items-center gap-10">
                        <label class="mb-0">{{trans('lang.all_products')}}</label>
                        <div class="form-group mb-0 custom-dropdown" id="product_categories">
                            <select class="form-control search_category" id="category_dropdown" name="category_dropdown" tabindex="-1" aria-hidden="true" data-select2-id="product_categories">
                                <option value="" data-select2-id="23">{{trans('lang.select_categories')}}</option>                                                            
                            </select>
                        </div>
                        <div class="form-group mb-0" id="search-box">
                            <input type="text" name="search-product" id="search-product" class="form-control" placeholder="{{trans('lang.search_products')}}">
                        </div>
                        
                        <input type="button" class="btn btn-xs btn-secondary ml-2" id="clear_filter" value="Clear">
                    </div>
                </div>
                <div class="card-body">

                    <div class="pos-contant-wrap">
                        <div class="row">
                            <div class="col-md-7">
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


                            <div class="col-md-5">
                                <div class="pos-right border-left pl-4">                                  
                                    <div class="mt-2 d-flex justify-content-between align-items-center">
                                        <p class="text text-dark mb-0 font-weight-bold">
                                            {{trans('lang.select_user')}}
                                        </p>
                                       
                                    </div>

                                    <div class="mt-2 d-flex">
                                        <select class="select_user form-control" id="user_dropdown"  name="user_dropdown" data-select2-id="select_user_id" tabindex="-1" aria-hidden="true">
                                            <option value="">{{trans('lang.search_for_user')}}</option>
                                        </select>
                                        <input type="button" class="btn btn-xs btn-secondary ml-2" id="clear_user_search" value="Clear">
                                    </div>     
                                    <!-- User Details Block -->
                                    <div id="user_detail_block" class="mt-3 p-3 border rounded d-none bg-light">
                                        <div class="mb-1">
                                            <strong>{{trans('lang.name')}} :</strong>
                                            <span id="user_detail_name">-</span>
                                        </div>

                                        <div class="mb-1">
                                            <strong>{{trans('lang.email')}} :</strong>
                                            <span id="user_detail_email">-</span>
                                        </div>

                                        <div class="mb-1">
                                            <strong>{{trans('lang.phone')}} :</strong>
                                            <span id="user_detail_phone">-</span>
                                        </div>

                                        <div>
                                            <strong>{{trans('lang.wallet_amount')}} :</strong>
                                            <span class="text-primary" id="user_detail_wallet">$0.00</span>
                                        </div>
                                    </div>
                               
                                    <div class="shop-cart-table">
                                        <h3 class="mb-2 text-dark font-weight-semibold">{{trans('lang.cart')}}</h3>
                                        <div class="cart-table border-bottom pb-2 mb-2">
                                            <table cellpadding="0" cellspacing="0" class="table table-striped table-valign-middle" id="cartTable">
                                                <thead>
                                                    <tr>
                                                        <th>{{trans('lang.item_plural')}}</th>
                                                        <th>{{trans('lang.item_price')}}</th>
                                                        <th>{{trans('lang.qty')}}</th>
                                                        <th>{{trans('lang.edit')}}</th>
                                                    </tr>
                                                </thead>
                                                
                                                <tbody id="cart_list"> 
                                                    @if(empty(session('cart')))
                                                    <tr>
                                                        <td colspan="4" class="text-center">{{trans('lang.cart_is_empty')}}</td>
                                                    </tr>
                                                    @endif
                                                    @include('pos.cart_item')
                                                </tbody>

                                                <tfoot id="cart_total">
                                                    @if(!empty(session('cart')))
                                                        @include('pos.cart_total')
                                                    @endif
                                                </tfoot>
                                            
                                            </table>
                                        </div>

                                        <div class="shop-payment-method" style="display: none">
                                            <h3 class="mb-2 text-dark font-weight-semibold pb-2 border-bottom">{{trans('lang.payment_methods')}}</h3>
                                            <div class="payment-method-list">
                                                <div class="form-radio width-100 pb-2 border-bottom mb-2">
                                                    <input class="raio-input" name="paymentmethod" id="cash" type="radio" value="cod" checked>
                                                    <label class="control-label" for="cash">{{trans('lang.cash')}}</label>
                                                </div> 
                                                <div class="form-radio width-100 pb-2 border-bottom mb-2">
                                                    <input class="raio-input" name="paymentmethod" id="card" type="radio" value="Card">
                                                    <label class="control-label" for="card">{{trans('lang.card')}}</label>
                                                </div>                                              
                                            </div>                                            
                                        </div>

                                        <div class="text-center mt-4 shop-payment-method" style="display: none">
                                           <button class="btn btn-sm btn-clear_cart btn-danger mb-2 mx-3" id="clear_cart_btn">{{trans('lang.clear_cart')}}</button>
                                           <button class="btn btn-sm btn-purchase btn-primary mb-2" id="place_order_btn">{{trans('lang.place_order')}}</button>
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

<div class="modal fade" id="addToCartModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered location_modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('lang.add_to_cart') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="product-details mb-3">
                    <div class="d-flex align-items-center d-flex align-items-center box border">
                       <div class="mr-3 model-cart-img"> 
                        <img id="modal-product-image" src="" style="width:60px;height:60px;" />
                       </div>
                        <div class="model-cart-detail">
                            <h5 id="modal-product-name" class="mb-1 text-dark"></h5>
                            <p id="modal-product-price" class="mb-0 text-dark"></p>
                        </div>
                      
                    </div>
                </div>
                <div class="product-details mb-3">
                    <div class="align-items-center">                       
                        <div class="model-cart-detlist">
                            <div id="modal-product-quantity" class="pb-2 mb-2 d-flex align-items-center justify-content-between border-bottom"><strong class="text-dark">{{trans('lang.quantity')}} : </strong><span class="modal-product-quantity-count"></span></div>                                                    
                                                     
                           
                            <div class="product-attr-detail">
                                <div id="variation_info_temp">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <strong class="text-dark">{{ trans('lang.price') }}:</strong><span id="variant_price"></span>
                                        
                                    </div>
                                    <span id="variant_qty" class="text-muted"></span>
                                </div>
                                <div class="attribute_price_div d-none">
                                    <span class="price">
                                        <div class="variation_info" id="modal-variation-info">
                                            <span id="modal-variant-price"></span>
                                            <span id="modal-variant-qty"></span>
                                        </div>
                                    </span>
                                    <input type="hidden" id="selected_variant_id" name="selected_variant_id">
                                    <input type="hidden" id="selected_variant_price" name="selected_variant_price">
                                </div>
                            </div>

                           
                        </div>
                    </div>
                </div>
                <div id="modal-attributes-container" class="mb-3 row"></div>
                <div id="modal-addons-container" class="mb-3"></div>

                <div class="modal-footer d-flex justify-content-center">
                    <button type="button" class="btn btn-primary" id="modal-add-cart-btn">{{ trans('lang.add_to_cart') }}</a>
                    </button>                    
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('scripts')

<script>
    var database = firebase.firestore();
    var refProducts = database.collection('vendor_products');
    var refCategories = database.collection('vendor_categories');
    var refRestaurant = database.collection('vendors');
    var refUsers = database.collection('users');    
    var pageSize = 12;   
    var currentPage = 1;
    var totalPages = 1;
    var pageCursors = {}; 
    var selectedCategoryId = null;
    var selectedRestaurantId = "{{Auth::user()->getvendorId()}}";
    var searchText = "";
    let confirmedRestaurantId = null;
    let isInternalChange = false;
    let isConfirmDialogOpen = false;    
    var isCommissionEnabled;
    var commissionType;
    var commissionValue;
    var deliveryChargemain = [];
    var vendorUserId = "<?php echo $id; ?>";

    var placeholderImage = '';
    var placeholder = database.collection('settings').doc('placeHolderImage');
   
    var DeliveryCharge = database.collection('settings').doc('DeliveryCharge');    
    var refCurrency = database.collection('currencies').where('isActive', '==', true);    

    let taxesByScope = {};
    var taxScope = '';
    var platformCharge = '0';
   

    var packagingCharge = '0';
    var packagingChargeEnable = false;   

    var orderBasePrice = 0;
    var orderTaxAmount = 0;
    var total_tax_amount = 0;
    var subscriptionTotalOrders = -1;

    let vendorDetails = null;
    let currencyData = '';
    var section_id = '';
    var decimal_degits = 0;
    //check letter here for country whom inside user is
    document.addEventListener('DOMContentLoaded', async function() {   
        
        let userRef = await refUsers.doc(selectedRestaurantId).get();
        let userData = userRef.data();        
        
        selectedRestaurantId = userData.vendorID;       
        if(userData.vendorID != null && userData.vendorID != ''){
            const vendorSnap = await database.collection('vendors').doc(userData.vendorID).get();
            if (vendorSnap.exists) {
                vendorDetails = vendorSnap.data();
                section_id = vendorDetails.section_id;                 
                await database.collection('sections').doc(section_id).get().then(snapshot => {
                    var vendor_data = snapshot.data();
                    packagingChargeEnable = vendor_data.packagingChargeEnable
                }); 
                if(packagingChargeEnable){
                    packagingCharge = vendorDetails.packagingCharge ?? '0';
                }                
                let vendorCountry = getCookie('vendorCountryName');
                                // if (!vendorCountry) {
                //     vendorCountry = await getCountryFromLatLng(vendorDetails.latitude,vendorDetails.longitude);
                //     setCookie('vendorCountryName', vendorCountry, 365);
                // }
                setTimeout(async function () {
                    if (!vendorCountry && vendorDetails?.latitude && vendorDetails?.longitude) {
                        vendorCountry = await getCountryFromLatLng(
                            vendorDetails.latitude,
                            vendorDetails.longitude
                        );
                        setCookie('vendorCountryName', vendorCountry, 365);
                    }
                }, 2000);
                if(vendorCountry != null){
                    const scopes = ['order', 'packaging', 'platform', 'product'];
                    database.collection('tax').where('country', '==', vendorCountry).where('sectionId','==',section_id).where('enable', '==', true).where('scope', 'in', scopes).get().then(snapshot => {
                        snapshot.forEach(doc => {
                            const tax = doc.data();
                            (taxesByScope[tax.scope] ??= []).push(tax);
                        });
                    });
                }
                await database.collection('sections').doc(section_id).get().then(snapshot => {
                    var platformData = snapshot.data();
                    if (platformData?.platformFee?.enable) {
                        platformCharge = platformData.platformFee.fee;
                    }
                });                         
            }
        }

        placeholder.get().then(async function (snapshotsimage) {            
            var placeholderImageData = snapshotsimage.data();            
            placeholderImage = placeholderImageData.image;
        });

        DeliveryCharge.get().then(async function (deliveryChargeSnapshots) {
            deliveryChargemain = deliveryChargeSnapshots.data();
            deliveryCharge = deliveryChargemain.amount;
            $("#deliveryChargeMain").val(deliveryCharge);
            $("#deliveryCharge").val(deliveryCharge);
        });

        await refCurrency.get().then(async function (snapshots) {
            currencyData = snapshots.docs[0].data();
            currentCurrency = currencyData.symbol;
            currencyAtRight = currencyData.symbolAtRight;
            if (currencyData.decimal_degits) {
                decimal_degits = currencyData.decimal_degits;
            }
        });
        
        var refGlobal = database.collection('settings').doc("globalSettings");
        await refGlobal.get().then(async function(
            settingSnapshots) {
            if (settingSnapshots.data()) {
                var settingData = settingSnapshots.data();
                taxScope = settingData.taxScope;
            }
        });
        $('#data-table_processing').show();

        // === Fetch or load Admin Commission Settings ===

        let commissionData = null;
        let adminCommissionSettings = null;
        // === 1. Check vendor commission first ===
        if (
            vendorDetails &&
            vendorDetails.adminCommission &&
            vendorDetails.adminCommission.enable
        ) {
            adminCommissionSettings = vendorDetails.adminCommission;
            commissionData = vendorDetails.adminCommission;
        } else {
            // === 2. Fallback to cached section commission ===
            let cachedCommission = localStorage.getItem('adminCommissionSettings');
            if (cachedCommission) {
                cachedCommission = JSON.parse(cachedCommission);
                if (cachedCommission.enable) {
                    adminCommissionSettings = cachedCommission;
                    commissionData = cachedCommission;
                }
            } else {
                // === 3. Fetch section commission from Firestore ===
                try {
                    const settingsRef = firebase.firestore().collection('sections').doc(section_id);
                    const doc = await settingsRef.get();
                    if (doc.exists && doc.data().adminCommision) {
                        adminCommissionSettings = doc.data().adminCommision;
                        commissionData = adminCommissionSettings;
                        localStorage.setItem(
                            'adminCommissionSettings',
                            JSON.stringify(adminCommissionSettings)
                        );
                    }
                } catch (err) {
                    console.warn("Could not fetch AdminCommission settings:", err);
                }
            }
        }

        // === Final values ===
        isCommissionEnabled = adminCommissionSettings?.enable || false;
        commissionType = adminCommissionSettings?.type || "percentage";
        commissionValue = parseFloat(adminCommissionSettings?.commission || 0);

        // await clearCartAjax();
        await loadCategories();
        await loadProducts();
        await loadUsers();
        formatCartPrices();

        if($('#cart_list tr').length > 0 && !$('#cart_list').find('td[colspan="4"]').length) $('.shop-payment-method').show();

        $('#category_dropdown').on('change', async function () {
            selectedCategoryId = $(this).val();
            lastVisible = null;
            isLastPage = false;
            await loadProducts();
        });  
        function resetPagination() {
            currentPage = 1;
            pageCursors = {};
            lastVisible = null;
            isLastPage = false;
        }            

        let debounceTimeout;
      
        $('#search-product').on('input', function () {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(async () => {
                searchText = $(this).val().trim().toLowerCase();
                currentPage = 1;
                pageCursors = {};
                await loadProducts(1);
            }, 300);
        });  
        $('#user_dropdown').select2({
            placeholder: "{{trans('lang.search_for_user')}}",
            allowClear: true,
            width: '100%'
        }); 
        $('#category_dropdown').select2({
            placeholder: "{{trans('lang.select_categories')}}",
            allowClear: true,
            width: '100%'
        });    
       
    });

    async function loadCategories() {
        try {
            
            const querySnapshot = await refCategories.where('section_id', '==', section_id).get();
            let optionsHtml = '<option value="">{{trans("lang.select_categories")}}</option>';

            querySnapshot.forEach((doc) => {
                const data = doc.data();
                const id = doc.id;
                const title = data.title || "Unnamed";
                optionsHtml += `<option value="${id}">${title}</option>`;
            });

            $('#category_dropdown').html(optionsHtml);
            $('#category_dropdown').select2({
                placeholder: "{{trans('lang.select_categories')}}",
                allowClear: true,
                width: '100%'
            });  
        } catch (error) {
            console.error("Error fetching categories:", error);
        }
    }

    async function loadUsers() {
        try {
            const querySnapshot = await refUsers.where('role', '==', 'customer').where('active','==',true).get();
            let optionsHtml = '<option value="">{{trans("lang.search_for_user")}}</option>';

            querySnapshot.forEach((doc) => {
                const data = doc.data();
                const id = doc.id;
                const userName = data.firstName + ' ' + data.lastName || "Unnamed";
                optionsHtml += `<option value="${id}">${userName}</option>`;
            });

            $('#user_dropdown').html(optionsHtml);

            $('#user_dropdown').select2({
                placeholder: "{{trans('lang.search_for_user')}}",
                allowClear: true,
                width: '100%'
            });
        } catch (error) {
            console.error("Error fetching categories:", error);
        }
    }

    async function loadProducts(page = 1) {       
        $('#data-table_processing').show();

        if (!selectedRestaurantId) {
            $('#product-list').html(`
                <div class="col-12 d-flex justify-content-center align-items-center" style="min-height:150px;">
                    <p class="text-center w-100 text-muted">
                        {{ trans('lang.no_product_found') }}
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

            if (selectedCategoryId) {
                query = query.where('categoryID', '==', selectedCategoryId);
            }
            if (selectedRestaurantId) {
                query = query.where('vendorID', '==', selectedRestaurantId).where('publish', '==', true);
            }

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
            function formatPrice(amount) {                c
                const formatted = parseFloat(amount).toFixed(decimal_degits);
                return currencyAtRight 
                    ? `${formatted}${currentCurrency}` 
                    : `${currentCurrency}${formatted}`;
            }
          
            docs.forEach((doc) => {
                const data = doc.data();
                const id = doc.id;
                const name = data.name || 'Unnamed';
                const photo = data.photo || placeholderImage;

                 status = '{{ trans('lang.non_veg') }}';
                statusclass = 'closed';
                if (data.veg == true) {
                    status = '{{ trans('lang.veg') }}';
                    statusclass = 'open';
                }

                let priceHtmlContent = '';
                let originalCartPrice = 0;                

                function formatPrice(amount) {                    
                    const formatted = parseFloat(amount).toFixed(decimal_degits);
                    return currencyAtRight 
                        ? `${formatted}${currentCurrency}` 
                        : `${currentCurrency}${formatted}`;
                }

                // Case 1: Product with disPrice (no variants)
                if (data.disPrice && data.disPrice !== '0' && data.disPrice !== "" && !data.item_attribute) {
                    let originalPrice = parseFloat(data.price || 0);
                    let discountedPrice = parseFloat(data.disPrice);

                    originalCartPrice = discountedPrice; // ← Store original discounted price

                    let displayOriginal = originalPrice;
                    let displayDiscounted = discountedPrice;

                    /* if (isCommissionEnabled) {
                        displayOriginal = applyCommission(originalPrice);
                        displayDiscounted = applyCommission(discountedPrice);
                    }*/
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
                    originalCartPrice = originalPrice; // ← Store original

                    // let displayPrice = isCommissionEnabled ? applyCommission(originalPrice) : originalPrice;
                    let displayPrice = isCommissionEnabled ? originalPrice : originalPrice;
                    priceHtmlContent = formatPrice(displayPrice);
                }

                let route1 = '{{route("items.edit",":id")}}'.replace(':id', id);

                html += `
                    <div class="text-center col-md-4">
                        <div class="shop-item m-3">
                            <h3 class="shop-item-title mb-2 h6">
                                <a href="${route1}" class="text text-dark">${name}</a>
                            </h3>
                             <div class="member-plan position-absolute"><span class="badge badge-dark ${statusclass}">${status}</span></div>
                            <div class="shop-item-image d-flex justify-content-center align-items-center mb-3">
                                <a href="${route1}">
                                    <img class="item-image" src="${photo}" alt="${name}" 
                                        onerror="this.onerror=null;this.src='${placeholderImage}';">
                                </a>
                            </div>
                            <div class="shop-price mb-3">
                                <span class="form-control text-center text-dark">${priceHtmlContent}</span>
                            </div>
                            <div class="shop-item-btn justify-content-center add-to-cart" data-id="${id}">
                                <button class="btn btn-primary">{{trans('lang.add_to_cart')}}</button>
                            </div>
                            ${renderHiddenInputs(data, originalCartPrice)}
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

    function renderHiddenInputs(data, disPrice) {
        return `
            <input type="hidden" name="name_${data.id}" id="name_${data.id}" value="${data.name}">
            <input type="hidden" id="description_${data.id}" name="description_${data.id}" value='${data.description || ""}'>
            <input type="hidden" id="price_${data.id}" name="price_${data.id}" value="${data.price}">
            <input type="hidden" id="dis_price_${data.id}" name="dis_price_${data.id}" value="${disPrice}">
            <input type="hidden" id="discount_${data.id}" name="discount_${data.id}" value="${data.discount}">
            <input type="hidden" id="quantity_${data.id}" name="quantity_${data.id}" value="${data.quantity}">
            <input type="hidden" id="image_${data.id}" name="image_${data.id}" value="${data.photo ? data.photo : placeholderImage}">
            <input type="hidden" id="category_id_${data.id}" name="category_id_${data.id}" value="${data.categoryID}">
            <input type="hidden" id="vendor_id_${data.id}" name="vendor_id_${data.id}" value="${data.vendorID}">
            <input type="hidden" id="item_attribute_${data.id}" name="item_attribute_${data.id}" value='${JSON.stringify(data.item_attribute || {})}'>
            <input type="hidden" id="addons_price_${data.id}" value='${JSON.stringify(data.addOnsPrice || [])}'>
            <input type="hidden" id="addons_title_${data.id}" value='${JSON.stringify(data.addOnsTitle || [])}'>
            <input type="hidden" id="taxSetting_${data.id}" value='${JSON.stringify(data.taxSetting || [])}'>
        `;
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

    $('#category_dropdown').on('change', async function () {
        selectedCategoryId = $(this).val();
        currentPage = 1;
        pageCursors = {};
        await loadProducts(1);
    });
    $('#clear_user_search').on('click', function () {
        $('#user_dropdown').val('').trigger('change');
        $('#user_detail_block').addClass('d-none');
    });
    $('#clear_filter').on('click', function () {
        $('#category_dropdown').val('').trigger('change');
        $('#search-product').val('');
        selectedCategoryId = null;
        searchText = '';
        
    });
 
    $(document).on('click', '.add-to-cart', async function () {
        
        const productId = $(this).data('id');
       
        $('#addToCartModal').modal('show');
      
        const name = $('#name_' + productId).val() || 'N/A';
        const description = $('#description_' + productId).val() || '';
        const price = $('#price_' + productId).val() || '0';
        const discount = $('#discount_' + productId).val() || '0';
        const discountPrice = $('#dis_price_' + productId).val() || '0';
        const quantity = $('#quantity_' + productId).val() || '0';
       
        const photo = $('#image_' + productId).val() || '';
        const categoryId = $('#category_id_' + productId).val() || '';
        var vendorId = $('#vendor_id_' + productId).val() || '';
        let raw = $('#item_attribute_' + productId).val();
        let itemAttributes = {};
     
        let displayPrice = '';
        const hasDiscount = parseFloat(discount) > 0;

        let basePrice = parseFloat(price);
    

        if (hasDiscount) {
            const discountAmount = basePrice * parseFloat(discount) / 100;
            basePrice = basePrice - discountAmount;
        }

        // Apply admin commission AFTER discount
        displayPrice = currencyAtRight
            ? basePrice.toFixed(decimal_degits) + currentCurrency
            : currentCurrency + basePrice.toFixed(decimal_degits);


        let imgSrc = photo && photo !== '' ? photo : placeholderImage;

        $('#modal-product-image')
            .attr('src', imgSrc)
            .off('error') // avoid multiple bindings
            .on('error', function () {
                $(this).attr('src', placeholderImage);
            });
        $('#modal-product-name').text(name);
        
        let priceHtml = '';

        const originalPrice = parseFloat(price || 0);
      
        const disPrice = parseFloat(discountPrice || 0);

        if (disPrice > 0 && disPrice < originalPrice) {
          

            const formattedDis = currencyAtRight
                ? disPrice.toFixed(decimal_degits) + currentCurrency
                : currentCurrency + disPrice.toFixed(decimal_degits);

            const formattedOriginal = currencyAtRight
                ? originalPrice.toFixed(decimal_degits) + currentCurrency
                : currentCurrency + originalPrice.toFixed(decimal_degits);

            priceHtml = `
                <span class="text-danger font-weight-bold">${formattedDis}</span>
                <s class="text-muted ml-2">${formattedOriginal}</s>
            `;

        } else {
         

            const formatted = currencyAtRight
                ? originalPrice.toFixed(decimal_degits) + currentCurrency
                : currentCurrency + originalPrice.toFixed(decimal_degits);

            priceHtml = `<span>${formatted}</span>`;
        }

        // Apply to modal
        descriptionHtml = description || '-';
        $('#modal-product-price').text(
            description.length > 28 ? description.slice(0, 28) + '...' : description
        );
        $('#variant_price').html(priceHtml);

        if(quantity && quantity == -1){
            $('.modal-product-quantity-count').text('Unlimited');
        }else{
            $('.modal-product-quantity-count').text(quantity);
        }
        if(vendorId){
            const restaurantName = await getRestaurantName(vendorId);
            $('.modal-product-restaurant-name').text(restaurantName); // If you use restaurant instead of description, adjust this line
        }else{
            $('.modal-product-restaurant-name').text("-");
        }
       
        // $('#variant_price').text(displayPrice);
        
        $('#modal-add-cart-btn').data('product-id', productId);

        try {
            itemAttributes = JSON.parse(raw);
        } catch (e) {
            console.error("Invalid JSON:", raw);
        }

       
        $('#modal-attributes-container').empty();
        $('#modal-addons-container').empty();
        $('#variant_price').empty();
        $('#variant_qty').empty();
        $('#selected_variant_id').val('');
        $('#selected_variant_price').val('');

        if (
            itemAttributes &&
            itemAttributes.attributes &&
            itemAttributes.variants &&
            itemAttributes.attributes.length > 0 &&
            itemAttributes.variants.length > 0
        ) {
            const productData = {
                id: productId,
                name: name,
                description: description,
                price: price,
                discount: discount,
                stock_quantity: quantity,
                quantity: 1,                
                image: photo,
                category_id: categoryId,
                vendor_id: vendorId,
                item_attribute: itemAttributes
            };

            const encodedProduct = btoa(encodeURIComponent(JSON.stringify(productData)));

            $('#attribute-list-' + productId).data('product', encodedProduct);

            const variantHtml = await getVariantsHtml(productData, itemAttributes.attributes, itemAttributes.variants);
            $('#modal-attributes-container').html(variantHtml);
        } else {
            $('#modal-attributes-container').html('');
            // Reset price display for non-variant products
            let basePriceForNonVariant = disPrice > 0 && disPrice < originalPrice ? disPrice : originalPrice;
            let displayPriceForNonVariant = currencyAtRight
                ? basePriceForNonVariant.toFixed(decimal_degits) + currentCurrency
                : currentCurrency + basePriceForNonVariant.toFixed(decimal_degits);
            $('#variant_price').html(displayPriceForNonVariant);
        }
       
        // ===== ADDONS =====
        let addonsPriceRaw = $('#addons_price_' + productId).val();
        let addonsTitleRaw = $('#addons_title_' + productId).val();

        let addonsPrice = [];
        let addonsTitle = [];

        try {
            addonsPrice = JSON.parse(addonsPriceRaw || '[]');
            addonsTitle = JSON.parse(addonsTitleRaw || '[]');
        } catch (e) {
            console.error('Invalid addon JSON');
        }

        if (addonsPrice.length > 0) {
        
            let addonsHtml = `
                <div class="addons mt-2 mb-3">
                    <h5 class="font-weight-bold mb-3">{{ trans('lang.addons') }}</h5>
            `;
           for (let i = 0; i < addonsPrice.length; i++) {

                let originalAddonPrice = parseFloat(addonsPrice[i] || 0);

                let formattedPrice = currencyAtRight
                    ? originalAddonPrice.toFixed(decimal_degits) + currentCurrency
                    : currentCurrency + originalAddonPrice.toFixed(decimal_degits);

                addonsHtml += `
                    <div class="form-group row width-100 border-bottom p-0">
                        <div class="form-check width-100 pl-0">
                            <input 
                                type="checkbox"
                                class="form-check-input addon-checkbox"
                                id="addon_${productId}_${i}"
                                data-price="${originalAddonPrice}"
                                value="${addonsTitle[i]}"
                            >
                            <label class="control-label d-flex justify-content-between w-100" 
                                for="addon_${productId}_${i}">
                                <span>${addonsTitle[i]}</span>
                                <span>+${formattedPrice}</span>
                            </label>
                        </div>
                    </div>
                `;
            }


            addonsHtml += `</div>`;

            $('#modal-addons-container').html(addonsHtml);

        } else {
            $('#modal-addons-container').html('');
        }


    });
    async function getRestaurantName(restaurantId) {
        if (!restaurantId) return '';

        try {
            const doc = await database.collection('vendors').doc(restaurantId).get();
            if (doc.exists) {
                const data = doc.data();
                return data.title || ''; 
            } else {
                console.warn(`Vendor with ID ${restaurantId} not found`);
                return '';
            }
        } catch (error) {
            console.error("Error fetching Vendor:", error);
            return '';
        }
    }
    async function getVariantsHtml(vendorProduct, attributes, variants) {
        var attributesHtml = '';
        
        // Check if attributes exist and is an array
        if (!attributes || !Array.isArray(attributes) || attributes.length === 0) {
            return '';
        }
        
        for (let attribute of attributes) {
            // Skip if attribute is null or undefined
            if (!attribute) continue;
            
            var attributeHtmlRes = getAttributeHtml(vendorProduct, attribute);
            var attributeHtml = await attributeHtmlRes.then(function (html) {
                return html || '';
            }).catch(function() {
                return '';
            });
            attributesHtml += attributeHtml;
        }
        
        if ($('#attribute-list-' + vendorProduct.id).length) {
            $('#attribute-list-' + vendorProduct.id).html(attributesHtml);
        }
        
        setTimeout(() => {
            $('#modal-attributes-container .attribute-radio:checked')
                .first()
                .trigger('change');
        }, 0);

        var variation_info = {};
        var variation_sku = '';
        $('#attribute-list-' + vendorProduct.id + ' .attribute-drp').each(function () {
            variant_title = $(this).data('atitle') + '-';
            variation_sku += $(this).find('input[type="radio"]:checked').val() + '-';
            variation_info[$(this).data('atitle')] = $(this).find('input[type="radio"]:checked').val();
        });
        variation_sku = variation_sku.replace(/-$/, "");

        if (variation_sku && vendorProduct.item_attribute && vendorProduct.item_attribute.variants) {
            var variant_info = $.map(vendorProduct.item_attribute.variants, function (v, i) {
                if (v.variant_sku == variation_sku) {
                    return v;
                }
            });
            var variant_id = variant_image = variant_price = variant_price = variant_quantity = '';
            if (variant_info.length > 0) {
                var variant_id = variant_info[0].variant_id;
                var variant_image = variant_info[0].variant_image;
                var raw_variant_price = parseFloat(variant_info[0].variant_price);
                var variant_price = raw_variant_price;
                var variant_sku = variant_info[0].variant_sku;
                var variant_img = variant_info[0].variant_image;
                var variant_quantity = variant_info[0].variant_quantity;
                if (currencyAtRight) {
                    var pro_price = variant_price.toFixed(decimal_degits) + "" + currentCurrency;
                } else {
                    var pro_price = currentCurrency + "" + variant_price.toFixed(decimal_degits);
                }
                
                if ($('#variation_info_' + vendorProduct.id).length) {
                    $('#variation_info_' + vendorProduct.id).find('#variant_price').html(pro_price);
                    $('#variation_info_' + vendorProduct.id).find('#variant_price').attr('data-vid', variant_id);
                    $('#variation_info_' + vendorProduct.id).find('#variant_price').attr('data-vprice', variant_price);
                    $('#variation_info_' + vendorProduct.id).find('#variant_price').attr('data-vqty', variant_quantity);
                    $('#variation_info_' + vendorProduct.id).find('#variant_price').attr('data-vsku', variant_sku);
                    $('#variation_info_' + vendorProduct.id).find('#variant_price').attr('data-vimg', variant_img);
                    $('#variation_info_' + vendorProduct.id).find('#variant_price').attr('data-vinfo', JSON.stringify(variation_info));
                    
                    if (variant_quantity == '-1') {
                        $('#variation_info_' + vendorProduct.id).find('#variant_qty').html('{{trans("lang.qty_left")}}: {{trans("lang.unlimited")}}');
                    } else {
                        $('#variation_info_' + vendorProduct.id).find('#variant_qty').html('{{trans("lang.qty_left")}}: ' + variant_quantity);
                    }
                }
                
                // Update modal temp section
                if ($('#variation_info_temp').length) {
                    $('#variation_info_temp #variant_price')
                        .html(pro_price)
                        .attr('data-vid', variant_id)
                        .attr('data-vprice', variant_price)
                        .attr('data-vqty', variant_quantity)
                        .attr('data-vsku', variant_sku)
                        .attr('data-vimg', variant_img)
                        .attr('data-vinfo', JSON.stringify(variation_info));

                    $('#variation_info_temp #variant_qty').html(
                        variant_quantity == '-1' ? '{{trans("lang.qty_left")}}: {{trans("lang.unlimited")}}' : '{{trans("lang.qty_left")}}: ' + variant_quantity
                    );
                }
            }
        }
        return attributesHtml;
    }
    function getAttributeHtml(vendorProduct, attribute) {
        var html = '';
        
        // Check if attribute exists and has attribute_id
        if (!attribute || !attribute.attribute_id) {
            return Promise.resolve('');
        }        
        
        var vendorAttributesRef = database.collection('vendor_attributes').where('id', "==", attribute.attribute_id);
        
        return vendorAttributesRef.get().then(async function (attributeRef) {
            // Check if any documents were returned
            if (!attributeRef.docs || attributeRef.docs.length === 0) {
                return '';
            }
            
            var attributeInfo = attributeRef.docs[0].data();
            
            // Check if attributeInfo exists
            if (!attributeInfo) {
                return '';
            }

            html += '<div class="attribute-drp col-md-6" data-aid="' + attribute.attribute_id + '" data-atitle="' + (attributeInfo.title || '') + '">';
            html += '<h4 class="attribute-label">' + (attributeInfo.title || '') + '</h4>';
            html += '<div class="attribute-options">';
            
            if (attribute.attribute_options && attribute.attribute_options.length > 0) {
                $.each(attribute.attribute_options, function (i, option) {
                    const inputId = 'attribute-' + attribute.attribute_id + '-' + option.replace(/\s/g, '');
                    const checked = (i === 0) ? ' checked' : '';

                    html += '<div class="custom-control custom-radio border-bottom py-2 attribute-selection">';
                    html += '<input type="radio"'
                        + ' id="' + inputId + '"'
                        + ' name="attribute-options-' + attribute.attribute_id + '"'
                        + ' class="custom-control-input attribute-radio"'
                        + ' data-atitle="' + (attributeInfo.title || '') + '"'
                        + ' value="' + option.replace(/"/g, '&quot;') + '"'
                        + checked + '>';
                    html += '<label class="custom-control-label" for="' + inputId + '">' + option + '</label>';
                    html += '</div>';
                });
            }
            
            html += '</div>';
            html += '</div>';
            return html;
        }).catch(function(error) {
            console.error("Error fetching attribute:", error);
            return '';
        });
    }

    function getVariantPrice(vendorProduct) {
        var variation_info = {};
        var variation_sku = '';
        $('#attribute-list-' + vendorProduct.id + ' .attribute-drp').each(function () {
            var aid = $(this).parent().parent().data('aid');
            variation_sku += $(this).find('input[type="radio"]:checked').val() + '-';
            variation_info[$(this).data('atitle')] = $(this).find('input[type="radio"]:checked').val();
        });
        variation_sku = variation_sku.replace(/-$/, "");
        if (variation_sku) {
            var variant_info = $.map(vendorProduct.item_attribute.variants, function (v, i) {
                if (v.variant_sku == variation_sku) {
                    return v;
                }
            });

            var variant_id = variant_image = variant_price = variant_price = variant_quantity = '';
            if (variant_info.length > 0) {

                var variant_id = variant_info[0].variant_id;
                var variant_image = variant_info[0].variant_image;
                if (variant_image == undefined) {
                    variant_image = placeholderImage
                }
                var raw_variant_price = parseFloat(variant_info[0].variant_price);
                // var variant_price = applyCommission(raw_variant_price);
                var variant_price = raw_variant_price;
                var variant_sku = variant_info[0].variant_sku;
                var variant_quantity = variant_info[0].variant_quantity;
                if (currencyAtRight) {
                    var pro_price = variant_price.toFixed(decimal_degits) + "" + currentCurrency;
                } else {
                    var pro_price = currentCurrency + "" + variant_price.toFixed(decimal_degits);
                }
                
                $('#variation_info_' + vendorProduct.id).find('#variant_price').html(pro_price);
                $('#variation_info_' + vendorProduct.id).find('#variant_price').attr('data-vid', variant_id);
                $('#variation_info_' + vendorProduct.id).find('#variant_price').attr('data-vprice', variant_price);
                $('#variation_info_' + vendorProduct.id).find('#variant_price').attr('data-vqty', variant_quantity);
                $('#variation_info_' + vendorProduct.id).find('#variant_price').attr('data-vsku', variant_sku);
                $('#variation_info_' + vendorProduct.id).find('#variant_price').attr('data-vimg', variant_image);

                $('#variation_info_' + vendorProduct.id).find('#variant_price').attr('data-vinfo', JSON.stringify(variation_info));
                if (variant_quantity == '-1') {
                    $('#variation_info_' + vendorProduct.id).find('#variant_qty').html('{{trans("lang.qty_left")}}: {{trans("lang.unlimited")}}');
                } else {
                    $('#variation_info_' + vendorProduct.id).find('#variant_qty').html('{{trans("lang.qty_left")}}: ' + variant_quantity);
                }       
                //new added for modal 
                $('#variation_info_temp #variant_price')
                    .html(pro_price)
                    .attr('data-vid', variant_id)
                    .attr('data-vprice', variant_price)
                    .attr('data-vqty', variant_quantity)
                    .attr('data-vsku', variant_sku)
                    .attr('data-vimg', variant_img)
                    .attr('data-vinfo', JSON.stringify(variation_info));

                $('#variation_info_temp #variant_qty').html(
                    variant_quantity == '-1' ? '{{trans("lang.qty_left")}}: {{trans("lang.unlimited")}}' : '{{trans("lang.qty_left")}}: ' + variant_quantity
                );        

            }
        }
    }
    
    $(document).on("click", '#modal-add-cart-btn', async function (event) {

        @guest
            window.location.href = '<?php echo route('login'); ?>';
            return false;
        @endguest

        var id = $(this).data('product-id');
        var quantity = 1;
        var productQty = $('input[name="quantity_' + id + '"]').val();

        if (productQty == 0) {
            Swal.fire({ text: "{{trans('lang.invalid_qty')}}", icon: "error" });
            return false;
        }

        var discount = parseFloat($('input[name="discount_' + id + '"]').val() || 0);
        var dis_price = parseFloat($('input[name="dis_price_' + id + '"]').val() || 0);
        var name = $('input[name="name_' + id + '"]').val();
        var description = $('input[name="description_' + id + '"]').val() || '';
     
        var stock_quantity = $('#quantity_' + id).val();
        var image = $('input[name="image_' + id + '"]').val() || placeholderImage;
        var category_id = $('input[name="category_id_' + id + '"]').val();
        
        let raw = $('#item_attribute_' + id).val();
        let itemAttributes = {};
        try {
            itemAttributes = JSON.parse(raw || '{}');
        } catch (e) {
            console.error("Invalid JSON:", raw);
        }

        let hasVariant = itemAttributes &&
                        itemAttributes.attributes &&
                        itemAttributes.variants &&
                        itemAttributes.attributes.length > 0 &&
                        itemAttributes.variants.length > 0;

        let variant_info = {};
        let originalBasePrice = 0; // This will be the price stored in session (without commission)

        // === Determine original base price (no commission applied) ===
        if (hasVariant) {
            let selectedOptions = [];
            $('#modal-attributes-container .attribute-options').each(function () {
                const selected = $(this).find('input[type="radio"]:checked').val();
                if (selected) selectedOptions.push(selected.trim());
            });

            let totalAttributes = itemAttributes.attributes.length;
            let selectedCount = selectedOptions.length;

            if (selectedCount === 0) {
                // No variant selected → use main product
                variant_info = {};
            } else if (selectedCount < totalAttributes) {
                Swal.fire({
                    text: "{{trans('lang.select_all_variant_attributes')}}",
                    icon: "error"
                });
                return false;
            } else {
                // All attributes selected → find matching variant
                let normalizedSelectedSku = selectedOptions.join('-').toLowerCase().replace(/\s+/g, '');
                let matchedVariant = null;

                itemAttributes.variants.forEach(v => {
                    let variantSku = (v.variant_sku || '').toLowerCase().replace(/\s+/g, '');
                    if (variantSku === normalizedSelectedSku) {
                        matchedVariant = v;
                    }
                });

                if (!matchedVariant || !matchedVariant.variant_id) {
                    Swal.fire({ text: "{{trans('lang.no_valid_variant_matched')}}", icon: "error" });
                    return false;
                }

                let variant_qty = parseInt(matchedVariant.variant_quantity);
                if (quantity > variant_qty && variant_qty != -1) {
                    Swal.fire({ text: "{{trans('lang.invalid_qty')}}", icon: "error" });
                    return false;
                }

                // Fetch attribute titles for display
                let variant_options_map = {};
                for (let index = 0; index < itemAttributes.attributes.length; index++) {
                    const attribute = itemAttributes.attributes[index];
                    const selected = selectedOptions[index];

                    try {
                        const vendorAttrSnap = await database.collection('vendor_attributes')
                            .doc(attribute.attribute_id)
                            .get();

                        const title = vendorAttrSnap.exists ? vendorAttrSnap.data().title : attribute.attribute_id;
                        variant_options_map[title] = selected;
                    } catch (error) {
                        console.error("Error fetching vendor attribute:", error);
                    }
                }

                // Store ORIGINAL variant price in session
                originalBasePrice = parseFloat(matchedVariant.variant_price || 0);

                variant_info = {
                    variant_id: matchedVariant.variant_id,
                    variant_sku: matchedVariant.variant_sku,
                    variant_price: originalBasePrice,                    // ← Original price (no commission)
                    variant_qty: matchedVariant.variant_quantity,
                    variant_image: matchedVariant.variant_image ?? placeholderImage,
                    variant_options: variant_options_map
                };

                discount = 0;
                dis_price = 0;
            }
        }

        // If no variant or none selected → use main product price
        if (originalBasePrice === 0) {
            let rawPrice = parseFloat($('input[name="price_' + id + '"]').val() || 0);
            if (dis_price > 0) {
                rawPrice = dis_price;
            }
            originalBasePrice = rawPrice;
        }

        let selectedAddons = [];
        let selectedAddonsTotal = 0;

        $('.addon-checkbox:checked').each(function () {
            let addonPrice = parseFloat($(this).data('price') || 0); // Already has commission applied

            selectedAddons.push({
                title: $(this).val(),
                price: addonPrice
            });

            selectedAddonsTotal += addonPrice;
        });

        let priceForSession = originalBasePrice + selectedAddonsTotal;
        let totalPriceForSession = priceForSession * quantity;

        setCookie('deliveryChargemain', JSON.stringify(deliveryChargemain), 356);

        let productTaxSetting = $('#taxSetting_' + id).val();            
        $.ajax({
            type: 'POST',
            url: "{{ route('add-to-cart') }}",
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
                quantity: quantity,
                stock_quantity: stock_quantity,
                name: name,
                original_base_price: originalBasePrice,
                price: totalPriceForSession,           // Total with quantity
                dis_price: dis_price,
                discount: discount,
                image: image,
                item_price: priceForSession,           // Per unit price
                variant_info: JSON.stringify(variant_info),
                category_id: category_id,
                decimal_degits: decimal_degits,
                addons: JSON.stringify(selectedAddons),
                addons_total: selectedAddonsTotal,
                commission_enabled: isCommissionEnabled ? '1' : '0',
                commission_type: commissionType,
                commission_value: commissionValue,
                taxSetting: productTaxSetting,
                taxScope: taxScope,
                taxesByScope: taxesByScope,
                packagingCharge: packagingCharge,
                platformCharge: platformCharge,
                currencyData: currencyData,
                restaurant_id: selectedRestaurantId,
            },
            success: function (response) {
                $('#cart_list').html(response.html);
                $('#cart_total').html(response.total);
                formatCartPrices();
                loadcurrency();
                $('.shop-payment-method').show();
                $('#addToCartModal').modal('hide');
                Swal.fire({
                    text: "{{trans('lang.item_added_to_cart')}}",
                    icon: "success",
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    });
    function loadcurrency() {
        if (currencyAtRight) {
            jQuery('.currency-symbol-left').hide();
            jQuery('.currency-symbol-right').show();
            jQuery('.currency-symbol-right').text(currentCurrency);
        } else {
            jQuery('.currency-symbol-left').show();
            jQuery('.currency-symbol-right').hide();
            jQuery('.currency-symbol-left').text(currentCurrency);
        }
    }
   
    $(document).on('click', '.update-cart', function () {
        const index = $(this).data('index');
        const operation = $(this).data('operation');

        $.ajax({
            url: '{{ route("cart.update") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                index: index,
                operation: operation,
                commission_enabled: isCommissionEnabled ? '1' : '0',
                commission_type: commissionType,
                commission_value: commissionValue,
            },
            success: function (res) {
                $('#cart_list').html(res.html);
                $('#cart_total').html(res.total);
                formatCartPrices();
                $('.shop-payment-method').show();
                if (res.error) {
                    Swal.fire({
                        icon: 'warning',
                        text: res.error,
                    });
                }
            }
        });
    });

    $(document).on('click', '.delete-cart-item', function () {
        const index = $(this).data('index');

        $.ajax({
            url: '{{ route("cart.remove",["index" => ":index"]) }}'.replace(":index", index),
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function (res) {

                if (res.empty) {                            
                    clearCartAjax();
                }

                // cart still has items
                $('#cart_list').html(res.html);
                $('#cart_total').html(res.total);
                formatCartPrices();
                $('.shop-payment-method').show();
            }
        });
    });
    
    $(document).on('click', '#clear_cart_btn', async function (e) {
        e.preventDefault();

        await clearCartAjax();
    });



    function clearCartAjax() {
        return $.ajax({
            url: '{{ route('clear.cart') }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                if (response.success) {
                    $('#cartTable tbody').empty();
                   

                    $('#cart_list').html(`
                        <tr>
                            <td colspan="4" class="text-center">
                                {{ trans('lang.cart_is_empty') }}
                            </td>
                        </tr>
                    `);
                    $('#cart_total').empty();
                    $('.shop-payment-method').hide();

                    Swal.fire({
                        text: "{{ trans('lang.cart_has_been_cleared') }}",
                        icon: "success",
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            }
        });
    } 
    $(document).on('change', '#modal-attributes-container .attribute-radio', function () {

        const productId = $('#modal-add-cart-btn').data('product-id');
        const raw = $('#item_attribute_' + productId).val();

        if (!raw) return;

        let itemAttributes = JSON.parse(raw);

        let selectedValues = {};
        let skuParts = [];

        $('#modal-attributes-container .attribute-drp').each(function () {

            const title = $(this).data('atitle');
            const value = $(this).find('.attribute-radio:checked').val();

            if (!value) return;

            selectedValues[title] = value;
            skuParts.push(value);
        });

        if (skuParts.length !== $('#modal-attributes-container .attribute-drp').length) {
            return;
        }

        const selectedSku = skuParts.join('-');

        const matchedVariant = itemAttributes.variants.find(v =>
            v.variant_sku === selectedSku
        );

        const $priceTag = $('#variant_price');
        const $qtyTag = $('.modal-product-quantity-count');

        if (!matchedVariant) {
            $priceTag.text('');
            $qtyTag.text('');
            return;
        }

        const rawPrice = parseFloat(matchedVariant.variant_price || 0);
        const variantPrice = rawPrice;

        let addonTotal = 0;
        $('.addon-checkbox:checked').each(function () {
            addonTotal += parseFloat($(this).data('price') || 0);
        });

        let totalWithAddons =  parseFloat(addonTotal) +  parseFloat(variantPrice);
        
        const priceDisplay = currencyAtRight
            ? totalWithAddons.toFixed(decimal_degits) + currentCurrency
            : currentCurrency + totalWithAddons.toFixed(decimal_degits);

        $priceTag
            .html(priceDisplay)
            .attr({
                'data-vid': matchedVariant.variant_id,
                'data-vprice': variantPrice,
                'data-vqty': matchedVariant.variant_quantity,
                'data-vsku': matchedVariant.variant_sku,
                'data-vimg': matchedVariant.variant_image || '',
                'data-vinfo': JSON.stringify(selectedValues)
            });
          

        $('#selected_variant_id').val(matchedVariant.variant_id);
        $('#selected_variant_price').val(variantPrice);

     
         $('.modal-product-quantity-count').text(
            matchedVariant.variant_quantity === '-1' ? 'Unlimited' : matchedVariant.variant_quantity
        );
    });


    $(document).on('click', '#place_order_btn', async function () {

        const response = await fetch('/get-session-cart');
        const cart = await response.json();

        const selectedUser = $('#user_dropdown').val();
        const selectedPaymentMethod = $('input[name="paymentmethod"]:checked').val();

        const orderVendorID = selectedRestaurantId;        

        if (!orderVendorID) {
            Swal.fire({
                icon: 'error',
                text: '{{trans("lang.vendor_not_found_for_cart_items")}}',
            });
            return;
        }
                
        if (!cart || !cart.item) {
            Swal.fire({
                icon: 'warning',
                text: "{{trans('lang.cart_is_empty_error')}}",
            });
            return;
        }

       if (!selectedUser) {
            Swal.fire({
                icon: 'warning',
                text: "{{trans('lang.select_user_before_placing_order')}}",
            });
            return;
        }

        if (!selectedPaymentMethod) {
            Swal.fire({
                icon: 'warning',
                text: "{{trans('lang.please_select_payment_method')}}",
            });
            return;
        }

        var id_order = database.collection('vendor_orders').doc().id;        
        var userSnapshot = await database.collection('users').doc(selectedUser).get();
        var userDetails = '';
        let defaultShippingAddress = null;
        if (userSnapshot.exists) {
            userDetails = userSnapshot.data();
            const shippingAddresses = userDetails.shippingAddress || [];
            if (Array.isArray(shippingAddresses)) {
                defaultShippingAddress = shippingAddresses.find(addr => addr && addr.isDefault === true) || null;
            }
        }

        if (vendorDetails == null) {
            Swal.fire({
                icon: 'error',
                text: '{{trans("lang.vendor_record_not_found")}}',
            });
            return;
        } 

        let products = [];

        for (const [restaurantId, restaurantItems] of Object.entries(cart.item)) {

            // loop products
            for (const [productId, item] of Object.entries(restaurantItems)) {

                let product = {
                    id: item.id || null,
                    name: item.name || '',
                    category_id: item.category_id || '',
                    price: String(applyCommission(item.original_base_price || item.price || 0)),
                    // discountPrice: String(applyCommission(item.dis_price || 0)),
                    discountPrice: String(
                        (item.variant_info && item.variant_info.variant_id) ? 0 : (applyCommission(item.discountPrice || item.dis_price || 0))
                    ),
                    quantity: parseInt(item.quantity) || 1,
                    photo: item.image || '',
                    vendorID: orderVendorID,
                    variant_info: null,
                    extras: item.extras ?? [],
                    extras_price: String(applyCommission(item.extras_price ?? 0)),
                    taxSetting: item.taxSetting ?? [],
                };

                if (item.variant_info) {
                    let variantInfo = item.variant_info;
                    if (typeof variantInfo === 'string') {
                        try {
                            variantInfo = JSON.parse(variantInfo);
                        } catch (e) {
                            console.error('Invalid variant_info JSON', e);
                            variantInfo = {};
                        }
                    }
                    if (variantInfo.variant_id) {
                        product.variant_info = {
                            variant_id: variantInfo.variant_id || '',
                            variant_image: variantInfo.variant_image || '',
                            variant_options: variantInfo.variant_options || [],
                            variant_price: String(variantInfo.variant_price || ''),
                            variant_qty: String(variantInfo.variant_qty || ''),
                            variant_sku: variantInfo.variant_sku || ''
                        };
                    }
                }
                products.push(product);
            }
        }
       
        var taxSetting = cart.taxSetting ? cart.taxSetting : [];
        var taxScope = cart.taxScope ? cart.taxScope : 'order';
        var driverDeliveryTax = (cart.taxesByScope && cart.taxesByScope.delivery) ? cart.taxesByScope.delivery : [];
        var packagingTax = (cart.taxesByScope && cart.taxesByScope.packaging) ? cart.taxesByScope.packaging : [];
        var platformTax = (cart.taxesByScope && cart.taxesByScope.platform) ? cart.taxesByScope.platform : [];
        var platformCharge = cart.platformCharge ? cart.platformCharge : '0';

        let orderData = {          
            "createdAt": firebase.firestore.FieldValue.serverTimestamp(),          
            'deliveryCharge': "0",
            'discount': 0,
            'couponCode': "",
            'couponId': "",
            'notes': "",
            'driverID': null,            
            'scheduleTime': null,
            'id': id_order,          
            'payment_method': selectedPaymentMethod,           
            'adminCommission': String(commissionValue),
            'adminCommissionType': commissionType,
            'products': products,
            'tip_amount': "0.0",
            'takeAway': false,
            'status': "Order Completed",            
            "taxSetting": taxSetting,           
            "author":userDetails,
            "authorID": selectedUser,           
            'vendor': vendorDetails,           
            'vendorID': orderVendorID,
            'isPosOrder': true,
            'taxScope': taxScope,
            "driverDeliveryTax": driverDeliveryTax,
            "packagingTax": packagingTax,
            "platformFee": platformCharge,
            "platformTax": platformTax,
            "section_id": section_id,
            "packagingChargeEnable": packagingChargeEnable,
        };

        if (defaultShippingAddress) {
            orderData.address = defaultShippingAddress;
        }

        await manageInventory(products);

        await walletTransaction(products, cart, id_order, orderVendorID, vendorDetails);

        await database.collection('vendor_orders').doc(id_order).set(
           orderData
        ).then(() => {
            $.ajax({
                url: "{{ route('clear.cart') }}", 
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function () {
                    Swal.fire({
                        icon: 'success',
                        title: "{{trans('lang.order_placed_successfully')}}",
                        text: "{{trans('lang.order_has_been_placed')}}",
                    }).then(() => {
                        window.location.reload(); 
                    });
                }
            });
        })
        .catch((error) => {
            console.error("Error placing order:", error);
            Swal.fire({
                icon: 'error',
                title: "{{trans('lang.order_failed')}}",
                text: "{{trans('lang.there_was_issue_placing_order')}}",
            });
        });
    });
    function formatCartPrices() {
        document.querySelectorAll('.cart-price').forEach(function (el) {
            var rawPrice = el.getAttribute('data-price');

            if (currencyAtRight) {
                el.innerHTML = parseFloat(rawPrice).toFixed(decimal_degits) + '' + currentCurrency;
            } else {
                el.innerHTML = currentCurrency + '' + parseFloat(rawPrice).toFixed(decimal_degits);
            }
        });
    }
    async function manageInventory(products) {

        for (let i = 0; i < products.length; i++) {

            const item = products[i];
            const product_id = item.id;
            const orderQty = parseInt(item.quantity) || 0;

            const variantInfo = item.variant_info || null;

            const productDoc = await database.collection('vendor_products').doc(product_id).get();
            if (!productDoc.exists) continue;

            const productInfo = productDoc.data();
           
            // CASE 1: Variant product (has variant_sku in session)
            
            if (variantInfo && variantInfo.variant_sku) {

                let variants = productInfo.item_attribute?.variants || [];

                let updatedVariants = variants.map(v => {

                    // match both sides by variant_sku
                    if (v.variant_sku === variantInfo.variant_sku) {

                        let currentQty = (v.variant_quantity !== undefined && v.variant_quantity !== "-1")
                                        ? parseInt(v.variant_quantity)
                                        : null;

                        if (currentQty !== null) {
                            let newQty = currentQty - orderQty;
                            v.variant_quantity = (newQty <= 0 ? 0 : newQty).toString();
                        }
                    }

                    return v;
                });

                await database.collection('vendor_products')
                    .doc(product_id)
                    .update({
                        "item_attribute.variants": updatedVariants
                    });

            }

            // CASE 2: Simple product (no variant)
            else {
                let currentQty = parseInt(productInfo.quantity);
                if (!isNaN(currentQty) && currentQty !== -1) {

                    let currentQty = parseInt(productInfo.quantity);
                    let newQty = currentQty - orderQty;
                    if (newQty < 0) newQty = 0;

                    await database.collection('vendor_products')
                        .doc(product_id)
                        .update({
                            quantity: newQty
                        });
                }
            }
        }
    }

    async function walletTransaction(products, cart, id_order, orderVendorID, vendorDetails) {

        let order_subtotal = 0;
        let total_discount = 0;
        //  Calculate subtotal and product extras
        for (let i = 0; i < products.length; i++) {
            let product = products[i];
            let basePrice = (product.discountPrice && parseFloat(product.discountPrice) > 0) ? parseFloat(product.discountPrice) : parseFloat(product.price);
            let itemGross = (basePrice + parseFloat(product.extras_price || 0)) * parseInt(product.quantity);
            order_subtotal += itemGross;
        }

        // Calculate item-level taxes (if product-level)
        var taxScope = cart.taxScope ? cart.taxScope : 'order';
        var taxSetting = cart.taxSetting ? cart.taxSetting : [];
        var packagingTax = (cart.taxesByScope && cart.taxesByScope.packaging) ? cart.taxesByScope.packaging : [];
        
        if (taxScope === "product") {
            let itemSubtotal = order_subtotal;
            let itemCombinedTax = 0;
            products.forEach(product => {
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
        if (taxScope === "order") {
            let orderTaxable = Math.max(0, order_subtotal - total_discount);
            let orderCombinedTax = 0;
            taxSetting.forEach(tax => {
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

        orderTaxAmount = total_tax_amount

        // packaging taxes
        let extraCharges = [
            {key: 'packaging', amount: packagingCharge, taxes: packagingTax || []},
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
                    orderTaxAmount += parseFloat(taxAmount);
                }
            });
        });
        
        var priceWithCommision = order_subtotal;
        var adminCommHtml = "";
        if (commissionType == "Percent") {
            basePrice = (priceWithCommision / (1 + (parseFloat(commissionValue) / 100)));
            adminCommission = parseFloat(priceWithCommision - basePrice);
        } else {
            basePrice = priceWithCommision - commissionValue;
            adminCommission = parseFloat(priceWithCommision - basePrice);
        }
        
        orderBasePrice = (basePrice - total_discount ) + parseFloat(packagingCharge);

        var wId = database.collection('temp').doc().id;

        let vendorAuthor = vendorDetails.author;

        await database.collection('wallet').doc(wId).set({
            'amount': parseFloat(orderBasePrice).toFixed(decimal_degits),
            'date': firebase.firestore.FieldValue.serverTimestamp(),
            'id': wId,
            'isTopUp': true,
            'order_id': id_order,
            'payment_method': 'Wallet',
            'payment_status': 'success',
            'transactionUser': 'vendor',
            'note': 'Order amount credited',
            'user_id': vendorAuthor
        });

        var vendorAmount = orderBasePrice;

        if (orderTaxAmount != 0 && orderTaxAmount != '') {
            var wId2 = database.collection('temp').doc().id;
            await database.collection('wallet').doc(wId2).set({
                'amount': parseFloat(orderTaxAmount).toFixed(decimal_degits),
                'date': firebase.firestore.FieldValue.serverTimestamp(),
                'id': wId2,
                'isTopUp': true,
                'order_id': id_order,
                'payment_method': 'tax',
                'payment_status': 'success',
                'transactionUser': 'vendor',
                'user_id': vendorAuthor,
                'note': 'Order tax credited'
            });
        }

        const snapshotsnew = await database.collection('users').where('id', '==', vendorAuthor).get();

        var vendordata = snapshotsnew.docs[0].data();

        if (vendordata) {
            if (parseInt(subscriptionTotalOrders) != -1) {
                subscriptionTotalOrders = parseInt(subscriptionTotalOrders) - 1;
                await database.collection('vendors')
                    .doc(vendorAuthor)
                    .update({
                        'subscriptionTotalOrders': subscriptionTotalOrders.toString()
                    });                    
            }
            var vendorWallet = isNaN(vendordata.wallet_amount) || vendordata.wallet_amount == undefined
            ? 0 : parseFloat(vendordata.wallet_amount);
            var newVendorWallet = vendorWallet + vendorAmount + parseFloat(orderTaxAmount);
            await database.collection('users')
                .doc(vendorAuthor)
                .update({
                    'wallet_amount': parseFloat(newVendorWallet).toFixed(decimal_degits)
            });
        }
    }
    
    $(document).on('change', '.addon-checkbox', function () {

        const productId = $('#modal-add-cart-btn').data('product-id');
        let basePrice = 0;
        
        // Check if there are attributes in the modal (meaning it's a variant product)
        const hasAttributes = $('#modal-attributes-container .attribute-drp').length > 0;
        
        if (hasAttributes) {
            // For variant product, use selected variant price
            basePrice = parseFloat($('#selected_variant_price').val() || 0);
        } else {
            // For non-variant product, use the dis_price from hidden input
            basePrice = parseFloat($(`#dis_price_${productId}`).val() || 0);
        }
        
        // If basePrice is still 0, fallback to regular price
        if (basePrice === 0) {
            basePrice = parseFloat($(`#price_${productId}`).val() || 0);
        }
        

        let addonTotal = 0;
        $('.addon-checkbox:checked').each(function () {
            addonTotal += parseFloat($(this).data('price') || 0);
        });

        let finalPrice = basePrice + addonTotal;
     

        let display = currencyAtRight
            ? finalPrice.toFixed(decimal_degits) + currentCurrency
            : currentCurrency + finalPrice.toFixed(decimal_degits);

        $('#variant_price').html(display);
    });


    $('#user_dropdown').on('change', async function () {
        const userId = $(this).val();

        if (!userId) {
            $('#user_detail_block').addClass('d-none');
            return;
        }

        try {
            const userDoc = await refUsers.doc(userId).get();

            if (!userDoc.exists) {
                $('#user_detail_block').addClass('d-none');
                return;
            }

            const data = userDoc.data();

            const fullName = ((data.firstName || '') + ' ' + (data.lastName || '')).trim() || 'N/A';
            const email = data.email || 'N/A';
            const phone = data.phoneNumber || 'N/A';
            const wallet = data.wallet_amount ? parseFloat(data.wallet_amount).toFixed(2) : '0.00';

            $('#user_detail_name').text(fullName);
            $('#user_detail_email').text(email);
            $('#user_detail_phone').text(phone);
            $('#user_detail_wallet').text('$ ' + wallet);

            $('#user_detail_block').removeClass('d-none');

        } catch (error) {
            console.error('Error loading user details:', error);
            $('#user_detail_block').addClass('d-none');
        }
    });
    function applyCommission(basePrice) {
        if (!isCommissionEnabled || isNaN(basePrice)) {
            return parseFloat(basePrice || 0);
        }

        let final = parseFloat(basePrice);
        if (commissionType === "percentage") {
            final += final * (commissionValue / 100);
        } else {
            final += commissionValue;
        }
        return final;
    }
    
</script>
@endsection
