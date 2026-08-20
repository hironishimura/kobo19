<?php
/**
 * テンプレートから呼び出す小さな関数群。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

/**
 * 説明書の章を、順序どおりに並べて返す。
 *
 * @return WP_Post[]
 */
function kobo19_manual_chapters() {
	static $chapters = null;

	if ( null !== $chapters ) {
		return $chapters;
	}

	$chapters = get_posts(
		array(
			'post_type'      => 'manual',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'ASC',
			),
			'order'          => 'ASC',
		)
	);

	return $chapters;
}

/**
 * 章の通し番号を返す。並びの何番目かで決まります。
 *
 * @param int|null $post_id 投稿ID。
 * @return int 見つからなければ 0。
 */
function kobo19_chapter_number( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	foreach ( kobo19_manual_chapters() as $index => $chapter ) {
		if ( (int) $chapter->ID === (int) $post_id ) {
			return $index + 1;
		}
	}

	return 0;
}

/**
 * 章番号を「01」の形にして返す。
 *
 * @param int|null $post_id 投稿ID。
 * @return string
 */
function kobo19_chapter_label( $post_id = null ) {
	$number = kobo19_chapter_number( $post_id );

	return $number ? sprintf( '%02d', $number ) : '—';
}

/**
 * いま見ている章の前後を返す。
 *
 * @param int|null $post_id 投稿ID。
 * @return array{prev: ?WP_Post, next: ?WP_Post}
 */
function kobo19_chapter_siblings( $post_id = null ) {
	$post_id  = $post_id ? $post_id : get_the_ID();
	$chapters = kobo19_manual_chapters();
	$index    = kobo19_chapter_number( $post_id ) - 1;

	return array(
		'prev' => ( $index > 0 && isset( $chapters[ $index - 1 ] ) ) ? $chapters[ $index - 1 ] : null,
		'next' => isset( $chapters[ $index + 1 ] ) ? $chapters[ $index + 1 ] : null,
	);
}

/**
 * 説明書の目次を出力する。章のページでは、いま見ている章に印を付けます。
 *
 * @param int|null $current_id いま見ている章のID。
 */
function kobo19_manual_toc( $current_id = null ) {
	$chapters = kobo19_manual_chapters();

	if ( ! $chapters ) {
		return;
	}

	echo '<nav class="toc" aria-label="説明書の目次">';
	echo '<p class="toc__title">説明書</p>';
	echo '<ol class="toc__list">';

	foreach ( $chapters as $index => $chapter ) {
		$is_current = ( (int) $chapter->ID === (int) $current_id );

		printf(
			'<li class="toc__item%s"><a href="%s"%s><span class="toc__no">%02d</span>%s</a></li>',
			$is_current ? ' is-current' : '',
			esc_url( get_permalink( $chapter ) ),
			$is_current ? ' aria-current="page"' : '',
			$index + 1,
			esc_html( get_the_title( $chapter ) )
		);
	}

	echo '</ol>';
	echo '</nav>';
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
		$text = wp_strip_all_tags( strip_shortcodes( get_the_content() ) );
	}

	$text = trim( preg_replace( '/\s+/u', ' ', $text ) );

	if ( mb_strlen( $text ) > $length ) {
		$text = mb_substr( $text, 0, $length ) . '…';
	}

	return $text;
}

/**
 * 固定ページを、あれば URL つきで返す。
 *
 * @param string $slug スラッグ。
 * @return array{url: string, title: string}|null
 */
function kobo19_page_link( $slug ) {
	$page = get_page_by_path( $slug );

	if ( ! $page ) {
		return null;
	}

	return array(
		'url'   => get_permalink( $page ),
		'title' => get_the_title( $page ),
	);
}
