<?php

namespace App\Http\Controllers;

use App\Jobs\CreatePlaidItemJob;
use Illuminate\Http\Request;

class PlaidController extends Controller
{
    public function link(Request $request)
    {
        if (!empty($request->input('public_token'))) {
            CreatePlaidItemJob::dispatch($request->all());

            return redirect()->route('dashboard');
        }

        return view('plaid.link');
    }
}
