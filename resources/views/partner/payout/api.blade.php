<x-partner-layout :title="$pageTitle">

<div class="row">

    <div class="col-md-12">
        <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
            <div class="card-body">
                <h3 style="color: #7367f0">{{ __('partner_basic.Commissions_Summary_en')}}</h3>

                <div class="table-responsive">
                    <table class="categories-show-table table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>

                                <th scope="col">@lang('partner_basic.Partner/Agent_en')</th>
                                <th scope="col">@lang('partner_basic.Deposit_Amount_en')</th>
                                <th scope="col">@lang('partner_basic.Deposit_Charges_en')</th>
                                <th scope="col">@lang('partner_basic.Deposit_Net_Amount_en')</th>
                                <th scope="col">@lang('partner_basic.Deposit_Profit_en')</th>
                                <th scope="col">@lang('partner_basic.Withdrawal_Amount_en')</th>
                                <th scope="col">@lang('partner_basic.Withdrawal_Charges_en')</th>
                                <th scope="col">@lang('partner_basic.Withdrawal_Net_Amount_en')</th>
                                <th scope="col">@lang('partner_basic.Withdrawal_Profit_en')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $key => $item)
                            <tr>
                                <td>{{ $item->api->name }}</td>
                                <td>{{ number_format($item->sum_amount_type_1, 2) }}</td>
                                <td>{{ number_format($item->sum_charges_type_1, 2) }}</td>
                                <td>{{ number_format($item->sum_total_amount_type_1, 2) }}</td>
                                <td>{{ number_format($item->sum_profit_type_1, 2) }}</td>
                                <td>{{ number_format($item->sum_amount_type_2, 2) }}</td>
                                <td>{{ number_format($item->sum_charges_type_2, 2) }}</td>
                                <td>{{ number_format($item->sum_total_amount_type_2, 2) }}</td>
                                <td>{{ number_format($item->sum_profit_type_2, 2) }}</td>


                            </tr>
                            @empty
                            <tr>
                                <td colspan="100%">
                                    <p class="text-dark">@lang('partner_basic.no_data_found')</p>
                                </td>
                            </tr>

                            @endforelse
                        </tbody>
                    </table>
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

@endpush
</x-partner-layout>
