@extends('admin.layouts.master')
@section('title')
    Ride Management
@endsection

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <style>
        :root {
            --primary-color: #249722;
            --primary-light: #e8f5e9;
            --secondary-color: #6c757d;
            --light-bg: #f8f9fa;
            --border-color: #e9ecef;
            --card-shadow: 0 4px 12px rgba(25, 151, 34, 0.08);
        }

        .ride-management-container {
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
        }

        .stats-label {
            color: var(--secondary-color);
            font-size: 0.95rem;
            font-weight: 600;
        }

        .filter-section {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(25, 151, 34, 0.1);
        }

        .filter-options {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 12px 24px;
            border: 2px solid transparent;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            background: var(--light-bg);
            color: var(--secondary-color);
            text-decoration: none;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: linear-gradient(135deg, var(--primary-color), #34c759);
            color: white;
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }

        .ride-table {
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
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-active {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
        }

        .status-inactive {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }

        .status-completed {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
        }

        .status-cancelled {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }

        .action-btn {
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            margin-right: 5px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), #34c759);
            border: none;
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1e7a1d, #249722);
            transform: translateY(-2px);
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

        .datatable-custom-search input {
            border: none;
            outline: none;
            width: 100%;
            background: transparent;
            font-size: 1rem;
            color: #2c3e50;
        }

        @media (max-width: 768px) {
            .filter-options {
                flex-direction: column;
            }

            .stats-number {
                font-size: 2rem;
            }
        }
    </style>


@section('content')
    <div class="ride-management-container">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="h3 mb-0">Ride Management</h1>
                    <p class="mb-0 text-muted">Manage and monitor all rides</p>
                </div>
                {{-- <div class="col-md-6 text-end">
                    <a href="{{ route('admin.rides.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Add New Ride
                    </a>
                </div> --}}
            </div>
        </div>


       


        <!-- Rides Table -->
        <div class="ride-table">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="ridesTable">
                    <thead>
                        <tr>
                            <th>Ride ID</th>
                            <th>Driver</th>
                            <th>Car</th>
                            <th>From → To</th>
                            <th>Date & Time</th>
                            <th>Seats</th>
                            <th>Price/Seat</th>
                            <th>Luggage</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rides as $ride)
                            <tr>
                                <td class="fw-bold">#{{ $ride->id }}</td>
                                <td>
                                    @if ($ride->car && $ride->car->user)
                                        <div class="fw-bold">{{ $ride->car->user->name }}</div>
                                        <small class="text-muted">{{ $ride->car->user->phone ?? 'N/A' }}</small>
                                    @else
                                        <div class="fw-bold text-muted">Unknown</div>
                                        <small class="text-muted">N/A</small>
                                    @endif
                                </td>
                                <td>
                                    @if ($ride->car)
                                        <div class="fw-bold">{{ $ride->car->car_make }} {{ $ride->car->car_model }}</div>
                                        <small class="text-muted">{{ $ride->car->licence_plate }}</small>
                                    @else
                                        <div class="fw-bold text-muted">No Car</div>
                                        <small class="text-muted">N/A</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $ride->pickup_point }}</div>
                                    <div class="text-muted small">→ {{ $ride->drop_point }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $ride->date_time->format('M d, Y') }}</div>
                                    <div class="text-muted small">{{ $ride->date_time->format('h:i A') }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $ride->total_seats }}</div>
                                    <div class="text-muted small">Total Seats</div>
                                </td>
                                <td class="fw-bold text-success">${{ number_format($ride->price_per_seat, 2) }}</td>
                                <td>
                                    @if ($ride->luggage_allowed)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="status-badge status-{{ $ride->status }}">
                                        {{ ucfirst($ride->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.rides.show', $ride) }}"
                                            class="btn btn-primary btn-sm action-btn" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        {{-- <a href="{{ route('admin.rides.edit', $ride) }}"
                                            class="btn btn-outline-secondary btn-sm action-btn" title="Edit Ride">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.rides.destroy', $ride) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm action-btn"
                                                onclick="return confirm('Are you sure you want to delete this ride?')"
                                                title="Delete Ride">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form> --}}
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
            // Initialize DataTable with proper configuration
            const table = $('#ridesTable').DataTable({
                "pageLength": 10,
                "lengthMenu": [10, 25, 50, 100],
                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                "language": {
                    "zeroRecords": "No matching rides found",
                    "info": "Showing _START_ to _END_ of _TOTAL_ rides",
                    "infoEmpty": "No rides available",
                    "infoFiltered": "(filtered from _MAX_ total rides)",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    },
                    "lengthMenu": "Show _MENU_ entries"
                },
                "columnDefs": [{
                        "orderable": true,
                        "targets": [0, 4, 5, 6, 8]
                    }, // Make specific columns sortable
                    {
                        "orderable": false,
                        "targets": [1, 2, 3, 7, 9]
                    } // Make other columns not sortable
                ],
                "order": [
                    [0, 'desc']
                ] // Default sort by Ride ID descending
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
