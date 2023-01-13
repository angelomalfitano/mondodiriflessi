<?php $deposit = $booking->getDeposit(); ?>

<a href="#" onclick="slb_stripe_checkout();return false;">
    <?php if($deposit > 0): ?>
	<?php echo sprintf(__('Pay %s as a deposit with %s', 'salon-booking-system'), $plugin->format()->moneyFormatted($deposit), $paymentMethod->getMethodLabel()) ?>
    <?php else : ?>
	<?php echo sprintf(__('Pay with %s', 'salon-booking-system'), $paymentMethod->getMethodLabel()) ?>
    <?php endif ?>
</a>

<script src="https://js.stripe.com/v3/"></script>

<script>

    function slb_stripe_checkout () {

	var stripe = Stripe('<?php echo $paymentMethod->getApiKeyPublic()?>');

	stripe.redirectToCheckout({
	  // Make the id field from the Checkout Session creation API response
	  // available to this file, so you can provide it as parameter here
	  // instead of the {{CHECKOUT_SESSION_ID}} placeholder.
	  sessionId: '<?php echo $sessionID ?>'
	}).then(function (result) {
	  // If `redirectToCheckout` fails due to a browser or network
	  // error, display the localized error message to your customer
	  // using `result.error.message`.
	});
    }

</script>
