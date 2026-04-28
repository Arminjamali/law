<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ردیاب مطالعه وکالت')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --sidebar-w: 240px; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f8fafc; }

        /* ── Sidebar (desktop) ── */
        .sidebar {
            width: var(--sidebar-w); min-height: 100vh; background: #1e1b4b;
            position: fixed; right: 0; top: 0; z-index: 1040;
            display: flex; flex-direction: column; transition: transform .25s ease;
        }
        .sidebar .brand {
            padding: 1.1rem 1rem; color: #fff; font-size: 1rem; font-weight: 700;
            border-bottom: 1px solid #312e81; display: flex; align-items: center; justify-content: space-between;
        }
        .sidebar .nav-link {
            color: #c7d2fe; padding: .55rem 1rem; border-radius: 8px; margin: 1px 8px;
            display: flex; align-items: center; gap: .5rem; font-size: .9rem; transition: all .15s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #3730a3; color: #fff; }
        .sidebar .nav-link i { font-size: 1.05rem; width: 1.25rem; text-align: center; }
        .sidebar-overlay {
            display: none; position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 1039;
        }

        /* ── Main ── */
        .main-wrap { margin-right: var(--sidebar-w); min-height: 100vh; }
        .topbar {
            background: #fff; border-bottom: 1px solid #e5e7eb;
            padding: .65rem 1.25rem; display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 100;
        }
        .page-content { padding: 1.25rem; }

        /* ── Cards / UI ── */
        .card { border: none; box-shadow: 0 1px 3px rgba(0,0,0,.08); border-radius: 12px; }
        .card-header { background: transparent; border-bottom: 1px solid #f1f5f9; font-weight: 600; }
        .stat-card { border-radius: 12px; padding: 1.1rem; color: #fff; }
        .subject-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
        .progress { height: 8px; border-radius: 4px; }
        .jalali-input { direction: ltr; text-align: center; }
        .difficulty-stars { color: #f59e0b; }
        .table > :not(caption) > * > * { padding: .55rem .65rem; }
        .btn-action { padding: .2rem .45rem; font-size: .78rem; }
        .flash-success { background: #d1fae5; border-color: #6ee7b7; color: #065f46; }

        /* ── Bottom nav (mobile) ── */
        .bottom-nav {
            display: none; position: fixed; bottom: 0; left: 0; right: 0;
            background: #1e1b4b; z-index: 1050; padding: .4rem 0;
            border-top: 1px solid #312e81;
        }
        .bottom-nav a {
            color: #c7d2fe; text-decoration: none; display: flex; flex-direction: column;
            align-items: center; font-size: .62rem; gap: 2px; flex: 1; padding: .2rem 0;
        }
        .bottom-nav a i { font-size: 1.3rem; }
        .bottom-nav a.active, .bottom-nav a:hover { color: #fff; }

        /* ── Mobile overrides ── */
        @media(max-width: 767.98px) {
            .sidebar { transform: translateX(100%); box-shadow: -4px 0 20px rgba(0,0,0,.3); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay.open { display: block; }
            .sidebar .brand .close-btn { display: flex !important; }
            .main-wrap { margin-right: 0; padding-bottom: 65px; }
            .page-content { padding: .85rem; }
            .topbar { padding: .6rem .85rem; }
            .topbar .menu-btn { display: flex !important; }
            .stat-card { padding: .85rem; }
            .stat-card .fs-4 { font-size: 1.4rem !important; }
            .bottom-nav { display: flex; }
            .table-mobile-stack thead { display: none; }
            .table-mobile-stack tr { display: block; border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: .6rem; padding: .5rem; }
            .table-mobile-stack td { display: flex; justify-content: space-between; align-items: center; border: none; padding: .3rem .5rem; font-size: .88rem; }
            .table-mobile-stack td[data-label]::before { content: attr(data-label); font-weight: 600; color: #6b7280; font-size: .78rem; white-space: nowrap; margin-left: .5rem; }
            .hide-mobile { display: none !important; }
        }
        @media(min-width: 768px) {
            .sidebar .brand .close-btn { display: none !important; }
            .topbar .menu-btn { display: none !important; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- Overlay for mobile sidebar --}}
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

{{-- Sidebar --}}
<div class="sidebar" id="sidebar">
    <div class="brand">
        <span><i class="bi bi-book-half me-2"></i>ردیاب مطالعه</span>
        <button class="close-btn btn btn-sm text-white border-0 bg-transparent" onclick="closeSidebar()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    @php $todayPlanUrl = str_replace('/', '-', \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::today())->format('Y/m/d')); @endphp
    <nav class="mt-1 flex-grow-1 overflow-auto">
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" onclick="closeSidebar()">
            <i class="bi bi-speedometer2"></i> داشبورد
        </a>
        <a href="{{ route('plan.show', ['jalali' => $todayPlanUrl]) }}" class="nav-link {{ request()->routeIs('plan.*') ? 'active' : '' }}" onclick="closeSidebar()">
            <i class="bi bi-calendar-check"></i> برنامه روزانه
        </a>
        <a href="{{ route('study.create') }}" class="nav-link {{ request()->routeIs('study.create') ? 'active' : '' }}" onclick="closeSidebar()">
            <i class="bi bi-plus-circle"></i> ثبت مطالعه
        </a>
        <a href="{{ route('test.create') }}" class="nav-link {{ request()->routeIs('test.create') ? 'active' : '' }}" onclick="closeSidebar()">
            <i class="bi bi-pencil-square"></i> ثبت تست
        </a>
        <a href="{{ route('study.index') }}" class="nav-link {{ request()->routeIs('study.index') ? 'active' : '' }}" onclick="closeSidebar()">
            <i class="bi bi-journal-text"></i> سوابق مطالعه
        </a>
        <a href="{{ route('test.index') }}" class="nav-link {{ request()->routeIs('test.index') ? 'active' : '' }}" onclick="closeSidebar()">
            <i class="bi bi-list-check"></i> سوابق تست
        </a>
        <a href="{{ route('subjects.index') }}" class="nav-link {{ request()->routeIs('subjects.*') ? 'active' : '' }}" onclick="closeSidebar()">
            <i class="bi bi-collection"></i> درس‌ها
        </a>
        <a href="{{ route('report.index') }}" class="nav-link {{ request()->routeIs('report.*') ? 'active' : '' }}" onclick="closeSidebar()">
            <i class="bi bi-bar-chart-line"></i> گزارش
        </a>
        <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" onclick="closeSidebar()">
            <i class="bi bi-gear"></i> تنظیمات
        </a>
    </nav>
</div>

{{-- Main content --}}
<div class="main-wrap">
    <div class="topbar">
        <div class="d-flex align-items-center gap-2">
            <button class="menu-btn btn btn-sm btn-outline-secondary" onclick="openSidebar()">
                <i class="bi bi-list fs-5"></i>
            </button>
            <h6 class="mb-0 fw-semibold">@yield('page-title', 'داشبورد')</h6>
        </div>
        <small class="text-muted" id="today-date"></small>
    </div>
    <div class="page-content">
        @if(session('success'))
            <div class="alert flash-success alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @yield('content')
    </div>
</div>

{{-- Bottom nav (mobile only) --}}
@php $todayPlanUrl = str_replace('/', '-', \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::today())->format('Y/m/d')); @endphp
<nav class="bottom-nav">
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i><span>داشبورد</span>
    </a>
    <a href="{{ route('plan.show', ['jalali' => $todayPlanUrl]) }}" class="{{ request()->routeIs('plan.*') ? 'active' : '' }}">
        <i class="bi bi-calendar-check"></i><span>برنامه</span>
    </a>
    <a href="{{ route('study.create') }}" class="{{ request()->routeIs('study.create') ? 'active' : '' }}">
        <i class="bi bi-plus-circle-fill" style="font-size:1.6rem"></i><span>مطالعه</span>
    </a>
    <a href="{{ route('test.create') }}" class="{{ request()->routeIs('test.create') ? 'active' : '' }}">
        <i class="bi bi-pencil-square"></i><span>تست</span>
    </a>
    <a href="{{ route('report.index') }}" class="{{ request()->routeIs('report.*') ? 'active' : '' }}">
        <i class="bi bi-bar-chart-line"></i><span>گزارش</span>
    </a>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
@stack('scripts')
<script>
    document.getElementById('today-date').textContent = new Date().toLocaleDateString('fa-IR');

    function openSidebar() {
        document.getElementById('sidebar').classList.add('open');
        document.getElementById('sidebarOverlay').classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebarOverlay').classList.remove('open');
        document.body.style.overflow = '';
    }
</script>
</body>
</html>
