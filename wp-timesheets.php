<?php
/*
  Plugin Name: WP Timesheets
  Plugin URI: http://www.github.com/declarebrands/wp-timesheets
  Description: Track the time and frequency you work on Tasks and Projects with this plugin.
  Version: 1.0.0
  Author: Chris MacKay
  Author URI: http://www.chrismackay.me
  License: GPL2
  
  Copyright 2013-2014 Declare Brands Inc (email: cmackay@declarebrands.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* Begin redirect to wp-login.php if User is not logged in. */
function wp_timesheets_not_loggedin_redirect() {
	global $pagenow;
	if (!is_user_logged_in() && $pagenow != 'wp-login.php'){
		wp_redirect( wp_login_url(), 302 );
	}
}
/* End redirect to wp-login.php if User is not logged in. */

global $wp_timesheets;
$wp_timesheets = "1.0.0";

function wp_timesheets_install() {
  global $wpdb;
  global $wp_timesheets;
	add_option("wp_timesheets", $wp_timesheets);
	require_once(ABSPATH.'wp-admin/includes/upgrade.php');
	
  $table_name = $wpdb->prefix."wp_timesheets_tasks";
  $sql = "CREATE TABLE ".$table_name." (
    id mediumint(11) NOT NULL AUTO_INCREMENT,
		active mediumint(2) DEFAULT '0' NOT NULL,
		locked mediumint(2) DEFAULT '0' NOT NULL,
    created_date date NOT NULL,
		created_time time NOT NULL,
		completed_date date NOT NULL,
		completed_time time NOT NULL,
		updated timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
		user_id mediumint(11) NOT NULL,
    project_id mediumint(11) NOT NULL,
		type_id mediumint(11) NOT NULL,
		concurrent mediumint(1) DEFAULT '0' NOT NULL,
    description text NOT NULL,
    UNIQUE KEY id (id)
  );";
  dbDelta($sql);
	
	$table_name = $wpdb->prefix."wp_timesheets_projects";
  $sql = "CREATE TABLE ".$table_name." (
    id mediumint(11) NOT NULL AUTO_INCREMENT,
		active mediumint(2) DEFAULT '0' NOT NULL,
		locked mediumint(2) DEFAULT '0' NOT NULL,
    created_date date NOT NULL,
		created_time time NOT NULL,
		updated timestamp NOT NULL,
		completed timestamp NOT NULL,
		user_id mediumint(11) NOT NULL,
		name text NOT NULL,
    description text NOT NULL,
		color_code varchar(6) DEFAULT 'FFFFFF' NOT NULL,
    UNIQUE KEY id (id)
  );";
  dbDelta($sql);
	
	$table_name = $wpdb->prefix."wp_timesheets_types";
  $sql = "CREATE TABLE ".$table_name." (
    id mediumint(11) NOT NULL AUTO_INCREMENT,
		active mediumint(2) DEFAULT '0' NOT NULL,
		locked mediumint(2) DEFAULT '0' NOT NULL,
    created_date date NOT NULL,
		created_time time NOT NULL,
		updated timestamp NOT NULL,
		completed timestamp NOT NULL,
		user_id mediumint(11) NOT NULL,
		name text NOT NULL,
    description text NOT NULL,
		color_code varchar(6) DEFAULT 'FFFFFF' NOT NULL,
    UNIQUE KEY id (id)
  );";
  dbDelta($sql);
  
}
register_activation_hook( __FILE__, 'wp_timesheets_install' );

add_action('init', 'wp_timesheets_add_thickbox');
function wp_timesheets_add_thickbox() {
   add_thickbox();
}

add_action('wp_enqueue_scripts', 'wp_timesheets_scripts');
function wp_timesheets_scripts(){
	wp_register_script('wp-timesheets-jscolor', plugin_dir_url( __FILE__ ).'js/jscolor.js', array(), '0.0.0', false);
	wp_enqueue_script('wp-timesheets-jscolor');
	wp_register_script('wp-timesheets-show-hide', plugin_dir_url( __FILE__ ).'js/show_hide.js', array(), '0.0.0', false);
	wp_enqueue_script('wp-timesheets-show-hide');
	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
	wp_register_script('jquery-ui-timepicker', plugin_dir_url( __FILE__ ).'js/jquery-ui-timepicker-addon.js', array('jquery'), '0.0.0', true);
	wp_enqueue_script('jquery-ui-timepicker');
}

