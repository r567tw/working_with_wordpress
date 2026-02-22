<?php

/**
 * Plugin Name: WooCommerce Category Toggle Widget
 * Description: WooCommerce 商品分類可展開/收合 Widget
 * Version: 1.0
 * Author: Your Name
 */

if (!defined('ABSPATH')) exit;

/**
 * 註冊 Widget
 */
add_action('widgets_init', function () {
    register_widget('WC_Product_Cat_Toggle_Widget');
});

class WC_Product_Cat_Toggle_Widget extends WP_Widget
{

    public function __construct()
    {
        parent::__construct(
            'wc_product_cat_toggle',
            'Woo 商品分類（可收合）'
        );
    }

    public function widget($args, $instance)
    {
        echo $args['before_widget'];
        echo $args['before_title'] . '商品分類' . $args['after_title'];

        echo '<ul class="wc-cat-toggle">';

        // 只顯示主分類（父級分類）
        $parent_categories = get_terms([
            'taxonomy'   => 'product_cat',
            'hide_empty' => true,
            'parent'     => 0, // 只取頂級分類
        ]);

        if (!empty($parent_categories) && !is_wp_error($parent_categories)) {
            foreach ($parent_categories as $parent_cat) {
                $this->display_category_item($parent_cat);
            }
        }

        echo '</ul>';

        echo $args['after_widget'];
    }

    /**
     * 顯示分類項目（遞迴顯示子分類）
     */
    private function display_category_item($category)
    {
        // 取得該分類的子分類
        $children = get_terms([
            'taxonomy'   => 'product_cat',
            'hide_empty' => true,
            'parent'     => $category->term_id,
        ]);

        $has_children = !empty($children) && !is_wp_error($children);
        $cat_class = $has_children ? 'cat-parent' : '';

        echo '<li class="' . esc_attr($cat_class) . '">';

        // 如果有子分類，顯示箭頭
        if ($has_children) {
            echo '<span class="wc-cat-arrow">▸</span>';
        }

        // 分類連結
        $cat_link = get_term_link($category);
        echo '<a href="' . esc_url($cat_link) . '">';
        echo esc_html($category->name);
        echo ' <span class="count">(' . $category->count . ')</span>';
        echo '</a>';

        // 如果有子分類，遞迴顯示
        if ($has_children) {
            echo '<ul class="children" style="display:none;">';
            foreach ($children as $child_cat) {
                $this->display_category_item($child_cat);
            }
            echo '</ul>';
        }

        echo '</li>';
    }
}

/**
 * 前端 JS
 */
add_action('wp_footer', function () {
?>
    <script>
        (function() {
            'use strict';

            function initCategoryToggle() {
                // 為所有箭頭添加點擊事件
                var arrows = document.querySelectorAll('.wc-cat-toggle .wc-cat-arrow');

                arrows.forEach(function(arrow) {
                    arrow.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        // 找到對應的父 li 和子分類列表
                        var li = arrow.parentElement;
                        var childList = li.querySelector('ul.children');

                        if (childList) {
                            // 切換顯示/隱藏
                            var isOpen = childList.style.display === 'block';
                            childList.style.display = isOpen ? 'none' : 'block';

                            // 切換箭頭方向
                            arrow.textContent = isOpen ? '▸' : '▾';

                            // 添加/移除展開狀態的 class
                            if (isOpen) {
                                li.classList.remove('is-open');
                            } else {
                                li.classList.add('is-open');
                            }
                        }
                    });
                });
            }

            // DOM 載入完成後執行
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initCategoryToggle);
            } else {
                initCategoryToggle();
            }
        })();
    </script>
<?php
});

/**
 * CSS
 */
add_action('wp_head', function () {
?>
    <style>
        .wc-cat-toggle {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .wc-cat-toggle li {
            list-style: none;
            margin: 8px 0;
            position: relative;
        }

        .wc-cat-toggle .children {
            margin-left: 20px;
            padding-left: 10px;
            border-left: 2px solid #e0e0e0;
            margin-top: 8px;
        }

        .wc-cat-arrow {
            cursor: pointer;
            display: inline-block;
            width: 20px;
            height: 20px;
            line-height: 20px;
            text-align: center;
            user-select: none;
            font-weight: bold;
            color: #666;
            transition: transform 0.2s ease;
            margin-right: 5px;
        }

        .wc-cat-arrow:hover {
            color: #333;
            background-color: #f0f0f0;
            border-radius: 3px;
        }

        .wc-cat-toggle li.is-open>.wc-cat-arrow {
            transform: rotate(90deg);
        }

        .wc-cat-toggle a {
            text-decoration: none;
            color: #333;
            transition: color 0.2s ease;
        }

        .wc-cat-toggle a:hover {
            color: #0073aa;
        }

        .wc-cat-toggle .count {
            color: #999;
            font-size: 0.9em;
        }
    </style>
<?php
});

