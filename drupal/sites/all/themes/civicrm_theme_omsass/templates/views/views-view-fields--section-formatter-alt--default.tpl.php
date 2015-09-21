<?php

/**
 * @file
 * Default simple view template to all the fields as a row.
 *
 * - $view: The view in use.
 * - $fields: an array of $field objects. Each one contains:
 *   - $field->content: The output of the field.
 *   - $field->raw: The raw data for the field, if it exists. This is NOT output safe.
 *   - $field->class: The safe class id to use.
 *   - $field->handler: The Views field handler object controlling this field. Do not use
 *     var_export to dump this object, as it can't handle the recursion.
 *   - $field->inline: Whether or not the field should be inline.
 *   - $field->inline_html: either div or span based on the above flag.
 *   - $field->wrapper_prefix: A complete wrapper containing the inline_html to use.
 *   - $field->wrapper_suffix: The closing tag for the wrapper.
 *   - $field->separator: an optional separator that may appear before a field.
 *   - $field->label: The wrap label text to use.
 *   - $field->label_html: The full HTML of the label to use including
 *     configured element type.
 * - $row: The raw result object from the query, with all data it fetched.
 *
 * @ingroup views_templates
 */
$excluded_fields = array('field_image','field_section_image_position','field_color');
$inlineStyles = array();
$classes = array();


if (isset($fields['field_color']->content))
	$classes[] = sprintf('l-region--%s', $fields['field_color']->content);

if (isset($fields['field_section_image_position']) && $fields['field_section_image_position']->content == 'background') {
	preg_match('/src="([^"]*)"/',$fields['field_image']->content, $matches);
	$imageUrl = $matches[1];
	$inlineStyles = array_merge($inlineStyles, array("background: url($imageUrl) no-repeat top left",'background-size: cover','color: #fff'));
}
?>

<div class="section <?php (empty($classes)) ?: print(implode(' ',$classes)); ?>" <?php (empty($inlineStyles)) ?: printf('style="%s"',implode('; ',$inlineStyles)); ?>>
	<div class="section-inner">
	<?php foreach ($fields as $id => $field): ?>
	  <?php if (in_array($id, $excluded_fields)) continue; ?>
	  <?php if (!empty($field->separator)): ?>
	    <?php print $field->separator; ?>
	  <?php endif; ?>

	  <?php print $field->wrapper_prefix; ?>
	    <?php print $field->label_html; ?>
	    <?php print $field->content; ?>
	  <?php print $field->wrapper_suffix; ?>
	<?php endforeach; ?>
	</div>
</div>