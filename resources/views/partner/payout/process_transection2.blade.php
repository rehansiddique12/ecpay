<x-iframe-layout>
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
            font-weight: 500;
        }

        .form-section {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin: 24px 24px 0 24px;
        }

        .form-group {
            flex: 1 1 220px;
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
            padding: 10px 16px;
            font-size: 1.2rem;
            font-weight: 500;
            margin-bottom: 6px;
            border: 1px solid #e0e0e0;
            gap: 10px;
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
            width: 100%;
            font-size: 1.1rem;
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

        @media (max-width: 600px) {
            .container {
                margin: 0;
                border-radius: 0;
                box-shadow: none;
                width: 100vw;
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
            .header-title,
            .header-desc,
            .service-label,
            .pay-badge {
                font-size: 1rem !important;
            }

            .wallet-box {
                font-size: 1rem;
                padding: 8px 6px;
            }

            .copy-btn {
                padding: 8px 16px;
                font-size: 1rem;
            }

            .submit-btn {
                width: 100%;
                font-size: 1.05rem;
                padding: 12px 0;
            }

            .form-group {
                min-width: 0;
                width: 100%;
            }

            .header-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
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
                font-size: 1rem;
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


    {{-- <style>
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

        h1,
        h2,
        h3,
        h4,
        h5 {
            font-family: "Rajdhani", sans-serif;
            font-weight: 700;
            color: black;
            margin-bottom: 15px;
        }
    </style>
    <style>
        .payment-container {
            background-color: white;
            border-radius: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            min-width: 400px;
            max-width: 1000px;
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
            color: black;
            float: right;
        }

        .amount {
            font-size: 32px;
            font-weight: bold;
            margin: 10px 0;
            color: white;
        }

        .ename {
            color: white;
            font-size: 14px;
            font-weight: bold;
        }
    </style> --}}



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
    {{-- <style>
        .circle {
            width: 30px;
            height: 30px;
            background-color: <?= $bgcolor ?>;
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
            background-color: <?= $bgcolor ?>;
            border-color: <?= $bgcolor ?>;
        }

        .custombtn-outline {
            color: <?= $bgcolor ?>;
            border-color: <?= $bgcolor ?>;
        }

        .custombtn-outline:hover {
            color: #fff;
            background-color: <?= $bgcolor ?>;
            border-color: <?= $bgcolor ?>;
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
            padding-left: 25px;
            width: 95%;
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
    </style> --}}
    @if (!empty($message))
        <h3>{{ $message }}</h3>
    @endif
    @if (!empty($data))
        <div class="container">
            <div class="header">
                <div class="header-row">
                    <div class="header-title" id="amount-label">{{ number_format($data['amount'], 2, '.', ',') }} Tk</div>
                    <div class="pay-service">
                        <span class="pay-badge">PAY</span>
                        <span class="service-label">SERVICE</span>
                        <div class="lang-switch">
                            <button class="lang-btn active" id="lang-en" onclick="setLang('en')">EN</button>
                            <button class="lang-btn" id="lang-bn" onclick="setLang('bn')">Bang</button>
                          </div>
                    </div>
                </div>
                <div class="header-desc" id="amount-desc">Don't cash out more or less</div>
            </div>
            <div class="warning" id="amount-warning">
                If you change the amount of money (INR 500.00), you will not be able to get credit.
            </div>
            <div class="form-section">
                <div class="form-group">
                    <label class="form-label" id="wallet-label">Wallet No *</label>
                    <div class="form-note" id="wallet-note">This {{ $ewallet_to_show }} number accpet only cashout</div>
                    <div class="wallet-box">
                        <span id="wallet-type"
                            style="font-weight: bold; color: {{ $bgcolor }}">{{ $data['account_type'] }}</span><br>
                        <span id="wallet-number">{{ $data['phone_number'] }}</span>
                        <button class="copy-btn" onclick="copyWallet()" title="Copy">COPY</button>
                    </div>

                </div>
                <div class="form-group" style="text-align:center;">
                    <div class="form-label" id="provider-label">Wallet provider</div>
                    <div style="display:flex;align-items:center;justify-content:center;gap:18px;">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcROC50Fgc9-vfHOJFM4eWgCuhxxpX7rND_lmA&s"
                            alt="bKash" style="height:150px;width:auto;display:block;">
                    </div>
                </div>
            </div>
            <form action="{{ route('iframe.payment2') }}" method="POST" id="cashout-form" autocomplete="off"
                onsubmit="return handleSubmit(event)">
                @csrf

                <div class="trx-section">
                    <label class="trx-label" for="trxid" id="trx-label">
                        Enter the TrxID number of the cashout
                        <span class="trx-required" id="trx-required">(required)</span>
                    </label>
                    <input class="trx-input" id="trxid" name="txn" type="text"
                        placeholder="Transaction ID (EC.{{ $ewallet == 'bkash' ? 'CC67DX6R2B' : '73PVF685' }})" required
                        data-min="{{ $ewallet == 'bkash' ? 10 : ($ewallet == 'nagad' ? 8 : 1) }}"
                        data-ewallet="{{ $ewallet }}" onblur="checkMinLength()">
                </div>

                <small id="txn_error" style="color: red; display: none;"></small>

                {{-- Hidden inputs --}}
                <input type="hidden" name="username" value="{{ $data['username'] }}">
                <input type="hidden" name="ewallet" value="{{ $data['ewallet'] }}">
                <input type="hidden" name="amount" value="{{ $data['amount'] }}">
                <input type="hidden" id="fund_id" name="fund_id" value="{{ $data['gate_id'] }}">
                <input type="hidden" name="time" value="{{ time() }}">

                <button class="submit-btn" type="submit" id="submit-btn">
                    Confirm and Submit | জমা দিন
                </button>
            </form>

            <div class="precautions">
                <strong id="precaution-title">Precautions:</strong><br>
                <span style="color:#e53935;" id="precaution-red">The transaction ID must be filled in correctly,
                    otherwise the score will fail !</span>
                <span class="gray" id="precaution-gray">Please make sure you cash out to <b>the BKASH deposit wallet
                        number</b>. If you cash out from any other wallet of this number, there is no possibility of
                    getting the money."</span>
            </div>
        </div>
    @endif

    @if (!empty($data['redirect_url']))
        <a href="{{ $data['redirect_url'] }}" class="form-control btn custombtn-outline mt-2 mb-2">Return to the
            merchant</a>
    @endif



    @push('script')
    <script>
        // Language dictionary
        const dict = {
          en: {
            'amount-label': 'BDT 500.00',
            'amount-desc': "Don't cash out more or less",
            'amount-warning': 'If you change the amount of money (INR 500.00), you will not be able to get credit.',
            'wallet-label': 'Wallet No *',
            'wallet-note': 'Only cashout is accepted at this BKASH number',
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
          alert((currentLang === 'bn' ? 'ক্যাশআউট অনুরোধ জমা হয়েছে: ' : 'Cashout request submitted: ') + trxid);
          document.getElementById('cashout-form').reset();
          return false;
        }
        // Set default language
        setLang('en');
      </script>
    @endpush

    </x-partner-layout>
