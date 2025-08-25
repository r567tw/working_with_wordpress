<?php
/**
 * Plugin Name: Hide Login URL
 * Description: 隱藏 WordPress 預設登入頁面，並設定自訂的登入 URL。
 * Version: 1.0
 * Author: Your Name
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// 定義自訂的登入 URL
define('CUSTOM_LOGIN_SLUG', 'my-secret-login');

// 攔截登入請求並重導
add_action('init', function () {
    // 若訪問的是預設登入頁，則重導到首頁
    if (strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false) {
        wp_redirect(home_url());
        exit;
    }
    
    // 若訪問自訂登入 URL，載入預設的登入頁面
    if (strpos($_SERVER['REQUEST_URI'], CUSTOM_LOGIN_SLUG) !== false) {
        require_once ABSPATH . 'wp-login.php';
        exit;
    }
});

// 攔截登出請求並重導
add_action('wp_logout', function () {
    wp_redirect(home_url());
    exit;
});

// 攔截登入後重導
add_filter('login_redirect', function ($redirect_to, $request, $user) {
    return home_url(); // 登入後重導到首頁或其他位置
}, 10, 3);
