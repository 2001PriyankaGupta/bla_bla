@extends('admin.layouts.master')
@section('title')
    User Management
@endsection

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
    td{
        font-size: 12px;
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
        background: white;
        border-radius: 12px;
        padding: 16px;
        display: flex;
        align-items: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        margin-bottom: 24px;
        border: 1px solid rgba(0, 0, 0, 0.03);
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        position: relative;
        overflow: hidden;
        cursor: pointer;
        text-decoration: none !important;
    }

    .stats-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .stats-card.active {
        background: var(--primary-light);
        border-color: var(--primary-color);
        box-shadow: 0 4px 15px rgba(25, 151, 34, 0.1);
    }

    .stats-icon {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-right: 15px;
        background: var(--primary-light);
        color: var(--primary-color);
        transition: all 0.4s ease;
    }

    .stats-card:hover .stats-icon {
        background: var(--primary-color);
        color: white;
        transform: rotate(-10deg) scale(1.1);
    }

    .stats-info {
        flex: 1;
    }

    .stats-number {
        font-size: 1.4rem;
        font-weight: 800;
        color: #2c3e50;
        margin-bottom: 2px;
        line-height: 1.2;
    }

    .stats-label {
        color: var(--secondary-color);
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    /* Enhanced Filter Section */
    .filter-section {
        background: white;
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(0, 0, 0, 0.03);
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
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }

    .filter-option {
        position: relative;
    }

    .filter-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
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
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        display: inline-block;
        min-width: 85px;
        text-align: center;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .status-active {
        background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
        color: white;
        border: 1px solid #27ae60;
    }

    .status-inactive {
        background: linear-gradient(135deg, #e74c3c 0%, #ff6b6b 100%);
        color: white;
        border: 1px solid #e74c3c;
    }

    .status-badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
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
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 10px;
        font-size: 0.85rem;
        margin-right: 4px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid transparent;
        background-color: var(--light-bg);
    }

    .action-btn i {
        line-height: 1;
    }

    .btn-outline-primary.action-btn {
        color: var(--primary-color);
        background-color: var(--primary-light);
    }
    .btn-outline-primary.action-btn:hover {
        background-color: var(--primary-color);
        color: white;
    }

    .btn-outline-secondary.action-btn {
        color: var(--secondary-color);
        background-color: #f1f3f4;
    }
    .btn-outline-secondary.action-btn:hover {
        background-color: var(--secondary-color);
        color: white;
    }

    .btn-outline-warning.action-btn {
        color: #f39c12;
        background-color: #fdf5e6;
    }
    .btn-outline-warning.action-btn:hover {
        background-color: #f39c12;
        color: white;
    }

    .btn-outline-danger.action-btn {
        color: #e74c3c;
        background-color: #fdeaeb;
    }
    .btn-outline-danger.action-btn:hover {
        background-color: #e74c3c;
        color: white;
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

    /* Search Box Premium Styling */
    .search-box-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
        max-width: 450px;
        margin-left: auto;
    }

    .search-icon {
        position: absolute;
        left: 15px;
        color: var(--secondary-color);
        font-size: 1rem;
        z-index: 5;
    }

    #customSearchInput {
        padding: 12px 60px 12px 45px;
        border-radius: 12px;
        border: 2px solid #edf2f7;
        background: #f8f9fa;
        font-size: 0.95rem;
        font-weight: 500;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        width: 100%;
        color: #2d3748;
    }

    #customSearchInput:focus {
        border-color: var(--primary-color);
        background: white;
        box-shadow: 0 4px 15px rgba(36, 151, 34, 0.1);
        outline: none;
    }

    .search-btn {
        position: absolute;
        right: 8px;
        top: 6px;
        bottom: 6px;
        width: 40px;
        background: var(--primary-color);
        border: none;
        border-radius: 8px;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .search-btn:hover {
        background: #1b7a19;
        transform: scale(1.05);
    }

    .search-btn i {
        font-size: 0.9rem;
    }

    @media (max-width: 991.98px) {
        .search-box-wrapper {
            max-width: 100%;
            margin-top: 20px;
        }
        
        .filter-header {
            margin-right: 0 !important;
            margin-bottom: 20px !important;
        }
        
        .filter-options {
            justify-content: center;
        }
    }

    @media (max-width: 575.98px) {
        .filter-options {
            flex-direction: column;
            width: 100%;
        }
        
        .filter-option, .filter-option label {
            width: 100%;
        }
    }

    /* Animation */
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .user-management-container {
        animation: slideInUp 0.8s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    .table tbody tr {
        transition: all 0.3s ease;
    }
    .type-badge {
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        min-width: 105px;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .type-driver {
        background: linear-gradient(135deg, #1fa000 0%, #2ecc71 100%);
        color: white;
        border: 1px solid #1fa000;
    }

    .type-passenger {
        background: linear-gradient(135deg, #7b1fa2 0%, #9c27b0 100%);
        color: white;
        border: 1px solid #7b1fa2;
    }

    .type-badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
</style>


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

        <!-- Statistics Cards (Clickable for filtering) -->
        <div class="row row-cols-1 row-cols-md-5 g-3 mb-4">
            <div class="col">
                <div class="stats-card filter-card active" data-status="all">
                    <div class="stats-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-info">
                        <div class="stats-number">{{ $stats['total'] }}</div>
                        <div class="stats-label">All Users</div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="stats-card filter-card" data-status="active">
                    <div class="stats-icon" style="background: #e8f5e9; color: #2e7d32;">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stats-info">
                        <div class="stats-number">{{ $stats['active'] }}</div>
                        <div class="stats-label">Active Users</div>
                    </div>
                </div>
            </div>
            
            <div class="col">
                <div class="stats-card filter-card" data-status="inactive">
                    <div class="stats-icon" style="background: #fdeaeb; color: #e74c3c;">
                        <i class="fas fa-user-times"></i>
                    </div>
                    <div class="stats-info">
                        <div class="stats-number">{{ $stats['inactive'] }}</div>
                        <div class="stats-label">Inactive Users</div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="stats-card filter-card" data-type="driver">
                    <div class="stats-icon" style="background: #fff3e0; color: #ef6c00;">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <div class="stats-info">
                        <div class="stats-number">{{ $stats['drivers'] }}</div>
                        <div class="stats-label">Total Drivers</div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="stats-card filter-card" data-type="passenger">
                    <div class="stats-icon" style="background: #e8f5e9; color: #1fa000;">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <div class="stats-info">
                        <div class="stats-number">{{ $stats['passengers'] }}</div>
                        <div class="stats-label">Total Passengers</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="user-table">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0" style="color: #2c3e50; font-weight: 700;">User List</h5>
                <div class="search-box-wrapper" style="max-width: 300px; width: 100%; margin: 0;">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="customSearchInput" class="form-control" placeholder="Quick search users...">
                    <button id="customSearchBtn" class="search-btn">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="usersTable">
                    <thead>
                        <tr>
                            <th>User Details</th>
                            <th>Email Address</th>
                            <th class="text-center">Activity</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">User Type</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr data-status="{{ $user->status }}" data-user-type="{{ $user->user_type }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="position-relative">
                                            <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=249722&color=fff&size=128&bold=true' }}"
                                                alt="{{ $user->name }}" class="user-avatar shadow-sm border">
                                        </div>
                                        <div class="ms-3">
                                            <div class="fw-bold" style="color: #2c3e50;">{{ $user->name }}</div>
                                            <small class="text-muted d-block"><i class="fas fa-phone-alt me-1 font-size-10"></i>{{ $user->phone }}</small>
                                        </div>
                                    </div>
                                </td>

                                <td>{{ $user->email }}</td>
                                <td class="text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        <span class="fw-bold text-primary mb-1">
                                            {{ $user->user_type === 'driver' ? $user->rides_count : $user->bookings_count }}
                                        </span>
                                        <div class="d-flex align-items-center text-warning font-size-12">
                                            <i class="fas fa-star me-1"></i>
                                            <span class="fw-bold text-dark">
                                                @if($user->user_type === 'driver')
                                                    {{ number_format($user->driver_reviews_avg_rating ?? 0, 1) }}
                                                @else
                                                    {{ number_format($user->passenger_reviews_avg_rating ?? 0, 1) }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="text-center" data-search="{{ $user->status }}">
                                    <span class="status-badge {{ $user->status === 'active' ? 'status-active' : 'status-inactive' }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>
                                <td class="text-center" data-search="{{ $user->user_type }}">
                                    <span class="type-badge {{ $user->user_type === 'driver' ? 'type-driver' : 'type-passenger' }}">
                                        @if($user->user_type === 'driver')
                                            <i class="fas fa-car-side"></i> Driver
                                        @else
                                            <i class="fas fa-user"></i> Passenger
                                        @endif
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center">
                                        <a href="{{ route('admin.users.show', $user) }}"
                                            class="action-btn btn-outline-primary" data-bs-toggle="tooltip"
                                            title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                            class="action-btn btn-outline-secondary" data-bs-toggle="tooltip"
                                            title="Edit User">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST"
                                            class="d-inline toggle-status-form">
                                            @csrf
                                            <button type="submit" class="action-btn btn-outline-warning"
                                                data-bs-toggle="tooltip" title="Toggle Status">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                            class="d-inline delete-user-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn btn-outline-danger"
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
                "pageLength": 10,
                "lengthMenu": [10, 25, 50, 100],
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

            // Handle initial filtering from URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const statusParam = urlParams.get('status');
            
            // Filter state
            let currentFilter = {
                status: statusParam || 'all',
                type: 'all'
            };

            if (statusParam) {
                $('.filter-card').removeClass('active');
                $(`.filter-card[data-status="${statusParam}"]`).addClass('active');
            }

            // Custom DataTable filter (Status and Type)
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    // Get row data using data attributes
                    const rowStatus = $(table.row(dataIndex).node()).attr('data-status');
                    const rowType = $(table.row(dataIndex).node()).attr('data-user-type');
                    
                    let statusMatch = currentFilter.status === 'all' || rowStatus === currentFilter.status;
                    let typeMatch = currentFilter.type === 'all' || rowType === currentFilter.type;
                    
                    return statusMatch && typeMatch;
                }
            );

            table.draw();

            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            const tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Custom search functionality
            $('#customSearchBtn').on('click', function() {
                table.search($('#customSearchInput').val()).draw();
            });

            $('#customSearchInput').on('keyup', function(e) {
                if (e.key === "Enter" || e.keyCode === 13) {
                    table.search(this.value).draw();
                }
            });

            // Handle clicking on stat cards to filter
            $('.filter-card').on('click', function() {
                $('.filter-card').removeClass('active');
                $(this).addClass('active');

                // Determine filter from data attributes
                const filterStatus = $(this).data('status');
                const filterType = $(this).data('type');

                currentFilter.status = filterStatus ? filterStatus : 'all';
                currentFilter.type = filterType ? filterType : 'all';

                table.draw();
            });

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