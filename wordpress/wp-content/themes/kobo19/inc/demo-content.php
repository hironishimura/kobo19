<?php
/**
 * テーマを有効化したときに、説明書の章と固定ページを一度だけ登録する。
 *
 * 文章そのものは inc/starter-manual.php と inc/starter-pages.php にあります。
 * 登録済みかどうかはオプション kobo19_starter_installed で判定するので、
 * 入れ直したいときはこのオプションを削除してからテーマを切り替え直してください。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

require_once get_template_directory() . '/inc/starter-manual.php';
require_once get_template_directory() . '/inc/starter-pages.php';

/**
 * 素の文章をブロックエディタの形式に変換する。
 *
 * 空行でまとまりを分け、次の書き方を見ます。
 *
 *   ## 見出し
 *   - 箇条書き
 *   | 表 | の | 行 |
 *   [calc] … [/calc]  ショートコードはそのまま1ブロックにする
 *   **強調**
 *
 * @param string $text 素の文章。
 * @return string
 */
function kobo19_to_blocks( $text ) {
	$chunks = preg_split( '/\n{2,}/', trim( $text ) );
	$blocks = array();

	foreach ( $chunks as $chunk ) {
		$chunk = trim( $chunk );

		if ( '' === $chunk ) {
			continue;
		}

		// ショートコード（[calc] など）はそのまま1ブロックに入れる。
		if ( '[' === substr( $chunk, 0, 1 ) ) {
			$blocks[] = "<!-- wp:shortcode -->\n{$chunk}\n<!-- /wp:shortcode -->";
			continue;
		}

		// 見出し
		if ( 0 === strpos( $chunk, '### ' ) ) {
			$heading  = kobo19_inline_markup( substr( $chunk, 4 ) );
			$blocks[] = "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">{$heading}</h3>\n<!-- /wp:heading -->";
			continue;
		}

		if ( 0 === strpos( $chunk, '## ' ) ) {
			$heading  = kobo19_inline_markup( substr( $chunk, 3 ) );
			$blocks[] = "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$heading}</h2>\n<!-- /wp:heading -->";
			continue;
		}

		// 表
		if ( '|' === substr( $chunk, 0, 1 ) ) {
			$blocks[] = kobo19_table_block( $chunk );
			continue;
		}

		// 箇条書き
		if ( 0 === strpos( $chunk, '- ' ) ) {
			$items = array();

			foreach ( explode( "\n", $chunk ) as $line ) {
				$line = trim( $line );
				if ( 0 === strpos( $line, '- ' ) ) {
					$items[] = "<!-- wp:list-item -->\n<li>" . kobo19_inline_markup( substr( $line, 2 ) ) . "</li>\n<!-- /wp:list-item -->";
				}
			}

			$blocks[] = "<!-- wp:list -->\n<ul class=\"wp-block-list\">" . implode( '', $items ) . "</ul>\n<!-- /wp:list -->";
			continue;
		}

		$paragraph = kobo19_inline_markup( str_replace( "\n", '<br>', $chunk ) );
		$blocks[]  = "<!-- wp:paragraph -->\n<p>{$paragraph}</p>\n<!-- /wp:paragraph -->";
	}

	return implode( "\n\n", $blocks );
}

/**
 * 縦棒で書いた表を、表ブロックに変換する。1行目を見出しとして扱います。
 *
 * @param string $chunk 表のまとまり。
 * @return string
 */
function kobo19_table_block( $chunk ) {
	$rows = array();

	foreach ( explode( "\n", $chunk ) as $line ) {
		$line = trim( $line );

		if ( '' === $line || '|' !== substr( $line, 0, 1 ) ) {
			continue;
		}

		// 区切りの行（|---|---|）は読み飛ばす。
		if ( preg_match( '/^\|[\s:-]+\|/', $line ) ) {
			continue;
		}

		$cells  = array_map( 'trim', explode( '|', trim( $line, '|' ) ) );
		$rows[] = $cells;
	}

	if ( ! $rows ) {
		return '';
	}

	$head = array_shift( $rows );

	$html  = "<!-- wp:table -->\n<figure class=\"wp-block-table\"><table><thead><tr>";
	foreach ( $head as $cell ) {
		$html .= '<th>' . kobo19_inline_markup( $cell ) . '</th>';
	}
	$html .= '</tr></thead><tbody>';

	foreach ( $rows as $row ) {
		$html .= '<tr>';
		foreach ( $row as $cell ) {
			$html .= '<td>' . kobo19_inline_markup( $cell ) . '</td>';
		}
		$html .= '</tr>';
	}

	return $html . "</tbody></table></figure>\n<!-- /wp:table -->";
}

/**
 * **強調** と `コード` をタグに置き換える。
 *
 * @param string $text 文章。
 * @return string
 */
function kobo19_inline_markup( $text ) {
	$text = preg_replace( '/\*\*(.+?)\*\*/u', '<strong>$1</strong>', $text );
	$text = preg_replace( '/`([^`]+)`/u', '<code>$1</code>', $text );

	// メールアドレスは自動でリンクにする。
	$text = preg_replace( '/([\w.+-]+@[\w-]+\.[\w.-]+)/u', '<a href="mailto:$1">$1</a>', $text );

	return $text;
}

/**
 * 説明書の章と固定ページを登録する。
 */
function kobo19_install_demo_content() {
	if ( get_option( 'kobo19_starter_installed' ) ) {
		return;
	}

	$order = 1;

	foreach ( kobo19_manual_source() as $chapter ) {
		if ( get_page_by_path( $chapter['slug'], OBJECT, 'manual' ) ) {
			++$order;
			continue;
		}

		wp_insert_post(
			array(
				'post_type'    => 'manual',
				'post_status'  => 'publish',
				'post_title'   => $chapter['title'],
				'post_name'    => $chapter['slug'],
				'post_excerpt' => $chapter['excerpt'],
				'post_content' => kobo19_to_blocks( $chapter['content'] ),
				'menu_order'   => $order,
			)
		);

		++$order;
	}

	foreach ( kobo19_page_source() as $slug => $page ) {
		if ( get_page_by_path( $slug ) ) {
			continue;
		}

		wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $page['title'],
				'post_name'    => $slug,
				'post_excerpt' => isset( $page['excerpt'] ) ? $page['excerpt'] : '',
				'post_content' => kobo19_to_blocks( $page['content'] ),
			)
		);
	}

	update_option( 'kobo19_starter_installed', KOBO19_VERSION );
}
