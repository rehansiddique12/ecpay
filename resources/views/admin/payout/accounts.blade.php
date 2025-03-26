<x-admin-layout :title="$pageTitle">
    @push('styles')
    <script src="{{ asset('public/assets/css/select2.min.css')}}"></script>
    @endpush
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
            <div class="card-body">


                <div class="table-responsive">
                    <table class="categories-show-table table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>QR Code</th>
                                <th scope="col">E-Wallet</th>
                                <!-- <th scope="col">@lang('Phone')</th>
                                <th scope="col">@lang('Type')</th> -->
                                <th scope="col">Live Status</th>
                                <th scope="col">Deposit Limit</th>
                                <th scope="col">Withdrawal Limit</th>
                                <th scope="col">Received</th>
                                <!-- <th scope="col">Monthly Received</th>
                                <th scope="col">Total Received</th> -->
                                <th scope="col">Sent</th>
                                <!-- <th scope="col">Monthly Sent</th>
                                <th scope="col">Total Sent</th> -->
                                <th scope="col">Fee</th>
                                <th scope="col">Commission</th>
                                <th scope="col">Earned</th>
                                <th scope="col">Balance</th>
                                <th scope="col">Live Balance</th>
                                <th scope="col">Max Withdrawal</th>
                                <th scope="col">Deposit Alert</th>
                                <th scope="col">Withdrawal Alert</th>
                                <th scope="col">Time Limit</th>
                                <th scope="col">Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $key => $item)
                            <tr style="background-color: {{($item['daily_received'] > ($item['daily_limit']*$item['deposit_daily_limit_percentage']/100)) || ($item['monthly_received'] > ($item['monthly_limit']*$item['deposit_monthly_limit_percentage']/100)) || ($item['daily_sent'] > ($item['daily_limit_withdrawal']*$item['withdrawal_daily_limit_percentage']/100)) || ($item['monthly_sent'] > ($item['monthly_limit_withdrawal']*$item['withdrawal_monthly_limit_percentage']/100))?'yellow':''}}">
                                <td>
                                    <img style="width:100px" src="{{ getFile(config('location.withdraw.path').$item->image)}}" alt="{{$item->name}}" class="gateway">
                                </td>
                                <td>
                                    {{ $item['e_wallet_name'] }}
                                    <br>
                                    {{ $item['account_no'] }}
                                    <br>
                                    {{ $item['type'] }}
                                    <br>
                                    {{ $item['account_type'] }}
                                </td>

                                <td>
                                    <span id="status-indicator-{{ $item['id'] }}" class="{{ $item['live'] == 1 ? 'dot' : 'reddot' }}"></span>
                                </td>
                                <td>
                                    Daily&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ $item['daily_limit'] }}
                                    <br>
                                    Monthly&nbsp; {{ $item['monthly_limit'] }}
                                </td>
                                <td>
                                    Daily&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ $item['daily_limit_withdrawal'] }}
                                    <br>
                                    Monthly&nbsp; {{ $item['monthly_limit_withdrawal'] }}
                                </td>
                                <td>
                                    Daily&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ $item['daily_received'] }}
                                    <br>
                                    Monthly&nbsp; {{ $item['monthly_received'] }}
                                    <br>
                                    Total&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ $item['received'] }}
                                </td>
                                <td>
                                    Daily&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ $item['daily_sent'] }}
                                    <br>
                                    Monthly&nbsp; {{ $item['monthly_sent'] }}
                                    <br>
                                    Total&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ $item['send'] }}
                                </td>
                                <!-- <td></td>
                                <td></td> -->

                                <td>{{ $item['fee'] }}</td>
                                <td>{{ $item['commission'] }}</td>
                                <td>{{ $item['commission'] - $item['fee'] }}</td>
                                <td>{{ $item['balance'] }}</td>
                                <td>{{ $item['live_balance'] }}</td>
                                <td>{{ $item['max_withdrawal_amount'] }}</td>
                                <td>
                                    <span style="font-size:18px; color:{{$item['daily_received'] > ($item['daily_limit']*$item['deposit_daily_limit_percentage']/100)?'red':''}}">Daily<br>{{ $item['deposit_daily_limit_percentage'] }}%</span><br>
                                    <span style="font-size:18px; color:{{$item['monthly_received'] > ($item['monthly_limit']*$item['deposit_monthly_limit_percentage']/100)?'red':''}}">Daily<br>{{ $item['deposit_monthly_limit_percentage'] }}%</span><br>
                                </td>
                                <td>
                                    <span style="font-size:18px; color:{{$item['daily_sent'] > ($item['daily_limit_withdrawal']*$item['withdrawal_daily_limit_percentage']/100)?'red':''}}">Daily<br>{{ $item['withdrawal_daily_limit_percentage'] }}%</span><br>
                                    <span style="font-size:18px; color:{{$item['monthly_sent'] > ($item['monthly_limit_withdrawal']*$item['withdrawal_monthly_limit_percentage']/100)?'red':''}}">Daily<br>{{ $item['withdrawal_monthly_limit_percentage'] }}%</span><br>
                                </td>
                                <td class="text-lg-center text-right">
                                    <span class="badge badge-light">
                                        <i class="fa fa-circle text-{{ $item->apply_time_limit == 1 ? 'success' : 'danger' }} font-12"></i>
                                        @lang($item->apply_time_limit == 1 ? 'Active' : 'Inactive')
                                    </span>
                                    @if($item->apply_time_limit == 1)
                                        <br>From: {{ $item['from_time'] }}<br>To: {{ $item['to_time'] }}
                                    @endif
                                </td>
                                <td data-label="@lang('Status')" class="text-lg-center text-right">
                                    <span class="badge badge-light">
                                        <i class="fa fa-circle text-{{ $item->status == 1 ? 'success' : 'danger' }} font-12"></i>
                                        @lang($item->status == 1 ? 'Active' : 'Inactive')
                                    </span>
                                </td>


                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="icon-base ti tabler-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            @if(adminAccessRoute(config('role.e_wallet_accounts.access.delete')))
                                            <form action="{{ route('admin.merchant.delete', $item['id']) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-icon edit_button"><i
                                                    class="icon-base ti tabler-trash me-1"></i> Delete</button>
                                            </form>
                                            @endif
                                            @if(adminAccessRoute(config('role.e_wallet_accounts.access.edit')))
                                            <a href="{{route('admin.accounts.edit', $item->id)}}" class="btn btn-sm btn-icon edit_button"><i class="icon-base ti tabler-pencil me-1"></i> Edit</a>
                                            <br>
                                            <button type="button" class="btn btn-sm btn-icon" data-bs-toggle="modal"
                                                    data-bs-target="#newModalb"
                                                    onclick="setBalanceItem({{ $item['id'] }})">
                                                    <i class="icon-base ti tabler-currency me-1"></i>Add Balance
                                                </button>
                                            <br>
                                            <button type="button" class="btn btn-sm btn-icon" data-bs-toggle="modal"
                                            data-bs-target="#newModalc"
                                            onclick="setBalanceItem({{ $item['id'] }})">
                                            <i class="icon-base ti tabler-user me-1"></i>Edit Balance
                                        </button>
                                    <br>
                                            <form action="{{ route('admin.accounts.charges', $item->id) }}" method="GET">
                                                <button type="submit" class="btn btn-sm btn-icon edit_button"><i
                                                    class="icon-base ti tabler-calculator me-1"></i> Charges %</button>
                                            </form>
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
                </div>
                <div class="card-footer">
                    {{ $records->appends($_GET)->links('partials.pagination') }}
                </div>
            </div>
        </div>
    </div>

