<?php
/**
 *	@package TN ads network 
 */
/*
Plugin Name: Turtle Ad Network
Plugin URI: https://t.me/turtleadnetwork
Description: Peer-to-Peer Ads using the Turtle Network blockchain
Version: 1.0.13
Auther: https://t.me/gordobtel
Auther URI: https://t.me/turtleadnetwork
Text Domain: turtle-ad-network
License: GPLv3
*/

namespace TurtleAdsNetwork;


if ( ! defined('ABSPATH') ) {
	die;
}

global $wpdb;
$tadn_jal_db_version = '1.1';

class TADN_TurtleAdsNetwork
{

	/*
	**	initialze all tables and populate required predefined data 
	*/
	function __construct(){
		/*
		**	Initialize some stuff to get started
		*/
		$this->tadn_create_WalletAdress_table();
		$this->tadn_create_adSegment_table();
		$this->tadn_create_adSize_table();
		$this->tadn_create_ad_segment_details_table();
		$this->tadn_create_min_amount_txid_table();
		
		include( plugin_dir_path( __FILE__ ). 'includes/create-menus.php');

		/*
		**	handle wallet address form request
		**	submit-form-tn
		**  submit-form-add-ad-slots
		*/
		add_action('admin_post_submit-form-tn', array($this , 'tadn_handle_form_action')); 
		add_action('admin_post_nopriv_submit-form-tn', array($this, 'tadn_handle_form_action')); 

		add_action('admin_post_submit-form-add-ad-slots', array($this , 'tadn_handle_form_action_slot')); 
		add_action('admin_post_nopriv_submit-form-add-ad-slots', array($this, 'tadn_handle_form_action_slot')); 

		
		add_action('tan_cronjob', array($this, 'tadn_do_this_hourly'));

		add_filter( 'cron_schedules', array($this,'tadn_add_cron_interval' ));

		
	}

	

	function tadn_add_cron_interval( $schedules ) {
		 $schedules['onemin'] = array(
		 'interval' => 60,
		 'display' => esc_html__( 'Every one min' ),
		 );

		return $schedules;
	}

	
	function tadn_activate(){

	    
	    $this->tadn_create_page();
		
		if (! wp_next_scheduled ( 'tan_cronjob' )) {
			wp_schedule_event(time(), 'onemin', 'tan_cronjob');
			update_option('api_server','https://ninjastar.ninjaturtle.co.za');
		}
	}

