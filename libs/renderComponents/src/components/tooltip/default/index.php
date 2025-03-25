<?php
    /*
        |-------------|-----------|--------------|----------------------------------------------------------------------------------|
        | Par�metro:  | Tipo:     | Obrigat�rio: | Descri��o:                                                                       |
        |-------------|-----------|--------------|----------------------------------------------------------------------------------|
        | targetEl    | Elemento  | Sim          | Elemento de conte�do da dica de ferramenta, mostrado/ocultado pelo gatilho.      |
        | triggerEl   | Elemento  | Sim          | Elemento que aciona a dica de ferramenta ao clicar ou passar o mouse.            |
        | body        | String    | Sim          | Define a mensagem que ser� exibida no corpo do tooltip. Aceita conte�do em HTML. |
        |-------------|-----------|--------------|----------------------------------------------------------------------------------|
    */
    $targetEl = isset($variaveisComponents['targetEl']) 
        ? $variaveisComponents['targetEl'] 
        : null;

    $triggerEl = isset($variaveisComponents['triggerEl']) 
        ? $variaveisComponents['triggerEl'] 
        : null;

    $body = isset($variaveisComponents['body']) 
        ? $variaveisComponents['body'] 
        : null;

    /*
        |-------------|----------|-------------|---------------------------------------------------------------------------------|
        | Par�metro:  | Tipo:    | Obrigat�rio:| Descri��o:                                                                      |
        |-------------|----------|-------------|---------------------------------------------------------------------------------|
        | options     | Array    | N�o         | Define posicionamento, tipo de gatilho, deslocamentos e outras configura��es.   |
        |-------------|----------|-------------|---------------------------------------------------------------------------------|
        |/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\|
        |-------------|----------|-----------------------------------------------------------------------------------------------|
        | Op��es:     | Tipo:    | Descri��o:                                                                                    |
        |-------------|----------|-----------------------------------------------------------------------------------------------|
        | placement   | String   | Define a posi��o da dica de ferramenta em rela��o ao gatilho.                                 |
        |             |          | Op��es: 'top', 'right', 'bottom', 'left'.                                                     |
        |-------------|----------|-----------------------------------------------------------------------------------------------|
        | triggerType | String   | Define o tipo de evento que aciona a dica de ferramenta.                                      |
        |             |          | Op��es: 'hover', 'click', 'none'.                                                             |
        |-------------|----------|-----------------------------------------------------------------------------------------------|
        | onHide      | Fun��o   | Fun��o de callback chamada quando a dica de ferramenta � ocultada.                            |
        | onShow      | Fun��o   | Fun��o de callback chamada quando a dica de ferramenta � exibida.                             |
        | onToggle    | Fun��o   | Fun��o de callback chamada ao alternar a visibilidade da dica de ferramenta.                  |
        |-------------|----------|-----------------------------------------------------------------------------------------------|
    */
    $options = isset($variaveisComponents['options']) 
        ? json_encode($variaveisComponents['options'])
        : '{placement: "top", triggerType: "hover"}';

    $placement = $variaveisComponents['options']['placement'] ?? 'top';

    /*
        |-------------|----------|-------------|---------------------------------------------------------------------------------|
        | Par�metro:  | Tipo:    | Obrigat�rio:| Descri��o:                                                                      |
        |-------------|----------|-------------|---------------------------------------------------------------------------------|
        | size        | String   | N�o         | Define a largura maxima do tooltip.                                             |
        |-------------|----------|-------------|---------------------------------------------------------------------------------|
    */
    $size = isset($variaveisComponents['size']) 
        ? $variaveisComponents['size'] 
        : 'auto';

    /*
        |-----------------|----------|-------------|---------------------------------------------------------------------------------|
        | Par�metro:      | Tipo:    | Obrigat�rio:| Descri��o:                                                                      |
        |-----------------|----------|-------------|---------------------------------------------------------------------------------|
        | backgroundColor | String   | N�o         | Define a cor de fundo do tooltip.                                               |
        |-----------------|----------|-------------|---------------------------------------------------------------------------------|
    */
    $backgroundColor = isset($variaveisComponents['backgroundColor']) 
        ? $variaveisComponents['backgroundColor'] 
        : '#dbdbdb';

    /*
        |-------------|----------|-------------|---------------------------------------------------------------------------------|
        | Par�metro:  | Tipo:    | Obrigat�rio:| Descri��o:                                                                      |
        |-------------|----------|-------------|---------------------------------------------------------------------------------|
        | fontColor   | String   | N�o         | Define a cor da fonte do tooltip maxima do tooltip.                             |
        |-------------|----------|-------------|---------------------------------------------------------------------------------|
    */
    $fontColor = isset($variaveisComponents['fontColor']) 
        ? $variaveisComponents['fontColor'] 
        : '#333';

    // Inclui o arquivos CSS
    includeOnceAsset('css', '/libs/renderComponents/src/components/tooltip/default/default.css');
?>

<style>
    .tooltip-default-K835A {
        color: <?= $fontColor ?>;
        background-color: <?= $backgroundColor ?>;
    }

    .bottom-K835A::after {
        border-color: transparent transparent <?= $backgroundColor ?> transparent;
    }

    .right-K835A::after {
        border-color: transparent <?= $backgroundColor ?> transparent transparent;
    }

    .left-K835A::after {
        border-color: transparent transparent transparent <?= $backgroundColor ?>;
    }

    .top-K835A::after {
        border-color: <?= $backgroundColor ?> transparent transparent transparent;
    }
</style>
    
<span id="<?= $targetEl ?>" class="tooltip-default-K835A <?= $placement ?>-K835A <?= $size ?>" style="display: none; <?= ($size != 'auto')?: 'white-space: nowrap;'; ?>">
    <?= $body ?>
</span>

<script type="module">
    import { TooltipRC } from '<?= $this->baseUrl ?>/libs/renderComponents/src/components/tooltip/default/TooltipRC.js';
    
    document.addEventListener("DOMContentLoaded", () => {
        const triggerElement = document.getElementById("<?= $triggerEl ?>");
        const tooltipElement = document.getElementById("<?= $targetEl ?>");
                
        /*
            |--------------------------|-------------------------------------------------------------------------------------------|
            | M�todos:                 | Descri��o:                                                                                |
            |--------------------------|-------------------------------------------------------------------------------------------|
            | show()                   | Exibe o conte�do da dica de ferramenta.                                                   |
            |--------------------------|-------------------------------------------------------------------------------------------|
            | hide()                   | Oculta o conte�do da dica de ferramenta.                                                  |
            |--------------------------|-------------------------------------------------------------------------------------------|
            | toggle()                 | Alterna a visibilidade do conte�do da dica de ferramenta.                                 |
            |--------------------------|-------------------------------------------------------------------------------------------|
            | updateOnShow(callback)   | Define uma fun��o de callback personalizada quando a dica de ferramenta for exibida.      |
            |--------------------------|-------------------------------------------------------------------------------------------|
            | updateOnHide(callback)   | Define uma fun��o de callback personalizada quando a dica de ferramenta estiver oculta.   |
            |--------------------------|-------------------------------------------------------------------------------------------|
            | updateOnToggle(callback) | Define uma fun��o de callback personalizada quando a visibilidade for alternada.          |
            |--------------------------|-------------------------------------------------------------------------------------------|
        */
        new TooltipRC(tooltipElement, triggerElement, <?= $options ?>);
    });
</script>

<?php
    // Limpar vari�veis ap�s uso
    unset($id, $body, $size);
?>