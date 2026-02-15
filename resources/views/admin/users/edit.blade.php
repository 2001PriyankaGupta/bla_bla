@extends('admin.layouts.master')
@section('title')
    Edit User - {{ $user->name }}
@endsection

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    :root {
        --primary-color: #249722;
        --primary-light: #e8f5e9;
        --secondary-color: #6c757d;
        --light-bg: #f8f9fa;
        --border-color: #e9ecef;
        --card-shadow: 0 4px 12px rgba(25, 151, 34, 0.08);
    }

    .user-edit-container {
        background-color: #f5f6f8;
        min-height: 100vh;
        padding: 20px;
    }

    .page-header {
        background: white;
        border-radius: 16px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: var(--card-shadow);
        border: 1px solid rgba(25, 151, 34, 0.1);
    }

    .edit-form-card {
        background: white;
        border-radius: 16px;
        padding: 30px;
        box-shadow: var(--card-shadow);
        border: 1px solid rgba(25, 151, 34, 0.1);
    }

    .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 8px;
    }

    .form-control,
    .form-select {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 12px 15px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(25, 151, 34, 0.15);
    }

    .avatar-upload {
        text-align: center;
        padding: 20px;
        border: 2px dashed var(--border-color);
        border-radius: 16px;
        background: var(--light-bg);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .avatar-upload:hover {
        border-color: var(--primary-color);
        background: var(--primary-light);
    }

    .avatar-preview {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid var(--primary-light);
        margin-bottom: 15px;
    }

    .upload-text {
        color: var(--secondary-color);
        margin-bottom: 10px;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color), #34c759);
        border: none;
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #1e7a1d, #249722);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(25, 151, 34, 0.4);
    }

    .btn-outline-secondary {
        color: var(--secondary-color);
        border-color: var(--secondary-color);
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 600;
    }

    .btn-outline-secondary:hover {
        background: var(--secondary-color);
        color: white;
    }

    @media (max-width: 768px) {
        .avatar-preview {
            width: 80px;
            height: 80px;
        }
    }
</style>


@section('content')
    <div class="user-edit-container">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="h3 mb-0">Edit User</h1>
                    <p class="mb-0 text-muted">Update information for {{ $user->name }}</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Details
                    </a>
                </div>
            </div>
        </div>

        <div class="row ">
            <div class="col-lg-12">
                <div class="edit-form-card">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data"
                        id="userEditForm">
                        @csrf
                        @method('PUT')

                        <!-- Profile Picture -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Profile Picture</label>
                                <div class="avatar-upload" onclick="document.getElementById('profile_picture').click()">
                                    <img id="avatarPreview"
                                        src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=249722&color=fff&size=128&bold=true' }}"
                                        alt="Avatar Preview" class="avatar-preview">
                                    <div class="upload-text">
                                        <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                        <p>Click to upload</p>
                                    </div>
                                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*"
                                        style="display: none;" onchange="previewImage(this)">
                                </div>
                            </div>
                        </div>

                        <!-- Personal Information -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                    id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select @error('gender') is-invalid @enderror" id="gender"
                                    name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>
                                        Male</option>
                                    <option value="female"
                                        {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>
                                        Other</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="locality" class="form-label">Locality</label>
                                <input type="text" class="form-control @error('locality') is-invalid @enderror"
                                    id="locality" name="locality" value="{{ old('locality', $user->locality) }}">
                                @error('locality')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="user_type" class="form-label">User Type *</label>
                                <select class="form-select @error('user_type') is-invalid @enderror" id="user_type"
                                    name="user_type" required>
                                    <option value="passenger"
                                        {{ old('user_type', $user->user_type) == 'passenger' ? 'selected' : '' }}>Passenger
                                    </option>
                                    <option value="driver"
                                        {{ old('user_type', $user->user_type) == 'driver' ? 'selected' : '' }}>Driver
                                    </option>
                                </select>
                                @error('user_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>



                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary me-3">
                                    <i class="fas fa-save me-2"></i> Update User
                                </button>
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function previewImage(input) {
            const preview = document.getElementById('avatarPreview');
            const file = input.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                }

                reader.readAsDataURL(file);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Display success message from session if exists
            @if (session('success'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: '{{ session('error') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif
        });
    </script>
@endsection
