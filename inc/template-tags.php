<?php
/**
 * Shared template helpers.
 *
 * @package Proenem
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render post metadata.
 *
 * @param int|null $post_id Post ID.
 * @return void
 */
function proenem_render_post_meta( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();

	if ( ! $post_id ) {
		return;
	}
	?>
	<div class="entry-meta" aria-label="<?php esc_attr_e( 'Post information', 'proenem-wordpress-theme' ); ?>">
		<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C, $post_id ) ); ?>">
			<?php echo esc_html( get_the_date( '', $post_id ) ); ?>
		</time>
		<span class="entry-meta__separator" aria-hidden="true">/</span>
		<span><?php echo esc_html( get_the_author_meta( 'display_name', (int) get_post_field( 'post_author', $post_id ) ) ); ?></span>
	</div>
	<?php
}

/**
 * Render posts pagination.
 *
 * @return void
 */
function proenem_render_posts_pagination() {
	$links = paginate_links(
		array(
			'type'      => 'array',
			'prev_text' => __( 'Previous', 'proenem-wordpress-theme' ),
			'next_text' => __( 'Next', 'proenem-wordpress-theme' ),
		)
	);

	if ( empty( $links ) || ! is_array( $links ) ) {
		return;
	}
	?>
	<nav class="pagination" aria-label="<?php esc_attr_e( 'Posts pagination', 'proenem-wordpress-theme' ); ?>">
		<ul class="pagination__items">
			<?php foreach ( $links as $link ) : ?>
				<li class="pagination__item"><?php echo wp_kses_post( $link ); ?></li>
			<?php endforeach; ?>
		</ul>
	</nav>
	<?php
}

/**
 * Get a listing excerpt with a stable fallback.
 *
 * @return string
 */
function proenem_get_listing_excerpt() {
	if ( has_excerpt() ) {
		return get_the_excerpt();
	}

	return wp_trim_words( get_the_content(), 28 );
}

/**
 * Get a compact excerpt for a post card.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function proenem_get_post_card_excerpt( $post_id ) {
	$excerpt = get_the_excerpt( $post_id );

	if ( $excerpt ) {
		return wp_trim_words( $excerpt, 18 );
	}

	return wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ), 18 );
}

/**
 * Get the primary visible category label for a post.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function proenem_get_post_category_label( $post_id ) {
	$categories = get_the_category( $post_id );

	if ( empty( $categories ) || is_wp_error( $categories ) ) {
		return __( 'Blog', 'proenem-wordpress-theme' );
	}

	return $categories[0]->name;
}

/**
 * Get the image slot expected by Proenem blog cards.
 *
 * @param int    $post_id Post ID.
 * @param string $size    Image size.
 * @return array{src:string,alt:string}
 */
function proenem_get_post_image_slot( $post_id, $size = 'large' ) {
	if ( has_post_thumbnail( $post_id ) ) {
		$thumbnail_id = get_post_thumbnail_id( $post_id );
		$image        = wp_get_attachment_image_src( $thumbnail_id, $size );

		if ( $image ) {
			$alt = get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true );

			return array(
				'src' => $image[0],
				'alt' => $alt ? $alt : get_the_title( $post_id ),
			);
		}
	}

	$fallbacks = array(
		'hero-student.webp',
		'pillar-meta.webp',
		'pillar-diagnostico.webp',
		'pillar-execucao.webp',
		'proof-students-1.webp',
		'student_school_1.webp',
		'student_school_2.webp',
	);
	$index     = absint( $post_id ) % count( $fallbacks );

	return array(
		'src' => PROENEM_THEME_URI . '/assets/images/home/' . $fallbacks[ $index ],
		'alt' => '',
	);
}

/**
 * Estimate post reading time.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function proenem_get_post_read_time( $post_id ) {
	$word_count = str_word_count( wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ) );
	$minutes    = max( 1, (int) ceil( $word_count / 200 ) );

	return sprintf(
		/* translators: %d: Estimated reading time in minutes. */
		_n( '%d min de leitura', '%d min de leitura', $minutes, 'proenem-wordpress-theme' ),
		$minutes
	);
}

/**
 * Get the blog landing URL.
 *
 * @return string
 */
function proenem_get_blog_url() {
	$posts_page_id = (int) get_option( 'page_for_posts' );

	if ( $posts_page_id ) {
		return get_permalink( $posts_page_id );
	}

	$post_archive = get_post_type_archive_link( 'post' );

	return $post_archive ? $post_archive : home_url( '/' );
}

/**
 * Render the category badge from the Proenem blog component contract.
 *
 * @param string $label Badge label.
 * @return void
 */
function proenem_render_post_category_badge( $label ) {
	?>
	<span class="pen-post-category-badge"><?php echo esc_html( $label ); ?></span>
	<?php
}

/**
 * Render post meta using the Proenem blog component contract.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function proenem_render_design_system_post_meta( $post_id ) {
	?>
	<div class="pen-post-meta">
		<span><?php echo esc_html( proenem_get_post_category_label( $post_id ) ); ?></span>
		<span><?php echo esc_html( get_the_date( '', $post_id ) ); ?></span>
		<span><?php echo esc_html( proenem_get_post_read_time( $post_id ) ); ?></span>
	</div>
	<?php
}

/**
 * Render a post card using the public Proenem design-system markup.
 *
 * @param int    $post_id Post ID.
 * @param string $variant Card variant.
 * @return void
 */
function proenem_render_blog_post_card( $post_id, $variant = 'default' ) {
	$image   = proenem_get_post_image_slot( $post_id, 'large' );
	$classes = 'pen-post-card pen-post-card--' . sanitize_html_class( $variant );
	?>
	<a class="<?php echo esc_attr( $classes ); ?>" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
		<span class="pen-post-card__media">
			<img src="<?php echo esc_url( $image['src'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>">
			<?php proenem_render_post_category_badge( proenem_get_post_category_label( $post_id ) ); ?>
		</span>
		<span class="pen-post-card__content">
			<?php proenem_render_design_system_post_meta( $post_id ); ?>
			<strong><?php echo esc_html( get_the_title( $post_id ) ); ?></strong>
			<span><?php echo esc_html( proenem_get_post_card_excerpt( $post_id ) ); ?></span>
		</span>
		<span class="pen-post-card__arrow" aria-hidden="true">↗</span>
	</a>
	<?php
}

/**
 * Render category filters for the blog index.
 *
 * @return void
 */
function proenem_render_blog_category_tabs() {
	$categories = get_categories(
		array(
			'hide_empty' => true,
			'number'     => 5,
		)
	);
	?>
	<nav class="pen-blog-category-tabs" aria-label="<?php esc_attr_e( 'Categorias do blog', 'proenem-wordpress-theme' ); ?>">
		<a class="pen-blog-category-tabs__item<?php echo ( is_home() && ! is_paged() ) ? ' is-active' : ''; ?>" href="<?php echo esc_url( proenem_get_blog_url() ); ?>"<?php echo ( is_home() && ! is_paged() ) ? ' aria-current="page"' : ''; ?>>
			<?php esc_html_e( 'Ver tudo', 'proenem-wordpress-theme' ); ?>
		</a>
		<?php foreach ( $categories as $category ) : ?>
			<?php $is_current = is_category( $category->term_id ); ?>
			<a class="pen-blog-category-tabs__item<?php echo $is_current ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_category_link( $category ) ); ?>"<?php echo $is_current ? ' aria-current="page"' : ''; ?>>
				<?php echo esc_html( $category->name ); ?>
			</a>
		<?php endforeach; ?>
	</nav>
	<?php
}

/**
 * Render the blog filter bar.
 *
 * @return void
 */
function proenem_render_blog_filter_bar() {
	?>
	<div class="pen-blog-filter-bar">
		<?php proenem_render_blog_category_tabs(); ?>
		<label class="pen-blog-sort-select">
			<span><?php esc_html_e( 'Ordenar posts', 'proenem-wordpress-theme' ); ?></span>
			<select name="orderby" aria-label="<?php esc_attr_e( 'Ordenar posts', 'proenem-wordpress-theme' ); ?>">
				<option value="recent"><?php esc_html_e( 'Mais recente', 'proenem-wordpress-theme' ); ?></option>
			</select>
		</label>
	</div>
	<?php
}

/**
 * Render pagination using the public Proenem pagination markup.
 *
 * @return void
 */
function proenem_render_design_system_posts_pagination() {
	global $wp_query;

	$total = isset( $wp_query->max_num_pages ) ? (int) $wp_query->max_num_pages : 1;

	if ( $total < 2 ) {
		return;
	}

	$current  = max( 1, (int) get_query_var( 'paged' ) );
	$previous = $current > 1 ? get_pagenum_link( $current - 1 ) : '';
	$next     = $current < $total ? get_pagenum_link( $current + 1 ) : '';
	?>
	<nav class="pen-pagination" aria-label="<?php esc_attr_e( 'Paginação', 'proenem-wordpress-theme' ); ?>">
		<?php if ( $previous ) : ?>
			<a class="pen-pagination__control" href="<?php echo esc_url( $previous ); ?>"><?php esc_html_e( '← Anterior', 'proenem-wordpress-theme' ); ?></a>
		<?php else : ?>
			<span class="pen-pagination__control is-disabled"><?php esc_html_e( '← Anterior', 'proenem-wordpress-theme' ); ?></span>
		<?php endif; ?>

		<div class="pen-pagination__pages">
			<?php for ( $page = 1; $page <= $total; $page++ ) : ?>
				<a class="pen-pagination__item<?php echo $page === $current ? ' is-current' : ''; ?>" href="<?php echo esc_url( get_pagenum_link( $page ) ); ?>"<?php echo $page === $current ? ' aria-current="page"' : ''; ?>>
					<?php echo esc_html( (string) $page ); ?>
				</a>
			<?php endfor; ?>
		</div>

		<?php if ( $next ) : ?>
			<a class="pen-pagination__control" href="<?php echo esc_url( $next ); ?>"><?php esc_html_e( 'Próxima →', 'proenem-wordpress-theme' ); ?></a>
		<?php else : ?>
			<span class="pen-pagination__control is-disabled"><?php esc_html_e( 'Próxima →', 'proenem-wordpress-theme' ); ?></span>
		<?php endif; ?>
	</nav>
	<?php
}

