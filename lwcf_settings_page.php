<table width="100%">						<tr><th><h2><?php echo TOIT_PLUGIN_TITLE; ?></h2></th></tr>	<tr><th>Version <?php echo TOIT_CURRENT_VERSION; ?>. Developed by ThinkOverIT (<a href="http://thinkoverit.com">http://thinkoverit.com</a>)</th></tr>	<tr><td><hr /></td></tr></table><table width="100%" class="toi-cont-table"><tr>	<td>		<table width="100%" >			<tr><th colspan="4" align="left"><h3>Your Forms</h3></th></tr><?php 	$forms = get_all_contact_forms();	if($forms){ 		foreach($forms as $form){?>				<tr>					<td>[thinkit-wp-contact-form <?php echo $form['form_id']; ?>]</td>					<td><a href="<?php echo toitcf_admin_url(array("toitcf_current_id"=>$form['form_id'])); ?>">Edit</a></td>					<td><a onclick="var r = confirm('Are you sure you want to delete this form?'); if(r== true) return true; else return false;"href="<?php echo toitcf_admin_url(array("toitcf_current_id"=>$form['form_id'], "action"=>"delete")); ?>" >Delete</a></form></td>						<td>Please use short-code <strong>[thinkit-wp-contact-form <?php echo $form['form_id']; ?>]</strong> in page or post.</td>								</tr>				<tr><td></td></tr>				<tr><td colspan="3">Theme builders: You can use 'echo do_shortcode("[thinkit-wp-contact-form *]");' in your theme files. '*' shall be replaced by respective form Id.</td></tr><?php }}else{  ?>		<tr><td colspan="3">No forms created yet.</td></tr><?php } ?>			</table>		</td>	</tr>	<tr><td><hr /></td></tr></table><form id="form_toitcf_create" method="post" action=""><?php if ( toitcf_has_admin_edit_cap() ) wp_nonce_field( 'toitcf_add_update_' . $toitcf_current_id ); ?>		<table width="100%" class="toi-cont-table"><tr>	<td><?php		$toitcf_fields_count= 4;		if(empty($toitcf_current_id)){			$edit_mode = false;		}else {			$edit_mode = true;			$form = get_contact_form($toitcf_current_id);			$toitcf_fields_count = count($form['fields']);		}?><input type="hidden" name="toitcf_fields_count" id="toitcf_fields_count" value="<?php echo $toitcf_fields_count; ?>" /><input type="hidden" name="toitcf_current_id" id="toitcf_current_id" value="<?php echo $toitcf_current_id; ?>" />		<table width="100%" id="toit-form-table">			<tr>				<th colspan="4" align="left"><h3><?php if(!empty($toitcf_current_id)) echo 'Edit form [thinkit-wp-contact-form '.$toitcf_current_id.']'; else echo 'Create new form';  ?></h3></th>							</tr>			<tr>				<td>Field Label</td>				<td>Field Type</td>				<td>Placeholder</td>				<td>Class</td>				<td>Required?</td>				<td>Order</td>							</tr>			<tr>				<td><input type="text" name="toitcf_label1" value="Full Name" /></td>				<td><select name="toitcf_field1" id=""><option value="textbox">TextBox</option></select></td>				<td><input type="text" name="toitcf_placeholder1"  value="<?php if(isset($form['fields'][0]['placeholder'])) echo $form['fields'][0]['placeholder']; ?>" /></td>				<td><input type="text" name="toitcf_class1" value="<?php if(isset($form['fields'][0]['class'])) echo $form['fields'][0]['class']; ?>" /></td>				<td><input type="checkbox" name="toitcf_required1" <?php if($form['fields'][0]['required']=="on") echo 'checked="checked"'; ?>/></td>				<td><select name="toitcf_order1"><?php 	for($i=1; $i<=$toitcf_fields_count;$i++){			if($edit_mode && $form['fields'][0]['order'] == $i) 				$selected= 'selected="selected"';			else $selected= '';			echo '<option '.$selected.' value="'.$i.'">'.$i.'</option>';		}?>				</select></td>				</td><td>&nbsp;</td>			</tr>			<tr>				<td><input type="text" name="toitcf_label2" value="Email" /></td>				<td><select name="toitcf_field2"><option value="email">Email</option></select></td>				<td><input type="text" name="toitcf_placeholder2" value="<?php if(isset($form['fields'][1]['placeholder'])) echo $form['fields'][1]['placeholder']; ?>" /></td>				<td><input type="text" name="toitcf_class2" value="<?php if(isset($form['fields'][1]['class'])) echo $form['fields'][1]['class']; ?>" /></td>				<td><input type="checkbox" name="toitcf_required2" <?php if($form['fields'][1]['required']=="on") echo 'checked="checked"'; ?>/></td>				<td><select name="toitcf_order2"><?php 	for($i=1; $i<=$toitcf_fields_count;$i++){			if($edit_mode && $form['fields'][1]['order'] == $i) 				$selected= 'selected="selected"';			else $selected= '';			echo '<option '.$selected.' value="'.$i.'">'.$i.'</option>';		}?>				</select></td>				</td><td>&nbsp;</td>			</tr>			<tr>				<td><input type="text" name="toitcf_label3" value="Message" /></td>				<td><select name="toitcf_field3">					<option value="textbox" <?php if($form['fields'][2]['field'] == 'textbox') echo 'selected="selected"'; ?> >TextBox</option>					<option value="textarea" <?php if($form['fields'][2]['field'] == 'textarea') echo 'selected="selected"'; ?> >TextArea</option>					</select></td>				<td><input type="text" name="toitcf_placeholder3" value="<?php if(isset($form['fields'][2]['placeholder'])) echo $form['fields'][2]['placeholder']; ?>" /></td>				<td><input type="text" name="toitcf_class3" value="<?php if(isset($form['fields'][2]['class'])) echo $form['fields'][2]['class']; ?>" /></td>				<td><input type="checkbox" name="toitcf_required3" <?php if($form['fields'][2]['required']=="on") echo 'checked="checked"'; ?>/></td>				<td><select name="toitcf_order3"><?php 	for($i=1; $i<=$toitcf_fields_count;$i++){			if($edit_mode && $form['fields'][2]['order'] == $i) 				$selected= 'selected="selected"';			else $selected= '';			echo '<option '.$selected.' value="'.$i.'">'.$i.'</option>';		}?>				</select></td>				</td><td>&nbsp;</td>			</tr>			<tr>				<td><input type="text" name="toitcf_label4" value="<?php if($edit_mode) echo $form['fields'][3]['label']; else echo 'Send'; ?>" /></td>				<td><input type="button" name="sample" value="Button" /><input type="hidden" name="toitcf_field4" value="button" /></td>				<td>&nbsp;</td>				<td><input type="text" name="toitcf_class4" value="<?php if(isset($form['fields'][3]['class'])) echo $form['fields'][3]['class']; ?>" /></td>				<td><input type="hidden" name="toitcf_required4" value="on"/></td>				<td><select name="toitcf_order4"><?php 	for($i=1; $i<=$toitcf_fields_count;$i++){			if($edit_mode && $form['fields'][3]['order'] == $i) 				$selected= 'selected="selected"';			else $selected= '';			echo '<option '.$selected.' value="'.$i.'">'.$i.'</option>';		}?>				</select></td>				</td><td>&nbsp;</td>			</tr>			<?php for($j=5;$j<=$toitcf_fields_count;$j++){  			$toitcf_field= $form['fields'][$j-1]['field'];			$toitcf_required= $form['fields'][$j-1]['required'];?>				<tr><td><input type="text" name="toitcf_label<?php echo $j; ?>" value="<?php if($edit_mode) echo $form['fields'][$j-1]['label']; ?>" /></td>				<td><select name="toitcf_field<?php echo $j; ?>" id="">					<option value="textbox" <?php if($toitcf_field == 'textbox') echo 'selected="selected"'; ?> >TextBox</option>					<option value="textarea" <?php if($toitcf_field == 'textarea') echo 'selected="selected"'; ?> >TextArea</option>					<option value="email" <?php if($toitcf_field == 'email') echo 'selected="selected"'; ?> >Email</option>					<option value="url" <?php if($toitcf_field == 'url') echo 'selected="selected"'; ?> >Url</option>					<option value="checkbox" <?php if($toitcf_field == 'checkbox') echo 'selected="selected"'; ?> >CheckBox</option>					</select>				</td>				<td><input type="text" name="toitcf_placeholder<?php echo $j; ?>" value="<?php if(isset($form['fields'][$j-1]['placeholder'])) echo $form['fields'][$j-1]['placeholder']; ?>" /></td>				<td><input type="text" name="toitcf_class<?php echo $j; ?>" value="<?php if(isset($form['fields'][$j-1]['class'])) echo $form['fields'][$j-1]['class']; ?>" /></td>				<td><input type="checkbox" name="toitcf_required<?php echo $j; ?>" <?php if($toitcf_required=="on") echo 'checked="checked"'; ?>/></td>				<td><select name="toitcf_order<?php echo $j; ?>">					<?php 	for($i=1; $i<=$toitcf_fields_count;$i++){			if($edit_mode && $form['fields'][$j-1]['order'] == $i) 				$selected= 'selected="selected"';			else $selected= '';			echo '<option '.$selected.' value="'.$i.'">'.$i.'</option>';		}?>				</select></td><td>&nbsp;</td></tr><?Php } ?>		</table>	</td></tr><tr>	<td colspan="4"><a onclick="toitcf_add_field();return false;" href="javascript:void(0);">Add Another Field</a></td></tr><tr>	<td><hr /></td></tr><tr>	<td>		<table>			<tr>				<th colspan="4" align="left"><h3>Admin Settings for this form</h3></th>							</tr>			<tr>				<td>Email:</td>				<td><input type="text" name="toitcf_form_email" placeholder="admin@something.com" value="<?php if($edit_mode) echo $form['email']; else echo get_bloginfo('admin_email'); ?>" /> <br /> (Emails will be sent to this address. Comma-separated.)</td>															</tr>			<tr>				<td>Subject:</td>				<td><input type="text" name="toitcf_form_subject" placeholder="Query from site" value="<?php if($edit_mode) echo $form['subject'];else echo 'Query from site '.get_bloginfo('wpurl'); ?>" /> <br /> (Subject of the Email, that will be sent to above address)</td>				</tr>			<tr>				<td>Email Body:</td>				<td>				  <input type="text" name="toitcf_form_message_top" value="<?php if($edit_mode) echo $form['top_message']; ?>" /> <br />				  <em>(Add any text on top of the email message)</em> <br /><br />				  <div class="email_body">					{Field Label} : {Field value} <br />					{Field Label} : {Field value} <br />				  </div>						  (The email will contain the message in above format using the contact form data)<br /><br />				  <input type="text" name="toitcf_form_message_bottom"  value="<?php if($edit_mode) echo $form['bottom_message']; ?>" /> <br />				  (Add any text on bottom of the email message)				</td>															</tr>			<tr><td>					<p class="submit">					<input type="submit" name="toit-add-update" class="button-primary" value="<?php if($edit_mode) _e('Update'); else _e('Save'); ?>" />				</p>				</td>				</tr>		</table>	</td></tr><tr>	<td><hr /></td></tr></table></form><script>var toitcf_fields_count= <?php echo $toitcf_fields_count; ?>;function toitcf_add_field(){	var oldfields = toitcf_fields_count++;			for(i=1; i<=oldfields; i++){		var id= 'toitcf_order'+i;		jQuery('select[name="'+id+'"]').append('<option value="'+toitcf_fields_count+'" >'+toitcf_fields_count+'</option>');	}	$html = '<tr id="element'+toitcf_fields_count+'"><td><input type="text" name="toitcf_label'+toitcf_fields_count+'" value="" /></td><td><select name="toitcf_field'+toitcf_fields_count+'"><option value="textbox">TextBox</option><option value="textarea">TextArea</option><option value="email">Email</option><option value="url">Url</option><option value="checkbox">CheckBox</option></select></td><td><input type="text" name="toitcf_placeholder'+toitcf_fields_count+'" value="" /></td><td><input type="text" name="toitcf_class'+toitcf_fields_count+'" value="" /></td><td><input type="checkbox" name="toitcf_required_'+toitcf_fields_count+'" /></td><td><select name="toitcf_order'+toitcf_fields_count+'">';	for(i=1; i<=toitcf_fields_count; i++){		if(i==toitcf_fields_count) var selected = 'selected="selected"';		else var selected = '';		$html += '<option value="'+i+'" '+selected+'>'+i+'</option>';	}	$html += '</select></td><td><span onclick="removeElement('+toitcf_fields_count+');">Remove</span></td></tr>';		jQuery("#toit-form-table").append($html);		jQuery("#toitcf_fields_count").val(toitcf_fields_count);	}function removeElement(id){	jQuery("#element"+id).remove();}</script>