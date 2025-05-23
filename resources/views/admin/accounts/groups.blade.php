<x-admin-layout :title="$pageTitle">
    @push('styles')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}">
    <style>
        /* Fix for Select2 inside Bootstrap modal */
        .select2-container {
            z-index: 9999 !important;
        }

        .select2-dropdown {
            z-index: 9999 !important;
        }

        .modal {
            overflow-y: auto;
        }
    </style>
    @endpush
    @php
    $currentRoute = Route::currentRouteName();
    @endphp

    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <div class="row justify-content-between">
            <div class="col-md-12">
                <div class="row ">
                    <div class="col-md-5 gap-6 d-flex justify-content-between">
                        <div>
                            <button
                                class="btn {{ $currentRoute == 'admin.ewallet.accounts.details' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.ewallet.accounts.details') }}" class="menu-link">
                                    <div data-i18n="Accounts List">Accounts List</div>
                                </a>
                            </button>
                        </div>
                        <div>
                            <button
                                class="btn {{ $currentRoute == 'admin.account_management.add_account' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.account_management.add_account') }}" class="menu-link">
                                    <div data-i18n="Add Accounts">Add Accounts</div>
                                </a>
                            </button>
                        </div>

                        <div>
                            <button
                                class="btn {{ $currentRoute == 'admin.account_management.account_group' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.account_management.account_group') }}" class="menu-link">
                                    <div data-i18n="Account Group">Account Group</div>
                                </a>
                            </button>
                        </div>

                        <div>
                            <button
                                class="btn {{ $currentRoute == 'admin.account_management.gateway' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.account_management.gateway') }}" class="menu-link">
                                    <div data-i18n="Gateway">Gateway</div>
                                </a>
                            </button>
                        </div>
                        <div>
                            <button
                                class="btn {{ $currentRoute == 'admin.account_management.add_category' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.account_management.add_category') }}" class="menu-link">
                                    <div data-i18n="Add Category">Categories</div>
                                </a>
                            </button>
                        </div>



                    </div>
                </div>

            </div>
        </div>
    </div>


    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">

            <div class="">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#groupModal" id="newCategoryButton">
                        Add Account Group
                    </button>
                </div>


                <div class="table-responsive">
                    <table class="table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">Group Name</th>
                                <th scope="col">Accounts</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groups as $group)
                            <tr>
                                <td>{{$group->group_name}}</td>
                                <td>{{$group->pairs}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>


        </div>
    </div>


        <!-- Group Modal -->
        <div class="modal fade" id="groupModal" tabindex="-1" aria-labelledby="groupModalLabel" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="groupModalLabel">Add Group</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <form action="{{ route('admin.accounts.addpairs') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="groupName" class="form-label">Group Name</label>
                                <input type="text" name="group_name" class="form-control" id="groupName"
                                    placeholder="Enter group name">
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label" >Select Pairs</label>
                                <select id="" name="pairs[]" class="form-select select2" multiple z-index="9999">
                                    @foreach($records as $accounts)
                                    <option value="{{$accounts->account_no}}"> {{$accounts->account_no}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save Group</button>
                            </div>
                        </form>

                    </div>


                </div>
            </div>
        </div>

    @push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{asset('assets/DataTables/datatables.min.js')}}"></script>
    <script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
    <script>
                   let $select = $('.select2').select2({
                        dropdownParent: $('#groupModal'), // Ensures dropdown appears inside modal
                        allowClear: true,
                        selectOnClose: true,
                    });
                    // Prevent dropdown from opening on clear
                    $select.on('select2:unselecting', function (e) {
                        $(this).data('unselecting', true);
                    });

                    $select.on('select2:opening', function (e) {
                        if ($(this).data('unselecting')) {
                            $(this).removeData('unselecting');
                            e.preventDefault();
                        }
                    });
    </script>
    @endpush
</x-admin-layout>
