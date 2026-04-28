@extends('layouts.app')
@section('title', 'سوابق تست')
@section('page-title', 'سوابق تست‌زنی')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <form class="d-flex gap-2" method="GET">
        <select name="subject_id" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">همه درس‌ها</option>
            @foreach($subjects as $s)
            <option value="{{ $s->id }}" {{ request('subject_id')==$s->id?'selected':'' }}>{{ $s->name }}</option>
            @endforeach
        </select>
    </form>
    <a href="{{ route('test.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>تست جدید</a>
</div>

<div class="card">
<div class="card-body p-0">
<div class="table-responsive">
<table class="table table-hover mb-0 table-mobile-stack">
<thead class="table-light">
    <tr>
        <th>تاریخ</th><th>درس</th><th class="hide-mobile">مبحث</th>
        <th>کل</th><th class="text-success">✓</th><th class="text-danger">✗</th>
        <th class="text-secondary hide-mobile">ممتنع</th>
        <th>دقت</th><th class="hide-mobile">نمره</th><th class="hide-mobile">منبع</th><th></th>
    </tr>
</thead>
<tbody>
@forelse($sessions as $s)
<tr>
    <td data-label="تاریخ" class="text-nowrap">{{ \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($s->date))->format('Y/m/d') }}</td>
    <td data-label="درس"><span class="subject-dot me-1" style="background:{{ $s->subject->color }}"></span>{{ $s->subject->name }}</td>
    <td data-label="مبحث" class="text-muted small hide-mobile">{{ $s->topic?->name ?? '—' }}</td>
    <td data-label="کل">{{ $s->total_questions }}</td>
    <td data-label="صحیح" class="text-success fw-semibold">{{ $s->correct_count }}</td>
    <td data-label="غلط" class="text-danger">{{ $s->wrong_count }}</td>
    <td data-label="ممتنع" class="text-secondary hide-mobile">{{ $s->unanswered_count }}</td>
    <td data-label="دقت">
        <span class="badge {{ $s->accuracy >= 70 ? 'bg-success' : ($s->accuracy >= 50 ? 'bg-warning' : 'bg-danger') }}">{{ $s->accuracy }}%</span>
    </td>
    <td data-label="نمره" class="hide-mobile">{{ $s->score }}%</td>
    <td data-label="منبع" class="text-muted small hide-mobile">{{ $s->source ?? '—' }}</td>
    <td>
        <a href="{{ route('test.edit', $s) }}" class="btn btn-outline-secondary btn-action"><i class="bi bi-pencil"></i></a>
        <form method="POST" action="{{ route('test.destroy', $s) }}" class="d-inline" onsubmit="return confirm('حذف شود؟')">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger btn-action"><i class="bi bi-trash"></i></button>
        </form>
    </td>
</tr>
@empty
<tr><td colspan="11" class="text-center text-muted py-4">هیچ جلسه تستی ثبت نشده.</td></tr>
@endforelse
</tbody>
</table>
</div>
</div>
</div>
<div class="mt-3">{{ $sessions->links() }}</div>
@endsection
