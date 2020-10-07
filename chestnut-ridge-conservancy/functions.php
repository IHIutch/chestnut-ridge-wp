<?php

/**
 * Functions - Child theme custom functions
 */


/*****************************************************************************************************************
Caution: do not remove this or you will lose all the customization capabilities created by Divi Children plugin */
require_once('divi-children-engine/divi_children_engine.php');
/****************************************************************************************************************/


/**
 * Patch to fix Divi issue: Duplicated Predefined Layouts.
 */
remove_action('admin_init', 'et_pb_update_predefined_layouts');
function Divichild_pb_update_predefined_layouts()
{
	if ('on' === get_theme_mod('et_pb_predefined_layouts_updated_2_0')) {
		return;
	}
	if (!get_theme_mod('et_pb_predefined_layouts_added') or ('on' === get_theme_mod('et_pb_predefined_layouts_added'))) {
		et_pb_delete_predefined_layouts();
	}
	et_pb_add_predefined_layouts();
	set_theme_mod('et_pb_predefined_layouts_updated_2_0', 'on');
}
add_action('admin_init', 'Divichild_pb_update_predefined_layouts');

require 'functions/register_custom_post_types.php';

add_filter('acf/pre_save_post', function ($post_id) {
	// print_r($post_id);
	if ($_POST['form_id'] == "auctionItemBid") {
		// print_r($_POST);
		$price_field = 'field_5f7a1575d3ff7';
		$email_field = 'field_5f7b23cc208d3';
		$name_field = 'field_5f7d26936b718';

		$item_id = $_POST['_acf_post_id'];
		$price = (int)$_POST['acf'][$price_field];
		$email = $_POST['acf'][$email_field];
		$name = $_POST['acf'][$name_field];

		$oldPrice = (int)get_field('price', $item_id);

		if ($oldPrice < $price) {
			update_field($price_field, $price, $item_id);
			update_field($email_field, $email, $item_id);
			update_field($name_field, $name, $item_id);
		};
	}
}, 10, 1);

add_filter('acf/prepare_field', function ($field) {
	// Target ACF Form Front only
	if (is_admin() && !wp_doing_ajax())
		return $field;

	$field['description']['class'] .= ' small text-muted';
	$field['wrapper']['class'] .= ' form-group border-0 p-0 mb-3';
	$field['class'] .= ' form-control';

	return $field;
});
