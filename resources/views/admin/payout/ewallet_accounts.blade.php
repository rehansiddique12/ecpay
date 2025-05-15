<x-admin-layout :title="$pageTitle">
<style>
    .dot {
        display: inline-block;
        width: 20px;
        height: 20px;
        background-color: green;
        border-radius: 50%;
        margin: 10px;
        opacity: 0;
        /* Initially hidden */
        transition: opacity 0.5s ease;
        /* Smooth transition for visibility */
    }

    .reddot {
        display: inline-block;
        width: 20px;
        height: 20px;
        background-color: red;
        border-radius: 50%;
        margin: 10px;
    }
    #pagination{
        margin-top: 1rem;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
            <div class="card-body">


                <div class="table-responsive">
                    <table class="categories-show-table table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">E-Wallet Account No.</th>
                                <th scope="col">E-Wallet Name</th>
                                <th scope="col">@lang('Type')</th>
                                <th scope="col">Live Status</th>
                                <th scope="col">Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $key => $item)
                            <tr>
                                <td>
                                    {{ $item['account_no'] }}
                                </td>
                                <td>
                                    {{ $item['e_wallet_name'] }}
                                </td>

                                <td>
                                    {{ $item['type'] }}
                                </td>

                                <td>
                                    <span id="status-indicator-{{ $item['id'] }}" class="{{ $item['live'] == 1 ? 'dot' : 'reddot' }}"></span>
                                </td>
                                <td data-label="@lang('Status')" class="text-lg-center text-right">
                                    @if($item->status == 1)
                                    <span class="badge badge-light"> <i class="icon-base ti tabler-IconCircleFilled me-1 text-success"></i> @lang('Active')</span>
                                    @else
                                    <span class="badge badge-light"> <i class="icon-base ti tabler-IconCircleFilled text-danger me-1 text-success"></i> @lang('Inactive')</span>
                                    @endif


                                </td>
                                <td data-label="@lang('Action')">
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="icon-base ti tabler-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <!-- active / deactive button here -->
                                            @if(adminAccessRoute(config('role.e_wallet_accounts_test.access.edit')))
                                            <button type="button" class="btn btn-sm edit_button" data-bs-toggle="modal" data-bs-target="#newModalb" onclick="setBalanceItem({{ $item['id'] }}, '{{ $item['e_wallet_name'] }}')">
                                                <i class="icon-base ti tabler-cash-banknote me-1 text-success"></i>  Test Account
                                            </button><br>
                                            <a href="{{ route('admin.e_wallet_accounts.toggle_status', $item['id']) }}" class="btn btn-sm">
                                                @if($item->status == 1)
                                                <i class="icon-base ti tabler-circle-dashed-x me-1 text-danger"></i> @lang('Deactivate')
                                                @else
                                                <i class="icon-base ti tabler-check me-1 text-success"></i>  @lang('Activate')
                                                @endif
                                            </a>
                                            @endif

                                        </div>
                                    </div>
                                </td>
                            </tr>


                            @empty
                            <tr>
                                <td colspan="100%">
                                    <p class="text-dark">@lang('No Data Found')</p>
                                </td>
                            </tr>

                            @endforelse

                        </tbody>

                    </table>
                   {{-- {{ $records->appends($_GET)->links('partials.pagination') }} --}}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
            <div class="card-body">
                <h3>Admin E-wallet Testing Accounts</h3>
                @if(adminAccessRoute(config('role.e_wallet_accounts_test.access.add')))
                <button type="button" class="btn btn-success btn-sm edit_button mb-3" data-bs-toggle="modal" data-bs-target="#newModalc">
                    Add Admin Account
                </button>
                @endif
                <div class="table-responsive">
                    <table class="categories-show-table table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">E-Wallet Account No.</th>
                                <th scope="col">E-Wallet Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($accounts as $key => $item)
                            <tr>
                                <td>
                                    {{ $item['acc_no'] }}
                                </td>
                                <td>
                                    {{ $item['e_wallet_name'] }}
                                </td>

                                <td data-label="@lang('Action')">
                                    @if(adminAccessRoute(config('role.e_wallet_accounts_test.access.delete')))
                                        <a href="#" class="btn btn-sm edit_button text-danger delete-role" data-id="{{ $item['id'] }}">
                                            <i class="icon-base ti tabler-trash me-1"></i> Delete
                                        </a>
                                    @endif
                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="100%">
                                    <p class="text-dark">@lang('No Data Found')</p>
                                </td>
                            </tr>

                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="modal modal-top fade" id="newModalb" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalTopTitle">@lang('E-Wallet Test')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addBalanceForm" action="{{ route('admin.deposit.test') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row justify-content-between align-items-center">


                        <input type="text" id="balanceInput" class="form-control" name="gateway">
                        <input type="text" id="account_id" class="form-control" name="account_id">
                        <input type="text" id="orderid" class="form-control" name="orderid">
                        <input type="text" id="wid" class="form-control" name="wid">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">Admin Acc. No.</label>
                                <select required name="account_no" id="acc_no" class="form-control">
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">Amount</label>
                                <input type="text" value="200" class="form-control" name="amount" readonly />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <!-- <button type="button" id="runDepositTest" class="btn btn-primary">Run Test</button> -->
                            <button type="button" id="runWithdrawalTest" class="btn btn-primary mt-3">Run Test</button>
                            <br>
                            <br>
                            Withdrawal
                            <span id="spinner2" style="display: none;">
                                <span class="spinner-border text-primary" role="status">
                                </span>
                            </span>
                            <span id="tickMark2" style="display: none;">
                                <i class="fa fa-check-circle text-success"></i>
                            </span>
                            <br>
                            <br>
                            Deposit
                            <span id="spinner" style="display: none;">
                                <span class="spinner-border text-primary" role="status">
                                </span>
                            </span>
                            <span id="tickMark" style="display: none;">
                                <i class="fa fa-check-circle text-success"></i>
                            </span>

                        </div>

                        <!-- <br>
                        <br> -->

                        <!-- <div class="col-md-12">
                            <button type="button" disabled id="runWithdrawalTest" class="btn btn-primary">Run Withdrawal Test</button>

                        </div> -->
                    </div>

                </div>
        </div>
        </form>
    </div>