/**
 * Render the shared marketing sections that follow the blog index.
 *
 * @return void
 */
function proenem_render_blog_index_after_sections() {
	?>
	<section class="pen-marquee pen-marquee--lp pen-marquee--animated" aria-label="<?php esc_attr_e( 'Destaques Proenem', 'proenem-wordpress-theme' ); ?>">
		<div class="pen-marquee__track">
			<?php for ( $loop = 0; $loop < 2; $loop++ ) : ?>
				<span class="pen-marquee__item"><?php esc_html_e( 'Troque a sorte pela estratégia', 'proenem-wordpress-theme' ); ?></span>
				<span class="pen-marquee__separator" aria-hidden="true">⚡</span>
				<span class="pen-marquee__item"><?php esc_html_e( 'Conheça o Método PRO', 'proenem-wordpress-theme' ); ?></span>
				<span class="pen-marquee__separator" aria-hidden="true">•</span>
				<span class="pen-marquee__item"><?php esc_html_e( 'A engenharia da sua aprovação', 'proenem-wordpress-theme' ); ?></span>
				<span class="pen-marquee__separator" aria-hidden="true">⚡</span>
			<?php endfor; ?>
		</div>
	</section>

	<section class="pen-marketing-cta pro-blog__cta">
		<div class="pen-marketing-cta__content">
			<h2><?php esc_html_e( 'Pronto para transformar a preparação ENEM na sua escola?', 'proenem-wordpress-theme' ); ?></h2>
			<p><?php esc_html_e( 'Converse conosco e equipe sua instituição com uma proposta personalizada de acordo com o tamanho e perfil da sua operação.', 'proenem-wordpress-theme' ); ?></p>
		</div>
		<div class="pen-marketing-cta__actions">
			<a class="pen-button pen-button--cta-free pen-button--md" href="<?php echo esc_url( home_url( '/#planos' ) ); ?>">
				<?php esc_html_e( 'Começar gratuitamente', 'proenem-wordpress-theme' ); ?>
				<span class="pen-button__arrow" aria-hidden="true">→</span>
			</a>
		</div>
	</section>

	<section class="pen-faq-section pro-blog__faq">
		<div class="pen-faq-section__header">
			<span class="pen-section-pill"><?php esc_html_e( 'Perguntas frequentes', 'proenem-wordpress-theme' ); ?></span>
			<h2><?php esc_html_e( 'Já sentiu que estuda muito, mas a nota não sobe?', 'proenem-wordpress-theme' ); ?></h2>
		</div>
		<div class="pen-faq-section__items">
			<details class="pen-faq-item">
				<summary><?php esc_html_e( 'O que é o Método PRO?', 'proenem-wordpress-theme' ); ?></summary>
				<p><?php esc_html_e( 'É a metodologia da Proenem para organizar estudo, diagnóstico, prática e revisão em uma rotina clara.', 'proenem-wordpress-theme' ); ?></p>
			</details>
			<details class="pen-faq-item" open>
				<summary><?php esc_html_e( 'Posso começar a estudar de graça?', 'proenem-wordpress-theme' ); ?></summary>
				<p><?php esc_html_e( 'Sim. Você pode criar uma conta gratuita e acessar conteúdos iniciais antes de escolher o plano ideal.', 'proenem-wordpress-theme' ); ?></p>
			</details>
			<details class="pen-faq-item">
				<summary><?php esc_html_e( 'O que é a Aceleração PRO?', 'proenem-wordpress-theme' ); ?></summary>
				<p><?php esc_html_e( 'É uma jornada intensiva para estudantes que precisam acelerar preparação com acompanhamento e foco.', 'proenem-wordpress-theme' ); ?></p>
			</details>
		</div>
	</section>
	<?php
}

/**
 * Render latest posts for the single article page.
 *
 * @param int $current_post_id Current post ID.
 * @return void
 */
function proenem_render_latest_posts_section( $current_post_id ) {
	$latest_posts = new WP_Query(
		array(
			'post__not_in'        => array( $current_post_id ),
			'posts_per_page'      => 3,
			'ignore_sticky_posts' => true,
		)
	);

	if ( ! $latest_posts->have_posts() ) {
		return;
	}
	?>
	<section class="pen-latest-posts-section">
		<div class="pen-latest-posts-section__header">
			<h2><?php esc_html_e( 'Últimas postagens', 'proenem-wordpress-theme' ); ?></h2>
			<p><?php esc_html_e( 'Entrevistas, dicas, guias, práticas recomendadas do setor e notícias.', 'proenem-wordpress-theme' ); ?></p>
		</div>
		<div class="pen-post-grid">
			<?php
			while ( $latest_posts->have_posts() ) :
				$latest_posts->the_post();
				proenem_render_blog_post_card( get_the_ID() );
			endwhile;
			wp_reset_postdata();
			?>
		</div>
		<a class="pen-latest-posts-section__action" href="<?php echo esc_url( proenem_get_blog_url() ); ?>">
			<?php esc_html_e( 'Todas as postagens', 'proenem-wordpress-theme' ); ?>
		</a>
	</section>
	<?php
}

/**
 * Get the Free Materials post type contract.
 *
 * @return string
 */
function proenem_get_free_materials_post_type() {
	return function_exists( 'free_materials_post_type' ) ? free_materials_post_type() : 'material_gratuito';
}

/**
 * Get the Free Materials taxonomy contract.
 *
 * @return string
 */
function proenem_get_free_materials_taxonomy() {
	return function_exists( 'free_materials_taxonomy' ) ? free_materials_taxonomy() : 'material_categoria';
}

/**
 * Get the Free Materials CTA label meta key.
 *
 * @return string
 */
function proenem_get_free_materials_cta_label_meta_key() {
	return function_exists( 'free_materials_cta_label_meta_key' ) ? free_materials_cta_label_meta_key() : '_executive_signal_material_capture_label';
}

/**
 * Get the Free Materials delivery URL meta key.
 *
 * @return string
 */
function proenem_get_free_materials_delivery_url_meta_key() {
	return function_exists( 'free_materials_brevo_delivery_url_meta_key' ) ? free_materials_brevo_delivery_url_meta_key() : '_brevo_leads_capture_delivery_url';
}

/**
 * Check whether the Free Materials plugin contract is available.
 *
 * @return bool
 */
function proenem_free_materials_is_available() {
	return post_type_exists( proenem_get_free_materials_post_type() ) && taxonomy_exists( proenem_get_free_materials_taxonomy() );
}

/**
 * Check whether the current request belongs to the Free Materials surface.
 *
 * @return bool
 */
function proenem_is_free_materials_surface() {
	return is_page_template( 'page-templates/free-materials.php' )
		|| is_singular( proenem_get_free_materials_post_type() )
		|| is_tax( proenem_get_free_materials_taxonomy() );
}

/**
 * Get selected material category slugs from the request.
 *
 * @return string[]
 */
function proenem_get_selected_material_category_slugs() {
	$raw_categories = filter_input( INPUT_GET, 'material_categoria', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

	if ( ! is_array( $raw_categories ) ) {
		$raw_category = filter_input( INPUT_GET, 'material_categoria', FILTER_DEFAULT );

		$raw_categories = null === $raw_category ? array() : array( $raw_category );
	}

	$slugs = array();

	foreach ( $raw_categories as $raw_category ) {
		$slug = sanitize_title( $raw_category );

		if ( $slug ) {
			$slugs[] = $slug;
		}
	}

	return array_values( array_unique( $slugs ) );
}

/**
 * Get the material category label for cards and single pages.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function proenem_get_material_category_label( $post_id ) {
	$terms = get_the_terms( $post_id, proenem_get_free_materials_taxonomy() );

	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return __( 'Material gratuito', 'proenem-wordpress-theme' );
	}

	return $terms[0]->name;
}

/**
 * Get the material excerpt.
 *
 * @param int $post_id Post ID.
 * @param int $word_count Word count.
 * @return string
 */
function proenem_get_material_excerpt( $post_id, $word_count = 20 ) {
	$excerpt = get_the_excerpt( $post_id );

	if ( $excerpt ) {
		return wp_trim_words( $excerpt, $word_count );
	}

	return wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ), $word_count );
}

/**
 * Get the image slot expected by Proenem material cards.
 *
 * @param int    $post_id Post ID.
 * @param string $size    Image size.
 * @return array{src:string,alt:string}
 */
function proenem_get_material_image_slot( $post_id, $size = 'large' ) {
	return proenem_get_post_image_slot( $post_id, $size );
}

