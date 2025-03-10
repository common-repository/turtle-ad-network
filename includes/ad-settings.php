<?php 

if ( ! defined('ABSPATH') ) {
	die;
}

if(!current_user_can('administrator')){
  die("Sorry, you dont have Permission to perfrom this action.");
}


if(isset($_POST['tn_setting_submit']) && current_user_can('administrator')){

  if ( ! isset( $_POST['turtle-network-settings-form-field'] ) || ! wp_verify_nonce( $_POST['turtle-network-settings-form-field'], 'turtle-network-settings-form-action' ) ) {

   print 'Sorry, you don\'t have Permission to perfrom this action.';
   exit;

} else {

    global $wpdb;

    if(!floatval($_POST['min_amount']) || !floatval($_POST['ad_time']) || !floatval($_POST['ad_cost'])){
      _e("Only Numbers are allowed!");
    }	else{

    	update_option('min_amount', floatval($_POST['min_amount']));
    	update_option('ad_time', floatval($_POST['ad_time']));
    	update_option('ad_cost', floatval($_POST['ad_cost']));
    	update_option('api_server', esc_url_raw($_POST['api_server']));
    	update_option('blacklist', esc_html($_POST['blacklist']));
      update_option('ad_approval', intval($_POST['ad_approval']));
      update_option('payment_type', esc_html($_POST['payment_type']));
      $t1 = $wpdb->prefix.'tan_ad_segment_details';
      if($_POST['ad_approval'] == 0){

        $res = $wpdb->get_results("SELECT * FROM $t1");

        foreach($res as $value){

          $wpdb->update($t1,array('approve_status' => 0), array('ID' => intval($value->ID)));

        }
      }



      _e("Updated");

    }

  }

}
?>
<style>
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input {display:none;}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}

.tooltip {
    position: relative;
    display: inline-block;
    border-bottom: 1px dotted black;
}

.tooltip .tooltiptext {
    visibility: hidden;
    width: 150px;
    background-color: black;
    color: #fff;
    text-align: center;
    border-radius: 6px;
    padding: 7px 7px;
    position: absolute;
    z-index: 1;
    top: 150%;
    left: 50%;
    margin-left: -60px;
    word-break: break-word;
}

.tooltip .tooltiptext::after {
    content: "";
    position: absolute;
    bottom: 100%;
    left: 50%;
    margin-left: -5px;
    border-width: 5px;
    border-style: solid;
    border-color: transparent transparent black transparent;
}

