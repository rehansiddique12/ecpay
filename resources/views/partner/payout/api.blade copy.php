@extends('partner.layouts.app')
@section('title')
@lang($page_title)
@endsection
@section('content')


<div class="row">
    <div class="col-md-12">
        <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
            <div class="card-body">

                <a href="javascript:void(0)" class="btn btn-sm btn-primary mr-2" data-target="#newModal" data-toggle="modal">
                    <span><i class="fa fa-plus-circle"></i> @lang('Add New')</span>
                </a>

                <div class="table-responsive">
                    <table class="categories-show-table table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">@lang('Name')</th>
                                <th scope="col">@lang('Username')</th>
                                <th scope="col">Deposit</th>
                                <th scope="col">Deposit Profit</th>
                                <th scope="col">Withdarawal</th>
                                <th scope="col">Withdarawal Profit</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $key => $item)
                            <tr>
                                <td>{{ $item['name'] }}</td>
                                <td>{{ $item['username'] }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>

                                <td data-label="@lang('Action')">
                                    <div class="dropdown show ">
                                        <a class="dropdown-toggle p-3" href="#" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                            <form action="{{ route('partner.apis.commission', $item['id']) }}" method="GET">
                                                <button type="submit" class="btn btn-sm btn-icon edit_button"><i class="fa fa-calculator"></i> Commission %</button>
                                            </form>
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
            </div>
        </div>
    </div>

</div>





{{-- New MODAL --}}
<div id="newModal" class="modal fade show" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header modal-colored-header bg-primary">
                <h5 class="modal-title">@lang('Add New')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('partner.apis.add') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row justify-content-between align-items-center">



                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">Name</label>
                                <input type="text" class="form-control" name="name" required />
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
                                <label class="pr-3">E-Mail</label>
                                <input type="text" class="form-control" name="email" />
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">Phone</label>
                                <input type="text" class="form-control" name="phone" />
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">Password</label>
                                <input type="text" class="form-control" name="password" required />
                            </div>
                        </div>


                        @if($user->acc_type=="Partner")
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">Website</label>
                                <input type="text" class="form-control" placeholder="http://ecwin.asia" name="website" required />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">API End-Point</label>
                                <input type="text" class="form-control" placeholder="http://ecwin.asia/api" name="api_endpoint" />
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">@lang('Save')</button>
                    <button type="button" class="btn btn-dark" data-dismiss="modal">@lang('Close')</button>
                </div>
            </form>
        </div>
    </div>
</div>







@endsection
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
<script>
    function setBalanceItem(itemId) {
        // Find the input field in the modal
        var balanceInput = document.getElementById("balanceInput");

        // Set the value of the input field to the item id
        balanceInput.value = itemId;
    }
</script>

@endpush