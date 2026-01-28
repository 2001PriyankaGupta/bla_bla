@extends('admin.layouts.master')
@section('title')
    Support Management
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: #f8f9fa;
            color: #333;
        }

        .swal2-toast {
            font-size: 12px !important;
            padding: 6px 10px !important;
            min-width: auto !important;
            width: 220px !important;
            line-height: 1.3em !important;
        }

        .swal2-toast .swal2-icon {
            width: 24px !important;
            height: 24px !important;
            margin-right: 6px !important;
        }

        .swal2-toast .swal2-title {
            font-size: 13px !important;
        }

        .page-header {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            border-left: 4px solid #249722;
        }

        .support-tabs {
            display: flex;
            gap: 24px;
            border-bottom: 2px solid #eaeaea;
            margin-bottom: 24px;
            padding: 0 10px;
        }

        .support-tab-link {
            font-size: 1.08rem;
            color: #6c757d;
            font-weight: 600;
            cursor: pointer;
            padding: 12px 0;
            margin-right: 24px;
            border: none;
            background: transparent;
            border-bottom: 3px solid transparent;
            transition: color 0.2s, border 0.2s;
        }

        .support-tab-link.active {
            color: #249722;
            border-bottom: 3px solid #249722;
        }

        .card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            margin-bottom: 25px;
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid #eaeaea;
            padding: 16px 20px;
            border-radius: 10px 10px 0 0 !important;
            font-weight: 600;
            color: #333;
        }

        .card-body {
            padding: 20px;
        }

        .dataTables_wrapper {
            padding: 0;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 15px;
        }

        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 6px;
            border: 1px solid #ddd;
            padding: 5px 10px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 6px !important;
            margin: 0 2px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #249722 !important;
            border-color: #249722 !important;
            color: white !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #e9ecef !important;
            border-color: #dee2e6 !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #1e7e1c !important;
            border-color: #1e7e1c !important;
        }

        table.dataTable {
            border-collapse: collapse !important;
            margin-top: 0 !important;
            margin-bottom: 15px !important;
        }

        table.dataTable thead th {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            border-bottom: 1px solid #dee2e6;
            padding: 12px 15px;
            vertical-align: middle;
        }

        table.dataTable tbody td {
            padding: 12px 15px;
            vertical-align: middle;
            border-top: 1px solid #f0f0f0;
        }

        table.dataTable tbody tr {
            background: #fff;
            transition: background 0.2s;
        }

        table.dataTable tbody tr:hover {
            background: #f8f9fa;
        }

        table.dataTable tbody tr:last-child td {
            border-bottom: 1px solid #f0f0f0;
        }

        .badge-status,
        .badge-priority {
            border-radius: 20px;
            padding: 5px 12px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .badge-status.open,
        .badge-priority.high {
            background: #249722;
            color: #fff;
        }

        .badge-status.progress,
        .badge-priority.medium {
            background: #ffc107;
            color: #212529;
            height: 18px;
        }

        .badge-status.closed,
        .badge-priority.low {
            background: #6c757d;
            color: #fff;
        }

        .action-btn {
            color: #249722;
            font-weight: 500;
            text-decoration: none;
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            transition: color 0.2s;
        }

        .action-btn:hover {
            color: #1e7e1c;
            text-decoration: underline;
        }

        .btn-primary {
            background: #249722;
            border-color: #249722;
            font-weight: 500;
        }

        .btn-primary:hover {
            background: #1e7e1c;
            border-color: #1e7e1c;
        }

        .btn-outline-primary {
            color: #249722;
            border-color: #249722;
            font-weight: 500;
        }

        .btn-outline-primary:hover {
            background: #249722;
            border-color: #249722;
            color: white;
        }

        .tooltip {
            font-size: 0.875rem;
        }

        .chat-section {
            margin-top: 30px;
        }

        .ticket-user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 12px;
        }

        .chat-input {
            width: 100%;
            border-radius: 8px;
            border: 1.5px solid #cfcfcf;
            padding: 12px 15px;
            font-size: 1rem;
            outline: none;
            transition: border 0.2s;
        }

        .chat-input:focus {
            border-color: #249722;
        }

        .btn-send {
            background: #249722;
            color: #fff;
            padding: 10px 24px;
            font-weight: 500;
            border-radius: 6px;
            border: none;
            transition: background 0.2s;
        }

        .btn-send:hover {
            background: #1e7e1c;
        }

        .quick-reply-card {
            margin-top: 25px;
        }

        .quick-reply-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }

        .quick-reply-row:last-child {
            border-bottom: none;
        }

        .btn-quickuse {
            background: none;
            border: none;
            color: #249722;
            font-weight: 500;
            text-decoration: underline;
            font-size: 0.95rem;
            cursor: pointer;
            transition: color 0.2s;
        }

        .btn-quickuse:hover {
            color: #1e7e1c;
        }

        /* FAQ Modal Styles */
        .modal-title {
            color: #249722;
            font-weight: 700;
        }

        .modal-header {
            border-bottom: 1px solid #eaeaea;
        }

        .modal-footer {
            border-top: 1px solid #eaeaea;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #dee2e6;
        }

        @media (max-width: 768px) {
            .support-tabs {
                flex-direction: column;
                gap: 0;
            }

            .support-tab-link {
                margin-right: 0;
                text-align: left;
                padding: 10px 15px;
                border-bottom: 1px solid #eaeaea;
                border-left: 3px solid transparent;
            }

            .support-tab-link.active {
                border-bottom: 1px solid #eaeaea;
                border-left: 3px solid #249722;
            }

            .card-body {
                padding: 15px;
            }

            table.dataTable thead th,
            table.dataTable tbody td {
                padding: 8px 10px;
                font-size: 0.9rem;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid py-4 px-md-4">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold mb-1">Support Management</h3>
                    <p class="text-muted mb-0">Manage support tickets and FAQs</p>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge bg-light text-dark me-2">
                        <i class="fas fa-ticket-alt me-1"></i>
                        {{ $tickets->count() }} Tickets
                    </span>
                    <span class="badge bg-light text-dark">
                        <i class="fas fa-question-circle me-1"></i>
                        {{ $faqs->count() }} FAQs
                    </span>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="support-tabs">
            <button class="support-tab-link active" id="ticketListTab" onclick="showTab('ticket-list', this)">
                <i class="fas fa-ticket-alt me-2"></i>Ticket List
            </button>
            <button class="support-tab-link" id="faqTab" onclick="showTab('faq-section', this)">
                <i class="fas fa-question-circle me-2"></i>FAQ Management
            </button>
        </div>

        <!-- Ticket Table -->
        <div id="ticket-list" class="tab-content">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Support Tickets</span>
                    <div>
                        <button class="btn btn-outline-primary btn-sm" onclick="exportTickets()">
                            <i class="fas fa-download me-1"></i> Export Excel
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="ticketsTable" class="table table-hover w-100">
                        <thead>
                            <tr>
                                <th>Ticket ID</th>
                                <th>User</th>
                                <th>Subject</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tickets as $ticket)
                                <tr>
                                    <td>{{ $ticket->ticket_id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($ticket->user->name ?? 'Unknown') }}&background=249722&color=fff"
                                                class="ticket-user-avatar" alt="">
                                            <div>
                                                <div class="fw-medium">{{ $ticket->user->name ?? '-' }}</div>
                                                <small class="text-muted">{{ $ticket->user->email ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $ticket->subject }}</td>
                                    <td>
                                        <span class="badge badge-priority {{ strtolower($ticket->priority) }}">
                                            {{ ucfirst($ticket->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge badge-status 
                                            @if (strtolower($ticket->status) == 'open') open 
                                            @elseif(strtolower($ticket->status) == 'in progress') progress 
                                            @elseif(strtolower($ticket->status) == 'closed') closed @endif">
                                            {{ ucfirst($ticket->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($ticket->assignedAgent)
                                            <div class="d-flex align-items-center">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($ticket->assignedAgent->name) }}&background=6c757d&color=fff"
                                                    width="30" height="30" class="rounded-circle me-2"
                                                    alt="">
                                                {{ $ticket->assignedAgent->name }}
                                            </div>
                                        @else
                                            <span class="text-muted">Unassigned</span>
                                        @endif
                                    </td>
                                    <td>{{ $ticket->created_at->format('M j, Y') }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <!-- View Button with Tooltip -->
                                            <a class="action-btn me-1"
                                                href="{{ route('admin.support.tickets.show', $ticket->id) }}"
                                                data-bs-toggle="tooltip" data-bs-title="View Ticket">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <!-- Close Button with Tooltip -->
                                            @if (strtolower($ticket->status) != 'Closed')
                                                <form action="{{ route('admin.support.tickets.status', $ticket->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Are you sure you want to close this ticket?')">
                                                    @csrf
                                                    <input type="hidden" name="status" value="Closed">
                                                    <button type="submit" class="action-btn text-warning"
                                                        data-bs-toggle="tooltip" data-bs-title="Close Ticket">
                                                        <i class="fas fa-times-circle"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            <!-- Delete Button with Tooltip -->
                                            <form action="{{ route('admin.support.tickets.destroy', $ticket->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete this ticket? This will also delete all related replies.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-btn text-danger"
                                                    data-bs-toggle="tooltip" data-bs-title="Delete Ticket">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">
                                        <div class="empty-state">
                                            <i class="fas fa-ticket-alt"></i>
                                            <h5>No support tickets found</h5>
                                            <p>When customers submit support requests, they will appear here.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Chat Section -->
            <div class="card chat-section">
                <div class="card-header">
                    <span>Ticket Conversation</span>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <img src="https://ui-avatars.com/api/?name=Admin+User&background=249722&color=fff"
                            class="ticket-user-avatar" alt="Admin">
                        <input type="text" id="chat-input" class="chat-input" placeholder="Type your message...">
                        <button class="btn btn-send ms-2">
                            <i class="fas fa-paper-plane me-1"></i>Send
                        </button>
                    </div>

                    <!-- Quick Replies -->
                    <div class="card quick-reply-card">
                        <div class="card-header">
                            <span>Quick Reply Templates</span>
                        </div>
                        <div class="card-body">
                            @forelse($quickReplies as $template)
                                <div class="quick-reply-row">
                                    <span>{{ $template->content }}</span>
                                    <button class="btn-quickuse"
                                        onclick="quickReply('{{ addslashes($template->content) }}')">
                                        <i class="fas fa-reply me-1"></i>Use
                                    </button>
                                </div>
                            @empty
                                <div class="text-muted text-center py-2">No quick replies found.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ Management Section (hidden by default) -->
        <div id="faq-section" class="tab-content" style="display: none;">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Frequently Asked Questions</span>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#faqCreateModal">
                        <i class="fas fa-plus me-1"></i> Add FAQ
                    </button>
                </div>
                <div class="card-body">
                    <table id="faqsTable" class="table table-hover w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Question</th>
                                <th>Answer</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($faqs as $faq)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $faq->question }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($faq->answer, 70) }}</td>
                                    <td>
                                        <button class="action-btn me-2"
                                            onclick="openEditFAQ({{ $faq->id }}, '{{ addslashes($faq->question) }}', '{{ addslashes($faq->answer) }}')">
                                            <i class="fas fa-edit me-1"></i>
                                        </button>
                                        <form action="{{ route('admin.support.faqs.destroy', $faq->id) }}" method="POST"
                                            style="display:inline;" onsubmit="return confirm('Delete this FAQ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn text-danger">
                                                <i class="fas fa-trash me-1"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">
                                        <div class="empty-state">
                                            <i class="fas fa-question-circle"></i>
                                            <h5>No FAQs yet</h5>
                                            <p>Add frequently asked questions to help customers find answers quickly.</p>
                                            <button class="btn btn-primary mt-2" data-bs-toggle="modal"
                                                data-bs-target="#faqCreateModal">
                                                <i class="fas fa-plus me-1"></i> Create Your First FAQ
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create FAQ Modal -->
    <div class="modal fade" id="faqCreateModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('admin.support.faqs.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New FAQ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Question</label>
                        <input type="text" class="form-control" name="question" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Answer</label>
                        <textarea class="form-control" name="answer" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add FAQ</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit FAQ Modal -->
    <div class="modal fade" id="faqEditModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" id="editFaqForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit FAQ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Question</label>
                        <input type="text" class="form-control" name="question" id="editFaqQuestion" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Answer</label>
                        <textarea class="form-control" name="answer" id="editFaqAnswer" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update FAQ</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Initialize DataTables
        $(document).ready(function() {

            // Initialize tooltips
            document.addEventListener('DOMContentLoaded', function() {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll(
                    '[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            });

            // Display success message from session if exists
            @if (session('success'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            // Display error message from session if exists
            @if (session('error'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: '{{ session('error') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif
            $('#ticketsTable').DataTable({
                "pageLength": 5,
                "order": [
                    [0, "desc"]
                ],
                "language": {
                    "search": "Search tickets:",
                    "lengthMenu": "Show _MENU_ tickets",
                    "info": "Showing _START_ to _END_ of _TOTAL_ tickets",
                    "paginate": {
                        "previous": "‹",
                        "next": "›"
                    }
                }
            });

            $('#faqsTable').DataTable({
                "pageLength": 5,
                "language": {
                    "search": "Search FAQs:",
                    "lengthMenu": "Show _MENU_ FAQs",
                    "info": "Showing _START_ to _END_ of _TOTAL_ FAQs",
                    "paginate": {
                        "previous": "‹",
                        "next": "›"
                    }
                }
            });
        });

        function quickReply(content) {
            document.getElementById('chat-input').value = content;
            document.getElementById('chat-input').focus();
        }

        // Tab switching logic
        function showTab(tab, el) {
            document.getElementById('ticket-list').style.display = (tab === 'ticket-list') ? 'block' : 'none';
            document.getElementById('faq-section').style.display = (tab === 'faq-section') ? 'block' : 'none';
            document.getElementById('ticketListTab').classList.toggle('active', tab === 'ticket-list');
            document.getElementById('faqTab').classList.toggle('active', tab === 'faq-section');
        }

        // FAQ Edit Modal Logic
        function openEditFAQ(id, question, answer) {
            document.getElementById('editFaqQuestion').value = question;
            document.getElementById('editFaqAnswer').value = answer;
            document.getElementById('editFaqForm').action = "{{ url('admin/support/faqs') }}/" + id;
            var faqEditModal = new bootstrap.Modal(document.getElementById('faqEditModal'));
            faqEditModal.show();
        }

        // Close ticket function
        function closeTicket(ticketId) {
            if (confirm('Are you sure you want to close this ticket?')) {
                // Create form data instead of JSON
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('status', 'closed');
                formData.append('_method', 'POST'); // If needed

                fetch("{{ url('admin/support/tickets') }}/" + ticketId + "/status", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Show success message and reload
                            showAlert('Ticket closed successfully!', 'success');
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            throw new Error(data.message || 'Error closing ticket');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('Error closing ticket: ' + error.message, 'error');
                    });
            }
        }


        function exportTickets() {
            Swal.fire({
                title: 'Exporting Tickets',
                text: 'Please wait while we prepare your export...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Get current DataTable search value
            const table = $('#ticketsTable').DataTable();
            const search = table.search();

            // Build export URL with current filters
            let exportUrl = "{{ route('admin.support.tickets.export') }}";
            let params = [];

            if (search) {
                params.push('search=' + encodeURIComponent(search));
            }

            if (params.length > 0) {
                exportUrl += '?' + params.join('&');
            }

            // Create and trigger download
            const link = document.createElement('a');
            link.href = exportUrl;
            link.target = '_blank';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            // Close loading after download starts
            setTimeout(() => {
                Swal.close();
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Tickets exported successfully!',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            }, 1000);
        }
    </script>
@endsection