/**
 * Get the material CTA label.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function proenem_get_material_cta_label( $post_id ) {
	$label = get_post_meta( $post_id, proenem_get_free_materials_cta_label_meta_key(), true );

	if ( is_string( $label ) && '' !== trim( $label ) ) {
		return $label;
	}

	return __( 'Acessar material', 'proenem-wordpress-theme' );
}

/**
 * Get the material delivery URL.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function proenem_get_material_delivery_url( $post_id ) {
	$url = get_post_meta( $post_id, proenem_get_free_materials_delivery_url_meta_key(), true );

	return is_string( $url ) ? $url : '';
}

/**
 * Render a Free Materials card.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function proenem_render_material_card( $post_id ) {
	$image          = proenem_get_material_image_slot( $post_id, 'large' );
	$category_terms = get_the_terms( $post_id, proenem_get_free_materials_taxonomy() );
	$category_slugs = array();

	if ( ! empty( $category_terms ) && ! is_wp_error( $category_terms ) ) {
		$category_slugs = wp_list_pluck( $category_terms, 'slug' );
	}
	?>
	<article class="pro-material-card" data-pro-material-card data-material-categories="<?php echo esc_attr( wp_json_encode( array_values( $category_slugs ) ) ); ?>">
		<a class="pro-material-card__media" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
			<img src="<?php echo esc_url( $image['src'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>">
			<span class="pro-material-card__badge"><?php echo esc_html( proenem_get_material_category_label( $post_id ) ); ?></span>
		</a>
		<div class="pro-material-card__body">
			<h2><a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php echo esc_html( get_the_title( $post_id ) ); ?></a></h2>
			<p><?php echo esc_html( proenem_get_material_excerpt( $post_id ) ); ?></p>
			<a class="pro-material-card__action" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
				<?php echo esc_html( proenem_get_material_cta_label( $post_id ) ); ?>
				<span aria-hidden="true">→</span>
			</a>
		</div>
	</article>
	<?php
}

/**
 * Render material category filters.
 *
 * @param WP_Term[] $terms          Terms.
 * @param string[]  $selected_slugs Selected slugs.
 * @return void
 */
function proenem_render_material_category_filters( $terms, $selected_slugs ) {
	?>
		<form class="pro-materials-filter" method="get" action="<?php echo esc_url( home_url( '/materiais-gratuitos/' ) ); ?>" data-pro-materials-filter>
			<div class="pro-materials-filter__header">
				<h2><?php esc_html_e( 'Categorias', 'proenem-wordpress-theme' ); ?></h2>
				<a href="<?php echo esc_url( home_url( '/materiais-gratuitos/' ) ); ?>" data-pro-materials-clear<?php echo empty( $selected_slugs ) ? ' hidden' : ''; ?>><?php esc_html_e( 'Limpar filtros', 'proenem-wordpress-theme' ); ?></a>
			</div>
		<div class="pro-materials-filter__options">
			<?php if ( empty( $terms ) ) : ?>
				<p><?php esc_html_e( 'Nenhuma categoria cadastrada ainda.', 'proenem-wordpress-theme' ); ?></p>
			<?php else : ?>
				<?php foreach ( $terms as $term ) : ?>
					<label class="pro-materials-filter__option">
						<input type="checkbox" name="material_categoria[]" value="<?php echo esc_attr( $term->slug ); ?>"<?php checked( in_array( $term->slug, $selected_slugs, true ) ); ?>>
						<span><?php echo esc_html( $term->name ); ?></span>
						<small><?php echo esc_html( (string) $term->count ); ?></small>
					</label>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
		<button class="pen-button pen-button--primary pen-button--sm pro-materials-filter__submit" type="submit">
			<?php esc_html_e( 'Filtrar materiais', 'proenem-wordpress-theme' ); ?>
		</button>
	</form>
	<?php
}

/**
 * Render a local empty state for the materials surface.
 *
 * @param string $title Empty title.
 * @param string $body  Empty body.
 * @return void
 */
function proenem_render_materials_empty_state( $title, $body ) {
	?>
	<section class="pro-materials-empty">
		<span aria-hidden="true">✦</span>
		<h2><?php echo esc_html( $title ); ?></h2>
		<p><?php echo esc_html( $body ); ?></p>
	</section>
	<?php
}

/**
 * Get the Testimonials post type contract.
 *
 * @return string
 */
function proenem_get_testimonials_post_type() {
	return function_exists( 'testimonials_post_type' ) ? testimonials_post_type() : 'depoimento';
}

/**
 * Get the Testimonials taxonomy contract.
 *
 * @return string
 */
function proenem_get_testimonials_taxonomy() {
	return function_exists( 'testimonials_taxonomy' ) ? testimonials_taxonomy() : 'depoimento_categoria';
}

/**
 * Get the Testimonials video URL meta key.
 *
 * @return string
 */
function proenem_get_testimonials_video_url_meta_key() {
	return function_exists( 'testimonials_video_url_meta_key' ) ? testimonials_video_url_meta_key() : '_testimonials_video_url';
}

/**
 * Get the Testimonials student name meta key.
 *
 * @return string
 */
function proenem_get_testimonials_student_name_meta_key() {
	return function_exists( 'testimonials_student_name_meta_key' ) ? testimonials_student_name_meta_key() : '_testimonials_student_name';
}

/**
 * Get the Testimonials approved at meta key.
 *
 * @return string
 */
function proenem_get_testimonials_approved_at_meta_key() {
	return function_exists( 'testimonials_approved_at_meta_key' ) ? testimonials_approved_at_meta_key() : '_testimonials_approved_at';
}

/**
 * Get the Testimonials placement meta key.
 *
 * @return string
 */
function proenem_get_testimonials_placement_meta_key() {
	return function_exists( 'testimonials_placement_meta_key' ) ? testimonials_placement_meta_key() : '_testimonials_placement';
}

/**
 * Get the Testimonials course meta key.
 *
 * @return string
 */
function proenem_get_testimonials_course_meta_key() {
	return function_exists( 'testimonials_course_meta_key' ) ? testimonials_course_meta_key() : '_testimonials_course';
}

/**
 * Get the Testimonials institution meta key.
 *
 * @return string
 */
function proenem_get_testimonials_institution_meta_key() {
	return function_exists( 'testimonials_institution_meta_key' ) ? testimonials_institution_meta_key() : '_testimonials_institution';
}

/**
 * Get the Testimonials approval year meta key.
 *
 * @return string
 */
function proenem_get_testimonials_approval_year_meta_key() {
	return function_exists( 'testimonials_approval_year_meta_key' ) ? testimonials_approval_year_meta_key() : '_testimonials_approval_year';
}

/**
 * Get the Testimonials home proof selection meta key.
 *
 * @return string
 */
function proenem_get_testimonials_home_proof_enabled_meta_key() {
	return function_exists( 'testimonials_home_proof_enabled_meta_key' ) ? testimonials_home_proof_enabled_meta_key() : '_testimonials_home_proof_enabled';
}

/**
 * Check whether the Testimonials plugin contract is available.
 *
 * @return bool
 */
function proenem_testimonials_is_available() {
	return post_type_exists( proenem_get_testimonials_post_type() ) && taxonomy_exists( proenem_get_testimonials_taxonomy() );
}

/**
 * Check whether the verified home proof contract is available.
 *
 * @return bool
 */
function proenem_testimonials_home_proof_is_available() {
	return proenem_testimonials_is_available() && function_exists( 'testimonials_is_home_proof_eligible' );
}

/**
 * Check whether the current request belongs to the Testimonials surface.
 *
 * @return bool
 */
function proenem_is_testimonials_surface() {
	return is_page_template( 'page-templates/testimonials.php' )
		|| is_singular( proenem_get_testimonials_post_type() )
		|| is_tax( proenem_get_testimonials_taxonomy() );
}

/**
 * Get the Testimonials listing page URL.
 *
 * @return string
 */
function proenem_get_testimonials_url() {
	return home_url( '/depoimentos/' );
}

/**
 * Get selected testimonial category slugs from the request.
 *
 * @return string[]
 */
function proenem_get_selected_testimonial_category_slugs() {
	$raw_categories = filter_input( INPUT_GET, 'depoimento_categoria', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

	if ( ! is_array( $raw_categories ) ) {
		$raw_category = filter_input( INPUT_GET, 'depoimento_categoria', FILTER_DEFAULT );

		$raw_categories = null === $raw_category ? array() : array( $raw_category );
	}

	$slugs = array();

	foreach ( $raw_categories as $raw_category ) {
		$slug = sanitize_title( $raw_category );

		if ( $slug ) {
			$slugs[] = $slug;
		}
	}

	return array_values( array_unique( $slugs ) );
}

/**
 * Get the testimonial category label for cards and single pages.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function proenem_get_testimonial_category_label( $post_id ) {
	$terms = get_the_terms( $post_id, proenem_get_testimonials_taxonomy() );

	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return __( 'Depoimento', 'proenem-wordpress-theme' );
	}

	return $terms[0]->name;
}

/**
 * Get a trimmed testimonial quote.
 *
 * @param int $post_id Post ID.
 * @param int $word_count Word count.
 * @return string
 */
function proenem_get_testimonial_quote( $post_id, $word_count = 30 ) {
	$excerpt = get_the_excerpt( $post_id );

	if ( $excerpt ) {
		return wp_trim_words( $excerpt, $word_count );
	}

	return wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ), $word_count );
}

