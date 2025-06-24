@extends('partner.layouts.iframe')
@section('content')
@endsection
<style>
    .payment-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        max-width: 300px;
        max-width: 500px;
        margin: auto;
        position: relative;
        /* padding: 20px; */
        /* Added padding */
    }

    .header-logo {
        display: block;
        margin: 0 auto;
        max-width: 100%;
        height: auto;
    }

    .bkash-time {
        position: absolute;
        top: 0px;
        right: 0px;
        background-color: #C31C57;
        color: white;
        padding: 5px 20px;
        border-radius: 5px;
        font-size: 14px;
    }

    .nagad-time {
        position: absolute;
        top: 0px;
        right: 0px;
        background-color: #FF9600;
        color: white;
        padding: 5px 20px;
        border-radius: 4px;
        font-size: 14px;
    }

    .rocket-time {
        position: absolute;
        top: 0px;
        right: 0px;
        background-color: #8F2A85;
        color: white;
        padding: 5px 20px;
        border-radius: 4px;
        font-size: 14px;
    }
</style>

@php
    if ($ewallet == 'bkash') {
        $time_class = 'bkash-time';
        $color = '#8F2A85';
    }
    if ($ewallet == 'nagad') {
        $time_class = 'nagad-time';
        $color = '#FF9600';
    }
    if ($ewallet == 'rocket') {
        $time_class = 'rocket-time';
        $color = '#8F2A85';
    }
@endphp

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="payment-container">
        <span id="timer" class="{{ $time_class }}">00:00</span>
        <div class="p-2">
            <img src="{{ $logo }}" alt="Logo" class="header-logo">
        </div>
        <div class="p-0 m-0">
            <img height="5" width="100%" src="{{ $banner }}" alt="Logo">
        </div>

        @if ($processing == 1)
        <div id="intime">
            <div class="" style="max-width:400px" id="processing_div">
                <div class="text-center">
                    <img class="img-fluid" src="{{ asset('assets/images/processing.gif') }}" />
                    <h5>Please wait while we check your payment...</h5>
                    <h5>আমরা আপনার পেমেন্ট চেক করা পর্যন্ত অপেক্ষা করুন...</h5>
                </div>
            </div>
            </div>
            <div id="outtime" style="display: none;">
                    <div class="text-center">
                    <img class="img-fluid" src="{{ asset('assets/images/error_transparent.gif') }}" />
                    <h5>Sorry. We are not able to 
process your payment. 
Please contact customer 
service for assistance.
                </h5>
                <h5>দুঃখিত। আমরা আপনার পেমেন্ট প্রক্রিয়া করতে সক্ষম নই। সহায়তার জন্য গ্রাহক পরিষেবার সাথে যোগাযোগ করুন।
                </h5>
                    
                </div>
                </div>
        @endif

        @if ($processing == 2)
            <div class="" style="max-width:400px" id="error_div">
                <div class="text-center">
                    <img class="img-fluid" src="{{ asset('assets/images/error_transparent.gif') }}" />
                    <h5>Sorry. We are not able to 
process your payment. 
Please contact customer 
service for assistance.
                </h5>
                <h5>দুঃখিত। আমরা আপনার পেমেন্ট প্রক্রিয়া করতে সক্ষম নই। সহায়তার জন্য গ্রাহক পরিষেবার সাথে যোগাযোগ করুন।
                </h5>
                    {{-- <h5>{{$message}}</h5> --}}
                    
                </div>
            </div>
        @endif

        <div style="max-width:400px;display:none;" id="complete_div">
            <img class="img-fluid" src="{{ asset('assets/images/complete.gif') }}" />
            <div class="col-md-10 mx-auto text-center">
                <h5>Your payment has been successfully received.<br>
                    Thank you!
                </h5>
                <h5>আপনার পেমেন্ট সফলভাবে গৃহীত হয়েছে.
                    ধন্যবাদ!
                </h5>
            </div>
        </div>

        <div class="p-0 m-0">
            <img height="5" width="100%" src="{{ $banner }}" alt="Logo">
        </div>

    </div>
</div>

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

        let timerInterval;
        let timerRunning = true;

        function checkStatus() {
            $.ajax({
                url: "{{ route('partner.update_fund_order_status.iframe', ['id' => $id]) }}",
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    if (response.status === 'success') {
                        // Handle success condition (e.g., update the UI).
                        console.log(response);
                        $('#processing_div').hide();
                        $('#error_div').hide();
                        $('#complete_div').show();
                        console.log('Status is success.');

                        timerRunning = false;
                        clearInterval(timerInterval);
                        // $('#intime').hide();
                        $('#timer').hide();
                        $('#outtime').hide();
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
    <script>
        $(document).ready(function() {
        let remainingTime = @json($remainingTime);
        let processing = @json($processing);

        const timerElement = $('#timer');
            const intime = $('#intime');
            const outtime = $('#outtime');

        if(processing!==1){
           
                    timerElement.hide();
                    clearInterval(timerInterval);
        }else if (remainingTime > 0) {
            

            const updateTimer = () => {
                if (remainingTime > 0 && timerRunning) {
                    const minutes = Math.floor(remainingTime / 60);
                    const seconds = remainingTime % 60;
                    timerElement.text(`${minutes}:${seconds < 10 ? '0' : ''}${seconds}`);
                    remainingTime--;
                } else if (remainingTime <= 0) {
                    intime.hide();
                    timerElement.hide();
                    outtime.show();
                    clearInterval(timerInterval);
                }
            };

            timerInterval = setInterval(updateTimer, 1000);
            updateTimer();  // Initial call to display the timer immediately
        } else {
            $('#timer').hide();
            $('#expired').show();
        }

        checkStatus(); // Initial call to check status immediately
    });
    </script>
@endpush
