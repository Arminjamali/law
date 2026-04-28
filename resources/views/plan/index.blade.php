@extends('layouts.app')
@section('title', 'تاریخچه برنامه‌ها')
@section('page-title', 'تاریخچه برنامه‌ها')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h6 class="mb-0">همه برنامه‌های روزانه</h6>
    <a href="{{ route('plan.show') }}" class="btn btn-primary btn-sm"><i class="bi bi-calendar-plus me-1"></i>برنامه امروز</a>
</div>
<div class="card">
<div class="card-body p-0">
<table class="table table-hover mb-0">
<thead class="table-light">
    <tr><th>تاریخ</th><th>هدف</th><th>آیتم‌ها</th><th>پیشرفت</th><th></th></tr>
</thead>
<tbody>
@forelse($plans as $plan)
@php
    $jalali = \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($plan->date))->format('Y/m/d');
    $done = $plan->items->where('is_completed',true)->count();
    $total = $plan->items->count();
@endphp
<tr>
    <td>{{ $jalali }}</td>
    <td>{{ $plan->goal_hours ? $plan->goal_hours . 'h' : '—' }}</td>
    <td>{{ $total }} آیتم</td>
    <td style="min-width:120px">
        <div class="d-flex align-items-center gap-2">
            <div class="progress flex-grow-1"><div class="progress-bar bg-success" style="width:{{ $plan->completion_percentage }}%"></div></div>
            <small>{{ $done }}/{{ $total }}</small>
        </div>
    </td>
    <td><a href="{{ route('plan.show', ['jalali'=>$jalali]) }}" class="btn btn-sm btn-outline-primary">مشاهده</a></td>
</tr>
@empty
<tr><td colspan="5" class="text-center text-muted py-4">هیچ برنامه‌ای ثبت نشده.</td></tr>
@endforelse
</tbody>
</table>
</div>
</div>
<div class="mt-3">{{ $plans->links() }}</div>
@endsection
