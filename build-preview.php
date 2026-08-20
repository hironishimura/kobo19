<?php
/**
 * 内容確認用プレビューを作り直す。
 *
 *   php build-preview.php
 *
 * WordPress を立てずに、テーマの文章とショートコードをそのまま使って
 * preview/ に静的な HTML を書き出します。WordPress の関数はこのファイルの
 * 上のほうで最低限のものだけ用意しています（テーマ側は書き換えません）。
 */

// ---------------------------------------------------------------- WordPress の代役

define( 'ABSPATH', __DIR__ );
define( 'KOBO19_VERSION', '1.0.0' );

function esc_html( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $s ) { return esc_html( $s ); }
function esc_url( $s ) { return esc_html( $s ); }
function wp_kses_post( $s ) { return $s; }
function wp_strip_all_tags( $s ) { return strip_tags( (string) $s ); }
function add_shortcode( $tag, $cb ) { $GLOBALS['kobo19_shortcodes'][ $tag ] = $cb; }
function get_template_directory() { return __DIR__ . '/wordpress/wp-content/themes/kobo19'; }

function shortcode_atts( $pairs, $atts, $shortcode = '' ) {
	$atts = (array) $atts;
	$out  = array();
	foreach ( $pairs as $name => $default ) {
		$out[ $name ] = array_key_exists( $name, $atts ) ? $atts[ $name ] : $default;
	}
	return $out;
}

/** [calc …] などを展開する。属性は name="value" だけ読めれば足りる。 */
function do_shortcode( $text ) {
	return preg_replace_callback(
		'/\[(calc|note|key)([^\]]*)\](.*?)\[\/\1\]/s',
		function ( $m ) {
			$atts = array();
			if ( preg_match_all( '/(\w+)="([^"]*)"/', $m[2], $found, PREG_SET_ORDER ) ) {
				foreach ( $found as $f ) {
					$atts[ $f[1] ] = $f[2];
				}
			}
			return call_user_func( $GLOBALS['kobo19_shortcodes'][ $m[1] ], $atts, $m[3] );
		},
		$text
	);
}

require_once get_template_directory() . '/inc/shortcodes.php';
require_once get_template_directory() . '/inc/starter-manual.php';
require_once get_template_directory() . '/inc/starter-pages.php';

// ---------------------------------------------------------------- 文章 → HTML

/**
 * 素の文章を、そのまま表示できる HTML にする。
 * 記法は inc/demo-content.php の kobo19_to_blocks と同じです。
 */
function kobo19_render( $text ) {
	$out = array();

	foreach ( preg_split( '/\n{2,}/', trim( $text ) ) as $chunk ) {
		$chunk = trim( $chunk );

		if ( '' === $chunk ) {
			continue;
		}

		if ( '[' === substr( $chunk, 0, 1 ) ) {
			$out[] = do_shortcode( $chunk );
			continue;
		}

		if ( 0 === strpos( $chunk, '### ' ) ) {
			$out[] = '<h3>' . kobo19_inline( substr( $chunk, 4 ) ) . '</h3>';
			continue;
		}

		if ( 0 === strpos( $chunk, '## ' ) ) {
			$out[] = '<h2>' . kobo19_inline( substr( $chunk, 3 ) ) . '</h2>';
			continue;
		}

		if ( '|' === substr( $chunk, 0, 1 ) ) {
			$out[] = kobo19_render_table( $chunk );
			continue;
		}

		if ( 0 === strpos( $chunk, '- ' ) ) {
			$items = array();
			foreach ( explode( "\n", $chunk ) as $line ) {
				$line = trim( $line );
				if ( 0 === strpos( $line, '- ' ) ) {
					$items[] = '<li>' . kobo19_inline( substr( $line, 2 ) ) . '</li>';
				}
			}
			$out[] = '<ul>' . implode( '', $items ) . '</ul>';
			continue;
		}

		$out[] = '<p>' . kobo19_inline( str_replace( "\n", '<br>', $chunk ) ) . '</p>';
	}

	return implode( "\n", $out );
}

function kobo19_render_table( $chunk ) {
	$rows = array();

	foreach ( explode( "\n", $chunk ) as $line ) {
		$line = trim( $line );
		if ( '' === $line || '|' !== substr( $line, 0, 1 ) || preg_match( '/^\|[\s:-]+\|/', $line ) ) {
			continue;
		}
		$rows[] = array_map( 'trim', explode( '|', trim( $line, '|' ) ) );
	}

	if ( ! $rows ) {
		return '';
	}

	$head = array_shift( $rows );
	$html = '<figure class="wp-block-table"><table><thead><tr>';

	foreach ( $head as $cell ) {
		$html .= '<th>' . kobo19_inline( $cell ) . '</th>';
	}
	$html .= '</tr></thead><tbody>';

	foreach ( $rows as $row ) {
		$html .= '<tr>';
		foreach ( $row as $cell ) {
			$html .= '<td>' . kobo19_inline( $cell ) . '</td>';
		}
		$html .= '</tr>';
	}

	return $html . '</tbody></table></figure>';
}

function kobo19_inline( $text ) {
	$text = preg_replace( '/\*\*(.+?)\*\*/u', '<strong>$1</strong>', $text );
	$text = preg_replace( '/`([^`]+)`/u', '<code>$1</code>', $text );
	// メールアドレスは自動でリンクにする。
	$text = preg_replace( '/([\w.+-]+@[\w-]+\.[\w.-]+)/u', '<a href="mailto:$1">$1</a>', $text );
	return $text;
}

// ---------------------------------------------------------------- 共通の枠

$chapters = kobo19_manual_source();
$pages    = kobo19_page_source();

$APP   = 'SujiCalc';
$EMAIL = 'tapes-penne05@icloud.com';

function kobo19_head( $title ) {
	return '<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>' . esc_html( $title ) . '</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600&family=Zen+Kaku+Gothic+New:wght@400;500;700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../wordpress/wp-content/themes/kobo19/assets/css/main.css">
</head>
<body>
<a class="skip-link" href="#main">本文へ移動</a>

<div class="site">

	<header class="site-header">
		<div class="wrap site-header__inner">
			<a class="brand" href="index.html" rel="home">
				<span class="brand__mark">19</span>
				<span class="brand__name">19工房</span>
				<span class="brand__tagline">つくったものを置いておく場所</span>
			</a>

			<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav">メニュー</button>

			<nav class="nav" id="site-nav" aria-label="メインメニュー">
				<ul>
					<li><a href="manual.html">使い方</a></li>
					<li><a href="support.html">サポート</a></li>
					<li><a href="privacy.html">プライバシー</a></li>
				</ul>
			</nav>
		</div>
	</header>

	<main id="main">
';
}

function kobo19_foot() {
	global $EMAIL;

	return '	</main>

	<section class="contact">
		<div class="wrap contact__inner">
			<div>
				<h2 class="contact__title">お困りのときは</h2>
				<p class="contact__text">使い方のご質問、不具合のご報告、ご要望をお待ちしています。お使いの端末と OS のバージョン、再現する式を添えていただけると助かります。</p>
			</div>
			<div><a class="btn" href="support.html">サポートを見る</a></div>
		</div>
	</section>

	<footer class="site-footer">
		<div class="wrap">
			<div class="site-footer__inner">
				<div>
					<a class="brand" href="index.html" rel="home">
						<span class="brand__mark">19</span>
						<span class="brand__name">19工房</span>
					</a>
					<p class="site-footer__mail"><a href="mailto:' . $EMAIL . '">' . $EMAIL . '</a></p>
				</div>
				<nav aria-label="フッターメニュー">
					<ul>
						<li><a href="manual.html">使い方</a></li>
						<li><a href="support.html">サポート</a></li>
						<li><a href="privacy.html">プライバシーポリシー</a></li>
						<li><a href="terms.html">利用規約</a></li>
					</ul>
				</nav>
			</div>
			<div class="colophon">
				<span>&copy; 2026 19工房</span>
				<span>このアプリは利用者の情報を収集しません</span>
			</div>
		</div>
	</footer>

</div>

<script src="../wordpress/wp-content/themes/kobo19/assets/js/main.js"></script>
</body>
</html>
';
}

/** 説明書の目次（章のページの左に出るもの）。 */
function kobo19_toc( $chapters, $current = null ) {
	$html = '<nav class="toc" aria-label="説明書の目次"><p class="toc__title">説明書</p><ol class="toc__list">';

	foreach ( $chapters as $i => $c ) {
		$is  = ( $c['slug'] === $current );
		$html .= sprintf(
			'<li class="toc__item%s"><a href="manual-%s.html"%s><span class="toc__no">%02d</span>%s</a></li>',
			$is ? ' is-current' : '',
			$c['slug'],
			$is ? ' aria-current="page"' : '',
			$i + 1,
			esc_html( $c['title'] )
		);
	}

	return $html . '</ol></nav>';
}

/** 目次の一覧（トップと目次ページで使う縦並び）。 */
function kobo19_chapter_list( $chapters ) {
	$html = '<ol class="chapter-list">';

	foreach ( $chapters as $i => $c ) {
		$html .= sprintf(
			'<li class="chapter-list__item"><a href="manual-%s.html"><span class="chapter-list__no">%02d</span>'
			. '<span class="chapter-list__body"><span class="chapter-list__title">%s</span>'
			. '<span class="chapter-list__text">%s</span></span></a></li>',
			$c['slug'],
			$i + 1,
			esc_html( $c['title'] ),
			esc_html( $c['excerpt'] )
		);
	}

	return $html . '</ol>';
}

