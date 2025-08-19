<x-admin-layout :title="$pageTitle">
    <div class="row">
        <div class="col-md-12">

            {{-- Filter Form --}}
            <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow p-6">
                <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
                <form method="GET" action="{{ route('admin.ewallet_commission_by_wallet') }}" class="mb-4">
                    <div class="row g-2">
                        {{-- From Date --}}
                        <div class="col-md-4">
                            <input type="date" name="from_date"
                                   value="{{ request('from_date', now()->format('Y-m-d')) }}" class="form-control">
                        </div>

                        {{-- To Date --}}
                        <div class="col-md-4">
                            <input type="date" name="to_date"
                                   value="{{ request('to_date', now()->format('Y-m-d')) }}" class="form-control">
                        </div>


                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </div>

                </form>
            </div>

            {{-- Table --}}
            <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="categories-show-table table table-hover table-striped table-bordered settable table-sm">
                            <thead class="thead-dark bg-primary">
                                <tr>
                                    <th scope="col">Ewallet Name</th>
                                    <th scope="col">Ewallet Type</th>
                                    <th scope="col">Total Commission</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $item)
                                    <tr>
                                        <td>{{ $item->e_wallet_name ?? 'N/A' }}</td>
                                        <td>{{ $item->e_wallet_type ?? 'N/A' }}</td>
                                        <td>${{ number_format($item->total_commission, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2">
                                            <p class="text-dark">@lang('No Data Found')</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2">Total Commission:</th>
                                    <th>${{ number_format($totalCommission, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if (method_exists($records, 'links'))
                        <div class="card-footer">
                            {{ $records->appends(request()->query())->links('partials.pagination') }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-admin-layout>
