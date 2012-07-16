<?php
// FIND PAGE IDS
$FSTPages['Tickets'] = $wpdb->get_var("SELECT ID FROM ".$wpdb->prefix."posts WHERE post_content LIKE '%[fst-tickets]%' AND post_status = 'publish' LIMIT 1"); $FSTPages['TicketsURL'] = $wpdb->get_var("SELECT post_name FROM ".$wpdb->prefix."posts WHERE post_content LIKE '%[fst-tickets]%' AND post_status = 'publish' LIMIT 1"); 

// GET CONFIG VALUES
$sql = mysql_query("SELECT * FROM ".$wpdb->prefix."fst_config");
while($dbfstconfig = mysql_fetch_array($sql)) {
	$fstconfig[$dbfstconfig['config_name']] = $dbfstconfig['config_value'];
}

// PERMALINKS WARNING
$FSREPPermalinkStructure = $wpdb->get_var("SELECT option_value FROM ".$wpdb->prefix."options WHERE option_name = 'permalink_structure'");
if ($FSREPPermalinkStructure == '' && !isset($_POST['submit'])) {
	function fst_permalink_warning() {
		echo '<div class="updated fade"><p><strong>Support Ticket Plugin Error: </strong> Permalinks cannot be set to default for the plugin to function.</p></div>';
	}
	add_action('admin_notices', 'fst_permalink_warning');
	return;
}
?>
