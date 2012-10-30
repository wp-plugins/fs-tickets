<?php

add_action('admin_init', 'fst_editor_admin_init');
add_action('admin_head', 'fst_editor_admin_head');
 
function fst_editor_admin_init() {
  wp_enqueue_script('word-count');
  wp_enqueue_script('post');
  wp_enqueue_script('editor');
  wp_enqueue_script('media-upload');
}
 
function fst_editor_admin_head() {
  wp_tiny_mce();
}

function fst_add_pages($Pages) {
	global $wpdb;
	foreach ($Pages as $Title => $Content) {
		if ($wpdb->get_var("SELECT COUNT(post_content) FROM ".$wpdb->prefix."posts WHERE post_content = '$Content' AND post_status IN ('publish', 'private')") == 0) {
			wp_insert_post(array(
			'post_title' => $Title,
			'post_content' => $Content,
			'post_type' => 'page',
			'post_status' => 'publish',
			'comment_status' => 'closed', 
			'ping_status' => 'closed', 
			'post_author' => 1
			));
		}
	}
}
function fst_sql_alter ($DBNAME, $TABLENAME, $COLUMNNAME, $TYPE) {
	global $wpdb;
	$AlterTable = TRUE;
	$tableFields = mysql_list_fields($DBNAME, $TABLENAME);
	for($i=0;$i<mysql_num_fields($tableFields);$i++) { 
		if(mysql_field_name($tableFields, $i) == $COLUMNNAME) {
			$AlterTable = FALSE;
		}
	}
	if ($AlterTable == TRUE) {
		$wpdb->query("ALTER TABLE $TABLENAME ADD $COLUMNNAME $TYPE");
	}
}
function fst_sql_insert($TableName, $ColumnName, $Value, $Columns, $Values) {
	global $wpdb;
	if ($wpdb->get_var("SELECT COUNT(*) FROM $TableName WHERE $ColumnName = '$Value'") == 0) {
		$wpdb->query("INSERT INTO $TableName ($Columns) VALUES ($Values)");
	}
}
function fst_print_hidden_input($name, $value) {
	echo '<input type="hidden" name="'.$name.'" value="'.$value.'">';
}
function fst_print_file_input($label, $name, $value, $length) {
	echo '<div id="fsrep_input"><div id="fsrep_input_label">'.$label.'</div><input type="file" name="'.$name.'" value="'.$value.'" size="'.$length.'"></div>';
}
function fst_print_password_input($label, $name, $value, $length) {
	echo '<div id="fsrep_input"><div id="fsrep_input_label">'.$label.'</div><input type="password" name="'.$name.'" value="'.$value.'" size="'.$length.'"></div>';
}
function fst_print_input($label, $name, $value, $length) {
	echo '<div id="fsrep_input"><div id="fsrep_input_label">'.$label.'</div><input type="text" name="'.$name.'" value="'.$value.'" size="'.$length.'"></div>';
}
function fst_print_textarea($label, $name, $value, $rows, $cols) {
	echo '<div id="fsrep_input"><div id="fsrep_input_label">'.$label.'</div><textarea name="'.$name.'" rows="'.$rows.'" cols="'.$cols.'">'.$value.'</textarea></div>';
}
function fst_print_selectbox($label, $name, $selvalue, $options) {
	echo '<div id="fsrep_input"><div id="fsrep_input_label">'.$label.'</div>';
	echo '<select name="'.$name.'">';
	foreach ($options as $key=>$value) {
		$selected = '';
		if ($selvalue == $value) { $selected = 'selected'; }
		echo '<option value="'.$value.'" '.$selected.'>'.$key.'</option>';
	}
	echo '</select>';
	echo '</div>';
}
function fst_print_admin_file_input($label, $name, $value, $length, $description) {
	echo '<tr><td>'.$label.'</td><td><input type="file" name="'.$name.'" value="'.$value.'" size="'.$length.'"></td><td>'.$description.'</td></tr>';
}
function fst_print_admin_input($label, $name, $value, $length, $description) {
	echo '<tr><td>'.$label.'</td><td><input type="text" name="'.$name.'" value="'.$value.'" size="'.$length.'"></td><td>'.$description.'</td></tr>';
}
function fst_print_admin_textarea($label, $name, $value, $cols, $rows, $description) {
	echo '<tr><td>'.$label.'</td><td><textarea name="'.$name.'" cols="'.$cols.'" rows="'.$rows.'">'.$value.'</textarea></td><td>'.$description.'</td></tr>';
}
function fst_print_admin_selectbox($label, $name, $selvalue, $options, $description) {
	echo '<tr><td>'.$label.'</td>';
	echo '<td><select name="'.$name.'">';
	foreach ($options as $key=>$value) {
		$selected = '';
		if ($selvalue == $value) { $selected = 'selected'; }
		echo '<option value="'.$value.'" '.$selected.'>'.$key.'</option>';
	}
	echo '</select>';
	echo '</td><td style="font-weight: normal;">'.$description.'</td></tr>';
}
function fst_print_admin_ext($label, $name, $selvalue, $options, $activated, $version) {
	global $fscartconfig;
	$Disabled = '';
	if ($activated == FALSE) { 
		$Disabled = ' disabled="disabled"'; $description = 'This feature is currently disabled. To purchase this extended feature, visit <a href="http://www.firestorminteractive.com/wordpress/ecommerce/" target="_blank" style="color:#999999;">www.firestorminteractive.com/wordpress/ecommerce/</a>.'; 
	} else { 
		if (function_exists(fst_extension_updatge)) { $description .= fst_extension_update($name, $version); }	else { $description = 'Version: '.$version; }
		 
	}
	echo '<tr><td>'.$label.'</td>';
	if ($activated == FALSE) {
		echo '<td>&nbsp;</td><td style="font-weight: normal; color: #999999;">'.$description.'</td></tr>';
	} else {
		echo '<td><input type="text" name="'.$name.'L" value="'.$fscartconfig[$name.'L'].'" size="30"></td><td style="font-weight: normal; color: #999999;">'.$description.'</td></tr>';
	}
}
function fst_spam_check($Var) {
	$SpamCheck = FALSE;
	foreach ($Var as $Var => $Value) {
		if (preg_match("/wp_users/i", $Value)) {
			$SpamCheck = TRUE;
		}
	}
	return $SpamCheck;
}
function fst_tickets_title() {
	global $wpdb,$pageurl;
	return ' - '.$wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'fst_tickets WHERE ticket_url = "'.$pageurl[1].'"');
}
function fst_grab_ticket_id() {
	global $wpdb,$pageurl;
	$ticket_id = $wpdb->get_var("SELECT ticket_id FROM ".$wpdb->prefix."fst_tickets WHERE ticket_url = '".$pageurl[1]."'");
	return $ticket_id;
}	
function fst_url_generator($url) {
	$url = str_replace(" ", "-", $url);
	$url = str_replace("_", "-", $url);
	$special = array('!','@','#','$','%','^','&','*','(',')','_','+','{','}','|','[',']',':',';','<','>','?',',','.','/','`','~','/','!','&','*');
	$url = str_replace(' ',' ',str_replace($special,'',$url));
	$url = str_replace("'", "", $url);
	$url = str_replace('"', '', $url);
	$url = str_replace("--", "-", $url);
	$url = strip_tags($url);
	$url = substr(strtolower($url), 0, 45);
	return $url;
}
function fst_flush_rewrite_rules() {
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}
function fst_add_rewrite_rules($wp_rewrite) {
	global $wpdb,$wp_rewrite,$FSTPages;
	$new_rules = array(
											$FSTPages['TicketsURL'].'/(.+)' => 'index.php?page_id='.$FSTPages['Tickets'].'&TicketPage='.$wp_rewrite->preg_index(1)
										);
	$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
}
function fst_imageresizer($source_pic, $destination_pic, $max_width, $max_height) {
	$src = imagecreatefromjpeg($source_pic);
	list($width,$height)=getimagesize($source_pic);
	$x_ratio = $max_width / $width;
	$y_ratio = $max_height / $height;
	if (($width <= $max_width) && ($height <= $max_height)) {
		$tn_width = $width;
		$tn_height = $height;
	} elseif (($x_ratio * $height) < $max_height) {
		$tn_height = ceil($x_ratio * $height);
		$tn_width = $max_width;
	} else {
		$tn_width = ceil($y_ratio * $width);
		$tn_height = $max_height;
	}
	$tmp=imagecreatetruecolor($tn_width,$tn_height);
	imagecopyresampled($tmp,$src,0,0,0,0,$tn_width, $tn_height,$width,$height);
	imagejpeg($tmp,$destination_pic,80);
	imagedestroy($src);
	imagedestroy($tmp);
}
function fst_form_input($label, $name, $value, $size) {
	return "<label for=\"$name\">$label</label><input type=\"text\" id=\"$name\" name=\"$name\" value=\"$value\" size=\"$size\">";
}
function fst_print_text_box($name, $value, $size) {
	if ($name == 'cardnumber' || $name == 'name_on_card' || $name == 'cardexpm' || $name == 'cardexpy' || $name == 'cvdvalue' || $name == 'customer_email') {
		return "<input type=\"text\" id=\"$name\" name=\"$name\" value=\"$value\" size=\"$size\" class=\"ClickTaleSensitive\">";
	} else {
		return "<input type=\"text\" id=\"$name\" name=\"$name\" value=\"$value\" size=\"$size\">";
	}
}
function fst_print_select_box($current_value, $value, $option) {
	$selected = "";
	if ($current_value == $value) {
		$selected = "selected";
	}
	return "<option value=\"$value\" $selected>$option</option>";
}
function fst_categories ($ParentID, $Recurrence) {
	global $tr_color,$wpdb;
	$Recurrence = $Recurrence + 1;
	$Categories = $wpdb->get_results("SELECT parent_id, category_order, category_name, category_id FROM ".$wpdb->prefix."fst_categories WHERE parent_id = $ParentID ORDER BY category_order");
	$CategoryCount = count($Categories);
	if ($CategoryCount > 0) {
		foreach ($Categories as $Categories) {
			if ($tr_color == 'FFFFFF') {
				$tr_color = 'alternate ';
			} else {
				$tr_color = 'FFFFFF';
			}
			echo '<tr id="page-22" class="'.$tr_color.'iedit">';
			echo '<td align="center" width="60">';
			if ($Categories->category_order == 1) {
				echo '<img src="'.get_option('home').'/wp-content/plugins/fs-tickets/images/btn-mini-up-g.gif" alt="UP"> ';
			} else {
				echo '<a href="admin.php?page=fst-categories&f=up&id='.$Categories->category_id.'&pid='.$Categories->parent_id.'"><img src="'.get_option('home').'/wp-content/plugins/fs-tickets/images/btn-mini-up.gif" border="0" alt="UP"></a> ';
			}
			if ($Categories->category_order == $CategoryCount) {
				echo '<img src="'.get_option('home').'/wp-content/plugins/fs-tickets/images/btn-mini-down-g.gif" border="0" alt="DOWN"><br />';
			} else {
				echo '<a href="admin.php?page=fst-categories&f=down&id='.$Categories->category_id.'&pid='.$Categories->parent_id.'"><img src="'.get_option('home').'/wp-content/plugins/fs-tickets/images/btn-mini-down.gif" border="0" alt="DOWN"></a><br />';
			}
			echo '</td>';
			$tab = '';
			for ($i=1; $i<$Recurrence; $i++) {
				$tab .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			echo '<td>'.$tab.stripslashes($Categories->category_name);
			echo '<br />'.$tab.'<a href="admin.php?page=fst-categories&f=del&id='.$Categories->category_id.'&pid='.$Categories->parent_id.'" onClick="return confirm(\'Are you sure you want to delete this category along with any subcategories in this category?\')">delete</a></td></tr>';
			fst_categories ($Categories->category_id, $Recurrence);
		}
	}
}
function fst_category_options ($parent_id, $level, $id, $value_adder) {
	global $wpdb;
	$level++;
	$Categories = $wpdb->get_results("SELECT parent_id, category_id, category_name, category_order FROM ".$wpdb->prefix."fst_categories WHERE parent_id = $parent_id ORDER BY category_order");
	$count = count($Categories);
	if ($count > 0) {
		foreach($Categories as $Categories) {
			$tab = '';
			$selected = '';
			if ($Categories->category_id == $id) {
				$selected = 'selected';
			}
			for ($i=1; $i<$level; $i++) {
				$tab .= '&nbsp;&nbsp;&nbsp;';
			}
			print '<option value="'.$value_adder.$Categories->category_id.'" '.$selected.'>'.$previous_id.' '.$tab.$Categories->category_name.'</option>';
			fst_category_options ($Categories->category_id, $level, $id, $value_adder);
		}
	}
}
?>