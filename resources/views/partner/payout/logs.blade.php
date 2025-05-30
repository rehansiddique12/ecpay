<x-partner-layout :title="$pageTitle">
<h1 class="text-center">
    <span class="badge badge-primary">Available to withdraw: <b>{{ $withdrawal_able_amount }} TK</b></span>
</h1>

    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('partner.payout-log.search') }}" method="get">
            <div class="row justify-content-between align-items-center">
                <!--<div class="col-md-3">-->
                <!--    <div class="form-group">-->
                <!--        <input type="text" name="name" value="{{@request()->name}}" class="form-control"-->
                <!--               placeholder="@lang('Email/ Username/ Trx')">-->
                <!--    </div>-->
                <!--</div>-->

                <input type="text" name="name" hidden value="{{@request()->name}}" class="form-control"
                               placeholder="@lang('Email/ Username/ Trx')">


                <div class="col-md-3">
                    <div class="form-group">
                        <input type="date" class="form-control" value="{{@request()->date_time}}" name="date_time" id="datepicker"/>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <input type="text" name="partner_transection_id" value="{{@request()->partner_transection_id}}" class="form-control" placeholder="@lang('Transection No.')">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <select name="status" class="form-control">

                            <option value="1"
                                    @if(@request()->status == '1') selected @endif>@lang('Pending Payment')</option>
                                    <option value="4" @if(@request()->status == '4') selected @endif>@lang('All Payment')</option>
                            <option value="2"
                                    @if(@request()->status == '2') selected @endif>@lang('Complete Payment')</option>
                            <option value="3"
                                    @if(@request()->status == '3') selected @endif>@lang('Cancel Payment')</option>
                        </select>
                    </div>
                </div>






                <div class="col-md-2">
                    <div class="form-group">
                        <button type="submit" class="btn waves-effect waves-light btn-primary"><i class="icon-base ti tabler-search me-1"></i> @lang('Search')</button>
                    </div>
                </div>

            </div>
        </form>

    </div>


    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">

            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">@lang('Date')</th>
                        <th scope="col">@lang('Trx Number')</th>
                        <th scope="col">@lang('Partner Trx Number')</th>
                        <th scope="col">@lang('Method')</th>
                        <th scope="col">@lang('Amount')</th>
                        <th scope="col">@lang('Merchant Charge')</th>
                        <th scope="col">@lang('Net Amount')</th>
                        <th scope="col">@lang('Status')</th>
                        <th scope="col">@lang('Transfer Status')</th>
                        <th scope="col">@lang('Sent From')</th>
                        <th scope="col">@lang('More')</th>

                    </tr>
                    </thead>
                    <tbody>
                    @forelse($records as $key => $item)
                        <tr>
                            <td data-label="@lang('Date')"> {{ convertToUserTimezone($item->created_at) }}</td>
                            <td data-label="@lang('Trx Number')" class="font-weight-bold text-uppercase">
                                {{ $item->trx_id }}<br>
                                <span class="text text-success">{{ $item->txn_id }}</span>

                            </td>
                            <td>{{ $item->partner_transection_id }}
                                <br>
                                {{ $item->member_id }}
                            </td>

                            <td>{{ $item->e_wallet_name }}</td>
                            <td data-label="@lang('Amount')"
                                class="font-weight-bold">{{ getAmount($item->amount ) }} {{ $basic->currency_symbol }}</td>
                            <td data-label="@lang('Charge')"
                                class="text-success">{{ $item->charge }} {{ $basic->currency_symbol }}</td>

                            <td data-label="@lang('Net Amount')"
                                class="font-weight-bold">{{ getAmount($item->amount + $item->charge) }} {{ $basic->currency_symbol }}</td>

                            <td data-label="@lang('Status')" class="text-lg-center text-right">
                                @if($item->transfer_status == 2)
                                    <span class="badge badge-light"><i class="fa fa-circle text-success font-12"></i> @lang('Request Approved')</span>
                                @elseif($item->transfer_status == 1)
                                    <span class="badge badge-light"><i class="fa fa-circle text-warning font-12"></i> @lang('Request Pending')</span>
                                @elseif($item->transfer_status == 3)
                                    <span class="badge badge-light"><i class="fa fa-circle text-danger font-12"></i> @lang('Request Rejected')</span>
                                @endif
                                <br>
                                
                            </td>
                            <td data-label="@lang('Method')">
                                @if($item->status == "Complete")
                                    <span class="badge badge-light"><i class="fa fa-circle text-success font-12"></i> @lang('Transfered')</span>
                                @elseif($item->status == "Reject")
                                <span class="badge badge-light"><i class="fa fa-circle text-danger font-12"></i> @lang('Transfer Rejected')</span>
                                @else

                                <span class="badge badge-light"><i class="fa fa-circle text-warning font-12"></i> @lang('Transfer Pending')</span>
                                    
                                @endif
                            </td>
                            <td data-label="@lang('Method')">
                                {{ $item->e_wallet_phone_number }}
                                <br>
                                {{ $item->e_wallet_type }}
                            </td>

                                <td data-label="@lang('More')">
                                    @php
                                        $details = ($item->information != null) ? json_encode($item->information) : null;
                                    @endphp
                                    <button type="button" class="btn btn-primary btn-icon edit_button"
                                            data-toggle="modal" data-target="#myModal"
                                            data-route="{{route('partner.payout-action',$item->id)}}"
                                            data-feedback="{{$item->feedback}}"
                                            data-info="{{$details}}"
                                            data-id="{{$item->id}}"
                                            data-status="{{$item->status}}">
                                        @if(Request::routeIs('partner.payout-request'))
                                            <i class="fa fa-pencil-alt"></i>
                                        @else
                                            <i class="fa fa-eye"></i>
                                        @endif
                                    </button>
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
                {{ $records->appends($_GET)->links('partials.pagination') }}
            </div>
        </div>
    </div>




    <!-- Modal for Edit button -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content ">
                <div class="modal-header modal-colored-header bg-primary">
                    <h4 class="modal-title" id="myModalLabel">@lang('Payout Information')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>

                <form role="form" method="POST" class="actionRoute" action="" enctype="multipart/form-data">
                    @csrf
                    @method('put')
                    <div class="modal-body">
                        <ul class="list-group withdraw-detail">
                        </ul>

                        @if(Request::routeIs('partner.payout-request'))

                            <div class="form-group addForm">

                            </div>
                        @endif

                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('Close')
                        </button>
                        @if(Request::routeIs('partner.payout-request'))
                            <input type="hidden" class="action_id" name="id">
                            <button type="submit" class="btn btn-primary" name="status"
                                    value="2">@lang('Approve')</button>
                            <button type="submit" class="btn btn-danger" name="status"
                                    value="3">@lang('Reject')</button>
                        @endif
                    </div>

                </form>


            </div>
        </div>
    </div>

