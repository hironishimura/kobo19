<?php
/**
 * カスタマイザーの設定。アプリの名前・価格・App Store の URL などをここで変えられます。
 *
 * 外観 → カスタマイズ → 「アプリの情報」から編集します。
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
		'kobo19_app',
		array(
			'title'       => 'アプリの情報',
			'priority'    => 30,
			'description' => 'トップページに出るアプリの名前・説明・配布先を編集します。',
		)
	);

	$fields = array(
		'kobo19_app_name'      => array(
			'label'   => 'アプリの名前',
			'default' => 'SujiCalc',
			'type'    => 'text',
		),
		'kobo19_app_tagline'   => array(
			'label'   => '一行の説明',
			'default' => '打つと右に答えが出る、ノート型の電卓。',
			'type'    => 'text',
		),
		'kobo19_app_lead'      => array(
			'label'   => 'トップの説明文',
			'default' => "式を打つと、その行の右側にすぐ答えが出ます。「＝」は要りません。\n書いた式はそのまま残るので、あとから数字を直せば、続きの計算も一度に合い直ります。",
			'type'    => 'textarea',
		),
		'kobo19_appstore_url'  => array(
			'label'   => 'App Store の URL',
			'default' => '',
			'type'    => 'url',
		),
		'kobo19_app_price'     => array(
			'label'   => '価格の表示',
			'default' => '',
			'type'    => 'text',
		),
		'kobo19_app_version'   => array(
			'label'   => 'バージョン',
			'default' => '1.0',
			'type'    => 'text',
		),
		'kobo19_app_requires'  => array(
			'label'   => '対応する OS',
			'default' => 'iOS 17 / iPadOS 17 / macOS 14 以降',
			'type'    => 'text',
		),
		'kobo19_app_size'      => array(
			'label'   => 'カテゴリ',
			'default' => '仕事効率化',
			'type'    => 'text',
		),
		'kobo19_contact_email' => array(
			'label'   => '連絡先メールアドレス',
			'default' => 'tapes-penne05@icloud.com',
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
				'sanitize_callback' => 'url' === $field['type'] ? 'esc_url_raw' : ( 'textarea' === $field['type'] ? 'wp_kses_post' : 'sanitize_text_field' ),
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			$id,
			array(
				'label'   => $field['label'],
				'section' => 'kobo19_app',
				'type'    => 'url' === $field['type'] ? 'url' : $field['type'],
			)
		);
	}
}
add_action( 'customize_register', 'kobo19_customize_register' );

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
