<x-admin-layout :title="$pageTitle">
    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('admin.partner.balance.search') }}" method="get">
            <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
            <div class="row justify-content-between align-items-center">

                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ __('partner.from_date') }}</label>
                        <input type="date" class="form-control" value="{{ @request()->from_date }}" name="from_date"
                            id="datepicker" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ __('partner.to_date') }}</label>
                        <input type="date" class="form-control" value="{{ @request()->to_date }}" name="to_date"
                            id="datepicker" />
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

                    <div class="col-md-2">
                        <div class="form-group">
                            <br>
                            <button type="submit" class="btn waves-effect waves-light btn-primary"><i
                                    class="icon-base ti tabler-search me-1"></i> {{ __('partner.search') }}</button>
                        </div>
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
                                    <th scope="col">{{ __('partner.name') }}</th>
                                    <th scope="col">{{ __('partner.user_name') }}</th>
                                    <th scope="col">{{ __('partner.website') }}</th>
                                    <th scope="col">{{ __('partner.amount') }}</th>
                                    <th scope="col">{{ __('partner.charges') }}</th>
                                    <th scope="col">{{ __('partner.adjustment_type') }}</th>
                                    <th scope="col" style="width: 500px;">{{ __('partner.remarks') }}</th>
                                    <th scope="col">{{ __('partner.created_at') }}</th>
                                </tr>

                            </thead>
                            <tbody>
                                @forelse($records as $key => $item)
                                    @if (isset($item->api))
                                        <tr>
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
                                            <td>{{ $item->created_at }}</td>
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
        </script>
    @endpush
</x-admin-layout>
