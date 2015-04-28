<?php 
namespace Controllers;

use Framework\Account;

use Core\Configs;
use Core\Consts;
use Views\Balance as BalanceViews;

if (!defined('TOKEN')) define ('TOKEN', '930706B292F94B46B8C06DDA5E8EF2B5');

class Balance
{
    static function retorno_automatico
    (
            $VendedorEmail, $TransacaoID, $Referencia, $TipoFrete,
            $ValorFrete, $Anotacao, $DataTransacao, $TipoPagamento,
            $StatusTransacao, $CliNome, $CliEmail, $CliEndereco,
            $CliNumero, $CliComplemento, $CliBairro, $CliCidade,
            $CliEstado, $CliCEP, $CliTelefone, $produtos, $NumItens, $key
    )
    {
    
        if($StatusTransacao == "Completo")
            exit();
    
        $contr = new \Framework\Contribute();
        if($contr->load($Referencia)){
    
            $prod = $produtos[0];
    
            if($StatusTransacao == "Aprovado" && $contr->status == 0){
                if(($prod["ProdValor"] * 100) == $contr->balance){
                    $contr->status = \t_PaymentStatus::Finished;
                    
                    if(!$contr->auth)
                        $contr->auth = $TransacaoID;
    
                    $account = new Account();
                    $account->load($contr->account_id);
                    
                    if($contr->balance >= 2000)
                        $account->addBalance($contr->balance * 1.5);
                    else 
                        $account->addBalance($contr->balance);
                    
                    $account->save();
                }
                else{
                    $contr->status = \t_PaymentStatus::Canceled;
                }
            }
            elseif($StatusTransacao == "Aguardando Pagto"){
    
                $contr->auth = $TransacaoID;
            }
            elseif($StatusTransacao == "Em Análise"){
                $contr->status = \t_PaymentStatus::Analysis;
            }
            elseif($StatusTransacao == "Cancelado"){
                $contr->status = \t_PaymentStatus::Canceled;
                /*
                 * Aqui seria bom checar o status anterior, pois se o pagamento já estava Aprovado e passou para cancelado provavelmente ouve alguma sacanagem
                * Se for o caso, seria ideal aplicar um ban na conta...
                * */
            }
    
            $contr->save();
        }
    }    
    
    function PagSeguro(){	
        include_once('libs/pagseguro/retorno.class.php');
		
        if(!$_POST){
            $data = array();
            new BalanceViews\PagSeguro($data);
            
            return true;
        }
        else{
            exit();
        }
    }
}
?>