/**
 * Get a string testimonial meta value.
 *
 * @param int    $post_id  Post ID.
 * @param string $meta_key Meta key.
 * @return string
 */
function proenem_get_testimonial_string_meta( $post_id, $meta_key ) {
	$value = get_post_meta( $post_id, $meta_key, true );

	return is_string( $value ) ? trim( $value ) : '';
}

/**
 * Get the testimonial student name.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function proenem_get_testimonial_student_name( $post_id ) {
	$student_name = proenem_get_testimonial_string_meta( $post_id, proenem_get_testimonials_student_name_meta_key() );

	return $student_name ? $student_name : get_the_title( $post_id );
}

/**
 * Get the testimonial approved at value.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function proenem_get_testimonial_approved_at( $post_id ) {
	return proenem_get_testimonial_string_meta( $post_id, proenem_get_testimonials_approved_at_meta_key() );
}

/**
 * Get the testimonial placement value.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function proenem_get_testimonial_placement( $post_id ) {
	return proenem_get_testimonial_string_meta( $post_id, proenem_get_testimonials_placement_meta_key() );
}

/**
 * Get the testimonial course.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function proenem_get_testimonial_course( $post_id ) {
	return proenem_get_testimonial_string_meta( $post_id, proenem_get_testimonials_course_meta_key() );
}

/**
 * Get the testimonial institution.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function proenem_get_testimonial_institution( $post_id ) {
	return proenem_get_testimonial_string_meta( $post_id, proenem_get_testimonials_institution_meta_key() );
}

/**
 * Get the testimonial approval year.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function proenem_get_testimonial_approval_year( $post_id ) {
	return proenem_get_testimonial_string_meta( $post_id, proenem_get_testimonials_approval_year_meta_key() );
}

/**
 * Get verified testimonial records selected for the home proof section.
 *
 * @param int[] $requested_ids Optional explicitly selected post IDs.
 * @param int   $limit Maximum number of records.
 * @return WP_Post[]
 */
function proenem_get_home_proof_testimonials( $requested_ids = array(), $limit = 6 ) {
	if ( ! proenem_testimonials_home_proof_is_available() ) {
		return array();
	}

	$limit         = max( 1, min( 12, absint( $limit ) ) );
	$requested_ids = array_values( array_unique( array_filter( array_map( 'absint', (array) $requested_ids ) ) ) );
	$query_args    = array(
		'ignore_sticky_posts' => true,
		'meta_key'            => proenem_get_testimonials_home_proof_enabled_meta_key(), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- The plugin owns this explicit editorial selection contract.
		'meta_value'          => '1', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- The query must exclude records not selected for home proof.
		'no_found_rows'       => true,
		'order'               => 'DESC',
		'orderby'             => 'date',
		'post_status'         => 'publish',
		'post_type'           => proenem_get_testimonials_post_type(),
		'posts_per_page'      => -1,
		'suppress_filters'    => false,
	);

	if ( $requested_ids ) {
		$query_args['orderby']  = 'post__in';
		$query_args['post__in'] = $requested_ids;
	}

	$testimonials = get_posts( $query_args );

	return array_slice(
		array_values(
			array_filter(
				$testimonials,
				static function ( $testimonial ) {
					return $testimonial instanceof WP_Post && testimonials_is_home_proof_eligible( $testimonial->ID );
				}
			)
		),
		0,
		$limit
	);
}

/**
 * Get eligible home proof records as Elementor select options.
 *
 * @return array<int,string>
 */
function proenem_get_home_proof_testimonial_options() {
	$options = array();

	foreach ( proenem_get_home_proof_testimonials( array(), 12 ) as $testimonial ) {
		$options[ $testimonial->ID ] = sprintf(
			/* translators: 1: Student name. 2: Course. 3: Institution. */
			__( '%1$s — %2$s, %3$s', 'proenem-wordpress-theme' ),
			proenem_get_testimonial_student_name( $testimonial->ID ),
			proenem_get_testimonial_course( $testimonial->ID ),
			proenem_get_testimonial_institution( $testimonial->ID )
		);
	}

	return $options;
}

/**
 * Normalize persisted home proof copy across Elementor revisions.
 *
 * @param string $copy Persisted or default copy.
 * @param string $context Copy context: title, support or testimonials.
 * @return string
 */
function proenem_normalize_home_proof_copy( $copy, $context ) {
	$defaults = array(
		'title'        => __( '+ de 40.000 aprovados em universidades públicas', 'proenem-wordpress-theme' ),
		'support'      => __( 'Alunos reais, aprovados em algumas das universidades mais concorridas do país.', 'proenem-wordpress-theme' ),
		'testimonials' => __( 'Conheça histórias de alunos que estudaram com a Proenem.', 'proenem-wordpress-theme' ),
	);
	$replaced = array(
		'title'        => array( 'Aprovações verificadas de alunos da Proenem' ),
		'support'      => array( 'Dados de aprovação conferidos e publicados com autorização.' ),
		'testimonials' => array(
			'Mais de 40 mil alunos já foram aprovados com a Proenem. Conheça algumas histórias.',
			'Mais de 40 mil alunos já foram aprovados com a ProEnem. Conheça algumas histórias.',
		),
	);
	$copy     = trim( (string) $copy );

	if ( ! isset( $defaults[ $context ] ) ) {
		return $copy;
	}

	return '' === $copy || in_array( $copy, $replaced[ $context ], true ) ? $defaults[ $context ] : $copy;
}

/**
 * Render the shared verified home proof section.
 *
 * @param WP_Post[] $testimonials Eligible testimonial records.
 * @param array     $args Section copy and identifiers.
 * @return void
 */
