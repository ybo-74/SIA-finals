@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-gray-800">Admin Grade Input: {{ $section->name }}</h1>
            <p class="text-muted mb-0">
                {{ $section->subject->code }} - {{ $section->subject->name }} | 
                <span class="badge badge-info">{{ $section->academic_year }}</span>
                <span class="badge badge-success">{{ $section->semester }}</span>
            </p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="alert alert-warning">
        <i class="fas fa-lock"></i> <strong>Admin Only Access:</strong> 
        You can input <strong>Term Exams</strong> and other admin-only grade criteria.
        Click on any cell to edit. Changes are saved automatically.
    </div>

    @if($students->isEmpty())
        <div class="alert alert-info">No students enrolled in this section yet.</div>
    @else
        <!-- Term Tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#prelim">Prelim</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#midterm">Midterm</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#finals">Finals</a>
            </li>
        </ul>

        <div class="tab-content">
            @foreach(['Prelim', 'Midterm', 'Finals'] as $term)
                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ strtolower($term) }}">
                    <!-- Spreadsheet-style Grade Input -->
                    <div class="card shadow mb-4 mt-3">
                        <div class="card-header py-3 bg-warning text-dark">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-table"></i> {{ $term }} Term - Admin Grade Input ({{ $students->count() }} students)
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div style="overflow-x: auto;">
                                <table class="table table-bordered table-hover mb-0 grade-table">
                                    <thead class="thead-warning" style="position: sticky; top: 0; z-index: 10;">
                                        <tr>
                                            <th style="min-width: 50px; position: sticky; left: 0; background: #fff3cd; z-index: 11;" class="text-center">#</th>
                                            <th style="min-width: 200px; position: sticky; left: 50px; background: #fff3cd; z-index: 11;">Student Name</th>
                                            
                                            @php
                                                $lectureCriteria = $adminCriteria->get($term, collect())->get('lecture', collect());
                                                $labCriteria = $adminCriteria->get($term, collect())->get('laboratory', collect());
                                            @endphp
                                            
                                            @if($lectureCriteria->isNotEmpty())
                                                <th colspan="{{ $lectureCriteria->count() }}" class="text-center bg-warning text-dark">
                                                    <i class="fas fa-lock"></i> Lecture - Admin Only
                                                </th>
                                            @endif
                                            
                                            @if($section->subject->has_lab && $labCriteria->isNotEmpty())
                                                <th colspan="{{ $labCriteria->count() }}" class="text-center bg-warning text-dark">
                                                    <i class="fas fa-lock"></i> Laboratory - Admin Only
                                                </th>
                                            @endif
                                            
                                            <th style="min-width: 100px; background: #d1ecf1;" class="text-center">
                                                <strong>Term Grade</strong>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th style="position: sticky; left: 0; background: #fff3cd; z-index: 11;"></th>
                                            <th style="position: sticky; left: 50px; background: #fff3cd; z-index: 11;"></th>
                                            
                                            <!-- Lecture Admin Criteria -->
                                            @foreach($lectureCriteria as $criteria)
                                                <th class="text-center" style="min-width: 120px; font-size: 0.85rem; background: #ffe4b3;">
                                                    <i class="fas fa-lock"></i> {{ $criteria->name }}<br>
                                                    <span class="badge badge-warning editable-max" 
                                                          style="cursor: pointer; font-size: 0.7rem; margin: 2px 0;"
                                                          data-criteria-id="{{ $criteria->id }}"
                                                          data-current-max="{{ $criteria->max_score ?? 100 }}">
                                                        {{ $criteria->max_score ?? 100 }} items
                                                    </span><br>
                                                    <small class="text-muted">({{ $criteria->weight }}%)</small>
                                                </th>
                                            @endforeach
                                            
                                            <!-- Lab Admin Criteria -->
                                            @if($section->subject->has_lab)
                                                @foreach($labCriteria as $criteria)
                                                    <th class="text-center" style="min-width: 120px; font-size: 0.85rem; background: #ffd4d4;">
                                                        <i class="fas fa-lock"></i> {{ $criteria->name }}<br>
                                                        <span class="badge badge-warning editable-max" 
                                                              style="cursor: pointer; font-size: 0.7rem; margin: 2px 0;"
                                                              data-criteria-id="{{ $criteria->id }}"
                                                              data-current-max="{{ $criteria->max_score ?? 100 }}">
                                                            {{ $criteria->max_score ?? 100 }} items
                                                        </span><br>
                                                        <small class="text-muted">({{ $criteria->weight }}%)</small>
                                                    </th>
                                                @endforeach
                                            @endif
                                            
                                            <th style="background: #d1ecf1;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($students as $index => $student)
                                            <tr data-student-id="{{ $student->id }}" data-term="{{ $term }}">
                                                <td class="text-center font-weight-bold" style="position: sticky; left: 0; background: white;">
                                                    {{ $index + 1 }}
                                                </td>
                                                <td style="position: sticky; left: 50px; background: white;">
                                                    <strong>{{ $student->formatted_name }}</strong><br>
                                                    <small class="text-muted">{{ $student->email }}</small>
                                                </td>
                                                
                                                <!-- Lecture Admin Grades -->
                                                @foreach($lectureCriteria as $criteria)
                                                    @php
                                                        $existingGrade = $existingGrades->get($student->id)?->firstWhere('grade_criteria_id', $criteria->id);
                                                    @endphp
                                                    <td class="text-center p-1 grade-cell" 
                                                        data-student-id="{{ $student->id }}"
                                                        data-criteria-id="{{ $criteria->id }}"
                                                        style="cursor: pointer; background: #fff9e6;">
                                                        <input type="number" 
                                                               class="form-control form-control-sm text-center grade-input" 
                                                               value="{{ $existingGrade ? number_format($existingGrade->score, 2, '.', '') : '' }}"
                                                               min="0" 
                                                               max="{{ $criteria->max_score ?? 100 }}" 
                                                               step="0.01"
                                                               data-max="{{ $criteria->max_score ?? 100 }}"
                                                               placeholder="--"
                                                               style="border: none; background: transparent; font-weight: bold; color: #856404;">
                                                    </td>
                                                @endforeach
                                                
                                                <!-- Lab Admin Grades -->
                                                @if($section->subject->has_lab)
                                                    @foreach($labCriteria as $criteria)
                                                        @php
                                                            $existingGrade = $existingGrades->get($student->id)?->firstWhere('grade_criteria_id', $criteria->id);
                                                        @endphp
                                                        <td class="text-center p-1 grade-cell" 
                                                            data-student-id="{{ $student->id }}"
                                                            data-criteria-id="{{ $criteria->id }}"
                                                            style="cursor: pointer; background: #fff0f0;">
                                                            <input type="number" 
                                                                   class="form-control form-control-sm text-center grade-input" 
                                                                   value="{{ $existingGrade ? number_format($existingGrade->score, 2, '.', '') : '' }}"
                                                                   min="0" 
                                                                   max="{{ $criteria->max_score ?? 100 }}" 
                                                                   step="0.01"
                                                                   data-max="{{ $criteria->max_score ?? 100 }}"
                                                                   placeholder="--"
                                                                   style="border: none; background: transparent; font-weight: bold; color: #856404;">
                                                        </td>
                                                    @endforeach
                                                @endif
                                                
                                                <!-- Term Grade (calculated server-side) -->
                                                <td class="text-center font-weight-bold term-grade-cell" style="background: #d1ecf1; font-size: 1.1rem;">
                                                    <span class="term-grade-value">
                                                        {{ number_format($student->{strtolower($term) . '_grade'} ?? 0, 2) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Info Cards -->
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow mb-4 border-warning">
                    <div class="card-header py-3 bg-warning text-dark">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-shield-alt"></i> Admin-Only Grade Criteria
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">You are inputting grades for admin-only criteria.</p>
                        <ul class="mb-0">
                            @foreach(['Prelim', 'Midterm', 'Finals'] as $term)
                                @php
                                    $termCriteria = $adminCriteria->get($term, collect());
                                    $allTermCriteria = $termCriteria->flatten();
                                @endphp
                                @if($allTermCriteria->isNotEmpty())
                                    <li><strong>{{ $term }}:</strong> 
                                        {{ $allTermCriteria->pluck('name')->join(', ') }}
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                        <hr>
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> These grades can only be input by administrators. 
                            Professors can view but cannot edit these fields.
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow mb-4 border-info">
                    <div class="card-header py-3 bg-info text-white">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-calculator"></i> Grade Calculation
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Grade Formula:</strong></p>
                        @if($section->subject->has_lab)
                            <ul class="mb-3">
                                <li>Term Grade = (Lecture × 70%) + (Lab × 30%)</li>
                                <li>Final Grade = (Prelim + Midterm + Finals) ÷ 3</li>
                            </ul>
                        @else
                            <ul class="mb-3">
                                <li>Term Grade = Lecture (100%)</li>
                                <li>Final Grade = (Prelim + Midterm + Finals) ÷ 3</li>
                            </ul>
                        @endif
                        <div class="alert alert-light mb-0">
                            <small class="text-muted">
                                <strong>Note:</strong> The term grade is automatically calculated. 
                                Each category uses weighted averages of its criteria.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Edit Max Items Modal -->
<div class="modal fade" id="editMaxModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Max Items</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="editMaxForm">
                    @csrf
                    <input type="hidden" id="edit_criteria_id">
                    <div class="form-group">
                        <label>Maximum Items <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="edit_max_score" min="1" step="0.01" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-save"></i> Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.editable-max:hover { background: #ec971f !important; transform: scale(1.05); }
.grade-input:focus {
    outline: 2px solid #ffc107;
    background: white !important;
}

.grade-cell:hover {
    background: #fff9e6 !important;
}

.saving {
    background: #fff3cd !important;
}

.saved {
    background: #d4edda !important;
    transition: background 0.3s;
}

.error {
    background: #f8d7da !important;
}

.grade-table {
    font-size: 0.9rem;
}

.grade-table th, .grade-table td {
    border: 1px solid #dee2e6;
}

.thead-warning th {
    background: #fff3cd;
    color: #856404;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const gradeInputs = document.querySelectorAll('.grade-input');
    
    // DON'T calculate on page load - use server-calculated values instead
    // Only recalculate when a grade is actually changed
    
    gradeInputs.forEach(input => {
        let originalValue = input.value;
        
        input.addEventListener('blur', function() {
            if (this.value !== originalValue) {
                saveGrade(this);
            }
        });
        
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (this.value !== originalValue) {
                    saveGrade(this);
                }
                moveToNextRow(this);
            } else if (e.key === 'Escape') {
                this.value = originalValue;
                this.blur();
            }
        });
        
        input.addEventListener('saved', function() {
            originalValue = this.value;
        });
        
        // Real-time validation on input
        input.addEventListener('input', function() {
            const value = parseFloat(this.value);
            if (this.value !== '' && !isNaN(value)) {
                if (value < 0 || value > 100) {
                    this.style.borderColor = '#dc3545';
                    this.style.borderWidth = '2px';
                } else {
                    this.style.borderColor = '';
                    this.style.borderWidth = '';
                }
            } else {
                this.style.borderColor = '';
                this.style.borderWidth = '';
            }
        });
    });
    
    function saveGrade(input) {
        const cell = input.closest('.grade-cell');
        const studentId = cell.dataset.studentId;
        const criteriaId = cell.dataset.criteriaId;
        const score = input.value;
        
        // Validation with popup alerts
        if (score === '') {
            return;
        }
        
        if (score < 0) {
            alert('⚠️ Invalid Grade!\n\nGrade cannot be less than 0.\nPlease enter a value between 0 and 100.');
            input.value = '';
            input.focus();
            return;
        }
        
        if (score > 100) {
            alert('⚠️ Invalid Grade!\n\nGrade cannot be greater than 100.\nPlease enter a value between 0 and 100.');
            input.value = '';
            input.focus();
            return;
        }
        
        cell.classList.add('saving');
        
        fetch('{{ route("grades.admin.store", $section) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                student_id: studentId,
                grade_criteria_id: criteriaId,
                score: score
            })
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            cell.classList.remove('saving');
            cell.classList.add('saved');
            setTimeout(() => cell.classList.remove('saved'), 1000);
            
            // Format display to 2 decimals
            const numericScore = parseFloat(score);
            if (!isNaN(numericScore)) {
                input.value = numericScore.toFixed(2);
            }
            
            input.dispatchEvent(new Event('saved'));
            
            // Update term grade for the student's row in the current tab
            if (data.final_grade !== undefined) {
                const row = input.closest('tr');
                const term = row.dataset.term;
                updateTermGradeForStudentInTerm(studentId, term, data.final_grade);
            }
        })
        .catch(error => {
            cell.classList.remove('saving');
            cell.classList.add('error');
            setTimeout(() => cell.classList.remove('error'), 2000);
            console.error('Error saving grade:', error);
            alert('Failed to save grade. Please try again.');
        });
    }
    
    function moveToNextRow(currentInput) {
        const currentRow = currentInput.closest('tr');
        const currentCell = currentInput.closest('td');
        const cellIndex = Array.from(currentRow.children).indexOf(currentCell);
        
        const nextRow = currentRow.nextElementSibling;
        if (nextRow) {
            const nextCell = nextRow.children[cellIndex];
            if (nextCell) {
                const nextInput = nextCell.querySelector('.grade-input');
                if (nextInput) {
                    nextInput.focus();
                    nextInput.select();
                }
            }
        }
    }
    
    // Update term grade for a specific student in a specific term tab
    function updateTermGradeForStudentInTerm(studentId, term, termGrade) {
        const termTabId = term.toLowerCase();
        const termTab = document.getElementById(termTabId);
        
        if (!termTab) return;
        
        const row = termTab.querySelector(`tr[data-student-id="${studentId}"][data-term="${term}"]`);
        if (!row) return;
        
        const termGradeCell = row.querySelector('.term-grade-cell');
        const termGradeValue = termGradeCell ? termGradeCell.querySelector('.term-grade-value') : null;
        
        if (termGradeValue) {
            termGradeValue.textContent = termGrade > 0 ? termGrade.toFixed(2) : '--';
            
            termGradeCell.style.animation = 'pulse 0.5s';
            setTimeout(() => {
                termGradeCell.style.animation = '';
            }, 500);
        }
    }
    
    // Calculate and display term grades for all students in all terms on page load
    function calculateAllTermGrades() {
        ['Prelim', 'Midterm', 'Finals'].forEach(term => {
            const termTabId = term.toLowerCase();
            const termTab = document.getElementById(termTabId);
            
            if (!termTab) return;
            
            const rows = termTab.querySelectorAll('tr[data-student-id][data-term]');
            
            rows.forEach(row => {
                const termGrade = calculateTermGradeFromRow(row);
                
                const termGradeCell = row.querySelector('.term-grade-cell');
                const termGradeValue = termGradeCell ? termGradeCell.querySelector('.term-grade-value') : null;
                
                if (termGradeValue) {
                    termGradeValue.textContent = termGrade > 0 ? termGrade.toFixed(2) : '--';
                }
            });
        });
    }
    
    // FIXED: Calculate term grade using WEIGHTED AVERAGES from the current row's input values
    function calculateTermGradeFromRow(row) {
        const gradeInputs = row.querySelectorAll('.grade-input');
        
        if (gradeInputs.length === 0) return 0;
        
        let lectureData = [];
        let labData = [];
        
        // Get the table headers to extract weights
        const table = row.closest('table');
        const headerRow = table.querySelector('thead tr:last-child');
        const headerCells = headerRow.querySelectorAll('th');
        
        // Build maps of column index to weight and max_score
        const columnWeights = new Map();
        const columnMaxScores = new Map();
        headerCells.forEach((header, index) => {
            const weightMatch = header.textContent.match(/\((\d+\.?\d*)\%\)/);
            if (weightMatch) {
                columnWeights.set(index, parseFloat(weightMatch[1]));
            }
            
            const maxScoreMatch = header.textContent.match(/(\d+\.?\d*)\s*items/);
            if (maxScoreMatch) {
                columnMaxScores.set(index, parseFloat(maxScoreMatch[1]));
            }
        });
        
        // Get all grades from inputs with their weights and max scores
        gradeInputs.forEach(input => {
            const value = parseFloat(input.value);
            if (!isNaN(value) && value >= 0) {
                const cell = input.closest('td');
                const cellIndex = Array.from(row.children).indexOf(cell);
                const weight = columnWeights.get(cellIndex) || 0;
                const maxScore = columnMaxScores.get(cellIndex) || parseFloat(input.dataset.max) || 100;
                
                const cellBg = cell.style.background;
                const isLab = cellBg.includes('fff0f0') || cellBg.includes('ffd4d4');
                
                if (isLab) {
                    labData.push({ score: value, weight: weight, maxScore: maxScore });
                } else {
                    lectureData.push({ score: value, weight: weight, maxScore: maxScore });
                }
            }
        });
        
        // Calculate weighted averages
        function calculateWeightedAverage(data) {
            if (data.length === 0) return 0;
            
            let totalWeight = 0;
            let weightedSum = 0;
            
            data.forEach(item => {
                // Convert raw score to percentage first
                const percentage = (item.score / item.maxScore) * 100;
                
                // Then apply weight
                const weightedScore = percentage * (item.weight / 100);
                
                weightedSum += weightedScore;
                totalWeight += item.weight;
            });
            
            return totalWeight > 0 ? weightedSum : 0;
        }
        
        const lectureAvg = calculateWeightedAverage(lectureData);
        const labAvg = calculateWeightedAverage(labData);
        
        const hasLab = labData.length > 0;
        
        if (hasLab) {
            if (lectureAvg > 0 && labAvg > 0) {
                return (lectureAvg * 0.7) + (labAvg * 0.3);
            } else if (lectureAvg > 0) {
                return lectureAvg * 0.7;
            } else if (labAvg > 0) {
                return labAvg * 0.3;
            }
        } else {
            return lectureAvg;
        }
        
        return 0;
    }
});

