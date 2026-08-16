<?php
/**
 * ページが見つからないとき。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<section class="page-head">
	<div class="wrap">
		<p class="eyebrow">404</p>
		<h1 class="page-head__title">この図面はありません</h1>
		<p class="page-head__lead">アドレスが変わったか、まだ引いていないページです。制作物の一覧から探してみてください。</p>

		<div class="notice">
			<p class="notice__code">図番なし</p>
			<?php get_search_form(); ?>
			<p style="margin-top:1.8rem;">
				<a class="btn" href="<?php echo esc_url( get_post_type_archive_link( 'work' ) ); ?>">制作物を見る</a>
			</p>
		</div>
	</div>
</section>

<?php
get_footer();
