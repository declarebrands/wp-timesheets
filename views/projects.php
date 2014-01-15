<?php
        $html .= '<h1>Add Project</h1>'.PHP_EOL;
				$new_project_count = 0;
				$popup_html = '';
        $html .= '<form name="wp_timesheets_projects" method="post" action=".">'.PHP_EOL;
				  $html .= '<table>'.PHP_EOL;
					  $html .= '<tr>'.PHP_EOL;
						  $html .= '<td>'.PHP_EOL;
				        $html .= '<input type="text" name="new_project['.$new_project_count.'][name]">'.PHP_EOL;
								//$html .= '<input class="color" type="text" name="new_project['.$new_project_count.'][color_code]">'.PHP_EOL;
					      $html .= '<input type="hidden" name="action" value="update-projects">'.PHP_EOL;
								$html .= '<input type="hidden" name="view" value="projects">'.PHP_EOL;
					      $html .= '<input type="submit" value="Save">'.PHP_EOL;
								$new_project_count++;
							$html .= '</td>'.PHP_EOL;
						$html .= '</tr>'.PHP_EOL;
					$html .= '</table>'.PHP_EOL;
				$html .= '</form>'.PHP_EOL;
				$sql = "SELECT COUNT(*) FROM ".$wpdb->prefix."wp_timesheets_projects WHERE active='1' AND user_id='".$current_user->ID."'";
				$project_count = $wpdb->get_var($sql);
				if ($project_count >= 1){
				  $html .= '<table>'.PHP_EOL;
				    $html .= '<thead>'.PHP_EOL;
					    $html .= '<th>Name</th>'.PHP_EOL;
							$html .= '<th>Description</th>'.PHP_EOL;
						  $html .= '<th>Actions</th>'.PHP_EOL;	
					  $html .= '</thead>'.PHP_EOL;
						$sql = "SELECT * FROM ".$wpdb->prefix."wp_timesheets_projects WHERE active='1' AND user_id='".$current_user->ID."' ORDER BY name ASC";
						$projects = $wpdb->get_results($sql);
						foreach ($projects as $project){
						  $html .= '<tr>'.PHP_EOL;
							  $html .= '<td>'.$project->name.'</td>'.PHP_EOL;
								$html .= '<td>'.$project->description.'</td>'.PHP_EOL;
								$html .= '<td>'.PHP_EOL;
								  if ($project->locked == 0){
									  $html .= '<input onclick="window.location.href=';
										$html .= "'./?view=".$view."&id=".$project->id."&action=disable-projects'";
										$html .= '" value="Disable" type="button">';
									} else {
									  $html .= '<input onclick="window.location.href=';
										$html .= "'./?view=".$view."&id=".$project->id."&action=enable-projects'";
										$html .= '" value="Enable" type="button">';
									}
									$html .= '<input onclick="window.location.href=';
										$html .= "'./?view=".$view."&id=".$project->id."&action=delete-projects'";
										$html .= '" value="Delete" type="button">';
									$html .= '&nbsp;<input alt="#TB_inline?height=300&amp;width=400&amp;inlineId=wp_timesheets_projects_edit_'.$project->id.'" title="" class="thickbox" type="button" value="Edit" />'.PHP_EOL;
								$html .= '</td>'.PHP_EOL;
							$html .= '</tr>'.PHP_EOL;
							$popup_html .= '<div id="wp_timesheets_projects_edit_'.$project->id.'" style="padding: 20px; display: none;">'.PHP_EOL;
							  $popup_html .= do_shortcode('[wp_timesheets_projects_edit id='.$project->id.']');
							$popup_html .= '</div>'.PHP_EOL;
						}
				  $html .= '</table>'.PHP_EOL;
				}
				$html .= $popup_html;
?>
