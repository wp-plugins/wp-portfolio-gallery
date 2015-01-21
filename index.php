<?php
/*
Plugin Name: WP Portfolio Gallery
Plugin URI: http://jeweltheme.com
Description: WP Portfolio Gallery is awesome Filterable Isotop Portfolio Gallery
Version: 1.0.0
Author: Liton Arefin
Author URI: http://www.jeweltheme.com
License: GPL2
http://www.gnu.org/licenses/gpl-2.0.html


License: 
 
 Copyright 2012 JewelTheme (support@jeweltheme.com) 
 
  This program is free software; you can redistribute it and/or modify 
  it under the terms of the GNU General Public License, version 2, as  
  published by the Free Software Foundation. 
 
  This program is distributed in the hope that it will be useful, 
  but WITHOUT ANY WARRANTY; without even the implied warranty of 
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
  GNU General Public License for more details. 
 
  You should have received a copy of the GNU General Public License 
  along with this program; if not, write to the Free Software 
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA 
   
*/ 

//Custom Post Type Portfolio

    function jeweltheme_portfolio() {
    $labels = array(
        'name'               => _x( 'Portfolio', 'post type general name' ),
        'singular_name'      => _x( 'Portfolio', 'post type singular name' ),
        'add_new'            => _x( 'Add New', 'book' ),
        'add_new_item'       => __( 'Add New Portfolio' ),
        'edit_item'          => __( 'Edit Portfolio' ),
        'new_item'           => __( 'New Portfolio Items' ),
        'all_items'          => __( 'All Portfolio' ),
        'view_item'          => __( 'View Portfolio' ),
        'search_items'       => __( 'Search Portfolio' ),
        'not_found'          => __( 'No Portfolio Items found' ),
        'not_found_in_trash' => __( 'No Portfolio Items found in the Trash' ), 
        'parent_item_colon'  => '',
        'menu_name'          => 'WP Portfolio'
    );


    $args = array(
        'labels'        => $labels,
        'description'   => 'Holds Portfolio specific data',
        'public'        => true,
        'show_ui'       => true,
        'show_in_menu'  => true,
        'query_var'     => true,
        'rewrite' => array(
        'slug' => 'portfolio/%year%',
        'with_front' => true
        ),
        'capability_type'=> 'post',
        'has_archive'   => true,
        'hierarchical'  => false,
        'menu_position' => 5,
        'supports'      => array( 'title', 'editor', 'thumbnail'),
        'menu_icon' => plugins_url( 'images/portfolio.png', __FILE__ )  // Icon Path
    );
    register_post_type( 'portfolio', $args ); 

    // Custom taxonomy for Portfolio Tags  

  $labels = array(  
    'name' => _x( 'Categories', 'taxonomy general name' ),  
    'singular_name' => _x( 'WP Portfolio Category', 'taxonomy singular name' ),  
    'search_items' =>  __( 'Search Types' ),  
    'all_items' => __( 'All Categories' ),  
    'parent_item' => __( 'Parent Category' ),  
    'parent_item_colon' => __( 'Parent Category:' ),  
    'edit_item' => __( 'Edit Category' ),  
    'update_item' => __( 'Update Category' ),  
    'add_new_item' => __( 'Add New Category' ),  
    'new_item_name' => __( 'New Category Name' ),  
  );  
// Custom taxonomy for Project Tags  
register_taxonomy('jwtag', array('portfolio'), array(  
    'hierarchical' => true,  
    'labels' => $labels,  
    'show_ui' => true,  
    'query_var' => true,  
    'rewrite' =>true,  
  ));

}
add_action( 'init', 'jeweltheme_portfolio' );

