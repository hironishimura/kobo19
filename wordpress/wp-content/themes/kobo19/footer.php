<?php
/**
 * フッター。サポートの案内と、サイト情報。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

$kobo19_support = kobo19_support_link();
$kobo19_email   = kobo19_option( 'kobo19_contact_email' );
?>
	</main>

	<?php if ( $kobo19_support || $kobo19_email ) : ?>
	<section class="contact">
		<div class="wrap contact__inner">
			<div>
				<h2 class="contact__title">お困りのときは</h2>
				<p class="contact__text">使い方のご質問、不具合のご報告、ご要望をお待ちしています。お使いの端末と OS のバージョン、再現する式や手順を添えていただけると助かります。</p>
			</div>
			<div>
				<?php if ( $kobo19_support ) : ?>
					<a class="btn" href="<?php echo esc_url( $kobo19_support['url'] ); ?>"><?php echo esc_html( $kobo19_support['label'] ); ?></a>
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
						$kobo19_links = kobo19_primary_links();

						// アプリが1本のときは、フッターに利用規約も足す。
						$kobo19_apps = kobo19_apps();
						if ( 1 === count( $kobo19_apps ) ) {
							$kobo19_policies = kobo19_app_policies( $kobo19_apps[0]->ID );
							if ( isset( $kobo19_policies['terms'] ) ) {
								$kobo19_links[] = array(
									'url'   => get_permalink( $kobo19_policies['terms'] ),
									'label' => '利用規約',
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
				<span>ここに置いているアプリは、利用者の情報を収集しません</span>
			</div>

		</div>
	</footer>

</div><!-- /.site -->

<?php wp_footer(); ?>
</body>
</html>
