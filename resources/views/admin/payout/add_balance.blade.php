<x-admin-layout :title="$pageTitle">
    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            <h3 style="color: #7367f0">{{ $pageTitle }}</h3>

            <form action="{{ route('admin.apis.balance.add') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row justify-content-between align-items-center">

                        <div class="col-md-12">
                            <div class="form-group">
                                <select name="partner_id" class="form-select select2" data-allow-clear="true"
                                    data-placeholder="{{ __('partner.select_domain') }}" required>
                                    <option></option>
                                    <option value="">{{ __('partner.select_domain') }}</option>
                                    @foreach ($domains as $domain)
                                        <option value="{{ $domain->id }}"
                                            @if (@request()->domain == $domain->id) selected @endif>
                                            {{ $domain->name }} ===> ( {{ $domain->website }} )
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3 mt-4">{{ __('partner.amount') }}</label>
                                <input type="number" step="0.01" class="form-control" name="amount" required />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="pr-3 mt-4">{{ __('partner.charges') }}</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" name="charges"/ required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="pr-3 mt-4">{{ __('partner.charges_type') }}</label>
                                <select class="form-select" name="charges_type">
                                    <option value="1">{{ __('partner.amount_option_amount') }}</option>
                                    <option value="2">{{ __('partner.amount_option_percentage') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3 mt-4">{{ __('partner.type') }}</label>
                                <select class="form-select" name="adjustment" id="adjustment" required>
                                    <option value="4">{{ __('partner.type_top_up') }}</option>
                                    <option value="1">{{ __('partner.type_balance_adjustment') }}</option>
                                    <option value="2">{{ __('partner.type_deposit_adjustment') }}</option>
                                    <option value="3">{{ __('partner.type_withdrawal_adjustment') }}</option>

                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group mt-4">
                                <input value="1" type="radio" name="amount_type" id="amount_type1" checked>
                                <label class="pr-3">(+) {{ __('partner.add') }}</label>
                                <input value="2" type="radio" name="amount_type" id="amount_type2">
                                <label class="pr-3">(-) {{ __('partner.deduct') }}</label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3 mt-4">{{ __('partner.source') }}</label>
                                <select class="form-select" name="source" required>
                                    <option value="E-Wallet">{{ __('partner.source_e_wallet') }}</option>
                                    <option value="Cash">{{ __('partner.source_cash') }}</option>
                                    <option value="Bank Transfer">{{ __('partner.source_bank_transfer') }}</option>
                                    <option value="Other">{{ __('partner.source_other') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3 mt-4">{{ __('partner.transactions_id') }}</label>
                                <input type="text" class="form-control" name="txn" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3 mt-4">{{ __('partner.remarks') }}</label>
                                <textarea name="reason" class="form-control"></textarea>
                            </div>
                        </div>

                    </div>
                    <button type="submit" class="btn btn-primary mt-4">{{ __('partner.add') }}</button>
                </div>

            </form>


        </div>
    </div>

    <div class="pagination float-right mr-4">
    </div>


    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
    @endpush
    @push('js')
        <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
        <script>
            $(document).ready(function() {
                $('form').on('submit', function() {
                    const $form = $(this);
                    const $submitButton = $form.find('button[type="submit"]');

                    // Disable button and change text (optional)
                    $submitButton.prop('disabled', true);
                    $submitButton.html('<i class="fa fa-spinner fa-spin me-1"></i> @lang('Processing...')');

                    // Allow form to proceed
                    return true;
                });
                let $select = $('.select2').select2({
                    // placeholder: "Select Partner",
                    allowClear: true,
                    selectOnClose: true,
                });

                // Prevent dropdown from opening on clear
                $select.on('select2:unselecting', function(e) {
                    $(this).data('unselecting', true);
                });

                $select.on('select2:opening', function(e) {
                    if ($(this).data('unselecting')) {
                        $(this).removeData('unselecting');
                        e.preventDefault();
                    }
                });
            });
        </script>
    @endpush
</x-admin-layout>
