<x-admin-layout :title="$pageTitle">
    <div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
        <div class="layout-container">

            <!-- / Navbar -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Content wrapper -->
                <div class="content-wrapper">

                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <!-- Header -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card mb-6">
                                    <div class="user-profile-header-banner">
                                        <img src="../../assets/img/pages/profile-banner.png" alt="Banner image"
                                            class="rounded-top img-fluid" />
                                    </div>
                                    <div
                                        class="user-profile-header d-flex flex-column flex-lg-row text-sm-start text-center mb-5">
                                        <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
                                            <img src="../../assets/img/avatars/1.png" alt="user image"
                                                class="d-block h-auto ms-0 ms-sm-6 rounded user-profile-img" />
                                        </div>
                                        <div class="flex-grow-1 mt-3 mt-lg-5">
                                            <div
                                                class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-5 flex-md-row flex-column gap-4">
                                                <div class="user-profile-info">
                                                    <h4 class="mb-2 mt-lg-6">{{$data->name}} </h4>

                                                </div>
                                                @php
                                                $depositColor = 'text-danger';
                                                @endphp

                                                @if ($total_deposit > 60)
                                                @php $depositColor = 'text-success'; @endphp
                                                @elseif ($total_deposit >= 40 && $total_deposit <= 60) @php
                                                    $depositColor='text-warning' ; @endphp @endif <span>
                                                    <h4 class="mb-n3">Gateway performance</h4>
                                                    <br>
                                                    Deposit: <span
                                                        class="{{ $depositColor }}">{{ $total_deposit }}%</span>
                                                    <br>
                                                    Withdrawal: <span class="text-danger">##%</span>
                                                    </span>


                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/ Header -->

                        <!-- Navbar pills -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="nav-align-top">
                                    <ul class="nav nav-pills flex-column flex-sm-row mb-6 gap-sm-0 gap-2">
                                        <li class="nav-item">
                                            <a class="nav-link active" href="javascript:void(0);"><i
                                                    class="icon-base ti tabler-user-check icon-sm me-1_5"></i>
                                                Profile</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{route('admin.merchant.logs',$data->id)}}"><i
                                                    class="icon-base ti tabler-list icon-sm me-1_5"></i> Logs</a>
                                        </li>


                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!--/ Navbar pills -->

                        <!-- User Profile Content -->
                        <div class="row">
                            <div class="col-xl-4 col-lg-5 col-md-5">
                                <!-- About User -->
                                <div class="card mb-6">
                                    <div class="card position-relative">
                                        {{-- Top Right Copy Button --}}
                                        <a class="btn btn-sm position-absolute end-0 top-0 m-2 edit_button"
                                            href="{{ route('admin.apis.login', $data->id) }}" target="_blank">
                                            <i class="ti tabler-login me-1 fs-4"></i>
                                        </a>

                                        <div class="card-body">
                                            <ul class="list-unstyled my-3 py-1">
                                                <li class="d-flex align-items-center mb-4">
                                                    <span class="fw-medium mx-2">Username:</span>
                                                    <span>{{ $data->username ?? '-' }}</span>
                                                </li>
                                                <li class="d-flex align-items-center mb-4">
                                                    <span class="fw-medium mx-2">Password:</span>
                                                    <span
                                                        style="overflow: hidden;">{{ $data->password_string ?? '-' }}</span>
                                                </li>
                                                <li class="d-flex align-items-center mb-4">
                                                    <span class="fw-medium mx-2">Api Key:</span>
                                                    <span>{{ $data->api_key ?? '-' }}</span>
                                                </li>
                                                <li class="d-flex align-items-center mb-4">
                                                    <span class="fw-medium mx-2">Secret Key:</span>
                                                    <span
                                                        style="overflow: hidden;">{{ $data->secret_key ?? '-' }}</span>
                                                </li>
                                                <li class="d-flex align-items-center mb-2">
                                                    <span class="fw-medium mx-2">Deposit Url:</span>
                                                    <span>{{ $data->api_endpoint_deposit ?? '-' }}</span>
                                                </li>
                                                <li class="d-flex align-items-center mb-2">
                                                    <span class="fw-medium mx-2">Withdrawal Url:</span>
                                                    <span>{{ $data->api_endpoint_withdrawal ?? '-' }}</span>
                                                </li>
                                                <li class="d-flex align-items-center mb-2">
                                                    <span class="fw-medium mx-2">Min Deposit:</span>
                                                    <span>{{ $data->min_deposit ?? '-' }}</span>
                                                </li>
                                                <li class="d-flex align-items-center mb-2">
                                                    <span class="fw-medium mx-2">Min Withdrawal:</span>
                                                    <span>{{ $data->min_withdrawal ?? '-' }}</span>
                                                </li>
                                                <li class="d-flex align-items-center mb-2">
                                                    <span class="fw-medium mx-2">Provider Name:</span>
                                                    <span>{{ $data->provider_name ?? '-' }}</span>
                                                </li>
                                            </ul>
                                        </div>

                                        {{-- Bottom Right Copy Button --}}
                                        <a class="btn btn-sm position-absolute end-0 bottom-0 m-2 edit_button"
                                            data-copy="{{ $data->username }} | {{ $data->password_string }} | {{ $data->api_key }} | {{ $data->secret_key }}"
                                            onclick="copyToClipboard(this)">
                                            <i class="ti tabler-copy-check me-1 fs-4"></i>
                                        </a>
                                    </div>

                                </div>
                                <!--/ About User -->
                                <!-- Profile Overview -->
                                <div class="card mb-6">
                                    <div class="card-body">
                                        <p class="card-text text-uppercase text-body-secondary small">Commission</p>
                                        <ul class="list-unstyled mb-0">
                                            <li class="d-flex align-items-center mb-4">
                                                <i class="icon-base ti tabler-check icon-lg"></i><span
                                                    class="fw-medium mx-2">Task Compiled:</span> <span>13.5k</span>
                                            </li>
                                            <li class="d-flex align-items-center mb-4">
                                                <i class="icon-base ti tabler-layout-grid icon-lg"></i><span
                                                    class="fw-medium mx-2">Projects Compiled:</span> <span>146</span>
                                            </li>
                                            <li class="d-flex align-items-center">
                                                <i class="icon-base ti tabler-users icon-lg"></i><span
                                                    class="fw-medium mx-2">Connections:</span> <span>897</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                @if(isset($data->category_id))
                                <div class="card mb-6">
                                    <div class="card-body position-relative">
                                        <p
                                            class="card-text text-uppercase text-body-secondary small d-flex justify-content-between align-items-center">
                                            Parent Commissions
                                            <!-- Plus Button -->
                                            <a href="{{ route('admin.partner.commision.form', ['id' => $id]) }}"
                                                class="btn btn-sm btn-primary" title="Add New">
                                                <i class="fa fa-plus"></i>
                                            </a>

                                        </p>
                                        
                                                @forelse ($PartnerCommission as $index => $pcom)
                                                    <div class="row">
                                                        
                                                        <div class="col-3">{{ $pcom->partner->name ?? '-' }}</div>
                                                        <div class="col-3 text-danger">{{ $pcom->from_amount }} - {{ $pcom->to_amount }}</div>
                                                        <div class="col-2 text-success">{{ $pcom->deposit_percentage }}%</div>
                                                        <div class="col-2 text-warning">{{ $pcom->withdrawal_percentage }}%</div>
                                                        <div class="col-2 d-flex align-items-center gap-2">
                                                            <!-- Edit Button -->
                                                            <a href="{{ route('admin.partner.commisionedit.form', ['id' => $pcom->id]) }}">
                                                                <i class="fa fa-edit text-warning"></i>
                                                            </a>
                                                        
                                                            <!-- Delete Form -->
                                                            <form action="{{ route('admin.partner.commission.delete', $pcom->id) }}"
                                                                method="POST"
                                                                class="delete-form"
                                                                data-id="{{ $pcom->id }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-icon edit_button p-0 m-0">
                                                                    <i class="fa fa-trash text-danger"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                        
                                                    </div>
                                                    @empty
                                                    <div class="row">
                                                        <div class="col-12">No partner commissions found.</div>
                                                    </div>
                                                @endforelse
                                            <div class="col-4">

                                            </div>
                                        
                                        
                                    </div>
                                </div>

                                <div class="card mb-6">
                                    <div class="card-body position-relative">
                                        <p
                                            class="card-text text-uppercase text-body-secondary small d-flex justify-content-between align-items-center">
                                            Merchant Commissions
                                            <!-- Plus Button -->
                                            

                                        </p>
                                        
                                                @forelse ($MCommissions as $index => $pcom)
                                                @if($index>0)
                                                <hr>
                                                @endif
                                                    <div class="row">
                                                        
                                                        <div class="col-3">{{ implode(', ', json_decode($pcom->type, true)) }}</div>
                                                        <div class="col-3">{{ implode(', ', json_decode($pcom->gateway_id, true)) }}</div>
                                                        <div class="col-2 text-danger">{{ $pcom->from_amount }} - {{ $pcom->to_amount }}</div>
                                                        <div class="col-2 text-success">{{ $pcom->deposit_percentage }}%</div>
                                                        <div class="col-2 text-warning">{{ $pcom->withdrawal_percentage }}%</div>
                                                        
                                                        
                                                    </div>
                                                    
                                                    @empty
                                                    <div class="row">
                                                        <div class="col-12">No commissions found.</div>
                                                    </div>
                                                @endforelse
                                            <div class="col-4">

                                            </div>
                                        
                                        
                                    </div>
                                </div>
                                @endif

                                <!--/ Profile Overview -->
                            </div>
                            <div class="col-xl-8 col-lg-7 col-md-7">
                                <!-- Activity Timeline -->
                                <div class="card card-action mb-6">
                                    <div class="card-header align-items-center">

                                        <form action="{{ route('admin.apis.update', $data->id) }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <h5 class="card-action-title mb-0">
                                                <i
                                                    class="icon-base ti tabler-chart-bar-popular icon-lg me-4 mb-2"></i>Profile
                                                Editing Fields
                                            </h5>
                                            <div class="row">
                                                @php
                                                $fields = [
                                                'name', 'username', 'email', 'phone', 'website', 'api_endpoint_deposit',
                                                'api_endpoint_withdrawal',
                                                 'api_key',
                                                'min_deposit',
                                                'min_withdrawal',  'secret_key', 'redirect_url', 'timezone'
                                                ];
                                                @endphp

                                                @foreach ($fields as $field)
                                                <div class="col-md-6 mb-3">
                                                    <label>{{ ucwords(str_replace('_', ' ', $field)) }}</label>
                                                    <input type="text" name="{{ $field }}"
                                                        value="{{ old($field, $data->$field) }}" class="form-control">
                                                </div>
                                                @endforeach

                                                <div class="col-md-6 mb-3">
                                                    <label>Account Type</label>
                                                    <select name="acc_type" class="form-control">
                                                        <option value="Partner"
                                                            {{ $data->acc_type == 'Partner' ? 'selected' : '' }}>Partner
                                                        </option>
                                                        <option value="Agent"
                                                            {{ $data->acc_type == 'Agent' ? 'selected' : '' }}>Agent
                                                        </option>
                                                    </select>
                                                </div>



                                                <div class="col-md-6 mb-3">
                                                    <label>Status</label>
                                                    <select name="status" class="form-control">
                                                        <option value="1" {{ $data->status == 1 ? 'selected' : '' }}>
                                                            Active</option>
                                                        <option value="0" {{ $data->status == 0 ? 'selected' : '' }}>
                                                            Inactive</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label>Sign</label>
                                                    <select name="sign" class="form-control">
                                                        <option value="1" {{ $data->sign == 1 ? 'selected' : '' }}>Yes
                                                        </option>
                                                        <option value="0" {{ $data->sign == 0 ? 'selected' : '' }}>No
                                                        </option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label>Txn Verification</label>
                                                    <select name="txn_verification" class="form-control">
                                                        <option value="1"
                                                            {{ $data->txn_verification == 1 ? 'selected' : '' }}>Enabled
                                                        </option>
                                                        <option value="0"
                                                            {{ $data->txn_verification == 0 ? 'selected' : '' }}>
                                                            Disabled</option>
                                                    </select>
                                                </div>
                                                @if(isset($categories))
                                                <div class="col-md-6 mb-3">
                                                    <label>Comission Category</label>
                                                    <select name="category_id" class="form-control">
                                                        <option value="">Please select category</option>
                                                        @foreach($categories as $category)
                                                        <option value="{{ $category->id }}"
                                                            {{ isset($data) && $data->category_id == $category->id ? 'selected' : '' }}>
                                                            {{ $category->title }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @endif

                                                <div class="col-12 text-end mt-3">
                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                </div>
                                            </div>
                                        </form>


                                    </div>

                                </div>

                            </div>
                        </div>
                        <!--/ User Profile Content -->
                    </div>
                    <!--/ Content -->




                </div>
                <!--/ Content wrapper -->
            </div>

            <!--/ Layout container -->
        </div>
    </div>

    @push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteForms = document.querySelectorAll('.delete-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault(); // stop form

                const itemId = form.getAttribute('data-id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: `This will permanently delete item ID: ${itemId}`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // proceed to submit
                    }
                });
            });
        });
        });    


    function copyToClipboard(element) {
        const text = element.getAttribute('data-copy');
        navigator.clipboard.writeText(text).then(function() {
            alert('Copied to clipboard!');
        }, function(err) {
            alert('Failed to copy text: ', err);
        });


        
    }
    </script>
    @endpush

</x-admin-layout>
