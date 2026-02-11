<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarController extends Controller
{
    // public function index()
    // {
    //     $cars = Car::with('user')->latest()->paginate(10);
    //     return view('admin.cars.index', compact('cars'));
    // }

    public function index()
    {
        $cars = Car::with('user')->get();
        $totalCars = $cars->count();
        $verifiedCars = $cars->where('license_verified', 'verified')->count();
        $pendingCars = $cars->where('license_verified', 'pending')->count();
        $rejectedCars = $cars->where('license_verified', 'rejected')->count();
        
        return view('admin.cars.index', compact(
            'cars',
            'totalCars',
            'verifiedCars',
            'pendingCars',
            'rejectedCars'
        ));
    }

    public function create()
    {
        $users = User::all(); // Retrieve all users to assign a car
        return view('admin.cars.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'car_make' => 'required|string|max:255',
            'car_model' => 'required|string|max:255',
            'car_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'car_color' => 'required|string|max:255',
            'licence_plate' => 'required|string|unique:cars,licence_plate',
            'car_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'driver_license_front' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'driver_license_back' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'license_verified' => 'required|in:pending,verified,rejected',
        ]);

        $input = $request->all();

        if ($request->hasFile('car_photo')) {
            $path = $request->file('car_photo')->store('car_photos', 'public');
            $input['car_photo'] = $path;
        }

        if ($request->hasFile('driver_license_front')) {
            $path = $request->file('driver_license_front')->store('driver_licenses', 'public');
            $input['driver_license_front'] = $path;
        }

        if ($request->hasFile('driver_license_back')) {
            $path = $request->file('driver_license_back')->store('driver_licenses', 'public');
            $input['driver_license_back'] = $path;
        }

        if ($input['license_verified'] == 'verified') {
            $input['verified_by'] = auth()->user()->name ?? 'Admin';
            $input['verified_at'] = now();
        }

        Car::create($input);

        return redirect()->route('admin.cars.index')->with('success', 'Car created successfully.');
    }

    public function edit(Car $car)
    {
        $users = User::all();
        return view('admin.cars.edit', compact('car', 'users'));
    }

    public function show(Car $car)
    {
        return view('admin.cars.show', compact('car'));
    }



    public function update(Request $request, Car $car)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'car_make' => 'required|string|max:255',
            'car_model' => 'required|string|max:255',
            'car_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'car_color' => 'required|string|max:255',
            'licence_plate' => 'required|string|unique:cars,licence_plate,' . $car->id,
            'car_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'driver_license_front' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'driver_license_back' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'license_verified' => 'required|in:pending,verified,rejected',
        ]);

        $input = $request->all();

        // Keep existing car photo if no new one is uploaded
        if ($request->hasFile('car_photo')) {
            // Delete old image from storage
            if ($car->car_photo) {
                // Remove storage/ prefix if exists for proper deletion
                $oldPath = preg_replace('/^\/?storage\//', '', $car->car_photo);
                Storage::disk('public')->delete($oldPath);
            }
            // Store new image
            $path = $request->file('car_photo')->store('car_photos', 'public');
            $input['car_photo'] = $path;
        } else {
            // Keep the existing car_photo
            $input['car_photo'] = $car->car_photo;
        }

        // Keep existing driver license front if no new one is uploaded
        if ($request->hasFile('driver_license_front')) {
            // Delete old image from storage
            if ($car->driver_license_front) {
                // Remove storage/ prefix if exists
                $oldPath = preg_replace('/^\/?storage\//', '', $car->driver_license_front);
                Storage::disk('public')->delete($oldPath);
            }
            // Store new image
            $path = $request->file('driver_license_front')->store('driver_licenses', 'public');
            $input['driver_license_front'] = $path;
        } else {
            // Keep the existing driver_license_front
            $input['driver_license_front'] = $car->driver_license_front;
        }

        // Keep existing driver license back if no new one is uploaded
        if ($request->hasFile('driver_license_back')) {
            // Delete old image from storage
            if ($car->driver_license_back) {
                // Remove storage/ prefix if exists
                $oldPath = preg_replace('/^\/?storage\//', '', $car->driver_license_back);
                Storage::disk('public')->delete($oldPath);
            }
            // Store new image
            $path = $request->file('driver_license_back')->store('driver_licenses', 'public');
            $input['driver_license_back'] = $path;
        } else {
            // Keep the existing driver_license_back
            $input['driver_license_back'] = $car->driver_license_back;
        }
        
        // Handle verification status change
        if ($input['license_verified'] == 'verified' && $car->license_verified != 'verified') {
            $input['verified_by'] = auth()->user()->name ?? 'Admin';
            $input['verified_at'] = now();
        }

        $car->update($input);

        return redirect()->route('admin.cars.index')->with('success', 'Car updated successfully.');
    }

    public function destroy(Car $car)
    {
        if ($car->car_photo) {
            Storage::disk('public')->delete($car->car_photo);
        }
        if ($car->driver_license_front) {
            Storage::disk('public')->delete($car->driver_license_front);
        }
        if ($car->driver_license_back) {
            Storage::disk('public')->delete($car->driver_license_back);
        }

        $car->delete();

        return redirect()->route('admin.cars.index')->with('success', 'Car deleted successfully.');
    }
    
    public function updateStatus(Request $request, Car $car)
    {
        $request->validate([
            'license_verified' => 'required|in:pending,verified,rejected',
        ]);
        
        $car->license_verified = $request->license_verified;
        
        if ($request->license_verified == 'verified') {
             $car->verified_by = auth()->user()->name ?? 'Admin';
             $car->verified_at = now();
        }
        
        $car->save();
        
        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }
}
