@extends('partner.layouts.open')
@section('title')
@lang('Select Method')
@endsection
@section('content')

<center>
    <div class="" style="margin-top:10%">
        <div class="row">
            <div class="col-md-6">
                <a href="deposit">
                    <div>
                        <!-- <i class="fas fa-hand-holding-usd" style="font-size:200px;color:red"></i> -->
                        <p style="font-size:80px;color:red"><span class="badge badge-success">Deposit</span></p>
                    </div>
                </a>
            </div>
            <div class="col-md-6">
                <a href="withdrawal">
                    <div>
                        <!-- <i class="fas fa-credit-card" style="font-size:200px;color:blue"></i> -->
                        <p style="font-size:80px;"><span class="badge badge-primary">Withdrawal</span></p>
                    </div>
                </a>
            </div>
        </div>

    </div>
</center>

@endsection

@push('script')
@endpush