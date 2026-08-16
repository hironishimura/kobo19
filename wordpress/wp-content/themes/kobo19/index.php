<?php
/**
 * 記事一覧・検索結果・その他の受け皿。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

get_header();

$kobo19_title = '記事';
$kobo19_lead  = '';

if ( is_search() ) {
	$kobo19_title = '検索結果';
	/* translators: %s: 検索語 */
	$kobo19_lead = sprintf( '「%s」で探しました。', get_search_query() );
} elseif ( is_archive() ) {
	$kobo19_title = wp_strip_all_tags( get_the_archive_title() );
	$kobo19_lead  = wp_strip_all_tags( get_the_archive_description() );
} elseif ( is_home() ) {
	$kobo19_title = '記事';
	$kobo19_lead  = 'つくっている途中で分かったこと、道具の使いかたの覚え書き。';
}
?>

<section class="page-head">
	<div class="wrap">
		<p class="eyebrow">Notes</p>
		<h1 class="page-head__title"><?php echo esc_html( $kobo19_title ); ?></h1>
		<?php if ( $kobo19_lead ) : ?>
			<p class="page-head__lead"><?php echo esc_html( $kobo19_lead ); ?></p>
		<?php endif; ?>

		<?php if ( is_search() ) : ?>
			<div style="margin-top:2rem;"><?php get_search_form(); ?></div>
		<?php endif; ?>
	</div>
</section>

<section class="section" style="padding-top:0;">
	<div class="wrap">
		<?php if ( have_posts() ) : ?>
			<ul class="post-list">
				<?php while ( have_posts() ) : the_post(); ?>
					<li>
						<a href="<?php the_permalink(); ?>">
							<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></time>
							<div>
								<h2><?php the_title(); ?></h2>
								<?php $kobo19_summary = kobo19_summary( 88 ); ?>
								<?php if ( $kobo19_summary ) : ?>
									<p style="margin:0.4em 0 0;font-size:0.88rem;"><?php echo esc_html( $kobo19_summary ); ?></p>
								<?php endif; ?>
							</div>
						</a>
					</li>
				<?php endwhile; ?>
			</ul>

			<?php
			the_posts_pagination(
				array(
					'class'     => 'pagination',
					'mid_size'  => 2,
					'prev_text' => '前へ',
					'next_text' => '次へ',
				)
			);
			?>
		<?php else : ?>
			<div class="notice">
				<p class="notice__code">該当なし</p>
				<p>条件に合うものが見つかりませんでした。別の言葉で探すか、制作物の一覧をご覧ください。</p>
				<p style="margin-top:1.6rem;">
					<a class="btn btn--quiet" href="<?php echo esc_url( get_post_type_archive_link( 'work' ) ); ?>">制作物を見る</a>
				</p>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
