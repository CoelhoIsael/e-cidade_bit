<?php
    // Atribui valores a vari�veis ou usa valores padr�o
    $id = isset($variaveisComponents['id']) ? $variaveisComponents['id'] : '';

    // Define a classe CSS com base nos valores de $variaveisComponents
    $class = isset($variaveisComponents['class']) 
        ? $variaveisComponents['class'] 
        : 'btn solid ' . (isset($variaveisComponents['design']) ? $variaveisComponents['design'] : '');

    // Atribui uma mensagem ou deixa em branco caso n�o exista a chave 'message'
    $message = isset($variaveisComponents['message']) 
        ? $variaveisComponents['message'] 
        : '';

    // Inclui o arquivos CSS
    includeOnceAsset('css', '/libs/renderComponents/src/components/toast/default/default.css');

    // Inclui o arquivos JavaScript
    includeOnceAsset('js', '/libs/renderComponents/src/components/toast/default/default.js');
?>

<div id="toast-container-98P6m"></div>

<?php
    // Limpar vari�veis ap�s uso
    unset($id, $class, $message);
?>