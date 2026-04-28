<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TestSession;
use App\Models\Subject;
use App\Models\Topic;
use Morilog\Jalali\Jalalian;
use Carbon\Carbon;

class TestSessionController extends Controller
{
    public function index(Request $request)
    {
        $query = TestSession::with(['subject', 'topic'])->orderByDesc('date')->orderByDesc('created_at');
        if ($request->subject_id) $query->where('subject_id', $request->subject_id);
        $sessions = $query->paginate(20);
        $subjects = Subject::where('is_active', true)->orderBy('order')->get();
        return view('test.index', compact('sessions', 'subjects'));
    }

    public function create()
    {
        $subjects    = Subject::where('is_active', true)->orderBy('order')->get();
        $todayJalali = Jalalian::fromCarbon(Carbon::today())->format('Y/m/d');
        return view('test.create', compact('subjects', 'todayJalali'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject_id'       => 'required|exists:subjects,id',
            'topic_id'         => 'nullable|exists:topics,id',
            'jalali_date'      => 'required|string',
            'start_time'       => 'nullable|date_format:H:i',
            'end_time'         => 'nullable|date_format:H:i',
            'total_questions'  => 'required|integer|min:1',
            'correct_count'    => 'required|integer|min:0',
            'wrong_count'      => 'required|integer|min:0',
            'unanswered_count' => 'required|integer|min:0',
            'source'           => 'nullable|string|max:200',
            'notes'            => 'nullable|string|max:1000',
        ]);
        $data['date'] = Jalalian::fromFormat('Y/m/d', $data['jalali_date'])->toCarbon()->toDateString();
        unset($data['jalali_date']);
        TestSession::create($data);
        return redirect()->route('test.index')->with('success', 'جلسه تست ثبت شد.');
    }

    public function edit(TestSession $test)
    {
        $subjects   = Subject::where('is_active', true)->orderBy('order')->get();
        $topics     = Topic::where('subject_id', $test->subject_id)->orderBy('order')->get();
        $jalaliDate = Jalalian::fromCarbon(Carbon::parse($test->date))->format('Y/m/d');
        return view('test.edit', compact('test', 'subjects', 'topics', 'jalaliDate'));
    }

    public function update(Request $request, TestSession $test)
    {
        $data = $request->validate([
            'subject_id'       => 'required|exists:subjects,id',
            'topic_id'         => 'nullable|exists:topics,id',
            'jalali_date'      => 'required|string',
            'start_time'       => 'nullable|date_format:H:i',
            'end_time'         => 'nullable|date_format:H:i',
            'total_questions'  => 'required|integer|min:1',
            'correct_count'    => 'required|integer|min:0',
            'wrong_count'      => 'required|integer|min:0',
            'unanswered_count' => 'required|integer|min:0',
            'source'           => 'nullable|string|max:200',
            'notes'            => 'nullable|string|max:1000',
        ]);
        $data['date'] = Jalalian::fromFormat('Y/m/d', $data['jalali_date'])->toCarbon()->toDateString();
        unset($data['jalali_date']);
        $test->update($data);
        return redirect()->route('test.index')->with('success', 'جلسه تست ویرایش شد.');
    }

    public function destroy(TestSession $test)
    {
        $test->delete();
        return back()->with('success', 'جلسه تست حذف شد.');
    }
}
