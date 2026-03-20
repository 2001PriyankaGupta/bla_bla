@extends('admin.layouts.master')

@section('title', 'Ride Details - ' . $ride->id)

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                                {{ $ride->total_seats - $ride->bookings->where('status', 'confirmed')->sum('seats_booked') }}
                            </div>
                        </div>
                        <div class="col-auto"><i class="fas fa-chair fa-2x text-gray-300"></i></div>
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

            <!-- Stop Points -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Stop Points</h6>
                </div>
                <div class="card-body">
                    @if($ride->stopPoints->count() > 0)
                        <table class="table">
                            <thead>
                                <tr><th>City</th><th>Price</th></tr>
                            </thead>
                            <tbody>
                                @foreach($ride->stopPoints as $stop)
                                    <tr><td>{{ $stop->city_name }}</td><td>₹{{ number_format($stop->price_from_pickup, 2) }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">No intermediate stops.</p>
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
                        <a href="{{ route('admin.rides.edit', $ride->id) }}" class="btn btn-warning">
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
        </div>
    </div>
</div>
@endsection
