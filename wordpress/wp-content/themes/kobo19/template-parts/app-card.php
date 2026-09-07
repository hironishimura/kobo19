<?php
/**
 * アプリのカード。アプリが2本以上あるときの一覧に使います。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

$kobo19_app_id   = get_the_ID();
$kobo19_status   = kobo19_app_meta( 'status' );
$kobo19_store    = kobo19_app_meta( 'store' );
$kobo19_chapters = kobo19_app_chapters( $kobo19_app_id );
?>
<article class="app-card reveal">
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="app-card__icon"><?php the_post_thumbnail( 'kobo19-icon' ); ?></div>
	<?php endif; ?>

	<div class="app-card__body">
		<h2 class="app-card__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			<?php if ( $kobo19_status ) : ?>
				<span class="badge"><?php echo esc_html( $kobo19_status ); ?></span>
			<?php endif; ?>
		</h2>

		<?php $kobo19_tagline = kobo19_app_meta( 'tagline' ); ?>
		<?php if ( $kobo19_tagline ) : ?>
			<p class="app-card__tagline"><?php echo esc_html( $kobo19_tagline ); ?></p>
		<?php endif; ?>

		<?php $kobo19_lead = kobo19_app_meta( 'lead' ); ?>
		<?php if ( $kobo19_lead ) : ?>
			<p class="app-card__text"><?php echo esc_html( wp_trim_words( $kobo19_lead, 60, '…' ) ); ?></p>
		<?php endif; ?>

		<?php $kobo19_facts = kobo19_app_facts( $kobo19_app_id ); ?>
		<?php if ( $kobo19_facts ) : ?>
			<ul class="app-card__facts">
				<?php foreach ( array_slice( $kobo19_facts, 0, 3 ) as $kobo19_value ) : ?>
					<li><?php echo esc_html( $kobo19_value ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<p class="app-card__actions">
			<a class="btn" href="<?php the_permalink(); ?>"><?php the_title(); ?> を見る</a>

			<?php if ( $kobo19_chapters ) : ?>
				<a class="btn btn--quiet" href="<?php echo esc_url( get_permalink( $kobo19_chapters[0] ) ); ?>">使い方（<?php echo esc_html( (string) count( $kobo19_chapters ) ); ?>章）</a>
			<?php endif; ?>

			<?php if ( $kobo19_store ) : ?>
				<a class="btn btn--quiet" href="<?php echo esc_url( $kobo19_store ); ?>">App Store</a>
			<?php endif; ?>
		</p>
	</div>
</article>
