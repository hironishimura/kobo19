<?php
/**
 * アプリの見出し。トップ（アプリが1本のとき）と製品ページで共通に使います。
 *
 * @param int $args['app_id'] アプリのID。
 * @param bool $args['is_home'] トップに出しているか（見出しレベルの判断に使う）。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

$kobo19_app_id  = isset( $args['app_id'] ) ? (int) $args['app_id'] : get_the_ID();
$kobo19_heading = ( isset( $args['heading'] ) ) ? $args['heading'] : 'h1';

$kobo19_store    = kobo19_app_meta( 'store', $kobo19_app_id );
$kobo19_demo     = kobo19_app_meta( 'demo', $kobo19_app_id );
$kobo19_facts    = kobo19_app_facts( $kobo19_app_id );
$kobo19_chapters = kobo19_app_chapters( $kobo19_app_id );
$kobo19_status   = kobo19_app_meta( 'status', $kobo19_app_id );
?>
<section class="hero">
	<div class="wrap">
		<div class="hero__grid<?php echo $kobo19_demo ? '' : ' hero__grid--single'; ?>">
			<div class="hero__text">
				<p class="hero__eyebrow">
					<?php echo esc_html( kobo19_app_meta( 'requires', $kobo19_app_id ) ? kobo19_app_meta( 'category', $kobo19_app_id ) : '' ); ?>
					<?php if ( $kobo19_status ) : ?>
						<span class="badge"><?php echo esc_html( $kobo19_status ); ?></span>
					<?php endif; ?>
				</p>

				<<?php echo esc_html( $kobo19_heading ); ?> class="hero__title"><?php echo esc_html( get_the_title( $kobo19_app_id ) ); ?></<?php echo esc_html( $kobo19_heading ); ?>>

				<?php $kobo19_tagline = kobo19_app_meta( 'tagline', $kobo19_app_id ); ?>
				<?php if ( $kobo19_tagline ) : ?>
					<p class="hero__tagline"><?php echo esc_html( $kobo19_tagline ); ?></p>
				<?php endif; ?>

				<?php $kobo19_lead = kobo19_app_meta( 'lead', $kobo19_app_id ); ?>
				<?php if ( $kobo19_lead ) : ?>
					<p class="hero__lead"><?php echo nl2br( esc_html( $kobo19_lead ) ); ?></p>
				<?php endif; ?>

				<div class="hero__actions">
					<?php if ( $kobo19_store ) : ?>
						<a class="btn" href="<?php echo esc_url( $kobo19_store ); ?>">App Store で見る</a>
					<?php endif; ?>

					<?php if ( $kobo19_chapters ) : ?>
						<a class="btn<?php echo $kobo19_store ? ' btn--quiet' : ''; ?>" href="<?php echo esc_url( get_permalink( $kobo19_chapters[0] ) ); ?>">使い方を読む</a>
					<?php endif; ?>
				</div>

				<?php if ( $kobo19_facts ) : ?>
					<dl class="facts">
						<?php foreach ( $kobo19_facts as $kobo19_key => $kobo19_value ) : ?>
							<div class="facts__row">
								<dt><?php echo esc_html( $kobo19_key ); ?></dt>
								<dd><?php echo esc_html( $kobo19_value ); ?></dd>
							</div>
						<?php endforeach; ?>
					</dl>
				<?php endif; ?>
			</div>

			<?php if ( $kobo19_demo ) : ?>
				<div class="hero__demo"><?php echo do_shortcode( $kobo19_demo ); ?></div>
			<?php endif; ?>
		</div>
	</div>
</section>
