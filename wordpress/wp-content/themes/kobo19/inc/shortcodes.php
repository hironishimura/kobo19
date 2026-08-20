<?php
/**
 * 本文で使えるショートコード。
 *
 * [calc] は、SujiCalc の画面と同じ「左に式・右に答え」の形で例を見せます。
 * 行ごとに縦棒で区切って書きます。
 *
 *   [calc]
 *   単価 = 1200円 | 1,200円
 *   個数 = 35 | 35
 *   単価 * 個数 | 42,000円
 *   [/calc]
 *
 * 答えが出ない行は縦棒ごと省きます。エラーになる例は「|!」で区切ります。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

/**
 * 計算ノートの見た目で例を出す。
 *
 * @param array<string, string>|string $atts    属性。numbers="no" で行番号を消す。
 * @param string                       $content 中身。
 * @return string
 */
function kobo19_calc_shortcode( $atts, $content = '' ) {
	$atts = shortcode_atts(
		array(
			'numbers' => 'yes',
			'start'   => '1',
			'label'   => '',
		),
		$atts,
		'calc'
	);

	// 本文の整形（wpautop）で入った改行タグを、素の改行に戻す。
	$text = preg_replace( '#<br\s*/?>#i', "\n", (string) $content );
	$text = preg_replace( '#</?p[^>]*>#i', "\n", $text );
	$text = html_entity_decode( $text, ENT_QUOTES, 'UTF-8' );

	$lines   = preg_split( '/\R/u', trim( $text ) );
	$numbers = ( 'no' !== $atts['numbers'] );
	$no      = max( 1, (int) $atts['start'] );

	$rows = array();

	foreach ( $lines as $line ) {
		$line = rtrim( $line );

		if ( '' === trim( $line ) ) {
			// 空行もそのまま1行として見せる（SujiCalc では区切りの意味を持つため）。
			$rows[] = kobo19_calc_row( $numbers ? $no : null, '', '', false );
			++$no;
			continue;
		}

		$is_error = false;
		$answer   = '';
		$expr     = $line;

		if ( false !== strpos( $line, '|!' ) ) {
			list( $expr, $answer ) = array_map( 'trim', explode( '|!', $line, 2 ) );
			$is_error              = true;
		} elseif ( false !== strpos( $line, '|' ) ) {
			list( $expr, $answer ) = array_map( 'trim', explode( '|', $line, 2 ) );
		}

		$rows[] = kobo19_calc_row( $numbers ? $no : null, $expr, $answer, $is_error );
		++$no;
	}

	$classes = 'calc' . ( $numbers ? '' : ' calc--plain' );
	$label   = $atts['label'] ? '<p class="calc__label">' . esc_html( $atts['label'] ) . '</p>' : '';

	return $label . '<div class="' . esc_attr( $classes ) . '">' . implode( '', $rows ) . '</div>';
}
add_shortcode( 'calc', 'kobo19_calc_shortcode' );

/**
 * 計算ノートの1行を組み立てる。
 *
 * @param int|null $no       行番号。null なら出さない。
 * @param string   $expr     式。
 * @param string   $answer   答え。
 * @param bool     $is_error 答えの代わりにエラーを出すかどうか。
 * @return string
 */
function kobo19_calc_row( $no, $expr, $answer, $is_error ) {
	$out = '<div class="calc__line">';

	if ( null !== $no ) {
		$out .= '<span class="calc__no" aria-hidden="true">' . esc_html( (string) $no ) . '</span>';
	}

	$out .= '<code class="calc__expr">' . esc_html( $expr ) . '</code>';

	if ( '' !== $answer ) {
		$class = $is_error ? 'calc__ans calc__ans--error' : 'calc__ans';
		$out  .= '<span class="' . esc_attr( $class ) . '">' . esc_html( $answer ) . '</span>';
	} else {
		$out .= '<span class="calc__ans"></span>';
	}

	return $out . '</div>';
}

/**
 * キーの表記。[key]⌘Z[/key] のように使います。
 *
 * @param array<string, string>|string $atts    属性（未使用）。
 * @param string                       $content キーの名前。
 * @return string
 */
function kobo19_key_shortcode( $atts, $content = '' ) {
	return '<kbd>' . esc_html( wp_strip_all_tags( (string) $content ) ) . '</kbd>';
}
add_shortcode( 'key', 'kobo19_key_shortcode' );

/**
 * 覚え書きの囲み。[note]…[/note]
 *
 * @param array<string, string>|string $atts    属性。title で見出しを付けられます。
 * @param string                       $content 中身。
 * @return string
 */
function kobo19_note_shortcode( $atts, $content = '' ) {
	$atts = shortcode_atts( array( 'title' => '' ), $atts, 'note' );

	$title = $atts['title'] ? '<p class="callout__title">' . esc_html( $atts['title'] ) . '</p>' : '';

	return '<aside class="callout">' . $title . wp_kses_post( trim( (string) $content ) ) . '</aside>';
}
add_shortcode( 'note', 'kobo19_note_shortcode' );
