@extends('layouts.app')

@section('content')

<?php if ($id == 'create') {

    $id = '';

} ?>

<div class="page-wrapper">

    <div class="row page-titles">


        <div class="col-md-5 align-self-center">

            <h3 class="text-themecolor">{{trans('lang.vendors_payout_plural')}}</h3>

        </div>

        <div class="col-md-7 align-self-center">

            <ol class="breadcrumb">

                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{trans('lang.dashboard')}}</a></li>

                <?php if ($id != '') { ?>

                    <li class="breadcrumb-item"><a
                                href="{{ route('vendors.payout', ['id' => $id]) }}">{{trans('lang.vendors_payout_table')}}</a>
                    </li>

                <?php } ?>

                <li class="breadcrumb-item">{{trans('lang.vendors_payout_create')}}</li>

            </ol>

        </div>

    </div>


    <div class="card-body">

        <div class="error_top"></div>

        <div class="row vendor_payout_create">

            <div class="vendor_payout_create-inner">

                <fieldset>

                    <legend>{{trans('lang.vendors_payout_create')}}</legend>

                    <?php if ($id == '') { ?>

                        <div class="form-group row width-100">

                            <label class="col-3 control-label">{{ trans('lang.select_vendor')}}</label>

                            <div class="col-7">

                                <select id="select_vendor" class="form-control">

                                    <option value="">{{ trans('lang.select_vendor') }}</option>

                                </select>

                                <div class="form-text text-muted">

                                    {{ trans("lang.select_vendor") }}

                                </div>

                            </div>

                        </div>

                    <?php } ?>

                    <div class="form-group row width-100">

                        <label class="col-3 control-label">{{trans('lang.payout_method')}}</label>

                        <div class="col-7">

                            <select id="payout_method" class="form-control">
                                <option value="pix" selected>{{ trans('lang.pix') }}</option>
                                <option value="bank">{{ trans('lang.bankdetails') }}</option>
                            </select>

                        </div>

                    </div>

                    <div class="form-group row width-100 pix-fields">

                        <label class="col-3 control-label">{{trans('lang.pix_key_type')}}</label>

                        <div class="col-7">

                            <select id="pix_key_type" class="form-control">
                                <option value="cpf">{{ trans('lang.cpf') }}</option>
                                <option value="cnpj">{{ trans('lang.cnpj') }}</option>
                                <option value="email">{{ trans('lang.email') }}</option>
                                <option value="phone">{{ trans('lang.user_phone') }}</option>
                                <option value="evp">{{ trans('lang.pix_key_evp') }}</option>
                            </select>

                        </div>

                    </div>

                    <div class="form-group row width-100 pix-fields">

                        <label class="col-3 control-label">{{trans('lang.pix_key')}}</label>

                        <div class="col-7">

                            <input type="text" class="form-control" id="pix_key">

                            <div class="form-text text-muted">{{ trans('lang.pix_key_help') }}</div>

                        </div>

                    </div>

                    <div class="form-group row width-100">

                        <label class="col-3 control-label">{{trans('lang.payout_currency_brl')}}</label>

                        <div class="col-7">

                            <input type="number" step="0.01" min="0" class="form-control payout_amount">

                            <div class="form-text text-muted">

                                {{ trans("lang.vendors_payout_amount_placeholder") }}

                            </div>

                        </div>

                    </div>


                    <div class="form-group row width-100">

                        <label class="col-3 control-label">{{ trans('lang.vendors_payout_note')}}</label>

                        <div class="col-7">

                            <textarea type="text" rows="8" class="form-control payout_note"></textarea>

                        </div>

                    </div>


                </fieldset>

            </div>

        </div>

    </div>


    <div class="form-group col-12 text-center btm-btn">

        <button type="button" class="btn btn-primary save-form-btn"><i class="fa fa-save"></i>
            {{trans('lang.save')}}
        </button>

        <?php if ($id != '') { ?>

            <a href="{{route('vendors.payout',$id)}}" class="btn btn-default"><i
                        class="fa fa-undo"></i>{{trans('lang.cancel')}}</a>

        <?php } else { ?>

            <a href="{!! route('payoutRequests.vendor.disbursement') !!}" class="btn btn-default"><i
                        class="fa fa-undo"></i>{{trans('lang.cancel')}}</a>

        <?php } ?> 

    </div>

</div>

</div>

</div>


@endsection

@section('scripts')

