@php session_start(); @endphp
@if(@$order_complete)
    <div class="d-flex siddhi-cart-item-profile bg-white p-3">
        <p>{{trans('lang.your_order_placed_successfully')}}</p>
    </div>
@endif
@if(@$service_charge_cart)
    @php
        $digit_decimal = 0;
        $total_price = 0;
    @endphp
    @if(@$service_charge_cart['decimal_degits'])
        @php $digit_decimal = $service_charge_cart['decimal_degits']; @endphp
    @endif
    <input type="hidden" id="extraCharge" value="{{@$service_charge_cart['extra_charge']}}">
    <input type="hidden" id="orderId" value="{{@$service_charge_cart['order_id']}}">
    <input type="hidden" id="serviceTotal" value="{{@$service_charge_cart['service_total']}}">
    <input type="hidden" id="provider_id" value="{{@$service_charge_cart['provider_id']}}">
    <div class="bg-white p-3 sidebar-item-list">
        <h6 class="pb-3">{{trans('lang.price_details')}}</h6>
    </div>
    @php
        $total_price = floatval(@$service_charge_cart['sub_total']);
    @endphp
    <div class="bg-white px-3 clearfix">
        <div class="border-bottom pb-3">
            <div class="input-group-sm mb-2 input-group">
                <input placeholder="{{trans('lang.promo_help')}}"
                       value="{{@$service_charge_cart['coupon']['coupon_code']}}"
                       id="coupon_code" type="text" class="form-control">
                <div class="input-group-append">
                    <button type="button" class="btn btn-primary" data-id="{{@$service_charge_cart['id']}}"
                            data-provider="{{@$service_charge_cart['provider_id']}}" id="apply-coupon-code">
                        <i class="feather-percent"></i> {{trans('lang.apply')}}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-white p-3 clearfix btm-total">

        <p class="mb-2">
            {{trans('lang.sub_total')}}
            <span class="float-right text-dark">
                {{ formatCurrency($total_price, $service_charge_cart['currencyData']) }}                                                                        
            </span>
        </p>
        @php 
        $discount_amount = 0;
        $coupon_id = '';
        $coupon_code = '';
        $discount = '';
        $discountType = '';
        @endphp
        @if(@$service_charge_cart['coupon'] && $service_charge_cart['coupon']['discountType'])
            <hr>
            <p class="mb-1">
                @php
                    $discountType = @$service_charge_cart['coupon']['discountType'];
                    $coupon_code = @$service_charge_cart['coupon']['coupon_code'];
                    $coupon_id = @$service_charge_cart['coupon']['coupon_id'];
                    $discount = @$service_charge_cart['coupon']['discount'];
                @endphp
                @if($discountType == "Fix Price")
                    @php
                        $discount_amount = floatval($service_charge_cart['coupon']['discount']);
                    @endphp
                    @if($discount_amount > $total_price)
                        @php $discount_amount = $total_price; @endphp
                    @endif
                    @if($total_price < 0)
                        @php $total_price=0; @endphp
                    @endif
                @else
                    @php
                        $discount_amount=$service_charge_cart['coupon']['discount'];
                        $discount_amount=($total_price *$discount_amount) / 100;
                    @endphp
                    @if($discount_amount> $total_price)
                        @php $discount_amount = $total_price;@endphp
                    @endif
                    @if($total_price < 0)
                        @php $total_price=0; @endphp
                    @endif
                @endif
                {{trans('lang.total')}}
                {{trans('lang.discount')}} 
                <span class="float-right text-danger">
                    (-{{ formatCurrency($discount_amount, $service_charge_cart['currencyData']) }})                                                                        
                </span>
            </p>
            @if(isset($service_charge_cart['coupon']) && !empty($service_charge_cart['coupon']['coupon_code']))
                <div class="remove-coupon text-right">
                    <small><a href="javascript:void(0)" class="text-primary">{{ trans('lang.remove_discount') }}</a></small>
                </div>
            @endif
        @else
        @endif
        <input type="hidden" id="discount_amount" value="{{$discount_amount}}">
        <input type="hidden" id="coupon_id" value="{{$coupon_id}}">
        <input type="hidden" id="coupon_code_main" value="{{$coupon_code}}">
        <input type="hidden" id="discount" value="{{$discount}}">
        <input type="hidden" id="discountType" value="{{$discountType}}">

        @php
            $total_price = $total_price - $discount_amount;
        @endphp

        @if($service_charge_cart['platformCharge'])
        <hr>
        <p class="mb-2">
            {{trans('lang.platform_charge')}} 
            <span class="float-right text-dark">
                {{ formatCurrency($service_charge_cart['platformCharge'], $service_charge_cart['currencyData']) }}                                                                        
            </span>
        </p>
            @php $total_price =$total_price + $service_charge_cart['platformCharge'] @endphp
        @endif

        @if(!empty($service_charge_cart['taxBreakdownGrouped']))
            <hr>
            {{-- Order-level --}}
            @if($service_charge_cart['taxScope'] === 'order')
                <p class="mb-2">
                    {{trans('lang.tax_on_order_total')}} 
                    <span class="float-right text-dark">
                        {{ formatCurrency(array_sum($service_charge_cart['taxBreakdownGrouped']['order']), $service_charge_cart['currencyData']) }}                                                          
                    </span>
                </p>
            @endif
            <hr>
            {{-- Platform-level --}}
            @foreach($service_charge_cart['taxBreakdownGrouped']['platform'] ?? [] as $title => $amount)
                <p class="mb-2">
                    {{trans('lang.tax_on_platform_fee')}} 
                    <span class="float-right text-dark">
                        {{ formatCurrency($amount, $service_charge_cart['currencyData']) }}                                                          
                    </span>
                </p>
                <hr>
            @endforeach
            {{-- Total --}}
            <p class="mb-2">
                <strong>{{trans('lang.total_tax_amount')}} </strong>
                <span class="float-right text-dark">
                    <strong>{{ formatCurrency($service_charge_cart['total_tax'], $service_charge_cart['currencyData']) }}</strong>                                                          
                </span>
            </p>
        @endif

        @php $total_price = $total_price + $service_charge_cart['total_tax'] @endphp
        <hr>
        <p class="mb-2">
            <strong>{{trans('lang.total_amount')}} </strong>
            <span class="float-right text-dark">
                <strong>{{ formatCurrency($total_price, $service_charge_cart['currencyData']) }}</strong>                                                          
            </span>
        </p>

        @if($total_price && @$service_charge_cart['extra_charge'] && @$service_charge_cart['extra_charge']!=0 )
            <hr>
            <p class="mb-1">
                {{trans('lang.extra_charge')}}
                <span class="float-right text-dark">
                    {{ formatCurrency($service_charge_cart['extra_charge'], $service_charge_cart['currencyData']) }}
                </span>
            </p>
            @php $total_price=$total_price+floatval(@$service_charge_cart['extra_charge']) @endphp
        @endif
        <hr>
        <h6 class="font-weight-bold mb-0">{{trans('lang.final_total')}}
            <p class="float-right">
                <span class="float-right text-dark">
                    {{ formatCurrency($total_price, $service_charge_cart['currencyData']) }}
                </span>
            </p>
        </h6>
    </div>
    <input type="hidden" id="total_pay" value="{{round($total_price, $digit_decimal)}}">
    <div class="p-3">
        @if($total_price>0)
            <a class="btn btn-primary btn-block btn-lg" href="javascript:void(0)" onclick="payServiceCharge()">
               {{trans('lang.pay')}} 
               {{ formatCurrency($total_price, $service_charge_cart['currencyData']) }}
                <i class="feather-arrow-right"></i>
            </a>
        @endif
    </div>
@endif