<x-iframe-layout>
 <style>
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
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            min-width: 300px;
            max-width: 800px;
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
            /* position: absolute; */
            /* top: 0px; */
            /* right: 0px; */
            background-color: #C31C57;
            color: white;
            padding: 5px 5px;
            border-radius: 5px;
            font-size: 16px;
        }

        .nagad-time {
            /* position: absolute;
            top: 0px;
            right: 0px; */
            background-color: #FF9600;
            color: white;
            padding: 5px 5px;
            border-radius: 5px;
            font-size: 16px;
        }

        .rocket-time {
            /* position: absolute;
            top: 0px;
            right: 0px; */
            background-color: #8F2A85;
            color: white;
            padding: 5px 5px;
            border-radius: 5px;
            font-size: 16px;
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
            /* margin-right: 10px; */
        }

        .input-container button {
            flex-shrink: 0;
        }


        a.disabled {
    pointer-events: none;
    opacity: 0.6;
    cursor: not-allowed;
}



.timer {
            font-size: 14px;
            margin-bottom: 5px;
            color:black;
            float: right;
        }
        .amount {
            font-size: 32px;
            font-weight: bold;
            margin: 10px 0;
            color:white;
        }

        .ename{
           color:white;
           font-size: 14px;
           font-weight: bold;
        }



    </style>


    @php
    $ewallet = strtolower($ewallet);

        if ($ewallet == 'bkash') {
            $bangla_ewallet = 'বিকাশ';
            $time_class = 'bkash-time';
            $background_image = 'bkash-responsive-row';
            $button_style = 'bkash-complete-btn';
            $color = '#e2136e';
            $bgcolor = '#e2136e';
            $bgcolorrbga = 'rgb(226, 19, 110,0.2)';
        }
        if ($ewallet == 'nagad') {
            $bangla_ewallet = 'নগদ';
            $time_class = 'nagad-time';
            $background_image = 'nagad-responsive-row';
            $button_style = 'nagad-complete-btn';
            $color = '#FF9600';
            $bgcolor = '#FF9600';
            $bgcolorrbga = 'rgb(255, 150, 0,0.2)';
        }
        if ($ewallet == 'rocket') {
            $bangla_ewallet = 'রকেট';
            $time_class = 'rocket-time';
            $background_image = 'rocket-responsive-row';
            $button_style = 'rocket-complete-btn';
            $color = '#8F2A85';
            $bgcolor = '#8F2A85';
            $bgcolorrbga = 'rgb(143, 42, 133,0.2)';
        }
        $amount = $data['amount'];
    @endphp
    <style>
        .circle {
            width: 30px;
            height: 30px;
            background-color: <?=$bgcolor?>;
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 14px;
            margin: 0 auto 10px;
        }

        .custombtn {
            color: #fff;
            background-color: <?=$bgcolor?>;
            border-color: <?=$bgcolor?>;
        }

        .custombtn-outline {
            color: <?=$bgcolor?>;
            border-color: <?=$bgcolor?>;
        }

        .custombtn-outline:hover {
            color: #fff;
            background-color: <?=$bgcolor?>;
            border-color: <?=$bgcolor?>;
        }

        .arrow {
            position: absolute;
            top: 0;
            right: 0;
            cursor: pointer;
            font-size: 20px;
            transition: transform 0.3s;
        }
        .content {
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 16px;
            color: #555;
            position: relative;
            padding-left:25px;
            width:95%;
        }
        .expanded {
            display: block;
            white-space: normal;
            -webkit-line-clamp: unset;
        }
    </style>
    <style>
.textanimation {
  animation: zoomInOut 1.5s infinite;
  height: 50px;
}

@keyframes zoomInOut {
  0% {
    font-size: 12.5px;
    padding: 0px;
  }
  15% {
    font-size: 10px;
    padding: 12.5px;
  }
  30% {
    font-size: 12.5px;
    padding: 0px;
  }
  45% {
    font-size: 10px;
    padding: 12.5px;
  }
  60% {
    font-size: 12.5px;
    padding: 0px;
  }
  100% {
    font-size: 12.5px;
    padding: 0px;
  }
}

