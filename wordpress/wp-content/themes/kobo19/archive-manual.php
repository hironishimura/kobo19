<?php
/**
 * 説明書の目次。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

get_header();

$kobo19_app = kobo19_option( 'kobo19_app_name', 'SujiCalc' );
?>

<section class="page-head">
	<div class="wrap">
		<p class="eyebrow"><?php echo esc_html( $kobo19_app ); ?> ／ 説明書</p>
		<h1 class="page-head__title">使い方</h1>
		<p class="page-head__lead">式の書き方から、単位・グラフ・プログラム・同期まで。上から順に読めば一通り分かるように並べています。</p>
	</div>
</section>

<section class="section" style="padding-top:0;">
	<div class="wrap">
		<?php if ( have_posts() ) : ?>
			<ol class="chapter-list">
				<?php
				$kobo19_index = 0;
				while ( have_posts() ) :
					the_post();
					++$kobo19_index;
					?>
					<li class="chapter-list__item">
						<a href="<?php the_permalink(); ?>">
							<span class="chapter-list__no"><?php echo esc_html( sprintf( '%02d', $kobo19_index ) ); ?></span>
							<span class="chapter-list__body">
								<span class="chapter-list__title"><?php the_title(); ?></span>
								<?php $kobo19_summary = kobo19_summary( 74 ); ?>
								<?php if ( $kobo19_summary ) : ?>
									<span class="chapter-list__text"><?php echo esc_html( $kobo19_summary ); ?></span>
								<?php endif; ?>
							</span>
						</a>
					</li>
				<?php endwhile; ?>
			</ol>
		<?php else : ?>
			<div class="notice">
				<p class="notice__code">まだ章がありません</p>
				<p>管理画面の「説明書」から章を追加すると、ここに順番に並びます。</p>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
