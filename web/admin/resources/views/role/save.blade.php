@extends('layouts.app')

@section('content')
    <div class="page-wrapper">
       <div class="row page-titles">
       <div class="col-md-5 align-self-center">
              <h3 class="text-themecolor">{{trans('lang.create_role')}}</h3>
       </div>

       <div class="col-md-7 align-self-center">
              <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
              <li class="breadcrumb-item"><a href="{{ url('role') }}">{{trans('lang.role_plural')}}</a></li>
              <li class="breadcrumb-item active">{{trans('lang.create_role')}}</li>
              </ol>
       </div>
       </div>

       <div class="card-body">

              <div class="error_top" style="display:none"></div>


              <form action="{{route('role.store')}}" method="post" id="roleForm">
                     @csrf
                     <div class="row vendor_payout_create">

                     <div class="vendor_payout_create-inner">

                            <fieldset>
                            <legend>{{trans('lang.role_details')}}</legend>
                            <div class="form-group row width-100 d-flex">
                                   <label class="col-3 control-label">{{trans('lang.name')}}</label>
                                   <div class="col-6">
                                          <input type="text" class="form-control" id="name" name="name">
                                   </div>
                                   <div class="col-6 text-right">
                                          <label for="permissions"
                                                 class="form-label">{{trans('lang.assign_permissions')}}</label>
                                          <div class="text-right">
                                          <input type="checkbox" name="all_permission" id="all_permission">
                                          <label class="control-label"
                                                 for="all_permission">{{trans('lang.all_permissions')}}</label>
                                          </div>
                                   </div>

                            </div>

                            <div class="form-group row width-100">

                                   <div class="role-table width-100">
                                          <div class="col-12">
                                          <table class="table table-striped">
                                                 <thead>
                                                 <th>Menu</th>
                                                 <th>Permission</th>
                                                 </thead>
                                                 <tbody>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.section_plural')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="section-service-list"
                                                               value="section-service.list" name="section-service[]"
                                                               class="permission">
                                                        <label class="contol-label2"
                                                               for="section-service-list">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="section-service-save"
                                                               value="section.service.save"
                                                               name="section-service[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="section-service-save">{{trans('lang.create')}}</label>

                                                        <input type="checkbox" id="section-service-edit"
                                                               value="section.service.edit"
                                                               name="section-service[]" class="permission">
                                                        <label class=" contol-label2"
                                                               for="section-service-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="section-service-delete"
                                                               value="section.service.delete"
                                                               name="section-service[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="section-service-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                                 </tr>


                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.role_plural')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="role-list" value="role.index"
                                                               name="roles[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="role-list">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="role-save" value="role.save"
                                                               name="roles[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="role-save">{{trans('lang.create')}}</label>

                                                        <input type="checkbox" id="role-store" value="role.store"
                                                               name="roles[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="role-store">{{trans('lang.store')}}</label>

                                                        <input type="checkbox" id="role-edit" value="role.edit"
                                                               name="roles[]" class="permission">
                                                        <label class=" contol-label2"
                                                               for="role-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="role-update" value="role.update"
                                                               name="roles[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="role-update">{{trans('lang.update')}}</label>

                                                        <input type="checkbox" id="role-delete" value="role.delete"
                                                               name="roles[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="role-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                                 </tr>
                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.admin_plural')}}</strong>

                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="admin-list" value="admin.users"
                                                               name="admins[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="admin-list">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="admin-create" value="admin.users.create"
                                                               name="admins[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="admin-create">{{trans('lang.create')}}</label>

                                                        <input type="checkbox" id="admin-store" value="admin.users.store"
                                                               name="admins[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="admin-store">{{trans('lang.store')}}</label>

                                                        <input type="checkbox" id="admin-edit" value="admin.users.edit"
                                                               name="admins[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="admin-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="admin-update" value="admin.users.update"
                                                               name="admins[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="admin-update">{{trans('lang.update')}}</label>

                                                        <input type="checkbox" id="admin-delete" value="admin.users.delete"
                                                               name="admins[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="admin-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                                 </tr>
                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.user_customer')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="user-list" value="users" name="users[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="user-list">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="user-create" value="users.create"
                                                               name="users[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="user-create">{{trans('lang.create')}}</label>

                                                        <input type="checkbox" id="user-edit" value="users.edit"
                                                               name="users[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="user-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="user-view" value="users.view"
                                                               name="users[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="user-view">{{trans('lang.view')}}</label>

                                                        <input type="checkbox" id="user-delete" value="users.delete"
                                                               name="users[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="user-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                                 </tr>
                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.owner_vendor')}}</strong>

                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="vendors-list" value="vendors"
                                                               name="vendors[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="vendors-list">{{trans('lang.list')}}</label>
                                                        <input type="checkbox" id="vendors-create" value="vendors.create"
                                                            name="vendors[]" class="permission">
                                                        <label class=" control-label2"
                                                            for="vendors-create">{{ trans('lang.create') }}</label>
                                                            <input type="checkbox" id="vendors-edit" value="vendors.edit"
                                                            name="vendors[]" class="permission">
                                                        <label class=" control-label2"
                                                            for="vendors-edit">{{ trans('lang.edit') }}</label>
                                                        <input type="checkbox" id="vendors-delete" value="vendors.delete"
                                                               name="vendors[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="vendors-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                                 </tr>
                                          
                                          <tr>
                                                 <td>
                                                 <strong>{{trans('lang.provider_plural')}}</strong>

                                                 </td>
                                                 <td>
                                                 <input type="checkbox" id="providers-list" value="providers"
                                                        name="providers[]" class="permission">
                                                 <label class="contol-label2"
                                                        for="providers-list">{{trans('lang.list')}}</label>

                                                 <input type="checkbox" id="providers-create" value="providers.create"
                                                        name="providers[]" class="permission">
                                                 <label class="contol-label2"
                                                        for="providers-create">{{trans('lang.create')}}</label>

                                                 <input type="checkbox" id="providers-edit" value="providers.edit"
                                                        name="providers[]" class="permission">
                                                 <label class="contol-label2"
                                                        for="providers-edit">{{trans('lang.edit')}}</label>

                                                 <input type="checkbox" id="providers-view" value="providers.view"
                                                        name="providers[]" class="permission">
                                                 <label class="contol-label2"
                                                        for="providers-view">{{trans('lang.view')}}</label>      

                                                 <input type="checkbox" id="providers-delete" value="providers.delete"
                                                        name="providers[]" class="permission">
                                                 <label class="contol-label2"
                                                        for="providers-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                          </tr>
                                   

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.vendor_plural')}}</strong>

                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="stores-list" value="stores"
                                                               name="stores[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="stores-list">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="stores-create"
                                                               value="stores.create" name="stores[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="stores-create">{{trans('lang.create')}}</label>

                                                        <input type="checkbox" id="stores-edit"
                                                               value="stores.edit" name="stores[]"
                                                               class="permission">
                                                        <label class="contol-label2"
                                                               for="stores-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="stores-view"
                                                               value="stores.view" name="stores[]"
                                                               class="permission">
                                                        <label class="contol-label2"
                                                               for="stores-view">{{trans('lang.view')}}</label>

                                                        <input type="checkbox" id="stores-copy"
                                                               value="stores.copy" name="stores[]"
                                                               class="permission">
                                                        <label class="contol-label2"
                                                               for="stores-copy">{{trans('lang.copy')}}</label>

                                                        <input type="checkbox" id="stores-delete" value="stores.delete"
                                                               name="stores[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="stores-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                                 </tr>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.driver_plural')}}</strong>

                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="drivers-list" value="drivers"
                                                               name="drivers[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="drivers-list">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="drivers-create" value="drivers.create"
                                                               name="drivers[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="drivers-create">{{trans('lang.create')}}</label>

                                                        <input type="checkbox" id="drivers-edit" value="drivers.edit"
                                                               name="drivers[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="drivers-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="drivers-view" value="drivers.view"
                                                               name="drivers[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="drivers-view">{{trans('lang.view')}}</label>

                                                        <input type="checkbox" id="drivers-delete" value="drivers.delete"
                                                               name="drivers[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="drivers-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                                 </tr>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.category_plural')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="categories-list" value="categories"
                                                               name="categories[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="categories-list">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="categories-create"
                                                               value="categories.create" name="categories[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="categories-create">{{trans('lang.create')}}</label>

                                                        <input type="checkbox" id="categories-edit" value="categories.edit"
                                                               name="categories[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="categories-edit">{{trans('lang.edit')}}</label>


                                                        <input type="checkbox" id="categories-delete"
                                                               value="categories.delete"
                                                               name="categories[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="categories-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                                 </tr>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.brand')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="brands-list" value="brands"
                                                               name="brands[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="brands-list">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="brands-create"
                                                               value="brands.create" name="brands[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="brands-create">{{trans('lang.create')}}</label>

                                                        <input type="checkbox" id="brands-edit" value="brands.edit"
                                                               name="brands[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="brands-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="brands-delete" value="brands.delete"
                                                               name="brands[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="brands-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                                 </tr>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.destination')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="destinations-list" value="destinations"
                                                               name="destinations[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="destinations-list">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="destinations-create"
                                                               value="destinations.create" name="destinations[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="destinations-create">{{trans('lang.create')}}</label>

                                                        <input type="checkbox" id="destinations-edit"
                                                               value="destinations.edit"
                                                               name="destinations[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="destinations-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="destinations-delete"
                                                               value="destinations.delete"
                                                               name="destinations[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="destinations-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                                 </tr>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.item_attribute_plural')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="item-attributes-list"
                                                               value="item.attributes"
                                                               name="item-attributes[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="item-attributes-list">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="item-attributes-create"
                                                               value="item.attributes.create" name="item-attributes[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="item-attributes-create">{{trans('lang.create')}}</label>

                                                        <input type="checkbox" id="item-attributes-edit"
                                                               value="item.attributes.edit"
                                                               name="item-attributes[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="item-attributes-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="item-attributes-delete"
                                                               value="item.attributes.delete"
                                                               name="item-attributes[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="item-attributes-delete">{{trans('lang.delete')}}</label>
                                                 </td>
                                                 </tr>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.review_attribute_plural')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="review-attributes-list"
                                                               value="review.attributes"
                                                               name="review-attributes[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="review-attributes-list">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="review-attributes-create"
                                                               value="review.attributes.create" name="review-attributes[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="review-attributes-create">{{trans('lang.create')}}</label>

                                                        <input type="checkbox" id="review-attributes-edit"
                                                               value="review.attributes.edit"
                                                               name="review-attributes[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="review-attributes-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="review-attributes-delete"
                                                               value="review.attributes.delete"
                                                               name="review-attributes[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="review-attributes-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                                 </tr>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.report_plural')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="report-sales"
                                                               value="sales"
                                                               name="report[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="report-sales">{{trans('lang.reports_sale')}}</label>

                                                 </td>
                                                 </tr>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.item_plural')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="items-list" value="items" name="items[]"
                                                               class="permission">
                                                        <label class="contol-label2"
                                                               for="items-list">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="items-create" value="items.create"
                                                               name="items[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="items-create">{{trans('lang.create')}}</label>

                                                        <input type="checkbox" id="items-edit" value="items.edit"
                                                               name="items[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="items-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="items-delete"
                                                               value="items.delete"
                                                               name="items[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="items-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                                 </tr>
                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.god_eye')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="god-eye" value="map" name="god-eye[]"
                                                               class="permission">

                                                        <label class="control-label2"
                                                               for="god-eye">{{trans('lang.view')}}</label>
                                                 </td>
                                                 </tr>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.order_plural')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="orders-list" value="orders"
                                                               name="orders[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="orders-list">{{trans('lang.list')}}</label>


                                                        <input type="checkbox" id="orders-edit" value="orders.edit"
                                                               name="orders[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="orders-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="orders-print" value="orders.print"
                                                               name="orders[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="orders-print">{{trans('lang.print')}}</label>

                                                        <input type="checkbox" id="orders-delete" value="orders.delete"
                                                               name="orders[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="orders-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                                 </tr>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.gift_card_plural')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="gift-card" value="gift-card.index"
                                                               name="gift-cards[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="gift-card">{{trans('lang.list')}}</label>
                                                        <input type="checkbox" id="gift-card-save" value="gift-card.save"
                                                               name="gift-cards[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="gift-card-save">{{trans('lang.create')}}</label>
                                                        <input type="checkbox" id="gift-card-edit" value="gift-card.edit"
                                                               name="gift-cards[]" class="permission">
                                                        <label class=" contol-label2"
                                                               for="gift-card-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="gift-card-delete"
                                                               value="gift-card.delete"
                                                               name="gift-cards[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="gift-card-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                                 </tr>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.coupon_plural')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="coupons" value="coupons" name="coupons[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="coupons">{{trans('lang.list')}}</label>
                                                        <input type="checkbox" id="coupons-create" value="coupons.create"
                                                               name="coupons[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="coupons-create">{{trans('lang.create')}}</label>
                                                        <input type="checkbox" id="coupons-edit" value="coupons.edit"
                                                               name="coupons[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="coupons-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="coupons-delete" value="coupons.delete"
                                                               name="coupons[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="coupons-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                                 </tr>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.menu_items')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="banners" value="banners"
                                                               name="banners[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="banners">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="banners-create"
                                                               value="banners.create" name="banners[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="banners-create">{{trans('lang.create')}}</label>

                                                        <input type="checkbox" id="banners-edit"
                                                               value="banners.edit" name="banners[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="banners-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="banners-delete" value="banners.delete"
                                                               name="banners[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="banners-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                                 </tr>
                                                 <tr>
                                                  <td>
                                                        <strong>{{ trans('lang.subscription_plans') }}</strong>
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" id="subscription-plans-list"
                                                            value="subscription-plans" name="subscription-plans[]"
                                                            class="permission">
                                                        <label class=" control-label2"
                                                            for="subscription-plans-list">{{ trans('lang.list') }}</label>
                                                        <input type="checkbox" id="subscription-plans-create"
                                                            value="subscription-plans.create"
                                                            name="subscription-plans[]" class="permission">
                                                        <label class=" control-label2"
                                                            for="subscription-plans-create">{{ trans('lang.create') }}</label>
                                                        <input type="checkbox" id="subscription-plans-edit"
                                                            value="subscription-plans.edit" name="subscription-plans[]"
                                                            class="permission">
                                                        <label class=" control-label2"
                                                            for="subscription-plans-edit">{{ trans('lang.edit') }}</label>
                                                        <input type="checkbox" id="subscription-plans-delete"
                                                            value="subscription-plans.delete"
                                                            name="subscription-plans[]" class="permission">
                                                        <label class=" control-label2"
                                                            for="subscription-plans-delete">{{ trans('lang.delete') }}</label>
                                                    </td>
                                                </tr>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <strong>{{ trans('lang.subscription_history') }}</strong>
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" id="subscription.history"
                                                            value="subscription.history" name="subscription-history[]"
                                                            class="permission">
                                                        <label class=" control-label2"
                                                            for="subscription.history">{{ trans('lang.list') }}</label>
                                                    </td>
                                                </tr>
                                                 
                                                 
                                                 <tr>
                                                        <td>
                                                        <strong>{{trans('lang.ondemandcategory_plural')}}</strong>
                                                        </td>
                                                        <td>
                                                        <input type="checkbox" id="ondemand-categories-list"
                                                               value="ondemand.categories"
                                                               name="ondemand-categories[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="ondemand-categories-list">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="ondemand-categories-create"
                                                               value="ondemand.categories.create"
                                                               name="ondemand-categories[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="ondemand-categories-create">{{trans('lang.create')}}</label>

                                                        <input type="checkbox" id="ondemand-categories-edit"
                                                               value="ondemand.categories.edit"
                                                               name="ondemand-categories[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="ondemand-categories-edit">{{trans('lang.edit')}}</label>


                                                        <input type="checkbox" id="ondemand-categories-delete"
                                                               value="ondemand.categories.delete"
                                                               name="ondemand-categories[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="ondemand-categories-delete">{{trans('lang.delete')}}</label>

                                                        </td>
                                                 </tr>
                                                 <tr>
                                                        <td>
                                                        <strong>{{trans('lang.ondemandcoupon_plural')}}</strong>
                                                        </td>
                                                        <td>
                                                        <input type="checkbox" id="ondemand-coupons-list"
                                                               value="ondemand.coupons"
                                                               name="ondemand-coupons[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="ondemand-coupons-list">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="ondemand-coupons-create"
                                                               value="ondemand.coupons.create"
                                                               name="ondemand-coupons[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="ondemand-coupons-create">{{trans('lang.create')}}</label>

                                                        <input type="checkbox" id="ondemand-coupons-edit"
                                                               value="ondemand.coupons.edit"
                                                               name="ondemand-coupons[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="ondemand-coupons-edit">{{trans('lang.edit')}}</label>


                                                        <input type="checkbox" id="ondemand-coupons-delete"
                                                               value="ondemand.coupons.delete"
                                                               name="ondemand-coupons[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="ondemand-coupons-delete">{{trans('lang.delete')}}</label>

                                                        </td>
                                                 </tr>

                                                 <tr>
                                                        <td>
                                                        <strong>{{trans('lang.ondemand_service')}}</strong>
                                                        </td>
                                                        <td>
                                                        <input type="checkbox" id="ondemand-services-list"
                                                               value="ondemand.services"
                                                               name="ondemand-services[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="ondemand-services-list">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="ondemand-services-create"
                                                               value="ondemand.services.create"
                                                               name="ondemand-services[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="ondemand-services-create">{{trans('lang.create')}}</label>

                                                        <input type="checkbox" id="ondemand-services-edit"
                                                               value="ondemand.services.edit"
                                                               name="ondemand-services[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="ondemand-services-edit">{{trans('lang.edit')}}</label>


                                                        <input type="checkbox" id="ondemand-services-delete"
                                                               value="ondemand.services.delete"
                                                               name="ondemand-services[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="ondemand-services-delete">{{trans('lang.delete')}}</label>

                                                        </td>
                                                 </tr>
                                                 <tr>
                                                        <td>
                                                        <strong>{{trans('lang.ondemand_bookings')}}</strong>
                                                        </td>
                                                        <td>
                                                        <input type="checkbox" id="ondemand-bookings-list"
                                                               value="ondemand.bookings.index"
                                                               name="ondemand-bookings[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="ondemand-bookings-list">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="ondemand-bookings-print"
                                                               value="ondemand.bookings.print"
                                                               name="ondemand-bookings[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="ondemand-bookings-print">{{trans('lang.print')}}</label>


                                                        <input type="checkbox" id="ondemand-bookings-edit"
                                                               value="ondemand.bookings.edit"
                                                               name="ondemand-bookings[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="ondemand-bookings-edit">{{trans('lang.edit')}}</label>


                                                        <input type="checkbox" id="ondemand-bookings-delete"
                                                               value="ondemand.bookings.delete"
                                                               name="ondemand-bookings[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="ondemand-bookings-delete">{{trans('lang.delete')}}</label>

                                                        </td>
                                                 </tr>
                                                 <tr>
                                                        <td>
                                                        <strong>{{trans('lang.ondemand_workers')}}</strong>
                                                        </td>
                                                        <td>
                                                        <input type="checkbox" id="ondemand-workers-list"
                                                               value="ondemand.workers.index"
                                                               name="ondemand-workers[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="ondemand-workers-list">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="ondemand-workers-create"
                                                               value="ondemand.workers.create"
                                                               name="ondemand-workers[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="ondemand-workers-create">{{trans('lang.create')}}</label>


                                                        <input type="checkbox" id="ondemand-workers-edit"
                                                               value="ondemand.workers.edit"
                                                               name="ondemand-workers[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="ondemand-workers-edit">{{trans('lang.edit')}}</label>


                                                        <input type="checkbox" id="ondemand-workers-delete"
                                                               value="ondemand.workers.delete"
                                                               name="ondemand-workers[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="ondemand-workers-delete">{{trans('lang.delete')}}</label>

                                                        </td>
                                                 </tr>
                                                 <tr>
                                                        <td>
                                                        <strong>{{trans('lang.on_board_plural')}}</strong>
                                                        </td>
                                                        <td>
                                                        <input type="checkbox" id="ondemand-onboard-list"
                                                               value="onboard.list"
                                                               name="on-board[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="ondemand-onboard-list">{{trans('lang.list')}}</label>

                                                        
                                                        <input type="checkbox" id="ondemand-onboard-edit"
                                                               value="onboard.edit"
                                                               name="on-board[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="ondemand-onboard-edit">{{trans('lang.edit')}}</label>

                                                        </td>
                                                 </tr>

                                                 


                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.parcel_plural')}} {{trans('lang.god_eye')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="parcel-service-god-eye"
                                                               value="parcel-service-map" name="parcel-service-god-eye[]"
                                                               class="permission">

                                                        <label class="control-label2"
                                                               for="parcel-service-god-eye">{{trans('lang.view')}}</label>
                                                 </td>
                                                 </tr>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.parcelcategory_plural')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="parcel-categories-list"
                                                               value="parcel.categories"
                                                               name="parcel-categories[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="parcel-categories-list">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="parcel-categories-create"
                                                               value="parcel.categories.create" name="parcel-categories[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="parcel-categories-create">{{trans('lang.create')}}</label>

                                                        <input type="checkbox" id="parcel-categories-edit"
                                                               value="parcel.categories.edit"
                                                               name="parcel-categories[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="parcel-categories-edit">{{trans('lang.edit')}}</label>


                                                        <input type="checkbox" id="parcel-categories-delete"
                                                               value="parcel.categories.delete"
                                                               name="parcel-categories[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="parcel-categories-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                                 </tr>
                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.parcel_weight')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="parcel-weight-list"
                                                               value="parcel.weight"
                                                               name="parcel-weight[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="parcel-weight-list">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="parcel-weight-create"
                                                               value="parcel.weight.create" name="parcel-weight[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="parcel-weight-create">{{trans('lang.create')}}</label>

                                                        <input type="checkbox" id="parcel-weight-edit"
                                                               value="parcel.weight.edit"
                                                               name="parcel-weight[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="parcel-weight-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="parcel-weight-delete"
                                                               value="parcel.weight.delete"
                                                               name="parcel-weight[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="parcel-weight-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                                 </tr>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.parcel_coupons')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="parcel-coupons" value="parcel.coupons"
                                                               name="parcel-coupons[]"
                                                               class="permission">
                                                        <label class="contol-label2"
                                                               for="parcel-coupons">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="parcel-coupons-create"
                                                               value="parcel.coupons.create"
                                                               name="parcel-coupons[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="parcel-coupons-create">{{trans('lang.create')}}</label>

                                                        <input type="checkbox" id="parcel-coupons-edit"
                                                               value="parcel.coupons.edit"
                                                               name="parcel-coupons[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="parcel-coupons-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="parcel-coupons-delete"
                                                               value="parcel.coupons.delete"
                                                               name="parcel-coupons[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="parcel-coupons-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                                 </tr>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.parcel_orders')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="parcel-orders-list" value="parcel.orders"
                                                               name="parcel-orders[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="parcel-orders-list">{{trans('lang.list')}}</label>


                                                        <input type="checkbox" id="parcel-orders-edit"
                                                               value="parcel.orders.edit"
                                                               name="parcel-orders[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="parcel-orders-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="parcel-orders-print"
                                                               value="parcel.orders.print"
                                                               name="parcel-orders[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="parcel-orders-print">{{trans('lang.print')}}</label>

                                                        <input type="checkbox" id="parcel-orders-delete"
                                                               value="parcel.orders.delete"
                                                               name="parcel-orders[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="parcel-orders-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                                 </tr>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.cab_service')}} {{trans('lang.god_eye')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="cab-service-god-eye"
                                                               value="cab-service-map" name="cab-service-god-eye[]"
                                                               class="permission">

                                                        <label class="control-label2"
                                                               for="cab-service-god-eye">{{trans('lang.view')}}</label>
                                                 </td>
                                                 </tr>
                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.rides')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="rides-list" value="rides"
                                                               name="rides[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="rides-list">{{trans('lang.list')}}</label>


                                                        <input type="checkbox" id="rides-edit" value="rides.edit"
                                                               name="rides[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="rides-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="rides-delete" value="rides.delete"
                                                               name="rides[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="rides-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                                 </tr>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.sos_ride')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="sos-ride-list" value="sos.rides"
                                                               name="sos-rides[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="sos-ride-list">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="sos-rides-edit" value="sos.rides.edit"
                                                               name="sos-rides[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="sos-rides-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="sos-rides-delete"
                                                               value="sos.rides.delete"
                                                               name="sos-rides[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="sos-rides-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                                 </tr>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.promo_pural')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="cab-promo" value="cab.promo"
                                                               name="cab-promo[]"
                                                               class="permission">
                                                        <label class="contol-label2"
                                                               for="cab-promo">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="cab-promo-create"
                                                               value="cab.promo.create"
                                                               name="cab-promo[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="cab-promo-create">{{trans('lang.create')}}</label>

                                                        <input type="checkbox" id="cab-promo-edit"
                                                               value="cab.promo.edit"
                                                               name="cab-promo[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="cab-promo-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="cab-promo-delete"
                                                               value="cab.promo.delete"
                                                               name="cab-promo[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="cab-promo-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                                 </tr>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.complaints')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="complaints-list" value="complaints"
                                                               name="complaints[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="complaints-list">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="complaints-edit" value="complaints.edit"
                                                               name="complaints[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="complaints-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="complaints-delete"
                                                               value="complaints.delete"
                                                               name="complaints[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="complaints-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                                 </tr>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.cab')}} {{trans('lang.vehicle_type')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="cab-vehicle-type"
                                                               value="cab-vehicle-type"
                                                               name="cab-vehicle-type[]"
                                                               class="permission">
                                                        <label class="contol-label2"
                                                               for="cab-vehicle-type">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="cab-vehicle-type-create"
                                                               value="cab-vehicle-type.create"
                                                               name="cab-vehicle-type[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="cab-vehicle-type-create">{{trans('lang.add_more')}}</label>

                                                        <input type="checkbox" id="cab-vehicle-type-edit"
                                                               value="cab-vehicle-type.edit"
                                                               name="cab-vehicle-type[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="cab-vehicle-type-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="cab-vehicle-type-delete"
                                                               value="cab-vehicle-type.delete"
                                                               name="cab-vehicle-type[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="cab-vehicle-type-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                                 </tr>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.rental_plural')}} {{trans('lang.god_eye')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="rental-plural-god-eye"
                                                               value="rental-plural-map" name="rental-plural-god-eye[]"
                                                               class="permission">

                                                        <label class="control-label2"
                                                               for="rental-plural-god-eye">{{trans('lang.view')}}</label>
                                                 </td>
                                                 </tr>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.rental_vehicle_type')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="rental-vehicle-type"
                                                               value="rental-vehicle-type"
                                                               name="rental-vehicle-type[]"
                                                               class="permission">
                                                        <label class="contol-label2"
                                                               for="rental-vehicle-type">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="rental-vehicle-type-create"
                                                               value="rental-vehicle-type.create"
                                                               name="rental-vehicle-type[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="rental-vehicle-type-create">{{trans('lang.add_more')}}</label>

                                                        <input type="checkbox" id="rental-vehicle-type-edit"
                                                               value="rental-vehicle-type.edit"
                                                               name="rental-vehicle-type[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="rental-vehicle-type-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="rental-vehicle-type-delete"
                                                               value="rental-vehicle-type.delete"
                                                               name="rental-vehicle-type[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="rental-vehicle-type-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                                 </tr>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.rental_discount')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="rental-discount" value="rental-discount"
                                                               name="rental-discount[]"
                                                               class="permission">
                                                        <label class="contol-label2"
                                                               for="rental-discount">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="rental-discount-create"
                                                               value="rental-discount.create"
                                                               name="rental-discount[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="rental-discount-create">{{trans('lang.create')}}</label>

                                                        <input type="checkbox" id="rental-discount-edit"
                                                               value="rental-discount.edit"
                                                               name="rental-discount[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="rental-discount-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="rental-discount-delete"
                                                               value="rental-discount.delete"
                                                               name="rental-discount[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="rental-discount-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                                 </tr>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.rental_orders')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="rental-orders-list" value="rental-orders"
                                                               name="rental-orders[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="rental-orders-list">{{trans('lang.list')}}</label>


                                                        <input type="checkbox" id="rental-orders-edit"
                                                               value="rental-orders.edit"
                                                               name="rental-orders[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="rental-orders-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="rental-orders-print"
                                                               value="rental-orders.print"
                                                               name="rental-orders[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="rental-orders-print">{{trans('lang.print')}}</label>

                                                        <input type="checkbox" id="rental-orders-delete"
                                                               value="rental-orders.delete"
                                                               name="rental-orders[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="rental-orders-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                                 </tr>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.rental_vehicle')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="rental-vehicle-list"
                                                               value="rental-vehicle"
                                                               name="rental-vehicle[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="rental-vehicle-list">{{trans('lang.list')}}</label>


                                                        <input type="checkbox" id="rental-vehicle-view"
                                                               value="rental-vehicle.view"
                                                               name="rental-vehicle[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="rental-vehicle-view">{{trans('lang.view')}}</label>

                                                 </td>
                                                 </tr>


                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.vehicle_manage')}} {{trans('lang.make')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="make" value="make"
                                                               name="make[]"
                                                               class="permission">
                                                        <label class="contol-label2"
                                                               for="make">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="make-create"
                                                               value="make.create"
                                                               name="make[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="make-create">{{trans('lang.add_more')}}</label>

                                                        <input type="checkbox" id="make-edit"
                                                               value="make.edit"
                                                               name="make[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="make-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="make-delete"
                                                               value="make.delete"
                                                               name="make[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="make-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                                 </tr>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.vehicle_manage')}} {{trans('lang.model')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="model" value="model"
                                                               name="model[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="model">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="model-create"
                                                               value="model.create"
                                                               name="model[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="model-create">{{trans('lang.add_more')}}</label>

                                                        <input type="checkbox" id="model-edit"
                                                               value="model.edit"
                                                               name="model[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="model-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="model-delete"
                                                               value="model.delete"
                                                               name="model[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="model-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                                 </tr>


                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.send_notification')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="notification-list" value="notification"
                                                               name="general-notifications[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="notification-list">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="notification-send"
                                                               value="notification.send" name="general-notifications[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="notification-send">{{trans('lang.create')}}</label>

                                                        <input type="checkbox" id="notification-delete"
                                                               value="notification.delete"
                                                               name="general-notifications[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="notification-delete">{{trans('lang.delete')}}</label>
                                                 </td>
                                                 </tr>
                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.dynamic_notification')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="dynamicnotification-list"
                                                               value="dynamic-notification.index"
                                                               name="dynamic-notifications[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="dynamicnotification-list">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="dynamicnotification-save"
                                                               value="dynamic-notification.save"
                                                               name="dynamic-notifications[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="dynamicnotification-save">{{trans('lang.edit')}}</label>


                                                 </td>
                                                 </tr>
                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.email_templates')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="email-template"
                                                               value="email-templates.index" name="email-template[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="email-template">{{trans('lang.list')}}</label>
                                                        <input type="checkbox" id="email-template-edit"
                                                               value="email-templates.edit" name="email-template[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="email-template-edit">{{trans('lang.edit')}}</label>

                                                 </td>
                                                 </tr>


                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.cms_plural')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="cms" value="cms" name="cms[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="cms">{{trans('lang.list')}}</label>
                                                        <input type="checkbox" id="cms-create" value="cms.create"
                                                               name="cms[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="cms-create">{{trans('lang.create')}}</label>
                                                        <input type="checkbox" id="cms-edit" value="cms.edit" name="cms[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="cms-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="cms-delete" value="cms.delete"
                                                               name="cms[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="cms-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                                 </tr>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.payment_plural')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="stores-payment" value="stores.payment"
                                                               name="stores-payment[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="stores-payment">{{trans('lang.vendor_plural')}} {{trans('lang.payment_plural')}}</label>


                                                        <input type="checkbox" id="stores-payout" value="stores.payout"
                                                               name="stores-payout[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="stores-payout">{{trans('lang.vendors_payout_table')}}</label>

                                                        <input type="checkbox" id="drivers-payment" value="drivers.payment"
                                                               name="drivers-payment[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="drivers-payment">{{trans('lang.driver_plural')}} {{trans('lang.payment_plural')}}</label>

                                                        <input type="checkbox" id="drivers-payout" value="drivers.payout"
                                                               name="drivers-payout[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="drivers-payout">{{trans('lang.drivers_payout_table')}} </label>
                                                 <input type="checkbox" id="provider-payment" value="provider.payment"
                                                        name="provider-payment[]" class="permission">
                                                 <label class="contol-label2"
                                                        for="provider-payment">{{trans('lang.provider_plural')}} {{trans('lang.payment_plural')}}</label>


                                                 <input type="checkbox" id="provider-payout" value="provider.payout"
                                                        name="provider-payout[]" class="permission">
                                                 <label class="contol-label2"
                                                        for="provider-payout">{{trans('lang.provider_payout_table')}}</label>
                                                 
                                                        <input type="checkbox" id="wallet-transaction"
                                                               value="wallet-transaction"
                                                               name="wallet-transaction[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="wallet-transaction">{{trans('lang.wallet_transaction')}} </label>

                                                        <input type="checkbox" id="payout-request-vendor"
                                                               value="payout-request.vendor"
                                                               name="payout-request-vendor[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="payout-request-vendor">{{trans('lang.vendor_plural')}} {{trans('lang.payout_request')}} </label>

                                                        <input type="checkbox" id="payout-request-driver"
                                                               value="payout-request.driver"
                                                               name="payout-request-driver[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="payout-request-driver">{{trans('lang.driver_plural')}} {{trans('lang.payout_request')}} </label>
                                                 <input type="checkbox" id="payout-request-provider"
                                                        value="payout-request.provider"
                                                        name="payout-request-provider[]" class="permission">

                                                 <label class="contol-label2"
                                                        for="payout-request-provider">{{trans('lang.provider_plural')}} {{trans('lang.payout_request')}} </label>
                                                 </td>
                                                 </tr>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.vendors_payout_plural')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="stores-payout-create"
                                                               value="stores.payout.create"
                                                               name="stores-payout[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="stores-payout-create">{{trans('lang.create')}}</label>

                                                 </td>
                                                 </tr>


                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.drivers_payout_plural')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="drivers-payout-create"
                                                               value="drivers.payout.create"
                                                               name="drivers-payout[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="drivers-payout-create">{{trans('lang.create')}} </label>


                                                 </td>
                                                 </tr>
                                                        <tr>
                                                        <td>
                                                        <strong>{{trans('lang.provider_payout_plural')}}</strong>
                                                        </td>
                                                        <td>
                                                        <input type="checkbox" id="provider-payout-create"
                                                               value="provider.payout.create"
                                                               name="provider-payout[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="provider-payout-create">{{trans('lang.create')}}</label>

                                                        </td>
                                                 </tr>


                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.app_setting_globals')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="global-setting"
                                                               value="settings.app.globals" name="global-setting[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="global-setting">{{trans('lang.update')}}</label>
                                                 </td>
                                                 </tr>
                                                 <tr>
                                                    <td>
                                                        <strong>{{ trans('lang.business_model_settings') }}</strong>
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" id="business-model"
                                                            value="business-model"
                                                            name="business-model[]" class="permission">
                                                        <label class=" control-label2"
                                                            for="business-model">{{ trans('lang.update') }}</label>
                                                    </td>
                                                </tr>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.app_setting_banners')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="app-banners-setting"
                                                               value="settings.app.banners" name="app-banners-setting[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="app-banners-setting">{{trans('lang.update')}}</label>
                                                 </td>
                                                 </tr>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.currency_plural')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="currency-list" value="currencies"
                                                               name="currency[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="currency-list">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="currency-create"
                                                               value="currencies.create" name="currency[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="currency-create">{{trans('lang.create')}}</label>

                                                        <input type="checkbox" id="currency-edit" value="currencies.edit"
                                                               name="currency[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="currency-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="currency-delete" value="currency.delete"
                                                               name="currency[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="currency-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                                 </tr>
                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.payment_methods')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="payment-method-list"
                                                               value="payment-method" name="payment-method[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="payment-method-list">{{trans('lang.update')}}</label>
                                                 </td>
                                                 </tr>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.vendor_admin_commission')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="admin-commission"
                                                               value="settings.app.adminCommission"
                                                               name="admin-commission[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="admin-commission">{{trans('lang.update')}}</label>
                                                 </td>
                                                 </tr>
                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.radios_configuration')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="radius"
                                                               value="settings.app.radiusConfiguration" name="radius[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="radius">{{trans('lang.update')}}</label>
                                                 </td>
                                                 </tr>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.tax_setting')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="tax-list" value="tax" name="tax[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="tax-list">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="tax-create" value="tax.create"
                                                               name="tax[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="tax-create">{{trans('lang.create')}}</label>

                                                        <input type="checkbox" id="tax-edit" value="tax.edit" name="tax[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="tax-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="tax-delete" value="tax.delete"
                                                               name="tax[]" class="permission">


                                                        <label class="contol-label2"
                                                               for="tax-delete">{{trans('lang.delete')}}</label>


                                                 </td>
                                                 </tr>
                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.deliveryCharge')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="delivery-charge"
                                                               value="settings.app.deliveryCharge" name="delivery-charge[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="delivery-charge">{{trans('lang.update')}}</label>
                                                 </td>
                                                 </tr>
                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.languages')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="language-list"
                                                               value="language" name="language[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="language-list">{{trans('lang.list')}}</label>

                                                        <input type="checkbox" id="language-create"
                                                               value="language.create" name="language[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="language-create">{{trans('lang.create')}}</label>

                                                        <input type="checkbox" id="language-edit"
                                                               value="language.edit" name="language[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="language-edit">{{trans('lang.edit')}}</label>

                                                        <input type="checkbox" id="language-delete" value="language.delete"
                                                               name="language[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="language-delete">{{trans('lang.delete')}}</label>

                                                 </td>
                                                 </tr>
                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.special_offer')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="special-offer"
                                                               value="setting.specialOffer" name="special-offer[]"
                                                               class="permission">

                                                        <label class="contol-label2"
                                                               for="special-offer">{{trans('lang.update')}}</label>
                                                 </td>
                                                 </tr>

                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.terms_and_conditions')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="terms" value="termsAndConditions"
                                                               name="terms[]" class="permission">

                                                        <label class="contol-label2"
                                                               for="terms">{{trans('lang.update')}}</label>
                                                 </td>
                                                 </tr>
                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.privacy_policy')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="privacy" value="privacyPolicy"
                                                               name="privacy[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="privacy">{{trans('lang.update')}}</label>
                                                 </td>
                                                 </tr>
                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.homepageTemplate')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="home-page" value="homepageTemplate"
                                                               name="home-page[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="home-page">{{trans('lang.update')}}</label>
                                                 </td>
                                                 </tr>
                                                 <tr>
                                                 <td>
                                                        <strong>{{trans('lang.footer_template')}}</strong>
                                                 </td>
                                                 <td>
                                                        <input type="checkbox" id="footer" value="footerTemplate"
                                                               name="footer[]" class="permission">
                                                        <label class="contol-label2"
                                                               for="footer">{{trans('lang.update')}}</label>
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
              </form>


              <div class="form-group col-12 text-center btm-btn">
                     <button type="button" class="btn btn-primary save-form-btn"><i
                            class="fa fa-save"></i> {{trans('lang.save')}}
                     </button>
                     <a href="{{url('role')}}" class="btn btn-default"><i class="fa fa-undo"></i>{{trans('lang.cancel')}}</a>
              </div>

       </div>
    </div>

@endsection

@section('scripts')

<script>

       $(".save-form-btn").click(async function () {

              $(".error_top").hide();
              var name = $("#name").val();

              if (name == "") {
                     $(".error_top").show();
                     $(".error_top").html("");
                     $(".error_top").append("<p>{{trans('lang.user_name_help')}}</p>");
                     window.scrollTo(0, 0);
                     return false;
              } else {
                     $('form#roleForm').submit();

              }

       });

       $('#all_permission').on('click', function () {

              if ($(this).is(':checked')) {
                     $.each($('.permission'), function () {
                     $(this).prop('checked', true);
                     });
              } else {
                     $.each($('.permission'), function () {
                     $(this).prop('checked', false);
                     });
              }

       });


</script>

@endsection