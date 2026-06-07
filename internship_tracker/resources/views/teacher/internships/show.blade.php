@extends('layouts.app')

@section('title', 'Internship Details')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header" style="background-color: #216699; color: white;">
            <h4 class="mb-0">
                <i class="fas fa-briefcase"></i> Internship Details
            </h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Student</th>
                            <td>{{ $internship->student->name }}</p>
                        </tr>
                        <tr>
                            <th>Student ID</th>
                            <td>{{ $internship->student->student_id }}</p>
                        </tr>
                        <tr>
                            <th>Subject</th>
                            <td>{{ $internship->subject->code }} - {{ $internship->subject->name }}</p>
                        </tr>
                        <tr>
                            <th>Required Hours</th>
                            <td>{{ number_format($internship->subject->required_hours) }} hours</p>
                        </tr>
                        <tr>
                            <th>Company</th>
                            <td>{{ $internship->company_name ?? 'School-based' }}</p>
                        </tr>
                     sustainably
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th>Start Date</th>
                            <td>{{ $internship->start_date->format('F d, Y') }}</p>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge bg-{{ $internship->status_badge }}">
                                    {{ $internship->status_label }}
                                </span>
                            </p>
                        </tr>
                        <tr>
                            <th>Total Hours</th>
                            <td>{{ number_format($totalHours, 1) }} / {{ number_format($internship->subject->required_hours) }} hours</p>
                        </tr>
                        <tr>
                            <th>Progress</th>
                            <td>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar bg-success" style="width: {{ $progress }}%">
                                        {{ number_format($progress, 1) }}%
                                    </div>
                                </div>
                            </p>
                        </tr>
                        <tr>
                            <th>Remaining Hours</th>
                            <td>{{ number_format($internship->subject->required_hours - $totalHours, 1) }} hours</p>
                        </tr>
                    </div>
                </div>
            </div>

            @if($internship->remarks)
                <div class="alert alert-info mt-3">
                    <strong>Remarks:</strong> {{ $internship->remarks }}
                </div>
            @endif

            <div class="mt-4">
                <a href="{{ route('teacher.internships.index') }}" class="btn btn-secondary">Back</a>
                <a href="{{ route('teacher.internships.edit', $internship) }}" class="btn btn-warning">Edit</a>
            </div>
        </div>
    </div>
</div>
@endsection