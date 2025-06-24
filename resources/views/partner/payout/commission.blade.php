@extends('partner.layouts.app')
@section('title')
@lang($page_title)
@endsection
@section('content')

@php
$key = 0;
@endphp
<div class="row">
  <div class="col-md-12">
    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
      <form action="{{ route('partner.apis.commission.add') }}" method="post">
        @csrf
        <div class="">
          <input type="text" hidden value="{{$api_id}}" class="form-control" name="api_id" required>

          @if(count($commissions)>0)
          @foreach($commissions as $key => $commission)

          <div id="row-p{{$key}}">
            <br>
            <div style='border:1px solid;padding:20px'>
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="pr-3">From Amount</label>
                    <input type="text" value="{{$commission->id}}" name="c_id[]" hidden>
                    <input type="number" readonly value="{{$commission->from_amount}}" class="form-control" name="from_amount[]" / required>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-group">
                    <label class="pr-3">To Amount</label>
                    <input type="number" readonly value="{{$commission->to_amount}}" class="form-control" id="to_amount_{{$key}}" name="to_amount[]" / required>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="pr-3">Deposit Percentage</label>
                    <div class="input-group">
                      <input type="number" step="0.01" id="deposit_percentage{{$key+1}}" onchange="calculateProfit({{$key+1}},{{$commission->parent->deposit_percentage}})" value="{{$commission->deposit_percentage}}" min="{{$commission->parent->deposit_percentage}}" class="form-control" name="deposit_percentage[]" / required>
                      <div class="input-group-append">
                        <span class="input-group-text">%</span>
                      </div>
                    </div>
                    <p class="mt-2 mb-1 text-danger">Company Profit: {{$commission->parent->deposit_percentage}}%</p>
                    <p class="text-success">Your Profit: <span id="your_profit{{$key+1}}">{{$commission->deposit_percentage-$commission->parent->deposit_percentage}}%</span></p>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="pr-3">Withdrawal Percentage</label>
                    <div class="input-group">
                      <input type="number" step="0.01" id="withdrawal_percentage{{$key+1}}" onchange="calculateProfitw({{$key+1}},{{$commission->parent->withdrawal_percentage}})" value="{{$commission->withdrawal_percentage}}" min="{{$commission->parent->withdrawal_percentage}}" class="form-control" name="withdrawal_percentage[]" / required>
                      <div class="input-group-append">
                        <span class="input-group-text">%</span>
                      </div>
                    </div>
                    <p class="mt-2 mb-1 text-danger">Company Profit: {{$commission->parent->withdrawal_percentage}}%</p>
                    <p class="text-success">Your Profit: <span id="your_profitw{{$key+1}}">{{$commission->withdrawal_percentage-$commission->parent->withdrawal_percentage}}%</span></p>
                  </div>
                </div>
              </div>

            </div>
          </div>

          @endforeach
          @endif
          <br>
          <div class="col-md-12">
            <div class="form-group">
              <button type="submit" class="btn waves-effect waves-light btn-primary">Submit</button>
            </div>
          </div>
        </div>
      </form>

    </div>
  </div>
</div>







@endsection
@push('js')
<script>
  function calculateProfit(index, deposit_percentage) {
    var inputValue = parseFloat($('#deposit_percentage' + index).val());
    var profitDifference = (inputValue - deposit_percentage).toFixed(2);
    $('#your_profit' + index).text(profitDifference);
  }
</script>

<script>
  function calculateProfitw(index, withdrawal_percentage) {
    var inputValue = parseFloat($('#withdrawal_percentage' + index).val());
    var profitDifference = (inputValue - withdrawal_percentage).toFixed(2);
    $('#your_profitw' + index).text(profitDifference);
  }
</script>

@endpush