-- Migração aditiva e repetível: combina plano + vencimento sem duplicidade.
-- Antes de executar, confira separadamente se há pares duplicados:
-- SELECT plano_id, data_vencimento, COUNT(*) AS quantidade
-- FROM cobrancas WHERE plano_id IS NOT NULL
-- GROUP BY plano_id, data_vencimento HAVING COUNT(*) > 1;
-- Se houver linhas, faça backup e trate esses registros antes do índice; não os apague automaticamente.

SET @pagueprof_sql = IF(
  EXISTS (
    SELECT 1
    FROM information_schema.statistics
    WHERE table_schema = DATABASE()
      AND table_name = 'cobrancas'
      AND index_name = 'uq_cobrancas_plano_data'
  ),
  'DO 0',
  'ALTER TABLE cobrancas ADD UNIQUE KEY uq_cobrancas_plano_data (plano_id, data_vencimento)'
);
PREPARE pagueprof_stmt FROM @pagueprof_sql;
EXECUTE pagueprof_stmt;
DEALLOCATE PREPARE pagueprof_stmt;
