<?php
/**
* EuPago Refund.
*/

// Add meta box for refund order.
function eupago_refund() {
   add_meta_box(
      'woocommerce-order-refund',
      __('Refund Request', 'eupago-gateway-for-woocommerce'),
      'eupago_refund_content',
      'shop_order',
      'side',
      'default'
   );
}

add_action( 'add_meta_boxes', 'eupago_refund' );


// Refund request form
function eupago_refund_content() { ?>
   <div class="eupago-site-url"><?php echo site_url(); ?></div>
   <form method="POST" action="">
      <p><input class="eupago-field" type="text" name="refund_name" value="" placeholder="<?php echo __('Name', 'eupago-gateway-for-woocommerce'); ?>"></p>
      <p><input class="eupago-field" type="text" name="refund_iban" value="" placeholder="<?php echo __('IBAN', 'eupago-gateway-for-woocommerce'); ?>"></p>
      <p><input class="eupago-field" type="text" name="refund_bic" value="" placeholder="<?php echo __('BIC', 'eupago-gateway-for-woocommerce'); ?>"></p>
      <p><input class="eupago-field" type="text" name="refund_amount" value="<?php echo get_post_meta( $_GET['post'], '_order_total', true ); ?>" placeholder="<?php echo __('Amount', 'eupago-gateway-for-woocommerce'); ?>"></p>
      <p><input class="eupago-field" type="text" name="refund_reason" value="" placeholder="<?php echo __('Reason', 'eupago-gateway-for-woocommerce'); ?>"></p>
      <div class="button button-primary eupago-refund-request"><?php echo __('Request a refund', 'eupago-gateway-for-woocommerce'); ?></div>
   </form>

   <div class="eupago-refund-response"></div>
<?php  
}
?>