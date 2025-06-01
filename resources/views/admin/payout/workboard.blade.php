<x-admin-layout :title="$pageTitle">
    @push('styles')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />



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

        #closeModal:hover {
            background-color: rgba(97, 96, 96, 0.137)
        }

    </style>
    @endpush
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
                            <input type="search" id="transaction-search" placeholder="TX / Ticket Number"
                                class="form-control form-control-sm search-box" />

                        </div>
                        <button class="btn btn-sm text-white btn-purple"
                            onclick=" $('#transactions-container').empty();">Close All</button>
                    </nav>

                    <!-- Cards Grid -->
                    <div class="row row-cols-2 g-2" id="transactions-container" style="margin-top: 1px;">
                        <!-- Cards will be appended here by JS -->
                    </div>


                    <div class="bg-red-400 mt-4 ">
                        <p class="text-White font-semibold text-lg">GATEWAY PERFORMACE MONITORING</p>
                        <div class=" h-full w-full" style="background-color: #504c79">
                            <p class="text-white fs-5 ms-4 px-2 pt-3">81% ~ 100%</p>
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
                        <div class="d-flex align-items-center p-2" style="background-color: #504c79;width:70%">
                            <p class="mb-0 me-2">SEARCH:</p>
                            <select name="search" id="api-search" class="form-control" style="width: 70%;">
                                <option value="">Select</option>
                                @foreach($apis as $api)
                                <option value="{{ $api->id }}">{{ $api->username }}</option>
                                @endforeach
                            </select>
                        </div>
                        <p id="api-balance" class="mt-5 fs-tiny" style="margin-left: 10px;">1,548,200.15 TK</p>
                    </div>

                </div>
            </div>
        </div>

    </div>
    <div class="modal modal-top fade" id="newModalb" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTopTitle">@lang('Send Callback')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addBalanceForm" action="{{ route('admin.run.deposit.callback') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row justify-content-between align-items-center">



                            <input type="text" hidden id="account_id" class="form-control" name="id">



                            <div class="col-md-12">
                                Callback Status
                                <span id="spinner2" style="display: none;">
                                    <span class="spinner-border text-primary" role="status">
                                    </span>
                                </span>
                                <span id="tickMark2" style="display: none;">
                                    <i class="fa fa-check-circle text-success"></i>
                                </span>
                                <span id="tickMark3" style="display: none;">
                                    <i class="fa fa-times-circle text-danger"></i>
                                </span>
                                <br>
                                <br>
                                <p>Message: <span id="text1"></span></p>
                                <br>
                                <div id="apiresponse" style="display: none;">
                                    <h4>Response</h4>
                                    <p>Response Code: <span id="text2"></span></p>
                                    <p>Response Body: </p>
                                    <div style="background-color: black;color:white;padding:10px"><span
                                            id="text3"></span></div>
                                </div>

                            </div>

                            <!-- <br>
                            <br> -->

                            <!-- <div class="col-md-12">
                                <button type="button" disabled id="runWithdrawalTest" class="btn btn-primary">Run Withdrawal Test</button>

                            </div> -->
                        </div>

                    </div>
            </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="activityModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Activity Log</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body table-responsive">
                    <table class="table table-bordered table-sm" id="activity-table" style="table-layout: fixed;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Method</th>
                                <th>URL</th>
                                <th style="width: 300px;">Request</th>
                                <th>Response</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Filled by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Transaction Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editTransactionForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Transaction</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editId" name="id">
                        <input type="hidden" id="editType" name="type">

                        <div class="mb-3">
                            <label>Sender Acc. No.</label>
                            <input class="form-control" name="sender" id="editSender" type="text" />
                        </div>

                        <div class="mb-3">
                            <label>E-Wallet No.</label>
                            <input class="form-control" name="e_wallet_phone_number" id="editEwallet" type="text"
                                required />
                        </div>

                        <div class="mb-3">
                            <label>Txn No.</label>
                            <input class="form-control" name="txn_id" id="editTxnId" type="text" />
                        </div>

                        <div class="mb-3">
                            <label>E-Wallet Type</label>
                            <select class="form-select" name="e_wallet_type" id="editEwalletType">
                                <option value="Personal">Personal</option>
                                <option value="Merchant">Merchant</option>
                            </select>
                        </div>

                        <input type="hidden" name="status" value="Complete" />

                        <div class="mb-3">
                            <label>Payment Receiving DateTime</label>
                            <input class="form-control" name="date_time" id="editDateTime" type="datetime-local"
                                value="{{ date('Y-m-d\TH:i') }}" required />
                        </div>
                        <button class="btn btn-primary mt-2">@lang('Approve')</button>
                    </div>
                </form>
                <form id="editTransactionForm1">
                    @csrf
                    <div class="modal-footer">
                        <input type="hidden" id="editrejectId" name="id">
                        <input type="hidden" name="status" value="Reject">
                        <input type="hidden" id="editrejectType" name="type">
                        <button type="submit" class="btn btn-danger">@lang('Reject')</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="modal modal-top fade" id="myModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTopTitle">@lang('Payout Information')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form role="form" method="POST" class="actionRoute" id="actionRoutee" action=""
                    enctype="multipart/form-data" onsubmit="submitForm(this)">
                    @csrf
                    @method('put')
                    <div class="modal-body">
                        <ul class="list-group withdraw-detail">
                        </ul>

                        {{-- @if(Request::routeIs('admin.payout-request')) --}}

                        <div class="form-group addForm">

                        </div>
                        {{-- @endif --}}

                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="status" name="status">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')
                        </button>

                        <input type="hidden" class="action_id" name="id">
                        <div id="submit1" style="display: none;">
                            <button type="submit" id="btn2" class="btn btn-primary" name="status"
                                value="2">@lang('Approve')</button>
                        </div>
                        <div id="submit2" style="display: none;">
                            <button type="submit" id="btn4" class="btn btn-dark" name="status" value="4">@lang('Mark As
                                Complete')</button>
                        </div>
                        <div id="submit4" style="display: none;">
                            <button type="submit" id="btn5" class="btn btn-warning" name="status" value="5">@lang('Mark
                                As Pending')</button>
                        </div>
                        <div id="submit3" style="display: none;">
                            <button type="submit" id="btn3" class="btn btn-danger" name="status"
                                value="3">@lang('Reject')</button>
                        </div>

                    </div>

                </form>


            </div>
        </div>
    </div>

    <!-- Manual Process Modal -->
    <div class="modal fade" id="manualProcessModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="manualProcessForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Manual Adjustment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="original_id" id="original_id">
                        <input type="hidden" name="type" id="type">

                        <div class="mb-3">
                            <label for="new_amount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="new_amount" name="new_amount" required>
                        </div>

                        <div class="mb-3">
                            <label for="txn_amount" class="form-label">Txn Number</label>
                            <input type="number" class="form-control" id="txn_amount" name="txn_amount" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    @push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        (function (jQuery) {

            jQuery(document).ready(function () {
                jQuery(document).on("click", '.edit_button', function (e) {
                    var id = jQuery(this).data('id');
                    jQuery(".action_id").val(id);
                    jQuery(".actionRoute").attr('action', jQuery(this).data('route'));
                    // var details = Object.entries(jQuery(this).data('info'));
                    var list = [];
                    var ImgPath = "{{ asset(config('location.withdrawLog.path')) }}";
                    // details.map(function (item, i) {
                    //     if (item[1].type == 'file') {
                    //         var singleInfo = `<br><img src="${ImgPath}/${item[1].field_name}" alt="..." class="w-50">`;
                    //     } else {
                    //         var singleInfo = `<span class="font-weight-bold ml-3">${item[1].field_name}</span>`;
                    //     }
                    //     list[i] = `<li class="list-group-item"><span class="font-weight-bold">${item[0].replace('_', " ")}</span> : ${singleInfo}</li>`;
                    // });

                    console.log(jQuery(this).data('status'));

                    if (jQuery(this).data('status') == '2') {
                        jQuery('#submit1').hide();
                        jQuery('#submit2').show();
                        jQuery('#submit3').show();
                    } else if (jQuery(this).data('status') == '3') {
                        jQuery('#submit1').hide();
                        jQuery('#submit2').hide();
                        jQuery('#submit3').hide();
                    } else {
                        jQuery('#submit1').show();
                        jQuery('#submit2').hide();
                        jQuery('#submit3').show();
                    }

                    if (jQuery(this).data('statusb') == 'Complete') {
                        jQuery('#submit4').show();
                        jQuery('#submit2').hide();
                    } else {
                        jQuery('#submit4').hide();
                    }

                    // list[details.length + 1] = ``;

                    jQuery('.addForm').html(`
                    <div class="form-group">
                        <label for="feedback">@lang('Remarks')</label>
                        <select class="form-control" name="feedback" id="feedback">
                            <option value="">@lang('Select Feedback')</option>
                            <option value="invalid_phone_number">@lang('Invalid phone number')</option>
                            <option value="account_limit_over">@lang('Account limit over')</option>
                            <option value="kyc_incomplete">@lang('Customer account did not complete KYC')</option>
                            <option value="nagad_server_down">@lang('Nagad server down')</option>
                            <option value="bkash_server_down">@lang('bKash server down')</option>
                            <option value="rocket_server_down">@lang('Rocket server down')</option>
                            <option value="others">@lang('Others')</option>
                        </select>
                    </div>
                `);

                    jQuery('.withdraw-detail').html(list);
                });
            });

            jQuery(document).on("click", '.edit_buttonc', function (e) {
                var id = jQuery(this).data('id');
                var e_wallet_phone_number = jQuery(this).data('e_wallet_phone_number');

                jQuery(".action_id").val(id);
                jQuery(".e_wallet_phone_number").val(e_wallet_phone_number);
            });

        })(jQuery);

    </script>
    <script>
        $(document).ready(function () {
            fetchTransactions();

            $('#editTransactionForm').submit(function (e) {
                e.preventDefault();

                const id = $('#editId').val();
                const type = $('#editType').val();
                const formData = $(this).serialize();

                let url = (type === 'payment') ?
                    "{{ route('admin.update.payment') }}" :
                    "{{ route('admin.update.payout') }}";

                $.post(url, formData, function (res) {
                    $('#editModal').modal('hide');
                    fetchTransactions(); // Refresh the card list
                }).fail(() => alert('Something went wrong. Please try again.'));
            });

            $('#editTransactionForm1').submit(function (e) {
                e.preventDefault();

                const id = $('#editrejectId').val();
                const type = $('#editrejectType').val();
                const formData = $(this).serialize();

                let url = (type === 'payment') ?
                    "{{ route('admin.update.payment') }}" :
                    "{{ route('admin.update.payout') }}";

                $.post(url, formData, function (res) {
                    $('#editModal').modal('hide');
                    fetchTransactions(); // Refresh the card list
                }).fail(() => alert('Something went wrong. Please try again.'));
            });

            // call every 1 minute
            setInterval(function () {
                console.log('Fetching transactions...');
                fetchTransactions(); // fetch the transactions every 1 minute
            }, 60000); // 60000 ms = 1 minute

            $('#manualProcessForm').on('submit', function (e) {
                e.preventDefault();

                const originalId = $('#original_id').val();
                const newAmount = $('#new_amount').val();
                const txnamount = $('#txn_amount').val();
                const type = $('#type').val();

                $.ajax({
                    url: "{{ route('admin.manual-process') }}", // Replace with your actual route
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        original_id: originalId,
                        new_amount: newAmount,
                        txn_amount: txnamount,
                        type: type
                    },
                    success: function (response) {
                        $('#manualProcessModal').modal('hide');
                        alert('New record added successfully!');
                        $('#manualProcessModal').find('input').val('');
                        fetchTransactions();
                        // Optional: reload or update your data table here
                    },
                    error: function (xhr) {
                        alert('Something went wrong. Please try again.');
                    }
                });
            });

            $('#transaction-search').on('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault(); // Optional: prevent form submission if inside a form
                    const searchValue = $(this).val();
                    $('#transactions-container').empty();
                    fetchTransactions(searchValue);
                }
            });


            function fetchTransactions(search = '', source = 'all') {
                $.ajax({
                    url: "{{ route('admin.workboard') }}", // your route
                    method: "GET",
                    dataType: "json",
                    data: {
                        search: search,
                        source: source // add source here
                    },
                    success: function (response) {
                        // Render Transactions
                        fetchNotifications(response.notifications);
                        fetchPendingList(response.pending_list);
                        $('#transactions-container').empty(); // clear previous if needed
                        $.each(response.transactions, function (index, transaction) {
                            $(document).off('click', '.edit-btn').on('click', '.edit-btn',
                                function () {
                                    const transaction = $(this).data(
                                        'transaction'); // set below
                                    console.log(transaction);
                                    $('#editId').val(transaction.id);
                                    $('#editrejectId').val(transaction.id);
                                    $('#editType').val(transaction.type);
                                    $('#editrejectType').val(transaction.type);
                                    $('#editSender').val(transaction.sender || '');
                                    $('#editEwallet').val(transaction
                                        .e_wallet_phone_number || '');
                                    $('#editTxnId').val(transaction.txn_id || '');
                                    $('#editEwalletType').val(transaction
                                        .e_wallet_type || 'Personal');
                                    $('#editDateTime').val(transaction.date_time ||
                                        new Date().toISOString().slice(0, 16));
                                    $('#editModal').modal('show');
                                });



                            const currentUserId = response.user_id;
                            let showAdjustment = transaction.adjusted_by == null ? '' :
                                'd-none';
                            let typeLabel = transaction.type === 'payment' ? 'DEPOSIT' :
                                'WITHDRAWL';
                            let statusColor = transaction.status === 'pending' ?
                                'text-warning' : 'text-success';
                            let showEdit = transaction.adjusted_by == currentUserId ? '' :
                                'd-none';
                            let editButton = '';

                            if (transaction.type === 'payment') {
                                editButton = `
                    <button class="px-4 btn btn-sm edit-btn ${showEdit}" data-transaction='${JSON.stringify(transaction)}' style="background-color: rgb(124, 3, 180); color: white;">
                        Edit
                    </button>`;
                            } else if (transaction.type === 'payout') {
                                const details = transaction.information ? JSON.stringify(
                                        transaction.information).replace(/"/g, '&quot;') :
                                    '';
                                const feedback = transaction.feedback || '';
                                const status = transaction.transfer_status || '';
                                const statusb = transaction.status || '';

                                // Use a route pattern or inject it via JavaScript context if needed
                                const payoutRoute =
                                    `/admin/payout-action/${transaction.id}`; // Must match your Laravel route

                                editButton = `
                    <button type="button" class="btn btn-sm edit_button ${showEdit}" style="background-color: rgb(124, 3, 180); color: white;"
                        data-bs-toggle="modal"
                        data-bs-target="#myModal"
                        data-route="${payoutRoute}"
                        data-feedback="${feedback}"
                        data-info="${details}"
                        data-id="${transaction.id}"
                        data-status="${status}"
                        data-statusb="${statusb}">
                        Edit P
                    </button>`;
                            }

                            let card = `
                        <div class="col transaction-card" data-id="${transaction.id}" data-type="${transaction.type}">
                            <div class="custom-card p-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="d-flex gap-4">
                                        <p class="text-success fw-semibold mb-1">${typeLabel}</p>
                                        <p class="text-success fw-semibold mb-1">${transaction.amount} TK</p>
                                        <p class="text-white mb-1">${transaction.id }</p>
                                    </div>
                                    <div class="d-flex gap-3 text-white">
                                        <i class="bi bi-arrow-repeat"></i>
                                        <button type="button" class="btn-close" aria-label="Close" data-id="${transaction.id}" data-type="${transaction.type}"></button>
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
                                    <button class="px-4 btn btn-sm" style="background-color: rgb(52, 152, 235); color: white;" data-bs-toggle="modal" data-bs-target="#newModalb" onclick="setBalanceItem(${transaction.id})">Resend</button>
                                    <button
        class="px-4 btn btn-sm activity-btn"
        style="background-color: blue; color: white;"
        data-partner-id="${transaction.partner_transection_id}">
        Activity
        </button>

                                </div>
                                <div class="d-flex gap-4 mt-3">
                                    ${editButton}
                                    <button class="px-4 btn btn-sm manual-process-btn"
    style="background-color: rgb(226, 15, 15); color: white;"
    data-id="${transaction.id}"
    data-type="${transaction.type}">
    Adjustment
</button>

                                    <button class="px-4 btn btn-sm btn-adjustment ${showAdjustment}" style="background-color: rgb(124, 3, 180); color: white;">Manual Process</button>
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

            $(document).on('fetchTransactions', function (e, searchValue, source) {
    fetchTransactions(searchValue, source);
});

            function renderEwallets(ewallets) {
                // Group by ewallet name (like bKash, Nagad, Rocket)
                let ewalletGroups = {};

                ewallets.forEach(function (account) {
                    if (!ewalletGroups[account.e_wallet_name]) {
                        ewalletGroups[account.e_wallet_name] = [];
                    }
                    ewalletGroups[account.e_wallet_name].push(account);
                });

                let buttonsHtml = '';
                let accountDetailsHtml = '';

                $.each(ewalletGroups, function (walletName, accounts) {
                    buttonsHtml += `
            <button class="px-4 btn btn-sm ewallet-btn"
                    data-wallet="${walletName}"
                    style="background-color: ${getRandomColor()}; color: white; border: none; cursor: pointer;">
                ${walletName.toUpperCase()} (${accounts.length})
            </button>
        `;

                    accounts.forEach(function (account) {
                        accountDetailsHtml += `
                <p class="wallet-data" data-wallet="${walletName}" style="display:none;">
                    ${walletName}: ${account.account_no} Current Balance = ${account.balance}TK
                </p>
            `;
                    });
                });

                $('#ewallet-buttons').html(buttonsHtml);
                $('#ewallet-details').html(accountDetailsHtml);

                // Use delegated event to toggle visibility
                $('#ewallet-buttons').off('click').on('click', '.ewallet-btn', function () {
                    let wallet = $(this).data('wallet');
                    let $walletData = $(`.wallet-data[data-wallet="${wallet}"]`);

                    if ($walletData.is(':visible')) {
                        $walletData.hide();
                    } else {
                        $('.wallet-data').hide(); // hide all
                        $walletData.show(); // show current
                    }
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
            $.each(notifications, function (index, notification) {
                // Calculate the time difference in minutes
                const createdAt = new Date(notification
                    .created_at); // Assuming created_at is provided in the notification object
                const currentTime = new Date();
                const timeDiffInMs = currentTime - createdAt;
                const timeDiffInMin = Math.floor(timeDiffInMs / 60000); // Convert ms to minutes

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
            $('#pending-list-container').empty();
            let currentUser = "{{ Auth::user()->name }}";
            let currentUserId = "{{ Auth::id() }}"; // for updating `adjusted_by`

            $.each(pendingList, function (index, item) {
                let createdAt = new Date(item.created_at);
                let now = new Date();
                let diffMs = now - createdAt;
                let diffMins = Math.floor(diffMs / 60000);
                let timeAgo = diffMins > 0 ? `${diffMins} min ago` : 'Just now';

                let pendingHtml = `
        <div class="w-full py-2 px-4 items-center text-white d-flex justify-content-between"
             style="background-color: #504c79;">
            <div>
                <p>${item.id} [Order Number]:${item.user_account_no}  ${item.amount} TK</p>
                <p>Account: ${item.e_wallet_name}</p>
                <p>Checking by: ${currentUser}</p>
            </div>
            <div>
                <button class="px-4 btn btn-sm check-btn"
                    data-txn="${item.txn_id}"
                    style="background-color: rgb(45, 199, 58); margin-left: 2rem; color: white; border: none; cursor: pointer;">
                    Check
                </button>
                <p class="mt-10">${timeAgo}</p>
            </div>
        </div>
    `;
                $('#pending-list-container').append(pendingHtml);
            });

            // Attach click handler after rendering
            $('.check-btn').on('click', function () {
                let txnId = $(this).data('txn');
                alert(txnId);
                $('#transaction-search').val(txnId); // set in search input

                // Update adjusted_by
                $.ajax({
                    url: 'update-adjusted-by', // your route
                    type: 'POST',
                    data: {
                        txn_id: txnId,
                        adjusted_by: currentUserId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        // Re-run the search
                        $('#transactions-container').empty();
                        $(document).trigger('fetchTransactions', [txnId, 'payout']);
                        alert('done');
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                    }
                });
            });
        }

    </script>

    <script>
        jQuery(document).off('click', '.activity-btn').on('click', '.activity-btn', function () {
            var partnerId = jQuery(this).data('partner-id');

            jQuery.ajax({
                url: '{{ route("admin.fetchActivityLogs") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    partner_transaction_id: partnerId
                },
                success: function (response) {
                    var tbody = '';
                    jQuery.each(response.data, function (index, log) {
                        tbody += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${log.request_method}</td>
                        <td>${log.request_url}</td>
                        <td>${log.request_payload}</td>
                        <td>${log.response_payload}</td>
                        <td>${log.created_at}</td>
                    </tr>
                `;
                    });

                    jQuery('#activity-table tbody').html(tbody);
                    var modal = new bootstrap.Modal(document.getElementById('activityModal'));
                    modal.show();
                }
            });
        });



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
            $('#api-search').select2({
                width: '100%',
                selectOnClose: true
            });
            $('#api-search').on('change', function () {
                let apiId = $(this).val();

                if (apiId) {
                    $.ajax({
                        url: 'get-api-balance/' + apiId,
                        type: 'GET',
                        success: function (response) {
                            // Assuming response has { balance: 1234.56 }
                            $('#api-balance').text(response.balance + ' TK');
                        },
                        error: function () {
                            $('#api-balance').text('Error fetching balance');
                        }
                    });
                } else {
                    $('#api-balance').text('0.00 TK');
                }
            });

        });

    </script>
    <script>
        function setBalanceItem(itemId) {
            var account_id = document.getElementById("account_id");
            account_id.value = itemId;

            jQuery('#spinner2').show();
            jQuery('#runWithdrawalTest').prop('disabled', true);

            var formData = new FormData(jQuery('#addBalanceForm')[0]); // Get form data

            jQuery.ajax({
                type: "POST",
                url: "{{ route('admin.run.deposit.callback') }}",
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                data: formData, // Use FormData object
                processData: false, // Don't process the data
                contentType: false, // Don't set contentType
                success: function (response) {
                    if (response.status === "success") {
                        jQuery('#spinner2').hide();
                        jQuery('#tickMark2').show();
                        jQuery('#apiresponse').show();
                    } else {
                        jQuery('#spinner2').hide();
                        jQuery('#tickMark3').show();
                        jQuery('#apiresponse').hide();
                    }

                    document.getElementById("text1").innerText = response.message;
                    document.getElementById("text2").innerText = response.code;
                    document.getElementById("text3").innerText = response.response_payload;
                },
                error: function (xhr, status, error) {
                    jQuery('#spinner2').hide();
                    jQuery('#tickMark3').show();
                    jQuery('#apiresponse').hide();

                    document.getElementById("text1").innerText =
                        'An error occurred while processing your request. Please try again.';
                    document.getElementById("text2").innerText = '';
                    document.getElementById("text3").innerText = '';
                }
            });
        }

        $('#transactions-container').on('click', '.btn-close', function () {
            const id = $(this).data('id');
            const type = $(this).data('type');
            const card = $(this).closest('.col');

            $.ajax({
                url: "{{ route('admin.hideTransaction') }}", // using route name
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    type: type
                },
                success: function (response) {
                    if (response.success) {
                        card.remove();
                    } else {
                        alert(response.message || 'Failed to hide transaction');
                    }
                },
                error: function () {
                    alert('Error occurred while hiding the transaction.');
                }
            });
        });

        $(document).on('click', '.btn-adjustment', function () {
            const card = $(this).closest('.transaction-card');
            const id = card.data('id');
            const type = card.data('type');
            const adjustmentBtn = $(this);
            const editBtn = card.find('.edit-btn');
            const edit_button = card.find('.edit_button');

            $.ajax({
                url: "{{ route('admin.adjust.transaction') }}", // you'll create this route
                method: 'POST',
                data: {
                    id: id,
                    type: type,
                    _token: "{{ csrf_token() }}"
                },
                success: function () {
                    adjustmentBtn.addClass('d-none');
                    editBtn.removeClass('d-none');
                    edit_button.removeClass('d-none');
                }
            });
        });


        $(document).on('click', '.manual-process-btn', function () {
            const id = $(this).data('id');
            const type = $(this).data('type');

            $('#original_id').val(id);
            $('#type').val(type);
            $('#manualProcessModal').modal('show');
        });

    </script>


    @endpush
</x-admin-layout>
