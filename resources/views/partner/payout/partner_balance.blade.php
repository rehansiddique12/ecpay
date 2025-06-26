<x-partner-layout :title="$pageTitle">
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
            <h4 class="mb-10 text-primary font-weight-medium ">@lang('partner_basic.Adjustments_en')</h4>
            <form id="dateFilterForm" action="{{ route('partner.partner.balance.search') }}" method="GET">
                @csrf

                <input type="hidden" id="from_date" name="from_date" value="{{ request('from_date') }}">
                <input type="hidden" id="to_date" name="to_date" value="{{ request('to_date') }}">

                <div class="ml-20 d-flex gap-3 mb-4" style="margin-left: 30px;">
                    <button type="button"
                        class="btn btn-yellow btn-date-filter {{ request('from_date') == $today && request('to_date') == $today ? 'active' : '' }}"
                        id="btn-today">@lang('partner_basic.Today_en')</button>

                    <button type="button"
                        class="btn btn-yellow btn-date-filter {{ request('from_date') == $yesterday && request('to_date') == $yesterday ? 'active' : '' }}"
                        id="btn-yesterday">@lang('partner_basic.Yesterday_en')</button>

                    <button type="button"
                        class="btn btn-yellow btn-date-filter {{ request('from_date') == $last7 && request('to_date') == $today ? 'active' : '' }}"
                        id="btn-last7">@lang('partner_basic.Last_7_days_en')</button>
                </div>
            </form>

        </div>
        <form action="{{ route('partner.partner.balance.search') }}" method="get">

            <div class="row justify-content-between align-items-center">

                <div class="col-md-4">
                    <div class="form-group">
                        <label>@lang('partner_basic.from_date_label')</label>
                        <input type="date" class="form-control" value="{{ @request()->from_date }}" name="from_date"
                            id="from_date" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>@lang('partner_basic.to_date_label')</label>
                        <input type="date" class="form-control" value="{{ @request()->to_date }}" name="to_date"
                            id="to_date" />
                    </div>
                </div>


                <div class="col-md-4">
                    <div class="form-group">
                        <label>@lang('partner_basic.Adjustment_Type_en')</label>
                        <select name="adjustment" class="form-control">
                            <option value="">@lang('partner_basic.all_en')</option>
                            <option value="4" @if (@request()->adjustment == '4') selected @endif>@lang('partner_basic.Top_Up_en')
                            </option>
                            <option value="1" @if (@request()->adjustment == '1') selected @endif>@lang('partner_basic.Balance_en')
                            </option>
                            <option value="2" @if (@request()->adjustment == '2') selected @endif>@lang('partner_basic.deposit_label')
                            </option>
                            <option value="3" @if (@request()->adjustment == '3') selected @endif>@lang('partner_basic.withdrawal_label')
                            </option>
                        </select>
                    </div>
                </div>

            <div class="d-flex justify-contant-center gap-5 mt-5">
                <div class="col-md-3">
                    <label>@lang('partner_basic.Search_by_Name_en') </label>
                    <input type="text" class="form-control" name="search_by_name" id="searchInput"
                        placeholder="Type name...">
                </div>



                    <div class="form-group mt-5">
                        <button type="submit" class="btn waves-effect waves-light btn-primary"><i class="icon-base ti tabler-search me-1"></i> @lang('partner_basic.search')</button>
                    </div>

                    <div class="mt-5">
                        <a href="{{ route('admin.blance_export', ['from_date' => @request()->from_date, 'to_date' => @request()->to_date, 'partner' => @request()->partner, 'search_by_name' => @request()->search_by_name, 'adjustment' => @request()->adjustment]) }}"
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
                    <h3 style="color: #7367f0">{{ __('partner_basic.Adjustments_en') }}</h3>
                    <div class="table-responsive">
                        <table class="categories-show-table table table-hover table-striped table-bordered">
                            <thead class="thead-dark">
                                <tr>


                                    <th scope="col">@lang('partner_basic.Created_At_en')</th>
                                    <th scope="col">@lang('partner_basic.Amount_en')</th>
                                    <th scope="col">@lang('partner_basic.Charges_en')</th>
                                    <th scope="col">@lang('partner_basic.Adjustment_Type_en')</th>
                                    <th scope="col">@lang('partner_basic.Remarks_en')</th>


                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $key => $item)
                                    @if (isset($item->api))
                                        <tr>
                                            
                                            <td>{{ convertToUserTimezone($item->created_at) }}</td>
                                            <td>{{ $item->amount }}</td>
                                            <td>{{ $item->charges }}</td>

                                            <td data-label="@lang('Status')" class="text-lg-center text-right">
                                                @if ($item->adjustment == 2)
                                                    <span class="badge badge-light">
                                                        <i class="fa fa-circle text-warning success font-12"></i>
                                                        @lang('Deposit')</span>
                                                @elseif($item->adjustment == 3)
                                                    <span class="badge badge-light">
                                                        <i class="fa fa-circle text-danger success font-12"></i>
                                                        @lang('Withdrawal')</span>
                                                @elseif($item->adjustment == 4)
                                                    <span class="badge badge-light">
                                                        <i class="fa fa-circle text-primary success font-12"></i>
                                                        @lang('Top-Up')</span>
                                                @else
                                                    <span class="badge badge-light">
                                                        <i class="fa fa-circle text-success success font-12"></i>
                                                        @lang('Balance')</span>
                                                @endif
                                            </td>
                                            <td>{{ $item->reason }}</td>
                                        </tr>
                                    @endif
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

            document.addEventListener("DOMContentLoaded", function() {
                const today = new Date();
                const yyyy = today.getFullYear();
                const mm = String(today.getMonth() + 1).padStart(2, '0');
                const dd = String(today.getDate()).padStart(2, '0');
                const todayStr = `${yyyy}-${mm}-${dd}`;
                const form = document.getElementById('dateFilterForm');

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
                    form.submit();
                });
            });
        </script>
    @endpush

</x-partner-layout>
