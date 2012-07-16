<?php
if ($fstconfig['AllTicketsPage'] == 1) {
	echo '<h2>All Tickets</h2>';
	
	echo '<p>';
	$Tickets = $wpdb->get_results("SELECT *, DATE_FORMAT(ticket_date_added, '%M %d %Y at %r') as DATEADDED FROM ".$wpdb->prefix."fst_tickets ORDER BY ticket_id DESC");
	
	foreach ($Tickets as $Tickets) {
		if ($Tickets->ticket_status == 'pendinguser') {
			$Status = $fstconfig['PendingUserText'];
		} elseif ($Tickets->ticket_status == 'resolved') {
			$Status = $fstconfig['ResolvedText'];
		} else {
			$Status = $fstconfig['PendingText'];
		}
		echo '<p><div class="fst-status">'.$Status.'</div><div class="fst-ticket"><strong><a href="'.get_option('home').'/tickets/'.$Tickets->ticket_url.'/">'.stripslashes($Tickets->ticket_title).'</a></strong><br /><i>Submitted by '.$wpdb->get_var("SELECT user_login FROM ".$wpdb->prefix."users WHERE ID = ".$Tickets->ID).' on '.$Tickets->DATEADDED.'</i></div></p>';
	}
	if (count($Tickets) == 0) { echo 'There are currently no support tickets.'; }
}
echo '</p>';
?>