<x-admin-layout :title="$pageTitle">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary shadow">
                    <div class="card-body">
                        @if(adminAccessRoute(config('role.payment_gateway.access.add')))
                        <a href="{{route('admin.deposit.accounts.create')}}"
                           class="btn btn-success btn-sm float-right mb-3"><i
                                class="fa fa-plus-circle"></i> {{trans('Add New')}}</a>
                        @endif

                        <table class="table ">
                            <thead class="thead-dark">
                            <tr>
                                <th scope="col">@lang('Name')</th>
                                <th scope="col">@lang('Status')</th>

                                @if(adminAccessRoute(config('role.payment_gateway.access.edit')))
                                    <th scope="col">@lang('Action')</th>
                                @endif
                            </tr>

                            </thead>
                            <tbody id="sortable">
                            @if(count($methods) > 0)
                                @foreach($methods as $method)
                                    <tr data-code="{{ $method->code }}">
                                        <td data-label="@lang('Name')">{{ $method->name }} </td>
                                        <td data-label="@lang('Status')" class="text-lg-center text-right">

                                            {!!  $method->status == 1 ? '<span class="badge badge-light"><i class="fa fa-circle text-success success font-12"></i>'.trans(' Active').'</span>' : '<span class="badge badge-light"><i class="fa fa-circle text-danger danger font-12"></i>'.trans(' Inactive').'</span>' !!}
                                        </td>
                                        @if(adminAccessRoute(config('role.payment_gateway.access.edit')))
                                            <td data-label="@lang('Action')">
                                                <a href="{{ route('admin.deposit.maccounts.edit', $method->id) }}"
                                                   class="btn btn-primary btn-circle"
                                                   data-toggle="tooltip"
                                                   data-placement="top"
                                                   data-original-title="@lang('Edit this Payment Methods info')">
                                                   <i class="icon-base ti tabler-pencil me-1"></i></a>
                                                <button type="button"
                                                        data-code="{{$method->code}}"
                                                        data-status="{{$method->status}}"
                                                        data-message="{{($method->status == 0)?'Enable':'Disable'}}"
                                                        class="btn btn-sm btn-{{($method->status == 0)?'success':'danger'}}   btn-circle disableBtn"
                                                        data-bs-toggle="modal" data-bs-target="#disableModal"><i
                                                        class="icon-base ti tabler-{{($method->status == 0)?'check':'ban'}}"></i>
                                                </button>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td class="text-center text-danger" colspan="8">
                                        @lang('No Data Found')
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>





                <div class="modal modal-top fade" id="disableModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title" id="modalTopTitle">@lang('Confirmation')</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                <form action="{{ route('admin.accounts.payment.methods.deactivate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="code">
                    <div class="modal-body">
                        <p class="font-weight-bold">@lang('Are you sure to') <span
                                class="messageShow"></span> {{trans('this?')}}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn waves-effect waves-light btn-dark"
                                data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn waves-effect waves-light btn-primary messageShow"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>




@push('js')
    <script>
        "use strict";
        $('.disableBtn').on('click', function () {
            var status = $(this).data('status');
            $('.messageShow').text($(this).data('message'));
            var modal = $('#disableModal');
            modal.find('input[name=code]').val($(this).data('code'));
        });
    </script>
@endpush

</x-admin-layout>
