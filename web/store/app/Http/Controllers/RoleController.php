<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class RoleController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        
        return view("role.index");
    }

     public function create()
    {
        return view("role.create");
    }
    public function edit($id){
       
        return view('role.edit', compact('id'));

    }   

}
