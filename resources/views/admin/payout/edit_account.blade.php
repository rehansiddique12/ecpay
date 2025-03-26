<x-admin-layout :title="$pageTitle">
    <div class="row ">
        <div class="col-12">
            <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
                <div class="card-body">

                    <form method="post" action="{{route('admin.accounts.update', $account->id)}}"
                          enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        <div class="row">
                            <div class="form-group col-md-4 col-4">
                                <label>{{trans('Name')}}</label>
                                <input type="text" class="form-control"
                                       name="e_wallet_name"
                                       value="{{ old('e_wallet_name', $account->e_wallet_name) }}" >

                                @error('e_wallet_name')
                                <span class="text-danger">{{ $message  }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-4 col-4">
                                <label>{{trans('Account No')}}</label>
                                <input type="text" class="form-control"
                                       name="account_no"
                                       value="{{ old('account_no', $account->account_no) }}" >
                                @error('account_no')
                                <span class="text-danger">{{ $message  }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-4 col-4">
                                <label>{{ trans('Type') }}</label>
                                <select class="form-control" name="type">
                                    <option value="">Select Name</option>
                                    <option value="Personal" @if(old('type', $account->type) === 'Personal') selected @endif>Personal</option>
                                    <option value="Merchant" @if(old('type', $account->type) === 'Merchant') selected @endif>Merchant</option>
                                    <option value="Agent" @if(old('type', $account->type) === 'Agent') selected @endif>Agent</option>
                                </select>

                                @error('type')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6 col-6">
                                <label>Deposit Daily Limit</label>
                                <input type="number" class="form-control"
                                       name="daily_limit"
                                       value="{{ old('daily_limit', round($account->daily_limit, 2) ?: '') }}" >

                                @error('daily_limit')
                                <span class="text-danger">{{ $message  }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6 col-6">
                                <label>Deposit Monthly Limit</label>
                                <input type="number" class="form-control"
                                       name="monthly_limit"
                                       value="{{ old('monthly_limit',round($account->monthly_limit, 2) ?: '') }}" >

                                @error('monthly_limit')
                                <span class="text-danger">{{ $message  }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6 col-6">
                                <label>Withdrawal Daily Limit</label>
                                <input type="number" class="form-control"
                                       name="daily_limit_withdrawal"
                                       value="{{ old('daily_limit_withdrawal',round($account->daily_limit_withdrawal, 2) ?: '') }}" >

                                @error('daily_limit_withdrawal')
                                <span class="text-danger">{{ $message  }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6 col-6">
                                <label>Withdrawal Monthly Limit</label>
                                <input type="number" class="form-control"
                                       name="monthly_limit_withdrawal"
                                       value="{{ old('monthly_limit_withdrawal',round($account->monthly_limit_withdrawal, 2) ?: '') }}" >

                                @error('monthly_limit_withdrawal')
                                <span class="text-danger">{{ $message  }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6 col-6">
                                <label>{{ trans('Account Type') }}</label>
                                <select class="form-control" name="account_type" id="account_type">
                                    <option value="Both" @if(old('account_type', $account->account_type) === 'Both') selected @endif>Both</option>
                                    <option value="Deposit" @if(old('account_type', $account->account_type) === 'Deposit') selected @endif>Deposit</option>
                                    <option value="Withdrawal" @if(old('account_type', $account->account_type) === 'Withdrawal') selected @endif>Withdrawal</option>
                                </select>

                                @error('account_type')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6 col-6" id="max_withdrawal_limit">
                                <label>Max Withdrawal Limit</label>
                                <input type="number" class="form-control"
                                    name="max_withdrawal_amount"
                                    value="{{ old('max_withdrawal_amount',round($account->max_withdrawal_amount, 2) ?: '') }}" >

                                @error('max_withdrawal_amount')
                                <span class="text-danger">{{ $message  }}</span>
                                @enderror
                            </div>
                        </div>


                        <div class="row">
                            <div class="form-group col-md-4 col-4">
                                <label>{{ trans('Apply Time Limit') }}</label>
                                <select class="form-control" name="apply_time_limit" id="apply_time_limit">
                                    <option value="1" @if(old('apply_time_limit', $account->apply_time_limit) === 1) selected @endif>Yes</option>
                                    <option value="0" @if(old('apply_time_limit', $account->apply_time_limit) === 0) selected @endif>No</option>
                                </select>

                                @error('apply_time_limit')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-4 col-4" id="from_time_div">
                                <label>From Time</label>
                                <input type="time" class="form-control"
                                    name="from_time"
                                    value="{{ old('from_time', $account->apply_time_limit==1?$account->from_time:'') }}" >

                                @error('from_time')
                                <span class="text-danger">{{ $message  }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-4 col-4" id="to_time_div">
                                <label>To Time</label>
                                <input type="time" class="form-control"
                                    name="to_time"
                                    value="{{ old('to_time', $account->apply_time_limit==1?$account->to_time:'') }}" >

                                @error('to_time')
                                <span class="text-danger">{{ $message  }}</span>
                                @enderror
                            </div>

                        </div>

                        <div class="row">
                            <div class="form-group col-md-3 col-3">
                                <label>Deposit Daily Limit Alert (%)</label>
                                <input type="number" class="form-control"
                                        min="1" max="100"
                                       name="deposit_daily_limit_percentage"
                                       value="{{ old('deposit_daily_limit_percentage',$account->deposit_daily_limit_percentage) }}" >

                                @error('deposit_daily_limit_percentage')
                                <span class="text-danger">{{ $message  }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-3 col-3">
                                <label>Deposit Monthly Limit Alert (%)</label>
                                <input type="number" class="form-control"
                                       name="deposit_monthly_limit_percentage"
                                       min="1" max="100"
                                       value="{{ old('deposit_monthly_limit_percentage',$account->deposit_monthly_limit_percentage) }}" >

                                @error('deposit_monthly_limit_percentage')
                                <span class="text-danger">{{ $message  }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-3 col-3">
                                <label>Withdrawal Daily Limit Alert (%)</label>
                                <input type="number" class="form-control"
                                        min="1" max="100"
                                       name="withdrawal_daily_limit_percentage"
                                       value="{{ old('withdrawal_daily_limit_percentage',$account->withdrawal_daily_limit_percentage) }}" >

                                @error('withdrawal_daily_limit_percentage')
                                <span class="text-danger">{{ $message  }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-3 col-3">
                                <label>Withdrawal Monthly Limit Alert (%)</label>
                                <input type="number" class="form-control"
                                       name="withdrawal_monthly_limit_percentage"
                                       min="1" max="100"
                                       value="{{ old('withdrawal_monthly_limit_percentage',$account->withdrawal_monthly_limit_percentage) }}" >

                                @error('withdrawal_monthly_limit_percentage')
                                <span class="text-danger">{{ $message  }}</span>
                                @enderror
                            </div>
                        </div>












                        <div class="row justify-content-between">
                            <div class="col-sm-6 col-md-3">
                                <label>QR Code</label>
                                <div class="image-input ">
                                    <label for="image-upload" id="image-label"><i class="fas fa-upload"></i></label>
                                    <input type="file" name="image" placeholder="@lang('Choose image')" id="image">
                                    <img id="image_preview_container" class="preview-image"
                                         src="{{ getFile(config('location.withdraw.path'))}}"
                                         alt="preview image">
                                </div>
                                @error('image')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mt-3 justify-content-between">
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('Status')</label>
                                    <div class="custom-switch-btn">
                                        <input type='hidden' value='1' name='status'>
                                        <input type="checkbox" name="status" class="custom-switch-checkbox" id="status"
                                               value="0" <?php if( $account->status == 0):echo 'checked'; endif ?>>
                                        <label class="custom-switch-checkbox-label" for="status">
                                            <span class="custom-switch-checkbox-inner"></span>
                                            <span class="custom-switch-checkbox-switch"></span>
                                        </label>
                                    </div>

                                </div>
                            </div>

                        </div>

                        <button type="submit"
                                class="btn  btn-primary btn-block mt-3">@lang('Save Changes')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

@push('js')
     <script>

        "use strict";
        $(document).ready(function (e) {


            $('#image').change(function () {
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#image_preview_container').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });


        });

        $(document).ready(function () {
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
</script>
@endpush
</x-admin-layout>