</style>
<style>

        .collapsible-header {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }
        .toggle-icon {
            margin-right: 10px;
            transition: transform 0.3s ease;
        }
        .collapsible-content {
            margin-top: 10px;
        }
        .hidden-content {
            display: none;
        }

    </style>
    <style>

        .header {
            font-size: 16px;
            font-weight: bold;
        }
        .success-text {
            color: green;
        }
        .cashout-text {
            color: red;
        }
        .icon {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }
        .icon img {
            width: 24px;
            height: 24px;
        }
        .hidden-text {
            background: #ddd;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 16px;
            letter-spacing: 3px;
        }
        .time-section {
            margin-top: 10px;
            font-size: 14px;
        }
        .transaction-id {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fcecec;
            padding: 8px;
            border-radius: 5px;
            margin-top: 10px;
            border: 1px solid #f5a3a3;
        }
        .transaction-id label {
            font-size: 14px;
            color: #d9534f;
            font-weight: bold;
        }
        .transaction-id span {
            font-size: 16px;
            font-weight: bold;
        }
        .copy-icon {
            cursor: pointer;
        }
    </style>
    @if (!empty($message))
        <h3>{{ $message }}</h3>
    @endif
    @if (!empty($data))
        <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="payment-container">
                <div id="intime">
                    <div style="min-height:70px" class="">
                        <img src="{{ $logo }}" style="width: 50%;" alt="Logo">
                        <span style="float: right;margin-top:20px" id="timer" class="{{ $time_class }}">00:00</span>
                        <span style="float: right;margin-top:20px;font-weight:bold;text-align:right;font-size:12px" style="margin-top: 20px">Remaining time: <br>বাকি সময়: </span>
                    </div>
                    <div style="background-color: black;text-align:center;" class="pb-1">
                        <br>
                        <h4 style="color: white">Total Payment | মোট পেমেন্ট</h4>

                        <p class="amount" style="color: #ffc000">{{ number_format($data['amount'], 2, '.', ',') }} Tk</p>

                        <p style="color: white;font-size: 11px;">Please do not change the amount. Changing amount will result in credit lost.</p>
                        <p style="color: white;font-size:11px">দয়া করে পরিমাণ পরিবর্তন করবেন না। পরিমাণ পরিবর্তন করলে ক্রেডিট নষ্ট হবে।</p>
                    </div>

                    <div style="background-color: <?=$bgcolorrbga?>;border-radius:5px" class="mt-2 mb-2 ml-3 mr-3 p-2">
                        <div style="display: flex; align-items: center;">
                            <div style="margin-right: 10px;">
                                <span class="circle">1</span>
                            </div>
                            <div>
                                <span style="font-weight: bold">STEP 1: Copy the <span id="acctype1">{{$data['account_type']}}</span> Account <br> ধাপ ১: এজেন্ট অ্যাকাউন্টটি কপি করুন</span><br>
                            </div>
                        </div>


                        <div style="background-color: white;border-radius:2px" class="p-2">
                            <span style="font-weight: bold;color:<?=$bgcolor?>" id="acctype2">{{$data['account_type']}}</span> <span id="accountNumber"> {{ $data['phone_number'] }}</span>
                                <button id="copyButton" style="color:<?=$bgcolor?>;float:right;margin-top:-8px"
                                    class="btn">
                                   <svg style="width: 20px" viewBox="0 0 512 512"><path fill="<?=$bgcolor?>" d="M64 464l224 0c8.8 0 16-7.2 16-16l0-64 48 0 0 64c0 35.3-28.7 64-64 64L64 512c-35.3 0-64-28.7-64-64L0 224c0-35.3 28.7-64 64-64l64 0 0 48-64 0c-8.8 0-16 7.2-16 16l0 224c0 8.8 7.2 16 16 16zM224 304l224 0c8.8 0 16-7.2 16-16l0-224c0-8.8-7.2-16-16-16L224 48c-8.8 0-16 7.2-16 16l0 224c0 8.8 7.2 16 16 16zm-64-16l0-224c0-35.3 28.7-64 64-64L448 0c35.3 0 64 28.7 64 64l0 224c0 35.3-28.7 64-64 64l-224 0c-35.3 0-64-28.7-64-64z"/></svg>
                                </button>
                        </div>

                        <div style="background-color: <?=$bgcolor?>;text-align:center;" class="p-2 mt-2">
                            <span style="font-weight: bold;color:white">This {{$ewallet_to_show}} number only accept cashout.<br>
                            এই {{$bangla_ewallet}} নম্বরটি শুধুমাত্র ক্যাশআউট গ্রহণ করে।
                            </span>
                        </div>

                        <div style="display: flex; align-items: center;" class="pt-2 mb-1">
                            <div style="margin-right: 10px;">
                                <span class="circle">2</span>
                            </div>
                            <div>
                                <span style="font-weight: bold">STEP 2: Enter the Transaction ID <span style="color: red"> ** REQUIRED </span> <br> ধাপ ২: লেনদেন আইডি লিখুন <span style="color: red">** প্রয়োজন</span> </span><br>
                            </div>
                        </div>


                        <form action="{{ route('iframe.payment2') }}" method="POST" id="payment-form">
                            @csrf
                        <div class="input-container" style="position: relative;">
                            <input type="text" name="txn" class="form-control" id="txn_verification"
                                <?php echo $txn_verification == 1 ? 'required' : ''; ?>
                                placeholder="Transaction ID (EC.{{$ewallet == 'bkash'?'CC67DX6R2B':'73PVF685'}})"
                                data-min="{{($ewallet == 'bkash') ? 10 : (($ewallet == 'nagad') ? 8 : 1)}}"
                                data-ewallet="{{$ewallet}}"
                                style="padding-right: 30px;" onblur="checkMinLength()">
                            <svg title="A Test Title" style="width:20px;position: absolute; right: 20px; top: 50%; transform: translateY(-50%); font-size: 18px; color: #333;" viewBox="0 0 512 512"><path fill="<?=$bgcolor?>" d="M464 256A208 208 0 1 0 48 256a208 208 0 1 0 416 0zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zm169.8-90.7c7.9-22.3 29.1-37.3 52.8-37.3l58.3 0c34.9 0 63.1 28.3 63.1 63.1c0 22.6-12.1 43.5-31.7 54.8L280 264.4c-.2 13-10.9 23.6-24 23.6c-13.3 0-24-10.7-24-24l0-13.5c0-8.6 4.6-16.5 12.1-20.8l44.3-25.4c4.7-2.7 7.6-7.7 7.6-13.1c0-8.4-6.8-15.1-15.1-15.1l-58.3 0c-3.4 0-6.4 2.1-7.5 5.3l-.4 1.2c-4.4 12.5-18.2 19-30.6 14.6s-19-18.2-14.6-30.6l.4-1.2zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"/></svg>
                        </div>
                        <small id="txn_error" style="color: red; display: none;"></small>


                        {{-- <div class="textanimation pt-2"><span style="font-weight: bold">Important Notice:</span><br> <span style="font-weight: bold;color:red">Please make sure the Transaction ID is correct before submitting.</span></div> --}}


                            <input type="hidden" name="username" value="{{ $data['username'] }}">
                            <input type="hidden" name="ewallet" value="{{ $data['ewallet'] }}">
                            <input type="hidden" name="amount" value="{{ $data['amount'] }}">
                            <input type="hidden" id="fund_id" name="fund_id" value="{{ $data['gate_id'] }}">
                            <input type="hidden" name="time" value="{{ time() }}">

                            <button type="submit" class="form-control btn btn-dark mt-2 mb-2 text-white" id="complete-button">
                                Submit | জমা দিন

                            </button>
                        </form>
                        @if(!empty($data['redirect_url']))
                        <a href="{{ $data['redirect_url'] }}" class="form-control btn custombtn-outline mt-2 mb-2">Return to the merchant</a>
                        @endif

                        <div style="background-color: <?=$bgcolor?>;text-align:center;" class="p-2 mt-2">
                            <span style="font-weight: bold;color:white;font-size:12px">Please make sure the Transaction ID is correct before submitting.<br>
                            জমা দেওয়ার আগে দয়া করে নিশ্চিত করুন যে লেনদেন আইডি সঠিক।

                            </span>
                        </div>





                    </div>

                    {{-- <div style="background-color: <?=$bgcolorrbga?>;border-radius:5px" class="mt-2 mb-2 ml-3 mr-3 p-2">
                        <div class="collapsible-header" onclick="toggleSection()">
                            <span class="toggle-icon">▼</span> How to Cash Out from {{$ewallet_to_show}}
                        </div>
                        <div class="collapsible-content">
                            <ol>
                                <li>Open the {{$ewallet_to_show}} App or USSD menu and enter (*247#).</li>
                                <li>Select <span style="color: <?=$bgcolor?>; font-weight: bold;">Cash Out</span>.</li>
                                <li>Enter the {{$ewallet_to_show}} Agent account number.</li>
                                <li>Enter the payment amount.</li>
                                <li>Enter the {{$ewallet_to_show}} PIN to confirm the payment.</li>
                                <li>Receive a confirmation message that the payment was successful.</li>
                                <li>Fill in the Transaction ID and click 'I Have Paid'.</li>
                            </ol>


                        <div style="background-color: white;border-radius: 5px;padding: 5px;">
                            <div class="header">
                                <span class="cashout-text">আপনার ক্যাশ আউট</span>
                                <span class="success-text"> সফল হয়েছে</span>
                                <svg style="width:30px;float: right;" viewBox="0 0 512 512"><path fill="<?=$bgcolor?>" d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-111 111-47-47c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l64 64c9.4 9.4 24.6 9.4 33.9 0L369 209z"/></svg>
                            </div>
                            <br>
                            <div class="icon">
                                <svg style="width: 40px" viewBox="0 0 512 512"><path fill="<?=$bgcolor?>" d="M399 384.2C376.9 345.8 335.4 320 288 320l-64 0c-47.4 0-88.9 25.8-111 64.2c35.2 39.2 86.2 63.8 143 63.8s107.8-24.7 143-63.8zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zm256 16a72 72 0 1 0 0-144 72 72 0 1 0 0 144z"/></svg>
                                <span style="margin-top: 10px;line-height: 0.8;">
                                &nbsp;&nbsp;  *********<br>
                                &nbsp;&nbsp; *********

                                </span>
                            </div>
                            <br>


                            <div style="width: 50%;border:2px solid <?=$bgcolor?>;border-radius: 5px;padding: 5px;float: right;">
                                <label>ট্রানজেকশন আইডি</label>
                                <span style="font-weight:bold">CAG45A8W8</span>
                                <svg style="width: 20px;float: right;margin-top:5px" viewBox="0 0 512 512"><path fill="<?=$bgcolor?>" d="M64 464l224 0c8.8 0 16-7.2 16-16l0-64 48 0 0 64c0 35.3-28.7 64-64 64L64 512c-35.3 0-64-28.7-64-64L0 224c0-35.3 28.7-64 64-64l64 0 0 48-64 0c-8.8 0-16 7.2-16 16l0 224c0 8.8 7.2 16 16 16zM224 304l224 0c8.8 0 16-7.2 16-16l0-224c0-8.8-7.2-16-16-16L224 48c-8.8 0-16 7.2-16 16l0 224c0 8.8 7.2 16 16 16zm-64-16l0-224c0-35.3 28.7-64 64-64L448 0c35.3 0 64 28.7 64 64l0 224c0 35.3-28.7 64-64 64l-224 0c-35.3 0-64-28.7-64-64z"/></svg>
                            </div>
                            <div class="time-section">
                                সময়:<br> <strong style="font-weight:bold"><?=date("h:ia d/m/y");?></strong>
                            </div>
                            <br>
                        </div>
                        </div>
                    </div> --}}
                </div>






                <div id="outtime" style="display: none;">
                    <div class="text-center">
                    <svg style="width: 250px;margin:20px" viewBox="0 0 512 512"><path fill="<?=$bgcolor?>" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zm0-384c13.3 0 24 10.7 24 24l0 112c0 13.3-10.7 24-24 24s-24-10.7-24-24l0-112c0-13.3 10.7-24 24-24zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"/></svg>
                    <h5>Sorry. We are not able to process your payment. Please contact customer service for assistance.
                </h5>
                <h5>দুঃখিত। আমরা আপনার পেমেন্ট প্রক্রিয়া করতে সক্ষম নই। সহায়তার জন্য গ্রাহক পরিষেবার সাথে যোগাযোগ করুন।</h5>

                </div>
                </div>

                <div id="accnotfound" style="display: none;">
                    <div class="text-center">
                    <svg style="width: 250px;margin:20px" viewBox="0 0 512 512"><path fill="<?=$bgcolor?>" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zm0-384c13.3 0 24 10.7 24 24l0 112c0 13.3-10.7 24-24 24s-24-10.7-24-24l0-112c0-13.3 10.7-24 24-24zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"/></svg>
                    <h5 id="failmessage">You Can not Proceed With this E-wallet account! Try Again Later.</h5>
                </div>
                </div>

            </div>
        </div>
    @endif
<script src="{{asset('assets/global/js/jquery.min.js') }}"></script>
    <script>
        $(document).ready(function() {

        let clickCount = 0; // Initialize click counter

$('#complete-button').on('click', function(event) {
    $(this).text('Processing...');
    $(this).css('opacity', '0.6');
    clickCount++; // Increment click count

    if (clickCount === 2) {
        event.preventDefault(); // Prevent the second form submission
        $(this).addClass('disabled');
        $(this).css('pointer-events', 'none');

    }
});



    @push('js')
        <script>
            // Language dictionary
            const dict = {
                en: {
                    'amount-label': `{{ number_format($data['amount'] ?? 0, 2, '.', ',') }} Tk`,
                    'amount-desc': "Don't cash out more or less",
                    'amount-warning': `If you change the amount of money (INR {{ number_format($data['amount'] ?? 0, 2, '.', ',') }}), you will not be able to get credit.`,
                    'wallet-label': 'Wallet No *',
                    'wallet-note': `This {{ $ewallet_to_show ?? 'wallet' }} number only accpet cashout `,
                    'provider-label': 'Wallet provider',
                    'provider-name': 'BKASH Deposit',
                    'trx-label': 'Enter the TrxID number of the cashout',
                    'trx-required': '(required)',
                    'submit-btn': 'Confirm and Submit',
                    'precaution-title': 'Precautions:',
                    'precaution-red': 'The transaction ID must be filled in correctly, otherwise the score will fail !',
                    'precaution-gray': 'Please make sure you cash out to <b>the BKASH deposit wallet number</b>. If you cash out from any other wallet of this number, there is no possibility of getting the money."',
                    'trxid-placeholder': 'TrxID must be correct!'
                },
                bn: {
                    'amount-label': 'বিডিটি ৫০০.০০',
                    'amount-desc': 'কম বা বেশি ক্যাশ আউট করবেন না',
                    'amount-warning': 'আপনি যদি টাকার পরিমাণ পরিবর্তন করেন (INR ৫০০.০০), তাহলে আপনি ক্রেডিট পাবেন না।',
                    'wallet-label': 'ওয়ালেট নম্বর *',
                    'wallet-note': 'শুধুমাত্র এই বিকাশ নম্বরে ক্যাশআউট গ্রহণযোগ্য',
                    'provider-label': 'ওয়ালেট প্রদানকারী',
                    'provider-name': 'বিকাশ ডিপোজিট',
                    'trx-label': 'ক্যাশআউটের TrxID নম্বর লিখুন',
                    'trx-required': '(প্রয়োজনীয়)',
                    'submit-btn': 'নিশ্চিত এবং জমা দিন',
                    'precaution-title': 'সতর্কতা:',
                    'precaution-red': 'লেনদেন আইডি অবশ্যই সঠিকভাবে পূরণ করতে হবে, না হলে স্কোর ফেল হবে!',
                    'precaution-gray': 'অনুগ্রহ করে নিশ্চিত করুন আপনি <b>বিকাশ ডিপোজিট ওয়ালেট নম্বরে</b> ক্যাশআউট করছেন। অন্য কোনো ওয়ালেট থেকে ক্যাশআউট করলে টাকা পাওয়ার কোনো সম্ভাবনা নেই।',
                    'trxid-placeholder': 'TrxID অবশ্যই সঠিক করতে হবে!'
                }
            });

            clipboard.on('success', function(e) {
                e.clearSelection();
                // $('#copyButton').text('Copied | অনুলিপি');
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

            let txnInput = document.getElementById('txn_verification');
            let minLength = txnInput.getAttribute('data-min');
            let ewalletType = txnInput.getAttribute('data-ewallet');
            let txnValue = txnInput.value.trim();

            if (isRequired && txnVerification === '') {
                $('#complete-button').prop('disabled', true).css({
                    'cursor': 'not-allowed',
                    'background-color': 'black',
                    'opacity': '0.6'
                });
            } else {
                $('#complete-button').prop('disabled', false).css({
                    'cursor': '',
                    'opacity': '1'
                });
            }

            if ((minLength && txnInput.value.length < minLength) || (ewalletType === 'bkash' && !/^[A-Za-z]/.test(txnValue)) || (ewalletType === 'nagad' && !/^[0-9]/.test(txnValue))) {
                $('#complete-button').prop('disabled', true).css({
                    'cursor': 'not-allowed',
                    'background-color': 'black',
                    'opacity': '0.6'
                });
            } else {
                $('#complete-button').prop('disabled', false).css({
                    'cursor': '',
                    'opacity': '1'
                });
            }


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
    function toggleSection() {
        var content = document.querySelector('.collapsible-content');
        var icon = document.querySelector('.toggle-icon');

        if (content.classList.contains('hidden-content')) {
            content.classList.remove('hidden-content');
            icon.innerHTML = '▼';
        } else {
            content.classList.add('hidden-content');
            icon.innerHTML = '▶';
        }
    }

    function checkMinLength() {
        let txnInput = document.getElementById('txn_verification');
        let errorMsg = document.getElementById('txn_error');
        let minLength = txnInput.getAttribute('data-min');
        let ewalletType = txnInput.getAttribute('data-ewallet');
        let txnValue = txnInput.value.trim();

        // Reset error message
        errorMsg.style.display = 'none';
        errorMsg.innerText = '';

        // Check minimum length
        if (txnValue.length < minLength) {
            errorMsg.innerText = 'Transaction ID must be at least ' + minLength + ' characters long.';
            errorMsg.style.display = 'block';
            return;
        }

        // Check first character based on ewallet type
        if (ewalletType === 'bkash' && !/^[A-Za-z]/.test(txnValue)) {
            errorMsg.innerText = '{{$ewallet_to_show}} Transaction ID must start with a letter (A-Z).';
            errorMsg.style.display = 'block';
            return;
        }

        if (ewalletType === 'nagad' && !/^[0-9]/.test(txnValue)) {
            errorMsg.innerText = '{{$ewallet_to_show}} Transaction ID must start with a digit (0-9).';
            errorMsg.style.display = 'block';
            return;
        }
    }
</script>
<script>
        function toggleText() {
            let text = $("#text");
            let arrow = $(".arrow");

            if (text.hasClass("expanded")) {
                text.removeClass("expanded").css({
                    "display": "-webkit-box",
                    "-webkit-line-clamp": "1",
                    "white-space": "nowrap"
                });
                arrow.html("&#9660;");
            } else {
                text.addClass("expanded").css({
                    "display": "block",
                    "white-space": "normal"
                });
                arrow.html("&#9650;");
            }
        }
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
                        timerElement.text(`00:${minutes < 10 ? '0' : ''}${minutes}:${seconds < 10 ? '0' : ''}${seconds}`);
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



            $.ajax({
                url: "{{ route('iframe.getaccount') }}", // Define the route for POST request
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}", // CSRF token for security
                    ewallet: "{{ $ewallet }}",
                    amount: "{{ $amount }}",
                    username: "{{ $data['username'] }}",
                    acc: "{{ $data['acc'] }}",
                    transection_id: "{{ $data['transection_id'] }}",
                    member_id: "{{ $data['member_id'] }}",
                    gate_id: "{{ $data['gate_id'] }}"
                },
                success: function(response) {
                    console.log(response);
                    if (response.status === 'success') {
                        $('#accountNumber').text(response.phone_number);
                        $('#fund_id').val(response.fund_id);

                        $('#acctype1').text(response.account_type);
                        $('#acctype2').text(response.account_type);

                    }

                    if (response.status === 'fail') {
                        $('#intime').hide();
                        $('#outtime').hide();
                        $('#accnotfound').show();
                        $('#failmessage').text(response.message);

                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    console.error('Status:', status);
                    console.error('Response Text:', xhr.responseText);
                }
            });

        });






    </script>


@push('script')

@endpush
