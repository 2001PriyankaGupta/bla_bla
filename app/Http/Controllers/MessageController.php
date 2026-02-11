<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Ride;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    public function sendMessage(Request $request, $rideId)
    {
        try {
            // JWT Authentication
            try {
                $user = JWTAuth::parseToken()->authenticate();
            } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please login first to send messages.'
                ], 401);
            }

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

            // Check if user is trying to send message to themselves
            if ($user->id == $request->receiver_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'You cannot send message to yourself'
                ], 400);
            }

            $ride = Ride::with('car')->find($rideId);
            if (!$ride) {
                return response()->json([
                    'status' => false,
                    'message' => 'Ride not found'
                ], 404);
            }

            // Check if user is related to the ride
            $isDriver = $ride->car->user_id == $user->id;
            $isPassenger = Booking::where('ride_id', $rideId)
                ->where('user_id', $user->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists();

            if (!$isDriver && !$isPassenger) {
                return response()->json([
                    'status' => false,
                    'message' => 'You are not associated with this ride'
                ], 403);
            }

            // Check if receiver is related to the ride
            $receiverIsDriver = $ride->car->user_id == $request->receiver_id;
            $receiverIsPassenger = Booking::where('ride_id', $rideId)
                ->where('user_id', $request->receiver_id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists();

            if (!$receiverIsDriver && !$receiverIsPassenger) {
                return response()->json([
                    'status' => false,
                    'message' => 'Receiver is not associated with this ride'
                ], 403);
            }

            // Create message
            $message = Message::create([
                'ride_id' => $rideId,
                'sender_id' => $user->id,
                'receiver_id' => $request->receiver_id,
                'message' => $request->message,
                'is_read' => false
            ]);

            // Load sender and receiver details
            $message->load(['sender', 'receiver']);

            Log::info('Message sent', [
                'ride_id' => $rideId,
                'sender_id' => $user->id,
                'receiver_id' => $request->receiver_id,
                'message_id' => $message->id
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Message sent successfully',
                'data' => [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender' => [
                        'id' => $message->sender->id,
                        'name' => $message->sender->name,
                        'user_type' => $message->sender->user_type
                    ],
                    'receiver' => [
                        'id' => $message->receiver->id,
                        'name' => $message->receiver->name,
                        'user_type' => $message->receiver->user_type
                    ],
                    'ride_id' => $message->ride_id,
                    'sent_at' => $message->created_at->format('Y-m-d H:i:s'),
                    'formatted_time' => $message->created_at->format('h:i A'),
                    'is_read' => $message->is_read
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Send message error', [
                'error' => $e->getMessage(),
                'ride_id' => $rideId,
                'user_id' => isset($user) ? $user->id : null
            ]);
            
            return response()->json([
                'status' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getConversation(Request $request, $rideId, $otherUserId)
    {
        try {
            // JWT Authentication
            try {
                $user = JWTAuth::parseToken()->authenticate();
            } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please login first to view conversations.'
                ], 401);
            }

            $ride = Ride::with('car')->find($rideId);
            if (!$ride) {
                return response()->json([
                    'status' => false,
                    'message' => 'Ride not found'
                ], 404);
            }

            // Check if user is related to the ride
            $isDriver = $ride->car->user_id == $user->id;
            $isPassenger = Booking::where('ride_id', $rideId)
                ->where('user_id', $user->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists();

            if (!$isDriver && !$isPassenger) {
                return response()->json([
                    'status' => false,
                    'message' => 'You are not associated with this ride'
                ], 403);
            }

            // Check if other user is related to the ride
            $otherIsDriver = $ride->car->user_id == $otherUserId;
            $otherIsPassenger = Booking::where('ride_id', $rideId)
                ->where('user_id', $otherUserId)
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists();

            if (!$otherIsDriver && !$otherIsPassenger) {
                return response()->json([
                    'status' => false,
                    'message' => 'Other user is not associated with this ride'
                ], 403);
            }

            $messages = Message::with(['sender', 'receiver'])
                ->where('ride_id', $rideId)
                ->where(function($query) use ($user, $otherUserId) {
                    $query->where(function($q) use ($user, $otherUserId) {
                        $q->where('sender_id', $user->id)
                          ->where('receiver_id', $otherUserId);
                    })->orWhere(function($q) use ($user, $otherUserId) {
                        $q->where('sender_id', $otherUserId)
                          ->where('receiver_id', $user->id);
                    });
                })
                ->orderBy('created_at', 'asc')
                ->get();

            // Mark messages as read
            Message::where('ride_id', $rideId)
                ->where('sender_id', $otherUserId)
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);

            // Format messages for response
            $formattedMessages = $messages->map(function($message) use ($user) {
                $isSender = $message->sender_id == $user->id;
                
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'is_sender' => $isSender,
                    'sender' => [
                        'id' => $message->sender->id,
                        'name' => $message->sender->name,
                        'user_type' => $message->sender->user_type
                    ],
                    'receiver' => [
                        'id' => $message->receiver->id,
                        'name' => $message->receiver->name,
                        'user_type' => $message->receiver->user_type
                    ],
                    'sent_at' => $message->created_at->format('Y-m-d H:i:s'),
                    'formatted_time' => $message->created_at->format('h:i A'),
                    'formatted_date' => $message->created_at->format('M d, Y'),
                    'is_read' => $message->is_read,
                    'message_type' => $isSender ? 'sent' : 'received'
                ];
            });

            Log::info('Conversation retrieved', [
                'ride_id' => $rideId,
                'user_id' => $user->id,
                'other_user_id' => $otherUserId,
                'message_count' => $messages->count()
            ]);

            return response()->json([
                'status' => true,
                'data' => [
                    'ride_id' => $rideId,
                    'current_user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'user_type' => $user->user_type
                    ],
                    'other_user' => [
                        'id' => (int)$otherUserId,
                        'name' => isset($messages[0]) ? 
                            ($messages[0]->sender_id == $otherUserId ? $messages[0]->sender->name : 
                             ($messages[0]->receiver_id == $otherUserId ? $messages[0]->receiver->name : 'N/A')) : 'N/A'
                    ],
                    'messages' => $formattedMessages,
                    'total_messages' => $messages->count()
                ],
                'message' => 'Conversation retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Get conversation error', [
                'error' => $e->getMessage(),
                'ride_id' => $rideId,
                'other_user_id' => $otherUserId,
                'user_id' => isset($user) ? $user->id : null
            ]);
            
            return response()->json([
                'status' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
}