@extends('partner.layouts.open')
@section('title')
@lang('Add Fund')
@endsection
@section('content')

<div class="row g-3 m-4">
    @foreach($gateways as $key => $gateway)
    {{-- @dd($gateway) --}}
    <div class="col-lg-2 col-6 col-sm-4 col-md-3">
        <div class="user-panel">
            <div class="deposit-box addFund" data-bs-toggle="modal" data-bs-target="#makeDeposit" data-id="{{$gateway->id}}" data-name="{{$gateway->name}}" data-currency="{{$gateway->currency}}" data-gateway="{{$gateway->code}}" data-qr_image="{{$gateway->qr_image!=''?getFile(config('location.gateway.path').$gateway->qr_image):''}}" data-min_amount="{{getAmount($gateway->min_amount, $basic->fraction_number)}}" data-max_amount="{{getAmount($gateway->max_amount,$basic->fraction_number)}}" data-percent_charge="{{getAmount($gateway->percentage_charge,$basic->fraction_number)}}" data-fix_charge="{{getAmount($gateway->fixed_charge, $basic->fraction_number)}}">
                <div class="img-box">
                    <img class="img-fluid" src="{{ getFile(config('location.gateway.path').$gateway->image)}}" alt="{{$gateway->name}}" />
                    <p>{{trans($gateway->name)}}</p>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

@push('loadModal')
<!-- Deposit Modal -->

<div class="modal fade" id="makeDeposit" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="makeDepositLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">

                <h4>@lang('Make Deposit')</h4>
                <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="general-error"></div>
            <div class="modal-body">
                <form>
                    <div class="payment-form">
                        @if(0 == $totalPayment)
                        <p class="text-danger depositLimit"></p>
                        @endif
                        <input type="hidden" class="gateway" name="gateway" value="">
                        <div class="form-group mb-30">
                            <div class="input-box">
                                <div class="input-group">
                                    <input type="text" class="amount form-control" required name="amount" autocomplete="off" placeholder="@lang('Amount')" @if($totalPayment !=null) value="{{$totalPayment}}" placeholder="@lang('Amount')" readonly @endif>
                                    <div class="input-group-append">
                                        <span class="input-group-text show-currency"></span>
                                    </div>
                                </div>
                            </div>
                            <pre class="text-danger amount-error"></pre>
                        </div>

                        <div class="form-group mb-30">
                            <div class="input-box">
                                <div class="input-group">
                                    <input type="text" class="account_no form-control" required name="account_no" autocomplete="off" placeholder="@lang('Sender Phone No.')">
                                    <div class="input-group-append">
                                        <span class="input-group-text">Sender Phone No.</span>
                                    </div>
                                </div>
                            </div>
                            <pre class="text-danger account-no-error"></pre>
                        </div>

                    </div>
                </form>
                <div class="payment-info text-center">
                    <img id="loading" src="{{asset('assets/admin/images/loading.gif')}}" alt="..." class="w-15" />
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-success checkCalc">@lang('Next')</button>
            </div>
        </div>
    </div>
</div>



@endpush
@endsection

@push('script')

