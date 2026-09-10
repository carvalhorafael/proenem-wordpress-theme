<?php
/**
 * Front page template.
 *
 * A hierarquia do WordPress consulta este arquivo antes do modelo escolhido no
 * editor, entao um front-page.php incondicional tornava o seletor de modelo
 * inerte na home: trocar o modelo na pagina nao tinha efeito nenhum. As
 * variantes de conversao existem para serem trocadas sem deploy, e isso exige
 * honrar a escolha.
 *
 * Sem modelo escolhido, ou com a home configurada como ultimos posts, o slug
 * vem vazio e a home de sempre continua sendo servida.
 *
 * @package Proenem
 */

$proenem_front_template = get_page_template_slug( get_queried_object_id() );

if ( '' !== (string) $proenem_front_template && locate_template( $proenem_front_template ) ) {
	locate_template( $proenem_front_template, true, false );
	return;
}

locate_template( 'page-templates/home.php', true, false );
