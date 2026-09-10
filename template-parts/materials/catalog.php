<?php
/**
 * Free Materials catalog body: category filters plus the results grid.
 *
 * Shared by the catalog page template and the category archive so both
 * surfaces render the same markup.
 *
 * Expected args:
 * - terms          WP_Term[] Categories to offer as filters.
 * - selected_slugs string[]  Currently selected category slugs.
 * - query          WP_Query|null Materials query, already filtered.
 * - heading        string    Results heading.
 * - heading_level  string    Heading tag for the results title.
 *
 * @package Proenem
 */

$materials_terms    = isset( $args['terms'] ) && is_array( $args['terms'] ) ? $args['terms'] : array();
$materials_selected = isset( $args['selected_slugs'] ) && is_array( $args['selected_slugs'] ) ? $args['selected_slugs'] : array();
$materials_query    = isset( $args['query'] ) && $args['query'] instanceof WP_Query ? $args['query'] : null;
$materials_heading  = isset( $args['heading'] ) && '' !== $args['heading']
	? $args['heading']
	: __( 'Todos os materiais', 'proenem-wordpress-theme' );
$materials_tag      = isset( $args['heading_level'] ) && in_array( $args['heading_level'], array( 'h2', 'h3' ), true )
	? $args['heading_level']
	: 'h2';
$materials_total    = $materials_query instanceof WP_Query ? (int) $materials_query->found_posts : 0;
$materials_featured = isset( $args['featured_id'] ) ? (int) $args['featured_id'] : 0;
?>

<div class="pro-materials-catalog">
	<section class="pro-materials-results" aria-labelledby="pro-materials-results-title">
		<div class="pro-materials-results__header">
			<<?php echo esc_html( $materials_tag ); ?> id="pro-materials-results-title"><?php echo esc_html( $materials_heading ); ?></<?php echo esc_html( $materials_tag ); ?>>
			<p data-pro-materials-count aria-live="polite">
				<?php
				printf(
					/* translators: %s: Number of materials found. */
					esc_html( _n( '%s material disponível', '%s materiais disponíveis', $materials_total, 'proenem-wordpress-theme' ) ),
					esc_html( number_format_i18n( $materials_total ) )
				);
				?>
			</p>
		</div>

		<div class="pen-blog-filter-bar pro-materials-filter-bar">
			<?php proenem_render_material_category_tabs( $materials_terms, $materials_selected ); ?>
		</div>

		<?php if ( ! proenem_free_materials_is_available() ) : ?>
			<?php
			proenem_render_materials_empty_state(
				__( 'Plugin Free Materials não está ativo.', 'proenem-wordpress-theme' ),
				__( 'Ative o plugin para publicar e listar materiais gratuitos nesta página.', 'proenem-wordpress-theme' )
			);
			?>
		<?php elseif ( $materials_query instanceof WP_Query && $materials_query->have_posts() ) : ?>
			<div class="pro-materials-grid" data-pro-materials-grid>
				<?php
				while ( $materials_query->have_posts() ) :
					$materials_query->the_post();
					proenem_render_material_card( get_the_ID(), get_the_ID() === $materials_featured );
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		<?php elseif ( ! empty( $materials_selected ) ) : ?>
			<?php
			proenem_render_materials_empty_state(
				__( 'Nenhum material encontrado.', 'proenem-wordpress-theme' ),
				__( 'Tente selecionar outra categoria ou limpar os filtros.', 'proenem-wordpress-theme' )
			);
			?>
		<?php else : ?>
			<?php
			proenem_render_materials_empty_state(
				__( 'Nenhum material encontrado.', 'proenem-wordpress-theme' ),
				__( 'Cadastre novos materiais gratuitos no WordPress para vê-los aqui.', 'proenem-wordpress-theme' )
			);
			?>
		<?php endif; ?>
	</section>
</div>
