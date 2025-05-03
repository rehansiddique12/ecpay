<x-admin-layout :title="$pageTitle">
    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('admin.partner.balance.search') }}" method="get">
            <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
            <div class="row justify-content-between align-items-center">

                <div class="col-md-4">
                    <div class="form-group">
                        <label>From Date</label>
                        <input type="date" class="form-control" value="{{ @request()->from_date }}" name="from_date"
                            id="datepicker" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>To Date</label>
                        <input type="date" class="form-control" value="{{ @request()->to_date }}" name="to_date"
                            id="datepicker" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Partner</label>
                        <select name="partner" class="form-control">
                            <option value="">All</option>
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
                        <label>Adjustment Type</label>
                        <select name="adjustment" class="form-control">
                            <option value="">@lang('All')</option>
                            <option value="4" @if (@request()->adjustment == '4') selected @endif>@lang('Top-Up')
                            </option>
                            <option value="1" @if (@request()->adjustment == '1') selected @endif>@lang('Balance')
                            </option>
                            <option value="2" @if (@request()->adjustment == '2') selected @endif>@lang('Deposit')
                            </option>
                            <option value="3" @if (@request()->adjustment == '3') selected @endif>@lang('Withdrawal')
                            </option>
                        </select>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <br>
                        <button type="submit" class="btn waves-effect waves-light btn-primary"><i class="icon-base ti tabler-search me-1"></i> @lang('Search')</button>
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

                                    <th scope="col">@lang('Name')</th>
                                    <th scope="col">@lang('User-Name')</th>
                                    <th scope="col">Website</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Charges</th>
                                    <th scope="col">@lang('Ajustment Type')</th>
                                    <th scope="col">Remarks</th>
                                    <th scope="col">Created At</th>

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
                                            <td>{{ $item->created_at }}</td>
                                        </tr>
                                    @endif
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
                    <div class="card-footer">
                        {{ $records->appends($_GET)->links('partials.pagination') }}
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
