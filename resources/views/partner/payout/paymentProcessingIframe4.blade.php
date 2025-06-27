<x-iframe-layout>
<style>


body {
    color: black;
    background: var(--bgLight);
    font-weight: bold;
}


</style>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="payment-container p-3">       

        {{-- display:none; --}}
        

        @if($processing==2)
        <div style="max-width:400px;margin:auto;" id="complete_div">
            <img class="img-fluid" src="{{ asset('assets/images/complete.gif') }}" />
            <div class="col-md-10 mx-auto text-center">
                <h5>Your transaction has been successfully completed. Your money will be credited to your account now.<br>
                    Thank you!
                </h5>
                <h5>আপনার লেনদেন সফলভাবে সম্পন্ন হয়েছে। আপনার টাকা এখন আপনার অ্যাকাউন্টে জমা হবে।.<br>
                    ধন্যবাদ!
                </h5>
            </div>
        </div>
        @endif

        @if($processing==1)
        <div style="max-width:400px;margin:auto;" id="pre_complete_div">
            <img class="img-fluid" src="{{ asset('assets/images/complete.gif') }}" />
            <div class="col-md-10 mx-auto text-center">
                <h5>Your payment request has been received successfully, and the transaction will be processed within 5 minutes.<br>
                    Thank you!
                </h5>
                <h5>আপনার পেমেন্ট অনুরোধ সফলভাবে গ্রহণ করা হয়েছে এবং লেনদেন ৫ মিনিটের মধ্যে প্রক্রিয়া করা হবে।.<br>
                    ধন্যবাদ!
                </h5>
            </div>
        </div>
        @endif

        



    </div>
</div>

@push('script')

@endpush

</x-partner-layout>
