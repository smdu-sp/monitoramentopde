<?php
/**
 * Template Name: Reportar problema
 */

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (in_array(null, $_POST)) {
        return;
    }

    global $wpdb;
    $wpdb->show_errors();

    $camposForm = [
    'id_indicador',
    'nome',
    'email',
    'mensagem'
    ];

    $sqlData = [];
    foreach ($camposForm as $key => $coluna) {
        $sqlData[$coluna] = $_POST[$coluna];
    }
    $sqlData["resolvido"] = 0;

    $wpdb->insert('mpde_problema_indicador', $sqlData);
    
    echo "<p id='resposta'>sucesso</p>";

}
