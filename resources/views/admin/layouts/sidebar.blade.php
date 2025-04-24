@php
    $isAccountsActive =
        Request::routeIs('admin.accounts.add') ||
        Request::routeIs('admin.accounts') ||
        Request::routeIs('admin.balance.logs');

    $isMainActive = in_array(Route::currentRouteName(), [
        'admin.dashboard',
        'admin.staff',
        'admin.groups',
        'admin.parant',
    ]);

    $isPartnerActive = in_array(Route::currentRouteName(), [
        'admin.apis.balance.add.get',
        'admin.transfer.balance',
        'admin.settlements',
        'admin.apis',
        'admin.api.commissions',
        'admin.adjustments',
        'admin.adjustments.search',
        'admin.partner.balance',
        'admin.partner.balance.search',
        'admin.transections.apilogs'
    ]);
    $isReportsActive = in_array(Route::currentRouteName(), [
        'admin.reports.live_ewallet_balance',
        'admin.reports.daily_ewallet_summary',
        'admin.reports.daily_transection_summary',
        'admin.reports.merchant_charges_summary',
        'admin.reports.partner_account_summary',
        'admin.reports.partner_account_balance_summary',
        'admin.reports.partner_account_balance_summary_completions',
        'admin.reports.revenue_center',
        'admin.reports.logs',
        'admin.reports.cal',
        'admin.reports.cal2',
        'admin.reports.master_report',
        'admin.payment_gateway_performance_report',

        'admin.type'
    ]);
    // $isMerchantReportsActive = in_array(Route::currentRouteName(), [
    // 'partner.merchant_reports.by_date',
    // 'partner.merchant_reports.by_name',
    // 'partner.merchant_reports.by_month'

    // ]);

    $isMerchantReportsActive = in_array(Route::currentRouteName(), [
        'admin.merchant_reports.by_date',
        'admin.merchant_reports.by_name',
        'admin.merchant_reports.by_month',
    ]);

    $isTransactionActive = in_array(Route::currentRouteName(), [
        'admin.payment.log',
        'admin.payment.apiLog',
        'admin.payment.apiLogunclaimed',
        'admin.payment.report',
        'admin.payment.report.daily',
        'admin.payment.report.all',
        'admin.payout-report',
        'admin.payout.report.daily',
    ]);
    $isAccountsActive =
        Request::routeIs('admin.accounts.add') ||
        Request::routeIs('admin.accounts') ||
        Request::routeIs('admin.balance.logs') ||
        Request::routeIs('');

    $isMainActive = in_array(Route::currentRouteName(), [
        'admin.dashboard',
        'admin.staff',
        'admin.groups',
        'admin.parant',
        'admin.workboard',
    ]);
$isTransactionActive = in_array(Route::currentRouteName(), [
'admin.payment.log',
'admin.payment.apiLog',
'admin.payment.apiLogunclaimed',
'admin.payment.report',
'admin.payment.report.daily',
'admin.payment.report.all',
'admin.payout-report',
'admin.payout.report.daily',
]);
  $isAccountsActive = Request::routeIs('admin.accounts.add') ||
                      Request::routeIs('admin.accounts') ||
                      Request::routeIs('admin.balance.logs') ||
                      Request::routeIs('admin.ewallet.accounts') ||
                      Request::routeIs('') ;


  $isMainActive = in_array(Route::currentRouteName(), [
    'admin.dashboard',
    'admin.staff',
    'admin.groups',
    'admin.parant',
    'admin.workboard',
    'admin.deposit.manual.index'
  ]);

@endphp



