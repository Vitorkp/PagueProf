<?php
declare(strict_types=1);

final class AplicacaoController
{
    private Financeiro $financeiro;

    public function __construct()
    {
        $this->financeiro = new Financeiro();
    }

    public function painel(): void
    {
        $u=$this->usuario();$dados=[];
        if($this->temPerfil('professor')){
            $primeiro=date('Y-m-01');$ultimo=date('Y-m-t');
            $dados['resumo']=$this->financeiro->resumo((int)$u['id'],$primeiro,$ultimo);
            $dados['sugestoes']=$this->financeiro->sugestoesRenovacao((int)$u['id']);
            $dados['pendentes']=array_slice($this->financeiro->cobrancasProfessor((int)$u['id']),0,6);
        }
        if($this->temPerfil('aluno'))$dados['cobrancasAluno']=$this->financeiro->cobrancasAluno((int)$u['id']);
        $this->render('dashboard',$dados);
    }

    public function alunos(): void
    {
        $p=$this->professor();$erro='';
        if(($_SERVER['REQUEST_METHOD']??'GET')==='POST'){
            $this->csrf();$nome=trim($_POST['nome']??'');$email=filter_var(trim($_POST['email']??''),FILTER_VALIDATE_EMAIL);
            $telefone=trim($_POST['telefone']??'');$materia=trim($_POST['materia']??'');
            if(mb_strlen($nome)<2 || !$email){$erro='Informe nome e e-mail válido.';}
            else{try{$this->financeiro->criarAluno((int)$p['id'],$nome,$email,$telefone,$materia);$this->flash('mensagem','Aluno cadastrado. Se já tiver uma conta PagueProf com esse e-mail, o acesso de aluno foi vinculado.');$this->ir('alunos');}catch(Throwable $e){error_log($e->getMessage());$erro='Não foi possível cadastrar. Verifique se este e-mail já está na sua lista.';}}
        }
        $this->render('alunos',['alunos'=>$this->financeiro->alunos((int)$p['id']),'erro'=>$erro]);
    }

    public function planos(): void
    {
        $p=$this->professor();$erro='';
        if(($_SERVER['REQUEST_METHOD']??'GET')==='POST'){
            $this->csrf();
            try{
                $id=$this->financeiro->criarPlano((int)$p['id'],(int)($_POST['aluno_id']??0),(string)($_POST['modalidade']??''),(float)str_replace(',','.',(string)($_POST['valor']??'0')),(string)($_POST['frequencia']??''),(int)($_POST['intervalo']??1));
                $this->flash('mensagem','Plano criado. Gere as cobranças pela opção abaixo.');$this->ir('planos');
            }catch(Throwable $e){error_log($e->getMessage());$erro='Confira aluno, valor, modalidade e frequência.';}
        }
        $this->render('planos',['planos'=>$this->financeiro->planos((int)$p['id']),'alunos'=>$this->financeiro->alunos((int)$p['id']),'erro'=>$erro]);
    }

    public function gerarCobrancas(): void
    {
        $p=$this->professor();$this->csrf();
        try{$n=$this->financeiro->gerarCobrancas((int)$p['id'],(int)($_POST['plano_id']??0),(string)($_POST['data_vencimento']??''),(int)($_POST['quantidade']??1));if($n>0){try{(new NotificacoesEmail())->processar(12,'cobranca_gerada');}catch(Throwable $emailErro){error_log('PagueProf envio de avisos: '.$emailErro->getMessage());}}$this->flash('mensagem',$n.' cobrança(s) gerada(s). Os avisos por e-mail foram colocados na fila.');}
        catch(Throwable $e){error_log($e->getMessage());$this->flash('erro','Não foi possível gerar as cobranças. Verifique o plano e a data.');}
        $this->ir('planos');
    }

    public function cobrancas(): void
    {
        $p=$this->professor();
        $de=(string)($_GET['de']??'');$ate=(string)($_GET['ate']??'');$status=(string)($_GET['status']??'');
        $this->render('cobrancas',['cobrancas'=>$this->financeiro->cobrancasProfessor((int)$p['id'],$de,$ate,$status),'de'=>$de,'ate'=>$ate,'status'=>$status]);
    }

