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

## Como rodar o projeto
1. Clone o repositório.
2. Execute `composer install`.
3. Configure o `.env` com suas credenciais do MySQL.
4. Gere a chave da aplicação: `php artisan key:generate`.
5. Execute as migrations e o seeder:
   ```bash
   php artisan migrate:fresh --seed
