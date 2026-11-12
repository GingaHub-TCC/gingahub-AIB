# GingaHub - Sistema de Gestão para Academias

O **GingaHub** é uma plataforma desenvolvida para conectar alunos e academias, facilitando a gestão de matrículas, planos e treinos. O foco do projeto é a robustez técnica e a facilidade de uso para centros de artes marciais.

## Status do Projeto: Etapa 1 Concluída
A fundação do sistema (Autenticação, Identidade e Segurança) está operacional.

### Funcionalidades Implementadas (Etapa 1):
- **Cadastro Estruturado:** Registro de Alunos e Academias com herança lógica de tabelas (`pessoas`, `pessoa_fisicas`, `pessoa_juridicas`).
- **Segurança e Validação:**
    - Validação matemática de **CPF** e **CNPJ** (compatível com o novo padrão alfanumérico 2026).
    - Proteção contra **Mass Assignment** (atribuição de cargos via lógica de servidor).
    - Criptografia de senhas com `Bcrypt`.
- **Sessão Segura:** Logout automático por inatividade (5 minutos) para proteção de dados em terminais compartilhados.
- **Gestão de Vínculos:** Sistema de matrícula com **Reativação Inteligente** (evita duplicidade ao reconhecer ex-alunos).
- **Integridade Atômica:** Uso de **Database Transactions** em cadastros multi-tabelas.

## Etapa 2: Planos, Treinos e Assinaturas

### Painel da Academia (`/dashboard-academia`)
- **Locais** (reaproveitam a tabela `enderecos`), **Instrutores** e **Treinos** (tema, dia da semana, horário, instrutor e local).
- **Planos** com valor e periodicidade, escolhendo **quais treinos cada plano permite praticar** (tabela `plano_treino`).
- Plano já assinado não pode ser excluído, só **desativado** (some da vitrine e mantém o histórico).
- Lista de **alunos** (ativos e inativos) com os planos assinados, mais a grade semanal e as últimas assinaturas.

### Painel do Aluno (`/dashboard-aluno`)
- Matrícula em **várias academias**; com matrícula ativa, o aluno **abre a academia** (`/academia/{id}`) e vê planos e treinos.
- **Carrinho por academia** (guardado na sessão) → **Concluir pedido** cria um `pedido` (pagamento simulado, já `pago`) e uma `assinatura` por plano.
- **Desmatricular** inativa a matrícula (o registro continua) e **todas as assinaturas** dela, numa única transação.
- Cancelamento de uma assinatura avulsa e a grade "Minha semana" com os treinos liberados.

### Segurança
- Middlewares `aluno` e `academia` (`app/Http/Middleware/EnsureIs*.php`) restringem as rotas por tipo de usuário.
- Toda consulta parte da academia ou do aluno logado: um usuário não acessa nem altera dados de outro (retorna 404).
- Logout por inatividade (5 min), com aviso na tela no último minuto; login limitado a 5 tentativas por minuto.

### Contas de teste (senha `123456`)
| E-mail | Perfil |
|---|---|
| academia@teste.com | Academia Ginga Fit (com planos, treinos e instrutores) |
| dojo@teste.com | Academia Dojo Sol Nascente |
| aluno@teste.com | Aluno matriculado na Ginga Fit, assinando o plano "Regional 2x" |
| aluno2@teste.com | Aluno sem matrícula (para testar o fluxo do zero) |

### Testes automatizados
```bash
php artisan test
```

## Ambiente de Desenvolvimento
O projeto está sendo desenvolvido e testado no seguinte ambiente:

- **Servidor Web:** Apache 2.4.58 (Windows 64-bit)
- **Linguagem:** PHP 8.2.12
    - **Extensões Ativas:** `mysqli`, `curl`, `mbstring`, `openssl`
- **Banco de Dados:** MariaDB 10.4.32
    - **Protocolo:** v10 via TCP/IP
    - **Charset:** `utf8mb4_unicode_ci` (Suporte total a emojis e caracteres especiais)
- **Client DB:** libmysql - mysqlnd 8.2.12
- **Framework:** Laravel 12.58.0

## Como clonar o projeto localmente
1. Clone o repositório.
2. Execute `composer install`.
3. Configure o `.env` com suas credenciais do MySQL.
4. Gere a chave da aplicação: `php artisan key:generate`.
5. Execute as migrations e o seeder:
   ```bash
   php artisan migrate:fresh --seed

## Com rodar o projeto em servidor local
1. Execute o server na porta 8000:
   ```bash
   php artisan serve --port=8000
2. Abra no navegador o endereço 127.0.0.1:8000/(aqui adicione a rota para a view que deseja).
