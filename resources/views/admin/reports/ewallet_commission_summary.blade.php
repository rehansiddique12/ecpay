<x-admin-layout :title="$pageTitle">
       <div class="row">
        <div class="col-md-12">

            {{-- Filter Form --}}
            <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow p-6">
                <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
                <form method="GET" action="{{ route('admin.ewallet_commission_summary') }}" class="mb-4">
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

                        {{-- Ewallet Name --}}
                        <div class="col-md-4">
                            <select name="e_wallet_name" class="form-control">
                                <option value="">All Wallets</option>
                                @foreach (['Bkash', 'Rocket', 'Nagad'] as $wallet)
                                    <option value="{{ $wallet }}" {{ request('e_wallet_name') == $wallet ? 'selected' : '' }}>
                                        {{ $wallet }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Ewallet Type (added) --}}
                        <div class="col-md-4">
                            <select name="e_wallet_type" class="form-control">
                                <option value="">All Types</option>
                                @foreach (['Agent', 'Personal', 'Merchant'] as $type)
                                    <option value="{{ $type }}" {{ request('e_wallet_type') == $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Ewallet Number (optional filter) --}}
                        <div class="col-md-4">
                            <input type="text" name="e_wallet_phone_number"
                                   value="{{ request('e_wallet_phone_number') }}" class="form-control"
                                   placeholder="Wallet Number">
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
                                    <th scope="col">Date</th>
                                    <th scope="col">Ewallet Name</th>
                                    <th scope="col">Ewallet Number</th>
                                    <th scope="col">Ewallet Type</th> {{-- New Column --}}
                                    <th scope="col">Total Commission</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $item)
                                    <tr>
                                        {{-- Date --}}
                                        <td>{{ \Carbon\Carbon::parse($item->payment_date)->format('Y-m-d') }}</td>

                                        {{-- Ewallet Name --}}
                                        <td>{{ $item->e_wallet_name ?? 'N/A' }}</td>

                                        {{-- Ewallet Number --}}
                                        <td>{{ $item->e_wallet_phone_number ?? 'N/A' }}</td>

                                        {{-- Ewallet Type --}}
                                        <td>{{ $item->e_wallet_type ?? 'N/A' }}</td>

                                        {{-- Total Commission --}}
                                        <td>${{ number_format($item->total_commission, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <p class="text-dark">@lang('No Data Found')</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4">Total Commission:</th>
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

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endpush
</x-admin-layout>
