<?php
/**
 * 資料の一覧。アプリごとにまとめて出します。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

get_header();

$kobo19_apps = kobo19_apps();
?>

<section class="page-head">
	<div class="wrap">
		<p class="eyebrow">資料</p>
		<h1 class="page-head__title">説明書とサポート</h1>
		<p class="page-head__lead">アプリごとに、使い方の説明書と、サポート・プライバシー・利用規約をまとめています。</p>
	</div>
</section>

<section class="section" style="padding-top:0;">
	<div class="wrap">
		<?php if ( $kobo19_apps ) : ?>
			<?php foreach ( $kobo19_apps as $kobo19_app ) : ?>
				<?php
				$kobo19_chapters = kobo19_app_chapters( $kobo19_app->ID );
				$kobo19_policies = kobo19_app_policies( $kobo19_app->ID );

				if ( ! $kobo19_chapters && ! $kobo19_policies ) {
					continue;
				}
				?>
				<div class="doc-group">
					<h2 class="doc-group__title">
						<a href="<?php echo esc_url( get_permalink( $kobo19_app ) ); ?>"><?php echo esc_html( get_the_title( $kobo19_app ) ); ?></a>
					</h2>

					<?php if ( $kobo19_chapters ) : ?>
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
					<?php endif; ?>

					<?php if ( $kobo19_policies ) : ?>
						<ul class="policy-list">
							<?php foreach ( $kobo19_policies as $kobo19_doc ) : ?>
								<li>
									<a href="<?php echo esc_url( get_permalink( $kobo19_doc ) ); ?>">
										<span class="policy-list__title"><?php echo esc_html( get_the_title( $kobo19_doc ) ); ?></span>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		<?php else : ?>
			<div class="notice">
				<p class="notice__code">まだありません</p>
				<p>管理画面の「資料」から追加すると、アプリごとにここへ並びます。</p>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