// ---------------------------------------------------------------- 書き出し

$out = __DIR__ . '/preview';

// --- トップ ---
$features = array(
	array(
		'title' => '単位がそのまま通る',
		'text'  => '数字のうしろに単位を書くだけです。掛け算・割り算をすると <code>mm²</code> や <code>N/mm²</code> のように組み立てられ、種類の違う単位を足そうとするとエラーで止まります。',
		'calc'  => "120 mm * 300 mm | 36,000 mm²\n36 kN / 36000 mm2 | 1 N/mm²\n10 km in mile | 6.2137119224 mile",
	),
	array(
		'title' => '名前を付けて使い回す',
		'text'  => '値に名前を付けると、ほかの行から呼び出せます。名前を変えれば、その名前を使っている行もまとめて書き換わります。前の行は <code>prev</code>、3行目は <code>#3</code>、空行までの合計は <code>sum</code> です。',
		'calc'  => "梁幅 = 120 mm | 120 mm\n梁せい = 300 mm | 300 mm\n梁幅 * 梁せい | 36,000 mm²",
	),
	array(
		'title' => '表にしてまとめて集計',
		'text'  => '値を並べて持てます。<code>sum</code> <code>avg</code> <code>min</code> <code>max</code> <code>sort</code> で扱え、文字も混ぜられるので、計算書の見出しを付けられます。',
		'calc'  => "価格 = [4800, 12000, 3200] | [4,800, 12,000, 3,200]\nsum(価格) | 20,000\navg(価格) | 6,666.6666666667",
	),
	array(
		'title' => 'グラフになる',
		'text'  => '<code>plot</code> と書けば曲線が、<code>chart</code> と書けば棒グラフが本文の下に出ます。打ち直すたびにその場で描き直されます。',
		'calc'  => "plot(x^2 - 3x) |\nchart(家賃, 光熱費, 食費) |",
	),
	array(
		'title' => 'かんたんなプログラム',
		'text'  => '<code>if</code> <code>for</code> <code>while</code> <code>def</code> が Python と同じ書き方で使えます。繰り返しの計算や、自分用の関数を作れます。作った関数はほかのシートからも呼べます。',
		'calc'  => "def 税込(x): |\n    return x * 1.1 |\n税込(4800) | 5,280",
	),
	array(
		'title' => '3つの端末で同じ書類',
		'text'  => 'iPhone・iPad・Mac の書類は iCloud で自動的に同期します。現場の iPhone に打ち込んだ数字を、事務所の Mac でそのまま続けられます。別々のシートを編集していれば、どちらも残ります。',
		'calc'  => '',
	),
);

$feature_html = '';
foreach ( $features as $f ) {
	$feature_html .= '<div class="feature reveal"><h2 class="feature__title">' . esc_html( $f['title'] ) . '</h2>'
		. '<p class="feature__text">' . $f['text'] . '</p>';
	if ( $f['calc'] ) {
		$feature_html .= do_shortcode( "[calc numbers=\"no\"]\n" . $f['calc'] . "\n[/calc]" );
	}
	$feature_html .= '</div>';
}

$facts = array(
	'バージョン' => '1.0',
	'対応'       => 'iOS 17 / iPadOS 17 / macOS 14 以降',
	'カテゴリ'   => '仕事効率化',
);

$facts_html = '<dl class="facts">';
foreach ( $facts as $k => $v ) {
	$facts_html .= '<div class="facts__row"><dt>' . esc_html( $k ) . '</dt><dd>' . esc_html( $v ) . '</dd></div>';
}
$facts_html .= '</dl>';

$home = kobo19_head( $APP ) . '
	<section class="hero">
		<div class="wrap">
			<div class="hero__grid">
				<div class="hero__text">
					<p class="hero__eyebrow">iPhone ／ iPad ／ Mac</p>

					<h1 class="hero__title">' . $APP . '</h1>

					<p class="hero__tagline">打つと右に答えが出る、ノート型の電卓。</p>

					<p class="hero__lead">式を打つと、その行の右側にすぐ答えが出ます。「＝」は要りません。<br>書いた式はそのまま残るので、あとから数字を直せば、続きの計算も一度に合い直ります。</p>

					<div class="hero__actions">
						<a class="btn" href="manual.html">使い方を読む</a>
					</div>

					' . $facts_html . '
				</div>

				<div class="hero__demo">