function wp_timesheets_styles(){
	function wp_timesheets_styled_form_elements(){
	  ?>
		<style type="text/css">
		  input[type=radio] { display:none; }
      input[type=radio] + label {
        display:inline-block;
        margin:-2px;
        padding: 4px 12px;
        margin-bottom: 0;
        font-size: 14px;
        line-height: 20px;
        color: #333;
        text-align: center;
        text-shadow: 0 1px 1px rgba(255,255,255,0.75);
        vertical-align: middle;
        cursor: pointer;
        background-color: #f5f5f5;
        background-image: -moz-linear-gradient(top,#fff,#e6e6e6);
        background-image: -webkit-gradient(linear,0 0,0 100%,from(#fff),to(#e6e6e6));
        background-image: -webkit-linear-gradient(top,#fff,#e6e6e6);
        background-image: -o-linear-gradient(top,#fff,#e6e6e6);
        background-image: linear-gradient(to bottom,#fff,#e6e6e6);
        background-repeat: repeat-x;
        border: 1px solid #ccc;
        border-color: #e6e6e6 #e6e6e6 #bfbfbf;
        border-color: rgba(0,0,0,0.1) rgba(0,0,0,0.1) rgba(0,0,0,0.25);
        border-bottom-color: #b3b3b3;
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffffff',endColorstr='#ffe6e6e6',GradientType=0);
        filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
        -webkit-box-shadow: inset 0 1px 0 rgba(255,255,255,0.2),0 1px 2px rgba(0,0,0,0.05);
        -moz-box-shadow: inset 0 1px 0 rgba(255,255,255,0.2),0 1px 2px rgba(0,0,0,0.05);
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.2),0 1px 2px rgba(0,0,0,0.05);
      }
      input[type=radio]:checked + label {
        background-image: none;
        outline: 0;
        -webkit-box-shadow: inset 0 2px 4px rgba(0,0,0,0.15),0 1px 2px rgba(0,0,0,0.05);
        -moz-box-shadow: inset 0 2px 4px rgba(0,0,0,0.15),0 1px 2px rgba(0,0,0,0.05);
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.15),0 1px 2px rgba(0,0,0,0.05);
        background-color:#e0e0e0;
      }
			.wp_timesheets input[type=checkbox] { display:none; }
      .wp_timesheets input[type=checkbox] + label {
        display:inline-block;
        margin:-2px;
        padding: 4px 12px;
        margin-bottom: 0;
        font-size: 14px;
        line-height: 20px;
        color: #333;
        text-align: center;
        text-shadow: 0 1px 1px rgba(255,255,255,0.75);
        vertical-align: middle;
        cursor: pointer;
        background-color: #f5f5f5;
        background-image: -moz-linear-gradient(top,#fff,#e6e6e6);
        background-image: -webkit-gradient(linear,0 0,0 100%,from(#fff),to(#e6e6e6));
        background-image: -webkit-linear-gradient(top,#fff,#e6e6e6);
        background-image: -o-linear-gradient(top,#fff,#e6e6e6);
        background-image: linear-gradient(to bottom,#fff,#e6e6e6);
        background-repeat: repeat-x;
        border: 1px solid #ccc;
        border-color: #e6e6e6 #e6e6e6 #bfbfbf;
        border-color: rgba(0,0,0,0.1) rgba(0,0,0,0.1) rgba(0,0,0,0.25);
        border-bottom-color: #b3b3b3;
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffffff',endColorstr='#ffe6e6e6',GradientType=0);
        filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
        -webkit-box-shadow: inset 0 1px 0 rgba(255,255,255,0.2),0 1px 2px rgba(0,0,0,0.05);
        -moz-box-shadow: inset 0 1px 0 rgba(255,255,255,0.2),0 1px 2px rgba(0,0,0,0.05);
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.2),0 1px 2px rgba(0,0,0,0.05);
      }
      .wp_timesheets input[type=checkbox]:checked + label {
        background-image: none;
        outline: 0;
        -webkit-box-shadow: inset 0 2px 4px rgba(0,0,0,0.15),0 1px 2px rgba(0,0,0,0.05);
        -moz-box-shadow: inset 0 2px 4px rgba(0,0,0,0.15),0 1px 2px rgba(0,0,0,0.05);
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.15),0 1px 2px rgba(0,0,0,0.05);
        background-color:#e0e0e0;
      }
			/* css for timepicker */
      .ui-timepicker-div .ui-widget-header { margin-bottom: 8px; }
      .ui-timepicker-div dl { text-align: left; }
      .ui-timepicker-div dl dt { float: left; clear:left; padding: 0 0 0 5px; }
      .ui-timepicker-div dl dd { margin: 0 10px 10px 45%; }
      .ui-timepicker-div td { font-size: 90%; }
      .ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }

      .ui-timepicker-rtl{ direction: rtl; }
      .ui-timepicker-rtl dl { text-align: right; padding: 0 5px 0 0; }
      .ui-timepicker-rtl dl dt{ float: right; clear: right; }
      .ui-timepicker-rtl dl dd { margin: 0 45% 10px 10px; }
			
			@media print {
			  .header-image { display: none; }
			  #center-nav { display: none; }
				#page { margin: 0; padding: 0; margin-top: -85px; }
			}
		</style>
	  <?
	}
	add_action('wp_head','wp_timesheets_styled_form_elements');
}
add_action('init', 'wp_timesheets_styles');

function wp_timesheets_list ( $atts ) {
  wp_timesheets_not_loggedin_redirect();
	global $current_user, $wp_roles, $wpdb;
  get_currentuserinfo();
  $debugMode = 0;
	$html = '';
	if ( ( (isset($_GET['action'])) && ($_GET['action'] == 'update-timesheets') ) || ( ('POST' == $_SERVER['REQUEST_METHOD']) && (!empty($_POST['action'])) && ($_POST['action'] == 'update-timesheets') ) ){
	  if ( (isset($_POST['existing_task'])) && (is_array($_POST['existing_task'])) ){
		  $updated_id = array();
			$failed_id = array();
		  foreach ($_POST['existing_task'] as $existing_tasks){
			  $update = $wpdb->update( $wpdb->prefix."wp_timesheets_tasks",
				  array(
					  'description' => $existing_tasks['description']
					),
					array (
					  'id' => $existing_tasks['id'],
						'user_id' => $current_user->ID
					)
				);
				if ($update){
				  array_push($updated_id, $existing_tasks['id']);
				} else {
				  array_push($failed_id, $existing_tasks['id']);
				}
				
				if (isset($existing_tasks['project_id'])){
				  $update = $wpdb->update( $wpdb->prefix."wp_timesheets_tasks",
				    array(
					    'project_id' => $existing_tasks['project_id']
					  ),
					  array (
					    'id' => $existing_tasks['id'],
						  'user_id' => $current_user->ID
					  )
				  );
				  if ($update){
				    array_push($updated_id, $existing_projects['id']);
				  } else {
				    array_push($failed_id, $existing_projects['id']);
				  }
				}
				
				if (isset($existing_tasks['type_id'])){
				  $update = $wpdb->update( $wpdb->prefix."wp_timesheets_tasks",
				    array(
					    'type_id' => $existing_tasks['type_id']
					  ),
					  array (
					    'id' => $existing_tasks['id'],
						  'user_id' => $current_user->ID
					  )
				  );
				  if ($update){
				    array_push($updated_id, $existing_projects['id']);
				  } else {
				    array_push($failed_id, $existing_projects['id']);
				  }
				}
				
				if ( (isset($existing_tasks['completed'])) && ($existing_tasks['completed'] == 1) ){
				  if (!empty($_POST['completed_time_'.$existing_tasks['id']])){
					  $completed_pieces = explode(' ', $_POST['completed_time_'.$existing_tasks['id']]);
						$completed_date = trim($completed_pieces[0]);
						$completed_time = trim($completed_pieces[1]);
						$completed_time = date('H:i:s', strtotime($completed_time) + 59);
					} else {
				    $completed_time = date('H:i:s');
					  $completed_time = date('H:i:s', strtotime($completed_time) - 60 * 60 * 7);
						$completed_date = date('Y-m-d');
					}
				  $update = $wpdb->update( $wpdb->prefix."wp_timesheets_tasks",
				    array(
					    'completed_date' => $completed_date,
							'completed_time' => $completed_time
					  ),
					  array (
					    'id' => $existing_tasks['id'],
						  'user_id' => $current_user->ID
					  )
				  );
				  if ($update){
				    array_push($updated_id, $existing_projects['id']);
				  } else {
				    array_push($failed_id, $existing_projects['id']);
				  }
				}
			}
		}
		if ( (isset($_POST['new_task'])) && (is_array($_POST['new_task'])) ){
		  $error_html = '';
		  foreach ($_POST['new_task'] as $new_tasks){
			  if (!empty($new_tasks['description'])){
				  if (isset($new_tasks['created_date'])){
					  $created_date = $new_tasks['created_date'];
					} else {
			      $created_date = date('Y-m-d');
					}
					if (isset($new_tasks['created_time'])){
					  $created_time = $new_tasks['created_time'];
					} else {
						$created_time = date('H:i:s');
						$created_time = date('H:i:s', strtotime($created_time) - 60 * 60 * 7);
					}
					if ( (isset($new_tasks['new'])) && ($new_tasks['new'] == 1) ){
					  $insert = $wpdb->insert( $wpdb->prefix."wp_timesheets_tasks",
				      array(
					      'active' => '1',
						    'locked' => '0',
						    'created_date' => $created_date,
						    'created_time' => $created_time,
						    'user_id' => $current_user->ID,
								'concurrent' => $new_tasks['new'],
						    'description' => $new_tasks['description']
					    )
				    );
					} else {
			      $sql = "SELECT * FROM ".$wpdb->prefix."wp_timesheets_tasks WHERE user_id='".$current_user->ID."' AND created_date='".$created_date."' AND created_time <= '".$created_time."' AND created_time > '".date( 'H:i:s', $created_time + 15 * 60 )."' AND concurrent='0' ORDER BY created_time DESC LIMIT 1";
						$running_tasks = $wpdb->get_results($sql);
						foreach ($running_tasks as $task){
							$task_end_time = date('H:i:s', strtotime($created_time) -1);
							$update = $wpdb->update( $wpdb->prefix."wp_timesheets_tasks",
							  array(
								  'completed_date' => date('Y-m-d'),
									'completed_time' => $task_end_time
								),
								array(
								  'id' => $task->id,
									'active' => '1',
									'locked' => '0',
									'user_id' => $current_user->ID
								)
							);
							if ($update){
				        array_push($updated_id, $task->id);
				      } else {
				        array_push($failed_id, $task->id);
				      }
						}
						$insert = $wpdb->insert( $wpdb->prefix."wp_timesheets_tasks",
				      array(
					      'active' => '1',
						    'locked' => '0',
						    'created_date' => $created_date,
						    'created_time' => $created_time,
						    'user_id' => $current_user->ID,
						    'description' => $new_tasks['description']
					    )
				    );
					}
				  if ($insert){
				    //woohoo
				  } else {
				    $error_html .= 'ID: '.$current_user->ID.' Description: '.$new_tasks['description'].'<br />';
				  }
				  if (!empty($error_html)){
				    print '<pre>'.$error_html.'</pre>';
				  }
				}
			}
		}
	} elseif ( ( (isset($_GET['action'])) && ($_GET['action'] == 'update-projects') ) || ( ('POST' == $_SERVER['REQUEST_METHOD']) && (!empty($_POST['action'])) && ($_POST['action'] == 'update-projects') ) ){
	  if ( (isset($_POST['existing_projects'])) && (is_array($_POST['existing_projects'])) ){
		  $updated_id = array();
			$failed_id = array();
		  foreach ($_POST['existing_projects'] as $existing_projects){
			  $update = $wpdb->update( $wpdb->prefix."wp_timesheets_projects",
				  array(
					  'name' => $existing_projects['name']
					),
					array (
					  'id' => $existing_projects['id'],
						'user_id' => $current_user->ID
					)
				);
				if ($update){
				  array_push($updated_id, $existing_projects['id']);
				} else {
				  array_push($failed_id, $existing_projects['id']);
				}
				
				if (!empty($existing_projects['description'])){
				  $update = $wpdb->update( $wpdb->prefix."wp_timesheets_projects",
				    array(
					    'description' => $existing_projects['description']
					  ),
					  array (
					    'id' => $existing_projects['id'],
						  'user_id' => $current_user->ID
					  )
				  );
				  if ($update){
				    array_push($updated_id, $existing_projects['id']);
				  } else {
				    array_push($failed_id, $existing_projects['id']);
				  }
				}
			}
		}
		if ( (isset($_POST['new_project'])) && (is_array($_POST['new_project'])) ){
		  $error_html = '';
		  foreach ($_POST['new_project'] as $new_projects){
			  if (!empty($new_projects['name'])){
				  if (isset($new_projects['created_date'])){
					  $created_date = $new_projects['created_date'];
					} else {
			      $created_date = date('Y-m-d');
					}
					if (isset($new_projects['created_time'])){
					  $created_time = $new_projects['created_time'];
					} else {
						$created_time = date('H:i:s');
						$created_time = date('H:i:s', strtotime($created_time) - 60 * 60 * 7);
					}
					if ( (isset($new_projects['color_code'])) && ($new_projects['color_code'] != 'FFFFFF') ){
			      $insert = $wpdb->insert( $wpdb->prefix."wp_timesheets_projects",
				      array(
					      'active' => '1',
						    'locked' => '0',
						    'created_date' => $created_date,
						    'created_time' => $created_time,
						    'user_id' => $current_user->ID,
						    'name' => $new_projects['name'],
								'color_code' => $new_projects['color_code']
					    )
				    );
					} else {
					  $insert = $wpdb->insert( $wpdb->prefix."wp_timesheets_projects",
				      array(
					      'active' => '1',
						    'locked' => '0',
						    'created_date' => $created_date,
						    'created_time' => $created_time,
						    'user_id' => $current_user->ID,
						    'name' => $new_projects['name']
					    )
				    );
				  }
				  if ($insert){
				    //woohoo
				  } else {
				    $error_html .= 'ID: '.$current_user->ID.' Name: '.$new_projects['name'].'<br />';
				  }
				  if (!empty($error_html)){
				    print '<pre>'.$error_html.'</pre>';
				  }
				}
			}
		}
	
	} elseif ( ( (isset($_GET['action'])) && ($_GET['action'] == 'disable-projects') ) || ( ('POST' == $_SERVER['REQUEST_METHOD']) && (!empty($_POST['action'])) && ($_POST['action'] == 'disable-projects') ) ){
	  if (isset($_GET['id'])){
		  $updated_id = array();
			$failed_id = array();
			$update = $wpdb->update( $wpdb->prefix."wp_timesheets_projects",
			  array(
				  'locked' => '1'
				),
				array (
				  'id' => $_GET['id'],
					'user_id' => $current_user->ID
				)
			);
			if ($update){
			  array_push($updated_id, $_GET['id']);
			} else {
			  array_push($failed_id, $_GET['id']);
			}
		}
	} elseif ( ( (isset($_GET['action'])) && ($_GET['action'] == 'enable-projects') ) || ( ('POST' == $_SERVER['REQUEST_METHOD']) && (!empty($_POST['action'])) && ($_POST['action'] == 'enable-projects') ) ){
	  if (isset($_GET['id'])){
		  $updated_id = array();
			$failed_id = array();
			$update = $wpdb->update( $wpdb->prefix."wp_timesheets_projects",
			  array(
				  'locked' => '0'
				),
				array (
				  'id' => $_GET['id'],
					'user_id' => $current_user->ID
				)
			);
			if ($update){
			  array_push($updated_id, $_GET['id']);
			} else {
			  array_push($failed_id, $_GET['id']);
			}
		}
	} elseif ( ( (isset($_GET['action'])) && ($_GET['action'] == 'delete-projects') ) || ( ('POST' == $_SERVER['REQUEST_METHOD']) && (!empty($_POST['action'])) && ($_POST['action'] == 'delete-projects') ) ){
	  if (isset($_GET['id'])){
		  $updated_id = array();
			$failed_id = array();
			$update = $wpdb->update( $wpdb->prefix."wp_timesheets_projects",
			  array(
				  'active' => '0'
				),
				array (
				  'id' => $_GET['id'],
					'user_id' => $current_user->ID
				)
			);
			if ($update){
			  array_push($updated_id, $_GET['id']);
			} else {
			  array_push($failed_id, $_GET['id']);
			}
		}
	} elseif ( ( (isset($_GET['action'])) && ($_GET['action'] == 'update-types') ) || ( ('POST' == $_SERVER['REQUEST_METHOD']) && (!empty($_POST['action'])) && ($_POST['action'] == 'update-types') ) ){
	  if ( (isset($_POST['existing_types'])) && (is_array($_POST['existing_types'])) ){
		  $updated_id = array();
			$failed_id = array();
		  foreach ($_POST['existing_types'] as $existing_types){
			  $update = $wpdb->update( $wpdb->prefix."wp_timesheets_types",
				  array(
					  'name' => $existing_types['name']
					),
					array (
					  'id' => $existing_types['id'],
						'user_id' => $current_user->ID
					)
				);
				if ($update){
				  array_push($updated_id, $existing_types['id']);
				} else {
				  array_push($failed_id, $existing_types['id']);
				}
				
				if (!empty($existing_types['description'])){
				  $update = $wpdb->update( $wpdb->prefix."wp_timesheets_types",
				    array(
					    'description' => $existing_types['description']
					  ),
					  array (
					    'id' => $existing_types['id'],
						  'user_id' => $current_user->ID
					  )
				  );
				  if ($update){
				    array_push($updated_id, $existing_types['id']);
				  } else {
				    array_push($failed_id, $existing_types['id']);
				  }
				}
			}
		}
		if ( (isset($_POST['new_type'])) && (is_array($_POST['new_type'])) ){
		  $error_html = '';
		  foreach ($_POST['new_type'] as $new_types){
			  if (!empty($new_types['name'])){
				  if (isset($new_types['created_date'])){
					  $created_date = $new_types['created_date'];
					} else {
			      $created_date = date('Y-m-d');
					}
					if (isset($new_types['created_time'])){
					  $created_time = $new_types['created_time'];
					} else {
						$created_time = date('H:i:s');
						$created_time = date('H:i:s', strtotime($created_time) - 60 * 60 * 7);
					}
					if ( (isset($new_types['color_code'])) && ($new_types['color_code'] != 'FFFFFF') ){
			      $insert = $wpdb->insert( $wpdb->prefix."wp_timesheets_types",
				      array(
					      'active' => '1',
						    'locked' => '0',
						    'created_date' => $created_date,
						    'created_time' => $created_time,
						    'user_id' => $current_user->ID,
						    'name' => $new_types['name'],
								'color_code' => $new_types['color_code']
					    )
				    );
					} else {
					  $insert = $wpdb->insert( $wpdb->prefix."wp_timesheets_types",
				      array(
					      'active' => '1',
						    'locked' => '0',
						    'created_date' => $created_date,
						    'created_time' => $created_time,
						    'user_id' => $current_user->ID,
						    'name' => $new_types['name']
					    )
				    );
				  }
				  if ($insert){
				    //woohoo
				  } else {
				    $error_html .= 'ID: '.$current_user->ID.' Name: '.$new_types['name'].'<br />';
				  }
				  if (!empty($error_html)){
				    print '<pre>'.$error_html.'</pre>';
				  }
				}
			}
		}
	
	} elseif ( ( (isset($_GET['action'])) && ($_GET['action'] == 'disable-types') ) || ( ('POST' == $_SERVER['REQUEST_METHOD']) && (!empty($_POST['action'])) && ($_POST['action'] == 'disable-types') ) ){
	  if (isset($_GET['id'])){
		  $updated_id = array();
			$failed_id = array();
			$update = $wpdb->update( $wpdb->prefix."wp_timesheets_types",
			  array(
				  'locked' => '1'
				),
				array (
				  'id' => $_GET['id'],
					'user_id' => $current_user->ID
				)
			);
			if ($update){
			  array_push($updated_id, $_GET['id']);
			} else {
			  array_push($failed_id, $_GET['id']);
			}
		}
	} elseif ( ( (isset($_GET['action'])) && ($_GET['action'] == 'enable-types') ) || ( ('POST' == $_SERVER['REQUEST_METHOD']) && (!empty($_POST['action'])) && ($_POST['action'] == 'enable-types') ) ){
	  if (isset($_GET['id'])){
		  $updated_id = array();
			$failed_id = array();
			$update = $wpdb->update( $wpdb->prefix."wp_timesheets_types",
			  array(
				  'locked' => '0'
				),
				array (
				  'id' => $_GET['id'],
					'user_id' => $current_user->ID
				)
			);
			if ($update){
			  array_push($updated_id, $_GET['id']);
			} else {
			  array_push($failed_id, $_GET['id']);
			}
		}
	} elseif ( ( (isset($_GET['action'])) && ($_GET['action'] == 'delete-types') ) || ( ('POST' == $_SERVER['REQUEST_METHOD']) && (!empty($_POST['action'])) && ($_POST['action'] == 'delete-types') ) ){
	  if (isset($_GET['id'])){
		  $updated_id = array();
			$failed_id = array();
			$update = $wpdb->update( $wpdb->prefix."wp_timesheets_types",
			  array(
				  'active' => '0'
				),
				array (
				  'id' => $_GET['id'],
					'user_id' => $current_user->ID
				)
			);
			if ($update){
			  array_push($updated_id, $_GET['id']);
			} else {
			  array_push($failed_id, $_GET['id']);
			}
		}
	}
	if ( (isset($_GET['edit'])) && ($_GET['edit'] >= 1) ){
	  $html .= do_shortcode('[wp_timesheets_edit id='.$_GET['edit'].']');
	} else {
	  if (isset($_GET['view'])){
		  $view = $_GET['view'];
		} else {
		  if (isset($_POST['view'])){
			  $view = $_POST['view'];
			} else {
		    $view = 'timesheets';
			}
		}
		$html .= '<ul id="center-nav">'.PHP_EOL;
		  if ($view == 'timesheets'){
			  $html .= '<li class="active"><a href="./">Timesheets</a></li>';
			} else {
			  $html .= '<li><a href="./">Timesheets</a></li>';
			}
			if ($view == 'projects'){
			  $html .= '<li class="active"><a href="./?view=projects">Projects</a></li>';
			} else {
			  $html .= '<li><a href="./?view=projects">Projects</a></li>';
			}
			if ($view == 'types'){
			  $html .= '<li class="active"><a href="./?view=types">Task Types</a></li>';
			} else {
			  $html .= '<li><a href="./?view=types">Task Types</a></li>';
			}
			if ($view == 'reports'){
			  $html .= '<li class="active"><a href="./?view=reports">Reports</a></li>';
			} else {
			  $html .= '<li><a href="./?view=reports">Reports</a></li>';
			}
		$html .= '</ul>'.PHP_EOL;
		switch ($view){
		  case 'projects':
			  include_once('views/projects.php');
			break;
			case 'types':
			  include_once('views/types.php');
			break;
			case 'reports':
			  include_once('views/reports.php');
			break;
			default:
			  include_once('views/timesheets.php');
			break;
		}
	}
	return $html;
}
add_shortcode('wp_timesheets_list', 'wp_timesheets_list');

function wp_timesheets_edit ( $atts ) {
  global $current_user, $wp_roles, $wpdb;
  get_currentuserinfo();
  $html = '';
  extract( shortcode_atts( array(
    'id' => '0',
  ), $atts, 'wp_timesheets_edit' ) );
	$html .= '<br /><h1>Editing Task #'.$id.'</h1><br />'.PHP_EOL;
	$sql = "SELECT COUNT(*) FROM ".$wpdb->prefix."wp_timesheets_tasks WHERE active='1' AND locked='0' AND user_id='".$current_user->ID."' AND id='".$id."'";
	$task_count = $wpdb->get_var($sql);
	if ( isset($debugMode) && $debugMode >= 1 ){
	  $html .= '<p>sql: '.$sql.'</p>'.PHP_EOL;
	  $html .= '<p>user_id: '.$current_user->ID.'</p>'.PHP_EOL;
	  $html .= '<p>Tasks: '.$task_count.'</p>'.PHP_EOL;
	}
	if ($task_count >= 1){
	  $html .= '<form name="wp_timesheets_edit" method="post" action=".">'.PHP_EOL;
		  $html .= '<table width="100%" cellspacing="10">';
			  $html .= '<tr>';
				  $html .= '<td>';
	          $sql = "SELECT * FROM ".$wpdb->prefix."wp_timesheets_tasks WHERE active='1' AND locked='0' AND user_id='".$current_user->ID."' AND id='".$id."'";
		        $tasks = $wpdb->get_results($sql);
		        foreach ( (array) $tasks as $task ){
		          $html .= '<input type="hidden" name="existing_task['.$task->id.'][id]" value="'.$task->id.'">'.PHP_EOL;
			        $html .= '<input type="text" name="existing_task['.$task->id.'][description]" value="'.$task->description.'">'.PHP_EOL;
		        }
			    $html .= '</td>';
				$html .= '</tr>';
			  $sql = "SELECT COUNT(*) FROM ".$wpdb->prefix."wp_timesheets_projects WHERE active='1' AND locked='0' AND user_id='".$current_user->ID."'";
			  $project_count = $wpdb->get_var($sql);
			  if ($project_count >= 1){
				  $html .= '<tr>';
					  $html .= '<td>';
			        $html .= '<select name="existing_task['.$task->id.'][project_id]">'.PHP_EOL;
				        $html .= '<option value="">Select Project</option>'.PHP_EOL;
			          $sql = "SELECT * FROM ".$wpdb->prefix."wp_timesheets_projects WHERE active='1' AND locked='0' AND user_id='".$current_user->ID."' ORDER BY name ASC";
				        $projects = $wpdb->get_results($sql);
				        foreach ( (array) $projects as $project ){
					        if ($project->id == $task->project_id){
				            $html .= '<option selected="selected" value="'.$project->id.'">'.$project->name.'</option>'.PHP_EOL;
						      } else {
						        $html .= '<option value="'.$project->id.'">'.$project->name.'</option>'.PHP_EOL;
						      }
				        }
				      $html .= '</select>';
						$html .= '</td>';
					$html .= '</tr>';
			  }
			  $sql = "SELECT COUNT(*) FROM ".$wpdb->prefix."wp_timesheets_types WHERE active='1' AND locked='0' AND user_id='".$current_user->ID."'";
			  $type_count = $wpdb->get_var($sql);
			  if ($type_count >= 1){
				  $html .= '<tr>';
					  $html .= '<td>';
			        $html .= '<select name="existing_task['.$task->id.'][type_id]">'.PHP_EOL;
				        $html .= '<option value="">Select Task Type</option>'.PHP_EOL;
			          $sql = "SELECT * FROM ".$wpdb->prefix."wp_timesheets_types WHERE active='1' AND locked='0' AND user_id='".$current_user->ID."' ORDER BY name ASC";
				        $types = $wpdb->get_results($sql);
				        foreach ( (array) $types as $type ){
					        if ($type->id == $task->type_id){
				            $html .= '<option selected="selected" value="'.$type->id.'">'.$type->name.'</option>'.PHP_EOL;
						      } else {
						        $html .= '<option value="'.$type->id.'">'.$type->name.'</option>'.PHP_EOL;
						      }
				        }
				      $html .= '</select>';
						$html .= '</td>';
					$html .= '</tr>';
			  }
			  if (strtotime($task->completed_date) <= 0){
			    $html .= '<tr>';
					  $html .= '<td>';
			        $html .= '<input id="radio1_'.$task->id.'" type="radio" name="existing_task['.$task->id.'][completed]" value="0" checked="checked" onclick="Test(this);"><label for="radio1_'.$task->id.'">Not Completed</label>';
				      $html .= '<input id="radio2_'.$task->id.'" type="radio" name="existing_task['.$task->id.'][completed]" value="1" onclick="Test(this);"><label for="radio2_'.$task->id.'">Completed</label>';
							$html .= '<div id="depot" style="display: none;">'.PHP_EOL;
							  $html .= '<input type="text" id="completed_time_'.$task->id.'" name="completed_time_'.$task->id.'" placeholder="Completed Time">'.PHP_EOL;
							$html .= '</div>'.PHP_EOL;
						$html .= '</td>';
					$html .= '</tr>';
					$html .= '<script type="text/javascript">';
          $html .= '      jQuery(document).ready(function() {';
          $html .= "				jQuery('#completed_time_".$task->id."').datetimepicker({dateFormat: 'yy-m-d'});";
          $html .= '      });';
          $html .= '	  </script>';
			  }
			  $html .= '<tr>';
			    $html .= '<td>';
			      $html .= '<input type="hidden" name="action" value="update-timesheets"><input type="submit" value="Save">'.PHP_EOL;
				  $html .= '</td>';
			  $html .= '</tr>';
		  $html .= '</table>';
		$html .= '</form>'.PHP_EOL;
	}
	return $html;
}
add_shortcode('wp_timesheets_edit', 'wp_timesheets_edit');

function wp_timesheets_projects_edit ( $atts ) {
  global $current_user, $wp_roles, $wpdb;
  get_currentuserinfo();
  $html = '';
  extract( shortcode_atts( array(
    'id' => '0',
  ), $atts, 'wp_timesheets_projects_edit' ) );
	$html .= '<br /><h1>Editing Project #'.$id.'</h1><br />'.PHP_EOL;
	$sql = "SELECT COUNT(*) FROM ".$wpdb->prefix."wp_timesheets_projects WHERE active='1' AND user_id='".$current_user->ID."' AND id='".$id."'";
	$project_count = $wpdb->get_var($sql);
	if ( isset($debugMode) && $debugMode >= 1 ){
	  $html .= '<p>sql: '.$sql.'</p>'.PHP_EOL;
	  $html .= '<p>user_id: '.$current_user->ID.'</p>'.PHP_EOL;
	  $html .= '<p>Tasks: '.$task_count.'</p>'.PHP_EOL;
	}
	if ($project_count >= 1){
	  $html .= '<form name="wp_timesheets_projects_edit" method="post" action=".">'.PHP_EOL;
	    $sql = "SELECT * FROM ".$wpdb->prefix."wp_timesheets_projects WHERE active='1' AND user_id='".$current_user->ID."' AND id='".$id."'";
		  $projects = $wpdb->get_results($sql);
		  foreach ( (array) $projects as $project ){
		    $html .= '<input type="hidden" name="existing_projects['.$project->id.'][id]" value="'.$project->id.'">'.PHP_EOL;
				$html .= '<input type="text" name="existing_projects['.$project->id.'][name]" value="'.$project->name.'">'.PHP_EOL;
				if (empty($project->description)){
			    $html .= '<input type="text" name="existing_projects['.$project->id.'][description]" value="'.$project->description.'" placeholder="Description">'.PHP_EOL;
				} else {
				  $html .= '<input type="text" name="existing_projects['.$project->id.'][description]" value="'.$project->description.'">'.PHP_EOL;
				}
		  }
			$html .= '<input type="hidden" name="action" value="update-projects">'.PHP_EOL;
			$html .= '<input type="hidden" name="view" value="projects">'.PHP_EOL;
			$html .= '<input type="submit" value="Save">'.PHP_EOL;
		$html .= '</form>'.PHP_EOL;
	}
	return $html;
}
add_shortcode('wp_timesheets_projects_edit', 'wp_timesheets_projects_edit');

function wp_timesheets_types_edit ( $atts ) {
  global $current_user, $wp_roles, $wpdb;
  get_currentuserinfo();
  $html = '';
  extract( shortcode_atts( array(
    'id' => '0',
  ), $atts, 'wp_timesheets_types_edit' ) );
	$html .= '<br /><h1>Editing Type #'.$id.'</h1><br />'.PHP_EOL;
	$sql = "SELECT COUNT(*) FROM ".$wpdb->prefix."wp_timesheets_types WHERE active='1' AND user_id='".$current_user->ID."' AND id='".$id."'";
	$type_count = $wpdb->get_var($sql);
	if ( isset($debugMode) && $debugMode >= 1 ){
	  $html .= '<p>sql: '.$sql.'</p>'.PHP_EOL;
	  $html .= '<p>user_id: '.$current_user->ID.'</p>'.PHP_EOL;
	  $html .= '<p>Types: '.$type_count.'</p>'.PHP_EOL;
	}
	if ($type_count >= 1){
	  $html .= '<form name="wp_timesheets_types_edit" method="post" action=".">'.PHP_EOL;
	    $sql = "SELECT * FROM ".$wpdb->prefix."wp_timesheets_types WHERE active='1' AND user_id='".$current_user->ID."' AND id='".$id."'";
		  $types = $wpdb->get_results($sql);
		  foreach ( (array) $types as $type ){
		    $html .= '<input type="hidden" name="existing_types['.$type->id.'][id]" value="'.$type->id.'">'.PHP_EOL;
				$html .= '<input type="text" name="existing_types['.$type->id.'][name]" value="'.$type->name.'">'.PHP_EOL;
				if (empty($type->description)){
			    $html .= '<input type="text" name="existing_types['.$type->id.'][description]" value="'.$type->description.'" placeholder="Description">'.PHP_EOL;
				} else {
				  $html .= '<input type="text" name="existing_types['.$type->id.'][description]" value="'.$type->description.'">'.PHP_EOL;
				}
		  }
			$html .= '<input type="hidden" name="action" value="update-types">'.PHP_EOL;
			$html .= '<input type="hidden" name="view" value="types">'.PHP_EOL;
			$html .= '<input type="submit" value="Save">'.PHP_EOL;
		$html .= '</form>'.PHP_EOL;
	}
	return $html;
}
add_shortcode('wp_timesheets_types_edit', 'wp_timesheets_types_edit');
?>