.tooltip:hover .tooltiptext {
    visibility: visible;
}
span.toggle_payment_type {
    text-transform: uppercase;
}
</style>
<form action='' method="post">
  <?php wp_nonce_field( 'turtle-network-settings-form-action', 'turtle-network-settings-form-field'); ?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
          <label class="tooltip" for="label">Set API Server
            <span class="tooltiptext">Set any node on the Turtle Network from list: https://explorer.turtlenetwork.eu/nodes</span>
          </label>
        </th>
				<td><input name="api_server" placeholder="" type="text" value="<?php echo esc_url(get_option('api_server'));?>" class="regular-text" required></td>
			</tr>
      <tr>
				<th scope="row"><label class="tooltip"  for="label">Payment Type <span class="tooltiptext">Set the Currency Payment Type for Ads, paid in tUSD or TN</span></label></th>
				<td>
          <select name="payment_type" id="payment_type" onchange="getPaymentType()">
          <?php 
            if(get_option('payment_type') == 'tn') {

              $selected_tn = 'Selected';

            }else if(get_option('payment_type') == 'tusd') {
              $selected_tusd = 'Selected';
            }

          ?>
          
            <option value="tn" <?php echo $selected_tn;?>>TN</option>
            <option value="tusd" <?php echo $selected_tusd;?>>TUSD</option>
          </select>
        </td>
			</tr>
			<tr>
				<th scope="row"><label class="tooltip"  for="label">Minimum Amount in <span class="toggle_payment_type"><?php echo (esc_html(get_option('payment_type')) != '') ? esc_html(get_option('payment_type')) : 'TN';?></span>  <span class="tooltiptext">Minimum payment required for an Ad to be displayed, in $<span class="toggle_payment_type"><?php echo  (esc_html(get_option('payment_type')) != '') ? esc_html(get_option('payment_type')) : 'TN';?></span></span></label></th>
				<td><input name="min_amount" placeholder="" type="text" value="<?php _e(intval(get_option('min_amount')));?>" class="regular-text" required></td>
			</tr>
			<tr>
				<th scope="row"><label class="tooltip"  for="address">Ad Display Cost / Impressions in <span class="toggle_payment_type"><?php echo  (esc_html(get_option('payment_type')) != '') ? esc_html(get_option('payment_type')) : 'TN';?></span><span class="tooltiptext">Set the cost, in $<span class="toggle_payment_type"><?php echo  (esc_html(get_option('payment_type')) != '') ? esc_html(get_option('payment_type')) : 'TN';?></span>, per amount of Ad impressions displayed, for all Ad Segments, multiple ads will round-robin</span></label> </th>
				<td><input name="ad_cost" placeholder="" type="text" value="<?php _e(intval(get_option('ad_cost')));?>" class="regular-text" required> <span class="toggle_payment_type"><?php echo  (esc_html(get_option('payment_type')) != '') ? esc_html(get_option('payment_type')) : 'TN';?></span> Per <input name="ad_time" placeholder="" type="text" value="<?php _e(intval( get_option('ad_time')));?>" class="regular-text" required> Impressions</td>
			</tr>
			<tr>
				<th scope="row"><label class="tooltip"  for="address">Blacklist / Spam Management <span class="tooltiptext">Allow auto Ad blocking based on wallet addresses, words or expressions. In the comma separated format 'word1,address,word2'</span></label> </th>
				<td><textarea name="blacklist" style="width: 90%;height: 150px"><?php if( esc_html(get_option('blacklist')) == ''){echo "Enter Words, Expressions, Address with comma separated values to block each Words, Address & Expressions. For example : blue,X+5,3JeNEsZRANEcCPRf1B1AvnKq6G3WnFzUKHP";}else{ echo esc_html(get_option('blacklist'));} ;?></textarea></td>
			</tr>
			<tr>
				<th scope="row"><label class="tooltip" for="address">Ad Approval <span class="tooltiptext">Enable or Disable Ad approvals, if enabled, manually managed in Ad Approvals by approve or reject action</span></label></th>
				<td><label class="switch">
					  <input type="checkbox" name="ad_approval" value="1" <?php if(intval(get_option('ad_approval')) == 1){echo 'checked';}?>>
					  <span class="slider round"></span>
            </label>
				</td>
			</tr>
      <tr>
        <th scope="row"><label for="address">Note </label></th>
        <td>
            
              <ul>

                <li><b>Text Ad Format:</b> Ad (headline)(description)(url)</li>
                <li><b>Text Ad Example:</b> Ad (Turtle Ad Network)(Ad Network using the TurtleNetwork blockchain)(https://t.me/turtleadnetwork)</li>
                <li><b>Text Ad submission process:</b> Send a transaction on the Turtle Network, with an attachment in the above format, to an assigned address. An assigned address is configured/linked to an Ad Segment.</li>
                <li><b>Text Ad Note:</b> Maximum of 140 characters allowed, Headline text is bold with 35 character limit & URL is clickable in Ad. Utilize a URL shortener service to track analytics and shorten URL's</li>
              </ul>
           
          
        </td>
      </tr>
		</tbody>
	</table>
	<p class="submit">
		<input type="submit" name="tn_setting_submit" class="button button-primary" value="Save">
	</p>
</form>
<script>
 function getPaymentType(){
  var payment_type = document.getElementById("payment_type").value;
  for(var i=0;i<document.getElementsByClassName("toggle_payment_type").length;i++){
    document.getElementsByClassName("toggle_payment_type")[i].innerHTML =  payment_type.toUpperCase();
  }
 }
</script>