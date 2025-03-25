<?php
    // Define o ID �nico do bot�o de r�dio ou usa um valor padr�o
    $id = isset($variaveisComponents['id']) 
        ? $variaveisComponents['id'] 
        : 'default-radio';

    // Define o nome do grupo de bot�es de r�dio ou usa 'radio-group' como padr�o
    $name = isset($variaveisComponents['name']) 
        ? $variaveisComponents['name'] 
        : 'radio-group';

    // Verifica se o bot�o deve estar marcado inicialmente
    $checked = isset($variaveisComponents['checked']) && $variaveisComponents['checked'] 
        ? 'checked' 
        : '';

    // Define o texto do r�tulo associado ao bot�o de r�dio
    $label = isset($variaveisComponents['label']) 
        ? $variaveisComponents['label'] 
        : 'Default radio';

    // Define as classes CSS para o container do bot�o de r�dio ou usa um valor padr�o
    $containerClass = isset($variaveisComponents['containerClass']) 
        ? $variaveisComponents['containerClass'] 
        : 'radio-container-075RN';

    // Define as classes CSS para o elemento `<input>` do bot�o de r�dio
    $inputClass = isset($variaveisComponents['inputClass']) 
        ? $variaveisComponents['inputClass'] 
        : 'radio-input-075RN';

    // Define as classes CSS para o r�tulo associado ao bot�o de r�dio
    $labelClass = isset($variaveisComponents['labelClass']) 
        ? $variaveisComponents['labelClass'] 
        : 'radio-label-075RN';

    // Define o valor do bot�o de r�dio ou usa um valor vazio por padr�o
    $value = isset($variaveisComponents['value']) 
        ? $variaveisComponents['value'] 
        : '';

    // Verifica se o bot�o de r�dio deve estar desabilitado
    $disabled = isset($variaveisComponents['disabled']) && $variaveisComponents['disabled'] 
        ? 'disabled' 
        : false;

    // Verifica se o bot�o de r�dio � obrigat�rio
    $required = isset($variaveisComponents['required']) && $variaveisComponents['required'] 
        ? 'required' 
        : false;

    // Inicializa uma string para armazenar atributos personalizados
    $customAttributes = '';
    if (isset($variaveisComponents['attributes']) && is_array($variaveisComponents['attributes'])) {
        foreach ($variaveisComponents['attributes'] as $key => $value) {
            // Escapa caracteres especiais para evitar vulnerabilidades (exemplo: XSS)
            $customAttributes .= htmlspecialchars($key) . '="' . htmlspecialchars($value) . '" ';
        }
    }

    // Inclui o arquivos CSS
    includeOnceAsset('css', '/libs/renderComponents/src/components/inputs/radios/bordered/bordered.css');
    (!$required) ?: includeOnceAsset('css', '/libs/renderComponents/src/components/icons/asterisk/asterisk.css');

    // Inclui o arquivos JavaScript
    includeOnceAsset('js', '/libs/renderComponents/src/components/inputs/radios/bordered/bordered.js');
?>

<div class="<?= $containerClass ?> <?= (!$disabled) ?: "disabled" ?>" for="<?= $id ?>">
    <input
        id="<?= $id ?>"
        type="radio"
        value="<?= $value ?>"
        name="<?= $name ?>"
        class="<?= $inputClass ?>"
        <?= $checked ?>
        <?= $disabled ?>
        <?= $required ?>
        <?= $customAttributes ?>
    >

    <label for="<?= $id ?>" class="<?= $labelClass ?> <?= (!$disabled) ?: "disabled-label" ?>">
        <?= $label ?>
        <?php if ($required): ?>
            <i class="icon asterisk"></i>
        <?php endif; ?>
    </label>
</div>

<?php
    // Limpar vari�veis ap�s uso
    unset($id, $name, $checked, $label, $containerClass, $inputClass, $labelClass, $value, $disabled, $required, $customAttributes);
?>