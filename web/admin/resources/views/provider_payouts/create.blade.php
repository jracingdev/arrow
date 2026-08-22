@extends('layouts.app')

@section('content')

<?php if ($id == 'create') {

    $id = '';
} ?>

<div class="page-wrapper">

    <div class="row page-titles">


        <div class="col-md-5 align-self-center">

            <h3 class="text-themecolor">{{trans('lang.provider_payout_plural')}}</h3>

        </div>

        <div class="col-md-7 align-self-center">

            <ol class="breadcrumb">

                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{trans('lang.dashboard')}}</a></li>

                <?php if ($id != '') { ?>

                    <li class="breadcrumb-item"><a href="{{ route('providerPayouts.payout', ['id' => $id]) }}">{{trans('lang.provider_payout_table')}}</a>
                    </li>

                <?php } ?>

                <li class="breadcrumb-item">{{trans('lang.provider_payout_create')}}</li>

            </ol>

        </div>

    </div>


    <div class="card-body">

        <div class="error_top"></div>

        <div class="row vendor_payout_create">

            <div class="vendor_payout_create-inner">

                <fieldset>

                    <legend>{{trans('lang.provider_payout_create')}}</legend>

                    <?php if ($id == '') { ?>

                        <div class="form-group row width-100">

                            <label class="col-3 control-label">{{ trans('lang.provider')}}</label>

                            <div class="col-7 select2-container-full">

                                <select id="select_provider" class="form-control">

                                    <option value="">{{ trans('lang.select_provider') }}</option>

                                </select>

                                <div class="form-text text-muted">

                                    {{ trans("lang.select_provider") }}

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

            <a href="{{route('providerPayouts.payout',$id)}}" class="btn btn-default"><i class="fa fa-undo"></i>{{trans('lang.cancel')}}</a>

        <?php } else { ?>

            <a href="{!! route('providerPayouts') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{trans('lang.cancel')}}</a>

        <?php } ?>

    </div>

</div>

</div>

</div>


@endsection

@section('scripts')

