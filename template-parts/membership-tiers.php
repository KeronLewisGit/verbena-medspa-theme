<?php
/**
 * Membership tier cards, read from the Memberships page fields.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

$verbena_page_id = $args['page_id'] ?? verbena_page_by_template( 'templates/template-memberships.php' );
$verbena_tiers   = $verbena_page_id ? verbena_field( 'membership_tiers', $verbena_page_id ) : array();
$verbena_heading = $args['heading_tag'] ?? 'h3';
if ( ! is_array( $verbena_tiers ) || ! $verbena_tiers ) {
	return;
}
?>
<ul class="grid gap-6 lg:grid-cols-3">
	<?php foreach ( $verbena_tiers as $verbena_tier ) : ?>
		<?php
		$verbena_featured = ! empty( $verbena_tier['featured'] );
		$verbena_perks    = array_filter( array_map( 'trim', explode( "\n", (string) $verbena_tier['perks'] ) ) );
		?>
		<li class="<?php echo esc_attr( $verbena_featured ? 'bg-ink text-bg' : 'bg-paper text-ink border border-line' ); ?> relative flex flex-col rounded-brand p-7 lg:p-8">
			<?php if ( $verbena_featured ) : ?>
				<p class="absolute right-6 top-6 rounded-full bg-blush px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em] text-ink"><?php esc_html_e( 'Most chosen', 'verbena' ); ?></p>
			<?php endif; ?>
			<<?php echo tag_escape( $verbena_heading ); ?> class="text-[1.75rem] leading-tight"><?php echo esc_html( $verbena_tier['name'] ); ?></<?php echo tag_escape( $verbena_heading ); ?>>
			<p class="<?php echo esc_attr( $verbena_featured ? 'text-mauve' : 'text-ink/70' ); ?> mt-1 text-base"><?php echo esc_html( $verbena_tier['summary'] ); ?></p>
			<p class="mt-6 flex items-baseline gap-1">
				<span class="font-display text-5xl"><?php echo esc_html( verbena_price( $verbena_tier['price'] ) ); ?></span>
				<span class="<?php echo esc_attr( $verbena_featured ? 'text-mauve' : 'text-muted' ); ?>"><?php esc_html_e( '/ month', 'verbena' ); ?></span>
			</p>
			<ul class="mt-6 grid gap-3 border-t pt-6 text-base <?php echo esc_attr( $verbena_featured ? 'border-bg/20' : 'border-line' ); ?>">
				<?php foreach ( $verbena_perks as $verbena_perk ) : ?>
					<li class="flex gap-3"><?php verbena_the_icon( 'check', 'mt-1 size-4 shrink-0 ' . ( $verbena_featured ? 'text-blush' : 'text-accent' ) ); ?><span><?php echo esc_html( $verbena_perk ); ?></span></li>
				<?php endforeach; ?>
			</ul>
			<div class="mt-auto pt-8">
				<?php
				echo wp_kses(
					verbena_book_button(
						/* translators: %s: tier name. */
						sprintf( __( 'Join %s', 'verbena' ), $verbena_tier['name'] ),
						'Membership: ' . $verbena_tier['name'],
						'',
						'membership_tier',
						$verbena_featured ? 'light' : 'ghost'
					),
					verbena_button_kses()
				);
				?>
			</div>
		</li>
	<?php endforeach; ?>
</ul>
