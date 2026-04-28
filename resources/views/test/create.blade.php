@extends('layouts.app')
@section('title', 'ثبت تست')
@section('page-title', 'ثبت جلسه تست‌زنی')

@section('content')
<div class="row justify-content-center">
<div class="col-md-8">
<div class="card">
<div class="card-header"><i class="bi bi-pencil-square me-2"></i>ثبت تست جدید</div>
<div class="card-body">
<form method="POST" action="{{ route('test.store') }}">
@csrf

<div class="row g-3">
<div class="col-md-6">
    <label class="form-label fw-semibold">درس <span class="text-danger">*</span></label>
    <select name="subject_id" id="subject_id" class="form-select" required onchange="loadTopics(this.value)">
        <option value="">انتخاب درس...</option>
        @foreach($subjects as $s)
        <option value="{{ $s->id }}" {{ old('subject_id')==$s->id?'selected':'' }}>{{ $s->name }}</option>
        @endforeach
    </select>
</div>
<div class="col-md-6">
    <label class="form-label fw-semibold">مبحث</label>
    <select name="topic_id" id="topic_id" class="form-select">
        <option value="">همه مباحث</option>
    </select>
</div>

<div class="col-md-4">
    <label class="form-label fw-semibold">تاریخ (شمسی) <span class="text-danger">*</span></label>
    <input type="text" name="jalali_date" class="form-control jalali-input" value="{{ old('jalali_date', $todayJalali) }}" required>
</div>
<div class="col-md-4">
    <label class="form-label fw-semibold">ساعت شروع</label>
    <input type="time" name="start_time" class="form-control" value="{{ old('start_time') }}">
</div>
<div class="col-md-4">
    <label class="form-label fw-semibold">ساعت پایان</label>
    <input type="time" name="end_time" class="form-control" value="{{ old('end_time') }}">
</div>

<div class="col-12"><hr class="my-1"><strong>نتایج تست</strong></div>

<div class="col-md-3">
    <label class="form-label fw-semibold">تعداد کل <span class="text-danger">*</span></label>
    <input type="number" name="total_questions" class="form-control" value="{{ old('total_questions') }}" min="1" required oninput="calcPercent()">
</div>
<div class="col-md-3">
    <label class="form-label fw-semibold text-success">صحیح ✓</label>
    <input type="number" name="correct_count" id="correct" class="form-control border-success" value="{{ old('correct_count',0) }}" min="0" required oninput="calcPercent()">
</div>
<div class="col-md-3">
    <label class="form-label fw-semibold text-danger">غلط ✗</label>
    <input type="number" name="wrong_count" id="wrong" class="form-control border-danger" value="{{ old('wrong_count',0) }}" min="0" required oninput="calcPercent()">
</div>
<div class="col-md-3">
    <label class="form-label fw-semibold text-secondary">ممتنع —</label>
    <input type="number" name="unanswered_count" id="unanswered" class="form-control" value="{{ old('unanswered_count',0) }}" min="0" required oninput="calcPercent()">
</div>

<div class="col-12">
    <div id="test-result" class="alert alert-info py-2 small" style="display:none"></div>
</div>

<div class="col-md-6">
    <label class="form-label fw-semibold">منبع تست</label>
    <input type="text" name="source" class="form-control" value="{{ old('source') }}" placeholder="مثال: هلی ۱۴۰۲، آزمون فروردین...">
</div>
<div class="col-12">
    <label class="form-label fw-semibold">یادداشت</label>
    <textarea name="notes" class="form-control" rows="2" placeholder="نکات، اشتباهات رایج...">{{ old('notes') }}</textarea>
</div>
</div>

<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>ثبت تست</button>
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
        .then(r => r.json())
        .then(data => {
            const t = document.getElementById('topic_id');
            t.innerHTML = '<option value="">همه مباحث</option>';
            data.topics.forEach(x => t.innerHTML += `<option value="${x.id}">${x.name}</option>`);
        });
}
function calcPercent() {
    const total   = parseInt(document.querySelector('[name=total_questions]').value) || 0;
    const correct = parseInt(document.getElementById('correct').value) || 0;
    const wrong   = parseInt(document.getElementById('wrong').value) || 0;
    const skip    = parseInt(document.getElementById('unanswered').value) || 0;
    if (!total) { document.getElementById('test-result').style.display='none'; return; }
    const accuracy = Math.round(correct / total * 100);
    const score    = Math.max(0, Math.round((correct - wrong/3) / total * 100));
    const el = document.getElementById('test-result');
    el.style.display = 'block';
    el.innerHTML = `دقت: <strong>${accuracy}%</strong> | نمره (با کسر اشتباه): <strong>${score}%</strong> | صحیح: ${correct} | غلط: ${wrong} | ممتنع: ${skip}`;
}
</script>
@endpush
