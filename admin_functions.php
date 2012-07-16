<?php
 function fst_admin_home() {
	echo '<div class="wrap">
<h2>FireStorm Support Ticket Plugin</h2>
<p>&nbsp;</p>
<h3>Version 1.0</h3>
<p>The latest version of the FireStorm Support Ticket Plugin can be downloaded from <a href="http://www.firestormplugins.com/plugins/support-tickets/" target="_blank">http://www.firestormplugins.com/plugins/support-tickets/</a>.</p>
<h3>Overview</h3>
<p>The FireStorm Support Ticket Plugin is an easy to use platform for managing support tickets from members and website visitors.</p>
<h3>Support and Customization</h3>
<p>For support and customization of the FireStorm Support Ticket Plugin, please <a href="http://www.firestormplugins.com/contact-us/" target="_blank">contact us</a>. You can also ask questions in our <a href="http://www.firestormplugins.com/forums/" target="_blank">support forums</a></p>
<h3>Developers</h3>
<p>The FireStorm Support Ticket Plugin is developed and maintained by <a href="http://www.firestorminteractive.com" target="_blank">FireStorm Interactive Inc.</a>. Specializing in WordPress Plugins, FireStorm Interactive creates custom solutions for any type of WordPress application. More information can be found at <a href="http://www.firestorminteractive.com" target="_blank">www.firestorminteractive.com</a>.</p>
</div>';
}

