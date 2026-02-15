<!-- ========== Left Sidebar Start ========== -->
<style>
    .vertical-menu {
        background-color: #249722;
        width: 260px;
        min-height: 100vh;
        padding: 30px;
    }

    .vertical-menu ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .vertical-menu li {
        margin-bottom: 18px;
    }

    .vertical-menu a {
        display: flex;
        align-items: center;
        gap: 16px;
        color: #fff;
        background: transparent;
        border: 2px solid #fff;
        border-radius: 14px;
        padding: 8px 17px;
        font-size: 12px;
        transition: background 0.2s, color 0.2s;
        font-weight: 500;
    }

    .vertical-menu a i {
        font-size: 22px;
    }

    .vertical-menu a.active-link,
    .vertical-menu li.mm-active>a {
        background: #fff;
        color: #249722 !important;
        font-weight: 600;
        border: 2px solid #fff;
    }

    .vertical-menu a.active-link i,
    .vertical-menu li.mm-active>a i {
        color: #249722 !important;
    }

    .vertical-menu a:hover:not(.active-link) {
        background: rgba(255, 255, 255, 0.15);
    }

    /* Responsive optimization for smaller screens */
    @media (max-width: 600px) {
        .vertical-menu {
            width: 100%;
            min-width: unset;
            padding: 10px 0;
        }

        .vertical-menu a {
            padding: 12px 12px;
            font-size: 15px;
        }
    }
</style>
<div class="vertical-menu">
    <ul>
        <li class="{{ request()->routeIs('admin.dashboard') ? 'mm-active' : '' }}">
            <a href="{{ route('admin.dashboard') }}"
                class="waves-effect {{ request()->routeIs('admin.dashboard') ? 'active-link' : '' }}">
                <i class="fas fa-th-large"></i>
                Dashboard
            </a>
        </li>
        <li class="{{ request()->routeIs('admin.users.*') ? 'mm-active' : '' }}">
            <a href="{{ route('admin.users.index') }}"
                class="waves-effect {{ request()->routeIs('admin.users.*') ? 'active-link' : '' }}">
                <i class="far fa-check-circle"></i>
                User Management
            </a>
        </li>
         <li class="{{ request()->routeIs('admin.cars.*') ? 'mm-active' : '' }}">
            <a href="{{ route('admin.cars.index') }}"
                class="waves-effect {{ request()->routeIs('admin.cars.*') ? 'active-link' : '' }}">
                <i class="fas fa-car"></i>
                Car Management
            </a>
        </li>
        <li class="{{ request()->routeIs('admin.bookings.*') ? 'mm-active' : '' }}">
            <a href="{{ route('admin.bookings.index') }}"
                class="waves-effect {{ request()->routeIs('admin.bookings.*') ? 'active-link' : '' }}">
                <i class="fas fa-calendar-check"></i>
                Booking Management
            </a>
        </li>
        <li class="{{ request()->routeIs('admin.rides.*') ? 'mm-active' : '' }}">
            <a href="{{ route('admin.rides.index') }}"
                class="waves-effect {{ request()->routeIs('admin.rides.*') ? 'active-link' : '' }}">
                <i class="fas fa-shopping-cart"></i>
                Ride Management
            </a>
        </li>
       
        <!-- <li class="{{ request()->routeIs('admin.fare-promo.*') ? 'mm-active' : '' }}">
            <a href="{{ route('admin.fare-promo.index') }}"
                class="waves-effect {{ request()->routeIs('admin.fare-promo.*') ? 'active-link' : '' }}">
                <i class="fas fa-seedling"></i>
                Fare Management
            </a>
        </li> -->
        <li class="{{ request()->routeIs('admin.payment.*') ? 'mm-active' : '' }}">
            <a href="{{ route('admin.payment.index') }}"
                class="waves-effect {{ request()->routeIs('admin.payment.*') ? 'active-link' : '' }}">
                <i class="far fa-dot-circle"></i>
                Payment Management
            </a>
        </li>
        <li class="{{ request()->routeIs('admin.support.*') ? 'mm-active' : '' }}">
            <a href="{{ route('admin.support.index') }}"
                class="waves-effect {{ request()->routeIs('admin.support.*') ? 'active-link' : '' }}">
                <i class="fas fa-box"></i>
                Support Management
            </a>
        </li>
        <li class="{{ request()->routeIs('admin.settings.*') ? 'mm-active' : '' }}">
            <a href="{{ route('admin.settings.index') }}"
                class="waves-effect {{ request()->routeIs('admin.settings.*') ? 'active-link' : '' }}">
                <i class="fas fa-user-circle"></i>
                Settings
            </a>
        </li>
        <li>
            <a href="{{ route('admin.logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-power-off"></i>
                Logout
            </a>
            <form id="logout-form" action="{{ route('admin.logout') }}" method="GET" style="display: none;">
                @csrf
            </form>
        </li>
    </ul>
</div>
<!-- Left Sidebar End -->
