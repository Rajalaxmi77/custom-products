<?php
// Block direct access to the plugin file
if (!defined('ABSPATH')) {
    die('Access denied.');
}
/*
Plugin Name: Custom Products
Description: Adds a custom product post type and taxonomy.
Version: 1.0
Author: Rajalaxmi
*/


// Register custom post type and taxonomy here
function create_product_post_type() {
    $labels = array(
        'name'               => __( 'Products', 'Product' ),
        'singular_name'      => __( 'Product', 'Product' ),
        'menu_name'          => __( 'Products', 'Product' ),
        'all_items'          => __( 'All Products', 'Product' ),
        'add_new'            => __( 'Add New', 'Product' ),
        'add_new_item'       => __( 'Add New Product', 'Product' ),
        'edit_item'          => __( 'Edit Product', 'Product' ),
        'new_item'           => __( 'New Product', 'Product' ),
        'view_item'          => __( 'View Product', 'Product' ),
        'search_items'       => __( 'Search Products', 'Product' ),
        'not_found'          => __( 'No products found', 'Product' ),
        'not_found_in_trash' => __( 'No products found in trash', 'Product' ),
        'parent_item_colon'  => __( 'Parent Product:', 'Product' ),
        'menu_icon'          => 'dashicons-cart', // Customize the menu icon as needed.
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => array( 'slug' => 'products' ),
        'supports'           => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
    );

    register_post_type( 'product', $args );

    // Add taxonomy for product categories
    $category_labels = array(
        'name'                       => _x( 'Categories', 'taxonomy general name', 'Product' ),
        'singular_name'              => _x( 'Category', 'taxonomy singular name', 'Product' ),
        'search_items'               => __( 'Search Categories', 'Product' ),
        'popular_items'              => __( 'Popular Categories', 'Product' ),
        'all_items'                  => __( 'All Categories', 'Product' ),
        'parent_item'                => null,
        'parent_item_colon'          => null,
        'edit_item'                  => __( 'Edit Category', 'Product' ),
        'update_item'                => __( 'Update Category', 'Product' ),
        'add_new_item'               => __( 'Add New Category', 'Product' ),
        'new_item_name'              => __( 'New Category Name', 'Product' ),
        'separate_items_with_commas' => __( 'Separate categories with commas', 'Product' ),
        'add_or_remove_items'        => __( 'Add or remove categories', 'Product' ),
        'choose_from_most_used'      => __( 'Choose from the most used categories', 'Product' ),
        'menu_name'                  => __( 'Categories', 'Product' ),
    );

    $category_args = array(
        'hierarchical'      => true,
        'labels'            => $category_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'product_category' ),
    );

    register_taxonomy( 'product_category', 'product', $category_args );

    // Add taxonomy for product brands
    $brand_labels = array(
        'name'                       => _x( 'Brands', 'taxonomy general name', 'Product' ),
        'singular_name'              => _x( 'Brand', 'taxonomy singular name', 'Product' ),
        'search_items'               => __( 'Search Brands', 'Product' ),
        'popular_items'              => __( 'Popular Brands', 'Product' ),
        'all_items'                  => __( 'All Brands', 'Product' ),
        'edit_item'                  => __( 'Edit Brand', 'Product' ),
        'update_item'                => __( 'Update Brand', 'Product' ),
        'add_new_item'               => __( 'Add New Brand', 'Product' ),
        'new_item_name'              => __( 'New Brand Name', 'Product' ),
        'separate_items_with_commas' => __( 'Separate brands with commas', 'Product' ),
        'add_or_remove_items'        => __( 'Add or remove brands', 'Product' ),
        'choose_from_most_used'      => __( 'Choose from the most used brands', 'Product' ),
        'menu_name'                  => __( 'Brands', 'Product' ),
    );

    $brand_args = array(
        'hierarchical'      => false,
        'labels'            => $brand_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'product_brand' ),
    );

    register_taxonomy( 'product_brand', 'product', $brand_args );
}

add_action( 'init', 'create_product_post_type' );

// Add meta boxes for actual price and sales price
function add_product_price_meta_boxes() {
    add_meta_box(
        'product_price',
        __( 'Product Price', 'Product' ),
        'product_price_callback',
        'product',
        'normal',
        'default'
    );

   
}

function product_price_callback($post) {
    // Get the actual price value for the current product
    $actual_price = get_post_meta($post->ID, '_product_actual_price', true);
    $sales_price = get_post_meta($post->ID, '_product_sales_price', true);

    // Output a input field for the actual price
    echo '<label for="product_actual_price">Product Price:</label>';
    echo '<input type="text" name="product_actual_price" placeholder="product price" value="' . esc_attr($actual_price) . '" />';
    echo '<label for="product_sales_price">Product Purchase Price:</label>';
    echo '<input type="text" name="product_sales_price" placeholder="product purchase price" value="' . esc_attr($sales_price) . '" />';

}

// Save the meta box data
function save_product_price_meta_data($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    // Save actual price
    if (isset($_POST['product_actual_price'])) {
        $actual_price = sanitize_text_field($_POST['product_actual_price']);
        update_post_meta($post_id, '_product_actual_price', $actual_price);
    }

    // Save sales price
    if (isset($_POST['product_sales_price'])) {
        $sales_price = sanitize_text_field($_POST['product_sales_price']);
        update_post_meta($post_id, '_product_sales_price', $sales_price);
    }
}

add_action('add_meta_boxes', 'add_product_price_meta_boxes');
add_action('save_post', 'save_product_price_meta_data');