function fst_tickets_page() {
	global $wpdb,$fstconfig;

	if (!isset($_GET['f'])) {
		$TicketPage = 'pending';
	} else {
		$TicketPage = $_GET['f'];
	}

	if (isset($_POST['ticketid'])) {
		$wpdb->query("UPDATE ".$wpdb->prefix."fst_tickets SET ticket_status = 'resolved' WHERE ticket_id = ".$_POST['ticketid']);
	}
	
	if (isset($_POST['ticket_status'])) {
		$wpdb->query("UPDATE ".$wpdb->prefix."fst_tickets SET ticket_status = '".$_POST['ticket_status']."' WHERE ticket_id = ".$_POST['ticket_id']);
		$StatusDetails = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."fst_tickets WHERE ticket_id = ".$_POST['ticket_id']);
		if ($StatusDetails->ticket_number == 0) {
			$wpdb->query("UPDATE ".$wpdb->prefix."fst_tickets SET ticket_number = '".$fstconfig['TicketNumber']."' WHERE ticket_id = ".$_POST['ticket_id']);
			$fstconfig['TicketNumber'] = $fstconfig['TicketNumber'] + 1;
			$wpdb->query("UPDATE ".$wpdb->prefix."fst_config SET config_value = '".$fstconfig['TicketNumber']."' WHERE config_name = 'TicketNumber'");			
			$fstconfig['PONumber'] = $fstconfig['PONumber'] + 1;
			$wpdb->query("UPDATE ".$wpdb->prefix."fst_config SET config_value = '".$fstconfig['PONumber']."' WHERE config_name = 'PONumber'");
		}
	}
	
	if (isset($_GET['del'])) {
		$wpdb->query("DELETE FROM ".$wpdb->prefix."fst_tickets WHERE ticket_id = ".$_GET['del']);
	}

	echo '<div class="wrap">';
	echo '<form name="update-fst-tickets" action="#" method="POST">';
	echo '<h2>Ticket Management</h2>';
	
	if (isset($_GET['tid'])) {
		$TicketDetails = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."fst_tickets WHERE ticket_id = ".$_GET['tid']);
		
		if (isset($_POST['submit'])) {
			$wpdb->query("UPDATE ".$wpdb->prefix."fst_tickets SET ticket_status = '".$_POST['ticket_status']."'");
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
		}
		
		echo '<table class="widefat page fixed" cellspacing="0" bTicket="1">
			<thead>
			<tr>
			<th scope="col" class="manage-column"><strong>'.stripslashes($TicketDetails->ticket_title).'</strong></th>
			</tr>
			</thead>
			<tfoot>
			<tr>
			<th scope="col" class="manage-column"><strong>'.stripslashes($TicketDetails->ticket_title).'</strong></th>
			</tr>
			</tfoot>
			<tbody><tr><td>';
			echo '<h3 style="margin-bottom: 0px;">User Details</h3><p>';
			$UserDetails = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."users WHERE ID = ".$TicketDetails->ID);
			echo '<strong>Username:</strong> '.$UserDetails->user_login.'<br />';
			echo '<strong>Email:</strong> '.$UserDetails->user_email.'<br />';
			echo '<strong>Registered:</strong> '.$UserDetails->user_registered.'<br />';
			echo '<strong>Tickets:</strong> '.$wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."fst_tickets WHERE ID = ".$TicketDetails->ID).'';
			echo '</p>';
			echo '<h3 style="margin-bottom: 0px;">External Links</h3><p>';
			if ($TicketDetails->ticket_link1 != '') { echo '<a href="'.$TicketDetails->ticket_link1.'" target="_blank">'.$TicketDetails->ticket_link1.'</a><br />'; }
			if ($TicketDetails->ticket_link2 != '') { echo '<a href="'.$TicketDetails->ticket_link2.'" target="_blank">'.$TicketDetails->ticket_link2.'</a><br />'; }
			if ($TicketDetails->ticket_link3 != '') { echo '<a href="'.$TicketDetails->ticket_link3.'" target="_blank">'.$TicketDetails->ticket_link3.'</a>'; }
			echo '</p>';
			echo '<h3 style="margin-bottom: 0px;">Screenshots</h3><p>';
			for($i=1;$i<=5;$i++) {
				if (file_exists(ABSPATH.'wp-content/uploads/fstickets/'.$TicketDetails->ticket_id.'-'.$i.'.jpg')) { 
					echo '<a href="'.get_option('home').'/wp-content/uploads/fstickets/'.$TicketDetails->ticket_id.'-'.$i.'.jpg" target="_blank">'.get_option('home').'/wp-content/uploads/fstickets/'.$TicketDetails->ticket_id.'-'.$i.'.jpg</a><br />'; 
				}
			}
			echo '</p>';
			echo '<h3 style="margin-bottom: 0px;">'.stripslashes($TicketDetails->ticket_title).'</h3>';
			$Messages = $wpdb->get_results("SELECT *, DATE_FORMAT(message_date_added, '%M %d %Y at %r') as DATEADDED FROM ".$wpdb->prefix."fst_tickets_messages WHERE ticket_id = ".$TicketDetails->ticket_id);
			foreach ($Messages as $Messages) {
				echo '<p>'.stripslashes($Messages->message_content).'<br /><i>Submitted by '.$UserDetails = $wpdb->get_var("SELECT user_login FROM ".$wpdb->prefix."users WHERE ID = ".$Messages->ID).' on '.$Messages->DATEADDED.'</i></p>';
				
				
				
			}
			echo '<h3 style="margin-bottom: 0px;">Reply</h3><p>';
			echo '<form name="ticket-reply" action="admin.php?page=fst-tickets&tid='.$TicketDetails->ticket_id.'" method="POST">';
			echo '<strong>Set Status:</strong> <select name="ticket_status">';
			$Status = array($fstconfig['PendingText'] => 'pending', $fstconfig['PendingUserText'] => 'pendinguser', $fstconfig['ResolvedText'] => 'resolved');
			foreach ($Status as $Status => $Value) {
				$selected = ''; if ($Value == 'resolved') { $selected = ' selected'; }
				echo '<option value="'.$Value.'"'.$selected.'>'.$Status.'</option>';
			}
			echo '</select></p>';
			echo '<p><textarea name="message_content" value="" style="width: 500px; height: 150px;"></textarea>';
			echo '</p>';
			echo '<p><input type="submit" name="submit" class="button-primary" value="Submit Response" style="padding: 3px 8px;"></p></form>';
			echo '</p>';
			echo '</td></tr>
			</tbody></table><br />';
	}
	
	if ($TicketPage == 'pending') {
		$Tickets = $wpdb->get_results("SELECT *, DATE_FORMAT(ticket_date_added, '%M %d %Y - %r') as DATEADDED, DATE_FORMAT(ticket_last_modified, '%M %d %Y - %r') as LASTMODIFIED FROM ".$wpdb->prefix."fst_tickets WHERE ticket_status = 'pending' ORDER BY ticket_id DESC");
	} elseif ($TicketPage == 'pendinguser') {
		$Tickets = $wpdb->get_results("SELECT *, DATE_FORMAT(ticket_date_added, '%M %d %Y - %r') as DATEADDED, DATE_FORMAT(ticket_last_modified, '%M %d %Y - %r') as LASTMODIFIED FROM ".$wpdb->prefix."fst_tickets WHERE ticket_status = 'pendinguser' ORDER BY ticket_id DESC");
	} elseif ($TicketPage == 'resolved') {
		$Tickets = $wpdb->get_results("SELECT *, DATE_FORMAT(ticket_date_added, '%M %d %Y - %r') as DATEADDED, DATE_FORMAT(ticket_last_modified, '%M %d %Y - %r') as LASTMODIFIED FROM ".$wpdb->prefix."fst_tickets WHERE ticket_status = 'resolved' ORDER BY ticket_id DESC");
	} else {
		$Tickets = $wpdb->get_results("SELECT *, DATE_FORMAT(ticket_date_added, '%M %d %Y - %r') as DATEADDED, DATE_FORMAT(ticket_last_modified, '%M %d %Y - %r') as LASTMODIFIED FROM ".$wpdb->prefix."fst_tickets ORDER BY ticket_id DESC");
	}
	
	echo '<div class="nav-tabs-nav">';
	echo '<div class="nav-tabs-wrapper">';
	echo '<div class="nav-tabs">';
	echo '<span class="nav-tab'; if ($TicketPage == 'pending') { echo ' nav-tab-active" style="background-color: #fafafa; bTicket-bottom: none;'; } echo '"><a href="admin.php?page=fst-tickets&f=pending" style="text-decoration: none; color: #333333;'; if ($TicketPage == 'pending') { echo ' font-weight: bold;'; } echo '">'.$fstconfig['PendingText'].'</a></span>';
	echo '<span class="nav-tab'; if ($TicketPage == 'pendinguser') { echo ' nav-tab-active" style="background-color: #fafafa; bTicket-bottom: none;'; } echo '"><a href="admin.php?page=fst-tickets&f=pendinguser" style="text-decoration: none; color: #333333;'; if ($TicketPage == 'pendinguser') { echo ' font-weight: bold;'; } echo '">'.$fstconfig['PendingUserText'].'</a></span>';
	echo '<span class="nav-tab'; if ($TicketPage == 'resolved') { echo ' nav-tab-active" style="background-color: #fafafa; bTicket-bottom: none;'; } echo '"><a href="admin.php?page=fst-tickets&f=resolved" style="text-decoration: none; color: #333333;'; if ($TicketPage == 'resolved') { echo ' font-weight: bold;'; } echo '">'.$fstconfig['ResolvedText'].'</a></span>';
	echo '<span class="nav-tab'; if ($TicketPage == 'all') { echo ' nav-tab-active" style="background-color: #fafafa; bTicket-bottom: none;'; } echo '"><a href="admin.php?page=fst-tickets&f=all" style="text-decoration: none; color: #333333;'; if ($TicketPage == 'all') { echo ' font-weight: bold;'; } echo '">All</a></span>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '<table class="widefat page fixed" cellspacing="0" bTicket="1">
		<thead>
		<tr>
		<th scope="col" class="manage-column" width="25">&nbsp;</th>
		<th scope="col" class="manage-column" width="160">Status</th>
		<th scope="col" class="manage-column" width="210">Request Date</th>
		<th scope="col" class="manage-column">Ticket Overview</th>
		</tr>
		</thead>
		<tfoot>
		<tr>
		<th scope="col" class="manage-column" width="25">&nbsp;</th>
		<th scope="col" class="manage-column" width="160">Status</th>
		<th scope="col" class="manage-column" width="210">Request Date</th>
		<th scope="col" class="manage-column">Ticket Overview</th>
		</tr>
		</tfoot>
			<tbody>';
		foreach ($Tickets as $Tickets) {
			echo '<tr>';
			echo '<td><a href="admin.php?page=fst-tickets&del='.$Tickets->ticket_id.'" onClick="return confirm(\'Are you sure you want to remove this ticket?\')"><img src="'.get_option('home').'/wp-content/plugins/fs-tickets/images/x.png" bTicket="0" alt="X"></a></td>';
			echo '<td><form name="updating-ticket-status" action="#" method="POST"><select name="ticket_status" onchange="this.form.submit()">';
			$Status = array($fstconfig['PendingText'] => 'pending', $fstconfig['PendingUserText'] => 'pendinguser', $fstconfig['ResolvedText'] => 'resolved');
			
			foreach ($Status as $Status => $Value) {
				$selected = ''; if ($Tickets->ticket_status == $Value) { $selected = ' selected'; }
				echo '<option value="'.$Value.'"'.$selected.'>'.$Status.'</option>';
			}
			echo '</select><input type="hidden" name="ticket_id" value="'.$Tickets->ticket_id.'"></form></td>';
			echo '<td>'.$Tickets->DATEADDED.'</td>';
			echo '<td>'.$Tickets->ticket_title.'<br />';
			echo '<a href="admin.php?page=fst-tickets&tid='.$Tickets->ticket_id.'">View Ticket</a> | <a href="admin.php?page=fst-tickets&del='.$Tickets->ticket_id.'" onClick="confirm(\'Are you sure you want to delete this ticket?\');">Delete Ticket</a>';
			echo '</td>';
			echo '</tr>';
		}
	echo '</tbody></table>';
	echo '</div>';
}

