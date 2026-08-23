<?php
/**
 * 編集画面の入力欄。
 *
 * アプリ … App Store に出す情報と、トップに出すデモ。
 * 資料   … どのアプリのものか、どの種別か。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

/**
 * アプリの入力項目。
 *
 * @return array<string, array<string, mixed>>
 */
function kobo19_app_fields() {
	return array(
		'tagline'  => array(
			'label' => '一行の説明',
			'type'  => 'text',
			'hint'  => 'アプリ名のすぐ下に出ます。例：打つと右に答えが出る、ノート型の電卓。',
		),
		'lead'     => array(
			'label' => '説明文',
			'type'  => 'textarea',
			'hint'  => '一行の説明の下に出る、2〜3行の文章。',
		),
		'store'    => array(
			'label' => 'App Store の URL',
			'type'  => 'url',
			'hint'  => '入れると「App Store で見る」ボタンが出ます。審査が通ってからで構いません。',
		),
		'version'  => array(
			'label' => 'バージョン',
			'type'  => 'text',
			'hint'  => '例：1.0',
		),
		'requires' => array(
			'label' => '対応する OS',
			'type'  => 'text',
			'hint'  => '例：iOS 17 / iPadOS 17 / macOS 14 以降',
		),
		'category' => array(
			'label' => 'カテゴリ',
			'type'  => 'text',
			'hint'  => '例：仕事効率化',
		),
		'price'    => array(
			'label' => '価格の表示',
			'type'  => 'text',
			'hint'  => '空欄なら出ません。例：無料、350円',
		),
		'status'   => array(
			'label'   => '状態',
			'type'    => 'select',
			'hint'    => '公開前のアプリは「準備中」にしておくと、一覧にその印が出ます。',
			'options' => array(
				''         => '指定しない',
				'公開中'   => '公開中',
				'審査中'   => '審査中',
				'準備中'   => '準備中',
				'制作中'   => '制作中',
			),
		),
		'demo'     => array(
			'label' => 'トップに出すデモ',
			'type'  => 'textarea',
			'hint'  => '[calc] のショートコードをそのまま書けます。空欄なら何も出ません。',
			'rows'  => 10,
		),
	);
}

/**
 * メタボックスを登録する。
 */
