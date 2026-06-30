<?php

namespace App\Http\Controllers;

class RentalController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function rentalOrders($id = '')
    {
        return view("rental_orders.index")->with('id', $id);
    }

    public function rentalOrderEdit($id)
    {
        return view('rental_orders.edit')->with('id', $id);
    }

}


