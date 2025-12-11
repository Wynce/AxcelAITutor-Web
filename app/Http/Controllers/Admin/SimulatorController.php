<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Simulator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SimulatorController extends Controller
{
    public function index(Request $request)
    {
        $query = Simulator::query();

        // Filter by subject
        if ($request->filled('subject')) {
            $query->where('subject', $request->subject);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by title
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $simulators = $query->orderBy('sort_order')->orderBy('created_at', 'desc')->paginate(15);

        $module_url = route('admin.simulators.index');
        $module_name = 'Simulators';
        $pageTitle = 'Simulators';

        return view('Admin.simulators.index', compact('simulators', 'module_url', 'module_name', 'pageTitle'));
    }

    public function create()
    {
        $subjects = Simulator::SUBJECTS;
        $statuses = Simulator::STATUSES;

        $module_url = route('admin.simulators.index');
        $module_name = 'Simulators';
        $pageTitle = 'Add Simulator';

        return view('Admin.simulators.create', compact('subjects', 'statuses', 'module_url', 'module_name', 'pageTitle'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|string|in:' . implode(',', Simulator::SUBJECTS),
            'description' => 'nullable|string',
            'embed_url' => 'required|url',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:' . implode(',', Simulator::STATUSES),
            'sort_order' => 'nullable|integer',
        ]);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('simulators', 'public');
        }

        $validated['created_by'] = auth()->guard('admin')->id();

        Simulator::create($validated);

        return redirect()->route('admin.simulators.index')
            ->with('success', 'Simulator created successfully.');
    }

    public function show(Simulator $simulator)
    {
        $module_url = route('admin.simulators.index');
        $module_name = 'Simulators';
        $pageTitle = 'View Simulator';

        return view('Admin.simulators.show', compact('simulator', 'module_url', 'module_name', 'pageTitle'));
    }

    public function edit(Simulator $simulator)
    {
        $subjects = Simulator::SUBJECTS;
        $statuses = Simulator::STATUSES;

        $module_url = route('admin.simulators.index');
        $module_name = 'Simulators';
        $pageTitle = 'Edit Simulator';

        return view('Admin.simulators.edit', compact('simulator', 'subjects', 'statuses', 'module_url', 'module_name', 'pageTitle'));
    }

    public function update(Request $request, Simulator $simulator)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|string|in:' . implode(',', Simulator::SUBJECTS),
            'description' => 'nullable|string',
            'embed_url' => 'required|url',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:' . implode(',', Simulator::STATUSES),
            'sort_order' => 'nullable|integer',
        ]);

        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($simulator->thumbnail) {
                Storage::disk('public')->delete($simulator->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('simulators', 'public');
        }

        $simulator->update($validated);

        return redirect()->route('admin.simulators.index')
            ->with('success', 'Simulator updated successfully.');
    }

    public function destroy(Simulator $simulator)
    {
        // Delete thumbnail
        if ($simulator->thumbnail) {
            Storage::disk('public')->delete($simulator->thumbnail);
        }

        $simulator->delete();

        return redirect()->route('admin.simulators.index')
            ->with('success', 'Simulator deleted successfully.');
    }
}