<script>
    $('#loading').hide();
    "use strict";
    var id, minAmount, maxAmount, baseSymbol, fixCharge, percentCharge, currency, amount, gateway, name, qr_image;
    $('.addFund').on('click', function() {
        id = $(this).data('id');
        name = $(this).data('name');
        gateway = $(this).data('gateway');
        minAmount = $(this).data('min_amount');
        maxAmount = $(this).data('max_amount');
        baseSymbol = "{{config('basic.currency_symbol')}}";
        fixCharge = $(this).data('fix_charge');
        percentCharge = $(this).data('percent_charge');
        currency = $(this).data('currency');
        qr_image = $(this).data('qr_image');
        $('.depositLimit').text(`@lang('Transaction Limit:') <?= $min_deposit ?> - ${maxAmount}  ${baseSymbol}`);

        var depositCharge = `@lang('Charge:') ${fixCharge} ${baseSymbol}  ${(0 < percentCharge) ? ' + ' + percentCharge + ' % ' : ''}`;
        $('.depositCharge').text(depositCharge);

        $('.method-name').text(`@lang('Payment By') ${$(this).data('name')} - ${currency}`);
        $('.show-currency').text("{{config('basic.currency')}}");
        $('.gateway').val(currency);

        // amount
    });


    $(".checkCalc").on('click', function() {
        const $button = $(this);
        $button.prop('disabled', true);
        $('#general-error').html('');
        $('.payment-form').addClass('d-none');
        $('#loading').show();
        $('.modal-backdrop.fade').addClass('show');

        amount = $('.amount').val();
        account_no = $('.account_no').val();
        var username = <?= json_encode($username); ?>;

        $.ajax({
            url: "{{route('partner.addFund.request.open')}}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                amount,
                gateway,
                account_no,
                username
            },
            success(data) {
                // console.log(data);

                $('.payment-form').addClass('d-none');
                $('.checkCalc').closest('.modal-footer').addClass('d-none');

                var htmlData = `

                     <ul class="list-group text-center text-white">
                        <li class="list-group-item bg-transparent">
                            <img class="w-100"src="${data.gateway_image}"
                                style="max-width:100px; max-height:100px; margin:0 auto;"/>
                        </li>
                        ${data.qr_image ? `
                        <li class="list-group-item bg-transparent">
                            <img class="w-100" src="${data.qr_image}"
                                style="width: 300px; margin: 0 auto;"/>
                        </li>` : ''}
                        <li style="font-size: 18px" class="list-group-item bg-transparent">
                            @lang('Account No'):
                            <strong id="accountNumber">${data.sender}</strong>
                            <button id="copyButton" class="btn btn-primary btn-sm ml-2">Copy</button>
                        </li>
                        <li class="list-group-item bg-transparent">
                            @lang('Amount'):
                            <strong>${data.amount} </strong>
                        </li>

                        ${(data.isCrypto == true) ? `
                        <li class="list-group-item bg-transparent">
                            ${data.conversion_with}
                        </li>
                        ` : ``}

                        ${qr_image ? `
                        <li class="list-group-item bg-transparent">

                            <a href="${data.payment_url}" class="btn btn-success line-h22   btn-block addFund ">@lang('Next')</a>
                        </li>` : `<li class="list-group-item bg-transparent">
                        <a href="${data.payment_url}" class="btn btn-success line-h22   btn-block addFund ">@lang('Pay Now')</a>
                        </li>`}


                        </ul>
                        `;

                $('.payment-info').html(htmlData)
            },
            complete: function() {
                $('#loading').hide();
                $button.prop('disabled', false);
            },
            error(err) {

                const errors = err.responseJSON;
                // Clear the general error list
                $('#general-error').html('');

                // Build a list of errors
                let errorHtml = '<ul class="text-danger">';

                // Check if 'errors' exist in the response
                if (errors.errors) {
                    for (const key in errors.errors) {
                        errors.errors[key].forEach(message => {
                            errorHtml += `<li>${message}</li>`;
                        });
                    }
                }

                // Check for general 'error' key in the response
                if (errors.error) {
                    errorHtml += `<li>${errors.error}</li>`;
                }

                // Check for directly returned fields like 'amount' in the response
                if (errors.amount) {
                    errors.amount.forEach(message => {
                        errorHtml += `<li>${message}</li>`;
                    });
                }

                errorHtml += '</ul>';

                // Display the errors
                $('#general-error').html(errorHtml);

                $('.payment-form').removeClass('d-none');
                $button.prop('disabled', false);
            }
        });
    });


    $('.close').on('click', function(e) {
        $('#loading').hide();
        $('.payment-form').removeClass('d-none');
        $('.checkCalc').closest('.modal-footer').removeClass('d-none');
        $('.payment-info').html(``)
        $('.amount').val(``);
        $("#addFundModal").modal("hide");
    });
</script>

<script>
    $(document).ready(function() {
        var clipboard = new ClipboardJS('#copyButton', {
            target: function() {
                return document.getElementById('accountNumber');
            }
        });


        clipboard.on('success', function(e) {
            e.clearSelection();
            $('#copyButton').text('Copied');
            $('#copyButton').addClass('disabled');
            $('#copyButton').prop('disabled', true);
        });

        clipboard.on('error', function(e) {
            console.error('Copy failed:', e.action);
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/clipboard@2/dist/clipboard.min.js"></script>

@endpush
