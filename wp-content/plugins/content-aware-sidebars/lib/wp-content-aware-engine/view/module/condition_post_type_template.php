<?php
/**
 * @package WP Content Aware Engine
 * @copyright Joachim Jensen <jv@intox.dk>
 * @license GPLv3
 */
?>
<script type="text/template" id="wpca-template-<?php echo $id."-".$post_type; ?>">
	<div class='cas-group-label'>
		<span class='js-wpca-condition-remove wpca-condition-remove dashicons dashicons-trash'></span>
		<%= label %>
	</div>
	<div class="cas-group-input">
		<select style="width:100%;" class="js-wpca-suggest" multiple="multiple" name="cas_condition[<?php echo $id; ?>][]" data-wpca-placeholder="<?php echo $placeholder; ?>" data-wpca-default="<?php echo $post_type; ?>"></select>
		<p>
			<label>
				<input type="checkbox" name="cas_condition[<?php echo $id; ?>][]" value="<?php echo $autoselect; ?>" <%= _.has(options,"<?php echo $autoselect; ?>") ? 'checked="checked"' : '' %> />
				<?php _e('Automatically add new children of a selected ancestor', WPCACore::DOMAIN); ?>
			</label>
		</p>
	</div>
	<div class="cas-group-sep"><?php _e("And",WPCACore::DOMAIN); ?></div>
</script>
