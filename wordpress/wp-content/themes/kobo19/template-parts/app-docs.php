<?php
/**
 * アプリに紐づく資料（説明書の目次と、App Store 用のページ）。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

$kobo19_app_id   = isset( $args['app_id'] ) ? (int) $args['app_id'] : get_the_ID();
$kobo19_chapters = kobo19_app_chapters( $kobo19_app_id );
$kobo19_policies = kobo19_app_policies( $kobo19_app_id );

if ( ! $kobo19_chapters && ! $kobo19_policies ) {
	return;
}
?>

<?php if ( $kobo19_chapters ) : ?>
<section class="section">
	<div class="wrap">
		<p class="eyebrow">説明書</p>
		<h2 class="section-title">使い方</h2>
		<p class="section-lead">上から順に読めば一通り分かるように並べています。</p>

		<ol class="chapter-list">
			<?php foreach ( $kobo19_chapters as $kobo19_index => $kobo19_chapter ) : ?>
				<li class="chapter-list__item">
					<a href="<?php echo esc_url( get_permalink( $kobo19_chapter ) ); ?>">
						<span class="chapter-list__no"><?php echo esc_html( sprintf( '%02d', $kobo19_index + 1 ) ); ?></span>
						<span class="chapter-list__body">
							<span class="chapter-list__title"><?php echo esc_html( get_the_title( $kobo19_chapter ) ); ?></span>
							<?php if ( $kobo19_chapter->post_excerpt ) : ?>
								<span class="chapter-list__text"><?php echo esc_html( $kobo19_chapter->post_excerpt ); ?></span>
							<?php endif; ?>
						</span>
					</a>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
<?php endif; ?>

<?php if ( $kobo19_policies ) : ?>
<section class="section">
	<div class="wrap">
		<p class="eyebrow">このアプリについて</p>

		<ul class="policy-list">
			<?php foreach ( $kobo19_policies as $kobo19_kind => $kobo19_doc ) : ?>
				<li>
					<a href="<?php echo esc_url( get_permalink( $kobo19_doc ) ); ?>">
						<span class="policy-list__title"><?php echo esc_html( get_the_title( $kobo19_doc ) ); ?></span>
						<?php if ( $kobo19_doc->post_excerpt ) : ?>
							<span class="policy-list__text"><?php echo esc_html( $kobo19_doc->post_excerpt ); ?></span>
						<?php endif; ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
<?php endif; ?>
