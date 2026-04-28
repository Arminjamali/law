<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Resource;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::withCount(['studySessions', 'testSessions', 'topics'])
            ->orderBy('order')->get();
        return view('subject.index', compact('subjects'));
    }

    public function show(Subject $subject)
    {
        $subject->load(['topics', 'resources', 'studySessions', 'testSessions']);
        return view('subject.show', compact('subject'));
    }

    public function storeTopic(Request $request, Subject $subject)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:200',
            'difficulty' => 'required|integer|min:1|max:5',
            'notes'      => 'nullable|string|max:500',
        ]);
        $data['subject_id'] = $subject->id;
        $data['order']      = Topic::where('subject_id', $subject->id)->max('order') + 1;
        Topic::create($data);
        return back()->with('success', 'مبحث اضافه شد.');
    }

    public function storeResource(Request $request, Subject $subject)
    {
        $data = $request->validate([
            'name'   => 'required|string|max:200',
            'type'   => 'required|in:book,pamphlet,video,other',
            'author' => 'nullable|string|max:200',
            'notes'  => 'nullable|string|max:500',
        ]);
        $data['subject_id'] = $subject->id;
        Resource::create($data);
        return back()->with('success', 'منبع اضافه شد.');
    }

    public function destroyTopic(Topic $topic)
    {
        $subject = $topic->subject;
        $topic->delete();
        return redirect()->route('subjects.show', $subject)->with('success', 'مبحث حذف شد.');
    }

    public function destroyResource(Resource $resource)
    {
        $subject = $resource->subject;
        $resource->delete();
        return redirect()->route('subjects.show', $subject)->with('success', 'منبع حذف شد.');
    }
}
