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
require_once get_template_directory() . '/inc/starter-app.php';
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

$app      = kobo19_app_source();
$chapters = kobo19_manual_source();
$pages    = kobo19_page_source();

$APP   = $app['title'];
$EMAIL = 'tapes-penne05@icloud.com';
$SITE  = '19工房';

/** アプリの数で中身が変わるメニュー。ここでは1本なので、その資料へ直接つなぐ。 */
function kobo19_menu() {
	return array(
		array( 'url' => 'manual-getting-started.html', 'label' => '使い方' ),
		array( 'url' => 'support.html', 'label' => 'サポート' ),
		array( 'url' => 'privacy.html', 'label' => 'プライバシー' ),
	);
}

function kobo19_head( $title ) {
	global $SITE;

	$nav = '';
	foreach ( kobo19_menu() as $item ) {
		$nav .= sprintf( '<li><a href="%s">%s</a></li>', $item['url'], esc_html( $item['label'] ) );
	}

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
				<span class="brand__name">' . $SITE . '</span>
				<span class="brand__tagline">つくったアプリを置いておく場所</span>
			</a>

			<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav">メニュー</button>

			<nav class="nav" id="site-nav" aria-label="メインメニュー">
				<ul>' . $nav . '</ul>
			</nav>
		</div>
	</header>

	<main id="main">
';
}

function kobo19_foot() {
	global $EMAIL, $SITE;

	return '	</main>

	<section class="contact">
		<div class="wrap contact__inner">
			<div>
				<h2 class="contact__title">お困りのときは</h2>
				<p class="contact__text">使い方のご質問、不具合のご報告、ご要望をお待ちしています。お使いの端末と OS のバージョン、再現する式や手順を添えていただけると助かります。</p>
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
						<span class="brand__name">' . $SITE . '</span>
					</a>
					<p class="site-footer__mail"><a href="mailto:' . $EMAIL . '">' . $EMAIL . '</a></p>
				</div>
				<nav aria-label="フッターメニュー">
					<ul>
						<li><a href="manual-getting-started.html">使い方</a></li>
						<li><a href="support.html">サポート</a></li>
						<li><a href="privacy.html">プライバシーポリシー</a></li>
						<li><a href="terms.html">利用規約</a></li>
					</ul>
				</nav>
			</div>
			<div class="colophon">
				<span>&copy; 2026 ' . $SITE . '</span>
				<span>ここに置いているアプリは、利用者の情報を収集しません</span>
			</div>
		</div>
	</footer>

</div>

<script src="../wordpress/wp-content/themes/kobo19/assets/js/main.js"></script>
</body>
</html>
';
}

/** アプリの見出し（トップと製品ページで共通）。 */
function kobo19_app_hero( $app ) {
	$facts = array_filter( array(
		'バージョン' => $app['meta']['version'],
		'対応'       => $app['meta']['requires'],
		'カテゴリ'   => $app['meta']['category'],
		'価格'       => $app['meta']['price'],
	) );

	$facts_html = '';
	if ( $facts ) {
		$facts_html = '<dl class="facts">';
		foreach ( $facts as $k => $v ) {
			$facts_html .= '<div class="facts__row"><dt>' . esc_html( $k ) . '</dt><dd>' . esc_html( $v ) . '</dd></div>';
		}
		$facts_html .= '</dl>';
	}

	$badge   = $app['meta']['status'] ? '<span class="badge">' . esc_html( $app['meta']['status'] ) . '</span>' : '';
	$store   = $app['meta']['store'];
	$buttons = '';

	if ( $store ) {
		$buttons .= '<a class="btn" href="' . esc_url( $store ) . '">App Store で見る</a>';
	}
	$buttons .= '<a class="btn' . ( $store ? ' btn--quiet' : '' ) . '" href="manual-getting-started.html">使い方を読む</a>';

	$demo = $app['meta']['demo'] ? '<div class="hero__demo">' . do_shortcode( $app['meta']['demo'] ) . '</div>' : '';

	return '
	<section class="hero">
		<div class="wrap">
			<div class="hero__grid' . ( $demo ? '' : ' hero__grid--single' ) . '">
				<div class="hero__text">
					<p class="hero__eyebrow">' . esc_html( $app['meta']['category'] ) . $badge . '</p>

					<h1 class="hero__title">' . esc_html( $app['title'] ) . '</h1>

					<p class="hero__tagline">' . esc_html( $app['meta']['tagline'] ) . '</p>

					<p class="hero__lead">' . nl2br( esc_html( $app['meta']['lead'] ) ) . '</p>

					<div class="hero__actions">' . $buttons . '</div>

					' . $facts_html . '
				</div>

				' . $demo . '
			</div>
		</div>
	</section>
';
}

