    @extends('layouts.app')

    @section('title', 'Edit Internship')

    @section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header" style="background-color: #216699; color: white;">
                        <h4 class="mb-0">Edit Internship</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('teacher.internships.update', $internship) }}">
                            @csrf @method('PUT')
                            
                            <div class="mb-3">
                                <label class="form-label">Student</label>
                                <input type="text" class="form-control" value="{{ $internship->student->name }}" disabled>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Subject</label>
                                <input type="text" class="form-control" value="{{ $internship->subject->code }} - {{ $internship->subject->name }}" disabled>
                            </div>
                            
                            <div class="mb-3">
                                <label for="company_name" class="form-label">Company Name</label>
                                <input type="text" class="form-control" name="company_name" 
                                    value="{{ old('company_name', $internship->company_name) }}">
                            </div>
                            
                            <div class="mb-3">
                                <label for="position" class="form-label">Position</label>
                                <input type="text" class="form-control" name="position" 
                                    value="{{ old('position', $internship->position) }}">
                            </div>
                            
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control" name="status" required>
                                    <option value="active" {{ $internship->status == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="pending" {{ $internship->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="completed" {{ $internship->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="dropped" {{ $internship->status == 'dropped' ? 'selected' : '' }}>Dropped</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="remarks" class="form-label">Remarks</label>
                                <textarea class="form-control" name="remarks" rows="3">{{ old('remarks', $internship->remarks) }}</textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Update Internship</button>
                            <a href="{{ route('teacher.internships.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection