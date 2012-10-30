<?php
$TicketDetails = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."fst_tickets WHERE ticket_url = '".$pageurl[1]."'");

if (isset($_POST['submit'])) {
	$wpdb->query("UPDATE ".$wpdb->prefix."fst_tickets SET ticket_status = '".$_POST['ticket_status']."' WHERE ticket_id = ".$TicketDetails->ticket_id);
	$wpdb->query("UPDATE ".$wpdb->prefix."fst_tickets SET ticket_last_modified = NOW() WHERE ticket_id = ".$TicketDetails->ticket_id);
	if ($_POST['message_content'] != '') {
		$wpdb->query("INSERT INTO ".$wpdb->prefix."fst_tickets_messages (
																														ID, 
																														ticket_id, 
																														message_content, 
																														message_last_modified, 
																														message_date_added
																													 ) VALUES (
																														'".$user_ID."', 
																														'".$TicketDetails->ticket_id."', 
																														'".addslashes($_POST['message_content'])."', 
																														NOW(), 
																														NOW()
																													 )");
	}
	$TicketDetails = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."fst_tickets WHERE ticket_url = '".$pageurl[1]."'");
	$UserDetails = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."users WHERE ID = ".$user_ID);
	
	$headers  = 'MIME-Version: 1.0' . "\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
	$headers .= 'From: '.get_bloginfo('name').' <'.get_bloginfo('admin_email').'>';
	mail(get_bloginfo('admin_email'), 'Support Ticket Updated', 'A support ticket has been updated entitled "'.$TicketDetails->ticket_title.'". You can view the support ticket here: <a href="'.get_bloginfo('wpurl').'/'.$FSTPages['TicketsURL'].'/'.$TicketDetails->ticket_url.'/">'.get_bloginfo('wpurl').'/'.$FSTPages['TicketsURL'].'/'.$TicketDetails->ticket_url.'/</a>', $headers);
	mail($UserDetails->user_email, 'Your Support Ticket Has Been Updated', 'Your support ticket entitled "'.$TicketDetails->ticket_title.'" has been updated. You can view your support ticket here: <a href="'.get_bloginfo('wpurl').'/'.$FSTPages['TicketsURL'].'/'.$TicketDetails->ticket_url.'/">'.get_bloginfo('wpurl').'/'.$FSTPages['TicketsURL'].'/'.$TicketDetails->ticket_url.'/</a>', $headers);
}

if ($user_ID == $TicketDetails->ID || $user_ID == 1) {
	echo '<div id="fs-ticket">';
			
	echo '<p><strong>'.stripslashes($TicketDetails->ticket_title).'</strong></p>';
	$Messages = $wpdb->get_results("SELECT *, DATE_FORMAT(message_date_added, '%M %d %Y at %r') as DATEADDED FROM ".$wpdb->prefix."fst_tickets_messages WHERE ticket_id = ".$TicketDetails->ticket_id);
	foreach ($Messages as $Messages) {
		echo '<p>'.stripslashes($Messages->message_content).'<br /><i>Submitted by '.$UserDetails = $wpdb->get_var("SELECT user_login FROM ".$wpdb->prefix."users WHERE ID = ".$Messages->ID).' on '.$Messages->DATEADDED.'</i></p>';
	}
	
		echo '<p>';
		echo '<form name="ticket-reply" action="" method="POST">';
		echo '<strong>Set Status:</strong> <select name="ticket_status">';
		if ($user_ID == 1) {
			$Status = array('Require Additional Information' => 'pendinguser', $fstconfig['ResolvedText'] => 'resolved');
		} else {
			$Status = array('Not Resolved' => 'pending', $fstconfig['ResolvedText'] => 'resolved');
		}
		foreach ($Status as $Status => $Value) {
			$selected = ''; if ($Value == $TicketDetails->ticket_status) { $selected = ' selected'; }
			echo '<option value="'.$Value.'"'.$selected.'>'.$Status.'</option>';
		}
		echo '</select></p>';
		echo '<p><textarea name="message_content" value="" style="width: 500px; height: 150px;"></textarea>';
		echo '</p>';
		echo '<p><input type="submit" name="submit" class="button-primary" value="Submit Response" style="padding: 3px 8px;"></p></form>';
		echo '</p>';
	
	echo '<p style="font-style: italic;">External links and uploaded images are only available by the website administrator.</p>';
	
	echo '</div>';
}

?>