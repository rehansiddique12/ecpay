<x-iframe4-layout>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">


    <style>
        body {
            background: #f4f7fa;
            font-family: 'Roboto', Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 24px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            padding: 0 0 24px 0;
        }

        .header {
            background: #e2136e;
            color: #fff;
            border-radius: 10px 10px 0 0;
            padding: 20px 24px 12px 24px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-title {
            font-size: 1.6rem;
            font-weight: 700;
        }

        .header-desc {
            font-size: 1rem;
            font-weight: 400;
            margin-top: 2px;
        }

        .pay-service {
            display: flex;
            align-items: center;
            float: right;
            gap: 8px;
        }

        .pay-badge {
            background: #ff9800;
            color: #000;
            font-weight: 700;
            border-radius: 4px;
            padding: 2px 10px;
            font-size: 1rem;
            margin-right: 2px;
        }

        .service-label {
            font-size: 1.1rem;
            font-weight: 500;
            letter-spacing: 1px;
        }

        .lang-switch {
            display: flex;
            gap: 4px;
            margin-left: 12px;
        }

        .lang-btn {
            background: #e5e5e5;
            border: none;
            border-radius: 3px;
            padding: 2px 10px;
            font-size: 0.95rem;
            cursor: pointer;
            font-weight: 500;
            color: #333;
            transition: background 0.2s;
        }

        .lang-btn.active {
            background: #ff9800;
            color: #000;
        }

        .warning {
            color: #e53935;
            font-size: 1rem;
            margin: 18px 24px 0 24px;
            font-weight: 800;
        }

        .form-section {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin: 24px 24px 0 24px;
        }

        .form-group {
            flex: 1 1 150px;
            min-width: 220px;
        }

        .form-label {
            font-weight: 700;
            font-size: 1.08rem;
            margin-bottom: 2px;
            display: block;
        }

        .form-note {
            font-size: 0.98rem;
            color: #444;
            margin-bottom: 8px;
        }

        .wallet-box {
            display: flex;
            align-items: center;
            background: #f7fafc;
            border-radius: 8px;
            padding: 10px 10px;
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 6px;
            border: 1px solid #e0e0e0;
            gap: 5px;
        }

        .copy-btn {
            background: #e2136e;
            border: none;
            border-radius: 4px;
            padding: 6px 18px;
            cursor: pointer;
            font-size: 1.08rem;
            color: #fff;
            margin-left: 8px;
            font-weight: 600;
            letter-spacing: 1px;
            transition: background 0.2s;
        }

        .copy-btn:active {
            background: #b3005a;
        }

        .provider-box {
            background: #d500b6;
            color: #fff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 18px;
            font-size: 1.15rem;
            font-weight: 600;
            margin-top: 8px;
            min-width: 180px;
            max-width: 220px;
        }

        .provider-logo {
            width: 48px;
            height: 48px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .provider-logo img {
            width: 38px;
            height: 38px;
        }

        .trx-section {
            margin: 18px 24px 0 24px;
        }

        .trx-label {
            font-weight: 700;
            font-size: 1.08rem;
        }

        .trx-required {
            color: #e53935;
            font-size: 1rem;
            font-weight: 500;
            margin-left: 2px;
        }

        .trx-input {
            width: 96%;
            font-size: 1.4rem;
            padding: 12px 10px;
            border: 2px solid #e53935;
            border-radius: 6px;
            margin-top: 8px;
            margin-bottom: 8px;
            outline: none;
            transition: border 0.2s;
        }

        .trx-input:focus {
            border: 2px solid #0a6c3d;
        }

        .submit-btn {
            display: block;
            margin: 18px auto 0 auto;
            background: #e2136e;
            color: #fff;
            border: 2px solid #e2136e;
            border-radius: 8px;
            font-size: 1.15rem;
            font-weight: 600;
            padding: 10px 38px;
            cursor: pointer;
            transition: background 0.2s, color 0.2s, border 0.2s;
        }

        .submit-btn:hover {
            background: #b3005a;
            color: #fff;
            border-color: #e2136e;
        }

        .precautions {
            margin: 28px 24px 0 24px;
            font-size: 1.05rem;
        }

        .precautions strong {
            color: #e53935;
            font-weight: 700;
        }

        .precautions .gray {
            color: #888;
            font-size: 0.98rem;
            margin-top: 4px;
            display: block;
        }


        .form-flex {
            display: flex;
            flex-direction: column;
        }

        /* Default order for desktop */
        .wallet-group {
            order: 1;
        }
        .provider-group {
            order: 2;
        }


        .pay-service {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap; /* allow wrap on small screen */
    gap: 8px;
}

.pay-labels {
    display: flex;
    align-items: center;
    gap: 8px;
}

        @media (min-width: 601px) {
            .form-group {
                width: auto;
            }

            .form-section {
                flex-direction: row;
            }
        }

        @media (max-width: 600px) {


            .pay-service {
                flex: 1 1 25%;
                display: flex;
                justify-content: flex-end;
                align-items: center;
                gap: 6px;
                flex-wrap: wrap;
            }

            .header-title{
                flex: 1 1 50%;
                font-size: 1.6rem !important;
            }

            .pay-labels {
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .lang-switch {
                display: flex;
                gap: 4px;
            }
            


            .container {
                margin: 0;
                border-radius: 0;
                box-shadow: none;
                /* width: 100vw; */
                min-width: 0;
                max-width: 100vw;
                padding: 0;
            }

            .form-section,
            .trx-section,
            .precautions,
            .warning {
                margin-left: 10px;
                margin-right: 10px;
            }

            .form-label,
            .trx-label,
            .header-desc,
            .service-label,
            .pay-badge {
                font-size: 1rem !important;
            }

            

            .wallet-box {
                font-size: 1.4rem;
                padding: 8px 6px;
            }

            .copy-btn {
                padding: 8px 16px;
                font-size: 1rem;
            }

            .submit-btn {
                width: 95%;
                font-size: 1.05rem;
                padding: 12px 0;
            }

            .form-group {
                min-width: 0;
                width: 100%;
            }

            .wallet-group {
                order: 2;
            }

            .provider-group {
                order: 1;
            }

            .header-row {
                display: flex;
                flex-direction: row; /* stay side-by-side */
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
            }

            .lang-switch {
                margin-left: 0;
            }

            .wallet-box,
            .form-group {
                width: 100%;
                box-sizing: border-box;
            }

            .form-section {
                flex-direction: column;
                gap: 8px;
            }

            .trx-input {
                font-size: 1.4rem;
                padding: 10px 8px;
            }

            .precautions {
                font-size: 0.98rem;
            }

            .provider-label {
                font-size: 1rem;
            }

            .form-group[style] img {
                height: 100px !important;
                max-width: 90vw;
            }


            .wallet-box {
                background-color: white;
                border-radius: 4px;
                padding: 10px;
                position: relative;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .copy-btn {
                background-color: #e2136e;
                color: white;
                border: none;
                padding: 6px 12px;
                border-radius: 3px;
                font-weight: bold;
                cursor: pointer;
            }
        }
    </style>



@php
$ewallet = strtolower($ewallet_to_show ?? 'wallet');
$amount = $data['amount'];

// Default values

$bgcolor = '#ffffff';

if ($ewallet == 'bkash') {
   
    $bgcolor = '#e2136e';
} elseif ($ewallet == 'nagad') {
    
    $bgcolor = '#db3312';
} elseif ($ewallet == 'rocket') {
   
    $bgcolor = '#8F2A85';
}
@endphp

    @if (!empty($message))
        <h3>{{ $message }}</h3>
    @endif
    @if (!empty($data))
    <div class="container" id="intime">
        <div class="header" style="background-color: #32612D;">
            <div class="header-row">
                <div class="header-title">
                    <div  id="amount-label">
                        {{ number_format($data['amount'] ?? 0, 2, '.', ',') }} Tk
                    </div>
                    <div class="header-desc" id="amount-desc">Don't cash out more or less</div>
                </div>
                
                <div class="pay-service">
                    <div class="pay-labels">
                        <span class="pay-badge">PAY</span>
                        <span class="service-label">SERVICE</span>
                    </div>
                    <div class="lang-switch">
                        <button class="lang-btn active" id="lang-en" onclick="setLang('en')">EN</button>
                        <button class="lang-btn" id="lang-bn" onclick="setLang('bn')">বাং</button>
                    </div>
                </div>
            </div>
            
        </div>

        
        <div class="warning" id="amount-warning">
            If you change the amount of money (TK 500.00), you will not be able to get credit.
        </div>
        <div style="background-color:<?=$bgcolor?>;padding:10px;text-align:center;display:flex;align-items:center;gap:10px;">
            <img src="{{ $logo }}" alt="{{ ucfirst($ewallet) }} logo" style="width:75px;height:75px;background-color:white;border-radius:50%;object-fit:contain;">
            <span style="font-size:18px;font-weight:bold;color:white">{{ $ewallet_to_show ?? 'wallet' }} Deposit</span>
        </div>
        

        <div class="form-section form-flex">
            {{-- <div class="form-group provider-group" style="text-align:center;">
                <div class="form-label" id="provider-label">Wallet provider</div>
                <div style="display:flex;align-items:center;justify-content:center;gap:18px; padding: 20px; border-radius: 10px;">
                    @if($logo)
                        <img src="{{ $logo }}" alt="{{ ucfirst($ewallet) }} logo" style="width:230px;display:block;">
                    @else
                        <span>No wallet selected</span>
                    @endif
                </div>
            </div> --}}
        
            <div class="form-group wallet-group">
                <label class="form-label">Wallet No *</label>
                <div class="form-note" id="wallet-note">
                    This {{ $ewallet_to_show ?? 'wallet' }} number accepts only cashout
                </div>
                <div class="wallet-box">
                    <span id="wallet-type" style="font-weight: bold; color: {{ $bgcolor ?? '#000' }}">
                        {{ $data['account_type'] ?? 'Account' }}
                    </span><br>
                    <span id="wallet-number">{{ $data['phone_number'] ?? 'N/A' }}</span>
                    <button class="copy-btn" onclick="copyWallet()" title="Copy" style="background-color: {{ $bgcolor }};">COPY</button>
                </div>
            </div>
        </div>
        

        <form action="{{ route('iframe.payment4') }}" method="POST" id="cashout-form" autocomplete="off" onsubmit="return handleSubmit(event)">
            @csrf

            <div class="trx-section">
                <label class="trx-label" for="trxid" >
                    <span id="trx-label">Enter the TrxID number of the cashout</span>
                    
                    <span style="color:{{ $bgcolor }}" id="trx-required">(required)</span>
                </label>


                <input
                    class="trx-input"
                    id="trxid"
                    <?php echo $txn_verification == 1 ? 'required' : ''; ?>
                    name="txn"
                    type="text"
                    placeholder="Transaction ID (EC.{{ $ewallet == 'bkash' ? 'CC67DX6R2B' : '73PVF685' }})"
                    data-min="{{ $ewallet == 'bkash' ? 10 : ($ewallet == 'nagad' ? 8 : 1) }}"
                    data-ewallet="{{ $ewallet }}"
                    onblur="checkMinLength()"
                >
            </div>

            <small id="txn_error" style="color: red; display: none;margin-left:25px"></small>

            {{-- Hidden inputs --}}
            <input type="hidden" name="username" value="{{ $data['username'] ?? '' }}">
            <input type="hidden" name="ewallet" value="{{ $data['ewallet'] ?? '' }}">
            <input type="hidden" name="amount" value="{{ $data['amount'] ?? 0 }}">
            <input type="hidden" id="fund_id" name="fund_id" value="{{ $data['gate_id'] ?? '' }}">
            <input type="hidden" name="time" value="{{ time() }}">

            <button class="submit-btn" type="submit" id="submit-btn" style="background-color: {{ $bgcolor }};">
                Confirm
            </button>
        </form>

        <div class="precautions">
            <strong id="precaution-title">Precautions:</strong><br>
            <span style="color:#e53935;" id="precaution-red">
                The transaction ID must be filled in correctly, otherwise the score will fail!
            </span>
            <span class="gray" id="precaution-gray">
                Please make sure you cash out to <b>the {{ $ewallet_to_show ?? 'wallet' }} deposit wallet number</b>.
                If you cash out from any other wallet of this number, there is no possibility of getting the money.
            </span>
        </div>

        <br>
        <br>
        
    </div>

    <div class="container" id="accnotfound" style="display: none; height: 100vh; justify-content: center; align-items: center;">
        <center>
        <div class="text-center">
            <svg style="width: 250px;margin:20px" viewBox="0 0 512 512"><path fill="<?=$bgcolor?>" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zm0-384c13.3 0 24 10.7 24 24l0 112c0 13.3-10.7 24-24 24s-24-10.7-24-24l0-112c0-13.3 10.7-24 24-24zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"/></svg>
            <h5 id="failmessage">You Can not Proceed With this E-wallet account! Try Again Later.</h5>
        </div>
        </center>
    </div>


    

    @endif

    @if (!empty($data['redirect_url']))
        <a href="{{ $data['redirect_url'] }}" class="form-control btn custombtn-outline mt-2 mb-2">Return to the
            merchant</a>
    @endif



    @push('js')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script>

function checkMinLength() {
        let txnInput = document.getElementById('trxid');
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



            // Language dictionary
            const dict = {
                en: {
                    'amount-label': `{{ number_format($data['amount'] ?? 0, 2, '.', ',') }} Tk`,
                    'amount-desc': "Don't cash out more or less",
                    'amount-warning': `If you change the amount of money (TK {{ number_format($data['amount'] ?? 0, 2, '.', ',') }}), you will not be able to get credit.`,
                    'wallet-label': 'Wallet No *',
                    'wallet-note': `Only cashout is accepted at this {{ $ewallet_to_show ?? 'wallet' }} number `,
                    'provider-label': 'Wallet provider',
                    'provider-name': '{{ $ewallet_to_show ?? 'wallet' }} Deposit',
                    'trx-label': 'Enter the TrxID number of the cashout',
                    'trx-required': '(Required)',
                    'submit-btn': 'Confirm',
                    'precaution-title': 'Precautions:',
                    'precaution-red': 'The transaction ID must be filled in correctly, otherwise the score will fail !',
                    'precaution-gray': 'Please make sure you cash out to <b>the {{ $ewallet_to_show ?? 'wallet' }} deposit wallet number</b>. If you cash out from any other wallet of this number, there is no possibility of getting the money."',
                    'trxid-placeholder': 'TrxID must be correct!'
                },
                bn: {
                    'amount-label': '{{ number_format($data['amount'] ?? 0, 2, '.', ',') }} টাকা',
                    'amount-desc': 'কম বা বেশি ক্যাশ আউট করবেন না',
                    'amount-warning': 'আপনি যদি টাকার পরিমাণ পরিবর্তন করেন ({{ number_format($data['amount'] ?? 0, 2, '.', ',') }} টাকা), তাহলে আপনি ক্রেডিট পাবেন না।',
                    'wallet-label': 'ওয়ালেট নম্বর *',
                    'wallet-note': 'শুধুমাত্র এই {{ $ewallet_to_show ?? 'wallet' }} নম্বরে ক্যাশআউট গ্রহণযোগ্য',
                    'provider-label': 'ওয়ালেট প্রদানকারী',
                    'provider-name': '{{ $ewallet_to_show ?? 'wallet' }} ডিপোজিট',
                    'trx-label': 'ক্যাশআউটের TrxID নম্বর লিখুন',
                    'trx-required': '(প্রয়োজন)',
                    'submit-btn': 'নিশ্চিত',
                    'precaution-title': 'সতর্কতা:',
                    'precaution-red': 'লেনদেন আইডি অবশ্যই সঠিকভাবে পূরণ করতে হবে, না হলে স্কোর ফেল হবে!',
                    'precaution-gray': 'অনুগ্রহ করে নিশ্চিত করুন আপনি <b>{{ $ewallet_to_show ?? 'wallet' }} ডিপোজিট ওয়ালেট নম্বরে</b> ক্যাশআউট করছেন। অন্য কোনো ওয়ালেট থেকে ক্যাশআউট করলে টাকা পাওয়ার কোনো সম্ভাবনা নেই।',
                    'trxid-placeholder': 'TrxID অবশ্যই সঠিক করতে হবে!'
                }
            };
            let currentLang = 'en';

            function setLang(lang) {
                currentLang = lang;
                document.getElementById('lang-en').classList.toggle('active', lang === 'en');
                document.getElementById('lang-bn').classList.toggle('active', lang === 'bn');
                for (const key in dict[lang]) {
                    const el = document.getElementById(key);
                    if (el) {
                        if (key === 'precaution-gray') {
                            el.innerHTML = dict[lang][key];
                        } else {
                            el.textContent = dict[lang][key];
                        }
                    }
                }
                document.getElementById('trxid').placeholder = dict[lang]['trxid-placeholder'];
            }

            function copyWallet() {
                const wallet = document.getElementById('wallet-number').textContent;
                navigator.clipboard.writeText(wallet);
                alert(currentLang === 'bn' ? 'ওয়ালেট নম্বর কপি হয়েছে!' : 'Wallet number copied!');
            }

            function handleSubmit(e) {
                e.preventDefault();
                const trxid = document.getElementById('trxid').value.trim();
                if (!trxid) {
                    alert(currentLang === 'bn' ? 'TrxID অবশ্যই পূরণ করতে হবে!' : 'TrxID is required!');
                    return false;
                }
                alert((currentLang === 'bn' ? 'ক্যাশআউট অনুরোধ জমা হয়েছে: ' : 'Cashout request submitted: ') +
                trxid);
                document.getElementById('cashout-form').reset();
                return false;
            }
            // Set default language
            setLang('bn');


            function handleSubmit(event) {
                const trxid = document.getElementById('trxid').value;

                if (!trxid || trxid.length < 5) { // example validation
                    document.getElementById('txn_error').innerText = "Please enter a valid TrxID.";
                    document.getElementById('txn_error').style.display = "block";
                    event.preventDefault(); // prevent form from submitting
                    return false;
                }

                // Let the form submit naturally via POST
                return true;
            }




            $(document).ready(function() {      
                
                let clickCount = 0; // Initialize click counter

                $('#submit-btn').on('click', function(event) {
                    $(this).text('Processing...');
                    $(this).css('opacity', '0.6');
                    clickCount++; // Increment click count

                    if (clickCount === 2) {
                        event.preventDefault(); // Prevent the second form submission
                        $(this).addClass('disabled');
                        $(this).css('pointer-events', 'none');

                    }
                });
                
                
                function toggleButton() {
                var txnVerification = $('#trxid').val().trim();
                var isRequired = $('#trxid').attr('required') !== undefined;

                let txnInput = document.getElementById('trxid');
                let minLength = txnInput.getAttribute('data-min');
                let ewalletType = txnInput.getAttribute('data-ewallet');
                let txnValue = txnInput.value.trim();

                if (isRequired && txnVerification === '') {
                    $('#submit-btn').prop('disabled', true).css({
                        'cursor': 'not-allowed',
                        'background-color': '{{ $bgcolor }}',
                        'opacity': '0.6'
                    });
                } else {
                    $('#submit-btn').prop('disabled', false).css({
                        'cursor': '',
                        'opacity': '1'
                    });
                }

                if ((minLength && txnInput.value.length < minLength) || (ewalletType === 'bkash' && !/^[A-Za-z]/.test(txnValue)) || (ewalletType === 'nagad' && !/^[0-9]/.test(txnValue))) {
                    $('#submit-btn').prop('disabled', true).css({
                        'cursor': 'not-allowed',
                        'background-color': '{{ $bgcolor }}',
                        'opacity': '0.6'
                    });
                } else {
                    $('#submit-btn').prop('disabled', false).css({
                        'cursor': '',
                        'opacity': '1'
                    });
                }


            }

            // Initial check
            toggleButton();

            // Check on every input change
            $('#trxid').on('input', function() {
                toggleButton();
            });



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
                        $('#wallet-number').text(response.phone_number);
                        $('#fund_id').val(response.fund_id);

                        // $('#acctype1').text(response.account_type);
                        // $('#acctype2').text(response.account_type);

                    }

                    if (response.status === 'fail') {
                        $('#intime').hide();
                        // $('#outtime').hide();
                        $('#accnotfound').css('display', 'flex');
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
        @endpush


</x-partner-layout>
