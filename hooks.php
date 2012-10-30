<?php
// ADMIN MENU HOOK
add_action('admin_menu', 'fst_admin_pages');
function fst_admin_pages() {
	global $wpdb;
	add_menu_page('FS Tickets', 'FS Tickets', 10, __FILE__, 'fst_admin_home');
	add_submenu_page(__FILE__, 'Help & Support', 'Help & Support', 0, __FILE__, 'fst_admin_home');
	add_submenu_page(__FILE__, 'Manage Tickets', 'Manage Tickets', 8, 'fst-tickets', 'fst_tickets_page');
	add_submenu_page(__FILE__, 'Categories', 'Categories', 8, 'fst-categories', 'fst_admin_categories');
	add_submenu_page(__FILE__, 'Configuration', 'Configuration', 8, 'fst-admin-config', 'fst_admin_config');
}

add_action('admin_bar_menu', 'fst_admin_bar', 100);
function fst_admin_bar() {
	global $wp_admin_bar,$wpdb,$fstconfig,$user_ID;
	if ($user_ID == 1) {
		$Tickets = $wpdb->get_var("SELECT COUNT(ticket_status) FROM ".$wpdb->prefix."fst_tickets WHERE ticket_status = 'pending'");
		if($Tickets == '') { $Tickets = 0; }
		$wp_admin_bar->add_menu( array(
		'id' => 'fst_admin_bar',
		'title' => 'FireStorm Support Tickets',
		'href' => admin_url('admin.php?page=fst-tickets'),
		));
		$wp_admin_bar->add_menu( array(
		'parent' => 'fst_admin_bar',
		'id' => 'fst_admin_tickets',
		'title' => 'Ticket Management ('.$Tickets.')',
		'href' => admin_url('admin.php?page=fst-tickets'),
		));
		$wp_admin_bar->add_menu( array(
		'parent' => 'fst_admin_bar',
		'id' => 'fst_admin_categories',
		'title' => 'Categories',
		'href' => admin_url('admin.php?page=fst-categories'),
		));
		$wp_admin_bar->add_menu( array(
		'parent' => 'fst_admin_bar',
		'id' => 'fst_admin_config',
		'title' => 'Configuration',
		'href' => admin_url('admin.php?page=fst-config'),
		));
	}
}


// HEAD TAG HOOK (CSS, JS)
add_action('wp_head', 'fst_head');
function fst_head() {
	global $fstconfig,$wpdb,$FSTPages;	
	if (preg_match("#/".$FSTPages['TicketsURL']."/#i", $_SERVER['REQUEST_URI'])) {
		echo '<link rel="stylesheet" href="'.get_option('home').'/wp-content/plugins/fs-tickets/style.css" type="text/css" media="screen" />'." \n";
		$METADescription = '';
		$METAKeywords = '';
		$pageurl = explode("/".$FSSCPages['TicketURL']."/", $_SERVER['REQUEST_URI']);
		$pageurl[1] = str_replace("/", "", $pageurl[1]);
		$METADescription = $wpdb->get_var("SELECT ticket_meta_description FROM ".$wpdb->prefix."fst_tickets WHERE ticket_url = '".$pageurl[1]."'");
		$METAKeywords = $wpdb->get_var("SELECT ticket_meta_keywords FROM ".$wpdb->prefix."fst_tickets WHERE ticket_url = '".$pageurl[1]."'");
		echo "<meta name=\"description\" content=\"".$METADescription."\" /> \n";
		echo "<meta name=\"keywords\" content=\"".$METAKeywords."\" /> \n";
	}
}

?>