function proenem_render_home_proof_section( $testimonials, $args = array() ) {
	$testimonials = array_values( array_filter( (array) $testimonials, static fn( $item ) => $item instanceof WP_Post ) );
	$universities = array(
		array(
			'name'   => __( 'UFRJ', 'proenem-wordpress-theme' ),
			'file'   => 'proof-logo-ufrj.webp',
			'width'  => 206,
			'height' => 102,
		),
		array(
			'name'   => __( 'UFRGS', 'proenem-wordpress-theme' ),
			'file'   => 'proof-logo-ufrgs.webp',
			'width'  => 117,
			'height' => 94,
		),
		array(
			'name'   => __( 'Unicamp', 'proenem-wordpress-theme' ),
			'file'   => 'proof-logo-unicamp.webp',
			'width'  => 99,
			'height' => 105,
		),
		array(
			'name'   => __( 'UFMG', 'proenem-wordpress-theme' ),
			'file'   => 'proof-logo-ufmg.webp',
			'width'  => 206,
			'height' => 88,
		),
		array(
			'name'   => __( 'USP', 'proenem-wordpress-theme' ),
			'file'   => 'proof-logo-usp.webp',
			'width'  => 171,
			'height' => 70,
		),
		array(
			'name'   => __( 'Unifesp', 'proenem-wordpress-theme' ),
			'file'   => 'proof-logo-unifesp.webp',
			'width'  => 182,
			'height' => 110,
		),
	);

	if ( empty( $testimonials ) ) {
		return;
	}

	$args = wp_parse_args(
		$args,
		array(
			'badge_line_1' => __( 'Nossos', 'proenem-wordpress-theme' ),
			'badge_line_2' => __( 'Alunos!', 'proenem-wordpress-theme' ),
			'heading_id'   => 'pro-proof-title',
			'section_id'   => 'aprovados',
			'support'      => proenem_normalize_home_proof_copy( '', 'support' ),
			'title'        => proenem_normalize_home_proof_copy( '', 'title' ),
		)
	);
	?>
	<section id="<?php echo esc_attr( $args['section_id'] ); ?>" class="pen-proof-section" aria-labelledby="<?php echo esc_attr( $args['heading_id'] ); ?>">
		<div class="pen-proof-section__students pro-home-proof-students">
			<p class="pen-proof-section__badge">
				<span><?php echo esc_html( $args['badge_line_1'] ); ?></span>
				<span><?php echo esc_html( $args['badge_line_2'] ); ?></span>
			</p>
			<?php foreach ( $testimonials as $testimonial ) : ?>
				<?php
				$student_name = proenem_get_testimonial_student_name( $testimonial->ID );
				$course       = proenem_get_testimonial_course( $testimonial->ID );
				$institution  = proenem_get_testimonial_institution( $testimonial->ID );
				$year         = proenem_get_testimonial_approval_year( $testimonial->ID );
				?>
				<figure class="pen-proof-section__student pro-home-proof-student">
					<?php
					echo get_the_post_thumbnail(
						$testimonial->ID,
						'medium_large',
						array(
							'alt'      => sprintf(
								/* translators: %s: Student name. */
								__( 'Foto de %s.', 'proenem-wordpress-theme' ),
								$student_name
							),
							'class'    => 'pen-proof-section__image',
							'decoding' => 'async',
							'loading'  => 'lazy',
						)
					); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
					<figcaption class="pen-proof-section__caption pro-home-proof-student__caption">
						<strong class="pen-proof-section__student-name"><?php echo esc_html( $student_name ); ?></strong>
						<span class="pen-proof-section__student-result"><?php echo esc_html( $course ); ?> · <?php echo esc_html( $institution ); ?></span>
						<?php if ( $year ) : ?>
							<time class="pen-proof-section__student-year" datetime="<?php echo esc_attr( $year ); ?>"><?php echo esc_html( $year ); ?></time>
						<?php endif; ?>
					</figcaption>
				</figure>
			<?php endforeach; ?>
		</div>
		<div class="pen-proof-section__strip">
			<h2 id="<?php echo esc_attr( $args['heading_id'] ); ?>"><?php echo esc_html( $args['title'] ); ?></h2>
			<p class="pro-home-proof-support"><?php echo esc_html( $args['support'] ); ?></p>
			<div class="pen-proof-section__logos" aria-label="<?php esc_attr_e( 'Universidades públicas com alunos aprovados pela Proenem', 'proenem-wordpress-theme' ); ?>">
				<?php foreach ( $universities as $university ) : ?>
					<img
						class="pen-proof-section__logo"
						src="<?php echo esc_url( get_theme_file_uri( '/assets/images/home/' . $university['file'] ) ); ?>"
						alt="<?php echo esc_attr( $university['name'] ); ?>"
						width="<?php echo esc_attr( $university['width'] ); ?>"
						height="<?php echo esc_attr( $university['height'] ); ?>"
						loading="lazy"
						decoding="async"
					>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Get verified testimonial records with an approved quote for the home carousel.
 *
 * @param int[] $requested_ids Optional explicitly selected post IDs.
 * @param int   $limit Maximum number of records.
 * @return WP_Post[]
 */
function proenem_get_home_testimonials( $requested_ids = array(), $limit = 6 ) {
	$limit = max( 1, min( 12, absint( $limit ) ) );

	return array_slice(
		array_values(
			array_filter(
				proenem_get_home_proof_testimonials( $requested_ids, 12 ),
				static function ( $testimonial ) {
					return $testimonial instanceof WP_Post && '' !== trim( proenem_get_testimonial_quote( $testimonial->ID, 40 ) );
				}
			)
		),
		0,
		$limit
	);
}

/**
 * Get eligible home carousel records as Elementor select options.
 *
 * @return array<int,string>
 */
function proenem_get_home_testimonial_options() {
	$options = array();

	foreach ( proenem_get_home_testimonials( array(), 12 ) as $testimonial ) {
		$options[ $testimonial->ID ] = sprintf(
			/* translators: 1: Student name. 2: Course. 3: Institution. */
			__( '%1$s — %2$s, %3$s', 'proenem-wordpress-theme' ),
			proenem_get_testimonial_student_name( $testimonial->ID ),
			proenem_get_testimonial_course( $testimonial->ID ),
			proenem_get_testimonial_institution( $testimonial->ID )
		);
	}

	return $options;
}

/**
 * Render the shared verified testimonial carousel on the home.
 *
 * @param WP_Post[] $testimonials Eligible testimonial records.
 * @param array     $args Section copy, link and identifiers.
 * @return void
 */
function proenem_render_home_testimonials_section( $testimonials, $args = array() ) {
	$testimonials = array_values( array_filter( (array) $testimonials, static fn( $item ) => $item instanceof WP_Post ) );

	if ( empty( $testimonials ) ) {
		return;
	}

	$args = wp_parse_args(
		$args,
		array(
			'body'           => proenem_normalize_home_proof_copy( '', 'testimonials' ),
			'eyebrow'        => __( 'Aprovados', 'proenem-wordpress-theme' ),
			'heading_id'     => 'pro-testimonials-title',
			'more_label'     => __( 'Ver mais', 'proenem-wordpress-theme' ),
			'more_url'       => 'https://aprovados.proenem.com.br/',
			'section_id'     => 'depoimentos',
			'title_emphasis' => __( 'chegou na vaga.', 'proenem-wordpress-theme' ),
			'title_line'     => __( 'Quem estudou com método,', 'proenem-wordpress-theme' ),
		)
	);

	$active_index = count( $testimonials ) > 1 ? 1 : 0;
	?>
	<section id="<?php echo esc_attr( $args['section_id'] ); ?>" class="pro-home-testimonials" aria-labelledby="<?php echo esc_attr( $args['heading_id'] ); ?>" data-pro-home-testimonials-slider>
		<div class="pro-home-testimonials__header">
			<span class="pen-section-pill"><?php echo esc_html( $args['eyebrow'] ); ?></span>
			<h2 id="<?php echo esc_attr( $args['heading_id'] ); ?>">
				<span><?php echo esc_html( $args['title_line'] ); ?></span>
				<strong><?php echo esc_html( $args['title_emphasis'] ); ?></strong>
			</h2>
			<p><?php echo esc_html( proenem_normalize_home_proof_copy( $args['body'], 'testimonials' ) ); ?></p>
		</div>
		<div class="pro-home-testimonials__viewport">
			<div class="pro-home-testimonials__track" role="group" aria-labelledby="<?php echo esc_attr( $args['heading_id'] ); ?>" tabindex="0" data-pro-home-testimonials-track>
				<?php foreach ( $testimonials as $testimonial_index => $testimonial ) : ?>
					<?php
					$student_name = proenem_get_testimonial_student_name( $testimonial->ID );
					$result       = implode(
						' · ',
						array_filter(
							array(
								proenem_get_testimonial_course( $testimonial->ID ),
								proenem_get_testimonial_institution( $testimonial->ID ),
								proenem_get_testimonial_approval_year( $testimonial->ID ),
							)
						)
					);
					?>
					<article class="pro-home-testimonial-card<?php echo $active_index === $testimonial_index ? ' is-active' : ''; ?>" data-pro-home-testimonial-card>
						<blockquote class="pro-home-testimonial-card__quote">
							<span aria-hidden="true">“</span>
							<p><?php echo esc_html( proenem_get_testimonial_quote( $testimonial->ID, 40 ) ); ?></p>
						</blockquote>
						<footer>
							<?php
							echo get_the_post_thumbnail(
								$testimonial->ID,
								'thumbnail',
								array(
									'alt'      => sprintf(
										/* translators: %s: Student name. */
										__( 'Foto de %s.', 'proenem-wordpress-theme' ),
										$student_name
									),
									'decoding' => 'async',
									'loading'  => 'lazy',
								)
							); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
							<span>
								<strong><?php echo esc_html( $student_name ); ?></strong>
								<small><?php echo esc_html( $result ); ?></small>
							</span>
						</footer>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
		<div class="pro-home-testimonials__controls" aria-label="<?php esc_attr_e( 'Controles dos depoimentos', 'proenem-wordpress-theme' ); ?>">
			<?php if ( count( $testimonials ) > 1 ) : ?>
				<button type="button" data-pro-home-testimonials-prev aria-label="<?php esc_attr_e( 'Depoimento anterior', 'proenem-wordpress-theme' ); ?>">←</button>
				<button type="button" data-pro-home-testimonials-next aria-label="<?php esc_attr_e( 'Próximo depoimento', 'proenem-wordpress-theme' ); ?>">→</button>
			<?php endif; ?>
			<a class="pen-button pen-button--secondary pen-button--md pro-home-testimonials__more" href="<?php echo esc_url( $args['more_url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $args['more_label'] ); ?></a>
		</div>
	</section>
	<?php
}

/**
 * Get the testimonial approval summary.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function proenem_get_testimonial_approval_summary( $post_id ) {
	$placement   = proenem_get_testimonial_placement( $post_id );
	$approved_at = proenem_get_testimonial_approved_at( $post_id );

	if ( $placement && $approved_at ) {
		return sprintf(
			/* translators: 1: Student placement. 2: Where the student was approved. */
			__( '%1$s · %2$s', 'proenem-wordpress-theme' ),
			$placement,
			$approved_at
		);
	}

	if ( $approved_at ) {
		return $approved_at;
	}

	if ( $placement ) {
		return $placement;
	}

	return proenem_get_testimonial_category_label( $post_id );
}

/**
 * Get the testimonial video URL.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function proenem_get_testimonial_video_url( $post_id ) {
	$url = get_post_meta( $post_id, proenem_get_testimonials_video_url_meta_key(), true );

	return is_string( $url ) ? $url : '';
}

/**
 * Get a YouTube video ID from common YouTube URL formats.
 *
 * @param string $url Video URL.
 * @return string
 */
function proenem_get_youtube_video_id( $url ) {
	$parts = wp_parse_url( $url );

	if ( empty( $parts['host'] ) ) {
		return '';
	}

	$host = strtolower( $parts['host'] );
	$path = isset( $parts['path'] ) ? trim( $parts['path'], '/' ) : '';

	if ( false !== strpos( $host, 'youtu.be' ) ) {
		return sanitize_text_field( strtok( $path, '/' ) );
	}

	if ( false !== strpos( $host, 'youtube.com' ) ) {
		if ( ! empty( $parts['query'] ) ) {
			parse_str( $parts['query'], $query );

			if ( ! empty( $query['v'] ) && is_string( $query['v'] ) ) {
				return sanitize_text_field( $query['v'] );
			}
		}

		if ( preg_match( '#(?:embed|shorts)/([^/?]+)#', $path, $matches ) ) {
			return sanitize_text_field( $matches[1] );
		}
	}

	return '';
}

/**
 * Get the testimonial video thumbnail URL.
 *
 * @param int    $post_id   Post ID.
 * @param string $video_url Video URL.
 * @return string
 */
