<?

$campos = 'distinct contabancaria.db83_sequencial,
                    contabancaria.db83_descricao,
                    bancoagencia.db89_codagencia,
                    bancoagencia.db89_digito,
                    contabancaria.db83_conta,
                    contabancaria.db83_dvconta,
                    case
                        when contabancaria.db83_tipoconta = 1 then \'Conta Corrente\'
                    when contabancaria.db83_tipoconta = 2 then \'Conta Poupan�a\'
                    when contabancaria.db83_tipoconta = 3 then \'Conta Aplica��o\'
                    when contabancaria.db83_tipoconta = 4 then \'Conta Sal�rio\'
                    end as "dl_Descricao Tipo de Conta",
                    c61_reduz,
                    c61_codtce "dl_Cod. TCE",
                    c61_codigo
               ';
              

?>