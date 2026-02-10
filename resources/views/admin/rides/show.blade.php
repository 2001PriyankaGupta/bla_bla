@extends('admin.layouts.master')

@section('title', 'Ride Details - ' . $ride->id)

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

<style>
    .passenger-card {
        border: 1px solid #e3e6f0;
        border-radius: 0.35rem;
        padding: 1rem;
        margin-bottom: 1rem;
        background: #f8f9fc;
    }

    .passenger-card:hover {
        background: #e3e6f0;
        transition: background 0.3s;
    }

    .card-header {
        background: #f8f9fc;
        border-bottom: 1px solid #e3e6f0;
    }

    .badge {
        font-size: 0.75em;
        padding: 0.35em 0.65em;
    }

    .btn-block {
        margin-bottom: 0.5rem;
    }
</style>

@section('content')
    <div class="container-fluid mt-4">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">Ride Details</h1>
            <div>
                <a href="{{ route('admin.rides.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Rides
                </a>
                <button class="btn btn-danger" onclick="confirmDelete()">
                    <i class="fas fa-trash"></i> Delete Ride
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Seats
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $ride->total_seats }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Available Seats
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $availableSeats }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chair fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Total Bookings
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $totalBookings }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Total Revenue
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    ${{ number_format($totalRevenue, 2) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row">
            <!-- Ride Details Card -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Ride Information</h6>
                        <span class="badge badge-{{ $ride->isFull() ? 'danger' : 'success' }}">
                            {{ $ride->isFull() ? 'FULL' : 'AVAILABLE' }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">Ride ID:</label>
                                    <p class="form-control-plaintext">#{{ str_pad($ride->id, 6, '0', STR_PAD_LEFT) }}</p>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">Pickup Point:</label>
                                    <p class="form-control-plaintext">{{ $ride->pickup_point }}</p>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">Drop Point:</label>
                                    <p class="form-control-plaintext">{{ $ride->drop_point }}</p>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">Date & Time:</label>
                                    <p class="form-control-plaintext">
                                        {{ $ride->date_time->format('d M Y, h:i A') }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">Price per Seat:</label>
                                    <p class="form-control-plaintext">${{ number_format($ride->price_per_seat, 2) }}</p>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">Total Seats:</label>
                                    <p class="form-control-plaintext">{{ $ride->total_seats }}</p>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">Luggage Allowed:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge badge-{{ $ride->luggage_allowed ? 'success' : 'danger' }}">
                                            {{ $ride->luggage_allowed ? 'Yes' : 'No' }}
                                        </span>
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">Created At:</label>
                                    <p class="form-control-plaintext">
                                        {{ $ride->created_at->format('d M Y, h:i A') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Driver Details Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Driver Information</h6>
                    </div>
                    <div class="card-body">
                        @if ($ride->driver)
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    @if ($ride->driver->profile_picture)
                                        <img src="{{ asset('storage/' . $ride->driver->profile_picture) }}"
                                            alt="{{ $ride->driver->name }}" class="rounded-circle img-fluid"
                                            style="width: 80px; height: 80px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center"
                                            style="width: 80px; height: 80px;">
                                            <i class="fas fa-user fa-2x text-white"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col">
                                    <h5 class="mb-1">{{ $ride->driver->name }}</h5>
                                    <p class="text-muted mb-1">
                                        <i class="fas fa-envelope mr-1"></i> {{ $ride->driver->email }}
                                    </p>
                                    <p class="text-muted mb-0">
                                        <i class="fas fa-phone mr-1"></i> {{ $ride->driver->phone ?? 'Not provided' }}
                                    </p>
                                </div>
                                <div class="col-auto">
                                    <a href="{{ route('admin.users.show', $ride->driver->id) }}"
                                        class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-external-link-alt"></i> View Profile
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No driver assigned to this ride</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Car Details Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Car Information</h6>
                    </div>
                    <div class="card-body">
                        @if ($ride->car)
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    @if ($ride->car->car_image)
                                        <img src="{{ asset('storage/' . $ride->car->car_image) }}"
                                            alt="{{ $ride->car->model }}" class="img-fluid rounded"
                                            style="max-height: 150px;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                            style="height: 150px;">
                                            <i class="fas fa-car fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold text-dark">Model:</label>
                                                <p class="form-control-plaintext">{{ $ride->car->model }}</p>
                                            </div>
                                            <div class="form-group">
                                                <label class="font-weight-bold text-dark">Brand:</label>
                                                <p class="form-control-plaintext">{{ $ride->car->brand ?? 'N/A' }}</p>
                                            </div>
                                            <div class="form-group">
                                                <label class="font-weight-bold text-dark">Year:</label>
                                                <p class="form-control-plaintext">{{ $ride->car->year ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold text-dark">Color:</label>
                                                <p class="form-control-plaintext">{{ $ride->car->color }}</p>
                                            </div>
                                            <div class="form-group">
                                                <label class="font-weight-bold text-dark">License Plate:</label>
                                                <p class="form-control-plaintext">{{ $ride->car->license_plate }}</p>
                                            </div>
                                            <div class="form-group">
                                                <label class="font-weight-bold text-dark">Verification Status:</label>
                                                <p>
                                                    <span
                                                        class="badge badge-{{ $ride->car->is_verified ? 'success' : 'warning' }}">
                                                        {{ $ride->car->is_verified ? 'Verified' : 'Pending' }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-car-crash fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No car assigned to this ride</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar - Bookings & Actions -->
            <div class="col-lg-4">
                <!-- Quick Actions Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.rides.edit', $ride->id) }}" class="btn btn-warning btn-block">
                                <i class="fas fa-edit mr-2"></i> Edit Ride
                            </a>
                            <button class="btn btn-info btn-block" onclick="shareRide()">
                                <i class="fas fa-share-alt mr-2"></i> Share Ride
                            </button>
                            <button class="btn btn-success btn-block" onclick="sendReminder()">
                                <i class="fas fa-bell mr-2"></i> Send Reminder
                            </button>
                            <button class="btn btn-danger btn-block" onclick="cancelRide()">
                                <i class="fas fa-times-circle mr-2"></i> Cancel Ride
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Seat Layout Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Seat Availability Status</h6>
                        <div>
                            @php
                                $confirmedSeatsCount = $ride->bookings->where('status', 'confirmed')->sum('seats_booked');
                                $pendingSeatsCount = $ride->bookings->where('status', 'pending')->sum('seats_booked');
                                $availableSeatsCount = $ride->total_seats - ($confirmedSeatsCount + $pendingSeatsCount);
                            @endphp
                            <span class="badge bg-danger me-1">{{ $confirmedSeatsCount }} Booked</span>
                            <span class="badge bg-warning text-dark me-1">{{ $pendingSeatsCount }} Pending</span>
                            <span class="badge bg-success">{{ $availableSeatsCount }} Free</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-center">
                            @for ($i = 1; $i <= $ride->total_seats; $i++)
                                @php
                                    $seatStatus = 'free';
                                    $seatColor = 'text-success';
                                    $badgeColor = 'bg-success';
                                    $statusText = 'Free';
                                    
                                    if ($i <= $confirmedSeatsCount) {
                                        $seatStatus = 'confirmed';
                                        $seatColor = 'text-danger';
                                        $badgeColor = 'bg-danger';
                                        $statusText = 'Booked';
                                    } elseif ($i <= ($confirmedSeatsCount + $pendingSeatsCount)) {
                                        $seatStatus = 'pending';
                                        $seatColor = 'text-warning';
                                        $badgeColor = 'bg-warning text-dark';
                                        $statusText = 'Pending';
                                    }
                                @endphp
                                <div class="text-center mx-2 mb-3" style="width: 60px;">
                                    <div class="mb-1">
                                        <i class="fas fa-chair fa-2x {{ $seatColor }}"></i>
                                    </div>
                                    <div class="text-dark small fw-bold mb-1">Seat {{ $i }}</div>
                                    <span class="badge {{ $badgeColor }} rounded-pill" style="font-size: 10px;">
                                        {{ $statusText }}
                                    </span>
                                </div>
                            @endfor
                        </div>
                        <div class="mt-3 text-center small text-muted border-top pt-3 d-flex justify-content-center gap-3">
                            <div><i class="fas fa-circle text-success me-1"></i> Available</div>
                            <div><i class="fas fa-circle text-warning me-1"></i> Pending Review</div>
                            <div><i class="fas fa-circle text-danger me-1"></i> Confirmed Booked</div>
                        </div>
                    </div>
                </div>

                <!-- Bookings Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Bookings ({{ $ride->bookings->count() }})</h6>
                        <span class="badge badge-primary">{{ $confirmedBookings }} confirmed</span>
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        @forelse($ride->bookings as $booking)
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="font-weight-bold mb-1">
                                            {{ $booking->user->name ?? 'Unknown User' }}
                                        </h6>
                                        <p class="text-muted small mb-1">
                                            <i class="fas fa-chair mr-1"></i> {{ $booking->seats_booked }} seat(s)
                                        </p>
                                        <p class="text-muted small mb-1">
                                            <i class="fas fa-dollar-sign mr-1"></i>
                                            ${{ number_format($booking->total_price, 2) }}
                                        </p>
                                    </div>
                                    <div>
                                        <span class="badge badge-{{ $this->getStatusBadge($booking->status) }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar mr-1"></i>
                                        {{ $booking->created_at->format('d M Y, h:i A') }}
                                    </small>
                                </div>
                                <div class="mt-2">
                                    @if ($booking->user)
                                        <a href="{{ route('admin.users.show', $booking->user->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View Passenger
                                        </a>
                                    @endif
                                    <button class="btn btn-sm btn-outline-info"
                                        onclick="viewBooking({{ $booking->id }})">
                                        <i class="fas fa-info-circle"></i> Details
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No bookings yet</p>
                                <a href="#" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus mr-1"></i> Create Booking
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Map Preview Card -->
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Route Map</h6>
                    </div>
                    <div class="card-body">
                        <div id="mapPreview" style="height: 200px; background: #f8f9fa;"
                            class="rounded d-flex align-items-center justify-content-center">
                            <div class="text-center">
                                <i class="fas fa-map-marked-alt fa-2x text-muted mb-2"></i>
                                <p class="text-muted small">Map preview would appear here</p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle mr-1"></i>
                                Distance: ~15.2 km • Duration: ~25 min
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this ride?</p>
                    <p class="text-danger"><strong>Warning:</strong> This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.rides.destroy', $ride->id) }}" method="POST" id="deleteForm">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Ride</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete() {
            $('#deleteModal').modal('show');
        }

        function shareRide() {
            // Implement share functionality
            const rideUrl = window.location.href;
            if (navigator.share) {
                navigator.share({
                    title: 'Ride Details',
                    text: 'Check out this ride details',
                    url: rideUrl
                });
            } else {
                navigator.clipboard.writeText(rideUrl);
                alert('Link copied to clipboard!');
            }
        }

        function sendReminder() {
            if (confirm('Send reminder to all passengers?')) {
                // AJAX call to send reminder
                fetch(`/admin/rides/{{ $ride->id }}/reminder`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        alert('Reminder sent successfully!');
                    })
                    .catch(error => {
                        alert('Error sending reminder');
                    });
            }
        }

        function cancelRide() {
            if (confirm('Cancel this ride? All bookings will be refunded.')) {
                // AJAX call to cancel ride
                fetch(`/admin/rides/{{ $ride->id }}/cancel`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        alert('Ride cancelled successfully!');
                        location.reload();
                    })
                    .catch(error => {
                        alert('Error cancelling ride');
                    });
            }
        }

        function viewBooking(bookingId) {
            window.location.href = `/admin/bookings/${bookingId}`;
        }

        // Helper function for status badge colors
        function getStatusBadge(status) {
            const badges = {
                'pending': 'warning',
                'confirmed': 'success',
                'cancelled': 'danger',
                'completed': 'info'
            };
            return badges[status] || 'secondary';
        }
    </script>


@endsection
