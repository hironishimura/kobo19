<?php
/**
 * テーマの読み込みと基本設定。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

define( 'KOBO19_VERSION', '1.0.0' );

require_once get_template_directory() . '/inc/cpt.php';
require_once get_template_directory() . '/inc/meta-box.php';
require_once get_template_directory() . '/inc/template-tags.php';
require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/demo-content.php';

/**
 * テーマがWordPressに対応している機能を宣言する。
 */
function kobo19_setup() {
	load_theme_textdomain( 'kobo19', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'custom-logo', array(
		'height'      => 64,
		'width'       => 240,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	) );

	add_editor_style( 'assets/css/editor.css' );

	add_image_size( 'kobo19-card', 960, 640, true );

	register_nav_menus( array(
		'primary' => 'ヘッダーのメニュー',
		'footer'  => 'フッターのメニュー',
	) );
}
add_action( 'after_setup_theme', 'kobo19_setup' );

/**
 * 抜粋の長さを日本語向けに調整する。
 *
 * @return int
 */
function kobo19_excerpt_length() {
	return 60;
}
add_filter( 'excerpt_length', 'kobo19_excerpt_length' );

/**
 * 抜粋の末尾記号。
 *
 * @return string
 */
function kobo19_excerpt_more() {
	return '…';
}
add_filter( 'excerpt_more', 'kobo19_excerpt_more' );

/**
 * スタイルとスクリプトを読み込む。
 */
function kobo19_enqueue_assets() {
	// 見出し＝Zen Kaku Gothic New、数値と符号＝IBM Plex Mono。
	wp_enqueue_style(
		'kobo19-fonts',
		'https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600&family=Zen+Kaku+Gothic+New:wght@400;500;700;900&display=swap',
		array(),
		null
	);

	wp_enqueue_style( 'kobo19-style', get_stylesheet_uri(), array(), KOBO19_VERSION );

	wp_enqueue_style(
		'kobo19-main',
		get_template_directory_uri() . '/assets/css/main.css',
		array( 'kobo19-style' ),
		KOBO19_VERSION
	);

	wp_enqueue_script(
		'kobo19-main',
		get_template_directory_uri() . '/assets/js/main.js',
		array(),
		KOBO19_VERSION,
		true
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'kobo19_enqueue_assets' );

/**
 * Google Fontsへの接続を先に開いておく。
 *
 * @param array<int, string> $urls          リソースURL。
 * @param string             $relation_type 関係の種類。
 * @return array<int, string>
 */
function kobo19_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type && wp_style_is( 'kobo19-fonts', 'queue' ) ) {
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'kobo19_resource_hints', 10, 2 );

/**
 * bodyに区分のクラスを足して、色分けの手がかりにする。
 *
 * @param string[] $classes bodyクラス。
 * @return string[]
 */
function kobo19_body_classes( $classes ) {
	if ( is_singular( 'work' ) ) {
		$classes[] = 'is-work is-cat-' . kobo19_work_category_slug();
	}

	if ( is_tax( 'work_category' ) ) {
		$term      = get_queried_object();
		$classes[] = 'is-cat-' . $term->slug;
	}

	return $classes;
}
add_filter( 'body_class', 'kobo19_body_classes' );

/**
 * ウィジェットエリアを登録する。
 */
function kobo19_widgets_init() {
	register_sidebar( array(
		'name'          => 'フッター',
		'id'            => 'footer-1',
		'description'   => 'フッターに表示するウィジェット。',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'kobo19_widgets_init' );

/**
 * テーマを有効化したときに、区分・デモの制作物・固定ページを用意し、
 * パーマリンクを書き直す。
 */
function kobo19_after_switch_theme() {
	kobo19_seed_work_categories();
	kobo19_install_demo_content();
	kobo19_setup_front_page();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'kobo19_after_switch_theme' );

/**
 * トップページを front-page.php に任せる設定にする。
 *
 * 「ホーム」固定ページと「制作物一覧」固定ページを作り、表示設定を整えます。
 */
function kobo19_setup_front_page() {
	if ( 'page' === get_option( 'show_on_front' ) && get_option( 'page_on_front' ) ) {
		return;
	}

	$home = get_page_by_path( 'home' );

	if ( ! $home ) {
		$home_id = wp_insert_post( array(
			'post_title'   => '19工房',
			'post_name'    => 'home',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => '',
		) );
	} else {
		$home_id = $home->ID;
	}

	if ( $home_id && ! is_wp_error( $home_id ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home_id );
	}
}
