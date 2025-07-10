<x-admin-layout :title="$pageTitle">

    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('admin.reports.partner_account_balance_summary') }}" method="get">
            <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
            <div class="row justify-content-between align-items-center">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('reports.from_date') }}</label>
                        <input type="date" class="form-control" value="{{ $from_date }}" name="from_date"
                            id="datepicker" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('reports.to_date') }}</label>
                        <input type="date" class="form-control" value="{{ $to_date }}" name="to_date" id="datepicker" />
                    </div>
                </div>


                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('reports.source') }}</label>
                        <select name="website" class="form-select select2" data-allow-clear="true"
                            data-placeholder="{{ __('reports.select_domain') }}">
                            <option></option>
                            <option value="">{{ __('reports.all_source') }}</option>
                            @foreach ($domains as $partner)
                            <option value="{{ $partner->id }}" @if (@request()->website == $partner->id) selected
                                @endif>
                                {{ $partner->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <div class="col-md-3">
                    <div class="form-group mt-2">
                        <br>
                        <button type="submit" class="btn waves-effect waves-light btn-primary"><i
                                class="icon-base ti tabler-search me-1"></i> {{ __('reports.search') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
                <div class="card-body">


                    <div class="table-responsive">
                        <table class="categories-show-table table table-hover table-striped table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">{{ __('reports.id') }}</th>
                                    <th scope="col">{{ __('reports.partner') }}</th>
                                    <th scope="col">{{ __('reports.date') }}</th>
                                    <th scope="col">{{ __('reports.opening_balance') }}</th>
                                    <th scope="col">{{ __('reports.total_deposit') }}</th>
                                    <th scope="col">{{ __('reports.total_deposit_charges') }}</th>
                                    <th scope="col">{{ __('reports.total_withdrawal') }}</th>
                                    <th scope="col">{{ __('reports.total_withdrawal_charges') }}</th>
                                    <th scope="col">{{ __('reports.total_settlement') }}</th>
                                    <th scope="col">{{ __('reports.total_settlement_charges') }}</th>
                                    <th scope="col">{{ __('reports.total_adjustment') }}</th>
                                    <th scope="col">{{ __('reports.adjustment_charges') }}</th>
                                    <th scope="col">{{ __('reports.commission_earned') }}</th>
                                    <th scope="col">{{ __('reports.closing_balance') }}</th>
                                    <th scope="col">{{ __('reports.difference') }}</th>
                                    <th scope="col">{{ __('reports.current_balance') }}</th>
                                    @auth
                                    @if (auth()->user()->username === 'dev')
                                    <th scope="col"></th>
                                    @endif
                                    @endauth


                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $deposit_amount = 0;
                                $deposit_charges = 0;
                                $withdrawal_amount = 0;
                                $withdrawal_charges = 0;
                                $settlement_amount = 0;
                                $settlement_charges = 0;
                                $adjustment = 0;
                                $adjustment_charges = 0;
                                $commission = 0;
                                ?>
                                @if (isset($data))
                                @forelse($data as $key => $item)
                                <?php

                                        $deposit_amount += $item['deposit_amount'];
                                        $deposit_charges += $item['deposit_charges'];
                                        $withdrawal_amount += $item['withdrawal_amount'];
                                        $withdrawal_charges += $item['withdrawal_charges'];
                                        $settlement_amount += $item['settlement_amount'];
                                        $settlement_charges += $item['settlement_charges'];
                                        $adjustment += $item['adjustment'];
                                        $adjustment_charges += $item['adjustment_charges'];
                                        $commission += $item['commission'];

                                        ?>
                                <tr>
                                    <td>{{ $item['id'] }}</td>
                                    <td>{{ $item['partner'] }}</td>
                                    <td>{{ $item['date'] }}</td>
                                    <td>{{ number_format($item['opening_balance'], 2) }}</td>
                                    <td>{{ number_format($item['deposit_amount'], 2) }}</td>
                                    <td>{{ number_format($item['deposit_charges'], 2) }}</td>
                                    <td>{{ number_format($item['withdrawal_amount'], 2) }}</td>
                                    <td>{{ number_format($item['withdrawal_charges'], 2) }}</td>
                                    <td>{{ number_format($item['settlement_amount'], 2) }}</td>
                                    <td>{{ number_format($item['settlement_charges'], 2) }}</td>
                                    <td>{{ number_format($item['adjustment'], 2) }}</td>
                                    <td>{{ number_format($item['adjustment_charges'], 2) }}</td>
                                    <td>{{ number_format($item['commission'], 2) }}</td>
                                    <td>{{ number_format($item['closing_balance'], 2) }}</td>
                                    <?php
                                            if (@request()->website && !empty(@request()->website)) {
                                                if ($item['differance'] == 0) {
                                                    echo '<td>' . $item['differance'] . '</td>';
                                                } else {
                                                    echo '<td style="background-color: red;color:white">' . $item['differance'] . '</td>';
                                                }
                                            } else {
                                                echo '<td></td>';
                                            }
                                            ?>

                                    @if ($item['date'] == date('Y-m-d'))
                                    @if ($item['current_balance'] - $item['closing_balance'] < 1 &&
                                        $item['current_balance'] - $item['closing_balance']> -1)
                                        <td style="background-color: green;color:white">
                                            {{ number_format($item['current_balance'], 2) }}</td>
                                        @else
                                        <td style="background-color: red;color:white">
                                            {{ number_format($item['current_balance'], 2) }}</td>
                                        @endif
                                        @else
                                        <td></td>
                                        @endif

                                        @if (auth()->user()->username === 'dev' &&  $item['differance'] > 0.01)
                                        <th scope="col">
                                            <a href="">
                                                <span class="badge">
                                                    <i class="fas fa-edit"></i>
                                                </span>
                                            </a>
                                        </th>
                                        @endif


                                </tr>

                                @empty
                                <tr>
                                    <td colspan="100%">
                                        <p class="text-dark">{{ __('reports.no_data_found') }}</p>
                                    </td>
                                </tr>
                                @endforelse
                                <thead class="thead-dark">
                                    <tr>
                                        <th></th>
                                        <th>{{ __('reports.total') }}</th>
                                        <th></th>
                                        <th></th>
                                        <th>{{ number_format($deposit_amount, 2) }}</th>
                                        <th>{{ number_format($deposit_charges, 2) }}</th>
                                        <th>{{ number_format($withdrawal_amount, 2) }}</th>
                                        <th>{{ number_format($withdrawal_charges, 2) }}</th>
                                        <th>{{ number_format($settlement_amount, 2) }}</th>
                                        <th>{{ number_format($settlement_charges, 2) }}</th>
                                        <th>{{ number_format($adjustment, 2) }}</th>
                                        <th>{{ number_format($adjustment_charges, 2) }}</th>
                                        <th>{{ number_format($commission, 2) }}</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>

                                    </tr>

                                </thead>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    {{-- <div class="card-footer">
                        {{ $records->appends($_GET)->links('partials.pagination') }}
                    </div> --}}
                </div>
            </div>
        </div>

    </div>
    @push('js')
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
                    $submitButton.html(
                        "<i class='fa fa-spinner fa-spin me-1'></i> {{ __('reports.processing') }}");

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
    @push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
    @endpush

</x-admin-layout>
