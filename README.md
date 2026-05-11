# GingaHub - Sistema de Gestão para Academias

O **GingaHub** é uma plataforma desenvolvida para conectar alunos e academias, facilitando a gestão de matrículas, planos e treinos.

## Status do Projeto: Etapa 1 Concluída
A fundação do sistema (Autenticação e Identidade) está pronta.

### Funcionalidades Implementadas (Etapa 1):
- **Cadastro Multi-nível:** Registro de Alunos e Academias com tabelas vinculadas (`pessoas`, `enderecos`).
- **Segurança:** - Validação matemática real de CPF e CNPJ.
  - Confirmação de senha e criptografia com Bcrypt.
- **Autenticação:** Sistema de login único com redirecionamento inteligente baseado no tipo de usuário.
- **Matrículas:** Vínculo inicial entre Aluno e Academia com trava de duplicidade no banco de dados.
- **Automação de Testes:** Seeder configurado para popular o banco com dados de teste em um clique.

## Tecnologias Utilizadas
- **Framework:** Laravel 11
- **Banco de Dados:** MySQL
- **Linguagem:** PHP 8.2

## Como rodar o projeto
1. Clone o repositório.
2. Execute `composer install`.
3. Configure seu `.env` com as credenciais do banco.
4. Execute as migrations com o seeder:
   ```bash
   php artisan migrate:fresh --seed