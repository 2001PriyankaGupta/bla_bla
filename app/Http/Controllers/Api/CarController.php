<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Log;


class CarController extends Controller
{
    private function authenticateUser()
    {
        try {
            return JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            throw new \Exception('Token expired');
        } catch (TokenInvalidException $e) {
            throw new \Exception('Token invalid');
        } catch (JWTException $e) {
            throw new \Exception('Token absent');
        }
    }

    // Get all cars for authenticated user
    public function index(Request $request)
    {
        try {
            try {
                $user = JWTAuth::parseToken()->authenticate();
            } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please login first to add a car.'
                ], 401);
            }

            $cars = Car::with('user') // eager load user
                    ->where('user_id', $user->id)
                    ->latest()
                    ->get();

            return response()->json([
                'status' => true,
                'message' => 'Cars fetched successfully',
                'data' => $cars
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }


   public function store(Request $request)
    {
        try {
            try {
                $user = JWTAuth::parseToken()->authenticate();
            } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please login first to add a car.'
                ], 401);
            }

            // Validate request
            $validator = Validator::make($request->all(), [
                'car_make' => 'required|string|max:255',
                'car_model' => 'required|string|max:255',
                'car_year' => 'required|integer|min:1990|max:' . (date('Y') + 1),
                'car_color' => 'required|string|max:100',
                'licence_plate' => 'required|string|unique:cars,licence_plate',
                'car_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
                'driver_license_front' => 'required|image|mimes:jpeg,png,jpg|max:5120',
                'driver_license_back' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            ], [
                'licence_plate.unique' => 'This licence plate is already registered.',
                'car_photo.required' => 'Car photo is required.',
                'driver_license_front.required' => 'Driver license front photo is required.',
                'driver_license_back.required' => 'Driver license back photo is required.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $carData = $request->only([
                'car_make', 'car_model', 'car_year', 'car_color', 'licence_plate'
            ]);
            $carData['user_id'] = $user->id;

            $uploadImage = function ($file, $folder) {
                $filename = $folder . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs($folder, $filename, 'public');
                return Storage::url($path);
            };
            $carData['car_photo'] = $uploadImage($request->file('car_photo'), 'car_photos');
            $carData['driver_license_front'] = $uploadImage($request->file('driver_license_front'), 'driver_licenses');
            $carData['driver_license_back'] = $uploadImage($request->file('driver_license_back'), 'driver_licenses');

            $car = Car::create($carData);

            return response()->json([
                'status' => true,
                'message' => 'Car added successfully. Driver license verification is pending.',
                'data' => $car
            ], 201);

        } catch (\Exception $e) {
            Log::error('Car registration failed: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get single car
    public function show($id)
    {
        try {
            $user = $this->authenticateUser();
            
            $car = Car::where('user_id', $user->id)->find($id);

            if (!$car) {
                return response()->json([
                    'status' => false,
                    'message' => 'Car not found'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Car details fetched successfully',
                'data' => $car
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }

    // Update car
    public function update(Request $request, $id)
    {
        try {
            try {
                $user = JWTAuth::parseToken()->authenticate();
            } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please login first to update car.'
                ], 401);
            }

            $car = Car::where('user_id', $user->id)->find($id);

            if (!$car) {
                return response()->json([
                    'status' => false,
                    'message' => 'Car not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'car_make' => 'sometimes|required|string|max:255',
                'car_model' => 'sometimes|required|string|max:255',
                'car_year' => 'sometimes|required|integer|min:1990|max:' . (date('Y') + 1),
                'car_color' => 'sometimes|required|string|max:100',
                'licence_plate' => 'sometimes|required|string|unique:cars,licence_plate,' . $id,
                'car_photo' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:5120',
                'driver_license_front' => 'sometimes|image|mimes:jpeg,png,jpg|max:5120',
                'driver_license_back' => 'sometimes|image|mimes:jpeg,png,jpg|max:5120',
            ], [
                'licence_plate.unique' => 'This licence plate is already registered.',
                'car_photo.image' => 'Car photo must be a valid image file.',
                'driver_license_front.image' => 'Driver license front must be a valid image file.',
                'driver_license_back.image' => 'Driver license back must be a valid image file.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $carData = $request->only([
                'car_make', 'car_model', 'car_year', 'car_color', 'licence_plate'
            ]);

            $resetVerification = false;

            // Reset verification status if important fields are updated
            if ($request->hasAny(['car_make', 'car_model', 'car_year', 'licence_plate'])) {
                $carData['license_verified'] = 'pending';
                $carData['verification_notes'] = null;
                $carData['verified_by'] = null;
                $carData['verified_at'] = null;
                $resetVerification = true;
            }

            // Image upload function
            $uploadImage = function ($file, $folder, $oldImage = null) {
                // Delete old image if exists
                if ($oldImage) {
                    $oldPath = str_replace('/storage/', '', $oldImage);
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }

                $filename = $folder . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs($folder, $filename, 'public');
                return Storage::url($path);
            };

            // Handle car photo update
            if ($request->hasFile('car_photo')) {
                $carData['car_photo'] = $uploadImage($request->file('car_photo'), 'car_photos', $car->car_photo);
            }

            // Handle driver license front update
            if ($request->hasFile('driver_license_front')) {
                $carData['driver_license_front'] = $uploadImage($request->file('driver_license_front'), 'driver_licenses', $car->driver_license_front);
                $carData['license_verified'] = 'pending';
                $carData['verification_notes'] = null;
                $carData['verified_by'] = null;
                $carData['verified_at'] = null;
                $resetVerification = true;
            }

            // Handle driver license back update
            if ($request->hasFile('driver_license_back')) {
                $carData['driver_license_back'] = $uploadImage($request->file('driver_license_back'), 'driver_licenses', $car->driver_license_back);
                $carData['license_verified'] = 'pending';
                $carData['verification_notes'] = null;
                $carData['verified_by'] = null;
                $carData['verified_at'] = null;
                $resetVerification = true;
            }

            $car->update($carData);

            // Refresh car data from database
            $car->refresh();

            return response()->json([
                'status' => true,
                'message' => 'Car updated successfully.' . 
                            ($resetVerification ? ' License verification status reset to pending.' : ''),
                'data' => $car
            ]);

        } catch (\Exception $e) {
            Log::error('Car update failed: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    // Delete car
    public function destroy($id)
    {
        try {
            $user = $this->authenticateUser();
            
            $car = Car::where('user_id', $user->id)->find($id);

            if (!$car) {
                return response()->json([
                    'status' => false,
                    'message' => 'Car not found'
                ], 404);
            }

            // Delete associated files
            $this->deleteCarFiles($car);

            $car->delete();

            return response()->json([
                'status' => true,
                'message' => 'Car deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }

    // Check license verification status
    public function checkVerification($id)
    {
        try {
            $user = $this->authenticateUser();
            
            $car = Car::where('user_id', $user->id)->find($id);

            if (!$car) {
                return response()->json([
                    'status' => false,
                    'message' => 'Car not found'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Verification status fetched successfully',
                'verification_status' => $car->license_verified,
                'verification_notes' => $car->verification_notes,
                'verified_at' => $car->verified_at,
                'verified_by' => $car->verified_by
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }

    // Helper method for image upload
    private function uploadImage($file, $folder)
    {
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = 'uploads/' . $folder . '/' . $filename;
        
        // Create directory if not exists
        if (!Storage::exists('uploads/' . $folder)) {
            Storage::makeDirectory('uploads/' . $folder);
        }
        
        $image = Image::make($file)->encode($file->getClientOriginalExtension(), 85);
        Storage::put($path, $image->stream());
        
        return $path;
    }

    // Helper method to delete car files
    private function deleteCarFiles($car)
    {
        $files = [
            $car->car_photo,
            $car->driver_license_front,
            $car->driver_license_back
        ];

        foreach ($files as $file) {
            if ($file && Storage::exists($file)) {
                Storage::delete($file);
            }
        }
    }
}