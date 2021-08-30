<?php
/**
* EuPago Refund.
*/

// Send sms for pending order.
function send_sms_pending($order_id) {
   $phone = get_post_meta($order_id, '_billing_phone', true);
   if ((get_option('eupago_sms_enable') == 'yes') && (get_option('eupago_sms_payment_hold') == 'yes') && (!empty($phone))) { 
      $payment_method = get_post_meta($order_id, '_payment_method', true);

      if ($payment_method == 'eupago_multibanco') {
         $entity        = get_post_meta($order_id, '_eupago_multibanco_entidade', true);
         $reference     = get_post_meta($order_id, '_eupago_multibanco_referencia', true);
         $amount        = get_post_meta($order_id, '_order_total', true) . get_post_meta($order_id, '_order_currency', true);
         $payment_data  = __( 'Entity:', 'eupago-gateway-for-woocommerce' ) . ' ' . $entity . ' ' . __( 'Reference:', 'eupago-gateway-for-woocommerce' ) . ' ' . $reference . ' ' . __( 'Value:', 'eupago-gateway-for-woocommerce' ) . ' ' . $amount; 
      } else if ($payment_method == 'eupago_payshop') {
         $reference     = get_post_meta($order_id, '_eupago_payshop_referencia', true);
         $amount        = get_post_meta($order_id, '_order_total', true) . get_post_meta($order_id, '_order_currency', true);
         $payment_data  =  __( 'Reference:', 'eupago-gateway-for-woocommerce' ) . ' ' . $reference . ' ' . __( 'Value:', 'eupago-gateway-for-woocommerce' ) . ' ' . $amount;  
      }

      $message = __( 'Your order', 'eupago-gateway-for-woocommerce' ) . ' #' . $order_id . ' ' . __( 'on', 'eupago-gateway-for-woocommerce' ) . ' ' . get_bloginfo('name') . ' ' . __( 'is completed. Payment details:', 'eupago-gateway-for-woocommerce' ) . ' ' . $payment_data;
      
      $body = [
         'mobile_num'    => $phone,
         'message'       => $message,
         'sender'        => get_option('eupago_intelidus_sender')
      ];
      
      $headers = [ 'Content-Type: application/json'];
      
      $curlOpts = [
         CURLOPT_URL             => 'https://dash.intelidus360.com/api/addSMS?accountid=' . get_option('eupago_sms_intelidus_id') . '&apikey=' . get_option('eupago_sms_intelidus_api'),
         CURLOPT_RETURNTRANSFER  => true,
         CURLOPT_POST            => 1,
         CURLOPT_TIMEOUT         => 60,
         CURLOPT_POSTFIELDS      => json_encode($body),
         CURLOPT_HTTPHEADER      => $headers,
      ];
      
      $curl = curl_init();
      curl_setopt_array($curl, $curlOpts);
      $response_body = curl_exec($curl);
      curl_close($curl);
      $response = json_decode($response_body, true);
   }
}

add_action( 'woocommerce_order_status_pending', 'send_sms_pending');
add_action( 'woocommerce_order_status_on-hold', 'send_sms_pending');


// Send sms for paid order.
function send_sms_processing($order_id) {
   $phone = get_post_meta($order_id, '_billing_phone', true);
   if ((get_option('eupago_sms_enable') == 'yes') && (get_option('eupago_sms_payment_confirmation') == 'yes') && (!empty($phone))) {

      $message = __( 'We have received your payment regarding your order', 'eupago-gateway-for-woocommerce' ) . ' #' . $order_id . ' ' . __( 'on', 'eupago-gateway-for-woocommerce' ) . ' ' . get_bloginfo('name') . '.';

      $body = [
         'mobile_num'    => $phone,
         'message'       => $message,
         'sender'        => get_option('eupago_intelidus_sender')
      ];
      
      $headers = [ 'Content-Type: application/json'];
      
      $curlOpts = [
         CURLOPT_URL             => 'https://dash.intelidus360.com/api/addSMS?accountid=' . get_option('eupago_sms_intelidus_id') . '&apikey=' . get_option('eupago_sms_intelidus_api'),
         CURLOPT_RETURNTRANSFER  => true,
         CURLOPT_POST            => 1,
         CURLOPT_TIMEOUT         => 60,
         CURLOPT_POSTFIELDS      => json_encode($body),
         CURLOPT_HTTPHEADER      => $headers,
      ];
      
      $curl = curl_init();
      curl_setopt_array($curl, $curlOpts);
      $response_body = curl_exec($curl);
      curl_close($curl);
      $response = json_decode($response_body, true);
   }
}

add_action( 'woocommerce_order_status_processing', 'send_sms_processing');


// Send sms for completed order.
function send_sms_completed($order_id) {
   $phone = get_post_meta($order_id, '_billing_phone', true);
   if ((get_option('eupago_sms_enable') == 'yes') && (get_option('eupago_sms_order_confirmation') == 'yes') && (!empty($phone))) {
      $message = sprintf( __( 'Your order #%d on %s is now finished.', 'eupago-gateway-for-woocommerce' ), $order_id, get_bloginfo('name') );
      $body = [
         'mobile_num'    => $phone,
         'message'       => $message,
         'sender'        => get_option('eupago_intelidus_sender')
      ];
      
      $headers = [ 'Content-Type: application/json'];
      
      $curlOpts = [
         CURLOPT_URL             => 'https://dash.intelidus360.com/api/addSMS?accountid=' . get_option('eupago_sms_intelidus_id') . '&apikey=' . get_option('eupago_sms_intelidus_api'),
         CURLOPT_RETURNTRANSFER  => true,
         CURLOPT_POST            => 1,
         CURLOPT_TIMEOUT         => 60,
         CURLOPT_POSTFIELDS      => json_encode($body),
         CURLOPT_HTTPHEADER      => $headers,
      ];
      
      $curl = curl_init();
      curl_setopt_array($curl, $curlOpts);
      $response_body = curl_exec($curl);
      curl_close($curl);
      $response = json_decode($response_body, true);
   }
}

add_action( 'woocommerce_order_status_completed', 'send_sms_completed');

?>