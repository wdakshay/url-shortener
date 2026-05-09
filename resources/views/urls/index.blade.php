@extends('layouts.app')

@section('title', 'Manage URLs')

@section('content')
    <div class="card" style="margin-bottom: 2rem;">
        <h2>Generate Short URL</h2>
        @if(!Auth::user()->isSuperAdmin())
        <form action="{{ route('urls.store') }}" method="POST" style="display: flex; gap: 1rem; align-items: flex-end;">
            @csrf
            <div class="form-group" style="flex: 1; margin-bottom: 0;">
                <label>Original URL</label>
                <input type="url" name="original_url" placeholder="https://example.com/long-page" required>
            </div>
            <button type="submit" class="btn btn-primary">Shorten</button>
        </form>
        @else
        <p style="color: var(--text-muted);">SuperAdmins cannot create short URLs.</p>
        @endif
    </div>

    <div class="card">
        <h2>Generated Short URLs</h2>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Short URL</th>
                        <th>Clicks</th>
                        @if(Auth::user()->isSuperAdmin() || Auth::user()->isAdmin())
                        <th>Created By</th>
                        @endif
                        @if(Auth::user()->isSuperAdmin())
                        <th>Company</th>
                        @endif
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($urls as $url)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <a href="{{ url('/' . $url->short_code) }}" target="_blank" class="short-url" style="color: var(--primary); text-decoration: none; font-weight: 600;">
                                    {{ url('/' . $url->short_code) }}
                                </a>
                                <button onclick="copyToClipboard('{{ url('/' . $url->short_code) }}', this)" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: var(--text-muted); padding: 0.2rem 0.5rem; border-radius: 6px; cursor: pointer; font-size: 0.65rem;">
                                    Copy
                                </button>
                            </div>
                            <span style="display: block; color: var(--text-muted); font-size: 0.75rem; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $url->original_url }}
                            </span>
                        </td>
                        <td>{{ $url->clicks }}</td>
                        @if(Auth::user()->isSuperAdmin() || Auth::user()->isAdmin())
                        <td>{{ $url->user->name }}</td>
                        @endif
                        @if(Auth::user()->isSuperAdmin())
                        <td><span style="font-size: 0.7rem; padding: 0.2rem 0.5rem; border-radius: 4px; background: rgba(255,255,255,0.05);">{{ $url->company->name }}</span></td>
                        @endif
                        <td style="font-size: 0.75rem; color: var(--text-muted)">
                            {{ $url->created_at->format('M d, Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 3rem;">
                            No URLs generated yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
