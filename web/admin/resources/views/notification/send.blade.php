@extends('layouts.app')

@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{trans('lang.send_notification')}}</h3>
            </div>

            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{trans('lang.dashboard')}}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('notification') }}">{{trans('lang.send_notification')}}</a>
                    </li>
                    <li class="breadcrumb-item active">{{trans('lang.notification')}}</li>
                </ol>
            </div>

        </div>
        <div>

            <div class="card-body">

                <div class="error_top text-danger font-weight-bold" style="display:none"></div>

                <div class="success_top text-success font-weight-bold" style="display:none"></div>

                <div class="row vendor_payout_create">

                    <div class="vendor_payout_create-inner">

                        <fieldset>
                            <legend>{{trans('lang.notification')}}</legend>


                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{trans('lang.notification_subject')}}</label>
                                <div class="col-7">
                                    <input type="text" class="form-control" id="subject">
                                </div>
                            </div>

                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{trans('lang.notification_message')}}</label>
                                <div class="col-7">
                                    <textarea class="form-control" id="message"></textarea>
                                </div>
                            </div>

                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{trans('lang.notification_audience')}}</label>
                                <div class="col-7">
                                    <select id="audience" class="form-control">
                                        <option value="all">{{trans('lang.notification_audience_all')}}</option>
                                        <option value="role">{{trans('lang.notification_audience_role')}}</option>
                                        <option value="topic">{{trans('lang.notification_audience_topic')}}</option>
                                        <option value="user">{{trans('lang.notification_audience_user')}}</option>
                                    </select>
                                    <small class="form-text text-muted">{{trans('lang.notification_audience_help')}}</small>
                                </div>
                            </div>

                            <div class="form-group row width-100 audience-role">
                                <label class="col-3 control-label">{{trans('lang.notification_send_to')}}</label>
                                <div class="col-7">
                                    <select id="role" class="form-control">
                                        <option value="vendor">{{trans('lang.vendor')}}</option>
                                        <option value="customer">{{trans('lang.customer')}}</option>
                                        <option value="driver">{{trans('lang.driver')}}</option>
                                        <option value="provider">{{trans('lang.provider')}}</option>
                                        <option value="worker">{{trans('lang.worker')}}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row width-100 audience-topic" style="display:none">
                                <label class="col-3 control-label">{{trans('lang.notification_topic_name')}}</label>
                                <div class="col-7">
                                    <input type="text" class="form-control" id="topic" placeholder="customer">
                                </div>
                            </div>

                            <div class="form-group row width-100 audience-user" style="display:none">
                                <label class="col-3 control-label">{{trans('lang.notification_user_search')}}</label>
                                <div class="col-7">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="user_query" placeholder="e-mail, telefone ou ID">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-secondary" id="user_search_btn">{{trans('lang.search')}}</button>
                                        </div>
                                    </div>
                                    <div id="user_search_result" class="mt-2 small"></div>
                                </div>
                            </div>

                            <div class="form-group row width-100">
                                <label class="col-3 control-label"></label>
                                <div class="col-7">
                                    <p id="recipient_hint" class="mb-0 text-muted"></p>
                                </div>
                            </div>
                        </fieldset>
                    </div>

                </div>

            </div>
            <div class="form-group col-12 text-center btm-btn">
                <button type="button" class="btn btn-primary save-form-btn"><i
                            class="fa fa-save"></i> {{ trans('lang.send')}}</button>
                <a href="{{url('/notification')}}" class="btn btn-default"><i
                            class="fa fa-undo"></i>{{ trans('lang.cancel')}}</a>
            </div>

        </div>

@endsection

@section('scripts')

<script type="text/javascript">

var id = "<?php echo $id;?>";
var database = firebase.firestore();
var selectedUserToken = '';
var selectedUserLabel = '';

function showError(text) {
    $(".success_top").hide();
    $(".error_top").show().empty().append($("<p>").text(text));
    window.scrollTo(0, 0);
}

function showSuccess(text) {
    $(".error_top").hide();
    $(".success_top").show().empty().append($("<p>").text(text));
    window.scrollTo(0, 0);
}

function toggleAudienceFields() {
    var audience = $("#audience").val();
    $(".audience-role").toggle(audience === "role");
    $(".audience-topic").toggle(audience === "topic");
    $(".audience-user").toggle(audience === "user");
    if (audience !== "user") {
        selectedUserToken = '';
        selectedUserLabel = '';
        $("#user_search_result").text("");
    }
    $("#recipient_hint").text("");
}

async function findUser(query) {
    query = (query || "").trim();
    if (!query) {
        return null;
    }
    var byId = await database.collection("users").doc(query).get();
    if (byId.exists) {
        return Object.assign({ id: byId.id }, byId.data());
    }
    var fields = ["email", "phoneNumber"];
    for (var i = 0; i < fields.length; i++) {
        var snap = await database.collection("users").where(fields[i], "==", query).limit(1).get();
        if (!snap.empty) {
            return Object.assign({ id: snap.docs[0].id }, snap.docs[0].data());
        }
    }
    return null;
}

