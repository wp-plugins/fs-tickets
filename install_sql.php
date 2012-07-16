<?php

$FSTTableName = $wpdb->prefix."fst_config";
$wpdb->query("CREATE TABLE IF NOT EXISTS " . $FSTTableName . " (config_id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY);");

fst_sql_alter (DB_NAME, $FSTTableName, 'config_name', 'VARCHAR( 255 ) NOT NULL');
fst_sql_alter (DB_NAME, $FSTTableName, 'config_value', 'TEXT NOT NULL');

fst_sql_insert($FSTTableName, 'config_name', 'RequireLogin', 'config_name, config_value', "'RequireLogin', '1'");
fst_sql_insert($FSTTableName, 'config_name', 'AllowMemberReplies', 'config_name, config_value', "'AllowMemberReplies', '0'");
fst_sql_insert($FSTTableName, 'config_name', 'PendingText', 'config_name, config_value', "'PendingText', 'Awaiting Reply'");
fst_sql_insert($FSTTableName, 'config_name', 'PendingUserText', 'config_name, config_value', "'PendingUserText', 'Awaiting User Reply'");
fst_sql_insert($FSTTableName, 'config_name', 'ResolvedText', 'config_name, config_value', "'ResolvedText', 'Resolved'");
fst_sql_insert($FSTTableName, 'config_name', 'CategoryLabel', 'config_name, config_value', "'CategoryLabel', 'Category'");
fst_sql_insert($FSTTableName, 'config_name', 'CategoryLabelPlural', 'config_name, config_value', "'CategoryLabelPlural', 'Categories'");
fst_sql_insert($FSTTableName, 'config_name', 'AllTicketsPage', 'config_name, config_value', "'AllTicketsPage', '0'");



$FSTTableName = $wpdb->prefix."fst_tickets";
$wpdb->query("CREATE TABLE IF NOT EXISTS " . $FSTTableName . " (ticket_id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY);");

fst_sql_alter (DB_NAME, $FSTTableName, 'ID', 'INT( 11 ) NOT NULL');
fst_sql_alter (DB_NAME, $FSTTableName, 'ticket_url', 'VARCHAR( 255 ) NOT NULL');
fst_sql_alter (DB_NAME, $FSTTableName, 'ticket_title', 'TEXT NOT NULL');
fst_sql_alter (DB_NAME, $FSTTableName, 'ticket_visibility', 'TINYINT(1) DEFAULT 1 NOT NULL');
fst_sql_alter (DB_NAME, $FSTTableName, 'ticket_status', "VARCHAR( 255 ) NOT NULL default 'pending'");
fst_sql_alter (DB_NAME, $FSTTableName, 'customer_ip', "VARCHAR( 255 ) NOT NULL");
fst_sql_alter (DB_NAME, $FSTTableName, 'products_id', "INT(11) DEFAULT 0 NOT NULL");
fst_sql_alter (DB_NAME, $FSTTableName, 'ticket_link1', 'VARCHAR( 255 ) NOT NULL');
fst_sql_alter (DB_NAME, $FSTTableName, 'ticket_link2', 'VARCHAR( 255 ) NOT NULL');
fst_sql_alter (DB_NAME, $FSTTableName, 'ticket_link3', 'VARCHAR( 255 ) NOT NULL');
fst_sql_alter (DB_NAME, $FSTTableName, 'ticket_views', "INT(11) DEFAULT 0 NOT NULL");
fst_sql_alter (DB_NAME, $FSTTableName, 'ticket_last_modified', "timestamp NOT NULL default CURRENT_TIMESTAMP");
fst_sql_alter (DB_NAME, $FSTTableName, 'ticket_date_added', "timestamp NOT NULL default '0000-00-00 00:00:00'");



$FSTTableName = $wpdb->prefix."fst_tickets_messages";
$wpdb->query("CREATE TABLE IF NOT EXISTS " . $FSTTableName . " (message_id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY);");

fst_sql_alter (DB_NAME, $FSTTableName, 'ID', 'INT( 11 ) NOT NULL');
fst_sql_alter (DB_NAME, $FSTTableName, 'ticket_id', 'VARCHAR( 255 ) NOT NULL');
fst_sql_alter (DB_NAME, $FSTTableName, 'message_content', 'TEXT NOT NULL');
fst_sql_alter (DB_NAME, $FSTTableName, 'message_last_modified', "timestamp NOT NULL default CURRENT_TIMESTAMP");
fst_sql_alter (DB_NAME, $FSTTableName, 'message_date_added', "timestamp NOT NULL default '0000-00-00 00:00:00'");



$FSTTableName = $wpdb->prefix."fst_categories";
$wpdb->query("CREATE TABLE IF NOT EXISTS " . $FSTTableName . " (category_id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY);");

fst_sql_alter (DB_NAME, $FSTTableName, 'parent_id', 'INT( 11 ) NOT NULL');
fst_sql_alter (DB_NAME, $FSTTableName, 'category_name', 'VARCHAR( 255 ) NOT NULL');
fst_sql_alter (DB_NAME, $FSTTableName, 'category_url', 'VARCHAR( 255 ) NOT NULL');
fst_sql_alter (DB_NAME, $FSTTableName, 'category_order', 'INT( 11 ) NOT NULL');
fst_sql_alter (DB_NAME, $FSTTableName, 'category_ticket_count', 'INT( 11 ) NOT NULL');
fst_sql_alter (DB_NAME, $FSTTableName, 'category_meta_description', 'VARCHAR( 255 ) NOT NULL');
fst_sql_alter (DB_NAME, $FSTTableName, 'category_meta_keywords', 'VARCHAR( 255 ) NOT NULL');



$FSTTableName = $wpdb->prefix."fst_tickets_to_categories";
$wpdb->query("CREATE TABLE IF NOT EXISTS " . $FSTTableName . " (association_id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY);");

fst_sql_alter (DB_NAME, $FSTTableName, 'ticket_id', "INT(11) NOT NULL");
fst_sql_alter (DB_NAME, $FSTTableName, 'category_id', "INT(11) NOT NULL");

?>