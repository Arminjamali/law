@extends('layouts.app')
@section('title', 'ویرایش تست')
@section('page-title', 'ویرایش جلسه تست')

@section('content')
<div class="row justify-content-center">
<div class="col-12 col-md-8">
<div class="card">
<div class="card-header"><i class="bi bi-pencil me-2"></i>ویرایش تست</div>
<div class="card-body">
<form method="POST" action="{{ route('test.update', $test) }}">
@csrf @method('PUT')
<div class="row g-3">
<div class="col-12 col-md-6">
    <label class="form-label fw-semibold">درس</label>
    <select name="subject_id" id="subject_id" class="form-select" required onchange="loadTopics(this.value)">
        @foreach($subjects as $s)
        <option value="{{ $s->id }}" {{ $test->subject_id==$s->id?'selected':'' }}>{{ $s->name }}</option>
        @endforeach
    </select>
</div>
<div class="col-12 col-md-6">
    <label class="form-label fw-semibold">مبحث</label>
    <select name="topic_id" id="topic_id" class="form-select">
        <option value="">همه مباحث</option>
        @foreach($topics as $t)
        <option value="{{ $t->id }}" {{ $test->topic_id==$t->id?'selected':'' }}>{{ $t->name }}</option>
        @endforeach
    </select>
</div>
<div class="col-12 col-md-4">
    <label class="form-label fw-semibold">تاریخ</label>
    <input type="text" name="jalali_date" class="form-control jalali-input" value="{{ $jalaliDate }}" required>
</div>
<div class="col-12 col-md-4">
    <label class="form-label fw-semibold">شروع</label>
    <input type="time" name="start_time" class="form-control" value="{{ $test->start_time ? substr($test->start_time,0,5) : '' }}">
</div>
<div class="col-12 col-md-4">
    <label class="form-label fw-semibold">پایان</label>
    <input type="time" name="end_time" class="form-control" value="{{ $test->end_time ? substr($test->end_time,0,5) : '' }}">
</div>
<div class="col-6 col-md-3">
    <label class="form-label fw-semibold">کل سوال</label>
    <input type="number" name="total_questions" class="form-control" value="{{ $test->total_questions }}" min="1" required>
</div>
<div class="col-6 col-md-3">
    <label class="form-label fw-semibold text-success">صحیح</label>
    <input type="number" name="correct_count" class="form-control border-success" value="{{ $test->correct_count }}" min="0" required>
</div>
<div class="col-6 col-md-3">
    <label class="form-label fw-semibold text-danger">غلط</label>
    <input type="number" name="wrong_count" class="form-control border-danger" value="{{ $test->wrong_count }}" min="0" required>
</div>
<div class="col-6 col-md-3">
    <label class="form-label fw-semibold text-secondary">ممتنع</label>
    <input type="number" name="unanswered_count" class="form-control" value="{{ $test->unanswered_count }}" min="0" required>
</div>
<div class="col-12 col-md-6">
    <label class="form-label fw-semibold">منبع تست</label>
    <input type="text" name="source" class="form-control" value="{{ $test->source }}">
</div>
<div class="col-12">
    <label class="form-label fw-semibold">یادداشت</label>
    <textarea name="notes" class="form-control" rows="2">{{ $test->notes }}</textarea>
</div>
</div>
<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary px-4">ذخیره تغییرات</button>
    <a href="{{ route('test.index') }}" class="btn btn-outline-secondary">انصراف</a>
</div>
</form>
</div>
</div>
</div>
</div>
@endsection
@push('scripts')
<script>
function loadTopics(subjectId) {
    fetch(`/api/topics?subject_id=${subjectId}`)
        .then(r=>r.json())
        .then(data=>{
            const t=document.getElementById('topic_id');
            t.innerHTML='<option value="">همه مباحث</option>';
            data.topics.forEach(x=>t.innerHTML+=`<option value="${x.id}">${x.name}</option>`);
        });
}
</script>
@endpush
