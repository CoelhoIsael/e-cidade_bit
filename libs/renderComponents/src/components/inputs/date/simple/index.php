<?php
    // Define o ID do campo de entrada ou usa 'default-datepicker' como padr�o
    $id = isset($variaveisComponents['id']) 
        ? $variaveisComponents['id'] 
        : 'default-datepicker';

    // Define as classes CSS adicionais para o campo de entrada ou usa uma classe padr�o
    $inputClass = isset($variaveisComponents['inputClass']) 
        ? $variaveisComponents['inputClass'] 
        : 'input-V1R77' . 
            ' ' . 
            (isset($variaveisComponents['size']) 
                ? $variaveisComponents['size'] . '-V1R77' 
                : 'md-V1R77');

    // Define o texto da label ou deixa em branco se n�o for fornecido
    $label = isset($variaveisComponents['label']) 
        ? $variaveisComponents['label'] 
        : '';

    // Define o atributo 'name' do campo ou usa 'date' como padr�o
    $name = isset($variaveisComponents['name']) 
        ? $variaveisComponents['name'] 
        : 'date';

    // Define o valor inicial do campo ou deixa em branco
    $value = isset($variaveisComponents['value']) 
        ? $variaveisComponents['value'] 
        : '';

    // Define se o bot�o estar� desabilitado ou n�o
    $disabled = (isset($variaveisComponents['disabled']) && $variaveisComponents['disabled']) 
        ? 'disabled ' 
        : '';

    // Define se o campo � obrigat�rio (required) ou n�o
    $required = isset($variaveisComponents['required']) && $variaveisComponents['required'] 
        ? 'required' 
        : false;

    // Define o tamanho do campo com base no valor passado ou usa 'datepicker-container-md' como padr�o
    $size = isset($variaveisComponents['size']) 
        ? $variaveisComponents['size'] . '-V1R77' 
        : 'md-V1R77';

    // Inicializa uma string para armazenar os atributos personalizados
    $customAttributes = '';
    // Verifica se existem atributos personalizados no array e itera sobre eles
    if (isset($variaveisComponents['attributes']) && is_array($variaveisComponents['attributes'])) {
        foreach ($variaveisComponents['attributes'] as $key => $value) {
            // Escapa os nomes e valores dos atributos para evitar problemas de seguran�a (exemplo: XSS)
            $customAttributes .= htmlspecialchars($key) . '="' . htmlspecialchars($value) . '" ';
        }
    }

    // Inclui o arquivos CSS
    includeOnceAsset('css', '/libs/renderComponents/src/components/inputs/date/simple/simple.css');
    (!$required) ?: includeOnceAsset('css', '/libs/renderComponents/src/components/icons/asterisk/asterisk.css');

    // Inclui o arquivos JavaScript
    includeOnceAsset('js', '/libs/renderComponents/src/components/inputs/date/simple/simple.js');
?>

<div class="datepicker-container-V1R77">
    <label for="<?= $id ?>" class="label-V1R77">
        <?= $label ?>
        <?php if ($required): ?>
            <i class="icon asterisk"></i>
        <?php endif; ?>
    </label>

    <input
        id="<?= $id ?>"
        type="date"
        name="<?= $name ?>"
        value="<?= $value ?>"
        class="<?= $inputClass ?> <?= $disabled ?>"
        <?= $disabled ?>
        <?= $required ?>
        <?= $customAttributes ?>
    >
</div>

<?php
    // Limpar vari�veis ap�s uso
    unset($id, $inputClass, $label, $name, $value, $required, $size, $customAttributes);
?>