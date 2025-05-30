<?php echo e($previousid); ?>



<script>
    // Replace this with the actual ID you want to pass
    const id = '<?php echo e($previousid); ?>';

    // Wait for 5 seconds (5000 milliseconds)
    setTimeout(function () {
        // Redirect to the Laravel route with the dynamic ID
        window.location.href = `/ecpay/admin/makeatest/${id}`;
    }, 5000);
</script>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/payment/makeatest.blade.php ENDPATH**/ ?>