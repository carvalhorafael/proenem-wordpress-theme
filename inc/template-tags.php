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
		'student_school_1.png',
		'student_school_2.png',
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
 * Get primary navigation data for the design-system navbar.
 *
 * @return array{links:array<int,array<string,mixed>>,actions:array<int,array<string,mixed>>}
 */
function proenem_get_primary_navigation_items() {
	$fallback = array(
		'links'   => array(
			array(
				'url'    => home_url( '/#metodo' ),
				'label'  => __( 'Método PRO', 'proenem-wordpress-theme' ),
				'active' => false,
			),
			array(
				'url'    => home_url( '/#planos' ),
				'label'  => __( 'Planos', 'proenem-wordpress-theme' ),
				'active' => false,
			),
			array(
				'url'    => home_url( '/blog/' ),
				'label'  => __( 'Blog', 'proenem-wordpress-theme' ),
				'active' => is_home() || is_singular( 'post' ) || is_archive(),
			),
		),
		'actions' => array(
			array(
				'url'     => home_url( '/login/' ),
				'label'   => __( 'Login', 'proenem-wordpress-theme' ),
				'variant' => 'secondary',
			),
			array(
				'url'     => home_url( '/#planos' ),
				'label'   => __( 'Comece grátis', 'proenem-wordpress-theme' ),
				'variant' => 'primary',
			),
		),
	);

	$locations = get_nav_menu_locations();

	if ( empty( $locations['primary'] ) ) {
		return $fallback;
	}

	$menu_items = wp_get_nav_menu_items( $locations['primary'] );

	if ( empty( $menu_items ) || is_wp_error( $menu_items ) ) {
		return $fallback;
	}

	$navigation = array(
		'links'   => array(),
		'actions' => array(),
	);

	foreach ( $menu_items as $menu_item ) {
		$classes = array_filter( (array) $menu_item->classes );
		$item    = array(
			'url'     => $menu_item->url,
			'label'   => $menu_item->title,
			'target'  => $menu_item->target,
			'rel'     => $menu_item->xfn,
			'classes' => array_map( 'sanitize_html_class', $classes ),
			'active'  => in_array( 'current-menu-item', $classes, true ) || in_array( 'current-menu-ancestor', $classes, true ),
		);

		if ( in_array( 'pen-navbar-action', $classes, true ) ) {
			$item['variant']         = in_array( 'pen-navbar-action-secondary', $classes, true ) ? 'secondary' : 'primary';
			$navigation['actions'][] = $item;
			continue;
		}

		$navigation['links'][] = $item;
	}

	if ( empty( $navigation['links'] ) ) {
		$navigation['links'] = $fallback['links'];
	}

	if ( empty( $navigation['actions'] ) ) {
		$navigation['actions'] = $fallback['actions'];
	}

	return $navigation;
}
