<?php
/**
 * Address, hours and a click-to-load map.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

$verbena_address = verbena_address_line();
$verbena_phone   = verbena_option( 'phone' );
$verbena_map     = 'https://maps.google.com/maps?q=' . rawurlencode( $verbena_address ) . '&z=15&output=embed';
$verbena_dir     = 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode( $verbena_address );
?>
<section class="py-16 lg:py-24" aria-labelledby="visit-heading">
	<div class="wrap grid gap-10 lg:grid-cols-12 lg:gap-12">
		<div class="lg:col-span-5">
			<p class="eyebrow"><?php esc_html_e( 'Visit us', 'verbena' ); ?></p>
			<h2 id="visit-heading" class="mt-4 text-section text-ink"><?php esc_html_e( 'On Routh Street, in Uptown', 'verbena' ); ?></h2>
			<address class="mt-6 not-italic text-lg text-ink/85">
				<?php echo esc_html( verbena_option( 'address_street' ) ); ?><br>
				<?php echo esc_html( trim( verbena_option( 'address_city' ) . ', ' . verbena_option( 'address_region' ) . ' ' . verbena_option( 'address_postal' ), ', ' ) ); ?>
			</address>
			<div class="mt-6 flex flex-wrap gap-3">
				<a class="btn btn-ghost" href="<?php echo esc_url( $verbena_dir ); ?>" rel="noopener"><?php verbena_the_icon( 'pin', 'size-4' ); ?><?php esc_html_e( 'Get directions', 'verbena' ); ?></a>
				<?php if ( $verbena_phone ) : ?>
					<a class="btn btn-ghost" href="<?php echo esc_url( verbena_tel( $verbena_phone ) ); ?>" data-location="visit"><?php verbena_the_icon( 'phone', 'size-4' ); ?><?php echo esc_html( $verbena_phone ); ?></a>
				<?php endif; ?>
			</div>

			<h3 class="mt-10 font-body text-sm font-semibold uppercase tracking-[0.16em] text-muted"><?php esc_html_e( 'Opening hours', 'verbena' ); ?></h3>
			<dl class="mt-3 divide-y divide-line border-y border-line">
				<?php foreach ( verbena_hours() as $verbena_row ) : ?>
					<div class="flex justify-between gap-4 py-3">
						<dt class="text-ink"><?php echo esc_html( $verbena_row['label'] ); ?></dt>
						<dd class="text-right font-medium text-ink"><?php echo $verbena_row['opens'] ? esc_html( verbena_time_label( $verbena_row['opens'] ) . ' – ' . verbena_time_label( $verbena_row['closes'] ) ) : esc_html__( 'Closed', 'verbena' ); ?></dd>
					</div>
				<?php endforeach; ?>
			</dl>
			<p class="mt-4 text-base text-muted"><?php esc_html_e( 'Validated parking in the garage behind the building, entrance on Cedar Springs Road.', 'verbena' ); ?></p>
		</div>

		<div class="lg:col-span-7">
			<div class="relative h-full min-h-[22rem] overflow-hidden rounded-brand border border-line bg-surface" data-facade data-facade-kind="map">
				<div class="absolute inset-0" data-facade-placeholder>
					<svg class="absolute inset-0 size-full text-line" aria-hidden="true" focusable="false" preserveAspectRatio="xMidYMid slice" viewBox="0 0 600 400" fill="none" stroke="currentColor">
						<path d="M-20 120 L640 40 M-20 260 L640 200 M-20 380 L640 330 M90 -20 L170 420 M260 -20 L330 420 M430 -20 L520 420" stroke-width="14"/>
						<path d="M-20 190 L640 120 M-20 320 L640 265 M180 -20 L250 420 M350 -20 L420 420" stroke-width="5"/>
						<path d="M-20 60 C 150 90, 260 20, 640 110" stroke-width="22" stroke="#d8c8bb"/>
					</svg>
					<div class="absolute left-1/2 top-1/2 flex -translate-x-1/2 -translate-y-full flex-col items-center">
						<span class="rounded-full bg-ink px-4 py-2 text-sm font-semibold text-bg shadow-lg"><?php echo esc_html( 'Verbena' ); ?></span>
						<span class="h-6 w-px bg-ink"></span>
						<span class="size-3 rounded-full bg-accent ring-4 ring-accent/25"></span>
					</div>
					<div class="absolute inset-x-4 bottom-4 flex flex-wrap items-center justify-between gap-3 rounded-brand bg-bg/95 p-4 sm:inset-x-6 sm:bottom-6">
						<p class="text-base text-ink"><?php echo esc_html( $verbena_address ); ?></p>
						<button type="button" class="btn btn-primary !min-h-11 !px-5" data-facade-load data-src="<?php echo esc_url( $verbena_map ); ?>" data-title="<?php esc_attr_e( 'Map showing Verbena Aesthetics', 'verbena' ); ?>"><?php esc_html_e( 'Load map', 'verbena' ); ?></button>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
