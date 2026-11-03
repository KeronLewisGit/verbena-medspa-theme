<?php
/**
 * Default page template.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	verbena_page_hero( verbena_page_hero_args() );
	?>
	<div class="wrap py-14 lg:py-20">
		<div class="prose-verbena mx-auto lg:mx-0 lg:ml-[8.33%]">
			<?php the_content(); ?>
		</div>
	</div>
	<?php
endwhile;

get_footer();
