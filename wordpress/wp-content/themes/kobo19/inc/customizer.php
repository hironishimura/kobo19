<?php
/**
 * カスタマイザーの設定。
 *
 * アプリごとの情報（名前・App Store の URL・バージョンなど）は、
 * それぞれのアプリの編集画面で入れます。ここはサイト全体の設定だけです。
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
		'kobo19_site',
		array(
			'title'       => 'サイトの設定',
			'priority'    => 30,
			'description' => 'アプリが2本以上になったときのトップの見出しと、連絡先を設定します。アプリ個別の情報は「アプリ」の編集画面で入れます。',
		)
	);

	$fields = array(
		'kobo19_home_eyebrow'  => array(
			'label'   => 'トップの小見出し',
			'default' => 'つくったもの',
			'type'    => 'text',
		),
		'kobo19_home_title'    => array(
			'label'   => 'トップの見出し',
			'default' => 'アプリ',
			'type'    => 'text',
		),
		'kobo19_home_lead'     => array(
			'label'   => 'トップの説明文',
			'default' => 'つくったアプリを置いています。使い方の説明書と、サポートの窓口はそれぞれのページにあります。',
			'type'    => 'textarea',
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
				'sanitize_callback' => 'textarea' === $field['type'] ? 'wp_kses_post' : 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			$id,
			array(
				'label'   => $field['label'],
				'section' => 'kobo19_site',
				'type'    => $field['type'],
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