<nav class="layout-navbar navbar navbar-expand-xl align-items-center" id="layout-navbar">
    <div class="container-xxl">
        <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4 ms-0">
            <a href="index.html" class="app-brand-link">
                <span class="app-brand-logo demo">
                    <span class="text-primary">
                        <svg width="32" height="22" viewBox="0 0 32 22" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z"
                                fill="currentColor" />
                            <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd"
                                d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z" fill="#161616" />
                            <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd"
                                d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z" fill="#161616" />
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z"
                                fill="currentColor" />
                        </svg>
                    </span>
                </span>
                <span class="app-brand-text demo menu-text fw-bold text-heading">Vuexy</span>
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
                <i class="icon-base ti tabler-x icon-sm d-flex align-items-center justify-content-center"></i>
            </a>
        </div>

        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
            <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
                <i class="icon-base ti tabler-menu-2 icon-md"></i>
            </a>
        </div>

        <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
            <ul class="navbar-nav flex-row align-items-center ms-md-auto">
                <!-- Search -->
                <li class="nav-item navbar-search-wrapper btn btn-text-secondary btn-icon rounded-pill">
                    <a class="nav-item nav-link search-toggler px-0" href="javascript:void(0);">
                        <span class="d-inline-block text-body-secondary fw-normal" id="autocomplete"></span>
                    </a>
                </li>
                <!-- /Search -->

                {{-- <li class="nav-item dropdown-language dropdown">
            <a
              class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
              href="javascript:void(0);"
              data-bs-toggle="dropdown">
              <i class="icon-base ti tabler-language icon-22px text-heading"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <a class="dropdown-item" href="javascript:void(0);" data-language="en" data-text-direction="ltr">
                  <span>English</span>
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="javascript:void(0);" data-language="fr" data-text-direction="ltr">
                  <span>French</span>
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="javascript:void(0);" data-language="ar" data-text-direction="rtl">
                  <span>Arabic</span>
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="javascript:void(0);" data-language="de" data-text-direction="ltr">
                  <span>German</span>
                </a>
              </li>
            </ul>
          </li> --}}
                <!--/ Language -->

                <!-- Style Switcher -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
                        id="nav-theme" href="javascript:void(0);" data-bs-toggle="dropdown">
                        <i class="icon-base ti tabler-sun icon-22px theme-icon-active text-heading"></i>
                        <span class="d-none ms-2" id="nav-theme-text">Toggle theme</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="nav-theme-text">
                        <li>
                            <button type="button" class="dropdown-item align-items-center active"
                                data-bs-theme-value="light" aria-pressed="false">
                                <span><i class="icon-base ti tabler-sun icon-22px me-3" data-icon="sun"></i>Light</span>
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="dark"
                                aria-pressed="true">
                                <span><i class="icon-base ti tabler-moon-stars icon-22px me-3"
                                        data-icon="moon-stars"></i>Dark</span>
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="system"
                                aria-pressed="false">
                                <span><i class="icon-base ti tabler-device-desktop-analytics icon-22px me-3"
                                        data-icon="device-desktop-analytics"></i>System</span>
                            </button>
                        </li>
                    </ul>
                </li>
                <!-- / Style Switcher-->

                <!-- Quick links  -->
                {{-- <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown">
            <a
              class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
              href="javascript:void(0);"
              data-bs-toggle="dropdown"
              data-bs-auto-close="outside"
              aria-expanded="false">
              <i class="icon-base ti tabler-layout-grid-add icon-22px text-heading"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-end p-0">
              <div class="dropdown-menu-header border-bottom">
                <div class="dropdown-header d-flex align-items-center py-3">
                  <h6 class="mb-0 me-auto">Shortcuts</h6>
                  <a
                    href="javascript:void(0)"
                    class="dropdown-shortcuts-add py-2 btn btn-text-secondary rounded-pill btn-icon"
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    title="Add shortcuts"
                    ><i class="icon-base ti tabler-plus icon-20px text-heading"></i
                  ></a>
                </div>
              </div>
              <div class="dropdown-shortcuts-list scrollable-container">
                <div class="row row-bordered overflow-visible g-0">
                  <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                      <i class="icon-base ti tabler-calendar icon-26px text-heading"></i>
                    </span>
                    <a href="app-calendar.html" class="stretched-link">Calendar</a>
                    <small>Appointments</small>
                  </div>
                  <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                      <i class="icon-base ti tabler-file-dollar icon-26px text-heading"></i>
                    </span>
                    <a href="app-invoice-list.html" class="stretched-link">Invoice App</a>
                    <small>Manage Accounts</small>
                  </div>
                </div>
                <div class="row row-bordered overflow-visible g-0">
                  <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                      <i class="icon-base ti tabler-user icon-26px text-heading"></i>
                    </span>
                    <a href="app-user-list.html" class="stretched-link">User App</a>
                    <small>Manage Users</small>
                  </div>
                  <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                      <i class="icon-base ti tabler-users icon-26px text-heading"></i>
                    </span>
                    <a href="app-access-roles.html" class="stretched-link">Role Management</a>
                    <small>Permission</small>
                  </div>
                </div>
                <div class="row row-bordered overflow-visible g-0">
                  <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                      <i class="icon-base ti tabler-device-desktop-analytics icon-26px text-heading"></i>
                    </span>
                    <a href="index.html" class="stretched-link">Dashboard</a>
                    <small>User Dashboard</small>
                  </div>
                  <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                      <i class="icon-base ti tabler-settings icon-26px text-heading"></i>
                    </span>
                    <a href="pages-account-settings-account.html" class="stretched-link">Setting</a>
                    <small>Account Settings</small>
                  </div>
                </div>
                <div class="row row-bordered overflow-visible g-0">
                  <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                      <i class="icon-base ti tabler-help-circle icon-26px text-heading"></i>
                    </span>
                    <a href="pages-faq.html" class="stretched-link">FAQs</a>
                    <small>FAQs & Articles</small>
                  </div>
                  <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                      <i class="icon-base ti tabler-square icon-26px text-heading"></i>
                    </span>
                    <a href="modal-examples.html" class="stretched-link">Modals</a>
                    <small>Useful Popups</small>
                  </div>
                </div>
              </div>
            </div>
          </li> --}}
                <!-- Quick links -->

                <!-- Notification -->
                {{-- <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
            <a
              class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
              href="javascript:void(0);"
              data-bs-toggle="dropdown"
              data-bs-auto-close="outside"
              aria-expanded="false">
              <span class="position-relative">
                <i class="icon-base ti tabler-bell icon-22px text-heading"></i>
                <span class="badge rounded-pill bg-danger badge-dot badge-notifications border"></span>
              </span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end p-0">
              <li class="dropdown-menu-header border-bottom">
                <div class="dropdown-header d-flex align-items-center py-3">
                  <h6 class="mb-0 me-auto">Notification</h6>
                  <div class="d-flex align-items-center h6 mb-0">
                    <span class="badge bg-label-primary me-2">8 New</span>
                    <a
                      href="javascript:void(0)"
                      class="dropdown-notifications-all p-2 btn btn-icon"
                      data-bs-toggle="tooltip"
                      data-bs-placement="top"
                      title="Mark all as read"
                      ><i class="icon-base ti tabler-mail-opened text-heading"></i
                    ></a>
                  </div>
                </div>
              </li>
              <li class="dropdown-notifications-list scrollable-container">
                <ul class="list-group list-group-flush">
                  <li class="list-group-item list-group-item-action dropdown-notifications-item">
                    <div class="d-flex">
                      <div class="flex-shrink-0 me-3">
                        <div class="avatar">
                          <img src="../../assets/img/avatars/1.png" alt class="rounded-circle" />
                        </div>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="small mb-1">Congratulation Lettie 🎉</h6>
                        <small class="mb-1 d-block text-body">Won the monthly best seller gold badge</small>
                        <small class="text-body-secondary">1h ago</small>
                      </div>
                      <div class="flex-shrink-0 dropdown-notifications-actions">
                        <a href="javascript:void(0)" class="dropdown-notifications-read"
                          ><span class="badge badge-dot"></span
                        ></a>
                        <a href="javascript:void(0)" class="dropdown-notifications-archive"
                          ><span class="icon-base ti tabler-x"></span
                        ></a>
                      </div>
                    </div>
                  </li>
                  <li class="list-group-item list-group-item-action dropdown-notifications-item">
                    <div class="d-flex">
                      <div class="flex-shrink-0 me-3">
                        <div class="avatar">
                          <span class="avatar-initial rounded-circle bg-label-danger">CF</span>
                        </div>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-1 small">Charles Franklin</h6>
                        <small class="mb-1 d-block text-body">Accepted your connection</small>
                        <small class="text-body-secondary">12hr ago</small>
                      </div>
                      <div class="flex-shrink-0 dropdown-notifications-actions">
                        <a href="javascript:void(0)" class="dropdown-notifications-read"
                          ><span class="badge badge-dot"></span
                        ></a>
                        <a href="javascript:void(0)" class="dropdown-notifications-archive"
                          ><span class="icon-base ti tabler-x"></span
                        ></a>
                      </div>
                    </div>
                  </li>
                  <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                    <div class="d-flex">
                      <div class="flex-shrink-0 me-3">
                        <div class="avatar">
                          <img src="../../assets/img/avatars/2.png" alt class="rounded-circle" />
                        </div>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-1 small">New Message ✉️</h6>
                        <small class="mb-1 d-block text-body">You have new message from Natalie</small>
                        <small class="text-body-secondary">1h ago</small>
                      </div>
                      <div class="flex-shrink-0 dropdown-notifications-actions">
                        <a href="javascript:void(0)" class="dropdown-notifications-read"
                          ><span class="badge badge-dot"></span
                        ></a>
                        <a href="javascript:void(0)" class="dropdown-notifications-archive"
                          ><span class="icon-base ti tabler-x"></span
                        ></a>
                      </div>
                    </div>
                  </li>
                  <li class="list-group-item list-group-item-action dropdown-notifications-item">
                    <div class="d-flex">
                      <div class="flex-shrink-0 me-3">
                        <div class="avatar">
                          <span class="avatar-initial rounded-circle bg-label-success"
                            ><i class="icon-base ti tabler-shopping-cart"></i
                          ></span>
                        </div>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-1 small">Whoo! You have new order 🛒</h6>
                        <small class="mb-1 d-block text-body">ACME Inc. made new order $1,154</small>
                        <small class="text-body-secondary">1 day ago</small>
                      </div>
                      <div class="flex-shrink-0 dropdown-notifications-actions">
                        <a href="javascript:void(0)" class="dropdown-notifications-read"
                          ><span class="badge badge-dot"></span
                        ></a>
                        <a href="javascript:void(0)" class="dropdown-notifications-archive"
                          ><span class="icon-base ti tabler-x"></span
                        ></a>
                      </div>
                    </div>
                  </li>
                  <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                    <div class="d-flex">
                      <div class="flex-shrink-0 me-3">
                        <div class="avatar">
                          <img src="../../assets/img/avatars/9.png" alt class="rounded-circle" />
                        </div>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-1 small">Application has been approved 🚀</h6>
                        <small class="mb-1 d-block text-body"
                          >Your ABC project application has been approved.</small
                        >
                        <small class="text-body-secondary">2 days ago</small>
                      </div>
                      <div class="flex-shrink-0 dropdown-notifications-actions">
                        <a href="javascript:void(0)" class="dropdown-notifications-read"
                          ><span class="badge badge-dot"></span
                        ></a>
                        <a href="javascript:void(0)" class="dropdown-notifications-archive"
                          ><span class="icon-base ti tabler-x"></span
                        ></a>
                      </div>
                    </div>
                  </li>
                  <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                    <div class="d-flex">
                      <div class="flex-shrink-0 me-3">
                        <div class="avatar">
                          <span class="avatar-initial rounded-circle bg-label-success"
                            ><i class="icon-base ti tabler-chart-pie"></i
                          ></span>
                        </div>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-1 small">Monthly report is generated</h6>
                        <small class="mb-1 d-block text-body">July monthly financial report is generated </small>
                        <small class="text-body-secondary">3 days ago</small>
                      </div>
                      <div class="flex-shrink-0 dropdown-notifications-actions">
                        <a href="javascript:void(0)" class="dropdown-notifications-read"
                          ><span class="badge badge-dot"></span
                        ></a>
                        <a href="javascript:void(0)" class="dropdown-notifications-archive"
                          ><span class="icon-base ti tabler-x"></span
                        ></a>
                      </div>
                    </div>
                  </li>
                  <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                    <div class="d-flex">
                      <div class="flex-shrink-0 me-3">
                        <div class="avatar">
                          <img src="../../assets/img/avatars/5.png" alt class="rounded-circle" />
                        </div>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-1 small">Send connection request</h6>
                        <small class="mb-1 d-block text-body">Peter sent you connection request</small>
                        <small class="text-body-secondary">4 days ago</small>
                      </div>
                      <div class="flex-shrink-0 dropdown-notifications-actions">
                        <a href="javascript:void(0)" class="dropdown-notifications-read"
                          ><span class="badge badge-dot"></span
                        ></a>
                        <a href="javascript:void(0)" class="dropdown-notifications-archive"
                          ><span class="icon-base ti tabler-x"></span
                        ></a>
                      </div>
                    </div>
                  </li>
                  <li class="list-group-item list-group-item-action dropdown-notifications-item">
                    <div class="d-flex">
                      <div class="flex-shrink-0 me-3">
                        <div class="avatar">
                          <img src="../../assets/img/avatars/6.png" alt class="rounded-circle" />
                        </div>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-1 small">New message from Jane</h6>
                        <small class="mb-1 d-block text-body">Your have new message from Jane</small>
                        <small class="text-body-secondary">5 days ago</small>
                      </div>
                      <div class="flex-shrink-0 dropdown-notifications-actions">
                        <a href="javascript:void(0)" class="dropdown-notifications-read"
                          ><span class="badge badge-dot"></span
                        ></a>
                        <a href="javascript:void(0)" class="dropdown-notifications-archive"
                          ><span class="icon-base ti tabler-x"></span
                        ></a>
                      </div>
                    </div>
                  </li>
                  <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                    <div class="d-flex">
                      <div class="flex-shrink-0 me-3">
                        <div class="avatar">
                          <span class="avatar-initial rounded-circle bg-label-warning"
                            ><i class="icon-base ti tabler-alert-triangle"></i
                          ></span>
                        </div>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-1 small">CPU is running high</h6>
                        <small class="mb-1 d-block text-body"
                          >CPU Utilization Percent is currently at 88.63%,</small
                        >
                        <small class="text-body-secondary">5 days ago</small>
                      </div>
                      <div class="flex-shrink-0 dropdown-notifications-actions">
                        <a href="javascript:void(0)" class="dropdown-notifications-read"
                          ><span class="badge badge-dot"></span
                        ></a>
                        <a href="javascript:void(0)" class="dropdown-notifications-archive"
                          ><span class="icon-base ti tabler-x"></span
                        ></a>
                      </div>
                    </div>
                  </li>
                </ul>
              </li>
              <li class="border-top">
                <div class="d-grid p-4">
                  <a class="btn btn-primary btn-sm d-flex" href="javascript:void(0);">
                    <small class="align-middle">View all notifications</small>
                  </a>
                </div>
              </li>
            </ul>
          </li> --}}
                <!--/ Notification -->

                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow p-0" href="profile" data-bs-toggle="dropdown">
                        <div class="avatar avatar-online">
                            <img src="{{ asset('public/uploads/admin/' . Auth::user()->image) }}"
                                alt="{{ Auth::user()->name }}" class="rounded-circle" />
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item mt-0" href="{{ route('admin.profile') }}">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <div class="avatar avatar-online">
                                            <img src="{{ asset('public/uploads/admin/' . Auth::user()->image) }}"
                                                alt="{{ Auth::user()->name }}" class="rounded-circle" />
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">{{ auth()->user()->username }}</h6>
                                        <small class="text-body-secondary">{{ auth()->user()->email }}</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider my-1 mx-n2"></div>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.profile') }}">
                                <i class="icon-base ti tabler-user me-3 icon-md"></i><span class="align-middle">My
                                    Profile</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.password') }}">
                                <i class="icon-base ti tabler-settings me-3 icon-md"></i><span
                                    class="align-middle">Password</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="pages-account-settings-billing.html">
                                <span class="d-flex align-items-center align-middle">
                                    <i class="flex-shrink-0 icon-base ti tabler-file-dollar me-3 icon-md"></i><span
                                        class="flex-grow-1 align-middle">Billing Plan</span>
                                    <span
                                        class="flex-shrink-0 badge bg-danger d-flex align-items-center justify-content-center">4</span>
                                </span>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider my-1 mx-n2"></div>
                        </li>
                        {{-- <li>
                <a class="dropdown-item" href="pages-pricing.html">
                  <i class="icon-base ti tabler-currency-dollar me-3 icon-md"></i
                  ><span class="align-middle">Pricing</span>
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="pages-faq.html">
                  <i class="icon-base ti tabler-question-mark me-3 icon-md"></i
                  ><span class="align-middle">FAQ</span>
                </a>
              </li> --}}
                        <li>
                            <div class="d-grid px-2 pt-2 pb-1">
                                <form method="POST" action="{{ route('admin.logout') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger btn-block d-flex align-items-center">
                                        <small class="align-middle">Logout</small>
                                        <i class="icon-base ti tabler-logout ms-2 icon-14px"></i>
                                    </button>
                                </form>
                            </div>
                        </li>
                    </ul>
                </li>
                <!--/ User -->
            </ul>
        </div>
    </div>
