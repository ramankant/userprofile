<?php
/*
Plugin Name: Usermeta
Description: if all usermeta data list in show this plugin if any check box click the already active this box 
Version: 1
Author: evoxyz.com
Author URI: http://evoxyz.com
*/
// function to create the DB / Options / Defaults					
function selectmeta_options_install() {

    global $wpdb;

    $table_name = $wpdb->prefix . "select_usermeta";
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			`usermeta_key` varchar(255),
			`usermeta_name` varchar(255),
			`active_status` ENUM('0', '1'),
			PRIMARY KEY (`id`)
          ) $charset_collate; ";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);
}

// run the install scripts upon plugin activation
register_activation_hook(__FILE__, 'selectmeta_options_install');

//menu items
add_action('admin_menu','select_meta_modifymenu');
function select_meta_modifymenu() {
	
	//this is the main item for the menu
	add_menu_page('Select metadata', //page title
	'User meta', //menu title
	'manage_options', //capabilities
	'select_meta_list', //menu slug
	'select_meta_list' //function
	);
	
	//this is a submenu
	add_submenu_page('select_meta_list', //parent slug
	'Select meta', //page title
	'Select meta', //menu title
	'manage_options', //capability
	'select_meta_create', //menu slug
	'select_meta_create'); //function
	
	//this submenu is HIDDEN, however, we need to add it anyways
	add_submenu_page(null, //parent slug
	'Update Selectmeta', //page title
	'Update', //menu title
	'manage_options', //capability
	'select_meta_update', //menu slug
	'select_meta_update'); //function
}

//add_action('admin_menu','autoloadmeta');
//add_action( 'user_register', 'autoloadmeta' );
add_action( 'edit_user_profile', 'autoloadmeta' );
//add_action( 'deleted_user', 'autoloadmeta' );
function autoloadmeta()
{
	global $wpdb;
         $table_name = $wpdb->prefix . "select_usermeta";
		$table_usermeta = $wpdb->prefix . "usermeta";
        $fetchusermeta = $wpdb->get_results("SELECT * from $table_usermeta group by meta_key order by umeta_id asc");
			//echo $wpdb->last_query; die;
			
			$fetchselectmeta = $wpdb->get_results("SELECT * from $table_name");
			$selmeta = array();
			foreach($fetchselectmeta as $selectmeta)
			{
				$selmeta[] =$selectmeta->usermeta_key;
			}
			
			$usmeta = array();
			
			
			foreach($fetchusermeta as $usermeta)
			{
				$usmeta[] = $usermeta->meta_key;
			}
			//echo "<pre>";print_r($usmeta);
			//echo "<pre>";print_r($selmeta);
			$resultdata=array_diff_assoc($usmeta,$selmeta);
			$deletedata=array_diff_assoc($selmeta,$usmeta);
			$dldata=array();
			foreach($deletedata as $dedata)
			{
				$dldata[]="'$dedata'";
			}
			
			//print_r($resultdata);
			//echo "<pre>";print_r($resultdata);
			//echo "<pre>";print_r($dldata);
			$deldata =implode(',',$dldata);
			//die;
			if(count($resultdata) >0)
			{
			foreach($resultdata as $result)
			{
			
	
       $wpdb->insert(
               $table_name, //table
                array('usermeta_key' => $result), //data
                array('%s', '%s') //data format			
       );
		
			}
			}
			if(count($dldata) >0)
			{
			$wpdb->query("delete from $table_name WHERE usermeta_key in($deldata)");
			//echo $wpdb->last_query; die;
			}
			
}


define('ROOTDIR', plugin_dir_path(__FILE__));
require_once(ROOTDIR . 'meta-list.php');
require_once(ROOTDIR . 'meta-create.php');
require_once(ROOTDIR . 'meta-update.php');
