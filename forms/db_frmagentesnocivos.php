<?

require_once("dbforms/db_classesgenericas.php");
$cliframe_alterar_excluir = new cl_iframe_alterar_excluir;
$clrotulo = new rotulocampo;

$clrotulo->label("rh01_regist");
$clagentesnocivos->rotulo->label();

if (isset($opcaoal)) {
    $db_opcao = 33;
    $db_botao = false;
} else if (isset($opcao) && $opcao == "alterar") {
    $db_botao = true;
    $db_opcao = 2;
} else if (isset($opcao) && $opcao == "excluir") {
    $db_opcao = 3;
    $db_botao = true;
} else {
    $db_opcao = 1;
    $db_botao = true;
    if (isset($novo) || isset($alterar) || isset($excluir) || (isset($incluir) && $sqlerro == false)) {
        $rh232_agente = "";
        $rh232_icdexposicao = "";
        $rh232_tipoavaliacao = "";
        $rh232_icdexposicao = "";
        $rh232_ltolerancia = "";
        $rh232_unidade = "";
        $rh232_tecnicamed = "";
        $rh232_epc = "";
        $rh232_epceficaz = "";
        $rh232_epi = "";
        $rh232_epieficaz = "";
        $rh232_epicertificado = "";
        $rh232_epidescricao = "";
        $rh232_epiporinviabilidade = "";
        $rh232_epiobscondicoes = "";
        $rh232_epiobsuso = "";
        $rh232_epiobsprazo = "";
        $rh232_obsperiodicidade = "";
        $rh232_obshigienizacao = "";
    }
}

