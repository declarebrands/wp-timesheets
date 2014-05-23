<?php
    if ( isset($debugMode) && $debugMode >= 1 ){
	    $html .= '<p>sql: '.$sql.'</p>'.PHP_EOL;
		  $html .= '<p>user_id: '.$current_user->ID.'</p>'.PHP_EOL;
	    $html .= '<p>Tasks: '.$task_count.'</p>'.PHP_EOL;
	  }
		$popup_html = '';
	  $inc = 15 * 60;
    $start = strtotime('8AM'); // 8AM
    $end = strtotime('5PM'); // 6PM
		if ( (isset($_GET['date'])) ){
		  $the_date = $_GET['date'];
		} else {
		  $the_date = date('Y-m-d');
		}
		$ts = strtotime($the_date);
		$year = date('o', $ts);
		$week = date('W', $ts);
		$html .= '<ul id="center-nav">'.PHP_EOL;
		  $html .= '<li class="action"><a href="./?view='.$view.'&date='.date("Y-m-d", strtotime("+1 year", $ts)).'">&rarr;</a></li>'.PHP_EOL;
			$html .= '<li class="action"><a href="./?view='.$view.'&date='.date("Y-m-d").'">Today</a></li>'.PHP_EOL;
			$html .= '<li class="action"><a href="./?view='.$view.'&date='.date("Y-m-d", strtotime("-1 year", $ts)).'">&larr;</a></li>'.PHP_EOL;
		  for($m = 1;$m <= 12; $m++){
		    $month =  date("F", mktime(0, 0, 0, $m));
			  if ( strlen($m) == 1 ){
			    $m = '0'.$m;
			  }
			  if ( date("F", $ts) == $month ){
          $html .= '<li class="active"><a href="./?view='.$view.'&date='.$year.'-'.$m.'-01">'.$month.'</a></li>'.PHP_EOL;
			  } else {
			    $html .= '<li><a href="./?view='.$view.'&date='.$year.'-'.$m.'-01">'.$month.'</a></li>'.PHP_EOL;
			  }
      } 
		$html .= '</ul>'.PHP_EOL;
		$html .= '<ul id="center-nav">'.PHP_EOL;
			$html .= '<li class="action"><a href="./?view='.$view.'&date='.date("Y-m-d", strtotime("+7 day", $ts)).'">&rarr;</a></li>'.PHP_EOL;
			$html .= '<li class="action"><a href="./?view='.$view.'&date='.date("Y-m-d").'">Today</a></li>'.PHP_EOL;
			$html .= '<li class="action"><a href="./?view='.$view.'&date='.date("Y-m-d", strtotime("-7 day", $ts)).'">&larr;</a></li>'.PHP_EOL;
		  for ($i = 1; $i <= 7; $i++){
		    $ts = strtotime($year.'W'.$week.$i);
			  if ( date("Y-m-d", $ts) == $the_date ){
			    $html .= '<li class="active"><a href="./?view='.$view.'&date='.date("Y-m-d", $ts).'">'.date("l (d)", $ts).'</a></li>'.PHP_EOL;
			  } else {
			    $html .= '<li><a href="./?view='.$view.'&date='.date("Y-m-d", $ts).'">'.date("l (d)", $ts).'</a></li>'.PHP_EOL;
			  }
		  }
		$html .= '</ul>'.PHP_EOL;
		$html .= '<h1>'.$current_user->first_name.' '.$current_user->last_name.'</h1>'.PHP_EOL;
		if ( (isset($_GET['date'])) ){
		  $the_date = $_GET['date'];
		} else {
		  $the_date = date('Y-m-d');
		}
		$ts = strtotime($the_date);
		$html .= '<h3 style="margin: 0 0 10px 0; font-size: 0.8em;">For the week of '.date("F", $ts).' '.date("d", $ts);
		
		if ( (date("F", strtotime("+6 day", $ts))) != (date("F", $ts)) ){
		  $html .= ' to '.date("F", strtotime("+6 day", $ts)).' '.date("d", strtotime("+6 day", $ts)).'</h3>'.PHP_EOL;
		} else {
		  $html .= ' to '.date("d", strtotime("+6 day", $ts)).'</h3>'.PHP_EOL;
		}
		
		$html .= '<table style="margin: 0;">'.PHP_EOL;
		$popup_html .= '<table style="margin: 0;">'.PHP_EOL;
		  $html .= '<tr>'.PHP_EOL;
			$popup_html .= '<tr>'.PHP_EOL;
		    for ($i = 1; $i <= 5; $i++){
				  $total_time = 0;
				  $html .= '<td width="20%">'.PHP_EOL;
					$popup_html .= '<td width="20%">'.PHP_EOL;
		        $ts = strtotime($year.'W'.$week.$i);
			      $today = date("Y-m-d", $ts);
						$the_day = date("D (d)", $ts);
						$html .= '<h4 style="margin: 0; color: #000;">'.$the_day.'</h4>'.PHP_EOL;
		        $sql = "SELECT COUNT(*) FROM ".$wpdb->prefix."wp_timesheets_tasks WHERE user_id='".$current_user->ID."' AND active='1' AND locked='0' AND created_date='".$today."'";
						$task_count = $wpdb->get_var($sql);
						$count = 0;
						if ($task_count >= 1){
						  $sql = "SELECT * FROM ".$wpdb->prefix."wp_timesheets_tasks WHERE user_id='".$current_user->ID."' AND active='1' AND locked='0' AND created_date='".$today."'";
							$results = $wpdb->get_results($sql);
							foreach ($results as $tasks){
							  $count++;
							  $start_time = strtotime($tasks->created_time);
								if ($tasks->completed_time == '00:00:00'){
								  $end_time = $end;
								} else {
								  $end_time = strtotime($tasks->completed_time);
								}
								$run_time = $end_time - $start_time;
								$total_time = $total_time + $run_time;
								$run_time = date("H:i:s", $run_time);
							  $html .= '<font size="0.9em" color="#000"><strong>'.$run_time.'</strong>&nbsp;'.$tasks->description.'</font><br />';
							}
						}
					$html .= '</td>'.PHP_EOL;
					$popup_html .= '<font color="#000">'.date("H:i:s", $total_time).'</font>';
					$popup_html .= '</td>'.PHP_EOL;
			
		    }
				
			$html .= '</tr>'.PHP_EOL;
			$popup_html .= '</tr>'.PHP_EOL;
		$html .= '</table>'.PHP_EOL;
		$popup_html .= '</table>'.PHP_EOL;
		$html .= $popup_html;
		
?>
