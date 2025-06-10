<x-partner-layout :title="$pageTitle">
<style>
  td:hover {
    background-color: lightgray;
    cursor: pointer;
  }

  .modal-auto-width {
    max-width: 80%;
  }
</style>


<div class="modal fade" id="myModal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-auto-width">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Record Detail</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modalContent1">
        <!-- Content will be dynamically loaded here -->
      </div>
    </div>
  </div>
</div>




<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-auto-width">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Record Detail</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modalContent">
        <!-- Content will be dynamically loaded here -->
      </div>
    </div>
  </div>
</div>



<div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
  <form action="{{ route('partner.payment.report.all.search') }}" method="get">
    <div class="row justify-content-between align-items-center">
      <div class="col-md-3">
        <div class="form-group">
          <label>From Date</label>
          <input type="date" class="form-control" value="{{$from_date}}" name="from_date" id="datepicker" />
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          <label>To Date</label>
          <input type="date" class="form-control" value="{{$to_date}}" name="to_date" id="datepicker" />
        </div>
      </div>

      <div class="col-md-3">
        <div class="form-group">
          <label>E-Wallet</label>
          <select name="gateway" class="form-select">
            <option value="">All</option>
            @foreach($gateways as $gateway)
            <option value="{{ $gateway->name }}" @if(@request()->gateway == $gateway->name) selected @endif>{{ $gateway->name }}</option>
            @endforeach
          </select>
        </div>
      </div>


      <div class="col-md-3">
        <div class="form-group mt-2">
          <br>
          <button type="submit" class="btn waves-effect waves-light btn-primary"><i class="icon-base ti tabler-search me-1"></i> @lang('Search')</button>
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
    <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
    <div class="table-responsive">
      <table class="categories-show-table table table-striped table-bordered">
        <thead class="bg bg-primary text-white">
          <tr>
            <th></th>
            <th colspan="6" class="bg bg-primary text-white">Deposit</th>
            <th colspan="6" class="bg bg-success text-white">Withdrawal</th>
          </tr>
        </thead>

        <thead class="thead-dark">
          <tr>
            <th scope="col">@lang('Date')</th>
            <th scope="col">@lang('Pending (QTY)')</th>
            <th scope="col">@lang('Pending Amount')</th>
            <th scope="col">@lang('Approved (QTY)')</th>
            <th scope="col">@lang('Approved Amount')</th>
            <th scope="col">@lang('Total (QTY)')</th>
            <th scope="col">@lang('Total Amount')</th>

            <th scope="col">@lang('Pending (QTY)')</th>
            <th scope="col">@lang('Pending Amount')</th>
            <th scope="col">@lang('Approved (QTY)')</th>
            <th scope="col">@lang('Approved Amount')</th>
            <th scope="col">@lang('Total (QTY)')</th>
            <th scope="col">@lang('Total Amount')</th>
          </tr>
        </thead>
        <tbody>
          @forelse($data as $key => $value)
          <tr>
            <td> {{ isset($value['date']) ? $value['date'] : '' }}</td>

            <td onclick="openmodel1('{{ $value['date'] }}', '{{ $gateway }}', 'Pending')" class="bg bg-primary text-white"> {{ isset($value['payment_pending_count']) ? $value['payment_pending_count'] : '' }}</td>
            <td onclick="openmodel1('{{ $value['date'] }}', '{{ $gateway }}', 'Pending')" class="bg bg-primary text-white"> {{ isset($value['payment_pending_amount']) ? $value['payment_pending_amount'] : '' }}</td>
            <td onclick="openmodel1('{{ $value['date'] }}', '{{ $gateway }}', 'Approved')" class="bg bg-primary text-white"> {{ isset($value['payment_complete_count']) ? $value['payment_complete_count'] : '' }}</td>
            <td onclick="openmodel1('{{ $value['date'] }}', '{{ $gateway }}', 'Approved')" class="bg bg-primary text-white"> {{ isset($value['payment_complete_amount']) ? $value['payment_complete_amount'] : '' }}</td>
            <td onclick="openmodel1('{{ $value['date'] }}', '{{ $gateway }}', 'All')" class="bg bg-primary text-white"> {{ isset($value['payment_count']) ? $value['payment_count'] : '' }}</td>
            <td onclick="openmodel1('{{ $value['date'] }}', '{{ $gateway }}', 'All')" class="bg bg-primary text-white"> {{ isset($value['payment_total_amount']) ? $value['payment_total_amount'] : '' }}</td>

            <td onclick="openmodel('{{ $value['date'] }}', '{{ $gateway }}', 'Pending')" class="bg bg-success text-white"> {{ isset($value['payout_pending_count']) ? $value['payout_pending_count'] : '' }}</td>
            <td onclick="openmodel('{{ $value['date'] }}', '{{ $gateway }}', 'Pending')" class="bg bg-success text-white"> {{ isset($value['payout_pending_amount']) ? $value['payout_pending_amount'] : '' }}</td>
            <td onclick="openmodel('{{ $value['date'] }}', '{{ $gateway }}', 'Approved')" class="bg bg-success text-white"> {{ isset($value['payout_complete_count']) ? $value['payout_complete_count'] : '' }}</td>
            <td onclick="openmodel('{{ $value['date'] }}', '{{ $gateway }}', 'Approved')" class="bg bg-success text-white"> {{ isset($value['payout_complete_amount']) ? $value['payout_complete_amount'] : '' }}</td>
            <td onclick="openmodel('{{ $value['date'] }}', '{{ $gateway }}', 'All')" class="bg bg-success text-white"> {{ isset($value['payout_count']) ? $value['payout_count'] : '' }}</td>
            <td onclick="openmodel('{{ $value['date'] }}', '{{ $gateway }}', 'All')" class="bg bg-success text-white"> {{ isset($value['payout_total_amount']) ? $value['payout_total_amount'] : '' }}</td>
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
@php
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$domain = $_SERVER['HTTP_HOST'];
$path = dirname($_SERVER['PHP_SELF']);
$baseUrl = $protocol . '://' . $domain . $path .'/assets/uploads/receipts/';
@endphp


