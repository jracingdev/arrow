@extends('layouts.app')



@section('content')

<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.coupon_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.coupon_plural')}}</li>
            </ol>
        </div>
        <div>
        </div>
    </div>
    <div class="container-fluid">
    
       <div class="admin-top-section"> 
        <div class="row">
            <div class="col-12">
                <div class="d-flex top-title-section pb-4 justify-content-between">
                    <div class="d-flex top-title-left align-self-center">
                        <span class="icon mr-3"><img src="{{ asset('images/coupon.png') }}"></span>
                        <h3 class="mb-0">{{trans('lang.coupon_plural')}}</h3>
                        <span class="counter ml-3 total_count"></span>
                    </div>
                    <div class="d-flex top-title-right align-self-center">
                        <div class="select-box pl-3">
                            <select id="section_id" class="form-control allModules filteredRecords" style="width:100%"
                                            onchange="clickLink(this.value)">
                                        <option value="">{{trans('lang.select')}} {{trans('lang.section_plural')}}
                                        </option>
                                    </select>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
       </div>
       <div class="table-list">
       <div class="row">
           <div class="col-12">
            <?php if ($id != '') { ?>

                <div class="menu-tab">

                    <ul>

                        <li>

                            <a href="{{route('vendors.view',$id)}}">{{trans('lang.tab_basic')}}</a>

                        </li>

                        <li>

                            <a href="{{route('vendors.items',$id)}}">{{trans('lang.tab_items')}}</a>

                        </li>

                        <li>

                            <a href="{{route('vendors.orders',$id)}}">{{trans('lang.tab_orders')}}</a>

                        </li>

                        <li>

                            <a href="{{route('vendors.reviews',$id)}}">{{trans('lang.tab_reviews')}}</a>

                        </li>

                        <li class="active">

                            <a href="{{route('vendors.coupons',$id)}}">{{trans('lang.tab_promos')}}</a>

                        <li>

                            <a href="{{route('vendors.payout',$id)}}">{{trans('lang.tab_payouts')}}</a>

                        </li>

                        <li>

                            <a href="{{route('payoutRequests.vendor.view',$id)}}">{{trans('lang.tab_payout_request')}}</a>

                        </li>

                        <li>

                            <a class="wallet_transaction">{{trans('lang.wallet_transaction')}}</a>

                        </li>

                        <li class="dine_in_future" style="display:none;">

                            <a href="{{route('vendors.booktable',$id)}}">{{trans('lang.dine_in_future')}}</a>

                        </li>
                        <?php
                        $subscription =  route("subscription.subscriptionPlanHistory", ":id");
                        $subscription =  str_replace(":id", "storeID=" . $id, $subscription);
                        ?>
                        <li>
                            <a href="{{ $subscription }}">{{trans('lang.subscription_history')}}</a>
                        </li>

                    </ul>

                </div>

                <?php } ?>
               <div class="card border">
                 <div class="card-header d-flex justify-content-between align-items-center border-0">
                   <div class="card-header-title">
                    <h3 class="text-dark-2 mb-2 h4">{{trans('lang.coupon_plural')}}</h3>
                    <p class="mb-0 text-dark-2">{{trans('lang.couponn_table_text')}}</p>
                   </div>
                   <div class="card-header-right d-flex align-items-center">
                    <div class="card-header-btn mr-3"> 
                    <?php if ($id != '' && $id != null) { ?>
                        <a class="btn-primary btn rounded-full" href="{{route('coupons.create',$id)}}"><i class="mdi mdi-plus mr-2"></i>{{trans('lang.coupon_create')}}</a>
                        <?php  } else { ?>
                        <a class="btn-primary btn rounded-full" href="{!! route('coupons.create') !!}"><i class="mdi mdi-plus mr-2"></i>{{trans('lang.coupon_create')}}</a>
                            <?php } ?>
                     </div>
                   </div>                
                 </div>
                 <div class="card-body">
                         <div class="table-responsive m-t-10">
                         <table id="couponTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <?php if (in_array('coupons.delete', json_decode(@session('user_permissions')))) { ?>
                                        <th class="delete-all"><input type="checkbox" id="is_active"><label

                                                class="col-3 control-label" for="is_active"><a id="deleteAll"

                                                    class="do_not_delete" href="javascript:void(0)"><i

                                                        class="fa fa-trash"></i> {{trans('lang.all')}}</a></label></th>

                                        <?php } ?>

                                        <th>{{trans('lang.coupon_code')}}</th>
                                        <th>{{trans('lang.coupon_discount')}}</th>
                                        <th>{{trans('lang.vendor')}}</th>
                                        <th>{{trans('lang.section')}}</th>
                                        <th>{{trans('lang.coupon_privacy')}}</th>
                                        <th>{{trans('lang.coupon_expires_at')}}</th>
                                        <th>{{trans('lang.coupon_enabled')}}</th>
                                        <th>{{trans('lang.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody id="append_list1">
                                </tbody>
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



    var user_permissions = '<?php echo @session('user_permissions') ?>';



    user_permissions = JSON.parse(user_permissions);



    var checkDeletePermission = false;



    if ($.inArray('coupons.delete', user_permissions) >= 0) {

        checkDeletePermission = true;

    }

    $('.allModules').select2({
        placeholder: '{{trans("lang.section")}}',  
        minimumResultsForSearch: Infinity,
        allowClear: true 
    });
    $('select').on("select2:unselecting", function(e) {
        var self = $(this);
        setTimeout(function() {
            self.select2('close');
        }, 0);
    });



    var database = firebase.firestore();



    var vendorID = "{{$id}}";



    $('.sectionDiv').hide();



    <?php if ($id != '') { ?>




        getStoreNameFunction('<?php echo $id; ?>');

        var ref = database.collection('coupons').where('vendorID', '==', '<?php echo $id; ?>');

    <?php } else { ?>

        $('.sectionDiv').show();

        var section_id = getCookie('section_id');

        if (section_id != '') {

            var ref = database.collection('coupons').where('section_id', '==', section_id);

        } else {

            var ref = database.collection('coupons');

        }

    <?php } ?>



    var ref_sections = database.collection('sections');



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



    var append_list = '';



    $(document).ready(function () {



        $(document.body).on('click', '.redirecttopage', function () {

            var url = $(this).attr('data-url');

            window.location.href = url;

        });



        jQuery("#data-table_processing").show();







        ref_sections.get().then(async function (snapshots) {



            snapshots.docs.forEach((listval) => {

                var data = listval.data();



                $('#section_id').append($("<option value=''>{{trans('lang.select')}} {{trans('lang.section_plural')}}</option>")

                    .attr("value", data.id)

                    .text(data.name));

            })



            $('#section_id').val(section_id);

        })





        const table = $('#couponTable').DataTable({

            pageLength: 10, // Number of rows per page

            processing: false, // Show processing indicator

            serverSide: true, // Enable server-side processing

            responsive: true,

            ajax: async function (data, callback, settings) {

                const start = data.start;

                const length = data.length;

                const searchValue = data.search.value.toLowerCase();

                const orderColumnIndex = data.order[0].column;

                const orderDirection = data.order[0].dir;

                var orderableColumns = (checkDeletePermission) ? ['', 'code', 'discount', 'store', 'section', 'privacy', 'expiresAt', '', ''] : ['code', 'discount', 'store', 'section', 'privacy', 'expiresAt', '', '']; // Ensure this matches the actual column names

                const orderByField = orderableColumns[orderColumnIndex]; // Adjust the index to match your table

                if (searchValue.length >= 3 || searchValue.length === 0) {

                    $('#data-table_processing').show();

                }

                await ref.get().then(async function (querySnapshot) {

                    if (querySnapshot.empty) {

                        $('.total_count').text(0); 

                        console.error("No data found in Firestore.");

                        $('#data-table_processing').hide(); // Hide loader

                        callback({

                            draw: data.draw,

                            recordsTotal: 0,

                            recordsFiltered: 0,

                            data: [] // No data

                        });

                        return;

                    }



                    let records = [];

                    let filteredRecords = [];

                    var sectionNames = {};

                    const sectionDocs = await database.collection('sections').get();

                    sectionDocs.forEach(doc => {

                        sectionNames[doc.id] = doc.data().name;

                    });

                    var storeNames = {};

                    const storeDocs = await database.collection('vendors').get();

                    storeDocs.forEach(doc => {

                        storeNames[doc.id] = doc.data().title;

                    });

                    await Promise.all(querySnapshot.docs.map(async (doc) => {

                        let childData = doc.data();

                        if (childData.hasOwnProperty("vendorID")) {

                            childData.store = storeNames[childData.vendorID] || '';

                        } else {

                            childData.store = '';

                        }



                        if (childData.hasOwnProperty("section_id")) {

                            childData.section = sectionNames[childData.section_id] || '';

                        } else {

                            childData.section = '';

                        }

                        childData.privacy = (childData.isPublic) ? '{{trans("lang.public")}}' : '{{trans("lang.private")}}';

                        childData.id = doc.id; // Ensure the document ID is included in the data              



                        if (searchValue) {

                            var date = '';

                            var time = '';

                            if (childData.hasOwnProperty("expiresAt")) {

                                try {

                                    date = childData.expiresAt.toDate().toDateString();

                                    time = childData.expiresAt.toDate().toLocaleTimeString('en-US');

                                } catch (err) {

                                }

                            }

                            var expireAt = date + ' ' + time;

                            if (

                                (childData.code && childData.code.toLowerCase().toString().includes(searchValue)) ||

                                (childData.store && childData.store.toLowerCase().toString().includes(searchValue)) ||

                                (expireAt && expireAt.toString().toLowerCase().indexOf(searchValue) > -1) ||

                                (childData.discount && childData.discount.toString().toLowerCase().includes(searchValue)) ||

                                (childData.section && childData.section.toString().toLowerCase().includes(searchValue)) ||

                                (childData.privacy && childData.privacy.toString().toLowerCase().includes(searchValue))



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



                        if (orderByField === 'expiresAt') {

                            try {

                                aValue = a[orderByField] ? new Date(a[orderByField].toDate()).getTime() : 0;

                                bValue = b[orderByField] ? new Date(b[orderByField].toDate()).getTime() : 0;

                            } catch (err) {



                            }

                        }

                        if(orderByField === 'discount') {

                            aValue = a[orderByField] ? parseInt(a[orderByField]) : 0;

                            bValue = b[orderByField] ? parseInt(b[orderByField]) : 0;



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



                    await Promise.all(paginatedRecords.map(async (childData) => {

                        var getData = await buildHTML(childData);

                        records.push(getData);

                    }));



                    $('#data-table_processing').hide(); // Hide loader

                    callback({

                        draw: data.draw,

                        recordsTotal: totalRecords, // Total number of records in Firestore

                        recordsFiltered: totalRecords, // Number of records after filtering (if any)

                        data: records // The actual data to display in the table

                    });

                }).catch(function (error) {

                    console.error("Error fetching data from Firestore:", error);

                    $('#data-table_processing').hide(); // Hide loader

                    callback({

                        draw: data.draw,

                        recordsTotal: 0,

                        recordsFiltered: 0,

                        data: [] // No data due to error

                    });

                });

            },

            order: (checkDeletePermission) ? [[6, 'desc']] : [[5, 'desc']],

            columnDefs: [

                {

                    targets: (checkDeletePermission) ? 6 : 5,

                    type: 'date',

                    render: function (data) {

                        return data;

                    }

                },

                { orderable: false, targets: (checkDeletePermission) ? [0, 5, 7, 8] : [4, 6, 7] },

            ],

            "language": {

                "zeroRecords": "{{trans("lang.no_record_found")}}",

                "emptyTable": "{{trans("lang.no_record_found")}}",

                "processing": "" // Remove default loader

            },



        });



        function debounce(func, wait) {

            let timeout;

            const context = this;

            return function (...args) {

                clearTimeout(timeout);

                timeout = setTimeout(() => func.apply(context, args), wait);

            };

        }

        $('#search-input').on('input', debounce(function () {

            const searchValue = $(this).val();

            if (searchValue.length >= 3) {

                $('#data-table_processing').show();

                table.search(searchValue).draw();

            } else if (searchValue.length === 0) {

                $('#data-table_processing').show();

                table.search('').draw();

            }

        }, 300));



    });



    function clickLink(value) {

        setCookie('section_id', value, 30);

        location.reload();

    }



    function getStoreNameFunction(vendorId) {

        var vendorName = '';

        database.collection('vendors').where('id', '==', vendorId).get().then(function (snapshots) {

            var vendorData = snapshots.docs[0].data();

            vendorName = vendorData.title;

            $(".storeTitle").text(' - ' + vendorName);

            if (vendorData.dine_in_active == true) {

                $(".dine_in_future").show();

            }
            
            var wallet_route = "{{route('users.walletstransaction','id')}}";

            $(".wallet_transaction").attr("href", wallet_route.replace('id', 'storeID=' + vendorData.author));


        });

        return vendorName;

    }



    async function buildHTML(val) {



        var html = [];

        newdate = '';

        if (currencyAtRight) {

            if (val.discountType == 'Percent' || val.discountType == 'Percentage') {

                discount_price = val.discount + "%";

            } else {

                discount_price = parseFloat(val.discount).toFixed(decimal_degits) + "" + currentCurrency;

            }

        } else {

            if (val.discountType == 'Percent' || val.discountType == 'Percentage') {

                discount_price = val.discount + "%";

            } else {

                discount_price = currentCurrency + "" + parseFloat(val.discount).toFixed(decimal_degits);

            }

        }
        const expireDate = new Date( val.expiresAt.toDate().toDateString()); 
        const currentDate = new Date();
        const isExpired = expireDate < currentDate;

        var id = val.id;

        var st_id = val.vendorID;

        var route1 = '{{route("coupons.edit",":id")}}';

        route1 = route1.replace(':id', id);

        var route2 = '{{route("vendors.view",":id")}}';

        route2 = route2.replace(':id', st_id);

        <?php if ($id != '') { ?>

            route1 = route1 + '?eid={{$id}}';

        <?php } ?>

        if (checkDeletePermission) {

            html.push('<td class="delete-all"><input type="checkbox" id="is_open_' + id + '" class="is_open" dataId="' + id + '"><label class="col-3 control-label"\n' +

                'for="is_open_' + id + '" ></label></td>');

        }

        html.push('<a  href="' + route1 + '" class="redirecttopage">' + val.code + '</a>');

        html.push(discount_price);

        if (val.hasOwnProperty("vendorID")) {

            html.push('<a  href="' + route2 + '" class="redirecttopage">' + val.store+ '</a>');

        } else {

            html.push('');

        }

        if (val.hasOwnProperty("section_id")) {

            html.push(val.section);

        } else {

            html.push('');

        }

        if (val.hasOwnProperty('isPublic') && val.isPublic) {

            html.push('<span class="success"><span class="badge badge-success py-2 px-3">{{trans("lang.public")}}</sapn></span>');

        } else {

            html.push('<span class="danger"><span class="badge badge-danger py-2 px-3">{{trans("lang.private")}}</sapn></span>');

        }

        var date = '';

        var time = '';

        if (val.hasOwnProperty("expiresAt")) {

            try {

                date = val.expiresAt.toDate().toDateString();

                time = val.expiresAt.toDate().toLocaleTimeString('en-US');

            } catch (err) {



            }

            html.push('<span class="dt-time">' + date + ' ' + time + '</span>');

        } else {

            html.push('');

        }

        if (val.isEnabled) {

            html.push('<label class="switch"><input type="checkbox" ' + (isExpired ? 'disabled' : 'checked') + ' id="' + val.id + '" name="isActive"><span class="slider round"></span></label>');

        } else {

            html.push('<label class="switch"><input type="checkbox" '+ (isExpired ? 'disabled' : '') + ' id="' + val.id + '" name="isActive"><span class="slider round"></span></label>');

        }

        var actionHtml = '';

        actionHtml += '<span class="action-btn"><a href="' + route1 + '"><i class="mdi mdi-lead-pencil"></i></a>';

        if (checkDeletePermission) {

            actionHtml = actionHtml + '<a id="' + val.id + '" name="coupon_delete_btn" class="delete-btn" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a>';

        }

        actionHtml = actionHtml + '</span>';

        html.push(actionHtml);

        return html;

    }





    $(document).on("click", "input[name='isActive']", function (e) {

        var ischeck = $(this).is(':checked');

        var id = this.id;

        if (ischeck) {

            database.collection('coupons').doc(id).update({ 'isEnabled': true }).then(function (result) {



            });

        } else {

            database.collection('coupons').doc(id).update({ 'isEnabled': false }).then(function (result) {



            });

        }

    });





    $("#is_active").click(function () {

        $("#couponTable .is_open").prop('checked', $(this).prop('checked'));

    });



    $("#deleteAll").click(function () {

        if ($('#couponTable .is_open:checked').length) {

            if (confirm("{{trans('lang.selected_delete_alert')}}")) {

                jQuery("#data-table_processing").show();

                $('#couponTable .is_open:checked').each(async function () {

                    var dataId = $(this).attr('dataId');

                    await deleteDocumentWithImage('coupons',dataId,'image');

                    window.location.reload();



                });

            }

        } else {

            alert("{{trans('lang.select_delete_alert')}}");

        }

    });



    $(document).on("click", "a[name='coupon_delete_btn']", async function (e) {

        var id = this.id;

        jQuery("#data-table_processing").show();

        await deleteDocumentWithImage('coupons',id,'image'); 

        window.location = "{{! url()->current() }}";

    });



</script>



@endsection