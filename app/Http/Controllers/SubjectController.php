<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\SubjectGradeCriteria;
use App\Models\User;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::with('professor')->get();
        return view('subjects.index', compact('subjects'));
    }

    public function create()
    {
        // Fetch professors via Spatie role
        $professors = User::role('professor')->get();
        return view('subjects.create', compact('professors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'code'         => 'required|string|max:50|unique:subjects,code',
            'professor_id' => 'nullable|exists:users,id',
            'has_lab'      => 'required|boolean',
            'criteria'     => 'required|array',
            'criteria.*.*.*' => 'required|array',
        ]);

        DB::beginTransaction();
        
        try {
            // Create subject
            $subject = Subject::create([
                'name' => $request->name,
                'code' => $request->code,
                'professor_id' => $request->professor_id,
                'has_lab' => $request->has_lab,
            ]);

            // Save criteria
            $order = 0;
            foreach ($request->criteria as $term => $categories) {
                foreach ($categories as $category => $criteriaList) {
                    foreach ($criteriaList as $criteriaData) {
                        SubjectGradeCriteria::create([
                            'subject_id' => $subject->id,
                            'term' => ucfirst($term),
                            'category' => $category,
                            'name' => $criteriaData['name'],
                            'weight' => $criteriaData['weight'],
                            'admin_only' => $criteriaData['admin_only'] ?? false,
                            'order' => $order++,
                        ]);
                    }
                }
            }

            // Audit Trail
            AuditTrail::log(
                action: 'create',
                modelType: 'Subject',
                modelId: $subject->id,
                description: sprintf(
                    'Created subject "%s" (%s) - %s with %d grading criteria',
                    $subject->name,
                    $subject->code,
                    $subject->has_lab ? 'Lecture + Laboratory' : 'Lecture Only',
                    $subject->gradeCriteria()->count()
                ),
                oldValues: null,
                newValues: [
                    'subject' => $subject->toArray(),
                    'criteria_count' => $subject->gradeCriteria()->count()
                ]
            );

            DB::commit();

            return redirect()->route('subjects.index')
                ->with('success', 'Subject created successfully with grading criteria!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Failed to create subject: ' . $e->getMessage());
        }
    }

    public function edit(Subject $subject)
    {
        $professors = User::role('professor')->get();
        
        // Load existing criteria grouped by term and category
        $existingCriteria = $subject->gradeCriteria()
            ->orderBy('term')
            ->orderBy('category')
            ->orderBy('order')
            ->get()
            ->groupBy(function($item) {
                return strtolower($item->term);
            })
            ->map(function($termGroup) {
                return $termGroup->groupBy('category');
            });
        
        return view('subjects.edit', compact('subject', 'professors', 'existingCriteria'));
    }

    public function update(Request $request, Subject $subject)
    {
        $oldValues = [
            'subject' => $subject->toArray(),
            'criteria' => $subject->gradeCriteria->toArray()
        ];

        $request->validate([
            'name'         => 'required|string|max:255',
            'code'         => 'required|string|max:50|unique:subjects,code,' . $subject->id,
            'professor_id' => 'nullable|exists:users,id',
            'has_lab'      => 'required|boolean',
            'criteria'     => 'required|array',
            'criteria.*.*.*' => 'required|array',
        ]);

        DB::beginTransaction();
        
        try {
            // Update subject
            $subject->update([
                'name' => $request->name,
                'code' => $request->code,
                'professor_id' => $request->professor_id,
                'has_lab' => $request->has_lab,
            ]);

            // Delete old criteria
            $subject->gradeCriteria()->delete();

            // Save new criteria
            $order = 0;
            foreach ($request->criteria as $term => $categories) {
                foreach ($categories as $category => $criteriaList) {
                    foreach ($criteriaList as $criteriaData) {
                        SubjectGradeCriteria::create([
                            'subject_id' => $subject->id,
                            'term' => ucfirst($term),
                            'category' => $category,
                            'name' => $criteriaData['name'],
                            'weight' => $criteriaData['weight'],
                            'admin_only' => $criteriaData['admin_only'] ?? false,
                            'order' => $order++,
                        ]);
                    }
                }
            }

            // Audit Trail
            AuditTrail::log(
                action: 'update',
                modelType: 'Subject',
                modelId: $subject->id,
                description: sprintf(
                    'Updated subject "%s" (%s) and its grading criteria',
                    $subject->name,
                    $subject->code
                ),
                oldValues: $oldValues,
                newValues: [
                    'subject' => $subject->toArray(),
                    'criteria_count' => $subject->gradeCriteria()->count()
                ]
            );

            DB::commit();

            return redirect()->route('subjects.index')
                ->with('success', 'Subject updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Failed to update subject: ' . $e->getMessage());
        }
    }

    public function destroy(Subject $subject)
    {
        $subjectData = [
            'subject' => $subject->toArray(),
            'criteria_count' => $subject->gradeCriteria()->count()
        ];

        // Check if subject has sections with grades
        if ($subject->sections()->count() > 0) {
            return back()->with('error', 
                'Cannot delete subject with existing sections. Please delete sections first.');
        }

        DB::beginTransaction();
        
        try {
            // Criteria will be deleted automatically due to cascade
            $subject->delete();

            // Audit Trail
            AuditTrail::log(
                action: 'delete',
                modelType: 'Subject',
                modelId: $subject->id,
                description: sprintf(
                    'Deleted subject "%s" (%s)',
                    $subject->name,
                    $subject->code
                ),
                oldValues: $subjectData,
                newValues: null
            );

            DB::commit();

            return redirect()->route('subjects.index')
                ->with('success', 'Subject deleted successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Failed to delete subject: ' . $e->getMessage());
        }
    }
}
