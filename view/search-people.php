<?php

/**
 * The template for displaying people search results
 */

$search_terms = get_search_query();

get_header(); ?>

<div id="wrap">
	<div id="content" role="main">
		<h2 class="entry-title">Person search for: <?php echo $search_terms; ?></h2>

			<?php
			ALP_Templates::search_form();

			$people = ALP_Query::get_people( false, $search_terms );

			ob_start();
			require AG_EXTRES_TEMPLATE_PATH . '/people-list.php';
			$output = ob_get_contents();
			ob_clean();

			echo $output;

			?>


	</div><!-- #content -->

</div><!-- #wrap -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>