function fst_admin_config() {
	global $fstconfig,$wpdb;
	
	if (isset($_POST['submit'])) {
		$wpdb->query("UPDATE ".$wpdb->prefix."fst_config SET config_value = '".addslashes($_POST['RequireLogin'])."' WHERE config_name = 'RequireLogin'");
		$wpdb->query("UPDATE ".$wpdb->prefix."fst_config SET config_value = '".addslashes($_POST['AllowMemberReplies'])."' WHERE config_name = 'AllowMemberReplies'");
		$wpdb->query("UPDATE ".$wpdb->prefix."fst_config SET config_value = '".addslashes($_POST['PendingText'])."' WHERE config_name = 'PendingText'");
		$wpdb->query("UPDATE ".$wpdb->prefix."fst_config SET config_value = '".addslashes($_POST['PendingUserText'])."' WHERE config_name = 'PendingUserText'");
		$wpdb->query("UPDATE ".$wpdb->prefix."fst_config SET config_value = '".addslashes($_POST['ResolvedText'])."' WHERE config_name = 'ResolvedText'");
		$wpdb->query("UPDATE ".$wpdb->prefix."fst_config SET config_value = '".addslashes($_POST['CategoryLabel'])."' WHERE config_name = 'CategoryLabel'");
		$wpdb->query("UPDATE ".$wpdb->prefix."fst_config SET config_value = '".addslashes($_POST['CategoryLabelPlural'])."' WHERE config_name = 'CategoryLabelPlural'");
		$wpdb->query("UPDATE ".$wpdb->prefix."fst_config SET config_value = '".addslashes($_POST['AllTicketsPage'])."' WHERE config_name = 'AllTicketsPage'");

		$sql = mysql_query("SELECT * FROM ".$wpdb->prefix."fst_config");
		while($dbfstconfig = mysql_fetch_array($sql)) {
			$fstconfig[$dbfstconfig['config_name']] = $dbfstconfig['config_value'];
		}
	}

	echo '<div class="wrap">';
	echo '<form name="update-fst-config" action="#" method="POST">';
	echo '<h2>FireStorm Support Ticket Configuration</h2>';
	echo '<table class="widefat page fixed" cellspacing="0" border="1">
		<thead>
		<tr>
		<th scope="col" class="manage-column" width="200"><b>General Settings</b></th>
		<th scope="col" class="manage-column" width="250">&nbsp;</th>
		<th scope="col" class="manage-column"><input type="submit" name="submit" class="button-primary" value="Update" style="padding: 3px 8px;"></th>
		</tr>
		</thead>
		<tfoot>
		<tr>
		<th scope="col" class="manage-column" width="200"><b>General Settings</b></th>
		<th scope="col" class="manage-column" width="250">&nbsp;</th>
		<th scope="col" class="manage-column"><input type="submit" name="submit" class="button-primary" value="Update" style="padding: 3px 8px;"></th>
		</tr>
		</tfoot>
		<tbody>';
	fst_print_admin_selectbox('Require Login', 'RequireLogin', $fstconfig['RequireLogin'], array('Yes' => 1, 'No' => 0), '');
	fst_print_admin_selectbox('Allow Other Member Replies', 'AllowMemberReplies', $fstconfig['AllowMemberReplies'], array('Yes' => 1, 'No' => 0), '');
	fst_print_admin_selectbox('Allow All Tickets Page', 'AllTicketsPage', $fstconfig['AllTicketsPage'], array('Yes' => 1, 'No' => 0), '');
	fst_print_admin_input('Pending Text', 'PendingText', $fstconfig['PendingText'], 30, '');
	fst_print_admin_input('Pending UserText', 'PendingUserText', $fstconfig['PendingUserText'], 30, '');
	fst_print_admin_input('Resolved Text', 'ResolvedText', $fstconfig['ResolvedText'], 30, '');
	fst_print_admin_input('Category Label', 'CategoryLabel', $fstconfig['CategoryLabel'], 30, '');
	fst_print_admin_input('Category Label (Plural)', 'CategoryLabelPlural', $fstconfig['CategoryLabelPlural'], 30, '');
	echo '</tbody></table>';
	echo '</form>';
	echo '</div>';
}

