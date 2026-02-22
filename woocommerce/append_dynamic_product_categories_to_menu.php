<?php

/**
 * 功能：在 WordPress 導覽選單中動態注入 WooCommerce 商品子分類
 * 觸發時機：當 wp_nav_menu 準備輸出選單項目物件列表時
 */
add_filter('wp_nav_menu_objects', function ($items, $args) {

    // 用來儲存重新組合後的選單項目（包含原本項目與動態插入的分類）
    $new_items = [];

    foreach ($items as $item) {
        // 1. 先把原本就在選單裡的項目存入新陣列，確保原有結構不變
        $new_items[] = $item;

        // 2. 條件過濾：僅針對標題為「商品分類」的選單項目進行擴充
        // 如果你的選單後台改名了，這裡也需要跟著改
        if ($item->title !== '商品分類') continue;

        // 3. 從資料庫抓取指定的 WooCommerce 分類
        $terms = get_terms([
            'taxonomy'   => 'product_cat', // 指定商品分類法
            'parent'     => 22,           // 僅抓取父分類 ID 為 22 的子項
            'hide_empty' => true,         // 沒商品的分類不顯示，避免導向空頁面
        ]);

        // 檢查抓取是否出錯或是否根本沒有子分類
        if (is_wp_error($terms) || empty($terms)) continue;

        // 4. 修改父項目的 CSS 類別，確保前端能正常渲染「下拉箭頭」或「子選單樣式」
        $item->classes[] = 'menu-item-has-children';

        // 5. 巡迴所有抓到的分類，並「偽裝」成 WordPress 選單物件
        foreach ($terms as $term) {
            // 手動建立一個與 WP_Post 選單物件結構相似的物件
            $sub_item = (object) [
                'ID'                => 'cat-' . $term->term_id,    // 唯一辨識碼
                'db_id'             => 'cat-' . $term->term_id,    // 資料庫辨識碼
                'title'             => $term->name,                // 顯示的分類名稱
                'url'               => get_term_link($term),       // 分類頁面的連結
                'menu_item_parent'  => $item->ID,                  // 關鍵：將父 ID 指向剛才的「商品分類」
                'menu_order'        => 0,                          // 排序（此處設為 0 會依抓取順序排列）
                'classes'           => ['menu-item', 'menu-item-type-taxonomy'], // 賦予標準選單類別
                'target'            => '',                         // 是否另開視窗
                'attr_title'        => '',                         // 連結標題屬性
                'description'       => '',                         // 描述
                'xfn'               => '',                         // 關係標籤 (Rel)
                'current'           => false,                      // 預設不設為「目前頁面」
                'current_item_ancestor' => false,
                'current_item_parent'   => false,
            ];

            // 6. 將偽裝好的子項目緊跟在父項目之後存入新陣列
            $new_items[] = $sub_item;
        }
    }

    // 回傳重新建構後的完整選單陣列
    return $new_items;
}, 10, 2);
