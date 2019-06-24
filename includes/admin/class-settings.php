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
    
    private $settings_fields = array(
        'gform' => array(
            'name' => 'wcgf_entry_auto_create_form',
            'key' => '_wcgf_entry_auto_create_form',
        ),
        'number' => array(
            'name' => 'wcgf_entry_auto_create_number',
            'key' => '_wcgf_entry_auto_create_number',
        ),
        'product_attribute_1' => array(
            'name' => 'wcgf_entry_auto_create_attribute_1',
            'key' => '_wcgf_entry_auto_create_attribute_1',
        ),
        'form_field_1' => array(
            'name' => 'wcgf_entry_auto_create_field_1',
            'key' => '_wcgf_entry_auto_create_field_1',
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

            $form_setting = $this->settings_fields['gform'];
            $form_existing_value = get_term_meta( $tag->term_id, $form_setting['key'], true );

            $number_setting = $this->settings_fields['number'];
            $number_existing_value = get_term_meta( $tag->term_id, $number_setting['key'], true );

            ?> 
            <tr class="form-field <?php echo $form_setting['name']; ?>-wrap">
                <th scope="row" valign="top">
                    <label for="<?php echo $form_setting['name']; ?>"><?php _e('Gravity Form'); ?><br><span style="font-weight: normal"><?php _e('in which to auto-create entries') ?></span></label>
                </th>
                <td>
                    <select type="text" name="<?php echo $form_setting['name']; ?>" id="<?php echo $form_setting['name']; ?>">
                        <option value="none">None</option>
                        <?php
                            foreach ( $forms as $form_id => $form ) {
                                $selected = ( $form_existing_value != NULL && $form_existing_value == $form_id ) ? 'selected' : '';
                                echo '<option value="' . $form_id . '" ' . $selected . '>' . $form['title'] . '</option>';
                            }
                        ?>
                    </select>
                    <p class="description">Entries will be created in this Gravity Form whenever a product in this category is purchased.</p>

                    <label for="<?php echo $number_setting['name'] ?>">
                    <input type="number" name="<?php echo $number_setting['name'] ?>" id="<?php echo $number_setting['name'] ?>" value="<?php echo $number_existing_value; ?>">
                </td>
            </tr>
            <?php

        }

    }

    function save_product_category_term( $term_id, $tt_id ) {
        foreach ( $this->settings_fields as $settings_field ) {
            if ( isset( $_POST[ $settings_field['name'] ] ) ) {
                update_term_meta( $term_id, $settings_field['key'], $settings_field['name'] );
            }
        }
    }

}