@extends('layouts.app')

@section('title', 'Add New Subject')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Add New Subject</h3>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.subjects.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="code" class="form-label">Subject Code *</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                   id="code" name="code" value="{{ old('code') }}" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Subject Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="units" class="form-label">Units *</label>
                                <input type="number" class="form-control @error('units') is-invalid @enderror" 
                                       id="units" name="units" value="{{ old('units', 3) }}" min="1" max="9" required>
                                @error('units')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="required_hours" class="form-label">Required Hours *</label>
                                <input type="number" class="form-control @error('required_hours') is-invalid @enderror" 
                                       id="required_hours" name="required_hours" value="{{ old('required_hours', 500) }}" min="100" max="1000" required>
                                @error('required_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="semester" class="form-label">Semester *</label>
                                <select class="form-control @error('semester') is-invalid @enderror" 
                                        id="semester" name="semester" required>
                                    <option value="1st" {{ old('semester') == '1st' ? 'selected' : '' }}>1st Semester</option>
                                    <option value="2nd" {{ old('semester') == '2nd' ? 'selected' : '' }}>2nd Semester</option>
                                    <option value="Summer" {{ old('semester') == 'Summer' ? 'selected' : '' }}>Summer</option>
                                </select>
                                @error('semester')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="school_year" class="form-label">School Year *</label>
                                <input type="text" class="form-control @error('school_year') is-invalid @enderror" 
                                       id="school_year" name="school_year" value="{{ old('school_year', date('Y')) }}" required>
                                @error('school_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-control @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Subject</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection