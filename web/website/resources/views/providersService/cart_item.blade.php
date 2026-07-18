
@if(@$order_complete)
    <div class="d-flex siddhi-cart-item-profile bg-white p-3">
        <p>{{trans('lang.your_order_placed_successfully')}}</p>
    </div>
@endif

@if(@$extra_charge_cart['extra_charge']!='')

    <input type="hidden" id="extraCharge" value="{{@$extra_charge_cart['extra_charge']}}">
    <input type="hidden" id="extraChargeId" value="{{@$extra_charge_cart['order_id']}}">
    <div class="bg-white p-3 clearfix btm-total">
        <h6 class="font-weight-bold mb-0">{{trans('lang.total')}}
            <p class="float-right">
                <span class="currency-symbol-left"></span>
                <span>
                {{number_format(floatval(@$extra_charge_cart['extra_charge']), 2)}}
            </span>
                <span class="currency-symbol-right"></span>
            </p>
        </h6>
        <div class="p-3">
            @if(@$extra_charge_cart['extra_charge']>0)
                <a class="btn btn-primary btn-block btn-lg" href="javascript:void(0)"
                   onclick="payExtraCharge()">{{trans('lang.pay')}}
                    <span class="currency-symbol-left"></span>
                    {{number_format(floatval(@$extra_charge_cart['extra_charge']), 2)}}
                    <span class="currency-symbol-right"></span><i class="feather-arrow-right"></i></a>
            @endif
        </div>
    </div>