</div>

<div class="modal modal-top fade" id="newModalc" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalTopTitle">@lang('Add Admin Account')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.ewallet.accounts.add') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row justify-content-between align-items-center">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">Account No.</label>
                                <input type="text" class="form-control" name="acc_no" required />
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">

                                <input id="Nagad" value="Nagad" type="radio" checked name="e_wallet_name" />
                                <label for="Nagad" class="pr-3">Nagad</label>
                                <br>
                                <input id="Rocket" value="Rocket" type="radio" name="e_wallet_name" />
                                <label for="Rocket" class="pr-3">Rocket</label>
                                <br>
                                <input id="bKash" value="bKash" type="radio" name="e_wallet_name" />
                                <label for="bKash" class="pr-3">bKash</label>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">@lang('Add')</button>
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </form>
        </div>
    </div>
</div>


@push('js')
<script>
    "use strict";
    $(document).ready(function(e) {
        $('#image').change(function() {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#image_preview_container').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        });
    });
</script>
<script>

$(document).on('click', '.delete-role', function(e) {
        e.preventDefault();
        var roleId = $(this).data('id');
        var url = '{{ route("admin.ewallet.accounts.delete", ":id") }}'.replace(':id', roleId);

        // SweetAlert2 confirmation dialog
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: roleId
                    },
                    success: function(response) {
                        // Handle success
                        Swal.fire(
                            'Deleted!',
                            response.message, // Success message
                            'success'
                        );

                        // Refresh the datatable
                        $('.categories-show-table').DataTable().ajax.reload(null, false);
                    },
                    error: function(xhr, status, error) {
                        // Handle error
                        Swal.fire(
                            'Error!',
                            'There was an error deleting the role.',
                            'error'
                        );
                    }
                });
            }
        });
    });

    function setBalanceItem(itemId, eWallet) {
        // Find the input field in the modal
        // $('#runDepositTest').prop('disabled', false);
        var balanceInput = document.getElementById("balanceInput");
        balanceInput.value = eWallet;
        var account_id = document.getElementById("account_id");
        account_id.value = itemId;


        var accounts = <?php echo json_encode($accounts); ?>;
        console.log(accounts);
        var accNoSelect = $('#acc_no');
        accNoSelect.empty();
        var filteredAccounts = accounts.filter(function(account) {
            return account.e_wallet_name === eWallet;
        });
        // Append options to acc_no select
        filteredAccounts.forEach(function(account) {
            var option = $('<option></option>').attr('value', account.acc_no).text(account.acc_no);
            accNoSelect.append(option);
        });


    }
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {


        setInterval(function() {
            const dots = document.querySelectorAll(".dot");
            dots.forEach(function(dot) {
                if (dot.style.opacity === "0") {
                    dot.style.opacity = "1";
                } else {
                    dot.style.opacity = "0";
                }
            });
        }, 700);
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Function to send AJAX request to update live status
        function updateLiveStatus(itemId) {
            const url = "{{ route('admin.update.status', ['id' => '__id__']) }}".replace('__id__', itemId);

            // Send AJAX request
            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // console.log(data);

                    // Check if the element exists before updating its className
                    const statusIndicator = document.getElementById('status-indicator-' + data.id);
                    if (statusIndicator) {
                        statusIndicator.className = data.live ? 'dot' : 'reddot';
                    } else {
                        console.error('Element not found:', 'status-indicator-' + data.id);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Run the updateLiveStatus function every minute
        setInterval(function() {
            // Get all items and update their live status
            const items = document.querySelectorAll('[id^="status-indicator-"]');
            items.forEach(function(item) {
                const itemId = item.id.split('-')[2]; // Extract the ID from the element ID
                updateLiveStatus(itemId);
            });
        }, 10000); // 60000 milliseconds = 1 minute

        // Initial call to update live status
        updateLiveStatus();
    });
</script>
<script>
    $(document).ready(function() {
        var intervalId; // To store the interval id
        var orderid = document.getElementById("orderid");
        var wid = document.getElementById("wid");
        var acc_no = document.getElementById("acc_no");

        // orderid.value = eWallet;

        function runDepositTest() {
            if (acc_no.value === "") {
                alert("Please select an Admin Account");
                return;
            }
            $('#spinner').show(); // Show spinner
            // $('#runDepositTest').prop('disabled', true);

            var formData = new FormData($('#addBalanceForm')[0]);

            $.ajax({
                type: "POST",
                url: $('#addBalanceForm').attr('action'),
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                data: formData, // Use FormData object
                processData: false, // Don't process the data
                contentType: false, // Don't set contentType
                success: function(response) {
                    console.log(response);
                    intervalId = setInterval(performAjax, 5000);
                    orderid.value = response.orderid;
                    // if (response === "success") {
                    //     $('#spinner').hide();
                    //     $('#tickMark').show();
                    //     clearInterval(intervalId);
                    // } else {


                    // }
                },
                error: function(xhr, status, error) {
                    // Handle error
                    console.error(xhr.responseText);
                    $('#spinner').hide(); // Hide spinner
                    // $('#runDepositTest').prop('disabled', false);
                    alert('An error occurred while processing your request. Please try again.');
                }
            });
        }

        // Function to perform the AJAX call
        function performAjax() {
            $('#spinner').show(); // Show spinner
            // $('#runDepositTest').prop('disabled', true);

            var formData = new FormData($('#addBalanceForm')[0]); // Get form data

            $.ajax({
                type: "POST",
                url: "{{ route('admin.deposit.testp') }}",
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                data: formData, // Use FormData object
                processData: false, // Don't process the data
                contentType: false, // Don't set contentType
                success: function(response) {
                    console.log(response);
                    if (response.result === "success") {
                        $('#spinner').hide(); // Hide spinner
                        $('#tickMark').show(); // Show tick mark
                        $('#runWithdrawalTest').prop('disabled', true);
                        clearInterval(intervalId); // Stop the interval
                    }
                },
                error: function(xhr, status, error) {
                    // Handle error
                    console.error(xhr.responseText);
                    $('#spinner').hide(); // Hide spinner
                    $('#runWithdrawalTest').prop('disabled', false);
                    alert('An error occurred while processing your request. Please try again.');
                }
            });
        }

        $('#runWithdrawalTest').click(function() {
            if (acc_no.value === "") {
                alert("Please select an Admin Account");
                return;
            }
            $('#spinner2').show(); // Show spinner
            $('#runWithdrawalTest').prop('disabled', true); // Disable button

            var formData = new FormData($('#addBalanceForm')[0]); // Get form data

            $.ajax({
                type: "POST",
                url: "{{ route('admin.withdrawal.test') }}",
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                data: formData, // Use FormData object
                processData: false, // Don't process the data
                contentType: false, // Don't set contentType
                success: function(response) {
                    console.log(response);
                    intervalId = setInterval(wperformAjax, 5000);
                    wid.value = response.orderid;
                    // if (response === "success") {
                    //     $('#spinner').hide();
                    //     $('#tickMark').show();
                    //     clearInterval(intervalId);
                    // } else {


                    // }
                },
                error: function(xhr, status, error) {
                    // Handle error
                    console.error(xhr.responseText);
                    $('#spinner2').hide(); // Hide spinner
                    $('#runWithdrawalTest').prop('disabled', false); // Enable button
                    alert('An error occurred while processing your request. Please try again.');
                }
            });
        });

        // Function to perform the AJAX call
        function wperformAjax() {
            $('#spinner2').show(); // Show spinner
            $('#runWithdrawalTest').prop('disabled', true); // Disable button

            var formData = new FormData($('#addBalanceForm')[0]); // Get form data

            $.ajax({
                type: "POST",
                url: "{{ route('admin.withdrawal.testp') }}",
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                data: formData, // Use FormData object
                processData: false, // Don't process the data
                contentType: false, // Don't set contentType
                success: function(response) {
                    console.log(response);
                    if (response.result === "success") {
                        $('#spinner2').hide(); // Hide spinner
                        $('#tickMark2').show(); // Show tick mark
                        clearInterval(intervalId); // Stop the interval
                        runDepositTest();
                    }
                },
                error: function(xhr, status, error) {
                    // Handle error
                    console.error(xhr.responseText);
                    $('#spinner2').hide(); // Hide spinner
                    $('#runWithdrawalTest').prop('disabled', false); // Enable button
                    alert('An error occurred while processing your request. Please try again.');
                }
            });
        }

        $('.modal-header .close').click(function() {
            clearInterval(intervalId); // Stop the interval
            // $('#runDepositTest').prop('disabled', false);
            $('#runWithdrawalTest').prop('disabled', false);
            $('#spinner').hide();
            $('#tickMark').hide();
            $('#spinner2').hide();
            $('#tickMark2').hide();
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush


</x-admin-layout>
