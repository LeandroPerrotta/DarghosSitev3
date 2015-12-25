<?php

$module .= "
<p>".tr("Se você necessita de algum tipo de suporte ou possui alguma duvida você pode tentar encontrar um membro da staff. Tutores poderão lhe ajudar em duvidas sobre conteudo do jogo e outras explicações comuns, se ele não puder lhe ajudar, você poderá tentar falar com um Game Master.")."</p>
<p>".tr("A maneira mais prática para isto é tentar usar o <b>canal de ajuda</b> (Help Channel) ou o <b>tela de violações</b> (<b>Control + R</b> dentro do jogo). Você pode ainda tentar entrar com contato direto com eles, enviando uma <b>mensagem particular</b> (PM).")."</p>
";

$module .= "

<p style='margin-top: 30px;'>".tr("Caso você não encontre nenhum membro da staff ou tutor para lhe ajudar você pode ainda tentar:")."</p>

<ul>
    <li><span style='font-size: 9px;'>".tr("Verificar nossa pagina de <a href='?ref=general.faq'>perguntas e respostas frequentes</a>.")."</span></li>
    <li><span style='font-size: 9px;'>".tr("Curta e envie uma mensagem inbox em nosso <a href='https://www.facebook.com/DarghosOT'>Facebook</a> (atendimento leva minutos ou algumas horas).")."</span></li>
    <li><span style='font-size: 9px;'>".tr("Contate-nos através do e-mail <a href='#'>".getConf(confEnum()->WEBSITE_EMAIL_SUPPORT)."</a> (atendimento leva 1-5 dias).")."</span></li>
</ul>

";

?>
