@extends('admin.layouts.master')

@section('title', 'Edit Ride #' . $ride->id)

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
    .form-label {
        font-weight: 600;
        color: var(--primary-green);
    }
    .suggestions-container {
        position: absolute;
        width: 100%;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        z-index: 1000;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        max-height: 200px;
        overflow-y: auto;
        display: none;
    }
    .suggestion-item {
        padding: 8px 12px;
        cursor: pointer;
    }
    .suggestion-item:hover {
        background: var(--light-green);
        color: var(--primary-green);
    }
    .btn-primary {
        background-color: var(--primary-green) !important;
        border-color: var(--primary-green) !important;
    }
    .btn-primary:hover {
        background-color: #157347 !important;
        border-color: #146c43 !important;
    }
    .btn-info {
        background-color: var(--secondary-green) !important;
        border-color: var(--secondary-green) !important;
        color: white !important;
    }
    .text-primary {
        color: var(--primary-green) !important;
    }
    .border-left-info {
        border-left: .25rem solid var(--secondary-green) !important;
    }
    .text-info {
        color: var(--secondary-green) !important;
    }
</style>

<div class="container-fluid mt-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Ride</h1>
        <a href="{{ route('admin.rides.index') }}" class="btn btn-sm btn-outline-success shadow-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Back to List
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Ride Details</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.rides.update', $ride->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <div class="col-md-6 position-relative">
                                <label class="form-label">Pickup Point</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt text-success"></i></span>
                                    <input type="text" name="pickup_point" id="pickup_point" class="form-control" value="{{ old('pickup_point', $ride->pickup_point) }}" required autocomplete="off">
                                </div>
                                <div id="pickup-suggestions" class="suggestions-container"></div>
                            </div>
                            <div class="col-md-6 position-relative">
                                <label class="form-label">Drop Point</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-flag-checkered text-danger"></i></span>
                                    <input type="text" name="drop_point" id="drop_point" class="form-control" value="{{ old('drop_point', $ride->drop_point) }}" required autocomplete="off">
                                </div>
                                <div id="drop-suggestions" class="suggestions-container"></div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Date & Time</label>
                                <input type="datetime-local" name="date_time" class="form-control" value="{{ old('date_time', date('Y-m-d\TH:i', strtotime($ride->date_time ?? now()))) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Car & Driver</label>
                                <select name="car_id" class="form-select" required>
                                    @foreach($cars as $car)
                                        <option value="{{ $car->id }}" {{ $ride->car_id == $car->id ? 'selected' : '' }}>
                                            {{ $car->car_make }} {{ $car->car_model }} ({{ $car->licence_plate }}) - {{ $car->user->name ?? 'No Driver' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Total Seats</label>
                                <input type="number" name="total_seats" class="form-control" value="{{ old('total_seats', $ride->total_seats) }}" min="1" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Price per Seat (₹)</label>
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

                        <!-- Intermediate Stops -->
                        <div class="card bg-light border-0 mb-4">
                            <div class="card-header border-0 bg-light py-3 d-flex justify-content-between align-items-center" style="background:#f8faf9 !important; border-bottom: 1px solid #e1eaea;">
                                <h6 class="m-0 font-weight-bold text-dark">Intermediate Stop Points</h6>
                                <button type="button" class="btn btn-sm btn-info" id="addStopPoint">
                                    <i class="fas fa-plus"></i> Add Stop
                                </button>
                            </div>
                            <div class="card-body pt-0" style="background:#f8faf9;">
                                @php $stopCount = 0; @endphp
                                <div id="stopPointsContainer">
                                    @if($ride->stopPoints && $ride->stopPoints->count() > 0)
                                        @foreach($ride->stopPoints as $stop)
                                            <div class="row g-2 mb-2 stop-point-row position-relative">
                                                <div class="col-md-7">
                                                    <input type="text" name="stop_points[{{ $stopCount }}][city_name]" class="form-control form-control-sm stop-input" placeholder="City Name" value="{{ $stop->city_name }}" required autocomplete="off">
                                                    <div class="suggestions-container stop-suggestions" style="top: 32px;"></div>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="number" step="0.01" name="stop_points[{{ $stopCount }}][price_from_pickup]" class="form-control form-control-sm" placeholder="Price" value="{{ $stop->price_from_pickup }}" required>
                                                </div>
                                                <div class="col-md-2 text-end">
                                                    <button type="button" class="btn btn-sm btn-outline-danger remove-stop-point">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @php $stopCount++; @endphp
                                        @endforeach
                                    @endif
                                </div>
                                <div id="noStopsMessage" class="text-center py-3 {{ ($ride->stopPoints && $ride->stopPoints->count() > 0) ? 'd-none' : '' }}">
                                    <small class="text-muted">No intermediate stops added yet.</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ $ride->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ $ride->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $ride->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="inactive" {{ $ride->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div class="mt-4 text-end">
                            <a href="{{ route('admin.rides.show', $ride->id) }}" class="btn btn-light px-4 border">Cancel</a>
                            <button type="submit" class="btn btn-primary px-5 font-weight-bold">Update Ride Info</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow mb-4 border-left-info">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Current Stats</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2"><strong>Bookings:</strong> {{ $ride->bookings->count() }}</div>
                    <div class="mb-2"><strong>Confirmed Seats:</strong> {{ $ride->bookings->where('status', 'confirmed')->sum('seats_booked') }}</div>
                    <div class="mb-2"><strong>Revenue:</strong> ₹{{ number_format($ride->bookings->where('status', 'confirmed')->sum('total_price'), 2) }}</div>
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
    const GOOGLE_MAPS_API_KEY = "{{ config('services.google.maps_api_key') }}";
    let stopCount = {{ $stopCount }};

    $('#addStopPoint').on('click', function() {
        $('#noStopsMessage').addClass('d-none');
        const html = `
            <div class="row g-2 mb-2 stop-point-row position-relative">
                <div class="col-md-7">
                    <input type="text" name="stop_points[${stopCount}][city_name]" class="form-control form-control-sm stop-input" placeholder="City Name" required autocomplete="off">
                    <div class="suggestions-container stop-suggestions" style="top: 32px;"></div>
                </div>
                <div class="col-md-3">
                    <input type="number" step="0.01" name="stop_points[${stopCount}][price_from_pickup]" class="form-control form-control-sm" placeholder="Price" required>
                </div>
                <div class="col-md-2 text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-stop-point">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        $('#stopPointsContainer').append(html);
        stopCount++;
    });

    $(document).on('click', '.remove-stop-point', function() {
        $(this).closest('.stop-point-row').remove();
        if ($('#stopPointsContainer').children().length === 0) {
            $('#noStopsMessage').removeClass('d-none');
        }
    });

    function fetchSuggestions(input, resultsContainer) {
        const query = input.val();
        if (query.length < 3) {
            resultsContainer.hide();
            return;
        }

        $.ajax({
            url: `https://maps.googleapis.com/maps/api/place/autocomplete/json`,
            data: {
                input: query,
                key: GOOGLE_MAPS_API_KEY,
                components: 'country:in'
            },
            success: function(data) {
                if (data.predictions && data.predictions.length > 0) {
                    let html = '';
                    data.predictions.forEach(prediction => {
                        html += `<div class="suggestion-item" data-desc="${prediction.description}">${prediction.description}</div>`;
                    });
                    resultsContainer.html(html).show();
                } else {
                    resultsContainer.hide();
                }
            }
        });
    }

    $(document).on('input', '#pickup_point', function() {
        fetchSuggestions($(this), $('#pickup-suggestions'));
    });

    $(document).on('input', '#drop_point', function() {
        fetchSuggestions($(this), $('#drop-suggestions'));
    });

    $(document).on('input', '.stop-input', function() {
        const container = $(this).siblings('.stop-suggestions');
        fetchSuggestions($(this), container);
    });

    $(document).on('click', '.suggestion-item', function() {
        const desc = $(this).data('desc');
        const input = $(this).closest('.position-relative').find('input');
        input.val(desc);
        $(this).parent().hide();
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.position-relative').length) {
            $('.suggestions-container').hide();
        }
    });
</script>
@endsection
