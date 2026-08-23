<?php
/**
 * アプリの製品ページ。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	get_template_part(
		'template-parts/app-hero',
		null,
		array(
			'app_id'  => get_the_ID(),
			'heading' => 'h1',
		)
	);

	if ( trim( wp_strip_all_tags( get_the_content() ) ) ) :
		?>
		<section class="section">
			<div class="wrap">
				<div class="app-body"><?php the_content(); ?></div>
			</div>
		</section>
		<?php
	endif;

	get_template_part( 'template-parts/app-docs', null, array( 'app_id' => get_the_ID() ) );

endwhile;

get_footer();
