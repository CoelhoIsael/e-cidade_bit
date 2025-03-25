<?php
    // Define o valor de `$id` com base em `$variaveisComponents['id']`, se estiver definido, e aplica `htmlspecialchars` para evitar XSS. 
    // Caso contr�rio, define como 'customModal'.
    $id = isset($variaveisComponents['id']) 
        ? htmlspecialchars($variaveisComponents['id']) 
        : 'customModal';

    // Define o valor de `$class` com base em `$variaveisComponents['class']`, com prote��o contra XSS. 
    // Caso contr�rio, usa a classe padr�o 'simple-modal'.
    $class = isset($variaveisComponents['class']) 
        ? htmlspecialchars($variaveisComponents['class']) 
        : 'simple-modal';

    // Define `$idContent` com base em `$variaveisComponents['idContent']`, aplicando `htmlspecialchars` para seguran�a. 
    // Caso contr�rio, deixa vazio.
    $idContent = isset($variaveisComponents['idContent']) 
        ? htmlspecialchars($variaveisComponents['idContent']) 
        : '';

    // Define `$classContent` combinando uma classe padr�o 'simple-modal-content' com o valor de `$variaveisComponents['size']`, se estiver definido. 
    // Prote��o contra XSS � aplicada. Caso contr�rio, define o tamanho padr�o como 'md'.
    $classContent = isset($variaveisComponents['classContent']) 
        ? htmlspecialchars($variaveisComponents['classContent']) 
        : 'simple-modal-content ' . (isset($variaveisComponents['size']) 
            ? htmlspecialchars($variaveisComponents['size']) 
            : 'md');

    // Define `$classClose` com base em `$variaveisComponents['classClose']`, com prote��o contra XSS. 
    // Caso contr�rio, usa a classe padr�o 'close'.
    $classClose = isset($variaveisComponents['classClose']) 
        ? htmlspecialchars($variaveisComponents['classClose']) 
        : 'close';

    // Define `$idClose` com base em `$variaveisComponents['idClose']`, aplicando `htmlspecialchars` para seguran�a. 
    // Caso contr�rio, deixa vazio.
    $idClose = isset($variaveisComponents['idClose']) 
        ? htmlspecialchars($variaveisComponents['idClose']) 
        : '';

    // Define `$title` com base em `$variaveisComponents['title']`. 
    // N�o � aplicado `htmlspecialchars` aqui, o que pode ser uma escolha intencional, mas deve-se tomar cuidado com entradas n�o confi�veis.
    $title = isset($variaveisComponents['title']) 
        ? $variaveisComponents['title'] 
        : '';

    // Define uma string JavaScript para fechar o modal, passando o `$id` como argumento para a fun��o `closeModal`.
    $closeModalFunction = "closeModal('$id')";

    // Inclui o arquivos CSS
    includeOnceAsset('css', '/libs/renderComponents/src/components/modais/simpleModal/simpleModal.css');

    // Inclui o arquivos JavaScript
    includeOnceAsset('js', '/libs/renderComponents/src/components/modais/simpleModal/simpleModal.js');
    includeOnceAsset('js', '/libs/renderComponents/src/components/modais/modais.js');
?>

<div id="<?= $id ?>" class="<?= $class ?>">
    <div class="<?= $idContent ?> <?= $classContent ?>">
        <span class="<?= $classClose ?>" id="<?= $idClose ?>" onclick="<?= $closeModalFunction ?>">
            &times;
        </span>
        <?php if ($title) : ?>
            <div class="simple-modal-content-title">
                <h2><?= $title ?></h2>
            </div>
        <?php endif; ?>

        <div class="form-modal-body-render-components">
<?php
    unset($id, $class, $idContent, $classContent, $classClose, $idClose, $title, $closeModalFunction);
?>