	function tadn_deactivate(){
	    
	    /* Rename the table name Query*/

		global $wpdb;
	    $table_name = "tan_adsegment";
	    $sql = "DROP TABLE IF EXISTS $table_name";
	    $wpdb->query($sql);	

	    $first_table_name = "tan_ad_segment_details";
	    $sql_first = "DROP TABLE IF EXISTS $first_table_name";
	    $wpdb->query($sql_first);	

	    $second_table_name = "tan_wallet_address";
	    $sql_second = "DROP TABLE IF EXISTS $second_table_name";
	    $wpdb->query($sql_second);	


	    $third_table_name = "tan_ads_size";
	    $sql_third = "DROP TABLE IF EXISTS $third_table_name";
	    $wpdb->query($sql_third);	


	    $fourth_table_name = "tan_min_amount_txid";
	    $sql_fourth = "DROP TABLE IF EXISTS $fourth_table_name";
	    $wpdb->query($sql_fourth);


	    // delete the tanstats page
	    $this->tadn_delete_page();

		// nothing here
		wp_clear_scheduled_hook('tan_cronjob');

		//$sql = "ALTER TABLE wp_ad_segment_details RENAME TO wp_tan_ad_segment_details";
		//$wpdb->query($sql);


	}
	function tadn_do_this_hourly() {
		// do something every one minute

		// include decoding file
		include(plugin_dir_path( __FILE__ ).'base58php/test.php');

		// db connection
		global $wpdb;
		//tables names
		$table_name_segment_details = $wpdb->prefix.'tan_ad_segment_details';
		$table_name_address 		= $wpdb->prefix.'tan_wallet_address';
		$table_name 				= $wpdb->prefix.'tan_adsegment';
		$slot_size_table_name 		= $wpdb->prefix.'tan_ads_size';

		//fetch ad_seg data
		$datas = $wpdb->get_results("SELECT * FROM $table_name");

		// some predefine value from settings
		$min_amount = get_option('min_amount');
		$ad_time = get_option('ad_time');;
		

		foreach($datas as $data){

			$seg_id = $data->id;
			$size_id = $data->size_id;
			$all_size = $wpdb->get_results("SELECT * FROM $slot_size_table_name WHERE id = $size_id");
			$slug_name = $data->ad_segment_name;
			$address = $data->address_id;
			$width = $all_size[0]->width;
			$height = $all_size[0]->height;

			$get_address = $wpdb->get_results("SELECT * FROM $table_name_address WHERE id = $address");
			$address = $get_address[0]->address;

			$blacklist_array = explode(',', get_option('blacklist'));
			// check for backlkist/spam address
			if(!in_array($address, $blacklist_array)){ echo "GOIND";

				$url = get_option('api_server'). '/transactions/address/'.$address.'/limit/10';

				$curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => $url,
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "GET",
                  CURLOPT_HTTPHEADER => array(
                    "Accept: */*",
                    "Cache-Control: no-cache",
                    "Connection: keep-alive",
                    "Host: ninjastar.ninjaturtle.co.za",
                  ),
                ));
                
                $response = curl_exec($curl);
                $err = curl_error($curl);
                
                curl_close($curl);
                
                if ($err) {
                  echo "cURL Error #:" . $err;
                } else {
                    
					$response = json_decode($response,true);
					
					// get selected payment type from settings
					$payment_type = get_option('payment_type');
				
    				foreach($response[0] as $value){
    
    					if($value['type'] == 4){
							// when tUSD is selected 
							if($payment_type == 'tusd' && $value['assetId'] == '2R7raH74LuuiCbJbcv3Aa7g14WY1vYPUGushCUJFwW1f'){
								$attachement = $value['attachment'];
								$amount = $value['amount'] / 100000000;
								$min_txid = $value['id'];
								$sender = $value['sender'];
								$decoded_attachment = decode_attachement($attachement);
		
								foreach($blacklist_array as $values){
		
									if(substr_count($decoded_attachment, $values) > 0 || in_array($sender, $blacklist_array)){
										$backlkist_count = 1;
									}
		
		
								}
		
								$table_name_min_txid =  $wpdb->prefix.'_tan_min_amount_txid';
		
								$chck_min_amount = $wpdb->get_results("SELECT * FROM $table_name_min_txid WHERE txid = '$min_txid'");
								if( $amount < $min_amount && empty($chck_min_amount)){
		
									$wpdb->insert($table_name_min_txid,array('txid' => $min_txid));
		
								}
		
								if( ( $amount >= $min_amount ) && ( strpos($decoded_attachment,'Ad') === 0 ) && (strpos($decoded_attachment,'(') === 3) && (strlen($decoded_attachment) <= 140) && !in_array($decoded_attachment, $blacklist_array) && substr_count($decoded_attachment,"(") == 3 && substr_count($decoded_attachment,")") == 3 && $backlkist_count == 0 && empty($chck_min_amount) ) {
		
									$a = explode("(",$decoded_attachment);
		
									$b = explode(")", $a[1]);
		
									$c = explode(")", $a[2]);
		
									$d = explode(")", $a[3]);
		
									$headline = $b[0];
		
									$des = $c[0];
		
									$clickable = $d[0];
		
									$txid = $value['id'];
		
									$time_period = $amount * $ad_time;
		
									$check = $wpdb->get_results('SELECT * FROM '.$table_name_segment_details .' WHERE txid = "'.$txid.'" AND headline = "'.esc_html($headline).'" AND des = "'.esc_html($des).'"');
		
									if(empty($check) && !empty($headline) && !empty($des) && !empty($clickable) && !empty($txid) && !empty($time_period)){
		
										$start_time = strtotime(date("Y-m-d h:i:sa"));
									
										//$end_time = date('Y-m-d h:i:sa', strtotime($add_time, $start_time));
		
										$insert = $wpdb->insert($table_name_segment_details,array(
											'seg_id' 		=> intval($seg_id),
											'headline'		=> esc_html($headline),
											'des'			=> esc_html($des),
											'clickable'		=> esc_url($clickable),
											'txid'			=> esc_html($txid),
											'time_period'	=> floatval($time_period),
											'time'			=> $start_time,
											'sender_add'    => $sender,
											'status'		=> 0
										));
										if($insert){
											echo "Inserted<br>";
										}else{
											echo "Not inserted<br>";
										}
									}else{
										echo "No duplicates :) <br>";
									}
		
								}else{
									echo "Ad submission formate not matched, Invalid ad :(";
								}
							}
							// when TN is selected or by default 
							if($payment_type == 'tn' || $payment_type == ''){
								$attachement = $value['attachment'];
								$amount = $value['amount'] / 100000000;
								$min_txid = $value['id'];
								$sender = $value['sender'];
								$decoded_attachment = decode_attachement($attachement);
		
								foreach($blacklist_array as $values){
		
									if(substr_count($decoded_attachment, $values) > 0 || in_array($sender, $blacklist_array)){
										$backlkist_count = 1;
									}
		
		
								}
		
								$table_name_min_txid = $wpdb->prefix.'tan_min_amount_txid';
		
								$chck_min_amount = $wpdb->get_results("SELECT * FROM $table_name_min_txid WHERE txid = '$min_txid'");
								if( $amount < $min_amount && empty($chck_min_amount)){
		
									$wpdb->insert($table_name_min_txid,array('txid' => $min_txid));
		
								}
		
								if( ( $amount >= $min_amount ) && ( strpos($decoded_attachment,'Ad') === 0 ) && (strpos($decoded_attachment,'(') === 3) && (strlen($decoded_attachment) <= 140) && !in_array($decoded_attachment, $blacklist_array) && substr_count($decoded_attachment,"(") == 3 && substr_count($decoded_attachment,")") == 3 && $backlkist_count == 0 && empty($chck_min_amount) ) {
		
									$a = explode("(",$decoded_attachment);
		
									$b = explode(")", $a[1]);
		
									$c = explode(")", $a[2]);
		
									$d = explode(")", $a[3]);
		
									$headline = $b[0];
		
									$des = $c[0];
		
									$clickable = $d[0];
		
									$txid = $value['id'];
		
									$time_period = $amount * $ad_time;
		
									$check = $wpdb->get_results('SELECT * FROM '.$table_name_segment_details .' WHERE txid = "'.$txid.'" AND headline = "'.esc_html($headline).'" AND des = "'.esc_html($des).'"');
		
									if(empty($check) && !empty($headline) && !empty($des) && !empty($clickable) && !empty($txid) && !empty($time_period)){
		
										$start_time = strtotime(date("Y-m-d h:i:sa"));
									
										//$end_time = date('Y-m-d h:i:sa', strtotime($add_time, $start_time));
		
										$insert = $wpdb->insert($table_name_segment_details,array(
											'seg_id' 		=> intval($seg_id),
											'headline'		=> esc_html($headline),
											'des'			=> esc_html($des),
											'clickable'		=> esc_url($clickable),
											'txid'			=> esc_html($txid),
											'time_period'	=> floatval($time_period),
											'time'			=> $start_time,
											'sender_add'    => $sender,
											'status'		=> 0
										));
										if($insert){
											echo "Inserted<br>";
										}else{
											echo "Not inserted<br>";
										}
									}else{
										echo "No duplicates :) <br>";
									}
		
								}else{
									echo "Ad submission formate not matched, Invalid ad :(";
								}
							}
    					}
    				}
                }
			}
			
		}
	}

	/*
	**	Create Wallet Address table 
	*/
	function tadn_create_WalletAdress_table(){

		global $wpdb;
		global $tadn_jal_db_version;

		$table_name = $wpdb->prefix.'tan_wallet_address';
		
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id int(11) NOT NULL AUTO_INCREMENT,
			address varchar(200) NOT NULL,
			label varchar(50) NOT NULL,
			status int(11) NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		add_option( 'tadn_jal_db_version', $tadn_jal_db_version );

	}

	/*
	**	Create Ad Segment table 
	*/
	function tadn_create_adSegment_table(){

		global $wpdb;
		global $tadn_jal_db_version;

		$table_name = $wpdb->prefix.'tan_adsegment';
		
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id int(11) NOT NULL AUTO_INCREMENT,
			address_id int(11) NOT NULL,
			status int(11) NOT NULL,
			size_id int(11) NOT NULL,
			ad_segment_name varchar(256) NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		add_option( 'tadn_jal_db_version', $tadn_jal_db_version );

	}

	/*
	**	Create Ad size table 
	** 	populate Ad size table with predefined size
	*/
	function tadn_create_adSize_table(){

		

		global $wpdb;
		global $tadn_jal_db_version;

		$table_name = $wpdb->prefix.'tan_ads_size';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id int(11) NOT NULL AUTO_INCREMENT,
			width int(11) NOT NULL,
			height int(11) NOT NULL,
			name varchar(256) NOT NULL,
			status int(11) NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		add_option( 'tadn_jal_db_version', $tadn_jal_db_version );

		
		/*
		** insert predefined size of ad segments 
		** insert only once
		*/
		global $wpdb;
		$check = $wpdb->get_results('SELECT * FROM ' . $table_name);
		if(empty($check)){
			$width_array = array(728,320);
			$height_array = array(90,100);
			$name_array = array('Leaderboard','Large Mobile Banner');
			for($i=0;$i<=1;$i++){
				$wpdb->insert( 
					$table_name, 
					array( 
						'width' => $width_array[$i], 
						'height' => $height_array[$i],
						'name' => $name_array[$i],
						'status' => 1
					) 
				);
			}
		}
	}

	/*
	**	Create create_ad_segment_details_table table 
	** 
	*/
	function tadn_create_ad_segment_details_table(){

		global $wpdb;
		global $tadn_jal_db_version;

		$table_name = $wpdb->prefix.'tan_ad_segment_details';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			ID int(11) NOT NULL AUTO_INCREMENT,
			seg_id int(11) NOT NULL,
			headline varchar(100) NOT NULL,
			des varchar(100) NOT NULL,
			clickable varchar(100) NOT NULL,
			txid varchar(1000) NOT NULL,
			time_period int(11) NOT NULL,
			time varchar(255) NOT NULL,
			status int(11) NOT NULL,
			approve_status int(11) NOT NULL,
			PRIMARY KEY  (ID)
		) $charset_collate;";


		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		add_option( 'tadn_jal_db_version', $tadn_jal_db_version );

		$installed_ver = get_option( "tadn_jal_db_version" );

		if ( $installed_ver != $tadn_jal_db_version ) {

			$table_name = $wpdb->prefix.'tan_ad_segment_details';
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
						ID int(11) NOT NULL AUTO_INCREMENT,
						seg_id int(11) NOT NULL,
						headline varchar(100) NOT NULL,
						des varchar(100) NOT NULL,
						clickable varchar(100) NOT NULL,
						txid varchar(1000) NOT NULL,
						time_period int(11) NOT NULL,
						time varchar(255) NOT NULL,
						sender_add varchar(255) NOT NULL,
						status int(11) NOT NULL,
						approve_status int(11) NOT NULL,
						display_status int(11) NOT NULL,
						PRIMARY KEY  (ID)
					) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

			update_option( "tadn_jal_db_version", $tadn_jal_db_version );
		}

	}

	/*
	**	Create create_ad_segment_details_table table 
	** 
	*/
	function tadn_create_min_amount_txid_table(){

		global $wpdb;
		global $tadn_jal_db_version;

		$table_name = $wpdb->prefix.'tan_min_amount_txid';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id int(11) NOT NULL AUTO_INCREMENT,
			txid varchar(656) NOT NULL,
			status int(11) NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		add_option( 'tadn_jal_db_version', $tadn_jal_db_version );
	}

	/*
	**	handle wallet address form submit
	*/

	function tadn_handle_form_action(){
		if(!current_user_can('administrator') || ! isset( $_POST['turtle-network-walletAddress-form-field'] ) || ! wp_verify_nonce( $_POST['turtle-network-walletAddress-form-field'], 'turtle-network-walletAddress-form-action' ) ) {

		   print 'Sorry, you Don\'t have Permission to perfrom this action.';
		   exit;

		}else{
			global $wpdb;
			$table_name = $wpdb->prefix.'tan_wallet_address';
			$address = sanitize_text_field($_POST['address']);
			$label = sanitize_text_field($_POST['label']);
			$id = intval($_POST['hide']);
			$strpos = strpos($address, '3J');
			$redirect_url = esc_url_raw($_POST['redirect_url']);
			if($strpos === 0 && strlen($address) == 35){

				$check = $wpdb->get_results("SELECT * FROM $table_name WHERE address = '$address' OR label = '$label' ");
				if($id == ''){
					if(empty($check)){

						$wpdb->insert($table_name, array('label' => $label, 'address' => $address));
						wp_redirect($redirect_url."&msg=1"); // add a hidden input with get_permalink()
			     		die();

					}else{

						wp_redirect($redirect_url."&msg=0"); // add a hidden input with get_permalink()
			     		die();

					}
				}else{
					$wpdb->update($table_name, array('label' => $label, 'address' => $address), array('id' => $id));
					wp_redirect($redirect_url."&msg=2"); // add a hidden input with get_permalink()
			     	die();

				}
			}else{

				wp_redirect($redirect_url."&msg=3"); // add a hidden input with get_permalink()
			    die();

			}
		}
	}

	/*
	**	handle ad segment form submit
	*/

	function tadn_handle_form_action_slot(){

		if(!current_user_can('administrator') || ! isset( $_POST['turtle-network-slot-form-field'] ) || ! wp_verify_nonce( $_POST['turtle-network-slot-form-field'], 'turtle-network-slot-form-action' ) ) {

		   print 'Sorry, you Don\'t have Permission to perfrom this action.';
		   exit;

		}else{

			global $wpdb;
			$table_name = $wpdb->prefix.'tan_adsegment';
			$address_id = intval($_POST['address_id']);
			$label = str_replace(" ", "_", sanitize_text_field($_POST['label']));
			$id = intval($_POST['hide']);
			$size_id = intval($_POST['size_id']);

			$redirect_url = esc_url_raw($_POST['redirect_url']);

			$check = $wpdb->get_results("SELECT * FROM $table_name WHERE ad_segment_name = '$label' ");
			if($id == ''){
				if(empty($check)){

					$wpdb->insert($table_name, array('ad_segment_name' => $label, 'address_id' => $address_id, 'size_id' => $size_id));
					wp_redirect($redirect_url."&msg=1"); // add a hidden input with get_permalink()
		     		die();

				}else{

					wp_redirect($redirect_url."&msg=0"); // add a hidden input with get_permalink()
		     		die();

				}
			}else{
				$wpdb->update($table_name, array('ad_segment_name' => $label, 'address_id' => $address_id, 'size_id' => $size_id), array('id' => $id));
				wp_redirect($redirect_url."&msg=2"); // add a hidden input with get_permalink()
		     	die();
			}
		}
	}
	
	/*
	**	Create page to show tanstats
	*/
	
	function tadn_create_page(){
	   
        // Create post object
        $my_post = array(
          'post_title'    => wp_strip_all_tags( 'TANstats' ),
          'post_content'  => '[TAN-showtanstats]',
          'post_status'   => 'publish',
          'post_author'   => 1,
          'post_type'     => 'page',
        );
    
        // Insert the post into the database
        wp_insert_post( $my_post );
	    
	}
	
	/*
	**	delete page to show tanstats
	*/
	function tadn_delete_page(){
	    
	    $page = get_page_by_path( 'TANstats' );
        
        wp_delete_post($page->ID, true);
	    
	}
}

