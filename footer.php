<?php
/**
 * Theme footer.
 *
 * @package Proenem
 */

?>
	<?php if ( ! is_front_page() && ! is_page_template( 'page-templates/home.php' ) ) : ?>
	<footer class="site-footer pen-site-footer">
		<div class="pen-site-footer__content">
			<span class="pen-section-pill"><?php esc_html_e( 'Manifesto Proenem', 'proenem-wordpress-theme' ); ?></span>
			<h2 class="pen-site-footer__title">
				<span><?php esc_html_e( 'Sua aprovação', 'proenem-wordpress-theme' ); ?></span><br>
				<span><?php esc_html_e( 'não é sorte.', 'proenem-wordpress-theme' ); ?></span><br>
				<span class="pen-site-footer__title-strong"><?php esc_html_e( 'É método.', 'proenem-wordpress-theme' ); ?></span>
			</h2>
			<p class="pen-site-footer__body"><?php esc_html_e( 'Construímos a infraestrutura que transforma esforço em resultado. Você estuda, a engenharia faz o resto.', 'proenem-wordpress-theme' ); ?></p>
		</div>

		<div class="pen-site-footer__links">
			<div class="pen-site-footer__column">
				<h3 class="pen-site-footer__column-title"><?php esc_html_e( 'Plataforma', 'proenem-wordpress-theme' ); ?></h3>
				<a href="<?php echo esc_url( home_url( '/#metodo' ) ); ?>"><?php esc_html_e( 'Método PRO', 'proenem-wordpress-theme' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/#planos' ) ); ?>"><?php esc_html_e( 'Planos', 'proenem-wordpress-theme' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/#questoes' ) ); ?>"><?php esc_html_e( 'Banco de questões', 'proenem-wordpress-theme' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'Blog', 'proenem-wordpress-theme' ); ?></a>
			</div>
			<div class="pen-site-footer__column">
				<h3 class="pen-site-footer__column-title"><?php esc_html_e( 'Suporte', 'proenem-wordpress-theme' ); ?></h3>
				<a href="<?php echo esc_url( home_url( '/contato/' ) ); ?>"><?php esc_html_e( 'Central de ajuda', 'proenem-wordpress-theme' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>"><?php esc_html_e( 'FAQ', 'proenem-wordpress-theme' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/status/' ) ); ?>"><?php esc_html_e( 'Status', 'proenem-wordpress-theme' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/carreiras/' ) ); ?>"><?php esc_html_e( 'Carreiras', 'proenem-wordpress-theme' ); ?></a>
			</div>
		</div>

		<div class="pen-site-footer__bottom">
			<a class="pen-site-footer__copyright" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php
				printf(
					/* translators: %s: Current year. */
					esc_html__( '@%s ProEnem - Grupo Q Educação', 'proenem-wordpress-theme' ),
					esc_html( gmdate( 'Y' ) )
				);
				?>
			</a>
			<span class="pen-site-footer__signature"><?php esc_html_e( 'Feito com ♥ para você', 'proenem-wordpress-theme' ); ?></span>
		</div>
	</footer>
	<?php endif; ?>
</div>
<?php wp_footer(); ?>
</body>
</html>
