<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\VendorUsers;

class ProviderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function vendorUuid()
    {
        $exist = VendorUsers::where('user_id', Auth::id())->first();
        return $exist ? $exist->uuid : Auth::id();
    }

    public function bookings()
    {
        return view('provider.bookings.index')->with('id', $this->vendorUuid());
    }

    public function bookingsEdit($id)
    {
        return view('provider.bookings.edit')->with('id', $id)->with('providerId', $this->vendorUuid());
    }

    public function services()
    {
        return view('provider.services.index')->with('id', $this->vendorUuid());
    }

    public function servicesCreate()
    {
        return view('provider.services.create')->with('id', $this->vendorUuid());
    }

    public function servicesEdit($id)
    {
        return view('provider.services.edit')->with('id', $id)->with('providerId', $this->vendorUuid());
    }

    public function workers()
    {
        return view('provider.workers.index')->with('id', $this->vendorUuid());
    }

    public function workersCreate()
    {
        return view('provider.workers.create')->with('id', $this->vendorUuid());
    }

    public function workersEdit($id)
    {
        return view('provider.workers.edit')->with('id', $id)->with('providerId', $this->vendorUuid());
    }
}
