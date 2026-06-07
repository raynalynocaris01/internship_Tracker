@extends('layouts.app')

@section('title', 'Edit Section')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Edit Section: {{ $section->name }}</h3>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.sections.update', $section) }}">
                        @csrf @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Section Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $section->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="code" class="form-label">Section Code *</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                   id="code" name="code" value="{{ old('code', $section->code) }}" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="course" class="form-label">Course *</label>
                                <select class="form-control @error('course') is-invalid @enderror" 
                                        id="course" name="course" required>
                                    <option value="BSIT" {{ old('course', $section->course) == 'BSIT' ? 'selected' : '' }}>BS Information Technology</option>
                                    <option value="BSCS" {{ old('course', $section->course) == 'BSCS' ? 'selected' : '' }}>BS Computer Science</option>
                                    <option value="BSIS" {{ old('course', $section->course) == 'BSIS' ? 'selected' : '' }}>BS Information Systems</option>
                                    <option value="BSECE" {{ old('course', $section->course) == 'BSECE' ? 'selected' : '' }}>BS Electronics Engineering</option>
                                </select>
                                @error('course')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="year_level" class="form-label">Year Level *</label>
                                <select class="form-control @error('year_level') is-invalid @enderror" 
                                        id="year_level" name="year_level" required>
                                    <option value="1" {{ old('year_level', $section->year_level) == 1 ? 'selected' : '' }}>1st Year</option>
                                    <option value="2" {{ old('year_level', $section->year_level) == 2 ? 'selected' : '' }}>2nd Year</option>
                                    <option value="3" {{ old('year_level', $section->year_level) == 3 ? 'selected' : '' }}>3rd Year</option>
                                    <option value="4" {{ old('year_level', $section->year_level) == 4 ? 'selected' : '' }}>4th Year</option>
                                </select>
                                @error('year_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="max_students" class="form-label">Maximum Students *</label>
                                <input type="number" class="form-control @error('max_students') is-invalid @enderror" 
                                       id="max_students" name="max_students" value="{{ old('max_students', $section->max_students) }}" min="1" max="60" required>
                                @error('max_students')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-control @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="active" {{ old('status', $section->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $section->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.sections.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Section</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection