<?php
/**
 * カスタマイザーの設定。屋号・肩書き・連絡先など、画面の文言をここで変えられます。
 *
 * 外観 → カスタマイズ → 「19工房の設定」から編集します。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

/**
 * カスタマイザーの項目を登録する。
 *
 * @param WP_Customize_Manager $wp_customize カスタマイザー。
 */
function kobo19_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	$wp_customize->add_section(
		'kobo19_settings',
		array(
			'title'       => '19工房の設定',
			'priority'    => 30,
			'description' => 'トップページの見出しやフッターの連絡先を編集します。',
		)
	);

	$fields = array(
		'kobo19_hero_eyebrow'  => array(
			'label'   => 'トップの小見出し',
			'default' => 'WORKSHOP 19 ／ 制作物一覧',
			'type'    => 'text',
		),
		'kobo19_hero_title'    => array(
			'label'   => 'トップの見出し',
			'default' => "つくるものは違っても、\nやることは同じです。",
			'type'    => 'textarea',
		),
		'kobo19_hero_lead'     => array(
			'label'   => 'トップの説明文',
			'default' => 'サイトも、業務の道具も、Macアプリも、椅子の3Dモデルも。図面を引いて、寸法を決めて、かたちにする。19工房はその繰り返しでできています。',
			'type'    => 'textarea',
		),
		'kobo19_hero_note'     => array(
			'label'   => 'トップ図版の注記',
			'default' => 'D-03 Yチェア ／ Rhinoceros・Blender',
			'type'    => 'text',
		),
		'kobo19_contact_label' => array(
			'label'   => 'フッターの連絡先の見出し',
			'default' => '相談する',
			'type'    => 'text',
		),
		'kobo19_contact_text'  => array(
			'label'   => 'フッターの連絡先の説明',
			'default' => 'つくりたいものが決まっていなくても構いません。困っている作業の話から始めましょう。',
			'type'    => 'textarea',
		),
		'kobo19_contact_email' => array(
			'label'   => '連絡先メールアドレス',
			'default' => '',
			'type'    => 'text',
		),
		'kobo19_established'   => array(
			'label'   => '開設年（フッターに表示）',
			'default' => '2026',
			'type'    => 'text',
		),
	);

	foreach ( $fields as $id => $field ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $field['default'],
				'sanitize_callback' => 'textarea' === $field['type'] ? 'wp_kses_post' : 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			$id,
			array(
				'label'   => $field['label'],
				'section' => 'kobo19_settings',
				'type'    => $field['type'],
			)
		);
	}

	$wp_customize->add_setting(
		'kobo19_show_hero_figure',
		array(
			'default'           => true,
			'sanitize_callback' => 'kobo19_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'kobo19_show_hero_figure',
		array(
			'label'       => 'トップに線画を表示する',
			'description' => '外すと見出しと説明文だけになります。',
			'section'     => 'kobo19_settings',
			'type'        => 'checkbox',
		)
	);
}
add_action( 'customize_register', 'kobo19_customize_register' );

/**
 * チェックボックスの値を真偽値にそろえる。
 *
 * @param mixed $checked 入力値。
 * @return bool
 */
function kobo19_sanitize_checkbox( $checked ) {
	return ( isset( $checked ) && true === (bool) $checked );
}

/**
 * カスタマイザーの値を取り出す。
 *
 * @param string $key     設定キー。
 * @param string $default 既定値。
 * @return string
 */
function kobo19_option( $key, $default = '' ) {
	return (string) get_theme_mod( $key, $default );
}

/**
 * プレビュー画面で見出しを即時更新するスクリプト。
 */
function kobo19_customize_preview_js() {
	wp_enqueue_script(
		'kobo19-customizer',
		get_template_directory_uri() . '/assets/js/customizer.js',
		array( 'customize-preview' ),
		KOBO19_VERSION,
		true
	);
}
add_action( 'customize_preview_init', 'kobo19_customize_preview_js' );
