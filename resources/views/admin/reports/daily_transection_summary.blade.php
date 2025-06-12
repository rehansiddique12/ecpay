<x-admin-layout :title="$pageTitle">

    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('admin.reports.daily_transection_summary') }}" method="get">
            <div class="row align-items-center">
                <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('reports.select_date') }}</label>
                        <input type="date" class="form-control" value="{{ $date }}" name="date"
                            id="datepicker" />
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
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
                    <h4>{{ __('reports.transection_report') }}</h4>
                    <!-- <h3>Deposit Report</h3> -->
                    <div class="table-responsive">
                        <table class="categories-show-table table table-hover table-striped table-bordered">
                            <thead class="thead-dark">
                                <tr>

                                    <th scope="col">{{ __('reports.deposit') }}</th>
                                    <th scope="col">{{ __('reports.nagad') }}</th>
                                    <th scope="col">{{ __('reports.bkash') }}</th>
                                    <th scope="col">{{ __('reports.rocket') }}</th>

                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($data))
                                    <tr>
                                        <td>{{ __('reports.deposit_transactions') }}</td>
                                        <td>{{ $data['nagad_d']->record_count }}</td>
                                        <td>{{ $data['bkash_d']->record_count }}</td>
                                        <td>{{ $data['rocket_d']->record_count }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('reports.deposit_amount') }}</td>
                                        <td>{{ getAmount($data['nagad_d']->total_amount, 2) }}TK</td>
                                        <td>{{ getAmount($data['bkash_d']->total_amount, 2) }}TK</td>
                                        <td>{{ getAmount($data['rocket_d']->total_amount, 2) }}TK</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('reports.transfer_in_transactions') }}</td>
                                        <td>{{ $data['nagad_in']->record_count }}</td>
                                        <td>{{ $data['bkash_in']->record_count }}</td>
                                        <td>{{ $data['rocket_in']->record_count }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('reports.transfer_in_amount') }}</td>
                                        <td>{{ getAmount($data['nagad_in']->total_amount, 2) }}TK</td>
                                        <td>{{ getAmount($data['bkash_in']->total_amount, 2) }}TK</td>
                                        <td>{{ getAmount($data['rocket_in']->total_amount, 2) }}TK</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('reports.total_transactions') }}</td>
                                        <td>{{ $data['nagad_d']->record_count + $data['nagad_in']->record_count }}</td>
                                        <td>{{ $data['bkash_d']->record_count + $data['bkash_in']->record_count }}</td>
                                        <td>{{ $data['rocket_d']->record_count + $data['rocket_in']->record_count }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('reports.total_amount') }}</td>
                                        <td>{{ getAmount($data['nagad_d']->total_amount + $data['nagad_in']->total_amount, 2) }}TK
                                        </td>
                                        <td>{{ getAmount($data['bkash_d']->total_amount + $data['bkash_in']->total_amount, 2) }}TK
                                        </td>
                                        <td>{{ getAmount($data['rocket_d']->total_amount + $data['rocket_in']->total_amount, 2) }}TK
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <br>
                    <div class="table-responsive">
                        <table class="categories-show-table table table-hover table-striped table-bordered">
                            <thead class="thead-dark">
                                <tr>

                                    <th scope="col">{{ __('reports.withdrawal') }}</th>
                                    <th scope="col">{{ __('reports.nagad') }}</th>
                                    <th scope="col">{{ __('reports.bkash') }}</th>
                                    <th scope="col">{{ __('reports.rocket') }}</th>

                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($data))
                                    <tr>
                                        <td>{{ __('reports.withdrawal_transactions') }}</td>
                                        <td>{{ $data['nagad_w']->record_count }}</td>
                                        <td>{{ $data['bkash_w']->record_count }}</td>
                                        <td>{{ $data['rocket_w']->record_count }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('reports.withdrawal_amount') }}</td>
                                        <td>{{ getAmount($data['nagad_w']->total_amount, 2) }}TK</td>
                                        <td>{{ getAmount($data['bkash_w']->total_amount, 2) }}TK</td>
                                        <td>{{ getAmount($data['rocket_w']->total_amount, 2) }}TK</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('reports.transfer_out_transactions') }}</td>
                                        <td>{{ $data['nagad_out']->record_count }}</td>
                                        <td>{{ $data['bkash_out']->record_count }}</td>
                                        <td>{{ $data['rocket_out']->record_count }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('reports.transfer_out_amount') }}</td>
                                        <td>{{ getAmount($data['nagad_out']->total_amount, 2) }}TK</td>
                                        <td>{{ getAmount($data['bkash_out']->total_amount, 2) }}TK</td>
                                        <td>{{ getAmount($data['rocket_out']->total_amount, 2) }}TK</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('reports.total_transactions') }}</td>
                                        <td>{{ $data['nagad_w']->record_count + $data['nagad_out']->record_count }}
                                        </td>
                                        <td>{{ $data['bkash_w']->record_count + $data['bkash_out']->record_count }}
                                        </td>
                                        <td>{{ $data['rocket_w']->record_count + $data['rocket_out']->record_count }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('reports.total_amount') }}</td>
                                        <td>{{ getAmount($data['nagad_w']->total_amount + $data['nagad_out']->total_amount, 2) }}TK
                                        </td>
                                        <td>{{ getAmount($data['bkash_w']->total_amount + $data['bkash_out']->total_amount, 2) }}TK
                                        </td>
                                        <td>{{ getAmount($data['rocket_w']->total_amount + $data['rocket_out']->total_amount, 2) }}TK
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
    @push('js')
    @endpush
</x-admin-layout>