function kobo19_add_meta_boxes() {
	add_meta_box( 'kobo19-app', 'アプリの情報', 'kobo19_render_app_box', 'app', 'normal', 'high' );
	add_meta_box( 'kobo19-doc', 'この資料について', 'kobo19_render_doc_box', 'doc', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'kobo19_add_meta_boxes' );

/**
 * 共通のスタイル。
 */
function kobo19_meta_style() {
	echo '<style>
		.kobo19-fields { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; margin-top: 8px; }
		.kobo19-fields .kobo19-wide { grid-column: 1 / -1; }
		.kobo19-fields label { display: block; font-weight: 600; margin-bottom: 4px; }
		.kobo19-fields input, .kobo19-fields select, .kobo19-fields textarea { width: 100%; }
		.kobo19-fields textarea { font-family: ui-monospace, Menlo, monospace; font-size: 13px; }
		.kobo19-fields p.description { margin-top: 4px; }
	</style>';
}

/**
 * アプリの入力欄を描く。
 *
 * @param WP_Post $post 編集中の投稿。
 */
function kobo19_render_app_box( $post ) {
	wp_nonce_field( 'kobo19_save_app', 'kobo19_app_nonce' );
	kobo19_meta_style();

	echo '<div class="kobo19-fields">';

	foreach ( kobo19_app_fields() as $key => $field ) {
		$value = get_post_meta( $post->ID, '_kobo19_' . $key, true );
		$id    = 'kobo19_' . $key;
		$wide  = in_array( $field['type'], array( 'textarea' ), true ) ? ' class="kobo19-wide"' : '';

		echo '<div' . $wide . '>';
		printf( '<label for="%s">%s</label>', esc_attr( $id ), esc_html( $field['label'] ) );

		if ( 'select' === $field['type'] ) {
			printf( '<select id="%s" name="%s">', esc_attr( $id ), esc_attr( $id ) );
			foreach ( $field['options'] as $option_value => $option_label ) {
				printf(
					'<option value="%s"%s>%s</option>',
					esc_attr( $option_value ),
					selected( $value, $option_value, false ),
					esc_html( $option_label )
				);
			}
			echo '</select>';
		} elseif ( 'textarea' === $field['type'] ) {
			printf(
				'<textarea id="%s" name="%s" rows="%d">%s</textarea>',
				esc_attr( $id ),
				esc_attr( $id ),
				isset( $field['rows'] ) ? (int) $field['rows'] : 4,
				esc_textarea( $value )
			);
		} else {
			printf(
				'<input type="%s" id="%s" name="%s" value="%s" />',
				esc_attr( 'url' === $field['type'] ? 'url' : 'text' ),
				esc_attr( $id ),
				esc_attr( $id ),
				esc_attr( $value )
			);
		}

		if ( ! empty( $field['hint'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $field['hint'] ) );
		}

		echo '</div>';
	}

	echo '</div>';
}

/**
 * 資料の入力欄を描く。
 *
 * @param WP_Post $post 編集中の投稿。
 */
function kobo19_render_doc_box( $post ) {
	wp_nonce_field( 'kobo19_save_doc', 'kobo19_doc_nonce' );

	$app  = (int) get_post_meta( $post->ID, '_kobo19_app', true );
	$kind = get_post_meta( $post->ID, '_kobo19_kind', true );
	$apps = get_posts(
		array(
			'post_type'      => 'app',
			'post_status'    => array( 'publish', 'draft', 'pending', 'future' ),
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);

	echo '<p><label for="kobo19_app"><strong>どのアプリの資料か</strong></label><br>';
	echo '<select id="kobo19_app" name="kobo19_app" style="width:100%">';
	echo '<option value="">選んでください</option>';

	foreach ( $apps as $item ) {
		printf(
			'<option value="%d"%s>%s</option>',
			$item->ID,
			selected( $app, $item->ID, false ),
			esc_html( get_the_title( $item ) )
		);
	}

	echo '</select></p>';

	if ( ! $apps ) {
		echo '<p class="description">先に「アプリ」を1件登録してください。</p>';
	}

	echo '<p><label for="kobo19_kind"><strong>種別</strong></label><br>';
	echo '<select id="kobo19_kind" name="kobo19_kind" style="width:100%">';

	foreach ( kobo19_doc_kinds() as $value => $label ) {
		printf(
			'<option value="%s"%s>%s</option>',
			esc_attr( $value ),
			selected( $kind ? $kind : 'chapter', $value, false ),
			esc_html( $label )
		);
	}

	echo '</select></p>';
	echo '<p class="description">「説明書の章」だけが目次に並び、章番号が付きます。順番は「ページ属性 → 順序」で決まります。</p>';
}

/**
 * アプリの入力を保存する。
 *
 * @param int $post_id 投稿ID。
 */
function kobo19_save_app( $post_id ) {
	if ( ! isset( $_POST['kobo19_app_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['kobo19_app_nonce'] ) ), 'kobo19_save_app' ) ) {
		return;
	}

	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	foreach ( kobo19_app_fields() as $key => $field ) {
		$name = 'kobo19_' . $key;

		if ( ! isset( $_POST[ $name ] ) ) {
			continue;
		}

		$raw = wp_unslash( $_POST[ $name ] );

		if ( 'url' === $field['type'] ) {
			$value = esc_url_raw( $raw );
		} elseif ( 'textarea' === $field['type'] ) {
			// [calc] のショートコードをそのまま入れられるようにする。
			$value = wp_kses_post( $raw );
		} else {
			$value = sanitize_text_field( $raw );
		}

		if ( '' === trim( (string) $value ) ) {
			delete_post_meta( $post_id, '_kobo19_' . $key );
		} else {
			update_post_meta( $post_id, '_kobo19_' . $key, $value );
		}
	}
}
add_action( 'save_post_app', 'kobo19_save_app' );

/**
 * 資料の入力を保存する。
 *
 * @param int $post_id 投稿ID。
 */
function kobo19_save_doc( $post_id ) {
	if ( ! isset( $_POST['kobo19_doc_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['kobo19_doc_nonce'] ) ), 'kobo19_save_doc' ) ) {
		return;
	}

	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['kobo19_app'] ) ) {
		$app = (int) $_POST['kobo19_app'];
		if ( $app ) {
			update_post_meta( $post_id, '_kobo19_app', $app );
		} else {
			delete_post_meta( $post_id, '_kobo19_app' );
		}
	}

	if ( isset( $_POST['kobo19_kind'] ) ) {
		$kind  = sanitize_text_field( wp_unslash( $_POST['kobo19_kind'] ) );
		$kinds = kobo19_doc_kinds();
		update_post_meta( $post_id, '_kobo19_kind', isset( $kinds[ $kind ] ) ? $kind : 'chapter' );
	}
}
add_action( 'save_post_doc', 'kobo19_save_doc' );

/**
 * 資料の一覧に「アプリ」と「種別」の列を足す。
 *
 * @param array<string, string> $columns 既存の列。
 * @return array<string, string>
 */
function kobo19_doc_columns( $columns ) {
	$new = array();

	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;

		if ( 'title' === $key ) {
			$new['kobo19_app']  = 'アプリ';
			$new['kobo19_kind'] = '種別';
		}
	}

	return $new;
}
add_filter( 'manage_doc_posts_columns', 'kobo19_doc_columns' );

/**
 * 足した列の中身を出す。
 *
 * @param string $column  列のキー。
 * @param int    $post_id 投稿ID。
 */
function kobo19_doc_column_content( $column, $post_id ) {
	if ( 'kobo19_app' === $column ) {
		$app = kobo19_doc_app( $post_id );
		echo $app ? esc_html( get_the_title( $app ) ) : '—';
	}

	if ( 'kobo19_kind' === $column ) {
		$kinds = kobo19_doc_kinds();
		$kind  = kobo19_doc_kind( $post_id );
		echo esc_html( isset( $kinds[ $kind ] ) ? $kinds[ $kind ] : '—' );
	}
}
add_action( 'manage_doc_posts_custom_column', 'kobo19_doc_column_content', 10, 2 );

/**
 * アプリ一覧に「状態」と「資料の数」を出す。
 *
 * @param array<string, string> $columns 既存の列。
 * @return array<string, string>
 */
function kobo19_app_columns( $columns ) {
	$new = array();

	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;

		if ( 'title' === $key ) {
			$new['kobo19_status'] = '状態';
			$new['kobo19_docs']   = '資料';
		}
	}

	return $new;
}
add_filter( 'manage_app_posts_columns', 'kobo19_app_columns' );

/**
 * 足した列の中身を出す。
 *
 * @param string $column  列のキー。
 * @param int    $post_id 投稿ID。
 */
function kobo19_app_column_content( $column, $post_id ) {
	if ( 'kobo19_status' === $column ) {
		$status = get_post_meta( $post_id, '_kobo19_status', true );
		echo $status ? esc_html( $status ) : '—';
	}

	if ( 'kobo19_docs' === $column ) {
		$counts = array();

		foreach ( kobo19_doc_kinds() as $kind => $label ) {
			$found = count( kobo19_app_docs( $post_id, $kind ) );
			if ( $found ) {
				$counts[] = ( 'chapter' === $kind ) ? sprintf( '説明書 %d章', $found ) : $label;
			}
		}

		echo $counts ? esc_html( implode( '／', $counts ) ) : '—';
	}
}
add_action( 'manage_app_posts_custom_column', 'kobo19_app_column_content', 10, 2 );
