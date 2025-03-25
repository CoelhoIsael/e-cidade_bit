<?php

    use App\Helpers\StringHelper;

    // Gera um identificador �nico (UUID) hexadecimal de 32 caracteres.
    $uuid = bin2hex(random_bytes(16));

    // Define o ID do componente, utilizando um valor padr�o caso n�o esteja definido nas vari�veis do componente.
    $id = isset($variaveisComponents['id']) 
        ? $variaveisComponents['id'] 
        : 'server-side-grid';

    // Define $multiSelect com o valor de 'multiSelect' ou false se n�o existir.
    $multiSelect = isset($variaveisComponents['multiSelect']) 
        ? $variaveisComponents['multiSelect'] 
        : false;
    
    // Define $rowClick com o valor de 'multiSelect' ou false (poss�vel erro l�gico).
    $rowClick = isset($variaveisComponents['rowClick']) 
        ? $variaveisComponents['rowClick'] 
        : false;

    // Define $rowClickFunction com o valor de 'rowClickFunction' ou false se n�o existir.
    $rowClickFunction = isset($variaveisComponents['rowClickFunction']) 
        ? $variaveisComponents['rowClickFunction'] 
        : false;
    
    // Define $rowDoubleClick com o valor de 'multiSelect' ou false (poss�vel erro l�gico).
    $rowDoubleClick = isset($variaveisComponents['rowDoubleClick']) 
        ? $variaveisComponents['rowDoubleClick'] 
        : false;

    $loadFunction = isset($variaveisComponents['loadFunction']) 
        ? $variaveisComponents['loadFunction'] 
        : null;

    // Define $rowDoubleClickFunction com o valor de 'rowDoubleClickFunction' ou false se n�o existir.
    $rowDoubleClickFunction = isset($variaveisComponents['rowDoubleClickFunction']) 
        ? $variaveisComponents['rowDoubleClickFunction'] 
        : false;

    // Define a classe CSS do componente, com um valor padr�o caso n�o esteja definido.
    $class = isset($variaveisComponents['class']) 
        ? $variaveisComponents['class'] 
        : 'grid-container-3b7d7b0517f09';

    // Define as colunas do grid. Caso n�o haja defini��o, utiliza um array padr�o com tr�s elementos "#".
    $columns = isset($variaveisComponents['columns']) 
        ? $variaveisComponents['columns'] 
        : ['#', '#', '#'];

    // Define a URL da API que ser� usada pelo grid, com valor padr�o vazio.
    $apiUrl = isset($variaveisComponents['apiUrl']) 
        ? $variaveisComponents['apiUrl'] 
        : '';

    // Define o n�mero de registros por p�gina no grid. O padr�o � 10.
    $perPage = isset($variaveisComponents['perPage']) 
        ? $variaveisComponents['perPage'] 
        : 10;

    // Indica se a funcionalidade de busca est� habilitada. O padr�o � true (desabilitado).
    $search = isset($variaveisComponents['search']) 
        ? $variaveisComponents['search'] 
        : false;

    // Indica se a funcionalidade de ordena��o est� habilitada. O padr�o � true (desabilitado).
    $sort = isset($variaveisComponents['sort']) 
        ? $variaveisComponents['sort'] 
        : false;

    // Indica se a funcionalidade de fixedHeader est� habilitada. O padr�o � true (desabilitado).
    $fixedHeader = isset($variaveisComponents['fixedHeader']) 
        ? $variaveisComponents['fixedHeader'] 
        : false;

    // Sanitiza o valor do ID, transformando-o para min�sculas e removendo caracteres n�o alfanum�ricos.
    $idSanitize = strtolower(preg_replace('/[^a-z0-9]/', '', $id));

    // Extrai os r�tulos ("label") das colunas, caso existam. Caso contr�rio, resultar� em um array vazio.
    $labels = array_column($columns, 'label');

    // Extrai os nomes das colunas, caso existam. Caso contr�rio, resultar� em um array vazio.
    $columnsNames = array_column($columns, 'name');

    // Define a mensagem exibida quando a tabela est� vazia na primeira carga. O padr�o � 'Mensagem de teste'.
    $emptyTableFirstLoadMessage = isset($variaveisComponents['emptyTableFirstLoadMessage']) 
        ? $variaveisComponents['emptyTableFirstLoadMessage'] 
        : 'Mensagem de teste';

    // Inclui o arquivos CSS
    includeOnceAsset('css', '/libs/renderComponents/src/components/tables/gridJs/serverSideLegacy/serverSide.css');
    includeOnceAsset('css', '/libs/renderComponents/src/components/tables/gridJs/mermaid.min.css');
    includeOnceAsset('css', '/libs/renderComponents/src/components/icons/cursor-arrow-rays/cursor-arrow-rays.css');
    includeOnceAsset('css', '/libs/renderComponents/src/components/icons/cursor-arrow-ripple/cursor-arrow-ripple.css');
    
    // Inclui o arquivos JavaScript
    includeOnceAsset('js', '/libs/renderComponents/src/components/tables/gridJs/serverSideLegacy/serverSide.js');
    includeOnceAsset('js', '/libs/renderComponents/src/components/tables/gridJs/gridjs.umd.js');
?>

<div id="gridjsContent<?=$uuid?>" class="<?=$class?> <?=(empty($apiUrl))?'hidden-gridjs-component':''?>">
    <div id="contentBox-0a0a" class="content-box-gridjs-component">

        <!-- Header da tabela -->
        <div class="row-gridjs-component">

            <!-- Barra de pesquisa -->
            <div class="search-input-container-gridjs-component">
                <?php if($search): ?>
                    <svg class="search-icon-gridjs-component" width="20" height="20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" />
                    </svg>

                    <input id="searchInput<?=$uuid?>" type="text" class="search-input-gridjs-component" placeholder="Pesquisar em todas as colunas...">
                <?php endif; ?>


            </div>

            <!-- Bot�o de configura��es da tabela -->
            <div style="display: flex;">

                <div style="display: flex; align-content: center; align-items: center; justify-content: center; margin-right: 20px;">
                    <?php if($rowClick): ?>
                        <span class="tooltip-8653X">
                            <icon id="iconRowClick" class="icon cursor-arrow-rays"></icon>
                            <span class="tooltip-text-8653X">
                                <?= StringHelper::toUtf8('Esta tabela permite a execu��o de a��es ao clicar em suas linhas.') ?>
                            </span>
                        </span>
                    <?php endif; ?>

                    <?php if($rowDoubleClick): ?>
                        <span class="tooltip-8653X">
                            <icon id="iconRowDoubleClick" class="icon cursor-arrow-ripple"></icon>
                            <span class="tooltip-text-8653X">
                                <?= StringHelper::toUtf8('Esta tabela permite a execu��o de a��es ao dar um duplo clique em suas linhas.') ?>
                            </span>
                        </span>
                    <?php endif; ?>
                </div>
                
                <?php if(!empty($columnsNames)): ?>
                    <!-- Bot�o de configura��es -->
                    <button id="dropdownToggleButton<?=$uuid?>" class="dropdown-settings-button-gridjs-component" type="button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                        </svg>

                        <?= StringHelper::toUtf8('Configura��es') ?>
                    </button>
                    
                    <!-- Menu dropdown -->
                    <div id="dropdownToggle<?=$uuid?>" class="dropdown-settings-menu-gridjs-component hidden-gridjs-component">

                        <div class="clear-filter-gridjs-component" onclick="cleanFilters<?=$uuid?>()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m3 3 1.664 1.664M21 21l-1.5-1.5m-5.485-1.242L12 17.25 4.5 21V8.742m.164-4.078a2.15 2.15 0 0 1 1.743-1.342 48.507 48.507 0 0 1 11.186 0c1.1.128 1.907 1.077 1.907 2.185V19.5M4.664 4.664 19.5 19.5" />
                            </svg>
                            <span class="tooltip-gridjs-component">Limpar filtros</span>
                        </div>

                        <!-- Pesquisa por colunas -->
                        <div class="content-search-column-gridjs-component">
                            <div class="label-search-column-gridjs-component">
                                <span class="span-label-search-column-gridjs-component">
                                    Pesquisa por colunas:
                                </span>
                                
                                <div id="warningLabelColumn<?=$uuid?>" class="warning-label-search-column-gridjs-component hidden-gridjs-component">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                    </svg>
                                    Selecione alguma coluna para a pesquisa
                                </div>

                                <div id="warningLabelSearch<?=$uuid?>" class="warning-label-search-input-gridjs-component hidden-gridjs-component">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                    </svg>
                                    <?= StringHelper::toUtf8('Busque um termo relacionado � coluna selecionada.') ?>
                                </div>
                            </div>

                            <div class="sub-content-search-column-gridjs-component">
                                <select class="select-column-search-gridjs-component" id="selectColumnSearch<?=$uuid?>">
                                    <option value="">Selecione</option>

                                    <?php foreach ($columns as $i => $column): ?>
                                        <option value="<?=$column['name']?>">
                                            <?=$column['label']?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <div class="search-column-input-container-gridjs-component">
                                    <svg class="search-column-icon-gridjs-component" width="20" height="20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" />
                                    </svg>

                                    <input type="text" id="inputColumnSearch<?=$uuid?>" placeholder="Pesquisar na coluna selecionada..." class="input-column-search-gridjs-component">
                                </div>
                            </div>
                        </div>

                        <!-- Acordion de checkbox de vizibilidade -->
                        <div class="accordion-settings-menu">
                            <div class="accordion-item">
                                <div class="accordion-header-gridjs-component" onclick="toggleAccordionGridjsComponent(event)">
                                    <span>Visibilidade das Colunas</span>
                                    
                                    <div class="icon-toggle">
                                        <svg class="icon-expand" xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                        </svg>

                                        <svg class="icon-collapse hidden-gridjs-component" xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                                        </svg>
                                    </div>
                                </div>

                                <div class="accordion-content-gridjs-component hidden-gridjs-component">
                                    <ul class="dropdown-list-gridjs-component">
                                        <?php foreach ($columns as $i => $column): ?>
                                            <li>
                                                <div class="dropdown-item-gridjs-component">
                                                    <input class="checkbox-settings-menu-gridjs-component" id="checkbox-item-<?=$i?>" type="checkbox" data-label="<?=$column['label']?>" name="gridColumns<?=$uuid?>" value="<?=$column['name']?>" checked>
                                                    
                                                    <label for="checkbox-item-<?=$i?>" class="label-settings-menu-gridjs-component">
                                                        <?=$column['label']?>
                                                    </label>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                    
                            <!-- Acordion de coluna com codi��o e pesquisa -->
                            <div class="accordion-item">
                                <div class="accordion-header-gridjs-component" onclick="toggleAccordionGridjsComponent(event)">
                                    <span><?= StringHelper::toUtf8('Filtragem por Condi��es') ?></span>
        
                                    <div class="icon-toggle">
                                        <svg class="icon-expand" xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                        </svg>

                                        <svg class="icon-collapse hidden-gridjs-component" xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                                        </svg>
                                    </div>
                                </div>

                                <div class="accordion-content-gridjs-component hidden-gridjs-component">
                                    <div style="display: flex;">

                                        <select id="select-column-search-condition-<?=$uuid?>" class="select-column-condition-search-gridjs-component">
                                            <option value="">Selecione uma coluna</option>

                                            <?php foreach ($columns as $i => $column): ?>
                                                <option value="<?=$column['name']?>"><?=$column['label']?></option>
                                            <?php endforeach; ?>
                                        </select>

                                        <select id="select-search-condition-<?=$uuid?>" class="select-condition-search-gridjs-component">
                                            <option value=""><?= StringHelper::toUtf8('Selecione uma condi��o') ?></option>
                                            <option value=">">maior que</option>
                                            <option value="<">menor que</option>
                                            <option value=">=">maior ou igual a</option>
                                            <option value="<=">menor ou igual a</option>
                                        </select>

                                        <div class="search-condition-column-input-container-gridjs-component">
                                            <svg class="search-condition-column-icon-gridjs-component" width="20" height="20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" />
                                            </svg>

                                            <input type="text" id="input-search-condition-<?=$uuid?>" placeholder="Informe um valor" class="input-condition-search-gridjs-component">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>

                <?php endif; ?>
            </div>
        </div>
    </div>
            
    <div id="<?=$id?>"></div>
</div>

<div id="gridEmptyContent<?=$uuid?>" class="<?=(empty($apiUrl))?'':'hidden-gridjs-component'?>">
    <div class="empty-table-gridjs-component">
        <div class="icon-container-empty-table-gridjs-component">
            <svg class="search-icon-empty-table-gridjs-component" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" />
            </svg>
        </div>

        <div class="title-empty-table-gridjs-component">
            Nenhum registro encontrado
        </div>

        <div class="message-empty-table-gridjs-component">
            <?= StringHelper::toUtf8($emptyTableFirstLoadMessage) ?>
        </div>
    </div>
</div>


<div id="auxiliarySpansServerSideLegacy<?=$uuid?>">

</div>

<script>
    let grid<?=$uuid?>;

    /**
     * Inicializa um componente de tabela Grid.js com configura��es personalizadas.
     * 
     * Esta fun��o configura e renderiza uma tabela Grid.js usando dados de configura��o
     * fornecidos, al�m de aplicar suporte a ordena��o, pagina��o e sele��o de linhas.
     * 
     * Funcionalidade:
     * - Ordena��o de coluna: Permite ordena��o de coluna via servidor, com URLs din�micas
     *   constru�das com base na coluna e dire��o de ordena��o.
     * - Pagina��o: Habilita pagina��o com suporte ao servidor, controlando o n�mero
     *   de resultados exibidos por p�gina e o total de registros.
     * - Sele��o de linha: Adiciona funcionalidade de clique duplo e clique �nico para 
     *   capturar dados da linha e destacar a linha selecionada.
     * - Verifica��o de carregamento: Reitera o carregamento do Grid.js at� o m�ximo
     *   de 10 segundos, caso ele ainda n�o esteja dispon�vel, evitando erros de renderiza��o.
     * 
     * Eventos:
     * - 'rowClick': Gerencia a sele��o de linha e captura dados para o banco de dados 
     *   no evento de clique duplo.
     */
    function initializeGrid<?=$uuid?>() 
    {
        let elapsedTime = 0;

        if (isGridJsAvailable()) {
            const gridElement<?=$uuid?> = document.getElementById('<?=$id?>');
            const gridOptions = createGridOptions<?=$uuid?>();

            // Renderize o grid
            grid<?=$uuid?> = new gridjs.Grid(gridOptions).render(gridElement<?=$uuid?>);

            <?php if($rowClick): ?>
                setupRowClickEvent<?=$uuid?>();
            <?php endif; ?>

            <?php if($rowDoubleClick): ?>
                setupDoubleClickEvent<?=$uuid?>();
            <?php endif; ?>

            <?php if(!empty($loadFunction)): ?>;
                setTimeout(() => {
                    <?= $loadFunction ?>('<?=$uuid?>');
                }, 2000);
            <?php endif; ?>
            
        } else if (elapsedTime < maxDuration) {
            elapsedTime += checkInterval;
            setTimeout(initializeGrid<?=$uuid?>, checkInterval);
        } else {
            console.error('Grid.js n�o foi carregado ap�s 10 segundos.');
        }
    }

    /**
     * Verifica se a biblioteca Grid.js est� dispon�vel no ambiente atual.
     * 
     * A fun��o verifica se os objetos `gridjs` e `gridjs.Grid` est�o definidos.
     * Se ambos existirem, isso indica que a biblioteca Grid.js foi carregada 
     * corretamente e est� pronta para uso.
     * 
     * @returns {boolean} Retorna `true` se a Grid.js estiver dispon�vel, caso contr�rio, `false`.
     */
    function isGridJsAvailable() 
    {
        return typeof gridjs !== 'undefined' && typeof gridjs.Grid !== 'undefined';
    }

    /**
     * Cria as op��es de configura��o para um grid utilizando a biblioteca Grid.js.
     * 
     * Esta fun��o gera dinamicamente as op��es de configura��o do grid com base em 
     * par�metros fornecidos pelo backend e l�gica interna. 
     * 
     * - As colunas s�o definidas a partir da vari�vel PHP `$labels`.
     * - Configura��es de estilo, pesquisa, redimensionamento, cabe�alho fixo e idioma
     *   tamb�m s�o inclu�das.
     * - Caso uma URL da API seja fornecida, s�o ativadas op��es de servidor, 
     *   como ordena��o, pagina��o e busca. 
     * - Se nenhuma URL for especificada, um grid vazio � retornado.
     * 
     * @returns {object} Objeto contendo as op��es configuradas para o grid.
     */
    function createGridOptions<?=$uuid?>() 
    {
        let labels = <?=json_encode($labels)?>;
        let labelsFilter = labels.filter(item => item !== undefined && item !== null && item !== "");

        <?php if($rowClick): ?>
            labelsFilter.unshift({name: gridjs.html('<i onclick="toggleSelection()" class="icon m-8653X"></i>'), sort: false});
        <?php endif; ?>

        const options = {
            columns: labelsFilter,
            style: { table: { 'white-space': 'nowrap' } },
            search: false,
            <?= ($fixedHeader) ? 'fixedHeader: true' : 'fixedHeader: false'; ?>,
            <?= ($sort) ? 'sort: true' : 'sort: false'; ?>,
            language: getLanguageOptions<?=$uuid?>(),
        };

        const url = '<?=$apiUrl?>';

        if (url.trim()) {
            options.server = getServerOptions<?=$uuid?>(url);
            <?php if($sort): ?>
                options.sort = getSortOptions<?=$uuid?>(url);
            <?php endif; ?>
            options.pagination = getPaginationOptions<?=$uuid?>(url);
        } else {
            options.data = [];
        }

        return options;
    }

    /**
     * Define as op��es de idioma para o grid, customizando textos de interface.
     * 
     * Esta fun��o retorna um objeto contendo as mensagens traduzidas ou adaptadas
     * para o idioma desejado. As tradu��es abrangem:
     * - Placeholders e mensagens da barra de busca.
     * - Mensagens de ordena��o, incluindo ordem crescente e decrescente.
     * - Mensagens de pagina��o, como navega��o entre p�ginas e resultados exibidos.
     * - Mensagens para estados de carregamento, erro ou aus�ncia de registros.
     * 
     * @returns {object} Objeto com as configura��es de idioma para o grid.
     */
    function getLanguageOptions<?=$uuid?>() 
    {
        return {
            search: { placeholder: 'Digite uma palavra-chave...' },
            sort: { sortAsc: 'Ordenar coluna em ordem crescente', sortDesc: 'Ordenar coluna em ordem decrescente' },
            pagination: {
                previous: 'Anterior', next: '<?=StringHelper::toUtf8('Pr�ximo')?>',
                navigate: (page, pages) => `P�gina ${page} de ${pages}`,
                page: (page) => `P�gina ${page}`, showing: 'Exibindo',
                of: 'de', to: 'a', results: 'resultados',
            },
            loading: 'Carregando...', noRecordsFound: 'Nenhum registro correspondente encontrado',
            error: 'Ocorreu um erro ao buscar os dados',
        };
    }

    /**
     * Define as op��es do servidor para o grid, configurando como os dados s�o 
     * obtidos e processados a partir de uma API.
     * 
     * Esta fun��o retorna um objeto que cont�m:
     * - A URL base da API, configurada com um par�metro adicional `?` para facilitar a adi��o de query strings.
     * - O m�todo HTTP usado para buscar os dados (neste caso, `GET`).
     * - Uma fun��o `then` que transforma os dados recebidos, convertendo cada item
     *   em um array com os valores das suas propriedades.
     * - Uma fun��o `total` que extrai o n�mero total de registros a partir da 
     *   propriedade `meta.total` da resposta da API.
     * - Uma fun��o `handle` que processa a resposta HTTP, convertendo-a para JSON.
     * 
     * @param {string} url - A URL base da API para buscar os dados.
     * @returns {object} Objeto configurado com as op��es do servidor para o grid.
     */
    function getServerOptions<?=$uuid?>(url) 
    {
        return {
            url: url.includes('?') ? url : url + '?',
            method: 'GET',
            then: data => {
                let dataColumns = JSON.parse('<?=json_encode($columns)?>').map(col => {
                    if (col.name.includes(' as ')) {
                        col.name = col.name.split(' as ')[1].trim();
                    }
                    return col;
                })

                const columnsToRemove = dataColumns
                    .map((col, index) => (col.label === "" || col.label === null ? index : -1))
                    .filter(index => index !== -1);
                
                const columnNamesToRemove = columnsToRemove.map(index => {
                    const fullName = dataColumns[index].name;
                    if (fullName.includes(' as ')) {
                        return fullName.split(' as ')[1].trim();
                    }
                    return fullName;
                });

                const filteredData = data.data.map(item => {
                    const filteredItem = {};
                    Object.keys(item).forEach(key => {
                        if (!columnNamesToRemove.includes(key)) {
                            filteredItem[key] = item[key];
                        }
                    });
                    
                    return Object.values(filteredItem);
                });

                document.getElementById('auxiliarySpansServerSideLegacy<?=$uuid?>').innerHTML = "";

                data.data.forEach((rowData, rowIndex) => {
                    const values = Object.values(rowData);
                    const hiddenSpan = document.createElement('span');
                    hiddenSpan.classList.add('hidden');

                    values.forEach((cellValue, colIndex) => {
                        if (colIndex < dataColumns.length) {
                            const column = dataColumns[colIndex];
                            hiddenSpan.setAttribute(`data-${column.name}`, cellValue);
                        }
                    });
                    
                    document.getElementById('auxiliarySpansServerSideLegacy<?=$uuid?>').appendChild(hiddenSpan);
                });

                // Adiciona o �cone para cada linha
                return filteredData.map(item => {
                    <?php if($rowClick): ?>
                        item.unshift(gridjs.html('<i data-uuid="<?=$uuid?>" class="icon circle-8653X"></i>'));
                    <?php endif; ?>
                    return item;
                });
            },
            total: (data) => data.meta.total,
            handle: (res) => res.json(),
        };
    }

    /**
     * Define as op��es de ordena��o para o grid, incluindo suporte para ordena��o 
     * no lado do servidor.
     * 
     * Esta fun��o retorna um objeto que configura:
     * - A desativa��o da ordena��o por m�ltiplas colunas (`multiColumn: false`).
     * - A l�gica para a constru��o da URL de ordena��o no servidor:
     *   - Utiliza a primeira coluna selecionada como refer�ncia para ordenar.
     *   - Determina a dire��o (`asc` ou `desc`) com base no valor da propriedade `direction` da coluna.
     *   - Mapeia o �ndice da coluna para o nome real da coluna no servidor, usando 
     *     o objeto PHP `$columnsNames`.
     *   - Adiciona par�metros `order` (nome da coluna) e `dir` (dire��o) � URL base.
     * 
     * @param {string} url - A URL base usada como ponto de partida para a ordena��o.
     * @returns {object} Objeto configurado com as op��es de ordena��o para o grid.
     */
    function getSortOptions<?=$uuid?>(url) 
    {
        return {
            multiColumn: false,
            server: {
                url: (prev, columns) => {
                    if (!columns.length) return prev;
                    const col = columns[0];
                    const dir = col.direction === 1 ? 'asc' : 'desc';
                    const colName = <?=json_encode($columnsNames)?>[col.index];
                    return `${prev}&order=${colName}&dir=${dir}`;
                },
            },
        };
    }

    /**
     * Define as op��es de pagina��o para o grid, incluindo suporte para pagina��o
     * no lado do servidor.
     * 
     * Esta fun��o retorna um objeto que configura:
     * - Habilita��o da pagina��o (`enabled: true`).
     * - O n�mero de registros exibidos por p�gina, definido pela vari�vel PHP `$perPage`.
     * - L�gica de pagina��o no servidor, que constr�i a URL com os par�metros de:
     *   - Quantidade de registros por p�gina (`show`).
     *   - P�gina atual (`page`), ajustada para ser baseada em 1 (padr�o do servidor).
     * 
     * @param {string} url - A URL base usada como ponto de partida para a pagina��o.
     * @returns {object} Objeto configurado com as op��es de pagina��o para o grid.
     */
    function getPaginationOptions<?=$uuid?>(url) {
        return {
            enabled: true,
            limit: <?=$perPage?>,
            server: {
                url: (prev, page, perPage) => `${prev}&show=<?=$perPage?>&page=${page+1}`,
            },
        };
    }

    /**
     * Gerencia a sele��o visual da linha clicada na tabela.
     * 
     * Mant�m o estilo da linha selecionada, adicionando uma classe CSS 
     * para destacar visualmente a linha. Remove a sele��o anterior ao 
     * clicar em uma nova linha.
     */
    function setupRowClickEvent<?=$uuid?>() {
        let selectedRow = null;

        grid<?=$uuid?>.on('rowClick', (event, row) => {
            const rowElement = event.target.closest('tr');
            <?php if(!$multiSelect): ?>
                if (selectedRow) selectedRow.classList.remove('gridjs-tr-selected');
                if (selectedRow) selectedRow.classList.remove('rowSelected<?=$idSanitize?>');
            <?php endif; ?>
            rowElement.classList.toggle('gridjs-tr-selected');
            rowElement.classList.toggle('rowSelected<?=$idSanitize?>');

            const iconElement = rowElement.querySelector('td:first-child i');
            if (iconElement) {
                if (iconElement.classList.contains('circle-8653X')) {
                    iconElement.classList.replace('circle-8653X', 'check-8653X');
                } else {
                    iconElement.classList.replace('check-8653X', 'circle-8653X');
                }
            }

            <?php if($rowClickFunction): ?>
                const selectedRows = document.querySelectorAll('.rowSelected<?=$idSanitize?>');
                <?=$rowClickFunction?>(selectedRows);
            <?php endif; ?>
        });
    }

    /**
     * Gerencia cliques �nicos e duplos em uma linha da tabela Grid.js.
     * 
     * Configura um timeout para detectar cliques duplos em uma linha.
     * Se um clique duplo for detectado, a fun��o `captureDB` � chamada 
     * com o elemento da linha clicada. Para cliques �nicos, o timeout 
     * � zerado ap�s 300ms, n�o realizando nenhuma a��o.
     */
    function setupDoubleClickEvent<?=$uuid?>() {
        <?php if($rowDoubleClick): ?>
            let clickTimeout;

            grid<?=$uuid?>.on('rowClick', (event, row) => {
                if (clickTimeout) {
                    clearTimeout(clickTimeout);
                    clickTimeout = null;
                    const rowElement = event.target.closest('tr');
                    <?php if($rowDoubleClickFunction): ?>
                        <?=$rowDoubleClickFunction?>(rowElement);
                    <?php endif; ?>
                } else {
                    clickTimeout = setTimeout(() => { clickTimeout = null; }, 300);
                }
            });
        <?php endif; ?>
    }

    /**
     * Atualiza o grid existente, recriando sua inst�ncia.
     * 
     * Esta fun��o � utilizada para reiniciar o grid quando h� necessidade de 
     * atualizar suas configura��es ou recarregar seus dados. O processo envolve:
     * - Verificar se a inst�ncia do grid (`grid$uuid`) existe.
    * - Destruir a inst�ncia atual usando o m�todo `destroy`.
    * - Recriar a inst�ncia do grid chamando a fun��o `initializeGrid$uuid`.
    * 
    * @returns {void}
    */
    function refreshGrid<?=$idSanitize?>() 
    {
        if (grid<?=$uuid?>) {
            grid<?=$uuid?>.destroy();
            initializeGrid<?=$uuid?>();
        }
    }

    /**
     * Obt�m o valor do c�digo da linha atualmente selecionada.
     *
     * @returns {string|null} Retorna o valor da c�lula "codigo" da linha selecionada, 
     *                        ou null se nenhuma linha estiver selecionada.
     */
    function getSelectedCodigo() {
        const selectedRows = document.querySelectorAll('.rowSelected<?=$idSanitize?>');
        return selectedRows;
    }

    /**
     * Limpa todos os filtros aplicados no Grid.js e recarrega os dados da tabela
     * a partir da URL original, sem par�metros de pesquisa ou filtros adicionais.
     * 
     * A fun��o redefine a URL do servidor para a URL base, fazendo uma nova requisi��o
     * ao servidor para buscar os dados sem filtros aplicados. Isso restaura a visualiza��o
     * padr�o da tabela.
     */
    function cleanFilters<?=$uuid?>()
    {
        const checkboxes = document.querySelectorAll("input[name='gridColumns<?=$uuid?>']");
        const selectedDataColumns = Array.from(checkboxes).map(checkbox => checkbox.getAttribute('data-label'));
        <?php if($rowClick): ?>
            selectedDataColumns.unshift({name: gridjs.html('<i onclick="toggleSelection()" class="icon m-8653X"></i>'), sort: false});
        <?php endif; ?>

        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
        });

        grid<?=$uuid?>.updateConfig({
            columns: selectedDataColumns,
            server: {
                url: `<?=$apiUrl?>?`,
                then: data => {
                    // Identifica os �ndices das colunas com label vazio ou null
                    const columnsToRemove = data.columns
                        .map((col, index) => (col.label === "" || col.label === null ? index : -1))
                        .filter(index => index !== -1);

                    // Filtra os dados
                    const filteredData = data.data.map(item => {
                        const values = Object.values(item);

                        // Remove os valores correspondentes aos �ndices das colunas removidas
                        return values.filter((_, index) => !columnsToRemove.includes(index));
                    });

                    document.getElementById('auxiliarySpansServerSideLegacy<?=$uuid?>').innerHTML = "";

                    data.data.forEach((cellValue, colIndex) => {
                        const values = Object.values(cellValue);
                        const hiddenSpan = document.createElement('span');
                        hiddenSpan.classList.add('hidden');

                        values.forEach((cellValue, colIndex) => {
                            const column = data.columns[colIndex];
                            hiddenSpan.setAttribute(`data-${column.name}`, cellValue);
                            document.getElementById('auxiliarySpansServerSideLegacy<?=$uuid?>').appendChild(hiddenSpan);
                        });
                    });

                    // Adiciona o �cone para cada linha
                    return filteredData.map(item => {
                        <?php if($rowClick): ?>
                            item.unshift(gridjs.html('<i data-uuid="<?=$uuid?>" class="icon circle-8653X"></i>'));
                        <?php endif; ?>
                        return item;
                    });
                },
                total: data => data.meta.total,
            }
        }).forceRender();
    }

    function toggleSelection() {
        let gridContainer = document.getElementById(`gridjsContent<?=$uuid?>`);
        if (!gridContainer) {
            console.error("Grid container not found!");
            return;
        }

        let allRows = gridContainer.querySelectorAll("tbody .gridjs-tr");
        let allIcons = gridContainer.querySelectorAll("tbody .gridjs-tr .icon");

        if (allIcons.length === 0) return;

        let areAllSelected = Array.from(allIcons).every(icon => icon.classList.contains("check-8653X"));

        allIcons.forEach(icon => {
            let row = icon.closest(".gridjs-tr");

            if (areAllSelected) {
                icon.classList.replace("check-8653X", "circle-8653X");
                row.classList.remove("gridjs-tr-selected", "rowSelected<?=$idSanitize?>");
            } else {
                icon.classList.replace("circle-8653X", "check-8653X");
                row.classList.add("gridjs-tr-selected", "rowSelected<?=$idSanitize?>");
            }
        });

        <?php if($rowClickFunction): ?>
            const selectedRows = document.querySelectorAll('.rowSelected<?=$idSanitize?>');
            <?=$rowClickFunction?>(selectedRows);
        <?php endif; ?>
    }

    /**
     * Inicializa um Grid.js din�mico com configura��o de colunas, pagina��o e ordena��o baseada em servidor.
     * 
     * @param {string} url - URL da API para carregar os dados do grid.
     * @param {Object} request - Objeto contendo informa��es de configura��o para o grid.
     * @param {Array} request.columns - Array de colunas com propriedades `label` e `name`.
     */
    function initializeGridjs<?=$idSanitize?>(url, request) 
    {
        const labels = request.columns
            .map(column => column.label)
            .filter(label => label && label.trim() !== "");

        <?php if($rowClick): ?>
            labels.unshift({name: gridjs.html('<i onclick="toggleSelection()" class="icon m-8653X"></i>'), sort: false});
        <?php endif; ?>

        const names = request.columns.map(column => column.name);
        const gridjsContent = document.getElementById('gridjsContent<?=$uuid?>');
        const gridEmptyContent = document.getElementById('gridEmptyContent<?=$uuid?>');

        gridjsContent.classList.remove("hidden-gridjs-component");
        gridEmptyContent.classList.add("hidden-gridjs-component");

        refreshGrid<?=$idSanitize?>()

        grid<?=$uuid?>.updateConfig({
            columns: labels,
            style: { table: { 'white-space': 'nowrap' } },
            sort: {
                multiColumn: false,
                server: {
                    url: (prev, columns) => {
                        if (!columns.length) return prev;

                        const col = columns[0];
                        const dir = col.direction === 1 ? 'asc' : 'desc';
                        const colName = names[col.index];

                        return `${prev}&order=${colName}&dir=${dir}`;
                    }
                }
            },
            pagination: {
                enabled: true,
                limit: <?=$perPage?>,
                server: {
                    url: (prev, page, perPage) => `${prev}&show=<?=$perPage?>&page=${page+1}`
                }
            },
            server: {
                url: url,
                method: 'GET',
                then: data => {
                    // Identifica os �ndices das colunas com label vazio ou null
                    const columnsToRemove = data.columns
                        .map((col, index) => (col.label === "" || col.label === null ? index : -1))
                        .filter(index => index !== -1);

                    // Filtra os dados
                    const filteredData = data.data.map(item => {
                        const values = Object.values(item);

                        // Remove os valores correspondentes aos �ndices das colunas removidas
                        return values.filter((_, index) => !columnsToRemove.includes(index));
                    });

                    document.getElementById('auxiliarySpansServerSideLegacy<?=$uuid?>').innerHTML = "";

                    data.data.forEach((cellValue, colIndex) => {
                        const values = Object.values(cellValue);
                        const hiddenSpan = document.createElement('span');
                        hiddenSpan.classList.add('hidden');

                        values.forEach((cellValue, colIndex) => {
                            const column = data.columns[colIndex];
                            hiddenSpan.setAttribute(`data-${column.name}`, cellValue);
                            document.getElementById('auxiliarySpansServerSideLegacy<?=$uuid?>').appendChild(hiddenSpan);
                        });
                    });

                    // Adiciona o �cone para cada linha
                    return filteredData.map(item => {
                        <?php if($rowClick): ?>
                            item.unshift(gridjs.html('<i data-uuid="<?=$uuid?>" class="icon circle-8653X"></i>'));
                        <?php endif; ?>
                        return item;
                    });
                },
                total: data => data.meta.total,
                handle: (res) => res.json(),
            }
        }).forceRender();

        <?php if($search): ?>
            document.getElementById("searchInput<?=$uuid?>").addEventListener("input", debounce<?=$uuid?>(function (event) {
                const searchTerm = event.target.value.toLowerCase();

                grid<?=$uuid?>.updateConfig({
                    server: {
                        url: url+`&search=${searchTerm}`,
                        then: data => {
                            // Identifica os �ndices das colunas com label vazio ou null
                            const columnsToRemove = data.columns
                                .map((col, index) => (col.label === "" || col.label === null ? index : -1))
                                .filter(index => index !== -1);

                            // Filtra os dados
                            const filteredData = data.data.map(item => {
                                const values = Object.values(item);

                                // Remove os valores correspondentes aos �ndices das colunas removidas
                                return values.filter((_, index) => !columnsToRemove.includes(index));
                            });

                            document.getElementById('auxiliarySpansServerSideLegacy<?=$uuid?>').innerHTML = "";

                            data.data.forEach((cellValue, colIndex) => {
                                const values = Object.values(cellValue);
                                const hiddenSpan = document.createElement('span');
                                hiddenSpan.classList.add('hidden');

                                values.forEach((cellValue, colIndex) => {
                                    const column = data.columns[colIndex];
                                    hiddenSpan.setAttribute(`data-${column.name}`, cellValue);
                                    document.getElementById('auxiliarySpansServerSideLegacy<?=$uuid?>').appendChild(hiddenSpan);
                                });
                            });

                            // Adiciona o �cone para cada linha
                            return filteredData.map(item => {
                                <?php if($rowClick): ?>
                                    item.unshift(gridjs.html('<i data-uuid="<?=$uuid?>" class="icon circle-8653X"></i>'));
                                <?php endif; ?>
                                return item;
                            });
                        },
                        total: data => data.meta.total,
                    }
                }).forceRender();
            }, 300));
        <?php endif; ?>

        <?php if(!empty($loadFunction)): ?>;
            setTimeout(() => {
                <?= $loadFunction ?>('<?=$uuid?>');
            }, 2000);
        <?php endif; ?>
    }

    /**
     * A fun��o `debounce` implementa uma t�cnica que limita a quantidade de vezes que uma fun��o pode ser executada em um intervalo de tempo. 
     * Ela � comumente usada para otimizar eventos de alta frequ�ncia, como o `input` de um campo de busca, evitando chamadas excessivas 
     * a uma fun��o, especialmente em intera��es r�pidas do usu�rio.
     * 
     * @param {Function} func - A fun��o que ser� "debounced". A fun��o original que ser� chamada ap�s o atraso.
     * @param {number} delay - O tempo de atraso (em milissegundos) ap�s a �ltima execu��o do evento, antes de chamar a fun��o.
     * 
     * @returns {Function} - Retorna uma nova fun��o que, quando chamada, cancela qualquer execu��o pendente e reinicia o timer.
     * Isso garante que `func` s� seja chamada ap�s o tempo `delay` de inatividade ap�s a �ltima chamada.
     * 
     * Exemplo de uso:
     *     - Usado frequentemente em campos de busca ou ao rolar a p�gina, para reduzir a quantidade de chamadas de API.
     */
    function debounce<?=$uuid?>(func, delay) 
    {
        let timeoutId;
        return function (...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                func(...args);
            }, delay);
        };
    }

    /**
     * Configura a p�gina ap�s o carregamento do DOM para inicializar o Grid.js, 
     * configurar o menu dropdown e adicionar funcionalidade de busca.
     */
    document.addEventListener('DOMContentLoaded', function() {
        initializeGrid<?=$uuid?>();

        <?php if(!empty($columnsNames)): ?>
            /**
             * O c�digo abaixo seleciona dois elementos do DOM usando seus respectivos IDs, que incluem um identificador �nico gerado dinamicamente
             * atrav�s da vari�vel `$uuid`. Esses elementos s�o usados para controlar a intera��o de um bot�o de toggle (abre e fecha) de um dropdown.
             */
            const dropdownToggleButton<?=$uuid?> = document.getElementById("dropdownToggleButton<?=$uuid?>");
            const dropdownToggle<?=$uuid?> = document.getElementById("dropdownToggle<?=$uuid?>");
        
            /**
             * Gerencia a pesquisa filtrada por coluna no Grid.js, permitindo ao usu�rio 
             * escolher a coluna para pesquisa e fornecer o termo de busca.
             * 
             * - Quando o campo de pesquisa ou a sele��o de coluna s�o alterados, 
             *   a URL da requisi��o � atualizada com os par�metros adequados para 
             *   filtrar os dados no servidor.
             */
            const selectColumnSearch = document.getElementById("selectColumnSearch<?=$uuid?>");
            const inputColumnSearch = document.getElementById("inputColumnSearch<?=$uuid?>");

            <?php if($search): ?>
                /*
                * Obt�m a refer�ncia ao elemento HTML de entrada (input) com o ID "searchInput$uuid".
                * 
                * - O "document.getElementById" � usado para selecionar o elemento HTML pelo seu ID.
                * - "$uuid" � provavelmente uma vari�vel PHP que est� sendo interpolada para 
                *   gerar um identificador �nico no lado do servidor. 
                */
                const inputSearch = document.getElementById("searchInput<?=$uuid?>");
            <?php endif; ?>

            /**
             * Alterna a visibilidade do menu dropdown ao clicar no bot�o.
             *
             * @param {Event} event - O evento de clique no bot�o do dropdown.
             * - Interrompe a propaga��o para que o clique fora do menu feche o dropdown.
             */
            dropdownToggleButton<?=$uuid?>.addEventListener("click", function(event) {
                dropdownToggle<?=$uuid?>.classList.toggle("hidden-gridjs-component");
                event.stopPropagation();
            });
        
            /**
             * Fecha o menu dropdown ao clicar fora dele.
             *
             * @param {Event} event - Evento de clique na janela.
             * - Verifica se o clique ocorreu fora dos elementos dropdown e, se sim, oculta o menu.
             */
            window.addEventListener("click", function(event) {
                if (!dropdownToggleButton<?=$uuid?>.contains(event.target) && !dropdownToggle<?=$uuid?>.contains(event.target)) {
                    dropdownToggle<?=$uuid?>.classList.add("hidden-gridjs-component");
                }
            });

            <?php if($search): ?>
                /**
                 * Configura uma funcionalidade de busca din�mica no Grid.js.
                 *
                 * @param {Event} event - O evento de entrada no campo de busca.
                 * - Captura o valor digitado, atualiza a configura��o de URL do Grid.js
                 *   para incluir o termo de busca e for�a a renderiza��o com novos resultados.
                 */
                function updateGridWithSearch(event) 
                {
                    const searchTerm = inputSearch.value.toLowerCase();

                    grid<?=$uuid?>.updateConfig({
                        server: {
                            url: `<?=$apiUrl?>?search=${searchTerm}`,
                            then: data => {
                                // Identifica os �ndices das colunas com label vazio ou null
                                const columnsToRemove = data.columns
                                    .map((col, index) => (col.label === "" || col.label === null ? index : -1))
                                    .filter(index => index !== -1);

                                // Filtra os dados
                                const filteredData = data.data.map(item => {
                                    const values = Object.values(item);

                                    // Remove os valores correspondentes aos �ndices das colunas removidas
                                    return values.filter((_, index) => !columnsToRemove.includes(index));
                                });

                                document.getElementById('auxiliarySpansServerSideLegacy<?=$uuid?>').innerHTML = "";

                                data.data.forEach((cellValue, colIndex) => {
                                    const values = Object.values(cellValue);
                                    const hiddenSpan = document.createElement('span');
                                    hiddenSpan.classList.add('hidden');

                                    values.forEach((cellValue, colIndex) => {
                                        const column = data.columns[colIndex];
                                        hiddenSpan.setAttribute(`data-${column.name}`, cellValue);
                                        document.getElementById('auxiliarySpansServerSideLegacy<?=$uuid?>').appendChild(hiddenSpan);
                                    });
                                });

                                // Adiciona o �cone para cada linha
                                return filteredData.map(item => {
                                    <?php if($rowClick): ?>
                                        item.unshift(gridjs.html('<i data-uuid="<?=$uuid?>" class="icon circle-8653X"></i>'));
                                    <?php endif; ?>
                                    return item;
                                });
                            },
                            total: data => data.meta.total,
                        }
                    }).forceRender();
                }
            <?php endif; ?>

            /**
             * Atualiza a tabela Grid.js com a URL de busca modificada conforme 
             * o termo de pesquisa e a coluna selecionada.
             * 
             * A URL da requisi��o ser� composta pelo termo de pesquisa e pela coluna
             * escolhida, aplicando os filtros no servidor para obter os dados.
             */
            function updateGridWithColumnSearch() 
            {
                const column = selectColumnSearch.value;
                const searchTerm = inputColumnSearch.value.toLowerCase();

                document.getElementById("warningLabelColumn<?=$uuid?>").classList.add("hidden-gridjs-component");
                document.getElementById("warningLabelSearch<?=$uuid?>").classList.add("hidden-gridjs-component");
                inputColumnSearch.classList.remove("border-color-red-gridjs-component");
                selectColumnSearch.classList.remove("border-color-red-gridjs-component");

                if (column != "" && searchTerm != "") {
                    grid<?=$uuid?>.updateConfig({
                        server: {
                            url: `<?=$apiUrl?>?search=${column}:${searchTerm}`,
                            then: data => {
                                // Identifica os �ndices das colunas com label vazio ou null
                                const columnsToRemove = data.columns
                                    .map((col, index) => (col.label === "" || col.label === null ? index : -1))
                                    .filter(index => index !== -1);

                                // Filtra os dados
                                const filteredData = data.data.map(item => {
                                    const values = Object.values(item);

                                    // Remove os valores correspondentes aos �ndices das colunas removidas
                                    return values.filter((_, index) => !columnsToRemove.includes(index));
                                });

                                document.getElementById('auxiliarySpansServerSideLegacy<?=$uuid?>').innerHTML = "";

                                data.data.forEach((cellValue, colIndex) => {
                                    const values = Object.values(cellValue);
                                    const hiddenSpan = document.createElement('span');
                                    hiddenSpan.classList.add('hidden');

                                    values.forEach((cellValue, colIndex) => {
                                        const column = data.columns[colIndex];
                                        hiddenSpan.setAttribute(`data-${column.name}`, cellValue);
                                        document.getElementById('auxiliarySpansServerSideLegacy<?=$uuid?>').appendChild(hiddenSpan);
                                    });
                                });

                                // Adiciona o �cone para cada linha
                                return filteredData.map(item => {
                                    <?php if($rowClick): ?>
                                        item.unshift(gridjs.html('<i data-uuid="<?=$uuid?>" class="icon circle-8653X"></i>'));
                                    <?php endif; ?>
                                    return item;
                                });
                            },
                            total: data => data.meta.total,
                        }
                    }).forceRender();
                } else if(column == "" && searchTerm != "") {
                    document.getElementById("warningLabelColumn<?=$uuid?>").classList.remove("hidden-gridjs-component");
                    selectColumnSearch.classList.add("border-color-red-gridjs-component");
                } else if(column != "" && searchTerm == "") {
                    document.getElementById("warningLabelSearch<?=$uuid?>").classList.remove("hidden-gridjs-component");
                    inputColumnSearch.classList.add("border-color-red-gridjs-component");
                } else {
                    console.log("Excecao de rotina");
                }
            }

            /**
             * A fun��o `updateGridColumnsWithFilters` � respons�vel por atualizar a configura��o das colunas no Grid.js com base nas colunas selecionadas.
             * Ela verifica se o usu�rio selecionou ao menos uma coluna e atualiza a URL da API para refletir as colunas escolhidas.
             *
             * - A fun��o captura todos os checkboxes selecionados e obt�m seus valores.
             * - Se nenhum checkbox for selecionado, exibe um alerta.
             * - Caso contr�rio, a configura��o do grid � atualizada com as colunas selecionadas e os dados s�o recuperados da API.
             */
            function updateGridColumnsWithFilters()
            {
                const checkboxes = document.querySelectorAll("input[name='gridColumns<?=$uuid?>']:checked");
                const selectedUriColumns = Array.from(checkboxes).map(checkbox => checkbox.value).join(','); 
                const selectedDataColumns = Array.from(checkboxes).map(checkbox => checkbox.getAttribute('data-label'));

                // const labels = request.columns
                //     .map(column => column.label)
                //     .filter(label => label && label.trim() !== "");

                <?php if($rowClick): ?>
                    selectedDataColumns.unshift({name: gridjs.html('<i onclick="toggleSelection()" class="icon m-8653X"></i>'), sort: false});
                <?php endif; ?>

                if (checkboxes.length === 0) {
                    alert("Por favor, selecione ao menos uma coluna.");
                    return;
                } else {
                    grid<?=$uuid?>.updateConfig({
                        columns: selectedDataColumns,
                        server: {
                            url: `<?=$apiUrl?>?only=${selectedUriColumns}`,
                            then: data => {
                                // Identifica os �ndices das colunas com label vazio ou null
                                const columnsToRemove = data.columns
                                    .map((col, index) => (col.label === "" || col.label === null ? index : -1))
                                    .filter(index => index !== -1);

                                // Filtra os dados
                                const filteredData = data.data.map(item => {
                                    const values = Object.values(item);

                                    // Remove os valores correspondentes aos �ndices das colunas removidas
                                    return values.filter((_, index) => !columnsToRemove.includes(index));
                                });

                                document.getElementById('auxiliarySpansServerSideLegacy<?=$uuid?>').innerHTML = "";

                                data.data.forEach((cellValue, colIndex) => {
                                    const values = Object.values(cellValue);
                                    const hiddenSpan = document.createElement('span');
                                    hiddenSpan.classList.add('hidden');

                                    values.forEach((cellValue, colIndex) => {
                                        const column = data.columns[colIndex];
                                        hiddenSpan.setAttribute(`data-${column.name}`, cellValue);
                                        document.getElementById('auxiliarySpansServerSideLegacy<?=$uuid?>').appendChild(hiddenSpan);
                                    });
                                });

                                // Adiciona o �cone para cada linha
                                return filteredData.map(item => {
                                    <?php if($rowClick): ?>
                                        item.unshift(gridjs.html('<i data-uuid="<?=$uuid?>" class="icon circle-8653X"></i>'));
                                    <?php endif; ?>
                                    return item;
                                });
                            },
                            total: data => data.meta.total,
                        }
                    }).forceRender();
        
                    setTimeout(() => {
                        grid<?=$uuid?>.forceRender();
                    }, 50);
                }
            }

            /*
             * Adiciona um ouvinte de evento ("input") ao elemento `inputSearch`.
             * 
             * - Similar ao exemplo anterior, o evento "input" aciona a execu��o da fun��o 
             *   `updateGridWithSearch`, mas com a limita��o do debounce para evitar chamadas 
             *   excessivas (tamb�m configurado para 300ms).
             */
            <?php if($search): ?>
                inputSearch.addEventListener("input", debounce<?=$uuid?>(updateGridWithSearch, 300));
            <?php endif; ?>
            
            /*
             * Adiciona um ouvinte de evento ("input") ao elemento `inputColumnSearch`.
             * 
             * - Quando o usu�rio digita no campo, o evento "input" � acionado.
             * - A fun��o `debounce$uuid` � chamada para limitar a frequ�ncia com que
             *   a fun��o `updateGridWithColumnSearch` � executada (neste caso, a cada 300ms).
             * - O `$uuid` � uma vari�vel PHP interpolada, garantindo um identificador �nico 
             *   para fun��es relacionadas.
             */
            inputColumnSearch.addEventListener("input", debounce<?=$uuid?>(updateGridWithColumnSearch, 300));

            /*
             * Adiciona um ouvinte de evento ("change") ao elemento `selectColumnSearch`.
             * 
             * - Diferentemente dos eventos anteriores, este evento � acionado sempre que o usu�rio
             *   muda a sele��o em um elemento `<select>`.
             * - A fun��o `updateGridWithColumnSearch` � chamada diretamente, sem debounce.
             */
            selectColumnSearch.addEventListener("change", updateGridWithColumnSearch);

            /*
             * Seleciona todos os elementos `input` do tipo checkbox com o atributo `name` igual a 'gridColumns$uuid'.
             * 
             * - `document.querySelectorAll` retorna todos os elementos que correspondem ao seletor fornecido.
             * - O seletor `"input[name='gridColumns$uuid']"` � usado para encontrar os checkboxes que t�m o nome 'gridColumns' seguido de um identificador �nico `$uuid`.
             * - O `$uuid` � uma vari�vel PHP interpolada que garante que o nome do elemento seja �nico, evitando conflitos em p�ginas din�micas.
             */
            document.querySelectorAll("input[name='gridColumns<?=$uuid?>']").forEach(checkbox => {
                checkbox.addEventListener("change", updateGridColumnsWithFilters);
            });
        <?php endif; ?>
    });
</script>

<?php
    // Limpar vari�veis ap�s uso
    unset($id, $class, $label, $columns, $apiUrl, $perPage, $search, $sort, $fixedHeader);
?>
