{{-- resources/views/teacher/dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Teacher Dashboard</h3>
                    <p>Welcome back, {{ Auth::user()->name }}!</p>
                    <p><strong>Teacher ID:</strong> {{ Auth::user()->teacher_id }}</p>
                    <p><strong>Department:</strong> {{ Auth::user()->department }}</p>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card text-white bg-primary mb-3">
                                <div class="card-header">My Students</div>
                                <div class="card-body">
                                    <h5 class="card-title">45</h5>
                                    <p class="card-text">Students under supervision</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-white bg-success mb-3">
                                <div class="card-header">Active Internships</div>
                                <div class="card-body">
                                    <h5 class="card-title">12</h5>
                                    <p class="card-text">Current internship programs</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-white bg-info mb-3">
                                <div class="card-header">Pending Reports</div>
                                <div class="card-body">
                                    <h5 class="card-title">8</h5>
                                    <p class="card-text">Reports awaiting review</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection