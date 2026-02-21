@extends('admin.layouts.master')
@section('title')
    User Details - {{ $user->name }}
@endsection


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .swal2-toast {
        font-size: 12px !important;
        padding: 6px 10px !important;
        min-width: auto !important;
        width: 220px !important;
        line-height: 1.3em !important;
    }

    .swal2-toast .swal2-icon {
        width: 24px !important;
        height: 24px !important;
        margin-right: 6px !important;
    }

    .swal2-toast .swal2-title {
        font-size: 13px !important;
    }
</style>
<style>
    :root {
        --primary-color: #249722;
        --primary-light: #e8f5e9;
        --secondary-color: #6c757d;
        --light-bg: #f8f9fa;
        --border-color: #e9ecef;
        --card-shadow: 0 4px 12px rgba(25, 151, 34, 0.08);
    }

    .user-details-container {
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

    .user-profile-card {
        background: white;
        border-radius: 16px;
        padding: 30px;
        margin-bottom: 25px;
        box-shadow: var(--card-shadow);
        border: 1px solid rgba(25, 151, 34, 0.1);
    }

    .user-avatar-large {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid var(--primary-light);
        margin-bottom: 20px;
    }

    .info-card {
        background: white;
        border-radius: 16px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: var(--card-shadow);
        border: 1px solid rgba(25, 151, 34, 0.1);
    }

    .info-card-header {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--primary-light);
    }

    .info-card-header i {
        color: var(--primary-color);
        font-size: 1.5rem;
        margin-right: 12px;
    }

    .info-card-header h5 {
        margin: 0;
        color: #2c3e50;
        font-weight: 700;
    }

    .info-item {
        display: flex;
        justify-content: between;
        
        padding: 12px 0;
        border-bottom: 1px solid #f1f3f4;
    }

    .info-label {
        font-weight: 600;
        color: #2c3e50;
        min-width: 150px;
    }

    .info-value {
        color: #6c757d;
        flex: 1;
    }

    .status-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-active {
        background: linear-gradient(135deg, #27ae60, #2ecc71);
        color: white;
        box-shadow: 0 2px 8px rgba(39, 174, 96, 0.3);
    }

    .status-inactive {
        background: linear-gradient(135deg, #e74c3c, #ff6b6b);
        color: white;
        box-shadow: 0 2px 8px rgba(231, 76, 60, 0.3);
    }

   

    .stats-card {
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        border-radius: 16px;
        padding: 25px;
        text-align: center;
        box-shadow: var(--card-shadow);
        margin-bottom: 20px;
        border: 1px solid rgba(25, 151, 34, 0.1);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), #34c759);
    }

    .stats-number {
        font-size: 2rem;
        font-weight: 800;
        color: var(--primary-color);
        margin-bottom: 8px;
    }

    .stats-label {
        color: var(--secondary-color);
        font-size: 0.9rem;
        font-weight: 600;
    }

    .action-btn {
        padding: 10px 20px;
        border-radius: 10px;
        font-size: 0.9rem;
        margin-right: 10px;
        transition: all 0.3s ease;
        border: none;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .action-btn i {
        margin-right: 8px;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color), #34c759);
        color: white;
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #1e7a1d, #249722);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(25, 151, 34, 0.4);
    }

    .btn-outline-secondary {
        color: var(--secondary-color);
        border-color: var(--secondary-color);
    }

    .btn-outline-secondary:hover {
        background: var(--secondary-color);
        color: white;
    }

    .btn-outline-warning {
        color: #f39c12;
        border-color: #f39c12;
    }

    .btn-outline-warning:hover {
        background: #f39c12;
        color: white;
    }

    .btn-outline-danger {
        color: #e74c3c;
        border-color: #e74c3c;
    }

    .btn-outline-danger:hover {
        background: #e74c3c;
        color: white;
    }

    .recent-activity {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .activity-item {
        display: flex;
        align-items: flex-start;
        padding: 15px 0;
        border-bottom: 1px solid #f1f3f4;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--primary-light);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        color: var(--primary-color);
        font-size: 1rem;
    }

    .activity-content {
        flex: 1;
    }

    .activity-title {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 5px;
    }

    .activity-time {
        font-size: 0.85rem;
        color: var(--secondary-color);
    }

    @media (max-width: 768px) {
        .user-avatar-large {
            width: 80px;
            height: 80px;
        }

        .stats-number {
            font-size: 1.5rem;
        }

        .action-btn {
            padding: 8px 15px;
            font-size: 0.8rem;
            margin-bottom: 10px;
        }
    }
</style>


@section('content')
    <div class="user-details-container">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="h3 mb-0">User Details</h1>
                    <p class="mb-0 text-muted">Complete information about {{ $user->name }}</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary action-btn">
                        <i class="fas fa-arrow-left"></i> Back to Users
                    </a>

                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column - User Profile & Stats -->
            <div class="col-md-4">
                <!-- User Profile Card -->
                <div class="user-profile-card text-center">
                    <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=249722&color=fff&size=128&bold=true' }}"
                        alt="{{ $user->name }}" class="user-avatar-large">
                    <h3 class="mb-2">{{ $user->name }}</h3>
                    <p class="text-muted mb-3">{{ $user->email }}</p>

                    <div class="mb-4 text-center">
                        <span
                            class="status-badge {{ $user->status === 'active' ? 'status-active' : 'status-inactive' }}">
                            <i class="fas {{ $user->status === 'active' ? 'fa-play' : 'fa-pause' }} me-1"></i>
                            {{ ucfirst($user->status) }}
                        </span>
                    </div>

                    <div class="d-grid gap-2">
                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-warning action-btn w-100">
                                <i class="fas fa-sync-alt"></i> Toggle Status
                            </button>
                        </form>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger action-btn w-100"
                                onclick="return confirm('Are you sure you want to delete this user?')">
                                <i class="fas fa-trash"></i> Delete User
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="stats-card">
                    <div class="stats-number">{{ $user->user_type === 'driver' ? $user->rides_count : $user->bookings_count }}</div>
                    <div class="stats-label">Total {{ $user->user_type === 'driver' ? 'Rides' : 'Bookings' }}</div>
                </div>
                <div class="stats-card">
                    <div class="stats-number">
                        @if($user->user_type === 'driver')
                            {{ number_format($user->driver_reviews_avg_rating ?? 0, 1) }}
                        @else
                            {{ number_format($user->passenger_reviews_avg_rating ?? 0, 1) }}
                        @endif
                    </div>
                    <div class="stats-label">Average Rating</div>
                </div>
                <div class="stats-card">
                    <div class="stats-number">
                        @php
                            $totalAmount = $user->user_type === 'driver' 
                                ? \App\Models\Booking::whereIn('ride_id', $user->rides->pluck('id'))->where('status', 'confirmed')->sum('total_price')
                                : $user->bookings->where('status', 'confirmed')->sum('total_price');
                        @endphp
                        {{ number_format($totalAmount, 2) }}
                    </div>
                    <div class="stats-label">{{ $user->user_type === 'driver' ? 'Total Earned' : 'Total Spent' }}</div>
                </div>
            </div>

            <!-- Right Column - User Details -->
            <div class="col-md-8">
                <!-- Personal Information -->
                <div class="info-card">
                    <div class="info-card-header">
                        <i class="fas fa-user-circle"></i>
                        <h5>Personal Information</h5>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Full Name:</span>
                        <span class="info-value">{{ $user->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email Address:</span>
                        <span class="info-value">{{ $user->email }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Phone Number:</span>
                        <span class="info-value">{{ $user->phone ?? 'Not provided' }}</span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">Member Since:</span>
                        <span class="info-value">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Last Login:</span>
                        <span
                            class="info-value">{{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : 'Never' }}</span>
                    </div>
                </div>

                <!-- Account Information -->
                <div class="info-card">
                    <div class="info-card-header">
                        <i class="fas fa-shield-alt"></i>
                        <h5>Account Information</h5>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Account Status:</span>
                        <span class="info-value">
                            <span
                                class="status-badge {{ $user->status === 'active' ? 'status-active' : 'status-inactive' }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email Verified:</span>
                        <span class="info-value">
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle me-1"></i>Verified
                            </span>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Phone Verified:</span>
                        <span class="info-value">
                            <span class="badge {{ $user->phone ? 'bg-success' : 'bg-secondary' }}">
                                <i class="fas {{ $user->phone ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                {{ $user->phone ? 'Verified' : 'Not Verified' }}
                            </span>
                        </span>
                    </div>
                    
                </div>

                <!-- Recent Activity -->
                <div class="info-card">
                    <div class="info-card-header">
                        <i class="fas fa-history"></i>
                        <h5>Recent Activity</h5>
                    </div>
                    <ul class="recent-activity">
                        <li class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-car"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Completed a ride</div>
                                <div class="activity-time">2 hours ago</div>
                            </div>
                        </li>
                        <li class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Rated a driver</div>
                                <div class="activity-time">5 hours ago</div>
                            </div>
                        </li>
                        <li class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Added payment method</div>
                                <div class="activity-time">1 day ago</div>
                            </div>
                        </li>
                        <li class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Updated location preferences</div>
                                <div class="activity-time">2 days ago</div>
                            </div>
                        </li>
                    </ul>
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
        document.addEventListener('DOMContentLoaded', function() {
            // SweetAlert notifications for actions
            const toggleStatusForm = document.querySelector('form[action*="toggle-status"]');
            const deleteForm = document.querySelector('form[action*="destroy"]');

            if (toggleStatusForm) {
                toggleStatusForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Toggle Status?',
                        text: "Are you sure you want to change this user's status?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#249722',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, change it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            }

            if (deleteForm) {
                deleteForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Delete User?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            }

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
