@extends('layouts.app')
@section('title', 'برنامه روزانه')
@section('page-title', 'برنامه روزانه')

@section('content')

{{-- تغییر تاریخ --}}
<div class="d-flex gap-2 align-items-center mb-3">
    <input type="text" id="dateNav" class="form-control jalali-input" style="max-width:160px"
        value="{{ $jalaliDate }}" placeholder="YYYY/MM/DD">
    <button class="btn btn-outline-primary btn-sm" onclick="goToDate()">برو</button>
    <a href="{{ route('plan.show', ['jalali' => $todayJalaliUrl]) }}" class="btn btn-sm btn-outline-secondary">امروز</a>
</div>

{{-- ایجاد/ویرایش برنامه روز --}}
<div class="card mb-3">
<div class="card-header py-2">
    <i class="bi bi-calendar-check me-2"></i>
    برنامه {{ $jalaliDate }}
    @if($plan)
        @php $pct = $plan->completion_percentage; @endphp
        <span class="ms-3 badge {{ $pct == 100 ? 'bg-success' : 'bg-primary' }}">{{ $pct }}% انجام شده</span>
    @endif
</div>
<div class="card-body">
    <form method="POST" action="{{ route('plan.store') }}" class="row g-2 align-items-end">
        @csrf
        <input type="hidden" name="jalali_date" value="{{ $jalaliDate }}">
        <div class="col-md-3">
            <label class="form-label small">هدف مطالعه (ساعت)</label>
            <input type="number" name="goal_hours" class="form-control form-control-sm"
                value="{{ $plan?->goal_hours ?? '' }}" min="0" max="24" step="0.5" placeholder="۶">
        </div>
        <div class="col-md-7">
            <label class="form-label small">یادداشت روز</label>
            <input type="text" name="notes" class="form-control form-control-sm"
                value="{{ $plan?->notes ?? '' }}" placeholder="توضیحات، اهداف روز...">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary btn-sm w-100">ذخیره</button>
        </div>
    </form>
</div>
</div>

{{-- آیتم‌های برنامه --}}
@if($plan)
<div class="card mb-3">
<div class="card-header py-2 d-flex justify-content-between">
    <span><i class="bi bi-list-ul me-2"></i>آیتم‌های برنامه</span>
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
        <i class="bi bi-plus-lg me-1"></i>افزودن آیتم
    </button>
</div>
<div class="card-body p-0">
<table class="table table-hover mb-0">
<thead class="table-light">
    <tr>
        <th style="width:40px"></th>
        <th>درس</th>
        <th>مبحث</th>
        <th>نوع</th>
        <th>ساعت</th>
        <th>منبع</th>
        <th>یادداشت</th>
        <th></th>
    </tr>
</thead>
<tbody>
@forelse($plan->items as $item)
<tr class="{{ $item->is_completed ? 'table-success' : '' }}">
    <td class="text-center">
        <div class="form-check">
            <input type="checkbox" class="form-check-input"
                {{ $item->is_completed ? 'checked' : '' }}
                onchange="toggleItem({{ $item->id }}, this)"
                style="cursor:pointer;width:1.2em;height:1.2em">
        </div>
    </td>
    <td>
        <span class="subject-dot me-1" style="background:{{ $item->subject->color }}"></span>
        <span class="{{ $item->is_completed ? 'text-decoration-line-through text-muted' : '' }}">
            {{ $item->subject->name }}
        </span>
    </td>
    <td class="text-muted small">{{ $item->topic?->name ?? '—' }}</td>
    <td>
        <span class="badge bg-secondary">{{ $item->getTypeLabel() }}</span>
        @if($item->study_type)
        <span class="badge bg-light text-dark">{{ $item->getStudyTypeLabel() }}</span>
        @endif
    </td>
    <td class="small text-nowrap">
        @if($item->start_time)
            {{ substr($item->start_time,0,5) }} – {{ substr($item->end_time,0,5) }}
        @else —
        @endif
    </td>
    <td class="text-muted small">{{ $item->resource?->name ?? '—' }}</td>
    <td class="text-muted small" style="max-width:180px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis">
        {{ $item->notes }}
    </td>
    <td>
        <form method="POST" action="{{ route('plan.item.destroy', $item) }}" class="d-inline" onsubmit="return confirm('حذف شود؟')">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger btn-action"><i class="bi bi-trash"></i></button>
        </form>
    </td>
