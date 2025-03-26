<x-admin-layout :title="$pageTitle">
@php
$key = 0;
@endphp
<div class="row">
    <div class="col-md-12">
        <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('admin.accounts.charges.add') }}" method="post">
            @csrf
            <div class="">
            <input type="text" hidden value="{{$account_id}}" class="form-control" name="account_id" required />
                <div class="row">
                    <div class="col-md-3">
                   <label class="pr-3">Free Transections Qty</label>
                </div>
                <div class="col-md-4">
                    <div class="form-group">

                            <input type="number" value="{{$free_transections_day}}" class="form-control"  name="free_transections_day"/ required>
                    </div>
                </div>
                </div>
            <hr>

                @if(count($commissions)>0)
               @foreach($commissions as $key => $commission)
               <div id="row-p{{$key}}">
                <br>
               <div style='border:1px solid;padding:20px'>
               <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="pr-3">From Amount</label>
                            <input type="number" readonly value="{{$commission->from_amount}}" class="form-control" name="from_amount[]"/ required>
                    </div>
                </div>

                 <div class="col-md-2">
                    <div class="form-group">
                        <label class="pr-3">To Amount</label>
                            <input type="number" value="{{$commission->to_amount}}" class="form-control" id="to_amount_{{$key}}" name="to_amount[]"/ required>
                    </div>
                </div>
                <div class="col-md-2">
                        <div class="form-group">
                            <label class="pr-3">Charges</label>
                            <div class="input-group">
                                <input type="number" step="0.01" value="{{$commission->charges}}" class="form-control" name="charges[]" required>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                        <label class="pr-3">Charges Type</label>
                            <select class="form-control" name="charges_type[]">
                                    <option {{$commission->charges_type==1?'selected':''}} value="1">Amount</option>
                                    <option {{$commission->charges_type==2?'selected':''}} value="2">Percentage</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                    <div class="form-group">
                     <label class="pr-3">Withdrawal Charges</label>
                    <div class="input-group">
                        <input type="number" value="{{$commission->wcharges}}" step="0.01" class="form-control" name="wcharges[]" required>
                      </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                     <label class="pr-3">Charges Type</label>
                        <select class="form-control" name="wcharges_type[]">
                                <option {{$commission->wcharges_type==1?'selected':''}} value="1">Amount</option>
                                <option {{$commission->wcharges_type==1?'selected':''}} value="2">Percentage</option>
                            </select>
                    </div>
                </div>

                <div class="col-md-2 mt-4">
                @if($key>0)
                 <div class="row">
                    <div class='col-1' style='padding-left:13px;'>
                      <button type='button' class='cancel-row btn btn-danger mt-1' data-row='p{{$key}}'>Cancel</button>
                    </div>
                    </div>
                 @endif
                 </div>





                </div>

                 </div>
                 </div>

               @endforeach
               @else
               <div style='border:1px solid;padding:20px'>
               <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="pr-3">From Amount</label>
                            <input type="number" readonly value="0" class="form-control" name="from_amount[]"/ required>
                    </div>
                </div>

                 <div class="col-md-2">
                    <div class="form-group">
                        <label class="pr-3">To Amount</label>
                            <input type="number" class="form-control" id="to_amount_0" name="to_amount[]"/ required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                     <label class="pr-2">Deposit Charges</label>
                    <div class="input-group">
                        <input type="number" step="0.01" class="form-control" name="charges[]"/ required>
                      </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                     <label class="pr-3">Deposit Charges Type</label>
                        <select class="form-control" name="charges_type[]">
                                <option value="1">Amount</option>
                                <option value="2">Percentage</option>
                            </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                     <label class="pr-3">Withdrawal Charges</label>
                    <div class="input-group">
                        <input type="number" step="0.01" class="form-control" name="wcharges[]"/ required>
                      </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                     <label class="pr-3">Charges Type</label>
                        <select class="form-control" name="wcharges_type[]">
                                <option value="1">Amount</option>
                                <option value="2">Percentage</option>
                            </select>
                    </div>
                </div>






                </div>
                 </div>
                 @endif


               <div id="add-row"></div>

                <div class="col-md-12 mb-5 mt-2">
                        <button type="button" class="duplicate-row btn btn-success add-more">Add More</button>
                    </div>



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








@push('js')
<script>
$(document).ready(function() {
  var rowCounter = '<?php echo $key?>';

  $('.duplicate-row').click(function() {



    var fromAmountValue = $("[id=to_amount_"+rowCounter+"]").val();



    rowCounter++;



    // Create the new row HTML

    var newRow = `
      <div id="row-${rowCounter}">
      <br>
      <div style='border:1px solid;padding:20px'>
      <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label class="pr-3">From Amount</label>
                    <input type="number" readonly value="${fromAmountValue}" class="form-control" name="from_amount[]"/ required>
            </div>
        </div>

         <div class="col-md-2">
            <div class="form-group">
                <label class="pr-3">To Amount</label>
                    <input type="number" class="form-control" id="to_amount_${rowCounter}" name="to_amount[]"/ required>
            </div>
        </div>
       <div class="col-md-2">
                    <div class="form-group">
                     <label class="pr-3">Charges</label>
                    <div class="input-group">
                        <input type="number" step="0.01" class="form-control" name="charges[]"/ required>
                      </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                     <label class="pr-3">Charges Type</label>
                        <select class="form-control" name="charges_type[]">
                                <option value="1">Amount</option>
                                <option value="2">Percentage</option>
                            </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                     <label class="pr-3">Withdrawal Charges</label>
                    <div class="input-group">
                        <input type="number" step="0.01" class="form-control" name="wcharges[]"/ required>
                      </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                     <label class="pr-3">Charges Type</label>
                        <select class="form-control" name="wcharges_type[]">
                                <option value="1">Amount</option>
                                <option value="2">Percentage</option>
                            </select>
                    </div>
                </div>

                <div class="col-md-2 mt-4">
                <div class="row">
        <div class='col-1' style='padding-left:13px;'>
          <button type='button' class='cancel-row btn btn-danger mt-1' data-row='${rowCounter}'>Cancel</button>
        </div>
        </div>
                </div>







</div>


        <div style='clear:both;'></div>
      </div>
      </div>
    `;


    // Append the new row to the add-row container
    $('#add-row').append(newRow);
  });

  // Cancel Row button click event
  $(document).on('click', '.cancel-row', function() {
    var row = $(this).data('row');

    // Remove the corresponding row
    $('#row-' + row).remove();
  });
});
</script>
@endpush

</x-admin-layout>
