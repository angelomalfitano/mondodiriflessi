<?php

class SLN_Payment_Stripe
{
    protected $plugin;

    public function __construct(SLN_Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    public static function getWebhookUrl()
    {
        return add_query_arg('sln_action', 'stripe_webhook', home_url('/'));
    }

    public function execute()
    {
        if ( ! isset($_GET['sln_action']) || $_GET['sln_action'] != 'stripe_webhook' ) {
	    return;
	}
        $this->initApi();

        $endpoint_secret = get_option('sln_stripe_webhook_endpoint_secret');

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent
                // Then define and call a method to handle the successful payment intent.
                // handlePaymentIntentSucceeded($paymentIntent);
                break;
        }

        if ($paymentIntent) {
            $booking = $this->plugin->getRepository(SLN_Plugin::POST_TYPE_BOOKING)->getOne(array(
                'post_status' => SLN_Enum_BookingStatus::PENDING_PAYMENT,
                '@wp_query'   => array(
                    'meta_query' => array(
                        array(
                            'key'     => '_sln_booking_stripe_payment_intent',
                            'value'   => $paymentIntent->id,
                            'compare' => '=',
                        )
                    ),
                )
            ));
        }

	if ($booking) {
            try {
                $paymentIntent = \Stripe\PaymentIntent::update($paymentIntent->id, array(
                        'description' => "Booking #" . $booking->getId(),
                ));

                if ($paymentIntent->status === 'succeeded') {
                    $transactionID = $paymentIntent->charges->data[0]->balance_transaction;
                    $booking->markPaid($transactionID);
                }

            } catch (Exception $ex) {
                    SLN_Plugin::addLog('stripe error: ' . $ex->getMessage());
            }
        }

	header('HTTP/1.1 200 OK');

	exit();

    }

    protected function initApi() {

        if ( ! class_exists('Stripe\Stripe') ) {
            require_once __DIR__ . '/../PaymentMethod/_stripe/vendor/autoload.php';
        }

        \Stripe\Stripe::setApiKey($this->plugin->getSettings()->get('pay_stripe_apiKey'));
    }

}
