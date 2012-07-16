<div id="fs-tickets">

<p><strong><a href="<?php echo get_option('home'); ?>/tickets/submit-ticket/">Submit Ticket</a></strong></p>

<?php /*
<p>Search <select name="search_category">
  <option value="1"=>All Tickets</option>
	<?php
    $Categories = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."fst_categories ORDER BY category_order");
    foreach ($Categories as $Categories) {
      echo '<option value="'.$Categories->category_id.'">'.$Categories->category_name.'</option>';
    }
  ?>  
</select> for <input type="input" name="string" style="width: 200px;" value="" /></p>
*/ ?>
<h2>Current Tickets</h2>

<p><?php
if ($user_ID == 0 && $fstconfig['RequireLogin'] == 1) {
	echo 'Please login to view your tickets.';
} else {
	if ($user_ID == 1) {
		$Tickets = $wpdb->get_results("SELECT *, DATE_FORMAT(ticket_date_added, '%M %d %Y at %r') as DATEADDED FROM ".$wpdb->prefix."fst_tickets ORDER BY ticket_id DESC");
	} else {
		$Tickets = $wpdb->get_results("SELECT *, DATE_FORMAT(ticket_date_added, '%M %d %Y at %r') as DATEADDED FROM ".$wpdb->prefix."fst_tickets WHERE ID = ".$user_ID." ORDER BY ticket_id DESC");
	}
	
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
?></p>

</div>