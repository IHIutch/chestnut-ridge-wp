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

			$update_post = get_post($item_id);

			wp_save_post_revision($update_post);
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

//ACF - Extend ACF Save Behaviour to ensure revisions include meta
add_action('_wp_put_post_revision', function ($revision_id) {
	// bail early if editing in admin
	if (is_admin())
		return;
	//For Non-Admin Cases - ensure the meta data is written to revision as well as post
	if (!empty($_POST['acf'])) {
		foreach ($_POST['acf'] as $k => $v) {
			// bail early if $value is not is a field_key
			if (!acf_is_field_key($k)) {
				continue;
			}
			update_field($k, $v, $revision_id);
			update_metadata('post', $revision_id, $k, $v);
		}
	}
});

add_filter('use_block_editor_for_post', function () {
	if ($post->ID === 3766) return false;
}, 10, 2);

add_filter('wpseo_metabox_prio', function () {
	return 'low';
}, 10);
