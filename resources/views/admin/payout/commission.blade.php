<x-admin-layout :title="$pageTitle">
    @push('styles')
    <script src="{{ asset('assets/css/select2.min.css')}}"></script>
    <style>
    tr th {
        color: white !important
    }
    </style>
    @endpush
    @php
    $key = 0;
    @endphp
    <div class="row">
        <div class="col-md-12">
            <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
                <h3 style="color: #7367f0">{{$pageTitle}}</h3>
                <form action="{{ route('admin.apis.commission.add') }}" method="post">
                    @csrf
                    <div class="">

                        @if(count($commissions)>0)
                        @foreach($commissions as $key => $commission)
                        <div id="row-p{{$key}}">
                            <br>
                            <div style='border:1px solid;padding:20px'>
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="pr-3">From Amount</label>
                                            <input type="text" hidden value="{{$commission->id}}" class="form-control"
                                                name="id[]" />
                                            <input type="text" hidden value="{{$id}}" class="form-control"
                                                name="category_id" />

                                            <input type="number" readonly value="{{$commission->from_amount}}"
                                                class="form-control" name="from_amount[]" required />
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="pr-3">To Amount</label>
                                            <input type="number" value="{{$commission->to_amount}}" class="form-control"
                                                id="to_amount_{{$key}}" name="to_amount[]" required />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="pr-3">Deposit Percentage</label>
                                            <div class="input-group">
                                                <input type="number" step="0.01"
                                                    value="{{$commission->deposit_percentage}}" class="form-control"
                                                    name="deposit_percentage[]" required />
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="pr-3">Withdrawal Percentage</label>
                                            <div class="input-group">
                                                <input type="number" step="0.01"
                                                    value="{{$commission->withdrawal_percentage}}" class="form-control"
                                                    name="withdrawal_percentage[]" required />
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="pr-3">Settlement Percentage</label>
                                            <div class="input-group">
                                                <input type="number" step="0.01"
                                                    value="{{$commission->settlement_percentage}}" class="form-control"
                                                    name="settlement_percentage[]" required />
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 mt-4">
                                        @if($key>0)
                                        <div class="row">
                                            <div class='col-1' style='padding-left:13px;'>
                                                <button type='button' class='cancel-row btn btn-danger mt-1'
                                                    data-row='p{{$key}}'>Cancel</button>
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
                                        <input type="text" hidden class="form-control" name="id[]" />
                                        <input type="number" readonly value="0" class="form-control"
                                            name="from_amount[]" / required>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="pr-3">To Amount</label>
                                        <input type="number" class="form-control" id="to_amount_0" name="to_amount[]" /
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="pr-3">Deposit Percentage</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control"
                                                name="deposit_percentage[]" / required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="pr-3">Withdrawal Percentage</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control"
                                                name="withdrawal_percentage[]" / required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="pr-3">Settlement Percentage</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control"
                                                name="settlement_percentage[]" / required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Type Dropdown -->
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="pr-3">Type</label>
                                        <div class="input-group">
                                            <select class="form-control " name="type" required>
                                                <option value="">Select Type</option>
                                                <option value="agent">Agent</option>
                                                <option value="personal">Personal</option>

                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Gateway Dropdown -->
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="pr-3">Gateway</label>
                                        <div class="input-group">
                                            <select class="form-control select2" name="settlement_gateway[]" required>
                                                <option value="">Select Gateway</option>
                                                @foreach($gateways as $gateway)
                                                <option value="{{ $gateway->id}}">{{ $gateway->source_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
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



                <hr>
                @if(count($cron_commissions)>0)
                <h3 style="color: #7367f0">Pending to Update</h3>
                @foreach($cron_commissions as $key => $commission)
                <div id="row-p{{$key}}">
                    <br>
                    <div style='border:1px solid;padding:20px'>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="pr-3">From Amount</label>
                                    <input type="text" hidden value="{{$commission->id}}" class="form-control"
                                        name="id[]" />
                                    <input type="number" readonly value="{{$commission->from_amount}}"
                                        class="form-control" required />
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="pr-3">To Amount</label>
                                    <input type="number" value="{{$commission->to_amount}}" class="form-control"
                                        required />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="pr-3">Deposit Percentage</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" value="{{$commission->deposit_percentage}}"
                                            class="form-control" required />
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="pr-3">Withdrawal Percentage</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" value="{{$commission->withdrawal_percentage}}"
                                            class="form-control" required />
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="pr-3">Settlement Percentage</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" value="{{$commission->settlement_percentage}}"
                                            class="form-control" required />
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 mt-4">
                            </div>





                        </div>

                    </div>
                </div>

                @endforeach
                @endif


            </div>
        </div>
    </div>

    @push('js')
    <script>
    $(document).ready(function() {
        var rowCounter = '<?php echo $key?>';

        $('.duplicate-row').click(function() {



            var fromAmountValue = $("[id=to_amount_" + rowCounter + "]").val();



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
                    <input type="text" hidden class="form-control" name="id[]" />
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
                     <label class="pr-3">Deposit Percentage</label>
                    <div class="input-group">
                        <input type="number" step="0.01" class="form-control" name="deposit_percentage[]"/ required>
                        <div class="input-group-append">
                          <span class="input-group-text">%</span>
                        </div>
                      </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                     <label class="pr-3">Withdrawal Percentage</label>
                    <div class="input-group">
                        <input type="number" step="0.01" class="form-control" name="withdrawal_percentage[]"/ required>
                        <div class="input-group-append">
                          <span class="input-group-text">%</span>
                        </div>
                      </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                     <label class="pr-3">Settlement Percentage</label>
                    <div class="input-group">
                        <input type="number" step="0.01" class="form-control" name="settlement_percentage[]"/ required>
                        <div class="input-group-append">
                          <span class="input-group-text">%</span>
                        </div>
                      </div>
                    </div>
                </div>
                 <div class="col-md-2">
                    <div class="form-group">
                     <label class="pr-3">Type</label>
                    <div class="input-group">
                       <select class="form-control " name="type" required>
                <option value="">Select Type</option>
                    <option value="agent">Agent</option>
                    <option value="personal">Personal</option>

            </select>
                      </div>
                    </div>
                </div>
                 <div class="col-md-2">
                    <div class="form-group">
                     <label class="pr-3">Gateway</label>
                    <div class="input-group">
                        <select class="form-control select2" name="settlement_gateway[]" required>
                <option value="">Select Gateway</option>
                @foreach($gateways as $gateway)
                    <option value="{{ $gateway->id}}">{{ $gateway->source_name}}</option>
                @endforeach
            </select>
                      </div>
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
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- <script src="{{ asset('assets/js/select2.min.js')}}"></script> -->
    <script>
    $(document).ready(function() {
            $('.select').select2();

    });
    </script>

    @endpush
</x-admin-layout>