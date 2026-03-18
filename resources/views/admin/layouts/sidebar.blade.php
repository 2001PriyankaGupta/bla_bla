<!-- ========== Left Sidebar Start ========== -->
<style>
    :root {
        --sidebar-bg: #248907; /* Updated to use the correct brand green */
        --sidebar-active-bg: #ffffff;
        --sidebar-active-text: #248907;
        --sidebar-text: #ffffff;
        --sidebar-hover-bg: rgba(255, 255, 255, 0.15);
        --transition-speed: 0.3s;
    }

    .vertical-menu {
        background-color: var(--sidebar-bg) !important;
        width: 260px;
        min-height: 100vh;
        padding: 20px 15px !important; /* Balanced padding */
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        z-index: 1001;
        transition: all var(--transition-speed) ease;
    }

    /* Fixed alignment for collapsed state in typical themes */
    body.vertical-collpsed .vertical-menu {
        padding: 20px 10px !important;
        width: 70px !important;
    }

    .vertical-menu ul {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        align-items: center; /* Helper for centering icons when collapsed */
    }

    .vertical-menu li {
        margin-bottom: 12px;
        width: 100%;
    }

    .vertical-menu a {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 12px;
        color: var(--sidebar-text);
        background: transparent;
        border: 1.5px solid rgba(255, 255, 255, 0.4);
        border-radius: 12px;
        padding: 10px 15px;
        font-size: 13.5px;
        transition: all var(--transition-speed);
        font-weight: 500;
        text-decoration: none;
        white-space: nowrap;
        overflow: hidden;
        width: 100%;
        box-sizing: border-box;
    }

    .vertical-menu a i {
        font-size: 18px;
        min-width: 20px;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Target the text part specifically to hide it when collapsed if the theme uses a specific class */
    .vertical-menu a span {
        transition: opacity var(--transition-speed);
    }

    /* Expanded state specific adjustments */
    .vertical-menu a.active-link,
    .vertical-menu li.mm-active>a {
        background: var(--sidebar-active-bg) !important;
        color: var(--sidebar-active-text) !important;
        font-weight: 700;
        border: 1.5px solid var(--sidebar-active-bg);
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }

    .vertical-menu a.active-link i,
    .vertical-menu li.mm-active>a i {
        color: var(--sidebar-active-text) !important;
    }

    .vertical-menu a:hover:not(.active-link) {
        background: var(--sidebar-hover-bg);
        border-color: rgba(255, 255, 255, 0.8);
        transform: translateY(-1px);
    }

    /* Handling collapsed state alignment - assuming Skote theme defaults or similar */
    body.vertical-collpsed .vertical-menu a {
        justify-content: center;
        padding: 10px;
        border-radius: 10px;
    }

    body.vertical-collpsed .vertical-menu a span {
        display: none;
    }

    body.vertical-collpsed .vertical-menu a i {
        font-size: 22px;
        margin-right: 0 !important;
    }

    /* Force visibility of icons in collapsed mode */
    .vertical-menu i {
        flex-shrink: 0;
    }

    /* Responsive optimization for mobile */
    @media (max-width: 991px) {
        .vertical-menu {
            width: 250px;
            padding: 15px 10px !important;
        }
        
        body.sidebar-enable .vertical-menu {
            left: 0;
        }
    }
</style>

<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <ul id="side-menu">
            <li class="{{ request()->routeIs('admin.dashboard') ? 'mm-active' : '' }}">
                <a href="{{ route('admin.dashboard') }}"
                    class="waves-effect {{ request()->routeIs('admin.dashboard') ? 'active-link' : '' }}">
                    <i class="mdi mdi-view-dashboard-outline"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <li class="{{ request()->routeIs('admin.users.*') ? 'mm-active' : '' }}">
                <a href="{{ route('admin.users.index') }}"
                    class="waves-effect {{ request()->routeIs('admin.users.*') ? 'active-link' : '' }}">
                    <i class="mdi mdi-account-group-outline"></i>
                    <span>User Management</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.cars.*') ? 'mm-active' : '' }}">
                <a href="{{ route('admin.cars.index') }}"
                    class="waves-effect {{ request()->routeIs('admin.cars.*') ? 'active-link' : '' }}">
                    <i class="mdi mdi-car-multiple"></i>
                    <span>Car Management</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.bookings.*') ? 'mm-active' : '' }}">
                <a href="{{ route('admin.bookings.index') }}"
                    class="waves-effect {{ request()->routeIs('admin.bookings.*') ? 'active-link' : '' }}">
                    <i class="mdi mdi-calendar-check-outline"></i>
                    <span>Booking Management</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.rides.*') ? 'mm-active' : '' }}">
                <a href="{{ route('admin.rides.index') }}"
                    class="waves-effect {{ request()->routeIs('admin.rides.*') ? 'active-link' : '' }}">
                    <i class="mdi mdi-map-marker-path"></i>
                    <span>Ride Management</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.payment.*') ? 'mm-active' : '' }}">
                <a href="{{ route('admin.payment.index') }}"
                    class="waves-effect {{ request()->routeIs('admin.payment.*') ? 'active-link' : '' }}">
                    <i class="mdi mdi-credit-card-outline"></i>
                    <span>Payment Management</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.support.*') ? 'mm-active' : '' }}">
                <a href="{{ route('admin.support.index') }}"
                    class="waves-effect {{ request()->routeIs('admin.support.*') ? 'active-link' : '' }}">
                    <i class="mdi mdi-face-agent"></i>
                    <span>Support Management</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.settings.*') ? 'mm-active' : '' }}">
                <a href="{{ route('admin.settings.index') }}"
                    class="waves-effect {{ request()->routeIs('admin.settings.*') ? 'active-link' : '' }}">
                    <i class="mdi mdi-cog-outline"></i>
                    <span>Settings</span>
                </a>
            </li>

            <li >
                <a href="{{ route('admin.logout') }}" 
                   class="text-danger-custom"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   style="border-color: rgba(255,255,255,0.2);">
                    <i class="mdi mdi-logout shadow" style="color: #ffcccc;"></i>
                    <span>Logout</span>
                </a>
                <form id="logout-form" action="{{ route('admin.logout') }}" method="GET" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
    </div>
</div>
<!-- Left Sidebar End -->

<!-- Left Sidebar End -->
