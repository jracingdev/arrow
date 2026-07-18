@extends('layouts.app')

@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ trans('lang.edit_role') }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('role.index') }}">{{ trans('lang.role_plural') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ trans('lang.edit_role') }}</li>
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
                                                    <input type="checkbox" class="form-check-input" id="isEnable" name="isEnable">
                                                    <label class="form-check-label" for="isEnable">
                                                        {{ trans('lang.active') }}
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="col-4 text-right">
                                                <label for="all_permission" class="form-label mr-2">
                                                    {{ trans('lang.assign_permissions') }}
                                                </label>
                                                <input type="checkbox" id="all_permission" name="all_permission">
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
                                                                    <label class="control-label2" for="addstory_manage">{{ trans('lang.allow_to_manage') }}</label>
                                                                </td>
                                                            </tr>

                                                            <!-- 2. Advertisement -->
                                                            <tr>
                                                                <td><strong>{{ trans('lang.advertisement_plural') }}</strong></td>
                                                                <td>
                                                                    <input type="checkbox" class="permission" id="advert_manage" data-title="Advertisement" data-action="isActive">
                                                                    <label class="control-label2" for="advert_manage">{{ trans('lang.allow_to_manage') }}</label>
                                                                </td>
                                                            </tr>

                                                            <!-- 3. Restaurant Information -->
                                                            <tr>
                                                                <td><strong>{{ trans('lang.store_information') }}</strong></td>
                                                                <td>
                                                                    <input type="checkbox" class="permission" id="restinfo_manage" data-title="Store Information's" data-action="isActive">
                                                                    <label class="control-label2" for="restinfo_manage">{{ trans('lang.allow_to_manage') }}</label>
                                                                </td>
                                                            </tr>

                                                            <!-- 4. Manage Products -->
                                                            <tr>
                                                                <td><strong>{{ trans('lang.manage_products') }}</strong></td>
                                                                <td>
                                                                    <input type="checkbox" class="permission" id="products_manage" data-title="Manage Products" data-action="isActive">
                                                                    <label class="control-label2" for="products_manage">{{ trans('lang.allow_to_manage') }}</label>
                                                                </td>
                                                            </tr>

                                                            <!-- 5. Working Hours -->
                                                            <tr>
                                                                <td><strong>{{ trans('lang.woking_hours') }}</strong></td>
                                                                <td>
                                                                    <input type="checkbox" class="permission" id="hours_manage" data-title="Working Hours" data-action="isActive">
                                                                    <label class="control-label2" for="hours_manage">{{ trans('lang.allow_to_manage') }}</label>
                                                                </td>
                                                            </tr>

                                                            <!-- 6. Withdraw Method -->
                                                            <tr>
                                                                <td><strong>{{ trans('lang.withdraw_method') }}</strong></td>
                                                                <td>
                                                                    <input type="checkbox" class="permission" id="withdraw_manage" data-title="Withdraw Method" data-action="isActive">
                                                                    <label class="control-label2" for="withdraw_manage">{{ trans('lang.allow_to_manage') }}</label>
                                                                </td>
                                                            </tr>

                                                            <!-- 7. Manage Delivery Man -->
                                                            <tr>
                                                                <td><strong>{{ trans('lang.manage_delivery_man') }}</strong></td>
                                                                <td>
                                                                    <input type="checkbox" class="permission" id="delivery_manage" data-title="Manage Delivery Man" data-action="isActive">
                                                                    <label class="control-label2" for="delivery_manage">{{ trans('lang.allow_to_manage') }}</label>
                                                                </td>
                                                            </tr>

                                                            <!-- 8. Employee Role -->
                                                            <tr>
                                                                <td><strong>{{ trans('lang.employee_role') }}</strong></td>
                                                                <td>
                                                                    <input type="checkbox" class="permission" id="emprole_manage" data-title="Employee Role" data-action="isActive">
                                                                    <label class="control-label2" for="emprole_manage">{{ trans('lang.allow_to_manage') }}</label>
                                                                </td>
                                                            </tr>

                                                            <!-- 9. All Employee -->
                                                            <tr>
                                                                <td><strong>{{ trans('lang.all_employee') }}</strong></td>
                                                                <td>
                                                                    <input type="checkbox" class="permission" id="allemp_manage" data-title="All Employee" data-action="isActive">
                                                                    <label class="control-label2" for="allemp_manage">{{ trans('lang.allow_to_manage') }}</label>
                                                                </td>
                                                            </tr>

                                                            <!-- 10. Add Dine in -->
                                                            <tr>
                                                                <td><strong>{{ trans('lang.add_dine_in') }}</strong></td>
                                                                <td>
                                                                    <input type="checkbox" class="permission" id="dinein_manage" data-title="Add Dine in" data-action="isActive">
                                                                    <label class="control-label2" for="dinein_manage">{{ trans('lang.allow_to_manage') }}</label>
                                                                </td>
                                                            </tr>

                                                            <!-- 11. Dine in Request -->
                                                            <tr>
                                                                <td><strong>{{ trans('lang.dine_in_request') }}</strong></td>
                                                                <td>
                                                                    <input type="checkbox" class="permission" id="dinereq_manage" data-title="Dine in Request" data-action="isActive">
                                                                    <label class="control-label2" for="dinereq_manage">{{ trans('lang.allow_to_manage') }}</label>
                                                                </td>
                                                            </tr>

                                                            <!-- 12. Offers -->
                                                            <tr>
                                                                <td><strong>{{ trans('lang.offers') }}</strong></td>
                                                                <td>
                                                                    <input type="checkbox" class="permission" id="offers_manage" data-title="Offers" data-action="isActive">
                                                                    <label class="control-label2" for="offers_manage">{{ trans('lang.allow_to_manage') }}</label>
                                                                </td>
                                                            </tr>

                                                            <!-- 13. Special Discounts -->
                                                            <tr>
                                                                <td><strong>{{ trans('lang.special_discounts') }}</strong></td>
                                                                <td>
                                                                    <input type="checkbox" class="permission" id="discounts_manage" data-title="Special Discounts" data-action="isActive">
                                                                    <label class="control-label2" for="discounts_manage">{{ trans('lang.allow_to_manage') }}</label>
                                                                </td>
                                                            </tr>

                                                            <!-- 14. Manage Order -->
                                                            <tr>
                                                                <td><strong>{{ trans('lang.manage_order') }}</strong></td>
                                                                <td>
                                                                    <input type="checkbox" class="permission" id="order_manage" data-title="Manage Order" data-action="isActive">
                                                                    <label class="control-label2" for="order_manage">{{ trans('lang.allow_to_manage') }}</label>
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

                            <div class="form-group col-12 text-center btm-btn mt-4">
                                <button type="button" class="btn btn-primary" id="updateEmployeeRole">
                                    <i class="fa fa-save"></i> {{ trans('lang.update') }}
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
    const roleId = "{{ $id ?? '' }}";

    document.addEventListener('DOMContentLoaded', async function () {

        if (!roleId) {
            alert("{{ trans('lang.role_id_is_missing') }}");
            return;
        }

        try {
            const docRef = database.collection('vendor_employee_roles').doc(roleId);
            const doc = await docRef.get();

            if (!doc.exists) {
                alert("{{ trans('lang.role_not_found') }}");
                return;
            }

            const data = doc.data();

            document.getElementById('name').value = data.title || '';
            document.getElementById('isEnable').checked = data.isEnable === true;

            // Load single isActive per permission group
            if (data.permissions && Array.isArray(data.permissions)) {
                data.permissions.forEach(perm => {
                    const title = perm.title;
                    const el = document.querySelector(`.permission[data-title="${title}"][data-action="isActive"]`);
                    if (el) {
                        el.checked = !!perm.isActive;
                    }
                });
            }

            // Update "select all" checkbox state
            const total = document.querySelectorAll('.permission').length;
            const checked = document.querySelectorAll('.permission:checked').length;
            document.getElementById('all_permission').checked = (total > 0 && checked === total);

        } catch (e) {
            console.error("Error loading role:", e);
            alert("{{ trans('lang.error_loading_role') }}");
        }
    });

    function collectPermissions() {
        const permissions = [];

        document.querySelectorAll('tbody tr').forEach(row => {
            const checkbox = row.querySelector('.permission[data-action="isActive"]');
            if (!checkbox) return;

            const title = checkbox.dataset.title;
            permissions.push({
                title: title,
                isActive: checkbox.checked
            });
        });

        return permissions;
    }

    async function updateVendorEmployeeRole() {
        if (!roleId) {
            alert("{{ trans('lang.role_id_is_missing') }}");
            return;
        }

        const name = document.getElementById('name').value.trim();
        if (!name) {
            alert("{{ trans('lang.please_enter_role_name') }}");
            return;
        }
        const checkedCount = $('.permission:checked').length;
        if (checkedCount === 0) {
            $(".error_top").show().html("<p>{{ trans('lang.at_least_one_permission_required') }}</p>");  
            window.scrollTo(0, 0);
            return;
        }

        const isEnable = document.getElementById('isEnable').checked;

        try {
            await database.collection('vendor_employee_roles')
                .doc(roleId)
                .set({
                    title: name,
                    isEnable: isEnable,
                    permissions: collectPermissions()
                }, { merge: true });

            window.location.href = "{{ route('role.index') }}";

        } catch (e) {
            console.error("Update failed:", e);
            alert("{{ trans('lang.error_updating_role') }}");
        }
    }

    document.getElementById('updateEmployeeRole').addEventListener('click', async () => {
        await updateVendorEmployeeRole();
    });

    // Select / deselect all
    document.getElementById('all_permission').addEventListener('change', function () {
        document.querySelectorAll('.permission').forEach(cb => {
            cb.checked = this.checked;
        });
    });

    // Uncheck "all" when any single permission is unchecked
    document.querySelectorAll('.permission').forEach(cb => {
        cb.addEventListener('change', function () {
            const total = document.querySelectorAll('.permission').length;
            const checked = document.querySelectorAll('.permission:checked').length;
            document.getElementById('all_permission').checked = (checked === total);
        });
    });
</script>
@endsection