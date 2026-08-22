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
                <input type="text" class="shadow-none border-0 form-control" placeholder="{{trans('lang.search_for_cab')}}">
            </div>
            <div class="text-white col-md-3 col-sm-3">
                <div class="title d-flex align-items-center">
                    <a class="text-white font-weight-bold ml-auto" data-toggle="modal" data-target="#exampleModal"
                       href="#">{{trans('lang.filter')}}</a>
                </div>
            </div>
        </div>
    </div>
    <div class="ecommerce-banner multivendor-banner section-content">
        <div class="ecommerce-inner">
            <div id="top_banner"></div>
        </div>
    </div>
    <div class="cabLandingPage">
        <section class="cab-landing-chrome container py-5">
            <div class="sction-title text-center mb-4">
                <h2>{{ trans('lang.cab_landing_title') }}</h2>
                <p class="mb-2">{{ trans('lang.wherever_you_going') }} {{ trans('lang.we_will_bring_you_uickly') }}</p>
                <p class="cab-landing-default mb-4">{{ trans('lang.cab_service_has_provided_car') }}</p>
                <a href="#cab-landing-cms" class="btn btn-primary">{{ trans('lang.cab_landing_cta') }}</a>
            </div>
        </section>
        <div id="cab-landing-cms" class="cab-landing-cms"></div>
    </div>
</div>
@include('layouts.footer')
<script src="https://unpkg.com/geofirestore@5.2.0/dist/geofirestore.js"></script>
<script src="https://cdn.firebase.com/libs/geofire/5.0.1/geofire.min.js"></script>
<script type="text/javascript" src="{{asset('js/slick/slick.min.js')}}"></script>
<script type="text/javascript">
    var database = firebase.firestore();
    var cabLandingPageRef = database.collection('sections').where('id', '==', section_id);
    var bannerref = database.collection('banner_items').where('sectionId', '==', section_id).where("is_publish", "==", true).orderBy('set_order', 'asc');
    var placeholderImageRef = database.collection('settings').doc('placeHolderImage');
    var placeholderImageSrc = '';

    function looksEnglishOnly(html) {
        if (!html) return true;
        var text = String(html).replace(/<[^>]+>/g, ' ');
        var hasPt = /[áàâãéêíóôõúç]|corrida|pedir|cidade|brasil|táxi|taxi/i.test(text);
        var hasEnCta = /book now|get started|download the app|ride with us|call now|our cab service|learn more/i.test(text);
        return hasEnCta && !hasPt;
    }

    function bannerHref(banner) {
        var redirect_id = 'javascript:void(0)';
        if (!banner.redirect_type) return redirect_id;
        if (banner.redirect_type == "provider") {
            return "{{ route('ondemand-providerdetail', ':id') }}".replace(':id', banner.redirect_id);
        }
        if (banner.redirect_type == "service") {
            return "{{ route('service', ':id') }}".replace(':id', banner.redirect_id);
        }
        if (banner.redirect_type == "store") {
            return "{{ route('vendor', ':id') }}".replace(':id', banner.redirect_id);
        }
        if (banner.redirect_type == "product") {
            return "{{ route('productdetail', ':id') }}".replace(':id', banner.redirect_id);
        }
        if (banner.redirect_type == "external_link") {
            return banner.redirect_id;
        }
        return redirect_id;
    }

    placeholderImageRef.get().then(function (placeholderImageSnapshots) {
        var placeHolderImageData = placeholderImageSnapshots.data();
        placeholderImageSrc = placeHolderImageData && placeHolderImageData.image ? placeHolderImageData.image : '';
    });

    bannerref.get().then(async function (banners) {
        var position1_banners = [];
        banners.docs.forEach(function (banner) {
            var bannerData = banner.data();
            if (bannerData.position != 'top') return;
            position1_banners.push({
                photo: bannerData.web_banner,
                redirect_type: bannerData.redirect_type || '',
                redirect_id: bannerData.redirect_id || ''
            });
        });
        if (position1_banners.length > 0) {
            var html = '';
            for (var banner of position1_banners) {
                var photo = banner.photo || placeholderImageSrc;
                html += '<div class="banner-item"><div class="banner-img">';
                html += '<a href="' + bannerHref(banner) + '"><img src="' + photo + '" onerror="this.onerror=null;this.src=\'' + placeholderImageSrc + '\'"></a>';
                html += '</div></div>';
            }
            $("#top_banner").html(html);
            if ($("#top_banner").hasClass('slick-initialized')) {
                $('#top_banner').slick('unslick');
            }
            $('#top_banner').slick({
                slidesToShow: 1,
                dots: true,
                arrows: true,
                autoplay: true,
                autoplaySpeed: 3000
            });
        }
    });

    jQuery("#overlay").show();
    cabLandingPageRef.get().then(async function (snapshots) {
        if (!snapshots.docs.length) {
            jQuery("#overlay").hide();
            return;
        }
        var cabLandingPageData = snapshots.docs[0].data();
        var cms = cabLandingPageData.cab_service_template || '';
        if (cms && !looksEnglishOnly(cms)) {
            $('.cab-landing-cms').html(cms);
            $('.cab-landing-default').hide();
        } else if (cms) {
            $('.cab-landing-cms').html(cms);
        }
        jQuery("#overlay").hide();
    }).catch(function () {
        jQuery("#overlay").hide();
    });
</script>
@include('layouts.nav')
