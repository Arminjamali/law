@extends('layouts.app')
@section('title', 'تنظیمات')
@section('page-title', 'تنظیمات برنامه')

@section('content')
<div class="row justify-content-center">
<div class="col-12 col-md-6">
<div class="card">
<div class="card-header"><i class="bi bi-gear me-2"></i>تنظیمات کلی</div>
<div class="card-body">
<form method="POST" action="{{ route('settings.update') }}">
@csrf @method('PUT')

<div class="mb-4">
    <label class="form-label fw-semibold">تاریخ آزمون وکالت (شمسی)</label>
    <input type="text" name="exam_jalali_date" class="form-control jalali-input"
        value="{{ $examJalali }}" placeholder="مثال: ۱۴۰۴/۰۸/۱۵">
    <div class="form-text">تاریخ آزمون را وارد کنید تا شمارش معکوس در داشبورد نمایش داده شود.</div>
</div>

<div class="mb-4">
    <label class="form-label fw-semibold">هدف مطالعه روزانه (ساعت)</label>
    <input type="number" name="daily_goal_hours" class="form-control" value="{{ $goalHours }}"
        min="1" max="24" step="0.5">
    <div class="form-text">چند ساعت در روز می‌خواهید مطالعه کنید؟</div>
</div>

<button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i>ذخیره تنظیمات</button>
</form>
</div>
</div>
</div>
</div>
@endsection
