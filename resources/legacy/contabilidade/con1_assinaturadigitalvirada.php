<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2012  DBselller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

require_once("libs/db_stdlib.php");
require_once("libs/db_conecta.php");
require_once("libs/db_sessoes.php");
require_once("libs/db_utils.php");
require_once("libs/db_app.utils.php");
require_once("libs/db_usuariosonline.php");
require_once("dbforms/db_funcoes.php");
require_once("dbforms/db_classesgenericas.php");
require_once("libs/JSON.php");

$iAnoDestino = db_getsession("DB_anousu") + 1;
?>
<html>

<head>
  <title>Contass Gest�o e Tecnologia Ltda - P&aacute;gina Inicial</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
  <?
  db_app::load("
    scripts.js,
    strings.js,
    prototype.js
  ");
  db_app::load("estilos.css");
  ?>
</head>

<body class="body-default">
  <div class="container">
    <form name='form1'>
      <fieldset class="form-container">
        <legend><b>Duplicar Assinantes</b></legend>
        <table width="100%">
          <tr>
            <td nowrap="nowrap"><b>Ano Destino:</b></td>
            <td>
              <?db_input("iAnoDestino", 10, null, true, 'text', 3);?>
            </td>
          </tr>
        </table>
      </fieldset>
      <input style="margin-top:10px;" type="button" name="btnProcessar" id="btnProcessar" value="Processar" onclick="processar()" />
    </form>
  </div>
</body>
<?
db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit"));
?>

</html>
<script>
  function processar() {
    js_divCarregando('Aguarde', 'div_aguarde');
    var params = {
      sExecuta: 'duplicarAssinantes'
    };

    var request = new Ajax.Request('con1_assinaturadigital.RPC.php', {
      method: 'post',
      parameters: 'json=' + Object.toJSON(params),
      onComplete: function(e) {
        var oRetorno = JSON.parse(e.responseText);
        js_removeObj('div_aguarde');
        alert(oRetorno.sMensagem.urlDecode());
      }
    });
  }
</script>