<x-admin-layout :title="$pageTitle">

<div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
    <form action="{{ route('admin.adjustments.search') }}" method="get">
        <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
        <div class="row justify-content-between align-items-center">

            <div class="col-md-4">
                <div class="form-group">
                    <label>From Date</label>
                    <input type="date" class="form-control" value="{{@request()->from_date}}" name="from_date" id="datepicker" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>To Date</label>
                    <input type="date" class="form-control" value="{{@request()->to_date}}" name="to_date" id="datepicker" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Partner</label>
                    <select name="partner" class="form-select select2" data-allow-clear="true" data-placeholder="Select Partner">
                        <option value="">All</option>
                        @foreach($partners as $partner)
                        <option value="{{ $partner->id }}" @if(@request()->partner == $partner->id) selected @endif>{{ $partner->website }}</option>
                        @endforeach
                    </select>
                </div>
            </div>


           <div class="row">
            <div class="col-md-5">
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-select">
                        <option value="">@lang('All')</option>
                        <option value="1" @if(@request()->status == '1') selected @endif>@lang('Completed')</option>
                        <option value="0" @if(@request()->status == '0') selected @endif>@lang('Pending')</option>
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
                                <th scope="col">Payment Amount</th>
                                <th scope="col">Withdrawal Amount</th>
                                <th scope="col">Amount Adjusted</th>
                                <th scope="col">@lang('Status')</th>
                                <th scope="col">Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $key => $item)
                            <tr>
                                <td>{{ $item->api->name }}</td>
                                <td>{{ $item->api->username }}</td>
                                <td>{{ $item->api->website }}</td>
                                <td>{{ $item->payment }}</td>
                                <td>{{ $item->payout }}</td>
                                <td>{{ $item->adjustment }}</td>
                                <td data-label="@lang('Status')" class="text-lg-center text-right">
                                    @if($item->status == 1)
                                    <span class="badge badge-light">
                                        <i class="fa fa-circle text-success success font-12"></i> @lang('Completed')</span>
                                    @else
                                    <span class="badge badge-light">
                                        <i class="fa fa-circle text-warning success font-12"></i> @lang('Pending')</span>
                                    @endif
                                </td>
                                <td>{{ $item->created_at }}</td>
                               


                                <td data-label="@lang('Action')">
                                    {{-- <div class="dropdown show ">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="icon-base ti tabler-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                            @if(adminAccessRoute(config('role.adjustments.access.edit')))
                                            <form action="{{ route('admin.adjustments.approve', $item['id']) }}"
                                                method="GET">
                                                <button type="submit" class="btn btn-sm btn-icon edit_button"><i
                                                        class="fa fa-check"></i> Approve</button>
                                            </form>
                                            
                                            @endif
                                        </div>
                                    </div> --}}
                                </td>

                            </tr>
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

</script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script>
    $(document).ready(function () {
        $('form').on('submit', function () {
            const $form = $(this);
            const $submitButton = $form.find('button[type="submit"]');

            // Disable button and change text (optional)
            $submitButton.prop('disabled', true);
            $submitButton.html('<i class="fa fa-spinner fa-spin me-1"></i> @lang("Processing...")');

            // Allow form to proceed
            return true;
        });
       let $select = $('.select2').select2({
                // placeholder: "Select Partner",
                allowClear: true,
                selectOnClose: true,
            });

            // Prevent dropdown from opening on clear
            $select.on('select2:unselecting', function (e) {
                $(this).data('unselecting', true);
            });

            $select.on('select2:opening', function (e) {
                if ($(this).data('unselecting')) {
                    $(this).removeData('unselecting');
                    e.preventDefault();
                }
            });
    });
</script>

@endpush
@push('styles')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}">
    @endpush
</x-admin-layout>
