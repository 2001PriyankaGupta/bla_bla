@extends('admin.layouts.master')

@section('title', 'Ride Details - ' . $ride->id)

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

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
                                    ₹ {{ number_format($totalRevenue, 2) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-rupee-sign fa-2x text-gray-300"></i>
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
                                    <p class="form-control-plaintext">₹ {{ number_format($ride->price_per_seat, 2) }}</p>
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
                                    @if ($ride->car->car_photo)
                                        <img src="{{ asset( 'storage/' . $ride->car->car_photo) }}"
                                            alt="{{ $ride->car->car_model }}" class="img-fluid rounded"
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
                                                <p class="form-control-plaintext">{{ $ride->car->car_model }}</p>
                                            </div>
                                            <div class="form-group">
                                                <label class="font-weight-bold text-dark">Brand:</label>
                                                <p class="form-control-plaintext">{{ $ride->car->car_make ?? 'N/A' }}</p>
                                            </div>
                                            <div class="form-group">
                                                <label class="font-weight-bold text-dark">Year:</label>
                                                <p class="form-control-plaintext">{{ $ride->car->car_year ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold text-dark">Color:</label>
                                                <p class="form-control-plaintext">{{ $ride->car->car_color }}</p>
                                            </div>
                                            <div class="form-group">
                                                <label class="font-weight-bold text-dark">License Plate:</label>
                                                <p class="form-control-plaintext">{{ $ride->car->licence_plate }}</p>
                                            </div>
                                            <div class="form-group">
                                                <label class="font-weight-bold text-dark">Verification Status:</label>
                                                <p>
                                                    <span
                                                        class="badge bg-{{ $ride->car->license_verified ? 'success' : 'warning' }}">
                                                        {{ $ride->car->license_verified ? 'Verified' : 'Pending' }}
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
                           
                            <button class="btn btn-danger btn-block" onclick="cancelRide()">
                                <i class="fas fa-times-circle mr-2"></i> Delete Ride
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
                <!-- <div class="card shadow mb-4">
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
                                            <i class="fas fa-rupee-sign mr-1"></i>
                                            ₹ {{ number_format($booking->total_price, 2) }}
                                        </p>
                                    </div>
                                    <div>
                                    @php
                                        $badgeClass = match($booking->status) {
                                            'pending' => 'warning',
                                            'confirmed' => 'success',
                                            'cancelled' => 'danger',
                                            'completed' => 'info',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge badge-{{ $badgeClass }}">
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
                </div> -->

                <!-- Map Preview Card -->
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Route Map</h6>
                    </div>
                    <div class="card-body">
                        <div id="mapPreview" style="height: 300px; width: 100%;" class="rounded border">
                            <div class="h-100 d-flex align-items-center justify-content-center bg-light">
                                <div class="text-center">
                                    <div class="spinner-border text-primary mb-2" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="text-muted small mb-0">Loading Map...</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 p-2 bg-light rounded border">
                            <div class="row text-center small">
                                <div class="col-6 border-end">
                                    <div class="text-muted mb-1">Estimated Distance</div>
                                    <div class="fw-bold text-primary h6 mb-0" id="routeDistance">Calculating...</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted mb-1">Estimated Duration</div>
                                    <div class="fw-bold text-primary h6 mb-0" id="routeDuration">Calculating...</div>
                                </div>
                            </div>
                        </div>
                    </div>
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
        // Google Maps Initialization
        function initMap() {
            const directionsService = new google.maps.DirectionsService();
            const directionsRenderer = new google.maps.DirectionsRenderer();
            const map = new google.maps.Map(document.getElementById("mapPreview"), {
                zoom: 7,
                center: { lat: 20.5937, lng: 78.9629 }, // Center of India
            });
            directionsRenderer.setMap(map);

            const origin = "{{ $ride->pickup_point }}";
            const destination = "{{ $ride->drop_point }}";

            if (origin && destination) {
                calculateAndDisplayRoute(directionsService, directionsRenderer, origin, destination);
            }
        }

        function calculateAndDisplayRoute(directionsService, directionsRenderer, origin, destination) {
            directionsService.route(
                {
                    origin: origin,
                    destination: destination,
                    travelMode: google.maps.TravelMode.DRIVING,
                },
                (response, status) => {
                    if (status === "OK") {
                        directionsRenderer.setDirections(response);
                        const route = response.routes[0].legs[0];
                        document.getElementById('routeDistance').innerText = route.distance.text;
                        document.getElementById('routeDuration').innerText = route.duration.text;
                    } else {
                        document.getElementById('mapPreview').innerHTML = `
                            <div class="h-100 d-flex align-items-center justify-content-center bg-light text-center p-3">
                                <div>
                                    <i class="fas fa-exclamation-triangle text-warning fa-2x mb-2"></i>
                                    <p class="text-muted small mb-0">Directions request failed due to ${status}. Or locations not found on Google Maps.</p>
                                </div>
                            </div>
                        `;
                        document.getElementById('routeDistance').innerText = 'N/A';
                        document.getElementById('routeDuration').innerText = 'N/A';
                    }
                }
            );
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&callback=initMap" async defer></script>

    <script>
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
                        if (data.status === 'success') {
                            alert(data.message);
                        } else {
                            alert('Warning: ' + data.message);
                        }
                    })
                    .catch(error => {
                        alert('Error sending reminder');
                    });
            }
        }

        function cancelRide() {
            if (confirm('Are you sure you want to delete this ride? This action cannot be undone.')) {
                // Submit the delete form
                document.getElementById('deleteForm').submit();
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