if( class_exists('\TurtleAdsNetwork\TADN_TurtleAdsNetwork') ){
	$turtle = new \TurtleAdsNetwork\TADN_TurtleAdsNetwork();
}


// db connection
global $wpdb;
//tables names
$table_name_segment_details = $wpdb->prefix.'tan_ad_segment_details';

$table_name 				= $wpdb->prefix.'tan_adsegment';
$slot_size_table_name 		= $wpdb->prefix.'tan_ads_size';

$imgUrl = plugins_url( 'includes/imgs/1.png', __FILE__ );
//fetch ad_seg data
$datas = $wpdb->get_results("SELECT * FROM $table_name");

foreach($datas as $data){
	
	
	$slug_name = esc_html($data->ad_segment_name);
	
	$cb = function() use ($slug_name) {

		// db connection
		global $wpdb;
		//tables names
		$table_name_segment_details = $wpdb->prefix.'tan_ad_segment_details';

		$table_name 				= $wpdb->prefix.'tan_adsegment';
		$slot_size_table_name 		= $wpdb->prefix.'tan_ads_size';
		if(intval(get_option('ad_approval')) == 1){
			$approval_query 			= "AND $table_name_segment_details.approve_status = 1";
		}else{
			$approval_query 			= "AND $table_name_segment_details.approve_status = 0";
		}

		$current_time = date("Y-m-d h:i:sa");
		$current_timestap = strtotime($current_time);
		//fetch ad_seg data
		$datas = $wpdb->get_results("SELECT * FROM $table_name_segment_details INNER JOIN $table_name ON $table_name_segment_details.seg_id=$table_name.id INNER JOIN $slot_size_table_name ON $table_name.size_id=$slot_size_table_name.id WHERE $table_name.ad_segment_name = '$slug_name' AND $table_name_segment_details.status < $table_name_segment_details.time_period $approval_query AND $table_name_segment_details.display_status != 2");

		$x = 0;
		if(!empty($datas)){
			foreach($datas as $data){


				$headline 	 = esc_html($data->headline);
				$width 		 = intval($data->width);
				$height 	 = intval($data->height);
				$des 		 = esc_html($data->des);
				$clickable 	 = esc_url($data->clickable);
				$time_period = floatval($data->time_period);
				$ids 	 	 = intval($data->ID);
				$txid		 = esc_html($data->txid);

				$imgUrl = plugins_url( 'includes/imgs/1.png', __FILE__ );

				if($width == 728){
					$img_width = '2%';
					$style_a = 'font-size:17px;padding:0px;';
				}else if($width = 320){
					$img_width = '3%';
					$style_a = 'font-size:13px;padding: 5px;';
				}else{
					$img_width = '5%';
				}

				$a[] = '<div style="width:'.$width.'px;height:'.$height.'px;border:1px solid black;text-align: center;'.$style_a.'"><b style="text-transform: uppercase;">'.$headline.'</b><br>'.$des.'<br><a href="'.$clickable.'" target="_blank" style="text-decoration:none">'.$clickable.'</a><a href="https://explorer.turtlenetwork.eu/tx/'.$txid.'" target="_blank" style="margin-left:10px;text-decoration:none">#</a></div>tan_idssx:'.$ids;
				$x++;

			}


			shuffle($a);

			$explode = explode("tan_idssx:",$a[0]);

			$ad = $explode[0];
			$id =  intval($explode[1]);

			$check = $wpdb->get_results("SELECT * FROM $table_name_segment_details WHERE ID = $id");

			$db_time_period = $check[0]->time_period;

			$brsw_time_period = $check[0]->status;

			// update brsw_time_period

			if(empty($brsw_time_period) || $brsw_time_period == 0){
			 	$brsw_time_period = 1;
			}else{
			 	$brsw_time_period = $brsw_time_period + 1;
			}
			$update = $wpdb->update($table_name_segment_details,array('status' => $brsw_time_period),array('ID' => $id));

			// show ad
			return $ad;
			
		}
	};  

    add_shortcode( "TAN-$slug_name", $cb );
    add_filter( 'widget_text', 'do_shortcode' );
}


