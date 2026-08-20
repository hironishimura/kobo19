<?php
/**
 * 投稿の詳細ページ（お知らせ・記事）。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>

<article class="entry">
	<div class="wrap">
		<header>
			<p class="eyebrow">Notes</p>
			<h1 class="entry__title"><?php the_title(); ?></h1>
			<p class="entry__meta">
				<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></time>
			</p>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="entry__figure">
				<?php the_post_thumbnail( 'large' ); ?>
			</figure>
		<?php endif; ?>

		<div class="entry__body">
			<?php
			the_content();

			wp_link_pages(
				array(
					'before' => '<nav class="pagination">',
					'after'  => '</nav>',
				)
			);
			?>
		</div>

		<nav class="chapter-nav" aria-label="前後の記事">
			<div class="chapter-nav__side"><?php previous_post_link( '%link', '<span class="chapter-nav__title">← %title</span>' ); ?></div>
			<div class="chapter-nav__side chapter-nav__side--next"><?php next_post_link( '%link', '<span class="chapter-nav__title">%title →</span>' ); ?></div>
		</nav>

		<?php
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
		?>
	</div>
</article>

	<?php
endwhile;

get_footer();
