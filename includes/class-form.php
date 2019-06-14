<?php

namespace WooCommerceGravityFormsEntryAutoCreate;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit();

/**
 * Form class
 * 
 * This class is responsible for providing functions and properties for interacitng with the Gravity Form
 */
class Form {
   
    // User meta key for holding the Muvi user id
    //private $user_meta_key_muvi_user = 'learndash_muvi_user_id';

    /**
     * Class __construct function
     * 
     * @param obj   $api 
     */
    public function __construct() {

        // add actions
        //add_action( 'woocommerce_created_customer', array( $this, 'woocommerce_created_user' ), 10, 3 );

    }

    /**
     * When a WordPress user's LearnDash course access is updated, create a Muvi user if one doesn't already exist
     * 
     * The problem with this function is that the WordPress user's password ($wp_user->user_pass) is MD5 encrypted with a one-way hash.
     * This function has been deprecated in favor of creating the Muvi user when a WordPress user is created
     * 
     * @param  int  	    $wp_user_id
     * @param  int  	    $course_id
     * @param  array  	    $access_list
     * @param  bool  	    $remove
     * @return int/bool     $primary_key_id or false
     */


}
