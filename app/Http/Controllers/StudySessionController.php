<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudySession;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Resource;
use Morilog\Jalali\Jalalian;
use Carbon\Carbon;

class StudySessionController extends Controller
{
    public function index(Request $request)
    {
        $query = StudySession::with(['subject', 'topic', 'resource'])->orderByDesc('date')->orderByDesc('start_time');

        if ($request->subject_id) {
            $query->where('subject_id', $request->subject_id);
        }

        $sessions = $query->paginate(20);
        $subjects = Subject::where('is_active', true)->orderBy('order')->get();
        return view('study.index', compact('sessions', 'subjects'));
    }

    public function create()
    {
        $subjects    = Subject::where('is_active', true)->orderBy('order')->get();
        $todayJalali = Jalalian::fromCarbon(Carbon::today())->format('Y/m/d');
        return view('study.create', compact('subjects', 'todayJalali'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject_id'   => 'required|exists:subjects,id',
            'topic_id'     => 'nullable|exists:topics,id',
            'resource_id'  => 'nullable|exists:resources,id',
            'jalali_date'  => 'required|string',
            'start_time'   => 'required|date_format:H:i',
            'end_time'     => 'required|date_format:H:i|after:start_time',
            'type'         => 'required|in:book,pamphlet,teaching,video',
            'repeat_count' => 'required|integer|min:1|max:10',
            'notes'        => 'nullable|string|max:1000',
        ]);

        $data['date'] = Jalalian::fromFormat('Y/m/d', $data['jalali_date'])->toCarbon()->toDateString();
        unset($data['jalali_date']);

        StudySession::create($data);
        return redirect()->route('study.index')->with('success', 'جلسه مطالعه ثبت شد.');
    }

    public function edit(StudySession $study)
    {
        $subjects  = Subject::where('is_active', true)->orderBy('order')->get();
        $topics    = Topic::where('subject_id', $study->subject_id)->orderBy('order')->get();
        $resources = Resource::where('subject_id', $study->subject_id)->get();
        $jalaliDate = Jalalian::fromCarbon(Carbon::parse($study->date))->format('Y/m/d');
        return view('study.edit', compact('study', 'subjects', 'topics', 'resources', 'jalaliDate'));
    }

    public function update(Request $request, StudySession $study)
    {
        $data = $request->validate([
            'subject_id'   => 'required|exists:subjects,id',
            'topic_id'     => 'nullable|exists:topics,id',
            'resource_id'  => 'nullable|exists:resources,id',
            'jalali_date'  => 'required|string',
            'start_time'   => 'required|date_format:H:i',
            'end_time'     => 'required|date_format:H:i|after:start_time',
            'type'         => 'required|in:book,pamphlet,teaching,video',
            'repeat_count' => 'required|integer|min:1|max:10',
            'notes'        => 'nullable|string|max:1000',
        ]);

        $data['date'] = Jalalian::fromFormat('Y/m/d', $data['jalali_date'])->toCarbon()->toDateString();
        unset($data['jalali_date']);

        $study->update($data);
        return redirect()->route('study.index')->with('success', 'جلسه مطالعه ویرایش شد.');
    }

    public function destroy(StudySession $study)
    {
        $study->delete();
        return back()->with('success', 'جلسه مطالعه حذف شد.');
    }

    public function getTopics(Request $request)
    {
        $topics    = Topic::where('subject_id', $request->subject_id)->orderBy('order')->get(['id', 'name']);
        $resources = Resource::where('subject_id', $request->subject_id)->get(['id', 'name', 'type']);
        return response()->json(compact('topics', 'resources'));
    }
}
