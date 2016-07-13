<?php

function select_meta_list() {
	?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/selectmetadata/style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h2>Selectmetadata</h2>
        <div class="tablenav top">
            <div class="alignleft actions">
                <a href="<?php echo admin_url('admin.php?page=select_meta_create'); ?>">Add meta</a>
            </div>
            <br class="clear">
        </div>
        <?php
        global $wpdb;
        $table_name = $wpdb->prefix . "select_usermeta";

        $rows = $wpdb->get_results("SELECT * from $table_name where active_status='1'");
        ?>
        <table class='wp-list-table widefat fixed striped posts'>
            <tr>
                <th class="manage-column ss-list-width">Usermeta</th>
                <th class="manage-column ss-list-width">Name</th>
				 <th class="manage-column ss-list-width">Action</th>
               
            </tr>
            <?php foreach ($rows as $row) { ?>
                <tr>
                    <td class="manage-column ss-list-width"><?php echo $row->usermeta_key; ?></td>
                    <td class="manage-column ss-list-width"><?php echo $row->usermeta_name; ?></td>
                    <td><a href="<?php echo admin_url('admin.php?page=select_meta_update&id=' . $row->id); ?>"><img src="<?php echo WP_PLUGIN_URL; ?>/usermetadata/image/edit-icon.png" alt="edit" height="26" width="26"></a></td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <?php
}