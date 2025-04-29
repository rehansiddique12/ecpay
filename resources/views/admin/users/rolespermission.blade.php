<x-admin-layout :title="$pageTitle">
    <style>
        .fa-ellipsis-v:before {
            content: "\f142";
        }
    </style>
    @php
        $currentRoute = Route::currentRouteName();
    @endphp
    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <div class="row justify-content-between">
            <div class="col-md-12">
                <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
                <div class="row ">
                    <div class="col-md-5 gap-6 d-flex justify-content-between">
                        <div>
                            <button class="btn {{ $currentRoute == 'admin.users' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.users') }}" class="menu-link">
                                    <div data-i18n="Manual Gateway">Manual Gateway</div>
                                </a>
                            </button>
                        </div>
                        <div>
                            <button class="btn {{ $currentRoute == 'admin.location' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.location') }}" class="menu-link">
                                    <div data-i18n="Location">Location</div>
                                </a>
                            </button>
                        </div>
                        <div>
                            <button
                                class="btn {{ $currentRoute == 'admin.roles_and_permission' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.roles_and_permission') }}" class="menu-link">
                                    <div data-i18n="Roles and Permission">Roles and Permission</div>
                                </a>
                            </button>
                        </div>
                        <div>
                            <button class="btn {{ $currentRoute == 'admin.rolescategory' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.rolescategory') }}" class="menu-link">
                                    <div data-i18n="Roles Category">Roles Category</div>
                                </a>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
                    <div class="card-body">
                        <label for="" class="mb-3">Category</label>
                        <div class="col-md-3">
                            <input type="text" class="form-control" placeholder="Enter Category">
                        </div>

                        {{-- <div className="grid grid-cols-2 gap-10">
                            <div className="mt-5">
                              <div className="grid grid-cols-2 mr-15">
                                <div className=" space-y-4 text-[#5C6584]">
                                  <p>Full Name</p>
                                  <p>Status</p>
                                  <p>Domain</p>
                                </div>
                                <div className=" font-bold space-y-4 ">
                                  <p>Salman</p>
                                  <p>Active</p>
                                  <p>SlamanBhaigmail.com</p>
                                </div>
                              </div>
                            </div>
                            <div className=" mt-5">
                              <div className="grid grid-cols-2 mr-15">
                                <div className="space-y-4 text-[#5C6584]">
                                  <p>Gender</p>
                                  <p>Reason</p>
                                </div>
                                <div className="font-bold space-y-4 ml-2">
                                  <p>Male</p>
                                  <p>Accepted Email</p>
                                </div>
                              </div>
                            </div>
                          </div> --}}

                          <div class="grid grid-cols-5  d-flex justify-content-between mt-10 p-8" style="background-color: rgba(124, 137, 170, 0.404)">
                            <div >
                                <label for="" style="font-size: 18px; font-weight: 600; color: #7367f0" class="mb-4">Permissions</label>
                               <div class="d-flex flex-column gap-5">
                                <p>User Management</p>
                                <p>Merchant Management</p>
                                <p>Agent Management</p>
                                <p>Account Management</p>
                                <p>Telegram Group</p>
                                <p>Deposit Log</p>
                                <p>Withdrawal Log</p>
                                <p>Transfer Log</p>
                                <p>Manual Transfer</p>
                                <p>Balance Adjustment</p>
                                <p>Live Account Balance</p>
                                <p>Daily Account Balance</p>
                                <p>Partner Balance Log</p>
                                <p>Partner Account Balance</p>
                                <p>Partner Settlement</p>
                                <p>Cash Flow Report</p>
                                <p>Adjustment Report</p>
                                <p>Opening/Closing Report</p>
                                <p>Merchant Charges Report</p>
                                <p>Agent Commission Report</p>
                                <p>All Report</p>
                                <p>Gateway Performance</p>
                               </div>
                            </div>
                            <div >
                                <label for="" style="font-size: 18px; font-weight: 600; color: #7367f0" class="mb-4">View</label>
                                <div class="d-flex gap-10 flex-column">
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>

                                </div>
                            </div>
                            <div>
                                <label for="" style="font-size: 18px; font-weight: 600; color: #7367f0" class="mb-4">Add</label>
                                <div class="d-flex gap-10 flex-column">
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>

                                </div>
                            </div>
                            <div>
                                <label for="" style="font-size: 18px; font-weight: 600; color: #7367f0" class="mb-4">Edit</label>
                                <div class="d-flex gap-10 flex-column">
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>

                                </div>
                            </div>
                            <div>
                                <label for="" style="font-size: 18px; font-weight: 600; color: #7367f0" class="mb-4 ">Delete</label>
                                <div class="d-flex gap-10 flex-column">
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>
                                    <div class="d-flex flex-row justify-content-center items-center">
                                        <input type="checkbox" name="view" id="view" style="width: 20px; height: 20px;" >
                                    </div>

                                </div>
                            </div>
                          </div>




                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Location Modal -->


    <!-- Edit Location Modal -->


    @push('js')
    @endpush
</x-admin-layout>
