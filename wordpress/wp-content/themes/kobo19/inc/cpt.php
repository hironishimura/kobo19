<?php
/**
 * カスタム投稿タイプの登録。
 *
 * アプリ（app）  … 1本のアプリ＝1件。製品ページになります。   /apps/{slug}/
 * 資料（doc）    … 説明書の章・サポート・プライバシー・利用規約。/docs/{slug}/
 *
 * 資料は「どのアプリのものか」と「種別」を持ちます。アプリが増えても、
 * それぞれに説明書と App Store 用のページを持たせられます。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

/**
 * 資料の種別。順番は、アプリのページに並べる順です。
 *
 * @return array<string, string>
 */
function kobo19_doc_kinds() {
	return array(
		'chapter' => '説明書の章',
		'support' => 'サポート',
		'privacy' => 'プライバシーポリシー',
		'terms'   => '利用規約',
	);
}

/**
 * 投稿タイプ app（アプリ）を登録する。
 */
function kobo19_register_app_post_type() {
	register_post_type(
		'app',
		array(
			'labels'        => array(
				'name'                  => 'アプリ',
				'singular_name'         => 'アプリ',
				'menu_name'             => 'アプリ',
				'add_new'               => '新規追加',
				'add_new_item'          => 'アプリを追加',
				'edit_item'             => 'アプリを編集',
				'new_item'              => '新しいアプリ',
				'view_item'             => 'アプリを表示',
				'view_items'            => 'アプリ一覧を表示',
				'search_items'          => 'アプリを検索',
				'not_found'             => 'アプリが見つかりません',
				'not_found_in_trash'    => 'ゴミ箱にアプリはありません',
				'all_items'             => 'すべてのアプリ',
				'archives'              => 'アプリ一覧',
				'featured_image'        => 'アイコン画像',
				'set_featured_image'    => 'アイコン画像を設定',
				'remove_featured_image' => 'アイコン画像を削除',
			),
			'public'        => true,
			'has_archive'   => true,
			'rewrite'       => array(
				'slug'       => 'apps',
				'with_front' => false,
			),
			'menu_icon'     => 'dashicons-smartphone',
			'menu_position' => 5,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
			'show_in_rest'  => true,
		)
	);
}
add_action( 'init', 'kobo19_register_app_post_type' );

/**
 * 投稿タイプ doc（資料）を登録する。
 */
function kobo19_register_doc_post_type() {
	register_post_type(
		'doc',
		array(
			'labels'        => array(
				'name'               => '資料',
				'singular_name'      => '資料',
				'menu_name'          => '資料',
				'add_new'            => '新規追加',
				'add_new_item'       => '資料を追加',
				'edit_item'          => '資料を編集',
				'new_item'           => '新しい資料',
				'view_item'          => '資料を表示',
				'view_items'         => '資料を表示',
				'search_items'       => '資料を検索',
				'not_found'          => '資料が見つかりません',
				'not_found_in_trash' => 'ゴミ箱に資料はありません',
				'all_items'          => 'すべての資料',
				'archives'           => '資料一覧',
			),
			'public'        => true,
			'has_archive'   => true,
			'rewrite'       => array(
				'slug'       => 'docs',
				'with_front' => false,
			),
			'menu_icon'     => 'dashicons-book-alt',
			'menu_position' => 6,
			'supports'      => array( 'title', 'editor', 'excerpt', 'revisions', 'page-attributes' ),
			'show_in_rest'  => true,
		)
	);
}
add_action( 'init', 'kobo19_register_doc_post_type' );

/**
 * 一覧ページの並び順を整える。
 *
 * @param WP_Query $query メインクエリ。
 */
function kobo19_archive_order( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( $query->is_post_type_archive( array( 'app', 'doc' ) ) ) {
		$query->set( 'posts_per_page', -1 );
		$query->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'ASC' ) );
		$query->set( 'order', 'ASC' );
	}
}
add_action( 'pre_get_posts', 'kobo19_archive_order' );
