@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{ trans('lang.provider_ratings') }}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                <li class="breadcrumb-item active">{{ trans('lang.provider_ratings') }}</li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display:none;">{{ trans('lang.processing') }}</div>
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-striped" id="ratingsTable">
                    <thead>
                        <tr>
                            <th>{{ trans('lang.user') }}</th>
                            <th>{{ trans('lang.item_review_rate') }}</th>
                            <th>{{ trans('lang.order_review') }}</th>
                            <th>{{ trans('lang.date') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    var database = firebase.firestore();
    var providerId = "<?php echo $id; ?>";

    function stars(rating) {
        var n = Math.round(Number(rating) || 0);
        if (n < 0) n = 0;
        if (n > 5) n = 5;
        var html = '';
        for (var i = 1; i <= 5; i++) {
            html += i <= n ? '★' : '☆';
        }
        return '<span class="text-warning">' + html + '</span> ' + n;
    }

    function customerName(review) {
        if (review.uname) return review.uname;
        return review.CustomerId || review.customerId || '{{ trans("lang.user") }}';
    }

    $(function () {
        $("#data-table_processing").show();
        database.collection('items_review').where('VendorId', '==', providerId).get().then(function (snap) {
            var rows = snap.docs.map(function (d) { return d.data() || {}; });
            rows.sort(function (a, b) {
                var ta = a.createdAt && a.createdAt.toDate ? a.createdAt.toDate().getTime() : 0;
                var tb = b.createdAt && b.createdAt.toDate ? b.createdAt.toDate().getTime() : 0;
                return tb - ta;
            });
            var html = '';
            rows.forEach(function (r) {
                var when = r.createdAt && r.createdAt.toDate ? ArrowDateTime.formatDate(r.createdAt.toDate()) : '';
                html += '<tr><td>' + customerName(r) + '</td><td>' + stars(r.rating) + '</td><td>' + (r.comment || '') + '</td><td>' + when + '</td></tr>';
            });
            $('#ratingsTable tbody').html(html || '<tr><td colspan="4">{{ trans("lang.provider_ratings_empty") }}</td></tr>');
            $("#data-table_processing").hide();
        }).catch(function () {
            $('#ratingsTable tbody').html('<tr><td colspan="4">{{ trans("lang.provider_ratings_empty") }}</td></tr>');
            $("#data-table_processing").hide();
        });
    });
</script>
@endsection
