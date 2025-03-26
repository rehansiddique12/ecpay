<x-admin-layout :title="$pageTitle">
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
            color: #6c757d;
        }

        .dropzone-message {
            font-size: 1rem;
            margin-bottom: 0.5rem;
            color: #333;
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

                    <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
                    <form method="post" action="{{ route('admin.accounts.create') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-4 col-4">
                                <label>{{ trans('Name') }}</label>
                                <input type="text" class="form-control" name="e_wallet_name"
                                    value="{{ old('e_wallet_name') }}">

                                @error('e_wallet_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-4 col-4">
                                <label>{{ trans('Account No') }}</label>
                                <input type="text" class="form-control" name="account_no"
                                    value="{{ old('account_no') }}">
                                @error('account_no')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-4 col-4">
                                <label>{{ trans('Type') }}</label>
                                <select class="form-control" name="type">
                                    <option value="">Select Name</option>
                                    <option value="Personal" @if (old('type') === 'Personal') selected @endif>Personal
                                    </option>
                                    <option value="Merchant" @if (old('type') === 'Merchant') selected @endif>Merchant
                                    </option>
                                    <option value="Agent" @if (old('type') === 'Agent') selected @endif>Agent
                                    </option>
                                </select>

                                @error('type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6 col-6">
                                <label>Deposit Daily Limit</label>
                                <input type="number" class="form-control" name="daily_limit"
                                    value="{{ old('daily_limit') }}">

                                @error('daily_limit')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6 col-6">
                                <label>Deposit Monthly Limit</label>
                                <input type="number" class="form-control" name="monthly_limit"
                                    value="{{ old('monthly_limit') }}">

                                @error('monthly_limit')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6 col-6">
                                <label>Withdrawal Daily Limit</label>
                                <input type="number" class="form-control" name="daily_limit_withdrawal"
                                    value="{{ old('daily_limit_withdrawal') }}">

                                @error('daily_limit_withdrawal')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6 col-6">
                                <label>Withdrawal Monthly Limit</label>
                                <input type="number" class="form-control" name="monthly_limit_withdrawal"
                                    value="{{ old('monthly_limit_withdrawal') }}">

                                @error('monthly_limit_withdrawal')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>



                        <div class="row">
                            <div class="form-group col-md-6 col-6">
                                <label>{{ trans('Account Type') }}</label>
                                <select class="form-control" name="account_type" id="account_type">
                                    <option value="Both" @if (old('account_type') === 'Both') selected @endif>Both
                                    </option>
                                    <option value="Deposit" @if (old('account_type') === 'Deposit') selected @endif>Deposit
                                    </option>
                                    <option value="Withdrawal" @if (old('account_type') === 'Withdrawal') selected @endif>
                                        Withdrawal</option>
                                </select>

                                @error('account_type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6 col-6" id="max_withdrawal_limit">
                                <label>Max Withdrawal Limit</label>
                                <input type="number" class="form-control" name="max_withdrawal_amount"
                                    value="{{ old('max_withdrawal_amount') }}">

                                @error('max_withdrawal_amount')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-4 col-4">
                                <label>{{ trans('Apply Time Limit') }}</label>
                                <select class="form-control" name="apply_time_limit" id="apply_time_limit">
                                    <option value="1" @if (old('apply_time_limit') === '1') selected @endif>Yes
                                    </option>
                                    <option value="0" @if (old('apply_time_limit') === '0') selected @endif>No</option>
                                </select>

                                @error('apply_time_limit')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-4 col-4" id="from_time_div">
                                <label>From Time</label>
                                <input type="time" class="form-control" name="from_time"
                                    value="{{ old('from_time') }}">

                                @error('from_time')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-4 col-4" id="to_time_div">
                                <label>To Time</label>
                                <input type="time" class="form-control" name="to_time" value="{{ old('to_time') }}">

                                @error('to_time')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        <div class="row">
                            <div class="form-group col-md-3 col-3">
                                <label>Deposit Daily Limit Alert (%)</label>
                                <input type="number" class="form-control" min="1" max="100"
                                    name="deposit_daily_limit_percentage"
                                    value="{{ old('deposit_daily_limit_percentage', 100) }}">

                                @error('deposit_daily_limit_percentage')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-3 col-3">
                                <label>Deposit Monthly Limit Alert (%)</label>
                                <input type="number" class="form-control" name="deposit_monthly_limit_percentage"
                                    min="1" max="100"
                                    value="{{ old('deposit_monthly_limit_percentage', 100) }}">

                                @error('deposit_monthly_limit_percentage')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-3 col-3">
                                <label>Withdrawal Daily Limit Alert (%)</label>
                                <input type="number" class="form-control" min="1" max="100"
                                    name="withdrawal_daily_limit_percentage"
                                    value="{{ old('withdrawal_daily_limit_percentage', 100) }}">

                                @error('withdrawal_daily_limit_percentage')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-3 col-3">
                                <label>Withdrawal Monthly Limit Alert (%)</label>
                                <input type="number" class="form-control" name="withdrawal_monthly_limit_percentage"
                                    min="1" max="100"
                                    value="{{ old('withdrawal_monthly_limit_percentage', 100) }}">

                                @error('withdrawal_monthly_limit_percentage')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>











                        <div class="row justify-content-between">
                            <div class="col-sm-6 col-md-3">
                                <div class="col-12">
                                    <div class="card mt-6">
                                        <div class="card-body">
                                            <div class="dropzone-container" id="my-dropzone">
                                                <input type="file" name="file" id="file-input" class="file-input" multiple>
                                                
                                                <!-- Preview Container -->
                                                <div id="image-preview" class="hidden">
                                                    <img id="preview-img" src="" alt="Selected Image" class="img-fluid rounded mt-2" />
                                                </div>
                        
                                                <div class="upload-icon">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M12 16V8M12 8L8 12M12 8L16 12" stroke="currentColor"
                                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M3 15V16C3 17.6569 3 18.4853 3.24224 19.0815C3.45338 19.5989 
                                                            3.80112 20.0466 4.31853 20.3578C4.91476 20.7 5.74319 20.7 7.4 20.7H16.6C18.2568 
                                                            20.7 19.0852 20.7 19.6815 20.3578C20.1989 20.0466 20.5466 19.5989 20.7578 19.0815C21 
                                                            18.4853 21 17.6569 21 16V15" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                        
                                                <div class="dropzone-content">
                                                    <p class="dropzone-message">Drop files here or click to upload</p>
                                                </div>
                                            </div>
                        
                                            @error('image')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3 justify-content-between">
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label>@lang('Status')</label>
                                    <div class="form-check form-switch d-flex align-items-center">
                                        <span id="disableText" class="me-12 text-primary">@lang('No')</span>
                                        <input class="form-check-input" type="checkbox" id="statusSwitch"
                                            name="status" value="1">
                                        <span id="enableText" class="ms-2 text-secondary">@lang('Yes')</span>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <button type="submit" class="btn  btn-primary btn-block mt-3">@lang('Save Changes')</button>
                    </form>
                </div>
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
                $('select').select2({
                    selectOnClose: true
                });
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


            document.addEventListener("DOMContentLoaded", function () {
        const fileInput = document.getElementById("file-input");
        const previewContainer = document.getElementById("image-preview");
        const previewImage = document.getElementById("preview-img");

        fileInput.addEventListener("change", function (event) {
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    previewImage.src = e.target.result;
                    previewContainer.classList.remove("hidden");
                };

                reader.readAsDataURL(file);
            }
        });
    });
        </script>
    @endpush

</x-admin-layout>
