<?php
    // Atribui um valor � vari�vel $id ou usa 'default-textfield' como valor padr�o, caso n�o esteja definido
    $id = isset($variaveisComponents['id']) 
        ? $variaveisComponents['id'] 
        : 'default-textfield';

    // Atribui um valor � vari�vel $placeholder ou usa uma string vazia como valor padr�o
    $placeholder = isset($variaveisComponents['placeholder']) 
        ? $variaveisComponents['placeholder'] 
        : '';

    // Atribui um valor � vari�vel $inputClass ou usa 'textfield-input' como valor padr�o
    $inputClass = isset($variaveisComponents['inputClass']) 
        ? $variaveisComponents['inputClass'] 
        : 'textfield-input';

    // Atribui um valor � vari�vel $label ou usa uma string vazia como valor padr�o
    $label = isset($variaveisComponents['label']) 
        ? $variaveisComponents['label'] 
        : '';

    // Atribui um valor � vari�vel $name ou usa uma string vazia como valor padr�o
    $name = isset($variaveisComponents['name']) 
        ? $variaveisComponents['name'] 
        : '';

    // Atribui um valor � vari�vel $value ou usa uma string vazia como valor padr�o
    $value = isset($variaveisComponents['value']) 
        ? $variaveisComponents['value'] 
        : '';

    // Define a vari�vel $required como 'required' se o valor correspondente for verdadeiro, ou como falso caso contr�rio
    $required = isset($variaveisComponents['required']) && $variaveisComponents['required'] 
        ? 'required' 
        : false;

    // Define o valor da classe de tamanho com base na chave 'size', ou usa 'textfield-container-md' como padr�o
    $size = isset($variaveisComponents['size']) 
        ? 'textfield-container-'.$variaveisComponents['size'] 
        : 'textfield-container-md';

    // Define a vari�vel $disabled como 'disabled ' se o valor correspondente for verdadeiro, ou como uma string vazia caso contr�rio
    $disabled = (isset($variaveisComponents['disabled']) && $variaveisComponents['disabled']) 
        ? 'disabled' 
        : '';

    // Inicializa a vari�vel $customAttributes como uma string vazia
    $customAttributes = '';

    // Verifica se 'attributes' est� definido e � um array, para adicionar atributos personalizados
    if (isset($variaveisComponents['attributes']) && is_array($variaveisComponents['attributes'])) {
        foreach ($variaveisComponents['attributes'] as $key => $value) {
            // Adiciona cada atributo e valor � string $customAttributes, escapando caracteres especiais
            $customAttributes .= htmlspecialchars($key) . '="' . htmlspecialchars($value) . '" ';
        }
    }

    // Inclui o arquivos CSS
    includeOnceAsset('css', '/libs/renderComponents/src/components/inputs/text/simple/simple.css');

    // Inclui o arquivos JavaScript
    includeOnceAsset('js', '/libs/renderComponents/src/components/inputs/text/simple/simple.js');
?>

<div class="textfield-container">
    <label for="<?= $id ?>" class="label">
        <?= $label ?>
        <?php if ($required): ?>
            <i class="icon asterisk"></i>
        <?php endif; ?>
    </label>

    <input
        <?= (!empty($disabled))?'style="cursor: not-allowed;"':''?>
        id="<?= $id ?>"
        type="text"
        name="<?= $name ?>"
        value="<?= $value ?>"
        class="<?= $inputClass ?> <?= $size ?>"
        placeholder="<?= $placeholder ?>"
        <?= $disabled ?>
        <?= $required ?>
        <?= $customAttributes ?>
    >
</div>

<?php
    // Limpar vari�veis ap�s uso
    unset($id, $placeholder, $inputClass, $label, $name, $value, $required, $size, $disabled, $customAttributes);
?>