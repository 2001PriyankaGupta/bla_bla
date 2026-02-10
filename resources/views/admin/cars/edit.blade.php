@extends('admin.layouts.master')
@section('title') Edit Car @endsection
@section('content')
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Edit Car</h4>
                    <form action="{{ route('admin.cars.update', $car->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="user_id" class="form-label">Owner (User) <span class="text-danger">*</span></label>
                                <select class="form-control select2" name="user_id" required>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ $car->user_id == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="license_verified" class="form-label">Verification Status</label>
                                <select class="form-control" name="license_verified">
                                    <option value="pending" {{ $car->license_verified == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="verified" {{ $car->license_verified == 'verified' ? 'selected' : '' }}>Verified</option>
                                    <option value="rejected" {{ $car->license_verified == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="car_make" class="form-label">Car Make <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="car_make" value="{{ $car->car_make }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="car_model" class="form-label">Car Model <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="car_model" value="{{ $car->car_model }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="car_year" class="form-label">Car Year <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="car_year" value="{{ $car->car_year }}" required min="1900" max="{{ date('Y') + 1 }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="car_color" class="form-label">Car Color <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="car_color" value="{{ $car->car_color }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="licence_plate" class="form-label">License Plate <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="licence_plate" value="{{ $car->licence_plate }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="car_photo" class="form-label">Car Photo</label>
                                <input type="file" class="form-control" name="car_photo" accept="image/*">
                                @if($car->car_photo)
                                    <div class="mt-2">
                                        <img src="{{ Storage::url($car->car_photo) }}" alt="Current Photo" class="img-thumbnail" width="150">
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="driver_license_front" class="form-label">License Front</label>
                                <input type="file" class="form-control" name="driver_license_front" accept="image/*">
                                @if($car->driver_license_front)
                                    <div class="mt-2">
                                        <img src="{{ Storage::url($car->driver_license_front) }}" alt="License Front" class="img-thumbnail" width="150">
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="driver_license_back" class="form-label">License Back</label>
                                <input type="file" class="form-control" name="driver_license_back" accept="image/*">
                                @if($car->driver_license_back)
                                    <div class="mt-2">
                                        <img src="{{ Storage::url($car->driver_license_back) }}" alt="License Back" class="img-thumbnail" width="150">
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="verification_notes" class="form-label">Verification Notes</label>
                                <textarea class="form-control" name="verification_notes" rows="3">{{ $car->verification_notes }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary w-md">Update Car</button>
                            <a href="{{ route('admin.cars.index') }}" class="btn btn-secondary w-md">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
