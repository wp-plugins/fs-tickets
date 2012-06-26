<?php
/*

Plugin Name: FireStorm Support Ticket System
Plugin URI: http://www.firestormplugins.com/plugins/tickets/
Description: This is a WordPress support ticket plugin created by Wes Fernley @ FireStorm Interactive Inc..
Author: Wes Fernley
Version: 1.00
Author URI: http://www.firestormplugins.com/

Copyright (C) 2008-2009 FireStorm Interactive Inc., www.firestorminteractive.com, info@firestorminteractive.com

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

session_start();
ini_set("memory_limit","80M");
set_time_limit(999);
remove_action('wp_head', 'rel_canonical');
require_once(ABSPATH.'/wp-includes/pluggable.php');

// ASSIGN VERSION
global $fst_version,$wpdb,$user_ID;
$fst_version = "1.00";

require("fst_install.php");
require("common_functions.php");

// ADMIN PAGES
require("admin_functions.php");

// INSTALL / UPGRADE
register_activation_hook(__FILE__,'fst_install');

// CREATE REWRITE RULES
add_action('init', 'fst_flush_rewrite_rules');
add_action('generate_rewrite_rules', 'fst_add_rewrite_rules');

include("define.php");
require("hooks.php");
require("filters.php");

?>
