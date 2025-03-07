<?php

if ( ! defined('ABSPATH') ) {
    die;
}

// get admin url for current page using slug
$admin_url = menu_page_url( 'tan-approve-ad',false); 
?>

<h3>Ad Manager</h3>
<?php
global $wpdb;

$t1 = $wpdb->prefix.'tan_ad_segment_details';
$t2 = $wpdb->prefix.'tan_adsegment';
$t3 = $wpdb->prefix.'tan_ads_size';
$t4 = $wpdb->prefix.'tan_wallet_address';

if(isset($_GET['id']) && isset($_GET['action'])){
    if(wp_verify_nonce($_GET['turtle-network-approve-nonce'], 'turtle-network-approve-action') && current_user_can('administrator')){
        if(intval($_GET['action']) == 1){ // approved

            $ID = intval($_GET['id']);
            $time = $wpdb->get_var("SELECT time_period FROM $t1 WHERE ID = $ID");

            $start_time = strtotime(date("Y-m-d h:i:sa"));
            $add_time = "+" . $time." minutes ";           
            $end_time = date('Y-m-d h:i:sa', strtotime($add_time, $start_time));
            $wpdb->update($t1,array('approve_status' => 1), array('ID' => intval($_GET['id'])));
    ?>
            <script>
                var url = window.location.href;
                var urll = url.split('&');
                window.location.href = urll[0]+'&msg=1';
            </script>
    <?php       
        }else if(intval($_GET['action']) == 3){ // stop
            $wpdb->update($t1,array('display_status' => 2), array('ID' => intval($_GET['id'])));
    ?>
            <script>
                var url = window.location.href;
                var urll = url.split('&');
                window.location.href = urll[0]+'&msg=3';
            </script>
    <?php 
        }else if(intval($_GET['action']) == 2){ // start
             $wpdb->update($t1,array('display_status' => 1), array('ID' => intval($_GET['id'])));
    ?>
            <script>
                var url = window.location.href;
                var urll = url.split('&');
                window.location.href = urll[0]+'&msg=4';
            </script>
    <?php 
        }else{ // rejected
         $wpdb->update($t1,array('approve_status' => 2), array('ID' => intval($_GET['id'])));
    ?>
            <script>
                var url = window.location.href;
                var urll = url.split('&');
                window.location.href = urll[0]+'&msg=2';
            </script>
    <?php 
        }
        
    }else{
        _e("Sorry, you dont have permission to perform this action.");
    }
}

$datas = $wpdb->get_results("SELECT * FROM $t1 INNER JOIN $t2 ON $t1.seg_id=$t2.id INNER JOIN $t3 ON $t2.size_id=$t3.id");

if(isset($_GET['msg']) && intval($_GET['msg']) == 1){
    _e("Approved Successfully!!");
}else if(isset($_GET['msg']) && intval($_GET['msg']) == 2){
    _e("Rejected Successfully!!");
}else if(isset($_GET['msg']) && intval($_GET['msg']) == 3){
    _e("Stop Successfully!!");
}else if(isset($_GET['msg']) && intval($_GET['msg']) == 4){
    _e("Started Successfully!!");
}

?>

