<?php
/**
 * カスタム投稿タイプ「制作物」とカテゴリー分類の登録。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

/**
 * 投稿タイプ work（制作物）を登録する。
 */
function kobo19_register_work_post_type() {
	$labels = array(
		'name'                  => '制作物',
		'singular_name'         => '制作物',
		'menu_name'             => '制作物',
		'add_new'               => '新規追加',
		'add_new_item'          => '制作物を追加',
		'edit_item'             => '制作物を編集',
		'new_item'              => '新しい制作物',
		'view_item'             => '制作物を表示',
		'view_items'            => '制作物一覧を表示',
		'search_items'          => '制作物を検索',
		'not_found'             => '制作物が見つかりません',
		'not_found_in_trash'    => 'ゴミ箱に制作物はありません',
		'all_items'             => 'すべての制作物',
		'archives'              => '制作物アーカイブ',
		'featured_image'        => 'サムネイル画像',
		'set_featured_image'    => 'サムネイル画像を設定',
		'remove_featured_image' => 'サムネイル画像を削除',
		'use_featured_image'    => 'サムネイル画像として使う',
	);

	register_post_type(
		'work',
		array(
			'labels'        => $labels,
			'public'        => true,
			'has_archive'   => true,
			'rewrite'       => array(
				'slug'       => 'works',
				'with_front' => false,
			),
			'menu_icon'     => 'dashicons-hammer',
			'menu_position' => 5,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
			'show_in_rest'  => true,
			'taxonomies'    => array( 'work_category' ),
		)
	);
}
add_action( 'init', 'kobo19_register_work_post_type' );

/**
 * 分類 work_category（区分）を登録する。ホームページ / プログラム / プロダクツ。
 */
function kobo19_register_work_taxonomy() {
	$labels = array(
		'name'              => '区分',
		'singular_name'     => '区分',
		'search_items'      => '区分を検索',
		'all_items'         => 'すべての区分',
		'edit_item'         => '区分を編集',
		'update_item'       => '区分を更新',
		'add_new_item'      => '区分を追加',
		'new_item_name'     => '新しい区分の名前',
		'menu_name'         => '区分',
		'back_to_items'     => '区分一覧に戻る',
	);

	register_taxonomy(
		'work_category',
		array( 'work' ),
		array(
			'labels'            => $labels,
			'public'            => true,
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array(
				'slug'       => 'works/category',
				'with_front' => false,
			),
		)
	);
}
add_action( 'init', 'kobo19_register_work_taxonomy' );

/**
 * 初期区分（ホームページ・プログラム・プロダクツ）を用意する。
 *
 * 区分が1件もないときだけ作成するので、あとから名前を変えても上書きされません。
 */
function kobo19_seed_work_categories() {
	if ( ! taxonomy_exists( 'work_category' ) ) {
		return;
	}

	$existing = get_terms(
		array(
			'taxonomy'   => 'work_category',
			'hide_empty' => false,
			'fields'     => 'ids',
		)
	);

	if ( is_wp_error( $existing ) || ! empty( $existing ) ) {
		return;
	}

	$defaults = array(
		'homepage' => array(
			'name'        => 'ホームページ',
			'description' => 'お店・会社・催しのサイト。設計から公開後の運用まで。',
		),
		'program'  => array(
			'name'        => 'プログラム',
			'description' => '毎日の手作業を減らす道具。集計・通知・記録を任せるための仕組み。',
		),
		'product'  => array(
			'name'        => 'プロダクツ',
			'description' => '手元に置いて使うもの。Macアプリと、3Dでかたちにしたもの。',
		),
	);

	$order = 1;
	foreach ( $defaults as $slug => $data ) {
		$term = wp_insert_term(
			$data['name'],
			'work_category',
			array(
				'slug'        => $slug,
				'description' => $data['description'],
			)
		);

		if ( ! is_wp_error( $term ) ) {
			update_term_meta( $term['term_id'], 'kobo19_order', $order );
		}
		++$order;
	}
}

/**
 * 制作物アーカイブの表示件数と並び順を整える。
 *
 * @param WP_Query $query メインクエリ。
 */
function kobo19_work_archive_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( $query->is_post_type_archive( 'work' ) || $query->is_tax( 'work_category' ) ) {
		$query->set( 'posts_per_page', 24 );
		$query->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
	}
}
add_action( 'pre_get_posts', 'kobo19_work_archive_query' );
