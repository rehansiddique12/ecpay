<x-admin-layout :title="$pageTitle">
    @push('styles')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}">
    <style>
        tr th {
            color: white !important;
        }
    </style>
    @endpush


    @php
    $key = 0;
    @endphp
    <div class="row">
        <div class="col-md-12">
            <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
                <h3 style="color: #7367f0">{{ $pageTitle }}</h3>

                <form action="{{ route('admin.apis.commission.add') }}" method="POST">
                    @csrf
                    <input type="hidden" value="{{ $id }}" name="category_id" />

                    <div class="mb-3">
                        <label for="partner_id" class="form-label">Select Parent</label>
                        <select name="partner_id" id="partner_id" class="form-select" required>
                            <option value="">-- Choose Parent --</option>
                            @foreach ($partners as $partner)
                            <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if(count($commissions) > 0)
                    @foreach($commissions as $commission)
                    <div id="row-p{{ $key }}">
                        <br>
                        <div style="border:1px solid;padding:20px">
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="row">
                                    <div class="col-md-3">
                                        <label>From Amount</label>
                                        <input type="hidden" name="id[]" value="{{ $commission->id }}">
                                        <input type="number" class="form-control" name="from_amount[]"
                                            value="{{ $commission->from_amount }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label>To Amount</label>
                                        <input type="number" class="form-control" name="to_amount[]"
                                            value="{{ $commission->to_amount }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Deposit %</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control"
                                                name="deposit_percentage[]" value="{{ $commission->deposit_percentage }}"
                                                required>
                                            <div class="input-group-append"><span class="input-group-text">%</span></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Withdrawal %</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control"
                                                name="withdrawal_percentage[]"
                                                value="{{ $commission->withdrawal_percentage }}" required>
                                            <div class="input-group-append"><span class="input-group-text">%</span></div>
                                        </div>
                                    </div>
                                   
                                </div>
                                </div>
                                
                                @php
                                $selectedGateways = json_decode($commission->gateway_id ?? '');
                                $selectedtypes = json_decode($commission->type ?? '');
                                @endphp
                                <div class="col-md-2">
                                    <label>Type</label>
                                    <select class="form-select select2" multiple name="type[{{ $key }}][]" required>
                                        
                                        <option value="Agent" {{ in_array('Agent', $selectedtypes)? 'selected' : '' }}>Agent
                                        </option>
                                        <option value="Personal" {{ in_array('Personal', $selectedtypes)? 'selected' : '' }}>Personal</option>
                                        <option value="Merchant" {{ in_array('Merchant', $selectedtypes)? 'selected' : '' }}>Merchant</option>    
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Gateway</label>
                                    <select class="form-select select2" multiple name="settlement_gateway[{{ $key }}][]">
                                        @foreach($gateways as $gateway)
                                        <option value="{{ $gateway->name }}" {{ in_array($gateway->name, $selectedGateways)
                                            ? 'selected' : '' }}>
                                            {{ $gateway->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                
                            </div>

                            <div class="row">
                                <div class="col-md-6 mt-4">
                                    <label for="deposit" class="form-label">Deposit Percentage</label>
                                    <input style="border:2px solid green;" type="number" name="deposit_percentage" id="deposit" class="form-control" step="0.01"
                                        placeholder="Enter deposit percentage">
                                </div>
                                <div class="col-md-6 mt-4">
                                    <label for="withdrawal" class="form-label">Withdrawal Percentage</label>
                                    <input style="border:2px solid green;" type="number" name="withdrawal_percentage" id="withdrawal" class="form-control" step="0.01"
                                        placeholder="Enter withdrawal percentage">
                                </div>
                            </div>
                        </div>
                    </div>
                    @php
                    $key++;
                    @endphp
                    @endforeach
                    @else
                    
                    @endif

                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('js')
    <script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
    <script>
        var key = '<?php echo $key?>';
        let $select = $('.select2').select2({
            // placeholder: "Select Partner",
            // allowClear: true,
            selectOnClose: true,
        });


        

        // Prevent dropdown from opening on clear
        $select.on('select2:unselecting', function (e) {
            $(this).data('unselecting', true);
        });

        $select.on('select2:opening', function (e) {
            if ($(this).data('unselecting')) {
                $(this).removeData('unselecting');
                e.preventDefault();
            }
        });

    

    
    </script>
    @endpush
</x-admin-layout>



