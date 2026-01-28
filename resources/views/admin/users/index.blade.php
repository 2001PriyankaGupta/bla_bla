@extends('admin.layouts.master')
@section('title')
    User Management
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <style>
        .property-type-img {
            max-width: 80px;
            max-height: 60px;
            border-radius: 4px;
            object-fit: cover;
        }

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

        .user-management-container {
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

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(25, 151, 34, 0.15);
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 8px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .stats-label {
            color: var(--secondary-color);
            font-size: 0.95rem;
            font-weight: 600;
        }

        /* Enhanced Filter Section */
        .filter-section {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(25, 151, 34, 0.1);
        }

        .filter-header {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--primary-light);
        }

        .filter-header i {
            color: var(--primary-color);
            font-size: 1.5rem;
            margin-right: 12px;
        }

        .filter-header h5 {
            margin: 0;
            color: #2c3e50;
            font-weight: 700;
        }

        .filter-group {
            margin-bottom: 25px;
        }

        .filter-title {
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
        }

        .filter-title i {
            margin-right: 8px;
            color: var(--primary-color);
        }

        .filter-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 12px;
        }

        .filter-option {
            position: relative;
        }

        .filter-option input[type="radio"] {
            display: none;
        }

        .filter-option label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px 20px;
            background: var(--light-bg);
            border: 2px solid transparent;
            border-radius: 12px;
            color: var(--secondary-color);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            min-height: 60px;
        }

        .filter-option label:hover {
            background: var(--primary-light);
            border-color: rgba(25, 151, 34, 0.2);
            transform: translateY(-2px);
        }

        .filter-option input[type="radio"]:checked+label {
            background: linear-gradient(135deg, var(--primary-color), #34c759);
            color: white;
            border-color: var(--primary-color);
            box-shadow: 0 4px 15px rgba(25, 151, 34, 0.3);
        }

        .filter-option label i {
            margin-right: 8px;
            font-size: 1.1rem;
        }

        .user-table {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            padding: 25px;
            border: 1px solid rgba(25, 151, 34, 0.1);
        }

        .table th {
            background: linear-gradient(135deg, var(--primary-light), #f8f9fa);
            border-bottom: 3px solid var(--primary-color);
            font-weight: 700;
            color: #2c3e50;
            padding: 18px;
            font-size: 0.95rem;
        }

        .table td {
            padding: 16px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f3f4;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: var(--primary-light);
            transform: scale(1.01);
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-light);
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-active {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            box-shadow: 0 2px 8px rgba(39, 174, 96, 0.3);
            padding: 5px;
            border-radius: 19px;
            font-size: smaller;
        }

        .status-inactive {
            background: linear-gradient(135deg, #e74c3c, #ff6b6b);
            color: white;
            box-shadow: 0 2px 8px rgba(231, 76, 60, 0.3);
        }

        .kyc-badge {
            padding: 6px 12px;
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .kyc-verified {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            border-radius: 27px;
            padding: 3px;
            font-size: smaller;
        }

        .kyc-pending {
            background: linear-gradient(135deg, #f39c12, #f1c40f);
            color: white;
        }

        .action-btn {
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            margin-right: 5px;
            transition: all 0.3s ease;
            border: none;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Custom Search Box */
        .datatable-custom-search {
            background: linear-gradient(135deg, #fff, #f8f9fa);
            border-radius: 12px;
            padding: 18px 25px;
            margin-bottom: 25px;
            box-shadow: var(--card-shadow);
            display: flex;
            align-items: center;
            gap: 15px;
            border: 1px solid rgba(25, 151, 34, 0.1);
        }

        .datatable-custom-search input[type="text"] {
            border: none;
            outline: none;
            width: 100%;
            background: transparent;
            font-size: 1rem;
            color: #2c3e50;
            font-weight: 500;
        }

        .datatable-custom-search input[type="text"]::placeholder {
            color: #95a5a6;
        }

        .datatable-custom-search .btn {
            background: linear-gradient(135deg, var(--primary-color), #34c759);
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(25, 151, 34, 0.3);
        }

        .datatable-custom-search .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(25, 151, 34, 0.4);
        }

        /* DataTable Custom Styles */
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_length label {
            display: none !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 8px 16px;
            margin: 0 4px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: linear-gradient(135deg, var(--primary-color), #34c759);
            color: white !important;
            border: 1px solid var(--primary-color);
            box-shadow: 0 4px 15px rgba(25, 151, 34, 0.3);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: linear-gradient(135deg, #1e7a1d, #249722);
            color: white !important;
            border: 1px solid #1e7a1d;
            transform: translateY(-2px);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .filter-options {
                grid-template-columns: 1fr;
            }

            .stats-number {
                font-size: 2rem;
            }

            .table-responsive {
                font-size: 0.9rem;
            }
        }

        /* Animation for filter changes */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .table tbody tr {
            animation: fadeIn 0.5s ease;
        }
    </style>
@endsection

@section('content')
    <div class="user-management-container">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="h3 mb-0">User Management</h1>
                    <p class="mb-0 text-muted">Manage and monitor platform users</p>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number" id="totalUsers">{{ $users->total() }}</div>
                    <div class="stats-label">Total Users</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number" id="passengersCount">{{ $users->where('user_type', 'passenger')->count() }}
                    </div>
                    <div class="stats-label">Passengers</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number" id="driversCount">{{ $users->where('user_type', 'driver')->count() }}</div>
                    <div class="stats-label">Drivers</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number" id="activeUsers">{{ $users->where('status', 'active')->count() }}</div>
                    <div class="stats-label">Active Users</div>
                </div>
            </div>
        </div>

        <!-- Enhanced Filter Section -->
        <div class="filter-section">
            <div class="filter-header">
                <i class="fas fa-sliders-h"></i>
                <h5>Filter Users</h5>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="filter-group">
                        <div class="filter-title">
                            <i class="fas fa-users"></i>
                            User Type
                        </div>
                        <div class="filter-options">
                            <div class="filter-option">
                                <input type="radio" id="allUsers" name="userType" value="all" checked>
                                <label for="allUsers">
                                    <i class="fas fa-layer-group"></i>
                                    All Users
                                </label>
                            </div>
                            <div class="filter-option">
                                <input type="radio" id="passengers" name="userType" value="passenger">
                                <label for="passengers">
                                    <i class="fas fa-user"></i>
                                    Passengers
                                </label>
                            </div>
                            <div class="filter-option">
                                <input type="radio" id="drivers" name="userType" value="driver">
                                <label for="drivers">
                                    <i class="fas fa-car"></i>
                                    Drivers
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="filter-group">
                        <div class="filter-title">
                            <i class="fas fa-user-check"></i>
                            Account Status
                        </div>
                        <div class="filter-options">
                            <div class="filter-option">
                                <input type="radio" id="allStatus" name="status" value="all" checked>
                                <label for="allStatus">
                                    <i class="fas fa-globe"></i>
                                    All Status
                                </label>
                            </div>
                            <div class="filter-option">
                                <input type="radio" id="active" name="status" value="active">
                                <label for="active">
                                    <i class="fas fa-check-circle"></i>
                                    Active
                                </label>
                            </div>
                            <div class="filter-option">
                                <input type="radio" id="inactive" name="status" value="inactive">
                                <label for="inactive">
                                    <i class="fas fa-pause-circle"></i>
                                    Inactive
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Users Table -->
        <div class="user-table">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="usersTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Rides</th>
                            <th>Rating</th>
                            <th>KYC</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $user->profile_picture ? asset($user->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=249722&color=fff&size=128&bold=true' }}"
                                            alt="{{ $user->name }}" class="user-avatar me-3">
                                        <div>
                                            <div class="fw-bold">{{ $user->name }}</div>
                                            <small class="text-muted">{{ $user->phone }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark text-capitalize px-3 py-2">
                                        <i class="fas {{ $user->user_type === 'driver' ? 'fa-car' : 'fa-user' }} me-2"></i>
                                        {{ $user->user_type ?? 'passenger' }}
                                    </span>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="fw-bold text-primary">{{ rand(50, 200) }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="text-warning me-2">
                                            <i class="fas fa-star"></i>
                                        </span>
                                        <span class="fw-bold">{{ number_format(rand(40, 50) / 10, 1) }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="kyc-verified">
                                        <i class="fas fa-shield-check me-1"></i>Verified
                                    </span>
                                </td>
                                <td>
                                    <span class="{{ $user->status === 'active' ? 'status-active' : 'status-inactive' }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.users.show', $user) }}"
                                            class="btn btn-sm btn-outline-primary action-btn" data-bs-toggle="tooltip"
                                            title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                            class="btn btn-sm btn-outline-secondary action-btn" data-bs-toggle="tooltip"
                                            title="Edit User">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST"
                                            class="d-inline toggle-status-form">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-warning action-btn"
                                                data-bs-toggle="tooltip" title="Toggle Status">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                            class="d-inline delete-user-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger action-btn"
                                                data-bs-toggle="tooltip" title="Delete User">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const table = $('#usersTable').DataTable({
                "pageLength": 7,
                "lengthMenu": [7, 15, 25, 50],
                "dom": 't<"d-flex justify-content-between align-items-center mt-4"lip>',
                "language": {
                    "zeroRecords": "No matching users found",
                    "info": "Showing _START_ to _END_ of _TOTAL_ users",
                    "infoEmpty": "No users available",
                    "infoFiltered": "(filtered from _MAX_ total users)",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    }
                }
            });

            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            const tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Custom search functionality
            $('#customSearchBtn').on('click', function() {
                table.search($('#customSearchInput').val()).draw();
                updateStatistics();
            });

            $('#customSearchInput').on('keyup', function(e) {
                if (e.key === "Enter" || e.keyCode === 13) {
                    table.search(this.value).draw();
                    updateStatistics();
                }
            });

            // User type filter functionality
            $('input[name="userType"]').on('change', function() {
                const userType = $(this).val();
                if (userType === 'all') {
                    table.column(1).search('').draw();
                } else {
                    table.column(1).search(userType).draw();
                }
                updateStatistics();
            });

            // Status filter functionality - FIXED
            $('input[name="status"]').on('change', function() {
                const status = $(this).val();
                if (status === 'all') {
                    table.column(6).search('').draw();
                } else {
                    // Search for the status in the status column (column index 6)
                    table.column(6).search('^' + status + '$', true, false).draw();
                }
                updateStatistics();
            });

            // Update statistics based on filtered data
            function updateStatistics() {
                setTimeout(() => {
                    const visibleRows = table.rows({
                        filter: 'applied'
                    }).count();
                    const passengerCount = table.column(1, {
                        search: 'applied'
                    }).data().toArray().filter(type => type.includes('passenger')).length;
                    const driverCount = table.column(1, {
                        search: 'applied'
                    }).data().toArray().filter(type => type.includes('driver')).length;
                    const activeCount = table.column(6, {
                        search: 'applied'
                    }).data().toArray().filter(status => status.includes('active')).length;

                    $('#totalUsers').text(visibleRows);
                    $('#passengersCount').text(passengerCount);
                    $('#driversCount').text(driverCount);
                    $('#activeUsers').text(activeCount);
                }, 100);
            }

            // Add animation to table rows
            table.on('draw', function() {
                $('tbody tr').css('opacity', '0').animate({
                    opacity: 1
                }, 500);
            });

            // SweetAlert notifications for user actions
            // Toggle status form submission
            $('.toggle-status-form').on('submit', function(e) {
                e.preventDefault();
                const form = this;

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You are about to change this user's status",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#249722',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, change it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading toast
                        Swal.fire({
                            title: 'Updating...',
                            text: 'Please wait',
                            icon: 'info',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Submit the form
                        $.ajax({
                            url: $(form).attr('action'),
                            method: 'POST',
                            data: $(form).serialize(),
                            success: function(response) {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: 'User status updated successfully!',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true
                                });

                                // Reload the page after a short delay to reflect changes
                                setTimeout(() => {
                                    location.reload();
                                }, 1500);
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'error',
                                    title: 'Error updating user status!',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true
                                });
                            }
                        });
                    }
                });
            });

            // Delete user form submission
            $('.delete-user-form').on('submit', function(e) {
                e.preventDefault();
                const form = this;

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading toast
                        Swal.fire({
                            title: 'Deleting...',
                            text: 'Please wait',
                            icon: 'info',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Submit the form
                        $.ajax({
                            url: $(form).attr('action'),
                            method: 'POST',
                            data: $(form).serialize(),
                            success: function(response) {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: 'User deleted successfully!',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true
                                });

                                // Remove the row from the table
                                $(form).closest('tr').fadeOut(500, function() {
                                    table.row($(this)).remove().draw();
                                    updateStatistics();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'error',
                                    title: 'Error deleting user!',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true
                                });
                            }
                        });
                    }
                });
            });

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

            // Display error message from session if exists
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
