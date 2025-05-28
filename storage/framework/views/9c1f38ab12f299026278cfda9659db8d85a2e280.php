<?php if (isset($component)) { $__componentOriginal23c27112e6c74ae5cb1838f9f51eac2489efd1f7 = $component; } ?>
<?php $component = App\View\Components\IframeLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('iframe-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\IframeLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
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
<?php
    $ewallet = strtolower($ewallet);
        if ($ewallet == 'bkash') {
            $time_class = 'bkash-time';
            $background_image = 'bkash-responsive-row';
            $button_style = 'bkash-complete-btn';
            $color = '#e2136e';
            $bgcolor = '#e2136e';
            $bgcolorrbga = 'rgb(226, 19, 110,0.2)';
        }
        if ($ewallet == 'nagad') {
            $time_class = '';
            $background_image = 'nagad-responsive-row';
            $button_style = 'nagad-complete-btn';
            $color = '#FF9600';
            $bgcolor = '#FF9600';
            $bgcolorrbga = 'rgb(255, 150, 0,0.2)';
        }
        if ($ewallet == 'rocket') {
            $time_class = '';
            $background_image = 'rocket-responsive-row';
            $button_style = 'rocket-complete-btn';
            $color = '#8F2A85';
            $bgcolor = '#8F2A85';
            $bgcolorrbga = 'rgb(143, 42, 133,0.2)';
        }
    ?>
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

        #txn_verification::placeholder {
    color: white;
    opacity: 1; /* Ensures full visibility in some browsers */
}


.timer-container {
        position: relative;
        width: 100px;
        height: 100px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    #timer {
        position: absolute;
        font-size: 30px;
        font-weight: bold;
        color: <?=$bgcolor?>;
    }

    #progress-circle {
        transform: rotate(-90deg); /* Start from top and move clockwise */
        transform-origin: center;
        transition: stroke-dashoffset 1s linear;
    }
</style>

<?php
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
?>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="payment-container p-3">

        <div>
            <br>
            <p style="font-weight: bold;color:black;text-align:center">Your Transaction ID has been submitted.<br> Please wait for the result below.
            </p>
             <p style="font-weight: bold;color:green;text-align:center">আপনার লেনদেন আইডি জমা দেওয়া হয়েছে।<br> অনুগ্রহ করে নীচের ফলাফলের জন্য অপেক্ষা করুন।
            </p>
        </div>

        <div style="display: flex; align-items: center;" class="pt-2 mb-1">
            <div style="margin-right: 10px;">
                <span class="circle">1</span>
            </div>
            <div style="background-color: <?=$bgcolor?>;padding:10px;width:85%;text-align:center">
                <span style="font-weight: bold;color:white">Checking Status | স্থিতি পরীক্ষা করা হচ্ছে </span><br>
            </div>
        </div>
        

        <?php if($processing == 1): ?>
        <div id="intime">
            <div class="" style="max-width:400px;margin:auto" id="processing_div">
                <div class="text-center">

                    <div style="display: flex; align-items: center;padding:20px 40px">
                        <div style="margin-right: 10px;">

                            <div class="timer-container">
                                <svg id="timer-circle" width="100" height="100" viewBox="0 0 100 100">
                                    <!-- Background Circle (Always Filled) -->
                                    <circle cx="50" cy="50" r="45" stroke="<?=$bgcolor?>" stroke-width="8" fill="none" />

                                    <!-- White Overlay Circle (Shrinking Clockwise) -->
                                    <circle id="progress-circle" cx="50" cy="50" r="45" stroke="white" stroke-width="8" fill="none"
                                        stroke-dasharray="283" stroke-dashoffset="0" />
                                </svg>
                                <span id="timer">30</span>
                            </div>
                        </div>
                        <div style="text-align:right;width:100%">
                            <span style="font-weight: bold;color:black;">Processing  <span id="t1">1</span> / 10 <br>প্রক্রিয়াকরণ  <span id="t2">1</span> / 10</span><br>
                        </div>
                    </div>


                    <h5>Please hold while we check on the transaction status. Please do not submit a new deposit. By submitting new deposit will delay the money credited to your account!</h5>
                    <h5><p style="font-weight: bold;color:green;text-align:center">লেনদেনের স্থিতি পরীক্ষা করার জন্য অপেক্ষা করুন। অনুগ্রহ করে নতুন আমানত জমা দেবেন না। নতুন আমানত জমা দিলে আপনার অ্যাকাউন্টে টাকা জমা হতে দেরি হবে!</p></h5>
                </div>
            </div>
            <div class="" style="max-width:400px;margin:auto;display: none;" id="attemptfailed">
                <div class="text-center">

                    <div style="display: flex; align-items: center;padding:20px 40px">
                        <div style="margin-right: 10px;">
                            <img class="img-fluid" src="<?php echo e(asset('assets/images/close.png')); ?>" style="width:100px;" />
                        </div>
                        <div style="text-align:right;width:100%">
                            <span style="font-weight: bold;color:black;">Processing  <span id="t3">1</span> / 10 <br>প্রক্রিয়াকরণ  <span id="t4">1</span> / 10</span><br>
                        </div>
                    </div>


                    <h5>We are sorry. We are not able to match your Transaction ID. Please contact merchant for assistance.</h5>
                    <h5>আমরা দুঃখিত। আমরা আপনার লেনদেন আইডি মেলাতে পারছি না। সহায়তার জন্য অনুগ্রহ করে মার্চেন্টের সাথে যোগাযোগ করুন।</h5>
                </div>
            </div>
            </div>
            <div id="outtime" style="display: none;">
                    <div class="text-center">
                    <img class="img-fluid" src="<?php echo e(asset('assets/images/error_transparent.gif')); ?>" />
                    <h5>Sorry. We are not able to