</tr>
@empty
<tr><td colspan="8" class="text-center text-muted py-3">هنوز آیتمی اضافه نشده.</td></tr>
@endforelse
</tbody>
</table>
</div>
</div>
@else
<div class="alert alert-info">ابتدا برنامه روزانه را ذخیره کنید تا بتوانید آیتم اضافه کنید.</div>
@endif

{{-- مودال افزودن آیتم --}}
@if($plan)
<div class="modal fade" id="addItemModal" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">
<form method="POST" action="{{ route('plan.item.store') }}">
@csrf
<input type="hidden" name="daily_plan_id" value="{{ $plan->id }}">
<div class="modal-header">
    <h5 class="modal-title">افزودن آیتم به برنامه</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<div class="row g-3">
    <div class="col-12">
        <label class="form-label fw-semibold">درس</label>
        <select name="subject_id" id="item_subject" class="form-select" required onchange="loadItemTopics(this.value)">
            <option value="">انتخاب...</option>
            @foreach($subjects as $s)
            <option value="{{ $s->id }}">{{ $s->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">مبحث</label>
        <select name="topic_id" id="item_topic" class="form-select">
            <option value="">همه</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">نوع فعالیت</label>
        <select name="type" id="item_type" class="form-select" required onchange="toggleStudyType()">
            <option value="study">📖 مطالعه</option>
            <option value="test">📝 تست‌زنی</option>
        </select>
    </div>
    <div class="col-12" id="study_type_wrap">
        <label class="form-label fw-semibold">نوع مطالعه</label>
        <select name="study_type" class="form-select">
            <option value="book">📗 کتاب</option>
            <option value="pamphlet">📄 جزوه</option>
            <option value="teaching">🎓 تدریس</option>
            <option value="video">🎬 ویدیو</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">شروع</label>
        <input type="time" name="start_time" class="form-control">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">پایان</label>
        <input type="time" name="end_time" class="form-control">
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold">توضیحات</label>
        <textarea name="notes" class="form-control" rows="2" placeholder="توضیحات اضافه..."></textarea>
    </div>
</div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary">افزودن</button>
    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">انصراف</button>
</div>
</form>
</div>
</div>
</div>
@endif

@endsection

@push('scripts')
<script>
function goToDate() {
    const d = document.getElementById('dateNav').value.trim();
    if (d) window.location = '/plan/' + d.replace(/\//g, '-');
}
document.getElementById('dateNav').addEventListener('keydown', e => { if(e.key==='Enter') goToDate(); });

function toggleItem(id, checkbox) {
    fetch(`/plan/item/${id}/toggle`, {
        method: 'PATCH',
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content}
    })
    .then(r => r.json())
    .then(data => {
        const row = checkbox.closest('tr');
        row.classList.toggle('table-success', data.is_completed);
        const nameCell = row.querySelector('td:nth-child(2) span:last-child');
        nameCell.classList.toggle('text-decoration-line-through', data.is_completed);
        nameCell.classList.toggle('text-muted', data.is_completed);
    });
}
function loadItemTopics(subjectId) {
    fetch(`/api/topics?subject_id=${subjectId}`)
        .then(r => r.json())
        .then(data => {
            const t = document.getElementById('item_topic');
            t.innerHTML = '<option value="">همه</option>';
            data.topics.forEach(x => t.innerHTML += `<option value="${x.id}">${x.name}</option>`);
        });
}
function toggleStudyType() {
    const v = document.getElementById('item_type').value;
    document.getElementById('study_type_wrap').style.display = v === 'study' ? '' : 'none';
}
</script>
@endpush
