@extends('layouts.app')
@section('title', 'ویرایش مطالعه')
@section('page-title', 'ویرایش جلسه مطالعه')

@section('content')
<div class="row justify-content-center">
<div class="col-12 col-md-8">
<div class="card">
<div class="card-header"><i class="bi bi-pencil me-2"></i>ویرایش مطالعه</div>
<div class="card-body">
<form method="POST" action="{{ route('study.update', $study) }}">
@csrf @method('PUT')

<div class="row g-3">
<div class="col-12 col-md-6">
    <label class="form-label fw-semibold">درس</label>
    <select name="subject_id" id="subject_id" class="form-select" required onchange="loadTopics(this.value)">
        @foreach($subjects as $s)
        <option value="{{ $s->id }}" {{ $study->subject_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
        @endforeach
    </select>
</div>
<div class="col-12 col-md-6">
    <label class="form-label fw-semibold">مبحث</label>
    <select name="topic_id" id="topic_id" class="form-select">
        <option value="">همه مباحث</option>
        @foreach($topics as $t)
        <option value="{{ $t->id }}" {{ $study->topic_id == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
        @endforeach
    </select>
</div>
<div class="col-12 col-md-6">
    <label class="form-label fw-semibold">منبع</label>
    <select name="resource_id" id="resource_id" class="form-select">
        <option value="">بدون منبع</option>
        @foreach($resources as $r)
        <option value="{{ $r->id }}" {{ $study->resource_id == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
        @endforeach
    </select>
</div>
<div class="col-12 col-md-6">
    <label class="form-label fw-semibold">نوع مطالعه</label>
    <select name="type" class="form-select" required>
        @foreach(['book'=>'📗 کتاب','pamphlet'=>'📄 جزوه','teaching'=>'🎓 تدریس','video'=>'🎬 ویدیو'] as $val=>$lbl)
        <option value="{{ $val }}" {{ $study->type==$val?'selected':'' }}>{{ $lbl }}</option>
        @endforeach
    </select>
</div>
<div class="col-12 col-md-4">
    <label class="form-label fw-semibold">تاریخ (شمسی)</label>
    <input type="text" name="jalali_date" class="form-control jalali-input" value="{{ $jalaliDate }}" required>
</div>
<div class="col-12 col-md-4">
    <label class="form-label fw-semibold">شروع</label>
    <input type="time" name="start_time" class="form-control" value="{{ substr($study->start_time,0,5) }}" required>
</div>
<div class="col-12 col-md-4">
    <label class="form-label fw-semibold">پایان</label>
    <input type="time" name="end_time" class="form-control" value="{{ substr($study->end_time,0,5) }}" required>
</div>
<div class="col-12 col-md-4">
    <label class="form-label fw-semibold">تکرار</label>
    <input type="number" name="repeat_count" class="form-control" value="{{ $study->repeat_count }}" min="1" max="10">
</div>
<div class="col-12">
    <label class="form-label fw-semibold">یادداشت</label>
    <textarea name="notes" class="form-control" rows="3">{{ $study->notes }}</textarea>
</div>
</div>
<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary px-4">ذخیره تغییرات</button>
    <a href="{{ route('study.index') }}" class="btn btn-outline-secondary">انصراف</a>
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
        .then(r => r.json())
        .then(data => {
            const t = document.getElementById('topic_id');
            const r = document.getElementById('resource_id');
            t.innerHTML = '<option value="">همه مباحث</option>';
            r.innerHTML = '<option value="">بدون منبع</option>';
            data.topics.forEach(x => t.innerHTML += `<option value="${x.id}">${x.name}</option>`);
            data.resources.forEach(x => r.innerHTML += `<option value="${x.id}">${x.name}</option>`);
        });
}
</script>
@endpush
