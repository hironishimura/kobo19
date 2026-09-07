<?php
/**
 * アプリの一覧。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<section class="page-head">
	<div class="wrap">
		<p class="eyebrow">プロダクツ</p>
		<h1 class="page-head__title">アプリ</h1>
		<p class="page-head__lead"><?php echo esc_html( kobo19_option( 'kobo19_home_lead', 'アプリや道具をつくって、ここに置いています。使い方の説明書と、サポートの窓口は、それぞれのページにあります。' ) ); ?></p>
	</div>
</section>

<section class="section" style="padding-top:0;">
	<div class="wrap">
		<?php if ( have_posts() ) : ?>
			<div class="app-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/app-card' );
				endwhile;
				?>
			</div>
		<?php else : ?>
			<div class="notice">
				<p class="notice__code">まだありません</p>
				<p>管理画面の「アプリ」から追加すると、ここに並びます。</p>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
