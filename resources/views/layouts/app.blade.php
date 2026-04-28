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
        :root {
            --sidebar-w: 240px;
            --primary: #4f46e5;
            --primary-light: #eef2ff;
        }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f8fafc; }
        .sidebar {
            width: var(--sidebar-w); min-height: 100vh; background: #1e1b4b;
            position: fixed; right: 0; top: 0; z-index: 100; display: flex; flex-direction: column;
        }
        .sidebar .brand { padding: 1.25rem 1rem; color: #fff; font-size: 1.1rem; font-weight: 700; border-bottom: 1px solid #312e81; }
        .sidebar .nav-link {
            color: #c7d2fe; padding: .6rem 1rem; border-radius: 8px; margin: 2px 8px;
            display: flex; align-items: center; gap: .5rem; font-size: .92rem; transition: all .15s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #3730a3; color: #fff; }
        .sidebar .nav-link i { font-size: 1.1rem; }
        .main-wrap { margin-right: var(--sidebar-w); min-height: 100vh; }
        .topbar { background: #fff; border-bottom: 1px solid #e5e7eb; padding: .75rem 1.5rem; display: flex; align-items: center; justify-content: space-between; }
        .page-content { padding: 1.5rem; }
        .card { border: none; box-shadow: 0 1px 3px rgba(0,0,0,.08); border-radius: 12px; }
        .card-header { background: transparent; border-bottom: 1px solid #f1f5f9; font-weight: 600; }
        .stat-card { border-radius: 12px; padding: 1.25rem; color: #fff; }
        .badge-pill { border-radius: 20px; padding: .3em .75em; }
        .subject-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
        .progress { height: 8px; border-radius: 4px; }
        .jalali-input { direction: ltr; text-align: center; }
        .check-item { cursor: pointer; transition: opacity .2s; }
        .check-item.done { opacity: .55; text-decoration: line-through; }
        .difficulty-stars { color: #f59e0b; }
        @media(max-width:768px){
            .sidebar { display: none; }
            .main-wrap { margin-right: 0; }
        }
        .table > :not(caption) > * > * { padding: .6rem .75rem; }
        .btn-action { padding: .25rem .5rem; font-size: .8rem; }
        .flash-success { background: #d1fae5; border-color: #6ee7b7; color: #065f46; }
    </style>
    @stack('styles')
</head>
<body>

<div class="sidebar">
    <div class="brand">
        <i class="bi bi-book-half me-2"></i> ردیاب مطالعه وکالت
    </div>
    <nav class="mt-2 flex-grow-1">
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> داشبورد
        </a>
        <a href="{{ route('plan.show') }}" class="nav-link {{ request()->routeIs('plan.*') ? 'active' : '' }}">
            <i class="bi bi-calendar-check"></i> برنامه روزانه
        </a>
        <a href="{{ route('study.create') }}" class="nav-link {{ request()->routeIs('study.create') ? 'active' : '' }}">
            <i class="bi bi-plus-circle"></i> ثبت مطالعه
        </a>
        <a href="{{ route('test.create') }}" class="nav-link {{ request()->routeIs('test.create') ? 'active' : '' }}">
            <i class="bi bi-pencil-square"></i> ثبت تست
        </a>
        <a href="{{ route('study.index') }}" class="nav-link {{ request()->routeIs('study.index') ? 'active' : '' }}">
            <i class="bi bi-journal-text"></i> سوابق مطالعه
        </a>
        <a href="{{ route('test.index') }}" class="nav-link {{ request()->routeIs('test.index') ? 'active' : '' }}">
            <i class="bi bi-list-check"></i> سوابق تست
        </a>
        <a href="{{ route('subjects.index') }}" class="nav-link {{ request()->routeIs('subjects.*') ? 'active' : '' }}">
            <i class="bi bi-collection"></i> درس‌ها و منابع
        </a>
        <a href="{{ route('report.index') }}" class="nav-link {{ request()->routeIs('report.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line"></i> گزارش و تحلیل
        </a>
        <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <i class="bi bi-gear"></i> تنظیمات
        </a>
    </nav>
</div>

<div class="main-wrap">
    <div class="topbar">
        <h6 class="mb-0 fw-semibold">@yield('page-title', 'داشبورد')</h6>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
@stack('scripts')
<script>
    // نمایش تاریخ شمسی امروز
    const now = new Date();
    const jalaliStr = now.toLocaleDateString('fa-IR');
    document.getElementById('today-date').textContent = jalaliStr;
</script>
</body>
</html>
