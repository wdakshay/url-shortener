@extends('layouts.app')

@section('title', 'Edit Company')

@section('content')
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <h2 style="margin-bottom: 2rem;">Edit Company</h2>
        
        <form action="{{ route('companies.update', $company) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Company Name</label>
                <input type="text" name="name" value="{{ old('name', $company->name) }}" required autofocus>
                @error('name')
                    <div style="color: var(--error); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Update Company</button>
                <a href="{{ route('companies.index') }}" class="btn" style="flex: 1; border: 1px solid rgba(255,255,255,0.1); color: var(--text-muted);">Cancel</a>
            </div>
        </form>
    </div>
@endsection
