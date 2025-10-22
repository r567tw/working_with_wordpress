<?php
add_action('init', function() {
    if ( ! class_exists('WooCommerce') ) return; // 確保 WooCommerce 已啟用

    // 在加入購物車的 validation 階段先清空現有購物車（這個 filter 在 AJAX 與非 AJAX 都會被呼叫）
    add_filter('woocommerce_add_to_cart_validation', function($passed, $product_id, $quantity, $variation_id = 0, $variations = []) {
        if ( WC()->cart && WC()->cart->get_cart_contents_count() > 0 ) {
            WC()->cart->empty_cart();
        }
        return $passed;
    }, 10, 5);

    // 強制加入數量為 1（來源可能是商品頁或自訂請求）
    add_filter('woocommerce_add_to_cart_quantity', function($qty, $product_id) {
        return 1;
    }, 10, 2);

    // 防止購物車頁面或惡意請求把數量改成 >1（支援 AJAX）
    add_action('woocommerce_before_calculate_totals', function($cart) {
        if ( is_admin() && ! defined('DOING_AJAX') ) return;
        foreach ( $cart->get_cart() as $key => $item ) {
            if ( $item['quantity'] > 1 ) {
                $cart->set_quantity( $key, 1, false ); // false 避免重複重新計算造成迴圈
            }
        }
    });
});
