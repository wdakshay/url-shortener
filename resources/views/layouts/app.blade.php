<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') | Modern URL Shortener</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --bg-dark: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.7);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --success: #22c55e;
            --error: #ef4444;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }

        body {
            background-color: var(--bg-dark);
            min-height: 100vh;
            color: var(--text-main);
            background-image: radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.05) 0px, transparent 50%);
        }

        .navbar {
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(8px);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.3s;
        }

        .nav-links a:hover, .nav-links a.active {
            color: var(--text-main);
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(to right, #818cf8, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .role-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.75rem;
            border-radius: 99px;
            background: rgba(99, 102, 241, 0.1);
            color: var(--primary);
            font-weight: 700;
            text-transform: uppercase;
        }

        .logout-btn {
            background: none;
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #ef4444;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.875rem;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 1.5rem;
            backdrop-filter: blur(12px);
        }

        .card h2 { font-size: 1.25rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }

        .alert { padding: 1rem; border-radius: 10px; margin-bottom: 1rem; font-size: 0.875rem; }
        .alert-success { background: rgba(34, 197, 94, 0.1); color: var(--success); border: 1px solid rgba(34, 197, 94, 0.2); }
        .alert-error { background: rgba(239, 68, 68, 0.1); color: var(--error); border: 1px solid rgba(239, 68, 68, 0.2); }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; padding: 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
        td { padding: 1rem; font-size: 0.875rem; border-bottom: 1px solid rgba(255, 255, 255, 0.05); }

        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.5rem; }
        input, select { width: 100%; background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(255, 255, 255, 0.1); padding: 0.75rem; border-radius: 10px; color: white; outline: none; }
        input:focus { border-color: var(--primary); }

        /* Dashboard Stats */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { padding: 2rem; }
        .stat-card .label { color: var(--text-muted); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem; }
        .stat-card .value { font-size: 2.5rem; font-weight: 700; color: var(--text-main); }
    </style>
    <script>
        function copyToClipboard(text, btn) {
            navigator.clipboard.writeText(text).then(() => {
                const originalText = btn.innerHTML;
                btn.innerHTML = 'Copied!';
                btn.style.background = 'var(--success)';
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.style.background = '';
                }, 2000);
            });
        }
    </script>
    @yield('head')
</head>
<body>
    <nav class="navbar">
        <div style="display: flex; align-items: center; gap: 3rem;">
            <a href="{{ route('dashboard') }}" class="logo">ShortURL</a>
            <div class="nav-links">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
                <a href="{{ route('urls.index') }}" class="{{ request()->routeIs('urls.index') ? 'active' : '' }}">URLs</a>
                @if(Auth::user()->isSuperAdmin() || Auth::user()->isAdmin())
                <a href="{{ route('team.index') }}" class="{{ request()->routeIs('team.index') ? 'active' : '' }}">Team</a>
                @endif
            </div>
        </div>
        <div class="user-info">
            <div style="text-align: right">
                <div style="font-weight: 600">{{ Auth::user()->name }}</div>
                <div class="role-badge">{{ Auth::user()->role }}</div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </nav>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        {{ session('success') }}
                        @if(session('invitation_link'))
                            <br><strong>Link:</strong> <code style="word-break: break-all">{{ session('invitation_link') }}</code>
                        @endif
                    </div>
                    @if(session('invitation_link'))
                        <button onclick="copyToClipboard('{{ session('invitation_link') }}', this)" class="btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.75rem;">
                            Copy Link
                        </button>
                    @endif
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                {{ $errors->first() }}
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>