</nav>

<div class="layout-page">
    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Menu -->
        <aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu flex-grow-0">
            <div class="container-xxl d-flex h-100">
                <ul class="menu-inner">
                    <!-- Dashboards -->
                    <li class="menu-item {{ request()->is('dashboard') ? 'active open' : '' }}">
                    <li class="menu-item {{ $isMainActive ? 'active open' : '' }}">
                        <a href="javascript:void(0)" class="menu-link menu-toggle">
                            <i class="menu-icon icon-base ti tabler-layout-grid-add"></i>
                            <div data-i18n="Main">Main</div>
                        </a>

                        <ul class="menu-sub">
                            <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.dashboard' ? 'active' : '' }}">
                                <a href="{{ route('admin.dashboard') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-smart-home"></i>
                                    <div data-i18n="Dashboards">Dashboard</div>
                                </a>
                            </li>

                            <li class="menu-item {{ Route::currentRouteName() == 'admin.staff' ? 'active' : '' }}">
                                <a href="{{ route('admin.staff') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Roles & Permission">Roles & Permission</div>
                                </a>
                            </li>

                            <li class="menu-item {{ Route::currentRouteName() == 'admin.groups' ? 'active' : '' }}">
                                <a href="{{ route('admin.groups') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="TelegramGroup">TelegramGroup</div>
                                </a>
                            </li>

                            <li class="menu-item {{ Route::currentRouteName() == 'admin.parant' ? 'active' : '' }}">
                                <a href="{{ route('admin.parant') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Partner Group">Partner Group</div>
                                </a>
                            </li>
                            <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.workboard' ? 'active' : '' }}">
                                <a href="{{ route('admin.workboard') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="WorkBoard">WorkBoard</div>
                                </a>
                            </li>
                              </li>

                              <li class="menu-item {{ Route::currentRouteName() == 'admin.deposit.manual.index' ? 'active' : '' }}">
                                <a href="{{ route('admin.deposit.manual.index') }}" class="menu-link">
                                  <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                  <div data-i18n="Manual Gateway">Manual Gateway</div>
                                </a>
                              </li>
                        </ul>
                    </li>


                    <!-- Layouts -->
                    <li class="menu-item {{ $isAccountsActive ? 'active open' : '' }}">
                        <a href="javascript:void(0)" class="menu-link menu-toggle">
                            <i class="menu-icon icon-base ti tabler-layout-sidebar"></i>
                            <div data-i18n="Accounts">Accounts</div>
                        </a>

                        <ul class="menu-sub">
                            <li class="menu-item {{ Request::routeIs('admin.accounts.add') ? 'active' : '' }}">
                                <a href="{{ route('admin.accounts.add') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Add Accounts">Add Accounts</div>
                                </a>
                            </li>
                            <li class="menu-item {{ Request::routeIs('admin.accounts') ? 'active' : '' }}">
                                <a href="{{ route('admin.accounts') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="All Accounts">All Accounts</div>
                                </a>
                            </li>
                            <li class="menu-item {{ Request::routeIs('admin.balance.logs') ? 'active' : '' }}">
                                <a href="{{ route('admin.balance.logs') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Account Balance">Account Balance</div>
                                </a>
                            </li>
                            <li class="menu-item {{ Request::routeIs('admin.ewallet.accounts') ? 'active' : '' }}">
                                <a href="{{ route('admin.ewallet.accounts') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="E-Wallet Test">E-Wallet Test </div>
                                </a>
                            </li>
                        </ul>
                    </li>


                    <!-- Apps -->
                    <li class="menu-item {{ $isPartnerActive ? 'active open' : '' }}">
                        <a href="javascript:void(0)" class="menu-link menu-toggle">
                            <i class="menu-icon icon-base ti tabler-users"></i>
                            <div data-i18n="Partner">Partner</div>
                        </a>

                        <ul class="menu-sub">
                            <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.apis.balance.add.get' ? 'active' : '' }}">
                                <a href="{{ route('admin.apis.balance.add') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-file-dollar"></i>
                                    <div data-i18n="Add Balance/Adjustment">Add Balance/Adjustment</div>
                                </a>
                            </li>

                            <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.transfer.balance' ? 'active' : '' }}">
                                <a href="{{ route('admin.transfer.balance') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-messages"></i>
                                    <div data-i18n="Transfer Balance">Transfer Balance</div>
                                </a>
                            </li>

                            <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.settlements' ? 'active' : '' }}">
                                <a href="{{ route('admin.settlements') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-calendar"></i>
                                    <div data-i18n="Partner Settelment">Partner Settelment</div>
                                </a>
                            </li>

                            <li class="menu-item {{ Route::currentRouteName() == 'admin.apis' ? 'active' : '' }}">
                                <a href="{{ route('admin.apis') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Api Key">Api Key</div>
                                </a>
                            </li>
                            <li class="menu-item {{ Route::currentRouteName() == 'admin.api.commissions' ? 'active' : '' }}">
                                <a href="{{ route('admin.api.commissions') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Partner Commission">Partner Commission</div>
                                </a>
                            </li>
                            <li class="menu-item {{ Route::currentRouteName() == 'admin.adjustments' ? 'active' : '' }}">
                                <a href="{{ route('admin.adjustments') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Monthly Adjustments ">Monthly Adjustments </div>
                                </a>
                            </li>
                            <li class="menu-item {{ Route::currentRouteName() == 'admin.partner.balance' ? 'active' : '' }}">
                                <a href="{{ route('admin.partner.balance') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Adjustments ">Adjustments </div>
                                </a>
                            </li>
                            <li class="menu-item {{ Route::currentRouteName() == 'admin.transections.apilogs' ? 'active' : '' }}">
                                <a href="{{ route('admin.transections.apilogs') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="API Logs ">API Logs </div>
                                </a>
                            </li>
                            <li class="menu-item {{ Route::currentRouteName() == 'admin.transfer.balance' ? 'active' : '' }}">
                                <a href="{{ route('admin.transfer.balance') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Transfer Balance">Transfer Balance</div>
                                </a>
                            </li>
                        </ul>
                    </li>



                    {{-- Transaction --}}
                    <li class="menu-item {{ $isTransactionActive ? 'active open' : '' }}">
                        <a href="javascript:void(0)" class="menu-link menu-toggle">
                            <i class="menu-icon icon-base ti tabler-users"></i>
                            <div data-i18n="Transactions">Transactions</div>
                        </a>

                        <ul class="menu-sub">
                            <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.payment.log' ? 'active' : '' }}">
                                <a href="{{ route('admin.payment.log') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-file-dollar"></i>
                                    <div data-i18n="Deposit Log">Deposit Log</div>
                                </a>
                            </li>

                            <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.payout-log' ? 'active' : '' }}">
                                <a href="{{ route('admin.payout-log') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-messages"></i>
                                    <div data-i18n="Withdrawl Log">Withdrawal Log </div>
                                </a>
                            </li>

                            <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.payment.apiLog' ? 'active' : '' }}">
                                <a href="{{ route('admin.payment.apiLog') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-messages"></i>
                                    <div data-i18n="Api Deposit Log">Api Deposit Log </div>
                                </a>
                            </li>

                            <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.payment.apiLogunclaimed' ? 'active' : '' }}">
                                <a href="{{ route('admin.payment.apiLogunclaimed') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-messages"></i>
                                    <div data-i18n="Unclaimed Payment">Unclaimed Payment </div>
                                </a>
                            </li>

                            <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.payment.report' ? 'active' : '' }}">
                                <a href="{{ route('admin.payment.report') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-messages"></i>
                                    <div data-i18n="Deposit Report">Deposit Report </div>
                                </a>
                            </li>

                            <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.payment.report.daily' ? 'active' : '' }}">
                                <a href="{{ route('admin.payment.report.daily') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-messages"></i>
                                    <div data-i18n="Daily Deposit Report">Daily Deposit Report </div>
                                </a>
                            </li>

                            <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.payment.report.all' ? 'active' : '' }}">
                                <a href="{{ route('admin.payment.report.all') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-messages"></i>
                                    <div data-i18n="All Report">All Report </div>
                                </a>
                            </li>

                            <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.payout-report' ? 'active' : '' }}">
                                <a href="{{ route('admin.payout-report') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-messages"></i>
                                    <div data-i18n="Withdrawal Report">Withdrawal Report </div>
                                </a>
                            </li>

                            <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.payout.report.daily' ? 'active' : '' }}">
                                <a href="{{ route('admin.payout.report.daily') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-messages"></i>
                                    <div data-i18n="Daily Withdrawal Report">Daily Withdrawal Report </div>
                                </a>
                            </li>


                        </ul>
                    </li>


                    {{-- rehan reports --}}
                    <li class="menu-item {{ $isReportsActive ? 'active open' : '' }}">
                        <a href="javascript:void(0)" class="menu-link menu-toggle">
                            <i class="menu-icon icon-base ti tabler-users"></i>
                            <div data-i18n="Reports">Reports</div>
                        </a>
                        <ul class="menu-sub">
                            <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.reports.live_ewallet_balance' ? 'active' : '' }}">
                                <a href="{{ route('admin.reports.live_ewallet_balance') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Live E-Wallet Balance">Live E-Wallet Balance</div>
                                </a>
                            </li>
                            <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.reports.daily_ewallet_summary' ? 'active' : '' }}">
                                <a href="{{ route('admin.reports.daily_ewallet_summary') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Daily E-Wallet Summary">Daily E-Wallet Summary </div>
                                </a>
                            </li>
                            <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.reports.daily_transection_summary' ? 'active' : '' }}">
                                <a href="{{ route('admin.reports.daily_transection_summary') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Daily Transection Summary">Daily Transection Summary </div>
                                </a>
                            </li>
                            <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.reports.merchant_charges_summary' ? 'active' : '' }}">
                                <a href="{{ route('admin.reports.merchant_charges_summary') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Merchant Charges Summary">Merchant Charges Summary </div>
                                </a>
                            </li>
                            <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.reports.partner_account_summary' ? 'active' : '' }}">
                                <a href="{{ route('admin.reports.partner_account_summary') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Partner Account Summary">Partner Account Summary </div>
                                </a>
                            </li>
                            <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.reports.partner_account_balance_summary' ? 'active' : '' }}">
                                <a href="{{ route('admin.reports.partner_account_balance_summary') }}"
                                    class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Partner Account Balance Summary Creations">Partner Account Balance
                                        Summary Creations </div>
                                </a>
                            </li>
                            <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.reports.partner_account_balance_summary_completions' ? 'active' : '' }}">
                                <a href="{{ route('admin.reports.partner_account_balance_summary_completions') }}"
                                    class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Partner Account Balance Summary Completions">Partner Account
                                        Balance Summary Completions </div>
                                </a>
                            </li>
                            <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.reports.revenue_center' ? 'active' : '' }}">
                                <a href="{{ route('admin.reports.revenue_center') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Revenue Center">Revenue Center </div>
                                </a>
                            </li>
                            <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.reports.logs' ? 'active' : '' }}">
                                <a href="{{ route('admin.reports.logs') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Partner Balance Logs">Partner Balance Logs </div>
                                </a>
                            </li>
                            <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.reports.cal' ? 'active' : '' }}">
                                <a href="{{ route('admin.reports.cal') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Partner Balance R1">Partner Balance R1 </div>
                                </a>
                            </li>
                            <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.reports.cal2' ? 'active' : '' }}">
                                <a href="{{ route('admin.reports.cal2') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Partner Balance R2">Partner Balance R2 </div>
                                </a>
                            </li>
                            <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.reports.master_report' ? 'active' : '' }}">
                                <a href="{{ route('admin.reports.master_report') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Master Report">Master Report </div>
                                </a>
                            </li>
                            <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.payment.payment_gateway_report' ? 'active' : '' }}">
                                <a href="{{ route('admin.payment.payment_gateway_report') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Gateway Performance Report">Gateway Performance Report </div>
                                </a>
                            </li>
                            <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.type' ? 'active' : '' }}">
                                <a href="{{ route('admin.type') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Payment Type">Payment Type </div>
                                </a>
                            </li>
                        </ul>
                    </li>
                    {{-- <li class="menu-item {{ $isMerchantReportsActive ? 'active open' : '' }}">
                        <a href="javascript:void(0)" class="menu-link menu-toggle">
                            <i class="menu-icon icon-base ti tabler-users"></i>
                            <div data-i18n="Merchant Reports">Merchant Reports</div>
                        </a>
                        <ul class="menu-sub">
                            <li
                                class="menu-item {{ Route::currentRouteName() == 'partner.merchant_reports.by_date' ? 'active' : '' }}">
                                <a href="{{ route('partner.merchant_reports.by_date') }}" class="menu-link">
                                     <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Summary By Date">Summary By Date</div>
                                </a>
                            </li>
                            <li
                                class="menu-item {{ Route::currentRouteName() == 'partner.merchant_reports.by_name' ? 'active' : '' }}">
                                <a href="{{ route('partner.merchant_reports.by_name') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Summary By Name">Summary By Name </div>
                                </a>
                            </li>
                            <li
                                class="menu-item {{ Route::currentRouteName() == 'partner.merchant_reports.by_month' ? 'active' : '' }}">
                                <a href="{{ route('partner.merchant_reports.by_month') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Summary By Year">Summary By Year </div>
                                </a>
                            </li>

                        </ul>
                    </li> --}}
                    <li class="menu-item {{ $isMerchantReportsActive ? 'active open' : '' }}">
                        <a href="javascript:void(0)" class="menu-link menu-toggle">
                            <i class="menu-icon icon-base ti tabler-users"></i>
                            <div data-i18n="Merchant Reports">Merchant Reports</div>
                        </a>
                        <ul class="menu-sub">
                            <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.merchant_reports.by_date' ? 'active' : '' }}">
                                <a href="{{ route('admin.merchant_reports.by_date') }}" class="menu-link">
                                    <i <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Summary By Date">Summary By Date</div>
                                </a>
                            </li>
                            <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.merchant_reports.by_name' ? 'active' : '' }}">
                                <a href="{{ route('admin.merchant_reports.by_name') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Summary By Name">Summary By Name </div>
                                </a>
                            </li>
                            <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.merchant_reports.by_month' ? 'active' : '' }}">
                                <a href="{{ route('admin.merchant_reports.by_month') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Summary By Year">Summary By Year </div>
                                </a>
                            </li>

                        </ul>
                    </li>
                </ul>
            </div>
        </aside>
        <!-- / Menu -->

        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <!-- Pricing Plans -->
<<<<<<< HEAD
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                            </li>
                        </ul>
                    </div>
                @endif
=======
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

>>>>>>> 17fcd96136f2f4e7d032ae5a41ccdf8f3e804f86

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                {{ $slot }}
            </div>
        </div>
