@extends('layouts.app')
@section('title', 'درس‌ها و منابع')
@section('page-title', 'درس‌ها و منابع')

@section('content')
<div class="row g-3">
@foreach($subjects as $subject)
<div class="col-md-4">
<div class="card h-100">
<div class="card-header py-2 d-flex justify-content-between align-items-center" style="border-right:4px solid {{ $subject->color }}">
    <span class="fw-semibold">{{ $subject->name }}</span>
    <a href="{{ route('subjects.show', $subject) }}" class="btn btn-sm btn-outline-primary">جزئیات</a>
</div>
<div class="card-body py-2">
    <div class="row text-center g-0">
        <div class="col border-end">
            <div class="small text-muted">مطالعه</div>
            <div class="fw-bold">{{ $subject->study_sessions_count }}</div>
        </div>
        <div class="col border-end">
            <div class="small text-muted">تست</div>
            <div class="fw-bold">{{ $subject->test_sessions_count }}</div>
        </div>
        <div class="col">
            <div class="small text-muted">مباحث</div>
            <div class="fw-bold">{{ $subject->topics_count }}</div>
        </div>
    </div>
</div>
</div>
</div>
@endforeach
</div>
@endsection
