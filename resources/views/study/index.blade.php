@extends('layouts.app')
@section('title', 'سوابق مطالعه')
@section('page-title', 'سوابق مطالعه')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <form class="d-flex gap-2" method="GET">
            <select name="subject_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">همه درس‌ها</option>
                @foreach($subjects as $s)
                <option value="{{ $s->id }}" {{ request('subject_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
        </form>
    </div>
    <a href="{{ route('study.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>مطالعه جدید</a>
</div>

<div class="card">
<div class="card-body p-0">
<div class="table-responsive">
<table class="table table-hover mb-0 table-mobile-stack">
<thead class="table-light">
    <tr>
        <th>تاریخ</th><th>درس</th><th class="hide-mobile">مبحث</th>
        <th>نوع</th><th class="hide-mobile">شروع</th><th class="hide-mobile">پایان</th>
        <th>مدت</th><th class="hide-mobile">تکرار</th><th class="hide-mobile">یادداشت</th><th></th>
    </tr>
</thead>
<tbody>
@forelse($sessions as $s)
<tr>
    <td data-label="تاریخ" class="text-nowrap">{{ \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($s->date))->format('Y/m/d') }}</td>
    <td data-label="درس"><span class="subject-dot me-1" style="background:{{ $s->subject->color }}"></span>{{ $s->subject->name }}</td>
    <td data-label="مبحث" class="text-muted small hide-mobile">{{ $s->topic?->name ?? '—' }}</td>
    <td data-label="نوع"><span class="badge bg-secondary">{{ \App\Models\StudySession::typeLabel($s->type) }}</span></td>
    <td data-label="شروع" class="hide-mobile">{{ substr($s->start_time,0,5) }}</td>
    <td data-label="پایان" class="hide-mobile">{{ substr($s->end_time,0,5) }}</td>
    <td data-label="مدت" class="fw-semibold">{{ $s->duration_formatted }}</td>
    <td data-label="تکرار" class="text-center hide-mobile">{{ $s->repeat_count }}×</td>
    <td data-label="یادداشت" class="text-muted small hide-mobile" style="max-width:180px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis">{{ $s->notes }}</td>
    <td>
        <a href="{{ route('study.edit', $s) }}" class="btn btn-outline-secondary btn-action"><i class="bi bi-pencil"></i></a>
        <form method="POST" action="{{ route('study.destroy', $s) }}" class="d-inline" onsubmit="return confirm('حذف شود؟')">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger btn-action"><i class="bi bi-trash"></i></button>
        </form>
    </td>
</tr>
@empty
<tr><td colspan="10" class="text-center text-muted py-4">هیچ جلسه مطالعه‌ای ثبت نشده.</td></tr>
@endforelse
</tbody>
</table>
</div>
</div>
</div>

<div class="mt-3">{{ $sessions->links() }}</div>
@endsection