const style = document.createElement('style');
style.textContent = `
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); background: #ffc107; }
        100% { transform: scale(1); }
    }
`;
document.head.appendChild(style);

// Edit max items
document.querySelectorAll('.editable-max').forEach(el => {
    el.onclick = function() {
        document.getElementById('edit_criteria_id').value = this.dataset.criteriaId;
        document.getElementById('edit_max_score').value = this.dataset.currentMax;
        $('#editMaxModal').modal('show');
    };
});

document.getElementById('editMaxForm').onsubmit = function(e) {
    e.preventDefault();
    const id = document.getElementById('edit_criteria_id').value;
    const max = document.getElementById('edit_max_score').value;
    
    // Save current active tab before reload
    const activeTab = document.querySelector('.nav-link.active[data-toggle="tab"]');
    if (activeTab) {
        const term = activeTab.getAttribute('href').replace('#', '');
        localStorage.setItem('activeGradeTerm', term);
    }
    
    fetch(`/criteria/${id}/update-max`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({max_score: max})
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error(text || 'Failed to update');
            });
        }
        return response.json();
    })
    .then(data => { 
        alert('Updated!'); 
        $('#editMaxModal').modal('hide');
        location.reload(); 
    })
    .catch(error => { 
        console.error('Error:', error);
        alert('Error: ' + error.message);
        localStorage.removeItem('activeGradeTerm');
    });
};

// Restore active tab after page reload
document.addEventListener('DOMContentLoaded', function() {
    const savedTerm = localStorage.getItem('activeGradeTerm');
    if (savedTerm) {
        const tabLink = document.querySelector(`a[href="#${savedTerm}"]`);
        if (tabLink) {
            $(tabLink).tab('show');
        }
        localStorage.removeItem('activeGradeTerm');
    }
});
</script>
@endsection
