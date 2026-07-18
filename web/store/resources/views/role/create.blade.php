@extends('layouts.app')

@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ trans('lang.create_role') }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('role.index') }}">{{ trans('lang.role_plural') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ trans('lang.create_role') }}</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="error_top" style="display:none"></div>
                        <div class="success_top" style="display:none"></div>

                        <form id="roleForm">
                            <div class="row restaurant_payout_create">
                                <div class="restaurant_payout_create-inner">
                                    <fieldset>
                                        <legend>{{ trans('lang.role_details') }}</legend>

                                        <div class="form-group row width-100 d-flex align-items-center">
                                            <label class="col-3 control-label">{{ trans('lang.name') }}</label>
                                            <div class="col-4">
                                                <input type="text" class="form-control" id="name" name="name" required>
                                            </div>

                                            <div class="col-4">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" id="isEnable" name="isEnable" checked>
                                                    <label class="form-check-label" for="isEnable">
                                                        {{ trans('lang.active') }} 
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="col-4 text-right">
                                                <label for="all_permission" class="form-label mr-2">
                                                    {{ trans('lang.assign_permissions') }}
                                                </label>
                                                <input type="checkbox" id="all_permission" class="checkbox_is_check" name="all_permission">
                                                <label class="control-label ml-1" for="all_permission">
                                                    {{ trans('lang.all_permissions') }}
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group row width-100">
                                            <div class="role-table width-100">
                                                <div class="col-12">
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>{{ trans('lang.menu') }}</th>
                                                                <th>{{ trans('lang.permission') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>

                                                            <!-- 1. Add Story -->
                                                            <tr>
                                                                <td><strong>{{ trans('lang.add_story') }}</strong></td>
                                                                <td>
                                                                    <input type="checkbox" class="permission" id="addstory_manage" data-title="Add Story" data-action="isActive">
                                                                    <label class="control-label2" for="addstory_manage">{{trans('lang.allow_to_manage')}}</label>
                                                                </td>
                                                            </tr>

                                                            <!-- 2. Advertisement -->
                                                            <tr>
                                                                <td><strong>{{ trans('lang.advertisement_plural') }}</strong></td>
                                                                <td>
                                                                    <input type="checkbox" class="permission" id="advert_manage" data-title="Advertisement" data-action="isActive">
                                                                    <label class="control-label2" for="advert_manage">{{trans('lang.allow_to_manage')}}</label>
                                                                </td>
                                                            </tr>

                                                            <!-- 3. Store Information -->
                                                            <tr>
                                                                <td><strong>{{ trans('lang.store_information') }}</strong></td>
                                                                <td>
                                                                    <input type="checkbox" class="permission" id="restinfo_manage" data-title="Store Information's" data-action="isActive">
                                                                    <label class="control-label2" for="restinfo_manage">{{trans('lang.allow_to_manage')}}</label>
                                                                </td>
                                                            </tr>

                                                            <!-- 4. Manage Products -->
                                                            <tr>
                                                                <td><strong>{{ trans('lang.manage_products') }}</strong></td>
                                                                <td>
                                                                    <input type="checkbox" class="permission" id="products_manage" data-title="Manage Products" data-action="isActive">
                                                                    <label class="control-label2" for="products_manage">{{trans('lang.allow_to_manage')}}</label>
                                                                </td>
                                                            </tr>

                                                            <!-- 5. Working Hours -->
                                                            <tr>
                                                                <td><strong>{{ trans('lang.woking_hours') }}</strong></td>
                                                                <td>
                                                                    <input type="checkbox" class="permission" id="hours_manage" data-title="Working Hours" data-action="isActive">
                                                                    <label class="control-label2" for="hours_manage">{{trans('lang.allow_to_manage')}}</label>
                                                                </td>
                                                            </tr>

                                                            <!-- 6. Withdraw Method -->
                                                            <tr>
                                                                <td><strong>{{ trans('lang.withdraw_method') }}</strong></td>
                                                                <td>
                                                                    <input type="checkbox" class="permission" id="withdraw_manage" data-title="Withdraw Method" data-action="isActive">
                                                                    <label class="control-label2" for="withdraw_manage">{{trans('lang.allow_to_manage')}}</label>
                                                                </td>
                                                            </tr>

                                                            <!-- 7. Manage Delivery Man -->
                                                            <tr>
                                                                <td><strong>{{ trans('lang.manage_delivery_man') }}</strong></td>
                                                                <td>
                                                                    <input type="checkbox" class="permission" id="delivery_manage" data-title="Manage Delivery Man" data-action="isActive">
                                                                    <label class="control-label2" for="delivery_manage">{{trans('lang.allow_to_manage')}}</label>
                                                                </td>
                                                            </tr>

                                                            <!-- 8. Employee Role -->
                                                            <tr>
                                                                <td><strong>{{ trans('lang.employee_role') }}</strong></td>
                                                                <td>
                                                                    <input type="checkbox" class="permission" id="emprole_manage" data-title="Employee Role" data-action="isActive">
                                                                    <label class="control-label2" for="emprole_manage">{{trans('lang.allow_to_manage')}}</label>
                                                                </td>
                                                            </tr>

                                                            <!-- 9. All Employee -->
                                                            <tr>
                                                                <td><strong>{{ trans('lang.all_employee') }}</strong></td>
                                                                <td>
                                                                    <input type="checkbox" class="permission" id="allemp_manage" data-title="All Employee" data-action="isActive">
                                                                    <label class="control-label2" for="allemp_manage">{{trans('lang.allow_to_manage')}}</label>
                                                                </td>
                                                            </tr>

                                                            <!-- 10. Add Dine in -->
                                                            <tr>
                                                                <td><strong>{{ trans('lang.add_dine_in') }}</strong></td>
                                                                <td>
                                                                    <input type="checkbox" class="permission" id="dinein_manage" data-title="Add Dine in" data-action="isActive">
                                                                    <label class="control-label2" for="dinein_manage">{{trans('lang.allow_to_manage')}}</label>
                                                                </td>
                                                            </tr>

                                                            <!-- 11. Dine in Request -->
                                                            <tr>
                                                                <td><strong>{{ trans('lang.dine_in_request') }}</strong></td>
                                                                <td>
                                                                    <input type="checkbox" class="permission" id="dinereq_manage" data-title="Dine in Request" data-action="isActive">
                                                                    <label class="control-label2" for="dinereq_manage">{{trans('lang.allow_to_manage')}}</label>
                                                                </td>
                                                            </tr>

                                                            <!-- 12. Offers -->
                                                            <tr>
                                                                <td><strong>{{ trans('lang.offers') }}</strong></td>
                                                                <td>
                                                                    <input type="checkbox" class="permission" id="offers_manage" data-title="Offers" data-action="isActive">
                                                                    <label class="control-label2" for="offers_manage">{{trans('lang.allow_to_manage')}}</label>
                                                                </td>
                                                            </tr>

                                                            <!-- 13. Special Discounts -->
                                                            <tr>
                                                                <td><strong>{{ trans('lang.special_discounts') }}</strong></td>
                                                                <td>
                                                                    <input type="checkbox" class="permission" id="discounts_manage" data-title="Special Discounts" data-action="isActive">
                                                                    <label class="control-label2" for="discounts_manage">{{trans('lang.allow_to_manage')}}</label>
                                                                </td>
                                                            </tr>

                                                            <!-- 14. Manage Order -->
                                                            <tr>
                                                                <td><strong>{{ trans('lang.manage_order') }}</strong></td>
                                                                <td>
                                                                    <input type="checkbox" class="permission" id="order_manage" data-title="Manage Order" data-action="isActive">
                                                                    <label class="control-label2" for="order_manage">{{trans('lang.allow_to_manage')}}</label>
                                                                </td>
                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="form-group col-12 text-center btm-btn mt-4 page-btn">
                                <button type="button" class="btn btn-primary" id="saveEmployeeRole">
                                    <i class="fa fa-save"></i> {{ trans('lang.save') }}
                                </button>
                                <a href="{{ route('role.index') }}" class="btn btn-default">
                                    <i class="fa fa-undo"></i> {{ trans('lang.cancel') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    var vendorUserId = "{{ $vendorUserId }}";
    var vendorId = "";    
    var authRole = "{{ $authRole }}";
    var empVendorId = "{{ $empVendorId }}";
    
    document.addEventListener("DOMContentLoaded", async function() {  
        if (authRole === 'employee') {               
            const perm = await getEmployeePermissionForTitle(vendorUserId, "Employee Role");
            if (!(perm.isActive ?? true)) {
                alert('{{ trans("lang.no_permission") }}');
                $('.page-btn').hide();
                $('.restaurant_payout_create').html('<p class="text-center text-danger font-weight-bold">{{ trans("lang.no_permission") }}</p>');
                return;
            }
        } 
        vendorUserId = "{{ trim($vendorUserId ?? '') }}";

        try {
            if(authRole == 'vendor'){
                var snapshot = await database
                    .collection('vendors')
                    .where('author', '==', vendorUserId)
                    .get();

                if (!snapshot.empty) {
                    const doc = snapshot.docs[0];
                    vendorId = doc.id;
                }
            }else{
                var snapshot = await database
                    .collection('vendors')
                    .where('id', '==', empVendorId)
                    .get();

                if (!snapshot.empty) {
                    const doc = snapshot.docs[0];
                    vendorId = doc.id;
                }
            }
        } catch (e) {
            console.error("Error fetching vendor:", e);
        }
    });

    function collectPermissions() {
        const permissions = [];
        
        $('tbody tr').each(function () {
            const $row = $(this);
            const $permission = $row.find('.permission');
            
            if ($permission.length === 0) return;
            
            const title = $permission.data('title');
            permissions.push({
                title: title,
                isActive: $permission.is(':checked')
            });
        });
        
        return permissions;
    }

    async function saveVendorEmployeeRole() {
        if (!vendorId) {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append(
                "<p>{{ trans('lang.vendor_id_not_found_please_try_again') }}</p>");
            window.scrollTo(0, 0);
            return;
        }

        const name = $('#name').val().trim();
        if (!name) {            
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append(
                "<p>{{ trans('lang.please_enter_role_name') }}</p>");
            window.scrollTo(0, 0);
            return;
        }
        const checkedCount = $('.permission:checked').length;
        if (checkedCount === 0) {
            $(".error_top").show().html("<p>{{ trans('lang.at_least_one_permission_required') }}</p>");  
            window.scrollTo(0, 0);
            return;
        }
        const id = database.collection('tmp').doc().id;
        const isEnable = $('#isEnable').is(':checked');
        try {
            await database.collection('vendor_employee_roles')
                .doc(id)
                .set({
                    id: id,
                    vendorId: vendorId,
                    isEnable: isEnable,
                    title: name,
                    permissions: collectPermissions(),
                });            
            window.location.href = "{{ route('role.index') }}";
        } catch (e) {
            console.error("Save failed:", e);            
        }
    }

    $('#saveEmployeeRole').on('click', async function () {
        await saveVendorEmployeeRole();
    });

    // All permissions checkbox handler
    $('#all_permission').on('change', function () {
        $('.permission').prop('checked', this.checked);
    });

    // Individual permission checkbox handler
    $('.permission').on('change', function() {
        if (!this.checked && $('#all_permission').is(':checked')) {
            $('#all_permission').prop('checked', false);
        }
        
        const totalPermissions = $('.permission').length;
        const checkedPermissions = $('.permission:checked').length;
        if (checkedPermissions === totalPermissions) {
            $('#all_permission').prop('checked', true);
        }
    });
</script>
@endsection