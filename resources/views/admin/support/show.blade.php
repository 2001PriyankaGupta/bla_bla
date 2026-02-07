@extends('admin.layouts.master')
@section('title')
    Ticket #{{ $ticket->ticket_id }} - Support Management
@endsection

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

    .ticket-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .info-item {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 3px solid #249722;
    }

    .info-label {
        font-size: 0.85rem;
        color: #6c757d;
        font-weight: 500;
        margin-bottom: 5px;
    }

    .info-value {
        font-size: 1rem;
        color: #333;
        font-weight: 600;
    }

    .badge-status,
    .badge-priority {
        border-radius: 20px;
        padding: 6px 12px;
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
    }

    .badge-status.closed,
    .badge-priority.low {
        background: #6c757d;
        color: #fff;
    }

    .chat-messages {
        max-height: 500px;
        overflow-y: auto;
        padding: 10px;
    }

    .message {
        margin-bottom: 20px;
        padding: 15px;
        border-radius: 10px;
        position: relative;
    }

    .message-user {
        font-weight: 600;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .message-time {
        font-size: 0.8rem;
        color: #6c757d;
    }

    .message-content {
        margin-top: 8px;
        line-height: 1.5;
    }

    .message-admin {
        background: #e8f5e8;
        border-left: 4px solid #249722;
        margin-left: 40px;
    }

    .message-customer {
        background: #f8f9fa;
        border-left: 4px solid #6c757d;
        margin-right: 40px;
    }

    .user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
    }

    .chat-input-section {
        border-top: 1px solid #eaeaea;
        padding-top: 20px;
        margin-top: 20px;
    }

    .chat-input {
        width: 100%;
        border-radius: 8px;
        border: 1.5px solid #cfcfcf;
        padding: 12px 15px;
        font-size: 1rem;
        outline: none;
        transition: border 0.2s;
        resize: vertical;
        min-height: 80px;
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
        margin-top: 20px;
    }

    .quick-reply-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #eee;
        padding: 10px 0;
        cursor: pointer;
        transition: background 0.2s;
    }

    .quick-reply-row:hover {
        background: #f8f9fa;
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

    .action-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
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

    .btn-primary {
        background: #249722;
        border-color: #249722;
        font-weight: 500;
    }

    .btn-primary:hover {
        background: #1e7e1c;
        border-color: #1e7e1c;
    }

    .back-link {
        color: #249722;
        text-decoration: none;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 15px;
    }

    .back-link:hover {
        color: #1e7e1c;
        text-decoration: underline;
    }

    .ticket-description {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border-left: 3px solid #249722;
        margin: 15px 0;
    }

    @media (max-width: 768px) {
        .ticket-info-grid {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            flex-direction: column;
        }

        .message-admin,
        .message-customer {
            margin-left: 0;
            margin-right: 0;
        }
    }
</style>


@section('content')
    <div class="container-fluid py-4 px-md-4">
        <!-- Back Button -->
        <a href="{{ route('admin.support.index') }}" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Tickets
        </a>

        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h3 class="fw-bold mb-1">Ticket {{ $ticket->ticket_id }}</h3>
                    <p class="text-muted mb-0">{{ $ticket->subject }}</p>
                </div>
                <div class="action-buttons mt-2 mt-md-0">
                    @if (strtolower($ticket->status) != 'closed')
                        <form action="{{ route('admin.support.tickets.status', $ticket->id) }}" method="POST"
                            class="d-inline">
                            @csrf
                            <input type="hidden" name="status" value="Closed">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-times-circle me-1"></i> Close Ticket
                            </button>
                        </form>
                    @endif

                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignAgentModal">
                        <i class="fas fa-user-plus me-1"></i> Assign Agent
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column - Ticket Info & Conversation -->
            <div class="col-lg-8">
                <!-- Ticket Information -->
                <div class="card">
                    <div class="card-header">
                        <span>Ticket Information</span>
                    </div>
                    <div class="card-body">
                        <div class="ticket-info-grid">
                            <div class="info-item">
                                <div class="info-label">Status</div>
                                <div class="info-value">
                                    <span
                                        class="badge badge-status 
                                        @if (strtolower($ticket->status) == 'open') open 
                                        @elseif(strtolower($ticket->status) == 'in progress') progress 
                                        @elseif(strtolower($ticket->status) == 'closed') closed @endif">
                                        {{ ucfirst($ticket->status) }}
                                    </span>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-label">Priority</div>
                                <div class="info-value">
                                    <span class="badge badge-priority {{ strtolower($ticket->priority) }}">
                                        {{ ucfirst($ticket->priority) }}
                                    </span>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-label">Created</div>
                                <div class="info-value">{{ $ticket->created_at->format('M j, Y g:i A') }}</div>
                            </div>

                            <div class="info-item">
                                <div class="info-label">Last Updated</div>
                                <div class="info-value">{{ $ticket->updated_at->format('M j, Y g:i A') }}</div>
                            </div>
                        </div>

                        <!-- Assigned Agent -->
                        <div class="info-item">
                            <div class="info-label">Assigned Agent</div>
                            <div class="info-value">
                                @if ($ticket->assignedAgent)
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($ticket->assignedAgent->name) }}&background=249722&color=fff"
                                            class="user-avatar me-2" alt="">
                                        {{ $ticket->assignedAgent->name }}
                                    </div>
                                @else
                                    <span class="text-muted">Unassigned</span>
                                @endif
                            </div>
                        </div>

                        <!-- Ticket Description -->
                        @if ($ticket->description)
                            <div class="ticket-description">
                                <div class="info-label">Description</div>
                                <div class="info-value">{{ $ticket->description }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Conversation -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Conversation</span>
                        <span class="badge bg-light text-dark">
                            {{ $ticket->replies->count() + 1 }} messages
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="chat-messages">
                            <!-- Original Ticket Message -->
                            <div class="message message-customer">
                                <div class="message-user">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($ticket->user->name ?? 'Customer') }}&background=6c757d&color=fff"
                                        class="user-avatar" alt="">
                                    {{ $ticket->user->name ?? 'Customer' }}
                                    <span class="message-time">{{ $ticket->created_at->format('M j, g:i A') }}</span>
                                </div>
                                <div class="message-content">
                                    {{ $ticket->description ?? 'No description provided.' }}
                                </div>
                            </div>

                            <!-- Replies -->
                            @forelse($ticket->replies as $reply)
                                <div
                                    class="message {{ $reply->user->user_type === 'admin' ? 'message-admin' : 'message-customer' }}">
                                    <div class="message-user">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($reply->user->name) }}&background={{ $reply->user->user_type === 'admin' ? '249722' : '6c757d' }}&color=fff"
                                            class="user-avatar" alt="">
                                        {{ $reply->user->name }}
                                        @if ($reply->user->user_type === 'admin')
                                            <span class="badge bg-success">Staff</span>
                                        @endif
                                        <span class="message-time">{{ $reply->created_at->format('M j, g:i A') }}</span>
                                    </div>
                                    <div class="message-content">
                                        {{ $reply->message }}
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-comments fa-2x mb-2"></i>
                                    <p>No replies yet. Be the first to respond!</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Reply Form -->
                        @if (strtolower($ticket->status) != 'closed')
                            <div class="chat-input-section">
                                <form action="{{ route('admin.support.tickets.reply', $ticket->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <textarea class="chat-input" name="message" placeholder="Type your reply..." required></textarea>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <button type="submit" class="btn btn-send">
                                            <i class="fas fa-paper-plane me-1"></i> Send Reply
                                        </button>
                                        <small class="text-muted">Press Enter to send</small>
                                    </div>
                                </form>
                            </div>
                        @else
                            <div class="alert alert-info text-center mt-3">
                                <i class="fas fa-info-circle me-2"></i>
                                This ticket is closed. No further replies can be sent.
                            </div>
                        @endif
                    </div>
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
                                    onclick="useQuickReply('{{ addslashes($template->content) }}')">
                                    <i class="fas fa-reply me-1"></i> Use
                                </button>
                            </div>
                        @empty
                            <div class="text-muted text-center py-2">No quick replies found.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Column - Actions & Customer Info -->
            <div class="col-lg-4">
                <!-- Customer Information -->
                <div class="card">
                    <div class="card-header">
                        <span>Customer Information</span>
                    </div>
                    <div class="card-body">
                        @if ($ticket->user)
                            <div class="text-center mb-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($ticket->user->name) }}&background=6c757d&color=fff&size=80"
                                    class="user-avatar mb-2" style="width: 80px; height: 80px;" alt="">
                                <h6 class="fw-bold mb-1">{{ $ticket->user->name }}</h6>
                                <p class="text-muted mb-2">{{ $ticket->user->email }}</p>
                            </div>

                            <div class="info-item mb-2">
                                <div class="info-label">Member Since</div>
                                <div class="info-value">{{ $ticket->user->created_at->format('M j, Y') }}</div>
                            </div>

                            <div class="info-item">
                                <div class="info-label">Total Tickets</div>
                                <div class="info-value">
                                    @if ($ticket->user && $ticket->user->tickets)
                                        {{ $ticket->user->tickets->count() }}
                                    @else
                                        0
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-user-slash fa-2x mb-2"></i>
                                <p>Customer information not available</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Ticket Actions -->
                <div class="card">
                    <div class="card-header">
                        <span>Ticket Actions</span>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if (strtolower($ticket->status) == 'open')
                                <form action="{{ route('admin.support.tickets.status', $ticket->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="In Progress">
                                    <button type="submit" class="btn btn-outline-primary w-100 mb-2">
                                        <i class="fas fa-play-circle me-1"></i> Mark In Progress
                                    </button>
                                </form>
                            @endif

                            @if (strtolower($ticket->status) == 'in progress')
                                <form action="{{ route('admin.support.tickets.status', $ticket->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="Open">
                                    <button type="submit" class="btn btn-outline-primary w-100 mb-2">
                                        <i class="fas fa-undo me-1"></i> Reopen Ticket
                                    </button>
                                </form>
                            @endif



                            @if (strtolower($ticket->status) != 'closed')
                                <form action="{{ route('admin.support.tickets.status', $ticket->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="Closed">
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="fas fa-times-circle me-1"></i> Close Ticket
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- System Information -->
                <div class="card">
                    <div class="card-header">
                        <span>System Information</span>
                    </div>
                    <div class="card-body">
                        <div class="info-item mb-2">
                            <div class="info-label">Ticket ID</div>
                            <div class="info-value">{{ $ticket->ticket_id }}</div>
                        </div>

                        <div class="info-item mb-2">
                            <div class="info-label">Category</div>
                            <div class="info-value">{{ $ticket->category ?? 'General' }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Created</div>
                            <div class="info-value">{{ $ticket->created_at->format('M j, Y g:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Agent Modal -->
    <div class="modal fade" id="assignAgentModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="POST"
                action="{{ route('admin.support.tickets.assign', $ticket->id) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Assign Support Agent</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Agent</label>
                        <select class="form-select" name="agent_id" required>
                            <option value="">-- Select an agent --</option>
                            @foreach ($agents as $agent)
                                <option value="{{ $agent->id }}"
                                    {{ $ticket->assigned_to == $agent->id ? 'selected' : '' }}>
                                    {{ $agent->name }} ({{ $agent->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Agent</button>
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
        function useQuickReply(content) {
            document.querySelector('textarea[name="message"]').value = content;
            document.querySelector('textarea[name="message"]').focus();
        }

        // Auto-scroll to bottom of chat messages
        document.addEventListener('DOMContentLoaded', function() {
            const chatMessages = document.querySelector('.chat-messages');
            if (chatMessages) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            // Enter to send (with Shift+Enter for new line)
            const textarea = document.querySelector('textarea[name="message"]');
            if (textarea) {
                textarea.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        this.form.submit();
                    }
                });
            }
        });

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
    </script>
@endsection
