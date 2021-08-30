<?php

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

$endpoint   = get_option('eupago_endpoint');
$trid       = get_post_meta( $_GET['refund_order'], '_transaction_id', true );

if (!empty($_GET['refund_name']) && !empty($_GET['refund_iban']) && !empty($_GET['refund_bic']) && !empty($_GET['refund_amount']) && !empty($_GET['refund_reason'])) {
    //Token
    $body_token = [
        "grant_type"      => 'password',
        "client_id"       => get_option('eupago_client_id'),
        "client_secret"   => get_option('eupago_client_secret'),
        "username"        => get_option('eupago_user'),
        "password"        => get_option('eupago_password')
    ];
      
    $headers_token = [ 'Content-Type: application/json' ];
      
    $curlOpts_token = [
        CURLOPT_URL             => 'https://' . $endpoint . '.eupago.pt/api/auth/token',
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_POST            => 1,
        CURLOPT_TIMEOUT         => 60,
        CURLOPT_POSTFIELDS      => json_encode($body_token),
        CURLOPT_HTTPHEADER      => $headers_token,
    ];
      
    $curl_token = curl_init();
    curl_setopt_array($curl_token, $curlOpts_token);
    $response_body_token = curl_exec($curl_token);
    curl_close($curl_token);
    $response_token = json_decode($response_body_token, true);
    $access_token = $response_token['access_token'];

    //Refund request
    $body_refund = [
        'name'      => $_GET['refund_name'],
        'iban'      => $_GET['refund_iban'],
        'bic'       => $_GET['refund_bic'],
        'amount'    => floatval($_GET['refund_amount']),
        'reason'    =>$_GET['refund_reason']
    ];
   
    $headers_refund = [
        "Authorization: Bearer " . $access_token,
        'Content-Type: application/json'
    ];

    $curlOpts_refund = [
        CURLOPT_URL             => 'https://'. $endpoint . '.eupago.pt/api/management/v1.02/refund/' . $trid,
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_POST            => 1,
        CURLOPT_TIMEOUT         => 60,
        CURLOPT_POSTFIELDS      => json_encode($body_refund),
        CURLOPT_HTTPHEADER      => $headers_refund,
    ];
      
    $curl_refund = curl_init();
    curl_setopt_array($curl_refund, $curlOpts_refund);
    $response_body_refund = curl_exec($curl_refund);
    curl_close($curl_refund);
    $response_refund = json_decode($response_body_refund, true);
 

    if ($response_refund['transactionStatus'] == 'Success') {
        $output_class     = 'eupago-output-success';
        $output_request   = __('Request made successfully', 'eupago-gateway-for-woocommerce');
    } else {
        $output_class     = 'eupago-output-error';
        if ($response_refund['code'] == 'IBAN_INVALID') {
            $output_request   = __('IBAN Invalid', 'eupago-gateway-for-woocommerce');
        } else if ($response_refund['code'] == 'BIC_INVALID') {
            $output_request   = __('BIC Invalid', 'eupago-gateway-for-woocommerce');
        } else if ($response_refund['code'] == 'AMOUNT_INVALID') {
            $output_request   = __('Amount Invalid', 'eupago-gateway-for-woocommerce');
        } else {
            $output_request   = __('Request error', 'eupago-gateway-for-woocommerce');
        }
    }
} else {
    $output_class     = 'eupago-output-error';
    $output_request   = __('Fill all fields', 'eupago-gateway-for-woocommerce');
}

echo '<p class="' . $output_class . '">' . $output_request . '</p>';

?>