function proenem_get_testimonial_video_thumbnail_url( $post_id, $video_url ) {
	$youtube_id = proenem_get_youtube_video_id( $video_url );

	if ( $youtube_id ) {
		return 'https://img.youtube.com/vi/' . rawurlencode( $youtube_id ) . '/hqdefault.jpg';
	}

	$image = proenem_get_post_image_slot( $post_id, 'large' );

	return $image['src'];
}

/**
 * Get an embeddable video URL with autoplay for inline testimonial playback.
 *
 * @param string $video_url Video URL.
 * @return string
 */
function proenem_get_testimonial_video_embed_url( $video_url ) {
	$youtube_id = proenem_get_youtube_video_id( $video_url );

	if ( $youtube_id ) {
		return add_query_arg(
			array(
				'autoplay' => '1',
				'rel'      => '0',
			),
			'https://www.youtube.com/embed/' . rawurlencode( $youtube_id )
		);
	}

	return esc_url_raw( $video_url );
}

/**
 * Get allowed HTML for WordPress oEmbed output.
 *
 * @return array<string,array<string,bool>>
 */
function proenem_get_oembed_allowed_html() {
	return array(
		'iframe' => array(
			'allow'           => true,
			'allowfullscreen' => true,
			'class'           => true,
			'frameborder'     => true,
			'height'          => true,
			'loading'         => true,
			'referrerpolicy'  => true,
			'src'             => true,
			'style'           => true,
			'title'           => true,
			'width'           => true,
		),
	);
}

/**
 * Render a testimonial card for theme-owned testimonial archives.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function proenem_render_testimonial_card( $post_id ) {
	$category_terms = get_the_terms( $post_id, proenem_get_testimonials_taxonomy() );
	$category_slugs = array();
	$video_url      = proenem_get_testimonial_video_url( $post_id );
	$embed_url      = $video_url ? proenem_get_testimonial_video_embed_url( $video_url ) : '';
	$thumbnail_url  = $video_url ? proenem_get_testimonial_video_thumbnail_url( $post_id, $video_url ) : proenem_get_post_image_slot( $post_id, 'large' )['src'];
	$student_name   = proenem_get_testimonial_student_name( $post_id );
	$approval_label = proenem_get_testimonial_approval_summary( $post_id );

	if ( ! empty( $category_terms ) && ! is_wp_error( $category_terms ) ) {
		$category_slugs = wp_list_pluck( $category_terms, 'slug' );
	}
	?>
	<article class="pro-testimonial-card-wrap" data-pro-testimonial-card data-testimonial-categories="<?php echo esc_attr( wp_json_encode( array_values( $category_slugs ) ) ); ?>">
		<div class="testimonials-card pro-testimonial-card">
			<div class="pro-testimonial-card__video" data-pro-testimonial-video>
				<?php if ( $embed_url ) : ?>
					<button
						class="pro-testimonial-card__play"
						type="button"
						data-pro-testimonial-play
						data-embed-url="<?php echo esc_url( $embed_url ); ?>"
						aria-label="<?php echo esc_attr( sprintf( /* translators: %s: Student name. */ __( 'Reproduzir vídeo de %s', 'proenem-wordpress-theme' ), $student_name ) ); ?>"
					>
						<img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="">
						<span aria-hidden="true"></span>
					</button>
				<?php else : ?>
					<img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="">
				<?php endif; ?>
			</div>
			<div class="pro-testimonial-card__body">
				<blockquote class="testimonials-card__quote">
					<p><?php echo esc_html( proenem_get_testimonial_quote( $post_id, 40 ) ); ?></p>
				</blockquote>
				<footer class="pro-testimonial-card__footer">
					<strong><?php echo esc_html( $student_name ); ?></strong>
					<p><?php echo esc_html( $approval_label ); ?></p>
				</footer>
				<a class="testimonials-card__action" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
					<?php esc_html_e( 'Ver história completa', 'proenem-wordpress-theme' ); ?>
					<span aria-hidden="true">→</span>
				</a>
			</div>
		</div>
	</article>
	<?php
}

/**
 * Render testimonial category filters.
 *
 * @param WP_Term[] $terms          Terms.
 * @param string[]  $selected_slugs Selected slugs.
 * @return void
 */
function proenem_render_testimonial_category_filters( $terms, $selected_slugs ) {
	?>
	<form class="pro-materials-filter pro-testimonials-filter" method="get" action="<?php echo esc_url( proenem_get_testimonials_url() ); ?>">
		<div class="pro-materials-filter__header">
			<h2><?php esc_html_e( 'Categorias', 'proenem-wordpress-theme' ); ?></h2>
			<a href="<?php echo esc_url( proenem_get_testimonials_url() ); ?>"<?php echo empty( $selected_slugs ) ? ' hidden' : ''; ?>><?php esc_html_e( 'Limpar filtros', 'proenem-wordpress-theme' ); ?></a>
		</div>
		<div class="pro-materials-filter__options">
			<?php if ( empty( $terms ) ) : ?>
				<p><?php esc_html_e( 'Nenhuma categoria cadastrada ainda.', 'proenem-wordpress-theme' ); ?></p>
			<?php else : ?>
				<?php foreach ( $terms as $term ) : ?>
					<label class="pro-materials-filter__option">
						<input type="checkbox" name="depoimento_categoria[]" value="<?php echo esc_attr( $term->slug ); ?>"<?php checked( in_array( $term->slug, $selected_slugs, true ) ); ?>>
						<span><?php echo esc_html( $term->name ); ?></span>
						<small><?php echo esc_html( (string) $term->count ); ?></small>
					</label>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
		<button class="pen-button pen-button--primary pen-button--sm pro-materials-filter__submit" type="submit">
			<?php esc_html_e( 'Filtrar depoimentos', 'proenem-wordpress-theme' ); ?>
		</button>
	</form>
	<?php
}

/**
 * Render a local empty state for the testimonials surface.
 *
 * @param string $title Empty title.
 * @param string $body  Empty body.
 * @return void
 */
function proenem_render_testimonials_empty_state( $title, $body ) {
	?>
	<section class="pro-materials-empty pro-testimonials-empty">
		<span aria-hidden="true">✦</span>
		<h2><?php echo esc_html( $title ); ?></h2>
		<p><?php echo esc_html( $body ); ?></p>
	</section>
	<?php
}

/**
 * Get configurable footer menu columns.
 *
 * @return array<string,string>
 */
function proenem_get_footer_menu_columns() {
	return array(
		'footer-subjects'    => __( 'Matérias lecionadas', 'proenem-wordpress-theme' ),
		'footer-answer-keys' => __( 'Gabaritos', 'proenem-wordpress-theme' ),
		'footer-tools'       => __( 'Ferramentas', 'proenem-wordpress-theme' ),
	);
}

/**
 * Render one configurable footer menu column.
 *
 * @param string $location Menu location.
 * @param string $title    Column title.
 * @return void
 */
function proenem_render_footer_menu_column( $location, $title ) {
	if ( ! has_nav_menu( $location ) ) {
		return;
	}
	?>
	<section class="pen-site-footer__column pen-site-footer__column--<?php echo esc_attr( sanitize_html_class( $location ) ); ?>" aria-labelledby="<?php echo esc_attr( 'proenem-' . $location . '-title' ); ?>">
		<h3 id="<?php echo esc_attr( 'proenem-' . $location . '-title' ); ?>" class="pen-site-footer__column-title"><?php echo esc_html( $title ); ?></h3>
		<?php
		wp_nav_menu(
			array(
				'theme_location' => $location,
				'container'      => false,
				'menu_class'     => 'pen-site-footer__menu',
				'depth'          => 1,
				'fallback_cb'    => false,
			)
		);
		?>
	</section>
	<?php
}

/**
 * Render a configurable footer widget area.
 *
 * @param string $sidebar_id Sidebar ID.
 * @param string $class_name Extra class name.
 * @return void
 */
function proenem_render_footer_widget_area( $sidebar_id, $class_name = '' ) {
	if ( ! is_active_sidebar( $sidebar_id ) ) {
		return;
	}
	?>
	<div class="pen-site-footer__widget-area <?php echo esc_attr( $class_name ); ?>">
		<?php dynamic_sidebar( $sidebar_id ); ?>
	</div>
	<?php
}

/**
 * Render the shared Proenem footer.
 *
 * @return void
 */
