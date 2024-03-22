
# 顯示目前用戶的權限與角色
```php
add_action('wp_head', 'display_current_user_role_and_capabilities');

function display_current_user_role_and_capabilities() {
    // 檢查用戶是否已登錄
    if (is_user_logged_in()) {
        // 獲取當前用戶對應的 WP_User 對象
        $current_user = wp_get_current_user();
        
        // 獲取用戶的角色名稱
        $user_roles = $current_user->roles;
        // 獲取用戶的角色權限
        $user_capabilities = $current_user->allcaps;
        
        // 將角色名稱和權限列印到 head 部分
        echo  "roles:".implode(', ', $user_roles);
		echo "<br/>";
        echo  "can:".implode(', ', array_keys($user_capabilities));
    }
}
```