//Post Type Update
function jeweltheme_messages( $messages ) {
    global $post, $post_ID;
    $messages['portfolio'] = array(
        0 => '', //If Unused, Messages start at index 1
        1 => sprintf( __('Portfolio Updated. <a href="%s">View Portfolio</a>'), esc_url( get_permalink($post_ID) ) ),
        2 => __('Portfolio Item Updated.'),
        3 => __('Portfolio Item Deleted.'),
        4 => __('Portfolio updated.'),
        5 => isset($_GET['revision']) ? sprintf( __('Portfolio restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
        6 => sprintf( __('Portfolio Published. <a href="%s">View product</a>'), esc_url( get_permalink($post_ID) ) ),
        7 => __('Portfolio saved.'),
        8 => sprintf( __('Portfolio submitted. <a target="_blank" href="%s">Preview product</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
        9 => sprintf( __('Portfolio scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview product</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
        10 => sprintf( __('Portfolio draft updated. <a target="_blank" href="%s">Preview product</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    );
    return $messages;
}
add_filter( 'post_updated_messages', 'jeweltheme_messages' );



/*--- Demo URL meta box ---*/  
  
add_action('admin_init','jeweltheme_portfolio_meta');  
  
function jeweltheme_portfolio_meta()  
{  
    // add a meta box for WordPress 'project' type  
    add_meta_box('jeweltheme_portfolio', 'Portfolio URL', 'jeweltheme_portfolio_meta_setup', 'portfolio', 'side', 'low');  
   
    // add a callback function to save any data a user enters in  
    add_action('save_post','jeweltheme_portfolio_meta_save');  
}  
  
function jeweltheme_portfolio_meta_setup()  
{  
    global $post;  
       
    ?>  
        <div class="portfolio_meta_control">  
            
            <p>  
                <input type="text" name="_url" placeholder="http://"value="http://<?php echo get_post_meta($post->ID,'_url',TRUE); ?>" style="width: 100%;" />  
            </p>  
            <em style="color:red;">Without http:// and starts with Example: www.sitename.com</em>
        </div>  
    <?php  
  
    // create for validation  
    echo '<input type="hidden" name="jeweltheme_meta_nonce" value="' . wp_create_nonce(__FILE__) . '" />';  
}  
  
function jeweltheme_portfolio_meta_save($post_id)   
{  
    // check nonce  
    if (!isset($_POST['jeweltheme_meta_nonce']) || !wp_verify_nonce($_POST['jeweltheme_meta_nonce'], __FILE__)) {  
    return $post_id;  
    }  
  
    // check capabilities  
    if ('post' == $_POST['portfolio']) {  
    if (!current_user_can('edit_post', $post_id)) {  
    return $post_id;  
    }  
    } elseif (!current_user_can('edit_page', $post_id)) {  
    return $post_id;  
    }  
  
    // exit on autosave  
    if (defined('DOING_AUTOSAVE') == DOING_AUTOSAVE) {  
    return $post_id;  
    }  
  
    if(isset($_POST['_url']))   
    {  
        update_post_meta($post_id, '_url', $_POST['_url']);  
    } else   
    {  
        delete_post_meta($post_id, '_url');  
    }  
}  


/* Activation Hook Runs when plugin is activated */
register_activation_hook(__FILE__,'jeweltheme_portfolio_install'); 

function jeweltheme_portfolio_install() {

    global $wpdb;

    $the_page_title = 'Portfolio';
    $the_page_name = 'portfolio';

    // the menu entry...
    delete_option("jeweltheme_portfolio_page_title");
    add_option("jeweltheme_portfolio_page_title", $the_page_title, '', 'yes');
    // the slug...
    delete_option("jeweltheme_portfolio_page_name");
    add_option("jeweltheme_portfolio_page_name", $the_page_name, '', 'yes');
    // the id...
    delete_option("jeweltheme_portfolio_page_id");
    add_option("jeweltheme_portfolio_page_id", '0', '', 'yes');

    $the_page = get_page_by_title( $the_page_title );

    if ( ! $the_page ) {

        // Create post object
        $_p = array();
        $_p['post_title'] = $the_page_title;        
        $_p['post_status'] = 'publish';
        $_p['post_type'] = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status'] = 'closed';
        $_p['post_category'] = array(1); // the default 'Uncatrgorised'

        // Insert the post into the database
        $the_page_id = wp_insert_post( $_p );

    }
    else {
        // the plugin may have been previously active and the page may just be trashed...

        $the_page_id = $the_page->ID;

        $the_page->post_status = 'publish';
        $the_page_id = wp_update_post( $the_page );
    }

    delete_option( 'jeweltheme_portfolio_page_id' );
    add_option( 'jeweltheme_portfolio_page_id', $the_page_id );
}

function my_plugin_remove() {

    global $wpdb;

    $the_page_title = get_option( "jeweltheme_portfolio_page_title" );
    $the_page_name = get_option( "jeweltheme_portfolio_page_name" );

    //  the id of our page...
    $the_page_id = get_option( 'jeweltheme_portfolio_page_id' );
    if( $the_page_id ) {

        wp_delete_post( $the_page_id ); // this will trash, not delete

    }

    delete_option("jeweltheme_portfolio_page_title");
    delete_option("jeweltheme_portfolio_page_name");
    delete_option("jeweltheme_portfolio_page_id");

}


add_action( 'init', 'jeweltheme_choose_template' );
function jeweltheme_choose_template()
{
add_filter( 'template_include', 'jeweltheme_include_template_function', 1 );

    function jeweltheme_include_template_function( $template_path ) {
        if ( get_the_title() == 'Portfolio' ) {   
            $template_path = plugin_dir_path( __FILE__ ) . '/theme/page-portfolio.php'; 
        }
        
        return $template_path;  
    }
}

function jeweltheme_portfolio_scripts()
{
        if(!is_admin()){
        wp_enqueue_script('jquery');

        wp_register_script('mixitup', plugins_url('/wp-portfolio-gallery/js/jquery.mixitup.js'));
        wp_enqueue_script('mixitup');

        wp_register_script('jquery-mousewheel', plugins_url('/wp-portfolio-gallery/js/jquery.mousewheel.pack.js'));
        wp_enqueue_script('jquery-mousewheel');

        wp_register_script('jquery-prettyPhoto-js', plugins_url('/wp-portfolio-gallery/js/jquery.prettyPhoto.js'));
        wp_enqueue_script('jquery-prettyPhoto-js');

        wp_register_style('wp-portfolio-gallery',plugins_url('/wp-portfolio-gallery/css/wp-portfolio-gallery.css'));
        wp_enqueue_style('wp-portfolio-gallery');

        wp_register_style('wp-portfolio-gallery-animation',plugins_url('/wp-portfolio-gallery/css/animation.css'));
        wp_enqueue_style('wp-portfolio-gallery-animation');

        wp_register_style('jquery-prettyPhoto-css',plugins_url('/wp-portfolio-gallery/css/prettyPhoto.css'));
        wp_enqueue_style('jquery-prettyPhoto-css');

    }
}

add_action('init','jeweltheme_portfolio_scripts');


//Portfolio Settings Fields

add_action('admin_menu', 'jeweltheme_add_options');
add_action('admin_init', 'jeweltheme_service_settings_store');

//Add options page 
function jeweltheme_add_options() {
   add_submenu_page('edit.php?post_type=portfolio', 'Portfolio Admin','Portfolio Settings', 'edit_posts', basename(__FILE__), 'jeweltheme_portfolio_setting_functions');    
   register_setting( 'portfolio_settings', 'plugin_options' );
}

//Register Settings Page
function jeweltheme_service_settings_store() {
    register_setting('jeweltheme_portfolio_settings', 'jeweltheme_items');   
    register_setting('jeweltheme_portfolio_settings', 'jeweltheme_layouts');
    register_setting('jeweltheme_portfolio_settings', 'jeweltheme_thumb_width');
    register_setting('jeweltheme_portfolio_settings', 'jeweltheme_thumb_height');
}

function jeweltheme_portfolio_setting_functions(){
    ?>
        <div class="wrap">
       <div class="icon32" id="icon-options-general"><br></div>
        <h2>WP Portfolio Gallery Settings</h2>
     <p>Settings sections for WP Portfolio Gallery Text, Animation, CSS etc</p>
       <form method="post" action="options.php">
            <?php settings_fields('jeweltheme_portfolio_settings'); ?>

                <table class="form-table">       
            <tr><th>
                    <label>Portfolio Items Per Column</label>
               </th><td>
                    <input type="number" name="jeweltheme_items" value="<?php echo get_option('jeweltheme_items'); ?>" />
            </td></tr>
            
            <tr><th>
                    <label>Thumbnail Width:</label>
               </th><td>
                    <input type="number" name="jeweltheme_thumb_width" value="<?php echo get_option('jeweltheme_thumb_width'); ?>" />
            </td></tr>

            <tr><th>
                    <label>Thumbnail Height:</label>
               </th><td>
                    <input type="number" name="jeweltheme_thumb_height" value="<?php echo get_option('jeweltheme_thumb_height'); ?>" />
            </td></tr>

            <tr><th>
                    <label>Layout</label>
               </th><td>
                   <?php 
                        $options = get_option('jeweltheme_layouts');
                        $items = array("Layout 1", "Layout 2", "Layout 3", "Layout 4", "Layout 5");
                        echo "<select id='layout' name='jeweltheme_layouts[layout]'>";
                        foreach($items as $item) {
                            $selected = ($options['layout']==$item) ? 'selected="selected"' : '';
                            echo "<option value='$item' $selected>$item</option>";
                        }
                        echo "</select>";
            ?>
               </td></tr>

           


        <tr><td>
            <input type="submit" class="button-primary" value="Save Changes" />
        </td></tr>

            </table>
        </form>

<?php
}


//Redirect to the Shaper Portfolio Template
add_action( 'template_redirect', 'jeweltheme_portfolio_template_redirect' );
function jeweltheme_portfolio_template_redirect() {
    global $wp_query;
    global $wp;

    if ( $wp_query->query_vars['post_type'] === 'portfolio' ) {

        if ( have_posts() )
        {
            $template_path = plugin_dir_path( __FILE__ ) . '/theme/page-portfolio.php'; 
            die();
        }
        else
        {
            $wp_query->is_404 = true;
        }

    }
}

//Set Thumbnail Image size
$jeweltheme_width=get_option('jeweltheme_thumb_width');
$jeweltheme_height=get_option('jeweltheme_thumb_height');
set_post_thumbnail_size( 'jw-portfolio-thumb', $jeweltheme_width, $jeweltheme_height ); 



/* Display a notice that can be dismissed */

add_action('admin_notices', 'jeweltheme_wp_portfolio_gallery_admin_notice');

function jeweltheme_wp_portfolio_gallery_admin_notice() {
    global $current_user ;
        $user_id = $current_user->ID;
    if ( ! get_user_meta($user_id, 'jeweltheme_wp_gallery_ignore_notice') ) {
        echo '<div class="updated"><p>'; 
        printf(__('Check out Premium <a href="http://jeweltheme.com/product/wp-awesome-faq-pro/" target="_blank">WP Awesome FAQ</a> Plugin.  Why this Plugin is really awesome !!! | Check out other Awesome stuffs <a href="http://jeweltheme.com" target="_blank">here</a> <a style="float: right;" href="%1$s">X</a>'), '?jeweltheme_wp_gallery_ignore=0');
        echo "</p></div>";
    }
}

add_action('admin_init', 'jeweltheme_wp_gallery_ignore');

function jeweltheme_wp_gallery_ignore() {
    global $current_user;
        $user_id = $current_user->ID;
        if ( isset($_GET['jeweltheme_wp_gallery_ignore']) && '0' == $_GET['jeweltheme_wp_gallery_ignore'] ) {
             add_user_meta($user_id, 'jeweltheme_wp_gallery_ignore_notice', 'true', true);
    }
}