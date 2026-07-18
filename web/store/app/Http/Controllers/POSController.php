<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\VendorUsers;

class POSController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function pointOfSale(){
        $user = Auth::user();
        $id = Auth::id();
        $exist = VendorUsers::where('user_id',$id)->first();
        $id=$exist->uuid;
        $commissionSettings = session('commissionSettings', [
            'enabled' => false,
            'type' => 'Percent',
            'value' => 0,
        ]);       
        return view("pos.index", compact('commissionSettings','id'));
    }

    public function posOrder(){
        return view("pos.order_index");
    }
}
