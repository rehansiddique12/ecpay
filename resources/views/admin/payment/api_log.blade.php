<x-admin-layout :title="$pageTitle">
    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('admin.payment.apisearch') }}" method="get">
            <div class="row justify-content-between align-items-center">
                <div class="col-md-5">
                    <div class="form-group mb-2">
                        <input type="text" name="name" value="{{@request()->name}}" class="form-control"
                               placeholder="@lang('Type Here')">
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="form-group mb-2">
                        <select name="status" class="form-control">
                            <option value=""
                                    @if(@request()->status == '') selected @endif>@lang('All Payment')</option>
                            <option value="Complete"
                                    @if(@request()->status == 'Complete') selected @endif>@lang('Complete Payment')</option>
                            <option value="Pending"
                                    @if(@request()->status == 'Pending') selected @endif>@lang('Pending Payment')</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-2"></div>

                <div class="col-md-5">
                    <div class="form-group">
                        <input type="date" class="form-control" name="date_time" id="datepicker"/>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <select name="domain" class="form-control">
                            <option value="">@lang('Select Domain')</option>

                            @foreach($domains as $domain)
                                <option value="{{ $domain->id }}"
                                        @if(request()->domain == $domain->id) selected @endif>
                                    {{ $domain->name }} ===> ( {{ $domain->website }} )
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <button type="submit" class="btn waves-effect waves-light btn-primary"><i class="icon-base ti tabler-search me-1"></i>  @lang('Search')</button>
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
                        <th scope="col">@lang('Sender')</th>
                        <th scope="col">@lang('Method')</th>
                        <th scope="col">@lang('Received Account')</th>
                        <th scope="col">@lang('Amount')</th>
                        <th scope="col">@lang('Merchant Charge')</th>
                        <th scope="col">@lang('Payable')</th>
                        <th scope="col">@lang('Status')</th>
                         <th scope="col">@lang('Source')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($funds as $key => $fund)
                        <tr>
                            <td data-label="@lang('Date')"> {{ dateTime($fund->created_at,'d M,Y H:i') }}</td>
                            <td>{{ $fund->txn_id }}</td>
                            <td class="font-weight-bold text-uppercase">{{ $fund->sender }}</td>
                            <td class="font-weight-bold text-uppercase">{{ $fund->e_wallet_name }}</td>
                            <td class="font-weight-bold text-uppercase">{{ $fund->e_wallet_phone_number }}</td>
                            <td class="font-weight-bold text-uppercase">{{ getAmount($fund->amount,2) }}</td>
                            <td class="font-weight-bold text-uppercase">{{ getAmount($fund->charge,2) }}</td>
                            <td class="font-weight-bold text-uppercase">{{ getAmount(($fund->amount+$fund->charge)),2 }}</td>
                            <td class="font-weight-bold text-uppercase">{{ $fund->status }}</td>
                            <td class="font-weight-bold text-uppercase">{{ $fund->source }}</td>
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
@endpush

</x-admin-layout>
