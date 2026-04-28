<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\StudySession;
use App\Models\TestSession;
use App\Models\DailyPlan;
use App\Models\AppSetting;
use Morilog\Jalali\Jalalian;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $todayJalali = Jalalian::fromCarbon($today)->format('Y/m/d');

        // اعداد کلی
        $totalStudyMinutes = StudySession::all()->sum(fn($s) =>
            Carbon::parse($s->start_time)->diffInMinutes(Carbon::parse($s->end_time))
        );
        $totalTests    = TestSession::sum('total_questions');
        $totalCorrect  = TestSession::sum('correct_count');
        $overallAccuracy = $totalTests > 0 ? round($totalCorrect / $totalTests * 100, 1) : 0;

        // هفته جاری
        $weekStart = $today->copy()->startOfWeek(Carbon::SATURDAY);
        $weekStudyMinutes = StudySession::whereBetween('date', [$weekStart, $today])
            ->get()->sum(fn($s) =>
                Carbon::parse($s->start_time)->diffInMinutes(Carbon::parse($s->end_time))
            );

        // امروز
        $todayPlan = DailyPlan::with(['items.subject', 'items.topic'])
            ->whereDate('date', $today)->first();

        // ۷ روز اخیر برای نمودار
        $last7 = collect(range(6, 0))->map(function ($daysAgo) use ($today) {
            $date = $today->copy()->subDays($daysAgo);
            $mins = StudySession::whereDate('date', $date)->get()
                ->sum(fn($s) => Carbon::parse($s->start_time)->diffInMinutes(Carbon::parse($s->end_time)));
            return [
                'label' => Jalalian::fromCarbon($date)->format('m/d'),
                'minutes' => $mins,
                'hours' => round($mins / 60, 1),
            ];
        });

        // درصد پیشرفت هر درس
        $subjects = Subject::where('is_active', true)
            ->with(['studySessions', 'testSessions'])
            ->orderBy('order')->get();

        $examDate = AppSetting::get('exam_date');
        $daysLeft = null;
        if ($examDate) {
            $daysLeft = max(0, Carbon::today()->diffInDays(Carbon::parse($examDate), false));
        }

        $goalHours = (float) AppSetting::get('daily_goal_hours', 6);

        return view('dashboard', compact(
            'totalStudyMinutes', 'totalTests', 'overallAccuracy',
            'weekStudyMinutes', 'todayPlan', 'last7', 'subjects',
            'daysLeft', 'examDate', 'todayJalali', 'goalHours'
        ));
    }
}
