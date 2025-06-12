<x-admin-layout :title="$pageTitle">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
                <div class="card-body">
                    <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
                    <div class="table-responsive">
                        <table class="categories-show-table table table-hover table-striped table-bordered">
                            <thead class="thead-dark">
                                <tr>

                                    <th scope="col">{{ __('reports.e_wallet') }}</th>
                                    <th scope="col">{{ __('reports.live_balance') }}</th>
                                    <th scope="col">{{ __('reports.deposit') }}</th>
                                    <th scope="col">{{ __('reports.withdrawal') }}</th>

                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($data))
                                    <tr>
                                        <td>{{ __('reports.daily_total') }}</td>
                                        <td>{{ number_format($sumBalance, 2) }}</td>
                                        <td>{{ number_format($sumDailySent, 2) }}</td>
                                        <td>{{ number_format($sumDailyReceived, 2) }}</td>

                                    </tr>
                                    <tr>
                                        <td colspan="4">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">
                                            <h3><b>{{ __('reports.nagad') }}</b></h3>
                                        </td>
                                    </tr>
                                    @foreach ($data as $key => $item)
                                        @if ($item->e_wallet_name == 'Nagad')
                                            <tr>
                                                <td>{{ $item->account_no }}</td>
                                                <td>{{ number_format($item->balance, 2) }}</td>
                                                <td>{{ number_format($item->received, 2) }}</td>
                                                <td>{{ number_format($item->send, 2) }}</td>

                                            </tr>
                                        @endif
                                    @endforeach

                                    <tr>
                                        <td colspan="4">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">
                                            <h3><b>{{ __('reports.bkash') }}</b></h3>
                                        </td>
                                    </tr>
                                    @foreach ($data as $key => $item)
                                        @if ($item->e_wallet_name == 'bKash')
                                            <tr>
                                                <td>{{ $item->account_no }}</td>
                                                <td>{{ number_format($item->balance, 2) }}</td>
                                                <td>{{ number_format($item->received, 2) }}</td>
                                                <td>{{ number_format($item->send, 2) }}</td>

                                            </tr>
                                        @endif
                                    @endforeach

                                    <tr>
                                        <td colspan="4">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">
                                            <h3><b>{{ __('reports.rocket') }}</b></h3>
                                        </td>
                                    </tr>
                                    @foreach ($data as $key => $item)
                                        @if ($item->e_wallet_name == 'Rocket')
                                            <tr>
                                                <td>{{ $item->account_no }}</td>
                                                <td>{{ number_format($item->balance, 2) }}</td>
                                                <td>{{ number_format($item->received, 2) }}</td>
                                                <td>{{ number_format($item->send, 2) }}</td>

                                            </tr>
                                        @endif
                                    @endforeach
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
