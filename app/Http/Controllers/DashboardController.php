<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Url;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            $stats = [
                'total_urls' => Url::count(),
                'total_clicks' => Url::sum('clicks'),
                'total_companies' => Company::count(),
                'total_users' => User::count(),
            ];
        } elseif ($user->isAdmin()) {
            $stats = [
                'total_urls' => Url::where('company_id', $user->company_id)->count(),
                'total_clicks' => Url::where('company_id', $user->company_id)->sum('clicks'),
                'total_members' => User::where('company_id', $user->company_id)->count(),
            ];
        } else {
            $stats = [
                'total_urls' => Url::where('user_id', $user->id)->count(),
                'total_clicks' => Url::where('user_id', $user->id)->sum('clicks'),
            ];
        }

        return view('dashboard', compact('stats'));
    }
}
