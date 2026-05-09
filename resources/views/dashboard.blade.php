@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="welcome-section" style="margin-bottom: 2rem;">
        <h2 style="font-size: 2rem;">Hello, {{ Auth::user()->name }}!</h2>
        <p style="color: var(--text-muted);">Here's what's happening with your account today.</p>
    </div>

    <div class="stats-grid">
        <div class="card stat-card">
            <div class="label">Total Short URLs</div>
            <div class="value">{{ $stats['total_urls'] }}</div>
        </div>

        <div class="card stat-card">
            <div class="label">Total Clicks</div>
            <div class="value">{{ $stats['total_clicks'] }}</div>
        </div>

        @if(Auth::user()->isSuperAdmin())
            <div class="card stat-card">
                <div class="label">Total Companies</div>
                <div class="value">{{ $stats['total_companies'] }}</div>
            </div>
            <div class="card stat-card">
                <div class="label">Total Users</div>
                <div class="value">{{ $stats['total_users'] }}</div>
            </div>
        @elseif(Auth::user()->isAdmin())
            <div class="card stat-card">
                <div class="label">Total Members</div>
                <div class="value">{{ $stats['total_members'] }}</div>
            </div>
        @endif
    </div>
@endsection