' . do_shortcode( "[calc label=\"打つと、右に出る\"]\n// 8月分の見積もり |\n単価 = 1200円 | 1,200円\n個数 = 35 | 35\n単価 * 個数 | 42,000円\n+ 10% | 46,200円\n \n梁幅 = 120 mm | 120 mm\n梁せい = 300 mm | 300 mm\n梁幅 * 梁せい | 36,000 mm²\n[/calc]" ) . '
				</div>
			</div>
		</div>
	</section>

	<section class="section">
		<div class="wrap">
			<p class="eyebrow">できること</p>
			<div class="features">' . $feature_html . '</div>
		</div>
	</section>

	<section class="section">
		<div class="wrap">
			<div class="privacy-note">
				<div>
					<p class="eyebrow">プライバシー</p>
					<h2 class="section-title">何も集めません</h2>
					<p class="section-lead">広告も、利用状況の解析も入っていません。そもそも、外部と通信する機能がアプリに含まれていません。書いた内容は、お使いの端末とご自身の iCloud にだけ保存されます。</p>
				</div>
				<p class="section-more"><a class="btn btn--quiet" href="privacy.html">プライバシーポリシー</a></p>
			</div>
		</div>
	</section>

	<section class="section">
		<div class="wrap">
			<p class="eyebrow">説明書</p>
			<h2 class="section-title">使い方</h2>
			<p class="section-lead">式の書き方から、単位・グラフ・プログラム・同期まで、例つきでまとめています。</p>
' . kobo19_chapter_list( $chapters ) . '
		</div>
	</section>
' . kobo19_foot();

file_put_contents( "$out/index.html", $home );

// --- 説明書の目次 ---
$toc_page = kobo19_head( "使い方｜$APP" ) . '
	<section class="page-head">
		<div class="wrap">
			<p class="eyebrow">' . $APP . ' ／ 説明書</p>
			<h1 class="page-head__title">使い方</h1>
			<p class="page-head__lead">式の書き方から、単位・グラフ・プログラム・同期まで。上から順に読めば一通り分かるように並べています。</p>
		</div>
	</section>

	<section class="section" style="padding-top:0;">
		<div class="wrap">' . kobo19_chapter_list( $chapters ) . '</div>
	</section>
' . kobo19_foot();

file_put_contents( "$out/manual.html", $toc_page );

// --- 各章 ---
foreach ( $chapters as $i => $c ) {
	$prev = isset( $chapters[ $i - 1 ] ) && $i > 0 ? $chapters[ $i - 1 ] : null;
	$next = isset( $chapters[ $i + 1 ] ) ? $chapters[ $i + 1 ] : null;

	$nav = '<nav class="chapter-nav" aria-label="前後の章"><div class="chapter-nav__side">';
	if ( $prev ) {
		$nav .= '<a href="manual-' . $prev['slug'] . '.html"><span class="chapter-nav__label">前の章</span>'
			. '<span class="chapter-nav__title">← ' . esc_html( $prev['title'] ) . '</span></a>';
	}
	$nav .= '</div><div class="chapter-nav__side chapter-nav__side--next">';
	if ( $next ) {
		$nav .= '<a href="manual-' . $next['slug'] . '.html"><span class="chapter-nav__label">次の章</span>'
			. '<span class="chapter-nav__title">' . esc_html( $next['title'] ) . ' →</span></a>';
	}
	$nav .= '</div></nav>';

	$page = kobo19_head( $c['title'] . "｜$APP" ) . '
	<article class="manual">
		<div class="wrap manual__grid">
			<aside class="manual__side">' . kobo19_toc( $chapters, $c['slug'] ) . '</aside>

			<div class="manual__main">
				<header class="manual__head">
					<p class="eyebrow"><a href="manual.html">説明書</a> ／ 第' . ( $i + 1 ) . '章</p>
					<h1 class="manual__title"><span class="manual__no">' . sprintf( '%02d', $i + 1 ) . '</span>' . esc_html( $c['title'] ) . '</h1>
					<p class="manual__summary">' . esc_html( $c['excerpt'] ) . '</p>
				</header>

				<div class="entry__body">
' . kobo19_render( $c['content'] ) . '
				</div>

				' . $nav . '
			</div>
		</div>
	</article>
' . kobo19_foot();

	file_put_contents( "$out/manual-{$c['slug']}.html", $page );
}

// --- 固定ページ ---
foreach ( $pages as $slug => $p ) {
	$page = kobo19_head( $p['title'] . "｜$APP" ) . '
	<article class="entry">
		<div class="wrap">
			<header>
				<p class="eyebrow">' . $APP . '</p>
				<h1 class="entry__title">' . esc_html( $p['title'] ) . '</h1>
			</header>

			<div class="entry__body">
' . kobo19_render( $p['content'] ) . '
			</div>
		</div>
	</article>
' . kobo19_foot();

	file_put_contents( "$out/$slug.html", $page );
}

printf( "プレビューを書き出しました: %d ページ\n", 2 + count( $chapters ) + count( $pages ) );
