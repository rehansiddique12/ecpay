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
                    <p class="fs-4 fw-bold">WORKBOARD</p>

                    <nav class="custom-box1 nav-box d-flex justify-content-between align-items-center text-light">
                        <div class="d-flex align-items-center">
                            <p class="mb-0 me-2">SEARCH:</p>
                            <input type="search" placeholder="TX / Ticket Number"
                                class="form-control form-control-sm search-box" />
                        </div>
                        <button class="btn btn-sm text-white btn-purple">Close All</button>
                    </nav>

                    <!-- Cards Grid -->
                    <div class="row row-cols-2 g-2" style="margin-top: 1px">
                        <div class="col">
                            <div class="custom-card p-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="d-flex gap-4">
                                        <p class="text-success fw-semibold mb-1">DEPOSIT</p>
                                        <p class="text-success fw-semibold mb-1">2,000.00 TK</p>
                                        <p class="text-white mb-1">BK33</p>
                                    </div>
                                    <div class="d-flex gap-3 text-white">
                                        <!-- Icons will need to be replaced with inline SVG or blade-friendly components -->
                                        <i class="bi bi-arrow-repeat"></i>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-3">
                                    <p class="mb-0">Order Number: ABCD1234</p>
                                    <p class="mb-0 text-warning fw-semibold">STATUS: <span>PENDING</span></p>
                                </div>

                                <div class="d-flex gap-5 mt-2">
                                    <p class="mb-0">Account Name: NAGAD</p>
                                    <p class="">01810665588</p>
                                </div>
                                <div>
                                    <p class="">Location: Office 1</p>
                                    <p class="">Created At : 14/03/2025 13:48:56</p>
                                    <p class="">Updated At : 14/03/2025 13:48:56</p>
                                    <p class="">Input Transaction Number: CB34653AS1</p>
                                    <p class="">Verified Transaction Number:</p>
                                </div>

                                <div class="d-flex gap-4 mt-3">
                                    <div class="justify-content-center">
                                        <p class="">Callback Status: Null</p>
                                    </div>
                                    <button class=" px-4 btn btn-sm"
                                        style="background-color: rgb(52, 152, 235); color: white; border: none; cursor: pointer;">Resend</button>
                                    <button class=px-4 btn btn-sm"
                                        style="background-color: blue; color: white; border: none; cursor: pointer;">Activity</button>
                                </div>
                                <div class="d-flex gap-4 mt-3">
                                    <button class=" px-4 btn btn-sm"
                                        style="background-color: rgb(45, 199, 58); color: white; border: none; cursor: pointer;">Edit</button>
                                    <button class=px-4 btn btn-sm"
                                        style="background-color: rgb(226, 15, 15); color: white; border: none; cursor: pointer;">Manual
                                        Process</button>
                                    <button class=" px-4 btn btn-sm"
                                        style="background-color: rgb(124, 3, 180); color: white; border: none; cursor: pointer;">Adjustment</button>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="custom-card p-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="d-flex gap-4">
                                        <p class="text-success fw-semibold mb-1">DEPOSIT</p>
                                        <p class="text-success fw-semibold mb-1">2,000.00 TK</p>
                                        <p class="text-white mb-1">KU91</p>
                                    </div>
                                    <div class="d-flex gap-3 text-white">
                                        <!-- Icons will need to be replaced with inline SVG or blade-friendly components -->
                                        <i class="bi bi-arrow-repeat"></i>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mt-3">
                                    <p class="mb-0">Order Number: ABCD1234</p>
                                    <p class="mb-0 text-warning fw-semibold">STATUS: <span>PENDING</span></p>
                                </div>

                                <div class="d-flex gap-5 mt-2">
                                    <p class="mb-0">Account Name: NAGAD</p>
                                    <p class="">01810665588</p>
                                </div>
                                <div>
                                    <p class="">Location: Office 1</p>
                                    <p class="">Created At : 14/03/2025 13:48:56</p>
                                    <p class="">Updated At : 14/03/2025 13:48:56</p>
                                    <p class="">Transaction Number: CB34653AS1</p>
                                    <p class="">Remarks:</p>
                                </div>
                                <div class="d-flex gap-4 mt-3">
                                    <div class="justify-content-center">
                                        <p class="">Callback Status: Null</p>
                                    </div>
                                    <button class="bg-green-600 px-4 btn btn-sm"
                                        style="background-color: rgb(52, 152, 235); color: white; border: none; cursor: pointer;">Resend</button>
                                    <button class="bg-red-600 px-4 btn btn-sm"
                                        style="background-color: blue; color: white; border: none; cursor: pointer;">Activity</button>
                                </div>
                                <div class="d-flex gap-2 mt-3">
                                    <button class="bg-green-600 px-4 btn btn-sm"
                                        style="background-color: rgb(45, 199, 58); color: white; border: none; cursor: pointer;">Manual</button>
                                    <button class="bg-red-600 px-4 btn btn-sm"
                                        style="background-color: rgb(226, 15, 15); color: white; border: none; cursor: pointer; text-sm">Manual
                                        Complete</button>
                                    <button class="bg-purple-600 px-4 btn btn-sm"
                                        style="background-color: rgb(180, 109, 3); color: white; border: none; cursor: pointer;">Retry</button>
                                    <button class="bg-purple-600 px-4 btn btn-sm"
                                        style="background-color: rgb(226, 15, 15); color: white; border: none; cursor: pointer;">Reject</button>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="custom-card p-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="d-flex gap-4">
                                        <p class="text-success fw-semibold mb-1">DEPOSIT</p>
                                        <p class="text-success fw-semibold mb-1">2,000.00 TK</p>
                                        <p class="text-white mb-1">WICKET</p>
                                    </div>
                                    <div class="d-flex gap-3 text-white">
                                        <!-- Icons will need to be replaced with inline SVG or blade-friendly components -->
                                        <i class="bi bi-arrow-repeat"></i>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mt-3">
                                    <p class="mb-0">Order Number: ABCD1234</p>
                                    <p class="mb-0  fw-semibold" style="color: red">STATUS: <span>REJECT</span></p>
                                </div>

                                <div class="d-flex gap-5 mt-2">
                                    <p class="mb-0">Account Name: NAGAD</p>
                                    <p class="">01810665588</p>
                                </div>
                                <div>
                                    <p class="">Location: Office 1</p>
                                    <p class="">Created At : 14/03/2025 13:48:56</p>
                                    <p class="">Updated At : 14/03/2025 13:48:56</p>
                                    <p class="">Input Transaction Number: CB34653AS1</p>
                                    <p class="">Remarks:</p>
                                </div>
                                <div class="d-flex gap-4 mt-3">
                                    <div class="justify-content-center">
                                        <p class="">Callback Status: Null</p>
                                    </div>
                                    <button class="bg-green-600 px-4 btn btn-sm"
                                        style="background-color: rgb(52, 152, 235); color: white; border: none; cursor: pointer;">Resend</button>
                                    <button class="bg-red-600 px-4 btn btn-sm"
                                        style="background-color: blue; color: white; border: none; cursor: pointer;">Activity</button>
                                </div>

                            </div>
                        </div>
                        <div class="col">
                            <div class="custom-card p-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="d-flex gap-4">
                                        <p class="text-success fw-semibold mb-1">DEPOSIT</p>
                                        <p class="text-success fw-semibold mb-1">2,000.00 TK</p>
                                        <p class="text-white mb-1">PKLUCK</p>
                                    </div>
                                    <div class="d-flex gap-3 text-white">
                                        <!-- Icons will need to be replaced with inline SVG or blade-friendly components -->
                                        <i class="bi bi-arrow-repeat"></i>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mt-3">
                                    <p class="mb-0">Order Number: ABCD1234</p>
                                    <p class="mb-0 fw-semibold" style="color: rgb(44, 185, 44);">STATUS:
                                        <span>COMPPLETE</span></p>
                                </div>

                                <div class="d-flex gap-5 mt-2">
                                    <p class="mb-0">Account Name: NAGAD</p>
                                    <p class="">01810665588</p>
                                </div>
                                <div>
                                    <p class="">Location: Office 1</p>
                                    <p class="">Created At : 14/03/2025 13:48:56</p>
                                    <p class="">Updated At : 14/03/2025 13:48:56</p>
                                    <p class="">Input Transaction Number: CB34653AS1</p>
                                    <p class="">Verified Transaction Number:</p>
                                </div>
                                <div class="d-flex gap-4 mt-3">
                                    <div class="justify-content-center">
                                        <p class="">Callback Status: Success</p>
                                    </div>
                                    <button class="bg-green-600 px-4 btn btn-sm"
                                        style="background-color: rgb(52, 152, 235); color: white; border: none; cursor: pointer;">Resend</button>
                                    <button class="bg-red-600 px-4 btn btn-sm"
                                        style="background-color: blue; color: white; border: none; cursor: pointer;">Activity</button>
                                </div>

                            </div>
                        </div>
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
                        <div class="d-flex gap-4 mb-3">
                            <button class=" px-4 btn btn-sm"
                                style="background-color: rgb(45, 199, 58); color: white; border: none; cursor: pointer;">BKASH
                                (5)</button>
                            <button class=" px-4 btn btn-sm"
                                style="background-color: rgb(226, 213, 30); color: white; border: none; cursor: pointer;">NAGAD
                                (4)</button>
                            <button class=" px-4 btn btn-sm"
                                style="background-color: rgb(168, 32, 196); color: white; border: none; cursor: pointer">ROCKET
                                (2)</button>
                        </div>
                        <div>
                            <p>bKash: 0128885568 Current Balance = 5,000TK</p>
                            <p>bKash: 0128885568 Current Balance = 5,000TK</p>
                            <p>bKash: 0128885568 Current Balance = 5,000TK</p>
                            <p>bKash: 0128885568 Current Balance = 5,000TK</p>
                            <p>bKash: 0128885568 Current Balance = 5,000TK </p>
                        </div>

                    </div>
                    <p class="pt-4">NOTIFICATION CENTER</p>
                    <div class="w-full py-2  px-2 items-center text-White d-flex justify-content-between"
                        style="background-color: #504c79;">
                        <p>Warning!! bKash 0128885568 <br>balance is low.</p>
                        <div>
                            <button type="button" class="btn-close " style="margin-left: 3rem;"
                                data-bs-dismiss="modal" aria-label="Close"></button>
                            <p>1 mint ago</p>
                        </div>
                    </div>
                    <div class="w-full py-2  px-2 items-center text-White d-flex justify-content-between mt-3"
                        style="background-color: #504c79;">
                        <p>Warning!! bKash 0128885568 <br>balance is low.</p>
                        <div>
                            <button type="button" class="btn-close " style="margin-left: 3rem;"
                                data-bs-dismiss="modal" aria-label="Close"></button>
                            <p>1 mint ago</p>
                        </div>
                    </div>
                    <p class="pt-4">WITHDRAWAL PENDING LIST (5 MINUTES)</p>
                    <div class=" w-full py-2  px-4  items-center text-White d-flex justify-content-between"
                        style="background-color: #504c79;">
                        <div>
                            <p>BK33 [Order Number] : 1,000TK</p>
                            <p>Account: Nagad 01238857776</p>
                            <p>Checking by: [Admin Name]</p>
                        </div>
                        <div>
                            <button class=" px-4 btn btn-sm"
                                style="background-color: rgb(45, 199, 58); margin-left: 2rem; color: white; border: none; cursor: pointer;">Check</button>
                            <p class="mt-10">1 mint ago</p>
                        </div>
                    </div>
                    <div class=" w-full py-2  px-4 mt-3 items-center text-White d-flex justify-content-between"
                        style="background-color: #504c79;">
                        <div>
                            <p>BK33 [Order Number] : 1,000TK</p>
                            <p>Account: Nagad 01238857776</p>
                            <p>Checking by: [Admin Name]</p>
                        </div>
                        <div>
                            <button class=" px-4 btn btn-sm"
                                style="background-color: rgb(45, 199, 58); margin-left: 2rem; color: white; border: none; cursor: pointer;">Check</button>
                            <p class="mt-10">1 mint ago</p>
                        </div>
                    </div>
                    <div class=" w-full py-2  px-4 mt-3 items-center text-White d-flex justify-content-between"
                        style="background-color: #504c79;">
                        <div>
                            <p>BK33 [Order Number] : 1,000TK</p>
                            <p>Account: Nagad 01238857776</p>
                            <p>Checking by: [Admin Name]</p>
                        </div>
                        <div>
                            <button class=" px-4 btn btn-sm"
                                style="background-color: rgb(45, 199, 58); margin-left: 2rem; color: white; border: none; cursor: pointer;">Check</button>
                            <p class="mt-10">1 mint ago</p>
                        </div>
                    </div>
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
