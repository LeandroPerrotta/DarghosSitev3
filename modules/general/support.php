<?php

$module .= "
<p>".tr("Se você necessita de algum tipo de suporte ou possui alguma duvida você pode tentar encontrar um membro da staff. Tutores poderão lhe ajudar em duvidas sobre conteudo do jogo e outras explicações comuns, se ele não puder lhe ajudar, você poderá tentar falar com um Game Master.")."</p>
<p>".tr("A maneira mais prática para isto é tentar usar o <b>canal de ajuda</b> (Help Channel) ou o <b>tela de violações</b> (<b>Control + R</b> dentro do jogo). Você pode ainda tentar entrar com contato direto com eles, enviando uma <b>mensagem particular</b> (PM).")."</p>
";

function getRowStr($player_id){
    $player = new Framework\Player();
    $player->load($player_id);
    
    $str = "";
    $status_str = "";
    
    if($player->getOnline()){
        $status_str .= "<span style='color: #00ff00; font-weight: bold;'>".tr("online")."</span>";
    }
    else{
        $lastlogin = new DateTime("@{$player->getLastLogin()}");
        $now = new DateTime();
        $diff = $lastlogin->diff($now);
    
        $status_str .= tr("visto ultima vez: @v1@ dia(s) atrás", $diff->format("%a"));
    }
    
    $str .= "
    <tr>
        <td class='name'><a href='?ref=character.view&name={$player->getName()}'>{$player->getName()}</a></td>
        <td><span style='font-size: 9px;'>{$status_str}</span></td>
    </tr>
    ";    
    
    return $str;
}

$members_str = "";
$tutors_str = "";

$staff_groups = array(t_Group::GameMaster, t_Group::CommunityManager, t_Group::Administrator, t_Group::SuperAdministrator);
$tutors_groups = array(t_Group::Tutor, t_Group::SeniorTutor);

$query = \Core\Main::$DB->query("SELECT `id` FROM `players` WHERE `group_id` IN (".implode(",", $staff_groups).")");
while($fetch = $query->fetch()){ $members_str .= getRowStr($fetch->id); }

$query = \Core\Main::$DB->query("SELECT `id` FROM `players` WHERE `group_id` IN (".implode(",", $tutors_groups).")");
while($fetch = $query->fetch()){ $tutors_str .= getRowStr($fetch->id); }

$module .= '

<h3 style="margin-top: 15px;">'.tr("Membros da Equipe").'</h3>

<table cellspacing="0" cellpadding="0" id="table">
		<tbody>
		    <tr>
    			<th width="50%">'.tr("Nome").'</th> <th width="25%">'.tr("Status").'</th>
    		</tr>	
	
			'.$members_str.'
		
	    </tbody>
</table>

<h3 style="margin-top: 15px;">'.tr("Lista de Tutores").'</h3>

<table cellspacing="0" cellpadding="0" id="table">
		<tbody>
		    <tr>
    			<th width="65%">'.tr("Nome").'</th> <th width="25%">'.tr("Status").'</th>
    		</tr>	
	
			'.$tutors_str.'
		
	    </tbody>
</table>';

$module .= "

<p style='margin-top: 30px;'>".tr("Caso você não encontre nenhum membro da staff ou tutor para lhe ajudar você pode ainda tentar:")."</p>

<ul>
    <li><span style='font-size: 9px;'>".tr("Verificar nossa pagina de <a href='?ref=general.faq'>perguntas e respostas frequentes</a>.")."</span></li>
    <li><span style='font-size: 9px;'>".tr("Criar um post na seção de Suporte de nosso Fórum.")."</span></li>
    <li><span style='font-size: 9px;'>".tr("Curta e envie uma mensagem inbox em nosso <a href='https://www.facebook.com/DarghosOT'>Facebook</a> (atendimento leva minutos ou algumas horas).")."</span></li>
    <li><span style='font-size: 9px;'>".tr("Contate-nos através do e-mail <a href='#'>suporte@darghos.com</a> (atendimento leva 1-5 dias).")."</span></li>
</ul>

";

?>
