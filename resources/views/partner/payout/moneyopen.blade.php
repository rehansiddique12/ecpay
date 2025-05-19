@extends('partner.layouts.open')
@section('title', trans($title))

@section('content')

<div class="row g-3 m-3">
    @foreach($gateways as $key => $gateway)
    <div class="col-lg-2 col-6 col-sm-4 col-md-3">
        <div class="user-panel">
            <div class="deposit-box addFund" data-id="{{$gateway->id}}" data-name="{{$gateway->name}}" data-min_amount="{{getAmount($min_withdrawal, $basic->fraction_number)}}" data-max_amount="{{getAmount($gateway->max_amount,$basic->fraction_number)}}" data-percent_charge="{{getAmount($gateway->percent_charge,$basic->fraction_number)}}" data-fix_charge="{{getAmount($gateway->fixed_charge, $basic->fraction_number)}}" data-backdrop='static' data-keyboard='false' data-bs-toggle="modal" data-bs-target="#makeDeposit">
                <div class="img-box">
                    <img class="img-fluid gateway" src="{{ getFile(config('location.withdraw.path').$gateway->image)}}" alt="{{$gateway->name}}">
                </div>
                <p>{{trans($gateway->name)}}</p>
            </div>
        </div>
    </div>
    @endforeach
</div>

@push('loadModal')
<div id="makeDeposit" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="method-name"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="white-text"></span>
                </button>
            </div>

            <form action="{{route('partner.payout.moneyRequest.transection')}}" method="post" id="moneyRequestForm">
                @csrf
                <div class="modal-body ">
                    <div class="payment-form">
                        <p class="text-danger depositLimit"></p>


                        <input type="hidden" class="gateway" name="gateway" value="">
                        <div class="form-group">
                            <label>@lang('Amount')</label>
                            <div class="input-box">
                                <div class="input-group input-group-lg">
                                    <input type="text" hidden value="{{$username}}" class="amount form-control" name="username">
                                    <input type="text" class="amount form-control" name="amount">
                                    <div class="input-group-append">
                                        <span class="input-group-text show-currency"></span>
                                    </div>
                                </div>
                                @error('amount')
                                <p class="text-danger">{{$message}}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="submit" class="btn btn-success" id="submitButton" onclick="disableSubmitButton()">@lang('Next')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush

@endsection
@push('script')

@if(count($errors) > 0 )
<script>

</script>
@endif

<script>
    "use strict";

    var id, minAmount, maxAmount, baseSymbol, fixCharge, percentCharge, currency, gateway;

    $('.addFund').on('click', function() {
        id = $(this).data('id');
        gateway = $(this).data('gateway');
        minAmount = $(this).data('min_amount');
        maxAmount = $(this).data('max_amount');
        baseSymbol = "{{config('basic.currency_symbol')}}";
        fixCharge = $(this).data('fix_charge');
        percentCharge = $(this).data('percent_charge');
        currency = $(this).data('currency');
        $('.depositLimit').text(`@lang('Transaction Limit:') ${minAmount} - ${maxAmount}  ${baseSymbol}`);

        var depositCharge = `@lang('Charge:') ${fixCharge} ${baseSymbol}  ${(0 < percentCharge) ? ' + ' + percentCharge + ' % ' : ''}`;
        $('.depositCharge').text(depositCharge);
        $('.method-name').text(`@lang('Payout By') ${$(this).data('name')}`);
        $('.show-currency').text("{{config('basic.currency')}}");
        $('.gateway').val(id);
    });
    $('.close').on('click', function(e) {
        $('#loading').hide();
        $('.amount').val(``);
        $("#makeDeposit").modal("hide");
    });
    function disableSubmitButton() {
        const submitButton = document.getElementById('submitButton');
        submitButton.disabled = true; // Disable the button
        submitButton.textContent = 'Processing...'; // Optional: Update button text
        document.getElementById('moneyRequestForm').submit(); // Submit the form
    }
</script>
@endpush
