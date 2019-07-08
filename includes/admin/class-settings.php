<?php

namespace WooCommerceGravityFormsEntryAutoCreate;


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit();

/**
 * Settings class
 *
 * This class is responsible for creating the settings page
 */
class Settings {
    
    private static $settings_fields = array(
        'gform' => array(
            'name' => 'wcgf_entry_auto_create_form',
            'key' => '_wcgf_entry_auto_create_form',
        ),
        'number' => array(
            'name' => 'wcgf_entry_auto_create_number',
            'key' => '_wcgf_entry_auto_create_number',
        ),
        'first_pending' => array(
            'name' => 'wcgf_entry_auto_create_first_pending',
            'key' => '_wcgf_entry_auto_create_first_pending',
        ),
        'order_id_field_id' => array(
            'name' => 'wcgf_entry_order_id_field',
            'key' => '_wcgf_entry_order_id_field',
        ),
        'email_field_id' => array(
            'name' => 'wcgf_entry_email_field',
            'key' => '_wcgf_entry_email_field',
        ),
        'phone_field_id' => array(
            'name' => 'wcgf_entry_phone_field',
            'key' => '_wcgf_entry_phone_field',
        ),
        'product_field_id_1' => array(
            'name' => 'wcgf_entry_auto_create_attribute_1',
            'key' => '_wcgf_entry_auto_create_attribute_1',
        ),
        'gform_field_id_1' => array(
            'name' => 'wcgf_entry_auto_create_field_1',
            'key' => '_wcgf_entry_auto_create_field_1',
        ),
        'product_field_id_2' => array(
            'name' => 'wcgf_entry_auto_create_attribute_2',
            'key' => '_wcgf_entry_auto_create_attribute_2',
        ),
        'gform_field_id_2' => array(
            'name' => 'wcgf_entry_auto_create_field_2',
            'key' => '_wcgf_entry_auto_create_field_2',
        ),
    );


    /**
     * Class __construct function
     *
     * @param str	@auth_setting_id
     */
    public function __construct( ) {

        add_action( 'product_cat_edit_form_fields', array( $this, 'edit_product_category_term' ), 10, 2 );
        add_action( 'edited_product_cat', array( $this, 'save_product_category_term' ), 10, 2 );

    }