function proenem_render_site_footer() {
	$footer_columns = proenem_get_footer_menu_columns();
	?>
	<footer class="pen-site-footer">
		<div class="pen-site-footer__content">
			<p class="pen-section-pill"><?php esc_html_e( 'Manifesto Proenem', 'proenem-wordpress-theme' ); ?></p>
			<h2 class="pen-site-footer__title">
				<?php esc_html_e( 'Sua aprovação', 'proenem-wordpress-theme' ); ?><br>
				<span><?php esc_html_e( 'não é sorte.', 'proenem-wordpress-theme' ); ?></span><br>
				<strong><?php esc_html_e( 'É método.', 'proenem-wordpress-theme' ); ?></strong>
			</h2>
			<p class="pen-site-footer__body"><?php esc_html_e( 'Construímos a infraestrutura que transforma esforço em resultado: método, ritmo e correção de rota. Você estuda com estratégia — e não estuda sozinho.', 'proenem-wordpress-theme' ); ?></p>
			<div class="pen-site-footer__manifest-links">
				<?php proenem_render_footer_menu_column( 'footer-classes', __( 'Nossas turmas', 'proenem-wordpress-theme' ) ); ?>
			</div>
		</div>

		<div class="pen-site-footer__top-widgets">
			<?php proenem_render_footer_widget_area( 'footer-social', 'pen-site-footer__social' ); ?>
		</div>

		<nav class="pen-site-footer__links" aria-label="<?php esc_attr_e( 'Links do rodapé', 'proenem-wordpress-theme' ); ?>">
			<?php
			foreach ( $footer_columns as $location => $title ) {
				proenem_render_footer_menu_column( $location, $title );
			}
			?>
		</nav>

		<?php if ( has_nav_menu( 'footer-legal' ) ) : ?>
			<nav class="pen-site-footer__legal" aria-label="<?php esc_attr_e( 'Links legais', 'proenem-wordpress-theme' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer-legal',
						'container'      => false,
						'menu_class'     => 'pen-site-footer__legal-menu',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>
		<?php endif; ?>

		<div class="pen-site-footer__meta">
			<?php proenem_render_footer_widget_area( 'footer-trust', 'pen-site-footer__trust' ); ?>
			<?php proenem_render_footer_widget_area( 'footer-payment', 'pen-site-footer__payment' ); ?>
		</div>

		<div class="pen-site-footer__bottom">
			<div class="pen-site-footer__company">
				<a class="pen-site-footer__copyright" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php
					printf(
						/* translators: %s: Current year. */
						esc_html__( '@%s Proenem - Grupo Q Educação', 'proenem-wordpress-theme' ),
						esc_html( gmdate( 'Y' ) )
					);
					?>
				</a>
				<?php proenem_render_footer_widget_area( 'footer-company-info', 'pen-site-footer__company-info' ); ?>
			</div>
			<span class="pen-site-footer__signature"><?php esc_html_e( 'Feito com ♥ para você', 'proenem-wordpress-theme' ); ?></span>
		</div>
	</footer>
	<?php
}

/**
 * Get primary navigation data for the design-system navbar.
 *
 * @param string $context Navigation context.
 * @param int    $menu_id Optional menu term ID.
 * @return array{links:array<int,array<string,mixed>>,actions:array<int,array<string,mixed>>}
 */
function proenem_get_primary_navigation_items( $context = 'site', $menu_id = 0 ) {
	$navigation = array(
		'links'   => array(),
		'actions' => array(),
	);

	$menu_id = absint( $menu_id );

	if ( ! $menu_id ) {
		$locations = get_nav_menu_locations();

		if ( empty( $locations['primary'] ) ) {
			return $navigation;
		}

		$menu_id = absint( $locations['primary'] );
	}

	$menu_items = wp_get_nav_menu_items( $menu_id );

	if ( empty( $menu_items ) || is_wp_error( $menu_items ) ) {
		return $navigation;
	}

	$children = array();

	foreach ( $menu_items as $menu_item ) {
		if ( '0' === (string) $menu_item->menu_item_parent ) {
			continue;
		}

		$children[ (int) $menu_item->menu_item_parent ][] = $menu_item;
	}

	foreach ( $menu_items as $menu_item ) {
		if ( '0' !== (string) $menu_item->menu_item_parent ) {
			continue;
		}

		$classes = array_filter( (array) $menu_item->classes );
		$item    = array(
			'url'      => proenem_resolve_primary_navigation_url( $menu_item->url, $menu_item->title ),
			'label'    => $menu_item->title,
			'target'   => $menu_item->target,
			'rel'      => $menu_item->xfn,
			'classes'  => array_map( 'sanitize_html_class', $classes ),
			'active'   => in_array( 'current-menu-item', $classes, true ) || in_array( 'current-menu-ancestor', $classes, true ),
			'children' => array(),
		);

		foreach ( $children[ (int) $menu_item->ID ] ?? array() as $child_menu_item ) {
			$child_classes      = array_filter( (array) $child_menu_item->classes );
			$item['children'][] = array(
				'url'     => proenem_resolve_primary_navigation_url( $child_menu_item->url, $child_menu_item->title ),
				'label'   => $child_menu_item->title,
				'target'  => $child_menu_item->target,
				'rel'     => $child_menu_item->xfn,
				'classes' => array_map( 'sanitize_html_class', $child_classes ),
				'active'  => in_array( 'current-menu-item', $child_classes, true ),
			);
		}

		if ( in_array( 'pen-navbar-action', $classes, true ) ) {
			$item['variant']         = in_array( 'pen-navbar-action-secondary', $classes, true ) ? 'secondary' : 'primary';
			$navigation['actions'][] = $item;
			continue;
		}

		$navigation['links'][] = $item;
	}

	return $navigation;
}

/**
 * Get a canonical home conversion destination.
 *
 * These URLs are presentation-level defaults. Consumers may override them with
 * the filter when the approved destination changes outside a theme release.
 *
 * @param string $intent Conversion intent.
 * @return string
 */
function proenem_get_home_cta_destination( $intent ) {
	$destinations = array(
		'advanced'   => 'https://medicina.proenem.com.br/',
		'method_pro' => 'https://pay.hotmart.com/W106752534O?off=jg51ayrs&checkoutMode=10',
		'plans'      => home_url( '/#planos' ),
		'questions'  => 'https://estude.proenem.com.br/treino/questoes',
		'signup'     => 'https://estude.proenem.com.br/signup',
		'study'      => 'https://estude.proenem.com.br/',
	);

	$destination = $destinations[ $intent ] ?? '';

	/**
	 * Filter a canonical home conversion destination.
	 *
	 * @param string $destination Destination URL.
	 * @param string $intent      Conversion intent.
	 */
	return (string) apply_filters( 'proenem_home_cta_destination', $destination, $intent );
}

/**
 * Replace invalid persisted menu placeholders with a compatible destination.
 *
 * Persisted WordPress menu data remains the source of truth. This fallback
 * prevents a literal hash from reaching the rendered navigation before the
 * operational content sync has been applied to an environment.
 *
 * @param string $url   Persisted menu URL.
 * @param string $label Visible menu label.
 * @return string
 */
function proenem_resolve_primary_navigation_url( $url, $label ) {
	$url = trim( (string) $url );

	if ( '' !== $url && '#' !== $url ) {
		return $url;
	}

	$destinations = array(
		'aprovados'          => home_url( '/#aprovados' ),
		'comece-gratis'      => proenem_get_home_cta_destination( 'signup' ),
		'comecar-gratis'     => proenem_get_home_cta_destination( 'signup' ),
		'criar-conta-gratis' => proenem_get_home_cta_destination( 'signup' ),
		'entrar'             => proenem_get_home_cta_destination( 'study' ),
		'faq'                => home_url( '/#faq' ),
		'planos'             => proenem_get_home_cta_destination( 'plans' ),
		'questoes'           => proenem_get_home_cta_destination( 'questions' ),
	);
	$label_slug   = sanitize_title( (string) $label );
	$destination  = $destinations[ $label_slug ] ?? home_url( '/' );

	/**
	 * Filter the fallback for an invalid persisted primary menu URL.
	 *
	 * @param string $destination Resolved destination.
	 * @param string $label       Visible menu label.
	 */
	return (string) apply_filters( 'proenem_primary_navigation_fallback_url', $destination, $label );
}

/**
 * Upgrade a legacy Elementor home link to its canonical destination.
 *
 * @param array|string $link   Elementor link settings or a URL string.
 * @param string       $intent Conversion intent.
 * @return array|string
 */
function proenem_upgrade_home_cta_link( $link, $intent ) {
	$canonical_url               = proenem_get_home_cta_destination( $intent );
	$is_legacy_advanced_checkout = static function ( $url ) use ( $intent ) {
		return 'advanced' === $intent && false !== strpos( $url, 'pay.hotmart.com/X99453521F' );
	};
	$is_method_pro_checkout      = static function ( $url ) use ( $intent ) {
		return 'method_pro' === $intent && false !== strpos( $url, 'pay.hotmart.com/W106752534O' );
	};

	if ( is_array( $link ) ) {
		$current_url = trim( (string) ( $link['url'] ?? '' ) );

		if ( '' === $current_url || '#planos' === $current_url || '#' === $current_url || $is_legacy_advanced_checkout( $current_url ) || $is_method_pro_checkout( $current_url ) ) {
			$link['url'] = $canonical_url;
		}

		return $link;
	}

	$current_url = trim( (string) $link );

	return ( '' === $current_url || '#planos' === $current_url || '#' === $current_url || $is_legacy_advanced_checkout( $current_url ) || $is_method_pro_checkout( $current_url ) ) ? $canonical_url : $current_url;
}

/**
 * Render the temporary mobile persistent conversion action.
 *
 * @param array{label?:string,url?:string,threshold?:int} $action Action settings.
 * @return void
 */
function proenem_render_mobile_persistent_action( $action = array() ) {
	$action = wp_parse_args(
		$action,
		array(
			'label'     => __( 'Criar conta grátis', 'proenem-wordpress-theme' ),
			'threshold' => 600,
			'url'       => proenem_get_home_cta_destination( 'signup' ),
		)
	);

	if ( empty( $action['label'] ) || empty( $action['url'] ) ) {
		return;
	}
	?>
	<aside
		class="pro-mobile-persistent-action"
		data-pro-mobile-persistent-action
		data-scroll-threshold="<?php echo esc_attr( max( 0, absint( $action['threshold'] ) ) ); ?>"
		hidden
		aria-label="<?php esc_attr_e( 'Próximo passo', 'proenem-wordpress-theme' ); ?>"
	>
		<a class="pen-button pen-button--primary pen-button--md" href="<?php echo esc_url( $action['url'] ); ?>">
			<?php echo esc_html( $action['label'] ); ?>
			<span class="pen-button__arrow" aria-hidden="true">-&gt;</span>
		</a>
	</aside>
	<?php
}

