<?php
declare(strict_types=1);

final class Financeiro
{
    private function db(): PDO { return Database::conexao(); }

    public function configuracao(int $professorId): array
    {
        $s = $this->db()->prepare('INSERT IGNORE INTO configuracoes_professor (professor_id) VALUES (:id)');
        $s->execute(['id' => $professorId]);
        $s = $this->db()->prepare('SELECT * FROM configuracoes_professor WHERE professor_id=:id');
        $s->execute(['id' => $professorId]);
        return $s->fetch() ?: ['instrucoes_pagamento'=>''];
    }

    public function salvarConfiguracao(int $professorId, string $instrucoes): void
    {
        $sql = 'INSERT INTO configuracoes_professor (professor_id,instrucoes_pagamento,atualizado_em)
                VALUES (:id,:instr,NOW()) ON DUPLICATE KEY UPDATE instrucoes_pagamento=VALUES(instrucoes_pagamento), atualizado_em=NOW()';
        $this->db()->prepare($sql)->execute(['id'=>$professorId,'instr'=>substr(trim($instrucoes),0,1000)]);
    }

    public function alunos(int $professorId): array
    {
        $sql = 'SELECT a.*, (SELECT COUNT(*) FROM cobrancas c WHERE c.aluno_id=a.id AND c.professor_id=a.professor_id AND c.status IN ("pendente","recusada","em_analise") AND c.data_vencimento<CURDATE()) AS dividas_vencidas,
                (SELECT MAX(c.data_vencimento) FROM cobrancas c WHERE c.aluno_id=a.id AND c.professor_id=a.professor_id) AS ultimo_vencimento
                FROM alunos a WHERE a.professor_id=:p AND a.ativo=1 ORDER BY a.nome';
        $s=$this->db()->prepare($sql); $s->execute(['p'=>$professorId]); return $s->fetchAll();
    }

    public function criarAluno(int $professorId, string $nome, string $email, string $telefone, string $materia): int
    {
        $pdo=$this->db();
        $pdo->beginTransaction();
        try {
            $usuarioId=null;
            $s=$pdo->prepare('SELECT u.id FROM usuarios u JOIN usuarios_perfis up ON up.usuario_id=u.id JOIN perfis p ON p.id=up.perfil_id WHERE u.email=:email AND p.nome="aluno" LIMIT 1');
            $s->execute(['email'=>mb_strtolower(trim($email))]);
            $existente=$s->fetch();
            if ($existente) $usuarioId=(int)$existente['id'];
            $s=$pdo->prepare('INSERT INTO alunos (professor_id,usuario_id,nome,email,telefone,materia) VALUES (:p,:u,:n,:e,:t,:m)');
            $s->execute(['p'=>$professorId,'u'=>$usuarioId,'n'=>trim($nome),'e'=>mb_strtolower(trim($email)),'t'=>trim($telefone) ?: null,'m'=>trim($materia) ?: null]);
            $id=(int)$pdo->lastInsertId();
            $pdo->commit(); return $id;
        } catch (Throwable $e) { if($pdo->inTransaction())$pdo->rollBack(); throw $e; }
    }

    public function alunoDoProfessor(int $professorId, int $alunoId): ?array
    {
        $s=$this->db()->prepare('SELECT * FROM alunos WHERE professor_id=:p AND id=:a AND ativo=1');
        $s->execute(['p'=>$professorId,'a'=>$alunoId]); return $s->fetch() ?: null;
    }

    public function planos(int $professorId): array
    {
        $sql='SELECT pc.*,a.nome AS aluno_nome,a.email AS aluno_email FROM planos_cobranca pc JOIN alunos a ON a.id=pc.aluno_id WHERE pc.professor_id=:p AND pc.ativo=1 ORDER BY a.nome,pc.id DESC';
        $s=$this->db()->prepare($sql);$s->execute(['p'=>$professorId]);return $s->fetchAll();
    }

    public function criarPlano(int $professorId,int $alunoId,string $modalidade,float $valor,string $frequencia,int $intervalo): int
    {
        if (!$this->alunoDoProfessor($professorId,$alunoId)) throw new RuntimeException('Aluno não encontrado.');
        if (!in_array($modalidade,['mensal','por_aula'],true) || !in_array($frequencia,['mensal','quinzenal','semanal','por_aula'],true) || $valor<=0 || $intervalo<1 || $intervalo>12) throw new InvalidArgumentException('Dados de plano inválidos.');
        $s=$this->db()->prepare('INSERT INTO planos_cobranca (professor_id,aluno_id,modalidade,valor,frequencia,intervalo) VALUES (:p,:a,:m,:v,:f,:i)');
        $s->execute(['p'=>$professorId,'a'=>$alunoId,'m'=>$modalidade,'v'=>number_format($valor,2,'.',''),'f'=>$frequencia,'i'=>$intervalo]);return (int)$this->db()->lastInsertId();
    }

