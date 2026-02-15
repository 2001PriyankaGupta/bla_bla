<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupportController extends Controller
{
    /**
     * Get all active FAQs
     */
    public function getFaqs()
    {
        $faqs = Faq::where('is_active', true)
            ->orderBy('order')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $faqs
        ]);
    }

    /**
     * Get all tickets for the authenticated user
     */
    public function getTickets(Request $request)
    {
        $tickets = SupportTicket::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $tickets
        ]);
    }

    /**
     * Create a new support ticket
     */
    public function createTicket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'nullable|string|in:Low,Medium,High',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        $ticket = SupportTicket::create([
            'user_id' => $request->user()->id,
            'subject' => $request->subject,
            'description' => $request->description,
            'priority' => $request->priority ?? 'Low',
            'status' => 'Open',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Ticket created successfully',
            'data' => $ticket
        ]);
    }

    /**
     * Get ticket details with replies
     */
    public function getTicketDetails($id, Request $request)
    {
        $ticket = SupportTicket::with(['replies.user'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $ticket
        ]);
    }

    /**
     * Reply to a ticket
     */
    public function replyTicket(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        $ticket = SupportTicket::where('user_id', $request->user()->id)->findOrFail($id);

        $reply = TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'message' => $request->message,
            'is_internal' => false
        ]);

        // Automatically set status back to 'Open' if user replies and it was 'In Progress' or 'Closed'? 
        // Maybe just leave it for now.
        if ($ticket->status == 'Closed') {
            $ticket->update(['status' => 'Open']);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Reply sent successfully',
            'data' => $reply->load('user')
        ]);
    }
}
