<?php
/**
 * Print Subscription needs a real mailing address to deliver the paper.
 * PMPro's PayPal Express gateway sends NOSHIPPING=1 by default, which
 * skips PayPal's own shipping-address prompt entirely. Stripping it here
 * (rather than editing PMPro core) means PayPal asks for the address
 * during its hosted checkout step, and that address then shows up
 * automatically in PayPal's payment-notification email to the site owner.
 */
add_filter( 'pmpro_set_express_checkout_nvpstr', 'fmx_remove_paypal_noshipping', 10, 2 );
add_filter( 'pmpro_do_express_checkout_payment_nvpstr', 'fmx_remove_paypal_noshipping', 10, 2 );
function fmx_remove_paypal_noshipping( $nvpStr, $order ) {
	return str_replace( '&NOSHIPPING=1', '', $nvpStr );
}
