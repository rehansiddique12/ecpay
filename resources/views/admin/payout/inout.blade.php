@push('styles')
{{-- <script src="{{ asset('public/assets/css/select2.min.css')}}"></script> --}}
<style>
.switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 32px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
    text-align: left;
    padding-left: 10px;
    line-height: 32px;
    color: white;
    font-size: 14px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked+.slider {
    background-color: #4CAF50;
    text-align: right;
    padding-right: 10px;
    padding-left: 0;
}

input:checked+.slider:before {
    transform: translateX(26px);
}
</style>

@endpush
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
            <div class="card-body">
                <h6 style="color: #7367f0">Accounts Status</h6>


                <div class="table-responsive">
                    <table class=" table table-hover table-striped table-bordered table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">Accoount Name</th>
                                <th scope="col"> Status</th>
                                <th scope="col">Deposit </th>
                                <th scope="col">Withdrawal </th>
                                <th scope="col">Sent selected</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $key => $item)
                            <td>
                                {{ $item['e_wallet_name'] }}

                            </td>
                            <!-- For Account Status -->

                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="form-check form-switch m-0">
                                        <input type="checkbox" class="form-check-input toggle-status"
                                            id="toggle_{{ $item['id'] }}" data-id="{{ $item['id'] }}" data-type="status"
                                            {{ in_array($item->status, ['1', 1, true]) ? 'checked' : '' }}>
                                    </div>
                                    <label for="toggle_{{ $item['id'] }}" class="ms-2 mb-0 fw-bold">
                                        {{ in_array($item->status, ['1', 1, true]) ? 'On' : 'Off' }}
                                    </label>
                                </div>
                            </td>

                            <!-- Deposit Toggle -->
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="form-check form-switch m-0">
                                        <input type="checkbox" class="form-check-input toggle-status"
                                            data-id="{{ $item->id }}" data-type="deposit"
                                            id="deposit-toggle_{{ $item->id }}"
                                            {{ in_array(strtolower($item->account_type), ['deposit', 'both']) ? 'checked' : '' }}>
                                    </div>
                                    <label for="deposit-toggle_{{ $item->id }}" class="ms-2 mb-0 fw-bold">
                                        {{ in_array(strtolower($item->account_type), ['deposit', 'both']) ? 'On' : 'Off' }}
                                    </label>
                                </div>
                            </td>

                            <!-- Withdrawal Toggle -->
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="form-check form-switch m-0">
                                        <input type="checkbox" class="form-check-input toggle-status"
                                            data-id="{{ $item->id }}" data-type="withdrawal"
                                            id="withdrawal-toggle_{{ $item->id }}"
                                            {{ in_array(strtolower($item->account_type), ['withdrawal', 'both']) ? 'checked' : '' }}>
                                    </div>
                                    <label for="withdrawal-toggle_{{ $item->id }}" class="ms-2 mb-0 fw-bold">
                                        {{ in_array(strtolower($item->account_type), ['withdrawal', 'both']) ? 'On' : 'Off' }}
                                    </label>
                                </div>
                            </td>


                            <td class="text-center">
                                <input type="checkbox" name="checkbox_{{ $item->id }}" value="{{ $item->id }}">
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary btn-sm">
                                    <i class="fa fa-paper-plane me-1"></i> Send Notice
                                </button>
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
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal"
                        aria-label="Close">@lang('Close')</button>
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
                                <input type="number" id="currentbalance" step="0.01" class="form-control" name="amount"
                                    required />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">Live Balance</label>
                                <input type="number" step="0.01" id="livebalance" class="form-control"
                                    name="live_balance" required />
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">@lang('Update')</button>
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal"
                        aria-label="Close">@lang('Close')</button>
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
    // $('select').select2({
    //     selectOnClose: true
    // });
});
</script>
<script>
function setBalanceItem(itemId) {
    // Find the input field in the modal
    var balanceInput = document.getElementById("balanceInput");

    // Set the value of the input field to the item id
    balanceInput.value = itemId;
}

function editBalanceItem(itemId, balance, live_balance) {
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

$('.toggle-status').on('change', function() {
    let id = $(this).data('id');
    let type = $(this).data('type');
    let status = $(this).is(':checked') ? 1 : 0;

    $.ajax({
        url: "{{ route('admin.update.accstatus') }}",
        method: 'POST',
        data: {
            _token: "{{ csrf_token() }}",
            id: id,
            status: status,
            type: type
        },
        success: function(res) {
            alert('updated ');
        }
    });
});
</script>


@endpush
