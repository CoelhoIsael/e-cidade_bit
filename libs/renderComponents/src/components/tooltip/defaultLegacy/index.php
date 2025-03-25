<?php
    // Atribui um valor � vari�vel $id ou define um valor padr�o 'tooltip-default-F071PM'
    $id =  isset($variaveisComponents['id']) 
        ? $variaveisComponents['id'] 
        : 'tooltip-default-F071PM';

    // Atribui o valor de 'body' � vari�vel $body ou deixa como string vazia se n�o for definido
    $body = isset($variaveisComponents['body']) 
        ? $variaveisComponents['body'] 
        : '';

    // Atribui o valor de 'size' � vari�vel $size ou define 'auto' como padr�o
    $size =  isset($variaveisComponents['size']) 
        ? $variaveisComponents['size'] 
        : 'auto';

    includeOnceAsset('css', '/libs/renderComponents/src/components/tooltip/defaultLegacy/defaultLegacy.css');
?>

<div id="<?= $id ?>" class="tooltip-default-F071PM">
    <?= $body ?>
    <span class="tooltip-default-F071PM-text tooltip-default-F071PM-<?= $size ?>" style="<?= ($size != 'auto')?: 'white-space: nowrap;'; ?>"></span>
</div>

<?php
    // Limpar vari�veis ap�s uso
    unset($id, $body, $size);
?>