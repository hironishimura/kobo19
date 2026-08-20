<?php
/**
 * フッター。サポートの案内と、サイト情報。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

$kobo19_support = kobo19_page_link( 'support' );
$kobo19_email   = kobo19_option( 'kobo19_contact_email' );
$kobo19_manual  = get_post_type_archive_link( 'manual' );
?>
	</main>

	<?php if ( $kobo19_support || $kobo19_email ) : ?>
	<section class="contact">
		<div class="wrap contact__inner">
			<div>
				<h2 class="contact__title">お困りのときは</h2>
				<p class="contact__text">使い方のご質問、不具合のご報告、ご要望をお待ちしています。お使いの端末と OS のバージョン、再現する式を添えていただけると助かります。</p>
			</div>
			<div>
				<?php if ( $kobo19_support ) : ?>
					<a class="btn" href="<?php echo esc_url( $kobo19_support['url'] ); ?>">サポートを見る</a>
				<?php elseif ( $kobo19_email ) : ?>
					<a class="btn" href="mailto:<?php echo esc_attr( $kobo19_email ); ?>">メールを送る</a>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<footer class="site-footer">
		<div class="wrap">

			<div class="site-footer__inner">
				<div>
					<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
						<span class="brand__mark">19</span>
						<span class="brand__name"><?php bloginfo( 'name' ); ?></span>
					</a>
					<?php if ( $kobo19_email ) : ?>
						<p class="site-footer__mail">
							<a href="mailto:<?php echo esc_attr( $kobo19_email ); ?>"><?php echo esc_html( $kobo19_email ); ?></a>
						</p>
					<?php endif; ?>
				</div>

				<nav aria-label="フッターメニュー">
					<?php
					if ( has_nav_menu( 'footer' ) ) {
						wp_nav_menu(
							array(
								'theme_location' => 'footer',
								'container'      => false,
								'depth'          => 1,
								'fallback_cb'    => false,
							)
						);
					} else {
						$kobo19_links = array();

						if ( $kobo19_manual ) {
							$kobo19_links[] = array(
								'url'   => $kobo19_manual,
								'label' => '使い方',
							);
						}

						foreach ( array( 'support', 'privacy', 'terms' ) as $kobo19_slug ) {
							$kobo19_page = kobo19_page_link( $kobo19_slug );
							if ( $kobo19_page ) {
								$kobo19_links[] = array(
									'url'   => $kobo19_page['url'],
									'label' => $kobo19_page['title'],
								);
							}
						}

						echo '<ul>';
						foreach ( $kobo19_links as $kobo19_link ) {
							printf(
								'<li><a href="%s">%s</a></li>',
								esc_url( $kobo19_link['url'] ),
								esc_html( $kobo19_link['label'] )
							);
						}
						echo '</ul>';
					}
					?>
				</nav>

				<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
					<div><?php dynamic_sidebar( 'footer-1' ); ?></div>
				<?php endif; ?>
			</div>

			<div class="colophon">
				<span>&copy; <?php echo esc_html( kobo19_option( 'kobo19_established', '2026' ) ); ?>–<?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></span>
				<span>このアプリは利用者の情報を収集しません</span>
			</div>

		</div>
	</footer>

</div><!-- /.site -->

<?php wp_footer(); ?>
</body>
</html>
