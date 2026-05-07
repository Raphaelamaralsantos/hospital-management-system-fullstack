<?php
// Script para criar/atualizar tabelas no banco hospital

try {
    $pdo = new PDO('mysql:host=localhost;dbname=hospital;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Criar tabela exames se não existir
    $sql = "
    CREATE TABLE IF NOT EXISTS exames (
      id INT AUTO_INCREMENT PRIMARY KEY,
      nome VARCHAR(150) NOT NULL,
      email VARCHAR(150) NOT NULL,
      telefone VARCHAR(20) NOT NULL,
      tipo_exame VARCHAR(100) NOT NULL,
      data_exame DATE NOT NULL,
      hora_exame TIME NOT NULL,
      observacoes TEXT NULL,
      created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";

    $pdo->exec($sql);
    echo "✅ Tabela 'exames' criada/atualizada com sucesso!";

} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>