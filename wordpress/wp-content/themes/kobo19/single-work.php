<?php
/**
 * 制作物の詳細ページ。左に本文、右に表題欄。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$kobo19_stack     = kobo19_stack_list();
	$kobo19_year      = kobo19_meta( 'year' );
	$kobo19_status    = kobo19_meta( 'status' );
	$kobo19_client    = kobo19_meta( 'client' );
	$kobo19_url       = kobo19_meta( 'url' );
	$kobo19_url_label = kobo19_meta( 'url_label' );
	$kobo19_terms     = get_the_terms( get_the_ID(), 'work_category' );
	?>

<article class="work-single">
	<div class="wrap">

		<header class="work-single__head">
			<div>
				<p class="eyebrow">
					<?php echo esc_html( kobo19_drawing_no() ); ?>
					<?php if ( $kobo19_terms && ! is_wp_error( $kobo19_terms ) ) : ?>
						／ <a href="<?php echo esc_url( get_term_link( $kobo19_terms[0] ) ); ?>"><?php echo esc_html( $kobo19_terms[0]->name ); ?></a>
					<?php endif; ?>
				</p>

				<h1 class="work-single__title"><?php the_title(); ?></h1>

				<?php if ( has_excerpt() ) : ?>
					<p class="work-single__summary"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>

				<?php if ( $kobo19_url ) : ?>
					<p style="margin-top:2rem;">
						<a class="btn" href="<?php echo esc_url( $kobo19_url ); ?>" target="_blank" rel="noopener">
							<?php echo esc_html( $kobo19_url_label ? $kobo19_url_label : '開く' ); ?>
						</a>
					</p>
				<?php endif; ?>
			</div>

			<div class="spec">
				<div class="spec__row">
					<span class="spec__key">図番</span>
					<span class="spec__value spec__value--mono"><?php echo esc_html( kobo19_drawing_no() ); ?></span>
				</div>
				<div class="spec__row">
					<span class="spec__key">区分</span>
					<span class="spec__value"><?php echo esc_html( kobo19_work_category_name() ); ?></span>
				</div>
				<?php if ( $kobo19_year ) : ?>
					<div class="spec__row">
						<span class="spec__key">年</span>
						<span class="spec__value spec__value--mono"><?php echo esc_html( $kobo19_year ); ?></span>
					</div>
				<?php endif; ?>
				<?php if ( $kobo19_status ) : ?>
					<div class="spec__row">
						<span class="spec__key">状態</span>
						<span class="spec__value"><?php echo esc_html( $kobo19_status ); ?></span>
					</div>
				<?php endif; ?>
				<?php if ( $kobo19_client ) : ?>
					<div class="spec__row">
						<span class="spec__key">施主</span>
						<span class="spec__value"><?php echo esc_html( $kobo19_client ); ?></span>
					</div>
				<?php endif; ?>
				<?php if ( $kobo19_stack ) : ?>
					<div class="spec__row">
						<span class="spec__key">材料</span>
						<span class="spec__value spec__value--mono"><?php echo esc_html( implode( ' / ', $kobo19_stack ) ); ?></span>
					</div>
				<?php endif; ?>
			</div>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="work-single__figure">
				<?php the_post_thumbnail( 'large' ); ?>
			</figure>
		<?php endif; ?>

		<div class="work-body">
			<?php the_content(); ?>
		</div>

		<?php kobo19_work_pagination(); ?>

	</div>
</article>

	<?php
endwhile;

get_footer();
