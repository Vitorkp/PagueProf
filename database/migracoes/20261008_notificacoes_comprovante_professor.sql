-- Acrescenta a referência do comprovante às notificações já existentes.
-- Repetível em MySQL/MariaDB: cada mudança só é executada se ainda faltar.
-- A tabela comprovantes e a fila notificacoes_email precisam existir antes.

SET @pagueprof_sql = IF(
  EXISTS (
    SELECT 1 FROM information_schema.columns
    WHERE table_schema = DATABASE()
      AND table_name = 'notificacoes_email'
      AND column_name = 'tipo'
      AND character_maximum_length < 50
  ),
  'ALTER TABLE notificacoes_email MODIFY tipo VARCHAR(50) NOT NULL',
  'DO 0'
);
PREPARE pagueprof_stmt FROM @pagueprof_sql;
EXECUTE pagueprof_stmt;
DEALLOCATE PREPARE pagueprof_stmt;

SET @pagueprof_sql = IF(
  NOT EXISTS (
    SELECT 1 FROM information_schema.columns
    WHERE table_schema = DATABASE()
      AND table_name = 'notificacoes_email'
      AND column_name = 'comprovante_id'
  ),
  'ALTER TABLE notificacoes_email ADD COLUMN comprovante_id BIGINT UNSIGNED NULL AFTER cobranca_id',
  'DO 0'
);
PREPARE pagueprof_stmt FROM @pagueprof_sql;
EXECUTE pagueprof_stmt;
DEALLOCATE PREPARE pagueprof_stmt;

SET @pagueprof_sql = IF(
  NOT EXISTS (
    SELECT 1 FROM information_schema.statistics
    WHERE table_schema = DATABASE()
      AND table_name = 'notificacoes_email'
      AND index_name = 'ix_notificacao_comprovante'
  ),
  'ALTER TABLE notificacoes_email ADD KEY ix_notificacao_comprovante (comprovante_id)',
  'DO 0'
);
PREPARE pagueprof_stmt FROM @pagueprof_sql;
EXECUTE pagueprof_stmt;
DEALLOCATE PREPARE pagueprof_stmt;

SET @pagueprof_sql = IF(
  NOT EXISTS (
    SELECT 1 FROM information_schema.table_constraints
    WHERE constraint_schema = DATABASE()
      AND table_name = 'notificacoes_email'
      AND constraint_name = 'fk_notificacao_comprovante'
      AND constraint_type = 'FOREIGN KEY'
  ),
  'ALTER TABLE notificacoes_email ADD CONSTRAINT fk_notificacao_comprovante FOREIGN KEY (comprovante_id) REFERENCES comprovantes(id) ON DELETE CASCADE',
  'DO 0'
);
PREPARE pagueprof_stmt FROM @pagueprof_sql;
EXECUTE pagueprof_stmt;
DEALLOCATE PREPARE pagueprof_stmt;
