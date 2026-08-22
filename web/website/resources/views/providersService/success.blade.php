@include('layouts.app')
@include('layouts.header')
<div class="siddhi-checkout">
    <div class="container position-relative">
        <div class="py-5 row">
            <div class="col-md-12 mb-3">
                <div>
                    <div class="siddhi-cart-item mb-3 rounded shadow-sm bg-white overflow-hidden">
                        <div class="siddhi-cart-item-profile bg-white p-3">
                            <div class="card card-default">
                                <?php $authorName = @$cart['cart_order']['authorName']; ?>
                                @if($message = Session::get('success'))
                                    <div class="py-5 linus-coming-soon d-flex justify-content-center align-items-center">
                                        <div class="col-md-6">
                                            <div class="text-center pb-3">
                                                <h1 class="font-weight-bold"><?php if (@$authorName) {
                                                        echo @strtoupper($authorName) . ",";
                                                    } ?> {{trans('lang.your_booking_has_been_successful')}}</h1>
                                                <p>Check your order status in <a href="{{route('my-bookings')}}"
                                                                                 class="font-weight-bold text-decoration-none text-primary">{{trans('lang.my_booking')}}</a>
                                                    {{trans('lang.about_next_steps_information')}}</p>
                                            </div>
                                            <div class="bg-white rounded text-center p-4 shadow-sm">
                                                <h1 class="display-1 mb-4">{{trans('lang.emoji')}}</h1>
                                                <a href="{{route('my-bookings')}}"
                                                   class="btn rounded btn-primary btn-lg btn-block">{{trans('lang.view_booking')}}</a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="data-table_processing_order" class="dataTables_processing panel panel-default" style="display: none;">
    {{trans('lang.processing_success')}}
</div>
@include('layouts.footer')
@include('layouts.nav')

