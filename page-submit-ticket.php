<?php
global $user_ID;

	if (isset($_POST['submit'])) {
		if (
				$_POST['ticket_category'] == '' ||
				$_POST['ticket_title'] == '' ||
				$_POST['ticket_content'] == ''
			 ) { 
			echo '<p>Please provide all required information.</p>';
		} else {
			$_POST['ticket_url'] = fst_url_generator($_POST['ticket_title']);
			$_POST['ticket_category'] = addslashes(strip_tags($_POST['ticket_category']));
			$_POST['ticket_title'] = addslashes(strip_tags($_POST['ticket_title']));
			$_POST['ticket_content'] = addslashes(strip_tags($_POST['ticket_content']));
			$_POST['ticket_link1'] = strip_tags($_POST['ticket_link1']);
			$_POST['ticket_link2'] = strip_tags($_POST['ticket_link2']);
			$_POST['ticket_link3'] = strip_tags($_POST['ticket_link3']);
			$_POST['ticket_file1'] = strip_tags($_POST['ticket_file1']);
			$_POST['ticket_file2'] = strip_tags($_POST['ticket_file2']);
			$_POST['ticket_file3'] = strip_tags($_POST['ticket_file3']);
			$_POST['ticket_file4'] = strip_tags($_POST['ticket_file4']);
			$_POST['ticket_file5'] = strip_tags($_POST['ticket_file5']);
			
			$_POST['products_id'] = 0;
			$_POST['customer_ip'] = $_SERVER['REMOTE_ADDR'];
			$URLCheck = $wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."fst_tickets WHERE ticket_url = '".$_POST['ticket_url']."'");
			if ($URLCheck > 0) {
				for ($i=1;$i<99;$i++) {
					$newurl = $_POST['ticket_url'].$i;
					$NewURLCheck = $wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."fst_tickets WHERE ticket_url = '$newurl'");
					if ($NewURLCheck == 0) {
						$_POST['ticket_url'] = $newurl;
						$i = 100;
					}
				}
			}

			
			$wpdb->query("INSERT INTO ".$wpdb->prefix."fst_tickets (
																														  ID, 
																															ticket_url, 
																															ticket_title, 
																															ticket_visibility, 
																															ticket_status, 
																															customer_ip, 
																															products_id, 
																															ticket_link1, 
																															ticket_link2, 
																															ticket_link3, 
																															ticket_views, 
																															ticket_last_modified, 
																															ticket_date_added
																														 ) VALUES (
																														  '".$user_ID."', 
																														  '".$_POST['ticket_url']."', 
																															'".$_POST['ticket_title']."', 
																															'1', 
																															'pending', 
																															'".$_POST['customer_ip']."', 
																															'".$_POST['products_id']."', 
																															'".$_POST['ticket_link1']."', 
																															'".$_POST['ticket_link2']."', 
																															'".$_POST['ticket_link3']."', 
																															0, 
																															NOW(), 
																															NOW()
																														 )");
																														 
			$TicketID = $wpdb->get_var("SELECT ticket_id FROM ".$wpdb->prefix."fst_tickets ORDER BY ticket_id DESC LIMIT 1");
			
			$wpdb->query("INSERT INTO ".$wpdb->prefix."fst_tickets_messages (
																														  ID, 
																															ticket_id, 
																															message_content, 
																															message_last_modified, 
																															message_date_added
																														 ) VALUES (
																														  '".$user_ID."', 
																														  '".$TicketID."', 
																															'".$_POST['ticket_content']."', 
																															NOW(), 
																															NOW()
																														 )");

			for($i=1;$i<=5;$i++) {
				if ($_FILES['ticket_file'.$i] != "") {
					if (file_exists(ABSPATH.'wp-content/uploads/fstickets/'.$TicketID.'-'.$i.'.jpg')) { unlink(ABSPATH.'wp-content/uploads/fstickets/'.$TicketID.'-'.$i.'.jpg'); }
					$uploaddir = ABSPATH.'wp-content/uploads/fstickets/';
					$uploadfile = $uploaddir . basename($_FILES['ticket_file'.$i]['name']);
					if (move_uploaded_file($_FILES['ticket_file'.$i]['tmp_name'], $uploadfile)) {
						rename(ABSPATH.'wp-content/uploads/fstickets/'.basename($_FILES['ticket_file'.$i]['name']), ABSPATH.'wp-content/uploads/fstickets/'.$TicketID.'-'.$i.'.jpg');
					}
				}
			}

			echo '<p><strong>Your support ticket has been created.</strong></p>';
		}
	}
?>

<div id="fs-tickets">

<?php if ($user_ID == 0 && $fstconfig['RequireLogin'] == 1) { echo '<p>You must be logged in to submit a ticket.</p>'; } else { ?>

<p class="required">* Required Field</p>

<form action="" method="POST" name="submit-ticket" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />

<p><label name="ticket_category"><?php echo $fstconfig['CategoryLabel']; ?>*</label><select name="ticket_category" id="ticket_category" />
	<?php fst_category_options (0, 0, 0, ''); ?>  
</select></p>

<p><label name="ticket_title">Ticket Title*</label><input type="text" name="ticket_title" value="" style="width: 250px;" /></p>

<p><label name="ticket_content">Issue Description*</label><textarea name="ticket_content" value="" style="width: 250px; height: 150px;"></textarea></p>

<p>
<label name="ticket_link1">External Link</label><input type="text" name="ticket_link1" value="" style="width: 250px;" /><br />
<label name="ticket_link2">External Link</label><input type="text" name="ticket_link2" value="" style="width: 250px;" /><br />
<label name="ticket_link3">External Link</label><input type="text" name="ticket_link3" value="" style="width: 250px;" />
</p>

<p>
<label name="ticket_file1">Upload Image</label><input type="file" name="ticket_file1" value="" style="width: 250px;" /><br />
<label name="ticket_file2">Upload Image</label><input type="file" name="ticket_file2" value="" style="width: 250px;" /><br />
<label name="ticket_file3">Upload Image</label><input type="file" name="ticket_file3" value="" style="width: 250px;" /><br />
<label name="ticket_file4">Upload Image</label><input type="file" name="ticket_file4" value="" style="width: 250px;" /><br />
<label name="ticket_file5">Upload Image</label><input type="file" name="ticket_file5" value="" style="width: 250px;" />
</p>

<input type="submit" name="submit" value="Submit Ticket" />

</form>

<?php } ?>

</div>