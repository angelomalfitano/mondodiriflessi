<?php
include $this->plugin->getViewFile('admin/utilities/settings_inpage_navbar');
sum(
	// link anchor, link text
	array('#sln-online_payment_status', __('Online payment', 'salon-booking-system')),
	array('#sln-currency', __('Currency', 'salon-booking-system')),
	array('#sln-payment_methods', __('Payment methods', 'salon-booking-system')),
	array('#sln-pay_later', __('Pay later', 'salon-booking-system')),
	array('#sln-pay_a_deposit', __('Pay a deposit', 'salon-booking-system')),
	array('#sln-tip_request', __('Tip request', 'salon-booking-system')),
	array('#sln-apply_transaction_fee', __('Apply a transaction fee', 'salon-booking-system')),
	array('#sln-prices_visibility', __('Prices visibility', 'salon-booking-system')),
	array('#sln-pending_payment_email', __('Pending payment email', 'salon-booking-system')),
	array('#sln-minimum_order_amount', __('Minimum order amount', 'salon-booking-system')),
	array('#sln-unpaid_reservations', __('Unpaid reservations', 'salon-booking-system'))
);
?><div id="sln-online_payment_status" class="sln-box sln-box--main sln-box--online-payment sln-box--haspanel sln-box--haspanel--open">
        <h2 class="sln-box-title sln-box__paneltitle sln-box__paneltitle--open"><?php _e('Online payment<span>Allow users to pay in advance using one of the available payments methods.</span>', 'salon-booking-system');?></h2>
    <div class="collapse in sln-box__panelcollapse">
        <div class="row">
        <div class="col-xs-12">
		<div class="row">
		    <div class="col-xs-12 col-sm-6">
                <div class="sln-switch">
			<?php $this->row_input_checkbox_switch(
	'pay_enabled',
	'Online payment Status',
	array(
		'bigLabelOn' => 'Online payment ON',
		'bigLabelOff' => 'Online payment OFF',
	)
);?>
</div>
<div class="sln-box-maininfo">
            <p class="sln-box-info"><?php _e('If enabled you need to setup one of the available payments methods.', 'salon-booking-system');?></p>
            </div>


		    </div>
		</div>
	    </div>
	</div>
    </div>
    </div>
<div id="sln-currency" class="sln-box sln-box--main sln-box--haspanel">
        <h2 class="sln-box-title sln-box__paneltitle"><?php _e('Currency', 'salon-booking-system');?></h2>
        <div class="collapse sln-box__panelcollapse">
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-4 form-group sln-select ">
                    <label for="salon_settings_pay_currency"><?php _e('Set your currency', 'salon-booking-system')?></label>
                    <?php echo SLN_Form::fieldCurrency(
	"salon_settings[pay_currency]",
	$this->settings->getCurrency()
) ?>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-4 form-group sln-select ">
                    <label for="salon_settings_pay_currency_pos"><?php _e('Set your currency position', 'salon-booking-system')?></label>
                     <?php echo SLN_Form::fieldSelect(
	'salon_settings[pay_currency_pos]',
	array('left' => __('on left side', 'salon-booking-system'), 'right' => __('on right side', 'salon-booking-system')),
	$this->settings->get('pay_currency_pos'),
	array(),
	true
) ?>
                </div>
            <div class="col-xs-12 col-sm-6 col-md-4 visible-lg-block sln-box-maininfo align-top">
                <p class="sln-box-info"><?php _e('If you want a new currency to be added please send us an email to support@wpchef.it', 'salon-booking-system');?></p>
            </div>
            <div class="clearfix visible-lg-block"></div>
            <div class="col-xs-6 col-sm-3 col-md-2 sln-input--simple">
                <?php $this->row_input_text('pay_decimal_separator', __('Decimal separator', 'salon-booking-system'));?>
            </div>
            <div class="col-xs-6 col-sm-3 col-md-2 sln-input--simple sln-pay-thousand-separator-option">
                <?php $this->row_input_text('pay_thousand_separator', __('Thousand separator', 'salon-booking-system'));?>
            </div>
            <?php /* this box is a carbon copy of the one some lines above, this one is visible on smaller screens, the other one on large screens. They must have the same content. */?>
            <div class="col-xs-12 col-sm-6 col-md-4 hidden-lg sln-box-maininfo">
                <p class="sln-box-info"><?php _e('If you want a new currency to be added please send us an email to support@wpchef.it', 'salon-booking-system');?></p>
            </div>
                </div>

        <div class="row">

        </div>
        </div>
    </div>