<table class="widefat fixed" cellspacing="0">
    <thead>
    <tr>

            <th id="cb" class="manage-column column-columnname" scope="col"  style="width: 10px;">#</th> 
            <th id="columnname" class="manage-column column-columnname" scope="col">Ad Segment Name</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Headline</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Description</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Clickable Link</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Txid</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Impressions Purchased</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Assigned Address</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Assigned Size</th> 
            <th id="columnname" class="manage-column column-columnname" scope="col">Shortcode</th> 
            <th id="columnname" class="manage-column column-columnname" scope="col">Start Date</th> 
            <th id="columnname" class="manage-column column-columnname" scope="col">Current Impressions</th> 
           
            <th id="columnname" class="manage-column column-columnname" scope="col">Action</th> 
          
    </tr>
    </thead>

    <tfoot>
    <tr>
            
            <th id="cb" class="manage-column column-columnname" scope="col"  style="width: 10px;">#</th> 
            <th id="columnname1" class="manage-column column-columnname" scope="col">Ad Segment Name</th>
            <th id="columnname2" class="manage-column column-columnname" scope="col">Headline</th>
            <th id="columnname3" class="manage-column column-columnname" scope="col">Description</th>
            <th id="columnname4" class="manage-column column-columnname" scope="col">Clickable Link</th>
            <th id="columnname5" class="manage-column column-columnname" scope="col">Txid</th>
            <th id="columnname6" class="manage-column column-columnname" scope="col">Impressions Purchased</th>
            <th id="columnname7" class="manage-column column-columnname" scope="col">Assigned Address</th>
            <th id="columnname8" class="manage-column column-columnname" scope="col">Assigned Size</th> 
            <th id="columnname9" class="manage-column column-columnname" scope="col">Shortcode</th> 
            <th id="columnname" class="manage-column column-columnname" scope="col">Start Date</th> 
            <th id="columnname" class="manage-column column-columnname" scope="col">Current Impressions</th>
            
            <th id="columnname" class="manage-column column-columnname" scope="col">Action</th> 
        
    </tr>
    </tfoot>

    <tbody>       
    <?php 
    
        $data = $wpdb->get_results("SELECT * FROM $t1 INNER JOIN $t2 ON $t1.seg_id=$t2.id INNER JOIN $t3 ON $t2.size_id=$t3.id ORDER BY $t1.ID DESC");
        $x=1;
        if(empty($data)){_e('No data yet!!');}  
        foreach ($data as $value) {

            $data_size = $value->width." X ". $value->height ." - " .$value->name;
            $address_table_name = $wpdb->prefix.'tan_wallet_address';
            $ad_id = intval($value->address_id);
            $all_addresss =  $wpdb->get_results("SELECT * FROM $t4 WHERE id = $ad_id");
            $addd = $all_addresss[0]->address;

            $ID = intval($value->ID);
            $start_timee = date('Y-m-d h:i:s a',$value->time);
            $end_timee = $wpdb->get_var("SELECT status FROM $t1 WHERE ID = $ID");


    ?>
        
        <tr class="alternate" valign="top"> 
            <th class="check-column" scope="row"><?php echo intval($value->ID);?></th>
            <td class="column-columnname"><?php echo esc_html($value->ad_segment_name);?></td>
            <td class="column-columnname"><?php echo esc_html($value->headline);?></td>
            <td class="column-columnname"><?php echo esc_html($value->des);?></td>
            <td class="column-columnname"><?php echo esc_url($value->clickable);?></td>
            <td class="column-columnname">
                <a href="https://explorer.turtlenetwork.eu/tx/<?php echo $value->txid;?>" target="_blank">
                    <?php echo esc_html($value->txid);?>
                </a>
            </td>
            <td class="column-columnname"><?php echo floatval($value->time_period);?></td>
            <td class="column-columnname"><?php echo esc_html($addd);?></td>
            <td class="column-columnname"><?php echo esc_html($data_size);?></td>
            <td class="column-columnname"><?php echo esc_html("[TAN-" . $value->ad_segment_name . "]");?></td>
            <td class="column-columnname"><?php echo esc_html($start_timee);?></td>
            <td class="column-columnname"><?php echo floatval($end_timee);?></td>
            
            <td class="column-columnname">
                <?php if($value->time_period != $end_timee){?>
                <?php if(intval(get_option('ad_approval')) == 1){?>
                <?php if(intval($value->approve_status) == 1){?>
                        
                    <button type="button" class="btn btn-primary" style="background-color: green;color:#fff">
                        Approved
                    </button> 

                    
                <?php

                }else if(intval($value->approve_status) == 2){?>
                    
                    <button type="button" class="btn btn-primary" style="background-color: red;color:#fff">
                        Rejected
                    </button>


                <?php }else{ ?>

                    <a href="<?php print wp_nonce_url($admin_url.'&action=1&id='.intval($value->ID), 'turtle-network-approve-action', 'turtle-network-approve-nonce'); ?>">Approve</a> |
                   <a href="<?php print wp_nonce_url($admin_url.'&action=0&id='.intval($value->ID), 'turtle-network-approve-action', 'turtle-network-approve-nonce'); ?>">Reject</a> 

                <?php } } if(intval($value->display_status) == 2){ //stop  ?> 

                    <button type="button" class="btn btn-primary" style="background-color: red;color:#fff">
                        Stopped
                    </button> 
                    <a href="<?php print wp_nonce_url($admin_url.'&action=2&id='.intval($value->ID), 'turtle-network-approve-action', 'turtle-network-approve-nonce'); ?>">Start</a>

                <?php }elseif(intval($value->display_status) == 1){ //start ?>

                    <button type="button" class="btn btn-primary" style="background-color: green;color:#fff">
                        Started
                    </button> 
                    <a href="<?php print wp_nonce_url($admin_url.'&action=3&id='.intval($value->ID), 'turtle-network-approve-action', 'turtle-network-approve-nonce'); ?>">Stop</a>
                    
                <?php }else{ ?>
                    <!-- <a href="<?php print wp_nonce_url($admin_url.'&action=2&id='.intval($value->ID), 'turtle-network-approve-action', 'turtle-network-approve-nonce'); ?>">Start</a> | -->
                    <a href="<?php print wp_nonce_url($admin_url.'&action=3&id='.intval($value->ID), 'turtle-network-approve-action', 'turtle-network-approve-nonce'); ?>">Stop</a>
                <?php } }else{ ?>

                     <button type="button" class="btn btn-primary" style="background-color: green;color:#fff">
                        Completed
                    </button> 
                <?php } ?>
            </td>
        

        </tr>
     
    <?php $x++;} ?>
    </tbody>
</table>