<?php
function pagueprofEsc($v): string { return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8'); }
function pagueprofDinheiro($v): string { return 'R$ '.number_format((float)$v,2,',','.'); }
function pagueprofData($v): string { return $v ? date('d/m/Y',strtotime((string)$v)) : '—'; }
function pagueprofStatus(array $c): array {
    $s=(string)($c['status']??'pendente');
    if(in_array($s,['pendente','recusada'],true) && !empty($c['data_vencimento']) && $c['data_vencimento']<date('Y-m-d'))return ['Vencida','vermelho'];
    $rotulos=['pendente'=>'A pagar','paga'=>'Paga','em_analise'=>'Em análise','recusada'=>'Comprovante recusado','cancelada'=>'Cancelada'];
    $classes=['pendente'=>'amarelo','paga'=>'verde','em_analise'=>'azul','recusada'=>'vermelho','cancelada'=>'neutro'];
    return [$rotulos[$s]??ucfirst($s),$classes[$s]??'neutro'];
}
$csrfValor=htmlspecialchars((string)($_SESSION['csrf']??''),ENT_QUOTES,'UTF-8');
$perfisAtivos=(array)($usuario['perfis']??[]);
$temProfessor=in_array('professor',$perfisAtivos,true);$temAluno=in_array('aluno',$perfisAtivos,true);
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="theme-color" content="#f5f7fb">
  <link rel="icon" type="image/svg+xml" href="assets/favicon.svg">
  <title>PagueProf | <?= pagueprofEsc(ucfirst(str_replace('_',' ',$pagina))) ?></title>
  <link rel="stylesheet" href="assets/app.css">
</head>
<body class="app-body">
<header class="barra-app">
  <a class="marca" href="index.php?rota=painel"><span class="marca-icone">P</span><span>Pague<strong>Prof</strong></span></a>
  <div class="topo-direita"><span class="nome-sessao"><?= pagueprofEsc($usuario['nome']??'') ?></span><a class="sair" href="index.php?rota=sair">Sair</a></div>
</header>
<div class="app-shell">
  <nav class="menu-app" aria-label="Menu principal">
    <a class="<?= $pagina==='dashboard'?'ativo':'' ?>" href="index.php?rota=painel">Visão geral</a>
    <?php if($temProfessor): ?>
      <a class="<?= $pagina==='alunos'?'ativo':'' ?>" href="index.php?rota=alunos">Alunos</a>
      <a class="<?= $pagina==='planos'?'ativo':'' ?>" href="index.php?rota=planos">Planos e cobranças</a>
      <a class="<?= $pagina==='cobrancas'?'ativo':'' ?>" href="index.php?rota=cobrancas">Financeiro</a>
      <a class="<?= $pagina==='comprovantes'?'ativo':'' ?>" href="index.php?rota=comprovantes">Comprovantes</a>
      <a class="<?= $pagina==='relatorios'?'ativo':'' ?>" href="index.php?rota=relatorios">Relatórios</a>
      <a class="<?= $pagina==='configuracoes'?'ativo':'' ?>" href="index.php?rota=configuracoes">Recebimentos</a>
    <?php endif; ?>
    <?php if($temAluno): ?><a class="<?= $pagina==='minhas_cobrancas'?'ativo':'' ?>" href="index.php?rota=minhas_cobrancas">Meus pagamentos</a><?php endif; ?>
  </nav>
  <main class="conteudo-app">
    <?php if($mensagem): ?><div class="alerta sucesso"><?= pagueprofEsc($mensagem) ?></div><?php endif; ?>
    <?php if($erroFlash || !empty($erro)): ?><div class="alerta erro"><?= pagueprofEsc($erroFlash ?: $erro) ?></div><?php endif; ?>

    <?php if($pagina==='dashboard'): ?>
      <div class="cabecalho-pagina"><div><p class="eyebrow">PAGUEPROF</p><h1>Olá, <?= pagueprofEsc(explode(' ',trim($usuario['nome']))[0]??'') ?></h1><p>Acompanhe alunos e pagamentos em um só lugar.</p></div></div>
      <?php if($temProfessor && isset($resumo)): ?>
        <section class="grade-resumo">
          <article class="cartao-resumo"><span>Alunos ativos</span><strong><?= (int)$resumo['total_alunos'] ?></strong></article>
          <article class="cartao-resumo"><span>Recebido neste mês</span><strong><?= pagueprofDinheiro($resumo['recebido']??0) ?></strong></article>
          <article class="cartao-resumo"><span>Em aberto no período</span><strong><?= pagueprofDinheiro($resumo['aberto']??0) ?></strong></article>
          <article class="cartao-resumo alerta-resumo"><span>Vencido no período</span><strong><?= pagueprofDinheiro($resumo['vencido']??0) ?></strong></article>
        </section>
        <?php if(!empty($sugestoes)): ?><section class="bloco"><div class="bloco-cabecalho"><div><h2>Renovar cobranças</h2><p>Estes alunos não têm novas cobranças futuras após o último pagamento.</p></div></div>
          <?php foreach($sugestoes as $s): ?><article class="cartao-item"><div><strong><?= pagueprofEsc($s['aluno_nome']) ?></strong><span><?= pagueprofEsc(ucfirst(str_replace('_',' ',$s['frequencia']))) ?> · última paga em <?= pagueprofData($s['ultima_paga']) ?></span></div>
            <form method="post" action="index.php?rota=gerar_cobrancas" class="form-inline"><input type="hidden" name="csrf" value="<?= $csrfValor ?>"><input type="hidden" name="plano_id" value="<?= (int)$s['id'] ?>"><label>Vencimento sugerido <input type="date" name="data_vencimento" required value="<?= pagueprofEsc($s['proximo_vencimento']) ?>"></label><label>Períodos <input type="number" name="quantidade" min="1" max="12" value="3"></label><button class="botao pequeno" type="submit">Gerar próximas</button></form></article><?php endforeach; ?>
        </section><?php endif; ?>
        <section class="bloco"><div class="bloco-cabecalho"><div><h2>Cobranças recentes</h2><p>Últimos vencimentos e situações.</p></div><a class="link-acao" href="index.php?rota=cobrancas">Ver todas</a></div>
          <?php if(empty($pendentes)): ?><p class="vazio">Ainda não há cobranças. Cadastre um aluno e crie um plano.</p><?php else: foreach($pendentes as $c): [$label,$classe]=pagueprofStatus($c); ?><article class="linha-dados"><div><strong><?= pagueprofEsc($c['aluno_nome']) ?></strong><span><?= pagueprofData($c['data_vencimento']) ?> · <?= pagueprofDinheiro($c['valor']) ?></span></div><span class="estado <?= $classe ?>"><?= pagueprofEsc($label) ?></span></article><?php endforeach; endif; ?>
        </section>
      <?php endif; ?>
      <?php if($temAluno && !empty($cobrancasAluno)): ?><section class="bloco"><div class="bloco-cabecalho"><div><h2>Seus pagamentos</h2><p>Consulte cobranças e comprovantes.</p></div><a class="link-acao" href="index.php?rota=minhas_cobrancas">Abrir pagamentos</a></div><?php foreach(array_slice($cobrancasAluno,0,4) as $c): [$label,$classe]=pagueprofStatus($c); ?><article class="linha-dados"><div><strong><?= pagueprofEsc($c['professor_nome']) ?></strong><span>Vence em <?= pagueprofData($c['data_vencimento']) ?> · <?= pagueprofDinheiro($c['valor']) ?></span></div><span class="estado <?= $classe ?>"><?= pagueprofEsc($label) ?></span></article><?php endforeach; ?></section><?php elseif($temAluno): ?><section class="bloco"><h2>Meus pagamentos</h2><p class="vazio">Nenhuma cobrança foi vinculada à sua conta. Peça ao professor para cadastrar este e-mail.</p></section><?php endif; ?>
    <?php elseif($pagina==='alunos'): ?>
      <div class="cabecalho-pagina"><div><p class="eyebrow">PROFESSOR</p><h1>Alunos</h1><p>Cadastre alunos e acompanhe a situação de cada um.</p></div></div>
      <section class="bloco"><h2>Novo aluno</h2><form method="post" action="index.php?rota=alunos" class="form-grid"><input type="hidden" name="csrf" value="<?= $csrfValor ?>"><label>Nome completo<input name="nome" maxlength="140" autocomplete="name" required></label><label>E-mail<input name="email" type="email" maxlength="190" autocomplete="email" required></label><label>Telefone<input name="telefone" type="tel" maxlength="40"></label><label>Matéria ou aula<input name="materia" maxlength="100" placeholder="Ex.: Matemática"></label><div class="form-acoes"><button class="botao" type="submit">Cadastrar aluno</button></div></form></section>
      <section class="bloco"><div class="bloco-cabecalho"><div><h2>Lista de alunos</h2><p>Vermelho indica dívida vencida; verde indica que não há vencimentos em atraso.</p></div></div><?php if(empty($alunos)): ?><p class="vazio">Nenhum aluno cadastrado.</p><?php else: foreach($alunos as $a): $temDivida=(int)$a['dividas_vencidas']>0; ?><article class="aluno-item <?= $temDivida?'aluno-divida':'aluno-em-dia' ?>"><div class="avatar-inicial"><?= pagueprofEsc(mb_strtoupper(mb_substr($a['nome'],0,1))) ?></div><div class="aluno-dados"><strong><?= pagueprofEsc($a['nome']) ?></strong><span><?= pagueprofEsc($a['email']) ?><?= $a['materia']?' · '.pagueprofEsc($a['materia']):'' ?></span></div><span class="estado <?= $temDivida?'vermelho':'verde' ?>"><?= $temDivida?'Em atraso':'Em dia' ?></span></article><?php endforeach; endif; ?></section>
    <?php elseif($pagina==='planos'): ?>
      <div class="cabecalho-pagina"><div><p class="eyebrow">PROFESSOR</p><h1>Planos e cobranças</h1><p>Defina preço e frequência e gere cobranças futuras.</p></div></div>
      <section class="bloco"><h2>Novo plano</h2><?php if(empty($alunos)): ?><p class="vazio">Cadastre um aluno antes de criar um plano.</p><?php else: ?><form method="post" action="index.php?rota=planos" class="form-grid"><input type="hidden" name="csrf" value="<?= $csrfValor ?>"><label>Aluno<select name="aluno_id" required><?php foreach($alunos as $a): ?><option value="<?= (int)$a['id'] ?>"><?= pagueprofEsc($a['nome']) ?></option><?php endforeach; ?></select></label><label>Modalidade<select name="modalidade"><option value="mensal">Mensalidade</option><option value="por_aula">Por aula</option></select></label><label>Valor (R$)<input name="valor" type="number" min="0.01" max="999999" step="0.01" required></label><label>Frequência das cobranças<select name="frequencia"><option value="mensal">Mensal</option><option value="quinzenal">A cada 15 dias</option><option value="semanal">Semanal</option><option value="por_aula">Por aula (gerar uma por vez)</option></select></label><label>Intervalo da frequência<input name="intervalo" type="number" min="1" max="12" value="1" required><small>Em períodos; para “por aula”, informe o intervalo em dias quando for aplicável.</small></label><div class="form-acoes"><button class="botao" type="submit">Salvar plano</button></div></form><?php endif; ?></section>
      <section class="bloco"><h2>Planos ativos</h2><?php if(empty($planos)): ?><p class="vazio">Ainda não há planos ativos.</p><?php else: foreach($planos as $pl): ?><article class="cartao-item plano-item"><div><strong><?= pagueprofEsc($pl['aluno_nome']) ?></strong><span><?= $pl['modalidade']==='por_aula'?'Por aula':'Mensalidade' ?> · <?= pagueprofEsc($pl['frequencia']) ?> · <?= pagueprofDinheiro($pl['valor']) ?></span></div><form method="post" action="index.php?rota=gerar_cobrancas" class="form-inline"><input type="hidden" name="csrf" value="<?= $csrfValor ?>"><input type="hidden" name="plano_id" value="<?= (int)$pl['id'] ?>"><label>Primeiro vencimento<input type="date" name="data_vencimento" value="<?= pagueprofEsc(date('Y-m-d',strtotime('+1 month'))) ?>" required></label><label>Quantidade<input type="number" name="quantidade" min="1" max="12" value="3" required></label><button class="botao pequeno" type="submit">Gerar cobranças</button></form></article><?php endforeach; endif; ?></section>
    <?php elseif($pagina==='cobrancas'): ?>
      <div class="cabecalho-pagina"><div><p class="eyebrow">PROFESSOR</p><h1>Financeiro</h1><p>Consulte débitos, vencimentos e pagamentos recebidos.</p></div></div>
      <section class="bloco"><form method="get" class="filtros"><input type="hidden" name="rota" value="cobrancas"><label>De<input type="date" name="de" value="<?= pagueprofEsc($de) ?>"></label><label>Até<input type="date" name="ate" value="<?= pagueprofEsc($ate) ?>"></label><label>Situação<select name="status"><option value="">Todas</option><?php foreach(['pendente'=>'A pagar','paga'=>'Paga','em_analise'=>'Em análise','recusada'=>'Recusada','cancelada'=>'Cancelada'] as $v=>$t): ?><option value="<?= $v ?>" <?= $status===$v?'selected':'' ?>><?= $t ?></option><?php endforeach; ?></select></label><button class="botao secundario" type="submit">Filtrar</button></form></section>
      <section class="bloco"><h2>Cobranças</h2><?php if(empty($cobrancas)): ?><p class="vazio">Nenhuma cobrança neste filtro. Use Planos e cobranças para gerar novos vencimentos.</p><?php else: foreach($cobrancas as $c): [$label,$classe]=pagueprofStatus($c); ?><article class="linha-dados cobranca-item"><div><strong><?= pagueprofEsc($c['aluno_nome']) ?></strong><span>Vence <?= pagueprofData($c['data_vencimento']) ?> · <?= pagueprofEsc($c['modalidade']??'Cobrança') ?></span></div><div class="valor-estado"><strong><?= pagueprofDinheiro($c['valor']) ?></strong><span class="estado <?= $classe ?>"><?= pagueprofEsc($label) ?></span></div></article><?php endforeach; endif; ?></section>
    <?php elseif($pagina==='minhas_cobrancas'): ?>
      <div class="cabecalho-pagina"><div><p class="eyebrow">ALUNO</p><h1>Meus pagamentos</h1><p>Consulte cobranças e envie comprovantes para conferência.</p></div></div>
      <?php if(empty($cobrancas)): ?><section class="bloco"><p class="vazio">Nenhuma cobrança vinculada a esta conta. Confira se o professor cadastrou este mesmo e-mail.</p></section><?php else: foreach($cobrancas as $c): [$label,$classe]=pagueprofStatus($c); ?><section class="bloco pagamento-aluno"><div class="pagamento-topo"><div><p class="eyebrow">PROFESSOR</p><h2><?= pagueprofEsc($c['professor_nome']) ?></h2><p>Vencimento: <?= pagueprofData($c['data_vencimento']) ?></p></div><div class="pagamento-valor"><strong><?= pagueprofDinheiro($c['valor']) ?></strong><span class="estado <?= $classe ?>"><?= pagueprofEsc($label) ?></span></div></div>
        <?php if($c['status']==='paga'): ?><p class="alerta sucesso">Pagamento confirmado em <?= pagueprofData($c['data_pagamento']) ?>.</p><?php elseif($c['status']==='em_analise'): ?><p class="alerta informacao">Seu comprovante foi enviado e aguarda conferência do professor.</p><?php else: ?>
          <div class="manual-area"><h3>Enviar comprovante</h3><?php if(!empty($c['instrucoes_pagamento'])): ?><p><?= nl2br(pagueprofEsc($c['instrucoes_pagamento'])) ?></p><?php else: ?><p>Faça o pagamento conforme combinado com o professor e anexe o comprovante para conferência.</p><?php endif; ?><form method="post" enctype="multipart/form-data" action="index.php?rota=enviar_comprovante" class="form-inline"><input type="hidden" name="csrf" value="<?= $csrfValor ?>"><input type="hidden" name="cobranca_id" value="<?= (int)$c['id'] ?>"><label>Arquivo (PDF, JPG ou PNG; até 5 MB)<input type="file" name="comprovante" accept="application/pdf,image/jpeg,image/png" required></label><button class="botao secundario" type="submit">Enviar para análise</button></form></div>
        <?php endif; ?></section><?php endforeach; endif; ?>
    <?php elseif($pagina==='comprovantes'): ?>
      <div class="cabecalho-pagina"><div><p class="eyebrow">PROFESSOR</p><h1>Comprovantes</h1><p>Confira os anexos e confirme ou recuse o pagamento.</p></div></div>
      <?php if(empty($comprovantes)): ?><section class="bloco"><p class="vazio">Nenhum comprovante recebido.</p></section><?php else: foreach($comprovantes as $cp): ?><article id="comprovante-<?= (int)$cp['id'] ?>" class="bloco comprovante-item"><div class="bloco-cabecalho"><div><h2><?= pagueprofEsc($cp['aluno_nome']) ?></h2><p><?= pagueprofDinheiro($cp['valor']) ?> · vencimento <?= pagueprofData($cp['data_vencimento']) ?> · enviado <?= pagueprofData($cp['enviado_em']) ?></p></div><span class="estado <?= $cp['status']==='em_analise'?'azul':($cp['status']==='aprovado'?'verde':'vermelho') ?>"><?= pagueprofEsc(ucfirst(str_replace('_',' ',$cp['status']))) ?></span></div><a class="link-acao" target="_blank" rel="noopener" href="index.php?rota=baixar_comprovante&amp;id=<?= (int)$cp['id'] ?>">Abrir comprovante (<?= pagueprofEsc(strtoupper(pathinfo($cp['nome_original'],PATHINFO_EXTENSION))) ?>)</a><?php if($cp['status']==='em_analise'): ?><form method="post" action="index.php?rota=avaliar_comprovante" class="form-grid"><input type="hidden" name="csrf" value="<?= $csrfValor ?>"><input type="hidden" name="comprovante_id" value="<?= (int)$cp['id'] ?>"><label>Observação opcional<input name="observacao" maxlength="500" placeholder="Ex.: comprovante ilegível"></label><div class="form-acoes"><button class="botao" name="decisao" value="aprovado" type="submit">Confirmar pagamento</button><button class="botao perigo" name="decisao" value="rejeitado" type="submit">Recusar</button></div></form><?php elseif($cp['observacao_professor']): ?><p class="texto-secundario">Observação: <?= pagueprofEsc($cp['observacao_professor']) ?></p><?php endif; ?></article><?php endforeach; endif; ?>
    <?php elseif($pagina==='relatorios'): ?>
      <div class="cabecalho-pagina"><div><p class="eyebrow">PROFESSOR</p><h1>Relatórios</h1><p>Recebimentos pela data da baixa; cobranças em aberto pela data de vencimento.</p></div></div>
      <section class="bloco"><form method="get" class="filtros"><input type="hidden" name="rota" value="relatorios"><label>De<input type="date" name="de" value="<?= pagueprofEsc($de) ?>" required></label><label>Até<input type="date" name="ate" value="<?= pagueprofEsc($ate) ?>" required></label><button class="botao" type="submit">Atualizar período</button></form></section>
      <section class="grade-resumo"><article class="cartao-resumo"><span>Recebido</span><strong><?= pagueprofDinheiro($resumo['recebido']??0) ?></strong></article><article class="cartao-resumo"><span>Em aberto</span><strong><?= pagueprofDinheiro($resumo['aberto']??0) ?></strong></article><article class="cartao-resumo alerta-resumo"><span>Vencido</span><strong><?= pagueprofDinheiro($resumo['vencido']??0) ?></strong></article><article class="cartao-resumo"><span>Alunos ativos</span><strong><?= (int)($resumo['total_alunos']??0) ?></strong></article></section>
      <section class="bloco"><h2>Movimentações do período</h2><?php if(empty($cobrancas)): ?><p class="vazio">Nenhuma cobrança para o período selecionado.</p><?php else: foreach($cobrancas as $c): [$label,$classe]=pagueprofStatus($c); ?><article class="linha-dados"><div><strong><?= pagueprofEsc($c['aluno_nome']) ?></strong><span>Vencimento <?= pagueprofData($c['data_vencimento']) ?><?= $c['data_pagamento']?' · Pago em '.pagueprofData($c['data_pagamento']):'' ?></span></div><div class="valor-estado"><strong><?= pagueprofDinheiro($c['valor']) ?></strong><span class="estado <?= $classe ?>"><?= pagueprofEsc($label) ?></span></div></article><?php endforeach; endif; ?></section>
    <?php elseif($pagina==='configuracoes'): ?>
      <div class="cabecalho-pagina"><div><p class="eyebrow">PROFESSOR</p><h1>Recebimentos</h1><p>Escolha como seus alunos poderão pagar.</p></div></div>
      <section class="bloco"><h2>Instruções de pagamento</h2><form method="post" action="index.php?rota=configuracoes" class="form-grid"><input type="hidden" name="csrf" value="<?= $csrfValor ?>"><label class="campo-largo">Orientações para o aluno<textarea name="instrucoes_pagamento" rows="4" maxlength="1000" placeholder="Informe como o aluno deve pagar e enviar o comprovante."><?= pagueprofEsc($configuracao['instrucoes_pagamento']??'') ?></textarea></label><div class="form-acoes"><button class="botao" type="submit">Salvar instruções</button></div></form><p>O aluno envia um comprovante e o professor confirma ou recusa o pagamento.</p></section>
    <?php endif; ?>
  </main>
</div>
</body>
</html>
