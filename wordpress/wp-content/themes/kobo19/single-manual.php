<?php
/**
 * 説明書の各章。左に目次、右に本文。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$kobo19_siblings = kobo19_chapter_siblings();
	$kobo19_archive  = get_post_type_archive_link( 'manual' );
	?>

<article class="manual">
	<div class="wrap manual__grid">

		<aside class="manual__side">
			<?php kobo19_manual_toc( get_the_ID() ); ?>
		</aside>

		<div class="manual__main">
			<header class="manual__head">
				<p class="eyebrow">
					<?php if ( $kobo19_archive ) : ?>
						<a href="<?php echo esc_url( $kobo19_archive ); ?>">説明書</a> ／
					<?php endif; ?>
					第<?php echo esc_html( (string) kobo19_chapter_number() ); ?>章
				</p>

				<h1 class="manual__title">
					<span class="manual__no"><?php echo esc_html( kobo19_chapter_label() ); ?></span>
					<?php the_title(); ?>
				</h1>

				<?php if ( has_excerpt() ) : ?>
					<p class="manual__summary"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
			</header>

			<div class="entry__body">
				<?php the_content(); ?>
			</div>

			<nav class="chapter-nav" aria-label="前後の章">
				<div class="chapter-nav__side">
					<?php if ( $kobo19_siblings['prev'] ) : ?>
						<a href="<?php echo esc_url( get_permalink( $kobo19_siblings['prev'] ) ); ?>">
							<span class="chapter-nav__label">前の章</span>
							<span class="chapter-nav__title">← <?php echo esc_html( get_the_title( $kobo19_siblings['prev'] ) ); ?></span>
						</a>
					<?php endif; ?>
				</div>

				<div class="chapter-nav__side chapter-nav__side--next">
					<?php if ( $kobo19_siblings['next'] ) : ?>
						<a href="<?php echo esc_url( get_permalink( $kobo19_siblings['next'] ) ); ?>">
							<span class="chapter-nav__label">次の章</span>
							<span class="chapter-nav__title"><?php echo esc_html( get_the_title( $kobo19_siblings['next'] ) ); ?> →</span>
						</a>
					<?php endif; ?>
				</div>
			</nav>
		</div>

	</div>
</article>

	<?php
endwhile;

get_footer();
