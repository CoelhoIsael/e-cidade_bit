<?php
    // Atribui um valor � vari�vel $id ou define um valor padr�o 'tooltip-default'
    $id =  isset($variaveisComponents['id']) 
        ? $variaveisComponents['id'] 
        : 'tooltip-default';

    // Atribui o valor de 'body' � vari�vel $body ou deixa como string vazia se n�o for definido
    $body = isset($variaveisComponents['body']) 
        ? $variaveisComponents['body'] 
        : '';

    // Atribui o valor de 'size' � vari�vel $size ou define 'auto' como padr�o
    $size =  isset($variaveisComponents['size']) 
        ? $variaveisComponents['size'] 
        : 'auto';

    // Inclui o arquivos CSS
    includeOnceAsset('css', '/libs/renderComponents/src/components/tooltip/defaultRightLegacy/defaultRightLegacy.css');
?>

<div id="<?=$id?>" class="tooltip-09343">
    <?= $body ?> 
    <div id="<?=$id?>-body" class="tooltip-text-09343 tooltip-default-09343-<?= $size ?>" style="<?= ($size != 'auto')?: 'white-space: nowrap;'; ?>"></div>
</div>

<?php
    // Limpar vari�veis ap�s uso
    unset($id, $body, $size);
?>