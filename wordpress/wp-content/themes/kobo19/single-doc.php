<?php
/**
 * 資料のページ。説明書の章は左に目次を出し、前後の章へ送ります。
 * サポート・プライバシー・利用規約は本文だけを出します。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$kobo19_kind   = kobo19_doc_kind();
	$kobo19_app_id = kobo19_doc_app();
	$kobo19_app    = $kobo19_app_id ? get_post( $kobo19_app_id ) : null;
	$kobo19_number = kobo19_chapter_number();
	$kobo19_is_ch  = ( 'chapter' === $kobo19_kind && $kobo19_number );
	?>

<article class="manual">
	<div class="wrap manual__grid<?php echo $kobo19_is_ch ? '' : ' manual__grid--plain'; ?>">

		<?php if ( $kobo19_is_ch ) : ?>
			<aside class="manual__side">
				<?php kobo19_manual_toc( $kobo19_app_id, get_the_ID() ); ?>
			</aside>
		<?php endif; ?>

		<div class="manual__main">
			<header class="manual__head">
				<p class="eyebrow">
					<?php if ( $kobo19_app ) : ?>
						<a href="<?php echo esc_url( get_permalink( $kobo19_app ) ); ?>"><?php echo esc_html( get_the_title( $kobo19_app ) ); ?></a>
					<?php endif; ?>
					<?php if ( $kobo19_is_ch ) : ?>
						／ 第<?php echo esc_html( (string) $kobo19_number ); ?>章
					<?php endif; ?>
				</p>

				<h1 class="manual__title">
					<?php if ( $kobo19_is_ch ) : ?>
						<span class="manual__no"><?php echo esc_html( kobo19_chapter_label() ); ?></span>
					<?php endif; ?>
					<?php the_title(); ?>
				</h1>

				<?php if ( has_excerpt() ) : ?>
					<p class="manual__summary"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
			</header>

			<div class="entry__body">
				<?php the_content(); ?>
			</div>

			<?php if ( $kobo19_is_ch ) : ?>
				<?php $kobo19_siblings = kobo19_chapter_siblings(); ?>
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
			<?php elseif ( $kobo19_app ) : ?>
				<nav class="chapter-nav" aria-label="アプリのページへ">
					<div class="chapter-nav__side">
						<a href="<?php echo esc_url( get_permalink( $kobo19_app ) ); ?>">
							<span class="chapter-nav__label">戻る</span>
							<span class="chapter-nav__title">← <?php echo esc_html( get_the_title( $kobo19_app ) ); ?></span>
						</a>
					</div>
					<div class="chapter-nav__side chapter-nav__side--next"></div>
				</nav>
			<?php endif; ?>
		</div>

	</div>
</article>

	<?php
endwhile;

get_footer();
