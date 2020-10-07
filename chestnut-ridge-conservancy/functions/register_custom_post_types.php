<?php
register_post_type('auction-item', [
    'labels' => [
        'name' => __('Auction Items'),
        'singular_name' => __('Auction Item')
        // 'add_new_item' => __('Add Item'),
        // 'all_items' => __('All Items', 'text_domain'),
    ],
    'menu_icon' => __('dashicons-clipboard'),
    'public' => true,
    'has_archive' => false,
    // 'rewrite' => [
    // 'with_front' => false,
    // 'slug' => 'careers',
    // ],
    'supports' => ['title', 'thumbnail', 'revisions'],
    'exclude_from_search' => true
]);