<script type="text/javascript">
    var providers = [];

    var database = firebase.firestore();

    var email_templates = database.collection('email_templates').where('type', '==', 'payout_request');

    var emailTemplatesData = null;

    var adminEmail = '';

    var emailSetting = database.collection('settings').doc('emailSetting');

    var userName = '';
    var userContact = '';

    var currentCurrency = '';
    var currencyAtRight = false;
    var decimal_degits = 0;
    var section_id = getCookie('section_id');
    var refCurrency = database.collection('currencies').where('isActive', '==', true);
    refCurrency.get().then(async function(snapshots) {
        var currencyData = snapshots.docs[0].data();
        currentCurrency = currencyData.symbol;
        currencyAtRight = currencyData.symbolAtRight;
        if (currencyData.decimal_degits) {
            decimal_degits = currencyData.decimal_degits;
        }
    });

    $(document).ready(function() {

        $("#data-table_processing").show();

        email_templates.get().then(async function(snapshots) {
            emailTemplatesData = snapshots.docs[0].data();

        });


        emailSetting.get().then(async function(snapshots) {
            var emailSettingData = snapshots.data();

            adminEmail = emailSettingData.userName;
        });

        database.collection('users').where('role', '==', 'provider').where('section_id','==',section_id).get().then(async function(snapshots) {
            snapshots.docs.forEach((listval) => {
                var data = listval.data();
                providers.push(data);
                $('#select_provider').append($("<option></option>")
                    .attr("value", data.id)
                    .text(data.firstName + ' ' + data.lastName));

            })
            $('#select_provider').select2();
            $('#select_provider').on('change', function() {
                loadProviderPix($(this).val());
            });
        });

        async function loadProviderPix(providerId) {
            if (!providerId) {
                return;
            }
            var userSnap = await database.collection('users').doc(providerId).get();
            var user = userSnap.exists ? userSnap.data() : null;
            if (!user) {
                var byField = await database.collection('users').where('id', '==', providerId).get();
                if (!byField.empty) {
                    user = byField.docs[0].data();
                }
            }
            if (user && user.userBankDetails) {
                fillPixDetails(user.userBankDetails);
            }
            var methodSnap = await database.collection('withdraw_method').where('userId', '==', providerId).get();
            if (!methodSnap.empty) {
                var method = methodSnap.docs[0].data();
                if (method.pix) {
                    fillPixDetails({ pixKey: method.pix.chave, pixKeyType: method.pix.tipo });
                }
            }
        }

        <?php if ($id != '') { ?>
        loadProviderPix("<?php echo $id; ?>");
        <?php } ?>

        $("#data-table_processing").hide();

        var payoutId = "<?php echo uniqid(); ?>";

        $(".save-form-btn").click(async function() {

            <?php if ($id == '') { ?>

                var ProviderID = $("#select_provider").val();

            <?php } else { ?>

                var ProviderID = "<?php echo $id; ?>";

            <?php } ?>

            var providerEmail = await getProviderEmail(ProviderID);

            var remaining = await remainingPrice(ProviderID);

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

                if (ProviderID != '' && $(".payout_amount").val() != '') {

                    database.collection('payouts').doc(payoutId).set({
                        'vendorID': ProviderID,
                        'amount': amount,
                        'adminNote': note,
                        'id': payoutId,
                        'paidDate': date,
                        'paymentStatus': 'Success',
                        'role': 'provider',
                        'withdrawMethod': payoutMethod,
                        'pixKey': pixKey,
                        'pixKeyType': pixKeyType,
                        'currency': 'BRL'
                    }).then(function() {

                        price = remaining - amount;

                        database.collection('users').where("id", "==", ProviderID).get().then(function(snapshotss) {
                            if (snapshotss.docs.length) {
                                userdata = snapshotss.docs[0].data();
                                database.collection('users').doc(userdata.id).update({
                                    'wallet_amount': price
                                }).then(async function(result) {
                                    await upsertWithdrawMethodPix(database, ProviderID, pixKeyType, pixKey);
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
                                    subject = subject.replace(/{userid}/g, ProviderID);

                                    emailTemplatesData.subject = subject;

                                    var message = emailTemplatesData.message;
                                    message = message.replace(/{userid}/g, ProviderID);
                                    message = message.replace(/{date}/g, formattedDate);
                                    message = message.replace(/{amount}/g, amount);
                                    message = message.replace(/{payoutrequestid}/g, payoutId);
                                    message = message.replace(/{username}/g, userName);
                                    message = message.replace(/{usercontactinfo}/g, userContact);

                                    emailTemplatesData.message = message;

                                    var url = "{{route('send-email')}}";
                                    if (providerEmail != '' && providerEmail != null) {

                                        var sendEmailStatus = await sendEmail(url, emailTemplatesData.subject, emailTemplatesData.message, [adminEmail, providerEmail]);

                                        if (sendEmailStatus) {
                                            <?php if ($id != '') { ?>
                                                window.location.href = "{{route('providerPayouts.payout',$id)}}";
                                            <?php } else { ?>
                                                window.location.href = '{{ route("payoutRequests.providers.disbursement")}}';
                                            <?php } ?>
                                        }
                                    } else {
                                        <?php if ($id != '') { ?>
                                            window.location.href = "{{route('providerPayouts.payout',$id)}}";
                                        <?php } else { ?>
                                            window.location.href = '{{ route("payoutRequests.providers.disbursement")}}';
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

                $(".error_top").append("<p>{{trans('lang.insufficient_payment_error')}}</p>");

            }

        })

    })

    async function remainingPrice(providerId) {
        var remaining = 0;

        await database.collection('users').where("id", "==", providerId).get().then(async function(snapshotss) {
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

    async function getProviderEmail(providerUser) {
        var userEmail = '';
        await database.collection('users').where('id', "==", providerUser).get().then(async function(providerSnapshots) {
            if (providerSnapshots.docs[0]) {
                var providerData = providerSnapshots.docs[0].data();
                userEmail = providerData.email;
                userName = providerData.firstName + " " + providerData.lastName;
                userContact = providerData.phoneNumber;
            }
        });
        return userEmail;
    }
</script>


@endsection