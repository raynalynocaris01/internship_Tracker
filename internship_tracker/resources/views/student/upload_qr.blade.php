@extends('layouts.app')

@section('title', 'Upload QR Code')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-upload"></i> Upload QR Code Image</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('student.attendance.upload') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="qr_image" class="form-label">Select QR Code Image</label>
                            <input type="file" class="form-control @error('qr_image') is-invalid @enderror" name="qr_image" accept="image/*" required>
                            @error('qr_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-qrcode"></i> Scan QR from Image
                        </button>
                    </form>
                </div>
                <div class="card-footer text-muted text-center">
                    <small>Take a clear photo of the teacher's QR code and upload it here.</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection