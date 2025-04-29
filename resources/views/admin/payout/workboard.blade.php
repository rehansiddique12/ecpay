<x-admin-layout :title="$pageTitle">
    <style>
        .left-panel {
            width: 70%;
            padding: 10px;
            margin-top: px;
            background-color: ;
            /* Bootstrap red */
        }

        .right-panel {
            width: 30%;
            padding: px;
            background-color: ;
        }

        .nav-box {
            width: 100%;
            padding: 0.5rem 1.5rem;
            margin-top: 1rem;
        }

        .search-box {
            width: 250px;
            background-color: transparent;
            border: 1px solid #ccc;
            color: #ccc;
        }

        .btn-purple {
            background-color: #7367f0;
        }

        .custom-card {
            background-color: #504c79;
            padding: 1rem;
        }

        .custom-box {
            background-color: #504c79;
            height: 5rem;
        }

        .custom-box1 {
            background-color: #504c79;

        }
    </style>
    <div class="container-xxl flex-grow-1 container-p-y">
        <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
        {{-- <p>Page Title: {{ $pageTitle }}</p> --}}
        <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
            <div class="container-fluid  text-white d-flex p-0">
                <!-- Left Panel -->
                <div class="left-panel">
                    <nav class="custom-box1 nav-box d-flex justify-content-between align-items-center text-light">
                        <div class="d-flex align-items-center">
                            <p class="mb-0 me-2">SEARCH:</p>
                            <input type="search" placeholder="TX / Ticket Number"
                                class="form-control form-control-sm search-box" />
                        </div>
                        <button class="btn btn-sm text-white btn-purple" onclick=" $('#transactions-container').empty();">Close All</button>
                    </nav>

                    <!-- Cards Grid -->
                    <div class="row row-cols-2 g-2" id="transactions-container" style="margin-top: 1px;">
                        <!-- Cards will be appended here by JS -->
                    </div>


                    <div class="bg-red-400 mt-4 ">
                        <p class="text-White font-semibold text-lg">GATEWAY PERFORMACE MONITORING</p>
                        <div class=" h-full w-full" style="background-color: #504c79">
                            <p class="text-white fs-5 ms-4 px-2 pt-3" >81% ~ 100%</p>
                            <div style=" background-color: #7570a0;">
                                <div class="d-flex gap-5 px-4 pt-4">
                                <p class="text-White font-semibold text-md">MERCHANT</p>
                                <p class="text-White font-semibold text-md">SUCCESS RATE</p>
                                <p class="text-White font-semibold text-md">TOTAL RECEIVED</p>
                                <p class="text-White font-semibold text-md">TOTAL PROCESSED</p>
                                <p class="text-White font-semibold text-md">AUTO PROCESS</p>
                                <p class="text-White font-semibold text-md">MANUAL PROCESS</p>
                                </div>
                                <fieldset class="w-100 border-top border-2 mb-4 border-white"></fieldset>
                                <div class="h-16">
                                    <div class="d-flex gap-10 px-4">
                                        <p class="text-White ">MERCHANT A</p>
                                        <p class="text-White">82%</p>
                                        <p class="text-White" style="margin-left: 4.5rem;">5,834</p>
                                        <p class="text-White" style="margin-left: 4rem;">5,245</p>
                                        <p class="text-White" style="margin-left: 4rem;">5,200</p>
                                        <p class="text-White" style="margin-left: 4rem;">45</p>
                                    </div>
                                    <div class="d-flex gap-10 px-4  ">
                                        <p class="text-White ">MERCHANT B</p>
                                        <p class="text-White" style="margin-left:">87%</p>
                                        <p class="text-White" style="margin-left: 4rem;">6,482</p>
                                        <p class="text-White" style="margin-left: 4rem;">6,158</p>
                                        <p class="text-White" style="margin-left: 4rem;">6,000</p>
                                        <p class="text-White" style="margin-left: 4rem;">158</p>
                                    </div>
                                </div>
                            </div>
                            <p class="text-white fs-5 ms-4 px-2 mt-3">61% ~ 80%</p>
                            <div style=" background-color: #7570a0;">
                                <div class="d-flex gap-5 px-4 pt-4">
                                <p class="text-White font-semibold text-md">MERCHANT</p>
                                <p class="text-White font-semibold text-md">SUCCESS RATE</p>
                                <p class="text-White font-semibold text-md">TOTAL RECEIVED</p>
                                <p class="text-White font-semibold text-md">TOTAL PROCESSED</p>
                                <p class="text-White font-semibold text-md">AUTO PROCESS</p>
                                <p class="text-White font-semibold text-md">MANUAL PROCESS</p>
                                </div>
                                <fieldset class="w-100 border-top border-2 mb-4 border-white"></fieldset>
                                <div class="h-16">
                                    <div class="d-flex gap-10 px-4">
                                        <p class="text-White ">MERCHANT C</p>
                                        <p class="text-White">75%</p>
                                        <p class="text-White" style="margin-left: 4.5rem;">5,834</p>
                                        <p class="text-White" style="margin-left: 4rem;">5,245</p>
                                        <p class="text-White" style="margin-left: 4rem;">5,200</p>
                                        <p class="text-White" style="margin-left: 4rem;">45</p>
                                    </div>
                                    <div class="d-flex gap-10 px-4  ">
                                        <p class="text-White ">MERCHANT D</p>
                                        <p class="text-White" style="margin-left:">62%</p>
                                        <p class="text-White" style="margin-left: 4rem;">6,482</p>
                                        <p class="text-White" style="margin-left: 4rem;">6,158</p>
                                        <p class="text-White" style="margin-left: 4rem;">6,000</p>
                                        <p class="text-White" style="margin-left: 4rem;">158</p>
                                    </div>
                                </div>
                            </div>
                            <p class="text-white fs-5 ms-4 px-2 mt-3">1% ~60%</p>
                            <div style=" background-color: #7570a0;">
                                <div class="d-flex gap-5 px-4 pt-4">
                                <p class="text-White font-semibold text-md">MERCHANT</p>
                                <p class="text-White font-semibold text-md">SUCCESS RATE</p>
                                <p class="text-White font-semibold text-md">TOTAL RECEIVED</p>
                                <p class="text-White font-semibold text-md">TOTAL PROCESSED</p>
                                <p class="text-White font-semibold text-md">AUTO PROCESS</p>
                                <p class="text-White font-semibold text-md">MANUAL PROCESS</p>
                                </div>
                                <fieldset class="w-100 border-top border-2 mb-4 border-white"></fieldset>
                                <div class="h-16">
                                    <div class="d-flex gap-10 px-4">
                                        <p class="text-White ">MERCHANT E</p>
                                        <p class="text-White">55%</p>
                                        <p class="text-White" style="margin-left: 4.5rem;">5,834</p>
                                        <p class="text-White" style="margin-left: 4rem;">5,245</p>
                                        <p class="text-White" style="margin-left: 4rem;">5,200</p>
                                        <p class="text-White" style="margin-left: 4rem;">45</p>
                                    </div>
                                    <div class="d-flex gap-10 px-4  ">
                                        <p class="text-White ">MERCHANT F</p>
                                        <p class="text-White" style="margin-left:">48%</p>
                                        <p class="text-White" style="margin-left: 4rem;">6,482</p>
                                        <p class="text-White" style="margin-left: 4rem;">6,158</p>
                                        <p class="text-White" style="margin-left: 4rem;">6,000</p>
                                        <p class="text-White" style="margin-left: 4rem;">158</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Panel -->
                <div class="right-panel">
                    <div class="bg-[#504c79] w-full py-3  px-4 justify-content-between items-center text-White"
                    style="margin-top: 3.6rem; background-color: #504c79;">
                   <div class="d-flex gap-4 mb-3" id="ewallet-buttons">
                       <!-- Buttons will come here -->
                   </div>
                   <div id="ewallet-details">
                       <!-- Wallet details will appear here -->
                   </div>
               </div>

                    <p class="pt-4">NOTIFICATION CENTER</p>
                    <div id="notifications-container"></div>

                    <p class="pt-4">WITHDRAWAL PENDING LIST (5 MINUTES)</p>
                    <div id="pending-list-container"></div>

                    <p class="pt-4">Check Balance</p>
                    <div class="d-flex">
                        <div class="d-flex align-items-center p-2" style="background-color: #504c79">
                            <p class="mb-0 me-2">SEARCH:</p>
                            <input type="search" placeholder="PKLUC" class="form-control form-control-sm search-box"
                                style="width: 70%" />
                        </div>
                        <p class="mt-5 fs-tiny" style="margin-left: 10px;">1,548,200.15 TK</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
    @push('js')
    <script src="{{ asset('public/assets/js/select2.min.js')}}"></script>
<script>


$(document).ready(function() {
    fetchTransactions();

// call every 1 minute
setInterval(function() {
    console.log('Fetching transactions...');
    fetchTransactions(); // fetch the transactions every 1 minute
}, 60000); // 60000 ms = 1 minute

    function fetchTransactions() {
        $.ajax({
            url: "{{ route('admin.workboard') }}", // your route
            method: "GET",
            dataType: "json",
            success: function(response) {
                // Render Transactions
                fetchNotifications(response.notifications);
                fetchPendingList(response.pending_list);
                $('#transactions-container').empty(); // clear previous if needed
                $.each(response.transactions, function(index, transaction) {
                    let typeLabel = transaction.type === 'payment' ? 'DEPOSIT' : 'WITHDRAWL';
                    let statusColor = transaction.status === 'pending' ? 'text-warning' : 'text-success';

                    let card = `
                        <div class="col">
                            <div class="custom-card p-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="d-flex gap-4">
                                        <p class="text-success fw-semibold mb-1">${typeLabel}</p>
                                        <p class="text-success fw-semibold mb-1">${transaction.amount} TK</p>
                                        <p class="text-white mb-1">${transaction.id}</p>
                                    </div>
                                    <div class="d-flex gap-3 text-white">
                                        <i class="bi bi-arrow-repeat"></i>
                                        <button type="button" class="btn-close" aria-label="Close"></button>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-3">
                                    <p class="mb-0">Order Number: ABCD1234</p>
                                    <p class="mb-0 ${statusColor} fw-semibold">STATUS: <span>${transaction.status.toUpperCase()}</span></p>
                                </div>

                                <div class="d-flex gap-5 mt-2">
                                    <p class="mb-0">Account Name: NAGAD</p>
                                    <p class="">01810665588</p>
                                </div>
                                <div>
                                    <p class="">Location: Office 1</p>
                                    <p class="">Created At: ${new Date(transaction.created_at).toLocaleString()}</p>
                                    <p class="">Updated At: ${new Date(transaction.created_at).toLocaleString()}</p>
                                    <p class="">Input Transaction Number: CB34653AS1</p>
                                    <p class="">Verified Transaction Number:</p>
                                </div>

                                <div class="d-flex gap-4 mt-3">
                                    <div class="justify-content-center">
                                        <p class="">Callback Status: Null</p>
                                    </div>
                                    <button class="px-4 btn btn-sm" style="background-color: rgb(52, 152, 235); color: white;">Resend</button>
                                    <button class="px-4 btn btn-sm" style="background-color: blue; color: white;">Activity</button>
                                </div>
                                <div class="d-flex gap-4 mt-3">
                                    <button class="px-4 btn btn-sm" style="background-color: rgb(45, 199, 58); color: white;">Edit</button>
                                    <button class="px-4 btn btn-sm" style="background-color: rgb(226, 15, 15); color: white;">Manual Process</button>
                                    <button class="px-4 btn btn-sm" style="background-color: rgb(124, 3, 180); color: white;">Adjustment</button>
                                </div>
                            </div>
                        </div>
                    `;
                    $('#transactions-container').append(card);
                });

                // Render Ewallets
                renderEwallets(response.ewallets);
            }
        });
    }

    function renderEwallets(ewallets) {
        // Group by ewallet name (like bKash, Nagad, Rocket)
        let ewalletGroups = {};

        ewallets.forEach(function(account) {
            if (!ewalletGroups[account.e_wallet_name]) {
                ewalletGroups[account.e_wallet_name] = [];
            }
            ewalletGroups[account.e_wallet_name].push(account);
        });

        let buttonsHtml = '';
        let accountDetailsHtml = '';

        $.each(ewalletGroups, function(walletName, accounts) {
            buttonsHtml += `
                <button class="px-4 btn btn-sm ewallet-btn"
                        data-wallet="${walletName}"
                        style="background-color: ${getRandomColor()}; color: white; border: none; cursor: pointer;">
                    ${walletName.toUpperCase()} (${accounts.length})
                </button>
            `;

            accounts.forEach(function(account) {
                accountDetailsHtml += `
                    <p class="wallet-data" data-wallet="${walletName}" style="display:none;">
                        ${walletName}: ${account.account_no} Current Balance = ${account.balance}TK
                    </p>
                `;
            });
        });

        $('#ewallet-buttons').html(buttonsHtml);
        $('#ewallet-details').html(accountDetailsHtml);

        // Click event to filter details
        $('.ewallet-btn').click(function() {
            let wallet = $(this).data('wallet');
            $('.wallet-data').hide();
            $(`.wallet-data[data-wallet="${wallet}"]`).show();
        });
    }

    function getRandomColor() {
        // Optional: Generate a random color for each button
        const colors = [
            "rgb(45, 199, 58)",
            "rgb(226, 213, 30)",
            "rgb(168, 32, 196)",
            "rgb(52, 152, 235)",
            "rgb(255, 99, 71)",
            "rgb(100, 149, 237)"
        ];
        return colors[Math.floor(Math.random() * colors.length)];
    }
});

function fetchNotifications(notifications) {
    $('#notifications-container').empty(); // clear previous
    $.each(notifications, function(index, notification) {
        // Calculate the time difference in minutes
        const createdAt = new Date(notification.created_at); // Assuming created_at is provided in the notification object
        const currentTime = new Date();
        const timeDiffInMs = currentTime - createdAt;
        const timeDiffInMin = Math.floor(timeDiffInMs / 10000); // Convert ms to minutes

        // Determine the display text for time difference
        let timeAgo = timeDiffInMin === 0 ? "Just now" : `${timeDiffInMin} min ago`;

        let notifHtml = `
            <div class="w-full py-2 px-2 items-center text-white d-flex justify-content-between"
                 style="background-color: #504c79;">
                <p>Warning!! ${notification.e_wallet_name} ${notification.account_no} <br>balance is low.</p>
                <div>
                    <button type="button" class="btn-close" style="margin-left: 3rem;" aria-label="Close"></button>
                    <p>${timeAgo}</p>
                </div>
            </div>
        `;
        $('#notifications-container').append(notifHtml);
    });
}


function fetchPendingList(pendingList) {
    $('#pending-list-container').empty(); // clear previous
    let currentUser = "{{ Auth::user()->name }}";
    $.each(pendingList, function(index, item) {
        let createdAt = new Date(item.created_at);
        let now = new Date();
        let diffMs = now - createdAt; // time difference in milliseconds
        let diffMins = Math.floor(diffMs / 60000); // convert to minutes

        let timeAgo = diffMins > 0 ? `${diffMins} min ago` : 'Just now';

        let pendingHtml = `
            <div class="w-full py-2 px-4 items-center text-white d-flex justify-content-between"
                 style="background-color: #504c79;">
                <div>
                    <p>${item.id} [Order Number]: ${item.amount} TK</p>
                    <p>Account: ${item.account_name} ${item.account_number}</p>
                    <p>Checking by: ${currentUser}</p>
                </div>
                <div>
                    <button class="px-4 btn btn-sm"
                        style="background-color: rgb(45, 199, 58); margin-left: 2rem; color: white; border: none; cursor: pointer;">Check</button>
                    <p class="mt-10">${timeAgo}</p>
                </div>
            </div>
        `;
        $('#pending-list-container').append(pendingHtml);
    });
}




</script>

    <script>
        "use strict";
        $(document).ready(function (e) {


            $('#image').change(function () {
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#image_preview_container').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });


        });

        $(document).ready(function () {
            $('select').select2({
                selectOnClose: true
            });
        });

    </script>
    @endpush
</x-admin-layout>
