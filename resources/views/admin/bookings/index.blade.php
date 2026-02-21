@extends('admin.layouts.master')
@section('title')
    Booking Management
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

    .card-header {
        background-color: white;
        border-bottom: 1px solid var(--border-color);
        padding: 1.5rem;
    }

    .card-body {
        padding: 1.5rem;
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

    .status-confirmed {
        background-color: rgba(40, 167, 69, 0.1);
        color: var(--success-color);
        border: 1px solid var(--success-color);
    }
    
    .status-completed {
        background-color: rgba(23, 162, 184, 0.1);
        color: var(--info-color);
        border: 1px solid var(--info-color);
    }

    .status-pending {
        background-color: rgba(255, 193, 7, 0.1);
        color: #856404;
        border: 1px solid var(--warning-color);
    }

    .status-rejected, .status-cancelled {
        background-color: rgba(220, 53, 69, 0.1);
        color: var(--danger-color);
        border: 1px solid var(--danger-color);
    }

    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid var(--border-color);
    }

    #bookingsTable {
        margin-bottom: 0 !important;
    }

    #bookingsTable thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid var(--border-color);
        font-weight: 600;
        color: #495057;
        padding: 1rem;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
    }

    #bookingsTable tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-color: var(--border-color);
    }

    #bookingsTable tbody tr {
        transition: all 0.3s ease;
    }

    #bookingsTable tbody tr:hover {
        background-color: rgba(40, 167, 69, 0.03);
        transform: translateY(-2px);
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 12px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
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
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .user-avatar {
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

    .user-details h6 {
        margin: 0;
        font-weight: 600;
        font-size: 14px;
    }

    .user-details small {
        color: #6c757d;
        font-size: 12px;
    }
    
    .ride-info {
        font-size: 13px;
        color: #495057;
    }
    
    .ride-route {
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 4px;
    }
    
    .ride-meta {
        color: #6c757d;
        font-size: 12px;
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

    .stats-icon.confirmed, .stats-icon.completed {
        background: rgba(40, 167, 69, 0.1);
        color: var(--success-color);
    }

    .stats-icon.pending {
        background: rgba(255, 193, 7, 0.1);
        color: #856404;
    }

    .stats-icon.rejected, .stats-icon.cancelled {
        background: rgba(220, 53, 69, 0.1);
        color: var(--danger-color);
    }

    .stats-icon.total {
        background: rgba(67, 97, 238, 0.1);
        color: #4361ee;
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


@section('content')
    <div class="container-fluid">
        <!-- Statistics Row -->
        <div class="row mb-4 mt-4">
            <div class="col-xl-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon total">
                        <i class="fas fa-list fa-2x"></i>
                    </div>
                    <h3 class="stats-number">{{ $totalBookings }}</h3>
                    <p class="stats-label">Total Bookings</p>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon confirmed">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <h3 class="stats-number">{{ $confirmedBookings + $completedBookings }}</h3>
                    <p class="stats-label">Confirmed/Completed</p>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon pending">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <h3 class="stats-number">{{ $pendingBookings }}</h3>
                    <p class="stats-label">Pending Review</p>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon rejected">
                        <i class="fas fa-times-circle fa-2x"></i>
                    </div>
                    <h3 class="stats-number">{{ $rejectedBookings + $cancelledBookings }}</h3>
                    <p class="stats-label">Rejected/Cancelled</p>
                </div>
            </div>
        </div>

        <!-- Header Row -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-2 font-size-18 fw-bold text-dark">Booking Management</h4>
                        <p class="text-muted mb-0">Manage all ride bookings and their status</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Table Card -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Booking Records</h5>
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
                        <div class="table-responsive" style="padding:20px;">
                            <table id="bookingsTable" class="table table-hover dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;">ID</th>
                                        <th>Passenger</th>
                                        <th>Route Details</th>
                                        <th>Seats & Price</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th style="width: 70px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $shortLoc = function($loc) {
                                            if(!$loc) return 'N/A';
                                            $parts = explode(',', $loc);
                                            return trim($parts[0]);
                                        };
                                    @endphp
                                    @foreach($bookings as $booking)
                                        <tr>
                                            <td>
                                                <span class="badge bg-light text-dark border">#{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</span>
                                            </td>
                                            <td>
                                                <div class="user-info">
                                                    <div class="user-avatar" style="width: 30px; height: 30px; font-size: 12px;">
                                                        {{ substr($booking->user->name ?? 'NA', 0, 1) }}
                                                    </div>
                                                    <div class="user-details">
                                                        <h6 class="mb-0" style="font-size: 13px;">{{ $booking->user->name ?? 'N/A' }}</h6>
                                                        <small class="text-muted" style="font-size: 11px;">{{ $booking->user->email ?? 'N/A' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="ride-info">
                                                    <div class="ride-route d-flex align-items-center">
                                                        <span class="text-success fw-bold">{{ $shortLoc($booking->ride->pickup_point ?? '') }}</span>
                                                        <i class="fas fa-long-arrow-alt-right text-muted mx-2"></i>
                                                        <span class="text-danger fw-bold">{{ $shortLoc($booking->ride->drop_point ?? '') }}</span>
                                                    </div>
                                                    <div class="ride-meta mt-1">
                                                        <span class="badge bg-light text-muted border-0 p-0" style="font-size: 11px;">
                                                            <i class="far fa-calendar-alt me-1"></i> {{ $booking->ride ? \Carbon\Carbon::parse($booking->ride->date_time)->format('d M, h:i A') : 'N/A' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 py-1 px-2">
                                                        {{ $booking->seats_booked }} Seats
                                                    </span>
                                                    <span class="fw-bold text-success" style="font-size: 14px;">
                                                        ₹{{ number_format($booking->total_price, 0) }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <select class="form-select form-select-sm status-select py-1 shadow-none" 
                                                        data-id="{{ $booking->id }}" 
                                                        style="width: 125px; font-size: 12px; border-radius: 6px; border-left: 4px solid {{ 
                                                            $booking->status == 'confirmed' ? 'var(--success-color)' : 
                                                            ($booking->status == 'completed' ? 'var(--info-color)' : 
                                                            ($booking->status == 'rejected' || $booking->status == 'cancelled' ? 'var(--danger-color)' : 
                                                            'var(--warning-color)')) 
                                                        }}">
                                                    <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                                    <option value="completed" {{ $booking->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                                    <option value="rejected" {{ $booking->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                    <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                </select>
                                            </td>
                                            <td>
                                                <span class="text-muted" style="font-size: 12px;">
                                                    {{ $booking->created_at->format('d M, Y') }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <form action="{{ route('admin.bookings.destroy', $booking->id) }}" 
                                                      method="POST" 
                                                      class="delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger border-0 p-1"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-title="Delete" style="width: 28px; height: 28px;">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
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
<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#bookingsTable').DataTable({
            responsive: true,
            lengthChange: true,
            lengthMenu: [10, 25, 50, 100],
            pageLength: 5,
            order: [[0, 'desc']],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>tr<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            buttons: [
                {
                    extend: 'csv',
                    text: '<i class="fas fa-file-csv me-1"></i> CSV',
                    className: 'btn btn-outline-secondary',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 5]
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print me-1"></i> Print',
                    className: 'btn btn-outline-secondary',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 5]
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
            var bookingId = $(this).data('id');
            var status = $(this).val();
            var $select = $(this);
            
            // Update border color based on status
            var borderColor = status === 'confirmed' ? 'var(--success-color)' : 
                             status === 'completed' ? 'var(--info-color)' : 
                             (status === 'rejected' || status === 'cancelled' ? 'var(--danger-color)' : 
                             'var(--warning-color)');
            $select.css('border-color', borderColor);
            
            Swal.fire({
                title: 'Update Status?',
                text: 'Are you sure you want to update the booking status?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, update it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/admin/bookings/' + bookingId + '/status',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            status: status
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
    });
</script>
@endsection