@if($message = Session::get('success'))

    <script src="{{ asset('js/geofirestore.js') }}"></script>

    <script type="text/javascript">

        var fcmToken = '';
        var id_order = database.collection("tmp").doc().id;
        var userId = "<?php echo $id; ?>";
        var userDetailsRef = database.collection('users').where('id', "==", userId);
        var userproviderDetailsRef = database.collection('users');
        var AdminCommission = database.collection('sections').where('id', '==', section_id);
        var razorpaySettings = database.collection('settings').doc('razorpaySettings');
        var firestore = firebase.firestore();
        var geoFirestore = new GeoFirestore(firestore);
        
        var cart = @json($cart);
        var taxSetting = cart?.taxSetting ?? [];
        var taxScope = cart?.taxScope ?? 'order';
        var platformTax        = cart?.taxesByScope?.platform ?? [];
        var platformCharge = cart?.platformCharge ?? '0';
        
        <?php if (@$cart['paymentStatus'] == true && !empty(@$cart['cart_order']['order_json'])) { ?>
            $("#data-table_processing_order").show();
            var order_json = '<?php echo json_encode($cart['cart_order']['order_json']); ?>';
            order_json = JSON.parse(order_json);
            var provider_id = order_json.provider_id;
            var service_id = order_json.service_id;
            if (provider_id) {
                try {
                    database.collection('users').where('id', "==", provider_id).get().then(async function (Snapshots) {
                    if (Snapshots.docs.length) {
                        var userDetails = Snapshots.docs[0].data();
                        if (userDetails && userDetails.fcmToken) {
                            fcmToken = userDetails.fcmToken;
                        }
                    }
                });
            } catch (error) {
            }
        }
        finalCheckout();

        async function getProviderUser(providerId) {
            var provider = '';
            await database.collection('users').where('id', "==", provider_id).get().then(async function (Snapshots) {
                if (Snapshots.docs.length > 0) {
                    provider = Snapshots.docs[0].data();
                }
            })
            return provider;
        }

        function finalCheckout() {
            userDetailsRef.get().then(async function (userSnapshots) {
                database.collection('providers_services').where('id', "==", service_id).get().then(async function (serviceSnapshots) {
                    var userDetails = userSnapshots.docs[0].data();
                    var providerDetails = await getProviderUser(provider_id);
                    var payment_method = '<?php echo $payment_method; ?>';
                    var serviceDetails = serviceSnapshots.docs[0].data();
                    if (serviceDetails) {
                        var createdAt = firebase.firestore.FieldValue.serverTimestamp();
                        var discount = 0;
                        var scheduleDateTime = null;
                        if (order_json.scheduleDateTime && order_json.scheduleDateTime != '' && order_json.scheduleDateTime != undefined) {
                            scheduleDateTime = new Date(order_json.scheduleDateTime);
                        }
                        var location = {
                            'latitude': parseFloat(getCookie('address_lat')),
                            'longitude': parseFloat(getCookie('address_lng'))
                        };
                        var address = {
                            'address': null,
                            'addressAs': null,
                            'id': null,
                            'isDefault': null,
                            'landmark': null,
                            'locality': getCookie('address_name'),
                            'location': location
                        };
                        if (order_json.address) {
                            var location = {
                                'latitude': parseFloat(order_json.address.location.latitude),
                                'longitude': parseFloat(order_json.address.location.longitude)
                            };
                            address = {
                                'address': order_json.address.address,
                                'addressAs': order_json.address.addressAs,
                                'id': order_json.address.id,
                                'isDefault': (order_json.address.isDefault == "true" || order_json.address.isDefault == true) ? true : false,
                                'landmark': order_json.address.landmark,
                                'locality': order_json.address.locality,
                                'location': location
                            };
                        }
                        var discount = order_json.discount;
                        var discountLabel = order_json.discountLabel
                        if (discountLabel == '' || discountLabel == null || discountLabel == undefined) {
                            discountLabel = '0.0';
                        }
                        if (discount == '' || discount == null || discount == undefined) {
                            discount = '0.0';
                        }
                        var otp = Math.floor(100000 + Math.random() * 900000);
                        database.collection('provider_orders').doc(id_order).set({
                            'address': address,
                            'adminCommission': order_json.adminCommission,
                            'adminCommissionType': order_json.adminCommissionType,
                            'author': userDetails,
                            'authorID': order_json.authorID,
                            "createdAt": createdAt,
                            'couponCode': (order_json.couponCode == null) ? "" : order_json.couponCode,
                            'discount': discount,
                            'discountLabel': discountLabel,
                            'discountType': order_json.discountType,
                            'extraCharges': '',
                            'id': id_order,
                            'newScheduleDateTime': null,
                            "notes": order_json.notes,
                            'paymentStatus': (order_json.paymentStatus == "true" || order_json.paymentStatus == true) && payment_method !== 'cod' && payment_method !== 'cash on delivery',
                            'payment_method': payment_method,
                            'dispatchMode': 'direct',
                            'extraPaymentStatus': true,
                            'provider': serviceDetails,
                            "quantity": parseInt(order_json.quantity),
                            'reason': null,
                            "scheduleDateTime": scheduleDateTime,
                            'sectionId': order_json.sectionId,
                            'status': order_json.status,
                            "workerId": '',
                            "otp": otp.toString(),
                            'extraChargesDescription': '',
                            'taxSetting': taxSetting,
                            'taxScope': taxScope,
                            'platformFee': platformCharge,
                            'platformTax': platformTax,
                        }).then(async function (result) {
                            var paidAmount = parseFloat(order_json.total_pay || cart.total_price || cart.total_pay || 0);
                            if (payment_method !== 'cod' && payment_method !== 'cash on delivery' && paidAmount > 0 && provider_id) {
                                var existing = await database.collection('wallet').where('user_id', '==', provider_id).where('order_id', '==', id_order).get();
                                var already = existing.docs.some(function (d) {
                                    var data = d.data() || {};
                                    return data.transactionUser === 'provider' && data.isTopUp === true && String(data.note || '').toLowerCase().indexOf('extra') < 0;
                                });
                                if (!already) {
                                    var creditId = database.collection('tmp').doc().id;
                                    await database.collection('wallet').doc(creditId).set({
                                        amount: paidAmount,
                                        date: firebase.firestore.FieldValue.serverTimestamp(),
                                        id: creditId,
                                        isTopUp: true,
                                        order_id: id_order,
                                        payment_method: 'wallet',
                                        payment_status: 'success',
                                        serviceType: 'ondemand-service',
                                        transactionUser: 'provider',
                                        user_id: provider_id,
                                        note: 'On-demand booking credited'
                                    });
                                    var providerUserSnap = await database.collection('users').doc(provider_id).get();
                                    var providerWallet = parseFloat((providerUserSnap.data() || {}).wallet_amount) || 0;
                                    await database.collection('users').doc(provider_id).update({ wallet_amount: providerWallet + paidAmount });
                                }
                            }
                            $.ajax({
                                type: 'POST',
                                url: "<?php echo route('ondemand-order-complete'); ?>",
                                data: {
                                    _token: '<?php echo csrf_token() ?>',
                                    'fcm': fcmToken,
                                    'authorName': userDetails.firstName,
                                    'subject': order_json.subject,
                                    'message': order_json.message
                                },
                                success: async function (data) {
                                    $("#data-table_processing_order").hide();
                                    var emailUserData = await sendOnDemandMailData(id_order, service_id, order_json.authorID);
                                    if (providerDetails && providerDetails != undefined) {
                                        var emailUserData = await sendOnDemandMailData(id_order, service_id, provider_id);
                                    }
                                }
                            });
                        });
                    }
                });
            });
        }
        <?php } ?>
    </script>
@endif