<?php

namespace App\Http\Controllers;

use App\Models\Officer;
use App\Models\Requisition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OfficerController extends Controller
{
    /**
     * Administration screen — super users only, same gate as User Management.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check() || Auth::user()->is_super != 1) {
                abort(403, 'Unauthorized');
            }

            return $next($request);
        });
    }

    public function index()
    {
        $officers = Officer::withCount('packages')
            ->orderByDesc('created_at')   // newest first
            ->orderByDesc('id')
            ->get();

        return view('officers.index', compact('officers'));
    }

    public function create()
    {
        return view('officers.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        Officer::create($data);

        return redirect()->route('officers.index')
            ->with('success', "Officer \"{$data['name']}\" created successfully.");
    }

    public function edit(Officer $officer)
    {
        return view('officers.edit', compact('officer'));
    }

    public function update(Request $request, Officer $officer)
    {
        $data = $this->validated($request, $officer);

        // Requisitions store the officer as free text, so a rename has to follow
        // through or their existing requisitions lose the link.
        $oldName = $officer->name;
        $officer->update($data);

        if ($oldName !== $officer->name) {
            Requisition::where('officer_name', $oldName)->update(['officer_name' => $officer->name]);
        }

        return redirect()->route('officers.index')
            ->with('success', "Officer \"{$officer->name}\" updated successfully.");
    }

    public function destroy(Officer $officer)
    {
        $packages     = $officer->packages()->count();
        $requisitions = Requisition::where('officer_name', $officer->name)->count();

        // Deleting would blank the officer on existing records; deactivating keeps
        // the history intact and is what the status field is for.
        if ($packages || $requisitions) {
            return redirect()->route('officers.index')->with('error', sprintf(
                'Cannot delete "%s" — still assigned to %d package(s) and %d requisition(s). Set the status to Inactive instead.',
                $officer->name,
                $packages,
                $requisitions
            ));
        }

        $name = $officer->name;
        $officer->delete();

        return redirect()->route('officers.index')
            ->with('success', "Officer \"{$name}\" deleted successfully.");
    }

    private function validated(Request $request, ?Officer $officer = null): array
    {
        $data = $request->validate([
            'name'      => [
                'required', 'string', 'max:255',
                Rule::unique('officers', 'name')->ignore($officer?->id),
            ],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Officer name is required.',
            'name.unique'   => 'That officer name already exists.',
        ]);

        $data['name']      = trim($data['name']);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
