<x-admin-layout :title="$pageTitle">
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">

            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">@lang('E-Wallet Name')</th>
                        <th scope="col">@lang('Phone')</th>
                        <th scope="col">@lang('Type')</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($records as $key => $item)
                        <tr>
                            <td>{{ $item['e_wallet_name'] }}</td>
                             <td>{{ $item['account_no'] }}</td>
                            <td>{{ $item['type'] }}</td>
                            <td>
                                <form action="{{ route('admin.merchant.delete', $item['id']) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-primary btn-icon edit_button"><i
                                        class="icon-base ti tabler-trash me-1 bg-white"></i></button>
                                </form>

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
    <div class="col-md-4">
        <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('admin.merchant.add') }}" method="post">
            @csrf
            <div class="row justify-content-between align-items-center">



                <div class="col-md-12">
                    <div class="form-group">
                        <label class="pr-3">E-Wallet Name</label>
                            <input type="text" class="form-control" name="e_wallet_name"/ required>
                    </div>
                </div>

                 <div class="col-md-12">
                    <div class="form-group">
                        <label class="pr-3">Phone/Account No.</label>
                            <input type="text" class="form-control" name="account_no"/ required>
                    </div>
                </div>


                <div class="col-md-12">
                    <div class="form-group">
                        <label class="pr-3">Type</label>
                            <input type="text" class="form-control" name="account_type"/ required>
                    </div>
                </div>


                <div class="col-md-12">
                    <div class="form-group">
                        <button type="submit" class="btn waves-effect waves-light btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        </form>

    </div>
    </div>
</div>








@push('js')
@endpush
</x-admin-layout>

