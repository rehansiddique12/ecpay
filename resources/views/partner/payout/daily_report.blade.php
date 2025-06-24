@extends('partner.layouts.app')
@section('title')
    @lang($page_title)
@endsection
@section('content')
<style>
    td:hover{
        background-color:lightgray;
        cursor: pointer;
    }
    .modal-auto-width {
    max-width: 80%;
  }
</style>


<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-auto-width" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Record Detail</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="modalContent">
        <!-- Content will be dynamically loaded here -->
      </div>
    </div>
  </div>
</div>


    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('partner.payout.report.daily.search') }}" method="get">
            <div class="row justify-content-between align-items-center">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>From Date</label>
                        <input type="date" class="form-control" value="{{$from_date}}" name="from_date" id="datepicker"/>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>To Date</label>
                        <input type="date" class="form-control" value="{{$to_date}}" name="to_date" id="datepicker"/>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Bank</label>
                        <select name="gateway" class="form-control">
                            <option value="">All</option>
                            @foreach($gateways as $gateway)
                                <option value="{{ $gateway->name }}"
                                @if(@request()->gateway == $gateway->name) selected @endif>{{ $gateway->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

               
                <div class="col-md-3">
                    <div class="form-group mt-2">
                        <br>
                        <button type="submit" class="btn waves-effect waves-light btn-primary"><i
                                class="fas fa-search"></i> @lang('Search')</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    @php
$gateway = "All";
if(!empty(@request()->gateway)){
$gateway = @request()->gateway;
}
@endphp

<!-- Add these lines to your HTML header section -->
<link rel="stylesheet" href="{{ asset('node_modules/@fancyapps/fancybox/dist/jquery.fancybox.min.css') }}">
<script src="{{ asset('node_modules/@fancyapps/fancybox/dist/jquery.fancybox.min.js') }}"></script>


    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">

            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">@lang('Date')</th>
                        
                        <th scope="col">@lang('Pending (QTY)')</th>
                        <th scope="col">@lang('Pending Amount')</th>
                        <th scope="col">@lang('Approved (QTY)')</th>
                        <th scope="col">@lang('Approved Amount')</th>
                        <th scope="col">@lang('Total (QTY)')</th>
                        <th scope="col">@lang('Total Amount')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($payoutsByDate as $key => $payout)
                        <tr>
                            <td onclick="openmodel('{{ $payout->payout_date }}', '{{ $gateway }}', 'All')"> {{ $payout->payout_date }}</td>
                            <td onclick="openmodel('{{ $payout->payout_date }}', '{{ $gateway }}', 'Pending')"> {{ $payout->pending_count }}</td>
                            <td onclick="openmodel('{{ $payout->payout_date }}', '{{ $gateway }}', 'Pending')"> {{ getAmount($payout->pending_amount,2) }}</td>
                            <td onclick="openmodel('{{ $payout->payout_date }}', '{{ $gateway }}', 'Approved')"> {{ $payout->complete_count }}</td>
                            <td onclick="openmodel('{{ $payout->payout_date }}', '{{ $gateway }}', 'Approved')"> {{ getAmount($payout->complete_amount,2) }}</td>
                            <td onclick="openmodel('{{ $payout->payout_date }}', '{{ $gateway }}', 'All')"> {{ $payout->payout_count }}</td>
                            <td onclick="openmodel('{{ $payout->payout_date }}', '{{ $gateway }}', 'All')"> {{ getAmount($payout->total_amount,2) }}</td>

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
@endsection

@push('js')
<script>

function openmodel(date, gateway, status) {

  // Ajax request to fetch data
  $.ajax({
   url: "{{ route('partner.payout.report.detail', ['date' => 'placeholder', 'gateway' => 'placeholder', 'status' => 'placeholder']) }}"
         .replace('placeholder', date)
         .replace('placeholder', gateway)
         .replace('placeholder', status),
    method: 'GET',
    success: function(response) {
      console.log(response);
      $('#modalContent').empty();

      // Iterate over the response data and append it to the modal body in a table format
      var table = $('<table class="table"></table>');
        var thead = $('<thead class="thead-dark"><tr><th>Date</th><th>Trx Number</th><th>Method</th><th>Amount</th><th>Merchant Charge</th><th>Payable</th><th>Bank Acc No</th><th>Status</th></tr></thead>');
      var tbody = $('<tbody></tbody>');

      // Assuming response is an array
      let totalSum = 0;
      for (var i = 0; i < response.length; i++) {
        let rowTotal = Number(response[i].amount) + Number(response[i].charge);
        var row = $('<tr></tr>');
        var createdAt = new Date(response[i].created_at).toLocaleString('en-GB', { day: 'numeric', month: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric', hour12: true });
        row.append('<td>' + createdAt + '</td>');
        row.append('<td>' + response[i].transection_no + '</td>');
        row.append('<td>' + response[i].domain + '</td>');
        row.append('<td>' + parseFloat(response[i].amount).toFixed(2) + ' {{trans($basic->currency_symbol)}}</td>');
        row.append('<td>' + parseFloat(response[i].charge).toFixed(2) + ' {{trans($basic->currency_symbol)}}</td>');
        row.append('<td>' + parseFloat(rowTotal).toFixed(2) + ' {{trans($basic->currency_symbol)}}</td>');
        row.append('<td>' + response[i].acc_no + '</td>');
        
        var statusBadge;
        if (response[i].status == 2) {
            statusBadge = '<span class="badge badge-light"><i class="fa fa-circle text-success success font-12"></i> Completed</span>';
            
        } else if (response[i].status == 1) {
            statusBadge = '<span class="badge badge-light"><i class="fa fa-circle text-warning success font-12"></i> Pending</span>';
        } else {
           statusBadge = '<span class="badge badge-light"><i class="fa fa-circle text-danger danger font-12"></i> Rejected</span>'; 
        }
    
        row.append('<td>' + statusBadge + '</td>');

        tbody.append(row);
      }
        
        table.append(thead);
      table.append(tbody);
      $('#modalContent').append(table);

      // Show the modal
      $('#myModal').modal('show');
    },
    error: function(error) {
      console.error('Error fetching data:', error);
    }
  });
}
</script>
@endpush
