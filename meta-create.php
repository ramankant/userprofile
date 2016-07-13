<?php

function select_meta_create() {
    $metaname = $_POST["metaname"];
	$metakeychecked = $_POST["metakeychecked"];
    $metakey = $_POST["metakey"];
	 $hiddenid = $_POST["hiddenid"];
	
			
    //insert
    if (isset($_POST['insert'])) {
		//echo "<pre>";print_r($metaname);
			//echo "<pre>";print_r($hiddenid); 

			//echo "<pre>";print_r($deletedata); 
			foreach(array_keys($hiddenid) as $i) {
        global $wpdb;
        $table_usermeta = $wpdb->prefix . "select_usermeta";
             $j =$i+1;
        $wpdb->query("UPDATE $table_usermeta SET usermeta_name ='$metaname[$i]' WHERE id='$j'");
		//echo $wpdb->last_query;
			}
	
	 global $wpdb;
	 $table_usermeta = $wpdb->prefix . "select_usermeta";
	
	if(count($metakey) >0)
	{
		
		$userid = array();
		$getusermetaid = $wpdb->get_results("SELECT * from $table_usermeta where active_status='1'");
		foreach($getusermetaid as $getid)
			{
				$userid[]=$getid->id;
			}
			
			$metaval = implode(',',$metakey);
		
		$wpdb->query("UPDATE $table_usermeta SET active_status = '1' WHERE id in($metaval)");
		$message.="Meta inserted";
		//echo "<br>";print_r($userid); 
		//echo "<br>";print_r($metakey); 
			$resultdata=array_diff($userid,$metakey);
				//echo "<br>";print_r($resultdata);
			$deactive = implode(',',$resultdata);
		//die;
		$wpdb->query("UPDATE $table_usermeta SET active_status = '0' WHERE id in($deactive)");
				
	}
	
    }
    ?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/selectmetadata/style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h2>Add metadata</h2>
        <?php if (isset($message)): ?><div class="updated"><p><?php echo $message; ?></p></div><?php endif; ?>
        <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
           
            <table class='wp-list-table widefat fixed'>
			<?php
			 global $wpdb;
        
		$table_usermeta = $wpdb->prefix . "select_usermeta";
        $fetchusermeta = $wpdb->get_results("SELECT * from $table_usermeta order by id asc");
			//echo $wpdb->last_query; die;
			foreach($fetchusermeta as $usermeta)
			{
			
			?>
			
                <tr>
                  
                    <td><input type="text" name="metaname12[]" value="<?php echo $usermeta->usermeta_key; ?>" readonly class="ss-field-width" /></td>
				<td><input type="text" name="metaname[]" value="<?php echo $usermeta->usermeta_name; ?>" class="ss-field-width" /></td>
				<?php if($usermeta->active_status =='1')
				{ ?>
                <td> <input type="checkbox" name="metakey[]" checked class="form-control" value="<?php echo $usermeta->id; ?>"></td>
				<?php } else {?>
				 <td> <input type="checkbox" name="metakey[]" class="form-control" value="<?php echo $usermeta->id; ?>"></td>
				<?php } ?>
                    
					<input type="hidden" name="hiddenid[]" value="<?php echo $usermeta->id; ?>" class="ss-field-width" />
					
                </tr>
				<?php } ?>
            </table>
            <input type='submit' name="insert" value='Save' class='button'>
        </form>
    </div>
    <?php
}
?>