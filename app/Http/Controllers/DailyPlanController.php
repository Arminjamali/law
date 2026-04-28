<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyPlan;
use App\Models\DailyPlanItem;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Resource;
use Morilog\Jalali\Jalalian;
use Carbon\Carbon;

class DailyPlanController extends Controller
{
    public function index()
    {
        $plans = DailyPlan::with(['items.subject'])->orderByDesc('date')->paginate(30);
        return view('plan.index', compact('plans'));
    }

    public function show(Request $request, $jalali = null)
    {
        // در URL از خط‌تیره استفاده می‌شه، برای parse باید به اسلش تبدیل شه
        $date = $jalali
            ? Jalalian::fromFormat('Y/m/d', str_replace('-', '/', $jalali))->toCarbon()->toDateString()
            : Carbon::today()->toDateString();

        $plan = DailyPlan::with(['items.subject', 'items.topic', 'items.resource'])
            ->whereDate('date', $date)->first();

        $subjects       = Subject::where('is_active', true)->orderBy('order')->get();
        $jalaliDate     = Jalalian::fromCarbon(Carbon::parse($date))->format('Y/m/d');
        $jalaliDateUrl  = str_replace('/', '-', $jalaliDate);
        $todayJalali    = Jalalian::fromCarbon(Carbon::today())->format('Y/m/d');
        $todayJalaliUrl = str_replace('/', '-', $todayJalali);

        return view('plan.show', compact('plan', 'date', 'jalaliDate', 'jalaliDateUrl', 'subjects', 'todayJalali', 'todayJalaliUrl'));
    }

    public function storeOrUpdate(Request $request)
    {
        $request->validate([
            'jalali_date' => 'required|string',
            'goal_hours'  => 'nullable|numeric|min:0|max:24',
            'notes'       => 'nullable|string|max:1000',
        ]);

        $date = Jalalian::fromFormat('Y/m/d', $request->jalali_date)->toCarbon()->toDateString();

        $plan = DailyPlan::updateOrCreate(
            ['date' => $date],
            ['goal_hours' => $request->goal_hours, 'notes' => $request->notes]
        );

        $urlDate = str_replace('/', '-', $request->jalali_date);
        return redirect()->route('plan.show', ['jalali' => $urlDate])
            ->with('success', 'برنامه روزانه ذخیره شد.');
    }

    public function addItem(Request $request)
    {
        $data = $request->validate([
            'daily_plan_id' => 'required|exists:daily_plans,id',
            'subject_id'    => 'required|exists:subjects,id',
            'topic_id'      => 'nullable|exists:topics,id',
            'resource_id'   => 'nullable|exists:resources,id',
            'type'          => 'required|in:study,test',
            'study_type'    => 'nullable|in:book,pamphlet,teaching,video',
            'start_time'    => 'nullable|date_format:H:i',
            'end_time'      => 'nullable|date_format:H:i',
            'notes'         => 'nullable|string|max:500',
        ]);

        $data['order'] = DailyPlanItem::where('daily_plan_id', $data['daily_plan_id'])->max('order') + 1;
        DailyPlanItem::create($data);

        $jalali = Jalalian::fromCarbon(Carbon::parse(DailyPlan::find($data['daily_plan_id'])->date))->format('Y-m-d');
        return redirect()->route('plan.show', ['jalali' => $jalali])->with('success', 'آیتم اضافه شد.');
    }

    public function toggleItem(DailyPlanItem $item)
    {
        $item->update(['is_completed' => !$item->is_completed]);
        return response()->json(['is_completed' => $item->is_completed]);
    }

    public function destroyItem(DailyPlanItem $item)
    {
        $date = $item->dailyPlan->date;
        $item->delete();
        $jalali = Jalalian::fromCarbon(Carbon::parse($date))->format('Y-m-d');
        return redirect()->route('plan.show', ['jalali' => $jalali])->with('success', 'آیتم حذف شد.');
    }
}
