<?php
    if ( isset($debugMode) && $debugMode >= 1 ){
	    $html .= '<p>sql: '.$sql.'</p>'.PHP_EOL;
		  $html .= '<p>user_id: '.$current_user->ID.'</p>'.PHP_EOL;
	    $html .= '<p>Tasks: '.$task_count.'</p>'.PHP_EOL;
	  }
		$popup_html = '';
	  $inc = 15 * 60;
    $start = (strtotime('8AM')); // 8AM
    $end = (strtotime('6PM')); // 6PM
		if ( (isset($_GET['date'])) ){
		  $the_date = $_GET['date'];
		} else {
		  $the_date = date('Y-m-d');
		}
		$ts = strtotime($the_date);
		$year = date('o', $ts);
		$week = date('W', $ts);
		$html .= '<ul id="center-nav">'.PHP_EOL;
		  $html .= '<li class="action"><a href="./?date='.date("Y-m-d", strtotime("+1 year", $ts)).'">&rarr;</a></li>'.PHP_EOL;
			$html .= '<li class="action"><a href="./?date='.date("Y-m-d").'">Today</a></li>'.PHP_EOL;
			$html .= '<li class="action"><a href="./?date='.date("Y-m-d", strtotime("-1 year", $ts)).'">&larr;</a></li>'.PHP_EOL;
		  for($m = 1;$m <= 12; $m++){
		    $month =  date("F", mktime(0, 0, 0, $m));
			  if ( strlen($m) == 1 ){
			    $m = '0'.$m;
			  }
			  if ( date("F", $ts) == $month ){
          $html .= '<li class="active"><a href="./?date='.$year.'-'.$m.'-01">'.$month.'</a></li>'.PHP_EOL;
			  } else {
			    $html .= '<li><a href="./?date='.$year.'-'.$m.'-01">'.$month.'</a></li>'.PHP_EOL;
			  }
      } 
		$html .= '</ul>'.PHP_EOL;
		$html .= '<ul id="center-nav">'.PHP_EOL;
			$html .= '<li class="action"><a href="./?date='.date("Y-m-d", strtotime("+7 day", $ts)).'">&rarr;</a></li>'.PHP_EOL;
			$html .= '<li class="action"><a href="./?date='.date("Y-m-d").'">Today</a></li>'.PHP_EOL;
			$html .= '<li class="action"><a href="./?date='.date("Y-m-d", strtotime("-7 day", $ts)).'">&larr;</a></li>'.PHP_EOL;
		  for ($i = 1; $i <= 7; $i++){
		    $ts = strtotime($year.'W'.$week.$i);
			  if ( date("Y-m-d", $ts) == $the_date ){
			    $html .= '<li class="active"><a href="./?date='.date("Y-m-d", $ts).'">'.date("l (d)", $ts).'</a></li>'.PHP_EOL;
			  } else {
			    $html .= '<li><a href="./?date='.date("Y-m-d", $ts).'">'.date("l (d)", $ts).'</a></li>'.PHP_EOL;
			  }
		  }
		$html .= '</ul>'.PHP_EOL;
	  $html .= '<form name="wp_timesheets" method="post" action=".">'.PHP_EOL;
		  $new_task_count = 0;
			$html .= '<h1>Quick Task</h1>';
			$html .= '<table>'.PHP_EOL;
			  $html .= '<tr>'.PHP_EOL;
				  $html .= '<td>'.PHP_EOL;						
					  $html .= '<input type="text" name="new_task['.$new_task_count.'][description]">'.PHP_EOL;
						$html .= '<input id="radio1" type="radio" name="new_task['.$new_task_count.'][new]" value="0" checked="checked"><label for="radio1">New Task</label>';
						$html .= '<input id="radio2" type="radio" name="new_task['.$new_task_count.'][new]" value="1"><label for="radio2">Concurrent Task</label>';
					$html .= '</td>'.PHP_EOL;
					$html .= '<td>'.PHP_EOL;
						$html .= '<input type="submit" value="Save">'.PHP_EOL;
						$new_task_count++;
					$html .= '</td>'.PHP_EOL;
				$html .= '</tr>'.PHP_EOL;
			$html .= '</table>'.PHP_EOL;
	    $html .= '<table cellpadding="10" cellspacing="10">'.PHP_EOL;
	      $html .= '<thead>'.PHP_EOL;
		      $html .= '<th>Time</th>'.PHP_EOL;
			    $html .= '<th>Task #1</th>'.PHP_EOL;
			    $html .= '<th>Task #2<th>'.PHP_EOL;
					$html .= '<th>&nbsp;</th>'.PHP_EOL;
		    $html .= '</thead>'.PHP_EOL;
		    $html .= '<tbody>'.PHP_EOL;
	        for( $i = $start; $i <= $end; $i += $inc ){
            $range = date( 'g:i', $i ).' - '.date( 'g:i A', $i + $inc );
			      $html .= '<tr>'.PHP_EOL;
				      $sql = "SELECT COUNT(*) FROM ".$wpdb->prefix."wp_timesheets_tasks WHERE user_id='".$current_user->ID."' AND created_date='".$the_date."' AND created_time >= '".date('H:i:s', $i)."' AND created_time < '".date( 'H:i:s', $i + $inc )."' LIMIT 2";
              $html .= '<td width="15%">'.$range.'</td>'.PHP_EOL;
					    $task_count = $wpdb->get_var($sql);
					    if ($task_count >= 1){
					      $sql = "SELECT * FROM ".$wpdb->prefix."wp_timesheets_tasks WHERE user_id='".$current_user->ID."' AND created_date='".$the_date."' AND created_time >= '".date('H:i:s', $i)."' AND created_time < '".date( 'H:i:s', $i + $inc )."' LIMIT 2";
					      $tasks = $wpdb->get_results($sql);
						    $loop_count = 0;
		            foreach ( (array) $tasks as $task ){
								  $sql = "SELECT color_code FROM ".$wpdb->prefix."wp_timesheets_types WHERE id='".$task->type_id."' AND active='1' AND locked='0'";
									$color_code = $wpdb->get_var($sql);
								  if ($color_code != 'FFFFFF'){
									  $html .= '<td style="background-color: #'.$color_code.';">';
									} else {
									  $html .= '<td>';
									}
									  $html .= '<input type="hidden" name="existing_task['.$task->id.'][id]" value="'.$task->id.'">';
										if (strtotime($task->completed_date) <= 0){
										  $html .= '<input type="text" name="existing_task['.$task->id.'][description]" value="'.$task->description.'" title="'.$task->created_date.' '.$task->created_time.'" style="width: 80%; background: url('.plugin_dir_url( __FILE__ ).'../img/loading.gif) no-repeat; background-position: right 10px center;">';
										} else {
										  $html .= '<input type="text" name="existing_task['.$task->id.'][description]" value="'.$task->description.'" title="'.$task->created_date.' '.$task->created_time.'" style="width: 80%;">';
										}
										//Begin Working
										/*
										$html .= '&nbsp;<a class="fancybox" href="#wp_timesheets_edit_'.$task->id.'">Edit</a>';
										$popup_html .= '<div class="fancybox-hidden" style="display: none;">'.PHP_EOL;
									    $popup_html .= '<div id="wp_timesheets_edit_'.$task->id.'" style="padding: 20px;">'.PHP_EOL;
											  $popup_html .= do_shortcode('[wp_timesheets_edit id='.$task->id.']');
										  $popup_html .= '</div>'.PHP_EOL;
									  $popup_html .= '</div>'.PHP_EOL;
										*/
										//End Working
										
										//Begin Also Working
										$html .= '&nbsp;<input alt="#TB_inline?height=300&amp;width=400&amp;inlineId=wp_timesheets_edit_'.$task->id.'" title="" class="thickbox" type="button" value="Edit" style="width: 15%;" />'.PHP_EOL;
										$popup_html .= '<div id="wp_timesheets_edit_'.$task->id.'" style="padding: 20px; display: none;">'.PHP_EOL;
										  $popup_html .= do_shortcode('[wp_timesheets_edit id='.$task->id.']');
										$popup_html .= '</div>'.PHP_EOL;
										//End Also Working
									$html .= '</td>'.PHP_EOL;
							    $loop_count++;
					      }
						    if ($loop_count == 1){
						      $html .= '<td>'.PHP_EOL;
									  $html .= '<input type="hidden" name="new_task['.$new_task_count.'][created_date]" value="'.$the_date.'">'.PHP_EOL;
										$html .= '<input type="hidden" name="new_task['.$new_task_count.'][created_time]" value="'.date('H:i:s', $i).'">'.PHP_EOL;
										$html .= '<input type="hidden" name="new_task['.$new_task_count.'][new]" value="1">'.PHP_EOL;
										$html .= '<input type="text" name="new_task['.$new_task_count.'][description]">'.PHP_EOL;
								  $html .= '</td>'.PHP_EOL;
										
							    $new_task_count++;
						    }
					    } else {
					      $html .= '<td>'.PHP_EOL;
									$html .= '<input type="hidden" name="new_task['.$new_task_count.'][created_date]" value="'.$the_date.'">'.PHP_EOL;
									$html .= '<input type="hidden" name="new_task['.$new_task_count.'][created_time]" value="'.date('H:i:s', $i).'">'.PHP_EOL;
								  $html .= '<input type="text" name="new_task['.$new_task_count.'][description]">'.PHP_EOL;
								$html .= '</td>'.PHP_EOL;
						    $new_task_count++;
						    $html .= '<td>'.PHP_EOL;
									$html .= '<input type="hidden" name="new_task['.$new_task_count.'][created_date]" value="'.$the_date.'">'.PHP_EOL;
									$html .= '<input type="hidden" name="new_task['.$new_task_count.'][created_time]" value="'.date('H:i:s', $i).'">'.PHP_EOL;
									$html .= '<input type="hidden" name="new_task['.$new_task_count.'][new]" value="1">'.PHP_EOL;
								  $html .= '<input type="text" name="new_task['.$new_task_count.'][description]">'.PHP_EOL;
								$html .= '</td>'.PHP_EOL;
						    $new_task_count++;
					    }
							$html .= '<td><input type="hidden" name="action" value="update-timesheets"><input type="submit" value="Save"></td>'.PHP_EOL;
			      $html .= '</tr>'.PHP_EOL;
						
          }
		    $html .= '</tbody>'.PHP_EOL;
	    $html .= '</table>'.PHP_EOL;
	    $html .= '<table>'.PHP_EOL;
	      $html .= '<thead>'.PHP_EOL;
			    $html .= '<th><input type="hidden" name="action" value="update-timesheets"><input type="submit" value="Save"></th>'.PHP_EOL;
		    $html .= '</thead>'.PHP_EOL;
	    $html .= '</table>'.PHP_EOL;
	  $html .= '</form>'.PHP_EOL;
		$html .= $popup_html;
?>
