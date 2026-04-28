@extends('layouts.app')
@section('title', 'گزارش و تحلیل')
@section('page-title', 'گزارش و تحلیل پیشرفت')

@section('content')

{{-- کارت‌های کلی --}}
<div class="row g-3 mb-4">
    <div class="col-4 col-md-2">
        <div class="card text-center py-3">
            <div class="fs-3 fw-bold text-primary">{{ number_format($totalStudyMinutes/60, 1) }}</div>
            <small class="text-muted">ساعت مطالعه</small>
        </div>
    </div>
    <div class="col-4 col-md-2">
        <div class="card text-center py-3">
            <div class="fs-3 fw-bold text-info">{{ $studyDays }}</div>
            <small class="text-muted">روز مطالعه</small>
        </div>
    </div>
    <div class="col-4 col-md-2">
        <div class="card text-center py-3">
            <div class="fs-3 fw-bold text-success">{{ number_format($totalTests) }}</div>
            <small class="text-muted">کل سوال تست</small>
        </div>
    </div>
    <div class="col-4 col-md-2">
        <div class="card text-center py-3">
            <div class="fs-3 fw-bold text-success">{{ $totalCorrect }}</div>
            <small class="text-muted">صحیح</small>
        </div>
    </div>
    <div class="col-4 col-md-2">
        <div class="card text-center py-3">
            <div class="fs-3 fw-bold text-danger">{{ $totalWrong }}</div>
            <small class="text-muted">غلط</small>
        </div>
    </div>
    <div class="col-4 col-md-2">
        <div class="card text-center py-3">
            <div class="fs-3 fw-bold {{ $overallAccuracy>=70?'text-success':($overallAccuracy>=50?'text-warning':'text-danger') }}">
                {{ $overallAccuracy }}%
            </div>
            <small class="text-muted">دقت کل</small>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- نمودار ۳۰ روز --}}
    <div class="col-12 col-md-8">
        <div class="card">
            <div class="card-header py-2"><i class="bi bi-graph-up me-2"></i>ساعات مطالعه ۳۰ روز اخیر</div>
            <div class="card-body"><canvas id="study30Chart" height="120"></canvas></div>
        </div>
    </div>

    {{-- نمودار توزیع درسی --}}
    <div class="col-12 col-md-4">
        <div class="card">
            <div class="card-header py-2"><i class="bi bi-pie-chart me-2"></i>توزیع مطالعه</div>
            <div class="card-body"><canvas id="subjectPieChart" height="200"></canvas></div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- دقت تست به تفکیک درس --}}
    <div class="col-12 col-md-6">
        <div class="card">
            <div class="card-header py-2"><i class="bi bi-bar-chart me-2"></i>دقت تست به تفکیک درس</div>
            <div class="card-body"><canvas id="accuracyChart" height="200"></canvas></div>
        </div>
    </div>

    {{-- جدول خلاصه درس‌ها --}}
    <div class="col-12 col-md-6">
        <div class="card">
            <div class="card-header py-2"><i class="bi bi-table me-2"></i>خلاصه هر درس</div>
            <div class="card-body p-0">
            <div class="table-responsive">
            <table class="table table-sm mb-0">
            <thead class="table-light">
                <tr><th>درس</th><th>مطالعه (h)</th><th>تست</th><th>دقت</th><th>نمره</th></tr>
            </thead>
            <tbody>
            @foreach($subjects as $s)
            @php
                $mins = $s->studySessions->sum(fn($ss) => \Carbon\Carbon::parse($ss->start_time)->diffInMinutes(\Carbon\Carbon::parse($ss->end_time)));
                $tests = $s->testSessions->sum('total_questions');
                $correct = $s->testSessions->sum('correct_count');
                $wrong = $s->testSessions->sum('wrong_count');
                $acc = $tests > 0 ? round($correct/$tests*100,1) : 0;
                $score = $tests > 0 ? round(max(0,($correct-$wrong/3)/$tests*100),1) : 0;
            @endphp
            <tr>
                <td><span class="subject-dot me-1" style="background:{{ $s->color }}"></span>{{ $s->name }}</td>
                <td>{{ number_format($mins/60,1) }}</td>
                <td>{{ $tests }}</td>
                <td>
                    @if($tests > 0)
                    <span class="badge {{ $acc>=70?'bg-success':($acc>=50?'bg-warning':'bg-danger') }}">{{ $acc }}%</span>
                    @else <span class="text-muted">—</span> @endif
                </td>
                <td>{{ $tests > 0 ? $score.'%' : '—' }}</td>
            </tr>
            @endforeach
            </tbody>
            </table>
            </div>{{-- table-responsive --}}
            </div>
        </div>
    </div>

    {{-- تعداد تست روزانه --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header py-2"><i class="bi bi-activity me-2"></i>تست‌های روزانه ۳۰ روز اخیر</div>
            <div class="card-body"><canvas id="test30Chart" height="80"></canvas></div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const last30 = @json($last30);
const studyBySubject = @json($studyBySubject);
const testAccuracy = @json($testAccuracy);

// ۳۰ روز مطالعه
new Chart(document.getElementById('study30Chart'), {
    type: 'bar',
    data: {
        labels: last30.map(d=>d.label),
        datasets: [{
            label:'ساعت', data: last30.map(d=>d.hours),
            backgroundColor:'#818cf8', borderRadius:4
        }]
    },
    options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
});

// دایره درسی
new Chart(document.getElementById('subjectPieChart'), {
    type: 'doughnut',
    data: {
        labels: studyBySubject.map(d=>d.name),
        datasets:[{data: studyBySubject.map(d=>Math.round(d.minutes/60*10)/10), backgroundColor: studyBySubject.map(d=>d.color)}]
    },
    options:{responsive:true,plugins:{legend:{position:'bottom'}}}
});

// دقت تست
new Chart(document.getElementById('accuracyChart'), {
    type: 'bar',
    data: {
        labels: testAccuracy.map(d=>d.name),
        datasets:[{
            label:'دقت %', data: testAccuracy.map(d=>d.accuracy),
            backgroundColor: testAccuracy.map(d => d.accuracy>=70?'#059669': d.accuracy>=50?'#d97706':'#dc2626'),
            borderRadius:6
        }]
    },
    options:{responsive:true,indexAxis:'y',plugins:{legend:{display:false}},scales:{x:{max:100,beginAtZero:true}}}
});

// تست روزانه
new Chart(document.getElementById('test30Chart'), {
    type: 'line',
    data: {
        labels: last30.map(d=>d.label),
        datasets:[{
            label:'تعداد تست', data: last30.map(d=>d.tests),
            borderColor:'#0891b2', backgroundColor:'rgba(8,145,178,.1)', fill:true, tension:.3, pointRadius:3
        }]
    },
    options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
});
</script>
@endpush
