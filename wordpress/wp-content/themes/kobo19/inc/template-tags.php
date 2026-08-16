<?php
/**
 * テンプレートから呼び出す小さな関数群。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

/**
 * 制作物の図番を返す。
 *
 * 手入力があればそれを使い、無ければ区分の頭文字＋通し番号を組み立てます。
 * ホームページ=W、プログラム=P、プロダクツ=D。
 *
 * @param int|null $post_id 投稿ID。
 * @return string
 */
function kobo19_drawing_no( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$manual  = get_post_meta( $post_id, '_kobo19_drawing_no', true );

	if ( $manual ) {
		return $manual;
	}

	$prefixes = array(
		'homepage' => 'W',
		'program'  => 'P',
		'product'  => 'D',
	);

	$terms  = get_the_terms( $post_id, 'work_category' );
	$prefix = 'K';

	if ( $terms && ! is_wp_error( $terms ) ) {
		$slug   = $terms[0]->slug;
		$prefix = isset( $prefixes[ $slug ] ) ? $prefixes[ $slug ] : strtoupper( substr( $slug, 0, 1 ) );
	}

	$order = (int) get_post_field( 'menu_order', $post_id );
	if ( $order < 1 ) {
		$order = 1;
	}

	return sprintf( '%s-%02d', $prefix, $order );
}

/**
 * 制作物の区分名を返す。
 *
 * @param int|null $post_id 投稿ID。
 * @return string
 */
function kobo19_work_category_name( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$terms   = get_the_terms( $post_id, 'work_category' );

	if ( ! $terms || is_wp_error( $terms ) ) {
		return '未分類';
	}

	return $terms[0]->name;
}

/**
 * 制作物の区分スラッグを返す。CSSの色分けに使います。
 *
 * @param int|null $post_id 投稿ID。
 * @return string
 */
function kobo19_work_category_slug( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$terms   = get_the_terms( $post_id, 'work_category' );

	if ( ! $terms || is_wp_error( $terms ) ) {
		return 'none';
	}

	return $terms[0]->slug;
}

/**
 * 表題欄の値を取り出す。
 *
 * @param string   $key     フィールドキー（drawing_no を除く）。
 * @param int|null $post_id 投稿ID。
 * @return string
 */
function kobo19_meta( $key, $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	return (string) get_post_meta( $post_id, '_kobo19_' . $key, true );
}

/**
 * 材料（技術）をスラッシュ区切りの配列にして返す。
 *
 * @param int|null $post_id 投稿ID。
 * @return string[]
 */
function kobo19_stack_list( $post_id = null ) {
	$raw = kobo19_meta( 'stack', $post_id );

	if ( '' === $raw ) {
		return array();
	}

	$items = preg_split( '#\s*[/／]\s*#u', $raw );

	return array_values( array_filter( array_map( 'trim', $items ) ) );
}

/**
 * 区分をカスタマイザーで決めた順に並べて返す。
 *
 * @return WP_Term[]
 */
function kobo19_ordered_categories() {
	$terms = get_terms(
		array(
			'taxonomy'   => 'work_category',
			'hide_empty' => false,
		)
	);

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return array();
	}

	usort(
		$terms,
		function ( $a, $b ) {
			$order_a = (int) get_term_meta( $a->term_id, 'kobo19_order', true );
			$order_b = (int) get_term_meta( $b->term_id, 'kobo19_order', true );

			$order_a = $order_a ? $order_a : 99;
			$order_b = $order_b ? $order_b : 99;

			if ( $order_a === $order_b ) {
				return strcmp( $a->slug, $b->slug );
			}

			return $order_a <=> $order_b;
		}
	);

	return $terms;
}

/**
 * 公開中の制作物の総数を返す。
 *
 * @return int
 */
function kobo19_work_count() {
	$counts = wp_count_posts( 'work' );
	return isset( $counts->publish ) ? (int) $counts->publish : 0;
}

/**
 * 抜粋を、無ければ本文から作って返す。
 *
 * @param int $length 文字数。
 * @return string
 */
function kobo19_summary( $length = 90 ) {
	$text = get_the_excerpt();

	if ( '' === trim( $text ) ) {
		$text = wp_strip_all_tags( get_the_content() );
	}

	$text = trim( preg_replace( '/\s+/u', ' ', $text ) );

	if ( mb_strlen( $text ) > $length ) {
		$text = mb_substr( $text, 0, $length ) . '…';
	}

	return $text;
}

/**
 * 制作物の詳細ページで使う、前後の制作物へのリンクを出力する。
 */
function kobo19_work_pagination() {
	$prev = get_previous_post_link( '%link', '← %title' );
	$next = get_next_post_link( '%link', '%title →' );

	if ( ! $prev && ! $next ) {
		return;
	}

	echo '<nav class="work-nav" aria-label="制作物の前後">';
	echo '<div class="work-nav__side">' . wp_kses_post( $prev ) . '</div>';
	echo '<div class="work-nav__side work-nav__side--next">' . wp_kses_post( $next ) . '</div>';
	echo '</nav>';
}
