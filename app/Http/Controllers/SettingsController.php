<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppSetting;
use Morilog\Jalali\Jalalian;
use Carbon\Carbon;

class SettingsController extends Controller
{
    public function index()
    {
        $examDate    = AppSetting::get('exam_date');
        $goalHours   = AppSetting::get('daily_goal_hours', 6);
        $examJalali  = $examDate ? Jalalian::fromCarbon(Carbon::parse($examDate))->format('Y/m/d') : null;
        return view('settings.index', compact('examDate', 'goalHours', 'examJalali'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'exam_jalali_date' => 'nullable|string',
            'daily_goal_hours' => 'required|numeric|min:1|max:24',
        ]);

        if ($request->exam_jalali_date) {
            $examDate = Jalalian::fromFormat('Y/m/d', $request->exam_jalali_date)->toCarbon()->toDateString();
            AppSetting::set('exam_date', $examDate);
        }
        AppSetting::set('daily_goal_hours', $request->daily_goal_hours);

        return back()->with('success', 'تنظیمات ذخیره شد.');
    }
}
