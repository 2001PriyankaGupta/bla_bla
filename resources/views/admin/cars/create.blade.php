@extends('admin.layouts.master')
@section('title') Add New Car @endsection
@section('content')
   
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Add New Car</h4>
                    <form action="{{ route('admin.cars.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="user_id" class="form-label">Owner (User) <span class="text-danger">*</span></label>
                                <select class="form-control select2" name="user_id" required>
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="license_verified" class="form-label">Verification Status</label>
                                <select class="form-control" name="license_verified">
                                    <option value="pending">Pending</option>
                                    <option value="verified">Verified</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="car_make" class="form-label">Car Make <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="car_make" required placeholder="e.g. Toyota">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="car_model" class="form-label">Car Model <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="car_model" required placeholder="e.g. Camry">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="car_year" class="form-label">Car Year <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="car_year" required min="1900" max="{{ date('Y') + 1 }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="car_color" class="form-label">Car Color <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="car_color" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="licence_plate" class="form-label">License Plate <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="licence_plate" required>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="car_photo" class="form-label">Car Photo</label>
                                <input type="file" class="form-control" name="car_photo" accept="image/*">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="driver_license_front" class="form-label">License Front</label>
                                <input type="file" class="form-control" name="driver_license_front" accept="image/*">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="driver_license_back" class="form-label">License Back</label>
                                <input type="file" class="form-control" name="driver_license_back" accept="image/*">
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary w-md">Create Car</button>
                            <a href="{{ route('admin.cars.index') }}" class="btn btn-secondary w-md">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
