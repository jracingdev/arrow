<?php

namespace App\Http\Controllers;

use App\Models\VendorUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use Illuminate\Support\Facades\Storage;
use Google\Client as Google_Client;

class ProductController extends Controller
{
    public function __construct()
    {
        if (!isset($_COOKIE['section_id']) && !isset($_COOKIE['address_name'])) {
            \Redirect::to('set-location')->send();
        }
    }

    public function productList($type, $id)
    {
        
        return view('products.list', ['type' => $type, 'id' => $id]);
    }

    public function productListAll()
    {
        return view('products.list_arrivals');
    }

    public function productDetail($id)
    {
        $cart = session()->get('cart', []);
        return view('products.detail', ['id' => $id, 'cart' => $cart]);
    }

    public function cart()
    {
        return view('checkout');
    }

    public function addToCart(Request $request)
    {

        $req = $request->all();
        $vendor_id = $req['vendor_id'];
        $id = $req['id'];

        $cart = Session::get('cart', []);

        // Initialize cart structure if empty
        if (!isset($cart['item'][$vendor_id])) {
            $cart['item'][$vendor_id] = [];
            $cart['item'] = array();
            Session::put('cart', $cart);
            Session::save();
        }

        // Add delivery charges info
        $cart['deliverychargemain'] = $req['deliveryCharge'] ?? 0;
        $cart['vendor_latitude'] = $req['vendor_latitude'];
        $cart['vendor_longitude'] = $req['vendor_longitude'];
        $cart['distanceType'] = $req['distanceType'];
        $cart['isSelfDelivery'] = $req['isSelfDelivery'];

        // Calculate distance-based delivery if cookies present
        $deliveryChargemain = @$_COOKIE['deliveryChargemain'];
        $address_lat = @$_COOKIE['address_lat'];
        $address_lng = @$_COOKIE['address_lng'];
        $vendor_latitude = @$_COOKIE['vendor_latitude'];
        $vendor_longitude = @$_COOKIE['vendor_longitude'];
        $selfDelivery = filter_var($cart['isSelfDelivery'], FILTER_VALIDATE_BOOLEAN);

        if (isset($_COOKIE['service_type']) && $_COOKIE['service_type'] == "Ecommerce Service" && isset($_COOKIE['ecommerce_delivery_charge'])) {
            $cart['deliverychargemain'] = @$_COOKIE['ecommerce_delivery_charge'];
            $cart['deliverykm'] = '';
        } else {
            if (@$deliveryChargemain && @$address_lat && @$address_lng && @$vendor_latitude && @$vendor_longitude) {
                $deliveryChargemain = json_decode($deliveryChargemain);
                if (!empty($deliveryChargemain)) {
                    if (! empty($req['distanceType'])) {
                        $distanceType = $req['distanceType'];
                    }else{
                        $distanceType = 'km';
                    }
                    $delivery_charges_per_km = $deliveryChargemain->delivery_charges_per_km;
                    $minimum_delivery_charges = $deliveryChargemain->minimum_delivery_charges;
                    $minimum_delivery_charges_within_km = $deliveryChargemain->minimum_delivery_charges_within_km;
                    $kmradius = $this->distance($address_lat, $address_lng, $vendor_latitude, $vendor_longitude, $distanceType);
                    if ($minimum_delivery_charges_within_km > $kmradius) {
                        $cart['deliverychargemain'] = $minimum_delivery_charges;
                    } else {
                        $cart['deliverychargemain'] = round(($kmradius * $delivery_charges_per_km), 2);
                    }
                    $cart['deliverykm'] = $kmradius;
                }
            }
        }

        // Delivery or takeaway
        $cart['delivery_option'] = Session::get('takeawayOption') === "true" ? "takeaway" : "delivery";
        $cart['deliveryCharge'] = ($cart['delivery_option'] === 'delivery' && !$selfDelivery) ? $cart['deliverychargemain'] : 0;
        $cart['tip_amount'] = 0;

        // Add item
        if (isset($req['variant_info']) && !empty($req['variant_info']['variant_id'])) {
            $id = $id . 'PV' . $req['variant_info']['variant_id'];
        }

        $cart['item'][$vendor_id][$id] = [
            "name" => $req['name'],
            "quantity" => $req['quantity'],
            "stock_quantity" => $req['stock_quantity'],
            "item_price" => $req['item_price'],
            "price" => $req['price'],
            "dis_price" => $req['dis_price'],
            "extra_price" => $req['extra_price'],
            "extra" => @$req['extra'],
            "image" => @$req['image'],
            "veg" => @$req['veg'],
            "iteam_extra_price" => $req['iteam_extra_price'] ?? null,
            "variant_info" => $req['variant_info'] ?? null,
            "category_id" => $req['category_id'] ?? null,
            "taxSetting" => $req['taxSetting'] ?? []
        ];

        // Store info
        $cart['vendor'] = [
            'id' => $vendor_id,
            'name' => $req['vendor_name'] ?? '',
            'location' => $req['vendor_location'] ?? '',
            'image' => $req['vendor_image'] ?? ''
        ];

        // Item subtotal before discount
        $itemSubtotal = 0;
        foreach ($cart['item'][$vendor_id] as $item) {
            $itemSubtotal += ($item['item_price'] + ($item['extra_price'] ?? 0)) * $item['quantity'];
        }
        
        // Calculate discount (coupon + special offer)
        $discount_amount = 0;
        if (!empty($cart['coupon'])) {
            if ($cart['coupon']['discountType'] === 'Fix Price') {
                $discount_amount = min($cart['coupon']['discount'], $itemSubtotal);
            } else {
                $discount_amount = min(($itemSubtotal * $cart['coupon']['discount']) / 100, $itemSubtotal);
            }
        }

        $specialOfferDiscount = 0;
        $specialOfferType = '';
        $specialOfferDiscountVal = 0;
        if (!empty($req['specialOfferForHour'])) {
            foreach ($req['specialOfferForHour'] as $offer) {
                $specialOfferType = $offer['type'];
                $specialOfferDiscountVal = $offer['discount'];
                $specialOfferDiscount = $offer['type'] === 'percentage'
                    ? ($itemSubtotal * $offer['discount']) / 100
                    : $offer['discount'];
            }
        }

        $totalDiscount = $discount_amount + $specialOfferDiscount;

        // Store discount info in cart
        $cart['specialOfferDiscount'] = $specialOfferDiscount;
        $cart['specialOfferDiscountVal'] = $specialOfferDiscountVal;
        $cart['specialOfferType'] = $specialOfferType;

        $cart['decimal_degits'] = $req['decimal_degits'] ?? 2;
        $cart['currencyData'] = $req['currencyData'] ?? [];
        $cart['taxScope'] = $req['taxScope'] ?? 'product';
        $cart['taxesByScope'] = $req['taxesByScope'] ?? [];
        $cart['taxSetting'] = $cart['taxScope'] == "order" ? ($cart['taxesByScope']['order'] ?? []) : [];
        $cart['packagingCharge'] = $req['packagingCharge'] ?? 0;
        $cart['packagingChargeEnable'] = $req['packagingChargeEnable'];
        $cart['platformCharge'] = $req['platformCharge'] ?? 0;

        $cart['taxBreakdownGrouped'] = [
            'item' => [],
            'order' => [],
            'delivery' => [],
            'packaging' => [],
            'platform' => []
        ];

        $totalTax = 0;

        // Prepare admin-enabled product taxes
        $globalProductTaxes = [];
        foreach ($cart['taxesByScope']['product'] ?? [] as $tax) {
            if ($tax['enable'] ?? false) {
                $globalProductTaxes[$tax['id']] = $tax;
            }
        }

        // PRODUCT-LEVEL TAX
        if ($cart['taxScope'] === 'product') {
            foreach ($cart['item'] as $vendorItemsKey => $vendorItems) {
                foreach ($vendorItems as $itemKey => $item) {
                    $itemGross = ($item['item_price'] + ($item['extra_price'] ?? 0)) * $item['quantity'];
                    $itemDiscount = ($itemSubtotal > 0) ? ($itemGross / $itemSubtotal) * $totalDiscount : 0;
                    $itemTaxable = max(0, $itemGross - $itemDiscount);

                    $itemTaxes = [];
                    foreach ($item['taxSetting'] ?? [] as $itemTax) {
                        if (($itemTax['scope'] ?? 'product') === 'product' && isset($globalProductTaxes[$itemTax['id']])) {
                            $adminTax = $globalProductTaxes[$itemTax['id']];
                            
                            if ($adminTax['type'] === 'percentage') {
                                $taxAmount = $this->applyTax($itemTaxable, $adminTax);
                            } else {
                                $taxAmount = $adminTax['tax'] * $item['quantity'];
                            }
                            $totalTax += $taxAmount;

                            $cart['taxBreakdownGrouped']['item'][$adminTax['title']] =
                                ($cart['taxBreakdownGrouped']['item'][$adminTax['title']] ?? 0) + $taxAmount;

                            $itemTaxes[] = ($adminTax['type'] ?? 'percentage') === 'percentage'
                                ? "{$adminTax['title']} ({$adminTax['tax']}%)"
                                : "{$adminTax['title']} (" . $this->formatCurrency($taxAmount, $cart['currencyData']) . ")";
                        }
                    }
                    
                    if (empty($itemTaxes)) {
                        $cart['taxBreakdownGrouped']['item']['none'] = ($cart['taxBreakdownGrouped']['item']['none'] ?? 0) + 0;
                    }
                    $cart['item'][$vendorItemsKey][$itemKey]['taxLabel'] = implode(', ', array_unique($itemTaxes));
                }
            }
        }

        // ORDER-LEVEL TAX
        if ($cart['taxScope'] === 'order') {
            $orderTaxable = max(0, $itemSubtotal - $totalDiscount);
            foreach ($cart['taxesByScope']['order'] ?? [] as $tax) {
                if ($tax['enable'] ?? true) {
                    $taxAmount = $this->applyTax($orderTaxable, $tax);
                    $totalTax += $taxAmount;
                    $cart['taxBreakdownGrouped']['order'][$tax['title']] =
                        ($cart['taxBreakdownGrouped']['order'][$tax['title']] ?? 0) + $taxAmount;
                }
            }
        }
        
        // DELIVERY, PACKAGING, PLATFORM TAXES
        $extraScopes = ['delivery', 'packaging', 'platform'];
        foreach ($extraScopes as $scope) {
            $charge = $scope == "delivery" ? ($cart['deliverycharge'] ?? 0) : ($cart[$scope . 'Charge'] ?? 0);
            foreach ($cart['taxesByScope'][$scope] ?? [] as $tax) {
                if (!isset($cart['taxBreakdownGrouped'][$scope][$tax['title']])) {
                    $cart['taxBreakdownGrouped'][$scope][$tax['title']] = 0;
                }
                $taxAmount = ($charge > 0) ? $this->applyTax($charge, $tax) : 0;
                $totalTax += $taxAmount;
                $cart['taxBreakdownGrouped'][$scope][$tax['title']] += $taxAmount;
            }
        }

        $cart['tax_amount'] = $totalTax;

        // Save cart to session
        Session::put('cart', $cart);
        Session::save();

        $res = [
            'status' => true,
            'html' => view('vendor.cart_item', ['cart' => $cart])->render()
        ];

        echo json_encode($res);
        exit;
    }

