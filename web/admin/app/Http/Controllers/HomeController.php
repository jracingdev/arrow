<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
    	if(@$_REQUEST['type'] == "cab-service"){
			return view('dashboard.cab');	
		}else if(@$_REQUEST['type'] == "delivery-service"){
			return view('dashboard.delivery');	
		}else if(@$_REQUEST['type'] == "ecommerce-service"){
			return view('dashboard.ecommerce');	
		}else if(@$_REQUEST['type'] == "parcel_delivery"){
			return view('dashboard.parcel');	
		}else if(@$_REQUEST['type'] == "rental-service"){
			return view('dashboard.rental');	
		}else if(@$_REQUEST['type'] == "ondemand-service"){
			return view('dashboard.ondemand');	
		}else{
			return view('dashboard.all');
		}
    }

	public function storeFirebaseService(Request $request){
		if(!empty($request->serviceJson) && !Storage::disk('local')->has('firebase/credentials.json')){
			Storage::disk('local')->put('firebase/credentials.json',file_get_contents(base64_decode($request->serviceJson)));
		}
	}
}
