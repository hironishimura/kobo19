<?php
/**
 * トップの線画。制作物 D-03「Yチェア」の正面図を、図面のように引きます。
 *
 * 読み込みのときに線が引かれていきますが、動きを減らす設定の環境では
 * 最初から引かれた状態で表示されます（main.css の指定）。
 *
 * @package kobo19
 */

defined( 'ABSPATH' ) || exit;
?>
<figure class="hero__figure">
	<svg class="drawing" viewBox="40 50 360 430" role="img"
		aria-label="Yチェアの正面図。座面の高さ430ミリの寸法線が添えられています。">

		<!-- 中心線 -->
		<path class="axis fade" d="M200 56 L200 470" />

		<!-- 脚 -->
		<g>
			<path class="line draw" style="--len:420" d="M98 250 L90 438" />
			<path class="line draw" style="--len:420" d="M302 250 L310 438" />
			<path class="line--thin line draw" style="--len:420" d="M108 432 L116 246" />
			<path class="line--thin line draw" style="--len:420" d="M292 432 L284 246" />
		</g>

		<!-- 座面 -->
		<g>
			<path class="line draw" style="--len:230;--delay:120ms" d="M88 248 L312 248" />
			<path class="line draw" style="--len:270;--delay:120ms" d="M88 248 L88 266 L312 266 L312 248" />
			<path class="line--thin line draw" style="--len:200;--delay:120ms" d="M104 238 L296 238" />
			<path class="line--thin line draw" style="--len:24;--delay:120ms" d="M88 248 L104 238" />
			<path class="line--thin line draw" style="--len:24;--delay:120ms" d="M312 248 L296 238" />
		</g>

		<!-- ペーパーコードの編み目 -->
		<g class="fade" style="--delay:1150ms">
			<path class="line--hair line" d="M116 248 L116 266" />
			<path class="line--hair line" d="M140 248 L140 266" />
			<path class="line--hair line" d="M164 248 L164 266" />
			<path class="line--hair line" d="M188 248 L188 266" />
			<path class="line--hair line" d="M212 248 L212 266" />
			<path class="line--hair line" d="M236 248 L236 266" />
			<path class="line--hair line" d="M260 248 L260 266" />
			<path class="line--hair line" d="M284 248 L284 266" />
		</g>

		<!-- 背柱と笠木 -->
		<g>
			<path class="line draw" style="--len:140;--delay:280ms" d="M116 246 L126 112" />
			<path class="line draw" style="--len:140;--delay:280ms" d="M284 246 L274 112" />
			<path class="line--bold line draw" style="--len:220;--delay:520ms"
				d="M104 124 C128 74 272 74 296 124" />
		</g>

		<!-- Y字の背板 -->
		<g>
			<path class="line draw" style="--len:190;--delay:760ms"
				d="M194 240 L194 168 C194 132 168 112 154 100" />
			<path class="line draw" style="--len:190;--delay:760ms"
				d="M206 240 L206 168 C206 132 232 112 246 100" />
		</g>

		<!-- 貫 -->
		<path class="line--thin line draw" style="--len:210;--delay:1000ms" d="M95 370 L305 370" />

		<!-- 座面高さの寸法 -->
		<g class="fade" style="--delay:1300ms">
			<path class="dim" d="M318 248 L360 248" />
			<path class="dim" d="M318 438 L360 438" />
			<path class="dim" d="M352 248 L352 438" />
			<path class="dim" d="M349 255 L352 246 L355 255" />
			<path class="dim" d="M349 431 L352 440 L355 431" />
			<text class="dim-text" x="358" y="347">430</text>
		</g>

		<!-- 図面の符号 -->
		<text class="dim-text fade" style="--delay:1450ms" x="46" y="466">SCALE 1:8</text>
	</svg>

	<figcaption><?php echo esc_html( kobo19_option( 'kobo19_hero_note', 'D-03 Yチェア ／ Rhinoceros・Blender' ) ); ?></figcaption>
</figure>
