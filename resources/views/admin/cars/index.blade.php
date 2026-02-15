@extends('admin.layouts.master')
@section('title')
    Car Management
@endsection


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">
<style>
    :root {
        --primary-color: #28a745;
        --success-color: #28a745;
        --warning-color: #ffc107;
        --danger-color: #dc3545;
        --info-color: #17a2b8;
        --light-bg: #f8f9fa;
        --border-color: #e9ecef;
    }

    .card {
        border: none;
        box-shadow: 0 0 20px rgba(0,0,0,0.08);
        border-radius: 10px;
        overflow: hidden;
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

    .card-header {
        background-color: white;
        border-bottom: 1px solid var(--border-color);
        padding: 1.5rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    .car-img {
        width: 80px;
        height: 60px;
        border-radius: 6px;
        object-fit: cover;
        border: 2px solid var(--border-color);
        transition: transform 0.3s ease;
    }

    .car-img:hover {
        transform: scale(1.8);
        z-index: 100;
        position: relative;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .status-verified {
        background-color: rgba(40, 167, 69, 0.1);
        color: var(--success-color);
        border: 1px solid var(--success-color);
    }

    .status-pending {
        background-color: rgba(255, 193, 7, 0.1);
        color: #856404;
        border: 1px solid var(--warning-color);
    }

    .status-rejected {
        background-color: rgba(220, 53, 69, 0.1);
        color: var(--danger-color);
        border: 1px solid var(--danger-color);
    }

    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid var(--border-color);
    }

    #carsTable {
        margin-bottom: 0 !important;
    }

    #carsTable thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid var(--border-color);
        font-weight: 600;
        color: #495057;
        padding: 1rem;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
    }

    #carsTable tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-color: var(--border-color);
    }

    #carsTable tbody tr {
        transition: all 0.3s ease;
    }

    #carsTable tbody tr:hover {
        background-color: rgba(67, 97, 238, 0.03);
        transform: translateY(-2px);
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }

    .action-btns {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 12px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-view {
        background-color: var(--info-color);
        border-color: var(--info-color);
        color: white;
    }

    .btn-view:hover {
        background-color: #138496;
        border-color: #117a8b;
    }

    .btn-status {
        min-width: 110px;
    }

    .status-select {
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        padding: 0.375rem 1.75rem 0.375rem 0.75rem;
        cursor: pointer;
        border: 1px solid #ced4da;
        transition: all 0.3s ease;
    }

    .status-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        padding: 1rem;
    }

    .dataTables_wrapper .dataTables_filter input {
        border-radius: 6px;
        border: 1px solid #ced4da;
        padding: 0.375rem 0.75rem;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 6px !important;
        margin: 0 2px;
        border: 1px solid #dee2e6 !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
        color: white !important;
    }

    .owner-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .owner-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--primary-color);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
    }

    .owner-details h6 {
        margin: 0;
        font-weight: 600;
        font-size: 14px;
    }

    .owner-details small {
        color: #6c757d;
        font-size: 12px;
    }

    .car-details {
        max-width: 200px;
    }

    .car-details .car-title {
        font-weight: 600;
        color: #343a40;
        margin-bottom: 4px;
        font-size: 14px;
    }

    .car-details .car-specs {
        font-size: 12px;
        color: #6c757d;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .car-specs span {
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .stats-card {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        border: 1px solid var(--border-color);
        transition: transform 0.3s ease;
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }

    .stats-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }

    .stats-icon.verified {
        background: rgba(40, 167, 69, 0.1);
        color: var(--success-color);
    }

    .stats-icon.pending {
        background: rgba(255, 193, 7, 0.1);
        color: #856404;
    }

    .stats-icon.rejected {
        background: rgba(220, 53, 69, 0.1);
        color: var(--danger-color);
    }

    .stats-icon.total {
        background: rgba(67, 97, 238, 0.1);
        color: var(--primary-color);
    }

    .stats-number {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .stats-label {
        color: #6c757d;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>


@section('content')
    <div class="container-fluid">
        <!-- Statistics Row -->
        <div class="row mb-4 mt-4">
            <div class="col-xl-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon total">
                        <i class="fas fa-car fa-2x"></i>
                    </div>
                    <h3 class="stats-number">{{ $totalCars }}</h3>
                    <p class="stats-label">Total Cars</p>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon verified">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <h3 class="stats-number">{{ $verifiedCars }}</h3>
                    <p class="stats-label">Verified Cars</p>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon pending">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <h3 class="stats-number">{{ $pendingCars }}</h3>
                    <p class="stats-label">Pending Review</p>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon rejected">
                        <i class="fas fa-times-circle fa-2x"></i>
                    </div>
                    <h3 class="stats-number">{{ $rejectedCars }}</h3>
                    <p class="stats-label">Rejected Cars</p>
                </div>
            </div>
        </div>

        <!-- Header Row -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-2 font-size-18 fw-bold text-dark">Car Management</h4>
                        <p class="text-muted mb-0">Manage all registered cars and their verification status</p>
                    </div>
                    <div class="page-title-right">
                        <a href="{{ route('admin.cars.create') }}" class="btn btn-primary waves-effect waves-light">
                            <i class="fas fa-plus me-2"></i> Add New Car
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Table Card -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Car Records</h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-secondary btn-sm" id="exportCsv">
                                <i class="fas fa-file-export me-1"></i> Export CSV
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" id="printTable">
                                <i class="fas fa-print me-1"></i> Print
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="carsTable" class="table table-hover dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Owner</th>
                                        <th>Car Details</th>
                                        <th>License Plate</th>
                                        <th>License Verified</th>
                                        <th>Photo</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cars as $car)
                                        <tr>
                                            <td>
                                                <span class="badge bg-light text-dark">#{{ str_pad($car->id, 5, '0', STR_PAD_LEFT) }}</span>
                                            </td>
                                            <td>
                                                <div class="owner-info">
                                                    <div class="owner-avatar">
                                                        {{ substr($car->user->name ?? 'NA', 0, 1) }}
                                                    </div>
                                                    <div class="owner-details">
                                                        <h6>{{ $car->user->name ?? 'N/A' }}</h6>
                                                        <small>{{ $car->user->email ?? 'No email' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="car-details">
                                                    <div class="car-title">{{ $car->car_make }} {{ $car->car_model }}</div>
                                                    <div class="car-specs">
                                                        <span><i class="fas fa-calendar-alt"></i> {{ $car->car_year }}</span>
                                                        <span><i class="fas fa-palette"></i> {{ $car->car_color }}</span>
                                                        @if($car->seating_capacity)
                                                            <span><i class="fas fa-users"></i> {{ $car->seating_capacity }} seats</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-dark">{{ $car->licence_plate }}</span>
                                            </td>
                                            <td>
                                                <select class="form-select form-select-sm status-select" 
                                                        data-id="{{ $car->id }}" 
                                                        style="border-color: {{ $car->license_verified == 'verified' ? 'var(--success-color)' : ($car->license_verified == 'rejected' ? 'var(--danger-color)' : 'var(--warning-color)') }}">
                                                    <option value="pending" {{ $car->license_verified == 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="verified" {{ $car->license_verified == 'verified' ? 'selected' : '' }}>Verified</option>
                                                    <option value="rejected" {{ $car->license_verified == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                </select>
                                            </td>
                                            <td>
                                                @if($car->car_photo)
                                                    <div class="position-relative">
                                                        <a href="{{ Storage::url($car->car_photo) }}" 
                                                           class="image-popup"
                                                           data-lightbox="car-{{ $car->id }}">
                                                            <img src="{{ Storage::url($car->car_photo) }}" 
                                                                 alt="Car Photo" 
                                                                 class="car-img">
                                                        </a>
                                                        
                                                    </div>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        <i class="fas fa-image me-1"></i> No Image
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="action-btns">
                                                    <a href="{{ route('admin.cars.edit', $car->id) }}" 
                                                       class="btn btn-sm btn-info"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-title="Edit" style="height: 25px;">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('admin.cars.show', $car->id) }}" 
                                                       class="btn btn-sm btn-success"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-title="View Details" style="height: 25px;">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <form action="{{ route('admin.cars.destroy', $car->id) }}" 
                                                          method="POST" 
                                                          class="d-inline delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-danger"
                                                                data-bs-toggle="tooltip"
                                                                data-bs-title="Delete" style="height: 25px;">
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
            </div>
        </div>
    </div>
@endsection

@section('scripts')
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
 

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/lightbox2@2.11.3/dist/js/lightbox.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#carsTable').DataTable({
            responsive: true,
            lengthChange: true,
            lengthMenu: [10, 25, 50, 100],
            pageLength: 5,
            order: [[0, 'desc']],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>tr<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search cars...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "No entries to show",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            },
            buttons: [
                {
                    extend: 'csv',
                    text: '<i class="fas fa-file-csv me-1"></i> CSV',
                    className: 'btn btn-outline-secondary',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print me-1"></i> Print',
                    className: 'btn btn-outline-secondary',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                }
            ],
            drawCallback: function() {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                });
            }
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Initialize lightbox safely
        if (typeof lightbox !== 'undefined') {
            lightbox.option({
                'resizeDuration': 200,
                'wrapAround': true,
                'albumLabel': "Car %1 of %2"
            });
        }

        // Export CSV button
        $('#exportCsv').on('click', function() {
            table.button('.buttons-csv').trigger();
        });

        // Print button
        $('#printTable').on('click', function() {
            table.button('.buttons-print').trigger();
        });

        // Show Flash Messages with SweetAlert
        @if(session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: '#28a745',
                color: 'white'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: "{{ session('error') }}",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: '#dc3545',
                color: 'white'
            });
        @endif

        // Status change handler (Event Delegation)
        $(document).on('change', '.status-select', function() {
            var carId = $(this).data('id');
            var status = $(this).val();
            var originalStatus = this.defaultValue; // Note: defaultValue might not always track correctly in SPA-like or multi-change scenarios without reload
            var $select = $(this);
            
            // Update border color based on status
            var borderColor = status === 'verified' ? 'var(--success-color)' : 
                             status === 'rejected' ? 'var(--danger-color)' : 'var(--warning-color)';
            $select.css('border-color', borderColor);
            
            Swal.fire({
                title: 'Update Status?',
                text: 'Are you sure you want to update the status of this car?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4361ee',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, update it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/admin/cars/' + carId + '/status',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            license_verified: status
                        },
                        beforeSend: function() {
                            $select.prop('disabled', true);
                        },
                        success: function(response) {
                            $select.prop('disabled', false);
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Status updated successfully',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                background: '#28a745',
                                color: 'white'
                            });
                            // Reload to reflect changes if counters/other UI elements depend on it
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        },
                        error: function() {
                            $select.prop('disabled', false);
                            // Revert to original value (or just reload)
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: 'Failed to update status',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                background: '#dc3545',
                                color: 'white'
                            });
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        }
                    });
                } else {
                    // Revert visual change if cancelled
                     location.reload(); 
                }
            });
        });

        // Delete confirmation (Event Delegation)
        $(document).on('submit', '.delete-form', function(e) {
            e.preventDefault();
            var form = this;
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // Filter by status
        $('.status-filter').on('click', function() {
            var status = $(this).data('status');
            if (status === 'all') {
                table.search('').columns().search('').draw();
            } else {
                table.columns(4).search(status).draw();
            }
        });

        // Add row highlight on hover
        $('#carsTable tbody').on('mouseenter', 'tr', function() {
            $(this).addClass('highlight');
        }).on('mouseleave', 'tr', function() {
            $(this).removeClass('highlight');
        });
    });
</script>
@endsection