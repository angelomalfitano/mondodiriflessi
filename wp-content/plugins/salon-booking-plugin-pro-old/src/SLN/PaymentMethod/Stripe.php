<?php

class SLN_PaymentMethod_Stripe extends SLN_PaymentMethod_Paypal//Abstract
{
    private static $zeroDecimal = array(
        'JPY', 'VND', 'XOF', 'VUV', 'GNF', 'KRV', 'DJF', 'RWF', 'KMF', 'CLP', 'XPF', 'XAF', 'BIF', 'MGA'
    );

    public static function isZeroDecimal($currency)
    {
        return in_array($currency, self::$zeroDecimal);
    }

    public function getFields()
    {
        return array(
            'pay_stripe_apiKey',
            'pay_stripe_apiKeyPublic',
            'pay_stripe_method'
        );
    }

    public function dispatchThankYou(SLN_Shortcode_Salon_ThankyouStep $shortcode, $booking = null)
    {

        if ($_GET['mode'] == $this->getMethodKey() && isset($_GET['op'])) {

            $op = explode('-', sanitize_text_field($_GET['op']));

            $action = $op[0];

            if ($action == 'success') {

                if(empty($booking)){
                    $booking = $this->plugin->createBooking($op[1]);
                }

                $sessionID = $booking->getMeta('stripe_session_id');

                if (!$sessionID) {
                    return __('Your payment has not been completed', 'salon-booking-system');
                }

                $this->initApi();

                try {
                    $sessionCheckout = \Stripe\Checkout\Session::retrieve($sessionID);
                    $paymentIntent = \Stripe\PaymentIntent::retrieve($sessionCheckout->payment_intent);

                    $paymentIntent = \Stripe\PaymentIntent::update($sessionCheckout->payment_intent, array(
                        'description' => "Booking #" . $booking->getId(),
                    ));

                    if ($paymentIntent->status === 'succeeded') {

                        $transactionID = $paymentIntent->charges->data[0]->balance_transaction;

                        $shortcode->goToThankyou();
                    } else {
                        return __('Your payment has not been completed', 'salon-booking-system');
                    }

                } catch (Exception $ex) {
                    SLN_Plugin::addLog('stripe error: ' . $ex->getMessage());
                    return __('Payment failed, please try again', 'salon-booking-system');
                }

            } elseif ($action == 'cancel') {
                return __('Your payment has not been completed', 'salon-booking-system');
            } else {
                throw new Exception('payment method operation not managed');
            }

        } else {
            throw new Exception('payment method mode not managed');
        }
    }

    public function getApiKeyPublic()
    {
        return $this->plugin->getSettings()->get('pay_stripe_apiKeyPublic');
    }

    public function getApiKey()
    {
        return $this->plugin->getSettings()->get('pay_stripe_apiKey');
    }

    public function getPaymentMethod()
    {
        $method = $this->plugin->getSettings()->get('pay_stripe_method');
        return $method ? $method : 'card';
    }

    public function renderPayButton($data)
    {

        extract($data);

        if ($this->isAjax()) {
            $_SERVER['REQUEST_URI'] = add_query_arg(array('sln_step_page' => 'thankyou', 'submit_thankyou' => 1, 'mode' => $this->getMethodKey(), 'op' => null), str_replace(array($_SERVER["SERVER_NAME"], 'https://', 'http://', 'www.'), '', $_SERVER['HTTP_REFERER']));
        } else {
            $_SERVER['REQUEST_URI'] = add_query_arg(array('sln_step_page' => 'thankyou', 'submit_thankyou' => 1, 'mode' => $this->getMethodKey(), 'op' => null));
        }

        $successUrl = add_query_arg(['mode' => $this->getMethodKey(), 'op' => 'success-' . $booking->getId()], SLN_Func::currPageUrl());
        $cancelUrl = add_query_arg(['mode' => $this->getMethodKey(), 'op' => 'cancel-' . $booking->getId()], SLN_Func::currPageUrl());

        $this->initApi();

        $amount = intval((string)($booking->getToPayAmount(false) * (self::isZeroDecimal($this->plugin->getSettings()->getCurrency()) ? 1 : 100)));

        try {

            $response = \Stripe\Checkout\Session::create(array(
                "success_url" => apply_filters('sln.payment.stripe.success_url', $successUrl),
                "cancel_url" => apply_filters('sln.payment.stripe.cancel_url', $cancelUrl),
                "payment_method_types" => array($this->getPaymentMethod()),
                "line_items" => array(array(
                    "name" => "Booking #" . $booking->getId(),
                    "amount" => $amount,
                    "currency" => strtolower($this->plugin->getSettings()->getCurrency()),
                    "quantity" => 1,
                )),
                'locale' => $this->plugin->getSettings()->getLocale(),
            ));

            $sessionID = $response->id;

            $sessionCheckout = \Stripe\Checkout\Session::retrieve($sessionID);
            $paymentIntent = \Stripe\PaymentIntent::retrieve($sessionCheckout->payment_intent);

            $paymentIntentID = $paymentIntent->id;

            $paymentIntent = \Stripe\PaymentIntent::update($paymentIntentID, array(
                'description' => "Booking #" . $booking->getId(),
            ));

            $endpoints = \Stripe\WebhookEndpoint::all();
            $endpointId = get_option('sln_stripe_webhook_endpoint_id') ?? '';
            $haveEndpoint = false;
            foreach ($endpoints as $ep) {
                if (SLN_Payment_Stripe::getWebhookUrl() === $ep->url) {
                    if ($endpointId === $ep->id) {
                        $haveEndpoint = true;
                        break;
                    } else {
                        $ep->delete();
                    }
                }
            }
            if (!$haveEndpoint) {
                $endpoint = \Stripe\WebhookEndpoint::create([
                    'url' => SLN_Payment_Stripe::getWebhookUrl(),
                    'enabled_events' => [
                        'payment_intent.succeeded',
                    ],
                ]);
                update_option('sln_stripe_webhook_endpoint_id', $endpoint->id);
                update_option('sln_stripe_webhook_endpoint_secret', $endpoint->secret);
            }

        } catch (Exception $e) {

            SLN_Plugin::addLog('stripe error: ' . $e->getMessage());
            error_log('stripe error: ' . $e->getMessage());

            echo __('Payment method failed, please see details in logs', 'salon-booking-system');

            return false;
        }

        $booking->setMeta('stripe_session_id', $sessionID);
        $booking->setMeta('stripe_payment_intent', $paymentIntentID);

        return $this->plugin->loadView('payment_method/' . $this->getMethodKey() . '/pay', compact('booking', 'paymentMethod', 'ajaxData', 'payUrl', 'sessionID'));
    }

    protected function initApi()
    {

        if (!class_exists('Stripe\Stripe')) {
            require_once __DIR__ . '/_stripe/vendor/autoload.php';
        }

        \Stripe\Stripe::setApiKey($this->plugin->getSettings()->get('pay_stripe_apiKey'));
    }

    public function isAjax()
    {
        return defined('DOING_AJAX') && DOING_AJAX;
    }

}
