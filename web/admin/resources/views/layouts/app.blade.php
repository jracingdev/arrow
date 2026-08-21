<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" <?php if (str_replace('_', '-', app()->getLocale()) == 'ar' || @$_COOKIE['is_rtl'] == 'true') { ?> dir="rtl" <?php } ?>>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo-light-icon.png') }}">
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <!-- Styles -->
    <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <?php if (str_replace('_', '-', app()->getLocale()) == 'ar' || @$_COOKIE['is_rtl'] == 'true') { ?>
        <link href="{{asset('assets/plugins/bootstrap/css/bootstrap-rtl.min.css')}}" rel="stylesheet">
    <?php } ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    
    <?php if (str_replace('_', '-', app()->getLocale()) == 'ar' || @$_COOKIE['is_rtl'] == 'true') { ?>
        <link href="{{asset('css/style_rtl.css')}}" rel="stylesheet">
    <?php } ?>
    
    <link href="{{ asset('css/icons/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/toast-master/css/jquery.toast.css')}}" rel="stylesheet">
    <link href="{{ asset('css/colors/blue.css') }}" rel="stylesheet">
    <link href="{{ asset('css/chosen.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-tagsinput.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/summernote/summernote-bs4.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <!-- Datatable css -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/dist/css/select2.min.css')}}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-draw/dist/leaflet.draw.css" />
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="{{ asset('css/toastr.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <script>
        function setCookie(cname, cvalue, exdays) {
            const d = new Date();
            d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
            let expires = "expires=" + d.toUTCString();
            document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        }

        function getCookie(cname) {
            let name = cname + "=";
            let ca = document.cookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        }
        let admin_panel_color = getCookie("admin_panel_color");
        if (admin_panel_color) {
            document.documentElement.style.setProperty('--admin-panel-color', admin_panel_color);
        }
    </script>

    <!-- @yield('style') -->
     
     <style>
        :root {
            --admin-panel-color: "#000000";
        }
    </style>

    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>

