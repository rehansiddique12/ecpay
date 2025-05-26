<style>
    .text-primary {
        color: #7367f0 !important;
    }

    .text-secondary {
        color: #6c757d !important;
    }



    .dropzone-container {
        border: 2px dashed #d9d9d9;
        border-radius: 4px;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        min-height: 150px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .dropzone-container.dragging {
        border-color: #6c757d;
        background-color: rgba(0, 0, 0, 0.02);
    }

    .file-input {
        position: absolute;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 2;
    }

    .upload-icon {
        margin-bottom: 1rem;
        color: whitesmoke;
    }

    .dropzone-message {
        font-size: 1rem;
        margin-bottom: 0.5rem;
        color: whitesmoke;
    }

    .dropzone-note {
        font-size: 0.875rem;
        color: #6c757d;
    }

    .fw-medium {
        font-weight: 500;
    }

    .file-list {
        margin-top: 1rem;
    }

    .file-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem;
        border-bottom: 1px solid #eee;
    }

    .remove-button {
        background: none;
        border: none;
        color: #dc3545;
        font-size: 1.25rem;
        cursor: pointer;
    }

    .hidden {
        display: none;
    }

    #preview-img {
        width: 100%;
        max-height: 200px;
        object-fit: cover;
    }
</style>
<div class="row ">
    <div class="col-12">
        <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
            <div class="card-body">

                <h6>Create Account In Batch
                </h6>
                <form method="post" action="{{ route('admin.accounts.create') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-6 col-6">
                            <label>{{ trans('Category Name') }}</label>
                            <select class="form-select" name="category_id">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id ?? '' }}">{{ $category->name ?? '' }}</option>
                                @endforeach

                            </select>

                            @error('category_id')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        <div class="form-group col-md-4 col-4">
                            <label>{{ trans('Select Account Name') }}</label>
                            <select class="form-select" name="account_id">
                                <option value="">Select Account Name</option>
                                @foreach($methods as $account)
                                <option value="{{ $account->id ?? '' }}">{{ $account->name ?? '' }}</option>
                                @endforeach

                            </select>

                            @error('account_id')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6 col-6">
                            <label>{{ trans('Currency') }}</label>
                            <select class="form-select" name="currency">
                                <option value="">Select Currency</option>
                                <option value="INR">INR</option>
                            </select>
                            @error('currency')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <hr style="border-top: 1px solid white;">
                    <h6 class="mb-0">{{ trans(' CONFIGURATION') }}</h6>
                    <div class="row">
                        <div class="form-group col-md-6 col-6">
                            <label> Daily Deposit Amount Limit</label>
                            <input type="number" class="form-control" name="daily_limit"
                                value="{{ old('daily_limit') }}">

                            @error('daily_limit')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-md-6 col-6">
                            <label> Monthly Deposit Amount Limit</label>
                            <input type="number" class="form-control" name="monthly_limit"
                                value="{{ old('monthly_limit') }}">

                            @error('monthly_limit')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6 col-6">
                            <label>Daily Withdrawal Amount Limit </label>
                            <input type="number" class="form-control" name="daily_limit_withdrawal"
                                value="{{ old('daily_limit_withdrawal') }}">

                            @error('daily_limit_withdrawal')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-md-6 col-6">
                            <label>Monthly Withdrawal Amount Limit</label>
                            <input type="number" class="form-control" name="monthly_limit_withdrawal"
                                value="{{ old('monthly_limit_withdrawal') }}">

                            @error('monthly_limit_withdrawal')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>


                    <div class="row">
                        <div class="form-group col-md-6 col-6">
                            <label> Monthly Deposit Transaction Limit</label>
                            <input type="number" class="form-control" name="monthly_deposit_transaction"
                                value="{{ old('monthly_deposit_transaction') }}">
                            @error('monthly_deposit_transaction')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-md-6 col-6">
                            <label> Monthly Withdrawl Transaction Limit</label>
                            <input type="number" class="form-control" name="monthly_withdrawl_transaction"
                                value="{{ old('monthly_withdrawl_transaction') }}">
                            @error('monthly_withdrawl_transaction')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6 col-6">
                            <label> Daily Deposit Transaction Limit</label>
                            <input type="number" class="form-control" name="daily_deposit_transaction"
                                value="{{ old('daily_deposit_transaction') }}">
                            @error('daily_deposit_transaction')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-md-6 col-6">
                            <label> Daily Withdrawl Transaction Limit</label>
                            <input type="number" class="form-control" name="daily_withdrawl_transaction"
                                value="{{ old('daily_withdrawl_transaction') }}">
                            @error('daily_withdrawl_transaction')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6 col-6">
                            <label> Max Amount Per Minute</label>
                            <input type="number" class="form-control" name="max_amount_per"
                                value="{{ old('max_amount_per') }}">
                            @error('max_amount_per')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-md-6 col-6">
                            <label> Max Amount Per Minute</label>
                            <input type="number" class="form-control" name="max_amount_per"
                                value="{{ old('max_amount_per') }}">
                            @error('max_amount_per')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

            </div>
            <hr style="border-top: 1px solid white;">

            <div class="row">
                <div class="form-group col-md-12 col-12">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">{{ trans('Time Configuration') }}</h6>
                        <div>
                            <input type="checkbox" id="check_all_slots" class="form-check-input">
                            <label for="check_all_slots" class="form-check-label text-white">Check All</label>
                        </div>
                    </div>
                    @php
                    $start = strtotime('00:00');
                    $end = strtotime('24:00');
                    $i = 0;
                    $slots = [];

                    for ($time = $start; $time < $end; $time +=1800) { $from=date('H:i', $time); $to=date('H:i', $time +
                        1800); $label="$from - $to" ; $slots[]=$label; } $chunks=array_chunk($slots, ceil(count($slots)
                        / 6)); // 6 columns @endphp <div class="row">
                        @foreach ($chunks as $column)
                        <div class="col-md-2 col-sm-4 col-6">
                            @foreach ($column as $slot)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="time_slots[]" value="{{ $slot }}"
                                    id="slot_{{ $i }}">
                                <label class="form-check-label text-white" for="slot_{{ $i }}">
                                    {{ $slot }}
                                </label>
                            </div>
                            @php $i++; @endphp
                            @endforeach
                        </div>
                        @endforeach
                </div>
            </div>


            <hr style="border-top: 1px solid white;">
            <div class="row">
                <h6>{{ trans('THRESHOLD ALERT') }}</h6>
                <div class="form-group col-md-3 col-3">
                    <label>Daily Deposit Limit Alert (%)</label>
                    <input type="number" class="form-control" min="1" max="100" name="deposit_daily_limit_percentage"
                        value="{{ old('deposit_daily_limit_percentage', 100) }}">

                    @error('deposit_daily_limit_percentage')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-3 col-3">
                    <label>Daily Withdrawl Limit Alert (%)</label>
                    <input type="number" class="form-control" min="1" max="100" name="withdrawl_daily_limit_percentage"
                        value="{{ old('withdrawl_daily_limit_percentage', 100) }}">

                    @error('withdrawl_daily_limit_percentage')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-3 col-3">
                    <label>Monthly Deposit Limit Alert (%)</label>
                    <input type="number" class="form-control" name="deposit_monthly_limit_percentage" min="1" max="100"
                        value="{{ old('depositC_monthly_limit_percentage', 100) }}">

                    @error('deposit_monthly_limit_percentage')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-3 col-3">
                    <label>Monthly Withdrawal Limit Alert (%)</label>
                    <input type="number" class="form-control" min="1" max="100"
                        name="withdrawal_monthly_limit_percentage"
                        value="{{ old('withdrawal_monthly_limit_percentage', 100) }}">

                    @error('withdrawal_monthly_limit_percentage')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-3 col-3">
                    <label>Low Balance Alert Amount</label>
                    <input type="number" class="form-control" name="low_balance_amount" min="1"
                        value="{{ old('low_balance_amount', 100) }}">

                    @error('low_balance_amount')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <hr>
            <div class="col-12 mb-3">
                <h6>{{ __('Add Account') }}</h6>
            </div>
            <div id="inputGroupContainer">
                <div class="row input-group-row">
                    <div class="form-group col-md-2 col-12">
                        <label>Account Name</label>
                        <input type="text" name="e_wallet_name[]" class="form-control" required>
                    </div>

                    <div class="form-group col-md-2 col-12">
                        <label>Device Name</label>
                        <input type="text" name="device_name[]" class="form-control" required>
                    </div>

                    <div class="form-group col-md-2 col-12">
                        <label> Account Number</label>
                        <input type="text" name="account_number[]" class="form-control" required>
                    </div>

                    <div class="form-group col-md-2 col-12">
                        <label>Account Group</label>
                        <select name="account_group[]" class="form-select" required>
                            <option value="">Select</option>
                            @foreach($groups as $group)
                            <option value="{{$group->id}}">{{$group->group_name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-1 col-12">
                        <label> Type</label>
                        <select name="account_type[]" class="form-select" required>
                            <option value="">Select</option>
                            <option value="Agent">Agent</option>
                            <option value="Personal">Personal</option>
                        </select>
                    </div>

                    <div class="form-group col-md-1 col-12">
                        <label>In/Out</label>
                        <select name="in_out[]" class="form-select" required>
                            <option value="">Select</option>
                            <option value="deposit">Deposit</option>
                            <option value="withdrawal">Withdrawal</option>
                            <option value="both">Both</option>
                        </select>
                    </div>

                    <div class="form-group col-md-2 col-12">
                        <label>Location</label>
                        <select name="location[]" class="form-select" required>
                            <option value="">Select</option>
                            <option value="Location 1">Location 1</option>
                            <option value="Location 2">Location 2</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2 col-12">
                        <label for="qr_file">QR</label>
                        <input type="file" name="image" id="qr_file" class="form-control"
                            accept="image/png, image/jpeg">
                    </div>

                </div>
            </div>

            <!-- More Button -->
            <div class="mt-3">
                <button type="button" id="addMoreBtn" class="btn btn-primary">+ More</button>
            </div>

            <div class="row mt-3 justify-content-between">
                <div class="col-lg-3 col-md-6">
                    <div class="form-group">
                        <label>@lang('Status')</label>
                        <div class="form-check form-switch d-flex align-items-center">
                            <span id="disableText" class="me-12 text-primary">@lang('No')</span>
                            <input class="form-check-input" type="checkbox" id="statusSwitch" name="status" value="1">
                            <span id="enableText" class="ms-2 text-secondary">@lang('Yes')</span>
                        </div>
                    </div>
                    <button type="submit" class="btn  btn-primary btn-block mt-3">@lang('Save Changes')</button>

                </div>
            </div>

            </form>
        </div>
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

$(document).ready(function() {
    // $('select').select2({
    //     selectOnClose: true
    // });
});
</script>
<script>
    $(document).ready(function() {
        function toggleMaxWithdrawalLimit() {
            if ($('#account_type').val() === 'Deposit') {
                $('#max_withdrawal_limit').hide();
            } else {
                $('#max_withdrawal_limit').show();
            }
        }

        $('#account_type').on('change', toggleMaxWithdrawalLimit);

        // Initialize the visibility on page load
        toggleMaxWithdrawalLimit();



            function toggleTimeFields() {
                if ($('#apply_time_limit').val() == 0) {
                    $('#from_time_div').hide();
                    $('#to_time_div').hide();
                } else {
                    $('#from_time_div').show();
                    $('#to_time_div').show();
                }
            }

        $('#apply_time_limit').on('change', toggleTimeFields);

        // Initialize the visibility on page load
        toggleTimeFields();
    });
document.addEventListener("DOMContentLoaded", function() {
    const statusSwitch = document.getElementById("statusSwitch");
    const disableText = document.getElementById("disableText");
    const enableText = document.getElementById("enableText");

    statusSwitch.addEventListener("change", function() {
        if (this.checked) {
            disableText.classList.remove("text-primary");
            disableText.classList.add("text-secondary");

            enableText.classList.remove("text-secondary");
            enableText.classList.add("text-primary");
        } else {
            disableText.classList.remove("text-secondary");
            disableText.classList.add("text-primary");

            enableText.classList.remove("text-primary");
            enableText.classList.add("text-secondary");
        }
    });
});


document.addEventListener("DOMContentLoaded", function() {
    const fileInput = document.getElementById("file-input");
    const previewContainer = document.getElementById("image-preview");
    const previewImage = document.getElementById("preview-img");

    fileInput.addEventListener("change", function(event) {
        const file = event.target.files[0];

        if (file) {
            const reader = new FileReader();

            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewContainer.classList.remove("hidden");
            };

            reader.readAsDataURL(file);
        }
    });
});
</script>

<script>
    document.getElementById('addMoreBtn').addEventListener('click', function() {
    let container = document.getElementById('inputGroupContainer');
    let rows = container.querySelectorAll('.input-group-row');
    let lastRow = rows[rows.length - 1];
    let clone = lastRow.cloneNode(true);

    // Clear values in inputs and selects
    clone.querySelectorAll('input, select').forEach(function(el) {
        el.value = ''; // clear values in the clone
    });

    container.appendChild(clone);
});

// Submit form
document.getElementById('myForm').addEventListener('submit', function(event) {
    // Allow the form to submit after cloning rows
    // Optionally, you could validate fields here before submission

    // If any additional checks are needed before form submission, do them here

    // The form will automatically include all dynamically added inputs
});
</script>
<script>
    document.getElementById('check_all_slots').addEventListener('change', function() {
    const isChecked = this.checked;
    const checkboxes = document.querySelectorAll('input[name="time_slots[]"]');
    checkboxes.forEach(cb => cb.checked = isChecked);
});
</script>


@endpush