process your payment.
Please contact customer
service for assistance.
                </h5>
                <h5>দুঃখিত। আমরা আপনার পেমেন্ট প্রক্রিয়া করতে সক্ষম নই। সহায়তার জন্য গ্রাহক পরিষেবার সাথে যোগাযোগ করুন।
                </h5>

                </div>
                </div>
        <?php endif; ?>

        <?php if($processing == 2): ?>
            <div class="" style="max-width:400px;margin:auto" id="error_div">
                <div class="text-center">
                    <img class="img-fluid" src="<?php echo e(asset('assets/images/error_transparent.gif')); ?>" />
                    <h5>Sorry. We are not able to
process your payment.
Please contact customer
service for assistance.
                </h5>
                <h5>দুঃখিত। আমরা আপনার পেমেন্ট প্রক্রিয়া করতে সক্ষম নই। সহায়তার জন্য গ্রাহক পরিষেবার সাথে যোগাযোগ করুন।
                </h5>
                    

                </div>
            </div>
        <?php endif; ?>

        <p id="countdown" style="text-align: center;color:red;font-weight:bold" class="p-1"></p>
        
        <div style="max-width:400px;display:none;margin:auto" id="complete_div">
            <center>
                <img class="img-fluid" src="<?php echo e(asset('assets/images/check.png')); ?>" style="width:200px;" />
            </center>

            <div style="display: flex;align-items: flex-start;" class="pt-2 mb-1">
                <div style="margin-right: 10px;padding-top:15px">
                    <span class="circle">2</span>
                </div>
                <div style="padding:10px;width:85%;text-align:left">
                    <span style="color:black">Your transaction has been successfully completed. Your money will be credited to your account now. <br>
                        আপনার লেনদেন সফলভাবে সম্পন্ন হয়েছে। আপনার টাকা এখন আপনার অ্যাকাউন্টে জমা হবে। </span><br>
                </div>
            </div>
        </div>


        <div style="max-width:400px;display:none;margin:auto" id="pre_complete_div">
            <img class="img-fluid" src="<?php echo e(asset('assets/images/complete.gif')); ?>" />
            <div class="col-md-10 mx-auto text-center">
                <h5>Your payment request has been received successfully, and the transaction will be processed within 5 minutes.<br>
                    Thank you!
                </h5>
                <h5>আপনার পেমেন্ট অনুরোধ সফলভাবে গ্রহণ করা হয়েছে এবং লেনদেন ৫ মিনিটের মধ্যে প্রক্রিয়া করা হবে।.<br>
                    ধন্যবাদ!
                </h5>
            </div>
        </div>

        <hr style="background-color:black">
        <p style="text-align:center;color:black">Transaction ID enter | লেনদেন আইডি লিখুন</p>
        <form action="<?php echo e(route('iframe.payment3')); ?>" method="POST" id="payment-form">
            <?php echo csrf_field(); ?>
        <div class="input-container" style="position: relative;">
            <input type="text" name="txn" class="form-control" id="txn_verification"
                placeholder="Transaction ID (EC.<?php echo e($ewallet == 'bkash'?'CC67DX6R2B':'73PVF685'); ?>)"
                data-min="<?php echo e(($ewallet == 'bkash') ? 10 : (($ewallet == 'nagad') ? 8 : 25)); ?>"
                data-ewallet="<?php echo e($ewallet); ?>"
                value="<?php echo e($txn_id); ?>"
                readonly
                style="padding-right: 30px;color:<?=$bgcolor?>;text-align:center;border:1px solid <?=$bgcolor?>;">

            <svg id="edit_icon" style="width: 20px; position: absolute; right: 20px; top: 50%;
                transform: translateY(-50%); font-size: 18px; color: #333; cursor: pointer;"
                viewBox="0 0 512 512">
                <path fill="<?=$bgcolor?>" d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9
                30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5
                21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4
                21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160L0 416c0 53 43
                96 96 96l256 0c53 0 96-43 96-96l0-96c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 96c0 17.7-14.3
                32-32 32L96 448c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l96 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 64z"/>
            </svg>
        </div>
        <small id="txn_error" style="color: red; display: none;"></small>


        


            <input type="hidden" name="id" value="<?php echo e($id); ?>">
            <input type="hidden" name="ewallet" value="<?php echo e($ewallet); ?>">
            <input type="hidden" name="time" value="<?php echo e(time()); ?>">

            <button type="submit" class="form-control btn btn-dark mt-2 mb-2 text-white" style="height: 60px" id="complete-button">
                Change Transaction ID
                <br>
                লেনদেন আইডি পরিবর্তন করুন
            </button>
        </form>
        

    </div>