<script type="text/javascript">

    var section_id = getCookie('section_id') || '';

    var vendors = [];

    var database = firebase.firestore();

    var email_templates = database.collection('email_templates').where('type', '==', 'payout_request');

    var emailTemplatesData = null;

    var adminEmail = '';

    var emailSetting = database.collection('settings').doc('emailSetting');

    var userName = '';
    var userContact = '';

    async function remainingPriceOLD(vendorID) {

        var paid_price = 0;

        var total_price = 0;

        var remaining = 0;

        var adminCommission = 0;

        var commission = 0;

        await database.collection('payouts').where('vendorID', '==', vendorID).get().then(async function (payoutSnapshots) {

            payoutSnapshots.docs.forEach((payout) => {

                var payoutData = payout.data();

                paid_price = parseFloat(paid_price) + parseFloat(payoutData.amount);

            })


            await database.collection('vendor_orders').where('vendor.id', '==', vendorID).where("status", "in", ["Order Completed"]).get().then(async function (orderSnapshots) {

                orderSnapshots.docs.forEach((order) => {

                    var orderData = order.data();

                    var productTotalmain = 0;

                    orderData.products.forEach((product) => {

                        var extras_price = 0;

                        if (product.price && product.quantity != 0) {

                            if (product.extras_price != undefined) {

                                extras_price = parseFloat(product.extras_price) * parseInt(product.quantity);

                            }

                            var productTotal = (parseFloat(product.price) * parseFloat(product.quantity)) + parseFloat(extras_price);


                            total_price = parseFloat(total_price) + parseFloat(productTotal);

                            productTotalmain = parseFloat(productTotalmain) + parseFloat(productTotal);

                        }

                    })

                    if (orderData.adminCommission != undefined && orderData.adminCommissionType != undefined) {

                        if (orderData.adminCommissionType == "Percent") {

                            commission = (productTotalmain * parseFloat(orderData.adminCommission)) / 100;


                        } else {

                            commission = parseFloat(orderData.adminCommission);

                        }

                    } else if (orderData.adminCommission != undefined) {

                        commission = parseFloat(orderData.adminCommission);

                    }


                })


                if (adminCommission != undefined) {

                    total_price = parseFloat(total_price) - parseFloat(commission);

                }


                remaining = parseFloat(total_price) - parseFloat(paid_price);

            });

        });

        return remaining;

    }


    var currentCurrency = '';
    var currencyAtRight = false;
    var decimal_degits = 0;

    var refCurrency = database.collection('currencies').where('isActive', '==', true);
    refCurrency.get().then(async function (snapshots) {
        var currencyData = snapshots.docs[0].data();
        currentCurrency = currencyData.symbol;
        currencyAtRight = currencyData.symbolAtRight;
        if (currencyData.decimal_degits) {
            decimal_degits = currencyData.decimal_degits;
        }
    });

    $(document).ready(function () {

        $("#data-table_processing").show();

        email_templates.get().then(async function (snapshots) {
            emailTemplatesData = snapshots.docs[0].data();

        });


        emailSetting.get().then(async function (snapshots) {
            var emailSettingData = snapshots.data();

            adminEmail = emailSettingData.userName;
        });

        database.collection('vendors').where('section_id', '==', section_id).get().then(async function (snapshots) {

            snapshots.docs.forEach((listval) => {

                var data = listval.data();
                vendors.push(data);
                $('#select_vendor').append($("<option></option>")

                    .attr("value", data.id)

                    .text(data.title));

            })
            $('#select_vendor').on('change', function () {
                loadVendorPix($(this).val());
            });

        });

        async function loadVendorPix(vendorID) {
            if (!vendorID) {
                return;
            }
            var ownerSnap = await database.collection('users').where('vendorID', '==', vendorID).where('role', '==', 'vendor').get();
            if (ownerSnap.empty) {
                return;
            }
            var owner = ownerSnap.docs[0].data();
            if (owner.userBankDetails) {
                fillPixDetails(owner.userBankDetails);
            }
            var methodSnap = await database.collection('withdraw_method').where('userId', '==', owner.id).get();
            if (!methodSnap.empty && methodSnap.docs[0].data().pix) {
                var pix = methodSnap.docs[0].data().pix;
                fillPixDetails({ pixKey: pix.chave, pixKeyType: pix.tipo });
            }
        }

        <?php if ($id != '') { ?>
        loadVendorPix("<?php echo $id; ?>");
        <?php } ?>

        $("#data-table_processing").hide();

        var payoutId = "<?php echo uniqid(); ?>";

        $(".save-form-btn").click(async function () {

            <?php if ($id == '') { ?>

            var vendorID = $("#select_vendor").val();

            <?php } else { ?>

            var vendorID = "<?php echo $id; ?>";

            <?php } ?>

            var vendorUserId = await getVendorId(vendorID);
            var vendorEmail = await getVendorEmail(vendorID);

            var remaining = await remainingPrice(vendorID);

            if (remaining > 0) {

                var amount = parseFloat($(".payout_amount").val());
                var note = $(".payout_note").val();
                var payoutMethod = $('#payout_method').val() || 'pix';
                var pixKey = ($('#pix_key').val() || '').trim();
                var pixKeyType = $('#pix_key_type').val() || 'cpf';

                var date = new Date(Date.now());

                if (payoutMethod === 'pix' && pixKey === '') {
                    $(".error_top").show();
                    $(".error_top").html("<p>{{trans('lang.pix_key_required')}}</p>");
                    $(window).scrollTop(0);
                    return;
                }

                if (vendorID != '' && $(".payout_amount").val() != '') {

                    database.collection('payouts').doc(payoutId).set({
                        'vendorID': vendorID,
                        'amount': amount,
                        'adminNote': note,
                        'id': payoutId,
                        'paidDate': date,
                        'paymentStatus': 'Success',
                        'withdrawMethod': payoutMethod,
                        'pixKey': pixKey,
                        'pixKeyType': pixKeyType,
                        'currency': 'BRL',
                        'note':''
                    }).then(function () {

                        price = remaining - amount;

                        database.collection('users').where("vendorID", "==", vendorID).where('role','==','vendor').get().then(function (snapshotss) {
                            if (snapshotss.docs.length) {
                                userdata = snapshotss.docs[0].data();
                                database.collection('users').doc(userdata.id).update({'wallet_amount': price}).then(async function (result) {
                                    await upsertWithdrawMethodPix(database, userdata.id, pixKeyType, pixKey);
                                    if (currencyAtRight) {
                                        amount = parseInt(amount).toFixed(decimal_degits) + "" + currentCurrency;
                                    } else {
                                        amount = currentCurrency + "" + parseInt(amount).toFixed(decimal_degits);
                                    }

                                    var formattedDate = new Date();
                                    var month = formattedDate.getMonth() + 1;
                                    var day = formattedDate.getDate();
                                    var year = formattedDate.getFullYear();

                                    month = month < 10 ? '0' + month : month;
                                    day = day < 10 ? '0' + day : day;

                                    formattedDate = day + '-' + month + '-' + year;

                                    var subject = emailTemplatesData.subject;
                                    subject = subject.replace(/{userid}/g, vendorUserId);

                                    emailTemplatesData.subject = subject;

                                    var message = emailTemplatesData.message;
                                    message = message.replace(/{userid}/g, vendorUserId);
                                    message = message.replace(/{date}/g, formattedDate);
                                    message = message.replace(/{amount}/g, amount);
                                    message = message.replace(/{payoutrequestid}/g, payoutId);
                                    message = message.replace(/{username}/g, userName);
                                    message = message.replace(/{usercontactinfo}/g, userContact);

                                    emailTemplatesData.message = message;

                                    var url = "{{url('send-email')}}";
                                    if(vendorEmail != '' && vendorEmail != null){
                                    var sendEmailStatus = await sendEmail(url, emailTemplatesData.subject, emailTemplatesData.message, [adminEmail, vendorEmail]);

                                    if (sendEmailStatus) {
                                        <?php if ($id == '') { ?>
                                        window.location.href = "{{route("payoutRequests.vendor.disbursement")}}";
                                        <?php } else { ?>
                                        window.location.href = '{{route('vendors.payout',$id)}}';
                                        <?php } ?>
                                     }
                                    }else{
                                        <?php if ($id == '') { ?>
                                        window.location.href = "{{route("payoutRequests.vendor.disbursement")}}";
                                        <?php } else { ?>
                                        window.location.href = '{{route('vendors.payout',$id)}}';
                                        <?php } ?>
                                    }

                                });

                            }
                        });

                    })

                } else {

                    $(".error_top").show();

                    $(".error_top").html("");

                    $(window).scrollTop(0);

                    $(".error_top").append("<p>{{trans('lang.please_enter_details')}}</p>");

                }

            } else {

                $(".error_top").show();

                $(window).scrollTop(0);

                $(".error_top").html("");

                $(".error_top").append("<p>{{trans('lang.vendor_insufficient_payment_error')}}</p>");

            }

        })

    })

    async function remainingPrice(vendorID) {
        var remaining = 0;

        await database.collection('users').where("vendorID", "==", vendorID).where('role','==','vendor').get().then(async function (snapshotss) {
            if (snapshotss.docs.length) {
                userdata = snapshotss.docs[0].data();
                if (isNaN(userdata.wallet_amount) || userdata.wallet_amount == undefined) {
                    remaining = 0;
                } else {
                    remaining = userdata.wallet_amount;
                }

            }
        });
        return remaining;
    }

    async function getVendorId(vendorUser) {
        var userId = '';

        await database.collection('vendors').where('id', "==", vendorUser).get().then(async function (vendorSnapshots) {
            var vendorData = vendorSnapshots.docs[0].data();
            userId = vendorData.author;

        });

        return userId;
    }

    async function getVendorEmail(vendorUser) {
        var userEmail = '';

        await database.collection('users').where('vendorID', "==", vendorUser).where('role','==','vendor').get().then(async function (vendorSnapshots) {

            if (vendorSnapshots.docs[0]) {
                var vendorData = vendorSnapshots.docs[0].data();
                userEmail = vendorData.email;

                userName = vendorData.firstName + " " + vendorData.lastName;
                userContact = vendorData.phoneNumber;
            }

        });

        return userEmail;
    }

</script>


@endsection