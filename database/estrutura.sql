-- PagueProf: estrutura completa do MVP.
-- Selecione primeiro a base da hospedagem no phpMyAdmin e importe este arquivo.
-- Todas as tabelas são IF NOT EXISTS: não apaga nem altera dados existentes.
-- Compatibilidade MySQL/MariaDB: somente criado_em usa TIMESTAMP automático.

CREATE TABLE IF NOT EXISTS usuarios (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome VARCHAR(140) NOT NULL,
  email VARCHAR(190) NOT NULL,
  senha VARCHAR(255) NOT NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (id), UNIQUE KEY uq_usuarios_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS perfis (
  id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome VARCHAR(30) NOT NULL,
  PRIMARY KEY (id), UNIQUE KEY uq_perfis_nome (nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS usuarios_perfis (
  usuario_id BIGINT UNSIGNED NOT NULL,
  perfil_id SMALLINT UNSIGNED NOT NULL,
  PRIMARY KEY (usuario_id, perfil_id),
  CONSTRAINT fk_usuarios_perfis_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  CONSTRAINT fk_usuarios_perfis_perfil FOREIGN KEY (perfil_id) REFERENCES perfis(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO perfis (nome) VALUES ('professor'), ('aluno');

CREATE TABLE IF NOT EXISTS alunos (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  professor_id BIGINT UNSIGNED NOT NULL,
  usuario_id BIGINT UNSIGNED NULL,
  nome VARCHAR(140) NOT NULL,
  email VARCHAR(190) NOT NULL,
  telefone VARCHAR(40) NULL,
  materia VARCHAR(100) NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_aluno_prof_email (professor_id, email),
  KEY ix_alunos_usuario (usuario_id),
  CONSTRAINT fk_alunos_professor FOREIGN KEY (professor_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  CONSTRAINT fk_alunos_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS planos_cobranca (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  professor_id BIGINT UNSIGNED NOT NULL,
  aluno_id BIGINT UNSIGNED NOT NULL,
  modalidade VARCHAR(20) NOT NULL DEFAULT 'mensal',
  valor DECIMAL(10,2) NOT NULL,
  frequencia VARCHAR(20) NOT NULL DEFAULT 'mensal',
  intervalo INT UNSIGNED NOT NULL DEFAULT 1,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY ix_planos_prof_aluno (professor_id, aluno_id, ativo),
  CONSTRAINT fk_planos_professor FOREIGN KEY (professor_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  CONSTRAINT fk_planos_aluno FOREIGN KEY (aluno_id) REFERENCES alunos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS configuracoes_professor (
  professor_id BIGINT UNSIGNED NOT NULL,
  instrucoes_pagamento VARCHAR(1000) NULL,
  atualizado_em DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (professor_id),
  CONSTRAINT fk_config_professor FOREIGN KEY (professor_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cobrancas (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  referencia_externa VARCHAR(80) NOT NULL,
  professor_id BIGINT UNSIGNED NOT NULL,
  aluno_id BIGINT UNSIGNED NOT NULL,
  plano_id BIGINT UNSIGNED NULL,
  valor DECIMAL(10,2) NOT NULL,
  data_vencimento DATE NOT NULL,
  status VARCHAR(32) NOT NULL DEFAULT 'pendente',
  data_pagamento DATETIME NULL,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_cobrancas_referencia (referencia_externa),
  KEY ix_cobrancas_prof_status_data (professor_id, status, data_vencimento),
  KEY ix_cobrancas_aluno_data (aluno_id, data_vencimento),
  UNIQUE KEY uq_cobrancas_plano_data (plano_id, data_vencimento),
  CONSTRAINT fk_cobrancas_professor FOREIGN KEY (professor_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  CONSTRAINT fk_cobrancas_aluno FOREIGN KEY (aluno_id) REFERENCES alunos(id) ON DELETE CASCADE,
  CONSTRAINT fk_cobrancas_plano FOREIGN KEY (plano_id) REFERENCES planos_cobranca(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS comprovantes (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  cobranca_id BIGINT UNSIGNED NOT NULL,
  aluno_usuario_id BIGINT UNSIGNED NOT NULL,
  arquivo VARCHAR(255) NOT NULL,
  nome_original VARCHAR(255) NOT NULL,
  tipo_arquivo VARCHAR(80) NOT NULL,
  tamanho_bytes INT UNSIGNED NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'em_analise',
  observacao_professor VARCHAR(500) NULL,
  enviado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  avaliado_em DATETIME NULL,
  PRIMARY KEY (id),
  KEY ix_comprovantes_cobranca (cobranca_id, enviado_em),
  CONSTRAINT fk_comprovantes_cobranca FOREIGN KEY (cobranca_id) REFERENCES cobrancas(id) ON DELETE CASCADE,
  CONSTRAINT fk_comprovantes_aluno FOREIGN KEY (aluno_usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS notificacoes_email (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  cobranca_id BIGINT UNSIGNED NOT NULL,
  comprovante_id BIGINT UNSIGNED NULL,
  tipo VARCHAR(50) NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'pendente',
  tentativas TINYINT UNSIGNED NOT NULL DEFAULT 0,
  ultimo_erro VARCHAR(250) NULL,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  enviada_em DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_notificacao_cobranca_tipo (cobranca_id,tipo),
  KEY ix_notificacao_fila (status,tentativas,id),
  KEY ix_notificacao_comprovante (comprovante_id),
  CONSTRAINT fk_notificacao_cobranca FOREIGN KEY (cobranca_id) REFERENCES cobrancas(id) ON DELETE CASCADE,
  CONSTRAINT fk_notificacao_comprovante FOREIGN KEY (comprovante_id) REFERENCES comprovantes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
