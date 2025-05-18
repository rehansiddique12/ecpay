<x-admin-layout :title="$pageTitle">
    @push('styles')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}">
    <style>
    tr th {
        color: white !important;
    }
    </style>
    @endpush



    <div class="row">
        <div class="col-md-12">
            <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
                <h3 style="color: #7367f0">{{ $pageTitle }}</h3>

                @if(count($commissions) > 0)
                @foreach($commissions as $key => $commission)
                <div id="row-p{{ $key }}">
                    <br>
                    <div style="border:1px solid;padding:20px">
                        <div class="row">
                            <div class="col-md-2">
                                <label>From Amount</label>
                                <input type="hidden" name="id[]" value="{{ $commission->id }}">
                                <input type="number" class="form-control" name="from_amount[]"
                                    value="{{ $commission->from_amount }}" readonly required>
                            </div>
                            <div class="col-md-2">
                                <label>To Amount</label>
                                <input type="number" class="form-control" name="to_amount[]"
                                    value="{{ $commission->to_amount }}" required>
                            </div>
                            <div class="col-md-2">
                                <label>Settlement %</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" name="settlement_percentage[]"
                                        value="{{ $commission->settlement_percentage }}" required>
                                    <div class="input-group-append"><span class="input-group-text">%</span></div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label>Type</label>
                                <select class="form-select" name="type[{{ $key }}]" required>
                                    <option value="">Select Type</option>
                                    <option value="agent" {{ $commission->type == 'agent' ? 'selected' : '' }}>Agent
                                    </option>
                                    <option value="personal" {{ $commission->type == 'personal' ? 'selected' : ''
                                            }}>Personal</option>
                                </select>
                            </div>
                            @php
                            $selectedGateways = json_decode($commission->gateway_id ?? '');
                            @endphp

                            <div class="col-md-4">
                                <label>Gateway</label>
                                <select class="form-select select2" multiple name="settlement_gateway[{{ $key }}][]">
                                    @foreach($gateways as $gateway)
                                    <option value="{{ $gateway->source_name }}" {{ in_array($gateway->source_name, $selectedGateways)
                                            ? 'selected' : '' }}>
                                        {{ $gateway->source_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            @if($key > 0)
                            <div class="col-md-1 mt-4">
                                <button type="button" class="btn btn-danger cancel-row"
                                    data-row="p{{ $key }}">Cancel</button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
                @endif
                <form method="post" action="{{route('admin.add.partner.commission')}}">
                    @csrf
                    <div id="add-row">
                        {{-- Partner Dropdown --}}
                        <div class="mb-3">
                            <label for="partner_id" class="form-label">Select Partner</label>
                            <select name="partner_id" id="partner_id" class="form-select" required>
                                <option value="">-- Choose Partner --</option>
                                @foreach ($partners as $partner)
                                <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Deposit Input --}}
                        <div class="mb-3">
                            <label for="deposit" class="form-label">Deposit Percentage</label>
                            <input type="number" name="deposit_percentage" id="deposit" class="form-control" step="0.01"
                                placeholder="Enter deposit percentage">
                        </div>

                        {{-- Withdrawal Input --}}
                        <div class="mb-3">
                            <label for="withdrawal" class="form-label">Withdrawal Percentage</label>
                            <input type="number" name="withdrawal_percentage" id="withdrawal" class="form-control" step="0.01"
                                placeholder="Enter withdrawal percentage">
                        </div>

                        {{-- Hidden Category --}}
                        <input type="hidden" name="user_id" value="{{ $id }}">

                        {{-- Submit --}}
                        <div class="col-md-12 mt-3">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>

                    </div>
                   
                </form>

                <!-- @if(count($cron_commissions) > 0)
                <hr>
                <h3 style="color: #7367f0">Pending to Update</h3>

                @foreach($cron_commissions as $commission)
                <div style="border:1px solid; padding:20px;" class="mb-3">
                    <div class="row">
                        <div class="col-md-2"><label>From</label><input type="number" class="form-control"
                                value="{{ $commission->from_amount }}" readonly /></div>
                        <div class="col-md-2"><label>To</label><input type="number" class="form-control"
                                value="{{ $commission->to_amount }}" readonly /></div>
                        <div class="col-md-2"><label>Deposit %</label><input type="number" class="form-control"
                                value="{{ $commission->deposit_percentage }}" readonly /></div>
                        <div class="col-md-2"><label>Withdrawal %</label><input type="number" class="form-control"
                                value="{{ $commission->withdrawal_percentage }}" readonly /></div>
                        <div class="col-md-2"><label>Settlement %</label><input type="number" class="form-control"
                                value="{{ $commission->settlement_percentage }}" readonly /></div>
                        <div class="col-md-2"><label>Type</label><input type="text" class="form-control"
                                value="{{ ucfirst($commission->type) }}" readonly /></div>
                    </div>
                </div>
                @endforeach
                @endif -->
            </div>
        </div>
    </div>

    @push('js')
    <script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
    <script>
    let key = {
        {
            count($commissions)
        }
    };
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
                                    <label>From Amount</label>
                                    <input type="hidden" name="id[]" />
                                    <input type="number" readonly value="0" class="form-control" name="from_amount[]" required />
                                </div>
                                <div class="col-md-2">
                                    <label>To Amount</label>
                                    <input type="number" class="form-control" name="to_amount[]" required />
                                </div>
                                <div class="col-md-2">
                                    <label>Deposit %</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" name="deposit_percentage[]" required />
                                        <div class="input-group-append"><span class="input-group-text">%</span></div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label>Withdrawal %</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" name="withdrawal_percentage[]" required />
                                        <div class="input-group-append"><span class="input-group-text">%</span></div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label>Settlement %</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" name="settlement_percentage[]" required />
                                        <div class="input-group-append"><span class="input-group-text">%</span></div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label>Type</label>
                                    <select class="form-select" name="type[${key}]" required>
                                        <option value="">Select Type</option>
                                        <option value="agent">Agent</option>
                                        <option value="personal">Personal</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label>Gateway</label>
                                    <select class="form-select select2" name="settlement_gateway[${key}][]" multiple required>
                                        @foreach($gateways as $gateway)
                                            <option value="{{ $gateway->source_name }}">{{ $gateway->source_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-1 mt-4">
                                    <button type="button" class="btn btn-danger cancel-row" data-row="p${key}">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>`;
        $('#add-row').append(html);
        $('.select2').select2();
        key++;
    });

    $(document).on('click', '.cancel-row', function() {
        const rowId = $(this).data('row');
        $(`#row-${rowId}`).remove();
    });
    </script>
    <script>
    let key = {
        {
            count($commissions) > 0 ? count($commissions) : 1
        }
    };
    // Start key count from existing commissions

    // Add new row on clicking Add More button
    $(document).on('click', '.duplicate-row', function() {
        let html = `
                    <div id="row-p${key}">
                        <br>
                        <div style='border:1px solid;padding:20px'>
                            <div class="row">
                                <div class="col-md-2">
                                    <label>From Amount</label>
                                    <input type="hidden" name="id[]" />
                                    <input type="number" readonly value="0" class="form-control" name="from_amount[]" required />
                                </div>
                                <div class="col-md-2">
                                    <label>To Amount</label>
                                    <input type="number" class="form-control" name="to_amount[]" required />
                                </div>
                                <div class="col-md-2">
                                    <label>Deposit %</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" name="deposit_percentage[]" required />
                                        <div class="input-group-append"><span class="input-group-text">%</span></div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label>Withdrawal %</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" name="withdrawal_percentage[]" required />
                                        <div class="input-group-append"><span class="input-group-text">%</span></div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label>Settlement %</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" name="settlement_percentage[]" required />
                                        <div class="input-group-append"><span class="input-group-text">%</span></div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label>Type</label>
                                    <select class="form-select" name="type[${key}]" required>
                                        <option value="">Select Type</option>
                                        <option value="agent">Agent</option>
                                        <option value="personal">Personal</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label>Gateway</label>
                                    <select class="form-select select2" name="settlement_gateway[${key}][]" multiple required>
                                        @foreach($gateways as $gateway)
                                            <option value="{{ $gateway->source_name }}">{{ $gateway->source_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-1 mt-4">
                                    <button type="button" class="btn btn-danger cancel-row" data-row="p${key}">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
        $('#add-row').append(html);
        // Reinitialize select2 for new elements
        $('.select2').select2();
        key++;
    });

    // Remove row on clicking Cancel button
    $(document).on('click', '.cancel-row', function() {
        const rowId = $(this).data('row');
        $(`#row-${rowId}`).remove();
    });
    </script>
    @endpush
</x-admin-layout>