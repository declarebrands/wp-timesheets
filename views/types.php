<?php
        $html .= '<h1>Add Task Type</h1>'.PHP_EOL;
				$new_type_count = 0;
				$popup_html = '';
        $html .= '<form name="wp_timesheets_types" method="post" action=".">'.PHP_EOL;
				  $html .= '<table>'.PHP_EOL;
					  $html .= '<tr>'.PHP_EOL;
						  $html .= '<td>'.PHP_EOL;
				        $html .= '<input type="text" name="new_type['.$new_type_count.'][name]">'.PHP_EOL;
								$html .= '<input class="color ';
								$html .= "{pickerClosable:true, pickerPosition:'top'}";
								$html .= '" type="text" name="new_type['.$new_type_count.'][color_code]">'.PHP_EOL;
					      $html .= '<input type="hidden" name="action" value="update-types">'.PHP_EOL;
								$html .= '<input type="hidden" name="view" value="types">'.PHP_EOL;
					      $html .= '<input type="submit" value="Save">'.PHP_EOL;
								$new_type_count++;
							$html .= '</td>'.PHP_EOL;
						$html .= '</tr>'.PHP_EOL;
					$html .= '</table>'.PHP_EOL;
				$html .= '</form>'.PHP_EOL;
				$sql = "SELECT COUNT(*) FROM ".$wpdb->prefix."wp_timesheets_types WHERE active='1' AND user_id='".$current_user->ID."'";
				$type_count = $wpdb->get_var($sql);
				if ($type_count >= 1){
				  $html .= '<table>'.PHP_EOL;
				    $html .= '<thead>'.PHP_EOL;
					    $html .= '<th>Name</th>'.PHP_EOL;
							$html .= '<th>Description</th>'.PHP_EOL;
							$html .= '<th>Color</th>'.PHP_EOL;
						  $html .= '<th>Actions</th>'.PHP_EOL;	
					  $html .= '</thead>'.PHP_EOL;
						$sql = "SELECT * FROM ".$wpdb->prefix."wp_timesheets_types WHERE active='1' AND user_id='".$current_user->ID."' ORDER BY name ASC";
						$types = $wpdb->get_results($sql);
						foreach ($types as $type){
						  $html .= '<tr>'.PHP_EOL;
							  $html .= '<td>'.$type->name.'</td>'.PHP_EOL;
								$html .= '<td>'.$type->description.'</td>'.PHP_EOL;
								$html .= '<td><font color="#'.$type->color_code.'">'.$type->color_code.'</font></td>'.PHP_EOL;
								$html .= '<td>'.PHP_EOL;
								  if ($project->locked == 0){
									  $html .= '<input onclick="window.location.href=';
										$html .= "'./?view=".$view."&id=".$type->id."&action=disable-types'";
										$html .= '" value="Disable" type="button">';
									} else {
									  $html .= '<input onclick="window.location.href=';
										$html .= "'./?view=".$view."&id=".$type->id."&action=enable-types'";
										$html .= '" value="Enable" type="button">';
									}
									$html .= '<input onclick="window.location.href=';
										$html .= "'./?view=".$view."&id=".$type->id."&action=delete-types'";
										$html .= '" value="Delete" type="button">';
									$html .= '&nbsp;<input alt="#TB_inline?height=300&amp;width=400&amp;inlineId=wp_timesheets_types_edit_'.$type->id.'" title="" class="thickbox" type="button" value="Edit" />'.PHP_EOL;
								$html .= '</td>'.PHP_EOL;
							$html .= '</tr>'.PHP_EOL;
							$popup_html .= '<div id="wp_timesheets_types_edit_'.$type->id.'" style="padding: 20px; display: none;">'.PHP_EOL;
							  $popup_html .= do_shortcode('[wp_timesheets_types_edit id='.$type->id.']');
							$popup_html .= '</div>'.PHP_EOL;
						}
				  $html .= '</table>'.PHP_EOL;
				}
				$html .= $popup_html;
?>