    public function minhasCobrancas(): void
    {
        $a=$this->aluno();$this->render('minhas_cobrancas',['cobrancas'=>$this->financeiro->cobrancasAluno((int)$a['id'])]);
    }

    public function enviarComprovante(): void
    {
        $this->csrf();$u=$this->aluno();$id=(int)($_POST['cobranca_id']??0);$file=$_FILES['comprovante']??null;
        if(!$file || ($file['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_OK || (int)$file['size']>5*1024*1024){$this->flash('erro','Selecione um arquivo de até 5 MB.');$this->ir('minhas_cobrancas');}
        $finfo=new finfo(FILEINFO_MIME_TYPE);$mime=$finfo->file($file['tmp_name']);$permitidos=['application/pdf'=>'pdf','image/jpeg'=>'jpg','image/png'=>'png'];
        if(!isset($permitidos[$mime])){$this->flash('erro','Envie somente PDF, JPG ou PNG.');$this->ir('minhas_cobrancas');}
        $pasta=dirname(__DIR__,2).'/storage/comprovantes';
        if(!is_dir($pasta) && !mkdir($pasta,0750,true) && !is_dir($pasta)){$this->flash('erro','Não foi possível salvar o arquivo na hospedagem.');$this->ir('minhas_cobrancas');}
        $nome=bin2hex(random_bytes(20)).'.'.$permitidos[$mime];$destino=$pasta.'/'.$nome;
        if(!move_uploaded_file($file['tmp_name'],$destino)){$this->flash('erro','Não foi possível receber o arquivo.');$this->ir('minhas_cobrancas');}
        try{
            $comprovanteId=$this->financeiro->comprovanteDoAluno((int)$u['id'],$id,$nome,basename((string)$file['name']),$mime,(int)$file['size']);
            $mensagem='Comprovante recebido. O sistema avisará o professor por e-mail.';
            try{$resultado=(new NotificacoesEmail())->processar(1,'comprovante_'.$comprovanteId);if(($resultado['enviados']??0)>0)$mensagem='Comprovante recebido e aviso enviado ao professor.';elseif(($resultado['falhas']??0)>0)$mensagem='Comprovante recebido. O aviso ficou na fila e será tentado novamente.';}
            catch(Throwable $emailErro){error_log('PagueProf aviso de comprovante: '.$emailErro->getMessage());$mensagem='Comprovante recebido. O aviso ficou na fila para nova tentativa.';}
            $this->flash('mensagem',$mensagem);
        }
        catch(Throwable $e){@unlink($destino);error_log($e->getMessage());$this->flash('erro','Não foi possível anexar o comprovante a esta cobrança.');}
        $this->ir('minhas_cobrancas');
    }

    public function comprovantes(): void
    {
        $p=$this->professor();$this->render('comprovantes',['comprovantes'=>$this->financeiro->comprovantesProfessor((int)$p['id'])]);
    }

    public function abrirComprovante(): void
    {
        $id=(int)($_GET['id']??0);if($id<1){http_response_code(404);exit('Comprovante não encontrado.');}
        if(empty($_SESSION['usuario'])){unset($_SESSION['abrir_arquivo_comprovante_apos_login']);$_SESSION['abrir_revisao_comprovante_apos_login']=$id;$this->ir('login');}
        $p=$this->professor();if(!$this->financeiro->arquivoComprovante((int)$p['id'],$id)){http_response_code(404);exit('Comprovante não encontrado.');}
        $this->render('comprovantes',['comprovantes'=>$this->financeiro->comprovantesProfessor((int)$p['id'])]);
    }

    public function avaliarComprovante(): void
    {
        $p=$this->professor();$this->csrf();
        try{$this->financeiro->avaliarComprovante((int)$p['id'],(int)($_POST['comprovante_id']??0),(string)($_POST['decisao']??''),(string)($_POST['observacao']??''));$this->flash('mensagem','Análise do comprovante registrada.');}
        catch(Throwable $e){error_log($e->getMessage());$this->flash('erro','Não foi possível analisar o comprovante. Atualize a lista e tente novamente.');}
        $this->ir('comprovantes');
    }

    public function baixarComprovante(): void
    {
        $id=(int)($_GET['id']??0);if($id<1){http_response_code(404);exit('Arquivo não encontrado.');}
        if(empty($_SESSION['usuario'])){unset($_SESSION['abrir_revisao_comprovante_apos_login']);$_SESSION['abrir_arquivo_comprovante_apos_login']=$id;$this->ir('login');}
        $p=$this->professor();$f=$this->financeiro->arquivoComprovante((int)$p['id'],$id);
        if(!$f){http_response_code(404);exit('Arquivo não encontrado.');}
        $path=dirname(__DIR__,2).'/storage/comprovantes/'.basename($f['arquivo']);
        if(!is_file($path)){http_response_code(404);exit('Arquivo não encontrado.');}
        $mime=in_array($f['tipo_arquivo'],['application/pdf','image/jpeg','image/png'],true)?$f['tipo_arquivo']:'application/octet-stream';
        header('Content-Type: '.$mime);header('Content-Length: '.filesize($path));header('X-Content-Type-Options: nosniff');header('Content-Disposition: inline; filename="comprovante-'.$id.'.'.pathinfo($path,PATHINFO_EXTENSION).'"');readfile($path);exit;
    }

    public function relatorios(): void
    {
        $p=$this->professor();$de=(string)($_GET['de']??date('Y-m-01'));$ate=(string)($_GET['ate']??date('Y-m-t'));
        if(!$this->dataValida($de))$de=date('Y-m-01');if(!$this->dataValida($ate))$ate=date('Y-m-t');
        $this->render('relatorios',['resumo'=>$this->financeiro->resumo((int)$p['id'],$de,$ate),'cobrancas'=>$this->financeiro->cobrancasRelatorio((int)$p['id'],$de,$ate),'de'=>$de,'ate'=>$ate]);
    }

    public function configuracoes(): void
    {
        $p=$this->professor();$erro='';
        if(($_SERVER['REQUEST_METHOD']??'GET')==='POST'){
            $this->csrf();
            try{$this->financeiro->salvarConfiguracao((int)$p['id'],(string)($_POST['instrucoes_pagamento']??''));$this->flash('mensagem','Instruções salvas.');$this->ir('configuracoes');}
            catch(Throwable $e){$erro='Selecione uma opção válida e confira as instruções.';}
        }
        $this->render('configuracoes',['configuracao'=>$this->financeiro->configuracao((int)$p['id']),'erro'=>$erro]);
    }

    private function render(string $pagina,array $dados=[]): void
    {
        $usuario=$this->usuario();$mensagem=$_SESSION['mensagem']??'';$erroFlash=$_SESSION['erro']??'';unset($_SESSION['mensagem'],$_SESSION['erro']);
        extract($dados,EXTR_SKIP);require __DIR__.'/../Views/area.php';
    }

    private function usuario(): array
    {
        if(empty($_SESSION['usuario'])){$this->ir('login');}
        return $_SESSION['usuario'];
    }

    private function professor(): array
    {
        $u=$this->usuario();if(!$this->temPerfil('professor')){$this->flash('erro','Este acesso é exclusivo para professor.');$this->ir('painel');}return $u;
    }

    private function aluno(): array
    {
        $u=$this->usuario();if(!$this->temPerfil('aluno')){$this->flash('erro','Este acesso é exclusivo para aluno.');$this->ir('painel');}return $u;
    }

    private function temPerfil(string $perfil): bool
    {
        return in_array($perfil,(array)($_SESSION['usuario']['perfis']??[]),true);
    }

    private function csrf(): void
    {
        if(!hash_equals((string)($_SESSION['csrf']??''),(string)($_POST['csrf']??''))){http_response_code(419);exit('A sessão expirou. Atualize a página.');}
    }

    private function flash(string $tipo,string $texto): void { $_SESSION[$tipo]=$texto; }
    private function ir(string $rota): void { header('Location: index.php?rota='.rawurlencode($rota));exit; }
    private function dataValida(string $data): bool { $d=DateTimeImmutable::createFromFormat('!Y-m-d',$data);return $d && $d->format('Y-m-d')===$data; }
}
