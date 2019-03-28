<?php
//Child Theme Functions File

add_action( 'wp_enqueue_scripts', 'enqueue_wp_child_theme' );

function enqueue_wp_child_theme() 
{
	wp_enqueue_style('parent-css', get_template_directory_uri().'/style.css' );

	wp_enqueue_style('child-css', get_stylesheet_uri());

	wp_enqueue_script('child-js', get_stylesheet_directory_uri() . '/js/script.js', array( 'jquery' ), '1.0', true );
}

// Adding funcionality to set Minimum Quantity requred to by "Group ticket" & change text "add tp cart"

if( ! function_exists( 'twentynineteen_child_wc_add_min_qty_product_field' ) ){
    
    // Adding field on product admin page in "inventory section"
    function twentynineteen_child_wc_add_min_qty_product_field() {
        echo '<div class="options_group">';
        woocommerce_wp_text_input( 
            array( 
                'id'          => '_wc_min_qty_product', 
                'label'       => esc_html__( 'Minimum Quantity', 'twentynineteen-child-min-quantity' ), 
                'placeholder' => '',
                'desc_tip'    => 'true',
                'description' => esc_html__( 'Optional: Set a minimum quantity limit allowed per order.', 'twentynineteen-child-min-quantity' ) 
            )
        );
        echo '</div>';
    }
    
    add_action( 'woocommerce_product_options_inventory_product_data', 'twentynineteen_child_wc_add_min_qty_product_field' );
}


if( ! function_exists( 'twentynineteen_child_wc_qty_save_product_field' ) ){
    
    // Saving the value set to Minimum Quantity options into _wc_min_qty_product meta key respectively
    function twentynineteen_child_wc_qty_save_product_field( $post_id ) {
        $val_min = trim( get_post_meta( $post_id, '_wc_min_qty_product', true ) );
        $new_min = sanitize_text_field( $_POST['_wc_min_qty_product'] );

        if ( $val_min != $new_min ) {
            update_post_meta( $post_id, '_wc_min_qty_product', $new_min );
        }
    }
    
    add_action( 'woocommerce_process_product_meta', 'twentynineteen_child_wc_qty_save_product_field' );
}


    

if( ! function_exists( 'twentynineteen_child_wc_qty_input_args' ) ){
    
    // Setting minimum for quantity input args.
    function twentynineteen_child_wc_qty_input_args( $args, $product ) {

        $product_id = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();

        $product_min = twentynineteen_child_wc_min_limit( $product_id );
        if ( ! empty( $product_min ) ) {
            // min is empty
            if ( false !== $product_min ) {
                $args['min_value'] = $product_min;
            }
        }

        if ( $product->managing_stock() && ! $product->backorders_allowed() ) {
            $stock = $product->get_stock_quantity();
            $args['max_value'] = min( $stock, $args['max_value'] );	
        }
        return $args;
    }
    
    add_filter( 'woocommerce_quantity_input_args', 'twentynineteen_child_wc_qty_input_args', 10, 2 );
}




if( ! function_exists( 'twentynineteen_child_wc_qty_add_to_cart_validation' ) ){
    
    // Checking the quantity on add to cart action with the quantity of the same product available in the cart.
    function twentynineteen_child_wc_qty_add_to_cart_validation( $passed, $product_id, $quantity, $variation_id = '', $variations = '' ) {
        $product_min = twentynineteen_child_wc_min_limit( $product_id );

        if ( ! empty( $product_min ) ) {
            // min is empty
            if ( false !== $product_min ) {
                $new_min = $product_min;
            } 
        }

        return $passed;
    }
    
    add_filter( 'woocommerce_add_to_cart_validation', 'twentynineteen_child_wc_qty_add_to_cart_validation', 1, 5 );
}


if( ! function_exists( 'twentynineteen_child_wc_qty_update_cart_validation' ) ){
    
    // Checking product quantity when cart is updated
    function twentynineteen_child_wc_qty_update_cart_validation( $passed, $cart_item_key, $values, $quantity ) {
        $product_min = twentynineteen_child_wc_min_limit( $values['product_id'] );

        if ( ! empty( $product_min ) ) {
            // min is empty
            if ( false !== $product_min ) {
                $new_min = $product_min;
            } 
        }
        $product = wc_get_product( $values['product_id'] );
        $already_in_cart = twentynineteen_child_wc_cart_qty( $values['product_id'], $cart_item_key );
        if ( ( $already_in_cart + $quantity )  < $new_min ) {
            wc_add_notice( apply_filters( 'wc_qty_error_message', sprintf( esc_html__( 'You should have minimum of %1$s %2$s\'s to %3$s.', 'twentynineteen-child-min-quantity' ),
                        $new_min,
                        $product->get_name(),
                        '<a href="' . esc_url( wc_get_cart_url() ) . '">' . esc_html__( 'your cart', 'twentynineteen-child-min-quantity' ) . '</a>'),
                    $new_min ),
            'error' );
            $passed = false;
        }
        return $passed;
    }
    add_filter( 'woocommerce_update_cart_validation', 'twentynineteen_child_wc_qty_update_cart_validation', 1, 4 );
    
}


//helper funkcije

if( ! function_exists( 'twentynineteen_child_wc_min_limit' ) ){
    
    // Getting product minimum limit by Product ID
    function twentynineteen_child_wc_min_limit( $product_id ) {
        $qty = get_post_meta( $product_id, '_wc_min_qty_product', true );
        if ( empty( $qty ) ) {
            $limit = false;
        } else {
            $limit = (int) $qty;
        }
        return $limit;
    }
    
}


if( ! function_exists( 'twentynineteen_child_wc_cart_qty' ) ){
    
    // Get the total quantity of the product available in the cart.
    function twentynineteen_child_wc_cart_qty( $product_id ) {
        global $woocommerce;
        $running_qty = 0; // iniializing quantity to 0
        // search the cart for the product in and calculate quantity.
        foreach($woocommerce->cart->get_cart() as $other_cart_item_keys => $values ) {
            if ( $product_id == $values['product_id'] ) {				
                $running_qty += (int) $values['quantity'];
            }
        }
        return $running_qty;
    }
}
