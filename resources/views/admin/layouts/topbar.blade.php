<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">

            <!-- LOGO -->
            <div class="navbar-brand-box" style="background-color: #248907; border-bottom: 1px solid rgba(255,255,255,0.1);">
                <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset('images/logo.png') }}" alt="" class="rounded-circle" style="width: 32px; height: 32px; margin-top: 2px; background-color: white; padding: 2px;">
                    </span>
                    <span class="logo-lg d-flex align-items-center">
                        <img src="{{ asset('images/logo.png') }}" alt="" class="rounded-circle" style="width: 48px; height: 48px; margin-top: 10px; background-color: white; padding: 3px;">
                        <h3 style="margin-top: 22px; margin-left: 10px; color:white; font-size: 20px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase;">ReNue EV</h3>
                    </span>
                </a>

                <a href="{{ route('admin.dashboard') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('images/logo.png') }}" alt="" class="rounded-circle" style="width: 32px; height: 32px; margin-top: 2px; background-color: white; padding: 2px;">
                    </span>
                    <span class="logo-lg d-flex align-items-center">
                        <img src="{{ asset('images/logo.png') }}" alt="" class="rounded-circle" style="width: 48px; height: 48px; margin-top: 10px; background-color: white; padding: 3px;">
                        <h3 style="margin-top: 22px; margin-left: 10px; color:white; font-size: 20px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase;">ReNue EV</h3>
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect" id="vertical-menu-btn" style="color: #248907;">
                <i class="mdi mdi-menu"></i>
            </button>


        </div>

        <!-- Search input -->
        <!-- <div class="search-wrap" id="search-wrap">
            <div class="search-bar">
                <input class="search-input form-control" placeholder="Search" />
                <a href="#" class="close-search toggle-search" data-target="#search-wrap">
                    <i class="mdi mdi-close-circle"></i>
                </a>
            </div>
        </div> -->

        <div class="d-flex">
            <!-- <div class="dropdown d-none d-lg-inline-block">
                <button type="button" class="btn header-item toggle-search noti-icon waves-effect"
                    data-target="#search-wrap">
                    <i class="mdi mdi-magnify"></i>
                </button>
            </div> -->


            <div class="dropdown d-none d-lg-inline-block ms-1">
                <button type="button" class="btn header-item noti-icon waves-effect" data-toggle="fullscreen">
                    <i class="mdi mdi-fullscreen"></i>
                </button>
            </div>

            @php
                $admin_notifications = \App\Models\Notification::where('user_id', auth()->id())
                    ->where('is_read', false)
                    ->latest()
                    ->take(5)
                    ->get();
                $unread_count = \App\Models\Notification::where('user_id', auth()->id())
                    ->where('is_read', false)
                    ->count();
            @endphp

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-notifications-dropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="mdi mdi-bell-outline"></i>
                    @if($unread_count > 0)
                        <span class="badge bg-danger rounded-circle" style="position: absolute; top: 15px; right: 8px; font-size: 10px; padding: 2px 5px;">{{ $unread_count }}</span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                    aria-labelledby="page-header-notifications-dropdown">
                    <div class="p-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-0 font-size-16"> Notifications </h6>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('admin.notifications.index') }}" class="small" style="color: #248907; font-weight: 600; text-decoration: none;"> View All</a>
                            </div>
                        </div>
                    </div>
                    <div data-simplebar style="max-height: 230px;">
                        @forelse($admin_notifications as $notification)
                            <a href="javascript:void(0);" class="text-reset notification-item" onclick="markAsRead({{ $notification->id }})" style="display: block; padding: 12px 20px; border-bottom: 1px solid #f1f1f1;">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar-xs" style="width: 32px; height: 32px;">
                                            <span class="avatar-title rounded-circle font-size-16" style="background-color: #248907;">
                                                <i class="mdi mdi-bell-outline"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1" style="font-size: 14px; font-weight: 600; color: #333;">{{ $notification->title }}</h6>
                                        <div class="font-size-13 text-muted">
                                            <p class="mb-1" style="line-height: 1.4; color: #666;">{{ $notification->message }}</p>
                                            <p class="mb-0" style="font-size: 11px; margin-top: 4px; color: #999;"><i class="mdi mdi-clock-outline"></i> {{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="p-3 text-center">
                                <div class="avatar-md mx-auto mb-3">
                                    <div class="avatar-title h3 rounded-circle" style="background-color: #e8f5e9; color: #248907;">
                                        <i class="mdi mdi-bell-off-outline"></i>
                                    </div>
                                </div>
                                <h5 class="font-size-14 text-muted">No new notifications</h5>
                            </div>
                        @endforelse
                    </div>
                    @if($unread_count > 0)
                        <div class="p-2 border-top d-grid">
                            <a class="btn btn-sm btn-link font-size-14 text-center" href="javascript:void(0)" onclick="markAllAsRead()" style="color: #248907; font-weight: 600; text-decoration: none;">
                                <i class="mdi mdi-check-all me-1"></i> Mark all as read
                            </a>
                        </div>
                    @endif
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
                            location.reload();
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


            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                    <img src="{{ Auth::user()->profile_picture ? asset(Auth::user()->profile_picture) : 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=500&q=80' }}"
                        alt="Header Avatar" class="rounded-circle header-profile-user">

                    <span class="d-none d-xl-inline-block ms-1">
                        {{ Auth::user()->name }}
                    </span>

                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    <a class="dropdown-item" href="{{ route('admin.profile') }}">
                        <i class="mdi mdi-account-circle-outline font-size-16 align-middle me-1"></i>
                        Profile
                    </a>


                   
            </div>

           

        </div>
    </div>
</header>
