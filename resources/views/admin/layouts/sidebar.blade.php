<aside id="layout-menu" class="layout-menu menu-vertical menu">
    <div class="app-brand demo">
        <a href="" class="app-brand-link">
            <span class="app-brand-logo demo">
                <span class="text-primary">
                    <svg width="32" height="22" viewBox="0 0 32 22" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z"
                            fill="currentColor" />
                        <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd"
                            d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z"
                            fill="#161616" />
                        <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd"
                            d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z"
                            fill="#161616" />
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z"
                            fill="currentColor" />
                    </svg>
                </span>
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-3">Vuexy</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="icon-base ti menu-toggle-icon d-none d-xl-block"></i>
            <i class="icon-base ti tabler-x d-block d-xl-none"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Page -->
        @if(adminAccessRoute(config('role.dashboard.access.view')))
        <li class="menu-item @activeLink('admin.dashboard')">
            <a href="{{ route('admin.dashboard') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-smart-home"></i>
                <div data-i18n="Page 1">Dashboard</div>
            </a>
        </li>

        @endif
        @if(adminAccessRoute(config('role.manage_staff.access.view')))
        <li class="menu-item @activeLink('admin.staff')">
            <a href="{{route('admin.staff')}}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-app-window"></i>
                <div data-i18n="Page 2">Roles & Permission</div>
            </a>
        </li>
        @endif


        <li class="menu-item @activeLink('admin.accounts.add')">
            <a href="{{route('admin.accounts.add')}}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-app-window"></i>
                <div data-i18n="Page 2">Add Accounts</div>
            </a>
        </li>
        <li class="menu-item @activeLink('admin.accounts')">
            <a href="{{route('admin.accounts')}}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-app-window"></i>
                <div data-i18n="Page 2">All Accounts</div>
            </a>
        </li>

         {{-- Rehan
          Date:3/18/2025 StartTime: 1:56pm --}}


        <li class="menu-item @activeLink('admin.apis.balance.add.get')">
            <a href="{{route('admin.apis.balance.add.get')}}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-app-window"></i>
                <div data-i18n="Page 2">Add Balance/Adjustment</div>
            </a>
            <li class="menu-item @activeLink('admin.balance.logs')">
                <a href="{{route('admin.balance.logs')}}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-app-window"></i>
                    <div data-i18n="Page 2">Account Balance</div>
                </a>
            </li>
            <li class="menu-item @activeLink('admin.transfer.balance')">
                <a href="{{route('admin.transfer.balance')}}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-app-window"></i>
                    <div data-i18n="Page 2">
                        Transfer Balance</div>
                </a>
            </li>
        </li>
        <li class="menu-item @activeLink('admin.groups')">
            <a href="{{route('admin.groups')}}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-app-window"></i>
                <div data-i18n="Page 2">Telegram Groups</div>
            </a>
        </li>
        <li class="menu-item @activeLink('admin.settlements')">
            <a href="{{route('admin.settlements')}}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-app-window"></i>
                <div data-i18n="Page 2">Partner Settlements</div>
            </a>
        </li>


        {{-- REHAN End --}}
        <li class="menu-item @activeLink('admin.apis')">
            <a href="{{route('admin.apis')}}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-app-window"></i>
                <div data-i18n="Page 2">API Keys</div>
            </a>
        </li>
    </ul>
</aside>