    public function gerarCobrancas(int $professorId,int $planoId,string $dataInicial,int $quantidade): int
    {
        $s=$this->db()->prepare('SELECT pc.*,a.nome FROM planos_cobranca pc JOIN alunos a ON a.id=pc.aluno_id WHERE pc.id=:id AND pc.professor_id=:p AND pc.ativo=1');
        $s->execute(['id'=>$planoId,'p'=>$professorId]);$plano=$s->fetch();if(!$plano)throw new RuntimeException('Plano não encontrado.');
        $data=DateTimeImmutable::createFromFormat('!Y-m-d',$dataInicial);
        if(!$data || $data->format('Y-m-d')!==$dataInicial || $quantidade<1 || $quantidade>12)throw new InvalidArgumentException('Data ou quantidade inválida.');
        $freq=$plano['frequencia'];$intervalo=max(1,(int)$plano['intervalo']);
        if($freq==='por_aula')$quantidade=min($quantidade,1);
        $diaReferencia=(int)$data->format('d');
        $pdo=$this->db();$criados=0;$pdo->beginTransaction();
        try {
            for($i=0;$i<$quantidade;$i++){
                $venc=$data->format('Y-m-d');
                $dup=$pdo->prepare('SELECT id FROM cobrancas WHERE plano_id=:plano AND data_vencimento=:data LIMIT 1');$dup->execute(['plano'=>$planoId,'data'=>$venc]);
                if(!$dup->fetch()){
                    $ref='pp-'.$planoId.'-'.bin2hex(random_bytes(12));
                    $ins=$pdo->prepare('INSERT INTO cobrancas (referencia_externa,professor_id,aluno_id,plano_id,valor,data_vencimento,status) VALUES (:ref,:p,:a,:pl,:v,:d,"pendente")');
                    $ins->execute(['ref'=>$ref,'p'=>$professorId,'a'=>$plano['aluno_id'],'pl'=>$planoId,'v'=>$plano['valor'],'d'=>$venc]);$cobrancaId=(int)$pdo->lastInsertId();
                    $notificacao=$pdo->prepare('INSERT IGNORE INTO notificacoes_email (cobranca_id,tipo,status,criado_em) VALUES (:id,"cobranca_gerada","pendente",NOW())');$notificacao->execute(['id'=>$cobrancaId]);$criados++;
                }
                if($freq==='mensal'){$proximo=$data->modify('first day of this month')->modify('+'.$intervalo.' months');$data=$proximo->setDate((int)$proximo->format('Y'),(int)$proximo->format('m'),min($diaReferencia,(int)$proximo->format('t')));}
                elseif($freq==='quinzenal')$data=$data->modify('+'.(15*$intervalo).' days');
                elseif($freq==='semanal')$data=$data->modify('+'.(7*$intervalo).' days');
                else $data=$data->modify('+'.$intervalo.' days');
            }
            $pdo->commit();return $criados;
        }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();throw $e;}
    }

    public function cobrancasProfessor(int $professorId, string $de='', string $ate='', string $status=''): array
    {
        $where=['c.professor_id=:p'];$args=['p'=>$professorId];
        if($de!=='' && $this->dataValida($de)){$where[]='c.data_vencimento>=:de';$args['de']=$de;}
        if($ate!=='' && $this->dataValida($ate)){$where[]='c.data_vencimento<=:ate';$args['ate']=$ate;}
        if(in_array($status,['pendente','paga','em_analise','recusada','cancelada'],true)){$where[]='c.status=:status';$args['status']=$status;}
        $sql='SELECT c.*,a.nome AS aluno_nome,a.email AS aluno_email,pc.modalidade,pc.frequencia FROM cobrancas c JOIN alunos a ON a.id=c.aluno_id LEFT JOIN planos_cobranca pc ON pc.id=c.plano_id WHERE '.implode(' AND ',$where).' ORDER BY c.data_vencimento DESC,c.id DESC';
        $s=$this->db()->prepare($sql);$s->execute($args);return $s->fetchAll();
    }

    public function cobrancasAluno(int $usuarioId): array
    {
        $sql='SELECT c.*,a.professor_id,a.email AS email_aluno,a.nome AS aluno_nome,u.nome AS professor_nome,cp.instrucoes_pagamento
              FROM cobrancas c JOIN alunos a ON a.id=c.aluno_id JOIN usuarios u ON u.id=c.professor_id
              LEFT JOIN configuracoes_professor cp ON cp.professor_id=c.professor_id
              WHERE a.usuario_id=:u ORDER BY c.data_vencimento DESC,c.id DESC';
        $s=$this->db()->prepare($sql);$s->execute(['u'=>$usuarioId]);return $s->fetchAll();
    }

    public function cobrancaDoAluno(int $usuarioId,int $cobrancaId): ?array
    {
        $s=$this->db()->prepare('SELECT c.*,a.usuario_id,a.professor_id,a.email AS email_aluno FROM cobrancas c JOIN alunos a ON a.id=c.aluno_id WHERE c.id=:c AND a.usuario_id=:u');
        $s->execute(['c'=>$cobrancaId,'u'=>$usuarioId]);return $s->fetch()?:null;
    }

    public function comprovanteDoAluno(int $usuarioId,int $cobrancaId,string $arquivo,string $original,string $mime,int $bytes): int
    {
        $c=$this->cobrancaDoAluno($usuarioId,$cobrancaId);if(!$c)throw new RuntimeException('Cobrança não encontrada.');
        if(in_array($c['status'],['paga','cancelada','em_analise'],true))throw new RuntimeException('Esta cobrança não aceita comprovante neste momento.');
        $pdo=$this->db();$pdo->beginTransaction();
        try{
            $s=$pdo->prepare('INSERT INTO comprovantes (cobranca_id,aluno_usuario_id,arquivo,nome_original,tipo_arquivo,tamanho_bytes,status) VALUES (:c,:u,:a,:n,:t,:b,"em_analise")');
            $s->execute(['c'=>$cobrancaId,'u'=>$usuarioId,'a'=>$arquivo,'n'=>substr($original,0,255),'t'=>$mime,'b'=>$bytes]);
            $id=(int)$pdo->lastInsertId();
            $pdo->prepare('UPDATE cobrancas SET status="em_analise",atualizado_em=NOW() WHERE id=:id')->execute(['id'=>$cobrancaId]);
            $tipo='comprovante_'.$id;
            $s=$pdo->prepare('INSERT INTO notificacoes_email (cobranca_id,comprovante_id,tipo,status,criado_em) VALUES (:c,:cp,:tipo,"pendente",NOW())');
            $s->execute(['c'=>$cobrancaId,'cp'=>$id,'tipo'=>$tipo]);
            $pdo->commit();return $id;
        }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();throw $e;}
    }

    public function comprovantesProfessor(int $professorId): array
    {
        $sql='SELECT cp.*,c.valor,c.data_vencimento,c.id AS cobranca_id,a.nome AS aluno_nome,a.email AS aluno_email
              FROM comprovantes cp JOIN cobrancas c ON c.id=cp.cobranca_id JOIN alunos a ON a.id=c.aluno_id
              WHERE c.professor_id=:p ORDER BY (cp.status="em_analise") DESC,cp.enviado_em DESC';
        $s=$this->db()->prepare($sql);$s->execute(['p'=>$professorId]);return $s->fetchAll();
    }

    public function avaliarComprovante(int $professorId,int $comprovanteId,string $decisao,string $observacao): void
    {
        if(!in_array($decisao,['aprovado','rejeitado'],true))throw new InvalidArgumentException('Decisão inválida.');
        $pdo=$this->db();$pdo->beginTransaction();
        try{
            $s=$pdo->prepare('SELECT cp.id,cp.cobranca_id,cp.status FROM comprovantes cp JOIN cobrancas c ON c.id=cp.cobranca_id WHERE cp.id=:id AND c.professor_id=:p FOR UPDATE');
            $s->execute(['id'=>$comprovanteId,'p'=>$professorId]);$row=$s->fetch();if(!$row || $row['status']!=='em_analise')throw new RuntimeException('Comprovante não está aguardando análise.');
            $pdo->prepare('UPDATE comprovantes SET status=:s,observacao_professor=:o,avaliado_em=NOW() WHERE id=:id')->execute(['s'=>$decisao,'o'=>substr(trim($observacao),0,500)?:null,'id'=>$comprovanteId]);
            if($decisao==='aprovado'){$pdo->prepare('UPDATE cobrancas SET status="paga",data_pagamento=NOW(),atualizado_em=NOW() WHERE id=:id')->execute(['id'=>$row['cobranca_id']]);$pdo->prepare('UPDATE notificacoes_email SET status="ignorada",ultimo_erro="Cobrança confirmada como paga" WHERE cobranca_id=:id AND comprovante_id IS NULL AND tipo IN ("cobranca_gerada","lembrete_vencimento") AND status IN ("pendente","falhou")')->execute(['id'=>$row['cobranca_id']]);}
            else $pdo->prepare('UPDATE cobrancas SET status="recusada",atualizado_em=NOW() WHERE id=:id')->execute(['id'=>$row['cobranca_id']]);
            $pdo->commit();
        }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();throw $e;}
    }

    public function arquivoComprovante(int $professorId,int $comprovanteId): ?array
    {
        $s=$this->db()->prepare('SELECT cp.* FROM comprovantes cp JOIN cobrancas c ON c.id=cp.cobranca_id WHERE cp.id=:id AND c.professor_id=:p');
        $s->execute(['id'=>$comprovanteId,'p'=>$professorId]);return $s->fetch()?:null;
    }

    public function resumo(int $professorId,string $de,string $ate): array
    {
        $s=$this->db()->prepare('SELECT COUNT(*) total_alunos FROM alunos WHERE professor_id=:p AND ativo=1');$s->execute(['p'=>$professorId]);$alunos=(int)$s->fetchColumn();
        $s=$this->db()->prepare('SELECT COALESCE(SUM(CASE WHEN status="paga" AND data_pagamento>=:de_pago AND data_pagamento<DATE_ADD(:ate_pago,INTERVAL 1 DAY) THEN valor ELSE 0 END),0) recebido,
            COALESCE(SUM(CASE WHEN status IN ("pendente","em_analise","recusada") AND data_vencimento BETWEEN :de_aberto AND :ate_aberto THEN valor ELSE 0 END),0) aberto,
            COALESCE(SUM(CASE WHEN status IN ("pendente","recusada") AND data_vencimento BETWEEN :de_vencido AND :ate_vencido AND data_vencimento<CURDATE() THEN valor ELSE 0 END),0) vencido
            FROM cobrancas WHERE professor_id=:p');
        $s->execute(['p'=>$professorId,'de_pago'=>$de,'ate_pago'=>$ate,'de_aberto'=>$de,'ate_aberto'=>$ate,'de_vencido'=>$de,'ate_vencido'=>$ate]);$r=$s->fetch()?:[];$r['total_alunos']=$alunos;return $r;
    }

    public function cobrancasRelatorio(int $professorId,string $de,string $ate): array
    {
        $sql='SELECT c.*,a.nome AS aluno_nome,a.email AS aluno_email,pc.modalidade,pc.frequencia FROM cobrancas c JOIN alunos a ON a.id=c.aluno_id LEFT JOIN planos_cobranca pc ON pc.id=c.plano_id
              WHERE c.professor_id=:p AND ((c.status="paga" AND c.data_pagamento>=:de_pago AND c.data_pagamento<DATE_ADD(:ate_pago,INTERVAL 1 DAY)) OR (c.status IN ("pendente","em_analise","recusada") AND c.data_vencimento BETWEEN :de_venc AND :ate_venc)) ORDER BY COALESCE(c.data_pagamento,c.data_vencimento) DESC,c.id DESC';
        $s=$this->db()->prepare($sql);$s->execute(['p'=>$professorId,'de_pago'=>$de,'ate_pago'=>$ate,'de_venc'=>$de,'ate_venc'=>$ate]);return $s->fetchAll();
    }

    public function sugestoesRenovacao(int $professorId): array
    {
        $planos=$this->planos($professorId);$out=[];
        foreach($planos as $p){
            $s=$this->db()->prepare('SELECT SUM(CASE WHEN data_vencimento>=CURDATE() AND status IN ("pendente","em_analise","recusada") THEN 1 ELSE 0 END) futuras, MAX(CASE WHEN status="paga" THEN data_vencimento ELSE NULL END) ultima_paga FROM cobrancas WHERE plano_id=:id');
            $s->execute(['id'=>$p['id']]);$r=$s->fetch();
            if((int)($r['futuras']??0)===0 && !empty($r['ultima_paga'])){
                $p['ultima_paga']=$r['ultima_paga'];$base=new DateTimeImmutable($r['ultima_paga']);$passo=max(1,(int)$p['intervalo']);$diaReferencia=(int)$base->format('d');
                if($p['frequencia']==='mensal'){$proximo=$base->modify('first day of this month')->modify('+'.$passo.' months');$base=$proximo->setDate((int)$proximo->format('Y'),(int)$proximo->format('m'),min($diaReferencia,(int)$proximo->format('t')));}
                elseif($p['frequencia']==='quinzenal')$base=$base->modify('+'.(15*$passo).' days');
                elseif($p['frequencia']==='semanal')$base=$base->modify('+'.(7*$passo).' days');
                else $base=$base->modify('+'.$passo.' days');
                $p['proximo_vencimento']=$base->format('Y-m-d');$out[]=$p;
            }
        }
        return $out;
    }

    private function dataValida(string $data): bool
    {
        $d=DateTimeImmutable::createFromFormat('!Y-m-d',$data);return $d && $d->format('Y-m-d')===$data;
    }
}
