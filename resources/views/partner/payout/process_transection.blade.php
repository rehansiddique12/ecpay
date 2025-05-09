<x-iframe-layout>
<style>
    /* * {
    margin: 0;
    padding: 0;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
} */

body {
    font-family: "Rajdhani", sans-serif;
    color: black;
    background: var(--bgLight);
    font-weight: 500;
    font-size: 15px;
}

.text-left {
    text-align: left !important;

}

h1, h2, h3, h4, h5 {
    font-family: "Rajdhani", sans-serif;
    font-weight: 700;
    color: black;
    margin-bottom: 15px;
}
</style>
    <style>
        .payment-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            min-width: 300px;
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

        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .instruction {
            margin-top: 20px;
        }

        .input-group {
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .note {
            font-size: 12px;
            tet color: #777;
        }

        .bkash-complete-btn {
            background-color: #C31C57;
            color: white;
            font-weight: bold;
            padding: 5px 5px;
            border: none;
            border-radius: 4px;
            display: block;
            text-align: center;
            outline: 2px solid white;
        }

        .nagad-complete-btn {
            background-color: #FF9600;
            color: white;
            font-weight: bold;
            padding: 5px 5px;
            border: none;
            border-radius: 4px;
            display: block;
            text-align: center;
            outline: 2px solid white;
        }

        .rocket-complete-btn {
            background-color: #8F2A85;
            color: white;
            font-weight: bold;
            padding: 5px 5px;
            border: none;
            border-radius: 4px;
            display: block;
            text-align: center;
            outline: 2px solid white;
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

        .bkash-responsive-row {
            /* height: 100px; */
            background-image: url('{{ asset('assets/images/bKash_Background.jpg') }}');
            background-size: 100% 100%;
            background-repeat: no-repeat;
            margin: 0 -15px;
            /* Adjusts the margin to fit within the card */
        }

        .nagad-responsive-row {
            /* height: 100px; */
            background-image: url('{{ asset('assets/images/Nsagad_backgroudn.jpg') }}');
            background-size: 100% 100%;
            background-repeat: no-repeat;
            margin: 0 -15px;
            /* Adjusts the margin to fit within the card */
        }

        .rocket-responsive-row {
            /* height: 100px; */
            background-image: url('{{ asset('assets/images/Rocket_Background.jpg') }}');
            background-size: 100% 100%;
            background-repeat: no-repeat;
            margin: 0 -15px;
            /* Adjusts the margin to fit within the card */
        }

        .bkash-responsive-row .col-md-4,
        .bkash-responsive-row .col-md-8 {
            padding: 0 15px;
        }

        .input-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .input-container input {
            flex: 1;
            margin-right: 10px;
        }

        .input-container button {
            flex-shrink: 0;
        }


        a.disabled {
    pointer-events: none;
    opacity: 0.6;
    cursor: not-allowed;
}

    </style>


    @php
    // dd($data['account_type']);
    $imgName = [
        'Personal' => asset('assets/partners/Personal.gif'),
        'Agent' => asset('assets/partners/Agent.gif'),
        'Merchant' => asset('assets/partners/Merchant.gif')
];
    $ewallet = strtolower($ewallet);
        if ($ewallet == 'bkash') {
            $time_class = 'bkash-time';
            $background_image = 'bkash-responsive-row';
            $button_style = 'bkash-complete-btn';
            $color = '#8F2A85';
        }
        if ($ewallet == 'nagad') {
            $time_class = 'nagad-time';
            $background_image = 'nagad-responsive-row';
            $button_style = 'nagad-complete-btn';
            $color = '#FF9600';
        }
        if ($ewallet == 'rocket') {
            $time_class = 'rocket-time';
            $background_image = 'rocket-responsive-row';
            $button_style = 'rocket-complete-btn';
            $color = '#8F2A85';
        }
    @endphp
    @if (!empty($message))
        <h3>{{ $message }}</h3>
    @endif
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
            </li>
        </ul>
    </div>
@endif
{{-- ======= --}}
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"
            aria-label="Close"></button>
    </div>
@endif
    @if (!empty($data))
        <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="payment-container">

                <span id="timer" class="{{ $time_class }}">00:00</span>
                <div class="p-2">
                    <img src="{{ $logo }}" alt="Logo" class="header-logo">
                </div>
                <div class="p-0 m-0">
                    <img height="5" width="100%" src="{{ $banner }}" alt="Logo">
                </div>

                <div class="row text-center">
                    <div class="col-4">
                        <img width="60px" src="{{ asset('assets/images/shopping-bag.png') }}"
                            alt="Logo">

                    </div>
                    <div class="col-8 p-0 m-0">
                        <p class="text-left text-bold" style="line-height: 1.2"><b><span style="font-size: 20px">Your payment amount is:</span><br> ৳
                               <span style="font-size: 30px"> {{ number_format($data['amount'], 2, '.', ',') }}<span></b></p>
                    </div>
                </div>

                <div id="intime">

                <div class="row {{ $background_image }} m-1 pb-2">
                    <div class="pt-3 col-md-4 {{ $ewallet == 'rocket' ? 'text-black' : 'text-white' }}">
                        <span>
                            @if($data['account_type']=="Merchant" || $data['account_type']=="Personal")
                            Please send to: <br>
                            পাঠান দয়া করে:
                            @else
                            Please cash out to: <br>
                            অনুগ্রহ করে ক্যাশ আউট করুন:
                            @endif
                        </span>
                    </div>
                    <div class="col-md-8 mt-4">
                          <div class="text-white input-container">
                            <input type="text" style="background-color:white" id="accountNumber" readonly value="{{ $data['phone_number'] }}" class="form-control">
                            <button id="copyButton"
                                class="{{ $button_style }} btn-sm {{ $ewallet == 'nagad' ? 'text-black' : '' }}">Copy |
                                অনুলিপি</button>
                        </div>
                        <div class="{{ $ewallet == 'rocket' ? 'text-black' : 'text-white' }}" style="margin-top:">Account Type: <b>{{$data['account_type']}}</b></div>
                    </div>
                </div>

                <div class="row text-center mt-2 p-2">
                    @if ($data['qr_image'])
                        {{-- <div class="bg-transparent ">
                            <img class="w-100" src="{{ $data['qr_image'] }}" style="width: 300px; margin: 0 auto;" />
                        </div> --}}
                    @endif

                </div>

                  @if($ewallet == "bkash")
                    <div class="row pl-3 pr-3 d-flex justify-content-center">
                        <img src="{{ $imgName[$data['account_type']] }}" class="img-fluid img-custom" alt="Responsive Image">
                    </div>
                @endif

                <style>
                    .img-custom {
                        max-width: 50%;
                        height: 20%;
                    }
                </style>


                <div class="row text-center mt-2 p-2" style="display: <?php echo $txn_verification==1?'block':'none'?>">
                    <h5 class="text-center" style="color: red">Please Enter Transaction ID Number
                    </h5>
                    <h6 class="text-center" style="color: red">অনুগ্রহ করে লেনদেন আইডি নম্বর লিখুন
                    </h6>
                    <div class="d-flex justify-content-center">
                        <input type="text" class="text-center" id="txn_verification" <?php echo $txn_verification==1?'required':''?> style="width: 80%;border:2px solid;border-radius:5px;font-size:25px" placeholder="Example: BFXXXXXXQT">
                    </div>
                </div>

                <div class="p-0 m-0">
                    <img height="5" width="100%" src="{{ $banner }}" alt="Logo">
                </div>

                <div class=" col-md-5 text-center mx-auto">
                    <a href="{{ route('partner.iframe.payment', ['id' => $data['id'],'txn'=>'','time'=>time()]) }}" class="{{ $button_style }}" id="complete-button">SUBMIT |
                        সম্পূর্ণ</a>
                </div>

                <p class="text-bold text-left mt-2 p-2 ">
                    Do not close this screen until you have completed the transaction in your e-wallet and enter the
                    transaction
                    number above. To complete this process, please click on "Complete" button below. Incomplete transaction
                    may
                    cause your deposit request to be delayed.
                </p>
                <p class="text-bold text-left mt-4 p-2">
                    অনুগ্রহ করে এই স্ক্রীনটি বন্ধ করবেন না যতক্ষণ না আপনি আপনার ই-ওয়ালেটে লেনদেন সম্পন্ন করেন এবং উপরে
                    লেনদেন
                    নম্বর লিখুন। এই প্রক্রিয়া সম্পূর্ণ করতে, নিচের "সম্পূর্ণ" বোতামে ক্লিক করুন। অসম্পূর্ণ লেনদেন আপনার
                    জমার
                    অনুরোধে বিলম্ব করতে পারে।
                </p>
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
                <div class="p-0 m-0">
                    <img height="5" width="100%" src="{{ $banner }}" alt="Logo">
                </div>
                </div>
            </div>
        </div>
    @endif

    @push('js')
<script src="{{asset('assets/global/js/jquery.min.js') }}"></script>
    <script>
        $(document).ready(function() {

        let clickCount = 0; // Initialize click counter

$('#complete-button').on('click', function(event) {
    clickCount++; // Increment click count

    if (clickCount === 2) {
        event.preventDefault(); // Prevent the default action of the link
        $(this).addClass('disabled'); // Add a class for styling
        $(this).css('pointer-events', 'none'); // Disable further clicks
    }
});



            var clipboard = new ClipboardJS('#copyButton', {
                target: function() {
                    return document.getElementById('accountNumber');
                }
            });

            clipboard.on('success', function(e) {
                e.clearSelection();
                $('#copyButton').text('Copied | অনুলিপি');
                $('#copyButton').addClass('disabled');
                $('#copyButton').prop('disabled', true);
            });

            clipboard.on('error', function(e) {
                console.error('Copy failed:', e.action);
            });
        });
    </script>
    <script src="{{asset('assets/iframe/clipboard.min.js') }}"></script>
    <script>
    $(document).ready(function() {
        function toggleButton() {
            var txnVerification = $('#txn_verification').val().trim();
            var isRequired = $('#txn_verification').attr('required') !== undefined;



            if (isRequired && txnVerification === '') {
                $('#complete-button').css({
                    'pointer-events': 'none',
                    'cursor': 'not-allowed',
                    'color':'gray'
                });
            } else {
                $('#complete-button').css({
                    'pointer-events': '',
                    'cursor': '',
                    'color':'white'
                });
            }

            var currentHref = $('#complete-button').attr('href');
            var updatedHref = currentHref.replace(/(txn=)[^\&]*/, '$1' + txnVerification);
            $('#complete-button').attr('href', updatedHref);
        }

        // Initial check
        toggleButton();

        // Check on every input change
        $('#txn_verification').on('input', function() {
            toggleButton();
        });
    });
</script>
<script>
        $(document).ready(function() {
            let remainingTime = @json($remainingTime);

            if (remainingTime > 0) {
                const timerElement = $('#timer');
                const intime = $('#intime');
                const outtime = $('#outtime');
                const updateTimer = () => {
                    if (remainingTime > 0) {
                        const minutes = Math.floor(remainingTime / 60);
                        const seconds = remainingTime % 60;
                        timerElement.text(`${minutes}:${seconds < 10 ? '0' : ''}${seconds}`);
                        remainingTime--;
                    } else {
                        intime.hide();
                        outtime.show();
                    }
                };

                setInterval(updateTimer, 1000);
                updateTimer();  // Initial call to display the timer immediately
            } else {
                $('#timer').hide();
                $('#expired').show();
            }
        });






    </script>





@endpush

</x-partner-layout>