$(document).ready(function () {
    toggleAudienceFields();
    $("#audience").on("change", toggleAudienceFields);

    $("#user_search_btn").on("click", async function () {
        var query = $("#user_query").val();
        selectedUserToken = "";
        selectedUserLabel = "";
        $("#user_search_result").text("{{trans('lang.notification_user_searching')}}");
        try {
            var user = await findUser(query);
            if (!user) {
                $("#user_search_result").text("{{trans('lang.notification_user_not_found')}}");
                return;
            }
            var name = [user.firstName, user.lastName].filter(Boolean).join(" ") || user.email || user.id;
            var token = ((user.fcmToken || "") + "").trim();
            selectedUserLabel = name + " (" + (user.role || "-") + ")";
            if (!token) {
                $("#user_search_result").text(selectedUserLabel + " — {{trans('lang.notification_user_token_missing')}}");
                return;
            }
            selectedUserToken = token;
            $("#user_search_result").text(selectedUserLabel + " — token FCM ok");
        } catch (err) {
            $("#user_search_result").text("{{trans('lang.notification_user_search_failed')}}");
        }
    });

    $(".save-form-btn").click(async function () {
        $(".success_top").hide();
        $(".error_top").hide();

        var message = $("#message").val();
        var subject = $("#subject").val();
        var audience = $("#audience").val();
        var role = $("#role").val();
        var topic = ($("#topic").val() || "").trim();

        if (subject == "") {
            showError("{{trans('lang.please_enter_subject')}}");
            return false;
        }
        if (message == "") {
            showError("{{trans('lang.please_enter_message')}}");
            return false;
        }
        if (audience === "topic" && topic === "") {
            showError("{{trans('lang.notification_please_enter_topic')}}");
            return false;
        }
        if (audience === "user" && !selectedUserToken) {
            showError("{{trans('lang.notification_please_select_user')}}");
            return false;
        }

        jQuery("#data-table_processing").show();

        var tokens = [];
        if (audience === "user") {
            tokens = selectedUserToken ? [selectedUserToken] : [];
            $("#recipient_hint").text("{{trans('lang.notification_sending_user')}}");
        } else {
            $("#recipient_hint").text("{{trans('lang.notification_sending_topics')}}");
        }

        var chunks = tokens.length ? [tokens] : [[]];

        var totalSent = 0;
        var totalFailed = 0;
        var totalSkipped = 0;
        var lastMessage = "";
        var allErrors = [];

        function postChunk(chunk) {
            return $.ajax({
                method: "POST",
                dataType: "json",
                url: "<?php echo route('broadcastnotification'); ?>",
                data: {
                    subject: subject,
                    message: message,
                    audience: audience,
                    role: role,
                    topic: topic,
                    tokens_json: JSON.stringify(chunk),
                    _token: "<?php echo csrf_token() ?>"
                }
            });
        }

        (async function () {
            try {
                for (var c = 0; c < chunks.length; c++) {
                    var response = await postChunk(chunks[c]);
                    totalSent += response.sent || 0;
                    totalFailed += response.failed || 0;
                    totalSkipped += response.skipped || 0;
                    lastMessage = response.message || lastMessage;
                    if (response.errors && response.errors.length) {
                        allErrors = allErrors.concat(response.errors);
                    }
                }
                jQuery("#data-table_processing").hide();
                if (totalSent > 0 && totalFailed === 0) {
                    var nid = database.collection("tmp").doc().id;
                    database.collection("notifications").doc(nid).set({
                        id: nid,
                        message: message,
                        subject: subject,
                        role: role,
                        audience: audience,
                        topic: topic,
                        sent: totalSent,
                        failed: totalFailed,
                        createdAt: firebase.firestore.FieldValue.serverTimestamp()
                    }).catch(function () {});
                    showSuccess(lastMessage || "{{trans('lang.notification_send_success')}}");
                    setTimeout(function () {
                        window.location.href = "{{ route('notification')}}";
                    }, 3000);
                } else {
                    var failMsg = lastMessage || "{{trans('lang.notification_send_failed')}}";
                    if (allErrors.length) {
                        failMsg += " " + allErrors.slice(0, 3).join(" ");
                    }
                    showError(failMsg);
                }
            } catch (xhr) {
                jQuery("#data-table_processing").hide();
                var msg = "{{trans('lang.notification_send_failed')}}";
                if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                } else if (xhr && xhr.status) {
                    msg = msg + " (HTTP " + xhr.status + ")";
                }
                showError(msg);
            }
        })();
    });
});

</script>

@endsection
