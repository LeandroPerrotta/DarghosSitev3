<?php
use \Core\Configs;
class Lang_Messages
{		
	static protected $messages = array();
	
	static function Load(e_LangMsg $e_Msg)
	{		
		self::$messages[$e_Msg->ERROR] = "Erro!";
		self::$messages[$e_Msg->SUCCESS] = "Sucesso!";
		self::$messages[$e_Msg->FILL_FORM] = "Preencha todos os campos corretamente!";
		self::$messages[$e_Msg->FILL_NUMERIC_FIELDS] = "Alguns campos deste formulario devem ser preenchidos apenas com caracteres numericos!";
		self::$messages[$e_Msg->PRIVACY_POLICY] = "Para jogar em nosso servidor é necessario concordar com nossa politica de privacidade.";
		self::$messages[$e_Msg->WRONG_EMAIL] = "O endereço de e-mail informado é incorreto ou invalido.";
		self::$messages[$e_Msg->WRONG_PASSWORD] = "A senha informada está incorreta.";
		self::$messages[$e_Msg->ACCOUNT_NAME_WRONG_SIZE] = "O nome de conta deve possuir entre 5 e 25 caracteres.";
		self::$messages[$e_Msg->ACCOUNT_EMAIL_ALREADY_USED] = "Este endereço de e-mail já esta em uso por outra conta.";
		self::$messages[$e_Msg->ACCOUNT_NAME_ALREADY_USED] = "Este nome de conta já esta em uso por outra conta.";
		self::$messages[$e_Msg->FAIL_SEND_EMAIL] = "Ouve uma falha ao enviar o email. Erro reportado ao administrador.";
		self::$messages[$e_Msg->FAIL_LOGIN] = "O nome de conta ou senha informados estão incorretos.";
		self::$messages[$e_Msg->RECOVERY_UNKNOWN_EMAIL] = "Não existe nenhuma conta em nosso banco registrada para este endereço de e-mail.";
		self::$messages[$e_Msg->RECOVERY_UNKNOWN_CHARACTER] = "Este personagem não pertence a conta do endereço de e-mail informado.";
		self::$messages[$e_Msg->RECOVERY_WRONG_KEY] = "Chave de recuperação de conta inexistente ou inválida.";
		self::$messages[$e_Msg->CAN_NOT_VALIDATE_EMAIL] = "Não foi possivel validar o e-mail para esta conta, tente novamente.";
		self::$messages[$e_Msg->OPERATION_REQUIRE_VALIDATED_EMAIL] = "Esta operação requer que sua conta já possua um endereço de e-mail registrado e valido.";
		self::$messages[$e_Msg->ACCOUNT_ALREADY_VALIDATED_EMAIL] = "A sua conta já possui um e-mail validado.";
		self::$messages[$e_Msg->RECOVERY_WRONG_SECRET_KEY] = "A chave secreta informada para mudança de e-mail de sua conta está incorreta. Por movitos de segurançaa você só poder efetuar 3 tentativas desta operação, após as 3 tentativas este recurso estará bloqueado por 24 horas.";
		self::$messages[$e_Msg->RECOVERY_FILL_CHARACTER_NAME] = "Para efetuar esta operação é necessario informar ao menos o nome de um personagem da conta que deseja recuperar.";
		self::$messages[$e_Msg->RECOVERY_DISABLED] = "Esta conta não possui uma chave de recuperação registrada e portanto este recurso não pode ser utilizado para ela.";
		self::$messages[$e_Msg->OPERATION_ARE_BLOCKED] = "Esta operação está bloqueada, por favor aguarde 24 horas após a ultima tentativa.";
		self::$messages[$e_Msg->OPERATION_HAS_BLOCKED] = "Você efetuou três tentativas erradas desta operação, por motivos de segurança este recurso estará bloqueado pelas proximas 24 horas.";
		self::$messages[$e_Msg->CHANGEPASS_WRONG_NEWPASS_CONFIRM] = "Confirmação da nova senha falhou.";
		self::$messages[$e_Msg->CHANGEPASS_SAME_PASSWORD] = "A nova senha deve ser diferente da senha atual.";
		self::$messages[$e_Msg->CHANGEPASS_WRONG_NEWPASS_LENGHT] = "A nova senhas deve possuir de 5 a 20 caracteres.";
		self::$messages[$e_Msg->CHANGEEMAIL_ALREADY_HAVE_REQUEST] = "Esta conta já possui uma mudança de e-mail agendada.";
		self::$messages[$e_Msg->CHANGEEMAIL_NOTHING] = "A sua conta não possui uma mudança de endereço de e-mail agendada para ser cancelada.";
		self::$messages[$e_Msg->CHARACTER_WRONG] = "Este personagem não existe em nosso banco de dados.";
		self::$messages[$e_Msg->WRONG_NAME] = "Este nome possui formatação ilegal ou está reservado. Tente novamente com outro nome.";
		self::$messages[$e_Msg->CHARACTER_NAME_ALREADY_USED] = "O nome escolhido já esta em uso por outro personagem no jogo. Escolha outro nome.";
		self::$messages[$e_Msg->CHARACTER_NOT_TO_DELETION] = "Este personagem não está marcado para ser deletado.";
		self::$messages[$e_Msg->CHARACTER_ALREADY_TO_DELETE] = "Este personagem já está marcado para ser deletado.";
		self::$messages[$e_Msg->CHARACTER_MUST_AWARE_INSTANT_DELETION] = "Você precisa confirmar que tem certeza que o seu personagem será deletado instantânamente caso ele seja level " . Configs::Get(Configs::eConf()->INSTANT_DELETION_MAX_LEVEL)." ou menos.";
		self::$messages[$e_Msg->CHARACTER_MUST_BE_RED_OR_BLACK_SKULL] = "Para esta operação é necessario que o seu personagem possua uma caveira vermelha ou preta.";
		self::$messages[$e_Msg->CHARACTER_NOT_FROM_YOUR_ACCOUNT] = "Este personagem não existe ou não pertence a sua conta e portanto esta operação não pode ser concluida.";
		self::$messages[$e_Msg->ACCOUNT_CANNOT_HAVE_MORE_CHARACTERS] = "A sua conta já possui 10 personagens criados. Para criar um novo personagem é necessario se desfazer de um já existente deletando-o.";
		self::$messages[$e_Msg->CHANGEINFOS_WRONG_SIZE] = "Os campos Nome Real e Localidade devem possuir no maximo 25 caracteres enquanto Website deve conter no maximo 50 caracteres.";
		self::$messages[$e_Msg->SECRETKEY_ALREADY_EXISTS] = "Esta conta já possui uma chave secreta configurada.";
		self::$messages[$e_Msg->SECRETKEY_WRONG_SIZE] = "A sua chave secreta deve possuir entre 6 e 15 caracteres e seu lembrete entre 5 e 25 caracteres.";
		self::$messages[$e_Msg->SECRETKEY_MUST_BY_UNLIKE_REMINDER] = "Para sua segurança, o seu lembrete e sua chave de recuperação não podem ser iguais.";
		self::$messages[$e_Msg->ACCOUNT_SETNAME_SAME_ID] = "O nome de sua conta deve ser diferente do seu antigo numero.";
		self::$messages[$e_Msg->CONTR_TERMS] = "Para aceitar uma contribuição é necessario estar de acordo com todas clausulas e termos de nosso contrato de serviço.";
		self::$messages[$e_Msg->CONTR_ORDER_NUMBER_DUPLICATED] = "Ouve uma falha ao obter um numero para seu pedido. Por favor tente novamente, se o problema persistir aguarde algumas horas.";
		self::$messages[$e_Msg->ACCOUNT_HAS_NO_ORDERS] = "Não existe nenhum pedido gerado por sua conta.";
		self::$messages[$e_Msg->REPORT] = "Um erro desconhecido ocorreu ou você não tem permissão para visualizar esta pagina. Um log foi enviado ao administrador. Por favor, tente novamente mais tarde.";
		self::$messages[$e_Msg->NEED_LOGIN] = "Para visualizar esta pagina é necessário primeiro acessar sua conta.";
		self::$messages[$e_Msg->NEED_PREMIUM] = "Para visualizar esta pagina é necessário possuir uma conta premium.";
		self::$messages[$e_Msg->PAGE_NOT_FOUND] = "Esta pagina não existe ou está em processo de construção.";
		self::$messages[$e_Msg->SQL_INJECTION] = "Detectada tentativa de inserção de codigo malicioso não autorizado. A tentativa ilegal do USER_IP: @v1@ foi reportado aos Administradores para investigação.";
		self::$messages[$e_Msg->GUILD_NOT_FOUND] = "A guilda @v1@ não existe. Verifique e tente novamente.";
		self::$messages[$e_Msg->GUILD_CHARACTER_NOT_INVITED] = "O personagem @v1@ não está convidado para nenhuma guilda.";
		self::$messages[$e_Msg->GUILD_NAME_ALREADY_USED] = "O nome @v1@ já está sendo usado por outra guilda. Escolha outro nome.";
		self::$messages[$e_Msg->GUILD_ONLY_ONE_VICE_PER_ACCOUNT] = "Somente é permitido possuir um lider ou vice-lider por conta.";
		self::$messages[$e_Msg->CHARACTER_ALREADY_MEMBER_GUILD] = "Este personagem já é membro de uma guild. Para criar uma nova guilda é necessario primeiro deixar a guilda atual.";
		self::$messages[$e_Msg->CHARACTER_COMMENT_WRONG_SIZE] = "O comentário de seu personagem não deve possuir mais de 500 caracteres.";
		self::$messages[$e_Msg->CHARACTER_CHANGE_THING_CONFIRM] = "Para modificar o nome ou sexo de seu personagem é necessario aceitar e estar ciente destas mudanças e os seus custos.";
		self::$messages[$e_Msg->CHARACTER_REMOVE_SKILLS_CONFIRM] = "Para a skull de seu personagem é necessario aceitar e estar ciente destas mudanças e os seus custos.";
		self::$messages[$e_Msg->CHARACTER_NEED_OFFLINE] = "Para efetuar esta operação é necessario que você faça um \"log-out\" no jogo.";
		self::$messages[$e_Msg->CHARACTER_CHANGENAME_COST] = "Você não possui os @v1@ dias de conta premium necessarios para modificar o nome de seu personagem.";
		self::$messages[$e_Msg->CHARACTER_CHANGESEX_COST] = "Você não possui os @v1@ dias de conta premium necessarios para modificar o sexo de seu personagem.";
		self::$messages[$e_Msg->ITEMSHOP_OLD_PURCHASE] = "Você deve fazer um \"log-in\" no jogo para receber sua antiga compra em nosso item shop antes de efetuar uma nova compra.";
		self::$messages[$e_Msg->ITEMSHOP_COST] = "Você não possui os @v1@ dias de conta premium necessarios para obter este item.";
		self::$messages[$e_Msg->ITEMSHOP_REQUIRE_DAYS] = "Este item tem um custo de @v1@ dias de conta premium, no entanto, você precisa ter mais de @v2@ dias de conta premium para comprar-lo.";
		self::$messages[$e_Msg->GUILD_NEED_NO_MEMBERS_DISBAND] = "A sua guilda ainda possui membros ativos. Para desmanchar uma guilda é necessario só existir o líder da guilda.";
		self::$messages[$e_Msg->GUILD_COMMENT_SIZE] = "O comentário de sua guilda não deve exceder 500 caracteres.";
		self::$messages[$e_Msg->GUILD_LOGO_SIZE] = "A imagem do logotipo de sua guilda não deve exceder 100 kb.";
		self::$messages[$e_Msg->GUILD_FILE_WRONG] = "Este arquivo não possui um formato e valido. Por favor, tente outro arquivo.";
		self::$messages[$e_Msg->GUILD_LOGO_DIMENSION_WRONG] = "As dimenções da imagem de logotipo para sua guilda deve ser exatamente de 100 pixel de largura por 100 pixels altura.";
		self::$messages[$e_Msg->GUILD_LOGO_EXTENSION_WRONG] = "O logotipo de sua guilda deve ser no formato GIF, JPG ou PNG.";
		self::$messages[$e_Msg->GUILD_INVITE_LIMIT] = "Só é permitido o envio de até 20 convites por vez.";
		self::$messages[$e_Msg->GUILD_INVITE_ALREADY_MEMBER] = "Os seguintes personagens já são membros de outras guildas e não podem ser convidados: @v1@";
		self::$messages[$e_Msg->GUILD_INVITE_ALREADY_INVITED] = "Os seguintes personagens já estão convidados para uma guilda e não podem ser convidados: @v1@";
		self::$messages[$e_Msg->GUILD_INVITE_CHARACTER_NOT_FOUNDS] = "Os seguintes personagens não existem em nosso banco de dados e não podem ser convidados: @v1@";
		self::$messages[$e_Msg->GUILD_INVITE_CHARACTER_NOT_SAME_WORLD] = "Os seguintes personagens não podem ser convidados por não pertencerem ao mesmo mundo de sua guild: @v1@";
		self::$messages[$e_Msg->GUILD_INVITE_CANCEL] = "O convite para o personagem @v1@ se juntar a sua guilda foi cancelado com sucesso!";
		self::$messages[$e_Msg->GUILD_IS_NOT_MEMBER] = "O personagem @v1@ não faz parte da guilda @v2@ e portanto esta operação não pode ser efetuada.";
		self::$messages[$e_Msg->GUILD_RANK_ONLY_PREMIUM] = "Apenas personagens com uma conta premium podem ser promovidos para este nível.";
		self::$messages[$e_Msg->GUILD_PERMISSION] = "Você não possui permissão sulficiente para efetuar esta operação. Você deve solicitar isto a um membro com rank superior ao seu.";
		self::$messages[$e_Msg->GUILD_NOT_SAME_WORLD] = "Esta operaçao requer que ambas guildas pertençam ao mesmo mundo.";
		self::$messages[$e_Msg->GUILD_TITLE_SIZE] = "O titulo do membro deve possuir entre 3 e 15 caracteres.";
		self::$messages[$e_Msg->GUILD_ACCOUNT_ALREADY_IS_HIGH_RANK] = "O personagem escolhido não pode ser promovido a este nível. Somente é permitido possuir 1 lider ou vice-lider por conta.";
		self::$messages[$e_Msg->GUILD_RANK_WRONG_ORDER] = "Os ranks estão em sequencia fora de ordem.";
		self::$messages[$e_Msg->GUILD_RANK_WRONG_SIZE] = "Os ranks devem possuir no maximo 35 caracteres.";
		self::$messages[$e_Msg->GUILD_RANK_MIMINUM_NEEDED] = "É necessario existir ao menos 3 ranks para sua guilda.";
		self::$messages[$e_Msg->GUILD_RANK_IN_USE] = "Um ou mais ranks que foram removidos de sua guild estão em uso (possuem membros). Para remover um rank é necessario que o mesmo não possua nenhum membro.";
		self::$messages[$e_Msg->GUILD_CANNOT_LEAVE] = "Você não pode abandonar a guilda @v1@ pois este personagem é o lider. Caso queira encerrar a guilda use a opção Desmanchar.";
		self::$messages[$e_Msg->GUILD_WAR_NO_HAVE_OPPONENTS] = "Não existe nenhuma guilda formada em nosso servidor portanto é impossivel iniciar uma guerra.";
		self::$messages[$e_Msg->GUILD_NEED_TO_BE_FORMED] = "Para efetuar esta operação em sua guilda é necessario que esta já esteja formada.";
		self::$messages[$e_Msg->GUILD_WAR_WRONG_FRAG_LIMIT] = "O limite de frags de uma guerra deve ser de 10 a 1000.";
		self::$messages[$e_Msg->GUILD_WAR_WRONG_END_DATE] = "O limite de tempo de uma guerra deve ser entre 7 e 360 dias.";
		self::$messages[$e_Msg->GUILD_WAR_WRONG_FEE] = "O pagamento da guilda derrotada ou rendida deve ser entre 0 e 100000000 gold coins .";
		self::$messages[$e_Msg->GUILD_WAR_WRONG_COMMENT_LENGTH] = "O comentario de declaração de guerra não deve exceder 500 caracteres.";
		self::$messages[$e_Msg->GUILD_WAR_REJECTED] = "A declaração de guerra da guilda @v1@ foi rejeitada com sucesso. Sua guilda não entrará mais nesta guerra.";
		self::$messages[$e_Msg->GUILD_IS_ON_WAR] = "A guilda @v1@ esta em guerra com outra(s) guildas, portanto, certas operações na guilda não estão disponiveis, como abandonar guilda, convidar novos membros, aceitar convites, remover membros. Tente novamente quando a guerra estiver encerrada.";
		self::$messages[$e_Msg->GUILD_BALANCE_TOO_LOW] = "O balanço no banco de sua guilda está muito baixo.";
		self::$messages[$e_Msg->GUILD_WAR_ALREADY] = "A sua guilda já declarou ou está em guerra contra a guilda @v1@. Você poderá declarar guerra contra esta guilda quando a guerra atual estiver terminada.";
		self::$messages[$e_Msg->FORUM_ACCOUNT_NOT_HAVE_USER] = "Caro jogador, sua conta ainda não efetuou o cadastro para poder usar o forum do Darghos para poder ler topicos, responder enquetes e etc. Para criar o seu usuario, clique <a href='?ref=forum.register'>aqui</a>.";
		self::$messages[$e_Msg->FORUM_ACCOUNT_NOT_HAVE_CHARACTERS] = "Caro jogador, para ultilizar os recursos de nosso Forum é necessário possui um personagem na conta ao menos com level 20 ou superior.";
		self::$messages[$e_Msg->FORUM_POLL_ALREADY_VOTED] = "Desculpe, você já votou para está enquete e somente é permitido um voto por úsuario.";
		self::$messages[$e_Msg->FORUM_POLL_ONLY_FOR_PREMIUM] = "Desculpe, mas está enquete só pode receber votos de usuarios que possuam uma conta premium.";
		self::$messages[$e_Msg->FORUM_POLL_NEED_MIN_LEVEL] = "Desculpe, mas para votar nesta enquete é preciso possuir na sua conta ao menos um personagem com nivel @v1@ ou superior.";
		self::$messages[$e_Msg->FORUM_POLL_TIME_EXPIRED] = "Desculpe, mas esta enquete já está encerrada.";
		self::$messages[$e_Msg->FORUM_POST_TOO_LONG] = "Desculpe, mas o seu post não pode exceder 2048 caracteres.";
		self::$messages[$e_Msg->STAMINA_NOT_HAVE_PREMDAYS] = "Você não possui tempo sulficiente em sua conta premium para recuperar esta quantidade de stamina.";
		self::$messages[$e_Msg->STAMINA_VALUE_WRONG] = "Você deve selecionar corretamente a quantidade de stamina que deseja recuperar.";
		self::$messages[$e_Msg->DARGHOSPOINTS_NEED_ACCEPT_TERMS] = "Para adquirir seus Darghos Points é necessario concordar com nossos termos de uso.";
		self::$messages[$e_Msg->MONSTER_NOT_FOUND] = "O monstro @v1@ não existe em nosso servidor.";
		self::$messages[$e_Msg->OPERATION_NEED_PREMDAYS] = "A sua conta não possui os @v1@ premdays disponiveis necessarios para concluir esta operação.";
		self::$messages[$e_Msg->ACCOUNT_CHANGENAME_SUCCESS] = "O nome da sua conta foi modificado para @v1@ com sucesso!";
	
		self::$messages[$e_Msg->ACCOUNT_REGISTERED] = "
			<p>Parabens, sua conta foi criada com sucesso!</p>
		";
		
		self::$messages[$e_Msg->ACCOUNT_INFOS_SEND] = "
			<p>Sua senha e outras informações foram enviadas em uma mensagem a seu e-mail cadastrado.</p>
			<p>Tenha um bom jogo!</p>
		";
		
		self::$messages[$e_Msg->ACCOUNT_PASSWORD_IS] = "
			<p>Sua senha é <font size='5'><b>@v1@</b></font>.</p>
			<p>Tenha um bom jogo!</p>
		";
		
		self::$messages[$e_Msg->RECOVERY_ACCOUNT_NAME_SEND] = "
			<p>Caro jogador, o número de sua conta foi enviado ao seu e-mail com sucesso!</p>
			<p>Este e-mail tem um prazo de até 24 horas para chegar, porem geralmente chega dentro de alguns instantes.</p>
			<p>Tenha um bom jogo!</p>
		";

		self::$messages[$e_Msg->RECOVERY_PASSWORD_SEND] = "
			<p>Caro jogador, uma mensagem foi enviada ao seu e-mail com as informações necessarias para você gerar uma nova senha para sua conta!</p>
			<p>Este e-mail tem um prazo de até 24 horas para chegar, porem geralmente chega dentro de alguns instantes.</p>
			<p>Tenha um bom jogo!</p>
		";		

		self::$messages[$e_Msg->RECOVERY_NEWPASS_SEND] = "
			<p>Caro jogador, a nova senha de sua conta foi enviada ao seu e-mail com sucesso!</p>
			<p>Este e-mail tem um prazo de até 24 horas para chegar, porem geralmente chega dentro de alguns instantes.</p>
			<p>Tenha um bom jogo!</p>
		";			

		self::$messages[$e_Msg->RECOVERY_BOTH_SEND] = "
			<p>Caro jogador, uma mensagem foi enviada ao seu e-mail com o número de sua conta e as informações necessarias para você gerar uma nova senha para sua conta!</p>
			<p>Este e-mail tem um prazo de até 24 horas para chegar, porem geralmente chega dentro de alguns instantes.</p>
			<p>Tenha um bom jogo!</p>
		";			

		self::$messages[$e_Msg->RECOVERY_EMAIL_CHANGED] = "
			<p>Caro jogador,</p>
			<p>O e-mail registrado em sua conta foi modificado ultilizando sua chave secreta com sucesso!</p>
			<p>Tenha um bom jogo!</p>
		";

		self::$messages[$e_Msg->ACCOUNT_PASSWORD_CHANGED] = "
			<p>A sua senha foi modificada com sucesso!</p>
		";			

		self::$messages[$e_Msg->CHANGEEMAIL_SCHEDULED] = "
			<p>Caro jogador,</p>
			<p>A mudança de email de sua conta foi agendada com sucesso.</p>
			<p>Tenha um bom jogo!</p>
		";			
		
		self::$messages[$e_Msg->CHANGEEMAIL_CANCELED] = "
			<p>Caro jogador,</p>
			<p>A mudança de endereço de email de sua conta foi cancelada com sucesso! Nenhuma mudança de endereço de e-mail acontecera!</p>
			<p>Tenha um bom jogo!</p>
		";			
		
		self::$messages[$e_Msg->CHANGEINFOS_SUCCESS] = "
			<p>Caro jogador,</p>
			<p>A mudança dasinformações de sua conta foram efetuadas com sucesso!</p>
			<p>Tenha um bom jogo!</p>
		";
		
		self::$messages[$e_Msg->SECRETKEY_SUCCESS] = "
			<p>Caro jogador,</p>
			<p>A chave secreta <span style='font-weight: bold; size: 20px'>@v1@</span> foi configurada com sucesso em sua conta!</p>
			<p><span style='color: red; font-weight: bold; size: 20px;'>IMPORTANTE:</span></p>
			<p>A baixo segue uma lista de coisas sobre a segurança de sua Chave Secreta que você <b>precisa</b> saber para que ela sempre esteja segura:</p>
			<p>
				• <b>Esta chave por razões de segurança é <span style='size: 15px'>IMODIFICAVEL</span>, ou seja, não pode ser modificada <u>NUNCA</u>!</b><br>
				• <b>Por isto, se você contar-la ou deixar outra(s) pessoa(s) saber-la, esta conta não será mais apénas sua, sera <u>SEMPRE</u> de você e desta(s) pessoa(s)!</b><br>
				• <b>É recomendavel que você anote esta chave em um papel e guarde-a em um local seguro e que a preserve!</b><br>
				• <b>Jamais guarde esta chave em qualquer lugar de seu computador como em arquivos do tipo texto (.txt)!</b><br>
				• <b>Se você um dia for hackeado, e for usar-la para recuperar sua conta, certifique-se que primeiro seu computador não está infectado (NESTAS OCASIÕES É ALTAMENTE RECOMENDAVEL A FORMATAÇÃO DO DISCO), pois se não, o hacker também irá saber de sua chave!</b><br>
			</p>			
			<p>Tenha um bom jogo!</p>
		";			
		
		self::$messages[$e_Msg->SECRETKEY_CUSTOM_SUCCESS] = "
			<p>Caro jogador,</p>
			<p>A sua chave secreta <span style='font-weight: bold; size: 20px'>@v1@</span> com o lembrete <span style='font-decoration: italic; size: 12px'>@v2@</span> foi configurada com sucesso em sua conta!</p>
			<p><span style='color: red; font-weight: bold; size: 20px;'>IMPORTANTE:</span></p>
			<p>A baixo segue uma lista de coisas sobre a segurança de sua Chave Secreta que você <b>precisa</b> saber para que ela sempre esteja segura:</p>
			<p>
				• <b>Esta chave por razões de segurança é <span style='size: 15px'>IMODIFICAVEL</span>, ou seja, não pode ser modificada <u>NUNCA</u>!</b><br>
				• <b>Por isto, se você contar-la ou deixar outra(s) pessoa(s) saber-la, esta conta não será mais apénas sua, sera <u>SEMPRE</u> de você e desta(s) pessoa(s)!</b><br>
				• <b>É recomendavel que você anote esta chave em um papel e guarde-a em um local seguro e que a preserve!</b><br>
				• <b>Jamais guarde esta chave em qualquer lugar de seu computador como em arquivos do tipo texto (.txt)!</b><br>
				• <b>Se você um dia for hackeado, e for usar-la para recuperar sua conta, certifique-se que primeiro seu computador não está infectado (NESTAS OCASIÕES É ALTAMENTE RECOMENDAVEL A FORMATAÇÃO DO DISCO), pois se não, o hacker também irá saber de sua chave!</b><br>
			</p>
			<p>Tenha um bom jogo!</p>
		";			
		
		self::$messages[$e_Msg->ACCOUNT_SETNAME_SUCCESS] = "
			<p>Caro jogador,</p>
			<p>A sua conta agora possui um Nome configurado corretamente!</p>
			<p>Tenha um bom jogo!</p>
		";			
		
		self::$messages[$e_Msg->CHARACTER_CREATED] = "
			<p>O personagem @v1@ foi criado com sucesso!</p>
			<p>Para começar a jogar clique <a href='?ref=general.howplay'>aqui</a> e siga as instruções.</p>
			<p>A sua aventura se inicia em Island of Peace, esta ilha funciona como um aprendizado com vários tipos de criaturas, NPCs, quests, academia de treino e muito mais, alem que na ilha não é possivel atacar outros jogadores. Quando você atingir o nivel 60 estara preparado para sair da ilha usando o Barco e explorar aos outros continentes do Darghos, aonde é possivel atacar outros personagens. É importante informar que você pode sair a qualquer momento da ilha independente do nivel, porem, uma vez fora, é impossivel retornar a ilha.</p>
			<p>Tenha uma boa jornada!</p>
		";			
		
		self::$messages[$e_Msg->CHARACTER_NO_MORE_DELETED] = "
			<p>Caro jogador,</p>
			<p>A exclusão do seu personagem @v1!@ foi cancelada! Este personagem não será mais deletado.</p>
			<p>Tenha um bom jogo!</p>
		";			
		
		self::$messages[$e_Msg->CONTR_ACTIVATED] = "
			<p>Caro jogador,</p>
			<p>A sua contribuição foi ativada com sucesso!</p>
			<p>Agora sua conta já possui status de Contra Premium, o que lhe permitira muitas novas possibilidades dentro do @v1@!</p>
			<p>Agradeçemos a preferencia e obrigado por contribuir conosco!</p>
			<p>Tenha um bom jogo!<br>Equipe UltraxSoft.</p>
		";			
		
		self::$messages[$e_Msg->CONTR_ORDER_CREATED] = "
			<fieldset>			
				
				<p><h3>Pedido Gerado com sucesso!</h3></p>
				
				<p>Caro jogador, o seu pedido foi gerado com sucesso! Anote abaixo o numero de seu pedido para consulta ou qualquer eventual problema.</p>
				<p>Clicando no botão Finalizar abaixo você será direcionado ao site do PagSeguro aonde você irá terminar o processo efetuando o pagamento de sua contribuição.</p>
				
				<p>Numero do Pedido de sua Contribuição: <h3>@v1@</h3></p>
				
				<p>@v2@</p>
			</fieldset>
		";		

		self::$messages[$e_Msg->GUILD_JOIN] = "
			<p>Caro jogador,</p>
			<p>O personagem @v1@ é um novo membro da guild @v2@!</p>
			<p>Tenha um bom jogo!</p>
		";				
	
		self::$messages[$e_Msg->GUILD_JOIN_REJECT] = "
			<p>Caro jogador,</p>
			<p>Você recusou o convite da guild @v1@ para seu persongem @v2@ com sucesso!</p>
			<p>Tenha um bom jogo!</p>
		";				
		
		self::$messages[$e_Msg->GUILD_CREATED] = "
			<p>A guilda @v1@ foi criada com sucesso!</p>
			<p>Inicialmente a sua guilda está em estagio de formação, e você deve nomear ao minimo @v2@ vice-lideres em @v3@ dias para que sua guilda seja formada! Caso contrario a guilda será automaticamente desmanchada.</p>
			<p>Tenha uma boa jornada!</p>
		";				
		
		self::$messages[$e_Msg->CHARACTER_DELETION_SCHEDULED] = "
			<p>Caro jogador,</p>
			<p>Foi agendado com sucesso a exclusão de seu personagem @v1@ para o dia @v2@!</p>
			<p>Tenha um bom jogo!</p>
		";			
			
		self::$messages[$e_Msg->CHARACTER_DELETED] = "
			<p>Caro jogador,</p>
			<p>O seu personagem @v1@ foi deletado com sucesso e agora não existe mais!</p>
			<p>Tenha um bom jogo!</p>
		";				
		
		self::$messages[$e_Msg->CHARACTER_COMMENT_CHANGED] = "
			<p>Caro jogador,</p>
			<p>A mudança das informações de seu personagem foi efetuada com exito!</p>
			<p>Tenha um bom jogo!</p>
		";			
			
		self::$messages[$e_Msg->CHARACTER_NAME_CHANGED] = "
			<p>Caro jogador,</p>
			<p>A mudança de nome de seu personagem de @v1@ para @v2@ foi efetuada com sucesso!</p>
			<p>Tenha um bom jogo!</p>
		";						self::$messages[$e_Msg->CHARACTER_CREATED] = "
			<p>O personagem @v1@ foi criado com sucesso!</p>
			<p>Para começar a jogar clique <a href='?ref=general.howplay'>aqui</a> e siga as instruções.</p>
			<p>A sua aventura se inicia em Island of Peace, esta ilha funciona como um aprendizado com vários tipos de criaturas, NPCs, quests, academia de treino e muito mais, alem que na ilha não é possivel atacar outros jogadores. Quando você atingir o nivel 60 estara preparado para sair da ilha usando o Barco e explorar aos outros continentes do Darghos, aonde é possivel atacar outros personagens. É importante informar que você pode sair a qualquer momento da ilha independente do nivel, porem, uma vez fora, é impossivel retornar a ilha.</p>
			<p>Tenha uma boa jornada!</p>
		";	
			
		self::$messages[$e_Msg->CHARACTER_SEX_CHANGED] = "
			<p>Caro jogador,</p>
			<p>A mudança de sexo de seu personagem @v1@ foi efetuada com sucesso!</p>
			<p>Tenha um bom jogo!</p>
		";				
			
		self::$messages[$e_Msg->ITEMSHOP_PURCHASE_SUCCESS] = "
			<p>Caro jogador,</p>
			<p>A compra de @v1@x @v2@ por @v3@ dias de sua conta premium foi efetuada com sucesso!</p>
			<p>Você o receberá dentro de um present box em seu proximo log-in no jogo.</p>
			<p>Tenha um bom jogo!</p>
		";				
			
		self::$messages[$e_Msg->GUILD_DISBANDED] = "
			<p>Caro jogador,</p>
			<p>A guilda @v1@ foi desmanchada com sucesso e não existe mais no servidor.</p>
			<p>Tenha um bom jogo!</p>
		";				
			
		self::$messages[$e_Msg->GUILD_DESC_CHANGED] = "
			<p>Caro jogador,</p>
			<p>As mudanças de descrição e exibição de sua guilda foram efetuadas com sucesso!</p>
			<p>Tenha um bom jogo!</p>
		";				
			
		self::$messages[$e_Msg->GUILD_INVITEDS] = "
			<p>Caro jogador,</p>
			<p>Todos jogadores da lista foram convidados para sua guilda com sucesso!</p>
			<p>O seu convite estará na pagina principal da conta do jogador aonde ele deve aceitar ou rejeitar o convite!</p>
			<p>Tenha um bom jogo!</p>
		";				
			
		self::$messages[$e_Msg->GUILD_LEAVE] = "
			<p>Caro jogador,</p>
			<p>O personagem @v1@ não faz mais parte da guilda @v2@!</p>
			<p>Tenha um bom jogo!</p>
		";				
			
		self::$messages[$e_Msg->GUILD_MEMBER_EDITED] = "
			<p>Caro jogador,</p>
			<p>O membro @v1@ da guild @v2@ foi modificado com sucesso!</p>
			<p>Tenha um bom jogo!</p>
		";				
			
		self::$messages[$e_Msg->GUILD_PASSLEADERSHIP] = "
			<p>Caro jogador,</p>
			<p>A guilda @v1@ teve a liderana transferida de @v2@ para @v3@ com sucesso!</p>
			<p>Tenha um bom jogo!</p>
		";				
			
		self::$messages[$e_Msg->GUILD_RANKS_EDITED] = "
			<p>Caro jogador,</p>
			<p>As alterações nos ranks de sua guild foi efetuado com sucesso!</p>
			<p>Tenha um bom jogo!</p>
		";				
			
		self::$messages[$e_Msg->GUILD_WAR_DECLARED] = "
			<p>Caro jogador,</p>
			<p>A declaração de guerra de sua guilda @v1@ a guilda inimiga @v2@ foi efetuado com sucesso!</p>
			<p>Os termos da guerra são:</p>
			<p>
				Limite de mortes: @v3@<br>
				Limite de tempo: @v4@<br>
				Nosso pagamento por rendição ou derrota: @v5@<br>
				Pagamento de nosso oponente por rendição ou derrota: @v6@
			</p>
			<p>O lider da guilda oponente irá analisar a proposta de guerra e poderá aceitar-la, rejeita-la ou fazer uma contra proposta alterando os termos. Se esta proposta não for finalizada em 7 dias ela será automaticamente cancelada.</p>
			<p>Tenha um bom jogo!</p>
		";
		
		self::$messages[$e_Msg->GUILD_WAR_ACCEPTED] = "
			<p>Caro jogador,</p>
			<p>A sua guilda @v1@ aceitou a declaração de guerra da guilda @v2@ com sucesso sob os termos acordados!</p>
			<p>No proximo server save será sera reservado de sua conta do banco o preço da rendição acordado, assim como do lider da guilda oponente.</p>
			<p>A guerra sera encerrada dentro dos termos acordados ou ainda pode ser declarada uma rendição pelo painel de guerras de sua guilda.</p>
			<p>Desejamos uma boa sorte a sua guilda nesta guerra e um bom jogo!</p>
		";				
		
		self::$messages[$e_Msg->GUILD_WAR_NEGOTIATE_SEND] = "
			<p>Caro jogador,</p>
			<p>Uma nova proposta foi enviada ao lider da outra guilda para ele analisar, ele poderá aceita-la, rejeitar-la ou ainda enviar uma nova proposta de volta a você.</p>
			<p>Caso a proposta seja aceita a guerra será iniciada no proximo server save, caso os gold coins de rendição possam ser reservados das contas de banco de cada um dos lideres das duas guildas.</p>
			<p>Desejamos uma boa sorte a sua guilda nesta guerra e um bom jogo!</p>
		";				
		
		self::$messages[$e_Msg->FORUM_ACCOUNT_REGISTERED] = "
			<p>Caro jogador,</p>
			<p>Sua conta foi registrada no forum com sucesso!</p>
			<p>Agora você já pode participar de nossos topicos e enquetes! Lembre-se de seguir as regras de uso do Forum.</p>
		";				
		
		self::$messages[$e_Msg->FORUM_POLL_VOTE_DONE] = "
			<p>Caro jogador,</p>
			<p>O seu voto foi computado em nosso banco de dados com sucesso!</p>
			<p>Obrigado por participar!</p>
		";				
		
		self::$messages[$e_Msg->FORUM_POST_SENT] = "
			<p>Caro jogador,</p>
			<p>O seu post foi enviado com sucesso para este topico!</p>
			<p>Obrigado por participar!</p>
		";				
		
		self::$messages[$e_Msg->FORUM_USER_BANNISHED] = "
			<p>Caro usuario,</p>
			<p>Seu usuario está bloqueado de participar de nossos topicos e enquetes desde o dia @v1@ com duração <b>@v2@</b>.</p>
			<p>Este bloqueio foi aplicado por @v3@ pelo seguinte motivo:<br><i>@v4@</i></p>
		";				
		
		self::$messages[$e_Msg->STAMINA_SUCCESSFULY] = "
			<p>Caro usuario,</p>
			<p>A recuperação de stamina do personagem de sua conta @v1@ foi concluida com sucesso! Agora ele possui <b>@v2@</b> horas de stamina!</p>
			<p>Tenha um bom jogo! Agora mais descançado!</p>
		";
		
		self::$messages[$e_Msg->REMOVE_SKULL_SUCCESSFULY] = "
			<p>Caro usuario,</p>
			<p>A caveira de seu personagem @v1@ foi removida com sucesso ao custo de @v2@ dos dias de sua conta premium!</p>
			<p>Evite mortes injustificadas para não obter a caveira novamente!</p>
			<p>Tenha um bom jogo!</p>
		";		

		self::$messages[$e_Msg->VALIDATE_EMAIL_SUCCESSFULY] = "
			<p>Caro usuario,</p>
			<p>O endereço de e-mail <b>@v1@</b> foi validado com sucesso em sua conta!</p>
			<p>Tenha um bom jogo!</p>
		";		
		
		self::$messages[$e_Msg->ACCOUNT_VALIDATING_EMAIL_SEND] = "
			<p>Caro usuario,</p>
			<p>Foi enviado a mensagem de validação para o endereço de e-mail <b>@v1@</b> com sucesso!</p>
			<p>Tenha um bom jogo!</p>
		";		
	}
}
?>