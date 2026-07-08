<?php
/**
 * Theme footer.
 *
 * @package Proenem
 */

?>
	<?php if ( ! is_front_page() && ! is_page_template( 'page-templates/home.php' ) ) : ?>
		<?php proenem_render_site_footer(); ?>
	<?php endif; ?>
</div>
<?php wp_footer(); ?>
</body>
</html>
