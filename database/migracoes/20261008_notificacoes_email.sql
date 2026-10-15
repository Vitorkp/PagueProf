-- Tabela de fila para avisos de cobrança, lembretes e novos comprovantes.
-- Pode ser importada mais de uma vez sem alterar registros existentes.
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