    public function edit_product_category_term( $tag, $taxonomy ) {

        if ( class_exists( 'GFAPI' ) ) {

            $forms = \GFAPI::get_forms();

            foreach ( self::$settings_fields as $id => $name_key ) {
                $settings[ $id ] = $name_key;
                $settings[ $id ][ 'existing' ] = get_term_meta( $tag->term_id, $settings[ $id ][ 'key' ], true );
            }

            ?> 
            <tr class="form-field <?php echo $settings['gform']['name']; ?>-wrap">
                <th scope="row" valign="top">
                    <label for="<?php echo $settings['gform']['name']; ?>"><?php _e('Gravity Form'); ?><br><span style="font-weight: normal"><?php _e('in which to auto-create entries') ?></span></label>
                </th>
                <td>
                    <select type="text" name="<?php echo $settings['gform']['name']; ?>" id="<?php echo $settings['gform']['name']; ?>">
                        <option value="none">None</option>
                        <?php
                            foreach ( $forms as $form ) {
                                $selected = ( $settings['gform']['existing'] != NULL && $settings['gform']['existing'] == $form['id'] ) ? 'selected' : '';
                                echo '<option value="' . $form['id'] . '" ' . $selected . '>' . $form['title'] . '</option>';
                            }
                        ?>
                    </select>
                    <p class="description">Entries will be created in this Gravity Form whenever a product in this category is purchased.</p>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="<?php echo $settings['number']['name']; ?>"><?php _e('# of Gravity Form Entries'); ?><br><span style="font-weight: normal"><?php _e('to be created') ?></span></label>
                </th>
                <td>
                    <input type="number" name="<?php echo $settings['number']['name'] ?>" id="<?php echo $settings['number']['name'] ?>" value="<?php echo $settings['number']['existing']; ?>">
                    <p class="description">Quantity of entries to create in the above Gravity Form when a product in this category is purchased.</p> 
                <br>
                    <input type="hidden" name="<?php echo $settings['first_pending']['name']; ?>" value="0">
                    <input type="checkbox" name="<?php echo $settings['first_pending']['name']; ?>" id="<?php echo $settings['first_pending']['name']; ?>" value="1"<?php echo ( $settings['first_pending']['existing'] == '1' ) ? ' checked' : ''; ?>>
                    <label for="<?php echo $settings['first_pending']['name']; ?>"><?php _e('Set first newly created entry to have a GravityView approval status of "Disapproved" (i.e. Pending)'); ?>

                </td>
            </tr>
            <tr>
                <th>
                    <label for="<?php echo $settings['order_id_field_id']['name']; ?>"><?php _e('Gravity Form Order ID Field'); ?></label>
                </th>
                <td>
                    <input type="number" name="<?php echo $settings['order_id_field_id']['name'] ?>" id="<?php echo $settings['order_id_field_id']['name'] ?>" value="<?php echo $settings['order_id_field_id']['existing']; ?>">
                    <p class="description">Enter the ID for the field into which you would like the Order ID placed for all auto-created entries.</p> 
                </td>
            </tr>
            <tr>
                <th>
                    <label for="<?php echo $settings['email_field_id']['name']; ?>"><?php _e('Gravity Form Email Field'); ?></label>
                </th>
                <td>
                    <input type="number" name="<?php echo $settings['email_field_id']['name'] ?>" id="<?php echo $settings['email_field_id']['name'] ?>" value="<?php echo $settings['email_field_id']['existing']; ?>">
                    <p class="description">Enter the ID for the field into which you would like the user's Email address placed for all auto-created entries.</p> 
                </td>
            </tr>
            <tr>
                <th>
                    <label for="<?php echo $settings['phone_field_id']['name']; ?>"><?php _e('Gravity Form Phone Field'); ?></label>
                </th>
                <td>
                    <input type="number" name="<?php echo $settings['phone_field_id']['name'] ?>" id="<?php echo $settings['phone_field_id']['name'] ?>" value="<?php echo $settings['phone_field_id']['existing']; ?>">
                    <p class="description">Enter the ID for the field into which you would like the user's Email address placed for all auto-created entries.</p> 
                </td>
            </tr>
            <tr>
                <th>
                    <label><?php _e('Gravity Form Field Mapping'); ?></span></label>
                </th>
                <td>
                    <table>
                        <tr>
                            <td>
                                <label for="<?php echo $settings['product_field_id_1']['name']; ?>"><?php _e('Product Add-On Form Field ID #1'); ?></label>
                                <input type="number" name="<?php echo $settings['product_field_id_1']['name'] ?>" id="<?php echo $settings['product_field_id_1']['name'] ?>" value="<?php echo $settings['product_field_id_1']['existing']; ?>">
                                <p class="description">The value that has been entered into a field with this ID on the Product's Add-On Gravity Form...</p> 
                            </td>
                            <td>
                                <label for="<?php echo $settings['gform_field_id_1']['name']; ?>"><?php _e('Auto-Created Entry Form Field ID #1'); ?></label>
                                <input type="number" name="<?php echo $settings['gform_field_id_1']['name'] ?>" id="<?php echo $settings['gform_field_id_1']['name'] ?>" value="<?php echo $settings['gform_field_id_1']['existing']; ?>">
                                <p class="description">...will be automatically copied into a field with this ID on the form selected above whenever a new entry is auto-created.</p> 
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="<?php echo $settings['product_field_id_2']['name']; ?>"><?php _e('Product Add-On Form Field ID #2'); ?></label>
                                <input type="number" name="<?php echo $settings['product_field_id_2']['name'] ?>" id="<?php echo $settings['product_field_id_2']['name'] ?>" value="<?php echo $settings['product_field_id_2']['existing']; ?>">
                                <p class="description">The value that has been entered into a field with this ID on the Product's Add-On Gravity Form...</p> 
                            </td>
                            <td>
                                <label for="<?php echo $settings['gform_field_id_2']['name']; ?>"><?php _e('Auto-Created Entry Form Field ID #2'); ?></label>
                                <input type="number" name="<?php echo $settings['gform_field_id_2']['name'] ?>" id="<?php echo $settings['gform_field_id_2']['name'] ?>" value="<?php echo $settings['gform_field_id_2']['existing']; ?>">
                                <p class="description">...will be automatically copied into a field with this ID on the form selected above whenever a new entry is auto-created.</p> 
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <?php

        }

    }

    function save_product_category_term( $term_id, $tt_id ) {
        foreach ( self::$settings_fields as $settings_field ) {
            if ( isset( $_POST[ $settings_field['name'] ] ) ) {
                if ( $_POST[ $settings_field['name'] ] !== "none" )
                    update_term_meta( $term_id, $settings_field['key'], $_POST[ $settings_field['name'] ] );
                else
                    delete_term_meta( $term_id, $settings_field['key'] );
            }
        }
    }

    public static function get_settings_fields( ) {
        return self::$settings_fields;
    }

}