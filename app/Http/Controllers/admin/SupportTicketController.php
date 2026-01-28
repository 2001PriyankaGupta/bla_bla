<?php

namespace App\Http\Controllers\admin;


use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\Faq;
use App\Models\QuickReplyTemplate;
use Illuminate\Http\Request;
use App\Exports\TicketsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;


class SupportTicketController extends Controller
{
    public function index()
    {
        $faqs = Faq::where('is_active', true)->get();
        $tickets = SupportTicket::with(['user', 'assignedAgent'])->latest()->paginate(10);
        $quickReplies = QuickReplyTemplate::where('is_active', true)->get();
        
        return view('admin.support.index', compact('tickets', 'quickReplies','faqs'));
    }

    public function show($id)
    {
        try {
            $ticket = SupportTicket::with(['user', 'assignedAgent'])->findOrFail($id);
            
            // Manually load replies with the correct relationship
            $ticket->load(['replies' => function($query) {
                $query->with('user');
            }]);
            
            $quickReplies = QuickReplyTemplate::where('is_active', true)->get();
            $agents = User::where('user_type', 'support_agent')->where('status', 'active')->get();
            
            return view('admin.support.show', compact('ticket', 'quickReplies', 'agents'));
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.support.index')
                ->with('error', 'Ticket not found.');
        }
    }

    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'status' => 'required|in:Open,In Progress,Closed'
        ]);
        

        $ticket->update(['status' => $request->status]);
        
        return back()->with('success', 'Ticket status updated successfully.');
    }

    public function assignAgent(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id'
        ]);

        $ticket->update(['assigned_to' => $request->assigned_to]);

        return back()->with('success', 'Agent assigned successfully.');
    }

    public function sendReply(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        // Create ticket reply logic here
        // You'll need to create a TicketReply model and migration

        return back()->with('success', 'Reply sent successfully.');
    }

    public function destroy($id)
    {
        try {
            $ticket = SupportTicket::with('replies')->findOrFail($id);
            
            // Delete all related replies first
            if ($ticket->replies) {
                $ticket->replies()->delete();
            }
            
            // Delete the ticket
            $ticket->delete();

            return redirect()->route('admin.support.index')
                ->with('success', 'Ticket and all related replies deleted successfully.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.support.index')
                ->with('error', 'Ticket not found.');

        } catch (\Exception $e) {
            Log::error('Error deleting ticket: ' . $e->getMessage());
            
            return redirect()->route('admin.support.index')
                ->with('error', 'Error deleting ticket. Please try again.');
        }
    }
    public function export(Request $request)
    {
        $status = $request->get('status');
        $search = $request->get('search');

        return Excel::download(new TicketsExport($status, $search), 'support-tickets-' . date('Y-m-d') . '.xlsx');
    }


}