<div id="sln-payment_methods" class="sln-box sln-box--main sln-box--haspanel">
        <h2 class="sln-box-title sln-box__paneltitle"><?php _e('Payment methods', 'salon-booking-system');?></h2>
        <div class="collapse sln-box__panelcollapse">
        <div class="row">
        <?php
$current_payment_method = $this->settings->getPaymentMethod();
foreach (SLN_Enum_PaymentMethodProvider::toArray() as $method => $name) {
	$checked = ($current_payment_method == $method) ? 'checked="checked"' : '';
	?>
                <div class="sln-radiobox sln-radiobox--fullwidth salon_settings_pay_method col-sm-3">
                    <input class="sln-pay_method-radio" id="salon_settings_availability_mode--<?php echo $method ?>" type="radio" name="salon_settings[pay_method]" value="<?php echo $method ?>" data-method="<?php echo $method ?>" <?php echo $checked; ?> >
                    <label for="salon_settings_availability_mode--<?php echo $method ?>"><?php echo $name ?></label>
                </div>
        <?php }?>

            <div class="col-xs-12 col-sm-6 sln-box-maininfo  align-top">
                <p class="sln-box-info"><?php _e('If you want to integrate a new custom payment gateway please refere to <strong>custom_payment_gateway.txt</strong> file inside our plugin folder.', 'salon-booking-system');?></p>
            </div>
        </div>
        <?php
foreach (SLN_Enum_PaymentMethodProvider::toArray() as $k => $v) {
	?><div class="sln-box--sub row sln-box--payment-mode-data payment-mode-data"  style="display: none;" id="payment-mode-<?php echo $k ?>"><?php
echo SLN_Enum_PaymentMethodProvider::getService($k, $this->plugin)->renderSettingsFields(
		array('adminSettings' => $this));
	?></div><?php
}
?>
    <?php ?>
    <div class="clearfix"></div>