$slug_name_stats = 'showtanstats';

$stats = function() use ($slug_name_stats) {
    
    $address = $_GET['address'];
?>
    
    <style>
        #customers {
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            max-width: 100%;
            font-size: 12px;
        }
        
        #customers td, #customers th {
          border: 1px solid #ddd;
          padding: 8px;
        }
        
        #customers tr:nth-child(even){background-color: #f2f2f2;}
        
        #customers tr:hover {background-color: #ddd;}
        
        #customers th {
          padding-top: 12px;
          padding-bottom: 12px;
          text-align: left;
          background-color: #4CAF50;
          color: white;
          min-width: 40px !important;
        }
    </style>
    
    
<?php global $wpdb;

    $t1 = $wpdb->prefix.'tan_ad_segment_details';
    $t2 = $wpdb->prefix.'tan_adsegment';
    $t3 = $wpdb->prefix.'tan_ads_size';
    $t4 = $wpdb->prefix.'tan_wallet_address';
    
    if($address != ''){
    
        $data = $wpdb->get_results("SELECT * FROM $t1 INNER JOIN $t2 ON $t1.seg_id=$t2.id INNER JOIN $t3 ON $t2.size_id=$t3.id WHERE sender_add = '$address' ORDER BY $t1.ID DESC");
        $x=1;
        if(empty($data)){
            _e('No data yet!!');
            
        }else{ 
     
        $table_top = '<table id="customers"><tr><th>#</th><th>Headline</th><th>Description</th><th>Clickable Link</th><th>Txid</th><th>Impressions Purchased</th><th>Assigned Size</th><th>Start Date</th><th>Current Impressions</th></tr>';
        
        ?>
        <?php foreach ($data as $value) {

            $data_size = $value->width." X ". $value->height ." - " .$value->name;
            $address_table_name = $wpdb->prefix.'tan_wallet_address';
            $ad_id = intval($value->address_id);
            $all_addresss =  $wpdb->get_results("SELECT * FROM $t4 WHERE id = $ad_id");
            $addd = $all_addresss[0]->address;

            $ID = intval($value->ID);
            $start_timee = date('Y-m-d h:i:s a',$value->time);
            $end_timee = $wpdb->get_var("SELECT status FROM $t1 WHERE ID = $ID");
            
            $table_body .= '<tr class="alternate" valign="top"> 
            <td>'.$ID.'</td>
            <td>'.$value->headline.'</td>
            <td>'.$value->des.'</td>
            <td>'.$value->clickable.'</td>
            <td><a href="https://explorer.turtlenetwork.eu/tx/'.$value->txid.'" target="_blank">'.$value->txid.'</a></td>
            <td>'.$value->time_period.'</td>
            <td>'.$data_size.'</td>
            <td>'.$start_timee.'</td>
            <td>'.$end_timee.'</td></tr>';
            
        }
        
        $table_bottom = "</table>";

            return $table_top.$table_body.$table_bottom;
        }
    }else{
        return "No address given!";
    }
};  
add_shortcode( "TAN-$slug_name_stats", $stats );





// activation
register_activation_hook( __FILE__, array( $turtle, 'tadn_activate' ) );

// deactivation
register_deactivation_hook( __FILE__, array( $turtle, 'tadn_deactivate' ) );