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
<table class="table table-hover mb-0">
<thead class="table-light">
    <tr>
        <th>تاریخ</th>
        <th>درس</th>
        <th>مبحث</th>
        <th>نوع</th>
        <th>شروع</th>
        <th>پایان</th>
        <th>مدت</th>
        <th>تکرار</th>
        <th>یادداشت</th>
        <th></th>
    </tr>
</thead>
<tbody>
@forelse($sessions as $s)
<tr>
    <td class="text-nowrap">{{ \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($s->date))->format('Y/m/d') }}</td>
    <td><span class="subject-dot me-1" style="background:{{ $s->subject->color }}"></span>{{ $s->subject->name }}</td>
    <td class="text-muted small">{{ $s->topic?->name ?? '—' }}</td>
    <td><span class="badge bg-secondary badge-pill">{{ \App\Models\StudySession::typeLabel($s->type) }}</span></td>
    <td>{{ substr($s->start_time,0,5) }}</td>
    <td>{{ substr($s->end_time,0,5) }}</td>
    <td class="fw-semibold">{{ $s->duration_formatted }}</td>
    <td class="text-center">{{ $s->repeat_count }}×</td>
    <td class="text-muted small" style="max-width:200px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis">{{ $s->notes }}</td>
    <td class="text-nowrap">
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

<div class="mt-3">{{ $sessions->links() }}</div>
@endsection
