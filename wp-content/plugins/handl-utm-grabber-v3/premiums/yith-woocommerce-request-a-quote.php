<?php
function handl_ywraq_order_meta_list($attr, $order_id, $raq){
	HandLUTMGrabberWooCommerceUpdateOrderMeta($order_id);
}
add_filter('ywraq_order_meta_list', 'handl_ywraq_order_meta_list', 10, 3);