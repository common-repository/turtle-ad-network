<?php

if ( ! defined('ABSPATH') ) {
	die;
}

// get admin url for current page using slug
$admin_url = menu_page_url( 'tan-add-wallet-address',false); 

?>

<h3>Add Wallet Address</h3>
<?php
global $wpdb;

$table_name = $wpdb->prefix.'tan_wallet_address';
if(isset($_GET['turtle-network-wallet-edit-nonce']) && current_user_can('administrator') && wp_verify_nonce($_GET['turtle-network-wallet-edit-nonce'], 'turtle-network-wallet-edit-action')){
	if(isset($_GET['id'])){
		if($_GET['action'] == 'delete'){

			$wpdb->delete($table_name,array('id' => intval($_GET['id'])));

			$table_name_seg = $wpdb->prefix.'tan_adsegment';
			$wpdb->delete($table_name_seg,array('address_id' => intval($_GET['id'])));

			_e('Deleted Successfully!!','turtle-ad-network');
			$btntxt = 'Add ';
			$labell =  '';
			$addresss =  '';
			$get_data = '';
			$id = '';

		}else{

			$id = intval($_GET['id']);
			$get_data = $wpdb->get_results("SELECT * FROM $table_name WHERE id = $id");
			$labell =  $get_data[0]->label;
			$addresss =  $get_data[0]->address;
			$btntxt = 'Update ';
		}
	}else{
		$id = '';
		$btntxt = 'Add ';
		$labell =  '';
		$addresss =  '';
		$get_data = '';
	}
}

if(isset($_GET['msg'])){

	if(intval($_GET['msg']) == 1){
		_e('Added Successfully!!','turtle-ad-network');
	}else if(intval($_GET['msg']) == 2){
		_e("Updated Successfully!!",'turtle-ad-network');
	}else if(intval($_GET['msg']) == 3){
		_e("Invalid Address",'turtle-ad-network');
	}else{
		_e("Label or Address already exists",'turtle-ad-network');
	
	}
}




?>

<form action='<?php _e(get_admin_url(),'turtle-ad-network'); ?>admin-post.php' method="post">
	<?php wp_nonce_field( 'turtle-network-walletAddress-form-action', 'turtle-network-walletAddress-form-field'); ?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row"><label for="label">Address Label</label></th>
				<td><input name="label" placeholder="Enter Address Label" type="text" id="label" value="<?php _e($labell,'turtle-ad-network');?>" class="regular-text" required></td>
			</tr>
			<tr>
				<th scope="row"><label for="address">Wallet Address</label></th>
				<td><input name="address" placeholder="Enter Turtle Network Wallet Address Only" type="text" id="address" value="<?php _e($addresss,'turtle-ad-network');?>" class="regular-text" required></td>
			</tr>
			<input type='hidden' name='action' value='submit-form-tn' />
			<input type='hidden' name='hide' value="<?php _e($id,'turtle-ad-network');?>" />
			<input type="hidden" name="redirect_url" value="<?php echo esc_url($admin_url);?>">
		</tbody>
	</table>
	<p class="submit">
		<input type="submit" name="tn_submit" id="submit" class="button button-primary" value="<?php _e($btntxt,'turtle-ad-network');?> Create Wallet Address">
	</p>
</form>

<table class="widefat fixed" cellspacing="0" style="width: 66%;">
    <thead>
    <tr>

            <th id="cb" class="manage-column column-columnname" scope="col" style="width: 5%;">#</th> 
            <th id="columnname" class="manage-column column-columnname" scope="col" style="width: 13%;">Label</th>
            <th id="columnname" class="manage-column column-columnname" scope="col" style="width: 33%;">Address</th> 
            
            

    </tr>
    </thead>

    <tfoot>
    <tr>

            <th id="cb" class="manage-column column-columnname" scope="col">#</th> 
            <th id="columnname" class="manage-column column-columnname" scope="col">Label</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Address</th> 
            
            

    </tr>
    </tfoot>

    <tbody>       
	<?php 
		
		$data = $wpdb->get_results("SELECT * FROM $table_name");
		$x=1;
		if(empty($data)){_e('No data yet!!','turtle-ad-network');}	
		foreach ($data as $value) {
	?>

        <tr class="alternate" valign="top"> 
            <th class="check-column" scope="row" style="padding: 11px 0 0 10px;"><?php _e($x,'turtle-ad-network');?></th>
            <td class="column-columnname"><?php esc_html_e($value->label,'turtle-ad-network');?>
                <div class="row-actions">
                	
                	
                    <span><a href="<?php print wp_nonce_url($admin_url.'&action=edit&id='.intval($value->id), 'turtle-network-wallet-edit-action', 'turtle-network-wallet-edit-nonce'); ?>">Edit</a> |</span>
                    <span><a href="#" onclick="deletefun('<?php print wp_nonce_url($admin_url.'&action=delete&id='.intval($value->id), 'turtle-network-wallet-edit-action', 'turtle-network-wallet-edit-nonce'); ?>')">Delete</a></span>
                </div>
            </td>
            <td class="column-columnname"><?php esc_html_e($value->address,'turtle-ad-network');?></td>
            
            
        </tr>

    <?php $x++;} ?>
    </tbody>
</table>
<script>
	function deletefun(id) {
		// body...
		var c = confirm("Are you sure you want to delete this? The assigned Ad segment will also delete!");

		if(c == true){

			window.location.href = id;
		}else{
			// do nothing 
		}


	}
</script>