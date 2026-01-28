@extends('admin.layouts.master')
@section('title')
    Fare & Promo Management
@endsection

@section('css')
    <style>
        :root {
            --primary-color: #249722;
            --primary-light: #e8f5e8;
            --primary-dark: #1e7a1c;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
        }

        .card {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .card-header {
            border-radius: 0.5rem 0.5rem 0 0 !important;
            padding: 1rem 1.25rem;
            font-weight: 600;
        }

        .bg-primary {
            background-color: var(--primary-color) !important;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .btn-success {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-success:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .badge.bg-primary {
            background-color: var(--primary-color) !important;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            color: #5a5c69;
        }

        .table>:not(caption)>*>* {
            padding: 0.75rem 0.6rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(36, 151, 34, 0.25);
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .page-title {
            color: #5a5c69;
            font-weight: 700;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #e3e6f0;
            padding-bottom: 0.5rem;
        }

        .section-divider {
            border-top: 2px solid #e3e6f0;
            margin: 2rem 0;
        }

        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .status-badge {
            font-size: 0.75rem;
        }

        .promo-code-badge {
            font-size: 0.8rem;
        }

        .usage-progress {
            height: 6px;
            margin-top: 5px;
        }

        .expired {
            color: var(--danger-color);
            font-weight: 600;
        }

        .expiring-soon {
            color: var(--warning-color);
            font-weight: 600;
        }

        .stat-card {
            border-left: 4px solid var(--primary-color);
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .stat-label {
            font-size: 0.875rem;
            color: #858796;
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
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-4">
            <h1 class="h3 mb-0 text-gray-800 page-title">
                Fare & Promo Management
            </h1>
        </div>

        <!-- Fare Configuration Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-white d-flex justify-content-between align-items-center"
                        style="background-color: #e0e3e0;">
                        <h5 class="card-title mb-0" style="color: black;"><i class="fas fa-table me-2"></i>Fare Configuration
                        </h5>
                        <span class="badge bg-light text-dark">
                            <i class="fas fa-info-circle me-1"></i>Current Rates
                        </span>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.fare-promo.save-fare') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="base_fare" class="form-label fw-semibold">Base Fare ($)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                        <input type="number" step="0.01" class="form-control" id="base_fare"
                                            name="base_fare" value="{{ old('base_fare', $fareConfig->base_fare ?? 0) }}"
                                            required>
                                    </div>
                                    <small class="form-text text-muted">Initial charge for all rides</small>
                                </div>
                                <div class="col-md-4">
                                    <label for="per_km_charge" class="form-label fw-semibold">Per Km Charge ($)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-road"></i></span>
                                        <input type="number" step="0.01" class="form-control" id="per_km_charge"
                                            name="per_km_charge"
                                            value="{{ old('per_km_charge', $fareConfig->per_km_charge ?? 0) }}" required>
                                    </div>
                                    <small class="form-text text-muted">Charge per kilometer traveled</small>
                                </div>
                                <div class="col-md-4">
                                    <label for="waiting_fee" class="form-label fw-semibold">Waiting Fee ($/min)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                        <input type="number" step="0.01" class="form-control" id="waiting_fee"
                                            name="waiting_fee"
                                            value="{{ old('waiting_fee', $fareConfig->waiting_fee ?? 0) }}" required>
                                    </div>
                                    <small class="form-text text-muted">Fee per minute of waiting time</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="home_pickup_fee" class="form-label fw-semibold">Home Pickup Fee
                                        ($)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-home"></i></span>
                                        <input type="number" step="0.01" class="form-control" id="home_pickup_fee"
                                            name="home_pickup_fee"
                                            value="{{ old('home_pickup_fee', $fareConfig->home_pickup_fee ?? 0) }}"
                                            required>
                                    </div>
                                    <small class="form-text text-muted">Additional fee for home pickup service</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="night_holiday_surcharge" class="form-label fw-semibold">Night / Holiday
                                        Surcharge ($)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-moon"></i></span>
                                        <input type="number" step="0.01" class="form-control"
                                            id="night_holiday_surcharge" name="night_holiday_surcharge"
                                            value="{{ old('night_holiday_surcharge', $fareConfig->night_holiday_surcharge ?? 0) }}"
                                            required>
                                    </div>
                                    <small class="form-text text-muted">Additional charge for night/holiday rides</small>
                                </div>
                            </div>
                            <div class="mt-4 pt-3 border-top" style="text-align: right">
                                <button type="submit" class="btn px-4"
                                    style="background-color: var(--primary-color); color: #fff;">
                                    <i class="fas fa-save me-2"></i>Save / Update Fare
                                </button>
                                <button type="reset" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-undo me-2"></i>Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-divider"></div>

        <!-- Promo Codes Section -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-white d-flex justify-content-between align-items-center"
                        style="background-color: var(--primary-color);">
                        <h5 class="card-title mb-0"><i class="fas fa-tags me-2"></i>Promo Codes Management</h5>
                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal"
                            data-bs-target="#promoModal">
                            <i class="fas fa-plus me-1"></i>Add Promo Code
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="promoCodesTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Promo Code</th>
                                        <th>Type</th>
                                        <th>Discount Value</th>
                                        <th>Usage</th>
                                        <th>Expiry Date</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($promoCodes as $promo)
                                        @php
                                            $usagePercentage = $promo->usage_limit
                                                ? ($promo->used_count / $promo->usage_limit) * 100
                                                : 0;
                                            $isExpired = $promo->expiry_date < now();
                                            $isExpiringSoon =
                                                !$isExpired && $promo->expiry_date->diffInDays(now()) <= 7;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span
                                                        class="badge bg-primary promo-code-badge me-2">{{ $promo->promo_code }}</span>
                                                    @if ($isExpired)
                                                        <span class="badge bg-danger">Expired</span>
                                                    @elseif($isExpiringSoon)
                                                        <span class="badge bg-warning text-dark">Expiring Soon</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge {{ $promo->type == 'percentage' ? 'bg-info' : 'bg-warning text-dark' }} status-badge">
                                                    {{ $promo->type == 'percentage' ? 'Percentage' : 'Fixed Amount' }}
                                                </span>
                                            </td>
                                            <td class="fw-semibold">
                                                {{ $promo->type == 'percentage' ? $promo->discount_value . '%' : '$' . number_format($promo->discount_value, 2) }}
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span>{{ $promo->used_count }} /
                                                        {{ $promo->usage_limit ?: '∞' }}</span>
                                                    @if ($promo->usage_limit)
                                                        <div class="progress usage-progress">
                                                            <div class="progress-bar {{ $usagePercentage >= 80 ? 'bg-danger' : ($usagePercentage >= 50 ? 'bg-warning' : 'bg-success') }}"
                                                                role="progressbar"
                                                                style="width: {{ min($usagePercentage, 100) }}%">
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span
                                                    class="{{ $isExpired ? 'expired' : ($isExpiringSoon ? 'expiring-soon' : '') }}">
                                                    {{ $promo->expiry_date->format('M d, Y') }}
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge {{ $promo->is_active ? 'bg-success' : 'bg-secondary' }} status-badge">
                                                    {{ $promo->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td class="text-center action-buttons">
                                                <button class="btn btn-sm btn-outline-primary edit-promo"
                                                    data-id="{{ $promo->id }}" data-bs-toggle="modal"
                                                    data-bs-target="#promoModal" title="Edit Promo Code">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('admin.fare-promo.delete-promo', $promo->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Are you sure you want to delete this promo code?')"
                                                        title="Delete Promo Code">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="fas fa-tags fa-3x mb-3"></i>
                                                    <h5>No promo codes found</h5>
                                                    <p>Click "Add New Promo Code" to create your first promo code.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Promo Code Modal -->
    <div class="modal fade" id="promoModal" tabindex="-1" aria-labelledby="promoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-white" style="background-color: var(--primary-color);">
                    <h5 class="modal-title" id="modalTitle" style="color:white;">Add New Promo Code</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="promoForm" action="{{ route('admin.fare-promo.save-promo') }}" method="POST">
                    @csrf
                    <input type="hidden" id="promoId" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="promo_code" class="form-label fw-semibold">Promo Code</label>
                            <input type="text" class="form-control" id="promo_code" name="promo_code"
                                placeholder="e.g., SUMMER20" required>
                            <div class="form-text">Enter a unique promo code (letters and numbers only)</div>
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label fw-semibold">Discount Type</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="fixed">Fixed Amount ($ off)</option>
                                <option value="percentage">Percentage (% off)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="discount_value" class="form-label fw-semibold">Discount Value</label>
                            <div class="input-group">
                                <span class="input-group-text" id="discountPrefix">$</span>
                                <input type="number" step="0.01" class="form-control" id="discount_value"
                                    name="discount_value" placeholder="0.00" required>
                            </div>
                            <div class="form-text" id="discountHelp">Enter the discount amount in dollars</div>
                        </div>
                        <div class="mb-3">
                            <label for="usage_limit" class="form-label fw-semibold">Usage Limit</label>
                            <input type="number" class="form-control" id="usage_limit" name="usage_limit"
                                placeholder="Leave empty for unlimited">
                            <div class="form-text">Maximum number of times this promo can be used</div>
                        </div>
                        <div class="mb-3">
                            <label for="expiry_date" class="form-label fw-semibold">Expiry Date</label>
                            <input type="date" class="form-control" id="expiry_date" name="expiry_date" required>
                            <div class="form-text">Promo code will automatically expire after this date</div>
                        </div>
                        <div class="mb-3 form-check form-switch">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                value="1" checked>
                            <label class="form-check-label fw-semibold" for="is_active">Active Promo Code</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Save Promo Code
                        </button>
                    </div>
                </form>
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
        $(document).ready(function() {
            // Initialize DataTable
            $('#promoCodesTable').DataTable({
                "pageLength": 10,
                "ordering": true,
                "responsive": true,
                "columns": [
                    null, // Promo Code
                    null, // Type
                    null, // Discount Value
                    null, // Usage
                    null, // Expiry Date
                    null, // Status
                    {
                        "orderable": false
                    } // Actions
                ],
                "language": {
                    "search": "Search promo codes:",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ promo codes",
                    "paginate": {
                        "previous": "<i class='fas fa-chevron-left'></i>",
                        "next": "<i class='fas fa-chevron-right'></i>"
                    }
                }
            });

            // Handle discount type change
            $('#type').change(function() {
                const type = $(this).val();
                const prefix = type === 'percentage' ? '' : '$';
                const suffix = type === 'percentage' ? '%' : '';
                const helpText = type === 'percentage' ?
                    'Enter the discount percentage (0-100)' :
                    'Enter the discount amount in dollars';

                $('#discountPrefix').text(prefix);
                $('#discountHelp').text(helpText);

                // Update placeholder
                $('#discount_value').attr('placeholder', type === 'percentage' ? '0' : '0.00');
            });

            // Edit promo code
            $('.edit-promo').click(function() {
                const promoId = $(this).data('id');

                fetch(`/admin/fare-promo/get-promo/${promoId}`)
                    .then(response => response.json())
                    .then(data => {
                        $('#modalTitle').text('Edit Promo Code');
                        $('#promoId').val(data.id);
                        $('#promo_code').val(data.promo_code);
                        $('#type').val(data.type).trigger('change');
                        $('#discount_value').val(data.discount_value);
                        $('#usage_limit').val(data.usage_limit);

                        // Fix date formatting issue
                        let expiryDate = data.expiry_date;
                        console.log('Original expiry_date:', expiryDate); // Debug log

                        // Handle different date formats
                        if (expiryDate.includes(' ')) {
                            expiryDate = expiryDate.split(' ')[0]; // Remove time part
                        }

                        // Convert to YYYY-MM-DD format
                        const dateObj = new Date(expiryDate);
                        const formattedDate = dateObj.toISOString().split('T')[0];
                        console.log('Formatted date:', formattedDate); // Debug log

                        $('#expiry_date').val(formattedDate);
                        $('#is_active').prop('checked', data.is_active == 1);
                    })
                    .catch(error => {
                        console.error('Error fetching promo code:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load promo code data'
                        });
                    });
            });

            // Reset modal when closed
            $('#promoModal').on('hidden.bs.modal', function() {
                $('#modalTitle').text('Add New Promo Code');
                $('#promoForm')[0].reset();
                $('#promoId').val('');
                $('#type').trigger('change');
                $('#is_active').prop('checked', true);

                // Set default expiry date to tomorrow
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                $('#expiry_date').val(tomorrow.toISOString().split('T')[0]);
            });

            // Set minimum date for expiry date to tomorrow for new entries
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            $('#expiry_date').attr('min', tomorrow.toISOString().split('T')[0]);

            // Set default expiry date to tomorrow for new entries
            $('#promoModal').on('show.bs.modal', function() {
                if (!$('#promoId').val()) {
                    $('#expiry_date').val(tomorrow.toISOString().split('T')[0]);
                }
            });

            // Form validation
            $('#promoForm').submit(function(e) {
                const discountValue = parseFloat($('#discount_value').val());
                const type = $('#type').val();

                if (type === 'percentage' && (discountValue < 0 || discountValue > 100)) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Discount',
                        text: 'Percentage discount must be between 0 and 100'
                    });
                    return false;
                }

                // Validate expiry date
                const expiryDate = new Date($('#expiry_date').val());
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                if (expiryDate <= today) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Expiry Date',
                        text: 'Expiry date must be in the future'
                    });
                    return false;
                }

                return true;
            });

            // SweetAlert for success messages
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

        // Utility function to format date for input field
        function formatDateForInput(dateString) {
            if (!dateString) return '';

            let date = new Date(dateString);

            // Handle invalid dates
            if (isNaN(date.getTime())) {
                console.error('Invalid date:', dateString);
                return '';
            }

            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');

            return `${year}-${month}-${day}`;
        }
    </script>
@endsection
