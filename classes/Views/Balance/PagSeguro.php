<?php 
namespace Views\Balance;
class PagSeguro extends \Core\Views
{
    function __construct($data){
        parent::__construct($data);
        
        \Core\Main::sendMessageBox("Pagamento em processo!", "O seu pedido foi concluido com sucesso!<br><br>O saldo adquirido será adicionado em sua conta automaticamente após a confirmação do pagamento. Você pode acompanhar o status de seu pedido na seção <a href='?ref=balance.history'>Histórico</a>.<br><br>Tempo para confirmação de pagamento:<br><br> ► Boleto bancário: 1 a 3 dias úteis<br> ► Cartão de credito/debito: Alguns minutos<br><br>Obrigado por colaborar com o Darghos!");
    }
}
?>