<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.smashstack.com/
 * @since      1.0.0
 *
 * @package    Smashstack_Csv_Importer
 * @subpackage Smashstack_Csv_Importer/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="csv-import-wrapper">
	<div class="container">
		<h1>Import CSV to Post</h1>

		<form action="" method="POST" class="form" id="csv-import-form" enctype="multipart/form-data">
		    <div class="file-upload-wrapper" data-text="Upload CSV!">
		      	<input name="csv_input_file" type="file" class="file-upload-field" value="">
		      	<input type="hidden" name="action" value="insert_csv_to_post">
		      	<input name="post_type" type="hidden" value="<?php echo $this->post_type; ?>">
		    </div>
		    <div class="post-fields">
		    	<h3>Post Fields</h3>
		    	<p class="col-title"><span>Headers</span><span class="pull-right">Post Fields</span></p>
		    	<div class="post-field-wrp">
		    		<div class="field-line" data-field="field1">
			    		<input type="text" name="p_fields[key]">
			    		<span><---></span>
			    		<select name="p_fields[value]">
			    			<option value="post_author">Author</option>
			    			<option value="post_date">Date Published</option>
			    			<option value="post_content">Content</option>
			    			<option value="post_title">Title</option>
			    			<option value="post_status">Status</option>
			    			<option value="post_name">Name (URL/Slug)</option>
			    			<option value="post_modified">Date Modified</option>
			    			<option value="post_category">Category</option>
			    			<option value="tags_input">Tags</option>
			    		</select>
			    		<a href="#" class="remove-condition">x</a>
			    	</div>
		    	</div>
		    	<a href="#" class="add_field_condition" data-count="1">Add Condition</a>
		    </div>
		    <br>
		    <br>
		    <div class="custom-fields">
		    	<h3>Custom Fields Settings</h3>
		    	<p class="col-title"><span>Headers</span><span class="pull-right">Post Fields</span></p>
		    	<div class="custom-field-wrp">
		    		<div class="field-line" data-field="field1">
			    		<input type="text" name="c_fields[key]">
			    		<span><---></span>
			    		<select name="c_fields[value]">
			    			<?php $fields = $this->get_custom_fields_by_post_type(); 
			    			foreach ($fields as $key => $value) {
			    				echo '<option value="'. $value["id"] .'">'. $value["name"] .'</option>';
			    			} ?>
			    		</select>
			    		<a href="#" class="remove-condition">x</a>
			    	</div>
		    	</div>
		    	<a href="#" class="add_field_condition" data-count="1">Add Condition</a>
		    </div>
		</form>

		<div id="progress-wrp">
		    <div class="progress-bar"></div>
		    <div class="status">0%</div>
		</div>		
		<div class="csv-upload-status">
		</div>
	</div>
</div>