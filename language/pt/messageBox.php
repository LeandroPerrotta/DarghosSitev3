<?php
$boxMessage = array();

$boxMessage['ERROR'] = "Erro!";
$boxMessage['SUCCESS'] = "Sucesso!";

$boxMessage['INCOMPLETE_FORM'] = "Por favor, preencha todos os campos do(s) formulário(s) corretamente.";
$boxMessage['NEED_ACCEPT_PRIVACY_POLICY'] = "Para jogar em nosso servidor é necessario aceitar nossos termos de politica de privacidade.";
$boxMessage['ACCOUNT_NAME_INCORRECT_SIZE'] = "O nome de conta informado deve possuir entre 5 e 25 caracteres.";
$boxMessage['EMAIL_ALREADY_IN_USE'] = "O e-mail informado já esta em uso por outro usuario em nosso servidor.";
$boxMessage['ACCOUNT_NAME_ALREADY_IN_USE'] = "O nome de conta informado já está em uso por outro usuario em nosso servidor.";
$boxMessage['INVALID_EMAIL'] = "Este não é um e-mail valido.";
$boxMessage['FAIL_SEND_EMAIL'] = "Não foi possivel enviar o email de validação de sua conta. Por favor, tente novamente mais tarde.";
$boxMessage['INCORRECT_ACCOUNT_NAME_OR_PASSWORD'] = "O nome da conta ou senha estão incorretos.";
$boxMessage['NONE_ACCOUNT_FOR_THIS_EMAIL'] = "Não existe nenhuma conta registrada neste e-mail em nosso banco de dados.";
$boxMessage['CHARACTER_NOT_FROM_EMAIL'] = "Este personagem não pertence a conta do e-mail informado.";
$boxMessage['CHARACTER_NAME_NEEDED'] = "Para efetuar esta operação é necessario informar ao menos o nome de um personagem da conta que deseja recuperar.";
$boxMessage['CHARACTER_NOT_FOUND'] = "Este personagem não existe em nosso banco de dados.";
$boxMessage['CHANGE_PASSWORD_KEY_NOT_FOUND'] = "Chave de recuperação inexistente.";
$boxMessage['ACCOUNT_NOT_HAVE_SECRET_KEY'] = "Esta conta não possui uma chave secreta configurada.";
$boxMessage['OPERATION_BLOCKED_STATE'] = "Está operação está bloqueada, por favor aguarde 24 horas após a ultima tentativa.";
$boxMessage['INCORRECT_SECRET_KEY'] = "A chave secreta informada para mudança de e-mail de sua conta está incorreta. Por movitos de segurança você só poderá efetuar 3 tentativas desta operação, após as 3 tentativas este recurso estará bloqueado por 24 horas.";
$boxMessage['MANY_ATTEMPS_OPERATION_BLOCKED'] = "Você efetuou três tentativas erradas desta operação, por motivos de segurança este recurso estará bloqueado pelas proximas 24 horas.";
$boxMessage['CURRENT_PASSWORD_FAIL'] = "A confirmação da senha atual falhou.";
$boxMessage['NEW_PASSWORD_FAIL'] = "Confirmação da nova senha falhou.";
$boxMessage['NEW_AND_CURRENT_PASSWORD_CAN_NOT_SAME'] = "A nova senha deve ser diferente da senha atual.";
$boxMessage['NEW_PASSWORD_INCORRECT_LENGHT'] = "A nova senhas deve possuir de 5 a 20 caracteres.";
$boxMessage['CONFIRMATION_PASSWORD_FAIL'] = "A senha informada para validar esta operação está incorreta.";
$boxMessage['ACCOUNT_ALREADY_HAVE_CHANGE_EMAIL_REQUEST'] = "Está conta já possui uma mudança de e-mail agendada.";

$boxMessage['SUCCESS.REGISTER'] = "		
	<p>Parabens, sua conta foi criada com sucesso!</p>
	<p>Sua senha e outras informações foram enviadas em uma mensagem a seu e-mail cadastrado.</p>
	<p>Tenha um bom jogo!</p>";

$boxMessage['SUCCESS.ACCOUNT_NAME_SENDED'] = "		
	<p>Caro jogador, o número de sua conta foi enviado ao seu e-mail com sucesso!</p>
	<p>Este e-mail tem um prazo de até 24 horas para chegar, porem geralmente chega dentro de alguns instantes.</p>
	<p>Tenha um bom jogo!</p>";

$boxMessage['SUCCESS.PASSWORD_SENDED'] = "		
	<p>Caro jogador, uma mensagem foi enviada ao seu e-mail com as informações necessarias para você gerar uma nova senha para sua conta!</p>
	<p>Este e-mail tem um prazo de até 24 horas para chegar, porem geralmente chega dentro de alguns instantes.</p>
	<p>Tenha um bom jogo!</p>";

$boxMessage['SUCCESS.BOTH_SENDED'] = "		
	<p>Caro jogador, uma mensagem foi enviada ao seu e-mail com o número de sua conta e as informações necessarias para você gerar uma nova senha para sua conta!</p>
	<p>Este e-mail tem um prazo de até 24 horas para chegar, porem geralmente chega dentro de alguns instantes.</p>
	<p>Tenha um bom jogo!</p>";

$boxMessage['SUCCESS.RECOVERY_PASSWORD'] = "		
	<p>Caro jogador, a nova senha de sua conta foi enviada ao seu e-mail com sucesso!</p>
	<p>Este e-mail tem um prazo de até 24 horas para chegar, porem geralmente chega dentro de alguns instantes.</p>
	<p>Tenha um bom jogo!</p>";

$boxMessage['SUCCESS.CHANGE_EMAIL_USING_RECOVERY_KEY'] = "		
	<p>Caro jogador,</p>
	<p>O e-mail registrado em sua conta foi modificado ultilizando sua chave secreta com sucesso!</p>
	<p>Tenha um bom jogo!</p>";

$boxMessage['SUCCESS.PASSWORD_CHANGED'] = "		
	<p>A sua senha foi modificada com sucesso!</p>";

$boxMessage['SUCCESS.CHANGE_EMAIL'] = "		
	<p>Caro jogador,</p>
	<p>A mudança de email de sua conta foi agendada com sucesso.</p>
	<p>Tenha um bom jogo!</p>";

?>