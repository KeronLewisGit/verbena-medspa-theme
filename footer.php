<?php
/**
 * Site footer.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

$verbena_phone = verbena_option( 'phone' );
$verbena_email = verbena_option( 'email' );
?>
</main>

<footer class="bg-ink pb-24 text-bg lg:pb-0">
	<div class="wrap grid gap-12 py-16 md:grid-cols-2 lg:grid-cols-12 lg:py-20">
		<div class="lg:col-span-4">
			<p class="font-display text-4xl leading-none"><?php echo esc_html( 'Verbena' ); ?></p>
			<p class="mt-2 text-[0.6875rem] font-semibold uppercase tracking-[0.32em] text-mauve"><?php esc_html_e( 'Aesthetics · Dallas', 'verbena' ); ?></p>
			<p class="mt-6 max-w-sm text-mauve"><?php echo esc_html( get_bloginfo( 'description' ) ); ?></p>
			<a class="btn btn-light mt-8" href="<?php echo esc_url( verbena_book_url() ); ?>" data-book data-treatment="" data-location="footer"><?php esc_html_e( 'Book a consult', 'verbena' ); ?><?php verbena_the_icon( 'arrow', 'size-4' ); ?></a>
		</div>

		<div class="lg:col-span-3">
			<h2 class="font-body text-sm font-semibold uppercase tracking-[0.16em] text-blush"><?php esc_html_e( 'Visit', 'verbena' ); ?></h2>
			<address class="mt-4 not-italic text-bg/90">
				<?php echo esc_html( verbena_option( 'address_street' ) ); ?><br>
				<?php echo esc_html( trim( verbena_option( 'address_city' ) . ', ' . verbena_option( 'address_region' ) . ' ' . verbena_option( 'address_postal' ), ', ' ) ); ?>
			</address>
			<ul class="mt-4 space-y-1">
				<?php if ( $verbena_phone ) : ?>
					<li><a class="inline-flex min-h-11 items-center gap-2 hover:text-blush" href="<?php echo esc_url( verbena_tel( $verbena_phone ) ); ?>" data-location="footer"><?php verbena_the_icon( 'phone', 'size-4' ); ?><?php echo esc_html( $verbena_phone ); ?></a></li>
				<?php endif; ?>
				<?php if ( $verbena_email ) : ?>
					<li><a class="inline-flex min-h-11 items-center gap-2 hover:text-blush" href="<?php echo esc_url( 'mailto:' . $verbena_email ); ?>"><?php verbena_the_icon( 'mail', 'size-4 shrink-0' ); ?><span><?php echo esc_html( strtok( $verbena_email, '@' ) ); ?><wbr>@<?php echo esc_html( substr( strrchr( $verbena_email, '@' ), 1 ) ); ?></span></a></li>
				<?php endif; ?>
			</ul>
		</div>

		<div class="lg:col-span-3">
			<h2 class="font-body text-sm font-semibold uppercase tracking-[0.16em] text-blush"><?php esc_html_e( 'Hours', 'verbena' ); ?></h2>
			<dl class="mt-4 space-y-2 text-bg/90">
				<?php foreach ( verbena_hours() as $verbena_row ) : ?>
					<div>
						<dt class="text-mauve"><?php echo esc_html( $verbena_row['label'] ); ?></dt>
						<dd><?php echo $verbena_row['opens'] ? esc_html( verbena_time_label( $verbena_row['opens'] ) . ' – ' . verbena_time_label( $verbena_row['closes'] ) ) : esc_html__( 'Closed', 'verbena' ); ?></dd>
					</div>
				<?php endforeach; ?>
			</dl>
		</div>

		<div class="lg:col-span-2">
			<h2 class="font-body text-sm font-semibold uppercase tracking-[0.16em] text-blush"><?php esc_html_e( 'Explore', 'verbena' ); ?></h2>
			<nav class="mt-3" aria-label="<?php esc_attr_e( 'Footer', 'verbena' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'grid text-bg/90',
						'fallback_cb'    => false,
						'depth'          => 1,
					)
				);
				?>
			</nav>
		</div>
	</div>

	<div class="border-t border-bg/15">
		<div class="wrap flex flex-col gap-4 py-6 text-sm text-mauve md:flex-row md:items-center md:justify-between">
			<p>
				<span class="mr-2 inline-block rounded-full border border-blush/50 px-2.5 py-0.5 text-xs font-semibold uppercase tracking-wider text-blush"><?php esc_html_e( 'Demo concept', 'verbena' ); ?></span>
				<?php esc_html_e( 'Fictional business. Names, prices, reviews and contact details are invented.', 'verbena' ); ?>
			</p>
			<div class="flex flex-wrap items-center gap-x-5 gap-y-1">
				<?php foreach ( verbena_social_links() as $verbena_label => $verbena_url ) : ?>
					<a class="inline-flex min-h-11 items-center hover:text-bg" href="<?php echo esc_url( $verbena_url ); ?>" rel="noopener"><?php echo esc_html( $verbena_label ); ?></a>
				<?php endforeach; ?>
				<span>
					<?php
					printf(
						/* translators: %s: author link. */
						esc_html__( 'Theme by %s', 'verbena' ),
						'<a class="underline underline-offset-4 hover:text-bg" href="' . esc_url( 'https://keronlewis.dev' ) . '">Keron Lewis</a>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inline.
					);
					?>
				</span>
			</div>
		</div>
	</div>
</footer>

<div class="fixed inset-x-0 bottom-0 z-40 border-t border-line bg-bg/95 p-3 backdrop-blur lg:hidden" data-mobile-bar>
	<div class="grid grid-cols-[auto_1fr] gap-3">
		<?php if ( $verbena_phone ) : ?>
			<a class="btn btn-ghost !px-5" href="<?php echo esc_url( verbena_tel( $verbena_phone ) ); ?>" data-location="mobile_bar" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: phone. */ __( 'Call %s', 'verbena' ), $verbena_phone ) ); ?>"><?php verbena_the_icon( 'phone', 'size-5' ); ?><span><?php esc_html_e( 'Call', 'verbena' ); ?></span></a>
		<?php endif; ?>
		<a class="btn btn-primary" href="<?php echo esc_url( verbena_book_url( is_singular( 'treatment' ) ? get_post_field( 'post_name' ) : '' ) ); ?>" data-book data-treatment="<?php echo esc_attr( is_singular( 'treatment' ) ? get_the_title() : '' ); ?>" data-location="mobile_bar"><?php verbena_the_icon( 'calendar', 'size-5' ); ?><?php esc_html_e( 'Book a consult', 'verbena' ); ?></a>
	</div>
</div>

<?php wp_footer(); ?>
</body>
</html>
