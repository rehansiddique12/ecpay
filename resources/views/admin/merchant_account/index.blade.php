<x-admin-layout :title="$pageTitle">

@push('styles')
<style>
  .settable {
    width: 100%;
    border-collapse: collapse;
  }
  .setcolumn {
    word-wrap: break-word;
    max-width: 100px;
  }
</style>
@endpush

<div class="row">
    <div class="col-md-12">
        <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
            <div class="card-body">
                @if(adminAccessRoute(config('role.merchant_accounts.access.add')))
                <a href="javascript:void(0)" class="btn btn-sm btn-primary mr-2 mb-2" data-bs-target="#newModal" data-bs-toggle="modal">
                    <span><i class="fa fa-plus-circle"></i> @lang('Add New')</span>
                </a>
                @endif

                @if(adminAccessRoute(config('role.merchant_accounts.access.view')))
                <div class="table-responsive">
                    <table class="categories-show-table table table-hover table-striped table-bordered settable">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">@lang('ID')</th>
                                <th scope="col">@lang('Account-Name')</th>
                                <th scope="col">@lang('Account No.')</th>
                                <th scope="col">@lang('Username')</th>
                                <th scope="col">@lang('Password')</th>
                                <th class="setcolumn" scope="col">API Data</th>
                                <th scope="col">@lang('Status')</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $key => $item)
                            <tr>
                                <td style="max-width: 75px;">{{ $item['id'] }}</td>
                                <td>{{ $item['account_name'] }}</td>
                                <td>{{ $item['e_wallet_phone_number'] }}</td>
                                <td>{{ $item['username'] }}</td>
                                <td >{{ $item['password'] }}</td>
                                <td style="max-width: 300px;"><span class="bg-success text-white p-1">App Key:</span><br> {{ $item['app_key'] }}<br>
                                <span class="bg-primary text-white p-1">App Secret:</span><br> {{ $item['app_secret'] }}<br>
                            </td>
                               
                                <td data-label="@lang('Status')" class="text-lg-center text-right">
                                    @if ($item->status == 0)
                                    <span class="badge badge-light">
                                        <i class="fa fa-circle text-danger danger font-12"></i> @lang('Deactive') </span>
                                    @else
                                    <span class="badge badge-light">
                                        <i class="fa fa-circle text-success success font-12"></i> @lang('Active')</span>
                                    @endif
                                </td>
                                <td data-label="@lang('Action')">
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="icon-base ti tabler-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                            @if(adminAccessRoute(config('role.merchant_accounts.access.delete')))
                                            <form action="{{ route('admin.merchant_accounts.delete', $item['id']) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-icon edit_button"><i class="fa fa-trash"></i> Delete</button>
                                            </form>
                                            @endif
                                            @if(adminAccessRoute(config('role.merchant_accounts.access.edit')))
                                            <button type="button" class="btn btn-sm btn-icon edit_button" data-bs-toggle="modal" data-bs-target="#editModal{{ $item['id'] }}">
                                                <i class="fa fa-edit"></i> Edit
                                            </button>
                                            @endif
                                        </div>
                                    </div>
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
                @endif
            </div>
        </div>
    </div>

</div>


@foreach($records as $item)
<!-- Edit Modal -->
<div id="editModal{{ $item['id'] }}" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header modal-colored-header bg-warning">
                <h5 class="modal-title">@lang('Edit Record')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.merchant_accounts.update', $item['id']) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row justify-content-between align-items-center">
                        <!-- Input fields for editing the record -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">Account-Name</label>
                                <input type="text" class="form-control" name="account_name" value="{{ $item['account_name'] }}" required />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">Account No.</label>
                                <input type="text" class="form-control" name="e_wallet_phone_number" value="{{ $item['e_wallet_phone_number'] }}" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">Username</label>
                                <input type="text" class="form-control" name="username" value="{{ $item['username'] }}" required />
                            </div>
                        </div>
                        <!-- Add other input fields for editing here -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">Password</label>
                                <input type="text" class="form-control" name="password" value="{{ $item['password'] }}" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">Status</label>
                                <select class="form-control" name="status" required>
                                    <option value="1" {{ $item['status'] == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $item['status'] == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                        
                        
                       
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">App Key</label>
                                <input type="text" class="form-control" name="app_key"  value="{{ $item['app_key'] }}" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">App Secret</label>
                                <input type="text" class="form-control" name="app_secret"  value="{{ $item['app_secret'] }}" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">@lang('Update')</button>
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach





{{-- New MODAL --}}
<div id="newModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header modal-colored-header bg-primary">
                <h5 class="modal-title">@lang('Add New')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.merchant_accounts.add') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row justify-content-between align-items-center">



                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">Account-Name</label>
                                <input type="text" class="form-control" name="account_name" required />
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">Account No.</label>
                                <input type="text" class="form-control" name="e_wallet_phone_number" />
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">Username</label>
                                <input type="text" class="form-control" name="username" required />
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">Password</label>
                                <input type="text" class="form-control" name="password" />
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">Status</label>
                                <select class="form-control" name="status" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">App Key</label>
                                <input type="text" class="form-control"  name="app_key" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">App Secret</label>
                                <input type="text" class="form-control"  name="app_secret" />
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">@lang('Save')</button>
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </form>
        </div>
    </div>
</div>


@push('js')
<script>
    "use strict";
    

    // $(document).ready(function() {
    //     $('select').select2({
    //         selectOnClose: true
    //     });
    // });
</script>

@endpush
</x-admin-layout>