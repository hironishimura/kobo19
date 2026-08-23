<?php
/**
 * テーマを有効化したときに、最初のアプリと資料を一度だけ登録する。
 *
 * 文章そのものは inc/starter-app.php・starter-manual.php・starter-pages.php にあります。
 * 登録済みかどうかはオプション kobo19_starter_installed で判定するので、
 * 入れ直したいときはこのオプションを削除してからテーマを切り替え直してください。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

require_once get_template_directory() . '/inc/starter-app.php';
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
 * 最初のアプリと、その資料（説明書・サポート・プライバシー・利用規約）を登録する。
 */
function kobo19_install_demo_content() {
	if ( get_option( 'kobo19_starter_installed' ) ) {
		return;
	}

	$source = kobo19_app_source();
	$app    = get_page_by_path( $source['slug'], OBJECT, 'app' );

	if ( $app ) {
		$app_id = $app->ID;
	} else {
		$app_id = wp_insert_post(
			array(
				'post_type'    => 'app',
				'post_status'  => 'publish',
				'post_title'   => $source['title'],
				'post_name'    => $source['slug'],
				'post_excerpt' => $source['excerpt'],
				'post_content' => kobo19_to_blocks( $source['content'] ),
				'menu_order'   => 1,
			)
		);

		if ( ! $app_id || is_wp_error( $app_id ) ) {
			return;
		}

		foreach ( $source['meta'] as $key => $value ) {
			if ( '' !== $value ) {
				update_post_meta( $app_id, '_kobo19_' . $key, $value );
			}
		}
	}

	// 説明書の章。スラッグはアプリ名で始めておくと、アプリが増えても衝突しません。
	$order = 1;

	foreach ( kobo19_manual_source() as $chapter ) {
		$slug = $source['slug'] . '-' . $chapter['slug'];

		if ( ! get_page_by_path( $slug, OBJECT, 'doc' ) ) {
			kobo19_insert_doc(
				array(
					'slug'    => $slug,
					'title'   => $chapter['title'],
					'excerpt' => $chapter['excerpt'],
					'content' => $chapter['content'],
					'app_id'  => $app_id,
					'kind'    => 'chapter',
					'order'   => $order,
				)
			);
		}

		++$order;
	}

	// サポート・プライバシーポリシー・利用規約。
	foreach ( kobo19_page_source() as $kind => $page ) {
		$slug = $source['slug'] . '-' . $kind;

		if ( get_page_by_path( $slug, OBJECT, 'doc' ) ) {
			continue;
		}

		kobo19_insert_doc(
			array(
				'slug'    => $slug,
				'title'   => $page['title'],
				'excerpt' => isset( $page['excerpt'] ) ? $page['excerpt'] : '',
				'content' => $page['content'],
				'app_id'  => $app_id,
				'kind'    => $kind,
				'order'   => 0,
			)
		);
	}

	update_option( 'kobo19_starter_installed', KOBO19_VERSION );
}

/**
 * 資料を1件登録する。
 *
 * @param array<string, mixed> $doc 登録する内容。
 * @return int|false 投稿ID。
 */
function kobo19_insert_doc( $doc ) {
	$post_id = wp_insert_post(
		array(
			'post_type'    => 'doc',
			'post_status'  => 'publish',
			'post_title'   => $doc['title'],
			'post_name'    => $doc['slug'],
			'post_excerpt' => $doc['excerpt'],
			'post_content' => kobo19_to_blocks( $doc['content'] ),
			'menu_order'   => (int) $doc['order'],
		)
	);

	if ( ! $post_id || is_wp_error( $post_id ) ) {
		return false;
	}

	update_post_meta( $post_id, '_kobo19_app', (int) $doc['app_id'] );
	update_post_meta( $post_id, '_kobo19_kind', $doc['kind'] );

	return $post_id;
}
