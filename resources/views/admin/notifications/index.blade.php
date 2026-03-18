@extends('admin.layouts.master')

@section('title', 'All Notifications')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">All Notifications</h6>
                    <button class="btn btn-sm btn-outline-primary" onclick="markAllAsRead()">Mark All as Read</button>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($notifications as $notification)
                            <div class="list-group-item list-group-item-action {{ $notification->is_read ? 'bg-light' : 'border-left-primary' }}" id="notification-{{ $notification->id }}">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1 fw-bold">{{ $notification->title }}</h6>
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1 text-mutedSmall">{{ $notification->message }}</p>
                                @if(!$notification->is_read)
                                    <button class="btn btn-sm btn-link p-0 text-primary" onclick="markAsRead({{ $notification->id }})">Mark as Read</button>
                                @endif
                            </div>
                        @empty
                            <div class="py-5 text-center">
                                <i class="mdi mdi-bell-off-outline h2 text-muted"></i>
                                <p class="text-muted">No notifications found.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                @if($notifications->hasPages())
                    <div class="card-footer">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    function markAsRead(id) {
        fetch("{{ url('admin/notifications') }}/" + id + "/mark-read", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(response => {
            if (response.ok) {
                // Update UI without reload
                document.getElementById('notification-' + id).classList.add('bg-light');
                document.getElementById('notification-' + id).classList.remove('border-left-primary');
                const btn = document.querySelector(`#notification-${id} button`);
                if (btn) btn.remove();
            }
        });
    }

    function markAllAsRead() {
        fetch("{{ route('admin.notifications.mark-all-read') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(response => {
            if (response.ok) {
                location.reload();
            }
        });
    }
</script>

<style>
    .border-left-primary {
        border-left: 4px solid #4e73df !important;
    }
    .text-mutedSmall {
        font-size: 0.9rem;
    }
</style>
@endsection
