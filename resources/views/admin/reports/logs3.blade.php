<x-admin-layout :title="$pageTitle">
<div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
    <form action="{{ route('admin.reports.cal2') }}" method="get">
        <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
        <div class="row justify-content-between align-items-center">
            <div class="col-md-3">
                <div class="form-group">
                    <label>From Date</label>
                    <input type="date" class="form-control" value="{{$from_date}}" name="from_date" id="datepicker" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>To Date</label>
                    <input type="date" class="form-control" value="{{$to_date}}" name="to_date" id="datepicker" />
                </div>
            </div>


            <div class="col-md-3">
                <div class="form-group">
                    <label>Source</label>
                    <select name="website" class="form-select select2" data-allow-clear="true" data-placeholder="Select Domain">
                            <option></option>
                        <option value="">All Source</option>
                        @foreach($domains as $partner)
                        <option value="{{ $partner->id }}" @if(@request()->website == $partner->id) selected @endif>{{ $partner->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>


            <div class="col-md-3">
                <div class="form-group mt-2">
                    <br>
                    <button type="submit" value="submit" class="btn waves-effect waves-light btn-primary"><i class="icon-base ti tabler-search me-1"></i> @lang('Search')</button>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
            <div class="card-body">
                <h3>Summary</h3>
                <table class="table table-bordered">
                    <thead>
                    <th>Amount</th>
                    <th>Charge</th>
                    <th>Final Amount</th>
                    <th>Type</th>
                    </thead>
                    <tbody>
                        <tr>
                            @if(isset(($deposits)))
                            @php $deposit_final_deposit = ($deposits->payment_amount - $deposits->payment_charge ) @endphp
                            <td>{{ $deposits->payment_amount }}</td>
                            <td> {{ $deposits->payment_charge }}</td>
                            <td>{{ $deposit_final_deposit }}</td>
                            <td>Deposit</td>
                            @endif
                        </tr>

                        <tr>
                            @if(isset(($withdrawals)))
                            @php $withdrawal_final_deposit = -($withdrawals->payment_amount + $withdrawals->payment_charge ) @endphp
                            <td>{{ $withdrawals->payment_amount }}</td>
                            <td> {{ $withdrawals->payment_charge }}</td>
                            <td>{{ $withdrawal_final_deposit }}</td>
                            <td>Withdrawal</td>
                            @endif
                        </tr>

                        <tr>
                            @if(isset(($ApiTransactions)))
                            @php $api_final_deposit = ($ApiTransactions->payment_amount - $ApiTransactions->payment_charge ) @endphp
                            <td>{{ $ApiTransactions->payment_amount }}</td>
                            <td> {{ $ApiTransactions->payment_charge }}</td>
                            <td>{{ $api_final_deposit }}</td>
                            <td>ApiTransactions</td>
                            @endif
                        </tr>

                        <tr>
                            @if(isset(($ApiTransactions)))
                            @php $sat_final_deposit = -($Settlements->payment_amount + $Settlements->payment_charge ) @endphp
                            <td>{{ $Settlements->payment_amount }}</td>
                            <td> {{ $Settlements->payment_charge }}</td>
                            <td>{{ $sat_final_deposit }}</td>
                            <td>Settlements</td>
                            @endif
                        </tr>

                        <tr>
                            @if(isset(($PartnerCommissions)))
                            @php $pat_final_deposit = $PartnerCommissions->partner_profit @endphp
                            <td>{{ $PartnerCommissions->partner_profit }}</td>
                            <td> </td>
                            <td></td>
                            <td>PartnerCommissions</td>
                            @endif
                        </tr>

                        <tr>
                            <td></td>
                            <td></td>
                            <td> {{ $deposit_final_deposit + $withdrawal_final_deposit +  $api_final_deposit +  $sat_final_deposit+ $pat_final_deposit  }}  </td>
                            <td>Balance</td>
                        </tr>


                    </tbody>
                </table>

            </div>
        </div>
    </div>

</div>

@push('js')
@endpush

@push('js')
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script>
    $(document).ready(function () {
        $('form').on('submit', function () {
            const $form = $(this);
            const $submitButton = $form.find('button[type="submit"]');

            // Disable button and change text (optional)
            $submitButton.prop('disabled', true);
            $submitButton.html('<i class="fa fa-spinner fa-spin me-1"></i> @lang("Processing...")');

            // Allow form to proceed
            return true;
        });
       let $select = $('.select2').select2({
                // placeholder: "Select Partner",
                allowClear: true,
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
    });
</script>
    @endpush
    @push('styles')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}">
    @endpush
</x-admin-layout>
