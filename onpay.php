<?php
/* -----------------------------------------------------------------------------------------
   OnPay payment module for ShopOS based on VamShop commerce
   http://onpay.ru
   http://vamshop.com

   Copyright (c) 2010 Norgen.
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(moneyorder.php,v 1.8 2003/02/16); www.oscommerce.com 
   (c) 2003   nextcommerce (moneyorder.php,v 1.4 2003/08/13); www.nextcommerce.org
   (c) 2004	 xt:Commerce (webmoney.php,v 1.4 2003/08/13); xt-commerce.com

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

function get_var($name, $default = 'none') {
  return (isset($_GET[$name])) ? $_GET[$name] : ((isset($_POST[$name])) ? $_POST[$name] : $default);
}

require('includes/top.php');
require (DIR_WS_CLASSES.'order.php');

$order = new order(get_var('pay_for'));


if (get_var('type') == 'check') {
   $md5 = strtoupper(md5('check'.';'.get_var('pay_for').';'.get_var('order_amount').';'.'RUR'.';'.'0'.';'.MODULE_PAYMENT_ONPAY_SECRET_KEY));
   $text = 'Payment Confirmed';
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<result>\n<code>0</code>\n<pay_for>".get_var('pay_for')."</pay_for>\n<comment>$text</comment>\n<md5>$md5</md5>\n</result>";
}

if (get_var('type') == 'pay') {

$crc = strtoupper(md5('pay'.';'.get_var('pay_for').';'.get_var('onpay_id').';'.get_var('order_amount').';'.'RUR'.';'.MODULE_PAYMENT_ONPAY_SECRET_KEY));
$hash = get_var('md5');

if($crc == $hash) {
if (number_format(get_var('order_amount'),0) == number_format($order->info['total'],0)) {
  $sql_data_array = array('orders_status' => MODULE_PAYMENT_ONPAY_ORDER_STATUS_ID);
  os_db_perform('os_orders', $sql_data_array, 'update', "orders_id='".get_var('pay_for')."'");

  $sql_data_arrax = array('orders_id' => get_var('pay_for'),
                          'orders_status_id' => MODULE_PAYMENT_WEBMONEY_MERCHANT_ORDER_STATUS_ID,
                          'date_added' => 'now()',
                          'customer_notified' => '0',
                          'comments' => 'OnPay.Ru accepted this order payment');
  os_db_perform('os_orders_status_history', $sql_data_arrax);

}
}
   $md5 = strtoupper(md5('pay'.';'.get_var('pay_for').';'.get_var('onpay_id').';'.get_var('pay_for').';'.get_var('order_amount').';'.'RUR'.';'.'0'.';'.MODULE_PAYMENT_ONPAY_SECRET_KEY));
   $text = 'Payment Received';
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<result>\n<code>0</code>\n <comment>$text</comment>\n<onpay_id>".get_var('onpay_id')."</onpay_id>\n <pay_for>".get_var('pay_for')."</pay_for>\n<order_id>".get_var('pay_for')."</order_id>\n<md5>$md5</md5>\n</result>";

}

?>
