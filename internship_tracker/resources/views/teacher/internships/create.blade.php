@extends('layouts.app')

@section('title', 'Assign Internship')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header" style="background-color: #216699; color: white;">
                    <h4 class="mb-0">Assign Internship</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('teacher.internships.store') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="student_id" class="form-label">Student *</label>
                            <select class="form-control @error('student_id') is-invalid @enderror" 
                                    name="student_id" required>
                                <option value="">Select Student</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}">
                                        {{ $student->student_id }} - {{ $student->name }} ({{ $student->course }})
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <!-- ADD SECTION DROPDOWN -->
                        <div class="mb-3">
                            <label for="section_id" class="form-label">Section *</label>
                            <select class="form-control @error('section_id') is-invalid @enderror" 
                                    name="section_id" required>
                                <option value="">Select Section</option>
                                @foreach($sections as $section)
                                    <option value="{{ $section->id }}">
                                        {{ $section->name }} ({{ $section->course }} - Year {{ $section->year_level }})
                                    </option>
                                @endforeach
                            </select>
                            @error('section_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">Select the section for this internship</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="subject_id" class="form-label">Subject *</label>
                            <select class="form-control @error('subject_id') is-invalid @enderror" 
                                    name="subject_id" required>
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">
                                        {{ $subject->code }} - {{ $subject->name }} ({{ $subject->required_hours }} hrs)
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="company_name" class="form-label">Company Name</label>
                            <input type="text" class="form-control" name="company_name" 
                                   placeholder="Leave blank for school-based internship">
                        </div>
                        
                        <div class="mb-3">
                            <label for="position" class="form-label">Position</label>
                            <input type="text" class="form-control" name="position" 
                                   placeholder="e.g., Intern, Assistant, etc.">
                        </div>
                        
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date *</label>
                            <input type="date" class="form-control" name="start_date" 
                                   value="{{ date('Y-m-d') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea class="form-control" name="remarks" rows="3"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Assign Internship</button>
                        <a href="{{ route('teacher.internships.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection