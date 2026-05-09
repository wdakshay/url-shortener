<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            $companies = Company::all();
            $invitations = Invitation::where('status', 'pending')->with('company')->latest()->get();
            // Fetch all users across all companies for SuperAdmin to see
            $members = User::where('role', '!=', 'superadmin')->with('company')->latest()->get();
            return view('team.index', compact('companies', 'invitations', 'members'));
        }

        $members = User::where('company_id', $user->company_id)->get();
        $invitations = Invitation::where('company_id', $user->company_id)
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('team.index', compact('members', 'invitations'));
    }
    public function invite(Request $request)
    {
        $user = Auth::user();

        if ($user->isMember()) {
            abort(403, 'Members cannot invite team members.');
        }

        $request->validate([
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,member',
        ]);

        $companyId = $user->company_id;

        // If SuperAdmin, they must select a company and role must be admin
        if ($user->isSuperAdmin()) {
            $request->validate([
                'company_id' => 'required|exists:companies,id',
                'role' => 'required|in:admin', // SuperAdmin can only invite Admins
            ]);
            $companyId = $request->company_id;
        }

        $token = Str::random(32);

        Invitation::create([
            'email' => $request->email,
            'company_id' => $companyId,
            'role' => $request->role,
            'token' => $token,
        ]);

        // In a real app, send an email here. 
        // For this demo, we'll just return the link.
        $invitationLink = url("/invitation/accept/{$token}");

        return back()->with('success', "Invitation sent! Link: {$invitationLink}")->with('invitation_link', $invitationLink);
    }

    public function accept($token)
    {
        $invitation = Invitation::where('token', $token)->where('status', 'pending')->firstOrFail();

        return view('auth.register_invited', compact('invitation'));
    }

    public function processAccept(Request $request, $token)
    {
        $invitation = Invitation::where('token', $token)->where('status', 'pending')->firstOrFail();

        $request->validate([
            'name' => 'required|string',
            'password' => 'required|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $invitation->email,
            'password' => bcrypt($request->password),
            'company_id' => $invitation->company_id,
            'role' => $invitation->role,
        ]);

        $invitation->update(['status' => 'accepted']);

        return redirect('/login')->with('success', 'Account created! Please login.');
    }
}