<body>
    <div id="app" class="fix-header fix-sidebar card-no-border">
        <div id="main-wrapper">
            <div id="data-table_processing" class="page-overlay" style="display:none;">
                <div class="overlay-text">
                    <img src="{{asset('images/spinner.gif')}}">
                </div>
            </div>
            <header class="topbar non-printable">
                <nav class="navbar top-navbar navbar-expand-md navbar-light">
                    @include('layouts.header')
                </nav>
            </header>
            <aside class="left-sidebar non-printable">
                <div class="scroll-sidebar">
                    @include('layouts.menu')
                </div>
            </aside>
        </div>
        <main class="py-4">
            @yield('content')
        </main>
    </div>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-draw/dist/leaflet.draw.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-editable/0.7.3/leaflet.editable.min.js"></script>
    <script src="https://unpkg.com/leaflet-draw@0.4.14/dist/leaflet.draw-src.js"></script>
    <script src="https://unpkg.com/leaflet-geojson-layer/src/leaflet.geojson.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="{{ asset('assets/plugins/bootstrap/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/jquery.slimscroll.js') }}"></script>
    <script src="{{ asset('js/waves.js') }}"></script>
    <script src="{{ asset('js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('assets/plugins/sticky-kit-master/dist/sticky-kit.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/sparkline/jquery.sparkline.min.js')}}"></script>
    <script src="{{ asset('js/custom.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/summernote/summernote-bs4.js')}}"></script>
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-firestore-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-storage-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-auth-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-database-compat.js"></script>
    <script src="https://unpkg.com/geofirestore@5.2.0/dist/geofirestore.js"></script>
    <script src="https://cdn.firebase.com/libs/geofire/5.0.1/geofire.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/chosen.jquery.js') }}"></script>
    <script src="{{ asset('js/jquery.resizeImg.js') }}"></script>
    <script src="{{ asset('js/bootstrap-tagsinput.js') }}"></script>
    <script src="{{ asset('js/crypto-js.js') }}"></script>
    <script src="{{ asset('js/jquery.cookie.js') }}"></script>
    @include('partials.firebase-init')
    <script src="{{ asset('js/firestore-safe.js') }}"></script>
    <script src="{{ asset('js/jquery.validate.js') }}"></script>
    <script src="{{ asset('assets/plugins/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('js/jquery.masking.js') }}"></script>
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    
    <!-- Datatable script -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.1/xlsx.full.min.js"></script>
    <script type="text/javascript"src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.24/jspdf.plugin.autotable.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="{{ asset('js/arrow-datetime.js') }}"></script>
    <script src="{{ asset('js/arrow-i18n.js') }}?v={{ @filemtime(public_path('js/arrow-i18n.js')) ?: time() }}"></script>
    <script type="text/javascript">
        window.ArrowI18n = window.ArrowI18n || {};
        window.ArrowI18n.searchPlaceholder = @json(trans('lang.search_here'));
        window.ArrowI18n.perHourSuffix = @json(trans('lang.per_hour_suffix'));
        window.ArrowI18n.viewDetails = @json(trans('lang.view_details'));
        (function (i18n) {
            var map = {
                'rental-service': 'Aluguel',
                'Rental Service': 'Aluguel',
                'delivery-service': 'Entrega marketplace (várias lojas)',
                'Multivendor Delivery Service': 'Entrega marketplace (várias lojas)',
                'ondemand-service': 'Serviço sob demanda',
                'On Demand Service': 'Serviço sob demanda',
                'ecommerce-service': 'E-commerce',
                'Ecommerce Service': 'E-commerce',
                'parcel_delivery': 'Encomendas',
                'parcel-service': 'Encomendas',
                'parcel-delivery': 'Encomendas',
                'Parcel Delivery Service': 'Encomendas',
                'Parcel Service': 'Encomendas',
                'cab-service': 'Corridas',
                'Cab Service': 'Corridas'
            };
            i18n.serviceTypeMap = Object.assign({}, map, i18n.serviceTypeMap || {});
            if (typeof i18n.serviceType !== 'function') {
                i18n.serviceType = function (s) {
                    if (s == null || s === '') return '';
                    var key = String(s).trim();
                    return (this.serviceTypeMap && this.serviceTypeMap[key]) || key;
                };
            }
            if (typeof i18n.serviceTypeLabel !== 'function') {
                i18n.serviceTypeLabel = function (flag, name) {
                    var m = this.serviceTypeMap || map;
                    if (flag && m[flag]) return m[flag];
                    if (name && m[name]) return m[name];
                    return name || flag || '';
                };
            }
        })(window.ArrowI18n);
    </script>
    <script type="text/javascript">
        window.ArrowChartMonths = [
            @json(trans('lang.month_short_jan')),
            @json(trans('lang.month_short_feb')),
            @json(trans('lang.month_short_mar')),
            @json(trans('lang.month_short_apr')),
            @json(trans('lang.month_short_may')),
            @json(trans('lang.month_short_jun')),
            @json(trans('lang.month_short_jul')),
            @json(trans('lang.month_short_aug')),
            @json(trans('lang.month_short_sep')),
            @json(trans('lang.month_short_oct')),
            @json(trans('lang.month_short_nov')),
            @json(trans('lang.month_short_dec'))
        ];
    </script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <script type="text/javascript">
        jQuery(window).scroll(function () {
            var scroll = jQuery(window).scrollTop();
            if (scroll <= 60) {
                jQuery("body").removeClass("sticky");
            } else {
                jQuery("body").addClass("sticky");
            }
        });
        const datatableLang = {
            "decimal":        "",
            "emptyTable":     "{{ trans('lang.no_record_found') }}",
            "info":           "{{ trans('lang.datatable_info') }}", 
            "infoEmpty":      "{{ trans('lang.datatable_info_empty') }}", 
            "infoFiltered":   "{{ trans('lang.datatable_info_filtered') }}", 
            "lengthMenu":     "{{ trans('lang.datatable_length_menu') }}",
            "loadingRecords": "{{ trans('lang.loading') }}",
            "processing":     "{{ trans('lang.processing') }}",
            "search":         "{{ trans('lang.search') }}",
            "zeroRecords":    "{{ trans('lang.no_record_found') }}",
            "paginate": {
                "first":      "{{ trans('lang.first') }}",
                "last":       "{{ trans('lang.last') }}",
                "next":       "{{ trans('lang.next') }}",
                "previous":   "{{ trans('lang.previous') }}"
            },
            "aria": {
                "sortAscending":  ": {{ trans('lang.sort_asc') }}",
                "sortDescending": ": {{ trans('lang.sort_desc') }}"
            }
        };
    </script>
    <script type="text/javascript">

        var languages_list_main = [];
        var database = firebase.firestore();
        var geoFirestore = new GeoFirestore(database);
        var createdAtman = firebase.firestore.Timestamp.fromDate(new Date());
        var createdAt = { _nanoseconds: createdAtman.nanoseconds, _seconds: createdAtman.seconds };
        var mapType = 'ONLINE';

        var sosInitialized = false; 
        database.collection('SOS').onSnapshot((snapshot) => {
            if (!sosInitialized) {               
                sosInitialized = true;
                return;
            }

            snapshot.docChanges().forEach((change) => {
                if (change.type === "added") {
                    var data = change.doc.data();
                    Swal.fire({
                        icon: 'warning',
                        title: @json(trans('lang.sos_alert')),
                        html: @json(trans('lang.new_sos_initiated')),
                        confirmButtonText: @json(trans('lang.view_details')),
                        confirmButtonColor: '#d33',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            var sosData = change.doc.data() || {};
                            if (sosData.serviceType === 'ondemand-service') {
                                window.location.href = '{{ url('/ondemand-reports/edit') }}/' + change.doc.id;
                            } else {
                                window.location.href = '/sos/edit/' + change.doc.id;
                            }
                        }
                    });
                }
            });
        });
        
        var ref = database.collection('settings').doc("globalSettings");
        ref.get().then(async function (snapshots) {
            var globalSettings = snapshots.exists ? (snapshots.data() || {}) : {};
            if (globalSettings.applicationName) {
                $("#app_name").html(globalSettings.applicationName);
            }
            if (globalSettings.appLogo) {
                $("#logo_web").attr('src', globalSettings.appLogo);
            }
            if (globalSettings.admin_panel_color) {
                document.documentElement.style.setProperty('--admin-panel-color', globalSettings.admin_panel_color);
            }
        }).catch(function (err) {
            console.error('globalSettings load failed:', err);
        });
        
        var placeholderImage = '';
        var placeholder = database.collection('settings').doc('placeHolderImage');
        placeholder.get().then(async function (snapshotsimage) {
            var placeholderImageData = snapshotsimage.exists ? snapshotsimage.data() : null;
            if (placeholderImageData && placeholderImageData.image) {
                placeholderImage = placeholderImageData.image;
            }
        }).catch(function () {});
        
        $(document).ready(async function () {
            getServiceSections();
            $(document).on('click', '.service-list-box', function() {
                let sectionUrl = $(this).data('section-url');
                let sectionId = $(this).data('section-id');
                let sectionType = $(this).data('section-type');
                if(sectionId && sectionType){
                    setCookie('section_id', sectionId, 1);
                    setCookie('service_type', sectionType, 1);
                }
                window.location.href = sectionUrl;
                /*window.location.reload();*/
            });
        });
        
        var langcount = 0;
        var languages_list = database.collection('settings').doc('languages');
        languages_list.get().then(async function (snapshotslang) {
            snapshotslang = snapshotslang.data();
            if (snapshotslang != undefined) {
                snapshotslang = snapshotslang.list;
                languages_list_main = snapshotslang;
                snapshotslang.forEach((data) => {
                    if (data.isActive == true) {
                        langcount++;
                        $('#language_dropdown').append($("<option></option>").attr("value", data.slug).text(data.title));
                    }
                });
                if (langcount > 1) {
                    $("#language_dropdown_box").css('visibility', 'visible');
                }
                <?php if (session()->get('locale')) { ?>
                    $("#language_dropdown").val("<?php    echo session()->get('locale'); ?>");
                <?php } ?>
            }
        });

        var url = "{{ route('changeLang') }}";
        $(".changeLang").change(function () {
            var slug = $(this).val();
            languages_list_main.forEach((data) => {
                if (slug == data.slug) {
                    if (data.is_rtl == undefined) {
                        setCookie('is_rtl', 'false', 365);
                    } else {
                        setCookie('is_rtl', data.is_rtl.toString(), 365);
                    }
                    window.location.href = url + "?lang=" + slug;
                }
            });
        });

        var version = database.collection('settings').doc("Version");
        version.get().then(async function (snapshots) {
            var version_data = snapshots.data();
            if (version_data == undefined) {
                database.collection('settings').doc('Version').set({});
            }
            try {
                $('.web_version').html("V:" + version_data.web_version);
            } catch (error) {
            }
        });
        
        async function sendEmail(url, subject, message, recipients) {
            var checkFlag = false;
            await $.ajax({
                type: 'POST',
                data: {
                    subject: subject,
                    message: btoa(message),
                    recipients: recipients
                },
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    checkFlag = true;
                },
                error: function (xhr, status, error) {
                    checkFlag = true;
                }
            });
            return checkFlag;
        }

        database.collection('settings').doc('DriverNearBy').get().then(async function (snapshots) {
            var data = snapshots.data();
            if (data && data.selectedMapType && data.selectedMapType == "osm") {
                mapType = "OFFLINE"
            }
        });
        
        async function getServiceSections() {
            const sectionsContainer = document.getElementById('sections_header');
            if (!sectionsContainer) {
                return;
            }
            try {
                let snapshot;
                try {
                    snapshot = await database.collection('sections')
                        .where('isActive', '==', true)
                        .orderBy('order')
                        .get();
                } catch (indexErr) {
                    // Índice composto ausente — fallback sem orderBy (warn só 1x por sessão)
                    if (!window.__sectionsOrderByWarned) {
                        window.__sectionsOrderByWarned = true;
                        console.warn('sections orderBy falhou (índice ausente?). Usando fallback. Deploy: firebase deploy --only firestore:indexes');
                    }
                    snapshot = await database.collection('sections')
                        .where('isActive', '==', true)
                        .get();
                }
                sectionsContainer.innerHTML = await buildServiceSectionsHTML(snapshot);
            } catch (err) {
                console.error('Erro ao carregar seções:', err);
                const addSectionRoute = "{{ route('section.create') }}";
                const sectionListRoute = "{{ route('section') }}";
                sectionsContainer.innerHTML = `
                    <div class="dropdown-service-list p-3">
                        <p class="text-danger mb-2">{{ trans('lang.no_section_found') }}</p>
                        <p class="mb-3"><a href="${sectionListRoute}">{{ trans('lang.section') }}</a></p>
                        <a class="btn btn-primary btn-sm" href="${addSectionRoute}">{{ trans('lang.add_more') }}</a>
                    </div>`;
            }
        }
        
        async function buildServiceSectionsHTML(snapshot) {
            let html = '';
            var addSectionRoute = "{{ route('section.create') }}";
            var sectionListRoute = "{{ route('section') }}";
            var idSecActive = getCookie('section_id') || '';
            var typeSecActive = getCookie('service_type') || '';
            const docs = (snapshot && snapshot.docs) ? snapshot.docs.slice() : [];
            docs.sort(function (a, b) {
                const ao = Number((a.data() || {}).order);
                const bo = Number((b.data() || {}).order);
                return (Number.isFinite(ao) ? ao : 9999) - (Number.isFinite(bo) ? bo : 9999);
            });
            if (!docs.length) {
                html += `
                <div class="col-md-12 mb-3">
                    <p class="mb-2">{{ trans('lang.no_section_found') }}</p>
                    <p class="mb-0"><a href="${sectionListRoute}">{{ trans('lang.section') }}</a> — {{ trans('lang.expand_by_adding_new_modules_as_your_business_grows') }}</p>
                </div>`;
            }
            docs.forEach(doc => {
                var data = doc.data() || {};
                var sectionName = data.name || 'Unnamed Section';
                var typeLabel = '';
                try {
                    if (window.ArrowI18n && typeof ArrowI18n.serviceTypeLabel === 'function') {
                        typeLabel = ArrowI18n.serviceTypeLabel(data.serviceTypeFlag, data.serviceType) || '';
                    } else {
                        typeLabel = data.serviceType || data.serviceTypeFlag || '';
                    }
                } catch (labelErr) {
                    typeLabel = data.serviceType || data.serviceTypeFlag || '';
                }
                var sectionDescription = data.description || typeLabel || '';
                var sectionImage = data.sectionImage || placeholderImage;
                var sectionId = data.id || doc.id;
                var sectionType = data.serviceTypeFlag || '';
                var sectionRoute = `{{ route('dashboard') }}/${sectionId}/${sectionType}`;
                var isSelected = (sectionId === idSecActive && sectionType === typeSecActive);
                var selectedClass = isSelected ? 'selected-section' : '';
                if (isSelected) {
                    $('#activeSectionLogo').attr('src', sectionImage || placeholderImage);
                    $('#activeSectionName').text(sectionName);
                }
                html += `
                <div class="col-md-4">
                    <div class="service-list-box ${selectedClass}" data-section-url="${sectionRoute}" data-section-id="${sectionId}" data-section-type="${sectionType}">
                        <img src="${sectionImage}" onerror="this.onerror=null;this.src='${placeholderImage}'">
                        <h3>${sectionName}</h3>
                        <p>${sectionDescription}</p>
                    </div>
                </div>
                `;
            });
            html += `
                <div class="col-md-12">
                    <div class="service-list-box" data-section-url="${addSectionRoute}" data-section-id="" data-section-type="">
                        <img src="{{ asset('images/add_more.png') }}">
                        <h3>{{trans('lang.add_more')}}</h3>
                        <p>{{trans('lang.expand_by_adding_new_modules_as_your_business_grows')}}</p>
                    </div>
                </div>
                `;
            html = `<div class="dropdown-service-list"><div class="row">${html}</div></div>`;
            return html;
        }

        async function loadGoogleMapsScript() {
            var googleMapKeySnapshotsHeader = await database.collection('settings').doc("googleMapKey").get();
            var placeholderImageHeaderData = googleMapKeySnapshotsHeader.data();
            googleMapKey = placeholderImageHeaderData.key;
            const script = document.createElement('script');
            if (mapType == "OFFLINE") {
                script.src = "https://unpkg.com/leaflet@1.7.1/dist/leaflet.js";
                script.src = "https://unpkg.com/leaflet-draw/dist/leaflet.draw.js";
                script.src = "https://cdnjs.cloudflare.com/ajax/libs/leaflet-editable/0.7.3/leaflet.editable.min.js";
                script.src = "https://unpkg.com/leaflet-draw@0.4.14/dist/leaflet.draw-src.js";
                script.src = "https://unpkg.com/leaflet-ajax/dist/leaflet.ajax.min.js";
                script.src = "https://unpkg.com/leaflet-geojson-layer/src/leaflet.geojson.js";
                script.src = "https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js";
            } else {
                script.src = "https://maps.googleapis.com/maps/api/js?key=" + googleMapKey + "&libraries=places,drawing";
            }
            script.onload = function () {
                navigator.geolocation.getCurrentPosition(GeolocationSuccessCallback, GeolocationErrorCallback);
                if (typeof window['InitializeGodsEyeMap'] === 'function') {
                    InitializeGodsEyeMap();
                }
            };
            document.head.appendChild(script);
        }

        const GeolocationSuccessCallback = (position) => {
            if (position.coords != undefined) {
                default_latitude = position.coords.latitude
                default_longitude = position.coords.longitude
                setCookie('default_latitude', default_latitude, 365);
                setCookie('default_longitude', default_longitude, 365);
            }
        };
        const GeolocationErrorCallback = (error) => {
            console.log('Error: You denied for your default Geolocation', error.message);
            setCookie('default_latitude', '23.022505', 365);
            setCookie('default_longitude', '72.571365', 365);
        };

        loadGoogleMapsScript();
        
        database.collection('settings').doc("notification_setting").get().then(async function (snapshots) {
            var data = snapshots.data();
            serviceJson = data.serviceJson;
            if (serviceJson != '' && serviceJson != null) {
                $.ajax({
                    type: 'POST',
                    data: {
                        serviceJson: btoa(serviceJson),
                    },
                    url: "{{ route('store-firebase-service') }}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        checkFlag = true;
                    }
                });
            }
        });

        //On delete item delete image also from bucket general code
        const deleteDocumentWithImage = async (collection, id, singleImageField, arrayImageField, profileImageField, carProofPictureURL, driverProofPictureURL) => {
            // Reference to the Firestore document
            const docRef = database.collection(collection).doc(id);
            try {
                const doc = await docRef.get();
                if (!doc.exists) {
                    console.log("No document found for deletion");
                    return;
                }
                const data = doc.data();
                // Handle single image deletion
                // Deleting single image field
                if (singleImageField) {
                    if (Array.isArray(singleImageField)) {
                        for (const field of singleImageField) {
                            const imageUrl = data[field];
                            if (imageUrl) await deleteImageFromBucket(imageUrl);
                        }
                    } else {
                        const imageUrl = data[singleImageField];
                        if (imageUrl) await deleteImageFromBucket(imageUrl);
                    }
                }
                // Deleting array image field
                if (arrayImageField) {
                    if (Array.isArray(arrayImageField)) {
                        for (const field of arrayImageField) {
                            const arrayImages = data[field];
                            if (arrayImages && Array.isArray(arrayImages)) {
                                for (const imageUrl of arrayImages) {
                                    if (imageUrl) await deleteImageFromBucket(imageUrl);
                                }
                            }
                        }
                    } else {
                        const arrayImages = data[arrayImageField];
                        if (arrayImages && Array.isArray(arrayImages)) {
                            for (const imageUrl of arrayImages) {
                                if (imageUrl) await deleteImageFromBucket(imageUrl);
                            }
                        }
                    }
                }
                // Handle variant images deletion
                const item_attribute = data.item_attribute || {};  // Access item_attribute
                const variants = item_attribute.variants || [];    // Access variants array inside item_attribute
                if (variants.length > 0) {
                    for (let i = 0; i < variants.length; i++) {
                        const variantImageUrl = variants[i].variant_image;
                        if (variantImageUrl) {
                            await deleteImageFromBucket(variantImageUrl);
                        }
                    }
                }
                // Handle profile_file_name image deletion
                const profile_file_name = data[profileImageField] || '';  // profile image field
                if (profile_file_name) {
                    await deleteImageFromBucket(profile_file_name);
                }
                // Handle carproof_file_name image deletion
                const carproof_file_name = data[carProofPictureURL] || '';  // carproof image field
                if (carproof_file_name) {
                    await deleteImageFromBucket(carproof_file_name);
                }
                // Handle driverproof_file_name image deletion
                const driverproof_file_name = data[driverProofPictureURL] || '';  // driverproof image field
                if (driverproof_file_name) {
                    await deleteImageFromBucket(driverproof_file_name);
                }
                // Optionally delete the Firestore document after image deletion
                await docRef.delete();
                console.log("Document and images deleted successfully.");
            } catch (error) {
                console.error("Error deleting document and images:", error);
            }
        };

        const deleteImageFromBucket = async (imageUrl) => {
            try {
                const storageRef = firebase.storage().ref();
                // Check if the imageUrl is a full URL or just a child path
                let oldImageUrlRef;
                if (imageUrl.includes('https://')) {
                    // Full URL
                    oldImageUrlRef = storageRef.storage.refFromURL(imageUrl);
                } else {
                    // Child path, use ref instead of refFromURL
                    oldImageUrlRef = storageRef.storage.ref(imageUrl);
                }
                var envBucket = "<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";
                var imageBucket = oldImageUrlRef.bucket;
                // Check if the bucket name matches
                if (imageBucket === envBucket) {
                    // Delete the image
                    await oldImageUrlRef.delete();
                    console.log("Image deleted successfully.");
                }
            } catch (error) {
            }
        };

        function exportData(dt, format, config) {
            const {
                columns,
                fileName = 'Export',
            } = config;
            const filteredRecords = dt.ajax.json().filteredData;
            const fieldTypes = {};
            const dataMapper = (record) => {
                return columns.map((col) => {
                    const value = record[col.key];
                    if (!fieldTypes[col.key]) {
                        if (value === true || value === false) {
                            fieldTypes[col.key] = 'boolean';
                        } else if (value && typeof value === 'object' && value.seconds) {
                            fieldTypes[col.key] = 'date';
                        } else if (typeof value === 'number') {
                            fieldTypes[col.key] = 'number';
                        } else if (typeof value === 'string') {
                            fieldTypes[col.key] = 'string';
                        } else {
                            fieldTypes[col.key] = 'string';
                        }
                    }
                    switch (fieldTypes[col.key]) {
                        case 'boolean':
                            return value ? 'Yes' : 'No';
                       /*  case 'date':
                            return value ? new Date(value.seconds * 1000).toLocaleString() : '-'; */
                        case 'date':
                            return value?.toDate ? value.toDate().toLocaleString() :
                                (value.seconds ? new Date(value.seconds * 1000).toLocaleString() : '-');
                        case 'number':
                            return typeof value === 'number' ? value : 0;
                        case 'string':
                        default:
                            return value || '-';
                    }
                });
            };
            const tableData = filteredRecords.map(dataMapper);
            const data = [columns.map(col => col.header), ...tableData];
            const columnWidths = columns.map((_, colIndex) =>
                Math.max(...data.map(row => row[colIndex]?.toString().length || 0))
            );
            if (format === 'csv') {
                const csv = data.map(row => row.map(cell => {
                    if (typeof cell === 'string' && (cell.includes(',') || cell.includes('\n') || cell.includes('"'))) {
                        return `"${cell.replace(/"/g, '""')}"`;
                    }
                    return cell;
                }).join(',')).join('\n');
                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                saveAs(blob, `${fileName}.csv`);
            } else if (format === 'excel') {
                const ws = XLSX.utils.aoa_to_sheet(data, { cellDates: true });
                ws['!cols'] = columnWidths.map(width => ({ wch: Math.min(width + 5, 30) }));
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, 'Data');
                XLSX.writeFile(wb, `${fileName}.xlsx`);
            } else if (format === 'pdf') {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF('l', 'mm', 'a4');
                doc.setFontSize(12);
                doc.text(fileName, 14, 16);
                doc.autoTable({
                    head: [columns.map(col => col.header)],
                    body: tableData,
                    startY: 20,
                    theme: 'striped',
                    styles: {
                        cellPadding: 1,
                        fontSize: 8,
                        overflow: 'linebreak',
                    },
                    columnStyles: {
                        0: { cellWidth: 'auto' },
                    },
                    margin: { top: 30, bottom: 30 },
                    pageBreak: 'auto',
                });
                doc.save(`${fileName}.pdf`);
            } else {
                console.error('Unsupported format');
            }
        }

        function showError(msg) {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append(`<p>${msg}</p>`);
            window.scrollTo(0, 0);
            return false;
        }

        function encodeGeohash(latitude, longitude, precision = 10) {

            const BASE32 = "0123456789bcdefghjkmnpqrstuvwxyz";
            let idx = 0;
            let bit = 0;
            let even = true;
            let geohash = "";

            let latMin = -90, latMax = 90;
            let lonMin = -180, lonMax = 180;

            while (geohash.length < precision) {
                if (even) {
                    let mid = (lonMin + lonMax) / 2;
                    if (longitude > mid) {
                        idx = idx * 2 + 1;
                        lonMin = mid;
                    } else {
                        idx = idx * 2;
                        lonMax = mid;
                    }
                } else {
                    let mid = (latMin + latMax) / 2;
                    if (latitude > mid) {
                        idx = idx * 2 + 1;
                        latMin = mid;
                    } else {
                        idx = idx * 2;
                        latMax = mid;
                    }
                }
                even = !even;

                if (++bit == 5) {
                    geohash += BASE32.charAt(idx);
                    bit = 0;
                    idx = 0;
                }
            }

            return geohash;
        }

        function showProcessing() {
            Swal.fire({
                title: window.translations.showProcessingTitle,
                text: window.translations.showProcessingText,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        function showSuccessMessage(message) {
            Swal.fire({
                icon: 'success',
                title: window.translations.successMessageTitle,
                text: message,
                showConfirmButton: false,
                timer: 2000,
            });
        }

        function showErrorMessage(message) {
            Swal.fire({
                icon: 'error',
                title: window.translations.errorMessageTitle,
                text: message,
                showConfirmButton: true
            });
        }

        window.translations = {
            showProcessingTitle:"{{trans('lang.please_wait')}}",
            showProcessingText:"{{trans('lang.processing_your_request')}}",
            errorMessageTitle:"{{trans('lang.error')}}",
            warningMessageTitle:"{{trans('lang.warning')}}",
            successMessageTitle:"{{trans('lang.success')}}",
        };

        async function getUserZoneId(address_lng,address_lat){
            var zone_id = null;
            var zone_list = [];
            var snapshots = await database.collection('zone').where("publish","==",true).get();
            if(snapshots.docs.length > 0){
                snapshots.docs.forEach((snapshot) => {
                    var zone_data = snapshot.data();
                    zone_list.push(zone_data);
                });   
            }
            if(zone_list.length > 0){
                for (i = 0; i < zone_list.length; i++) {
                    var zone = zone_list[i];
                    var vertices_x = [];
                    var vertices_y = [];
                    for (j = 0; j < zone.area.length; j++) {
                        var geopoint = zone.area[j];
                        vertices_x.push(geopoint.longitude);
                        vertices_y.push(geopoint.latitude);
                    }
                    var points_polygon = (vertices_x.length)-1; 
                    if(is_in_polygon(points_polygon, vertices_x, vertices_y, address_lng, address_lat)){
                        zone_id = zone.id;
                        break; 
                    }
                }
            }
            return zone_id;
        }

        function is_in_polygon($points_polygon, $vertices_x, $vertices_y, $longitude_x, $latitude_y){
            $i = $j = $c = $point = 0;
            for ($i = 0, $j = $points_polygon ; $i < $points_polygon; $j = $i++) {
                $point = $i;
                if( $point === $points_polygon )
                    $point = 0;
                if ( (($vertices_y[$point]  >  $latitude_y !== ($vertices_y[$j] > $latitude_y)) && ($longitude_x < ($vertices_x[$j] - $vertices_x[$point]) * ($latitude_y - $vertices_y[$point]) / ($vertices_y[$j] - $vertices_y[$point]) + $vertices_x[$point]) ) )
                    $c = !$c;
            }
            return $c;
        }

        const distanceRadius = (lat1, lon1, lat2, lon2) => {
            if ((lat1 === lat2) && (lon1 === lon2)) {
                return 0;
            }
            else {
                var radlat1 = Math.PI * lat1/180;
                var radlat2 = Math.PI * lat2/180;
                var theta = lon1-lon2;
                var radtheta = Math.PI * theta/180;
                var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
                if (dist > 1) {
                    dist = 1;
                }
                dist = Math.acos(dist);
                dist = dist * 180/Math.PI;
                dist = dist * 60 * 1.1515;
                dist = dist * 1.60934; // Convert to kilometers
                
                return dist;
            }
        }

        function formatCurrency(amount, currency = {}) {
            const symbol = currency.symbol || 'R$';
            const decimals = currency.decimal_degits ?? 2;
            const symbolAtRight = Boolean(currency.symbolAtRight);
            const num = Number.parseFloat(amount);
            const safe = Number.isFinite(num) ? num : 0;
            const formatted = safe.toLocaleString('pt-BR', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            });
            return symbolAtRight
                ? formatted + ' ' + symbol
                : symbol + ' ' + formatted;
        }
        
        async function getCountryFromLatLng(lat, lng) {
            const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`;
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            return data?.address?.country || '';
        }

        async function sendNotification(fcmToken = '', title, body, payload = null) {            
            var checkFlag = false;
            var sendNotificationUrl = "{{ route('send-notification') }}";
            if (fcmToken !== '') {
                await $.ajax({
                    type: 'POST',
                    url: sendNotificationUrl,
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        'fcm': fcmToken,
                        'title': title,
                        'message': body,
                        'payload': JSON.stringify(payload)
                    },
                    success: function (data) {
                        checkFlag = true;
                    },
                    error: function (error) {
                        checkFlag = true;
                    }
                });
            } else {
                checkFlag = true;
            }

            return checkFlag;
        }
        
    </script>

    @yield('scripts')

</body>
</html>