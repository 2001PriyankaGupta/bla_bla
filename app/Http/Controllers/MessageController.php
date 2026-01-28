<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Ride;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    
    public function sendMessage(Request $request, $rideId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'receiver_id' => 'required|exists:users,id',
                'message' => 'required|string|max:1000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'error' => $validator->errors()->first()
                ], 422);
            }

            $ride = Ride::find($rideId);
            if (!$ride) {
                return response()->json([
                    'status' => false,
                    'message' => 'Ride not found'
                ], 404);
            }

            // Check if user is related to the ride
            $isDriver = $ride->car->user_id == Auth::id();
            $isPassenger = Booking::where('ride_id', $rideId)
                ->where('user_id', Auth::id())
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists();

            if (!$isDriver && !$isPassenger) {
                return response()->json([
                    'status' => false,
                    'message' => 'You are not associated with this ride'
                ], 403);
            }

            // Create message
            $message = Message::create([
                'ride_id' => $rideId,
                'sender_id' => Auth::id(),
                'receiver_id' => $request->receiver_id,
                'message' => $request->message
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Message sent successfully',
                'data' => $message
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    
    public function getConversation($rideId, $otherUserId)
    {
        try {
            $messages = Message::where('ride_id', $rideId)
                ->where(function($query) use ($otherUserId) {
                    $query->where('sender_id', Auth::id())
                          ->where('receiver_id', $otherUserId)
                          ->orWhere('sender_id', $otherUserId)
                          ->where('receiver_id', Auth::id());
                })
                ->orderBy('created_at', 'asc')
                ->get();

            // Mark messages as read
            Message::where('ride_id', $rideId)
                ->where('sender_id', $otherUserId)
                ->where('receiver_id', Auth::id())
                ->where('is_read', false)
                ->update(['is_read' => true]);

            return response()->json([
                'status' => true,
                'data' => $messages,
                'message' => 'Conversation retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
}