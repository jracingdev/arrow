@extends('layouts.app')

@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ trans('lang.ondemand_reports') }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('lang.ondemand_reports') }}</li>
                </ol>
            </div>
        </div>
        <div class="container-fluid">
            <div class="admin-top-section">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex top-title-section pb-4 justify-content-between">
                            <div class="d-flex top-title-left align-self-center">
                                <span class="icon mr-3"><img src="{{ asset('images/faq.png') }}"></span>
                                <h3 class="mb-0">{{ trans('lang.ondemand_reports') }}</h3>
                                <span class="counter ml-3 total_count"></span>
                            </div>
                            <div class="select-box pl-3">
                                <select class="form-control status_selector" onchange="loadReports()">
                                    <option value="Initiated" selected>{{ trans('lang.initiated') }}</option>
                                    <option value="">{{ trans('lang.status') }}</option>
                                    <option value="Under Investigation">{{ trans('lang.Under_investigation') }}</option>
                                    <option value="Resolved">{{ trans('lang.resolved') }}</option>
                                    <option value="Dismissed">{{ trans('lang.dismiss_report') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-list">
                <div class="row">
                    <div class="col-12">
                        <div class="card border">
                            <div class="card-header d-flex justify-content-between align-items-center border-0">
                                <div class="card-header-title">
                                    <h3 class="text-dark-2 mb-2 h4">{{ trans('lang.ondemand_reports') }}</h3>
                                    <p class="mb-0 text-dark-2">{{ trans('lang.ondemand_reports_table_text') }}</p>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive m-t-10">
                                    <table id="ondemandReportsTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>{{ trans('lang.order_id') }}</th>
                                                <th>{{ trans('lang.report_priority') }}</th>
                                                <th>{{ trans('lang.report_category') }}</th>
                                                <th>{{ trans('lang.description') }}</th>
                                                <th>{{ trans('lang.reporter') }}</th>
                                                <th>{{ trans('lang.reported_user') }}</th>
                                                <th>{{ trans('lang.status') }}</th>
                                                <th>{{ trans('lang.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="append_list1"></tbody>
                                    </table>
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
    <script type="text/javascript">
        var database = firebase.firestore();
        var table = null;

        $(document).ready(function () {
            loadReports();
        });

        function loadReports() {
            jQuery("#data-table_processing").show();
            var status = $('.status_selector').val();
            var ref = database.collection('complaints').where('serviceType', '==', 'ondemand-service');
            if (status) {
                ref = ref.where('status', '==', status);
            }
            ref.get().then(async function (snapshots) {
                var html = '';
                var rows = [];
                snapshots.docs.forEach(function (doc) {
                    rows.push({ id: doc.id, data: doc.data() });
                });
                rows.sort(function (a, b) {
                    var ap = a.data.priority === 'high' ? 0 : 1;
                    var bp = b.data.priority === 'high' ? 0 : 1;
                    if (ap !== bp) return ap - bp;
                    var at = a.data.createdAt && a.data.createdAt.toMillis ? a.data.createdAt.toMillis() : 0;
                    var bt = b.data.createdAt && b.data.createdAt.toMillis ? b.data.createdAt.toMillis() : 0;
                    return bt - at;
                });
                for (var i = 0; i < rows.length; i++) {
                    html += await rowHtml(rows[i].data, rows[i].id);
                }
                if (table) {
                    table.destroy();
                }
                $('#append_list1').html(html);
                $('.total_count').text(rows.length);
                table = $('#ondemandReportsTable').DataTable({
                    order: [],
                    language: datatableLang,
                    responsive: true
                });
                jQuery("#data-table_processing").hide();
            }).catch(function () {
                jQuery("#data-table_processing").hide();
            });
        }

        async function rowHtml(val, id) {
            var orderRoute = '{{ route('ondemand.bookings.edit', ':id') }}'.replace(':id', val.orderId || '');
            var editRoute = '{{ route('ondemand.reports.edit', ':id') }}'.replace(':id', id);
            var reported = await database.collection('users').doc(val.reportedId || '').get();
            var reportedData = reported.exists ? reported.data() : {};
            var strikes = reportedData.reportStrikes || 0;
            var recommended = reportedData.banRecommended === true || strikes >= 3;
            var badge = val.priority === 'high' ? '<span class="badge badge-danger">SOS</span>' : '<span class="badge badge-secondary">normal</span>';
            var repeat = recommended ? ' <span class="badge badge-warning">{{ trans('lang.repeat_offender') }}</span>' : '';
            var html = '<tr>';
            html += '<td><a href="' + orderRoute + '">' + (val.orderId || '').toString().substring(0, 8) + '</a></td>';
            html += '<td>' + badge + '</td>';
            html += '<td>' + (val.category || val.title || '') + '</td>';
            html += '<td>' + (val.description || '') + '</td>';
            html += '<td>' + (val.reporterRole || '') + ' · ' + (val.customerName && val.reporterRole === 'customer' ? val.customerName : (val.driverName || '')) + '</td>';
            html += '<td>' + (val.reportedRole || '') + repeat + '</td>';
            html += '<td>' + (val.status || '') + '</td>';
            html += '<td><a href="' + editRoute + '"><i class="mdi mdi-lead-pencil"></i></a></td>';
            html += '</tr>';
            return html;
        }
    </script>
@endsection
