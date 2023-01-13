<?php

class SLN_Shortcode_Salon_ThankyouStep extends SLN_Shortcode_Salon_Step
{
    private $op;

    public function setOp($op)
    {
        $this->op = $op;
    }

    protected function dispatchForm()
    {
        $plugin = $this->getPlugin();
        $settings = $plugin->getSettings();
        $isCreateAfterPay = $settings->get('create_booking_after_pay') && $settings->isPayEnabled();
        $bb = $plugin->getBookingBuilder();
        if (isset($_GET['sln_booking_id']) && intval($_GET['sln_booking_id'])) {
            $bb->clear(intval($_GET['sln_booking_id']));
        }
        $booking = $bb;
        if(!$isCreateAfterPay){
            $booking = $bb->getLastBooking();
        }
        $paymentMethod = $settings->isPayEnabled() ? SLN_Enum_PaymentMethodProvider::getService($settings->getPaymentMethod(), $plugin) : false;
        $mode = sanitize_text_field(wp_unslash($_GET['mode']));
        $mode = isset($mode) ? $mode : null;
        if ($mode == 'confirm') {
            $this->goToThankyou();
        } elseif ($mode == 'later') {
            $bookingStatus = $booking->getStatus();
            if($bookingStatus == SLN_Enum_BookingStatus::PENDING_PAYMENT) {
                if ( $booking->getAmount() > 0.0 ) {
                    if($isCreateAfterPay){
                        $bookingStatus = SLN_Enum_BookingStatus::PAY_LATER;
                    }else{
                        $booking->setStatus(SLN_Enum_BookingStatus::PAY_LATER);
                    }
                } else {
                    if($isCreateAfterPay){
                        $bookingStatus = SLN_Enum_BookingStatus::CONFIRMED;
                    }else{
                        $booking->setStatus(SLN_Enum_BookingStatus::CONFIRMED);
                    }
                }
	        }
            if ( ! $bb->getLastBooking() && $isCreateAfterPay) {
                if ($bb->isValid() && $bb->get('services')) {
                    $bb->save();
                    do_action('sln.shortcode.summary.dispatchForm.before_booking_creation', $this, $bb);
                    if ( ! $this->hasErrors()) {
                        $bb->create($bookingStatus);
                        do_action('sln.shortcode.summary.dispatchForm.after_booking_creation', $bb);
                        if ($this->getPlugin()->getSettings()->get('confirmation')) {
                            $this->getPlugin()->messages()->sendSummaryMail($bb->getLastBooking());
                        }
                    }
                } else {
                    if ( empty( $bb->get('services') ) ) {
                        $this->addError(self::SERVICES_DATA_EMPTY);
                    } else {
                        $this->addError(self::SLOT_UNAVAILABLE);
                    }
                }
            }
            $this->goToThankyou();
        } elseif (isset($_GET['op']) || $mode) {
            if ($error = $paymentMethod->dispatchThankyou($this, $booking)) {
                $this->addError($error);
                if(! $bb->getLastBooking()){
                }
            } else {
                if ( ! $bb->getLastBooking() && $isCreateAfterPay) {
                    if ($bb->isValid() && $bb->get('services')) {
                        $bb->save();
                        do_action('sln.shortcode.summary.dispatchForm.before_booking_creation', $this, $bb);
                        if ( ! $this->hasErrors()) {
                            $bb->create(SLN_Enum_BookingStatus::PAID);
                            do_action('sln.shortcode.summary.dispatchForm.after_booking_creation', $bb);
                            if ($this->getPlugin()->getSettings()->get('confirmation')) {
                                $this->getPlugin()->messages()->sendSummaryMail($bb->getLastBooking());
                            }
                        }
                    } else {
                        if ( empty( $bb->get('services') ) ) {
                            $this->addError(self::SERVICES_DATA_EMPTY);
                        } else {
                            $this->addError(self::SLOT_UNAVAILABLE);
                        }
                    }
                }
                $this->goToThankyou();
            }
        }

        return false;
    }

    public function goToThankyou()
    {
        $id = $this->getPlugin()->getSettings()->getThankyouPageId();
        if ($id) {
            $this->redirect(get_permalink($id));
        }else{
            $this->redirect(home_url());
        }
    }

    public function render(){
        $settings = $this->getPlugin()->getSettings();
        $plugin = $this->getPlugin();
        if($settings->get('create_booking_after_pay')){
            $bookingPrice = $plugin->getBookingBuilder()->getTotal();
        }else{
            $bookingPrice = $plugin->getBookingBuilder()->getLastBooking()->getId();
            $bookingPrice = get_post_meta($bookingPrice, '_sln_booking_amount', true);
        }
        if($settings->isPayEnabled() && $bookingPrice != '0'){
            return $this->getPlugin()->loadView('shortcode/salon_' . $this->getStep(), $this->getViewData());
        }else{
            $this->goToThankyou();
        }
    }

    public function getViewData()
    {
        $ret = parent::getViewData();
        $formAction = $ret['formAction'];

	$laterUrl = add_query_arg(
	    array(
		'mode'			    => 'later',
		'submit_'.$this->getStep()  => 1
	    ),
	    $formAction
	);

	$laterUrl = apply_filters('sln.booking.thankyou-step.get-later-url', $laterUrl);

	$confirmUrl = add_query_arg(
	    array(
		'mode'			    => 'confirm',
		'submit_'.$this->getStep()  => 1
	    ),
	    $formAction
	);

	$confirmUrl = apply_filters('sln.booking.thankyou-step.get-confirm-url', $confirmUrl);

        $data = array(
            'formAction' => $formAction,
            'booking' => $this->getPlugin()->getBookingBuilder()->getLastBooking(),
            'confirmUrl' => $confirmUrl,
            'laterUrl' => $laterUrl,
        );
        if($this->getPlugin()->getSettings()->isPayEnabled()){

	    $payUrl = add_query_arg(
		array(
		    'mode'			=> $this->getPlugin()->getSettings()->getPaymentMethod(),
		    'submit_'.$this->getStep()  => 1,
		),
		$formAction
	    );

	    $payUrl = apply_filters('sln.booking.thankyou-step.get-pay-url', $payUrl);

            $data['payUrl'] = $payUrl;
            $data['payOp']  = $this->op;
        }

        return array_merge( $ret,$data );
    }

    public function redirect($url)
    {
        if ($this->isAjax()) {
            throw new SLN_Action_Ajax_RedirectException($url);
        } else {
            wp_redirect($url);die();
        }
    }

    public function isAjax()
    {
        return defined('DOING_AJAX') && DOING_AJAX;
    }
}
