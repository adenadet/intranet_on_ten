<aside class="main-sidebar sidebar-primary bg-white">
    <a href="/home" class="brand-link bg-navy text-white">
        <img src="{{asset(config('app.logo'))}}" alt="{{config('app.name')}}" class="brand-image img-circle elevation-3 bg-white" style="opacity: 1">
        <span class="brand-text font-weight-light text-white">{{config('app.name')}}</span>
    </a>
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image"><img src="{{asset('img/profile/'.Auth::user()->image)}}" class="img-circle elevation-2" alt="{{Auth::user()->first_name}} {{Auth::user()->last_name}}"></div>
            <div class="info"><a href="#" class="d-block">{{Auth::user()->first_name}} {{Auth::user()->last_name}}</a></div>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item has-treeview"><a href="/dashboard" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
                <li class="nav-item"><a href="/profile" class="nav-link"><i class="nav-icon fas fa-user"></i><p>Profile</p></a></li>
                <li class="nav-item"><a href="/contacts" class="nav-link"><i class="nav-icon fas fa-user-friends"></i><p>Contacts</p></a></li>
                @if(Auth::user()->hasRole('E-Services') || Auth::user()->hasRole('Super Admin'))
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link"><i class="nav-icon fas fa-briefcase-medical"></i><p>Electronic Services <i class="right fas fa-angle-left"></i></p></a>
                    <ul class="nav nav-treeview">
                        @if(Auth::user()->hasRole('E-Services FO') || Auth::user()->hasRole('Super Admin'))<li class="nav-item"><a href="/eservices/front_office" class="nav-link"><i class="fas fa-laptop-medical nav-icon"></i><p>Front Office</p></a></li>@endif
                        @if(Auth::user()->hasRole('E-Services MO') || Auth::user()->hasRole('Super Admin'))<li class="nav-item"><a href="/eservices/doctor" class="nav-link"><i class="fas fa-user-md nav-icon"></i><p>Medical Officer</p></a></li>@endif
                        @if(Auth::user()->hasRole('E-Services RA') || Auth::user()->hasRole('Super Admin'))<li class="nav-item"><a href="/eservices/radiologist" class="nav-link"><i class="fa fa-x-ray nav-icon"></i><p>Radiologist</p></a></li>@endif
                        @if(Auth::user()->hasRole('E-Services Admin') || Auth::user()->hasRole('Super Admin'))<li class="nav-item"><a href="/eservices/administrator" class="nav-link"><i class="fa fa-user-cog nav-icon"></i><p>Administrator</p></a></li>@endif
                        @if(Auth::user()->hasRole('E-Services Front Admin') || Auth::user()->hasRole('Super Admin'))<li class="nav-item"><a href="/eservices/front_admin" class="nav-link"><i class="fa fa-user-cog nav-icon"></i><p>Front Administrator</p></a></li>@endif
                    </ul>
                </li>
                @endif
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link"><i class="nav-icon fas fa-calendar-day"></i><p>Leave Management <i class="right fas fa-angle-left"></i></p></a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item"><a href="/hrms/leaves/requests" class="nav-link"><i class="fas fa-laptop-medical nav-icon"></i><p>My Requests</p></a></li>
                        <li class="nav-item"><a href="/hrms/leaves/assigned" class="nav-link"><i class="fas fa-user-md nav-icon"></i><p>Assigned Leave Types</p></a></li>
                        <li class="nav-item"><a href="/hrms/leaves/team_requests" class="nav-link"><i class="fa fa-users nav-icon"></i><p>Team Requests</p></a></li>
                    </ul>
                </li>
                @if(Auth::user()->hasRole('Super Admin') || Auth::user()->can('user_management') || Auth::user()->hasRole('Human Resource'))
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link"><i class="fa fa-cogs nav-icon"></i><p>Human Resources<i class="right fas fa-angle-left"></i></p></a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item"><a href="/hrms/admin/employees" class="nav-link"><i class="far fa-dot-circle nav-icon"></i><p>Employees</p></a></li>
                        <li class="nav-item"><a href="/hrms/admin/leaves/types" class="nav-link"><i class="far fa-dot-circle nav-icon"></i><p>Leave Types</p></a></li>
                        <li class="nav-item"><a href="/hrms/admin/leaves/requests" class="nav-link"><i class="far fa-dot-circle nav-icon"></i><p>All Requests</p></a></li>
                    </ul>
                </li>
                @endif
                <li class="nav-item"><a href="/staff_month" class="nav-link"><i class="nav-icon fas fa-user-circle"></i><p>Staff of the Month</p></a></li>
                <li class="nav-item"><a href="/ticketing" class="nav-link"><i class="nav-icon fas fa-tags"></i><p>Tickets</p></a></li>
                <li class="nav-item"><a href="/notices" class="nav-link"><i class="nav-icon fas fa-clipboard"></i><p>Notices</p></a></li>
                @if(Auth::user()->hasRole('Super Admin') || Auth::user()->can('user_management') || Auth::user()->hasRole('Human Resource'))<li class="nav-item"><a href="/users" class="nav-link"><i class="nav-icon fas fa-users"></i><p>Users</p></a></li>@endif
                <li class="nav-item"><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link"><i class="nav-icon fas fa-power-off"></i><p>Log Out </p></a></li>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </ul>
        </nav>
    </div>
</aside>