# Hospital Website - Rafael & Davi

## 🚀 Visão Geral
Site moderno e responsivo para hospital com sistema completo de gestão de pacientes:
- **Home** (`pages/Tela-home.html`): Landing page com cards de navegação
- **Serviços** (`pages/servicos.html`): 4 serviços detalhados
- **Login/Cadastro** (`pages/login.html`): Sistema de autenticação completo
- **Dashboard** (`pages/dashboard.html`): Área do paciente com histórico
- **Agendar** (`pages/agendar.html`): Formulário de consulta médica
- **Exames** (`pages/exames.html`): Agendamento de exames laboratoriais
- **Internação** (`pages/internacao.html`): 4 tipos de leito hospitalar

## ✨ Funcionalidades

### 🔐 Sistema de Autenticação
- ✅ Cadastro e login de usuários reais
- ✅ Sessões seguras com expiração automática
- ✅ Controle de acesso baseado em roles (paciente/admin)
- ✅ Dashboard personalizado por usuário

### 📊 Gestão de Dados
- ✅ **Agendamentos**: Consultas médicas com especialidades
- ✅ **Exames**: Agendamentos laboratoriais com horários
- ✅ **Internações**: 4 tipos de leito (Individual, Premium, Enfermaria, UTI)
- ✅ **Logs de Auditoria**: Rastreamento completo de ações

### 🎨 Interface & UX
- ✅ Design glassmorphism/gradientes responsivo (mobile-first)
- ✅ Navegação fixa consistente com backdrop-blur
- ✅ Animações CSS + hover effects suaves
- ✅ Notificações toast para feedback visual
- ✅ Estados de loading em todos os formulários
- ✅ Validação client-side e server-side

### 🔧 Backend Seguro
- ✅ APIs RESTful em PHP 8 + MySQL
- ✅ PDO para queries seguras (prepared statements)
- ✅ Sanitização de inputs e validação robusta
- ✅ Logs de auditoria com IP e timestamps
- ✅ Controle de sessão server-side

## 📁 Estrutura
```
projeto.web.rafael-davi/
├── pages/             # Páginas HTML responsivas
│   ├── Tela-home.html # Landing page com navegação
│   ├── login.html     # Autenticação + cadastro
│   ├── dashboard.html # Área do paciente
│   ├── agendar.html   # Agendamento de consultas
│   ├── exames.html    # Agendamento de exames
│   ├── internacao.html# Tipos de internação
│   └── reserva-*.html # Formulários de reserva
├── css/
│   └── styles.css     # Estilos compartilhados + toasts
├── server/
│   ├── db.sql         # Schema completo MySQL
│   ├── setup_database.php # Setup automatizado
│   └── api/           # Backend APIs
│       ├── auth.php   # Autenticação e sessões
│       ├── dashboard.php # Dados do usuário
│       ├── agendamentos.php # Consultas
│       ├── exames.php # Exames laboratoriais
│       └── internacoes.php # Internações
├── img/               # Assets visuais
├── docs/              # Documentação
└── README.md
```

## 🗄️ Banco de Dados

### Tabelas Principais
- **`usuarios`**: Pacientes e admins com autenticação
- **`sessions`**: Sessões ativas de usuários
- **`agendamentos`**: Consultas médicas agendadas
- **`exames`**: Exames laboratoriais agendados
- **`internacao`**: Registros de internação hospitalar
- **`logs`**: Auditoria completa do sistema

### Usuário Admin de Teste
- **Email**: admin@hospital.com
- **Senha**: admin123

## 🚀 Como Usar

### 1. Configuração Inicial
```bash
# Execute o setup do banco (uma vez):
http://localhost/hospital/server/setup_database.php
```

### 2. Fluxo do Usuário
1. **Acesse**: `http://localhost/hospital/pages/Tela-home.html`
2. **Cadastre-se** ou **faça login**
3. **Navegue pelos serviços** (cards na home)
4. **Agende consultas/exames** através dos formulários
5. **Acesse o dashboard** para ver histórico

### 3. URLs Importantes
- **Home**: `http://localhost/hospital/pages/Tela-home.html`
- **Login**: `http://localhost/hospital/pages/login.html`
- **Dashboard**: `http://localhost/hospital/pages/dashboard.html`

## 🛠️ Tecnologias
- **Frontend**: HTML5, CSS3 (Glassmorphism), JavaScript (ES6+)
- **Backend**: PHP 8.0+, MySQL 8.0+
- **Servidor**: XAMPP (Apache + MySQL)
- **APIs**: RESTful com JSON
- **Segurança**: PDO, Password Hashing, CSRF Protection

## 📈 Melhorias Implementadas

### Fase 1 ✅ (Concluída)
- [x] Sistema de autenticação completo
- [x] Dashboard do paciente
- [x] Notificações toast
- [x] Estados de loading
- [x] Logs de auditoria
- [x] Validação server-side

### Fase 2 🔄 (Próximas)
- [ ] PWA (Progressive Web App)
- [ ] Notificações push
- [ ] Busca avançada
- [ ] Painel administrativo
- [ ] Relatórios e métricas

## 🔒 Segurança
- ✅ Password hashing com bcrypt
- ✅ Sessões server-side seguras
- ✅ Sanitização de todos os inputs
- ✅ Logs de auditoria completos
- ✅ Controle de acesso por roles
- ✅ Validação client + server-side

## 📱 Responsividade
- ✅ Mobile-first design
- ✅ Breakpoints otimizados
- ✅ Touch-friendly interfaces
- ✅ Performance em dispositivos móveis

---

**Desenvolvido por Rafael & Davi** 🏥💙
- **internacao**: Reservas de leito

### Configuração XAMPP
1. Copie projeto para `C:\xampp\htdocs\hospital`
2. Inicie Apache + MySQL
3. Importe `server/db.sql` no phpMyAdmin
4. Teste: `http://localhost/hospital/server/test_db.php`

## 🎮 Como Usar
1. Abra `http://localhost/hospital/pages/index.html`
2. Navegue pelos links do menu
3. **Login demo**: paciente@test.com / 123456
4. Agende consulta ou reserve internação

## 🔧 Tecnologias
- HTML5 semântico
- CSS3 (Grid, Flexbox, Animations, Backdrop-filter)
- Vanilla JavaScript (localStorage, fetch API)
- PHP 8 + MySQL (backend)
- Google Fonts (Poppins)
- Totalmente responsivo

## 📱 Responsivo
- Desktop, Tablet, Mobile
- Clamp() para tipografia fluida
- Media queries otimizadas

Desenvolvido por Rafael & Davi 🏥✨
