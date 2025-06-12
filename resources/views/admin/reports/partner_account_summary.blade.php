<x-admin-layout :title="$pageTitle">

    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('admin.reports.partner_account_summary') }}" method="get">
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
                        <input type="date" class="form-control" value="{{ $to_date }}" name="to_date"
                            id="datepicker" />
                    </div>
                </div>


                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('reports.source') }}</label>
                        <select name="website" class="form-select select2" data-allow-clear="true"
                            data-placeholder="Select Domain">
                            <option></option>
                            <option value="">{{ __('reports.all_source') }}</option>
                            @foreach ($domains as $partner)
                                <option value="{{ $partner->website }}"
                                    @if (@request()->website == $partner->website) selected @endif>{{ $partner->name }}</option>
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
                                    <th scope="col">{{ __('reports.partner') }}</th>
                                    <th scope="col">{{ __('reports.date') }}</th>
                                    <th scope="col">{{ __('reports.total_receive') }}</th>
                                    <th scope="col">{{ __('reports.total_withdrawal') }}</th>
                                    <th scope="col">{{ __('reports.total_charges') }}</th>
                                    <th scope="col">{{ __('reports.daily_balance') }}</th>
                                    <th scope="col">{{ __('reports.success_rate') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($data))
                                    @forelse($data as $key => $item)
                                        <tr>
                                            <td>{{ $item['partner'] }}</td>
                                            <td>{{ $item['date'] }}</td>
                                            <td>{{ number_format($item['deposit_amount'], 2) }}</td>
                                            <td>{{ number_format($item['withdrawal_amount'], 2) }}</td>
                                            <td>{{ number_format($item['total_charges'], 2) }}</td>
                                            <td>{{ number_format($item['daily_balance'], 2) }}</td>
                                            <td>{{ number_format($item['success_rate'], 2) }}%</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="100%">
                                                <p class="text-dark">{{ __('reports.no_data_found') }}</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        {{-- {{ $domains->appends($_GET)->links('partials.pagination') }} --}}
                    </div>
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
