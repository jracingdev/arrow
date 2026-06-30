@include('layouts.app')
@include('layouts.header')
<div class="siddhi-home-page">
    <div class="bg-primary px-3 d-none mobile-filter pb-3">
        <div class="row align-items-center">
            <div class="input-group rounded shadow-sm overflow-hidden col-md-9 col-sm-9">
                <div class="input-group-prepend">
                    <button class="border-0 btn btn-outline-secondary text-dark bg-white btn-block">
                        <i class="feather-search"></i>
                    </button>
                </div>
                <input type="text" class="shadow-none border-0 form-control" placeholder="Search for vendors or dishes">
            </div>
            <div class="text-white col-md-3 col-sm-3">
                <div class="title d-flex align-items-center">
                    <a class="text-white font-weight-bold ml-auto" data-toggle="modal" data-target="#exampleModal" href="#">{{ trans('lang.filter') }}</a>
                </div>
            </div>
        </div>
    </div>
    <div class="ecommerce-banner multivendor-banner">
        <div class="ecommerce-inner">
            <div class="" id="top_banner"></div>
        </div>
    </div>
    <div class="ecommerce-content multi-vendore-content">
        <section class="restaurant_stories">
            <div class="container">
                <div id="stories" class="storiesWrapper"></div>
            </div>
        </section>
        <section class="top-categories">
            <div class="container">
                <div class="title d-flex align-items-center">
                    <h5>{{ trans('lang.top_categories') }}</h5>
                    <span class="see-all ml-auto">
                        <a href="{{ route('categorylist') }}">{{ trans('lang.see_all') }}</a>
                    </span>
                </div>
                <div class="append_categories" id="append_categories"></div>
            </div>
        </section>
        <section class="popular-section">
            <div class="container">
                <div class="title d-flex align-items-center">
                    <h5>{{ trans('lang.popular') }} {{ trans('lang.item') }}</h5>
                    <span class="see-all ml-auto">
                        <a href="{{ route('productlist.all') }}">{{ trans('lang.see_all') }}</a>
                    </span>
                </div>
                <div class="most_popular" id="most_sale1"></div>
            </div>
        </section>
        <section class="popular-fashion-store">
            <div class="container">
                <div class="title d-flex align-items-center">
                    <h5>{{ trans('lang.popular') }} {{ trans('lang.stores') }}</h5>
                    <span class="see-all ml-auto">
                        <a href="{{ route('vendors', 'popular=yes') }}">{{ trans('lang.see_all') }}</a>
                    </span>
                </div>
                <div class="most_popular" id="most_popular"></div>
            </div>
        </section>
        <section class="new-arrivals">
            <div class="container">
                <div class="title d-flex align-items-center">
                    <h5>{{ trans('lang.new_arrivals') }}</h5>
                    <span class="see-all ml-auto">
                        <a href="{{ route('vendors') }}">{{ trans('lang.see_all') }}</a>
                    </span>
                </div>
                <div class="most_sale1" id="new_arrival"></div>
            </div>
        </section>
        <section class="vendor-offer-section">
            <div class="container">
                <div class="title d-flex align-items-center">
                    <h5>{{ trans('lang.offers') }} {{ trans('lang.for_you') }}</h5>
                    <span class="see-all ml-auto">
                        <a href="{{ route('offers') }}">{{ trans('lang.see_all') }}</a>
                    </span>
                </div>
                <div style="display:none" class="coupon_code_copied_div mt-4 bg-success text-white text-center">
                    <span>{{ trans('lang.coupon_code_copied') }}</span>
                </div>
                <div class="most_sale1" id="offers_coupons"></div>
            </div>
        </section>
        <section class="middle-banners">
            <div class="container">
                <div class="" id="middle_banner"></div>
            </div>
        </section>
        <section class="home-categories">
            <div class="container" id="home_categories"></div>
        </section>
        <section class="all-store-section">
            <div class="container">
                <div class="title d-flex align-items-center">
                    <h5>{{ trans('lang.all_stores') }}</h5>
                    <span class="see-all ml-auto">
                        <a href="{{ route('vendors') }}">{{ trans('lang.see_all') }}</a>
                    </span>
                </div>
                <div class="most_sale1" id="all_stores"></div>
                <div class="row fu-loadmore-btn">
                    <a class="page-link loadmore-btn" href="javascript:void(0);" onclick="moreload()" data-dt-idx="0" tabindex="0" id="loadmore">{{ trans('lang.see') }} {{ trans('lang.more') }}</a>
                    <p style="display: none;color: red" id="noMoreCoupons">No More Store found..</p>
                </div>
            </div>
        </section>
    </div>
    @include('layouts.footer')
    <link rel="stylesheet" href="{{ asset('css/dist/zuck.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dist/skins/snapssenger.css') }}">
    <script src="{{ asset('js/dist/zuck.min.js') }}"></script>
    <script src="https://unpkg.com/geofirestore@5.2.0/dist/geofirestore.js"></script>
    <script src="https://cdn.firebase.com/libs/geofire/5.0.1/geofire.min.js"></script>
    <script type="text/javascript" src="{{ asset('vendor/slick/slick.min.js') }}"></script>
    <script type="text/javascript">
        var firestore = firebase.firestore();
        var geoFirestore = new GeoFirestore(firestore);
        var vendorId;
        var ref;
        var append_list = '';
        var append_categories = '';
        var most_popular = '';
        var most_sale = '';
        var offers_coupons = '';
        var appName = '';
        var popularStoresList = [];
        var inValidVendors = [];
        var myInterval = '';
        var currentCurrency = '';
        var currencyAtRight = false;
        var VendorNearBy = '';
        var radiusUnit = 'Km';
        var vendorNearByRef = database.collection('sections').doc(section_id);
        var radiusUnitRef = database.collection('settings').doc('DriverNearBy');
        var pagesize = 12;
        var offest = 1;
        var end = null;
        var endarray = [];
        var start = null;
        var itemCategoriesref = database.collection('vendor_categories').where('section_id', '==', section_id).where("publish", "==", true).limit(7);
        var bannerref = database.collection('banner_items').where('sectionId', '==', section_id).where("is_publish", "==", true).orderBy('set_order', 'asc');
        var vendorsref = geoFirestore.collection('vendors').where('section_id', '==', section_id);
        var refCurrency = database.collection('currencies').where('isActive', '==', true);
        var placeholderImageRef = database.collection('settings').doc('placeHolderImage');
        var placeholderImageSrc = '';
        placeholderImageRef.get().then(async function(placeholderImageSnapshots) {
            var placeHolderImageData = placeholderImageSnapshots.data();
            placeholderImageSrc = placeHolderImageData.image;
        })
        radiusUnitRef.get().then(async function(radiusSnapshots) {
            var radiusUnitData = radiusSnapshots.data();
            radiusUnit = radiusUnitData.distanceType;
        })
        bannerref.get().then(async function(banners) {
            var position1_banners = [];
            var position2_banners = [];
            banners.docs.forEach((banner) => {
                var bannerData = banner.data();
                var redirect_type = '';
                var redirect_id = '';
                if (bannerData.position == 'top') {
                    if (bannerData.hasOwnProperty('redirect_type')) {
                        redirect_type = bannerData.redirect_type;
                        redirect_id = bannerData.redirect_id;
                    }
                    var object = {
                        'photo': bannerData.web_banner,
                        'redirect_type': redirect_type,
                        'redirect_id': redirect_id,
                    }
                    position1_banners.push(object);
                }
                if (bannerData.position == 'middle') {
                    if (bannerData.hasOwnProperty('redirect_type')) {
                        redirect_type = bannerData.redirect_type;
                        redirect_id = bannerData.redirect_id;
                    }
                    var object = {
                        'photo': bannerData.web_banner,
                        'redirect_type': redirect_type,
                        'redirect_id': redirect_id,
                    }
                    position2_banners.push(object);
                }
            });
            if (position1_banners.length > 0) {
                var html = '';
                for (banner of position1_banners) {
                    html += '<div class="banner-item">';
                    html += '<div class="banner-img">';
                    var redirect_id = 'javascript::void()';
                    if (banner.redirect_type != '') {
                        if (banner.redirect_type == "store") {
                            redirect_id = "{{ route('vendor', ':id') }}";
                            redirect_id = redirect_id.replace(':id', banner.redirect_id);
                        } else if (banner.redirect_type == "product") {
                            redirect_id = "{{ route('productdetail', ':id') }}";
                            redirect_id = redirect_id.replace(':id', banner.redirect_id);
                        } else if (banner.redirect_type == "external_link") {
                            redirect_id = banner.redirect_id;
                        }
                    }
                    if (banner.photo) {
                        photo = banner.photo;
                    } else {
                        photo = placeholderImageSrc;
                    }
                    html += '<a href="' + redirect_id + '"><img src="' + banner.photo + '" onerror="this.onerror=null;this.src=\'' + placeholderImageSrc + '\'"></a>';
                    html += '</div>';
                    html += '</div>';
                }
                $("#top_banner").html(html);
            }
            if (position2_banners.length > 0) {
                var html = '';
                for (banner of position2_banners) {
                    html += '<div class="banner-item">';
                    html += '<div class="banner-img">';
                    var redirect_id = 'javascript::void()';
                    if (banner.redirect_type != '') {
                        if (banner.redirect_type == "store") {
                            redirect_id = "{{ route('vendor', ':id') }}";
                            redirect_id = redirect_id.replace(':id', banner.redirect_id);
                        } else if (banner.redirect_type == "product") {
                            redirect_id = "{{ route('productdetail', ':id') }}";
                            redirect_id = redirect_id.replace(':id', banner.redirect_id);
                        } else if (banner.redirect_type == "external_link") {
                            redirect_id = banner.redirect_id;
                        }
                    }
                    if (banner.photo) {
                        photo = banner.photo;
                    } else {
                        photo = placeholderImageSrc;
                    }
                    html += '<a href="' + redirect_id + '"><img src="' + photo + '" onerror="this.onerror=null;this.src=\'' + placeholderImageSrc + '\'" ></a>';
                    html += '</div>';
                    html += '</div>';
                }
                $("#middle_banner").html(html);
            } else {
                $('.middle-banners').remove();
            }
            slickcatCarousel();
        });
        const refs = database.collection('vendors').where('section_id', '==', section_id).limit(pagesize);
        const popularRestauantRef = geoFirestore.collection('vendors').where('section_id', '==', section_id).where('reviewsSum', '>', 4);
        var decimal_degits = 0;
        refCurrency.get().then(async function(snapshots) {
            var currencyData = snapshots.docs[0].data();
            currentCurrency = currencyData.symbol;
            currencyAtRight = currencyData.symbolAtRight;
            if (currencyData.decimal_degits) {
                decimal_degits = currencyData.decimal_degits;
            }
        });
        var storyEnabled = false;
        database.collection('settings').doc("story").get().then(async function(snapshots) {
            var story_data = snapshots.data();
            if (story_data.isEnabled) {
                getStories();
            } else {
                $(".restaurant_stories").remove();
            }
        });
        var couponsRef = database.collection('coupons').where('isEnabled', '==', true).where('isPublic', '==', true).where("section_id", "==", section_id).orderBy("expiresAt").startAt(new Date()).limit(4);
        var globalSettingsRef = database.collection('settings').doc("globalSettings");
        globalSettingsRef.get().then(async function(globalSettingsSnapshots) {
            var appData = globalSettingsSnapshots.data();
            appName = appData.applicationName;
        })
        if (address_lat == '' || address_lng == '' || address_lng == NaN || address_lat == NaN || address_lat == null || address_lng == null) {
            var res = getCurrentLocation();
        }

        function myStopTimer() {
            clearInterval(myInterval);
        }

        $(document).ready(async function() {
            jQuery("#overlay").show();
            inValidVendors = await getInvaidUserIds();
            priceData = await fetchVendorPriceData();

            myInterval = setInterval(callStore, 1000);
            getHomepageCategory();
            getItemCategories();
            getAllStore();
            getCouponsList();
        });

    
        async function getHomepageCategory() {
            var home_cat_ref = database.collection('vendor_categories')
                .where('section_id', '==', section_id)
                .where("publish", "==", true)
                .where('show_in_homepage', '==', true)
                .limit(5);

            let homeCategoriesSnapshot = await home_cat_ref.get();
            let home_categories = document.getElementById('home_categories');
            home_categories.innerHTML = '';

            if (homeCategoriesSnapshot.empty) {
                $('.home-categories').remove();
                return;
            }

            let categoryPromises = [];
            let homeCategorieshtml = '';

            for (let doc of homeCategoriesSnapshot.docs) {
                let categoryData = doc.data();
                categoryData.id = doc.id;

                let category_route = "{{ route('productlist', [':type', ':id']) }}"
                    .replace(':type', 'category')
                    .replace(':id', categoryData.id);

                let photo = categoryData.photo || placeholderImageSrc;

                categoryPromises.push((async () => {
                    let haveProducts = await catHaveProducts(categoryData.id);
                    if (!haveProducts) return '';

                    let productHtml = await buildHTMLHomeCategoryProducts(categoryData.id);

                    return `
                <div class="category-content mb-5">
                    <div class="title d-flex align-items-center">
                        <h5>${categoryData.title}</h5>
                        <span class="see-all ml-auto"><a href="${category_route}">{!! trans('lang.see_all') !!}</a></span>
                    </div>
                    ${productHtml}
                </div>`;
                })());
            }

            let categoryHtmlResults = await Promise.all(categoryPromises);
            homeCategorieshtml = categoryHtmlResults.filter(html => html !== '').join('');

            if (homeCategorieshtml) {
                home_categories.innerHTML = homeCategorieshtml;
            } else {
                $('.home-categories').remove();
            }
        }

        async function callStore() {
            if (address_lat == '' || address_lng == '' || address_lng == NaN || address_lat == NaN || address_lat == null || address_lng == null) {
                return false;
            }
            vendorNearByRef.get().then(async function(vendorNearByRefSnapshots) {
                var vendorNearByRefData = vendorNearByRefSnapshots.data();
                if (vendorNearByRefData.hasOwnProperty('nearByRadius') && vendorNearByRefData.nearByRadius != '') {
                    VendorNearBy = parseInt(vendorNearByRefData.nearByRadius);
                    if (radiusUnit == 'Miles') {
                        VendorNearBy = parseInt(VendorNearBy * 1.60934)
                    }
                }
                address_lat = parseFloat(address_lat);
                address_lng = parseFloat(address_lng);
                myStopTimer();
                getMostPopularStores();
                getMostSalesStore();
            })
        }

        async function getItemCategories() {
            itemCategoriesref.get().then(async function(foodCategories) {
                append_categories = document.getElementById('append_categories');
                append_categories.innerHTML = '';
                foodCategorieshtml = buildHTMLItemCategory(foodCategories);
                append_categories.innerHTML = foodCategorieshtml;
            })
        }

        async function getPopularItem() {
            if (popularStoresList.length > 0) {
                var popularStoresListnw = [];
                append_trending_vendor = document.getElementById('most_sale1');
                append_trending_vendor.innerHTML = '';
                var from = 0;
                var total = 0;
                for (let i = 0; i < (popularStoresList.length / 10); i++) {
                    from = i * 10;
                    popularStoresListnw = [];
                    total = 0;
                    for (let j = 0; j < popularStoresList.length; j++) {
                        if (j > from && total < 10) {
                            total++;
                            popularStoresListnw.push(popularStoresList[j]);
                        }
                    }
                    if (popularStoresListnw.length) {
                        var refpopularItem = database.collection('vendor_products').where("vendorID", "in", popularStoresListnw).limit(4);
                        refpopularItem.get().then(async function(snapshotsPopularItem) {
                            var trendingStorehtml = await buildHTMLPopularItem(snapshotsPopularItem);
                            append_trending_vendor.innerHTML = trendingStorehtml;
                        });

                    }
                }
            }
        }

        async function getMostPopularStores() {
            if (VendorNearBy != '') {
                var popularRestauantRefnew = geoFirestore.collection('vendors').near({
                    center: new firebase.firestore.GeoPoint(address_lat, address_lng),
                    radius: VendorNearBy
                }).where('section_id', '==', section_id).limit(200);
            } else {
                var popularRestauantRefnew = database.collection('vendors').where('section_id', '==', section_id).limit(200);
            }
            popularRestauantRefnew.get().then(async function(popularRestauantSnapshot) {
                most_popular = document.getElementById('most_popular');
                most_popular.innerHTML = '';
                var popularStorehtml = buildHTMLPopularStore(popularRestauantSnapshot);
                most_popular.innerHTML = popularStorehtml;
            })

        }

        async function getMostSalesStore() {
            var mostSalesStore = vendorsref.where('section_id', '==', section_id).limit(4);
            mostSalesStore.get().then(async function(mostSaleSnapshot) {
                most_sale = document.getElementById('new_arrival');
                most_sale.innerHTML = '';
                var mostSaleStorehtml = buildHTMLMostSaleStore(mostSaleSnapshot);
                most_sale.innerHTML = mostSaleStorehtml;
            })
        }

        async function getAllStore() {
            refs.get().then(async function(snapshots) {
                if (snapshots != undefined) {
                    var html = buildAllStoresHTML(snapshots);
                    var append_list = document.getElementById('all_stores');
                    append_list.innerHTML = html;
                    start = snapshots.docs[snapshots.docs.length - 1];
                    endarray.push(snapshots.docs[0]);
                    if (snapshots.docs.length < pagesize) {
                        $('#loadmore').hide();
                    }
                    jQuery("#overlay").hide();
                }
            });
        }

        function buildAllStoresHTML(snapshots) {
            var html = '';
            var alldata = [];
            if (snapshots.docs.length > 0) {
                snapshots.docs.forEach((listval) => {
                    var datas = listval.data();
                    datas.id = listval.id;
                    if (!inValidVendors.includes(datas.author)) {
                        alldata.push(datas);
                    }
                });
                var count = 0;
                html = html + '<div class="row">';
                alldata.forEach((listval) => {
                    var val = listval;

                    var rating = 0;
                    var reviewsCount = 0;
                    if (val.hasOwnProperty('reviewsSum') && val.reviewsSum != 0 && val.hasOwnProperty('reviewsCount') && val.reviewsCount != 0) {
                        rating = (val.reviewsSum / val.reviewsCount);
                        rating = Math.round(rating * 10) / 10;
                        reviewsCount = val.reviewsCount;
                    }
                    var status = 'Closed';
                    var statusclass = "closed";
                    if (val.hasOwnProperty('reststatus') && val.reststatus) {
                        status = 'Open';
                        statusclass = "open";
                    }
                    var vendor_id_single = val.id;
                    var view_vendor_details = "{{ route('vendor', ':id') }}";
                    view_vendor_details = view_vendor_details.replace(':id', vendor_id_single);
                    count++;
                    html = html + '<div class="col-md-3 pro-list"><div class="list-card position-relative"><div class="list-card-image">';
                    if (val.photo) {
                        photo = val.photo;
                    } else {
                        photo = placeholderImageSrc;
                    }
                    html = html + '<a href="' + view_vendor_details + '"><img alt="#" src="' + photo + '" onerror="this.onerror=null;this.src=\'' + placeholderImageSrc + '\'"  class="img-fluid item-img w-100"></a></div><div class="py-2 position-relative"><div class="list-card-body"><h6 class="mb-1 popul-title"><a href="' + view_vendor_details + '" class="text-black">' + val.title + '</a></h6><h6>' + val.location + '</h6>';
                    html = html + '<div class="star position-relative mt-3"><span class="badge badge-success "><i class="feather-star"></i>' + rating + ' (' + reviewsCount + ')</span></div>';
                    html = html + '</div>';
                    html = html + '</div></div></div>';
                });
                html = html + '</div>';
            } else {
                $('#noMoreCoupons').show();
                $('#loadmore').hide();
                setTimeout(
                    function() {
                        $("#noMoreCoupons").hide();
                    }, 4000);
            }
            return html;
        }

        function buildHTMLItemCategory(foodCategories) {
            var html = '';
            var alldata = [];
            foodCategories.docs.forEach((listval) => {
                var datas = listval.data();
                datas.id = listval.id;
                alldata.push(datas);
            });
            html += '<div class="row">';
            alldata.forEach((listval) => {
                var val = listval;
                var category_id = val.id;
                var trending_route = "{{ route('vendorsbycategory', [':id']) }}";
                trending_route = trending_route.replace(':id', category_id);
                if (val.publish) {
                    if (val.photo) {
                        photo = val.photo;
                    } else {
                        photo = placeholderImageSrc;
                    }
                    html = html + '<div class="col-md-2 top-cat-list"><a class="d-block text-center cat-link" href="' + trending_route + '"><span class="cat-img"><img alt="#" src="' + photo + '" onerror="this.onerror=null;this.src=\'' + placeholderImageSrc + '\'"  class="img-fluid mb-2"></span><h4 class="m-0">' + val.title + '</h4></a></div>';
                }
            });
            html += '</div>';
            return html;
        }

        async function buildHTMLHomeCategoryProducts(category_id) {
            var html = '';
            var vendorCatRef = database.collection('vendor_products')
                .where('categoryID', "==", category_id)
                .where('section_id', '==', section_id)
                .limit(200);

            var nearestRestauantSnapshot = await vendorCatRef.get();

            var alldata = [];
            var groupedData = {};
            var allVendorIDs = new Set();
            for (const listval of nearestRestauantSnapshot.docs) {
                var datas = listval.data();
                datas.id = listval.id;
                if (!groupedData[datas.vendorID]) {
                    groupedData[datas.vendorID] = [];
                }
                groupedData[datas.vendorID].push(datas);
                allVendorIDs.add(datas.vendorID);
            }

            for (const vendorID of Object.keys(groupedData)) {
                let products = groupedData[vendorID];
                let inValidProductIds = await getUserItemLimit(vendorID);
                products = products.filter(product => !inValidProductIds.includes(product.id));
                alldata = alldata.concat(products);
            }
            alldata = alldata.slice(0, 4);
            var count = 0;
            var popularFoodCount = 0;
            html += '<div class="row">';
            let tempHtml = '';
            
            for (const listval of alldata) {
                var val = listval;
                var vendor_id_single = val.id;
                var view_vendor_details = "{{ route('productdetail', ':id') }}";
                view_vendor_details = view_vendor_details.replace(':id', vendor_id_single);
                var rating = 0;
                var reviewsCount = 0;
                if (val.hasOwnProperty('reviewsSum') && val.reviewsSum !== 0 && val.hasOwnProperty('reviewsCount') && val.reviewsCount !== 0) {
                    rating = (val.reviewsSum / val.reviewsCount);
                    rating = Math.round(rating * 10) / 10;
                    reviewsCount = val.reviewsCount;
                }
                tempHtml += '<div class="col-md-3 product-list"><div class="list-card position-relative"><div class="list-card-image">';
                let photo = val.photo ? val.photo : placeholderImageSrc;
                tempHtml += '<a href="' + view_vendor_details + '"><img alt="#" src="' + photo + '" onerror="this.onerror=null;this.src=\'' + placeholderImageSrc + '\'" class="img-fluid item-img w-100"></a></div><div class="py-2 position-relative"><div class="list-card-body position-relative"><h6 class="product-title mb-1"><a href="' + view_vendor_details + '" class="text-black">' + val.name + '</a></h6>';
                tempHtml += '<h6 class="mb-1 popular_food_category_ pro-cat" id="popular_food_category_' + val.categoryID + '_' + val.id + '" ></h6>';
                let final_price = priceData[val.id];
                if (val.item_attribute && val.item_attribute.variants?.length > 0) {
                    let variantPrices = val.item_attribute.variants.map(v => v.variant_price);
                    let minPrice = Math.min(...variantPrices);
                    let maxPrice = Math.max(...variantPrices);
                    let or_price = minPrice !== maxPrice ?
                        `${getProductFormattedPrice(final_price.min)} - ${getProductFormattedPrice(final_price.max)}` :
                        getProductFormattedPrice(final_price.max);
                    tempHtml += `<h6 class="text-gray mb-1 pro-price">${or_price}</h6>`;
                } else if (val.hasOwnProperty('disPrice') && val.disPrice !== '' && val.disPrice !== '0') {
                    var or_price = getProductFormattedPrice(parseFloat(final_price.price));
                    var dis_price = getProductFormattedPrice(parseFloat(final_price.dis_price));
                    tempHtml += '<span class="pro-price">' + dis_price + '  <s>' + or_price + '</s></span>';
                } else {
                    var or_price = getProductFormattedPrice(parseFloat(final_price.price));
                    tempHtml += '<span class="pro-price">' + or_price + '</span>'
                }
                tempHtml += '<div class="star position-relative mt-3"><span class="badge badge-success"><i class="feather-star"></i>' + rating + ' (' + reviewsCount + ')</span></div>';
                tempHtml += '</div>';
                tempHtml += '</div></div></div>';
            }

            html += tempHtml;
            html += '</div>';

            return html;
        }

        sortArrayOfObjects = (arr, key) => {
            return arr.sort((a, b) => {
                return b[key] - a[key];
            });
        };

        function buildHTMLPopularStore(popularRestauantSnapshot) {
            var html = '';
            var alldata = [];
            popularRestauantSnapshot.docs.forEach((listval) => {
                var datas = listval.data();
                datas.id = listval.id;
                var rating = 0;
                var reviewsCount = 0;
                if (datas.hasOwnProperty('reviewsSum') && datas.reviewsSum != 0 && datas.hasOwnProperty('reviewsCount') && datas.reviewsCount != 0) {
                    rating = (datas.reviewsSum / datas.reviewsCount);
                    rating = Math.round(rating * 10) / 10;
                }
                datas.rating = rating;
                if (!inValidVendors.includes(datas.author)) {
                    alldata.push(datas);
                }
            });
            if (alldata.length) {
                alldata = sortArrayOfObjects(alldata, "rating");
                alldata = alldata.slice(0, 4);
            }
            var count = 0;
            var popularItemCount = 0;
            html = html + '<div class="row">';
            alldata.forEach((listval) => {
                var val = listval;
                var rating = 0;
                var reviewsCount = 0;
                if (val.hasOwnProperty('reviewsSum') && val.reviewsSum != 0 && val.hasOwnProperty('reviewsCount') && val.reviewsCount != 0) {
                    rating = (val.reviewsSum / val.reviewsCount);
                    rating = Math.round(rating * 10) / 10;
                    reviewsCount = val.reviewsCount;
                }
                if (popularItemCount < 10) {
                    popularItemCount++;
                    popularStoresList.push(val.id);
                }
                var status = 'Closed';
                var statusclass = "closed";
                if (val.hasOwnProperty('reststatus') && val.reststatus) {
                    status = 'Open';
                    statusclass = "open";
                }
                var vendor_id_single = val.id;
                var view_vendor_details = "{{ route('vendor', ':id') }}";
                view_vendor_details = view_vendor_details.replace(':id', vendor_id_single);
                count++;
                html = html + '<div class="col-md-3 pro-list"><div class="list-card position-relative"><div class="list-card-image">';
                if (val.photo) {
                    photo = val.photo;
                } else {
                    photo = placeholderImageSrc;
                }
                html = html + '<a href="' + view_vendor_details + '"><img alt="#" src="' + photo + '" onerror="this.onerror=null;this.src=\'' + placeholderImageSrc + '\'" class="img-fluid item-img w-100"></a></div><div class="py-2 position-relative"><div class="list-card-body"><h6 class="mb-1 popul-title"><a href="' + view_vendor_details + '" class="text-black">' + val.title + '</a></h6><h6>' + val.location + '</h6>';
                html = html + '<div class="star position-relative mt-3"><span class="badge badge-success "><i class="feather-star"></i>' + rating + ' (' + reviewsCount + ')</span></div>';
                html = html + '</div>';
                html = html + '</div></div></div>';
            });
            html = html + '</div>';
            getPopularItem();
            return html;
        }

        async function getCouponsList() {
            couponsRef.get().then(async function(couponListSnapshot) {
                offers_coupons = document.getElementById('offers_coupons');
                offers_coupons.innerHTML = '';
                var couponlistHTML = buildHTMLCouponList(couponListSnapshot);
                if (couponlistHTML != '') {
                    offers_coupons.innerHTML = couponlistHTML;
                } else {
                    $('.vendor-offer-section').remove();
                }
            })
        }

        async function moreload() {
            all_stores = document.getElementById('all_stores');
            if (start != undefined || start != null) {
                jQuery("#overlay").hide();
                listener = refs.startAfter(start).limit(pagesize).get();
                listener.then(async (snapshots) => {
                    html = '';
                    html = buildAllStoresHTML(snapshots);
                    jQuery("#overlay").hide();
                    if (html != '') {
                        all_stores.innerHTML += html;
                        start = snapshots.docs[snapshots.docs.length - 1];
                        if (endarray.indexOf(snapshots.docs[0]) != -1) {
                            endarray.splice(endarray.indexOf(snapshots.docs[0]), 1);
                        }
                        endarray.push(snapshots.docs[0]);
                        if (snapshots.docs.length < pagesize) {
                            $('#loadmore').hide();
                        }
                    }
                });
            }
        }

        function buildHTMLCouponList(couponListSnapshot) {
            var html = '';
            var alldata = [];
            couponListSnapshot.docs.forEach((listval) => {
                var datas = listval.data();
                datas.id = listval.id;
                alldata.push(datas);
            });
            if (alldata.length > 0) {
                html = html + '<div class="row">';
                alldata.forEach((listval) => {
                    var val = listval;
                    var status = 'Closed';
                    var statusclass = "closed";
                    if (val.hasOwnProperty('reststatus') && val.reststatus) {
                        status = 'Open';
                        statusclass = "open";
                    }
                    var vendor_id_single = val.vendorID;
                    var view_vendor_details = "";
                    if (vendor_id_single) {
                        view_vendor_details = "{{ route('vendor', ':id') }}";
                        view_vendor_details = view_vendor_details.replace(':id', vendor_id_single);
                    }
                    html = html + '<div class="col-md-3 pro-list"><div class="list-card position-relative"><div class="list-card-image">';
                    if (val.image) {
                        photo = val.image;
                    } else {
                        photo = placeholderImageSrc;
                    }
                    const vendorTitle = getVendorName(vendor_id_single);
                    html = html + '<a href="' + view_vendor_details + '"><img alt="#" src="' + photo + '" onerror="this.onerror=null;this.src=\'' + placeholderImageSrc + '\'" class="img-fluid item-img w-100"></a></div><div class="py-2 position-relative"><div class="list-card-body"><h6 class="mb-1 popul-title"><a href="' + view_vendor_details + '" class="text-black vendor_title_' + vendor_id_single + '"></a></h6>';
                    html = html + '<div class="text-gray mb-1 small"><a href="javascript:void(0)" onclick="copyToClipboard(`' + val.code + '`)"><i class="fa fa-file-text-o"></i> ' + val.code + '</a></div>';
                    html = html + '</div>';
                    html = html + '</div></div></div>';
                });
                html = html + '</div>';
            }
            return html;
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText("");
            navigator.clipboard.writeText(text);
            $(".coupon_code_copied_div").show();
            setTimeout(
                function() {
                    $(".coupon_code_copied_div").hide();
                }, 4000);
        }

        async function getMinDiscount(vendorId) {
            var min_discount = '';
            var disdata = [];
            var discountRes = couponsRef.where('vendorID', '==', vendorId).get().then(function(couponSnapshots) {
                var min_discount = '';
                couponSnapshots.docs.forEach((coupon) => {
                    var cdata = coupon.data();
                    disdata.push(parseInt(cdata.discount));
                });
                if (disdata.length) {
                    discount = Math.min.apply(Math, disdata);
                    min_discount = "Min " + discount + "% off";
                    return min_discount;
                }
            });
            var min_discount = await discountRes.then(function(html) {
                return html;
            })
            $('.vendor_dis_' + vendorId).text(min_discount);
        }

        function buildHTMLMostSaleStore(mostSaleSnapshot) {
            var html = '';
            var alldata = [];
            mostSaleSnapshot.docs.forEach((listval) => {
                var datas = listval.data();
                datas.id = listval.id;
                if (!inValidVendors.includes(datas.author)) {
                    alldata.push(datas);
                }
            });
            html = html + '<div class="row">';
            alldata.forEach((listval) => {
                var val = listval;
                var vendor_id_single = val.id;
                var view_vendor_details = "";
                if (vendor_id_single) {
                    view_vendor_details = "{{ route('vendor', ':id') }}";
                    view_vendor_details = view_vendor_details.replace(':id', vendor_id_single);
                }
                var rating = 0;
                var reviewsCount = 0;
                if (val.hasOwnProperty('reviewsSum') && val.reviewsSum != 0 && val.hasOwnProperty('reviewsCount') && val.reviewsCount != 0) {
                    rating = (val.reviewsSum / val.reviewsCount);
                    rating = Math.round(rating * 10) / 10;
                    reviewsCount = val.reviewsCount;
                }
                var status = 'Closed';
                var statusclass = "closed";
                if (val.hasOwnProperty('reststatus') && val.reststatus) {
                    status = 'Open';
                    statusclass = "open";
                }
                html = html + '<div class="col-md-3 pro-list">' +
                    '<div class="list-card position-relative">' +
                    '<div class="py-2 position-relative">' +
                    '<div class="list-card-body">' +
                    '<div class="list-card-top">' +
                    '<h6 class="mb-1 popul-title"><a href="' + view_vendor_details + '" class="text-black">' + val.title + '</a></h6><h6>' + val.location + '</h6>';
                html = html + '<div class="star position-relative mt-3"><span class="badge badge-success "><i class="feather-star"></i>' + rating + ' (' + reviewsCount + ')</span></div>';
                html = html + '</div><div class="list-card-image">';
                if (val.photo) {
                    photo = val.photo;
                } else {
                    photo = placeholderImageSrc;
                }
                html = html + '<a href="' + view_vendor_details + '"><img alt="#" src="' + photo + '" onerror="this.onerror=null;this.src=\'' + placeholderImageSrc + '\'"  class="img-fluid item-img w-100"></a></div>';
                html = html + '</div>';
                html = html + '</div></div></div>';
            });
            html = html + '</div>';
            return html;
        }

        async function buildHTMLPopularItem(popularItemsnapshot) {
            var html = '';
            var alldata = [];
            var groupedData = {};
            popularItemsnapshot.docs.forEach(async (listval) => {
                var datas = listval.data();
                datas.id = listval.id;
                if (!groupedData[datas.vendorID]) {
                    groupedData[datas.vendorID] = [];
                }
                groupedData[datas.vendorID].push(datas);


            });
            await Promise.all(Object.keys(groupedData).map(async (vendorID) => {
                let products = groupedData[vendorID];
                inValidProductIds = await getUserItemLimit(vendorID);
                products = products.filter(product => !inValidProductIds.includes(product.id));
                alldata = alldata.concat(products);
            }));
            html = html + '<div class="row">';
            await Promise.all(alldata.map(async (listval) => {

                var val = listval;

                var vendor_id_single = val.id;
                var view_vendor_details = "{{ route('productdetail', ':id') }}";
                view_vendor_details = view_vendor_details.replace(':id', vendor_id_single);
                var rating = 0;
                var reviewsCount = 0;
                if (val.hasOwnProperty('reviewsSum') && val.reviewsSum != 0 && val.hasOwnProperty('reviewsCount') && val.reviewsCount != 0) {
                    rating = (val.reviewsSum / val.reviewsCount);
                    rating = Math.round(rating * 10) / 10;
                    reviewsCount = val.reviewsCount;
                }
                getMinDiscount(val.vendorID);

                html = html + '<div class="col-md-3 pro-list"><div class="list-card position-relative"><div class="list-card-image">';
                if (val.photo) {
                    photo = val.photo;
                } else {
                    photo = placeholderImageSrc;
                }
                html = html + '<a href="' + view_vendor_details + '"><img alt="#" src="' + photo + '" onerror="this.onerror=null;this.src=\'' + placeholderImageSrc + '\'" class="img-fluid item-img w-100"></a></div><div class="py-2 position-relative"><div class="list-card-body"><h6 class="mb-1 popul-title"><a href="' + view_vendor_details + '" class="text-black">' + val.name + '</a></h6>';
                var popularItemCategorytitle = popularItemCategory(val.categoryID, val.id);
                html = html + '<h6 class="text-gray mb-1 cat-title" id="popular_food_category_' + val.categoryID + '_' + val.id + '"></h6>';

                let final_price = priceData[val.id];
                if (val.item_attribute && val.item_attribute.variants?.length > 0) {
                    let variantPrices = val.item_attribute.variants.map(v => v.variant_price);
                    let minPrice = Math.min(...variantPrices);
                    let maxPrice = Math.max(...variantPrices);
                    let or_price = minPrice !== maxPrice ?
                        `${getProductFormattedPrice(final_price.min)} - ${getProductFormattedPrice(final_price.max)}` :
                        getProductFormattedPrice(final_price.max);
                    html += `<h6 class="text-gray mb-1 pro-price">${or_price}</h6>`;
                } else if (val.hasOwnProperty('disPrice') && val.disPrice != '' && val.disPrice != '0') {
                    var or_price = getProductFormattedPrice(parseFloat(final_price.price));
                    var dis_price = getProductFormattedPrice(parseFloat(final_price.dis_price));
                    html = html + '<h6 class="text-gray mb-1 price">' + dis_price + '  <s>' + or_price + '</s></h6>';
                } else {
                    var or_price = getProductFormattedPrice(parseFloat(final_price.price));
                    html = html + '<h6 class="text-gray mb-1 price">' + or_price + '</h6>';
                }
                html = html + '<div class="star position-relative mt-3"><span class="badge badge-success"><i class="feather-star"></i>' + rating + ' (' + reviewsCount + ')</span></div>';
                html = html + '</div>';
                html = html + '</div></div></div>';

            }));
            html = html + '</div>';
            return html;
        }

        async function popularItemCategory(categoryId, foodId) {
            var popularItemCategory = '';
            await database.collection('vendor_categories').where("id", "==", categoryId).get().then(async function(categorySnapshots) {
                if (categorySnapshots.docs[0]) {
                    var categoryData = categorySnapshots.docs[0].data();
                    popularItemCategory = categoryData.title;
                    jQuery("#popular_food_category_" + categoryId + "_" + foodId).text(popularItemCategory);
                }
            });
            return popularItemCategory;
        }

        async function getVendorName(vendorId) {
            var vendorName = '';
            await database.collection('vendors').where("id", "==", vendorId).get().then(async function(categorySnapshots) {
                if (categorySnapshots.docs[0]) {
                    var categoryData = categorySnapshots.docs[0].data();
                    vendorName = categoryData.title;
                    jQuery(".vendor_title_" + vendorId).text(vendorName);
                   
                }
            });
            return vendorName;
        }

        async function catHaveProducts(categoryId) {
            var response = database.collection('vendor_products').where("categoryID", "==", categoryId).get().then(function(CatProducts) {
                if (CatProducts.docs.length > 0) {
                    return true;
                } else {
                    return false;
                }
            });
            return response;
        }

        function slickcatCarousel() {
            $('#top_banner').slick({
                slidesToShow: 1,
                arrows: true
            });
            $('#middle_banner').slick({
                slidesToShow: 3,
                arrows: true
            });
        }

        async function getStories() {
            var storyDatas = [];
            var alldata = [];
            var storySnapshots = await database.collection('story').where('sectionID', '==', section_id).get();
            storySnapshots.docs.forEach((story) => {
                var datas = story.data();
                alldata.push(datas);
            });
            for (data of alldata) {
                var vendorDataRes = await database.collection('vendors').doc(data.vendorID).get();
                var vendorData = vendorDataRes.data();
                if (vendorData != undefined) {
                    var vendorRating = '';
                    if (vendorData.hasOwnProperty('reviewsSum') && vendorData.reviewsSum != 0 && vendorData.hasOwnProperty('reviewsCount') && vendorData.reviewsCount != 0) {
                        rating = (vendorData.reviewsSum / vendorData.reviewsCount);
                        rating = Math.round(rating * 10) / 10;
                        reviewsCount = vendorData.reviewsCount;
                        vendorRating = vendorRating + '<div class="star position-relative ml-1 mt-3"><span class="badge badge-success "><i class="feather-star"></i>' + rating + ' (' + reviewsCount + ')</span></div>';
                    }
                    var vendorLink = "{{ route('vendor', ':id') }}";
                    vendorLink = vendorLink.replace(':id', vendorData.id);
                    var itemsObject = [];
                    data.videoUrl.forEach((video) => {
                        var itemObject = {
                            id: vendorData.id,
                            type: "video",
                            length: 5,
                            src: video,
                            link: vendorLink,
                            linkText: vendorData.title,
                            time: new Date(data.createdAt.toDate()).getTime() / 1000,
                            seen: false
                        };
                        itemsObject.push(itemObject);
                    });
                    var storyObject = {
                        id: vendorData.id,
                        photo: data.videoThumbnail,
                        name: vendorData.title,
                        link: vendorLink,
                        seen: false,
                        items: itemsObject
                    }
                    storyDatas.push(storyObject);
                }
            }
            var stories = new Zuck('stories', {
                backNative: true,
                previousTap: true,
                skin: 'snapssenger',
                autoFullScreen: true,
                avatars: true,
                list: false,
                cubeEffect: true,
                localStorage: true,
                stories: storyDatas,
                language: {
                    unmute: '<i class="fa fa-volume-up"></i>',
                }
            });
            $('#stories').slick({
                slidesToShow: 5,
                dots: false,
                arrows: true,
                responsive: [{
                        breakpoint: 991,
                        settings: {
                            slidesToShow: 4,
                        }
                    },
                    {
                        breakpoint: 767,
                        settings: {
                            slidesToShow: 3,
                        }
                    },
                    {
                        breakpoint: 650,
                        settings: {
                            slidesToShow: 2,
                        }
                    }
                ]
            });
        }
    </script>
    @include('layouts.nav')
