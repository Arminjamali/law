@extends('layouts.app')
@section('title', 'ثبت مطالعه')
@section('page-title', 'ثبت جلسه مطالعه')

@section('content')
<div class="row justify-content-center">
<div class="col-12 col-md-8">
<div class="card">
<div class="card-header"><i class="bi bi-plus-circle me-2"></i>ثبت مطالعه جدید</div>
<div class="card-body">
<form method="POST" action="{{ route('study.store') }}">
@csrf

<div class="row g-3">

<div class="col-12 col-md-6">
    <label class="form-label fw-semibold">درس <span class="text-danger">*</span></label>
    <select name="subject_id" id="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required onchange="loadTopics(this.value)">
        <option value="">انتخاب درس...</option>
        @foreach($subjects as $s)
        <option value="{{ $s->id }}" {{ old('subject_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
        @endforeach
    </select>
    @error('subject_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="col-12 col-md-6">
    <label class="form-label fw-semibold">مبحث</label>
    <select name="topic_id" id="topic_id" class="form-select">
        <option value="">همه مباحث</option>
    </select>
</div>

<div class="col-12 col-md-6">
    <label class="form-label fw-semibold">منبع</label>
    <select name="resource_id" id="resource_id" class="form-select">
        <option value="">بدون منبع</option>
    </select>
</div>

<div class="col-12 col-md-6">
    <label class="form-label fw-semibold">نوع مطالعه <span class="text-danger">*</span></label>
    <select name="type" class="form-select @error('type') is-invalid @enderror" required>
        <option value="book" {{ old('type')=='book'?'selected':'' }}>📗 کتاب</option>
        <option value="pamphlet" {{ old('type')=='pamphlet'?'selected':'' }}>📄 جزوه</option>
        <option value="teaching" {{ old('type')=='teaching'?'selected':'' }}>🎓 تدریس</option>
        <option value="video" {{ old('type')=='video'?'selected':'' }}>🎬 ویدیو</option>
    </select>
</div>

<div class="col-12 col-md-4">
    <label class="form-label fw-semibold">تاریخ (شمسی) <span class="text-danger">*</span></label>
    <input type="text" name="jalali_date" class="form-control jalali-input @error('jalali_date') is-invalid @enderror"
        value="{{ old('jalali_date', $todayJalali) }}" placeholder="مثال: ۱۴۰۴/۰۲/۰۷" required>
    <div class="form-text">فرمت: YYYY/MM/DD</div>
</div>

<div class="col-12 col-md-4">
    <label class="form-label fw-semibold">ساعت شروع <span class="text-danger">*</span></label>
    <input type="time" name="start_time" class="form-control @error('start_time') is-invalid @enderror"
        value="{{ old('start_time') }}" required>
</div>

<div class="col-12 col-md-4">
    <label class="form-label fw-semibold">ساعت پایان <span class="text-danger">*</span></label>
    <input type="time" name="end_time" class="form-control @error('end_time') is-invalid @enderror"
        value="{{ old('end_time') }}" required>
</div>

<div class="col-12 col-md-4">
    <label class="form-label fw-semibold">تعداد تکرار</label>
    <input type="number" name="repeat_count" class="form-control" value="{{ old('repeat_count',1) }}" min="1" max="10">
    <div class="form-text">چند بار این مبحث را خواندی؟</div>
</div>

<div class="col-12">
    <label class="form-label fw-semibold">یادداشت</label>
    <textarea name="notes" class="form-control" rows="3" placeholder="توضیحات، نکات مهم...">{{ old('notes') }}</textarea>
</div>

</div>

<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>ثبت مطالعه</button>
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
    if (!subjectId) return;
    fetch(`/api/topics?subject_id=${subjectId}`)
        .then(r => r.json())
        .then(data => {
            const topicSel = document.getElementById('topic_id');
            const resSel   = document.getElementById('resource_id');
            topicSel.innerHTML = '<option value="">همه مباحث</option>';
            resSel.innerHTML   = '<option value="">بدون منبع</option>';
            data.topics.forEach(t => {
                topicSel.innerHTML += `<option value="${t.id}">${t.name}</option>`;
            });
            data.resources.forEach(r => {
                const typeMap = {book:'کتاب', pamphlet:'جزوه', video:'ویدیو', other:'سایر'};
                resSel.innerHTML += `<option value="${r.id}">${r.name} (${typeMap[r.type]||r.type})</option>`;
            });
        });
}
</script>
@endpush
