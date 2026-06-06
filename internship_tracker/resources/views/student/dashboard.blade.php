@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Student Dashboard</h3>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card text-white bg-primary">
                                <div class="card-header">Welcome</div>
                                <div class="card-body">
                                    <h5 class="card-title">{{ Auth::user()->name }}</h5>
                                    <p class="card-text">Student ID: {{ Auth::user()->student_id }}</p>
                                    <p class="card-text">Course: {{ Auth::user()->course }} - Year {{ Auth::user()->year_level }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card text-white bg-success">
                                <div class="card-header">Attendance</div>
                                <div class="card-body">
                                    <h5 class="card-title">Today's Attendance</h5>
                                    <p class="card-text">Status: Not yet logged</p>
                                    <button class="btn btn-light">Scan QR Code</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card text-white bg-info">
                                <div class="card-header">Internship Hours</div>
                                <div class="card-body">
                                    <h5 class="card-title">Total Hours</h5>
                                    <p class="card-text">Completed: 0 hours</p>
                                    <p class="card-text">Required: 500 hours</p>
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