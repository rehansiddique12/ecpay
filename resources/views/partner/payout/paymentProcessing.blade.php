@extends('partner.layouts.app')
@section('title')
@lang('Add Fund')
@endsection
@section('content')

<center>
    <div class="">
        @if($processing == 0)
        <div class="alert bg-white" style="width:500px">

            <br>
            <br>
            <form method="post" action="{{route('partner.addFund.checkProcessPayment')}}" enctype="multipart/form-data">
                @csrf
                <div class="">
                    <div class="">
                        <h3>Upload Transaction Receipt</h3>
                        <div class="image-input ">
                            <label for="image-upload" id="image-label"><i class="fas fa-upload"></i></label>
                            <input type="file" name="image" placeholder="@lang('Choose image')" id="image">
                            <img id="image_preview_container" class="preview-image" src="{{ getFile(config('location.withdraw.path'))}}" alt="preview image">
                        </div>
                        @error('image')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <button type="submit" class="btn  btn-primary mt-3">@lang('Submit')</button>
                <a href="{{ route('partner.fund-history') }}" class="btn btn-success text-white mt-3">Skip</a>
            </form>
        </div>
        @endif

        <br>
        <br>
        <br>
        <br>

        @if($processing == 1 )

        <div class="" style="width:500px" id="processing_div">
            <div class="card">
                <img class="img-fluid" src="{{ getFile(config('location.public_images.path').'processing.gif')}}" />
                <h3>PLEASE WAIT WHILE WE ARE PROCESSING YOUR DEPOSIT.<br> THANK YOU!</h3>
            </div>
        </div>
        @endif

        <div style="width:500px;display:none;" id="complete_div">
            <img class="img-fluid" src="{{ getFile(config('location.public_images.path').'Complete.gif')}}" />
            <h3>TRANSACTION SUCCESSFUL!</h3>
        </div>

    </div>
</center>

@endsection

@push('script')
<script>
    "use strict";
    $(document).ready(function(e) {

        var process_status = "{{ $processing }}";

        if (process_status == 1) {
            checkStatus();
        }

        $('#image').change(function() {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#image_preview_container').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        });


    });

    $(document).ready(function() {
        $('select').select2({
            selectOnClose: true
        });
    });

    function checkStatus() {
        $.ajax({
            url: "{{ route('partner.update_fund_order_status') }}",
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                // console.log(response);
                if (response.status === 'success') {
                    // Handle success condition (e.g., update the UI).

                    $('#processing_div').hide();
                    $('#complete_div').show();

                    // Redirect to the fund history page
                    setTimeout(function() {
                        window.location.href = '{{ route("partner.fund-history") }}';
                    }, 1500);
                    console.log('Status is success.');
                } else {
                    // If status is not 'success', set a timeout to retry after 10 seconds.
                    setTimeout(checkStatus, 2000);
                }
            },
            error: function() {
                // Handle AJAX request error if needed.
                console.error('Error occurred during the AJAX request.');
            }
        });
    }
</script>
@endpush