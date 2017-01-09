<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FLGSController extends Controller
{
    public function store(Request $request) {
		global $currentUser;

		if (!$currentUser) {
			return redirect('401');
		}
	}
}
