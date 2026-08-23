<?php
/**
 * テンプレートから呼び出す小さな関数群。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

/**
 * 公開中のアプリを、並び順に返す。
 *
 * @return WP_Post[]
 */
function kobo19_apps() {
	static $apps = null;

	if ( null === $apps ) {
		$apps = get_posts(
			array(
				'post_type'      => 'app',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => array(
					'menu_order' => 'ASC',
					'date'       => 'ASC',
				),
				'order'          => 'ASC',
			)
		);
	}

	return $apps;
}

/**
 * アプリの入力値を返す。
 *
 * @param string   $key    フィールドキー（tagline / store / version など）。
 * @param int|null $app_id アプリのID。
 * @return string
 */
function kobo19_app_meta( $key, $app_id = null ) {
	$app_id = $app_id ? $app_id : get_the_ID();

	return (string) get_post_meta( $app_id, '_kobo19_' . $key, true );
}

/**
 * 資料が属するアプリのIDを返す。
 *
 * @param int|null $post_id 資料のID。
 * @return int 紐づいていなければ 0。
 */
function kobo19_doc_app( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	return (int) get_post_meta( $post_id, '_kobo19_app', true );
}

/**
 * 資料の種別を返す。
 *
 * @param int|null $post_id 資料のID。
 * @return string
 */
function kobo19_doc_kind( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$kind    = (string) get_post_meta( $post_id, '_kobo19_kind', true );

	return $kind ? $kind : 'chapter';
}

/**
 * あるアプリの資料を、種別で絞って返す。
 *
 * @param int         $app_id アプリのID。
 * @param string|null $kind   種別。null ならすべて。
 * @return WP_Post[]
 */
function kobo19_app_docs( $app_id, $kind = null ) {
	static $cache = array();

	$key = $app_id . '|' . (string) $kind;

	if ( isset( $cache[ $key ] ) ) {
		return $cache[ $key ];
	}

	$meta = array(
		array(
			'key'   => '_kobo19_app',
			'value' => (int) $app_id,
		),
	);

	if ( $kind ) {
		$meta[] = array(
			'key'   => '_kobo19_kind',
			'value' => $kind,
		);
	}

	$cache[ $key ] = get_posts(
		array(
			'post_type'      => 'doc',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'ASC',
			),
			'order'          => 'ASC',
			'meta_query'     => $meta,
		)
	);

	return $cache[ $key ];
}

/**
 * あるアプリの説明書の章を返す。
 *
 * @param int $app_id アプリのID。
 * @return WP_Post[]
 */
function kobo19_app_chapters( $app_id ) {
	return kobo19_app_docs( $app_id, 'chapter' );
}

/**
 * 章の通し番号を返す。同じアプリの章の中で何番目か。
 *
 * @param int|null $post_id 資料のID。
 * @return int 章でなければ 0。
 */
function kobo19_chapter_number( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	if ( 'chapter' !== kobo19_doc_kind( $post_id ) ) {
		return 0;
	}

	foreach ( kobo19_app_chapters( kobo19_doc_app( $post_id ) ) as $index => $chapter ) {
		if ( (int) $chapter->ID === (int) $post_id ) {
			return $index + 1;
		}
	}

	return 0;
}

/**
 * 章番号を「01」の形で返す。
 *
 * @param int|null $post_id 資料のID。
 * @return string
 */
function kobo19_chapter_label( $post_id = null ) {
	$number = kobo19_chapter_number( $post_id );

	return $number ? sprintf( '%02d', $number ) : '';
}

/**
 * 前後の章を返す。
 *
 * @param int|null $post_id 資料のID。
 * @return array{prev: ?WP_Post, next: ?WP_Post}
 */
function kobo19_chapter_siblings( $post_id = null ) {
	$post_id  = $post_id ? $post_id : get_the_ID();
	$chapters = kobo19_app_chapters( kobo19_doc_app( $post_id ) );
	$index    = kobo19_chapter_number( $post_id ) - 1;

	return array(
		'prev' => ( $index > 0 && isset( $chapters[ $index - 1 ] ) ) ? $chapters[ $index - 1 ] : null,
		'next' => isset( $chapters[ $index + 1 ] ) ? $chapters[ $index + 1 ] : null,
	);
}

