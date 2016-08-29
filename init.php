<?php
/*
Plugin Name: Evo User Profile Display
Description: if all usermeta data list in show this plugin if any check box click the already active this box and show the userprofie [evo_user_profile_display] use this shortcode
Version: 1
Author: evoxyz.com
Author URI: http://evoxyz.com
*/
// function to create the DB / Options / Defaults

ob_start();
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

    
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
	'Select Usermeta', //menu title
	'manage_options', //capabilities
	'select_meta_create', //menu slug
	'select_meta_create' //function
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

add_action( 'edit_user_profile', 'autoloadmeta' );

function autoloadmeta() {
	error_log("abhishek :  autoloadmeta");
	global $wpdb;
    $table_name = $wpdb->prefix . "select_usermeta";
	$table_usermeta = "wp_usermeta";
	$q1  = "SELECT * from $table_usermeta group by meta_key order by umeta_id asc";
	error_log("abhishek " . $q1);
    $fetchusermeta = $wpdb->get_results($q1);
	//echo $wpdb->last_query; die;
	$fetchselectmeta = $wpdb->get_results("SELECT * from $table_name");
	$selmeta = array();
	foreach($fetchselectmeta as $selectmeta) {
		$selmeta[] =$selectmeta->usermeta_key;
	}
	$usmeta = array();
	foreach($fetchusermeta as $usermeta) {
		$usmeta[] = $usermeta->meta_key;
	}
	//echo "<pre>";print_r($usmeta);
	//echo "<pre>";print_r($selmeta);
	$resultdata=array_diff_assoc($usmeta,$selmeta);
	$deletedata=array_diff_assoc($selmeta,$usmeta);
	$dldata=array();
	foreach($deletedata as $dedata) {
		$dldata[]="'$dedata'";
	}
			//print_r($resultdata);
			//echo "<pre>";print_r($resultdata);
			//echo "<pre>";print_r($dldata);
	$deldata =implode(',',$dldata);
			//die;
	if(count($resultdata) >0) {
		foreach($resultdata as $result) {
			error_log("abhishek - inserting");
			$wpdb->insert(
               $table_name, //table
                array('usermeta_key' => $result), //data
                array('%s', '%s') //data format			
   		    );
		}
	}
	if(count($dldata) >0) {
		$wpdb->query("delete from $table_name WHERE usermeta_key in($deldata)");
		//echo $wpdb->last_query; die;
	}
}


define('ROOTDIR', plugin_dir_path(__FILE__));
require_once(ROOTDIR . 'meta-list.php');
require_once(ROOTDIR . 'meta-create.php');
require_once(ROOTDIR . 'meta-update.php');

function evo_user_profile_form() {


	global $current_user, $wpdb;
$role = $wpdb->prefix . 'capabilities';
$current_user->role = array_keys($current_user->$role);
 $current_role = $current_user->role[0];
	
   if($current_role =='administrator'){
       if ($_GET['action'] =='delete') {
           $user_delid = $_GET['id'];
		 global $wpdb;
	      $table_users = $wpdb->prefix . "users";
		$table_usermeta = $wpdb->prefix . "usermeta";
		$table_bp_groups_members = $wpdb->prefix . "bp_groups_members";
		$table_bp_activity = $wpdb->prefix . "bp_activity";
		$table_bp_xprofile_data = $wpdb->prefix . "bp_xprofile_data";
		$table_comments = $wpdb->prefix . "comments";
$sql_user = $wpdb->prepare("DELETE FROM $table_users WHERE ID = '$user_delid'", $id) ;
$wpdb->query($sql_user); 
$sql_usermeta = $wpdb->prepare("DELETE FROM $table_usermeta WHERE user_id = '$user_delid'", $id) ;
$wpdb->query($sql_usermeta); 
$sql_bp_groups_members = $wpdb->prepare("DELETE FROM $table_bp_groups_members WHERE user_id = '$user_delid'", $id) ;
$wpdb->query($sql_bp_groups_members); 
$sql_bp_activity = $wpdb->prepare("DELETE FROM $table_bp_activity WHERE user_id = '$user_delid'", $id) ;
$wpdb->query($sql_bp_activity);	
$sql_bp_xprofile_data = $wpdb->prepare("DELETE FROM $table_bp_xprofile_data WHERE user_id = '$user_delid'", $id) ;
$wpdb->query($sql_bp_xprofile_data);	
$sql_comments = $wpdb->prepare("DELETE FROM $table_comments WHERE user_id = '$user_delid'", $id) ;
$wpdb->query($sql_comments);   
	   }
   }
   
        if (isset($_GET['id'])) {
            $userid = $_GET['id'];
           
        }
		
		error_log("abhishek " . $userid);
        global $wpdb;
       $table_usermeta = "wp_usermeta";
        $userdetails = $wpdb->get_row("SELECT * FROM  $table_usermeta where user_id='$userid' and meta_key='admission_number' ");
        ?>
       
           <table class="table1">
                 <a href='<?php echo site_url(); ?>/<?php echo get_the_title(); ?>/?action=delete&code=1&id=<?php echo $userid; ?>' onclick="return confirm('Are you sure Delete the Users')" style="padding: 4px 25px;background-color: #ccc;color: #ffffff;margin-left: 422px;text-decoration: none;border-radius: 4px;">DELETE </a>
				<thead>
                    <tr>
                        <th scope="col" abbr="Starter" style="font-size: 14px;">ADMISSION NUMBER</th>
                        <th scope="col" abbr="Starter" style="font-size: 14px;"><?php echo $userdetails->meta_value; ?></th>
                        
                    </tr>
                </thead>
                
                <tbody>
                    
					<?php
					global $wpdb;
        $table_selectmeta = $wpdb->prefix . "select_usermeta";
		$table_usermeta = "wp_usermeta";
        $getallselectedmeta = $wpdb->get_results("SELECT * FROM  $table_selectmeta where active_status='1' ");
		 $getallselectedmeta = $wpdb->get_results("SELECT $table_selectmeta.usermeta_key,$table_selectmeta.usermeta_name,$table_usermeta.meta_key,$table_usermeta.meta_value from $table_selectmeta join $table_usermeta on $table_usermeta.meta_key = $table_selectmeta.usermeta_key where $table_usermeta.user_id='$userid' and $table_selectmeta.active_status='1'");
		
		 foreach($getallselectedmeta as $getmeta){
			 $get_name = $getmeta->usermeta_name;
			 $get_userkey = $getmeta->usermeta_key;
			 if($get_name =='')
			 {
				 $metaname = $get_userkey;
			 }
			 else
			 {
				 $metaname = $get_name;
			 }
			 
		?> <tr>
                        <th scope="row"><?php echo $metaname; ?></th>
		
                        <td><?php echo $getmeta->meta_value; ?></td>
                        
                    </tr>
                    <?php } ?>
                    
                </tbody>
            </table>
        <?php
}
// userprofile_show a new shortcode: [evo_user_profile_display]
add_shortcode('evo_user_profile_display', 'evo_user_profile_form');

?>
