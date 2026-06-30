<nav class="sidebar-nav">

    <ul id="sidebarnav">

        <li><a class="waves-effect waves-dark" href="{!! url('dashboard') !!}" aria-expanded="false">

                <i class="mdi mdi-home"></i>

                <span class="hide-menu">{{ trans('lang.dashboard') }}</span>

            </a>
        </li>

        <li>
            <a class="waves-effect waves-dark" href="{!! url('store') !!}" aria-expanded="false">
                <i class="mdi mdi-store"></i>
                <span class="hide-menu">{{ trans('lang.mystore_plural') }}</span>
            </a>
        </li>
    </ul>

    <p class="web_version"></p>

</nav>

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-firestore.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-storage.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-database.js"></script>
<script src="{{ asset('js/geofirestore.js') }}"></script>
<script src="https://cdn.firebase.com/libs/geofire/5.0.1/geofire.min.js"></script>
<script src="{{ asset('js/crypto-js.js') }}"></script>
<script src="{{ asset('js/jquery.cookie.js') }}"></script>
<script src="{{ asset('js/jquery.validate.js') }}"></script>


<script type="text/javascript">
    var database = firebase.firestore();
    var vendorUserId = "<?php echo $id; ?>";
    var commisionModel = false;
    var subscriptionModel = false;
    var vendorId = null;
    var dineIn = false;
    var specialOffer = false;
    var section_id = '';
    var service_type = '';
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
    var newLi = '';

    database.collection('users').doc(vendorUserId).get().then(async function(usersnapshots) {
        var userData = usersnapshots.data();
        var checkVendor=null;
        var username = userData.firstName + ' ' + userData.lastName;
        $('#username').text(username);

        if (userData.hasOwnProperty('profilePictureURL') && userData.profilePictureURL !=
            "") {
            $('.userimage').attr('src', userData.profilePictureURL);
        }

        if (userData.hasOwnProperty('section_id')) {
            section_id = userData.section_id;
            service_type = await getSectionServiceType(section_id);
        }
        
        if (userData.hasOwnProperty('vendorID') && userData.vendorID != '' && userData
            .vendorID != null) {
            vendorId = userData.vendorID;
            checkVendor=userData.vendorID;
        }

        if (subscriptionModel == true || commisionModel == true) {
            newLi += `<li>
                            <a class="waves-effect waves-dark" href="{!! route('subscription-plan.show') !!}" aria-expanded="false">
                                <i class="mdi mdi-crown"></i>
                                <span class="hide-menu">{{ trans('lang.change_subscription') }}</span>
                            </a>
                        </li>`;

        }
        newLi += `<li>
                                    <a class="waves-effect waves-dark" href="{!! url('my-subscriptions') !!}" aria-expanded="false">
                                        <i class="mdi mdi-wallet-membership"></i>
                                        <span class="hide-menu">{{ trans('lang.my_subscriptions') }}</span>
                                    </a>
                                 </li>`;

        if (checkVendor != null) {
            newLi += `<li>
                                    <a class="waves-effect waves-dark" href="{!! url('items') !!}" aria-expanded="false">
                                        <i class="mdi mdi-shopping"></i>
                                        <span class="hide-menu">{{ trans('lang.item_plural') }}</span>
                                    </a>
                                </li>
                                <li>
                            <a class="waves-effect waves-dark" href="{!! url('orders') !!}" aria-expanded="false">
                                <i class="mdi mdi-reorder-horizontal"></i>
                                <span class="hide-menu">{{ trans('lang.order_plural') }}</span>
                            </a>
                        </li>
                        <li><a class="waves-effect waves-dark" href="{!! url('coupons') !!}" aria-expanded="false">
                                <i class="mdi mdi-sale"></i>
                                <span class="hide-menu">{{ trans('lang.coupon_plural') }}</span>
                            </a>
                        </li>
                        <li> <a class="waves-effect waves-dark" href="{!! url('payments') !!}" aria-expanded="false">
                                <i class="mdi mdi-wallet"></i>
                                <span class="hide-menu">{{ trans('lang.payouts_plural') }}</span>
                            </a>

                        </li>`;
            if (specialOffer) {
                newLi += `<li>
                            <a class="waves-effect waves-dark" href="{!! url('special-offer') !!}" aria-expanded="false">
                                <i class="fa fa-table "></i>
                                <span class="hide-menu">{{ trans('lang.special_offer') }}</span>
                            </a>
                        </li>`;
            }

            if (dineIn) {

                newLi += `<li class="dineInHistory" style="display: none;"><a class="waves-effect waves-dark"
                                                    href="{!! url('booktable') !!}" aria-expanded="false">
                                                    <i class="fa fa-table "></i>
                                                    <span class="hide-menu">{{ trans('lang.book_table') }} / DINE IN History</span>
                                                </a>
                                            </li>`;
            }

        }
        newLi += `<li> <a class="waves-effect waves-dark" href="{!! url('wallettransaction') !!}" aria-expanded="false">
                                    <i class="mdi mdi-swap-horizontal"></i>
                                    <span class="hide-menu">{{ trans('lang.wallet_transaction_plural') }}</span>
                                </a>
                            </li>

                            <li>
                                <a class=" waves-effect waves-dark" href="{!! url('withdraw-method') !!}" aria-expanded="false">
                                    <i class="fa fa-credit-card "></i>
                                    <span class="hide-menu">{{ trans('lang.withdrawal_method') }}</span>
                                </a>
                            </li>`;

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

        await sectionsRef.get().then(async function(snapshots) {
            var datas = snapshots.docs[0].data();
            service_type = datas.serviceType;
            var enabledDiveInFuture = datas.dine_in_active;
            if (enabledDiveInFuture) {
                dineIn = true;
            }
            var commissionSetting = datas.adminCommision;
            if (commissionSetting.enable == true) {
                commisionModel = true;
            }
        });
    }
</script>
