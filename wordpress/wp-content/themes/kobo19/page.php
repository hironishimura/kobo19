<?php
/**
 * 固定ページ。
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
			<p class="eyebrow"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
			<h1 class="entry__title"><?php the_title(); ?></h1>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="work-single__figure">
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
	</div>
</article>

	<?php
endwhile;

get_footer();
