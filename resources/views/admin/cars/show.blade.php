@extends('admin.layouts.master')
@section('title') Car Details @endsection

@section('content')
<div class="row mt-4">
    <div class="col-xl-4">
        <div class="card overflow-hidden">
            <div class="bg-soft" style="background-color: #249722;">
                <div class="row">
                    <div class="col-7">
                        <div class="text-white p-3">
                            <h5 class="text-white">Car Owner</h5>
                            <p>Car Owner Details</p>
                        </div>
                    </div>
                    <div class="col-5 align-self-end">
                        <img src="{{ asset('assets/images/profile-img.png') }}" alt="" class="img-fluid">
                    </div>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="avatar-md profile-user-wid mb-4">
                            <img src="{{ $car->user->avatar ? Storage::url($car->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($car->user->name) }}" alt="" class="img-thumbnail rounded-circle">
                        </div>
                    </div>

                    <div class="col-sm-8">
                        <div class="pt-4">
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="font-size-15">{{ $car->user->name }}</h5>
                                    <p class="text-muted mb-0">{{ $car->user->email }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-body border-top">
                    <div class="row">
                        <div class="col-sm-6">
                            <div>
                                <p class="text-muted mb-2">Phone</p>
                                <h5 class="font-size-15">{{ $car->user->phone ?? 'N/A' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div>
                                <p class="text-muted mb-2">Joined Date</p>
                                <h5 class="font-size-15">{{ $car->user->created_at->format('d M, Y') }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Status Information</h4>
                <div class="table-responsive">
                    <table class="table table-nowrap mb-0">
                        <tbody>
                            <tr>
                                <th scope="row">Status :</th>
                                <td>
                                    @if($car->license_verified == 'verified')
                                        <span class="badge badge-soft-success font-size-12">Verified</span>
                                    @elseif($car->license_verified == 'rejected')
                                        <span class="badge badge-soft-danger font-size-12">Rejected</span>
                                    @else
                                        <span class="badge badge-soft-warning font-size-12">Pending</span>
                                    @endif
                                </td>
                            </tr>
                            @if($car->verified_at)
                            <tr>
                                <th scope="row">Verified At :</th>
                                <td>{{ $car->verified_at->format('d M, Y H:i A') }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Verified By :</th>
                                <td>{{ $car->verified_by }}</td>
                            </tr>
                            @endif
                            @if($car->verification_notes)
                            <tr>
                                <th scope="row">Notes :</th>
                                <td>{{ $car->verification_notes }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                
                
            </div>
        </div>
    </div>

    <div class="col-xl-8">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Car Information</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Car Make</label>
                            <h5 class="font-size-14">{{ $car->car_make }}</h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Car Model</label>
                            <h5 class="font-size-14">{{ $car->car_model }}</h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Car Year</label>
                            <h5 class="font-size-14">{{ $car->car_year }}</h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Car Color</label>
                            <h5 class="font-size-14">{{ $car->car_color }}</h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">License Plate</label>
                            <h5 class="font-size-14">{{ $car->licence_plate }}</h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Created At</label>
                            <h5 class="font-size-14">{{ $car->created_at->format('d M, Y H:i A') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Car Documents & Photos</h4>
                
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <h5 class="font-size-14 mb-3">Car Photo</h5>
                        @if($car->car_photo)
                            <a href="{{ Storage::url($car->car_photo) }}" target="_blank">
                                <img src="{{ Storage::url($car->car_photo) }}" class="img-fluid rounded" alt="Car Photo">
                            </a>
                        @else
                            <div class="alert alert-warning">No car photo uploaded</div>
                        @endif
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <h5 class="font-size-14 mb-3">License Front</h5>
                        @if($car->driver_license_front)
                            <a href="{{ Storage::url($car->driver_license_front) }}" target="_blank">
                                <img src="{{ Storage::url($car->driver_license_front) }}" class="img-fluid rounded" alt="License Front">
                            </a>
                        @else
                            <div class="alert alert-warning">No license front photo</div>
                        @endif
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <h5 class="font-size-14 mb-3">License Back</h5>
                        @if($car->driver_license_back)
                            <a href="{{ Storage::url($car->driver_license_back) }}" target="_blank">
                                <img src="{{ Storage::url($car->driver_license_back) }}" class="img-fluid rounded" alt="License Back">
                            </a>
                        @else
                            <div class="alert alert-warning">No license back photo</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-end mb-4">
            <a href="{{ route('admin.cars.index') }}" class="btn btn-secondary me-2">Back to List</a>
            <a href="{{ route('admin.cars.edit', $car->id) }}" class="btn btn-primary">Edit Car</a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $('#statusForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Status updated successfully',
                    showConfirmButton: false,
                    timer: 3000
                }).then(() => {
                    location.reload();
                });
            },
            error: function() {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'Failed to update status',
                    showConfirmButton: false,
                    timer: 3000
                });
            }
        });
    });
</script>
@endsection
