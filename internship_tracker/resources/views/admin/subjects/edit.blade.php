@extends('layouts.app')

@section('title', 'Edit Subject')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header" style="background-color: #216699; color: white;">
                    <h4 class="mb-0">
                        <i class="fas fa-edit"></i> Edit Subject: {{ $subject->code }}
                    </h4>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.subjects.update', $subject) }}">
                        @csrf @method('PUT')

                        <!-- Basic Information -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <strong><i class="fas fa-info-circle"></i> Basic Information</strong>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="code" class="form-label">Subject Code <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                               id="code" name="code" value="{{ old('code', $subject->code) }}" required>
                                        @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Subject Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $subject->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="2">{{ old('description', $subject->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Academic Information -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <strong><i class="fas fa-graduation-cap"></i> Academic Information</strong>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="units" class="form-label">Units <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('units') is-invalid @enderror" 
                                               id="units" name="units" value="{{ old('units', $subject->units) }}" min="1" max="9" required>
                                        @error('units')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="required_hours" class="form-label">Required Hours <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('required_hours') is-invalid @enderror" 
                                               id="required_hours" name="required_hours" value="{{ old('required_hours', $subject->required_hours) }}" min="100" max="1000" required>
                                        @error('required_hours')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="semester" class="form-label">Semester <span class="text-danger">*</span></label>
                                        <select class="form-control @error('semester') is-invalid @enderror" 
                                                id="semester" name="semester" required>
                                            <option value="1st" {{ old('semester', $subject->semester) == '1st' ? 'selected' : '' }}>1st Semester</option>
                                            <option value="2nd" {{ old('semester', $subject->semester) == '2nd' ? 'selected' : '' }}>2nd Semester</option>
                                            <option value="Summer" {{ old('semester', $subject->semester) == 'Summer' ? 'selected' : '' }}>Summer</option>
                                        </select>
                                        @error('semester')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="school_year" class="form-label">School Year <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('school_year') is-invalid @enderror" 
                                               id="school_year" name="school_year" value="{{ old('school_year', $subject->school_year) }}" required>
                                        @error('school_year')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Teacher & Section Assignment -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <strong><i class="fas fa-chalkboard-user"></i> Teacher & Section Assignment</strong>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> 
                                    Assign teachers to teach this subject to specific sections.
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered" id="assignmentsTable">
                                        <thead>
                                            <tr>
                                                <th width="35%">Section</th>
                                                <th width="35%">Teacher</th>
                                                <th width="15%">Status</th>
                                                <th width="15%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="assignmentsBody">
                                            @if(isset($existingAssignments) && count($existingAssignments) > 0)
                                                @foreach($existingAssignments as $index => $assignment)
                                                <tr class="assignment-row">
                                                    <td>
                                                        <select name="assignments[{{ $index }}][section_id]" class="form-control" required>
                                                            <option value="">Select Section</option>
                                                            @foreach($sections as $section)
                                                                <option value="{{ $section->id }}" {{ $assignment['section_id'] == $section->id ? 'selected' : '' }}>
                                                                    {{ $section->name }} ({{ $section->course }} - Year {{ $section->year_level }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="assignments[{{ $index }}][teacher_id]" class="form-control" required>
                                                            <option value="">Select Teacher</option>
                                                            @foreach($teachers as $teacher)
                                                                <option value="{{ $teacher->id }}" {{ $assignment['teacher_id'] == $teacher->id ? 'selected' : '' }}>
                                                                    {{ $teacher->name }} ({{ $teacher->teacher_id }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="assignments[{{ $index }}][status]" class="form-control">
                                                            <option value="active" {{ ($assignment['status'] ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                                            <option value="inactive" {{ ($assignment['status'] ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                        </select>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-danger btn-sm remove-row">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                                <button type="button" class="btn btn-sm btn-success mt-2" id="addAssignmentRow">
                                    <i class="fas fa-plus"></i> Add Teacher Assignment
                                </button>
                                <small class="text-muted d-block mt-2">Note: A subject can be taught by different teachers to different sections.</small>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <strong><i class="fas fa-toggle-on"></i> Subject Status</strong>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Subject Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" name="status" required>
                                        <option value="active" {{ old('status', $subject->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $subject->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Subject
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Get the current max index from existing rows
    let maxIndex = 0;
    const rows = document.querySelectorAll('#assignmentsBody .assignment-row');
    rows.forEach(function(row) {
        const select = row.querySelector('select[name^="assignments["]');
        if (select) {
            const name = select.getAttribute('name');
            const match = name.match(/assignments\[(\d+)\]/);
            if (match && parseInt(match[1]) > maxIndex) {
                maxIndex = parseInt(match[1]);
            }
        }
    });
    
    let rowCounter = maxIndex + 1;

    // Add new assignment row
    document.getElementById('addAssignmentRow').addEventListener('click', function() {
        // Build sections options
        let sectionsOptions = '<option value="">Select Section</option>';
        @foreach($sections as $section)
            sectionsOptions += '<option value="{{ $section->id }}">{{ $section->name }} ({{ $section->course }} - Year {{ $section->year_level }})</option>';
        @endforeach
        
        // Build teachers options
        let teachersOptions = '<option value="">Select Teacher</option>';
        @foreach($teachers as $teacher)
            teachersOptions += '<option value="{{ $teacher->id }}">{{ $teacher->name }} ({{ $teacher->teacher_id }})</option>';
        @endforeach
        
        const newRow = document.createElement('tr');
        newRow.className = 'assignment-row';
        newRow.innerHTML = `
            <td>
                <select name="assignments[${rowCounter}][section_id]" class="form-control" required>
                    ${sectionsOptions}
                </select>
            </td>
            <td>
                <select name="assignments[${rowCounter}][teacher_id]" class="form-control" required>
                    ${teachersOptions}
                </select>
            </td>
            <td>
                <select name="assignments[${rowCounter}][status]" class="form-control">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm remove-row">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        
        document.getElementById('assignmentsBody').appendChild(newRow);
        rowCounter++;
        
        // Add remove event listener to the new button
        newRow.querySelector('.remove-row').addEventListener('click', function() {
            if (document.querySelectorAll('#assignmentsBody tr').length > 1) {
                newRow.remove();
            } else {
                alert('At least one assignment is required. Add more before removing.');
            }
        });
    });

    // Add remove event listeners to existing remove buttons
    document.querySelectorAll('.remove-row').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            if (document.querySelectorAll('#assignmentsBody tr').length > 1) {
                row.remove();
            } else {
                alert('At least one assignment is required. Add more before removing.');
            }
        });
    });
</script>
@endpush
@endsection