@push('js')
<script>
  function openmodel1(date, gateway, status) {

    $('#modalContent1').html('<div class="text-center p-4"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
    // Show modal immediately
    $('#myModal1').modal('show');

    const url = "{{ route('partner.payment.report.detail', ['date' => '__DATE__', 'gateway' => '__GATEWAY__', 'status' => '__STATUS__']) }}"
      .replace('__DATE__', encodeURIComponent(date))
      .replace('__GATEWAY__', encodeURIComponent(gateway))
      .replace('__STATUS__', encodeURIComponent(status));

    $.ajax({
      url: url,
      method: 'GET',
      success: function(response) {
        $('#modalContent1').empty();

        var table = $('<table class="table table-bordered"></table>');
        var thead = $('<thead class="thead-dark"><tr><th>Date</th><th>Trx Number</th><th>User Account</th><th>Method</th><th>Amount</th><th>Merchant Charge</th><th>Payable</th><th>E-Wallet No</th><th>Type</th><th>Status</th><th>Receipt</th></tr></thead>');
        var tbody = $('<tbody></tbody>');

        response.forEach(function(item) {
          var createdAt = new Date(item.created_at).toLocaleString('en-GB');
          var row = $('<tr></tr>');
          row.append('<td>' + createdAt + '</td>');
          row.append('<td>' + item.txn_id + '</td>');
          row.append('<td>' + item.sender + '</td>');
          row.append('<td>' + item.e_wallet_name + '</td>');
          row.append('<td>' + item.amount + '</td>');
          row.append('<td>' + item.charge + '</td>');
          row.append('<td>' + (item.amount - item.charge) + '</td>');
          row.append('<td>' + item.e_wallet_phone_number + '</td>');
          row.append('<td>' + item.e_wallet_type + '</td>');
          row.append('<td>' + item.status + '</td>');

          // Handle receipt image
          if (item.receipt) {
            row.append('<td><a href="' + '{{ $baseUrl }}' + item.receipt + '" data-fancybox="gallery" data-caption="Receipt"><img src="' + '{{ $baseUrl }}' + item.receipt + '" alt="Receipt" style="height:50px;"/></a></td>');
          } else {
            row.append('<td>—</td>');
          }

          tbody.append(row);
        });

        table.append(thead);
        table.append(tbody);
        $('#modalContent1').append(table);
        $('#myModal1').modal('show');
      },
      error: function(err) {
        $('#modalContent1').html('<p class="text-danger">Error loading data.</p>');
        $('#myModal1').modal('show');
      }
    });
  }

</script>

<script>
  function openmodel(date, gateway, status) {

    $('#modalContent').html('<div class="text-center p-4"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
    // Show modal immediately
    $('#myModal').modal('show');

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
        var thead = $('<thead class="thead-dark"><tr><th>Date</th><th>Trx Number</th><th>User Account</th><th>Method</th><th>Amount</th><th>Merchant Charge</th><th>Net Amount</th><th>Status</th><th>Sent From</th><th>Account Type</th></tr></thead>');
        var tbody = $('<tbody></tbody>');

        // Assuming response is an array
        for (var i = 0; i < response.length; i++) {
          var row = $('<tr></tr>');
          var createdAt = new Date(response[i].created_at).toLocaleString('en-GB', {
            day: 'numeric',
            month: 'numeric',
            year: 'numeric',
            hour: 'numeric',
            minute: 'numeric',
            hour12: true
          });
          row.append('<td>' + createdAt + '</td>');
          row.append('<td>' + response[i].txn_id + '</td>');
          row.append('<td>' + response[i].user_account_no + '</td>');
          row.append('<td>' + response[i].e_wallet_name + '</td>');
          row.append('<td>' + response[i].amount + 'TK</td>');
          row.append('<td>' + response[i].charge + 'TK</td>');
          row.append('<td>' + (response[i].amount + response[i].charge) + 'TK</td>');

          var statusBadge;
          if (response[i].transfer_status == 2) {
            statusBadge = '<span class="badge badge-light"><i class="fa fa-circle text-success success font-12"></i> Completed</span>';

          } else if (response[i].transfer_status == 1) {
            statusBadge = '<span class="badge badge-light"><i class="fa fa-circle text-warning success font-12"></i> Pending</span>';
          } else {
            statusBadge = '<span class="badge badge-light"><i class="fa fa-circle text-danger danger font-12"></i> Rejected</span>';
          }

          row.append('<td>' + statusBadge + '</td>');

          row.append('<td>' + response[i].e_wallet_phone_number + '</td>');
          row.append('<td>' + response[i].e_wallet_type + '</td>');

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
</x-partner-layout>
