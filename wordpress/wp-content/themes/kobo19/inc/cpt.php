<?php
/**
 * カスタム投稿タイプ「説明書」の登録。
 *
 * 説明書は章ごとに1件の投稿として持ち、「ページ属性 → 順序」の数字で並べます。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

/**
 * 投稿タイプ manual（説明書）を登録する。
 */
function kobo19_register_manual_post_type() {
	$labels = array(
		'name'               => '説明書',
		'singular_name'      => '説明書',
		'menu_name'          => '説明書',
		'add_new'            => '新しい章',
		'add_new_item'       => '章を追加',
		'edit_item'          => '章を編集',
		'new_item'           => '新しい章',
		'view_item'          => '章を表示',
		'view_items'         => '説明書を表示',
		'search_items'       => '章を検索',
		'not_found'          => '章が見つかりません',
		'not_found_in_trash' => 'ゴミ箱に章はありません',
		'all_items'          => 'すべての章',
		'archives'           => '説明書の目次',
	);

	register_post_type(
		'manual',
		array(
			'labels'        => $labels,
			'public'        => true,
			'has_archive'   => true,
			'rewrite'       => array(
				'slug'       => 'manual',
				'with_front' => false,
			),
			'menu_icon'     => 'dashicons-book-alt',
			'menu_position' => 5,
			'supports'      => array( 'title', 'editor', 'excerpt', 'revisions', 'page-attributes' ),
			'show_in_rest'  => true,
		)
	);
}
add_action( 'init', 'kobo19_register_manual_post_type' );

/**
 * 説明書の目次は章の順に、全件を1ページで出す。
 *
 * @param WP_Query $query メインクエリ。
 */
function kobo19_manual_archive_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( $query->is_post_type_archive( 'manual' ) ) {
		$query->set( 'posts_per_page', -1 );
		$query->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'ASC' ) );
		$query->set( 'order', 'ASC' );
	}
}
add_action( 'pre_get_posts', 'kobo19_manual_archive_query' );