if (isset($rh232_regist) && $rh232_regist != "") {
    $oInfoAmbiente  = new cl_infoambiente();
    $sSql           = $oInfoAmbiente->sql_query_file($rh232_regist);
    $rsInfoAmbiente = $oInfoAmbiente->sql_record($sSql);
    db_fieldsmemory($rsInfoAmbiente, 0);
}
?>
<form name="form1" method="post" action="" enctype="multipart/form-data">

    <br>
    <table align="center" border="0" cellspacing="4" cellpadding="0">
        <tr>
            <td nowrap title="<?= @$Trh01_regist ?>">
                <?= @$Lrh01_regist; ?>
            </td>
            <td nowrap colspan='10'>
                <?php
                db_input('rh232_sequencial', 10, $Irh232_sequencial, true, 'hidden', 3, "");
                db_input('rh232_regist', 6, $Irh232_regist, true, 'text', 3, "");
                ?>
            </td>
        </tr>
    </table>

    <table border='0'>
        <tr>
            <td>
                <fieldset>
                    <legend align="left"><b>AGENTES NOCIVOS</b></legend>
                    <table>
                        <tr>
                            <td nowrap title="<?php echo $Trh232_agente; ?>">
                                <? db_ancora($Lrh232_agente, 'js_pesquisaAgente(true)', $db_opcao); ?>
                            </td>
                            <td nowrap colspan="1">
                                <?
                                db_input('rh232_agente', 2, null, true, 'hidden', 3);
                                ?>
                                <?
                                db_input('rh233_codigo', 10, $Irh233_codigo, true, 'text', $db_opcao, "onchange='js_pesquisaAgente(false);'");
                                ?>
                                <?
                                db_input('rh233_descricao', 100, $Irh233_descricao, true, 'text', 3);
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <strong>Tipo de Avalia��o: </strong>
                            </td>
                            <td nowrap>
                                <?
                                $aTipoAval = array(
                                    "0" => "Selecione",
                                    "1" => "Crit�rio Quantitativo",
                                    "4" => "Crit�rio Qualitativo"
                                );

                                db_select("rh232_tipoavaliacao", $aTipoAval, true, $db_opcao, "");
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <strong>Intensidade, Concentra��o ou Dose da Exposi��o: </strong>
                            </td>
                            <td nowrap>
                                <?
                                db_input('rh232_icdexposicao', 10, $Irh232_icdexposicao, false, 'text', $db_opcao, "", "", "", "", 10);
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <strong>Limite de Toler�ncia: </strong>
                            </td>
                            <td nowrap>
                                <?
                                db_input('rh232_ltolerancia', 30, $Irh232_ltolerancia, false, 'text', $db_opcao, "", "", "", "", 30);
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <strong>Unidade de Medida: </strong>
                            </td>
                            <td nowrap>
                                <?
                                $aUnid = array(
                                    "0" => "Selecione",
                                    "1" => "1 - Dose di�ria de ru�do",
                                    "2" => "2 - Decibel linear (dB (linear))",
                                    "3" => "3 - Decibel (C) (dB(C))",
                                    "4" => "4 - Decibel (A) (dB(A))",
                                    "5" => "5 - Metro por segundo ao quadrado (m/s2)",
                                    "6" => "6 - Metro por segundo elevado a 1,75 (m/s1,75)",
                                    "7" => "7 - Parte de vapor ou g�s por milh�o de partes de ar contaminado (ppm)",
                                    "8" => "8 - Miligrama por metro c�bico de ar (mg/m3)",
                                    "9" => "9 - Fibra por cent�metro c�bico (f/cm3)",
                                    "10" => "10 - Grau Celsius (�C)",
                                    "11" => "11 - Metro por segundo (m/s)",
                                    "12" => "12 - Porcentual",
                                    "13" => "13 - Lux (lx)",
                                    "14" => "14 - Unidade formadora de col�nias por metro c�bico (ufc/m3)",
                                    "15" => "15 - Dose di�ria",
                                    "16" => "16 - Dose mensal",
                                    "17" => "17 - Dose trimestral",
                                    "18" => "18 - Dose anual",
                                    "19" => "19 - Watt por metro quadrado (W/m2)",
                                    "20" => "20 - Amp�re por metro (A/m)",
                                    "21" => "21 - Militesla (mT)",
                                    "22" => "22 - Microtesla (?T)",
                                    "23" => "23 - Miliamp�re (mA)",
                                    "24" => "24 - Quilovolt por metro (kV/m)",
                                    "25" => "25 - Volt por metro (V/m)",
                                    "26" => "26 - Joule por metro quadrado (J/m2)",
                                    "27" => "27 - Milijoule por cent�metro quadrado (mJ/cm2)",
                                    "28" => "28 - Milisievert (mSv)",
                                    "29" => "29 - Milh�o de part�culas por dec�metro c�bico (mppdc)",
                                    "30" => "30 - Umidade relativa do ar (UR (%))"
                                );

                                db_select("rh232_unidade", $aUnid, true, $db_opcao, "");
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <strong>T�cnica de Medi��o: </strong>
                            </td>
                            <td nowrap>
                                <?
                                db_input("rh232_tecnicamed", 40, $Irh232_tecnicamed, false, 'text', $db_opcao, "", "", "", "", 40);
                                ?>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </td>
        </tr>
        <tr>
            <td>
                <fieldset>
                    <legend align="left"><b>INFORMA��ES RELATIVAS A EPC E EPI</b></legend>
                    <table>
                        <tr>

                        </tr>
                        <tr>
                            <td>
                                <strong>Utiliza Equipamentos de Prote��o Coletiva: </strong>
                            </td>
                            <td nowrap>
                                <?
                                $aEpc = array(
                                    '0' => '0 - N�o se aplica',
                                    '1' => '1 - N�o implementa',
                                    '2' => '2 - Implementa'
                                );
                                db_select('rh232_epc', $aEpc, true, $db_opcao, "");
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <strong>Os EPCs s�o eficazes: </strong>
                            </td>
                            <td nowrap>
                                <?
                                $aEpcEficaz = array(
                                    '0' => 'Selecione',
                                    '1' => 'N�o',
                                    '2' => 'Sim'
                                );
                                db_select('rh232_epceficaz', $aEpcEficaz, true, $db_opcao, "");
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <strong>Utiliza Equipamentos de Prote��o Individual: </strong>
                            </td>
                            <td nowrap>
                                <?
                                $aEpi = array(
                                    '0' => '0 - N�o se aplica',
                                    '1' => '1 - N�o utilizado',
                                    '2' => '2 - Utilizado'
                                );
                                db_select("rh232_epi", $aEpi, true, $db_opcao, "");
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <strong>Os EPIs s�o eficazes: </strong>
                            </td>
                            <td nowrap>
                                <?
                                $aEpiEficaz = array(
                                    '0' => 'Selecione',
                                    '1' => 'N�o',
                                    '2' => 'Sim'
                                );
                                db_select("rh232_epieficaz", $aEpiEficaz, true, $db_opcao, "");
                                ?>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </td>
        </tr>
        <tr>
            <td>
                <fieldset>
                    <legend align="left"><b>EQUIPAMENTOS DE PROTE��O INDIVIDUAL - EPI</b></legend>
                    <table>
                        <tr>
                            <td>
                                <strong>Certificado de Aprova��o do EPI: </strong>
                            </td>
                            <td nowrap>
                                <?
                                db_textarea('rh232_epicertificado', 3, 100, 0, true, 'text', $db_opcao, "", "", "", 255);
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <strong>Descri��o EPI: </strong>
                            </td>
                            <td nowrap>
                                <?
                                db_textarea('rh232_epidescricao', 5, 100, 0, true, 'text', $db_opcao, "", "", "", 999);
                                ?>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </td>
        </tr>
        <tr>
            <td>
                <fieldset>
                    <legend align="left"><b>CONFIGURA��ES DO EPI</b></legend>
                    <table>
                        <tr>
                            <td>
                                <strong>Foi tentada a implementa��o de medidas de prote��o coletiva, optando-se pelo EPI por inviabilidade: </strong>
                            </td>
                            <td>
                                <?
                                $aInviabilidade = array(
                                    '0' => 'Selecione',
                                    '1' => 'N�o',
                                    '2' => 'Sim'
                                );
                                db_select("rh232_epiporinviabilidade", $aInviabilidade, true, $db_opcao, "");
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <strong>Foram observadas as condi��es de funcionamento do EPI ao longo do tempo, conforme especifica��o: </strong>
                            </td>
                            <td nowrap>
                                <?
                                $aObsCondicoes = array(
                                    '0' => 'Selecione',
                                    '1' => 'N�o',
                                    '2' => 'Sim'
                                );
                                db_select("rh232_epiobscondicoes", $aObsCondicoes, true, $db_opcao, "");
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <strong>Foi observado o uso ininterrupto do EPI ao longo do tempo, conforme especifica��o t�cnica: </strong>
                            </td>
                            <td nowrap>
                                <?
                                $aObsTempo = array(
                                    '0' => 'Selecione',
                                    '1' => 'N�o',
                                    '2' => 'Sim'
                                );
                                db_select("rh232_epiobsuso", $aObsTempo, true, $db_opcao, "");
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <strong>Foi observado o prazo de validade do CA no momento da compra do EPI: </strong>
                            </td>
                            <td nowrap>
                                <?
                                $aObsValidade = array(
                                    '0' => 'Selecione',
                                    '1' => 'N�o',
                                    '2' => 'Sim'
                                );
                                db_select("rh232_epiobsprazo", $aObsValidade, true, $db_opcao, "");
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <strong> � observada a periodicidade de troca definida pelo fabricante nacional ou importador e/ou programas ambientais, <br />comprovada mediante recibo assinado pelo usu�rio em �poca pr�pria: </strong>
                            </td>
                            <td nowrap>
                                <?
                                $aObsPeriodo = array(
                                    '0' => 'Selecione',
                                    '1' => 'N�o',
                                    '2' => 'Sim'
                                );
                                db_select("rh232_obsperiodicidade", $aObsPeriodo, true, $db_opcao, "");
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <strong> � observada a higieniza��o conforme orienta��o do fabricante nacional ou importador: </strong>
                            </td>
                            <td nowrap>
                                <?
                                $aObsHigiene = array(
                                    '0' => 'Selecione',
                                    '1' => 'N�o',
                                    '2' => 'Sim'
                                );
                                db_select("rh232_obshigienizacao", $aObsHigiene, true, $db_opcao, "");
                                ?>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <td colspan="2" align="center">
                <input name="<?= ($db_opcao == 1 ? "incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "alterar" : "excluir")) ?>" type="submit" id="db_opcao" value="<?= ($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir")) ?>" <?= ($db_botao == false ? "disabled" : "") ?>>
                <input name="novo" type="button" id="cancelar" value="Novo" onclick="js_cancelar();" <?= ($db_opcao == 1 || isset($db_opcaoal) ? "style='visibility:hidden;'" : "") ?>>
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <td valign="top" align="center">
                <?
                $chavepri = array("rh232_sequencial" => @$rh232_sequencial);
                $cliframe_alterar_excluir->chavepri = $chavepri;
                $sSqlIframe = $clagentesnocivos->sql_query_agente("", "*", "rh232_sequencial", "rh232_regist = {$rh232_regist}");
                $cliframe_alterar_excluir->sql     = $sSqlIframe;
                $cliframe_alterar_excluir->campos  = "rh232_sequencial,rh232_regist,rh233_codigo,rh232_tipoavaliacao,rh232_icdexposicao,rh232_ltolerancia,rh232_unidade,rh232_tecnicamed";
                $cliframe_alterar_excluir->legenda = "DADOS LAN�ADOS";
                $cliframe_alterar_excluir->iframe_height = "200";
                $cliframe_alterar_excluir->iframe_width = "1000";
                $cliframe_alterar_excluir->iframe_alterar_excluir($db_opcao);
                ?>
            </td>
        </tr>
    </table>
    </table>
    </fieldset>
    </center>
</form>
<script>
    function js_pesquisa() {
        js_OpenJanelaIframe('', 'db_iframe_agentesnocivos', 'func_agentesnocivos.php?funcao_js=parent.js_preenchepesquisa|rh232_regist', 'Pesquisa', true);
    }

    function js_preenchepesquisa(chave) {
        db_iframe_agentesnocivos.hide();
        <?
        if ($db_opcao != 1) {
            echo "  location.href = '" . basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]) . "?chavepesquisa='+chave";
        }
        ?>
    }

    function js_pesquisaAgente(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('', 'db_iframe_rhagente', 'func_rhagente.php?funcao_js=parent.js_mostraagente1|rh233_codigo|rh233_descricao|rh233_sequencial', 'Pesquisa', true, 0);
        } else {
            if (document.form1.rh233_codigo.value != '') {
                js_OpenJanelaIframe('', 'db_iframe_rhagente', 'func_rhagente.php?pesquisa_chave=' + document.form1.rh233_codigo.value + '&funcao_js=parent.js_mostraagente', 'Pesquisa', false, 0);
            } else {
                document.form1.rh233_descricao.value = '';
            }
        }
    }

    function js_mostraagente(chave, chave1, erro) {
        document.form1.rh232_agente.value = chave1;
        document.form1.rh233_descricao.value = chave;
        if (erro == true) {
            document.form1.rh233_codigo.focus();
            document.form1.rh233_codigo.value = '';
        }
    }

    function js_mostraagente1(chave1, chave2, chave3) {
        document.form1.rh233_codigo.value = chave1;
        document.form1.rh233_descricao.value = chave2;
        document.form1.rh232_agente.value = chave3;
        db_iframe_rhagente.hide();
    }

    function js_cancelar() {
        var opcao = document.createElement("input");
        opcao.setAttribute("type", "hidden");
        opcao.setAttribute("name", "novo");
        opcao.setAttribute("value", "true");
        document.form1.appendChild(opcao);
        document.form1.submit();
    }
</script>