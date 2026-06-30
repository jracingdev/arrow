@extends('layouts.app')

@section('content')
    <div class="page-wrapper">

        <div class="row page-titles">

            <div class="col-md-5 align-self-center">

                <h3 class="text-themecolor">{{ trans('lang.ondemand_plural') }} - {{ trans('lang.booking_plural') }}</h3>

            </div>

            <div class="col-md-7 align-self-center">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>

                    <li class="breadcrumb-item active">{{ trans('lang.booking_plural') }}</li>

                </ol>

            </div>

            <div>

            </div>

        </div>

        <div class="container-fluid">

            <div class="admin-top-section">

                <div class="row">

                    <div class="col-12">

                        @if ($id != '')
                            <div class="resttab-sec">

                                <div class="menu-tab tabDiv">

                                    <ul>

                                        <li><a href="{{ route('providers.view', $id) }}">{{ trans('lang.tab_basic') }}</a>

                                        </li>

                                        <li><a href="{{ route('ondemand.services.index', $id) }}">{{ trans('lang.services') }}</a></li>

                                        <li>

                                        <li><a href="{{ route('ondemand.workers.index', $id) }}">{{ trans('lang.workers') }}</a></li>

                                        <li>

                                        <li class="active"><a href="{{ route('ondemand.bookings.index', $id) }}">{{ trans('lang.booking_plural') }}</a></li>

                                        <li>

                                        <li><a href="{{ route('ondemand.coupons', $id) }}">{{ trans('lang.coupon_plural') }}</a></li>

                                        <li>

                                            <a href="{{ route('providerPayouts.payout', $id) }}">{{ trans('lang.tab_payouts') }}</a>

                                        </li>

                                        <li>

                                            <a href="{{ route('payoutRequests.providers', $id) }}">{{ trans('lang.tab_payout_request') }}</a>

                                        </li>

                                        <li>

                                            <a href="{{ route('users.walletstransaction', $id) }}" class="wallet_transaction">{{ trans('lang.wallet_transaction') }}</a>

                                        </li>

                                        <?php
                                        
                                        $subscription = route('subscription.subscriptionPlanHistory', ':id');
                                        
                                        $subscription = str_replace(':id', 'providerID=' . $id, $subscription);
                                        
                                        ?>

                                        <li>

                                            <a href="{{ $subscription }}">{{ trans('lang.subscription_history') }}</a>

                                        </li>

                                    </ul>

                                </div>

                            </div>
                        @endif

                        <div class="d-flex top-title-section pb-4 justify-content-between">

                            <div class="d-flex top-title-left align-self-center">

                                <span class="icon mr-3"><img src="{{ asset('images/booking.png') }}"></span>

                                <h3 class="mb-0">{{ trans('lang.booking_plural') }}</h3>

                                <span class="counter ml-3 total_count"></span>

                            </div>

                            <div class="d-flex top-title-right align-self-center">

                                <div class="select-box pl-3">

                                    <select class="form-control status_selector " onchange="filterData()">

                                        <option value="" selected>{{ trans('lang.status') }}</option>

                                        <option value="Order Placed">{{ trans('lang.order_placed') }}</option>

                                        <option value="Order Accepted">{{ trans('lang.order_accepted') }}</option>

                                        <option value="Order Cancelled">{{ trans('lang.order_rejected') }}</option>

                                        <option value="Order Ongoing">{{ trans('lang.order_ongoing') }}</option>

                                        <option value="Order Completed">{{ trans('lang.order_completed') }}</option>

                                    </select>

                                </div>

                                <div class="select-box pl-3">

                                    <div id="daterange"><i class="fa fa-calendar"></i>&nbsp;

                                        <span></span>&nbsp; <i class="fa fa-caret-down"></i>

                                    </div>

                                </div>

                                <div class="select-box pl-3">

                                    <select id="section_id" class="form-control section_selector" style="width:100%" onchange="clickLink(this.value)">

                                        <option value="">{{ trans('lang.select') }} {{ trans('lang.section_plural') }}

                                        </option>

                                    </select>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="row">

                    <div class="col-12">

                        <div class="card border">

                            <div class="card-body">

                                <div class="row">

                                    <div class="col-md-3">

                                        <div class="card card-box-with-icon bg--1">

                                            <div class="card-body d-flex justify-content-between align-items-center">

                                                <div class="card-box-with-content">

                                                    <h4 class="text-dark-2 mb-1 h4 order_count" id="order_count"></h4>

                                                    <p class="mb-0 small text-dark-2">{{ trans('lang.dashboard_total_orders') }}</p>

                                                </div>

                                                <span class="box-icon ab"><img src="{{ asset('images/total_orders.png') }}"></span>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-md-3">

                                        <div class="card card-box-with-icon bg--5">

                                            <div class="card-body d-flex justify-content-between align-items-center">

                                                <div class="card-box-with-content">

                                                    <h4 class="text-dark-2 mb-1 h4 placed_count" id="placed_count"></h4>

                                                    <p class="mb-0 small text-dark-2">{{ trans('lang.dashboard_order_placed') }}</p>

                                                </div>

                                                <span class="box-icon ab"><img src="{{ asset('images/order_placed.png') }}"></span>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-md-3">

                                        <div class="card card-box-with-icon bg--6">

                                            <div class="card-body d-flex justify-content-between align-items-center">

                                                <div class="card-box-with-content">

                                                    <h4 class="text-dark-2 mb-1 h4 accepted_count" id="accepted_count"></h4>

                                                    <p class="mb-0 small text-dark-2">{{ trans('lang.dashboard_order_accepted') }}</p>

                                                </div>

                                                <span class="box-icon ab"><img src="{{ asset('images/order_accepted.png') }}"></span>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-md-3">

                                        <div class="card card-box-with-icon bg--8">

                                            <div class="card-body d-flex justify-content-between align-items-center">

                                                <div class="card-box-with-content">

                                                    <h4 class="text-dark-2 mb-1 h4 order_completed" id="order_completed"></h4>

                                                    <p class="mb-0 small text-dark-2">{{ trans('lang.dashboard_order_completed') }}</p>

                                                </div>

                                                <span class="box-icon ab"><img src="{{ asset('images/order_completed.png') }}"></span>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="table-list">

                <div class="row">

                    <div class="col-12">

                        <div class="card border">

                            <div class="card-header">

                                <ul class="nav nav-pills mb-3" role="tablist">

                                    <li class="nav-item">

                                        <a class="nav-link new_booking_list active" data-toggle="pill" href="#new_booking_list" role="tab">{{ trans('lang.new_bookings') }}</a>

                                    </li>

                                    <li class="nav-item">

                                        <a class="nav-link today_booking_list" data-toggle="pill" href="#today_booking_list" role="tab">{{ trans('lang.today') }}</a>

                                    </li>

                                    <li class="nav-item">

                                        <a class="nav-link upcoming_booking_list" data-toggle="pill" href="#upcoming_booking_list" role="tab">{{ trans('lang.upcoming') }}</a>

                                    </li>

                                    <li class="nav-item">

                                        <a class="nav-link completed_booking_list" data-toggle="pill" href="#completed_booking_list" role="tab">{{ trans('lang.completed') }}</a>

                                    </li>

                                    <li class="nav-item">

                                        <a class="nav-link canceled_booking_list" data-toggle="pill" href="#canceled_booking_list" role="tab">{{ trans('lang.canceled') }}</a>

                                    </li>

                                </ul>

                            </div>

                            <div class="card-header d-flex justify-content-between align-items-center border-0">

                                <div class="card-header-title">

                                    <h3 class="text-dark-2 mb-2 h4">{{ trans('lang.booking_table') }}</h3>

                                    <p class="mb-0 text-dark-2">{{ trans('lang.booking_table_text') }}</p>

                                </div>

                            </div>

                            <div class="card-body">

                                <div class="table-responsive m-t-10">

                                    <div class="tab-content">

                                        <div class="tab-pane active" id="new_booking_list" role="tabpanel">

                                            <div class="table-responsive">

                                                <div class="dropdown text-right">

                                                    <button class="btn dropdown-toggle custom-export-btn" type="button" id="exportDropdown" data-toggle="dropdown" aria-expanded="false">

                                                        <i class="mdi mdi-cloud-download"></i> {{ trans('lang.export_as') }}

                                                    </button>

                                                    <ul class="dropdown-menu " aria-labelledby="exportDropdown">

                                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportBookingData('new_bookings','excel')">{{ trans('lang.export_excel') }}</a></li>

                                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportBookingData('new_bookings','pdf')">{{ trans('lang.export_pdf') }}</a></li>

                                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportBookingData('new_bookings','csv')">{{ trans('lang.export_csv') }}</a></li>

                                                    </ul>

                                                </div>

                                                <table id="newBookingTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">

                                                    <thead>

                                                        <tr>

                                                            <?php if (in_array('ondemand.bookings.delete', json_decode(@session('user_permissions')))) { ?>

                                                            <th class="delete-all"><input type="checkbox" id="del_new"><label class="col-3 control-label" for="del_new"><a id="deleteAllNew" class="delete-btn" href="javascript:void(0)"><i class="fa fa-trash"></i> {{ trans('lang.all') }}</a></label></th>

                                                            <?php } ?>

                                                            <th>{{ trans('lang.booking_id') }}</th>

                                                            <th>{{ trans('lang.order_user_id') }}</th>

                                                            <th>{{ trans('lang.status') }}</th>

                                                            <th>{{ trans('lang.amount') }}</th>

                                                            <th>{{ trans('lang.booking_date') }}</th>

                                                            <th>{{ trans('lang.created_at') }}</th>

                                                            <th>{{ trans('lang.section') }}</th>

                                                            <th>{{ trans('lang.actions') }}</th>

                                                        </tr>

                                                    </thead>

                                                    <tbody id="today_bookings_row"></tbody>

                                                </table>

                                            </div>

                                        </div>

                                        <div class="tab-pane" id="today_booking_list" role="tabpanel">

                                            <div class="table-responsive">

                                                <div class="dropdown text-right">

                                                    <button class="btn dropdown-toggle custom-export-btn" type="button" id="exportDropdown" data-toggle="dropdown" aria-expanded="false">

                                                        <i class="mdi mdi-cloud-download"></i> {{ trans('lang.export_as') }}

                                                    </button>

                                                    <ul class="dropdown-menu " aria-labelledby="exportDropdown">

                                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportBookingData('today_bookings','excel')">{{ trans('lang.export_excel') }}</a></li>

                                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportBookingData('today_bookings','pdf')">{{ trans('lang.export_pdf') }}</a></li>

                                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportBookingData('today_bookings','csv')">{{ trans('lang.export_csv') }}</a></li>

                                                    </ul>

                                                </div>

                                                <table id="todayBookingTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">

                                                    <thead>

                                                        <tr>

                                                            <?php if (in_array('ondemand.bookings.delete', json_decode(@session('user_permissions')))) { ?>

                                                            <th class="delete-all"><input type="checkbox" id="del_today"><label class="col-3 control-label" for="del_today"><a id="deleteAllToday" class="delete-btn" href="javascript:void(0)"><i class="fa fa-trash"></i> {{ trans('lang.all') }}</a></label></th>

                                                            <?php } ?>

                                                            <th>{{ trans('lang.booking_id') }}</th>

                                                            <th>{{ trans('lang.order_user_id') }}</th>

                                                            <th>{{ trans('lang.status') }}</th>

                                                            <th>{{ trans('lang.amount') }}</th>

                                                            <th>{{ trans('lang.booking_date') }}</th>

                                                            <th>{{ trans('lang.created_at') }}</th>

                                                            <th>{{ trans('lang.section') }}</th>

                                                            <th>{{ trans('lang.actions') }}</th>

                                                        </tr>

                                                    </thead>

                                                    <tbody id="today_bookings_row"></tbody>

                                                </table>

                                            </div>

                                        </div>

                                        <div class="tab-pane" id="upcoming_booking_list" role="tabpanel">

                                            <div class="table-responsive">

                                                <div class="dropdown text-right">

                                                    <button class="btn dropdown-toggle custom-export-btn" type="button" id="exportDropdown" data-toggle="dropdown" aria-expanded="false">

                                                        <i class="mdi mdi-cloud-download"></i> {{ trans('lang.export_as') }}

                                                    </button>

                                                    <ul class="dropdown-menu " aria-labelledby="exportDropdown">

                                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportBookingData('upcoming_bookings','excel')">{{ trans('lang.export_excel') }}</a></li>

                                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportBookingData('upcoming_bookings','pdf')">{{ trans('lang.export_pdf') }}</a></li>

                                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportBookingData('upcoming_bookings','csv')">{{ trans('lang.export_csv') }}</a></li>

                                                    </ul>

                                                </div>

                                                <table id="upcomingBookingTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">

                                                    <thead>

                                                        <tr>

                                                            <?php if (in_array('ondemand.bookings.delete', json_decode(@session('user_permissions')))) { ?>

                                                            <th class="delete-all"><input type="checkbox" id="del_upcoming"><label class="col-3 control-label" for="del_upcoming"><a id="deleteAllUpcoming" class="delete-btn" href="javascript:void(0)"><i class="fa fa-trash"></i> {{ trans('lang.all') }}</a></label></th>

                                                            <?php } ?>

                                                            <th>{{ trans('lang.booking_id') }}</th>

                                                            <th>{{ trans('lang.order_user_id') }}</th>

                                                            <th>{{ trans('lang.status') }}</th>

                                                            <th>{{ trans('lang.amount') }}</th>

                                                            <th>{{ trans('lang.booking_date') }}</th>

                                                            <th>{{ trans('lang.created_at') }}</th>

                                                            <th>{{ trans('lang.section') }}</th>

                                                            <th>{{ trans('lang.actions') }}</th>

                                                        </tr>

                                                    </thead>

                                                    <tbody id="upcoming_bookings_row"></tbody>

                                                </table>

                                            </div>

                                        </div>

                                        <div class="tab-pane" id="completed_booking_list" role="tabpanel">

                                            <div class="table-responsive">

                                                <div class="dropdown text-right">

                                                    <button class="btn dropdown-toggle custom-export-btn" type="button" id="exportDropdown" data-toggle="dropdown" aria-expanded="false">

                                                        <i class="mdi mdi-cloud-download"></i> {{ trans('lang.export_as') }}

                                                    </button>

                                                    <ul class="dropdown-menu " aria-labelledby="exportDropdown">

                                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportBookingData('completed_bookings','excel')">{{ trans('lang.export_excel') }}</a></li>

                                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportBookingData('completed_bookings','pdf')">{{ trans('lang.export_pdf') }}</a></li>

                                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportBookingData('completed_bookings','csv')">{{ trans('lang.export_csv') }}</a></li>

                                                    </ul>

                                                </div>

                                                <table id="completedBookingTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">

                                                    <thead>

                                                        <tr>

                                                            <?php if (in_array('ondemand.bookings.delete', json_decode(@session('user_permissions')))) { ?>

                                                            <th class="delete-all"><input type="checkbox" id="del_completed"><label class="col-3 control-label" for="del_completed"><a id="deleteAllCompleted" class="delete-btn" href="javascript:void(0)"><i class="fa fa-trash"></i> {{ trans('lang.all') }}</a></label></th>

                                                            <?php } ?>

                                                            <th>{{ trans('lang.booking_id') }}</th>

                                                            <th>{{ trans('lang.order_user_id') }}</th>

                                                            <th>{{ trans('lang.status') }}</th>

                                                            <th>{{ trans('lang.amount') }}</th>

                                                            <th>{{ trans('lang.booking_date') }}</th>

                                                            <th>{{ trans('lang.created_at') }}</th>

                                                            <th>{{ trans('lang.section') }}</th>

                                                            <th>{{ trans('lang.actions') }}</th>

                                                        </tr>

                                                    </thead>

                                                    <tbody id="completed_bookings_row"></tbody>

                                                </table>

                                            </div>

                                        </div>

                                        <div class="tab-pane" id="canceled_booking_list" role="tabpanel">

                                            <div class="table-responsive">

                                                <div class="dropdown text-right">

                                                    <button class="btn dropdown-toggle custom-export-btn" type="button" id="exportDropdown" data-toggle="dropdown" aria-expanded="false">

                                                        <i class="mdi mdi-cloud-download"></i> {{ trans('lang.export_as') }}

                                                    </button>

                                                    <ul class="dropdown-menu " aria-labelledby="exportDropdown">

                                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportBookingData('cancel_bookings','excel')">{{ trans('lang.export_excel') }}</a></li>

                                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportBookingData('cancel_bookings','pdf')">{{ trans('lang.export_pdf') }}</a></li>

                                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportBookingData('cancel_bookings','csv')">{{ trans('lang.export_csv') }}</a></li>

                                                    </ul>

                                                </div>

                                                <table id="cancelBookingTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">

                                                    <thead>

                                                        <tr>

                                                            <?php if (in_array('ondemand.bookings.delete', json_decode(@session('user_permissions')))) { ?>

                                                            <th class="delete-all"><input type="checkbox" id="del_canceled"><label class="col-3 control-label" for="del_canceled"><a id="deleteAllCancel" class="delete-btn" href="javascript:void(0)"><i class="fa fa-trash"></i> {{ trans('lang.all') }}</a></label></th>

                                                            <?php } ?>

                                                            <th>{{ trans('lang.booking_id') }}</th>

                                                            <th>{{ trans('lang.order_user_id') }}</th>

                                                            <th>{{ trans('lang.status') }}</th>

                                                            <th>{{ trans('lang.amount') }}</th>

                                                            <th>{{ trans('lang.booking_date') }}</th>

                                                            <th>{{ trans('lang.created_at') }}</th>

                                                            <th>{{ trans('lang.section') }}</th>

                                                            <th>{{ trans('lang.actions') }}</th>

                                                        </tr>

                                                    </thead>

                                                    <tbody id="cancel_bookings_row"></tbody>

                                                </table>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>
        @endsection

        @section('scripts')
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.21/jspdf.plugin.autotable.min.js"></script>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

            <script type="text/javascript">
                let filteredRecords = [];

                var user_permissions = '<?php echo @session('user_permissions'); ?>';

                user_permissions = JSON.parse(user_permissions);

                var checkDeletePermission = false;
                var tableName = "dataTable";

                var refVar = "";
                if ($.inArray('ondemand.bookings.delete', user_permissions) >= 0) {

                    checkDeletePermission = true;

                }

                $('.section_selector').select2({

                    placeholder: '{{ trans('lang.section') }}',

                    minimumResultsForSearch: Infinity,

                    allowClear: true

                });

                $('.status_selector').select2({

                    placeholder: '{{ trans('lang.status') }}',

                    minimumResultsForSearch: Infinity,

                    allowClear: true

                });

                $('select').on("select2:unselecting", function(e) {

                    var self = $(this);

                    setTimeout(function() {

                        self.select2('close');

                    }, 0);

                });



                function setDate() {

                    $('#daterange span').html('{{ trans('lang.select_range') }}');

                    $('#daterange').daterangepicker({

                        autoUpdateInput: false,

                    }, function(start, end) {

                        $('#daterange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                        //filterData();                     

                    });

                    $('#daterange').on('apply.daterangepicker', function(ev, picker) {

                        $('#daterange span').html(picker.startDate.format('MMMM D, YYYY') + ' - ' + picker.endDate.format('MMMM D, YYYY'));
                        filterData();

                    });

                    $('#daterange').on('cancel.daterangepicker', function(ev, picker) {

                        $('#daterange span').html('{{ trans('lang.select_range') }}');
                        filterData();

                    });

                }

                setDate();

                //$('.status_selector,.filteredRecords').change(async function() {
                async function filterData() {

                    var status = $('.status_selector').val();

                    var daterangepicker = $('#daterange').data('daterangepicker');

                    ref = database.collection('provider_orders');

                    if (status != '') {

                        ref = ref.where('status', '==', status);

                    }

                    if ($('#daterange span').html() != '{{ trans('lang.select_range') }}' && daterangepicker) {

                        var from = moment(daterangepicker.startDate).toDate();

                        var to = moment(daterangepicker.endDate).toDate();

                        if (from && to) {

                            var fromDate = firebase.firestore.Timestamp.fromDate(new Date(from));

                            ref = ref.where('createdAt', '>=', fromDate);

                            var toDate = firebase.firestore.Timestamp.fromDate(new Date(to));

                            ref = ref.where('createdAt', '<=', toDate);

                        }

                    }

                    refVar = ref;

                    var table = $('#newBookingTable').DataTable();
                    table.destroy();
                    const tableName = '#newBookingTable';
                    mainDataTable(tableName, refVar);


                    // });
                }
                $('select').on("select2:unselecting", function(e) {

                    var self = $(this);

                    setTimeout(function() {

                        self.select2('close');

                    }, 0);

                });



                var database = firebase.firestore();

                var offest = 1;

                var pagesize = 10;



                var append_list = '';

                var user_number = [];

                var id = "{{ $id }}";

                var currentDateTime = new Date();

                var startOfToday = new Date(currentDateTime);

                startOfToday.setHours(0, 0, 0, 0);

                var endOfToday = new Date(currentDateTime);

                endOfToday.setHours(23, 59, 59, 999);

                var startTimestamp = firebase.firestore.Timestamp.fromDate(startOfToday);

                var endTimestamp = firebase.firestore.Timestamp.fromDate(endOfToday);

                if (id != '') {

                    var wallet_route = "{{ route('users.walletstransaction', 'id') }}";

                    $(".wallet_transaction").attr("href", wallet_route.replace('id', 'providerID=' + id));



                    $('.tabDiv').show();

                    var newBookingRef = database.collection('provider_orders').where('status', '==', 'Order Placed').where('provider.author', '==', id).orderBy('createdAt', 'desc');

                    var todayBookingRef = database.collection('provider_orders').where('newScheduleDateTime', '>=', startTimestamp).where('newScheduleDateTime', '<=', endTimestamp).where('status', 'in', ['Order Accepted', 'Order Assigned', 'Order Ongoing']).where('provider.author', '==', id);

                    var upcomingBookingRef = database.collection('provider_orders').where('status', 'in', ['Order Accepted', 'Order Assigned']).where('newScheduleDateTime', '>=', endTimestamp).where('provider.author', '==', id);

                    var completedBookingRef = database.collection('provider_orders').where('status', '==', 'Order Completed').where('provider.author', '==', id).orderBy('createdAt', 'desc');

                    var cancelBookingRef = database.collection('provider_orders').where('status', 'in', ['Order Cancelled', 'Order Rejected']).where('provider.author', '==', id).orderBy('createdAt', 'desc');



                } else {

                    $('.tabDiv').hide();

                    var newBookingRef = database.collection('provider_orders').where('status', '==', 'Order Placed').orderBy('createdAt', 'desc');

                    var todayBookingRef = database.collection('provider_orders').where('newScheduleDateTime', '>=', startTimestamp).where('newScheduleDateTime', '<=', endTimestamp).where('status', 'in', ['Order Accepted', 'Order Assigned', 'Order Ongoing']);

                    var upcomingBookingRef = database.collection('provider_orders').where('status', 'in', ['Order Accepted', 'Order Assigned']).where('newScheduleDateTime', '>=', endTimestamp);

                    var completedBookingRef = database.collection('provider_orders').where('status', '==', 'Order Completed').orderBy('createdAt', 'desc');

                    var cancelBookingRef = database.collection('provider_orders').where('status', 'in', ['Order Cancelled', 'Order Rejected']).orderBy('createdAt', 'desc');

                }



                var section_id = '<?php if (@$_COOKIE['ondemand_section_id']) {
                    echo @$_COOKIE['ondemand_section_id'];
                } else {
                    echo '';
                } ?>';

                if (section_id != '') {

                    newBookingRef = newBookingRef.where('sectionId', '==', section_id);

                    todayBookingRef = todayBookingRef.where('sectionId', '==', section_id);

                    upcomingBookingRef = upcomingBookingRef.where('sectionId', '==', section_id);

                    completedBookingRef = completedBookingRef.where('sectionId', '==', section_id);

                    cancelBookingRef = cancelBookingRef.where('sectionId', '==', section_id);

                }

                var currentCurrency = '';

                var currencyAtRight = false;

                var decimal_degits = 0;



                var refCurrency = database.collection('currencies').where('isActive', '==', true);

                refCurrency.get().then(async function(snapshots) {

                    var currencyData = snapshots.docs[0].data();

                    currentCurrency = currencyData.symbol;

                    currencyAtRight = currencyData.symbolAtRight;

                    if (currencyData.decimal_degits) {

                        decimal_degits = currencyData.decimal_degits;

                    }

                });

                $(document).on('click', '.new_booking_list', function() {

                    getNewBookings();

                });

                $(document).on('click', '.today_booking_list', function() {

                    getTodayBookings();

                });

                $(document).on('click', '.upcoming_booking_list', function() {

                    getUpcomingBookings();

                });

                $(document).on('click', '.completed_booking_list', function() {

                    getCompletedBookings();

                });

                $(document).on('click', '.canceled_booking_list', function() {

                    getCancelBookings();

                });

                var orderStatus = '<?php if (isset($_GET['status'])) {
                    echo $_GET['status'];
                } else {
                    echo '';
                } ?>';



                $(document).ready(function() {

                    var refTotalOrder = database.collection('provider_orders');
                    if (id != '') {
                        refTotalOrder = refTotalOrder.where('provider.author', '==', id)
                    }
                    refTotalOrder.get().then((snapshot) => {

                        jQuery("#order_count").empty();

                        jQuery("#order_count").text(snapshot.docs.length);

                    });


                    var refPlacedOrder = database.collection('provider_orders').where('status', 'in', ["Order Placed"]);
                    if (id != '') {
                        refPlacedOrder = refPlacedOrder.where('provider.author', '==', id);
                    }
                    refPlacedOrder.get().then((snapshot) => {

                        jQuery("#placed_count").empty();

                        jQuery("#placed_count").text(snapshot.docs.length);

                    });


                    var refAcceptedOrder = database.collection('provider_orders').where('status', 'in', ["Order Accepted"]);
                    if (id != '') {
                        refAcceptedOrder = refAcceptedOrder.where('provider.author', '==', id);
                    }
                    refAcceptedOrder.get().then((snapshot) => {

                        jQuery("#accepted_count").empty();

                        jQuery("#accepted_count").text(snapshot.docs.length);

                    });


                    var refCompletedOrder = database.collection('provider_orders').where('status', 'in', ["Order Completed"]);
                    if (id != '') {
                        refCompletedOrder = refCompletedOrder.where('provider.author', '==', id);
                    }
                    refCompletedOrder.get().then((snapshot) => {

                        jQuery("#order_completed").empty();

                        jQuery("#order_completed").text(snapshot.docs.length);

                    });



                    $('.dt-button-collection').hide();





                    if (id != '') {

                        getProviderNameForFilter(id);

                    }

                    $(document.body).on('click', '.redirecttopage', function() {

                        var url = $(this).attr('data-url');

                        window.location.href = url;

                    });

                    database.collection('sections').where('serviceTypeFlag', '==', 'ondemand-service').get().then(async function(snapshots) {



                        snapshots.docs.forEach((listval) => {

                            var data = listval.data();

                            $('#section_id').append($("<option></option>")

                                .attr("value", data.id)

                                .text(data.name));



                        })



                        $('#section_id').val(section_id);

                    })



                    $('.new_booking_list').removeClass('active');

                    $('#new_booking_list').removeClass('active');



                    if (orderStatus == "order-placed") {

                        $('.new_booking_list').addClass('active');

                        $('#new_booking_list').addClass('active');

                        getNewBookings();



                    } else if (orderStatus == "order-today" || orderStatus == "order-ongoing") {

                        $('.today_booking_list').addClass('active');

                        $('#today_booking_list').addClass('active');

                        getTodayBookings();



                    } else if (orderStatus == "order-upcoming") {

                        $('.upcoming_booking_list').addClass('active');

                        $('#upcoming_booking_list').addClass('active');

                        getUpcomingBookings();



                    } else if (orderStatus == "order-completed") {

                        $('.completed_booking_list').addClass('active');

                        $('#completed_booking_list').addClass('active');

                        getCompletedBookings();



                    } else if (orderStatus == "order-canceled") {

                        $('.canceled_booking_list').addClass('active');

                        $('#canceled_booking_list').addClass('active');

                        getCancelBookings();



                    } else {

                        $('.new_booking_list').addClass('active');

                        $('#new_booking_list').addClass('active');

                        getNewBookings();

                    }







                });











                function getNewBookings() {

                    var table = $('#newBookingTable').DataTable();

                    table.destroy();

                    const tableName = '#newBookingTable';

                    var refVar = newBookingRef;

                    mainDataTable(tableName, refVar);

                }



                function getTodayBookings() {

                    var table = $('#todayBookingTable').DataTable();

                    table.destroy();

                    const tableName = '#todayBookingTable';

                    var refVar = todayBookingRef;

                    mainDataTable(tableName, refVar);

                }



                function getUpcomingBookings() {

                    var table = $('#upcomingBookingTable').DataTable();

                    table.destroy();

                    const tableName = '#upcomingBookingTable';

                    var refVar = upcomingBookingRef;

                    mainDataTable(tableName, refVar);

                }



                function getCompletedBookings() {

                    var table = $('#completedBookingTable').DataTable();

                    table.destroy();

                    const tableName = '#completedBookingTable';

                    var refVar = completedBookingRef;

                    mainDataTable(tableName, refVar);

                }



                function getCancelBookings() {

                    var table = $('#cancelBookingTable').DataTable();

                    table.destroy();

                    const tableName = '#cancelBookingTable';

                    var refVar = cancelBookingRef;

                    mainDataTable(tableName, refVar);

                }



                function mainDataTable(tableName, refVar) {

                    jQuery("#data-table_processing").show();



                    const table = $(tableName).DataTable({

                        pageLength: 10,

                        processing: false,

                        serverSide: true,

                        responsive: true,

                        ajax: async function(data, callback, settings) {

                            const start = data.start;

                            const length = data.length;

                            const searchValue = data.search.value.toLowerCase();

                            const orderColumnIndex = data.order[0].column;

                            const orderDirection = data.order[0].dir;



                            const orderableColumns = (checkDeletePermission == true) ? ['', 'id', 'authorName', 'status', 'price', 'bookingDateTime', 'createdAt', 'sectionName', ''] : ['id', 'authorName', 'status', 'price', 'bookingDateTime', 'createdAt', 'sectionName', ''];



                            const orderByField = orderableColumns[orderColumnIndex];



                            if (searchValue.length >= 3 || searchValue.length === 0) {

                                $('#data-table_processing').show();

                            }



                            try {

                                const querySnapshot = await refVar.get();

                                if (querySnapshot.empty) {

                                    $('.total_count').text(0);

                                    $('#data-table_processing').hide();

                                    callback({

                                        draw: data.draw,

                                        recordsTotal: 0,

                                        recordsFiltered: 0,

                                        data: []

                                    });

                                    return;

                                }



                                let records = [];

                                filteredRecords = [];



                                await Promise.all(querySnapshot.docs.map(async (doc) => {

                                    let childData = doc.data();
                                    childData.id = doc.id;



                                    var authorName = (childData.author != undefined) ? (childData.author.firstName + ' ' + childData.author.lastName) : '';

                                    childData.authorName = authorName ? authorName : '';



                                    var price = buildHTMLProductstotal(childData);

                                    if (childData.status != 'Order Completed' && childData.provider.priceUnit == 'Hourly') {

                                        var perHourPrice = parseFloat(childData.provider.price);

                                        if (childData.provider.disPrice != null && childData.provider.disPrice != undefined && childData.provider.disPrice != '' && childData.provider.disPrice != '0') {

                                            perHourPrice = parseFloat(childData.provider.disPrice)

                                        }

                                        if (currencyAtRight) {

                                            perHourPrice = perHourPrice.toFixed(decimal_degits) + "" + currentCurrency;

                                        } else {

                                            perHourPrice = currentCurrency + "" + perHourPrice.toFixed(decimal_degits);

                                        }

                                        price = perHourPrice + '/hr';

                                    }

                                    if (childData.hasOwnProperty("scheduleDateTime")) {

                                        childData.bookingDateTime = childData.scheduleDateTime;

                                    }

                                    if (childData.hasOwnProperty("newScheduleDateTime") && childData.newScheduleDateTime != null && childData.newScheduleDateTime != '') {

                                        childData.bookingDateTime = childData.newScheduleDateTime;

                                    }

                                    childData.price = price ? price : 0.00;

                                    const sectionName = await getSectionName(childData.sectionId);

                                    childData.sectionName = sectionName ? sectionName : '';

                                    if (searchValue) {

                                        var bookingDate = '';

                                        var bookingTime = '';

                                        if (childData.hasOwnProperty("scheduleDateTime")) {

                                            bookingDate = childData.scheduleDateTime.toDate().toDateString();

                                            bookingTime = childData.scheduleDateTime.toDate().toLocaleTimeString('en-US');

                                        }

                                        if (childData.hasOwnProperty("newScheduleDateTime") && childData.newScheduleDateTime != null && childData.newScheduleDateTime != '') {

                                            bookingDate = childData.newScheduleDateTime.toDate().toDateString();

                                            bookingTime = childData.newScheduleDateTime.toDate().toLocaleTimeString('en-US');

                                        }

                                        var bookingDateTime = bookingDate + ' ' + bookingTime;

                                        var date = '';

                                        var time = '';

                                        if (childData.hasOwnProperty("createdAt") && childData.createdAt != '') {

                                            try {

                                                date = childData.createdAt.toDate().toDateString();

                                                time = childData.createdAt.toDate().toLocaleTimeString('en-US');

                                            } catch (err) {



                                            }

                                        }

                                        var createdAt = date + ' ' + time;

                                        if (

                                            (childData.id && childData.id.toLowerCase().includes(searchValue)) ||

                                            (authorName && authorName.toLowerCase().includes(searchValue)) ||

                                            (childData.status && childData.status.toLowerCase().includes(searchValue)) ||

                                            (childData.price && childData.price.toLowerCase().includes(searchValue)) ||

                                            (sectionName && sectionName.toLowerCase().includes(searchValue)) ||

                                            (createdAt && createdAt.toString().toLowerCase().indexOf(searchValue) > -1) ||

                                            (bookingDateTime && bookingDateTime.toString().toLowerCase().indexOf(searchValue) > -1)

                                        ) {

                                            filteredRecords.push(childData);

                                        }

                                    } else {

                                        filteredRecords.push(childData);

                                    }

                                }));

                                filteredRecords.sort((a, b) => {

                                    let aValue = a[orderByField] ? a[orderByField].toString().toLowerCase().trim() : '';

                                    let bValue = b[orderByField] ? b[orderByField].toString().toLowerCase().trim() : '';

                                    if (orderByField === 'createdAt' && a[orderByField] != '' && b[orderByField] != '') {

                                        try {

                                            aValue = a[orderByField] ? new Date(a[orderByField].toDate()).getTime() : 0;

                                            bValue = b[orderByField] ? new Date(b[orderByField].toDate()).getTime() : 0;

                                        } catch (err) {}

                                    }

                                    if (orderByField === 'bookingDateTime' && a[orderByField] != '' && b[orderByField] != '') {

                                        try {

                                            aValue = a[orderByField] ? new Date(a[orderByField].toDate()).getTime() : 0;

                                            bValue = b[orderByField] ? new Date(b[orderByField].toDate()).getTime() : 0;

                                        } catch (err) {}

                                    }

                                    if (orderByField === 'price') {

                                        const parseAmount = (amountString) => {

                                            return parseFloat(amountString.replace(/[$,]/g, ''));

                                        };

                                        aValue = a[orderByField] ? parseAmount(a[orderByField]) : 0;

                                        bValue = b[orderByField] ? parseAmount(b[orderByField]) : 0;

                                    }

                                    if (orderDirection === 'asc') {

                                        return (aValue > bValue) ? 1 : -1;

                                    } else {

                                        return (aValue < bValue) ? 1 : -1;

                                    }

                                });



                                const totalRecords = filteredRecords.length;

                                $('.total_count').text(totalRecords);

                                const paginatedRecords = filteredRecords.slice(start, start + length);



                                const formattedRecords = await Promise.all(paginatedRecords.map(async (childData) => {

                                    return await buildHTML(childData);

                                }));



                                $('#data-table_processing').hide();

                                callback({

                                    draw: data.draw,

                                    recordsTotal: totalRecords,

                                    recordsFiltered: totalRecords,

                                    filteredData: filteredRecords,

                                    data: formattedRecords

                                });



                            } catch (error) {

                                console.error("Error fetching data from Firestore:", error);

                                $('#data-table_processing').hide();

                                callback({

                                    draw: data.draw,

                                    recordsTotal: 0,

                                    recordsFiltered: 0,

                                    data: []

                                });

                            }

                        },

                        order: checkDeletePermission ? [

                            ['6', 'desc']

                        ] : [

                            ['5', 'desc']

                        ],

                        columnDefs: [{

                                targets: checkDeletePermission ? [5, 6] : [4, 5],

                                type: 'date',

                                render: function(data) {

                                    return data;

                                }

                            },

                            {

                                orderable: false,

                                targets: checkDeletePermission ? [0, 8] : [7]

                            },

                        ],

                        "language": {

                            "zeroRecords": "{{ trans('lang.no_record_found') }}",

                            "emptyTable": "{{ trans('lang.no_record_found') }}",

                            "processing": "" // Remove default loader

                        },



                        initComplete: function() {

                            $('.dataTables_filter input').attr('placeholder', 'Search here...').attr('autocomplete', 'new-password').val('');

                            $('.dataTables_filter label').contents().filter(function() {

                                return this.nodeType === 3;

                            }).remove();

                        }

                    });

                }



                async function buildHTML(val) {



                    var html = [];



                    var id = val.id;

                    var route1 = '{{ route('ondemand.bookings.edit', ':id') }}';

                    route1 = route1.replace(':id', id);



                    var userRoute = '{{ route('users.view', ':id') }}';

                    userRoute = userRoute.replace(':id', val.author.id);



                    var printRoute = '{{ route('ondemand.bookings.print', ':id') }}';

                    printRoute = printRoute.replace(':id', id);



                    if (checkDeletePermission) {

                        html.push('<td class="delete-all"><input type="checkbox" id="is_open_' + id + '" class="is_open" dataId="' + id + '"><label class="col-3 control-label"\n' +

                            'for="is_open_' + id + '" ></label></td>');

                    }

                    html.push('<td><a href="' + route1 + '">' + val.id + '</a></td>');

                    html.push('<td><a href="' + userRoute + '">' + val.authorName + '<a/></td>');



                    if (val.status == 'Order Placed') {

                        html.push('<td class="order_placed"><span>' + val.status + '</span></td>');

                    } else if (val.status == 'Order Assigned') {

                        html.push('<td class="order_assigned"><span>' + val.status + '</span></td>');

                    } else if (val.status == 'Order Ongoing') {

                        html.push('<td class="order_ongoing"><span>' + val.status + '</span></td>');

                    } else if (val.status == 'Order Accepted') {

                        html.push('<td class="order_accept"><span>' + val.status + '</span></td>');

                    } else if (val.status == 'Order Rejected') {

                        html.push('<td class="order_rejected"><span>' + val.status + '</span></td>');

                    } else if (val.status == 'Order Completed') {

                        html.push('<td class="order_completed"><span>' + val.status + '</span></td>');

                    } else if (val.status == 'Order Cancelled') {

                        html.push('<td class="order_rejected"><span>' + val.status + '</span></td>');

                    } else {

                        html.push('<td class="order_completed"><span>' + val.status + '</span></td>');



                    }

                    html.push('<td>' + val.price + '</td>');

                    var bookingDate = '';

                    var bookingTime = '';

                    if (val.hasOwnProperty("scheduleDateTime")) {

                        bookingDate = val.scheduleDateTime.toDate().toDateString();

                        bookingTime = val.scheduleDateTime.toDate().toLocaleTimeString('en-US');

                    }

                    if (val.hasOwnProperty("newScheduleDateTime") && val.newScheduleDateTime != null && val.newScheduleDateTime != '') {

                        bookingDate = val.newScheduleDateTime.toDate().toDateString();

                        bookingTime = val.newScheduleDateTime.toDate().toLocaleTimeString('en-US');

                    }

                    html.push('<td class="dt-time">' + bookingDate + ' ' + bookingTime + '</td>');

                    var date = '';

                    var time = '';

                    if (val.hasOwnProperty("createdAt") && val.createdAt != '') {

                        try {

                            date = val.createdAt.toDate().toDateString();

                            time = val.createdAt.toDate().toLocaleTimeString('en-US');

                        } catch (err) {



                        }

                    }

                    html.push('<td class="dt-time">' + date + ' ' + time + '</td>');

                    html.push('<td>' + val.sectionName + '</td>');



                    var action = '';

                    action = action + '<span class="action-btn"><a href="' + printRoute + '"><i class="mdi mdi-printer" style="font-size:20px;"></i></a><a href="' + route1 + '"><i class="mdi mdi-lead-pencil"></i></a>';

                    if (checkDeletePermission) {

                        action = action + '<a id="' + val.id + '" name="order-delete" class="delete-btn" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a>';

                    }

                    action = action + '</span>';

                    html.push(action);



                    return html;



                }

                async function getSectionName(sectionId) {

                    var sectionName = '';

                    await database.collection('sections').where("id", "==", sectionId).get().then(async function(snapshots) {



                        if (snapshots.docs.length > 0) {

                            var data = snapshots.docs[0].data();

                            sectionName = data.name;

                        }

                    });

                    return sectionName;

                }





                $("#del_new").click(function() {

                    $("#newBookingTable .is_open").prop('checked', $(this).prop('checked'));

                });

                $("#del_today").click(function() {

                    $("#todayBookingTable .is_open").prop('checked', $(this).prop('checked'));

                });

                $("#del_upcoming").click(function() {

                    $("#upcomingBookingTable .is_open").prop('checked', $(this).prop('checked'));

                });

                $("#del_completed").click(function() {

                    $("#completedBookingTable .is_open").prop('checked', $(this).prop('checked'));

                });

                $("#del_canceled").click(function() {

                    $("#cancelBookingTable .is_open").prop('checked', $(this).prop('checked'));

                });



                $("#deleteAllNew").click(function() {

                    if ($('#newBookingTable .is_open:checked').length) {

                        if (confirm("{{ trans('lang.selected_delete_alert') }}")) {

                            jQuery("#data-table_processing").show();

                            $('#newBookingTable .is_open:checked').each(function() {

                                var dataId = $(this).attr('dataId');

                                database.collection('provider_orders').doc(dataId).delete().then(function() {

                                    window.location.reload();

                                });

                            });

                        }

                    } else {

                        alert("{{ trans('lang.select_delete_alert') }}");

                    }

                });

                $("#deleteAllToday").click(function() {

                    if ($('#todayBookingTable .is_open:checked').length) {

                        if (confirm("{{ trans('lang.selected_delete_alert') }}")) {

                            jQuery("#data-table_processing").show();

                            $('#todayBookingTable .is_open:checked').each(function() {

                                var dataId = $(this).attr('dataId');

                                database.collection('provider_orders').doc(dataId).delete().then(function() {

                                    window.location.reload();

                                });

                            });

                        }

                    } else {

                        alert("{{ trans('lang.select_delete_alert') }}");

                    }

                });

                $("#deleteAllUpcoming").click(function() {

                    if ($('#upcomingBookingTable .is_open:checked').length) {

                        if (confirm("{{ trans('lang.selected_delete_alert') }}")) {

                            jQuery("#data-table_processing").show();

                            $('#upcomingBookingTable .is_open:checked').each(function() {

                                var dataId = $(this).attr('dataId');

                                database.collection('provider_orders').doc(dataId).delete().then(function() {

                                    window.location.reload();

                                });

                            });

                        }

                    } else {

                        alert("{{ trans('lang.select_delete_alert') }}");

                    }

                });

                $("#deleteAllCompleted").click(function() {

                    if ($('#completedBookingTable .is_open:checked').length) {

                        if (confirm("{{ trans('lang.selected_delete_alert') }}")) {

                            jQuery("#data-table_processing").show();

                            $('#completedBookingTable .is_open:checked').each(function() {

                                var dataId = $(this).attr('dataId');

                                database.collection('provider_orders').doc(dataId).delete().then(function() {

                                    window.location.reload();

                                });

                            });

                        }

                    } else {

                        alert("{{ trans('lang.select_delete_alert') }}");

                    }

                });

                $("#deleteAllCancel").click(function() {

                    if ($('#cancelBookingTable .is_open:checked').length) {

                        if (confirm("{{ trans('lang.selected_delete_alert') }}")) {

                            jQuery("#data-table_processing").show();

                            $('#cancelBookingTable .is_open:checked').each(function() {

                                var dataId = $(this).attr('dataId');

                                database.collection('provider_orders').doc(dataId).delete().then(function() {

                                    window.location.reload();

                                });

                            });

                        }

                    } else {

                        alert("{{ trans('lang.select_delete_alert') }}");

                    }

                });



                $(document).on("click", "a[name='order-delete']", function(e) {

                    var id = this.id;

                    database.collection('provider_orders').doc(id).delete().then(function(result) {

                        window.location.href = '{{ url()->current() }}';

                    });

                });



                function buildHTMLProductstotal(snapshotsProducts) {

                    var adminCommission = snapshotsProducts.adminCommission;

                    var discount = snapshotsProducts.discount;

                    var couponCode = snapshotsProducts.couponCode;

                    var status = snapshotsProducts.status;

                    var products = snapshotsProducts;

                    var totalProductPrice = 0;

                    var total_price = 0;



                    var intRegex = /^\d+$/;

                    var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;





                    var val = products;



                    var sub_total = parseFloat(val.provider.price);



                    if (val.provider.disPrice != null && val.provider.disPrice != undefined && val.provider.disPrice != '' && val.provider.disPrice != '0') {

                        sub_total = parseFloat(val.provider.disPrice)

                    }

                    var price = sub_total;



                    sub_total = parseFloat(val.quantity) * sub_total;



                    total_price += parseFloat(sub_total);



                    if (intRegex.test(discount) || floatRegex.test(discount)) {



                        discount = parseFloat(discount).toFixed(decimal_degits);

                        total_price -= parseFloat(discount);



                        if (currencyAtRight) {

                            discount_val = discount + "" + currentCurrency;

                        } else {

                            discount_val = currentCurrency + "" + discount;

                        }





                    }

                    var tax = 0;

                    taxlabel = '';

                    taxlabeltype = '';



                    if (snapshotsProducts.hasOwnProperty('taxSetting')) {

                        var total_tax_amount = 0;

                        for (var i = 0; i < snapshotsProducts.taxSetting.length; i++) {

                            var data = snapshotsProducts.taxSetting[i];



                            if (data.type && data.tax) {

                                if (data.type == "percentage") {

                                    tax = (data.tax * total_price) / 100;

                                    taxlabeltype = "%";

                                } else {

                                    tax = data.tax;

                                    taxlabeltype = "fix";

                                }

                                taxlabel = data.title;

                            }



                            total_tax_amount += parseFloat(tax);

                        }

                        total_price = parseFloat(total_price) + parseFloat(total_tax_amount);

                    }



                    if (currencyAtRight) {

                        var total_price_val = parseFloat(total_price).toFixed(decimal_degits) + "" + currentCurrency;

                    } else {

                        var total_price_val = currentCurrency + "" + parseFloat(total_price).toFixed(decimal_degits);

                    }



                    return total_price_val;

                }

                async function getProviderNameForFilter(providerId) {

                    await database.collection('users').where('id', '==', providerId).get().then(async function(snapshots) {

                        var providerData = snapshots.docs[0].data();

                        providerName = providerData.firstName + ' ' + providerData.lastName;

                        $('.PageTitle').html("{{ trans('lang.booking_plural') }} - " + providerName);

                    });



                }



                function clickLink(value) {

                    setCookie('ondemand_section_id', value, 30);

                    location.reload();

                }



                function exportBookingData(fileName, format) {





                    var columns = [];

                    columns = [{

                            key: 'id',

                            header: "{{ trans('lang.booking_id') }}"

                        },

                        {

                            key: 'authorName',

                            header: "{{ trans('lang.order_user_id') }}"

                        },

                        {

                            key: 'status',

                            header: "{{ trans('lang.status') }}"

                        },

                        {

                            key: 'price',

                            header: "{{ trans('lang.amount') }}"

                        },

                        {

                            key: 'bookingDateTime',

                            header: "{{ trans('lang.booking_date') }}"

                        },

                        {

                            key: 'createdAt',

                            header: "{{ trans('lang.created_at') }}"

                        },

                        {

                            key: 'sectionName',

                            header: "{{ trans('lang.section') }}"

                        },

                    ];

                    const filteredData = filteredRecords;



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

                                case 'date':

                                    return value ? new Date(value.seconds * 1000).toLocaleString() : '-';

                                case 'number':

                                    return typeof value === 'number' ? value : 0;

                                case 'string':

                                default:

                                    return value || '-';

                            }

                        });

                    };



                    const tableData = filteredData.map(dataMapper);



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



                        const blob = new Blob([csv], {

                            type: 'text/csv;charset=utf-8;'

                        });

                        saveAs(blob, `${fileName}.csv`);

                    } else if (format === 'excel') {

                        const ws = XLSX.utils.aoa_to_sheet(data, {

                            cellDates: true

                        });



                        ws['!cols'] = columnWidths.map(width => ({

                            wch: Math.min(width + 5, 30)

                        }));



                        const wb = XLSX.utils.book_new();

                        XLSX.utils.book_append_sheet(wb, ws, 'Data');

                        XLSX.writeFile(wb, `${fileName}.xlsx`);

                    } else if (format === 'pdf') {

                        /* const { jsPDF } = window.jspdf;

                        const doc = new jsPDF();



                        const totalLength = columnWidths.reduce((sum, length) => sum + length, 0);

                        const columnStyles = {};

                        columnWidths.forEach((length, index) => {

                            columnStyles[index] = {

                                cellWidth: (length / totalLength) * 180, 

                            };

                        });



                        doc.setFontSize(16);

                        doc.text(fileName, 14, 16);



                        doc.autoTable({

                            head: [columns.map(col => col.header)],

                            body: tableData,

                            startY: 20,

                            theme: 'striped',

                            styles: {

                                cellPadding: 2,

                                fontSize: 10,

                            },

                            columnStyles,

                            margin: { top: 30, bottom: 30 },

                            didDrawPage: function (data) {

                                doc.setFontSize(10);

                                doc.text(fileName, data.settings.margin.left, 10);

                            }

                        });

                        doc.save(`${fileName}.pdf`); */

                        const {

                            jsPDF

                        } = window.jspdf;

                        const doc = new jsPDF('l', 'mm', 'a4'); // Landscape for more width



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

                                0: {

                                    cellWidth: 'auto'

                                }, // Adjust first column automatically

                            },

                            margin: {

                                top: 30,

                                bottom: 30

                            },

                            pageBreak: 'auto', // Ensures page break for long content

                        });



                        doc.save(`${fileName}.pdf`);



                    } else {

                        console.error('Unsupported format');

                    }

                }
            </script>
        @endsection
