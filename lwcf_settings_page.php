<div class="wrap"><h2><?php print TOIT_PLUGIN_NAME ." ". TOIT_CURRENT_VERSION; ?></h2><?php toit_admin_show_message(); ?><?php if(!empty($toit_current_id)){ ?><a href="<?php echo toit_admin_url(); ?>">Create new form</a><form id="form_toit_create" method="post" action="<?php echo toit_admin_url(); ?>">	<input type="hidden" name="toit_current_id"  value="<?php echo $toit_current_id; ?>" />	<input type="submit" name="toit-delete" class="delete" value="<?php  _e('Delete');  ?>" /></form><p><span>[thinkit-wp-contact-form <?php echo $toit_current_id; ?>]</span> Please add this code in post,page where you want to place this form.</p><?php } ?><form id="form_toit_create" method="post" action="options.php"><?php	settings_fields( 'toit-contact-form-group' ); 		$toit_form_count = get_option("toit_form_count");				$toit_variable_count= 0;		if(empty($toit_current_id)){			$edit_mode = false;			if(empty($toit_form_count)) $toit_form_id=1;			else $toit_form_id = $toit_form_count+1;		}else {			$edit_mode = true;			$toit_form_id = $toit_current_id;			$form = get_contact_form($toit_current_id);			$toit_variable_count = $form['toit_variable_count'];		}?>    <table class="toit-form-table" id="toit-form-table">        <tr valign="top">        <th scope="row">Form Name</th>        <td><input type="text" name="toit_form_name_<?php echo $toit_form_id; ?>" value="<?php if($edit_mode) echo $form['toit_form_name']; ?>" /></td>        <th scope="row">Email</th>        <td><input type="text" name="toit_form_email_<?php echo $toit_form_id; ?>" value="<?php if($edit_mode) echo $form['toit_form_email']; ?>" /></td>        <th scope="row">Subject</th>        <td><input type="text" name="toit_form_subject_<?php echo $toit_form_id; ?>" value="<?php if($edit_mode) echo $form['toit_form_subject']; ?>" /></td>		</tr> <?php for($i=1;$i<=$toit_variable_count;$i++){ 		$toit_label = $form['fields'][$i-1]['toit_label'];		$toit_field = $form['fields'][$i-1]['toit_field'];		$toit_class = $form['fields'][$i-1]['toit_class'];		$toit_required = $form['fields'][$i-1]['toit_required'];?>        <tr valign="top">			<th scope="row">Label</th>			<td><input type="text" name="toit_label_<?php echo $toit_form_id.$i; ?>" value="<?php echo $toit_label; ?>" /></td>			<th scope="row">Type of field</th>			<td>				<select name="toit_field_<?php echo $toit_form_id.$i; ?>">					<option value="textbox" <?php if($toit_field == 'textbox') echo 'selected="selected"'; ?> >TextBox</option>					<option value="email" <?php if($toit_field == 'email') echo 'selected="selected"'; ?> >Email</option>					<option value="url" <?php if($toit_field == 'url') echo 'selected="selected"'; ?> >Url</option>					<option value="checkbox" <?php if($toit_field == 'checkbox') echo 'selected="selected"'; ?> >CheckBox</option>				</select>			</td>			<th scope="row">Class</th>			<td><input type="text" name="toit_class_<?php echo $toit_form_id.$i; ?>" value="<?php echo $toit_class; ?>" /></td>			<th scope="row">Required</th>			<td><input type="checkbox" name="toit_required_<?php echo $toit_form_id.$i; ?>" <?php if($toit_required=="on") echo 'checked="checked"'; ?>/></td> 		</tr><?php	} ?>		<input type="hidden" name="toit_variable_count_<?php echo $toit_form_id; ?>" id="toit_variable_count_<?php echo $toit_form_id; ?>" value="<?php echo get_option("toit_variable_count_".$toit_form_id); ?>" />		<input type="hidden" name="toit_form_count" value="<?php echo ((int) get_option("toit_form_count") +1); ?>" />		<input type="hidden" name="toit_current_id"  value="<?php echo $toit_current_id; ?>" />		<input type="hidden" name="toit_form_id" value="<?php echo $toit_form_id; ?>" />    </table> 	<a onclick="toit_add_field();return false;" href="#">Add Another Field</a>       <p class="submit">		<input type="submit" name="toit-add-update" class="button-primary" value="<?php if($edit_mode) _e('Update Form'); else _e('Add New Form'); ?>" />   </p></form></div><div class="wrap">	<ul><?php 	$forms = get_all_contact_forms();	if($forms){		foreach($forms as $form){ ?>		<li><a href="<?php echo toit_admin_url(array("toit_current_id"=>$form['toit_form_id'])); ?>"><?php echo $form['toit_form_name']; ?></a></li>		<?php }} ?>	</ul></div><script>var toit_variable_count= <?php $toit_variable_count = get_option("toit_variable_count_".$toit_form_id); if(empty($toit_variable_count)) echo 0; else echo $toit_variable_count; ?>;var toit_form_count= <?php echo $toit_form_id; ?>;jQuery(document).ready(function() {});function toit_add_field(){	toit_variable_count++;	jQuery("#toit-form-table").append('<tr valign="top"><th scope="row">Label</th><td><input type="text" name="toit_label_'+toit_form_count+toit_variable_count+'" value="" /><th scope="row">Type of field</th><td><select name="toit_field_'+toit_form_count+toit_variable_count+'"><option value="textbox">TextBox</option><option value="email">Email</option><option value="url">Url</option><option value="checkbox">CheckBox</option></select></td><th scope="row">Class</th><td><input type="text" name="toit_class_'+toit_form_count+toit_variable_count+'" value="" /></td><th scope="row">Required</th><td><input type="checkbox" name="toit_required_'+toit_form_count+toit_variable_count+'" /></td></tr>');	jQuery("#toit_variable_count_<?php echo $toit_form_id; ?>").val(toit_variable_count);}function toit_create_form(){	jQuery("#form_toit_create").show();}</script>