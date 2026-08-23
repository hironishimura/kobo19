<?php
/**
 * トップページ。
 *
 * アプリが1本のときは、そのアプリの製品ページをそのまま出します。
 * 2本以上になったら、自動でアプリの一覧に切り替わります。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

get_header();

$kobo19_apps = kobo19_apps();
?>

<?php if ( 1 === count( $kobo19_apps ) ) : ?>

	<?php
	// アプリが1本だけのうちは、トップをそのアプリのページにする。
	$kobo19_app = $kobo19_apps[0];

	get_template_part(
		'template-parts/app-hero',
		null,
		array(
			'app_id'  => $kobo19_app->ID,
			'heading' => 'h1',
		)
	);

	$kobo19_body = apply_filters( 'the_content', $kobo19_app->post_content );

	if ( trim( wp_strip_all_tags( $kobo19_body ) ) ) :
		?>
		<section class="section">
			<div class="wrap">
				<div class="app-body"><?php echo $kobo19_body; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			</div>
		</section>
		<?php
	endif;

	get_template_part( 'template-parts/app-docs', null, array( 'app_id' => $kobo19_app->ID ) );
	?>

<?php elseif ( $kobo19_apps ) : ?>

	<section class="page-head">
		<div class="wrap">
			<p class="eyebrow"><?php echo esc_html( kobo19_option( 'kobo19_home_eyebrow', 'つくったもの' ) ); ?></p>
			<h1 class="page-head__title"><?php echo esc_html( kobo19_option( 'kobo19_home_title', 'アプリ' ) ); ?></h1>
			<p class="page-head__lead"><?php echo esc_html( kobo19_option( 'kobo19_home_lead', 'つくったアプリを置いています。使い方の説明書と、サポートの窓口はそれぞれのページにあります。' ) ); ?></p>
		</div>
	</section>

	<section class="section" style="padding-top:0;">
		<div class="wrap">
			<div class="app-grid">
				<?php
				foreach ( $kobo19_apps as $kobo19_app ) :
					$GLOBALS['post'] = $kobo19_app; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					setup_postdata( $kobo19_app );
					get_template_part( 'template-parts/app-card' );
				endforeach;
				wp_reset_postdata();
				?>
			</div>
		</div>
	</section>

<?php else : ?>

	<section class="page-head">
		<div class="wrap">
			<p class="eyebrow">はじめに</p>
			<h1 class="page-head__title">アプリを登録してください</h1>
			<p class="page-head__lead">管理画面の「アプリ」から1件追加すると、ここに出ます。説明書やサポートのページは「資料」から追加して、どのアプリのものかを選びます。</p>
		</div>
	</section>

<?php endif; ?>

<?php
get_footer();