/**
 * Render a navbar item submenu.
 *
 * @param array<string,mixed> $navigation_item Navigation item data.
 * @param string              $submenu_id      Submenu element ID.
 * @return void
 */
function proenem_render_site_navbar_submenu( $navigation_item, $submenu_id = '' ) {
	$children = $navigation_item['children'] ?? array();

	if ( empty( $children ) || ! is_array( $children ) ) {
		return;
	}
	?>
	<ul class="pen-navbar__submenu"<?php echo $submenu_id ? ' id="' . esc_attr( $submenu_id ) . '"' : ''; ?>>
		<?php foreach ( $children as $child_item ) : ?>
			<?php
			$child_item_rel = $child_item['rel'] ?? '';

			if ( '_blank' === ( $child_item['target'] ?? '' ) && empty( $child_item_rel ) ) {
				$child_item_rel = 'noopener';
			}
			?>
			<li class="pen-navbar__submenu-item">
				<a
					class="pen-navbar__submenu-link<?php echo ! empty( $child_item['active'] ) ? ' pen-navbar__submenu-link--active' : ''; ?>"
					href="<?php echo esc_url( $child_item['url'] ); ?>"
					<?php echo ! empty( $child_item['target'] ) ? 'target="' . esc_attr( $child_item['target'] ) . '"' : ''; ?>
					<?php echo ! empty( $child_item_rel ) ? 'rel="' . esc_attr( $child_item_rel ) . '"' : ''; ?>
					<?php echo ! empty( $child_item['active'] ) ? 'aria-current="page"' : ''; ?>
				>
					<?php echo esc_html( $child_item['label'] ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
}

/**
 * Render a mobile disclosure button for a navbar submenu.
 *
 * @param array<string,mixed> $navigation_item Navigation item data.
 * @param string              $submenu_id      Submenu element ID.
 * @return void
 */
function proenem_render_site_navbar_submenu_toggle( $navigation_item, $submenu_id ) {
	if ( empty( $navigation_item['children'] ) || ! $submenu_id ) {
		return;
	}

	$navigation_label = (string) ( $navigation_item['label'] ?? '' );
	$toggle_label     = sprintf(
		/* translators: %s: Navigation item label. */
		__( 'Alternar submenu de %s', 'proenem-wordpress-theme' ),
		$navigation_label
	);
	?>
	<button class="pro-home-navbar-submenu-toggle" type="button" aria-controls="<?php echo esc_attr( $submenu_id ); ?>" aria-expanded="false">
		<span class="screen-reader-text"><?php echo esc_html( $toggle_label ); ?></span>
		<span aria-hidden="true">⌄</span>
	</button>
	<?php
}

/**
 * Render the shared Proenem navbar markup.
 *
 * @param array{aria_label?:string,context?:string,logo_only?:bool,menu_id?:int} $args Navbar args.
 * @return void
 */
function proenem_render_site_navbar( $args = array() ) {
	$defaults   = array(
		'aria_label' => __( 'Navegação principal', 'proenem-wordpress-theme' ),
		'context'    => 'site',
		'logo_only'  => false,
		'menu_id'    => 0,
	);
	$args       = wp_parse_args( $args, $defaults );
	$navigation = proenem_get_primary_navigation_items( $args['context'], absint( $args['menu_id'] ) );
	$menu_id    = wp_unique_id( 'proenem-navbar-menu-' . sanitize_html_class( $args['context'] ) . '-' );
	$classes    = 'pen-navbar pro-site-navbar';

	if ( $args['logo_only'] ) {
		$classes .= ' pro-site-navbar--logo-only';
	}
	?>
	<nav class="<?php echo esc_attr( $classes ); ?>" aria-label="<?php echo esc_attr( $args['aria_label'] ); ?>" data-pro-home-navbar>
		<a class="pen-brand-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<img src="<?php echo esc_url( PROENEM_THEME_URI . '/assets/images/brand/logo_proenem.svg' ); ?>" alt="<?php esc_attr_e( 'Proenem', 'proenem-wordpress-theme' ); ?>" width="152" height="43">
		</a>
		<?php if ( $args['logo_only'] ) : ?>
			</nav>
			<?php
			return;
		endif;
		?>
		<button class="pro-home-navbar-toggle" type="button" aria-controls="<?php echo esc_attr( $menu_id ); ?>" aria-expanded="false">
			<span class="screen-reader-text"><?php esc_html_e( 'Abrir menu', 'proenem-wordpress-theme' ); ?></span>
			<span aria-hidden="true"></span>
			<span aria-hidden="true"></span>
			<span aria-hidden="true"></span>
		</button>
		<div id="<?php echo esc_attr( $menu_id ); ?>" class="pro-home-navbar-menu">
			<div class="pen-navbar__links">
				<?php foreach ( $navigation['links'] as $navigation_link ) : ?>
					<?php
					$navigation_link_class = 'pen-navbar__link';

					if ( ! empty( $navigation_link['active'] ) ) {
						$navigation_link_class .= ' pen-navbar__link--active';
					}

					$navigation_link_rel = $navigation_link['rel'] ?? '';
					$has_submenu         = ! empty( $navigation_link['children'] );
					$submenu_id          = $has_submenu ? wp_unique_id( 'proenem-navbar-submenu-' ) : '';

					if ( '_blank' === ( $navigation_link['target'] ?? '' ) && empty( $navigation_link_rel ) ) {
						$navigation_link_rel = 'noopener';
					}
					?>
					<div class="pen-navbar__item<?php echo $has_submenu ? ' pen-navbar__item--has-submenu' : ''; ?>">
						<a
							class="<?php echo esc_attr( $navigation_link_class ); ?>"
							href="<?php echo esc_url( $navigation_link['url'] ); ?>"
							<?php echo ! empty( $navigation_link['target'] ) ? 'target="' . esc_attr( $navigation_link['target'] ) . '"' : ''; ?>
							<?php echo ! empty( $navigation_link_rel ) ? 'rel="' . esc_attr( $navigation_link_rel ) . '"' : ''; ?>
							<?php echo ! empty( $navigation_link['active'] ) ? 'aria-current="page"' : ''; ?>
							<?php echo $has_submenu ? 'aria-haspopup="true"' : ''; ?>
						>
							<span class="pen-navbar__label" data-label="<?php echo esc_attr( $navigation_link['label'] ); ?>">
								<span class="pen-navbar__label-text"><?php echo esc_html( $navigation_link['label'] ); ?></span>
							</span>
						</a>
						<?php proenem_render_site_navbar_submenu_toggle( $navigation_link, $submenu_id ); ?>
						<?php proenem_render_site_navbar_submenu( $navigation_link, $submenu_id ); ?>
					</div>
				<?php endforeach; ?>
			</div>
			<?php if ( ! empty( $navigation['actions'] ) ) : ?>
				<div class="pen-navbar__actions">
					<?php foreach ( $navigation['actions'] as $navigation_action ) : ?>
						<?php
						$navigation_action_variant = in_array( $navigation_action['variant'] ?? '', array( 'primary', 'secondary' ), true )
							? $navigation_action['variant']
							: 'primary';
						$navigation_action_class   = 'pen-navbar__action pen-navbar__action--' . $navigation_action_variant;
						$navigation_action_class  .= ! empty( $navigation_action['classes'] )
							? ' ' . implode( ' ', $navigation_action['classes'] )
							: '';
						$navigation_action_rel     = $navigation_action['rel'] ?? '';
						$has_submenu               = ! empty( $navigation_action['children'] );
						$submenu_id                = $has_submenu ? wp_unique_id( 'proenem-navbar-submenu-' ) : '';

						if ( '_blank' === ( $navigation_action['target'] ?? '' ) && empty( $navigation_action_rel ) ) {
							$navigation_action_rel = 'noopener';
						}
						?>
						<div class="pen-navbar__item<?php echo $has_submenu ? ' pen-navbar__item--has-submenu' : ''; ?>">
							<a
								class="<?php echo esc_attr( $navigation_action_class ); ?>"
								href="<?php echo esc_url( $navigation_action['url'] ); ?>"
								<?php echo ! empty( $navigation_action['target'] ) ? 'target="' . esc_attr( $navigation_action['target'] ) . '"' : ''; ?>
								<?php echo ! empty( $navigation_action_rel ) ? 'rel="' . esc_attr( $navigation_action_rel ) . '"' : ''; ?>
								<?php echo $has_submenu ? 'aria-haspopup="true"' : ''; ?>
							>
								<span class="pen-navbar__label" data-label="<?php echo esc_attr( $navigation_action['label'] ); ?>">
									<span class="pen-navbar__label-text"><?php echo esc_html( $navigation_action['label'] ); ?></span>
								</span>
							</a>
							<?php proenem_render_site_navbar_submenu_toggle( $navigation_action, $submenu_id ); ?>
							<?php proenem_render_site_navbar_submenu( $navigation_action, $submenu_id ); ?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</nav>
	<?php
}
