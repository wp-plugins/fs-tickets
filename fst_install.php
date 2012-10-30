<?php

function fst_install() {
	global $wpdb,$fst_version;
	require_once(ABSPATH.'wp-admin/includes/upgrade.php');
	
	// ADD PAGES
	$Pages = array(
		'Tickets' => '[fst-tickets]'
	);
	fst_add_pages($Pages);
	
	$table_name = $wpdb->prefix."fst_config";
	if($wpdb->get_var("show tables like '".$table_name."'") != $table_name) {
		
		// ADD DB STRUCTURE
		include('install_sql.php');
		
		// ADD FSREP VERSION
		add_option("fst_db_version", $fst_version);
	} else {
		$installed_ver = get_option( "fst_db_version" );
		if( $installed_ver != $fst_version ) {
			// UPDATE DB STRUCTURE
			include('install_sql.php');
			
			// UPDATE FSREP VERSION
			update_option( "fst_db_version", $fst_version );
		}
	}
	
	// CREATE DIRECTORIES
	if (!file_exists(ABSPATH."wp-content/uploads")) { mkdir(ABSPATH."wp-content/uploads", 0777); }
	if (!file_exists(ABSPATH."wp-content/uploads/fstickets")) { mkdir(ABSPATH."wp-content/uploads/fstickets", 0777); }
}
?>