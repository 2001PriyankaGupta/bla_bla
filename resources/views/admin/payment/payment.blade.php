@extends('admin.layouts.master')
@section('title')
    Payment Management
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

    .payment-management-container {
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
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--primary-color);
        margin-bottom: 8px;
    }

    .stats-label {
        color: black;
        font-size: 16px;
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

    .payment-table {
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

    .status-completed {
        background: linear-gradient(135deg, #27ae60, #2ecc71);
        color: white;
    }

    .status-refunded {
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

    .btn-outline-primary {
        border: 2px solid var(--primary-color);
        color: var(--primary-color);
        background: transparent;
    }

    .btn-outline-primary:hover {
        background: var(--primary-color);
        color: white;
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

    .export-btn {
        background: linear-gradient(135deg, #6c757d, #495057);
        border: none;
        color: white;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .export-btn:hover {
        background: linear-gradient(135deg, #495057, #343a40);
        transform: translateY(-2px);
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

    <body>
        <div class="payment-management-container">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h1 class="h3 mb-0">Payment Management</h1>
                        <p class="mb-0 text-muted">Monitor payments, commissions, and transactions</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="{{ route('admin.payment.report') }}" class="btn btn-primary">
                            <i class="fas fa-download me-2"></i>Generate Monthly Report
                        </a>
                    </div>
                </div>
            </div>

            <!-- Summary Stats -->
            <div class="row">
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-label">Total Payouts to Drivers</div>
                        <div class="stats-number">Rs{{ number_format($driverPayouts, 2) }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-label">Platform Commission</div>
                        <div class="stats-number">Rs{{ number_format($platformCommission, 2) }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-label">Total Refunds</div>
                        <div class="stats-number">Rs{{ number_format($totalRefunds, 2) }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-label">Tax Collected</div>
                        <div class="stats-number">Rs{{ number_format($taxCollected, 2) }}</div>
                    </div>
                </div>
            </div>

            <!-- Filter Section Removed as per request -->

            <!-- Payment Table -->
            <div class="payment-table">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Transaction History</h5>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    
                    <a href="{{ route('admin.payment.export') }}" class="export-btn">
                        <i class="fas fa-file-csv me-2"></i>Export CSV
                    </a>
                </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="paymentTable">
                        <thead>
                            <tr>
                                <th>Txn ID</th>
                                <th>Ride ID</th>
                                <th>User</th>
                                <th>Amount</th>
                                <th>Mode</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                            <tr>
                                <td>{{ $payment->transaction_id ?? 'TXN-'.$payment->id }}</td>
                                <td>
                                    @if($payment->booking && $payment->booking->ride)
                                        RD-{{ $payment->booking->ride->id }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($payment->user && $payment->user->profile_picture)
                                            <!-- Assuming profile_picture is a path stored in DB -->
                                            <img src="{{ asset('storage/' . $payment->user->profile_picture) }}" class="rounded-circle me-2" width="30" height="30" style="object-fit:cover;" alt="Avatar" onerror="this.style.display='none'">
                                        @endif
                                        {{ $payment->user ? $payment->user->name : 'Unknown User' }}
                                    </div>
                                </td>
                                <td>Rs{{ number_format($payment->amount, 2) }}</td>
                                <td>{{ ucfirst($payment->payment_method ?? 'Online') }}</td>
                                <td>
                                    @if(strtolower($payment->status) == 'captured' || strtolower($payment->status) == 'completed' || strtolower($payment->status) == 'success')
                                        <span class="status-badge status-completed">Completed</span>
                                    @elseif(strtolower($payment->status) == 'refunded')
                                        <span class="status-badge status-refunded">Refunded</span>
                                    @elseif(strtolower($payment->status) == 'failed')
                                        <span class="status-badge bg-danger text-white">Failed</span>
                                    @else
                                        <span class="status-badge bg-warning text-dark">{{ ucfirst($payment->status) }}</span>
                                    @endif
                                </td>
                                <td>{{ $payment->created_at->format('M d, Y H:i') }}</td>
                                <td>
                                    <button class="action-btn btn-primary view-details-btn" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#paymentDetailsModal"
                                        data-transaction="{{ $payment->transaction_id ?? 'TXN-'.$payment->id }}"
                                        data-user="{{ $payment->user ? $payment->user->name : 'N/A' }}"
                                        data-amount="Rs{{ number_format($payment->amount, 2) }}"
                                        data-date="{{ $payment->created_at->format('M d, Y H:i') }}"
                                        data-status="{{ $payment->status }}"
                                        data-ride="{{ $payment->booking && $payment->booking->ride ? 'RD-'.$payment->booking->ride->id : 'N/A' }}"
                                        data-method="{{ $payment->payment_method }}"
                                        title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">No payment records found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Payment Details Modal -->
        <div class="modal fade" id="paymentDetailsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-bottom-0">
                        <h5 class="modal-title">Transaction Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="text-center mb-4">
                            <h3 class="text-primary fw-bold mb-1" id="modalAmount"></h3>
                            <span class="badge bg-success rounded-pill" id="modalStatus"></span>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="text-muted small">Transaction ID</label>
                                <p class="fw-bold mb-0" id="modalTxnId"></p>
                            </div>
                            <div class="col-6">
                                <label class="text-muted small">Date & Time</label>
                                <p class="fw-bold mb-0" id="modalDate"></p>
                            </div>
                            <div class="col-6">
                                <label class="text-muted small">User</label>
                                <p class="fw-bold mb-0" id="modalUser"></p>
                            </div>
                            <div class="col-6">
                                <label class="text-muted small">Payment Method</label>
                                <p class="fw-bold mb-0" id="modalMethod"></p>
                            </div>
                             <div class="col-12">
                                <label class="text-muted small">Ride ID</label>
                                <p class="fw-bold mb-0" id="modalRide"></p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Close</button>
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
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize DataTable with proper configuration
                const table = $('#paymentTable').DataTable({
                    "pageLength": 10,
                    "lengthMenu": [10, 25, 50, 100],
                    "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                    "language": {
                        "zeroRecords": "No matching transactions found",
                        "info": "Showing _START_ to _END_ of _TOTAL_ transactions",
                        "infoEmpty": "No transactions available",
                        "infoFiltered": "(filtered from _MAX_ total transactions)",
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
                            "targets": [0, 3, 6]
                        }, // Make specific columns sortable
                        {
                            "orderable": false,
                            "targets": [1, 2, 4, 5, 7]
                        } // Make other columns not sortable
                    ],
                    "order": [
                        [0, 'desc']
                    ] // Default sort by Txn ID descending
                });

                // Custom search functionality
                $('#customSearchInput').on('keyup', function(e) {
                    if (e.key === "Enter" || e.keyCode === 13) {
                        table.search(this.value).draw();
                    }
                });

                // Handle Modal Data Population
                $('.view-details-btn').on('click', function() {
                    const btn = $(this);
                    $('#modalTxnId').text(btn.data('transaction'));
                    $('#modalUser').text(btn.data('user'));
                    $('#modalAmount').text(btn.data('amount'));
                    $('#modalDate').text(btn.data('date'));
                    $('#modalStatus').text(btn.data('status'));
                    $('#modalRide').text(btn.data('ride'));
                    $('#modalMethod').text(btn.data('method'));
                });
            });
        </script>
    @endsection