@push('js')
    <script>
        (function ($) {

            $(document).ready(function () {
                $(document).on("click", '.edit_button', function (e) {
                    var id = $(this).data('id');
                    $(".action_id").val(id);
                    $(".actionRoute").attr('action', $(this).data('route'));
                    var details = Object.entries($(this).data('info'));
                    var list = [];
                    var ImgPath = "{{asset(config('location.withdrawLog.path'))}}";
                    details.map(function (item, i) {
                        if (item[1].type == 'file') {
                            var singleInfo = `<br><img src="${ImgPath}/${item[1].field_name}" alt="..." class="w-50">`;
                        } else {
                            var singleInfo = `<span class="font-weight-bold ml-3">${item[1].field_name}</span>  `;
                        }
                        list[i] = ` <li class="list-group-item"><span class="font-weight-bold "> ${item[0].replace('_', " ")} </span> : ${singleInfo}</li>`
                    });


                    if ($(this).data('status') != '1') {
                        list[details.length + 1] = `<li class="list-group-item"><span class="font-weight-bold">@lang('Partner Feedback')</span> : <span">${$(this).data('feedback')}</span></li>`;
                        $('.addForm').html(``)
                    } else {
                        list[details.length + 1] = ``;
                        $('.addForm').html(`
                                <div class="form-group">
                                <label for="feedback">@lang('feedback')</label>
                                <textarea class="form-control" name="feedback"></textarea>
                                </div>
                        `);
                    }

                    $('.withdraw-detail').html(list);
                });
            });
        })(jQuery);


        $(document).ready(function () {
            $('select').select2({
                selectOnClose: true
            });
        });
    </script>
@endpush


</x-partner-layout>

