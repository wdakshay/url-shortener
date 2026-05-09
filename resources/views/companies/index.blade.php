@extends('layouts.app')

@section('title', 'Manage Companies')

@section('content')
    <div class="card" style="margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2>Companies</h2>
            <a href="{{ route('companies.create') }}" class="btn btn-primary">Add New Company</a>
        </div>
    </div>

    <div class="card">
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Company Name</th>
                        <th>Members</th>
                        <th>Actions</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($companies as $company)
                    <tr>
                        <td>
                            <div style="font-weight: 700; color: var(--text-main); font-size: 0.95rem;">{{ $company->name }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.1rem;">ID: #{{ $company->id }}</div>
                        </td>
                        <td><span style="font-size: 0.75rem; font-weight: 600;">{{ $company->users_count }} Members</span></td>
                        <td>
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="{{ route('companies.edit', $company) }}" style="color: var(--primary); text-decoration: none; font-size: 0.875rem; font-weight: 600;">Edit</a>
                                <form action="{{ route('companies.destroy', $company) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: none; border: none; color: var(--error); cursor: pointer; font-size: 0.875rem; font-weight: 600;">Delete</button>
                                </form>
                            </div>
                        </td>
                        <td>{{ $company->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 3rem;">
                            No companies found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
