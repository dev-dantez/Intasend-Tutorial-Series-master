<?php
// paybutton.php
include_once('credentials.php');
$publishable_key = isset($credentials['publishable_key']) ? $credentials['publishable_key'] : '';
?>

<script src="https://unpkg.com/intasend-inlinejs-sdk@4.0.0/build/intasend-inline.js"></script>

<div class="max-w-xl mx-auto py-12 animate-fade-in">
    <div class="card bg-base-100 shadow-xl card-premium p-8 text-center bg-base-100/30 backdrop-blur-md">
        <h2 class="text-3xl font-bold mb-4">Pay Button</h2>
        <p class="mb-8 opacity-70 leading-relaxed">
            The IntaSend Inline JS SDK allows you to drop a payment button anywhere on your site with minimal configuration.
        </p>

    <?php if (empty($publishable_key)) { ?>
        <div class="alert alert-error shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>Please add your Publishable Key to credentials.php</span>
        </div>
    <?php } else { ?>
        <div class="relative py-8">
            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                <div class="w-full border-t border-gray-700"></div>
            </div>
            <div class="relative flex justify-center">
                <span class="bg-base-300 px-3 text-sm text-gray-500 rounded-full">Demo</span>
            </div>
        </div>
        
        <button
            class="inta-send-btn btn btn-primary-gradient btn-lg w-full shadow-xl hover:shadow-2xl transition-all hover:-translate-y-1 block"
            data-amount="10"
            data-currency="KES"
            data-email="joe@doe.com"
            data-first_name="JOE"
            data-last_name="DOE"
            data-country="KE"
        >
        Pay Now 10 KES
        </button>
        <p class="mt-4 text-xs text-base-content/50">Secured by IntaSend</p>
    <?php } ?>
    </div>
</div>

<script>
    new window.IntaSend({
        publicAPIKey: "<?php echo $publishable_key; ?>",
        live: true, // Set to true when going live
    })
    .on("COMPLETE", (results) => {
        console.log("Success", results);
        alert("Payment successful! See console for details.");
    })
    .on("FAILED", (results) => {
        console.log("Failed", results);
        alert("Payment failed! See console for details.");
    })
    .on("IN-PROGRESS", (results) => {
        console.log("In-progress", results);
    });
</script>
