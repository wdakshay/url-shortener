@extends('layouts.app')

@section('title', Auth::user()->isSuperAdmin() ? 'Manage Clients' : 'Manage Team')

@section('content')
    @if(!Auth::user()->isMember())
    <div class="card" style="margin-bottom: 2rem;">
        <h2>{{ Auth::user()->isSuperAdmin() ? 'Invite Client Admin' : 'Invite Team Member' }}</h2>
        <form action="{{ route('invite') }}" method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: flex-end;">
            @csrf
            @if(Auth::user()->isSuperAdmin())
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Company</label>
                    <select name="company_id" required>
                        <option value="">Select Company</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>
                <input type="hidden" name="role" value="admin">
            @else
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Role</label>
                    <select name="role">
                        <option value="member">Member</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            @endif
            <div class="form-group" style="margin-bottom: 0;">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="user@example.com" required>
            </div>
            <button type="submit" class="btn btn-primary">Send Invitation</button>
        </form>
    </div>
    @endif

    <div class="card">
        <h2>{{ Auth::user()->isSuperAdmin() ? 'Clients & Pending Invitations' : 'Team Members & Pending Invitations' }}</h2>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Name / Info</th>
                        <th>Role</th>
                        <th>Status / Actions</th>
                        @if(Auth::user()->isSuperAdmin())
                        <th>Company</th>
                        @endif
                        <th>Date Joined</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Active Members -->
                    @if(isset($members))
                        @foreach($members as $member)
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: var(--text-main); font-size: 0.95rem;">{{ $member->name }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.1rem;">{{ $member->email }}</div>
                            </td>
                            <td><span style="text-transform: uppercase; font-size: 0.7rem; font-weight: 700; color: var(--primary);">{{ $member->role }}</span></td>
                            <td><span style="background: rgba(34, 197, 94, 0.1); color: var(--success); font-size: 0.7rem; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 700;">ACTIVE</span></td>
                            @if(Auth::user()->isSuperAdmin())
                            <td>{{ $member->company->name }}</td>
                            @endif
                            <td>{{ $member->created_at->format('M d, Y') }}</td>
                        </tr>
                        @endforeach
                    @endif

                    <!-- Pending Invitations -->
                    @foreach($invitations as $invitation)
                    <tr style="opacity: 0.9;">
                        <td>
                            <div style="font-weight: 600; color: var(--text-main);">{{ $invitation->email }}</div>
                            <div style="font-size: 0.7rem; color: #fbbf24; text-transform: uppercase; font-weight: 700; margin-top: 0.25rem;">Pending Invitation</div>
                        </td>
                        <td><span style="text-transform: uppercase; font-size: 0.75rem;">{{ $invitation->role }}</span></td>
                        <td>
                            <button onclick="copyToClipboard('{{ url('/invitation/accept/' . $invitation->token) }}', this)" style="background: rgba(251, 191, 36, 0.1); border: 1px solid rgba(251, 191, 36, 0.2); color: #fbbf24; padding: 0.3rem 0.6rem; border-radius: 6px; cursor: pointer; font-size: 0.7rem; font-weight: 600;">
                                Copy Invitation Link
                            </button>
                        </td>
                        @if(Auth::user()->isSuperAdmin())
                        <td>{{ $invitation->company->name }}</td>
                        @endif
                        <td>{{ $invitation->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach

                    @if(count($invitations) == 0 && (!isset($members) || count($members) == 0))
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 3rem;">
                            No team data available.
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection
