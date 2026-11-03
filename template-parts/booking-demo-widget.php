<?php
/**
 * Stand-in for a third-party booking iframe (served at ?verbena_booking_demo=1).
 *
 * Deliberately self-contained and styled like a generic provider widget, not
 * like the theme: in production this frame is Zenoti, Boulevard, Vagaro etc.
 * No appointment is created.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

$verbena_selected = isset( $_GET['service'] ) ? sanitize_title( wp_unslash( $_GET['service'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only.
$verbena_services = verbena_get_treatments();
$verbena_tz       = wp_timezone();
$verbena_days     = array();
$verbena_cursor   = new DateTimeImmutable( 'tomorrow', $verbena_tz );
for ( $verbena_n = 0; $verbena_n < 6; ) {
	$verbena_dow = (int) $verbena_cursor->format( 'N' );
	if ( $verbena_dow >= 2 && $verbena_dow <= 6 ) {
		$verbena_days[] = $verbena_cursor;
		++$verbena_n;
	}
	$verbena_cursor = $verbena_cursor->modify( '+1 day' );
}
$verbena_slots = array( '10:00 am', '10:45 am', '11:30 am', '1:15 pm', '2:00 pm', '3:30 pm', '4:15 pm', '5:45 pm' );
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?php esc_html_e( 'Online booking (demo)', 'verbena' ); ?></title>
<style>
*{box-sizing:border-box}body{margin:0;font:16px/1.5 -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;color:#1f2430;background:#fff}
.bar{display:flex;justify-content:space-between;align-items:center;padding:14px 20px;border-bottom:1px solid #e6e8ee;font-size:14px;color:#5b6170}
.bar b{color:#1f2430}.wrap{padding:20px;max-width:760px;margin:0 auto}
h1{font-size:20px;margin:0 0 4px}p{margin:0 0 16px;color:#5b6170}
label{display:block;font-weight:600;font-size:14px;margin:18px 0 8px}
select{width:100%;min-height:44px;border:1px solid #c9ced8;border-radius:8px;padding:8px 12px;font:inherit;background:#fff}
.grid{display:grid;gap:8px;grid-template-columns:repeat(auto-fill,minmax(96px,1fr))}
.chip{min-height:44px;border:1px solid #c9ced8;border-radius:8px;background:#fff;font:inherit;cursor:pointer;padding:6px 8px;color:#1f2430}
.chip small{display:block;color:#5b6170;font-size:12px}
.chip[aria-pressed=true]{background:#2f4bd8;border-color:#2f4bd8;color:#fff}.chip[aria-pressed=true] small{color:#dfe4ff}
.chip:focus-visible,select:focus-visible,.go:focus-visible{outline:3px solid #2f4bd8;outline-offset:2px}
.go{margin-top:22px;width:100%;min-height:48px;border:0;border-radius:8px;background:#2f4bd8;color:#fff;font:600 16px/1 inherit;cursor:pointer}
.go[disabled]{background:#9aa6e6;cursor:not-allowed}
.note{margin-top:18px;padding:12px 14px;border-radius:8px;background:#f3f5fa;font-size:14px;color:#3b4150}
.done{display:none;padding:18px;border-radius:8px;background:#eaf7ef;color:#174d2c;margin-top:18px}
</style>
</head>
<body>
<div class="bar"><span><b><?php echo esc_html( get_bloginfo( 'name' ) ); ?></b> · <?php esc_html_e( 'Online booking', 'verbena' ); ?></span><span><?php esc_html_e( 'Demo provider widget', 'verbena' ); ?></span></div>
<div class="wrap">
	<h1><?php esc_html_e( 'Book an appointment', 'verbena' ); ?></h1>
	<p><?php esc_html_e( 'Choose a service, day and time.', 'verbena' ); ?></p>

	<label for="svc"><?php esc_html_e( 'Service', 'verbena' ); ?></label>
	<select id="svc">
		<option value="consult"><?php esc_html_e( 'Complimentary consultation (30 min)', 'verbena' ); ?></option>
		<?php foreach ( $verbena_services as $verbena_service ) : ?>
			<option value="<?php echo esc_attr( $verbena_service->post_name ); ?>" <?php selected( $verbena_selected, $verbena_service->post_name ); ?>><?php echo esc_html( $verbena_service->post_title . ' (' . verbena_field( 'duration', $verbena_service->ID ) . ')' ); ?></option>
		<?php endforeach; ?>
	</select>

	<label id="day-label"><?php esc_html_e( 'Day', 'verbena' ); ?></label>
	<div class="grid" role="group" aria-labelledby="day-label" data-group="day">
		<?php foreach ( $verbena_days as $verbena_i => $verbena_day ) : ?>
			<button type="button" class="chip" aria-pressed="<?php echo 0 === $verbena_i ? 'true' : 'false'; ?>"><?php echo esc_html( wp_date( 'D', $verbena_day->getTimestamp() ) ); ?><small><?php echo esc_html( wp_date( 'M j', $verbena_day->getTimestamp() ) ); ?></small></button>
		<?php endforeach; ?>
	</div>

	<label id="time-label"><?php esc_html_e( 'Time', 'verbena' ); ?></label>
	<div class="grid" role="group" aria-labelledby="time-label" data-group="time">
		<?php foreach ( $verbena_slots as $verbena_slot ) : ?>
			<button type="button" class="chip" aria-pressed="false"><?php echo esc_html( $verbena_slot ); ?></button>
		<?php endforeach; ?>
	</div>

	<button type="button" class="go" id="go" disabled><?php esc_html_e( 'Continue', 'verbena' ); ?></button>
	<div class="done" id="done" role="status"><?php esc_html_e( 'This is a demo widget, so no appointment was created. On a live site this step continues in the clinic\'s booking system (Zenoti, Boulevard, Vagaro, Phorest or Mindbody).', 'verbena' ); ?></div>
	<p class="note"><?php esc_html_e( 'Demo only. The theme loads this frame from the booking URL in Site settings; swap in the provider\'s embed URL and the same facade loads the real calendar.', 'verbena' ); ?></p>
</div>
<script>
(function(){var go=document.getElementById('go');
document.querySelectorAll('[data-group]').forEach(function(g){g.addEventListener('click',function(e){var b=e.target.closest('.chip');if(!b)return;g.querySelectorAll('.chip').forEach(function(c){c.setAttribute('aria-pressed',c===b?'true':'false');});
go.disabled=!document.querySelector('[data-group=time] [aria-pressed=true]');});});
go.addEventListener('click',function(){document.getElementById('done').style.display='block';});})();
</script>
</body>
</html>
