@extends('layouts.app')
@section('title', 'داشبورد')
@section('page-title', 'داشبورد')

@section('content')

{{-- شمارش معکوس --}}
@if($daysLeft !== null)
<div class="alert text-center mb-3 {{ $daysLeft <= 30 ? 'alert-danger' : 'alert-primary' }} border-0">
    <strong><i class="bi bi-alarm me-1"></i> {{ $daysLeft }} روز</strong> تا آزمون وکالت مرکز وکلا
</div>
@endif

{{-- کارت‌های آماری --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#4f46e5,#818cf8)">
            <div class="small mb-1 opacity-75">کل مطالعه</div>
            <div class="fs-4 fw-bold">{{ number_format($totalStudyMinutes/60,1) }} <small class="fs-6">ساعت</small></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#0891b2,#67e8f9)">
            <div class="small mb-1 opacity-75">این هفته</div>
            <div class="fs-4 fw-bold">{{ number_format($weekStudyMinutes/60,1) }} <small class="fs-6">ساعت</small></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#059669,#6ee7b7)">
            <div class="small mb-1 opacity-75">کل تست</div>
            <div class="fs-4 fw-bold">{{ number_format($totalTests) }} <small class="fs-6">سوال</small></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#d97706,#fcd34d)">
            <div class="small mb-1 opacity-75">دقت تست</div>
            <div class="fs-4 fw-bold">{{ $overallAccuracy }}<small class="fs-6">%</small></div>
        </div>
    </div>
</div>

<div class="row g-3">

    {{-- نمودار ۷ روز --}}
    <div class="col-md-7">
        <div class="card h-100">
            <div class="card-header py-2"><i class="bi bi-graph-up me-2"></i>مطالعه ۷ روز اخیر</div>
            <div class="card-body" style="min-height:180px">
                <canvas id="weekChart"></canvas>
            </div>
        </div>
    </div>

    {{-- پیشرفت هر درس --}}
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header py-2"><i class="bi bi-collection me-2"></i>پیشرفت درس‌ها</div>
            <div class="card-body">
                @foreach($subjects as $subject)
                @php
                    $mins = $subject->studySessions->sum(fn($s) =>
                        \Carbon\Carbon::parse($s->start_time)->diffInMinutes(\Carbon\Carbon::parse($s->end_time))
                    );
                    $totalMins = max(1, $subjects->sum(fn($sub) =>
                        $sub->studySessions->sum(fn($s) =>
                            \Carbon\Carbon::parse($s->start_time)->diffInMinutes(\Carbon\Carbon::parse($s->end_time))
                        )
                    ));
                    $pct = round($mins / $totalMins * 100);
                @endphp
                <div class="mb-2">
                    <div class="d-flex justify-content-between small mb-1">
                        <span><span class="subject-dot me-1" style="background:{{ $subject->color }}"></span>{{ $subject->name }}</span>
                        <span class="text-muted">{{ number_format($mins/60,1) }}h</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" style="width:{{ $pct }}%;background:{{ $subject->color }}"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- برنامه امروز --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar-day me-2"></i>برنامه امروز — {{ $todayJalali }}</span>
                <a href="{{ route('plan.show', ['jalali' => str_replace('/', '-', \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::today())->format('Y/m/d'))]) }}" class="btn btn-sm btn-outline-primary">مدیریت برنامه</a>
            </div>
            <div class="card-body">
                @if($todayPlan && $todayPlan->items->count())
                    @php $done = $todayPlan->items->where('is_completed',true)->count(); $total = $todayPlan->items->count(); @endphp
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="progress flex-grow-1" style="height:10px">
                            <div class="progress-bar bg-success" style="width:{{ $todayPlan->completion_percentage }}%"></div>
                        </div>
                        <small class="text-muted">{{ $done }}/{{ $total }}</small>
                    </div>
                    <div class="row g-2">
                        @foreach($todayPlan->items as $item)
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-2 p-2 rounded border check-item {{ $item->is_completed ? 'done' : '' }}"
                                 onclick="toggleItem({{ $item->id }}, this)" style="cursor:pointer">
                                <i class="bi {{ $item->is_completed ? 'bi-check-circle-fill text-success' : 'bi-circle' }} fs-5"></i>
                                <div>
                                    <div class="small fw-semibold">{{ $item->subject->name }}</div>
                                    <div class="small text-muted">
                                        {{ $item->getTypeLabel() }}
                                        @if($item->study_type) · {{ $item->getStudyTypeLabel() }} @endif
                                        @if($item->topic) · {{ $item->topic->name }} @endif
                                        @if($item->start_time) · {{ substr($item->start_time,0,5) }}–{{ substr($item->end_time,0,5) }} @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0 text-center py-3">
                        <i class="bi bi-calendar-plus fs-2 d-block mb-2"></i>
                        هنوز برنامه‌ای برای امروز ثبت نشده.
                        <a href="{{ route('plan.show', ['jalali' => str_replace('/', '-', \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::today())->format('Y/m/d'))]) }}">ثبت برنامه</a>
                    </p>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
const chartData = @json($last7);
new Chart(document.getElementById('weekChart'), {
    type: 'bar',
    data: {
        labels: chartData.map(d => d.label),
        datasets: [{
            label: 'ساعت مطالعه',
            data: chartData.map(d => d.hours),
            backgroundColor: '#818cf8',
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});

function toggleItem(id, el) {
    fetch(`/plan/item/${id}/toggle`, {
        method: 'PATCH',
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content}
    })
    .then(r => r.json())
    .then(data => {
        el.classList.toggle('done', data.is_completed);
        const icon = el.querySelector('i');
        icon.className = data.is_completed ? 'bi bi-check-circle-fill text-success fs-5' : 'bi bi-circle fs-5';
    });
}
</script>
@endpush
