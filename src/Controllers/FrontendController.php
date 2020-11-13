<?php

namespace App\Controllers;

class FrontendController extends Controller
{
	public function index()
	{
		return view('index');
	}
}