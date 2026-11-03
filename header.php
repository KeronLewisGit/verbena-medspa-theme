<?php
/**
 * Site header.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

$verbena_phone = verbena_option( 'phone' );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#f7f2ea">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'verbena' ); ?></a>

<header class="sticky top-0 z-40 border-b border-line/80 bg-bg/95 backdrop-blur supports-[backdrop-filter]:bg-bg/85" data-site-header>
	<div class="wrap flex h-[4.5rem] items-center justify-between gap-6 lg:h-20">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="group flex min-h-11 flex-col justify-center leading-none" rel="home">
			<span class="font-display text-[1.75rem] tracking-tight text-ink lg:text-[2rem]"><?php echo esc_html( 'Verbena' ); ?></span>
			<span class="mt-1 text-[0.6875rem] font-semibold uppercase tracking-[0.32em] text-muted"><?php esc_html_e( 'Aesthetics · Dallas', 'verbena' ); ?></span>
			<span class="sr-only"><?php esc_html_e( 'Verbena Aesthetics home', 'verbena' ); ?></span>
		</a>

		<nav class="hidden lg:block" aria-label="<?php esc_attr_e( 'Primary', 'verbena' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'flex items-center gap-7',
					'fallback_cb'    => false,
					'depth'          => 1,
				)
			);
			?>
		</nav>

		<div class="flex items-center gap-2 sm:gap-3">
			<?php if ( $verbena_phone ) : ?>
				<a class="hidden min-h-11 items-center gap-2 px-2 text-[0.98rem] font-medium text-ink/85 hover:text-ink xl:inline-flex" href="<?php echo esc_url( verbena_tel( $verbena_phone ) ); ?>" data-location="header">
					<?php verbena_the_icon( 'phone', 'size-4' ); ?>
					<?php echo esc_html( $verbena_phone ); ?>
				</a>
			<?php endif; ?>
			<a class="btn btn-primary hidden !min-h-11 !px-5 sm:inline-flex" href="<?php echo esc_url( verbena_book_url() ); ?>" data-book data-treatment="" data-location="header"><?php esc_html_e( 'Book a consult', 'verbena' ); ?></a>
			<button type="button" class="inline-flex size-12 items-center justify-center rounded-full border border-ink/20 text-ink lg:hidden" aria-expanded="false" aria-controls="mobile-nav" data-nav-toggle>
				<span class="sr-only" data-nav-label><?php esc_html_e( 'Open menu', 'verbena' ); ?></span>
				<?php verbena_the_icon( 'menu', 'size-6' ); ?>
			</button>
		</div>
	</div>

	<div id="mobile-nav" class="border-t border-line bg-bg lg:hidden" hidden data-nav-panel>
		<nav class="wrap py-6" aria-label="<?php esc_attr_e( 'Mobile', 'verbena' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'grid gap-1 font-display text-[1.75rem] [&_a]:flex [&_a]:min-h-12 [&_a]:items-center [&_a]:border-b [&_a]:border-line/70 [&_a]:py-2',
					'fallback_cb'    => false,
					'depth'          => 1,
					'link_before'    => '',
				)
			);
			?>
			<div class="mt-6 grid gap-3">
				<a class="btn btn-primary w-full" href="<?php echo esc_url( verbena_book_url() ); ?>" data-book data-treatment="" data-location="mobile_menu"><?php esc_html_e( 'Book a consult', 'verbena' ); ?></a>
				<?php if ( $verbena_phone ) : ?>
					<a class="btn btn-ghost w-full" href="<?php echo esc_url( verbena_tel( $verbena_phone ) ); ?>" data-location="mobile_menu"><?php verbena_the_icon( 'phone', 'size-4' ); ?><?php echo esc_html( $verbena_phone ); ?></a>
				<?php endif; ?>
			</div>
		</nav>
	</div>
</header>

<main id="main" tabindex="-1" class="focus:outline-none">
