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
    
    private $form_name = 'wcgf_entry_auto_create_form';
    private $form_key = '_wcgf_entry_auto_create_form';

    private $number_name = 'wcgf_entry_auto_create_number';
    private $number_key = '_wcgf_entry_auto_create_number';

    private $product_attribute_1_name = 'wcgf_entry_auto_create_attribute_1';
    private $product_attribute_1_key = '_wcgf_entry_auto_create_attribute_1';
    private $form_field_1_name = 'wcgf_entry_auto_create_field_1';
    private $form_field_1_meta_key = '_wcgf_entry_auto_create_field_1';


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

            $form_existing_value = get_term_meta( $tag->term_id, $this->form_key, true );

            ?> 
            <tr class="form-field <?php echo $this->form_name ?>-wrap">
                <th scope="row" valign="top">
                    <label for="<?php echo $this->form_name ?>"><?php _e('Gravity Form'); ?><br><span style="font-weight: normal"><?php _e('in which to auto-create entries') ?></span></label>
                </th>
                <td>
                    <select type="text" name="<?php echo $this->form_name ?>" id="<?php echo $this->form_name ?>">
                        <option value="none">None</option>
                        <?php
                            foreach ( $forms as $form_id => $form ) {
                                $selected = ( $form_existing_value != NULL && $form_existing_value == $form_id ) ? 'selected' : '';
                                echo '<option value="' . $form_id . '" ' . $selected . '>' . $form['title'] . '</option>';
                            }
                        ?>
                    </select>
                    <p class="description">Entries will be created in this Gravity Form whenever a product in this category is purchased.</p>

                    <label for="<?php echo $this->number_name ?>">
                </td>
            </tr>
            <?php

        }

    }

    function save_product_category_term( $term_id, $tt_id ) {
        if ( isset( $_POST[ $this->form_name ] ) ) {
            update_term_meta( $term_id, $this->form_key, $_POST[ $this->form_name ] );
        }
    }

}