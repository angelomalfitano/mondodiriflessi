<?php

class SLN_Action_Ajax_UpdateUser extends SLN_Action_Ajax_Abstract
{
    public function execute()
    {
       if(!current_user_can( 'manage_salon' )) throw new Exception('not allowed');
       $result = $this->getResult(sanitize_text_field(wp_unslash( $_POST['s'] )));
       if(!$result){
           $ret = array(
               'success' => 0,
               'errors' => array(__('User not found','salon-booking-system'))
           );
       }else{
		  
					global $wpdb;
					$serach_email =  $result['email'];
					 $queryt = "SELECT post_id FROM ".$wpdb->prefix."postmeta where meta_key='_sln_booking_email' and meta_value='$serach_email' order by post_id desc limit 3";
					$all_post_id = $wpdb->get_results($queryt,'ARRAY_A');
					$all_service_array = array();
					$all_attendant_array = array();
					foreach($all_post_id as $service_post_id){
						$get_services_id = get_post_meta( $service_post_id['post_id'], '_sln_booking_services',true ); 
						
						foreach($get_services_id as $single_service_id){
							$all_service_array[] = $single_service_id['service'];
							if($single_service_id['attendant'] >0){
							$all_attendant_array[$single_service_id['service']] = $single_service_id['attendant'];
							}else{
								$all_attendant_array[$single_service_id['service']] = 0;
							}
						}
						
					}
					$all_service_array = array_unique($all_service_array);
					$prev_service_html = '';
					$max_cnt = 1;
					foreach($all_service_array as $prev_service){
						if($max_cnt <=3){
						$servce_data = "'".$prev_service."','".$all_attendant_array[$prev_service]."'";
						$prev_service_html .= '<li><span style="cursor:pointer;" onclick="prev_services('.$servce_data.')">'.$_SESSION['all_services_data'][$prev_service].'</span></li>';
						$max_cnt++;
						}
						//$prev_service_html .= '<li onclick="open_wp_pop_by_lid(\''.$_SESSION[all_services_data][$prev_service].'\')"</li>';
					 }
           $ret = array(
               'success' => 1,
               'result' => $result,
			   'prev_service_html' => $prev_service_html, 
               'message' => __('User updated','salon-booking-system')
           );
       }
       return $ret;
    }
    private function getResult($id)
    {
        $number = 1;
        $u = new WP_User($id);
        if(!$u) return;
        $values = [ 'id' => $u->ID, ];
        foreach (SLN_Enum_CheckoutFields::forBookingAndCustomer()->appendSmsPrefix() as $key => $field){
            $values[$key] = $field->getValue($u->ID);
        }
        return $values;
    }
}
