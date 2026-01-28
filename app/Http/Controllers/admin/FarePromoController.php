<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FareConfiguration;
use App\Models\PromoCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FarePromoController extends Controller
{
    // Index method to show fare and promo management
    public function index()
    {
        $fareConfig = FareConfiguration::first();
        $promoCodes = PromoCode::orderBy('created_at', 'desc')->get();
        
        return view('admin.fare-promo.index', compact('fareConfig', 'promoCodes'));
    }

    // Save Fare Configuration
    public function saveFareConfig(Request $request)
    {
        $request->validate([
            'base_fare' => 'required|numeric|min:0',
            'per_km_charge' => 'required|numeric|min:0',
            'waiting_fee' => 'required|numeric|min:0',
            'home_pickup_fee' => 'required|numeric|min:0',
            'night_holiday_surcharge' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $fareConfig = FareConfiguration::first();
            
            if (!$fareConfig) {
                $fareConfig = new FareConfiguration();
            }

            $fareConfig->base_fare = $request->base_fare;
            $fareConfig->per_km_charge = $request->per_km_charge;
            $fareConfig->waiting_fee = $request->waiting_fee;
            $fareConfig->home_pickup_fee = $request->home_pickup_fee;
            $fareConfig->night_holiday_surcharge = $request->night_holiday_surcharge;
            $fareConfig->save();

            DB::commit();

            return redirect()->back()->with('success', 'Fare configuration saved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error saving fare configuration: ' . $e->getMessage());
        }
    }

    // Add/Edit Promo Code
    public function savePromoCode(Request $request)
    {
        $request->validate([
            'promo_code' => 'required|string|max:50|unique:promo_codes,promo_code,' . $request->id,
            'type' => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'expiry_date' => 'required|date|after:today',
            'is_active' => 'sometimes|boolean'
        ]);

        // For percentage type, validate discount value <= 100
        if ($request->type === 'percentage' && $request->discount_value > 100) {
            return redirect()->back()->with('error', 'Percentage discount cannot exceed 100%.');
        }

        try {
            DB::beginTransaction();

            $data = $request->all();
            $data['is_active'] = $request->has('is_active') ? 1 : 0;
            
            if ($request->has('id') && $request->id) {
                // Update existing promo code
                $promoCode = PromoCode::findOrFail($request->id);
                $promoCode->update($data);
                $message = 'Promo code updated successfully!';
            } else {
                // Create new promo code
                PromoCode::create($data);
                $message = 'Promo code added successfully!';
            }

            DB::commit();

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error saving promo code: ' . $e->getMessage());
        }
    }

    // Delete Promo Code
    public function deletePromoCode($id)
    {
        try {
            DB::beginTransaction();

            $promoCode = PromoCode::findOrFail($id);
            $promoCode->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Promo code deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error deleting promo code: ' . $e->getMessage());
        }
    }

    // Get Promo Code for editing
    public function getPromoCode($id)
    {
        try {
            $promoCode = PromoCode::findOrFail($id);
            return response()->json($promoCode);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Promo code not found'], 404);
        }
    }
}