</div>

<script src="<?php echo e(asset('assets/global/js/jquery.min.js')); ?>"></script>
<script>
    document.getElementById("edit_icon").addEventListener("click", function() {
        let inputField = document.getElementById("txn_verification");
        inputField.removeAttribute("readonly"); // Remove readonly attribute
        inputField.style.cursor = "text"; // Show text cursor
        inputField.focus(); // Move focus to input field
    });
</script>
<script>
        "use strict";
        $(document).ready(function(e) {

            var process_status = "<?php echo e($processing); ?>";

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

        let mytry = 0;
        let remainingTime = 30;

        function checkStatus() {
            $.ajax({
                url: "<?php echo e(route('update_fund_order_status.iframe', ['id' => $id])); ?>",
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    mytry++;
                    console.log(response);
                    document.getElementById("t1").innerHTML = mytry;
                    document.getElementById("t2").innerHTML = mytry;
                    document.getElementById("t3").innerHTML = mytry;
                    document.getElementById("t4").innerHTML = mytry;


                    if (response.status === 'success') {
                        // Handle success condition (e.g., update the UI).
                        console.log(response);
                        $('#processing_div').hide();
                        $('#error_div').hide();
                        $('#pre_complete_div').hide();
                        $('#attemptfailed').hide();
                        $('#complete_div').show();



                        timerRunning = false;
                        clearInterval(timerInterval);
                        // $('#intime').hide();
                        $('#timer').hide();
                        $('#outtime').hide();

                        var redirectUrl = <?php echo json_encode($url, 15, 512) ?>;
                        if (redirectUrl && redirectUrl.trim() !== "") {
                            startCountdownAndRedirect(redirectUrl, 'countdown');
                        }

                    } else {
                        if(mytry>9){
                            clearInterval(timerInterval);
                            $('#processing_div').hide();
                             $('#attemptfailed').show();
                        }else{
                            setTimeout(checkStatus, 30000);
                            remainingTime = 30;
                        }
                    }


                },
                error: function() {
                    // Handle AJAX request error if needed.
                    console.error('Error occurred during the AJAX request.');
                }
            });
        }

        function startCountdownAndRedirect(url, countdownElementId) {
            var count = 3;
            var countdownElement = $('#' + countdownElementId);
            countdownElement.text('We are redirecting you in ' + count + ' seconds');

            var countdownInterval = setInterval(function() {
                count--;
                if (count > 0) {
                    countdownElement.text('We are redirecting you in ' + count + ' seconds');
                } else {
                    countdownElement.text('We are redirecting you');
                    clearInterval(countdownInterval);
                    window.location.href = url;
                }
            }, 1000);
        }

    </script>
    <script>
        $(document).ready(function() {
        // let remainingTime = <?php echo json_encode($remainingTime, 15, 512) ?>;

        let processing = <?php echo json_encode($processing, 15, 512) ?>;

        const timerElement = $('#timer');
            const intime = $('#intime');
            const outtime = $('#outtime');
            let circle = document.getElementById("progress-circle");
    let circumference = 2 * Math.PI * 45; // Circle circumference


    circle.style.strokeDasharray = circumference;
    circle.style.strokeDashoffset = 0; // Initially full white overlay

        if(processing!==1){

                    timerElement.hide();
                    clearInterval(timerInterval);
        }else if (remainingTime > 0) {


            const updateTimer = () => {
                if (remainingTime > 0 && timerRunning) {
                    const minutes = Math.floor(remainingTime / 60);
                    const seconds = remainingTime % 60;
                    timerElement.text(seconds);
                    remainingTime--;
                    let progress = (remainingTime / 30) * circumference; // Adjust for 30 seconds
                    circle.style.strokeDashoffset = progress; // Clockwise shrink
                } else if (remainingTime <= 0) {
                    timerElement.text('0');
                    // intime.hide();
                    // timerElement.hide();
                    // outtime.show();
                    // clearInterval(timerInterval);
                }
            };

            timerInterval = setInterval(updateTimer, 1000);
            updateTimer();  // Initial call to display the timer immediately
        } else {

            // $('#timer').hide();
            // $('#expired').show();
        }

        // checkStatus();
    });
    </script>

<?php $__env->startPush('script'); ?>

<?php $__env->stopPush(); ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal23c27112e6c74ae5cb1838f9f51eac2489efd1f7)): ?>
<?php $component = $__componentOriginal23c27112e6c74ae5cb1838f9f51eac2489efd1f7; ?>
<?php unset($__componentOriginal23c27112e6c74ae5cb1838f9f51eac2489efd1f7); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/partner/payout/paymentProcessingIframe2.blade.php ENDPATH**/ ?>