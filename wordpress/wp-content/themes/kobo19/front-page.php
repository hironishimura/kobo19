<?php
/**
 * トップページ。つくったプロダクツの入口。
 *
 * アプリの本数にかかわらず、ここは常に一覧です。それぞれのカードから
 * 製品ページへ飛びます。文言は 外観 → カスタマイズ → サイトの設定 から。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

get_header();

$kobo19_apps = kobo19_apps();
?>

<section class="hero">
	<div class="wrap">
		<div class="hero__text hero__text--wide">
			<p class="hero__eyebrow"><?php echo esc_html( kobo19_option( 'kobo19_home_eyebrow', 'WORKSHOP 19' ) ); ?></p>

			<h1 class="hero__title hero__title--site"><?php echo esc_html( kobo19_option( 'kobo19_home_title', 'つくったもの' ) ); ?></h1>

			<p class="hero__lead"><?php echo esc_html( kobo19_option( 'kobo19_home_lead', 'アプリや道具をつくって、ここに置いています。使い方の説明書と、サポートの窓口は、それぞれのページにあります。' ) ); ?></p>
		</div>
	</div>
</section>

<section class="section">
	<div class="wrap">
		<?php if ( $kobo19_apps ) : ?>
			<p class="eyebrow">プロダクツ<?php echo count( $kobo19_apps ) > 1 ? ' ／ ' . esc_html( (string) count( $kobo19_apps ) ) . ' 点' : ''; ?></p>

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
		<?php else : ?>
			<div class="notice">
				<p class="notice__code">まだありません</p>
				<p>管理画面の「アプリ」から1件追加すると、ここに出ます。説明書やサポートのページは「資料」から追加して、どのアプリのものかを選びます。</p>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
