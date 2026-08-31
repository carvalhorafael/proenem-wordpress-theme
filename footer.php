<?php
/**
 * Theme footer.
 *
 * @package Proenem
 */

?>
	<?php if ( ! proenem_is_home_surface() ) : ?>
		<?php proenem_render_site_footer(); ?>
	<?php endif; ?>
</div>
<?php wp_footer(); ?>
</body>
</html>
