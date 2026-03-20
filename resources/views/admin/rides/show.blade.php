@extends('admin.layouts.master')

@section('title', 'Ride Details - ' . $ride->id)

@section('content')
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<style>
    :root {
        --primary-green: #198754;
        --secondary-green: #20c997;
        --light-green: #e9f7ef;
    }
    .card-header {
        background: var(--light-green) !important;
        border-bottom: 1px solid #d1e7dd;
    }
    .text-primary { color: var(--primary-green) !important; }
    .text-success { color: var(--primary-green) !important; }
    .btn-primary { 
        background-color: var(--primary-green) !important; 
        border-color: var(--primary-green) !important; 
    }
    .btn-warning { 
        background-color: #ffc107 !important; 
        border-color: #ffc107 !important;
        color: #000 !important;
    }
    .badge-primary { background-color: var(--primary-green) !important; }
    .border-left-primary { border-left: .25rem solid var(--primary-green) !important; }
    .border-left-success { border-left: .25rem solid var(--secondary-green) !important; }
</style>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Ride Details</h1>
        <div>
            <a href="{{ route('admin.rides.index') }}" class="btn btn-outline-success">
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
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Seats</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $ride->total_seats }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Available Seats</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $availableSeats }}
                            </div>
                        </div>
                        <div class="col-auto"><i class="fas fa-chair fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2" style="border-left: .25rem solid #ffc107;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1" style="color: #ffc107;">Booked Seats</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $ride->total_seats - $availableSeats }}
                            </div>
                        </div>
                        <div class="col-auto"><i class="fas fa-ticket-alt fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2" style="border-left: .25rem solid #17a2b8;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1" style="color: #17a2b8;">Total Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($totalRevenue, 2) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-rupee-sign fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ride Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Pickup:</strong> {{ $ride->pickup_point }}</p>
                            <p><strong>Drop:</strong> {{ $ride->drop_point }}</p>
                            <p><strong>Date:</strong> {{ $ride->date_time->format('M d, Y h:i A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Price:</strong> ₹{{ number_format($ride->price_per_seat, 2) }}</p>
                            <p><strong>Status:</strong> <span class="badge bg-success">{{ ucfirst($ride->status) }}</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Driver & Car Details -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Driver Information</h6>
                        </div>
                        <div class="card-body">
                            @if($ride->driver)
                            <div class="d-flex align-items-center mb-3">
                                @if($ride->driver->profile_picture)
                                    <img src="{{ Storage::url($ride->driver->profile_picture) }}" class="rounded-circle me-3" width="60" height="60" alt="Driver" style="object-fit:cover; margin-right: 15px;">
                                @else
                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 60px; height: 60px; margin-right: 15px;">
                                        <i class="fas fa-user fa-2x"></i>
                                    </div>
                                @endif
                                <div>
                                    <h5 class="mb-0">{{ $ride->driver->name }}</h5>
                                    <p class="mb-0 text-muted">{{ $ride->driver->email }}</p>
                                    <p class="mb-0 text-muted">{{ $ride->driver->phone_number }}</p>
                                </div>
                            </div>
                            @else
                                <p class="text-muted">No driver assigned.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Car Details</h6>
                        </div>
                        <div class="card-body">
                            @if($ride->car)
                            <p class="mb-1"><strong>Brand:</strong> {{ $ride->car->car_make }}</p>
                            <p class="mb-1"><strong>Model:</strong> {{ $ride->car->car_model }}</p>
                            <p class="mb-1"><strong>License Plate:</strong> {{ $ride->car->licence_plate }}</p>
                            <p class="mb-1"><strong>Color:</strong> {{ $ride->car->car_color }}</p>
                            <p class="mb-0"><strong>Year:</strong> {{ $ride->car->car_year }}</p>
                            @else
                                <p class="text-muted">No car specifically assigned.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stop Points -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Stop Points</h6>
                </div>
                <div class="card-body">
                    @if($ride->stopPoints->count() > 0)
                        <table class="table table-bordered">
                            <thead class="bg-light">
                                <tr><th>City</th><th>Price from Pickup</th></tr>
                            </thead>
                            <tbody>
                                @foreach($ride->stopPoints as $stop)
                                    <tr><td>{{ $stop->city_name }}</td><td>₹{{ number_format($stop->price_from_pickup, 2) }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted mb-0">No intermediate stops.</p>
                    @endif
                </div>
            </div>
            
            <!-- Passengers & Bookings -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Passengers & Bookings</h6>
                    <span class="badge bg-primary">{{ $totalBookings }} Total Bookings</span>
                </div>
                <div class="card-body">
                    @if($ride->bookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Passenger</th>
                                        <th>Seats</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ride->bookings as $booking)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($booking->user && $booking->user->profile_picture)
                                                        <img src="{{ Storage::url($booking->user->profile_picture) }}" class="rounded-circle" width="30" height="30" alt="User" style="object-fit:cover; margin-right: 10px;">
                                                    @else
                                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 30px; height: 30px; margin-right: 10px;">
                                                            <i class="fas fa-user fa-sm"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <strong>{{ $booking->user ? $booking->user->name : 'Unknown User' }}</strong>
                                                        <br><small class="text-muted">{{ $booking->user ? $booking->user->phone_number : '' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle">{{ $booking->seats_booked }}</td>
                                            <td class="align-middle">₹{{ number_format($booking->total_price, 2) }}</td>
                                            <td class="align-middle">
                                                @if($booking->status == 'confirmed')
                                                    <span class="badge bg-success">Confirmed</span>
                                                @elseif($booking->status == 'cancelled')
                                                    <span class="badge bg-danger">Cancelled</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">{{ ucfirst($booking->status) }}</span>
                                                @endif
                                            </td>
                                            <td class="align-middle">{{ $booking->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-3 mb-0">No bookings yet.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.rides.edit', $ride->id) }}" class="btn btn-warning mb-2" style="background-color: #ffc107 !important; border-color: #ffc107 !important; color: #000 !important;">
                            <i class="fas fa-edit"></i> Edit Ride Details
                        </a>
                        <form action="{{ route('admin.rides.destroy', $ride->id) }}" method="POST" onsubmit="return confirm('Delete this ride?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash"></i> Delete Ride
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Seat Map Visualization -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Seat Layout Map</h6>
                </div>
                <div class="card-body text-center">
                    <p class="text-muted small mb-3">Visual representation of booked and available seats.</p>
                    <div class="d-flex flex-wrap justify-content-center border rounded p-3 bg-light" style="gap: 15px;">
                        @php
                            $bookedSeatsCount = $ride->total_seats - $availableSeats;
                        @endphp
                        @for($i = 1; $i <= $ride->total_seats; $i++)
                            <div class="d-flex flex-column align-items-center">
                                @if($i <= $bookedSeatsCount)
                                    <!-- Booked Seat -->
                                    <i class="fas fa-couch fa-2x mb-1" style="color: #dc3545;"></i>
                                    <small class="fw-bold" style="color: #dc3545;">S{{ $i }}</small>
                                @else
                                    <!-- Available Seat -->
                                    <i class="fas fa-couch fa-2x mb-1" style="color: #198754;"></i>
                                    <small class="fw-bold" style="color: #198754;">S{{ $i }}</small>
                                @endif
                            </div>
                        @endfor
                    </div>
                    <div class="d-flex justify-content-center mt-3" style="gap: 20px;">
                        <span class="d-flex align-items-center"><i class="fas fa-couch mr-1" style="color: #dc3545;"></i> Booked</span>
                        <span class="d-flex align-items-center"><i class="fas fa-couch mr-1" style="color: #198754;"></i> Available</span>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#bookingsTable').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "pageLength": 10,
            "order": [[4, 'desc']]
        });
    });
</script>
@endsection

