<?php
wp_enqueue_script('thickbox', true);
wp_enqueue_style('thickbox');
add_filter('the_content', 'fst_content', 1);

if (preg_match("#/".$FSTPages['TicketsURL']."/#i", $_SERVER['REQUEST_URI'])) {
	add_filter('wp_title', 'fst_meta_title');
}

function fst_meta_title() {
	global $wpdb,$pageurl,$FSTPages;
	if (preg_match("#/".$FSTPages['TicketsURL']."/#i", $_SERVER['REQUEST_URI'])) {
		$pageurl = explode("/".$FSTPages['TicketsURL']."/", $_SERVER['REQUEST_URI']);
		$pageurl[1] = str_replace("/", "", $pageurl[1]);
		if (preg_match('/&/i', $pageurl[1])) {
			$pageurl = explode('?', $pageurl[1]);
			$pageurl[1] = $pageurl[0];
		}
		if (!$pageurl[1]) {
			return 'Support Tickets ';
		} elseif ($pageurl[1] == 'submit-ticket') {
			return 'Submit Support Ticket ';
		} elseif ($pageurl[1] == 'search') {
			return 'Search Support Tickets ';
		} elseif ($pageurl[1] == 'all') {
			return 'All Support Tickets ';
		} else {
			$ticket = stripslashes($wpdb->get_var("SELECT ticket_title FROM ".$wpdb->prefix."fst_tickets WHERE ticket_url = '".$pageurl[1]."'"));
			return $ticket.' - ';
		}
	}
}
function fst_content($content) {
	global $post,$wpdb,$wp_rewrite,$user_ID,$current_user,$fstconfig,$FSTPages,$pageurl;
	if (preg_match("#/".$FSTPages['TicketsURL']."/#i", $_SERVER['REQUEST_URI'])) {
		if ($post->ID == $FSTPages['Tickets']) {
			$pageurl = explode("/".$FSTPages['TicketsURL']."/", $_SERVER['REQUEST_URI']);
			$pageurl[1] = str_replace("/", "", $pageurl[1]);
			if (preg_match('/\?/i', $pageurl[1])) {
				$pageurl = explode('?', $pageurl[1]);
				$pageurl[1] = $pageurl[0];
			}
			if (!$pageurl[1]) {
				include("page-home.php");
			} elseif ($pageurl[1] == 'submit-ticket') {
				include("page-submit-ticket.php");
			} elseif ($pageurl[1] == 'all') {
				include("page-all-tickets.php");
			} elseif ($pageurl[1] == 'search') {
				if (isset($_POST['string'])) {
					$page_content = '<h1>Search Results</h1>';
					echo $page_content;
				}
			} else {
				$ticket_count = $wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."fst_tickets WHERE ticket_url = '".$pageurl[1]."'");
				if($ticket_count > 0) {
					$category_id = fst_grab_ticket_id();
					$ticket = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'fst_products WHERE products_url = "'.$pageurl[1].'"');
					$ticket_views = $ticket->ticket_views + 1;
					$wpdb->query("UPDATE ".$wpdb->prefix."fst_tickets SET ticket_views = ".$tickets_views." WHERE ticket_id = ".$ticket->ticket_id);
					include("page-ticket-details.php");
				} else {
					echo 'Error 404: Not Found.';
				}
			}
		}
	} else {
		include('text_filters.php');
		return($content);
	}
}
?>