/** 説明書の目次（章のページの左に出るもの）。 */
function kobo19_toc( $chapters, $app, $current = null ) {
	$html = '<nav class="toc" aria-label="説明書の目次"><p class="toc__title">'
		. '<a href="app-' . $app['slug'] . '.html">' . esc_html( $app['title'] ) . ' の説明書</a></p><ol class="toc__list">';

	foreach ( $chapters as $i => $c ) {
		$is    = ( $c['slug'] === $current );
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

/** 章の一覧（縦並び）。 */
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

/** サポート・プライバシー・利用規約への入口。 */
function kobo19_policy_list( $pages ) {
	$html = '<ul class="policy-list">';

	foreach ( $pages as $slug => $page ) {
		$html .= sprintf(
			'<li><a href="%s.html"><span class="policy-list__title">%s</span><span class="policy-list__text">%s</span></a></li>',
			$slug,
			esc_html( $page['title'] ),
			esc_html( isset( $page['excerpt'] ) ? $page['excerpt'] : '' )
		);
	}

	return $html . '</ul>';
}

/** アプリの資料（説明書＋App Store 用のページ）。 */
function kobo19_app_docs_section( $chapters, $pages ) {
	return '
	<section class="section">
		<div class="wrap">
			<p class="eyebrow">説明書</p>
			<h2 class="section-title">使い方</h2>
			<p class="section-lead">上から順に読めば一通り分かるように並べています。</p>
' . kobo19_chapter_list( $chapters ) . '
		</div>
	</section>

	<section class="section">
		<div class="wrap">
			<p class="eyebrow">このアプリについて</p>
' . kobo19_policy_list( $pages ) . '
		</div>
	</section>
';
}

/** アプリのカード（2本以上になったときの一覧）。 */
function kobo19_app_card( $app, $chapters ) {
	$facts = array_filter( array(
		$app['meta']['version'] ? 'バージョン ' . $app['meta']['version'] : '',
		$app['meta']['requires'],
		$app['meta']['category'],
	) );

	$facts_html = '';
	if ( $facts ) {
		$facts_html = '<ul class="app-card__facts">';
		foreach ( $facts as $f ) {
			$facts_html .= '<li>' . esc_html( $f ) . '</li>';
		}
		$facts_html .= '</ul>';
	}

	$badge = $app['meta']['status'] ? '<span class="badge">' . esc_html( $app['meta']['status'] ) . '</span>' : '';
	$store = $app['meta']['store'] ? '<a class="btn btn--quiet" href="' . esc_url( $app['meta']['store'] ) . '">App Store</a>' : '';

	return '<article class="app-card reveal">
		<div class="app-card__body">
			<h2 class="app-card__title"><a href="app-' . $app['slug'] . '.html">' . esc_html( $app['title'] ) . '</a>' . $badge . '</h2>
			<p class="app-card__tagline">' . esc_html( $app['meta']['tagline'] ) . '</p>
			<p class="app-card__text">' . esc_html( str_replace( "\n", ' ', $app['meta']['lead'] ) ) . '</p>
			' . $facts_html . '
			<p class="app-card__actions">
				<a class="btn" href="app-' . $app['slug'] . '.html">' . esc_html( $app['title'] ) . ' を見る</a>
				<a class="btn btn--quiet" href="manual-getting-started.html">使い方（' . count( $chapters ) . '章）</a>
				' . $store . '
			</p>
		</div>
	</article>';
}

// ---------------------------------------------------------------- 書き出し

$out  = __DIR__ . '/preview';
$body = kobo19_render( $app['content'] );

// --- トップ（プロダクツの入口。アプリの本数によらず一覧）---
file_put_contents(
	"$out/index.html",
	kobo19_head( $SITE ) . '
	<section class="hero">
		<div class="wrap">
			<div class="hero__text hero__text--wide">
				<p class="hero__eyebrow">WORKSHOP 19</p>

				<h1 class="hero__title hero__title--site">つくったもの</h1>

				<p class="hero__lead">アプリや道具をつくって、ここに置いています。使い方の説明書と、サポートの窓口は、それぞれのページにあります。</p>
			</div>
		</div>
	</section>

	<section class="section">
		<div class="wrap">
			<p class="eyebrow">プロダクツ</p>
			<div class="app-grid">' . kobo19_app_card( $app, $chapters ) . '</div>
		</div>
	</section>
' . kobo19_foot()
);

// --- 製品ページ ---
file_put_contents(
	"$out/app-{$app['slug']}.html",
	kobo19_head( $app['title'] . "｜$SITE" ) . kobo19_app_hero( $app )
	. '
	<section class="section">
		<div class="wrap">
			<div class="app-body">
' . $body . '
			</div>
		</div>
	</section>
' . kobo19_app_docs_section( $chapters, $pages ) . kobo19_foot()
);

// --- アプリ一覧（2本目が増えたときの見え方）---
file_put_contents(
	"$out/apps.html",
	kobo19_head( "アプリ｜$SITE" ) . '
	<section class="page-head">
		<div class="wrap">
			<p class="eyebrow">プロダクツ</p>
			<h1 class="page-head__title">アプリ</h1>
			<p class="page-head__lead">アプリや道具をつくって、ここに置いています。使い方の説明書と、サポートの窓口は、それぞれのページにあります。</p>
		</div>
	</section>

	<section class="section" style="padding-top:0;">
		<div class="wrap">
			<div class="app-grid">' . kobo19_app_card( $app, $chapters ) . '</div>
		</div>
	</section>
' . kobo19_foot()
);

// --- 資料一覧 ---
file_put_contents(
	"$out/docs.html",
	kobo19_head( "説明書とサポート｜$SITE" ) . '
	<section class="page-head">
		<div class="wrap">
			<p class="eyebrow">資料</p>
			<h1 class="page-head__title">説明書とサポート</h1>
			<p class="page-head__lead">アプリごとに、使い方の説明書と、サポート・プライバシー・利用規約をまとめています。</p>
		</div>
	</section>

	<section class="section" style="padding-top:0;">
		<div class="wrap">
			<div class="doc-group">
				<h2 class="doc-group__title"><a href="app-' . $app['slug'] . '.html">' . esc_html( $app['title'] ) . '</a></h2>
' . kobo19_chapter_list( $chapters ) . kobo19_policy_list( $pages ) . '
			</div>
		</div>
	</section>
' . kobo19_foot()
);

// --- 説明書の各章 ---
foreach ( $chapters as $i => $c ) {
	$prev = $i > 0 && isset( $chapters[ $i - 1 ] ) ? $chapters[ $i - 1 ] : null;
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

	file_put_contents(
		"$out/manual-{$c['slug']}.html",
		kobo19_head( $c['title'] . "｜$APP" ) . '
	<article class="manual">
		<div class="wrap manual__grid">
			<aside class="manual__side">' . kobo19_toc( $chapters, $app, $c['slug'] ) . '</aside>

			<div class="manual__main">
				<header class="manual__head">
					<p class="eyebrow"><a href="app-' . $app['slug'] . '.html">' . esc_html( $app['title'] ) . '</a> ／ 第' . ( $i + 1 ) . '章</p>
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
' . kobo19_foot()
	);
}

// --- サポート・プライバシー・利用規約 ---
foreach ( $pages as $slug => $page ) {
	file_put_contents(
		"$out/$slug.html",
		kobo19_head( $page['title'] . "｜$APP" ) . '
	<article class="manual">
		<div class="wrap manual__grid manual__grid--plain">
			<div class="manual__main">
				<header class="manual__head">
					<p class="eyebrow"><a href="app-' . $app['slug'] . '.html">' . esc_html( $app['title'] ) . '</a></p>
					<h1 class="manual__title">' . esc_html( $page['title'] ) . '</h1>
					<p class="manual__summary">' . esc_html( isset( $page['excerpt'] ) ? $page['excerpt'] : '' ) . '</p>
				</header>

				<div class="entry__body">
' . kobo19_render( $page['content'] ) . '
				</div>

				<nav class="chapter-nav" aria-label="アプリのページへ">
					<div class="chapter-nav__side">
						<a href="app-' . $app['slug'] . '.html"><span class="chapter-nav__label">戻る</span>'
						. '<span class="chapter-nav__title">← ' . esc_html( $app['title'] ) . '</span></a>
					</div>
					<div class="chapter-nav__side chapter-nav__side--next"></div>
				</nav>
			</div>
		</div>
	</article>
' . kobo19_foot()
	);
}

printf( "プレビューを書き出しました: %d ページ\n", 4 + count( $chapters ) + count( $pages ) );
