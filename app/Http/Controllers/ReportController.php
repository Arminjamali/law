<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\StudySession;
use App\Models\TestSession;
use Morilog\Jalali\Jalalian;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $subjects = Subject::where('is_active', true)
            ->with(['studySessions', 'testSessions'])
            ->orderBy('order')->get();

        // کل ساعات مطالعه به تفکیک درس
        $studyBySubject = $subjects->map(fn($s) => [
            'name'    => $s->name,
            'color'   => $s->color,
            'minutes' => $s->studySessions->sum(fn($ss) =>
                Carbon::parse($ss->start_time)->diffInMinutes(Carbon::parse($ss->end_time))
            ),
        ])->filter(fn($s) => $s['minutes'] > 0)->values();

        // ۳۰ روز اخیر - ساعت مطالعه روزانه
        $last30 = collect(range(29, 0))->map(function ($d) {
            $date = Carbon::today()->subDays($d);
            $mins = StudySession::whereDate('date', $date)->get()
                ->sum(fn($s) => Carbon::parse($s->start_time)->diffInMinutes(Carbon::parse($s->end_time)));
            $tests = TestSession::whereDate('date', $date)->sum('total_questions');
            return [
                'label'   => Jalalian::fromCarbon($date)->format('m/d'),
                'hours'   => round($mins / 60, 1),
                'tests'   => $tests,
            ];
        });

        // دقت تست به تفکیک درس
        $testAccuracy = $subjects->map(fn($s) => [
            'name'     => $s->name,
            'color'    => $s->color,
            'accuracy' => $s->test_accuracy,
            'total'    => $s->testSessions->sum('total_questions'),
        ])->filter(fn($s) => $s['total'] > 0)->values();

        // آمار کلی
        $totalStudyMinutes = StudySession::all()->sum(fn($s) =>
            Carbon::parse($s->start_time)->diffInMinutes(Carbon::parse($s->end_time))
        );
        $totalTests    = TestSession::sum('total_questions');
        $totalCorrect  = TestSession::sum('correct_count');
        $totalWrong    = TestSession::sum('wrong_count');
        $totalUnanswered = TestSession::sum('unanswered_count');
        $overallAccuracy = $totalTests > 0 ? round($totalCorrect / $totalTests * 100, 1) : 0;
        $overallScore = $totalTests > 0
            ? round(max(0, ($totalCorrect - $totalWrong / 3) / $totalTests * 100), 1)
            : 0;

        // تعداد روزهای مطالعه
        $studyDays = StudySession::selectRaw('date')->groupBy('date')->count();

        return view('report.index', compact(
            'subjects', 'studyBySubject', 'last30', 'testAccuracy',
            'totalStudyMinutes', 'totalTests', 'totalCorrect', 'totalWrong',
            'totalUnanswered', 'overallAccuracy', 'overallScore', 'studyDays'
        ));
    }
}
