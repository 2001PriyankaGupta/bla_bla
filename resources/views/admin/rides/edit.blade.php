@extends('admin.layouts.master')

@section('title', 'Edit Ride #' . $ride->id)
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
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Ride #{{ $ride->id }}</h1>
        <a href="{{ route('admin.rides.show', $ride->id) }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Details
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ride Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.rides.update', $ride->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Pickup Point</label>
                                <input type="text" name="pickup_point" class="form-control" value="{{ old('pickup_point', $ride->pickup_point) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Drop Point</label>
                                <input type="text" name="drop_point" class="form-control" value="{{ old('drop_point', $ride->drop_point) }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Date & Time</label>
                                <input type="datetime-local" name="date_time" class="form-control" value="{{ old('date_time', $ride->date_time->format('Y-m-d\TH:i')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Car & Driver</label>
                                <select name="car_id" class="form-select" required>
                                    @foreach($cars as $car)
                                        <option value="{{ $car->id }}" {{ $ride->car_id == $car->id ? 'selected' : '' }}>
                                            {{ $car->car_make }} {{ $car->car_model }} ({{ $car->licence_plate  }}) - {{ $car->user->name ?? 'No Driver' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Total Seats</label>
                                <input type="number" name="total_seats" class="form-control" value="{{ old('total_seats', $ride->total_seats) }}" min="1" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Price per Seat ($)</label>
                                <input type="number" step="0.01" name="price_per_seat" class="form-control" value="{{ old('price_per_seat', $ride->price_per_seat) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Luggage Allowed</label>
                                <select name="luggage_allowed" class="form-select">
                                    <option value="1" {{ $ride->luggage_allowed ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ !$ride->luggage_allowed ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ $ride->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $ride->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="completed" {{ $ride->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $ride->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                            <a href="{{ route('admin.rides.show', $ride->id) }}" class="btn btn-secondary px-4">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4 border-left-info">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Current Stats</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2"><strong>Bookings:</strong> {{ $ride->bookings->count() }}</div>
                    <div class="mb-2"><strong>Confirmed Seats:</strong> {{ $ride->bookings->where('status', 'confirmed')->sum('seats_booked') }}</div>
                    <div class="mb-2"><strong>Revenue:</strong> ${{ number_format($ride->bookings->where('status', 'confirmed')->sum('total_price'), 2) }}</div>
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
    


@endsection
