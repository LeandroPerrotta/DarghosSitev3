<?php
class Lang_Messages
{
	static protected $messages = array();
	
	static function Load()
	{
		self::$messages[LMSG_ERROR] = "Erro!";
		self::$messages[LMSG_SUCCESS] = "Sucesso!";
		self::$messages[LMSG_FILL_FORM] = "Preencha todos os campos corretamente!";
		self::$messages[LMSG_FILL_NUMERIC_FIELDS] = "Alguns campos deste formulario devem ser preenchidos apenas com caracteres numericos!";
		self::$messages[LMSG_PRIVACY_POLICY] = "Para jogar em nosso servidor é necessario concordar com nossa politica de privacidade.";
		self::$messages[LMSG_WRONG_EMAIL] = "O endereço de e-mail informado é incorreto ou invalido.";
		self::$messages[LMSG_WRONG_PASSWORD] = "A senha informada está incorreta.";
		self::$messages[LMSG_ACCOUNT_NAME_WRONG_SIZE] = "O nome de conta deve possuir entre 5 e 25 caracteres.";
		self::$messages[LMSG_ACCOUNT_EMAIL_ALREADY_USED] = "Este endereço de e-mail já esta em uso por outra conta.";
		self::$messages[LMSG_ACCOUNT_NAME_ALREADY_USED] = "Este nome de conta já esta em uso por outra conta.";
		self::$messages[LMSG_FAIL_SEND_EMAIL] = "Ouve uma falha ao enviar o email. Erro reportado ao administrador.";
		self::$messages[LMSG_FAIL_LOGIN] = "O nome de conta ou senha informados estão incorretos.";
		self::$messages[LMSG_RECOVERY_UNKNOWN_EMAIL] = "Não existe nenhuma conta em nosso banco registrada para este endereço de e-mail.";
		self::$messages[LMSG_RECOVERY_UNKNOWN_CHARACTER] = "Este personagem não pertence a conta do endereço de e-mail informado.";
		self::$messages[LMSG_RECOVERY_WRONG_KEY] = "Chave de recuperação de conta inexistente ou inválida.";
		self::$messages[LMSG_RECOVERY_WRONG_SECRET_KEY] = "A chave secreta informada para mudança de e-mail de sua conta está incorreta. Por movitos de segurançaa você só poder efetuar 3 tentativas desta operação, após as 3 tentativas este recurso estará bloqueado por 24 horas.";
		self::$messages[LMSG_RECOVERY_FILL_CHARACTER_NAME] = "Para efetuar esta operação é necessario informar ao menos o nome de um personagem da conta que deseja recuperar.";
		self::$messages[LMSG_RECOVERY_DISABLED] = "Esta conta não possui uma chave de recuperação registrada e portanto este recurso não pode ser utilizado para ela.";
		self::$messages[LMSG_OPERATION_ARE_BLOCKED] = "Esta operação está bloqueada, por favor aguarde 24 horas após a ultima tentativa.";
		self::$messages[LMSG_OPERATION_HAS_BLOCKED] = "Você efetuou três tentativas erradas desta operação, por motivos de segurança este recurso estará bloqueado pelas proximas 24 horas.";
		self::$messages[LMSG_CHANGEPASS_WRONG_NEWPASS_CONFIRM] = "Confirmação da nova senha falhou.";
		self::$messages[LMSG_CHANGEPASS_SAME_PASSWORD] = "A nova senha deve ser diferente da senha atual.";
		self::$messages[LMSG_CHANGEPASS_WRONG_NEWPASS_LENGHT] = "A nova senhas deve possuir de 5 a 20 caracteres.";
		self::$messages[LMSG_CHANGEEMAIL_ALREADY_HAVE_REQUEST] = "Esta conta já possui uma mudança de e-mail agendada.";
		self::$messages[LMSG_CHANGEEMAIL_NOTHING] = "A sua conta não possui uma mudança de endereço de e-mail agendada para ser cancelada.";
		self::$messages[LMSG_CHARACTER_WRONG] = "Este personagem não existe em nosso banco de dados.";
		self::$messages[LMSG_WRONG_NAME] = "Este nome possui formatação ilegal. Tente novamente com outro nome.";
		self::$messages[LMSG_CHARACTER_NAME_ALREADY_USED] = "O nome escolhido já esta em uso por outro personagem no jogo. Escolha outro nome.";
		self::$messages[LMSG_CHARACTER_NOT_TO_DELETION] = "Este personagem não está marcado para ser deletado.";
		self::$messages[LMSG_CHARACTER_ALREADY_TO_DELETE] = "Este personagem já está marcado para ser deletado.";
		self::$messages[LMSG_CHARACTER_NOT_FROM_YOUR_ACCOUNT] = "Este personagem não existe ou não pertence a sua conta e portanto esta operação não pode ser concluida.";
		self::$messages[LMSG_ACCOUNT_CANNOT_HAVE_MORE_CHARACTERS] = "A sua conta já possui 10 personagens criados. Para criar um novo personagem é necessario se desfazer de um já existente deletando-o.";
		self::$messages[LMSG_CHANGEINFOS_WRONG_SIZE] = "Os campos Nome Real e Localidade devem possuir no maximo 25 caracteres enquanto Website deve conter no maximo 50 caracteres.";
		self::$messages[LMSG_SECRETKEY_ALREADY_EXISTS] = "Esta conta já possui uma chave secreta configurada.";
		self::$messages[LMSG_SECRETKEY_WRONG_SIZE] = "A sua chave secreta deve possuir entre 10 e 50 caracteres e seu lembrete entre 5 e 25 caracteres.";
		self::$messages[LMSG_ACCOUNT_SETNAME_SAME_ID] = "O nome de sua conta deve ser diferente do seu antigo numero.";
		self::$messages[LMSG_CONTR_TERMS] = "Para aceitar uma contribuição é necessario estar de acordo com todas clausulas e termos de nosso contrato de serviço.";
		self::$messages[LMSG_CONTR_ORDER_NUMBER_DUPLICATED] = "Ouve uma falha ao obter um numero para seu pedido. Por favor tente novamente, se o problema persistir aguarde algumas horas.";
		self::$messages[LMSG_ACCOUNT_HAS_NO_ORDERS] = "Não existe nenhum pedido gerado por sua conta.";
		self::$messages[LMSG_REPORT] = "Um erro desconhecido ocorreu ou você não tem permissão para visualizar esta pagina. Um log foi enviado ao administrador. Por favor, tente novamente mais tarde.";
		self::$messages[LMSG_NEED_LOGIN] = "Para visualizar esta pagina é necessário primeiro acessar sua conta.";
		self::$messages[LMSG_NEED_PREMIUM] = "Para visualizar esta pagina é necessário possuir uma conta premium.";
		self::$messages[LMSG_PAGE_NOT_FOUND] = "Esta pagina não existe ou está em processo de construção.";
		self::$messages[LMSG_SQL_INJECTION] = "Detectada tentativa de inserção de codigo malicioso não autorizado. A tentativa ilegal do USER_IP: @v1@ foi reportado aos Administradores para investigação.";
		self::$messages[LMSG_GUILD_NOT_FOUND] = "A guilda @v1@ não existe. Verifique e tente novamente.";
		self::$messages[LMSG_GUILD_CHARACTER_NOT_INVITED] = "O personagem @v1@ não está convidado para nenhuma guilda.";
		self::$messages[LMSG_GUILD_NAME_ALREADY_USED] = "O nome @v1@ já está sendo usado por outra guilda. Escolha outro nome.";
		self::$messages[LMSG_GUILD_ONLY_ONE_VICE_PER_ACCOUNT] = "Somente é permitido possuir um lider ou vice-lider por conta.";
		self::$messages[LMSG_CHARACTER_ALREADY_MEMBER_GUILD] = "Este personagem já é membro de uma guild. Para criar uma nova guilda é necessario primeiro deixar a guilda atual.";
		self::$messages[LMSG_CHARACTER_COMMENT_WRONG_SIZE] = "O comentário de seu personagem não deve possuir mais de 500 caracteres.";
		self::$messages[LMSG_CHARACTER_CHANGE_THING_CONFIRM] = "Para modificar o nome ou sexo de seu personagem é necessario aceitar e estar ciente destas mudanças e os seus custos.";
		self::$messages[LMSG_CHARACTER_NEED_OFFLINE] = "Para efetuar esta operação é necessario que você faça um \"log-out\" no jogo.";
		self::$messages[LMSG_CHARACTER_CHANGENAME_COST] = "Você não possui os @v1@ dias de conta premium necessarios para modificar o nome de seu personagem.";
		self::$messages[LMSG_CHARACTER_CHANGESEX_COST] = "Você não possui os @v1@ dias de conta premium necessarios para modificar o sexo de seu personagem.";
		self::$messages[LMSG_ITEMSHOP_OLD_PURCHASE] = "Você deve fazer um \"log-in\" no jogo para receber sua antiga compra em nosso item shop antes de efetuar uma nova compra.";
		self::$messages[LMSG_ITEMSHOP_COST] = "Você não possui os @v1@ dias de conta premium necessarios para obter este item.";
		self::$messages[LMSG_GUILD_NEED_NO_MEMBERS_DISBAND] = "A sua guilda ainda possui membros ativos. Para desmanchar uma guilda é necessario só existir o líder da guilda.";
		self::$messages[LMSG_GUILD_COMMENT_SIZE] = "O comentário de sua guilda não deve exceder 500 caracteres.";
		self::$messages[LMSG_GUILD_LOGO_SIZE] = "A imagem do logotipo de sua guilda não deve exceder 100 kb.";
		self::$messages[LMSG_GUILD_FILE_WRONG] = "Este arquivo não possui um formato e valido. Por favor, tente outro arquivo.";
		self::$messages[LMSG_GUILD_LOGO_DIMENSION_WRONG] = "As dimenções da imagem de logotipo para sua guilda deve ser exatamente de 100 pixel de largura por 100 pixels altura.";
		self::$messages[LMSG_GUILD_LOGO_EXTENSION_WRONG] = "O logotipo de sua guilda deve ser no formato GIF, JPG ou PNG.";
		self::$messages[LMSG_GUILD_INVITE_LIMIT] = "Só é permitido o envio de até 20 convites por vez.";
		self::$messages[LMSG_GUILD_INVITE_ALREADY_MEMBER] = "Os seguintes personagens já são membros de outras guildas e não podem ser invitados: @v1@";
		self::$messages[LMSG_GUILD_INVITE_ALREADY_INVITED] = "Os seguintes personagens já estão convidados para uma guilda e não podem ser invitados: @v1@";
		self::$messages[LMSG_GUILD_INVITE_CHARACTER_NOT_FOUNDS] = "Os seguintes personagens não existem em nosso banco de dados e não podem ser invitados: @v1@";
		self::$messages[LMSG_GUILD_INVITE_CANCEL] = "O convite para o personagem @v1@ se juntar a sua guilda foi cancelado com sucesso!";
		self::$messages[LMSG_GUILD_IS_NOT_MEMBER] = "O personagem @v1@ não faz parte da guilda @v2@ e portanto esta operação não pode ser efetuada.";
		self::$messages[LMSG_GUILD_RANK_ONLY_PREMIUM] = "Apenas personagens com uma conta premium podem ser promovidos para este nível.";
		self::$messages[LMSG_GUILD_PERMISSION] = "Você não possui permissão sulficiente para efetuar esta operação. Você deve solicitar isto a um membro com rank superior ao seu.";
		self::$messages[LMSG_GUILD_TITLE_SIZE] = "O titulo do membro deve possuir entre 3 e 15 caracteres.";
		self::$messages[LMSG_GUILD_ACCOUNT_ALREADY_IS_HIGH_RANK] = "O personagem escolhido não pode ser promovido a este nível. Somente é permitido possuir 1 lider ou vice-lider por conta.";
		self::$messages[LMSG_GUILD_RANK_WRONG_ORDER] = "Os ranks estão em sequencia fora de ordem.";
		self::$messages[LMSG_GUILD_RANK_WRONG_SIZE] = "Os ranks devem possuir no maximo 35 caracteres.";
		self::$messages[LMSG_GUILD_RANK_MIMINUM_NEEDED] = "É necessario existir ao menos 3 ranks para sua guilda.";
		self::$messages[LMSG_GUILD_RANK_IN_USE] = "Um ou mais ranks que foram removidos de sua guild estão em uso (possuem membros). Para remover um rank é necessario que o mesmo não possua nenhum membro.";
		self::$messages[LMSG_GUILD_CANNOT_LEAVE] = "Você não pode abandonar a guilda @v1@ pois este personagem é o lider. Caso queira encerrar a guilda use a opção Desmanchar.";
		self::$messages[LMSG_GUILD_WAR_NO_HAVE_OPPONENTS] = "Não existe nenhuma guilda formada em nosso servidor portanto é impossivel iniciar uma guerra.";
		self::$messages[LMSG_GUILD_NEED_TO_BE_FORMED] = "Para efetuar esta operação em sua guilda é necessario que esta já esteja formada.";
		self::$messages[LMSG_GUILD_WAR_WRONG_FRAG_LIMIT] = "O limite de frags de uma guerra deve ser de 10 a 1000.";
		self::$messages[LMSG_GUILD_WAR_WRONG_END_DATE] = "O limite de tempo de uma guerra deve ser entre 7 e 360 dias.";
		self::$messages[LMSG_GUILD_WAR_WRONG_FEE] = "O pagamento da guilda derrotada ou rendida deve ser entre 0 e 100000000 gold coins .";
		self::$messages[LMSG_GUILD_WAR_WRONG_COMMENT_LENGTH] = "O comentario de declaração de guerra não deve exceder 500 caracteres.";
		self::$messages[LMSG_GUILD_WAR_REJECTED] = "A declaração de guerra da guilda @v1@ foi rejeitada com sucesso. Sua guilda não entrará mais nesta guerra.";
		self::$messages[LMSG_GUILD_IS_ON_WAR] = "A guilda @v1@ esta em guerra com outra(s) guildas, portanto, certas operações na guilda não estão disponiveis, como abandonar guilda, convidar novos membros, aceitar convites, remover membros. Tente novamente quando a guerra estiver encerrada.";
		self::$messages[LMSG_GUILD_WAR_ALREADY] = "A sua guilda já declarou ou está em guerra contra a guilda @v1@. Você poderá declarar guerra contra esta guilda quando a guerra atual estiver terminada.";
	
		self::$messages[LMSG_ACCOUNT_REGISTERED] = "
			<p>Parabens, sua conta foi criada com sucesso!</p>
		";
		
		self::$messages[LMSG_ACCOUNT_INFOS_SEND] = "
			<p>Sua senha e outras informações foram enviadas em uma mensagem a seu e-mail cadastrado.</p>
			<p>Tenha um bom jogo!</p>
		";
		
		self::$messages[LMSG_ACCOUNT_PASSWORD_IS] = "
			<p>Sua senha é <font size='5'><b>@v1@</b></font>.</p>
			<p>Tenha um bom jogo!</p>
		";
		
		self::$messages[LMSG_RECOVERY_ACCOUNT_NAME_SEND] = "
			<p>Caro jogador, o número de sua conta foi enviado ao seu e-mail com sucesso!</p>
			<p>Este e-mail tem um prazo de até 24 horas para chegar, porem geralmente chega dentro de alguns instantes.</p>
			<p>Tenha um bom jogo!</p>
		";

		self::$messages[LMSG_RECOVERY_PASSWORD_SEND] = "
			<p>Caro jogador, uma mensagem foi enviada ao seu e-mail com as informações necessarias para você gerar uma nova senha para sua conta!</p>
			<p>Este e-mail tem um prazo de até 24 horas para chegar, porem geralmente chega dentro de alguns instantes.</p>
			<p>Tenha um bom jogo!</p>
		";		

		self::$messages[LMSG_RECOVERY_NEWPASS_SEND] = "
			<p>Caro jogador, a nova senha de sua conta foi enviada ao seu e-mail com sucesso!</p>
			<p>Este e-mail tem um prazo de até 24 horas para chegar, porem geralmente chega dentro de alguns instantes.</p>
			<p>Tenha um bom jogo!</p>
		";			

		self::$messages[LMSG_RECOVERY_BOTH_SEND] = "
			<p>Caro jogador, uma mensagem foi enviada ao seu e-mail com o número de sua conta e as informações necessarias para você gerar uma nova senha para sua conta!</p>
			<p>Este e-mail tem um prazo de até 24 horas para chegar, porem geralmente chega dentro de alguns instantes.</p>
			<p>Tenha um bom jogo!</p>
		";			

		self::$messages[LMSG_RECOVERY_EMAIL_CHANGED] = "
			<p>Caro jogador,</p>
			<p>O e-mail registrado em sua conta foi modificado ultilizando sua chave secreta com sucesso!</p>
			<p>Tenha um bom jogo!</p>
		";

		self::$messages[LMSG_ACCOUNT_PASSWORD_CHANGED] = "
			<p>A sua senha foi modificada com sucesso!</p>
		";			

		self::$messages[LMSG_CHANGEEMAIL_SCHEDULED] = "
			<p>Caro jogador,</p>
			<p>A mudança de email de sua conta foi agendada com sucesso.</p>
			<p>Tenha um bom jogo!</p>
		";			
		
		self::$messages[LMSG_CHANGEEMAIL_CANCELED] = "
			<p>Caro jogador,</p>
			<p>A mudança de endereço de email de sua conta foi cancelada com sucesso! Nenhuma mudança de endereço de e-mail acontecera!</p>
			<p>Tenha um bom jogo!</p>
		";			
		
		self::$messages[LMSG_CHANGEINFOS_SUCCESS] = "
			<p>Caro jogador,</p>
			<p>A mudança dasinformações de sua conta foram efetuadas com sucesso!</p>
			<p>Tenha um bom jogo!</p>
		";
		
		self::$messages[LMSG_SECRETKEY_SUCCESS] = "
			<p>Caro jogador,</p>
			<p>A chave secreta @v1@ foi configurada com sucesso em sua conta!</p>
			<p>Tenha um bom jogo!</p>
		";			
		
		self::$messages[LMSG_SECRETKEY_CUSTOM_SUCCESS] = "
			<p>Caro jogador,</p>
			<p>A sua chave secreta @v1@ com o lembrete @v2@ foi configurada com sucesso em sua conta!</p>
			<p>Tenha um bom jogo!</p>
		";			
		
		self::$messages[LMSG_ACCOUNT_SETNAME_SUCCESS] = "
			<p>Caro jogador,</p>
			<p>A sua conta agora possui um Nome configurado corretamente!</p>
			<p>Tenha um bom jogo!</p>
		";			
		
		self::$messages[LMSG_CHARACTER_CREATED] = "
			<p>O personagem @v1@ foi criado com sucesso!</p>
			<p>Para começar a jogar clique <a href='?ref=general.howplay'>aqui</a> e siga as instruções.</p>
			<p>A sua aventura se inicia em Island of Peace, esta ilha funciona como um aprendizado com vários tipos de criaturas, NPCs, quests, academia de treino e muito mais, alem que na ilha não é possivel atacar outros jogadores. Quando você atingir o nivel 60 estara preparado para sair da ilha usando o Barco e explorar aos outros continentes do Darghos, aonde é possivel atacar outros personagens. É importante informar que você pode sair a qualquer momento da ilha independente do nivel, porem, uma vez fora, é impossivel retornar a ilha.</p>
			<p>Tenha uma boa jornada!</p>
		";			
		
		self::$messages[LMSG_CHARACTER_NO_MORE_DELETED] = "
			<p>Caro jogador,</p>
			<p>A exclusão do seu personagem @v1!@ foi cancelada! Este personagem não será mais deletado.</p>
			<p>Tenha um bom jogo!</p>
		";			
		
		self::$messages[LMSG_CONTR_ACTIVATED] = "
			<p>Caro jogador,</p>
			<p>A sua contribuição foi ativada com sucesso!</p>
			<p>Agora sua conta já possui status de Contra Premium, o que lhe permitira muitas novas possibilidades dentro do @v1@!</p>
			<p>Agradeçemos a preferencia e obrigado por contribuir conosco!</p>
			<p>Tenha um bom jogo!<br>Equipe UltraxSoft.</p>
		";			
		
		self::$messages[LMSG_CONTR_ORDER_CREATED] = "
			<fieldset>			
				
				<p><h3>Pedido Gerado com sucesso!</h3></p>
				
				<p>Caro jogador, o seu pedido foi gerado com sucesso! Anote abaixo o numero de seu pedido para consulta ou qualquer eventual problema.</p>
				<p>Clicando no botão Finalizar abaixo você será direcionado ao site do @v1@ aonde você irá terminar o processo efetuando o pagamento de sua contribuição.</p>
				
				<p>Numero do Pedido de sua Contribuição: <h3>@v2@</h3></p>
				
				<p>@v3@</p>
			</fieldset>
		";		

		self::$messages[LMSG_GUILD_JOIN] = "
			<p>Caro jogador,</p>
			<p>O personagem @v1@ é um novo membro da guild @v2@!</p>
			<p>Tenha um bom jogo!</p>
		";				
	
		self::$messages[LMSG_GUILD_JOIN_REJECT] = "
			<p>Caro jogador,</p>
			<p>Você recusou o convite da guild @v1@ para seu persongem @v2@ com sucesso!</p>
			<p>Tenha um bom jogo!</p>
		";				
		
		self::$messages[LMSG_GUILD_CREATED] = "
			<p>A guilda @v1@ foi criada com sucesso!</p>
			<p>Inicialmente a sua guilda está em estagio de formação, e você deve nomear ao minimo @v2@ vice-lideres em @v3@ dias para que sua guilda seja formada! Caso contrario a guilda será automaticamente desmanchada.</p>
			<p>Tenha uma boa jornada!</p>
		";				
		
		self::$messages[LMSG_CHARACTER_DELETION_SCHEDULED] = "
			<p>Caro jogador,</p>
			<p>Foi agendado com sucesso a exclusão de seu personagem @v1@ para o dia @v2@!</p>
			<p>Tenha um bom jogo!</p>
		";				
		
		self::$messages[LMSG_CHARACTER_COMMENT_CHANGED] = "
			<p>Caro jogador,</p>
			<p>A mudança das informações de seu personagem foi efetuada com exito!</p>
			<p>Tenha um bom jogo!</p>
		";			
			
		self::$messages[LMSG_CHARACTER_NAME_CHANGED] = "
			<p>Caro jogador,</p>
			<p>A mudança de nome de seu personagem de @v1@ para @v2@ foi efetuada com sucesso!</p>
			<p>Tenha um bom jogo!</p>
		";						self::$messages[LMSG_CHARACTER_CREATED] = "
			<p>O personagem @v1@ foi criado com sucesso!</p>
			<p>Para começar a jogar clique <a href='?ref=general.howplay'>aqui</a> e siga as instruções.</p>
			<p>A sua aventura se inicia em Island of Peace, esta ilha funciona como um aprendizado com vários tipos de criaturas, NPCs, quests, academia de treino e muito mais, alem que na ilha não é possivel atacar outros jogadores. Quando você atingir o nivel 60 estara preparado para sair da ilha usando o Barco e explorar aos outros continentes do Darghos, aonde é possivel atacar outros personagens. É importante informar que você pode sair a qualquer momento da ilha independente do nivel, porem, uma vez fora, é impossivel retornar a ilha.</p>
			<p>Tenha uma boa jornada!</p>
		";	
			
		self::$messages[LMSG_CHARACTER_SEX_CHANGED] = "
			<p>Caro jogador,</p>
			<p>A mudança de sexo de seu personagem @v1@ foi efetuada com sucesso!</p>
			<p>Tenha um bom jogo!</p>
		";				
			
		self::$messages[LMSG_ITEMSHOP_PURCHASE_SUCCESS] = "
			<p>Caro jogador,</p>
			<p>A compra de @v1@x @v2@ por @v3@ dias de sua conta premium foi efetuada com sucesso!</p>
			<p>O seu item estara em sua backpack principal no proximo log-in.</p>
			<p>Tenha um bom jogo!</p>
		";				
			
		self::$messages[LMSG_GUILD_DISBANDED] = "
			<p>Caro jogador,</p>
			<p>A guilda @v1@ foi desmanchada com sucesso e não existe mais no servidor.</p>
			<p>Tenha um bom jogo!</p>
		";				
			
		self::$messages[LMSG_GUILD_DESC_CHANGED] = "
			<p>Caro jogador,</p>
			<p>As mudanças de descrição e exibição de sua guilda foram efetuadas com sucesso!</p>
			<p>Tenha um bom jogo!</p>
		";				
			
		self::$messages[LMSG_GUILD_INVITEDS] = "
			<p>Caro jogador,</p>
			<p>Todos jogadores da lista foram convidados para sua guilda com sucesso!</p>
			<p>O seu convite estará na pagina principal da conta do jogador aonde ele deve aceitar ou rejeitar o convite!</p>
			<p>Tenha um bom jogo!</p>
		";				
			
		self::$messages[LMSG_GUILD_LEAVE] = "
			<p>Caro jogador,</p>
			<p>O personagem @v1@ não faz mais parte da guilda @v2@!</p>
			<p>Tenha um bom jogo!</p>
		";				
			
		self::$messages[LMSG_GUILD_MEMBER_EDITED] = "
			<p>Caro jogador,</p>
			<p>O membro @v1@ da guild @v2@ foi modificado com sucesso!</p>
			<p>Tenha um bom jogo!</p>
		";				
			
		self::$messages[LMSG_GUILD_PASSLEADERSHIP] = "
			<p>Caro jogador,</p>
			<p>A guilda @v1@ teve a liderana transferida de @v2@ para @v3@ com sucesso!</p>
			<p>Tenha um bom jogo!</p>
		";				
			
		self::$messages[LMSG_GUILD_RANKS_EDITED] = "
			<p>Caro jogador,</p>
			<p>As alterações nos ranks de sua guild foi efetuado com sucesso!</p>
			<p>Tenha um bom jogo!</p>
		";				
			
		self::$messages[LMSG_GUILD_WAR_DECLARED] = "
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
		
		self::$messages[LMSG_GUILD_WAR_ACCEPTED] = "
			<p>Caro jogador,</p>
			<p>A sua guilda @v1@ aceitou a declaração de guerra da guilda @v2@ com sucesso sob os termos acordados!</p>
			<p>No proximo server save será sera reservado de sua conta do banco o preço da rendição acordado, assim como do lider da guilda oponente.</p>
			<p>A guerra sera encerrada dentro dos termos acordados ou ainda pode ser declarada uma rendição pelo painel de guerras de sua guilda.</p>
			<p>Desejamos uma boa sorte a sua guilda nesta guerra e um bom jogo!</p>
		";				
		
		self::$messages[LMSG_GUILD_WAR_NEGOTIATE_SEND] = "
			<p>Caro jogador,</p>
			<p>Uma nova proposta foi enviada ao lider da outra guilda para ele analisar, ele poderá aceita-la, rejeitar-la ou ainda enviar uma nova proposta de volta a você.</p>
			<p>Caso a proposta seja aceita a guerra será iniciada no proximo server save, caso os gold coins de rendição possam ser reservados das contas de banco de cada um dos lideres das duas guildas.</p>
			<p>Desejamos uma boa sorte a sua guilda nesta guerra e um bom jogo!</p>
		";				
	}
}
?>