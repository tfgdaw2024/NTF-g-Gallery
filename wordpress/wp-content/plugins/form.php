<?php
/**
 * Plugin Name: Mi Formulario Plugin
 * Plugin URI: 
 * Description: Un plugin de formulario básico para WordPress.
 * Version: 1.0
 * Author: Martin Mejias
 * Author URI: 
 */
function mf_incluir_bootstrap()
{
    wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
    wp_enqueue_script('bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js', array('jquery'), null, true);
}

add_action('wp_enqueue_scripts', 'mf_incluir_bootstrap');
function mf_crear_formulario()
{
    $opciones = get_option('mf_opciones');
    $contenido = '<form action="#" method="post" class="container mt-5">';
    if (!empty($opciones['nombre'])) {
        $contenido .= '<div class="form-group">';
        $contenido .= '<label for="nombre">Nombre:</label>';
        $contenido .= '<input type="text" class="form-control" id="nombre" name="nombre" required>';
        $contenido .= '</div>';
    }

    if (!empty($opciones['apellido'])) {
        $contenido .= '<div class="form-group">';
        $contenido .= '<label for="apellido">Apellido:</label>';
        $contenido .= '<input type="text" class="form-control" id="apellido" name="apellido" required>';
        $contenido .= '</div>';
    }

    if (!empty($opciones['telefono'])) {
        $contenido .= '<div class="form-group">';
        $contenido .= '<label for="telefono">Teléfono:</label>';
        $contenido .= '<input type="tel" class="form-control" id="telefono" name="telefono" required>';
        $contenido .= '</div>';
    }

    if (!empty($opciones['email'])) {
        $contenido .= '<div class="form-group">';
        $contenido .= '<label for="email">Correo Electrónico:</label>';
        $contenido .= '<input type="email" class="form-control" id="email" name="email" required>';
        $contenido .= '</div>';
    }
    if (!empty($opciones['direccion'])) {
        $contenido .= '<div class="form-group">';
        $contenido .= '<label for="direccion">Dirección:</label>';
        $contenido .= '<input type="text" class="form-control" id="direccion" name="direccion" required>';
        $contenido .= '</div>';
    }
    if (!empty($opciones['comentarios'])) {
        $contenido .= '<div class="form-group">';
        $contenido .= '<label for="comentarios">Comentarios:</label>';
        $contenido .= '<textarea class="form-control" id="comentarios" name="comentarios" required></textarea>';
        $contenido .= '</div>';
    }
    $contenido .= '<div class="text-center">';
    $contenido .= '<input type="submit" class="btn btn-primary" value="Enviar">';
    $contenido .= '</div>';

    $contenido .= '</form>';

    return $contenido;
}


add_shortcode('formulario_basico', 'mf_crear_formulario');

function mf_registrar_pagina_opciones()
{
    add_options_page('Configuración de Mi Formulario Plugin', 'Mi Formulario Plugin', 'manage_options', 'mi-formulario-plugin', 'mf_pagina_opciones');
}
add_action('admin_menu', 'mf_registrar_pagina_opciones');

function mf_pagina_opciones()
{
    ?>
    <div class="wrap">
        <h2>Configuración de Mi Formulario Plugin</h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('mf_opciones_grupo');
            do_settings_sections('mi-formulario-plugin');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function mf_registrar_configuracion()
{
    register_setting('mf_opciones_grupo', 'mf_opciones');
    add_settings_section('mf_configuracion_seccion', 'Selección de Campos del Formulario', null, 'mi-formulario-plugin');

    add_settings_field('nombre', 'Nombre', 'mf_campo_checkbox', 'mi-formulario-plugin', 'mf_configuracion_seccion', array('id' => 'nombre'));
    add_settings_field('apellido', 'Apellido', 'mf_campo_checkbox', 'mi-formulario-plugin', 'mf_configuracion_seccion', array('id' => 'apellido'));
    add_settings_field('telefono', 'Teléfono', 'mf_campo_checkbox', 'mi-formulario-plugin', 'mf_configuracion_seccion', array('id' => 'telefono'));
    add_settings_field('email', 'Correo Electrónico', 'mf_campo_checkbox', 'mi-formulario-plugin', 'mf_configuracion_seccion', array('id' => 'email'));
    add_settings_field('direccion', 'Dirección', 'mf_campo_checkbox', 'mi-formulario-plugin', 'mf_configuracion_seccion', array('id' => 'direccion'));
    add_settings_field('comentarios', 'Comentarios', 'mf_campo_checkbox', 'mi-formulario-plugin', 'mf_configuracion_seccion', array('id' => 'comentarios'));
    // Añade aquí más campos según sea necesario
}
add_action('admin_init', 'mf_registrar_configuracion');

function mf_campo_checkbox($args) {
    $opciones = get_option('mf_opciones');
    if (!is_array($opciones)) {
        $opciones = [];
    }
    $id = $args['id'];
    $checked = isset($opciones[$id]) ? $opciones[$id] : '';
    echo '<input type="checkbox" id="'. $id .'" name="mf_opciones['. $id .']" '. checked(1, $checked, false) .' value="1">';
}