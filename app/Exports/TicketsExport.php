<?php 

namespace App\Exports;

use App\Models\SupportTicket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TicketsExport implements FromCollection, WithHeadings
{
    protected $status;
    protected $search;

    public function __construct($status, $search)
    {
        $this->status = $status;
        $this->search = $search;
    }

    public function collection()
    {
        $query = SupportTicket::with(['user', 'assignedAgent']);

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('subject', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        return $query->get()->map(function ($ticket) {
            return [
                $ticket->ticket_id,
                $ticket->user->name ?? 'N/A',
                $ticket->user->email ?? 'N/A',
                $ticket->subject,
                $ticket->description ?? 'N/A',
                ucfirst($ticket->priority),
                ucfirst($ticket->status),
                $ticket->assignedAgent->name ?? 'Unassigned',
                $ticket->created_at->format('Y-m-d H:i:s'),
                $ticket->updated_at->format('Y-m-d H:i:s'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Ticket ID',
            'Customer Name',
            'Customer Email',
            'Subject',
            'Description',
            'Priority',
            'Status',
            'Assigned Agent',
            'Created Date',
            'Last Updated',
        ];
    }
}