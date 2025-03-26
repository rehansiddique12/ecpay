<x-admin-layout :title="$pageTitle">
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
            <div class="card-body">



                <div class="table-responsive">
                    <table class="categories-show-table table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>

                                <th scope="col">@lang('Partner/Agent')</th>
                                <th scope="col">@lang('Deposit Amount')</th>
                                <th scope="col">@lang('Deposit Charges')</th>
                                <th scope="col">@lang('Deposit Net Amount')</th>
                                <th scope="col">@lang('Deposit Profit')</th>
                                <th scope="col">@lang('Withdrawal Amount')</th>
                                <th scope="col">@lang('Withdrawal Charges')</th>
                                <th scope="col">@lang('Withdrawal Net Amount')</th>
                                <th scope="col">@lang('Withdrawal Profit')</th>
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
                                    <p class="text-dark">@lang('No Data Found')</p>
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
</x-admin-layout>
