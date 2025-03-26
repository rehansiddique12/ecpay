<x-admin-layout :title="$pageTitle">
    @push('styles')
    <script src="{{ asset('public/assets/css/select2.min.css')}}"></script>
    <style>
        tr th{
          color: white !important
        }
    </style>
    @endpush
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
            <div class="card-body">
                {{-- @if(adminAccessRoute(config('role.partners.access.add'))) --}}
                {{-- <a href="javascript:void(0)" class="btn btn-sm btn-primary mr-2" data-target="#newModal" data-toggle="modal">
                    <span><i class="fa fa-plus-circle"></i> @lang('Add New')</span>
                </a> --}}
                {{-- @endif --}}

                <div class="table-responsive">
                    <table class="categories-show-table table table-hover table-striped table-bordered settable">
                        <thead class="thead-dark bg-primary">
                            <tr>
                                <th scope="col">@lang('ID')</th>
                                <th scope="col">@lang('Partner')</th>
                                <th scope="col">@lang('Group Name')</th>
                                <th scope="col">@lang('Group ID')</th>
                                <th scope="col">@lang('Status')</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $key => $item)
                            <tr>
                                <td>{{ $item['id'] }}</td>
                                <td>{{ $partners[$item['api_id']] ?? ''; }}</td>
                                <td>{{ $item['group_name'] }}</td>
                                <td>{{ $item['group_username'] }}</td>
                                <td data-label="@lang('Status')" class="text-lg-center text-right">
                                    @if($item->status == 1)
                                    <span class="badge badge-light"><i class="fa fa-circle text-success font-12"></i> @lang('Active')</span>
                                    @else
                                    <span class="badge badge-light"><i class="fa fa-circle text-danger font-12"></i> @lang('Inactive')</span>
                                    @endif
                                </td>


                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="icon-base ti tabler-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            {{-- @if(adminAccessRoute(config('role.partners.access.delete'))) --}}
                                            <form action="{{ route('admin.groups.delete', $item['id']) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-icon edit_button"><i
                                                    class="icon-base ti tabler-trash me-1"></i>  Delete</button>
                                            </form>
                                            {{-- @endif --}}
                                            {{-- @if(adminAccessRoute(config('role.partners.access.edit'))) --}}
                                            <button type="button" class="btn btn-sm btn-icon edit_button" data-bs-toggle="modal" data-bs-target="#editModal{{ $item['id'] }}">
                                                <i class="icon-base ti tabler-user me-1"></i> Edit
                                            </button><br>


                                            {{-- @endif --}}

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
                <div class="card-footer">
                    {{ $records->appends($_GET)->links('partials.pagination') }}
                </div>
            </div>
        </div>
    </div>

</div>

@foreach($records as $item)
<!-- Edit Modal -->
<div id="editModal{{ $item['id'] }}" class="modal modal-top fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header modal-colored-header bg-warning">
                <h5 class="modal-title" id="modalTopTitle">@lang('Edit Record') </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.groups.update', $item['id']) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row justify-content-between align-items-center">
                        <!-- Input fields for editing the record -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">Select Partner</label>
                                <select class="form-control" required name="api_id">
                                    <option value="">Select Partner</option>
                                    @foreach($partners as $id => $name)
                                        <option value="{{ $id }}" @if(old('api_id', $item->api_id) == $id) selected @endif>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="pr-3">Group Name</label>
                                <input type="text" class="form-control" name="group_name" value="{{ $item['group_name'] }}" required />
                            </div>
                            <div class="form-group">
                                <label class="pr-3">Group ID</label>
                                <input type="text" class="form-control" name="group_username" value="{{ $item['group_username'] }}" required />
                            </div>
                            <div class="form-group">
                                <label class="pr-3">Status</label>
                                <select class="form-control" name="status" required>
                                    <option value="1" {{ $item['status'] == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $item['status'] == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>




                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">@lang('Update')</button>
                    <button type="button" class="btn btn-dark" data-dismiss="modal">@lang('Close')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@push('js')
<script src="{{ asset('public/assets/js/select2.min.js')}}"></script>
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
<script>
    function setBalanceItem(itemId) {
        // Find the input field in the modal
        var balanceInput = document.getElementById("balanceInput");

        // Set the value of the input field to the item id
        balanceInput.value = itemId;
    }

    function setParentID(parentidd, acc_idd) {
        // Find the input field in the modal
        var parentidInput = document.getElementById("parentid");
        var acc_idInput = document.getElementById("acc_id");

        // Set the value of the input field to the item id
        parentidInput.value = parentidd;
        acc_idInput.value = acc_idd;
    }
</script>

<script>
$(document).ready(function(){
    // Attach change event listener to the select element
    $('#adjustment').change(function(){
        // Get the selected value
        var selectedValue = $(this).val();

        // Check if selected value is 1 or 2
        if(selectedValue == 1 || selectedValue == 2){
            // If selected value is 1 or 2, check amount_type1 and uncheck amount_type2
            $('#amount_type1').prop('checked', true);
            $('#amount_type2').prop('checked', false);
        }
        else if(selectedValue == 3){
            // If selected value is 3, check amount_type2 and uncheck amount_type1
            $('#amount_type2').prop('checked', true);
            $('#amount_type1').prop('checked', false);
        }
    });
});
</script>

@endpush
</x-admin-layout>
