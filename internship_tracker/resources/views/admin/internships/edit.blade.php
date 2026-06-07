@extends('layouts.app')

@section('title', 'Edit Internship')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header" style="background-color: #216699; color: white;">
                    <h4 class="mb-0">
                        <i class="fas fa-edit"></i> Edit Internship
                    </h4>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.internships.update', $internship) }}">
                        @csrf @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Student</label>
                            <input type="text" class="form-control" value="{{ $internship->student->name }} ({{ $internship->student->student_id }})" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input type="text" class="form-control" value="{{ $internship->subject->code }} - {{ $internship->subject->name }}" disabled>
                        </div>

                        <div class="mb-3">
                            <label for="section_id" class="form-label">Section</label>
                            <select class="form-control @error('section_id') is-invalid @enderror" 
                                    id="section_id" name="section_id">
                                <option value="">Select Section</option>
                                @foreach($sections as $section)
                                    <option value="{{ $section->id }}" {{ old('section_id', $internship->section_id) == $section->id ? 'selected' : '' }}>
                                        {{ $section->name }} ({{ $section->course }} - Year {{ $section->year_level }})
                                    </option>
                                @endforeach
                            </select>
                            @error('section_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="teacher_id" class="form-label">Supervising Teacher</label>
                            <select class="form-control @error('teacher_id') is-invalid @enderror" 
                                    id="teacher_id" name="teacher_id">
                                <option value="">Select Teacher</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('teacher_id', $internship->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->name }} ({{ $teacher->teacher_id }})
                                    </option>
                                @endforeach
                            </select>
                            @error('teacher_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="company_name" class="form-label">Company Name</label>
                            <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                   id="company_name" name="company_name" value="{{ old('company_name', $internship->company_name) }}" 
                                   placeholder="e.g., Tech Solutions Inc.">
                            @error('company_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="position" class="form-label">Position</label>
                            <input type="text" class="form-control @error('position') is-invalid @enderror" 
                                   id="position" name="position" value="{{ old('position', $internship->position) }}" 
                                   placeholder="e.g., Web Developer Intern">
                            @error('position')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" name="start_date" value="{{ old('start_date', $internship->start_date ? $internship->start_date->format('Y-m-d') : date('Y-m-d')) }}">
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="active" {{ old('status', $internship->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="pending" {{ old('status', $internship->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ old('status', $internship->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="dropped" {{ old('status', $internship->status) == 'dropped' ? 'selected' : '' }}>Dropped</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea class="form-control @error('remarks') is-invalid @enderror" 
                                      id="remarks" name="remarks" rows="3">{{ old('remarks', $internship->remarks) }}</textarea>
                            @error('remarks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Internship Progress:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Total Hours: {{ number_format($internship->total_hours_rendered, 1) }} / {{ $internship->subject->required_hours }} hours</li>
                                <li>Progress: {{ $internship->progress }}%</li>
                                @if($internship->status == 'completed')
                                    <li>Completed on: {{ $internship->completion_date ? $internship->completion_date->format('F d, Y') : 'N/A' }}</li>
                                @endif
                            </ul>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.internships.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Internship
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection