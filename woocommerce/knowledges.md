
# $gateway->supports
- tokenization: 允許使用者在「我的帳號」增加付款方式


# WooCommerce 訂單創建流程
1. 訂單建立（存進數據庫）：客戶完成結賬信息並提交訂單後，WooCommerce 先將訂單信息存儲進數據庫。
2. woocommerce_checkout_order_processed 鉤子觸發
3. $gateway->process_payment() 


#  $gateway->process_payment v.s woocommerce_checkout_order_processed
## WooCommerce checkout_order_processed
- 觸發時機：此鉤子在訂單數據被處理並存入數據庫之後觸發，但在支付過程之前。它允許開發者在訂單被最終確認前對訂單數據進行訪問或修改。
- 用途：主要用於訪問和操作訂單數據，例如添加自定義訂單元數據、執行基於訂單信息的自定義動作等。適合於不需要改變支付流程，但需要在訂單創建後立即執行操作的場景。

## $gateway->process_payment()
- 觸發時機：process_payment 方法在訂單提交到 Gateway 進行支付處理時被調用。它是整個支付流程的核心，負責處理支付邏輯，包括與支付服務提供商的通信。
- 用途：用於實現具體的支付邏輯，例如發送請求到支付服務提供商、處理響應、設置訂單狀態為已支付或支付失敗等。是自定義支付網關或修改現有支付流程的關鍵點。

## Summary
- 流程差異：checkout_order_processed 主要關注訂單的創建和處理，而 process_payment 則專注於支付過程的具體實現。前者在訂單準備進入支付流程之前被觸發，後者則是在實際進行支付處理時觸發。
- 使用場景：如果目標是在訂單被創建後立即執行某些非支付相關的操作（比如自定義日誌記錄、發送自定義通知等），則 checkout_order_processed 是更合適的選擇。若需要定制或干預實際的支付處理邏輯（如添加特定的支付參數、處理支付響應等），則應該使用 process_payment 方法。

## Order Object
- https://www.businessbloomer.com/woocommerce-easily-get-order-info-total-items-etc-from-order-object/
```
// Get Order ID and Key
$order->get_id();
$order->get_order_key();
 
// Get Order Totals
$order->get_formatted_order_total();
$order->get_cart_tax();
$order->get_currency();
$order->get_discount_tax();
$order->get_discount_to_display();
$order->get_discount_total();
$order->get_total_fees();
$order->get_formatted_line_subtotal();
$order->get_shipping_tax();
$order->get_shipping_total();
$order->get_subtotal();
$order->get_subtotal_to_display();
$order->get_tax_location();
$order->get_tax_totals();
$order->get_taxes();
$order->get_total();
$order->get_total_discount();
$order->get_total_tax();
$order->get_total_refunded();
$order->get_total_tax_refunded();
$order->get_total_shipping_refunded();
$order->get_item_count_refunded();
$order->get_total_qty_refunded();
$order->get_qty_refunded_for_item();
$order->get_total_refunded_for_item();
$order->get_tax_refunded_for_item();
$order->get_total_tax_refunded_by_rate_id();
$order->get_remaining_refund_amount();
  
// Get and Loop Over Order Items
foreach ( $order->get_items() as $item_id => $item ) {
   $product_id = $item->get_product_id();
   $variation_id = $item->get_variation_id();
   $product = $item->get_product(); // see link above to get $product info
   $product_name = $item->get_name();
   $quantity = $item->get_quantity();
   $subtotal = $item->get_subtotal();
   $total = $item->get_total();
   $tax = $item->get_subtotal_tax();
   $tax_class = $item->get_tax_class();
   $tax_status = $item->get_tax_status();
   $allmeta = $item->get_meta_data();
   $somemeta = $item->get_meta( '_whatever', true );
   $item_type = $item->get_type(); // e.g. "line_item", "fee"
}
 
// Other Secondary Items Stuff
$order->get_items_key();
$order->get_items_tax_classes();
$order->get_item_count();
$order->get_item_total();
$order->get_downloadable_items();
$order->get_coupon_codes();
  
// Get Order Lines
$order->get_line_subtotal();
$order->get_line_tax();
$order->get_line_total();
  
// Get Order Shipping
$order->get_shipping_method();
$order->get_shipping_methods();
$order->get_shipping_to_display();
  
// Get Order Dates
$order->get_date_created();
$order->get_date_modified();
$order->get_date_completed();
$order->get_date_paid();
  
// Get Order User, Billing & Shipping Addresses
$order->get_customer_id();
$order->get_user_id();
$order->get_user();
$order->get_customer_ip_address();
$order->get_customer_user_agent();
$order->get_created_via();
$order->get_customer_note();
$order->get_address_prop();
$order->get_billing_first_name();
$order->get_billing_last_name();
$order->get_billing_company();
$order->get_billing_address_1();
$order->get_billing_address_2();
$order->get_billing_city();
$order->get_billing_state();
$order->get_billing_postcode();
$order->get_billing_country();
$order->get_billing_email();
$order->get_billing_phone();
$order->get_shipping_first_name();
$order->get_shipping_last_name();
$order->get_shipping_company();
$order->get_shipping_address_1();
$order->get_shipping_address_2();
$order->get_shipping_city();
$order->get_shipping_state();
$order->get_shipping_postcode();
$order->get_shipping_country();
$order->get_address();
$order->get_shipping_address_map_url();
$order->get_formatted_billing_full_name();
$order->get_formatted_shipping_full_name();
$order->get_formatted_billing_address();
$order->get_formatted_shipping_address();
  
// Get Order Payment Details
$order->get_payment_method();
$order->get_payment_method_title();
$order->get_transaction_id();
  
// Get Order URLs
$order->get_checkout_payment_url();
$order->get_checkout_order_received_url();
$order->get_cancel_order_url();
$order->get_cancel_order_url_raw();
$order->get_cancel_endpoint();
$order->get_view_order_url();
$order->get_edit_order_url();
  
// Get Order Status
$order->get_status();
 
// Get Thank You Page URL
$order->get_checkout_order_received_url();

// Get $order object from order ID
  
$order = wc_get_order( $order_id );
  
// Now you have access to (see above)...
  
if ( $order ) {
   $order->get_formatted_order_total( );
   // etc.
   // etc.
}

// Get $order object from $email
  
$order = $email->object;
  
// Now you have access to (see above)...
  
if ( $order ) {
   $order->get_id();
   $order->get_formatted_order_total( );
   // etc.
   // etc.
}

```

## 如何用 coding 隱藏 WordPress Login Url