function fst_admin_categories() {
	global $fstconfig,$wpdb;

	if ($_GET['f'] == "down" || $_GET['f'] == "up") {
		$NewID = $_GET['id'];
		$NewCategoryInfo = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."fst_categories WHERE category_id = $NewID");
		$OldOrder = $NewCategoryInfo->category_order;
		if ($_GET['f'] == "down") { 
			$NewOrder = $NewCategoryInfo->category_order + 1;
		} else {
			$NewOrder = $NewCategoryInfo->category_order - 1;
		}
		$OldCategoryOrder = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."fst_categories WHERE category_order = $NewOrder AND parent_id = ".$_GET['pid']);
		if (count($OldCategoryOrder) > 0) {
			$OldID = $OldCategoryOrder->category_id;
			$wpdb->query("UPDATE ".$wpdb->prefix."fst_categories SET category_order = $NewOrder WHERE category_id = $NewID");
			$wpdb->query("UPDATE ".$wpdb->prefix."fst_categories SET category_order = $OldOrder WHERE category_id = $OldID");
		}
	} elseif ($_GET['f'] == "del" && $_GET['id'] != ""){
		$wpdb->query("DELETE FROM ".$wpdb->prefix."fst_categories WHERE category_id = ".$_GET['id']);
		$wpdb->query("DELETE FROM ".$wpdb->prefix."fst_categories WHERE parent_id = ".$_GET['id']);
		$wpdb->query("DELETE FROM ".$wpdb->prefix."fst_tickets_to_categories WHERE category_id = ".$_GET['id']);
		$count = 0;
		$UpdateCategoryOrdering = $wpdb->get_results("SELECT category_id, parent_id, category_order FROM ".$wpdb->prefix."fst_categories WHERE parent_id = ".$_GET['pid']." ORDER BY category_order");
		foreach ($UpdateCategoryOrdering as $UpdateCategoryOrdering) {
			$count++;
			$wpdb->query("UPDATE ".$wpdb->prefix."fst_categories SET category_order = $count WHERE category_id = ".$UpdateCategoryOrdering->category_id);
		}
	}
		
	if (isset($_POST['submit'])) {
		$_POST['category_url'] = fst_url_generator($_POST['category_name']);
		$URLCheck = $wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."fst_categories WHERE category_url = '".$_POST['category_url']."'");
		if ($URLCheck > 0) {
			for ($i=1;$i<99;$i++) {
				$newurl = $_POST['ticket_url'].$i;
				$NewURLCheck = $wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."fst_categories WHERE category_url = '$newurl'");
				if ($NewURLCheck == 0) {
					$_POST['category_url'] = $newurl;
					$i = 100;
				}
			}
		}
		$_POST['category_order'] = $wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."fst_categories WHERE parent_id = ".$_POST['parent_id']) + 1;
		$wpdb->query("INSERT INTO ".$wpdb->prefix."fst_categories (category_name, category_url, parent_id, category_order) VALUES ('".$_POST['category_name']."', '".$_POST['category_url']."', '".$_POST['parent_id']."', '".$_POST['category_order']."')");
	}
	
	echo '<div class="wrap"><form name="fst_categories" method="POST" action="admin.php?page=fst-categories">';
	echo '<h2>Categories <a href="admin.php?page=fst-categories&f=add" class="add-new-h2">Add New</a> </h2> ';
	echo '<table class="widefat page fixed" cellspacing="0">
		<thead>
		<tr>
		<th scope="col" id="title" class="manage-column" style="width: 200px;">Add Category</th>
		<th scope="col" id="title" class="manage-column" style=""><input type="submit" name="submit" class="button-primary" value="Add Category" style="padding: 3px 8px;"></th>
		</tr>
		</thead>
		<tbody>
		<td><input type="text" name="category_name" value="" size="20"></td>
		<td><select name="parent_id" /><option value="0">Parent Category</option>';
    fst_category_options (0, 0, 0, '');
		echo '</select></td></tr>';
	echo '</tbody></table>';
	echo '<table class="widefat page fixed" cellspacing="0">
		<thead>
		<tr>
		<th scope="col" id="date" class="manage-column" style="width: 50px;">&nbsp;</th>
		<th scope="col" id="title" class="manage-column" style="">Categories</th>
		</tr>
		</thead>
		<tfoot>
		<tr>
		<th scope="col" class="manage-column" style="width: 50px;">&nbsp;</th>
		<th scope="col" class="manage-column" style="">Categories</th>
		</tr>
		</tfoot>
		<tbody>';
		fst_categories (0, 0);
	echo '</tbody></table>';
	echo '</form></div>';
}
?>
