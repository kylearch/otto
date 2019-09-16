<?php

namespace App\Http\ViewComposers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LayoutViewComposer
{
    /**
     * Bind data to the view.
     *
     * @param View $view
     *
     * @return void
     */
    public function compose(View $view)
    {
        // $user = Auth::user();
        $user = User::first();
        $view->with('user', $user);
    }
}
