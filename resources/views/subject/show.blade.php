@extends('layouts.app')
@section('title', $subject->name)
@section('page-title', $subject->name)

@section('content')
<div class="row g-3">

{{-- مباحث --}}
<div class="col-md-6">
<div class="card">
<div class="card-header py-2 d-flex justify-content-between align-items-center">
    <span><i class="bi bi-list-nested me-2"></i>مباحث</span>
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addTopicModal">+ مبحث</button>
</div>
<div class="card-body p-0">
<table class="table table-sm mb-0">
<thead class="table-light"><tr><th>نام</th><th>دشواری</th><th>یادداشت</th><th></th></tr></thead>
<tbody>
@forelse($subject->topics as $topic)
<tr>
    <td>{{ $topic->name }}</td>
    <td class="difficulty-stars">{{ str_repeat('★', $topic->difficulty) }}{{ str_repeat('☆', 5-$topic->difficulty) }}</td>
    <td class="text-muted small">{{ Str::limit($topic->notes, 40) }}</td>
    <td>
        <form method="POST" action="{{ route('topics.destroy', $topic) }}" class="d-inline" onsubmit="return confirm('حذف؟')">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger btn-action"><i class="bi bi-trash"></i></button>
        </form>
    </td>
</tr>
@empty
<tr><td colspan="4" class="text-center text-muted py-3">مبحثی تعریف نشده.</td></tr>
@endforelse
</tbody>
</table>
</div>
</div>
</div>

{{-- منابع --}}
<div class="col-md-6">
<div class="card">
<div class="card-header py-2 d-flex justify-content-between align-items-center">
    <span><i class="bi bi-journal-bookmark me-2"></i>منابع</span>
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addResourceModal">+ منبع</button>
</div>
<div class="card-body p-0">
<table class="table table-sm mb-0">
<thead class="table-light"><tr><th>نام</th><th>نوع</th><th>نویسنده</th><th></th></tr></thead>
<tbody>
@forelse($subject->resources as $resource)
<tr>
    <td>{{ $resource->name }}</td>
    <td><span class="badge bg-secondary">{{ \App\Models\Resource::typeLabel($resource->type) }}</span></td>
    <td class="text-muted small">{{ $resource->author ?? '—' }}</td>
    <td>
        <form method="POST" action="{{ route('resources.destroy', $resource) }}" class="d-inline" onsubmit="return confirm('حذف؟')">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger btn-action"><i class="bi bi-trash"></i></button>
        </form>
    </td>
</tr>
@empty
<tr><td colspan="4" class="text-center text-muted py-3">منبعی تعریف نشده.</td></tr>
@endforelse
</tbody>
</table>
</div>
</div>
</div>
</div>

{{-- مودال مبحث --}}
<div class="modal fade" id="addTopicModal" tabindex="-1">
<div class="modal-dialog"><div class="modal-content">
<form method="POST" action="{{ route('subjects.topics.store', $subject) }}">@csrf
<div class="modal-header"><h5 class="modal-title">افزودن مبحث</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<div class="modal-body">
    <div class="mb-3"><label class="form-label">نام مبحث</label><input name="name" class="form-control" required></div>
    <div class="mb-3">
        <label class="form-label">سطح دشواری</label>
        <select name="difficulty" class="form-select">
            <option value="1">۱ — آسان</option>
            <option value="2">۲</option>
            <option value="3" selected>۳ — متوسط</option>
            <option value="4">۴</option>
            <option value="5">۵ — سخت</option>
        </select>
    </div>
    <div class="mb-3"><label class="form-label">یادداشت</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary">افزودن</button>
    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">انصراف</button>
</div>
</form>
</div></div>
</div>

{{-- مودال منبع --}}
<div class="modal fade" id="addResourceModal" tabindex="-1">
<div class="modal-dialog"><div class="modal-content">
<form method="POST" action="{{ route('subjects.resources.store', $subject) }}">@csrf
<div class="modal-header"><h5 class="modal-title">افزودن منبع</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<div class="modal-body">
    <div class="mb-3"><label class="form-label">نام منبع</label><input name="name" class="form-control" required placeholder="مثال: حقوق مدنی کاتوزیان"></div>
    <div class="mb-3">
        <label class="form-label">نوع</label>
        <select name="type" class="form-select">
            <option value="book">📗 کتاب</option>
            <option value="pamphlet">📄 جزوه</option>
            <option value="video">🎬 ویدیو</option>
            <option value="other">سایر</option>
        </select>
    </div>
    <div class="mb-3"><label class="form-label">نویسنده/استاد</label><input name="author" class="form-control" placeholder="اختیاری"></div>
    <div class="mb-3"><label class="form-label">یادداشت</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary">افزودن</button>
    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">انصراف</button>
</div>
</form>
</div></div>
</div>

@endsection
