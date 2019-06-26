<?php

namespace WooCommerceGravityFormsEntryAutoCreate;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit();

/**
 * Auto Entries class
 * 
 * This class is responsible for providing functions and properties for interacitng with the Gravity Form
 */
class Auto_Entries {
   
    /**
     * Settings fields array from Settings class
     */
    private $settings_fields;

    /**
     * Class __construct function
     * 
     * @param obj   Auto_Entries object
     */
    public function __construct() {

        // Add product purchase action
//      add_action( 'woocommerce_order_status_completed', array( $this, 'handle_product_purchased' ) );
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'handle_product_purchased' ) );

    }

    /**
     * Product purchased
     */
    public function handle_product_purchased( $order_id ) {

        if ( class_exists( 'GFAPI' ) ) {

            $order = new \WC_Order( $order_id );
            
            $items = $order->get_items();
            
            foreach ( $items as $item ) {

                $product_cats_ids = wc_get_product_term_ids( $item->get_product_id(), 'product_cat' );

                error_log('About to cycle through ' . var_export( $product_cats_ids, true ) );

                foreach( $product_cats_ids as $product_cat_id ) {
                    $this->create_entries( $product_cat_id, $item, $order );
                }
            }
        }

        return $order_id;
    }
    		
    /**
     * Create new Gravity Forms entries based on the products in an order
     */
    private function create_entries( int $cat_id, \WC_Order_Item_Product $item, \WC_Order $order ) {

        if ( class_exists( Settings::class ) ) {

            $this->settings_fields = Settings::get_settings_fields();

            $gform_id = $this->get_cat_setting_value( $cat_id, 'gform' );

            error_log( 'Cat ID ' . $cat_id . ' has gform value ' . var_export( $gform_id, true ) );

            if ( !is_null( $gform_id ) && $gform_id !== '' ) {

                $attribute_gform_entry = $this->get_cat_attribute_gform_entry( $item );

                $number = $this->get_cat_number_of_entries( $cat_id );

                error_log( 'Rady to create ' . $number . ' of entries' );

                $counter = 0;
                while( $counter < $number ) {
                    $entry = array(
                        'form_id' => $gform_id,
                        'created_by' => (int) $order->get_user_id(),
                    );

                    // Add order fields
                    if ( $order_id_field_id = $this->get_cat_setting_value( $cat_id, 'order_id_field_id' ) )
                        $entry[ $order_id_field_id ] = $order->get_id();
                    if ( $email_field_id = $this->get_cat_setting_value( $cat_id, 'email_field_id' ) )
                        $entry[ $email_field_id ] = $order->get_billing_email();
                    if ( $phone_field_id = $this->get_cat_setting_value( $cat_id, 'phone_field_id' ) )
                        $entry[ $phone_field_id ] = $order->get_billing_phone();


                    // Map field values
                    if ( $field_id_1 = $this->get_cat_entry_field_id( 1, $cat_id ) )
                        $entry[ $field_id_1 ] = $this->get_cat_attribute_field_value( 1, $cat_id, $attribute_gform_entry );
                    if ( $field_id_2 = $this->get_cat_entry_field_id( 2, $cat_id ) )
                        $entry[ $field_id_2 ] = $this->get_cat_attribute_field_value( 2, $cat_id, $attribute_gform_entry );

                    $added_entries[] = \GFAPI::add_entry( $entry );

                    error_log(' Just added to ' . var_export( $added_entries, true ) );

                    $counter++;
                }
                return $added_entries;
            }

        }

        return false;
    }

    /**
     * Get a value from the Category settings page based on it's field key
     */
    private function get_cat_setting_value( int $cat_id, string $settings_field_id ) {
        $form_setting = $this->settings_fields[ $settings_field_id ];
        $field_id = get_term_meta( $cat_id, $form_setting['key'], true );

        if ( $field_id !== '' )
            return $field_id;

        return null;
    }


    /**
     * Get the number of Gravity Form entries to create from a Category settings page
     */
    private function get_cat_number_of_entries( int $cat_id ) {
        $form_setting = $this->settings_fields[ 'number' ];
        $number = (int) get_term_meta( $cat_id, $form_setting['key'], true );
        if ( $number == 0 ) $number = 1;
        if ( $number > 20 ) $number = 20;

        return $number;
    }


    /**
     * Get the Product Attribute Gravity Form entry array for this Order Item (used to get_cat_attribute_field_value)
     */
    private function get_cat_attribute_gform_entry( \WC_Order_Item_Product $item ) {
        $gravity_forms_history = null;

        $meta_data = $item->get_meta_data();

        foreach ( $meta_data as $meta_data_item ) {
            $d = $meta_data_item->get_data();
            if ( $d['key'] == '_gravity_forms_history' ) {
                $gravity_forms_history = array( $meta_data_item );
                break;
            }
        }

        if ( $gravity_forms_history ) {
            $gravity_forms_history_value = array_pop( $gravity_forms_history );
            return $gravity_forms_history_value->value['_gravity_form_lead'];
        }

        return null;
    }

    /**
     * Get one of the Gravity Form entry field ids entered on the Category settings page
     */
    private function get_cat_entry_field_id( int $num, int $cat_id ) {
        $form_setting = $this->settings_fields[ 'gform_field_id_' . $num ];
        $field_id = get_term_meta( $cat_id, $form_setting['key'], true );

        if ( $field_id !== '' )
            return $field_id;

        return null;
    }

    /**
     * Get one of the Product Attribute Gravity Form entry values based on the field id entered on the Category settings page
     */
    private function get_cat_attribute_field_value( int $num, int $cat_id, array $gform_entry ) {
        $form_setting = $this->settings_fields[ 'product_field_id_' . $num ];
        $field_id = get_term_meta( $cat_id, $form_setting['key'], true );

        if ( $field_id !== '' && isset( $gform_entry[ $field_id ] ) )
            return $gform_entry[ $field_id ];

        return null;
    }

}
