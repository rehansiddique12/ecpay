<x-admin-layout :title="$pageTitle">
    @push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
@endpush

@php
    $today = \Carbon\Carbon::today()->toDateString();
    $yesterday = \Carbon\Carbon::yesterday()->toDateString();
    $last7 = \Carbon\Carbon::today()->subDays(6)->toDateString();
@endphp


<style>
    .hover:hover {
        background-color: #ffc000;
        color: white;
    }

    .btn-yellow.active {
        background-color: #ffc000 !important;
        color: white !important;
        border: 2px solid #e0a800;
    }
</style>

    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <div class="d-flex d-lg-flex d-md-block align-items-center">
            <h4 class="mb-10 text-primary font-weight-medium ">{{ __('partner_basic.Adjustments_en') }}</h4>
            <div class="ml-20 d-flex gap-5 mb-10" style="margin-left: 30px;">
                <button type="button"
                    class="btn btn-yellow btn-date-filter {{ request('from_date') == $today && request('to_date') == $today ? 'active' : '' }}"
                    id="btn-today">{{ __('transaction.today') }}</button>

                <button type="button"
                    class="btn btn-yellow btn-date-filter {{ request('from_date') == $yesterday && request('to_date') == $yesterday ? 'active' : '' }}"
                    id="btn-yesterday">{{ __('transaction.yesterday') }}</button>

                <button type="button"
                    class="btn btn-yellow btn-date-filter {{ request('from_date') == $last7 && request('to_date') == $today ? 'active' : '' }}"
                    id="btn-last7">{{ __('transaction.last_7_days') }}</button>
            </div>
        </div>
        <form action="{{ route('admin.partner.balance.search') }}" method="get">

            <div class="row justify-content-between align-items-center">

                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ __('partner.from_date') }}</label>
                        <input type="date" class="form-control" value="{{ @request()->from_date }}" name="from_date"
                        id="from_date" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ __('partner.to_date') }}</label>
                        <input type="date" class="form-control" value="{{ @request()->to_date }}" name="to_date"
                        id="to_date" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ __('partner.partner') }}</label>
                        <select id="select2Basic" name="partner" class="select2 form-select" data-allow-clear="true"
                            data-placeholder="{{ __('partner.select_partner') }}">
                            <option></option>
                            <option value="">{{ __('partner.all') }}</option>
                            @foreach ($partners as $partner)
                                <option value="{{ $partner->id }}" @if (@request()->partner == $partner->id) selected @endif>
                                    {{ $partner->website }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>{{ __('partner.adjustment_type') }}</label>
                            <select name="adjustment" class="form-select">
                                <option value="">{{ __('partner.all') }}</option>

                                <option value="4" @if (@request()->adjustment == '4') selected @endif>
                                    {{ __('partner.top_up') }}</option>
                                <option value="1" @if (@request()->adjustment == '1') selected @endif>
                                    {{ __('partner.balance') }}</option>
                                <option value="2" @if (@request()->adjustment == '2') selected @endif>
                                    {{ __('partner.deposit') }}</option>
                                <option value="3" @if (@request()->adjustment == '3') selected @endif>
                                    {{ __('partner.withdrawal') }}</option>

                            </select>
                        </div>
                    </div>

                    <div class="col-md-4 mt-3">
                        <label>@lang('partner_basic.Search_by_Name_en') </label>
                        <input type="text" class="form-control" name="search_by_name" id="searchInput" placeholder="Input Search ...">
                    </div>


                        <div class="col-md-4 d-flex justify-content-start align-items-center gap-6">
                        <div class="form-group">
                            <br>
                            <button type="submit" class="btn waves-effect waves-light btn-primary"><i
                                    class="icon-base ti tabler-search me-1"></i> {{ __('partner.search') }}</button>

                        </div>
                        {{-- <div class="form-group mt-8">
                            <input type="hidden" name="export" id="exportInput" value="">
                            <button type="submit" class="btn waves-effect waves-light btn-success"
                                onclick="document.getElementById('exportInput').value = 1;">
                                <i class="fas fa-download"></i> {{ __('transaction.export_data') }}
                            </button>
                        </div> --}}

                        {{-- <a href="{{ route('admin.blance_export', ['from_date' => @request()->from_date, 'to_date' => @request()->to_date, 'partner' => @request()->partner, 'search_by_name' => @request()->search_by_name, 'adjustment' => @request()->adjustment ]) }}"
                            class="btn waves-effect waves-light btn-success" id="exportButton">
                            <i class="icon-base ti tabler-download me-1"></i> {{ __('merchant_reports.export') }}
                        </a> --}}

                        <a href="{{ route('admin.blance_export', [
                            'from_date' => request()->from_date,
                            'to_date' => request()->to_date,
                            'partner' => request()->partner,
                            'search_by_name' => request()->search_by_name,
                            'adjustment' => request()->adjustment,
                        ]) }}"
                        class="btn waves-effect waves-light btn-success" id="exportButton">
                            <i class="icon-base ti tabler-download me-1"></i> {{ __('merchant_reports.export') }}
                        </a>
                </div>
                </div>
            </div>
        </form>

    </div>



    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
                <div class="card-body">
                    <button type="button" class="btn btn-primary mb-5" data-bs-toggle="modal" data-bs-target="#newModal">
                        Add Balance / Adjustment
                    </button>
                    <div class="table-responsive">
                        <table class="categories-show-table table table-hover table-striped table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">{{ __('partner.created_at') }}</th>
                                    <th scope="col">{{ __('partner.name') }}</th>
                                    <th scope="col">{{ __('partner.user_name') }}</th>
                                    <th scope="col">{{ __('partner.website') }}</th>
                                    <th scope="col">{{ __('partner.amount') }}</th>
                                    <th scope="col">{{ __('partner.charges') }}</th>
                                    <th scope="col">{{ __('partner.adjustment_type') }}</th>
                                    <th scope="col" style="width: 500px;">{{ __('partner.remarks') }}</th>
                                </tr>

                            </thead>
                            <tbody>
                                @forelse($records as $key => $item)
                                    @if (isset($item->api))
                                    <tr>
                                            <td>{{ $item->created_at }}</td>
                                            <td>{{ $item->api->name }}</td>
                                            <td>{{ $item->api->username }}</td>
                                            <td>{{ $item->api->website }}</td>
                                            <td>{{ $item->amount }}</td>
                                            <td>{{ $item->charges }}</td>

                                            <td data-label="{{ __('partner.status') }}"
                                                class="text-lg-center text-right">
                                                @if ($item->adjustment == 2)
                                                    <span class="badge bg-dark">
                                                        <i class="fa fa-circle text-warning success font-12"></i>
                                                        {{ __('partner.deposit') }}
                                                    </span>
                                                @elseif($item->adjustment == 3)
                                                    <span class="badge bg-dark">
                                                        <i class="fa fa-circle text-danger success font-12"></i>
                                                        {{ __('partner.withdrawal') }}
                                                    </span>
                                                @elseif($item->adjustment == 4)
                                                    <span class="badge bg-dark">
                                                        <i class="fa fa-circle text-primary success font-12"></i>
                                                        {{ __('partner.top_up') }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-dark">
                                                        <i class="fa fa-circle text-success success font-12"></i>
                                                        {{ __('partner.balance') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td data-label="{{ __('partner.remarks') }}">
                                                {{ $item->reason }}
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="100%">
                                            <p class="text-dark">{{ __('partner.no_data_found') }}</p>
                                        </td>
                                    </tr>

                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        {{ $records->appends($_GET)->links('partials.pagination') }}
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="modal modal-top fade" id="newModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title" id="modalTopTitle">{{ __('merchant.add_new_api') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.apis.balance.add') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row justify-content-between align-items-center">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Select Domain</label>
                                    <select name="partner_id" class="form-select" data-allow-clear="true"
                                        data-placeholder="{{ __('partner.select_domain') }}" required>
                                        <option></option>
                                        <option value="">{{ __('partner.select_domain') }}</option>
                                        @foreach ($domains as $domain)
                                            <option value="{{ $domain->id }}"
                                                @if (@request()->domain == $domain->id) selected @endif>
                                                {{ $domain->name }} ===> ( {{ $domain->website }} )
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3 mt-4">{{ __('partner.amount') }}</label>
                                    <input type="number" step="0.01" class="form-control" name="amount" required />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mt-4">
                                    <label class="pr-3">{{ __('partner.charges') }}</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" name="charges" class="form-control" placeholder="{{ __('partner.charges') }}" required>
                                        <select name="charges_type" class="form-select">
                                            <option value="1">{{ __('partner.amount_option_amount') }}</option>
                                            <option value="2">{{ __('partner.amount_option_percentage') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3 mt-4">{{ __('partner.type') }}</label>
                                    <select class="form-select" name="adjustment" id="adjustment" required>
                                        <option value="4">{{ __('partner.type_top_up') }}</option>
                                        <option value="1">{{ __('partner.type_balance_adjustment') }}</option>
                                        <option value="2">{{ __('partner.type_deposit_adjustment') }}</option>
                                        <option value="3">{{ __('partner.type_withdrawal_adjustment') }}</option>

                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mt-4">
                                    <input value="1" type="radio" name="amount_type" id="amount_type1" checked>
                                    <label class="pr-3">(+) {{ __('partner.add') }}</label>
                                    <input value="2" type="radio" name="amount_type" id="amount_type2">
                                    <label class="pr-3">(-) {{ __('partner.deduct') }}</label>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="pr-3 mt-4">{{ __('partner.source') }}</label>
                                        <select class="form-select" name="source" required>
                                            <option value="E-Wallet">{{ __('partner.source_e_wallet') }}</option>
                                            <option value="Cash">{{ __('partner.source_cash') }}</option>
                                            <option value="Bank Transfer">{{ __('partner.source_bank_transfer') }}</option>
                                            <option value="Other">{{ __('partner.source_other') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="pr-3 mt-4">{{ __('partner.transactions_id') }}</label>
                                        <input type="text" class="form-control" name="txn" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3 mt-4">{{ __('partner.remarks') }}</label>
                                    <textarea name="reason" class="form-control"></textarea>
                                </div>
                            </div>

                        </div>
                        <button type="submit" class="btn btn-primary mt-4">{{ __('partner.add') }}</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
    @endpush
    @push('js')
        <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
        <script>
            $(document).ready(function() {
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

            document.addEventListener("DOMContentLoaded", function() {
                const today = new Date();
                const yyyy = today.getFullYear();
                const mm = String(today.getMonth() + 1).padStart(2, '0');
                const dd = String(today.getDate()).padStart(2, '0');
                const todayStr = `${yyyy}-${mm}-${dd}`;

                function setDateInputs(from, to) {
                    document.getElementById('from_date').value = from;
                    document.getElementById('to_date').value = to;
                }

                function setActiveButton(buttonId) {
                    document.querySelectorAll('.btn-date-filter').forEach(btn => btn.classList.remove('active'));
                    document.getElementById(buttonId).classList.add('active');
                }

                document.getElementById('btn-today').addEventListener('click', function() {
                    setDateInputs(todayStr, todayStr);
                    setActiveButton('btn-today');
                    const form = document.querySelector(
                        'form[action="{{ route('admin.payment.report.search') }}"]');
                    form.submit();
                });

                document.getElementById('btn-yesterday').addEventListener('click', function() {
                    const yesterday = new Date();
                    yesterday.setDate(today.getDate() - 1);
                    const yyy = yesterday.getFullYear();
                    const mmm = String(yesterday.getMonth() + 1).padStart(2, '0');
                    const ddd = String(yesterday.getDate()).padStart(2, '0');
                    const yesterdayStr = `${yyy}-${mmm}-${ddd}`;
                    setDateInputs(yesterdayStr, yesterdayStr);
                    setActiveButton('btn-yesterday');
                    const form = document.querySelector(
                        'form[action="{{ route('admin.payment.report.search') }}"]');
                    form.submit();
                });

                document.getElementById('btn-last7').addEventListener('click', function() {
                    const from = new Date();
                    from.setDate(today.getDate() - 6);
                    const yyy = from.getFullYear();
                    const mmm = String(from.getMonth() + 1).padStart(2, '0');
                    const ddd = String(from.getDate()).padStart(2, '0');
                    const fromStr = `${yyy}-${mmm}-${ddd}`;
                    setDateInputs(fromStr, todayStr);
                    setActiveButton('btn-last7');
                    const form = document.querySelector(
                        'form[action="{{ route('admin.payment.report.search') }}"]');
                    form.submit();
                });
            });

                   </script>
    @endpush
</x-admin-layout>


