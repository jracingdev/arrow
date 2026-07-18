<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view("employee.index");
    }

    public function edit($id)
    {
        return view('employee.edit')->with('id', $id);
    }

    public function create()
    {
        return view('employee.create');
    }
}