@else

    @if(@$ondemand_cart['id'])

        <div class="bg-white p-3 sidebar-item-list">

            <h6 class="pb-3">{{trans('lang.cart_details')}}</h6>

            <div class="product-item gold-members row align-items-center py-2 border mb-2 rounded-lg m-0" id="item">

                <input type="hidden" id="price_{{@$ondemand_cart['id']}}" value="{{floatval(@$ondemand_cart['price'])}}">
                <input type="hidden" id="price_unit" value="{{@$ondemand_cart['price_unit']}}">
                <input type="hidden" id="dis_price_{{@$ondemand_cart['id']}}" value="{{floatval(@$ondemand_cart['dis_price'])}}">
                <input type="hidden" id="photo_{{ @$ondemand_cart['id']}}" value="{{@$ondemand_cart['image']}}">
                <input type="hidden" id="name_{{@$ondemand_cart['id']}}" value="{{@$ondemand_cart['name']}}">
                <input type="hidden" id="quantity_{{@$ondemand_cart['id']}}" value="{{@$ondemand_cart['quantity']}}">
                <input type="hidden" id="total_price_{{ @$ondemand_cart['id']}}" value="{{@$ondemand_cart['total_item_price']}}">
                <input type="hidden" id="provider_id_{{@$ondemand_cart['id']}}" value="{{@$ondemand_cart['providerId']}}">
                <input type="hidden" id="image_{{@$ondemand_cart['id']}}" value="{{@$ondemand_cart['image']}}">
                <input type="hidden" id="category_id_{{@$ondemand_cart['id']}}" value="{{@$ondemand_cart['serviceCategoryId']}}">
                <input type="hidden" id="photo_{{ @$ondemand_cart['id']}}" value="{{@$ondemand_cart['image']}}">
                <input type="hidden" id="provider_id" value="{{@$ondemand_cart['providerId']}}">
                <input type="hidden" id="service_id" value="{{@$ondemand_cart['id']}}">

                <div class="media align-items-center col-md-6">
                    <div class="media-body">
                        <p class="m-0">
                            <img src="{{@$ondemand_cart['image']}}" class="img-responsive img-rounded"
                                 style="max-height: 40px; max-width: 25px;">
                            {{@$ondemand_cart['name']}}
                        </p>
                    </div>
                </div>

                <div class="d-flex align-items-center count-number-box col-md-5">
                    @if(@$ondemand_cart['price_unit']!='Hourly')
                        <span class="count-number float-right">
                            <button type="button" data-id="{{@$ondemand_cart['id']}}"
                                    class="count-number-input-cart btn-sm left dec btn btn-outline-secondary">
                                <i class="feather-minus"></i>
                            </button>
                            <input class="count-number-input count_number_{{@$ondemand_cart['id']}}" type="text" readonly
                                value="{{@$ondemand_cart['quantity']}}">
                            <button type="button" data-id="{{@$ondemand_cart['id']}}"
                                    class="count-number-input-cart btn-sm right inc btn btn-outline-secondary count_number_right">
                                <i class="feather-plus"></i>
                            </button>
                        </span>
                    @endif
                    <p class="text-gray mb-0 float-right ml-3 text-muted small">
                        <span class="currency-symbol-left"></span>
                        <span class="cart_iteam_total_{{@$ondemand_cart['id']}}">
                            {{number_format($ondemand_cart['price'], $ondemand_cart['decimal_degits'])}}
                            @if(@$ondemand_cart['price_unit']=='Hourly')
                                {{' / hr'}}
                            @endif
                        </span>
                        <span class="currency-symbol-right"></span>
                    </p>
                </div>
                <div class="close remove_item col-md-1" data-id="{{$ondemand_cart['id']}}">
                    <i class="fa fa-times"></i>
                </div>
            </div>
        </div>
    
        @if(@$ondemand_cart['price_unit']!='Hourly')
            <div class="bg-white px-3 clearfix">
                <div class="border-bottom pb-3">
                    <div class="input-group-sm mb-2 input-group">
                        <input placeholder="{{trans('lang.promo_help')}}"
                            value="{{@$ondemand_cart['coupon']['coupon_code']}}"
                            id="coupon_code" type="text" class="form-control">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary" data-id="{{@$ondemand_cart['id']}}"
                                    data-provider="{{@$ondemand_cart['providerId']}}" id="apply-coupon-code">
                                <i class="feather-percent"></i> {{trans('lang.apply')}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white px-3 clearfix schedule-order pt-3">
            <div class="border-bottom pb-3">
                <h3>{{trans('lang.booking_date_slot')}}</h3>
                <span class="text-dark">
                    <input type="datetime-local" id="scheduleTime" name="scheduleTime" value="{{(@$ondemand_cart['scheduleTime']) ? $ondemand_cart['scheduleTime'] : ""}}">
                </span>
            </div>
        </div>

        <div class="bg-white p-3 clearfix btm-total">
            @if(@$ondemand_cart['price_unit']!='Hourly')
                <p class="mb-2">
                    {{trans('lang.sub_total')}}
                    <span class="float-right text-dark">
                        {{ formatCurrency($ondemand_cart['total_item_price'], $ondemand_cart['currencyData']) }}
                    </span>
                </p>
            @endif
            
            @php 
            $couponHtml = "";
            $discountType = '';
            $coupon_code = '';
            $coupon_id = '';
            $discount = '';
            $discount_amount = 0;
            @endphp
            @if(@$ondemand_cart['coupon'] && $ondemand_cart['coupon']['discountType'])
                @if(@$ondemand_cart['price_unit']!='Hourly')
                    <hr>
                    <p class="mb-1">
                        
                        @php
                            $discountType = $ondemand_cart['coupon']['discountType'];
                            $coupon_code = $ondemand_cart['coupon']['coupon_code'];
                            $coupon_id = $ondemand_cart['coupon']['coupon_id'];
                            $discount = $ondemand_cart['coupon']['discount'];
                            $discount_amount = $ondemand_cart['coupon']['discount_amount'];
                            if($ondemand_cart['coupon']['discountType'] == "Percentage") {
                                $couponHtml = " (" . $ondemand_cart['coupon']['discount'] . "%)";
                            } 
                        @endphp
                        {{trans('lang.total')}} {{trans('lang.discount')}} 
                        <span class="float-right text-danger">
                            (-{{ formatCurrency($discount_amount, $ondemand_cart['currencyData']) }})
                        </span>
                    </p>
                    <div class="remove-coupon text-right">
                        <small><a href="javascript:void(0)" class="text-primary">{{ trans('lang.remove_discount') }}</a></small>
                    </div>
                @endif
            @endif
            
            <input type="hidden" id="discount_amount" value="{{$discount_amount}}">
            <input type="hidden" id="coupon_id" value="{{$coupon_id}}">
            <input type="hidden" id="coupon_code_main" value="{{$coupon_code}}">
            <input type="hidden" id="discount" value="{{$discount}}">
            <input type="hidden" id="discountType" value="{{$discountType}}">
            <input type="hidden" id="adminCommission" value="0">
            <input type="hidden" id="adminCommissionType" value="Fix Price">
            <input type="hidden" id="total_pay" value="{{round($ondemand_cart['total_price'], 2)}}">

            @if(@$ondemand_cart['price_unit']!='Hourly')

                <hr>

                <p class="mb-2">
                    {{ trans('lang.platform_charge') }} 
                    <span class="float-right text-dark">
                        {{ formatCurrency($ondemand_cart['platformCharge'], $ondemand_cart['currencyData']) }}
                    </span>
                </p>
                        
                <hr>
                @if(!empty($ondemand_cart['taxBreakdownGrouped']))
                    {{-- Order-level --}}
                    @if($ondemand_cart['taxScope'] === 'order')
                        <p class="mb-2">
                            {{trans('lang.tax_on_order_total')}} 
                            <span class="float-right text-dark">
                                {{ formatCurrency(array_sum($ondemand_cart['taxBreakdownGrouped']['order']), $ondemand_cart['currencyData']) }}                                                          
                            </span>
                        </p>
                    @endif
                    <hr>

                    {{-- Platform-level --}}
                    @foreach($ondemand_cart['taxBreakdownGrouped']['platform'] ?? [] as $title => $amount)
                        <p class="mb-2">
                            {{trans('lang.tax_on_platform_fee')}} 
                            <span class="float-right text-dark">
                                {{ formatCurrency($amount, $ondemand_cart['currencyData']) }}                                                          
                            </span>
                        </p>
                        <hr>
                    @endforeach
                    
                    {{-- Total --}}
                    <p class="mb-2">
                        <strong>{{trans('lang.total_tax_amount')}} </strong>
                        <span class="float-right text-dark">
                            <strong>{{ formatCurrency($ondemand_cart['total_tax'], $ondemand_cart['currencyData']) }}</strong>                                                          
                        </span>
                    </p>
                    <hr>
                @endif
            
                <hr>

                <h6 class="font-weight-bold mb-0">{{trans('lang.total')}}
                    <p class="float-right">
                        {{ formatCurrency($ondemand_cart['total_price'], $ondemand_cart['currencyData']) }}
                    </p>
                </h6>
            @endif
        </div>

        @if(@$ondemand_cart['price_unit']!='Hourly')
            <div class="p-3">
                @if($ondemand_cart['total_price']>0)
                    <a class="btn btn-primary btn-block btn-lg" href="javascript:void(0)" onclick="finalCheckout()">
                        {{trans('lang.pay')}} 
                        {{ formatCurrency($ondemand_cart['total_price'], $ondemand_cart['currencyData']) }}
                        <i class="feather-arrow-right"></i></a>
                @endif
            </div>
        @else
            <a class="btn btn-primary btn-block btn-lg" href="javascript:void(0)" onclick="finalCheckout()">{{trans('lang.book')}}<i class="feather-arrow-right"></i></a>
        @endif

    @else

        <div class="bg-white py-2">
            <div class="gold-members d-flex align-items-center justify-content-between px-3 py-2">
                <span>{{trans('lang.your_cart_is_empty')}}</span>
            </div>
        </div>

    @endif
    
@endif