/**
 * 説明書の目次を出力する。
 *
 * @param int      $app_id     アプリのID。
 * @param int|null $current_id いま見ている章のID。
 */
function kobo19_manual_toc( $app_id, $current_id = null ) {
	$chapters = kobo19_app_chapters( $app_id );

	if ( ! $chapters ) {
		return;
	}

	printf(
		'<nav class="toc" aria-label="説明書の目次"><p class="toc__title"><a href="%s">%s の説明書</a></p><ol class="toc__list">',
		esc_url( get_permalink( $app_id ) ),
		esc_html( get_the_title( $app_id ) )
	);

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

	echo '</ol></nav>';
}

/**
 * アプリの「App Store に出すページ」（サポート・プライバシー・利用規約）を返す。
 *
 * @param int $app_id アプリのID。
 * @return array<string, WP_Post> 種別をキーにした配列。
 */
function kobo19_app_policies( $app_id ) {
	$found = array();

	foreach ( array( 'support', 'privacy', 'terms' ) as $kind ) {
		$docs = kobo19_app_docs( $app_id, $kind );

		if ( $docs ) {
			$found[ $kind ] = $docs[0];
		}
	}

	return $found;
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
 * アプリの facts（バージョン・対応 OS など）を、空でないものだけ返す。
 *
 * @param int $app_id アプリのID。
 * @return array<string, string>
 */
function kobo19_app_facts( $app_id ) {
	return array_filter(
		array(
			'バージョン' => kobo19_app_meta( 'version', $app_id ),
			'対応'       => kobo19_app_meta( 'requires', $app_id ),
			'カテゴリ'   => kobo19_app_meta( 'category', $app_id ),
			'価格'       => kobo19_app_meta( 'price', $app_id ),
		)
	);
}

/**
 * ヘッダー・フッターに並べるリンクを組み立てる。
 *
 * アプリが1本のうちは、そのアプリの説明書やサポートへ直接つなぎます。
 * 2本以上になったら、一覧へのリンクに切り替わります。
 *
 * @return array<int, array{url: string, label: string}>
 */
function kobo19_primary_links() {
	$apps  = kobo19_apps();
	$links = array();

	if ( 1 === count( $apps ) ) {
		$app      = $apps[0];
		$chapters = kobo19_app_chapters( $app->ID );
		$policies = kobo19_app_policies( $app->ID );

		if ( $chapters ) {
			$links[] = array(
				'url'   => get_permalink( $chapters[0] ),
				'label' => '使い方',
			);
		}

		foreach ( array( 'support' => 'サポート', 'privacy' => 'プライバシー' ) as $kind => $label ) {
			if ( isset( $policies[ $kind ] ) ) {
				$links[] = array(
					'url'   => get_permalink( $policies[ $kind ] ),
					'label' => $label,
				);
			}
		}

		$store = kobo19_app_meta( 'store', $app->ID );
		if ( $store ) {
			$links[] = array(
				'url'   => $store,
				'label' => 'App Store',
			);
		}

		return $links;
	}

	$apps_url = get_post_type_archive_link( 'app' );
	if ( $apps_url && $apps ) {
		$links[] = array(
			'url'   => $apps_url,
			'label' => 'アプリ',
		);
	}

	$docs_url = get_post_type_archive_link( 'doc' );
	if ( $docs_url ) {
		$links[] = array(
			'url'   => $docs_url,
			'label' => '説明書とサポート',
		);
	}

	return $links;
}

/**
 * フッターの「お困りのときは」から飛ばす先を返す。
 *
 * @return array{url: string, label: string}|null
 */
function kobo19_support_link() {
	$apps = kobo19_apps();

	if ( 1 === count( $apps ) ) {
		$policies = kobo19_app_policies( $apps[0]->ID );

		if ( isset( $policies['support'] ) ) {
			return array(
				'url'   => get_permalink( $policies['support'] ),
				'label' => 'サポートを見る',
			);
		}
	}

	$docs_url = get_post_type_archive_link( 'doc' );

	if ( $docs_url && $apps ) {
		return array(
			'url'   => $docs_url,
			'label' => '説明書とサポート',
		);
	}

	return null;
}
