{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>User Management</h3>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Add New User</a>
                </div>

                <div class="card-body">
                    <ul class="nav nav-tabs" id="userTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#students">Students</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#teachers">Teachers</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#admins">Admins</a>
                        </li>
                    </ul>

                    <div class="tab-content mt-3">
                        {{-- Students Tab --}}
                        <div class="tab-pane fade show active" id="students">
                            <table class="table table-bordered">
                                <thead>
                                    <tr><th>Student ID</th><th>Name</th><th>Email</th><th>Course</th><th>Year</th><th>Actions</th></tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $student)
                                    <tr>
                                        <td>{{ $student->student_id }}</td>
                                        <td>{{ $student->name }}</td>
                                        <td>{{ $student->email }}</td>
                                        <td>{{ $student->course }}</td>
                                        <td>{{ $student->year_level }}</td>
                                        <td>
                                            <a href="{{ route('admin.users.show', $student) }}" class="btn btn-sm btn-info">View</a>
                                            <a href="{{ route('admin.users.edit', $student) }}" class="btn btn-sm btn-warning">Edit</a>
                                            <form action="{{ route('admin.users.destroy', $student) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $students->links() }}
                        </div>

                        {{-- Teachers Tab --}}
                        <div class="tab-pane fade" id="teachers">
                            <table class="table table-bordered">
                                <thead>
                                    <tr><th>Teacher ID</th><th>Name</th><th>Email</th><th>Department</th><th>Actions</th></tr>
                                </thead>
                                <tbody>
                                    @foreach($teachers as $teacher)
                                    <tr>
                                        <td>{{ $teacher->teacher_id }}</td>
                                        <td>{{ $teacher->name }}</td>
                                        <td>{{ $teacher->email }}</td>
                                        <td>{{ $teacher->department }}</td>
                                        <td>
                                            <a href="{{ route('admin.users.show', $teacher) }}" class="btn btn-sm btn-info">View</a>
                                            <a href="{{ route('admin.users.edit', $teacher) }}" class="btn btn-sm btn-warning">Edit</a>
                                            <form action="{{ route('admin.users.destroy', $teacher) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $teachers->links() }}
                        </div>

                        {{-- Admins Tab --}}
                        <div class="tab-pane fade" id="admins">
                            <table class="table table-bordered">
                                <thead>
                                    <tr><th>Name</th><th>Email</th><th>Department</th><th>Actions</th></tr>
                                </thead>
                                <tbody>
                                    @foreach($admins as $admin)
                                    <tr>
                                        <td>{{ $admin->name }}</td>
                                        <td>{{ $admin->email }}</td>
                                        <td>{{ $admin->department }}</td>
                                        <td>
                                            <a href="{{ route('admin.users.edit', $admin) }}" class="btn btn-sm btn-warning">Edit</a>
                                            @if($admin->id !== auth()->id())
                                            <form action="{{ route('admin.users.destroy', $admin) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">Delete</button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $admins->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection