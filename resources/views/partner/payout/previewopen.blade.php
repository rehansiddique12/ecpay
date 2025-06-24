@extends('partner.layouts.open')
@section('title', trans($title))

@section('content')
<div class="row">
    <div class="card col-md-3 ms-3">
        <div class="payment-info text-center">
            <ul class="list-group">
                <li class="list-group-item font-weight-bold bg-transparent">
                    <img src="{{getFile(config('location.withdraw.path').optional($withdraw->method)->image)}}" class="card-img-top w-50" alt="{{optional($withdraw->method)->name}}">
                </li>
                <li class="list-group-item bg-transparent">@lang('Request Amount') :
                    <span class="float-right text-success">{{@$basic->currency_symbol}}{{getAmount($withdraw->amount)}} </span>
                </li>
                <li class="list-group-item bg-transparent">@lang('Charge Amount') :
                    <span class="float-right text-danger">{{@$basic->currency_symbol}}{{getAmount($withdraw->charge)}} </span>
                </li>
                <li class="list-group-item bg-transparent">@lang('Total Payable') :
                    <span class="float-right text-danger">{{@$basic->currency_symbol}}{{getAmount($withdraw->net_amount)}} </span>
                </li>
                <li class="list-group-item bg-transparent">@lang('Available Balance') :
                    <span class="float-right text-success">{{@$basic->currency_symbol}}{{$remaining}} </span>
                </li>
            </ul>
        </div>

    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header custom-header text-center">
                <h5 class="card-title">@lang('Additional Information To Withdraw Confirm')</h5>
            </div>
            <div class="card-body">

                <form action="" method="post" enctype="multipart/form-data" class="form-row text-left preview-form" id="withdrawForm">
                    @csrf
                    <div class="col-md-12">
                        <label><strong>Phone Number                                 <span class="text-danger">*</span>
                                </strong></label>
                        <div class="form-group input-box  mt-2">
                            <input type="text" name="PhoneNumber" class="form-control" required="">
                                                    </div>
                    </div>
                    
                    <div class="col-md-12 mt-4">
                        <div class=" form-group">
                            <button type="submit" class="btn btn-success" id="submitButton" onclick="disableSubmitButton()">
                                <span>@lang('Confirm Now')</span>
                            </button>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('css-lib')
<link rel="stylesheet" href="{{asset($themeTrue.'css/bootstrap-fileinput.css')}}">
@endpush

@push('extra-js')
<script src="{{asset($themeTrue.'js/bootstrap-fileinput.js')}}"></script>
@endpush

<script>
        function disableSubmitButton() {
        const submitButton = document.getElementById('submitButton');
        submitButton.disabled = true; // Disable the button
        submitButton.innerHTML = 'Processing...'; // Change button text to "Processing..."
        document.getElementById('withdrawForm').submit(); // Submit the form
    }
</script>

@push('script')

@endpush
