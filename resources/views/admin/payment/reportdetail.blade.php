<x-admin-layout :title="$pageTitle">
<div class="row">
    <div class="col-2"></div>
    <div class="col-4">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">{{$fund_count}}</h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('Total Transactions')
                                </h6>
                            </div>
                            <div class="ml-auto mt-md-3 mt-lg-0">
                                <span class="opacity-7 text-muted"><i class="fas fa-wallet"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">{{$fund_sum}}</h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('Total Deposit Amount')
                                </h6>
                            </div>
                            <div class="ml-auto mt-md-3 mt-lg-0">
                                <span class="opacity-7 text-muted"><i class="fa fa-hand-holding-usd"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
</div>


    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            <b>Date:</b>{{$heading['date']}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <b>Status:</b>{{$heading['status']}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <b>E-Wallet Name:</b>{{$heading['gateway']}}
            <br><br>
            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">@lang('Date')</th>
                        <th scope="col">@lang('Trx Number')</th>
                        <th scope="col">@lang('Username')</th>
                        <th scope="col">@lang('User Account')</th>
                        <th scope="col">@lang('Method')</th>
                        <th scope="col">@lang('Amount')</th>
                        <th scope="col">@lang('Merchant Charge')</th>
                        <th scope="col">@lang('Payable')</th>
                        <th scope="col">@lang('E-Wallet No')</th>
                        <th scope="col">@lang('Type')</th>
                        <th scope="col">@lang('Status')</th>
                         <th scope="col">@lang('Source')</th>
                         <th scope="col">@lang('Receipt')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($funds as $key => $fund)
                        <tr>
                            <td data-label="@lang('Date')"> {{ dateTime($fund->created_at,'d M,Y H:i') }}</td>
                            <td data-label="@lang('Trx Number')"
                                class="font-weight-bold text-uppercase">{{ optional($fund->payment)->txn_id }}</td>
                            <td data-label="@lang('Username')">
                                @if(optional($fund->user)->username!="dummyuser")
                                    <div class="d-lg-flex d-block align-items-center ">
                                        <div class="mr-3"><img
                                                src="{{getFile(config('location.user.path').optional($fund->user)->image) }}"
                                                alt="user"
                                                class="rounded-circle" width="45" height="45"></div>
                                        <div class="">
                                            <h5 class="text-dark mb-0 font-16 font-weight-medium">{{ optional($fund->user)->username }}</h5>
                                            <span class="text-muted font-14">{{ optional($fund->user)->email }}</span>
                                        </div>
                                    </div>
                                 @else
                                Partner Transection
                                @endif
                            </td>
                            <td data-label="@lang('Method')">{{ optional($fund->payment)->sender }}</td>
                            <td data-label="@lang('Method')">{{ optional($fund->gateway)->name }}</td>
                            <td data-label="@lang('Amount')"
                                class="font-weight-bold">{{ getAmount($fund->amount ) }} {{$fund->gateway->currency}}</td>
                            <td data-label="@lang('Charge')"
                                class="text-success">{{ getAmount(optional($fund->payment)->charge,2) }} {{$fund->gateway->currency}}</td>

                            <td data-label="@lang('Payable')"
                                class="font-weight-bold">{{ getAmount($fund->final_amount) }} {{$fund->gateway->currency}}</td>

                                <td data-label="@lang('Method')">{{ optional($fund->payment)->e_wallet_phone_number	 }}</td>
                                <td data-label="@lang('Method')">{{ optional($fund->payment)->e_wallet_type }}</td>


                            <td data-label="@lang('Status')" class="text-lg-center text-right">
                                @if($fund->status == 2)
                                    <span class="badge badge-light"><i
                                            class="fa fa-circle text-warning warning font-12"></i> @lang('Pending')</span>
                                @elseif($fund->status == 1)
                                    <span class="badge badge-light"><i
                                            class="fa fa-circle text-success success font-12"></i> @lang('Approved')</span>
                                @elseif($fund->status == 3)
                                    <span class="badge badge-light"><i
                                            class="fa fa-circle text-danger danger font-12"></i> @lang('Rejected')</span>
                                @endif
                            </td>
                            <td data-label="@lang('Method')">{{ optional($fund->payment)->source }}</td>
                            <td>
                                @if(!empty($fund->receipt_image))
                                <a data-fancybox="images" href="{{ getFile(config('location.receipts.path').$fund->receipt_image) }}">
                                    <h2><i class="fa fa-file"></i></h2>
                                </a>
                                @endif
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
                {{ $funds->appends($_GET)->links('partials.pagination') }}
            </div>
        </div>
    </div>

    <!-- Modal for Edit button -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content ">
                <div class="modal-header modal-colored-header bg-primary">
                    <h4 class="modal-title" id="myModalLabel">@lang('Deposit Information')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>

                <form role="form" method="POST" class="actionRoute" action="" enctype="multipart/form-data">
                    @csrf
                    @method('put')
                    <div class="modal-body">
                        <ul class="list-group withdraw-detail">
                        </ul>

                        <div class="get-feedback">

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('Close')</button>
                        @if(Request::routeIs('admin.payment.pending'))
                            <input type="hidden" class="action_id" name="id">
                            <button type="submit" class="btn btn-primary" name="status"
                                    value="1">@lang('Approve')</button>
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
        "use strict";
        $(document).ready(function () {
            $('select[name=status]').select2({
                selectOnClose: true
            });

            $(document).on("click", '.edit_button', function (e) {
                var id = $(this).data('id');
                var feedback = $(this).data('feedback');

                $(".action_id").val(id);
                $(".actionRoute").attr('action', $(this).data('route'));
                var details = Object.entries($(this).data('info'));
                var list = [];
                details.map(function (item, i) {
                    if (item[1].type == 'file') {
                        var singleInfo = `<br><img src="${item[1].field_name}" alt="..." class="w-100">`;
                    } else {
                        var singleInfo = `<span class="font-weight-bold ml-3">${item[1].field_name}</span>  `;
                    }
                    list[i] = ` <li class="list-group-item"><span class="font-weight-bold "> ${item[0].replace('_', " ")} </span> : ${singleInfo}</li>`
                });
                $('.withdraw-detail').html(list);

                if (feedback == '') {
                    var $res = `<div class="form-group"><br>
                                <label class="font-weight-bold">{{trans('Send You Feedback')}}</label>
                                <textarea name="feedback" class="form-control" row="3" required>{{old('feedback')}}</textarea>
                            </div>`
                } else {
                    var $res = `<h5>{{trans('Feedback')}}</h5>
                    <p>${feedback}</p>`
                }

                $('.get-feedback').html($res)
            });
        });
    </script>
    <script>
    $(document).ready(function () {
        $('[data-fancybox="images"]').fancybox({
            buttons: ["close"],
            loop: true, // Enables looping through images
        });
    });
</script>
@endpush
</x-admin-layout>
