<?php
    // Atribui um valor � vari�vel $id ou usa 'default-textarea' como valor padr�o, caso n�o esteja definido
    $id = isset($variaveisComponents['id']) 
        ? $variaveisComponents['id'] 
        : 'default-textarea';

    // Atribui um valor � vari�vel $placeholder ou usa 'Write your thoughts here...' como valor padr�o
    $placeholder = isset($variaveisComponents['placeholder']) 
        ? $variaveisComponents['placeholder'] 
        : 'Write your thoughts here...';

    // Atribui um valor � vari�vel $inputClass ou usa 'textarea-input' como valor padr�o
    $inputClass = isset($variaveisComponents['inputClass']) 
        ? $variaveisComponents['inputClass'] 
        : 'textarea-input';

    // Atribui um valor � vari�vel $label ou usa 'Your message' como valor padr�o
    $label = isset($variaveisComponents['label']) 
        ? $variaveisComponents['label'] 
        : 'Your message';

    // Atribui um valor � vari�vel $name ou usa uma string vazia como valor padr�o
    $name = isset($variaveisComponents['name']) 
        ? $variaveisComponents['name'] 
        : '';

    // Atribui um valor � vari�vel $value ou usa uma string vazia como valor padr�o
    $value = isset($variaveisComponents['value']) 
        ? $variaveisComponents['value'] 
        : '';

    // Define a vari�vel $required como 'required' se a chave correspondente for verdadeira, ou como falso caso contr�rio
    $required = isset($variaveisComponents['required']) && $variaveisComponents['required'] 
        ? 'required' 
        : false;

    // Define o valor da classe de tamanho com base na chave 'size', ou usa 'textarea-container-md' como padr�o
    $size = isset($variaveisComponents['size']) 
        ? 'textarea-container-'.$variaveisComponents['size'] 
        : 'textarea-container-md';

    // Define a vari�vel $disabled como 'disabled' se o valor correspondente for verdadeiro, ou como uma string vazia caso contr�rio
    $disabled = (isset($variaveisComponents['disabled']) && $variaveisComponents['disabled']) 
        ? 'disabled' 
        : '';

    // Inicializa a vari�vel $customAttributes como uma string vazia
    $customAttributes = '';

    if (isset($variaveisComponents['attributes']) && is_array($variaveisComponents['attributes'])) {
        foreach ($variaveisComponents['attributes'] as $key => $value) {
            // Adiciona cada atributo e valor � string $customAttributes, escapando caracteres especiais
            $customAttributes .= htmlspecialchars($key) . '="' . htmlspecialchars($value) . '" ';
        }
    }

    // Inclui o arquivos CSS
    includeOnceAsset('css', '/libs/renderComponents/src/components/inputs/textarea/simple/simple.css');

    // Inclui o arquivos JavaScript
    includeOnceAsset('js', '/libs/renderComponents/src/components/inputs/textarea/simple/simple.js');
?>

<div class="textarea-container">
    <label for="<?= $id ?>" class="label">
        <?= $label ?>
        <?php if ($required): ?>
            <i class="icon asterisk"></i>
        <?php endif; ?>
    </label>

    <textarea
        <?= (!empty($disabled))?'style="cursor: not-allowed;"':''?>
        id="<?= $id ?>"
        name="<?= $name ?>"
        rows="4"
        class="<?= $inputClass ?> <?= $size ?>"
        placeholder="<?= $placeholder ?>"
        <?= $disabled ?>
        <?= $required ?>
        <?= $customAttributes ?>
    ><?= htmlspecialchars($value) ?></textarea>
</div>

<?php
    // Limpar vari�veis ap�s uso
    unset($id, $placeholder, $inputClass, $label, $name, $value, $required, $size, $disabled, $customAttributes);
?>
