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
<table class="table table-hover mb-0">
<thead class="table-light">
    <tr>
        <th>تاریخ</th>
        <th>درس</th>
        <th>مبحث</th>
        <th>کل</th>
        <th class="text-success">صحیح</th>
        <th class="text-danger">غلط</th>
        <th class="text-secondary">ممتنع</th>
        <th>دقت</th>
        <th>نمره</th>
        <th>منبع</th>
        <th></th>
    </tr>
</thead>
<tbody>
@forelse($sessions as $s)
<tr>
    <td class="text-nowrap">{{ \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($s->date))->format('Y/m/d') }}</td>
    <td><span class="subject-dot me-1" style="background:{{ $s->subject->color }}"></span>{{ $s->subject->name }}</td>
    <td class="text-muted small">{{ $s->topic?->name ?? '—' }}</td>
    <td>{{ $s->total_questions }}</td>
    <td class="text-success fw-semibold">{{ $s->correct_count }}</td>
    <td class="text-danger">{{ $s->wrong_count }}</td>
    <td class="text-secondary">{{ $s->unanswered_count }}</td>
    <td>
        <span class="badge {{ $s->accuracy >= 70 ? 'bg-success' : ($s->accuracy >= 50 ? 'bg-warning' : 'bg-danger') }}">
            {{ $s->accuracy }}%
        </span>
    </td>
    <td>{{ $s->score }}%</td>
    <td class="text-muted small">{{ $s->source ?? '—' }}</td>
    <td class="text-nowrap">
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
<div class="mt-3">{{ $sessions->links() }}</div>
@endsection