    public function distance($lat1, $lon1, $lat2, $lon2, $unit)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);
        
        if ($unit == "KM") {
          
            return ($miles * 1.609344);
        }  else {
            return $miles;
        }
    }

    public function reorderaddToCart(Request $request)
    {
        $req = $request->all();
        $vendor_id = $req['vendor_id'];
        
        $cart = Session::get('cart', []);
        $cart['item'] = array();
        
        Session::put('cart', $cart);
        Session::save();

        // Add delivery charges info
        $cart['deliverychargemain'] = $req['deliveryCharge'] ?? 0;
        $cart['vendor_latitude'] = $req['vendor_latitude'];
        $cart['vendor_longitude'] = $req['vendor_longitude'];
        $cart['distanceType'] = $req['distanceType'];
        $cart['isSelfDelivery'] = $req['isSelfDelivery'];

        // Calculate distance-based delivery if cookies present
        $deliveryChargemain = @$_COOKIE['deliveryChargemain'];
        $address_lat = @$_COOKIE['address_lat'];
        $address_lng = @$_COOKIE['address_lng'];
        $vendor_latitude = @$_COOKIE['vendor_latitude'];
        $vendor_longitude = @$_COOKIE['vendor_longitude'];
        $selfDelivery = filter_var($cart['isSelfDelivery'], FILTER_VALIDATE_BOOLEAN);

         if ($deliveryChargemain && $address_lat && $address_lng && $vendor_latitude && $vendor_longitude) {
            $deliveryChargemain = json_decode($deliveryChargemain);
            if (!empty($deliveryChargemain)) {
                $distanceType = $req['distanceType'] ?? 'km';
                $kmradius = $this->distance(
                    $address_lat, $address_lng, $vendor_latitude, $vendor_longitude, $distanceType
                );
                $cart['deliverychargemain'] = ($kmradius <= $deliveryChargemain->minimum_delivery_charges_within_km)
                    ? $deliveryChargemain->minimum_delivery_charges
                    : round($kmradius * $deliveryChargemain->delivery_charges_per_km, 2);
                $cart['deliverykm'] = $kmradius;
            }
        }

        // Delivery or takeaway
        $cart['delivery_option'] = Session::get('takeawayOption') === "true" ? "takeaway" : "delivery";
        $cart['deliveryCharge'] = ($cart['delivery_option'] === 'delivery' && !$selfDelivery) ? $cart['deliverychargemain'] : 0;
        $cart['tip_amount'] = 0;
        
        foreach ($req['item'] as $key => $value) {
            $id = 0;
            $name = '';
            $quantity = 0;
            $item_price = 0;
            $price = 0;
            $extra_price = 0;
            $extra = '';
            $image = '';
            if ($value['id']) {
                $id = $value['id'];
            }
            if ($value['name']) {
                $name = $value['name'];
            }
            if ($value['quantity']) {
                $quantity = $value['quantity'];
            }
            if ($value['item_price']) {
                $item_price = $value['item_price'];
            }
            if ($value['price']) {
                $price = $value['price'];
            }
            if ($value['extra_price']) {
                $extra_price = $value['extra_price'];
            }
            if ($value['extra']) {
                $extra = explode(',', $value['extra']);
            }
            if ($value['image']) {
                $image = $value['image'];
            }
            /*by thm*/
            if (isset($req['variant_info']) && !empty($req['variant_info']['variant_id'])) {
                $id = $id . 'PV' . $req['variant_info']['variant_id'];
            }
            $cart['item'][$vendor_id][$id] = [
                "name" => @$name,
                "quantity" => @$quantity,
                "stock_quantity" => @$req['stock_quantity'],
                "item_price" => @$item_price,
                "price" => ($quantity * $price),
                "dis_price" => @$req['dis_price'],
                "extra_price" => ($quantity * $extra_price),
                "extra" => @$extra,
                "image" => @$image,
                "variant_info" => @$req['variant_info'],
                "category_id" => @$value['category_id'],
                "taxSetting" => @$value['taxSetting'] ?? []
            ];
        }

        $cart['vendor'] = [
            'id' => $vendor_id,
            'name' => $req['vendor_name'] ?? '',
            'location' => $req['vendor_location'] ?? '',
            'image' => $req['vendor_image'] ?? ''
        ];

        // Item subtotal before discount
        $itemSubtotal = 0;
        foreach ($cart['item'][$vendor_id] as $item) {
            $itemSubtotal += ($item['item_price'] + ($item['extra_price'] ?? 0)) * $item['quantity'];
        }

        // Calculate discount (coupon + special offer)
        $discount_amount = 0;
       
        $specialOfferDiscount = 0;
        $specialOfferType = '';
        $specialOfferDiscountVal = 0;
        if (!empty($req['specialOfferForHour'])) {
            foreach ($req['specialOfferForHour'] as $offer) {
                $specialOfferType = $offer['type'];
                $specialOfferDiscountVal = $offer['discount'];
                $specialOfferDiscount = $offer['type'] === 'percentage'
                    ? ($itemSubtotal * $offer['discount']) / 100
                    : $offer['discount'];
            }
        }

        $totalDiscount = $discount_amount + $specialOfferDiscount;

        // Store discount info in cart
        $cart['specialOfferDiscount'] = $specialOfferDiscount;
        $cart['specialOfferDiscountVal'] = $specialOfferDiscountVal;
        $cart['specialOfferType'] = $specialOfferType;

        $cart['decimal_degits'] = $req['decimal_degits'] ?? 2;
        $cart['currencyData'] = $req['currencyData'] ?? [];
        $cart['taxScope'] = $req['taxScope'] ?? 'product';
        $cart['taxesByScope'] = $req['taxesByScope'] ?? [];
        $cart['taxSetting'] = $cart['taxScope'] == "order" ? ($cart['taxesByScope']['order'] ?? []) : [];
        $cart['packagingCharge'] = $req['packagingCharge'] ?? 0;
        $cart['packagingChargeEnable'] = $req['packagingChargeEnable'];
        $cart['platformCharge'] = $req['platformCharge'] ?? 0;

        $cart['taxBreakdownGrouped'] = [
            'item' => [],
            'order' => [],
            'delivery' => [],
            'packaging' => [],
            'platform' => []
        ];

        $totalTax = 0;

        // Prepare admin-enabled product taxes
        $globalProductTaxes = [];
        foreach ($cart['taxesByScope']['product'] ?? [] as $tax) {
            if ($tax['enable'] ?? false) {
                $globalProductTaxes[$tax['id']] = $tax;
            }
        }

        // PRODUCT-LEVEL TAX
        if ($cart['taxScope'] === 'product') {
            foreach ($cart['item'] as $vendorItemsKey => $vendorItems) {
                foreach ($vendorItems as $itemKey => $item) {
                    $itemGross = ($item['item_price'] + ($item['extra_price'] ?? 0)) * $item['quantity'];
                    $itemDiscount = ($itemSubtotal > 0) ? ($itemGross / $itemSubtotal) * $totalDiscount : 0;
                    $itemTaxable = max(0, $itemGross - $itemDiscount);

                    $itemTaxes = [];
                    foreach ($item['taxSetting'] ?? [] as $itemTax) {
                        if (($itemTax['scope'] ?? 'product') === 'product' && isset($globalProductTaxes[$itemTax['id']])) {
                            $adminTax = $globalProductTaxes[$itemTax['id']];
                            
                            if ($adminTax['type'] === 'percentage') {
                                $taxAmount = $this->applyTax($itemTaxable, $adminTax);
                            } else {
                                $taxAmount = $adminTax['tax'] * $item['quantity'];
                            }
                            $totalTax += $taxAmount;

                            $cart['taxBreakdownGrouped']['item'][$adminTax['title']] =
                                ($cart['taxBreakdownGrouped']['item'][$adminTax['title']] ?? 0) + $taxAmount;

                            $itemTaxes[] = ($adminTax['type'] ?? 'percentage') === 'percentage'
                                ? "{$adminTax['title']} ({$adminTax['tax']}%)"
                                : "{$adminTax['title']} (" . $this->formatCurrency($taxAmount, $cart['currencyData']) . ")";
                        }
                    }

                    if (empty($itemTaxes)) {
                        $cart['taxBreakdownGrouped']['item']['none'] = ($cart['taxBreakdownGrouped']['item']['none'] ?? 0) + 0;
                    }

                    $cart['item'][$vendorItemsKey][$itemKey]['taxLabel'] = implode(', ', array_unique($itemTaxes));
                }
            }
        }

        // ORDER-LEVEL TAX
        if ($cart['taxScope'] === 'order') {
            $orderTaxable = max(0, $itemSubtotal - $totalDiscount);
            foreach ($cart['taxesByScope']['order'] ?? [] as $tax) {
                if ($tax['enable'] ?? true) {
                    $taxAmount = $this->applyTax($orderTaxable, $tax);
                    $totalTax += $taxAmount;
                    $cart['taxBreakdownGrouped']['order'][$tax['title']] =
                        ($cart['taxBreakdownGrouped']['order'][$tax['title']] ?? 0) + $taxAmount;
                }
            }
        }

        // DELIVERY, PACKAGING, PLATFORM TAXES
        $extraScopes = ['delivery', 'packaging', 'platform'];
        foreach ($extraScopes as $scope) {
            $charge = $scope == "delivery" ? ($cart['deliverycharge'] ?? 0) : ($cart[$scope . 'Charge'] ?? 0);
            foreach ($cart['taxesByScope'][$scope] ?? [] as $tax) {
                if (!isset($cart['taxBreakdownGrouped'][$scope][$tax['title']])) {
                    $cart['taxBreakdownGrouped'][$scope][$tax['title']] = 0;
                }
                $taxAmount = ($charge > 0) ? $this->applyTax($charge, $tax) : 0;
                $totalTax += $taxAmount;
                $cart['taxBreakdownGrouped'][$scope][$tax['title']] += $taxAmount;
            }
        }

        $cart['tax_amount'] = $totalTax;

        // Save cart to session
        Session::put('cart', $cart);
        Session::save();
        
        $res = array('status' => true);
        echo json_encode($res);
        exit;
    }

    public function orderTipAdd(Request $request)
    {

        $req = $request->all();
        $cart = Session::get('cart', []);
        $cart['tip_amount'] = $req['tip'];
        Session::put('cart', $cart);
        Session::save();
        if (@$req['is_checkout']) {
            $email = Auth::user()->email;
            $user = VendorUsers::where('email', $email)->first();
            $res = array('status' => true, 'html' => view('vendor.cart_item', ['is_checkout' => 1, 'id' => $user->uuid, 'cart' => $cart])->render());
        } else {
            $res = array('status' => true, 'html' => view('vendor.cart_item', ['cart' => $cart])->render());
        }
        echo json_encode($res);
        exit;
    }

    public function orderDeliveryOption(Request $request)
    {
        $req = $request->all();
        $cart = Session::get('cart', []);
        $cart['delivery_option'] = $req['delivery_option'];
        if ($req['delivery_option'] == "takeaway") {
            //deliveryCharge
            $cart['tip_amount'] = 0;
            $cart['deliverycharge'] = 0;
        } else {
            //delivery
            if (isset($cart['deliverychargemain'])) {
                $cart['deliverycharge'] = $cart['deliverychargemain'];
            } else if (isset($req['deliveryCharge'])) {
                $cart['deliverychargemain'] = $req['deliveryCharge'];
                $cart['deliverycharge'] = $cart['deliverychargemain'];
            }
        }
        Session::put('cart', $cart);
        Session::save();
        if (@$req['is_checkout']) {
            $email = Auth::user()->email;
            $user = VendorUsers::where('email', $email)->first();
            $res = array('status' => true, 'html' => view('vendor.cart_item', ['is_checkout' => 1, 'id' => $user->uuid, 'cart' => $cart])->render());
        } else {
            $res = array('status' => true, 'html' => view('vendor.cart_item', ['cart' => $cart])->render());
        }

        echo json_encode($res);
        exit;
    }

    public function changeQuantityCart(Request $request)
    {
        $req = $request->all();
        $id = $req['id'];
        $vendor_id = $req['vendor_id'];
        $quantity = $req['quantity'];
        $cart = Session::get('cart');

        if (isset($cart['item'][$vendor_id][$id])) {
            if ($quantity == 0) {
                if (isset($cart['item'][$vendor_id][$id])) {
                    unset($cart['item'][$vendor_id][$id]);
                    Session::put('cart', $cart);
                    Session::save();
                }
            } else {
                $cart['item'][$vendor_id][$id]['quantity'] = $quantity;
                Session::put('cart', $cart);
                Session::save();
            }
        }

        $cart = $this->calculateTax($cart);
        Session::put('cart', $cart);
        Session::save();
        
        $cart = Session::get('cart');
        $res = array('status' => true, 'html' => view('vendor.cart_item', ['cart' => $cart, 'is_checkout' => 1])->render());
        echo json_encode($res);
        exit;
    }

    public function update(Request $request)
    {
        if ($request->id && $request->quantity) {
            $cart = Session::get('cart');
            $cart['item'][$request->id]["quantity"] = $request->quantity;
            Session::put('cart', $cart);
            Session::save();
            $res = array('status' => true, 'html' => view('vendor.cart_item', ['cart' => $cart])->render());
            echo json_encode($res);
            exit;
        }
    }

    public function applyCoupon(Request $request)
    {
        if ($request->coupon_code) {
            $cart = Session::get('cart');
            $cart['coupon']['coupon_code'] = $request->coupon_code;
            $cart['coupon']['coupon_id'] = $request->coupon_id;
            $cart['coupon']['discount'] = $request->discount;
            $cart['coupon']['discountType'] = $request->discountType;
            
            $cart = $this->calculateTax($cart);
            Session::put('cart', $cart);
            Session::save();

            $cart = Session::get('cart');
            $res = array('status' => true, 'html' => view('vendor.cart_item', ['cart' => $cart])->render());
            echo json_encode($res);
            exit;
        }
    }

    public function removeCoupon(Request $request)
    {
        $cart = Session::get('cart');
        $cart['coupon'] = [];
        
        $cart = $this->calculateTax($cart);
        Session::put('cart', $cart);
        Session::save();
        
        exit;
    }

    public function orderComplete(Request $request)
    {

        $cart = array();
        Session::put('cart', $cart);
        Session::put('payfast_payment_token', '');
        Session::put('success', trans('lang.your_order_has_been_successful'));

        if(Storage::disk('local')->has('firebase/credentials.json')){
            
            $client= new Google_Client();
            $client->setAuthConfig(storage_path('app/firebase/credentials.json'));
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $client->refreshTokenWithAssertion();
            $client_token = $client->getAccessToken();
            $access_token = $client_token['access_token'];

            $fcm_token = $request->fcm;
            
            if(!empty($access_token) && !empty($fcm_token)){

                $projectId = env('FIREBASE_PROJECT_ID');
                $url = 'https://fcm.googleapis.com/v1/projects/'.$projectId.'/messages:send';

                $data = [
                    'message' => [
                        'notification' => [
                            'title' => $request->subject,
                            'body' => $request->message,
                        ],
                        'data' => [
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                            'id' => '1',
                            'status' => 'done',
                        ],
                        'token' => $fcm_token,
                    ],
                ];

                $headers = array(
                    'Content-Type: application/json',
                    'Authorization: Bearer '.$access_token
                );

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                
                $result = curl_exec($ch);
                if ($result === FALSE) {
                    die('FCM Send Error: ' . curl_error($ch));
                }
                curl_close($ch);
                $result=json_decode($result);

                $response = array();
                $response['success'] = true;
                $response['message'] = 'Notification successfully sent.';
                $response['result'] = $result;

            }else{
                $response = array();
                $response['success'] = false;
                $response['message'] = 'Missing sender id or token to send notification.';
            }

        }else{
            $response = array();
            $response['success'] = false;
            $response['message'] = 'Firebase credentials file not found.';
        }

        Session::save();

        $order_response = array('status' => true, 'order_complete' => true, 'html' => view('vendor.cart_item', ['cart' => $cart, 'order_complete' => true, 'is_checkout' => 1])->render(), 'response' => $response);
       
        return response()->json($order_response);
    }

    public function remove(Request $request)
    {
        if ($request->id && $request->vendor_id) {
            $cart = Session::get('cart');
            if (isset($cart['item'][$request->vendor_id][$request->id])) {
                unset($cart['item'][$request->vendor_id][$request->id]);
            }
            Session::put('cart', $cart);
            Session::save();
        }
        $cart = Session::get('cart');
        session()->flash('success', trans('lang.product_removed_successfully'));
        $res = array('status' => true, 'html' => view('vendor.cart_item', ['cart' => $cart])->render());
        echo json_encode($res);
        exit;
    }
    
    public function orderScheduleTimeAdd(Request $request)
    {
        $req = $request->all();
        $cart = Session::get('cart', []);
        $cart['scheduleTime'] = $req['scheduleTime'];
        Session::put('cart', $cart);
        Session::save();
        if (@$req['is_checkout']) {
            $email = Auth::user()->email;
            $user = VendorUsers::where('email', $email)->first();
            $res = array('status' => true, 'html' => view('vendor.cart_item', ['is_checkout' => 1, 'id' => $user->uuid, 'cart' => $cart])->render());
        } else {
            $res = array('status' => true, 'html' => view('vendor.cart_item', ['cart' => $cart])->render());
        }
        echo json_encode($res);
        exit;
    }

    public function applyTax($amount, $tax) {
        if (!$tax['enable']) return 0;
        if ($tax['type'] === 'percentage') {
            return ($amount * $tax['tax']) / 100;
        }
        if ($tax['type'] === 'fix') {
            return $tax['tax'];
        }
        return 0;
    }

    public function formatCurrency($amount, $currency = []) {
        $symbol = $currency['symbol'] ?? '';
        $decimals = $currency['decimal_degits'] ?? 2;
        $symbolAtRight = filter_var($currency['symbolAtRight'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $formatted = number_format($amount, $decimals, ',', '.');

        return $symbolAtRight
            ? $formatted . ' ' . $symbol
            : $symbol . $formatted;
    }

    public function calculateTax($cart){

        $cart['taxBreakdownGrouped'] = [
            'item' => [],
            'order' => [],
            'delivery' => [],
            'packaging' => [],
            'platform' => []
        ];

        if(!isset($cart['vendor'])) return;

        $vendor_id = $cart['vendor']['id'];
        
        // Item subtotal before discount
        $itemSubtotal = 0;
        foreach ($cart['item'][$vendor_id] as $item) {
            $itemSubtotal += ($item['item_price'] + ($item['extra_price'] ?? 0)) * $item['quantity'];
        }
        
        // Calculate discount (coupon + special offer)
        $discount_amount = 0;
        if (!empty($cart['coupon'])) {
            if ($cart['coupon']['discountType'] === 'Fix Price') {
                $discount_amount = min($cart['coupon']['discount'], $itemSubtotal);
            } else {
                $discount_amount = min(($itemSubtotal * $cart['coupon']['discount']) / 100, $itemSubtotal);
            }
        }

        // Recalculate special offer based on current subtotal! (FIX)
        $specialOfferDiscount = 0;
        if (!empty($cart['specialOfferType'])) {
            if ($cart['specialOfferType'] === 'percentage') {
                $specialOfferDiscount = ($itemSubtotal * $cart['specialOfferDiscountVal']) / 100;
            } else {
                $specialOfferDiscount = min($cart['specialOfferDiscountVal'], $itemSubtotal);
            }
        }

        $totalDiscount = $discount_amount + $specialOfferDiscount;

        $totalTax = 0;

        // Prepare admin-enabled product taxes
        $globalProductTaxes = [];
        foreach ($cart['taxesByScope']['product'] ?? [] as $tax) {
            if ($tax['enable'] ?? false) {
                $globalProductTaxes[$tax['id']] = $tax;
            }
        }

        // PRODUCT-LEVEL TAX
        if ($cart['taxScope'] === 'product') {
            foreach ($cart['item'] as $vendorItemsKey => $vendorItems) {
                foreach ($vendorItems as $itemKey => $item) {
                    $itemGross = ($item['item_price'] + ($item['extra_price'] ?? 0)) * $item['quantity'];
                    $itemDiscount = ($itemSubtotal > 0) ? ($itemGross / $itemSubtotal) * $totalDiscount : 0;
                    $itemTaxable = max(0, $itemGross - $itemDiscount);
                    $itemTaxes = [];
                    foreach ($item['taxSetting'] ?? [] as $itemTax) {
                        if (($itemTax['scope'] ?? 'product') === 'product' && isset($globalProductTaxes[$itemTax['id']])) {
                            $adminTax = $globalProductTaxes[$itemTax['id']];
                            if ($adminTax['type'] === 'percentage') {
                                $taxAmount = $this->applyTax($itemTaxable, $adminTax);
                            } else {
                                $taxAmount = $adminTax['tax'] * $item['quantity'];
                            }
                            $totalTax += $taxAmount;
                            $cart['taxBreakdownGrouped']['item'][$adminTax['title']] =
                                ($cart['taxBreakdownGrouped']['item'][$adminTax['title']] ?? 0) + $taxAmount;
                            $itemTaxes[] = ($adminTax['type'] ?? 'percentage') === 'percentage'
                                ? "{$adminTax['title']} ({$adminTax['tax']}%)"
                                : "{$adminTax['title']} (" . $this->formatCurrency($taxAmount, $cart['currencyData']) . ")";
                        }
                    }
                    if (empty($itemTaxes)) {
                        $cart['taxBreakdownGrouped']['item']['none'] = ($cart['taxBreakdownGrouped']['item']['none'] ?? 0) + 0;
                    }
                    $cart['item'][$vendorItemsKey][$itemKey]['taxLabel'] = implode(', ', array_unique($itemTaxes));
                }
            }
        }

        // ORDER-LEVEL TAX
        if ($cart['taxScope'] === 'order') {
            $orderTaxable = max(0, $itemSubtotal - $totalDiscount);
            foreach ($cart['taxesByScope']['order'] ?? [] as $tax) {
                if ($tax['enable'] ?? true) {
                    $taxAmount = $this->applyTax($orderTaxable, $tax);
                    $totalTax += $taxAmount;
                    $cart['taxBreakdownGrouped']['order'][$tax['title']] =
                        ($cart['taxBreakdownGrouped']['order'][$tax['title']] ?? 0) + $taxAmount;
                }
            }
        }

        // DELIVERY, PACKAGING, PLATFORM TAXES
        $extraScopes = ['delivery', 'packaging', 'platform'];
        foreach ($extraScopes as $scope) {
            $charge = $scope == "delivery" ? ($cart['deliverycharge'] ?? 0) : ($cart[$scope . 'Charge'] ?? 0);
            foreach ($cart['taxesByScope'][$scope] ?? [] as $tax) {
                if (!isset($cart['taxBreakdownGrouped'][$scope][$tax['title']])) {
                    $cart['taxBreakdownGrouped'][$scope][$tax['title']] = 0;
                }
                $taxAmount = ($charge > 0) ? $this->applyTax($charge, $tax) : 0;
                $totalTax += $taxAmount;
                $cart['taxBreakdownGrouped'][$scope][$tax['title']] += $taxAmount;
            }
        }

        $cart['tax_amount'] = $totalTax;

        return $cart;
    }
}