<?php
/**
 * Mac 版のダウンロード（製品ページ）。
 *
 * アプリの情報に「Mac 版のダウンロード URL」が入っているときだけ出ます。
 * ボタンのほかに、ファイルの大きさ・対応 OS と、入れかたの手順を並べます。
 *
 * @param int $args['app_id'] アプリのID。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;

$kobo19_app_id   = isset( $args['app_id'] ) ? (int) $args['app_id'] : get_the_ID();
$kobo19_download = kobo19_app_download( $kobo19_app_id );

if ( ! $kobo19_download ) {
	return;
}

$kobo19_name    = get_the_title( $kobo19_app_id );
$kobo19_version = kobo19_app_meta( 'version', $kobo19_app_id );
$kobo19_note    = kobo19_app_meta( 'download_note', $kobo19_app_id );
$kobo19_file    = $kobo19_download['file'] ? $kobo19_download['file'] : $kobo19_name;

// 手順はファイルの形で少し変わる。ZIP は Safari が勝手に開く、DMG はドラッグして取り出す
if ( 'dmg' === $kobo19_download['ext'] ) {
	$kobo19_steps = array(
		array( 'DMG を開く', sprintf( 'ダウンロードした %s をダブルクリックすると、%s.app の入ったウインドウが開きます。', $kobo19_file, $kobo19_name ) ),
		array( '「アプリケーション」へ移す', sprintf( '%s.app を、同じウインドウの「アプリケーション」フォルダへドラッグします。終わったら DMG は取り出して構いません。', $kobo19_name ) ),
		array( 'ダブルクリックで起動', '初めて開くときは「インターネットからダウンロードされたアプリケーションです」と確認が出るので、「開く」を押します。' ),
	);
} else {
	$kobo19_steps = array(
		array( 'ZIP を開く', sprintf( 'ダウンロードした %s をダブルクリックすると、%s.app が出てきます。Safari なら自動で開かれています。', $kobo19_file, $kobo19_name ) ),
		array( '「アプリケーション」へ移す', sprintf( '%s.app を Finder の「アプリケーション」フォルダへドラッグします。', $kobo19_name ) ),
		array( 'ダブルクリックで起動', '初めて開くときは「インターネットからダウンロードされたアプリケーションです」と確認が出るので、「開く」を押します。' ),
	);
}
?>
<section class="section" id="download">
	<div class="wrap">
		<p class="eyebrow">ダウンロード</p>

		<div class="download">
			<div class="download__text">
				<h2 class="section-title">Mac 版</h2>
				<p class="section-lead">App Store を通さずに、ここから直接入れられます。</p>

				<p class="download__actions">
					<a class="btn" href="<?php echo esc_url( $kobo19_download['url'] ); ?>" download>Mac 版をダウンロード</a>
				</p>

				<dl class="facts">
					<div class="facts__row">
						<dt>ファイル</dt>
						<dd><?php echo esc_html( $kobo19_file ); ?><?php echo $kobo19_download['size'] ? esc_html( '（' . $kobo19_download['size'] . '）' ) : ''; ?></dd>
					</div>
					<?php if ( $kobo19_version ) : ?>
						<div class="facts__row">
							<dt>バージョン</dt>
							<dd><?php echo esc_html( $kobo19_version ); ?></dd>
						</div>
					<?php endif; ?>
					<?php if ( $kobo19_download['requires'] ) : ?>
						<div class="facts__row">
							<dt>対応</dt>
							<dd><?php echo esc_html( $kobo19_download['requires'] ); ?></dd>
						</div>
					<?php endif; ?>
					<?php if ( $kobo19_download['date'] ) : ?>
						<div class="facts__row">
							<dt>更新</dt>
							<dd><?php echo esc_html( $kobo19_download['date'] ); ?></dd>
						</div>
					<?php endif; ?>
				</dl>
			</div>

			<div class="download__steps">
				<p class="download__heading">入れかた</p>

				<ol class="steps">
					<?php foreach ( $kobo19_steps as $kobo19_step ) : ?>
						<li>
							<strong><?php echo esc_html( $kobo19_step[0] ); ?></strong>
							<span><?php echo esc_html( $kobo19_step[1] ); ?></span>
						</li>
					<?php endforeach; ?>
				</ol>

				<aside class="callout">
					<p class="callout__title">「開発元を確認できません」と出たとき</p>
					システム設定 → プライバシーとセキュリティ を開き、下のほうにある「“<?php echo esc_html( $kobo19_name ); ?>” は開発元を確認できないため…」の行で「このまま開く」を押してください。次からはそのまま開けます。
				</aside>

				<?php if ( $kobo19_note ) : ?>
					<div class="download__note"><?php echo wp_kses_post( wpautop( $kobo19_note ) ); ?></div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