</div>
</div>
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6">
        <div id="sln-pay_later" class="sln-box sln-box--main sln-box--main--small">
        <h2 class="sln-box-title"><?php _e('Pay later', 'salon-booking-system');?></span></h2>
        <div class="row">
                <div class="col-xs-12">
                    <div class="sln-switch">
                    <?php $this->row_input_checkbox_switch(
	'pay_cash',
	'Pay later Status',
	array(
		'bigLabelOn' => 'Pay later is enabled',
		'bigLabelOff' => 'Pay later is disabled',
	)
);?>
</div>
<div class="sln-box-maininfo">
                <p class="sln-box-info"><?php _e('Give users the option to pay once they are at your salon.', 'salon-booking-system');?></p>
            </div>
                </div>
            </div>
        </div>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6">
            <div id="sln-pay_a_deposit" class="sln-box sln-box--main sln-box--main--small">
                <h2 class="sln-box-title"><?php _e('Pay a deposit', 'salon-booking-system');?></h2>
                <div class="row">
                    <div class="col-xs-12 col-md-7 form-group sln-select  sln-select--info-label">
                        <label for="salon_settings_pay_deposit"><?php _e('Percentage ', 'salon-booking-system')?></label>
                        <?php SLN_Form::fieldSelect(
	'salon_settings[pay_deposit]',
	SLN_Enum_PaymentDepositType::toArray(),
	$this->settings->get('pay_deposit'),
	array(),
	true
)?>
                    </div>
                    <div class="col-xs-12 col-md-5 form-group sln-input--simple">
    					<?php $this->row_input_text('pay_deposit_fixed_amount', __('Fixed amount', 'salon-booking-system'), array('attrs' => array('data-relate-to' => SLN_Enum_PaymentDepositType::FIXED)));?>
                        <p class="sln-input-help"><?php _e('Specify the amount without currency symbol', 'salon-booking-system');?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6">
            <div id="sln-tip_request" class="sln-box sln-box--main sln-box--main--small">
                <h2 class="sln-box-title"><?php _e('Tip request', 'salon-booking-system')?></h2>
                <div class="row">
                    <div class="col-xs-12 form-group">
                        <div class="sln-checkbox">
                            <?php $this->row_input_checkbox('pay_tip_request', __('Enable tip field', 'salon-booking-system'));?>
                        </div>
                        <div class="sln-box-maininfo">
                            <p class="sln-box-info"><?php _e('Select this option if you want collect tips from customers.', 'salon-booking-system')?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <div class="col-xs-12 col-sm-6 col-md-6">
            <div id="sln-apply_transaction_fee" class="sln-box sln-box--main sln-box--main--small">
                <h2 class="sln-box-title"><?php _e('Apply a transaction fee', 'salon-booking-system')?></h2>
                <div class="row">
                    <div class="col-xs-12 col-md-5 sln-input--simple">
                        <?php $this->row_input_text('pay_transaction_fee_amount', __('Amount', 'salon-booking-system'));?>
                        <p class="sln-input-help"><?php _e('Specify the fee amount without currency symbol', 'salon-booking-system')?></p>
                    </div>
            <div class="col-xs-12 col-md-7 form-group sln-select">
            <label for="salon_settings_pay_transaction_fee_type">
                <?php _e('Select mode', 'salon-booking-system')?>
            </label>
            <?php SLN_Form::fieldSelect(
	'salon_settings[pay_transaction_fee_type]',
	array(
		'percent' => __('Percentage', 'salon-booking-system'),
		'fixed' => __('Fixed', 'salon-booking-system'),
	),
	$this->settings->get('pay_transaction_fee_type'),
	array(),
	true
)?>
               <p class="sln-input-help"><?php _e('Choose among "Percentage" and "Fixed" mode', 'salon-booking-system')?></p>
            </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6">
            <div id="sln-prices_visibility" class="sln-box sln-box--main sln-box--main--small">
                <h2 class="sln-box-title"><?php _e('Prices visibility', 'salon-booking-system')?></h2>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="sln-checkbox">
                        <?php $this->row_input_checkbox('hide_prices', __('Hide Prices', 'salon-booking-system'));?>
                        </div>
                    <div class="sln-box-maininfo">
                        <p class="sln-box-info"><?php _e('Select this Option if you want to hide all prices from the front end.<br/>Note: Online Payment will be disabled.', 'salon-booking-system')?></p>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6">
            <div id="sln-pending_payment_email" class="sln-box sln-box--main sln-box--main--small">
                <h2 class="sln-box-title"><?php _e('Pending payment email', 'salon-booking-system')?></h2>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="sln-checkbox">
                        <?php $this->row_input_checkbox('disable_first_pending_payment_email_to_customer', __('Disable the first email sent to the customer', 'salon-booking-system'));?>
                        </div>
                    <div class="sln-box-maininfo">
                        <p class="sln-box-info"><?php _e('Select this Option if you want to disable first pending payment email sent to the customer.', 'salon-booking-system')?></p>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6">
            <div id="sln-minimum_order_amount" class="sln-box sln-box--main sln-box--main--small">
        <h2 class="sln-box-title"><?php _e('Minimum order amount', 'salon-booking-system');?></h2>
        <div class="row">
            <div class="col-xs-12 col-sm-5 col-md-8">
                <div class="sln-input--simple">
        <?php $this->row_input_text('pay_minimum_order_amount', __('Amount', 'salon-booking-system'));?>
                </div>
                <div class="sln-box-maininfo">
                        <p class="sln-box-info"><?php _e('Specify the amount without currency symbol', 'salon-booking-system');?></p>
                </div>
        </div>
        </div>
    </div>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6">
            <div id="sln-unpaid_reservations" class="sln-box sln-box--main sln-box--main--small">
                <h2 class="sln-box-title"><?php _e('Unpaid reservations', 'salon-booking-system');?></h2>
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-6 form-group sln-checkbox">
                <?php $this->row_input_checkbox(
	'pay_offset_enabled',
	__('Enable cancellation', 'salon-booking-system'),
	array('help' => __('Select this option if you want to automatically cancel unpaid reservations.', 'salon-booking-system'))
);?>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-6 form-group sln-select ">
                        <label><?php _e('Delete unpaid reservations after', 'salon-booking-system');?></label>
                <?php echo SLN_Form::fieldSelect(
	'salon_settings[pay_offset]',
	array(

		'3' => '3m',
		'15' => '15m',
		'30' => '30m',
		'60' => '1h',
		'120' => '2h',
		'360' => '6h',
		'720' => '12h',
		'1440' => '24h',
		'2880' => '48h',
	),
	$this->settings->get('pay_offset'),
	array(),
	true
) ?>
                        <p class="help-block"><?php _e('Set the time range to complete the online payment.', 'salon-booking-system')?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6">
            <div id="sln-create_booking_after_pay" class="sln-box sln-box--main sln-box--main--smal">
                <h2 class="sln-box-title"><?php _e('Create booking after payment only', 'salon-booking-system'); ?></h2>
                <div class="row">
                    <div class="col-xs-12 form-group">
                        <div class="sln-checkbox">
                            <?php $this->row_input_checkbox('create_booking_after_pay', __('Enable creating booking after paying field', 'salon-booking-system')); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>