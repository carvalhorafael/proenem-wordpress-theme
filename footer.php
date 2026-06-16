<?php
/**
 * Theme footer.
 *
 * @package Proenem
 */

?>
	<footer class="site-footer">
		<div class="site-footer__inner">
			<div class="site-footer__widgets">
				<?php for ( $footer_column = 1; $footer_column <= 3; $footer_column++ ) : ?>
					<aside class="site-footer__widget-area" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: Footer column number. */ __( 'Footer column %d', 'proenem-wordpress-theme' ), $footer_column ) ); ?>">
						<?php dynamic_sidebar( 'footer-' . $footer_column ); ?>
					</aside>
				<?php endfor; ?>
			</div>

			<div class="site-footer__bottom">
				<?php if ( is_active_sidebar( 'footer-bottom' ) ) : ?>
					<?php dynamic_sidebar( 'footer-bottom' ); ?>
				<?php else : ?>
					<p>
						<?php
						printf(
							/* translators: %s: Current year. */
							esc_html__( 'Copyright %s Proenem.', 'proenem-wordpress-theme' ),
							esc_html( gmdate( 'Y' ) )
						);
						?>
					</p>
				<?php endif; ?>
			</div>
		</div>
	</footer>
</div>
<?php wp_footer(); ?>
</body>
</html>