</div>



<div class="modal modal-top fade" id="newModalb" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTopTitle">@lang('Add Balance')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.account.balance.add') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row justify-content-between align-items-center">


                        <input type="text" hidden id="balanceInput" class="form-control" name="account_id">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">Balance</label>
                                <input type="number" step="0.01" class="form-control" name="amount" required />
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">

                                <input id="plus" value="plus" type="radio" checked name="type" />
                                <label for="plus" class="pr-3">+ Add Credit</label>
                                <br>
                                <input id="minus" value="minus" type="radio" name="type" />
                                <label for="minus" class="pr-3">- Subtract Credit</label>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">@lang('Add')</button>
                    <button type="button" class="btn btn-dark" data-dismiss="modal">@lang('Close')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal modal-top fade" id="newModalc" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTopTitle">@lang('Edit Balance')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.account.balance.edit') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row justify-content-between align-items-center">


                        <input type="text" hidden id="balanceInpute" class="form-control" name="account_id">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">Balance</label>
                                <input type="number" id="currentbalance" step="0.01" class="form-control" name="amount" required />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">Live Balance</label>
                                <input type="number" step="0.01" id="livebalance" class="form-control" name="live_balance" required />
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">@lang('Update')</button>
                    <button type="button" class="btn btn-dark" data-dismiss="modal">@lang('Close')</button>
                </div>
            </form>
        </div>
    </div>
</div>


@push('js')
<script src="{{ asset('public/assets/js/select2.min.js')}}"></script>
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

    $(document).ready(function() {
        $('select').select2({
            selectOnClose: true
        });
    });
</script>
<script>
    function setBalanceItem(itemId) {
        // Find the input field in the modal
        var balanceInput = document.getElementById("balanceInput");

        // Set the value of the input field to the item id
        balanceInput.value = itemId;
    }

    function editBalanceItem(itemId,balance,live_balance) {
        // Find the input field in the modal
        var balanceInput = document.getElementById("balanceInpute");
        var currentbalance = document.getElementById("currentbalance");
        var livebalance = document.getElementById("livebalance");

        // Set the value of the input field to the item id
        balanceInput.value = itemId;
        currentbalance.value = balance;
        livebalance.value = live_balance;
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
            if (!itemId) return; // Prevent errors if itemId is missing

            const url = "{{ route('admin.update.status', ['id' => '__id__']) }}".replace('__id__', itemId);
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            if (!csrfToken) {
                console.error('CSRF token missing!');
                return;
            }

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                // Ensure data.id exists before updating UI
                if (data.id !== undefined) {
                    const statusIndicator = document.getElementById('status-indicator-' + data.id);
                    if (statusIndicator) {
                        statusIndicator.className = data.live ? 'dot' : 'reddot';
                    }
                }
            })
            .catch(error => console.error('AJAX Error:', error));
        }

        // Run the updateLiveStatus function every 10 seconds
        setInterval(function() {
            document.querySelectorAll('[id^="status-indicator-"]').forEach(item => {
                const itemId = item.id.split('-')[2]; // Extract ID correctly
                updateLiveStatus(itemId);
            });
        }, 10000); // 10 seconds
    });
</script>


@endpush
</x-admin-layout>
