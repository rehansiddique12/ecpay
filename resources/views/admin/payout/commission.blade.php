<x-admin-layout :title="$pageTitle">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
        <style>
            tr th {
                color: white !important;
            }
        </style>
    @endpush


    @php
        $key = 0;
        $selectedGateways = '';
    @endphp
    <div class="row">
        <div class="col-md-12">
            <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
                <h3 style="color: #7367f0">{{ $pageTitle }}</h3>

                <form action="{{ route('admin.apis.commission.add') }}" method="POST">
                    @csrf
                    <input type="hidden" value="{{ $id }}" name="category_id" />

                    @if (count($commissions) > 0)
                        @foreach ($commissions as $commission)
                            <div id="row-p{{ $key }}">
                                <br>
                                <div style="border:1px solid;padding:20px">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ __('partner.from_amount') }}</label>
                                            <input type="hidden" name="id[]" value="{{ $commission->id }}">
                                            <input type="number" class="form-control" name="from_amount[]"
                                                value="{{ $commission->from_amount }}" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label>{{ __('partner.to_amount') }}</label>
                                            <input type="number" class="form-control" name="to_amount[]"
                                                value="{{ $commission->to_amount }}" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label>{{ __('partner.deposit_percentage') }}</label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" class="form-control"
                                                    name="deposit_percentage[]"
                                                    value="{{ $commission->deposit_percentage }}" required>
                                                <div class="input-group-append"><span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label>{{ __('partner.withdrawal_percentage') }}</label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" class="form-control"
                                                    name="withdrawal_percentage[]"
                                                    value="{{ $commission->withdrawal_percentage }}" required>
                                                <div class="input-group-append"><span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label>{{ __('partner.settlement_percentage') }}</label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" class="form-control"
                                                    name="settlement_percentage[]"
                                                    value="{{ $commission->settlement_percentage }}" required>
                                                <div class="input-group-append"><span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        @php
                                            $selectedGateways = json_decode($commission->gateway_id ?? '[]', true);
                                            $selectedtypes = json_decode($commission->type ?? '');
                                        @endphp
                                        <div class="col-md-2">
                                            <label>{{ __('partner.type') }}</label>
                                            <select class="form-select select2" multiple
                                                name="type[{{ $key }}][]" required>

                                                <option value="Agent"
                                                    {{ in_array('Agent', $selectedtypes) ? 'selected' : '' }}>
                                                    {{ __('partner.agent') }}
                                                </option>
                                                <option value="Personal"
                                                    {{ in_array('Personal', $selectedtypes) ? 'selected' : '' }}>
                                                    {{ __('partner.personal') }}</option>
                                                <option value="Merchant"
                                                    {{ in_array('Merchant', $selectedtypes) ? 'selected' : '' }}>
                                                    {{ __('partner.merchant') }}</option>
                                            </select>
                                        </div>

                                        <div class="col-md-5">
                                            <label>{{ __('partner.category') }}</label>
                                            <select class="form-select" id="category{{ $key }}"
                                                name="category[]">
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->name }}"
                                                        {{ $category->name == $commission->category ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-5">
                                            <label>{{ __('partner.gateway') }}</label>
                                            <select id="settlement_gateway{{ $key }}"
                                                class="form-select select2" multiple
                                                name="settlement_gateway[{{ $key }}][]"
                                                data-selected='@json($selectedGateways)'>

                                            </select>
                                        </div>
                                        @if ($key > 0)
                                            <div class="col-md-1 mt-4">
                                                <button type="button" class="btn btn-danger cancel-row"
                                                    data-row="p{{ $key }}">{{ __('partner.cancel') }}</button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @php
                                $key++;
                            @endphp
                        @endforeach
                    @else
                        @php
                            $key = 1;
                        @endphp
                        <div id="row-p0">
                            <br>
                            <div style='border:1px solid;padding:20px'>
                                <div class="row">
                                    <div class="col-md-2">
                                        <label>{{ __('partner.from_amount') }}</label>
                                        <input type="hidden" name="id[]" />
                                        <input type="number" readonly value="0" class="form-control"
                                            name="from_amount[]" required />
                                    </div>
                                    <div class="col-md-2">
                                        <label>{{ __('partner.to_amount') }}</label>
                                        <input type="number" class="form-control" name="to_amount[]" required />
                                    </div>
                                    <div class="col-md-2">
                                        <label>{{ __('partner.deposit_percentage') }}</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control"
                                                name="deposit_percentage[]" required />
                                            <div class="input-group-append"><span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label>{{ __('partner.withdrawal_percentage') }}</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control"
                                                name="withdrawal_percentage[]" required />
                                            <div class="input-group-append"><span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label>{{ __('partner.settlement_percentage') }}</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control"
                                                name="settlement_percentage[]" required />
                                            <div class="input-group-append"><span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label>{{ __('partner.type') }}</label>
                                        <select class="form-select select2" multiple name="type[0][]" required>

                                            <option value="Agent">{{ __('partner.agent') }}</option>
                                            <option value="Personal">{{ __('partner.personal') }}</option>
                                            <option value="Merchant">{{ __('partner.merchant') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <label>{{ __('partner.category') }}</label>
                                        <select class="form-select" id="category0" name="category[]">
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->name }}">
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>


                                    <div class="col-md-3">
                                        <label>{{ __('partner.gateway') }}</label>
                                        <select id="settlement_gateway0" class="form-select select2"
                                            name="settlement_gateway[0][]" multiple required>

                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div id="add-row"></div>

                    <div class="col-md-12 mb-4 mt-2">
                        <button type="button"
                            class="duplicate-row btn btn-success">{{ __('partner.add_more') }}</button>
                    </div>

                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">{{ __('partner.submit') }}</button>
                    </div>
                </form>

                @if (count($cron_commissions) > 0)
                    <hr>
                    <h3 style="color: #7367f0">Pending to Update</h3>

                    @foreach ($cron_commissions as $commission)
                        <div style="border:1px solid; padding:20px;" class="mb-3">
                            <div class="row">
                                @php
                                    $selectedGateways = json_decode($commission->gateway_id ?? '');
                                    $selectedtypes = json_decode($commission->type ?? '');
                                @endphp
                                <div class="col-md-1">
                                    <label>{{ __('partner.from_amount') }}</label><input type="number"
                                        class="form-control" value="{{ $commission->from_amount }}" readonly />
                                </div>
                                <div class="col-md-1">
                                    <label>{{ __('partner.to_amount') }}</label><input type="number"
                                        class="form-control" value="{{ $commission->to_amount }}" readonly />
                                </div>
                                <div class="col-md-1">
                                    <label>{{ __('partner.deposit_percentage') }}</label><input type="number"
                                        class="form-control" value="{{ $commission->deposit_percentage }}"
                                        readonly />
                                </div>
                                <div class="col-md-1">
                                    <label>{{ __('partner.withdrawal_percentage') }}</label><input type="number"
                                        class="form-control" value="{{ $commission->withdrawal_percentage }}"
                                        readonly />
                                </div>
                                <div class="col-md-1">
                                    <label>{{ __('partner.settlement_percentage') }}</label><input type="number"
                                        class="form-control" value="{{ $commission->settlement_percentage }}"
                                        readonly />
                                </div>
                                <div class="col-md-1"><label>{{ __('partner.category') }}</label><input
                                        type="text" class="form-control" value="{{ $commission->category }}"
                                        readonly /></div>
                                <div class="col-md-2">
                                    <label>{{ __('partner.type') }}</label>
                                    <select class="form-select select2" multiple readonly>
                                        <option value="Agent"
                                            {{ in_array('Agent', $selectedtypes) ? 'selected' : '' }}>
                                            {{ __('partner.agent') }}
                                        </option>
                                        <option value="Personal"
                                            {{ in_array('Personal', $selectedtypes) ? 'selected' : '' }}>
                                            {{ __('partner.personal') }}
                                        </option>
                                        <option value="Merchant"
                                            {{ in_array('Merchant', $selectedtypes) ? 'selected' : '' }}>
                                            {{ __('partner.merchant') }}
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label>{{ __('partner.gateway') }}</label>
                                    <select class="form-select select2" multiple readonly>
                                        @foreach ($allgateways as $gateway)
                                            <option value="{{ $gateway->name }}"
                                                {{ in_array($gateway->name, $selectedGateways) ? 'selected' : '' }}>
                                                {{ $gateway->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    @push('js')
        <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
        <script>
            var key = '<?php echo $key; ?>';
            let $select = $('.select2').select2({
                // placeholder: "Select Partner",
                // allowClear: true,
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

            $(document).on('click', '.duplicate-row', function() {
                let html = `
                    <div id="row-p${key}">
                        <br>
                        <div style='border:1px solid;padding:20px'>
                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ __('partner.from_amount') }}</label>
                                    <input type="hidden" name="id[]" />
                                    <input type="number" class="form-control" name="from_amount[]" required />
                                </div>
                                <div class="col-md-2">
                                    <label>{{ __('partner.to_amount') }}</label>
                                    <input type="number" class="form-control" name="to_amount[]" required />
                                </div>
                                <div class="col-md-2">
                                    <label>{{ __('partner.deposit_percentage') }}</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" name="deposit_percentage[]" required />
                                        <div class="input-group-append"><span class="input-group-text">%</span></div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label>{{ __('partner.withdrawal_percentage') }}</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" name="withdrawal_percentage[]" required />
                                        <div class="input-group-append"><span class="input-group-text">%</span></div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label>{{ __('partner.settlement_percentage') }}</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" name="settlement_percentage[]" required />
                                        <div class="input-group-append"><span class="input-group-text">%</span></div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label>{{ __('partner.type') }}</label>
                                    <select class="form-select select2" multiple name="type[${key}][]" required>
                                        
                                        <option value="Agent">{{ __('partner.agent') }}</option>
                                        <option value="Personal">{{ __('partner.personal') }}</option>
                                        <option value="Merchant">{{ __('partner.merchant') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label>{{ __('partner.category') }}</label>
                                    <select class="form-select" id="category${key}" name="category[]">
                                        @foreach ($categories as $category)
                                        <option value="{{ $category->name }}">
                                            {{ $category->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-5">
                                    <label>{{ __('partner.gateway') }}</label>
                                    <select id="settlement_gateway${key}" class="form-select select2" name="settlement_gateway[${key}][]" multiple required>
                                       
                                    </select>
                                </div>
                                <div class="col-md-1 mt-4">
                                    <button type="button" class="btn btn-danger cancel-row" data-row="p${key}">{{ __('partner.cancel') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>`;
                $('#add-row').append(html);
                $('.select2').select2();
                $('#category' + key).trigger('change');
                key++;
            });

            $(document).on('click', '.cancel-row', function() {
                const rowId = $(this).data('row');
                $(`#row-${rowId}`).remove();
            });


            let gateways = @json($gateways);

            // Load gateway options
            function loadGateways(category, gatewaySelect, preselected = []) {
                gatewaySelect.empty();

                if (gateways[category]) {
                    $.each(gateways[category], function(index, gateway) {
                        let selected = preselected.includes(gateway) ? 'selected' : '';
                        gatewaySelect.append('<option value="' + gateway + '" ' + selected + '>' + gateway +
                            '</option>');
                    });
                }

                gatewaySelect.trigger('change'); // Refresh Select2
            }

            // On change of any category select field
            $(document).on('change', 'select[id^="category"]', function() {
                let categorySelect = $(this);
                let key = categorySelect.attr('id').replace('category', '');
                let selectedCategory = categorySelect.val();
                let gatewaySelect = $('#settlement_gateway' + key);

                // Read selected gateways from data-selected attribute
                let preselected = gatewaySelect.data('selected') || [];

                loadGateways(selectedCategory, gatewaySelect, preselected);
            });

            // Trigger change on load for initial values
            $(document).ready(function() {
                $('select[id^="category"]').each(function() {
                    $(this).trigger('change');
                });
            });
        </script>
    @endpush
</x-admin-layout>
