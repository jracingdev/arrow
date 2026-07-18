@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ trans('lang.role_plural') }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('lang.role_plural') }}</li>
                </ol>
            </div>
        </div>
        
        <div class="container-fluid page-menu">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs align-items-end card-header-tabs w-100 ">
                                <li class="nav-item active">
                                    <a class="nav-link" href="{!! route('role.index') !!}"><i class="fa fa-list mr-2"></i>{{ trans('lang.role_plural') }}</a>
                                </li>
                                <li class="nav-item create-btn">
                                    <a class="nav-link" href="javascript:void(0)"><i class="fa fa-plus mr-2"></i>{{ trans('lang.create_role') }}</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive m-t-10">
                                <table id="roleTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('lang.title') }}</th>
                                            <th>{{ trans('lang.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="append_list">
                                        <!-- Data will be appended here -->
                                    </tbody>
                                </table>
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
    var vendorUserId = "{{ $vendorUserId }}";
    var vendorId = '';
    var authRole = "{{ $authRole }}";
    var empVendorId = "{{ $empVendorId }}";
    let currentPermissions = {
        isActive: true   
    };
    document.addEventListener("DOMContentLoaded", async function() {      


        if (authRole === 'employee') {               
            const perm = await getEmployeePermissionForTitle(vendorUserId, "Employee Role");
            currentPermissions = {
                isActive: perm.isActive ?? false
            };

            if (!currentPermissions.isActive) {
                alert('{{ trans("lang.no_permission") }}');
                $('#roleTable').hide();
                $('.page-menu').html('<p class="text-center text-danger font-weight-bold">{{ trans("lang.no_permission") }}</p>');
                return;
            }
        }
        // Get vendor ID first
        getVendorId().then(function() {
            // Initialize DataTable after vendorId is available
            initializeDataTable();
        });

        // Redirect function
        $(document.body).on('click', '.redirecttopage', function() {
            var url = $(this).attr('data-url');
            window.location.href = url;
        });
    });

    async function getVendorId() {
        return new Promise((resolve, reject) => {
            if(authRole == 'vendor'){
                database.collection('vendors')
                    .where('author', "==", vendorUserId)
                    .get()
                    .then(function(vendorSnapshots) {
                        if (!vendorSnapshots.empty) {
                            var vendorData = vendorSnapshots.docs[0].data();
                            vendorId = vendorData.id;
                            resolve(vendorId);
                        } else {
                            console.error("Vendor not found");
                            resolve(null);
                        }
                    })
                    .catch(function(error) {
                        console.error("Error getting vendor:", error);
                        reject(error);
                    });
            }else{
                database.collection('vendors')
                    .where('id', "==", empVendorId)
                    .get()
                    .then(function(vendorSnapshots) {
                        if (!vendorSnapshots.empty) {
                            var vendorData = vendorSnapshots.docs[0].data();
                            vendorId = vendorData.id;
                            resolve(vendorId);
                        } else {
                            console.error("Vendor not found");
                            resolve(null);
                        }
                    })
                    .catch(function(error) {
                        console.error("Error getting vendor:", error);
                        reject(error);
                    });
            }
        });
    }

    function initializeDataTable() {
        const table = $('#roleTable').DataTable({
            pageLength: 10,
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: async function(data, callback, settings) {
                const start = data.start;
                const length = data.length;
                const searchValue = data.search.value.toLowerCase();
                const orderColumnIndex = data.order[0].column;
                const orderDirection = data.order[0].dir;
                const orderableColumns = ['title', ''];
                const orderByField = orderableColumns[orderColumnIndex];
                
                if (searchValue.length >= 3 || searchValue.length === 0) {
                    $('#data-table_processing').show();
                }
                
                try {
                    if (!vendorId) {
                        console.error("Vendor ID not found");
                        callback({
                            draw: data.draw,
                            recordsTotal: 0,
                            recordsFiltered: 0,
                            data: []
                        });
                        $('#data-table_processing').hide();
                        return;
                    }
                    
                    // Get roles for this vendor
                    let query = database.collection('vendor_employee_roles')
                        .where('vendorId', "==", vendorId);
                    
                    const querySnapshot = await query.get();
                    
                    if (querySnapshot.empty) {
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
                    let filteredRecords = [];
                    
                    await Promise.all(querySnapshot.docs.map(async (doc) => {
                        let roleData = doc.data();
                        roleData.id = doc.id;
                        
                        // Search filter
                        if (searchValue) {
                            if (roleData.title && roleData.title.toString().toLowerCase().includes(searchValue)) {
                                filteredRecords.push(roleData);
                            }
                        } else {
                            filteredRecords.push(roleData);
                        }
                    }));
                    
                    // Sort records by title
                    filteredRecords.sort((a, b) => {
                        let aValue = a.title ? a.title.toString().toLowerCase() : '';
                        let bValue = b.title ? b.title.toString().toLowerCase() : '';
                        
                        if (orderDirection === 'asc') {
                            return (aValue > bValue) ? 1 : -1;
                        } else {
                            return (aValue < bValue) ? 1 : -1;
                        }
                    });
                    
                    const totalRecords = filteredRecords.length;
                    const paginatedRecords = filteredRecords.slice(start, start + length);
                    
                    const formattedRecords = await Promise.all(paginatedRecords.map(async (roleData) => {
                        return await buildHTML(roleData);
                    }));
                    
                    $('#data-table_processing').hide();
                    callback({
                        draw: data.draw,
                        recordsTotal: totalRecords,
                        recordsFiltered: totalRecords,
                        data: formattedRecords
                    });
                    
                } catch (error) {
                    console.error("Error fetching roles:", error);
                    $('#data-table_processing').hide();
                    callback({
                        draw: data.draw,
                        recordsTotal: 0,
                        recordsFiltered: 0,
                        data: []
                    });
                }
            },
            order: [[0, 'asc']], // Default sort by title ascending
            columnDefs: [{
                orderable: false,
                targets: [1] // Make actions column not orderable
            }],
            "language": datatableLang
        });
    }

    async function buildHTML(val) {
        var html = [];
        var id = val.id;
        
        // Edit route
        var editRoute = '{{ route('role.edit', ':id') }}';
        editRoute = editRoute.replace(':id', id);
        
        // Title column with link to edit
        html.push('<td><a href="' + editRoute + '">' + (val.title || 'Untitled Role') + '</a></td>');
        
        // Actions column
        var actions = '';
        actions += '<span class="action-btn">';
        actions += '<a href="' + editRoute + '" title="{{ trans("lang.edit") }}" class="mr-2"><i class="fa fa-edit"></i></a>';
       
        actions += '<a id="' + val.id + '" class="do_not_delete" name="role-delete" href="javascript:void(0)" title="{{ trans("lang.delete") }}"><i class="fa fa-trash"></i></a>';
        
        actions += '</span>';
        
        html.push(actions);
        
        return html;
    }

    // Delete single role
    $(document).on("click", "a[name='role-delete']", async function(e) {
        e.preventDefault();
        var id = this.id;
        if (confirm('{{ trans("lang.are_you_sure_want_to_delete_this_record") }}')) {
            try {
                await database.collection('vendor_employee_roles').doc(id).delete();
                alert('{{ trans("lang.role_deleted_successfully") }}');
                // Reload the DataTable
                $('#roleTable').DataTable().ajax.reload();
            } catch (error) {
                console.error("Error deleting role:", error);
                alert("{{ trans('lang.error_deleting_role') }}");
            }
        }
    });
    
    $(document).on("click", ".create-btn", function(e) {
        if (!vendorId) {
            alert("{{trans('lang.please_add_your_restaurant_details_before_creating_an_employee_role')}}");
            return;
        }

        window.location.href = "{{ route('role.create') }}";
    });

</script>
@endsection