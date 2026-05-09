<?php

namespace App\Http\Controllers;

use App\Models\Url;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class UrlController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isSuperAdmin()) {
            $stats = [
                'total_urls' => Url::count(),
                'total_clicks' => Url::sum('clicks'),
                'total_companies' => \App\Models\Company::count(),
                'total_users' => \App\Models\User::count(),
            ];
        } elseif ($user->isAdmin()) {
            $stats = [
                'total_urls' => Url::where('company_id', $user->company_id)->count(),
                'total_clicks' => Url::where('company_id', $user->company_id)->sum('clicks'),
                'total_members' => \App\Models\User::where('company_id', $user->company_id)->count(),
            ];
        } else {
            $stats = [
                'total_urls' => Url::where('user_id', $user->id)->count(),
                'total_clicks' => Url::where('user_id', $user->id)->sum('clicks'),
            ];
        }

        return view('dashboard', compact('stats'));
    }

    public function list()
    {
        $user = Auth::user();
        
        if ($user->isSuperAdmin()) {
            $urls = Url::with(['user', 'company'])->latest()->get();
        } elseif ($user->isAdmin()) {
            $urls = Url::where('company_id', $user->company_id)->with('user')->latest()->get();
        } else {
            $urls = Url::where('user_id', $user->id)->latest()->get();
        }

        return view('urls.index', compact('urls'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return back()->withErrors(['error' => 'SuperAdmin cannot create short URLs.']);
        }

        $request->validate([
            'original_url' => 'required|url',
        ]);

        $shortCode = Str::random(6);
        while (Url::where('short_code', $shortCode)->exists()) {
            $shortCode = Str::random(6);
        }

        Url::create([
            'original_url' => $request->original_url,
            'short_code' => $shortCode,
            'user_id' => $user->id,
            'company_id' => $user->company_id,
        ]);

        return back()->with('success', 'Short URL created successfully.');
    }

    public function redirect($shortCode)
    {
        $url = Url::where('short_code', $shortCode)->firstOrFail();
        $url->increment('clicks');

        return redirect($url->original_url);
    }
}
