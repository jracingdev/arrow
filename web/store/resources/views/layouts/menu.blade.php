<nav class="sidebar-nav">
    <ul id="sidebarnav">
        <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><a class="waves-effect waves-dark"
                href="{!! route('dashboard') !!}" aria-expanded="false">
                <i class="mdi mdi-home"></i>
                <span class="hide-menu">{{ trans('lang.dashboard') }}</span>
            </a>
        </li>
    </ul>
    <p class="web_version"></p>
</nav>

<script type="text/javascript">

    var database = firebase.firestore();
    var vendorUserId = "<?php echo $id; ?>";

    var commisionModel = false;
    var subscriptionModel = false;
    var documentVerificationEnable = false;
    var vendorId = null;
    var dineIn = false;
    var specialOffer = false;
    var enableAdvertisement = false;
    var enableSelfDelivery = false;
    var section_id = '';
    var service_type_name = '';
    var subscriptionBusinessModel = database.collection('settings').doc("vendor");
    subscriptionBusinessModel.get().then(async function(snapshots) {
        var subscriptionSetting = snapshots.data();
        if (subscriptionSetting.subscription_model == true) {
            subscriptionModel = true;
        }
    });

    var ref = database.collection('settings').doc("specialDiscountOffer");
    ref.get().then(async function(snapshots) {
        var specialDiscountOffer = snapshots.data();
        if (specialDiscountOffer.isEnable) {
            specialOffer = true;
        }
    });

    let isStoreDocumentVerify = getCookie('isStoreDocumentVerify_'+vendorUserId) === "true";
    let isAutoVerify = getCookie('isAutoVerify_'+vendorUserId) === "true";
    let isvendorID = getCookie('isvendorID_'+vendorUserId);
    let isVendorValid = (isvendorID !== null && isvendorID !== undefined && isvendorID !== "");
    var authRole = "{{ $authRole }}";
    var isStoreVerification = false;
    var allApproved = false;
    database.collection('documents_verify').where('id', '==', vendorUserId).get().then(function(querySnapshot) {     
        

        querySnapshot.forEach(function(doc) {
            var data = doc.data();
            if (Array.isArray(data.documents)) {
                data.documents.forEach(function(item) {
                    var st = (item.status || '').toString().trim().toLowerCase();
                    if (st == 'approved') {
                        allApproved = true;
                    }
                });
            } else {
                var st = (data.status || '').toString().trim().toLowerCase();
                if (st == 'approved') {
                    allApproved = true;
                }
            }
        });

        allDocsApproved = allApproved;
        
        database.collection('settings').doc("document_verification_settings").get().then(function(settingDoc) {
           
            if (settingDoc.exists) {
                var settingData = settingDoc.data();
                isStoreVerification = settingData.isStoreVerification;
            }
            // Only show store if ALL documents are approved
           
            if ((isStoreDocumentVerify == true && allApproved == true && isStoreVerification == true) || (isStoreVerification == true && isAutoVerify == true) || (isStoreVerification == false)) {
                if(authRole === 'vendor'){
                    var newLi = `
                    <li class="{{ request()->routeIs('store') ? 'active' : '' }}">
                        <a class="waves-effect waves-dark" href="{!! route('store') !!}" aria-expanded="false">
                            <i class="mdi mdi-store"></i>
                            <span class="hide-menu">{{ trans('lang.mystore_plural') }}</span>
                        </a>
                    </li>`;
                $('#sidebarnav').append(newLi);
                }
            }
            if (authRole === 'vendor' && ((isStoreVerification == true && isAutoVerify == false) || (isStoreVerification == true && isStoreDocumentVerify == false) )) {
            var newLi = `
                    <li class="{{ request()->routeIs('vendors.document') ? 'active' : '' }}">
                    <a class="waves-effect waves-dark" href="{!! route('vendors.document') !!}" aria-expanded="false">
                        <i class="mdi mdi-file-document"></i>
                        <span class="hide-menu">{{ trans('lang.document_plural') }}</span>
                    </a>
                </li>`;
                $('#sidebarnav').append(newLi);
            }
        }).catch(function(err) {
            console.log("Error reading document_verification_settings:", err);
        });
    }).catch(function(error) {
        console.log("Error checking documents:", error);
    });   
  
    database.collection('settings').doc("globalSettings").get().then(async function(
        settingSnapshots) {
        if (settingSnapshots.data()) {
            var settingData = settingSnapshots.data();
            if (settingData.isEnableAdsFeature) {
                enableAdvertisement = true;
            }
            if (settingData.isSelfDelivery) {
                enableSelfDelivery = true;
            }
        }
    })
    var enableEmployeeManagement = false;
    database.collection('settings').doc("vendor").get().then(function (snap) {
        if (snap.exists && snap.data().isEmployeeManagement === true) {
            enableEmployeeManagement = true;
        }
    });  
    var newLi = '';
    database.collection('users').doc(vendorUserId).get().then(async function(usersnapshots) {
        
        var userData = usersnapshots.data();
        var checkVendor = null;
        var username = userData.firstName + ' ' + userData.lastName;
        $('#username').text(username);

        if (userData.hasOwnProperty('profilePictureURL') && userData.profilePictureURL !=
            "") {
            $('.userimage').attr('src', userData.profilePictureURL);
        }
        if(authRole === 'vendor'){
            if (userData.hasOwnProperty('sectionId')) {
                section_id = userData.sectionId;
                service_type_name = await getSectionServiceType(section_id);

            }        
        }
        if(authRole === 'employee'){
            var userVendorSnapshots = await database.collection('vendors').doc(userData.vendorID).get();
            var userVendorData = userVendorSnapshots.data();
            if (userVendorData.hasOwnProperty('section_id')) {
                section_id = userVendorData.section_id;
                service_type_name = await getSectionServiceType(section_id);

            }    
            
        }

        if (userData.hasOwnProperty('vendorID') && userData.vendorID != '' && userData
            .vendorID != null) {
            vendorId = userData.vendorID;
            checkVendor = userData.vendorID;
        }
        if(userData.role == "provider"){
            newLi += `<li class="{{ request()->routeIs('provider.bookings') || request()->routeIs('provider.bookings.edit') ? 'active' : '' }}">
                    <a class="waves-effect waves-dark" href="{!! route('provider.bookings') !!}" aria-expanded="false">
                        <i class="mdi mdi-calendar-clock"></i>
                        <span class="hide-menu">{{ trans('lang.booking_plural') }}</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('provider.services') || request()->routeIs('provider.services.create') || request()->routeIs('provider.services.edit') ? 'active' : '' }}">
                    <a class="waves-effect waves-dark" href="{!! route('provider.services') !!}" aria-expanded="false">
                        <i class="mdi mdi-briefcase"></i>
                        <span class="hide-menu">{{ trans('lang.service_plural') }}</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('provider.workers') || request()->routeIs('provider.workers.create') || request()->routeIs('provider.workers.edit') ? 'active' : '' }}">
                    <a class="waves-effect waves-dark" href="{!! route('provider.workers') !!}" aria-expanded="false">
                        <i class="mdi mdi-account-multiple"></i>
                        <span class="hide-menu">{{ trans('lang.worker_plural') }}</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('wallettransaction.index') ? 'active' : '' }}">
                    <a class="waves-effect waves-dark" href="{!! route('wallettransaction.index') !!}" aria-expanded="false">
                        <i class="mdi mdi-swap-horizontal"></i>
                        <span class="hide-menu">{{ trans('lang.wallet_transaction_plural') }}</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('user.profile') ? 'active' : '' }}">
                    <a class="waves-effect waves-dark" href="{!! route('user.profile') !!}" aria-expanded="false">
                        <i class="mdi mdi-account"></i>
                        <span class="hide-menu">{{ trans('lang.profile') }}</span>
                    </a>
                </li>`;
            $('#sidebarnav').append(newLi);
            return;
        }
        if(userData.role == "vendor"){
             newLi += `<li>
                    <a class="waves-effect waves-dark" href="{!! route('point.of.sale') !!}" aria-expanded="false">
                        <i class="mdi mdi-calculator"></i>
                        <span class="hide-menu">{{ trans('lang.point_of_sale') }}</span>
                    </a>
                </li>`;
                newLi += `<li>
                    <a class="waves-effect waves-dark" href="{!! route('pos.order') !!}" aria-expanded="false">
                        <i class="mdi mdi-receipt"></i>
                        <span class="hide-menu">{{ trans('lang.pos_orders') }}</span>
                    </a>
                </li>`;  
            if (subscriptionModel == true || commisionModel == true) {
                newLi += `<li class="{{ request()->routeIs('subscription-plan.show') ? 'active' : '' }}">
                                <a class="waves-effect waves-dark" href="{!! route('subscription-plan.show') !!}" aria-expanded="false">
                                    <i class="mdi mdi-crown"></i>
                                    <span class="hide-menu">{{ trans('lang.change_subscription') }}</span>
                                </a>
                            </li>`;

            }
            newLi += `<li class="{{ request()->routeIs('my-subscriptions') ? 'active' : '' }}">
                                        <a class="waves-effect waves-dark" href="{!! route('my-subscriptions') !!}" aria-expanded="false">
                                            <i class="mdi mdi-wallet-membership"></i>
                                            <span class="hide-menu">{{ trans('lang.my_subscriptions') }}</span>
                                        </a>
                                    </li>`;
            if (enableEmployeeManagement) {
                newLi += `<li>
                    <a class="waves-effect waves-dark" href="{!! route('role.index') !!}" aria-expanded="false">
                        <i class="mdi mdi-lock"></i>
                        <span class="hide-menu">{{ trans('lang.employee_role') }}</span>
                    </a>
                </li>`;
                newLi += `<li>
                    <a class="waves-effect waves-dark" href="{!! route('employee.index') !!}" aria-expanded="false">
                        <i class="mdi mdi-account"></i>
                        <span class="hide-menu">{{ trans('lang.employee_plural') }}</span>
                    </a>
                </li>`;
            }

            if (checkVendor != null) {
                newLi += `<li class="{{ request()->routeIs('items') ? 'active' : '' }}">
                                        <a class="waves-effect waves-dark" href="{!! route('items') !!}" aria-expanded="false">
                                            <i class="mdi mdi-shopping"></i>
                                            <span class="hide-menu">{{ trans('lang.item_plural') }}</span>
                                        </a>
                                    </li>
                            <li class="{{ request()->routeIs('orders') ? 'active' : '' }}">
                                <a class="waves-effect waves-dark" href="{!! route('orders') !!}" aria-expanded="false">
                                    <i class="mdi mdi-reorder-horizontal"></i>
                                    <span class="hide-menu">{{ trans('lang.order_plural') }}</span>
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('coupons') ? 'active' : '' }}"><a class="waves-effect waves-dark" href="{!! route('coupons') !!}" aria-expanded="false">
                                    <i class="mdi mdi-sale"></i>
                                    <span class="hide-menu">{{ trans('lang.coupon_plural') }}</span>
                                </a>
                            </li>`;                           
                if (isVendorValid && enableAdvertisement) {
                    var adsParentActive =
                        "{{ request()->routeIs('advertisements.pending') || request()->routeIs('advertisements') ? 'active' : '' }}";
                    var pendingActive = "{{ request()->routeIs('advertisements.pending') ? 'active' : '' }}";
                    var listActive = "{{ request()->routeIs('advertisements') ? 'active' : '' }}";

                    var dropdownShow = (pendingActive || listActive) ? 'show' : '';
                    var ariaExpanded = (pendingActive || listActive) ? 'true' : 'false';
                    newLi += `<li class="${adsParentActive}"><a class="has-arrow waves-effect waves-dark" href="#"
                                                        data-toggle="collapse" data-target="#adsDropdown" aria-expanded="${ariaExpanded}">
                                                        <i class="mdi mdi-newspaper"></i>
                                                        <span class="hide-menu">{{ trans('lang.advertisement_plural') }}</span>
                                                    </a>
                                                    <ul id="adsDropdown" aria-expanded="false" class="collapse ${dropdownShow}">
                                                        <li class="${pendingActive}"><a class="${pendingActive}" href="{!! route('advertisements.pending') !!}">{{ trans('lang.pending') }}</a></li>
                                                        <li class="${listActive}"><a class="${listActive}" href="{!! route('advertisements') !!}">{{ trans('lang.ads_list') }}</a></li>
                                                    </ul>
                                                </li>`;
                }

                if (enableSelfDelivery && service_type_name == 'Multivendor Delivery Service') {
                    newLi += `<li class="{{ request()->routeIs('deliveryman') ? 'active' : '' }}"><a class="waves-effect waves-dark"
                                        href="{!! route('deliveryman') !!}" aria-expanded="false">
                                        <i class="mdi mdi-run"></i>
                                        <span class="hide-menu">{{ trans('lang.delivery_man') }}</span>
                                    </a>
                                </li>`;
                }
                newLi += `<li class="{{ request()->routeIs('payments') ? 'active' : '' }}"> <a class="waves-effect waves-dark" href="{!! route('payments') !!}" aria-expanded="false">
                                    <i class="mdi mdi-wallet"></i>
                                    <span class="hide-menu">{{ trans('lang.payouts_plural') }}</span>
                                </a>

                            </li>`;
                if (specialOffer) {
                    newLi += `<li class="{{ request()->routeIs('specialOffer') ? 'active' : '' }}">
                                <a class="waves-effect waves-dark" href="{!! route('specialOffer') !!}" aria-expanded="false">
                                    <i class="fa fa-table "></i>
                                    <span class="hide-menu">{{ trans('lang.special_offer') }}</span>
                                </a>
                            </li>`;
                }
                if (dineIn) {

                    newLi += `<li class="dineInHistory {{ request()->routeIs('booktable') ? 'active' : '' }}"><a class="waves-effect waves-dark"
                                                        href="{!! route('booktable') !!}" aria-expanded="false">
                                                        <i class="fa fa-table "></i>
                                                        <span class="hide-menu">{{trans('lang.dine_in_booking_history')}}</span>
                                                    </a>
                                                </li>`;
                }

            }
            newLi += `<li class="{{ request()->routeIs('wallettransaction.index') ? 'active' : '' }}"> <a class="waves-effect waves-dark" href="{!! route('wallettransaction.index') !!}" aria-expanded="false">
                                        <i class="mdi mdi-swap-horizontal"></i>
                                        <span class="hide-menu">{{ trans('lang.wallet_transaction_plural') }}</span>
                                    </a>
                                </li>

                                <li class="{{ request()->routeIs('withdraw-method') ? 'active' : '' }}">
                                    <a class=" waves-effect waves-dark" href="{!! route('withdraw-method') !!}" aria-expanded="false">
                                        <i class="fa fa-credit-card "></i>
                                        <span class="hide-menu">{{ trans('lang.withdrawal_method') }}</span>
                                    </a>
                                </li>`;
                                
            if (isVendorValid && enableAdvertisement){  
                                       
                newLi += `<li class="waves-effect waves-dark p-2">
                            <div class="promo-card">
                                <div class="position-relative">
                                    <img src="{{ asset('images/advertisement_promo.png') }}" class="mw-100" alt="">
                                    <h4 class="mb-2 mt-3">{{trans('lang.want_to_get_highlighted')}}</h4>
                                    <p class="mb-4">
                                        {{trans('lang.create_ads_to_get_highlighted_on_the_app_and_web_browser')}}
                                    </p>
                                    <a href="{{ route('advertisements.create') }}" class="btn btn-primary">{{trans('lang.create_ads')}}</a>
                                </div>
                            </div>
                        </li>`
            }
        }
        if(userData.role == "employee"){
            if (!userData.hasOwnProperty('employeePermissionId') || !userData.employeePermissionId) {
                // No role assigned → maybe show message or empty menu
                newLi += `<li><a href="#"><i class="mdi mdi-alert"></i> <span>{{trans('lang.no_permissions_assigned')}}</span></a></li>`;
            } else {
                database.collection('vendor_employee_roles')
                    .doc(userData.employeePermissionId)
                    .get()
                    .then(function(roleSnap) {
                        if (!roleSnap.exists) {
                            console.warn("Employee role document not found");
                            return;
                        }

                        var roleData = roleSnap.data();
                        $('#roleName').removeClass('d-none');
                        $('#roleName').text("{{trans('lang.assinged_role')}}" + ' : '+roleData.title);
                        var advPerm = false;

                        if (roleData.isEnable !== true) {                                       
                            return;
                        }

                        // Build menu based on permissions
                        var perms = roleData.permissions || [];

                        perms.forEach(function(perm) {
                            // Only add if isActive is true
                            if (perm.isActive !== true) return;

                            var title = perm.title;
                            var menuHtml = '';
                            switch (title) {
                                case "Employee Role":
                                    if (!enableEmployeeManagement) break;
                                    menuHtml = `
                                    <li>
                                        <a class="waves-effect waves-dark" href="{!! route('role.index') !!}" aria-expanded="false">
                                            <i class="mdi mdi-lock"></i>
                                            <span class="hide-menu">{{ trans('lang.employee_role') }}</span>
                                        </a>
                                    </li>`;
                                    break;

                                case "All Employee":
                                    if (!enableEmployeeManagement) break;
                                    menuHtml = `
                                    <li>
                                        <a class="waves-effect waves-dark" href="{!! route('employee.index') !!}" aria-expanded="false">
                                            <i class="mdi mdi-account"></i>
                                            <span class="hide-menu">{{ trans('lang.employee_plural') }}</span>
                                        </a>
                                    </li>`;
                                    break;

                                case "Store Information's":
                                    if ((isStoreDocumentVerify == true && allApproved == true && isStoreVerification == true) || (isStoreVerification == true && isAutoVerify == true) || (isStoreVerification == false)) {
                                        menuHtml = `
                                        <li>
                                            <a class="waves-effect waves-dark" href="{!! route('store') !!}" aria-expanded="false">
                                                <i class="mdi mdi-store"></i>
                                                <span class="hide-menu">{{ trans('lang.mystore_plural') }}</span>
                                            </a>
                                        </li>`;
                                    }
                                    break;
                                
                                case "Manage Products":
                                    menuHtml = `
                                    <li>
                                        <a class="waves-effect waves-dark" href="{!! route('items') !!}" aria-expanded="false">
                                            <i class="mdi mdi-food"></i>
                                            <span class="hide-menu">{{ trans('lang.item_plural') }}</span>
                                        </a>
                                    </li>`;
                                    break;

                                case "Manage Order":
                                    menuHtml = `
                                    <li>
                                        <a class="has-arrow waves-effect waves-dark" href="#"
                                        data-toggle="collapse" data-target="#orderDropdown">
                                            <i class="mdi mdi-reorder-horizontal"></i>
                                            <span class="hide-menu">{{ trans('lang.order_plural') }}</span>
                                        </a>
                                        <ul id="orderDropdown" aria-expanded="false" class="collapse">
                                            <li><a href="{!! route('orders') !!}">{{ trans('lang.order_plural') }}</a></li>
                                            <li><a href="{!! route('placedOrders') !!}">{{ trans('lang.placed_orders') }}</a></li>
                                            <li><a href="{!! route('acceptedOrders') !!}">{{ trans('lang.accepted_orders') }}</a></li>
                                            <li><a href="{!! route('rejectedOrders') !!}">{{ trans('lang.rejected_orders') }}</a></li>
                                        </ul>
                                    </li>`;
                                    break;   
                                case "Offers":
                                    menuHtml = `
                                    <li>
                                        <a class="waves-effect waves-dark" href="{!! route('coupons') !!}" aria-expanded="false">
                                            <i class="mdi mdi-sale"></i>
                                            <span class="hide-menu">{{ trans('lang.coupon_plural') }}</span>
                                        </a>
                                    </li>`;
                                    break;
                                case "Special Discounts":   
                                    if (specialOffer) {
                                        menuHtml += `<li class="{{ request()->routeIs('specialOffer') ? 'active' : '' }}">
                                            <a class="waves-effect waves-dark" href="{!! route('specialOffer') !!}" aria-expanded="false">
                                                <i class="fa fa-table "></i>
                                                <span class="hide-menu">{{ trans('lang.special_offer') }}</span>
                                            </a>
                                        </li>`;
                                    }                                            
                                    break;

                                case "Advertisement":
                                    if (isVendorValid && enableAdvertisement) {
                                        menuHtml = `
                                        <li><a class="has-arrow waves-effect waves-dark" href="#"
                                            data-toggle="collapse" data-target="#adsDropdown">
                                            <i class="mdi mdi-newspaper"></i>
                                            <span class="hide-menu">{{ trans('lang.advertisement_plural') }}</span>
                                                </a>
                                                <ul id="adsDropdown" aria-expanded="false" class="collapse">
                                                    <li><a href="{!! route('advertisements.pending') !!}">{{ trans('lang.pending') }}</a></li>
                                                    <li><a href="{!! route('advertisements') !!}">{{ trans('lang.ads_list') }}</a></li>
                                                </ul>
                                            </li>`;
                                    }
                                    advPerm = true;
                                    break;
                                
                                case "Manage Delivery Man":
                                    if (enableSelfDelivery) {
                                        menuHtml = `
                                        <li>
                                            <a class="waves-effect waves-dark" href="{!! route('deliveryman') !!}" aria-expanded="false">
                                                <i class="mdi mdi-run"></i>
                                                <span class="hide-menu">{{ trans('lang.delivery_man') }}</span>
                                            </a>
                                        </li>`;
                                    }
                                    break;

                                case "Woking Hours":   
                                    // Usually part of restaurant info
                                    break;

                                case "Withdraw Method":
                                    menuHtml = `
                                    <li>
                                        <a class="waves-effect waves-dark" href="{!! route('withdraw-method') !!}" aria-expanded="false">
                                            <i class="fa fa-credit-card"></i>
                                            <span class="hide-menu">{{ trans('lang.withdrawal_method') }}</span>
                                        </a>
                                    </li>`;
                                    break;
                                
                                case "Add Dine in":
                                    break;
                                case "Dine in Request":
                                    if (dineIn) {
                                        menuHtml = `
                                        <li>
                                            <a class="waves-effect waves-dark" href="{!! route('booktable') !!}" aria-expanded="false">
                                                <i class="fa fa-table"></i>
                                                <span class="hide-menu">{{ trans('lang.book_table') }} / {{ trans('lang.dine_in_booking_history') }}</span>
                                            </a>
                                        </li>`;
                                    }
                                    break;

                                default:
                                    // unknown permission 
                                    break;
                            }
                            
                            if (menuHtml) {
                                newLi += menuHtml;
                            }
                        });
                        if(isVendorValid && enableAdvertisement && advPerm){
                            newLi+=`<li class="waves-effect waves-dark p-2">
                                <div class="promo-card">
                                    <div class="position-relative">
                                        <img src="{{asset('images/advertisement_promo.png')}}" class="mw-100" alt="">
                                        <h4 class="mb-2 mt-3">{{trans('lang.want_to_get_highlighted')}}</h4>
                                        <p class="mb-4">
                                            {{trans('lang.create_ads_to_get_highlighted_on_the_app_and_web_browser')}}
                                        </p>
                                        <a href="{{route('advertisements.create')}}" class="btn btn-primary">{{trans('lang.create_ads')}}</a>
                                    </div>
                                </div>
                            </li>`
                        }

                        $('#sidebarnav').append(newLi);
                    })
                    .catch(function(err) {
                        console.error("Error fetching employee role:", err);
                    });

                return;
            }
        }

        $('#sidebarnav').append(newLi);

        if (commisionModel || subscriptionModel) {
            if (userData.hasOwnProperty('subscriptionPlanId') && userData.subscriptionPlanId != null) {
                var isSubscribed = true;
            } else {
                var isSubscribed = false;
            }
        } else {
            var isSubscribed = '';
        }

        var url = "{{ route('setSubcriptionFlag') }}";
        $.ajax({

            type: 'POST',

            url: url,

            data: {

                email: "{{ Auth::user()->email }}",
                isSubscribed: isSubscribed
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },

            success: function(data) {
                if (data.access) {

                }
            }

        })
    });
    
    async function getSectionServiceType(section_id) {
        var sectionsRef = database.collection('sections').where('id', '==', section_id);
        
        var snapshots = await sectionsRef.get();
        var datas = snapshots.docs[0].data();
        service_type_name = datas.serviceType;
        var enabledDiveInFuture = datas.dine_in_active;
        if (enabledDiveInFuture) {
            dineIn = true;
        }
        var commissionSetting = datas.adminCommision;
        if (commissionSetting.enable == true) {
            commisionModel = true;
        }        
        return service_type_name;
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
</script>
