<?php
// Script para atualizar o banco com todas as melhorias
// Execute: http://localhost/hospital/server/setup_database.php

try {
    $pdo = new PDO('mysql:host=localhost;dbname=hospital;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "<h2>🚀 Atualizando Banco de Dados do Hospital</h2>";

    // Criar tabela usuarios
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS usuarios (
          id INT AUTO_INCREMENT PRIMARY KEY,
          nome VARCHAR(150) NOT NULL,
          email VARCHAR(150) NOT NULL UNIQUE,
          telefone VARCHAR(20),
          senha_hash VARCHAR(255) NOT NULL,
          tipo ENUM('paciente', 'admin') DEFAULT 'paciente',
          ativo BOOLEAN DEFAULT TRUE,
          created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
          updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "✅ Tabela 'usuarios' criada<br>";

    // Criar tabela sessions
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS sessions (
          id VARCHAR(255) PRIMARY KEY,
          user_id INT NOT NULL,
          ip_address VARCHAR(45),
          user_agent TEXT,
          created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
          expires_at TIMESTAMP NULL,
          FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "✅ Tabela 'sessions' criada<br>";

    // Criar tabela logs
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS logs (
          id INT AUTO_INCREMENT PRIMARY KEY,
          user_id INT NULL,
          acao VARCHAR(100) NOT NULL,
          tabela VARCHAR(50),
          registro_id INT NULL,
          dados TEXT,
          ip_address VARCHAR(45),
          created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
          FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "✅ Tabela 'logs' criada<br>";

    // Verificar se tabelas existentes ainda funcionam
    $tables = ['agendamentos', 'internacao', 'exames'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Tabela '$table' existe<br>";
        } else {
            echo "❌ Tabela '$table' não encontrada<br>";
        }
    }

    // Criar usuário admin de exemplo
    $adminEmail = 'admin@hospital.com';
    $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
    $stmt->execute([$adminEmail]);

    if (!$stmt->fetch()) {
        $senhaHash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO usuarios (nome, email, senha_hash, tipo) VALUES (?, ?, ?, ?)');
        $stmt->execute(['Administrador', $adminEmail, $senhaHash, 'admin']);
        echo "✅ Usuário admin criado: admin@hospital.com / admin123<br>";
    } else {
        echo "✅ Usuário admin já existe<br>";
    }

    echo "<h3>🎉 Banco atualizado com sucesso!</h3>";
    echo "<p><strong>Funcionalidades adicionadas:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Sistema de autenticação completo</li>";
    echo "<li>✅ Sessões de usuário seguras</li>";
    echo "<li>✅ Logs de auditoria</li>";
    echo "<li>✅ Dashboard do paciente</li>";
    echo "<li>✅ Notificações toast</li>";
    echo "<li>✅ Estados de loading</li>";
    echo "</ul>";

    echo "<p><a href='../pages/login.html'>🔗 Ir para Login</a></p>";
    echo "<p><a href='../pages/dashboard.html'>🔗 Ir para Dashboard</a></p>";

} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ Erro ao atualizar banco:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>