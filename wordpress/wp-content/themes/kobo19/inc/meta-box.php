<?php
/**
 * 制作物の表題欄（図番・材料・年・状態・リンク）を入力する編集画面。
 *
 * 図面の表題欄をそのまま入力項目にしています。ここに入れた値が
 * 一覧カードと詳細ページの枠内に表示されます。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

/**
 * 表題欄の項目定義を返す。
 *
 * @return array<string, array<string, mixed>>
 */
function kobo19_title_block_fields() {
	return array(
		'drawing_no' => array(
			'label' => '図番',
			'type'  => 'text',
			'hint'  => '例：W-01。空欄なら区分の頭文字と登録順で自動採番します。',
		),
		'year'       => array(
			'label' => '年',
			'type'  => 'text',
			'hint'  => '例：2026。制作した年を入れます。',
		),
		'stack'      => array(
			'label' => '材料',
			'type'  => 'text',
			'hint'  => '使った技術をスラッシュ区切りで。例：WordPress / PHP / 自作テーマ',
		),
		'status'     => array(
			'label'   => '状態',
			'type'    => 'select',
			'hint'    => '',
			'options' => array(
				'公開中' => '公開中',
				'運用中' => '運用中',
				'設計中' => '設計中',
				'制作中' => '制作中',
				'完了'   => '完了',
			),
		),
		'url'        => array(
			'label' => 'リンク',
			'type'  => 'url',
			'hint'  => '公開先やリポジトリのURL。空欄ならボタンは出ません。',
		),
		'url_label'  => array(
			'label' => 'リンクの文言',
			'type'  => 'text',
			'hint'  => '例：サイトを見る。空欄なら「開く」と表示します。',
		),
		'client'     => array(
			'label' => '施主',
			'type'  => 'text',
			'hint'  => '依頼元の名前。自主制作なら空欄のままで構いません。',
		),
	);
}

/**
 * 編集画面にメタボックスを追加する。
 */
function kobo19_add_title_block_meta_box() {
	add_meta_box(
		'kobo19-title-block',
		'表題欄',
		'kobo19_render_title_block_meta_box',
		'work',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'kobo19_add_title_block_meta_box' );

/**
 * メタボックスの中身を描画する。
 *
 * @param WP_Post $post 編集中の投稿。
 */
function kobo19_render_title_block_meta_box( $post ) {
	wp_nonce_field( 'kobo19_save_title_block', 'kobo19_title_block_nonce' );

	echo '<style>
		.kobo19-fields { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-top: 8px; }
		.kobo19-fields label { display: block; font-weight: 600; margin-bottom: 4px; }
		.kobo19-fields input, .kobo19-fields select { width: 100%; }
		.kobo19-fields p.description { margin-top: 4px; }
	</style>';

	echo '<div class="kobo19-fields">';

	foreach ( kobo19_title_block_fields() as $key => $field ) {
		$value = get_post_meta( $post->ID, '_kobo19_' . $key, true );
		$id    = 'kobo19_' . $key;

		echo '<div>';
		printf( '<label for="%s">%s</label>', esc_attr( $id ), esc_html( $field['label'] ) );

		if ( 'select' === $field['type'] ) {
			printf( '<select id="%s" name="%s">', esc_attr( $id ), esc_attr( $id ) );
			echo '<option value="">選択しない</option>';
			foreach ( $field['options'] as $option_value => $option_label ) {
				printf(
					'<option value="%s"%s>%s</option>',
					esc_attr( $option_value ),
					selected( $value, $option_value, false ),
					esc_html( $option_label )
				);
			}
			echo '</select>';
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
 * 表題欄の入力を保存する。
 *
 * @param int $post_id 投稿ID。
 */
function kobo19_save_title_block( $post_id ) {
	if ( ! isset( $_POST['kobo19_title_block_nonce'] ) ) {
		return;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['kobo19_title_block_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'kobo19_save_title_block' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	foreach ( kobo19_title_block_fields() as $key => $field ) {
		$input_name = 'kobo19_' . $key;

		if ( ! isset( $_POST[ $input_name ] ) ) {
			continue;
		}

		$raw = wp_unslash( $_POST[ $input_name ] );

		if ( 'url' === $field['type'] ) {
			$value = esc_url_raw( $raw );
		} else {
			$value = sanitize_text_field( $raw );
		}

		if ( '' === $value ) {
			delete_post_meta( $post_id, '_kobo19_' . $key );
		} else {
			update_post_meta( $post_id, '_kobo19_' . $key, $value );
		}
	}
}
add_action( 'save_post_work', 'kobo19_save_title_block' );

/**
 * 制作物一覧に図番・区分・年の列を足す。
 *
 * @param array<string, string> $columns 既存の列。
 * @return array<string, string>
 */
function kobo19_work_admin_columns( $columns ) {
	$new = array();

	foreach ( $columns as $key => $label ) {
		if ( 'title' === $key ) {
			$new['kobo19_drawing_no'] = '図番';
		}
		$new[ $key ] = $label;

		if ( 'title' === $key ) {
			$new['kobo19_year'] = '年';
		}
	}

	return $new;
}
add_filter( 'manage_work_posts_columns', 'kobo19_work_admin_columns' );

/**
 * 追加した列の中身を出力する。
 *
 * @param string $column  列のキー。
 * @param int    $post_id 投稿ID。
 */
function kobo19_work_admin_column_content( $column, $post_id ) {
	if ( 'kobo19_drawing_no' === $column ) {
		echo esc_html( kobo19_drawing_no( $post_id ) );
	}

	if ( 'kobo19_year' === $column ) {
		echo esc_html( get_post_meta( $post_id, '_kobo19_year', true ) );
	}
}
add_action( 'manage_work_posts_custom_column', 'kobo19_work_admin_column_content', 10, 2 );
