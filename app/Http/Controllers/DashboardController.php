<?php

namespace App\Http\Controllers;

use App\Models\Finance\Account;
use App\Models\Finance\Transaction;

class DashboardController extends Controller
{
    public function index()
    {
        $accounts     = Account::open()->get();
        $transactions = Transaction::with('account')->orderBy('date', 'desc')->paginate(50);

        return view('pages.dashboard.index', compact('accounts', 'transactions'));
    }
}
