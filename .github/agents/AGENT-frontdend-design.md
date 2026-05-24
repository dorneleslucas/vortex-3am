Excelente! Baseado no seu projeto **VORTEX** e no feedback detalhado, preparei o arquivo de **Frontend Design** preenchido especificamente para o seu sistema.

Este documento agora serve como o guia definitivo para qualquer IA (ou desenvolvedor) construir as telas, componentes e a organização do frontend, seguindo todas as regras do projeto.

---

# AGENT de Frontend Design - Projeto VORTEX

## Objetivo deste arquivo
Use este documento para orientar o planejamento visual e estrutural do frontend do sistema VORTEX. Ele deve ajudar **estudantes** a descrever suas decisões de interface e também orientar **agentes de IA** a propor telas, componentes e organização de arquivos sem quebrar as convenções do projeto.

## Como preencher
- Substitua cada conteúdo entre colchetes por informações do seu sistema.
- Escreva descrições curtas, objetivas e úteis para implementação.
- Registre primeiro o **layout estático**; o consumo da API e a renderização dinâmica virão depois.

## Regras do projeto que devem ser respeitadas
- O conteúdo deve ser escrito em **Português do Brasil**.
- O código, nomes de arquivos, classes, funções e identificadores devem ficar em **English**.
- O frontend deve usar **HTML semântico**.
- Não utilizar `<div>` e não utilizar `<table>` para montar interface.
- Não utilizar `jQuery`.
- Não utilizar eventos inline no HTML.
- Usar `document.querySelector` no JavaScript.
- Requisições HTTP devem usar `HttpClientBase.js` quando a integração com API começar.
- Arquivos compartilhados devem ir em `views/assets/_common/`; arquivos específicos devem ficar em `views/assets/public/`, `views/assets/app/` ou `views/assets/admin/`.

## Escopo inicial do frontend
- Nesta etapa, os dados serão exibidos de forma **estática**.
- O JavaScript poderá ser usado para comportamentos como `menu responsivo`, `abrir e fechar formulários de cadastro rápido`, `alternar visualização de listas (cards/linhas)`, `máscaras visuais em campos de CPF/telefone` e `interações de navegação entre abas`.
- Não planeje regra de negócio no frontend; a View apenas apresenta dados e interações da interface.

## Contexto geral da interface
- **Nome do sistema:** `VORTEX`
- **Objetivo principal:** `Substituir planilhas e mensagens de WhatsApp, permitindo que personal trainers gerenciem alunos, criem treinos personalizados (academia, futebol, crossfit, casa, atletas) e acompanhem a evolução de forma centralizada e simples.`
- **Público principal:** `Personal trainers (usuários principais) e seus alunos (usuários secundários). O personal tem idade adulta, nível básico a médio de tecnologia. O aluno pode ser de qualquer idade, mas com acesso restrito.`
- **Dispositivos prioritários:** `Desktop (para o personal, que monta treinos) e Mobile (para o aluno, que consulta e executa treinos). O sistema deve ser responsivo para ambos.`
- **Estilo desejado:** `Moderno, limpo e focado na função (functional). Cores escuras ou semi-escuras com elementos vibrantes (roxo/ciano) para dar uma sensação de "energia" e tecnologia, remetendo ao nome VORTEX.`

## Área Pública (`public`)
- **Quem acessa:** `Visitantes sem login (potenciais clientes Personal) e Personais que precisam se cadastrar.`
- **Objetivo da área:** `Apresentar o sistema VORTEX, seus benefícios, captar leads para teste, permitir o cadastro de novos Personais e login de Personais existentes.`
- **Telas previstas:** `home`, `sobre`, `contato`, `login`, `cadastro`.
- **Componentes principais:** `cabeçalho com logo e navegação, menu de links, banner principal com chamada para ação, seção de benefícios/diferenciais, rodapé com contato rápido e links.`
- **Ação principal esperada do usuário:** `Clicar em "Começar Agora" ou "Cadastrar" para criar sua conta como Personal Trainer.`

## Área de Aplicação (`app`) - Onde o Personal trabalha
- **Quem acessa:** `Personal Trainer autenticado.`
- **Objetivo da área:** `Ser o centro de operações do Personal: gerenciar seus alunos, criar e editar treinos, visualizar o progresso e as execuções dos alunos.`
- **Telas previstas:** `dashboard (visão geral com próximos treinos e alertas)`, `lista_de_alunos`, `cadastro_edicao_aluno`, `biblioteca_de_exercicios`, `editor_de_treinos`, `visualizacao_do_treino_do_aluno`, `relatorios_de_evolucao`.
- **Componentes principais:** `menu lateral (sidebar) para navegação, barra superior com saudação e logout, cards de resumo (total de alunos, treinos ativos), formulários bem estruturados, listas de alunos com busca, modal para criação rápida de exercício.`
- **Ação principal esperada do usuário:** `Clicar em "Alunos", selecionar um aluno, e depois clicar em "Atribuir Novo Treino" para montar a série usando a biblioteca de exercícios.`

## Área Administrativa (`admin`)
- **Quem acessa:** `Administrador do sistema (não o Personal). Em versão futura, para gerenciar múltiplos personais.`
- **Objetivo da área:** `Gerenciar os planos, permissões e contas dos Personais que usam a plataforma.`
- **Telas previstas:** `painel_admin`, `gestao_de_personais`, `logs_de_acesso`, `configuracoes_globais`.
- **Componentes principais:** `tabelas semânticas para listar Personais, filtros de busca, formulários de edição de plano/status, cards com métricas de uso da plataforma.`
- **Ação principal esperada do usuário:** `Aprovar um novo cadastro de Personal ou alterar o limite de alunos de um plano existente.`

## Navegação e organização visual
- **Estrutura de navegação principal:**
    - **Área Pública:** Menu horizontal superior.
    - **Área App (Personal):** Menu lateral (sidebar) colapsável com ícones + texto.
    - **Área Aluno:** Menu inferior (mobile) ou superior (desktop) simplificado.
- **Fluxo entre telas (mais comum):** `Login (Personal)` -> `Dashboard` -> `Alunos` -> `Seleciona Aluno` -> `Visualizar Treinos do Aluno` -> `Editor de Treinos (para criar/editar)` -> `Salvar`.
- **Hierarquia visual:** Ações principais (botões de "Salvar", "Adicionar Exercício", "Atribuir Treino") devem ter cores vibrantes (roxo/ciano). Listas de alunos e exercícios devem ter alto contraste para leitura rápida.
- **Estados importantes da interface:**
    - **Vazio:** Ilustração + texto amigável ("Nenhum aluno cadastrado ainda. Clique em + para adicionar").
    - **Carregando:** Skeleton screens ou spinner centralizado com texto "Carregando...".
    - **Erro visual:** Borda vermelha em campos de formulário + mensagem clara do erro.
    - **Sucesso:** Toast notification ou alerta verde no canto superior direito que desaparece em 3 segundos.

## Responsividade e acessibilidade
- **Breakpoints desejados:** `mobile (até 768px)`, `tablet (769px a 1024px)`, `desktop (acima de 1024px)`.
- **Ajustes esperados por tela:**
    - **Mobile:** Menu lateral vira menu hambúrguer. Formulários ocupam 100% da largura. Listas viram cards empilhados.
    - **Desktop:** Menu lateral sempre visível. Formulários em colunas (grid). Listas podem ser tabelas ou cards em grid.
- **Cuidados de acessibilidade:** Contraste mínimo de 4.5:1. Navegação por teclado (Tab) em formulários e menus. Textos alternativos para ícones e imagens. Labels claras para todos os inputs.
- **Elementos semânticos esperados:** `<header>`, `<nav>`, `<main>`, `<section>`, `<article>` (para um treino individual), `<aside>` (para dicas ou resumo), `<footer>`. **Sem `<div>` e sem `<table>` para layout.**

## Identidade visual
- **Paleta principal:**
    - **Fundo principal:** `#0B0F19` (Azul muito escuro)
    - **Cards e superfícies:** `#1A1F2E` (Cinza azulado escuro)
    - **Destaque primário (Ações):** `#7C3AED` (Roxo vibrante)
    - **Destaque secundário (Alertas/info):** `#06B6D4` (Ciano)
    - **Texto principal:** `#E2E8F0` (Branco suave)
    - **Texto secundário:** `#94A3B8` (Cinza claro)
    - **Sucesso:** `#10B981` (Verde)
    - **Erro:** `#EF4444` (Vermelho)
- **Tipografia:** `'Inter', sans-serif` (para corpo) e `'Poppins', sans-serif` (para títulos e destaques). Usar fallback `system-ui`.
- **Referências visuais:** `Sistemas como Linear.app, Notion, e Stripe Dashboard.`
- **Sensação que a interface deve transmitir:** `Energia, foco, profissionalismo, tecnologia e simplicidade. O Personal deve se sentir no controle e o aluno deve se sentir motivado.`

## Organização de arquivos esperada
- **Estilos compartilhados:** `views/assets/_common/styles/global.css`, `views/assets/_common/styles/variables.css`, `views/assets/_common/styles/components.css`
- **Scripts compartilhados:** `views/assets/_common/scripts/HttpClientBase.js`, `views/assets/_common/scripts/utils.js`, `views/assets/_common/scripts/menuMobile.js`
- **Estilos da área pública:** `views/assets/public/styles/home.css`, `views/assets/public/styles/login.css`, `views/assets/public/styles/cadastro.css`
- **Scripts da área pública:** `views/assets/public/scripts/login.js`, `views/assets/public/scripts/cadastro.js`
- **Estilos da aplicação (Personal):** `views/assets/app/styles/dashboard.css`, `views/assets/app/styles/alunos.css`, `views/assets/app/styles/treino-editor.css`
- **Scripts da aplicação (Personal):** `views/assets/app/scripts/dashboard.js`, `views/assets/app/scripts/lista-alunos.js`, `views/assets/app/scripts/editor-treinos.js`
- **Estilos da área administrativa:** `views/assets/admin/styles/admin-painel.css`
- **Scripts da área administrativa:** `views/assets/admin/scripts/gestao-personais.js`

## Limite entre etapa atual e integração futura
- **Agora:** Criar HTML semântico, CSS e interações estáticas em JavaScript (ex: `mostrarModalCadastroExercicio()`, `filtrarListaDeAlunos()`, `adicionarLinhaNaTabelaDeTreino()`). Os dados serão arrays de objetos JavaScript mockados.
- **Depois:** Integrar com a API usando `HttpClientBase.js` (ex: `api.get('/alunos')`), substituir os mocks, tratar erros assíncronos (try/catch) e renderizar os dados dinamicamente no DOM.
- **Ao propor código, a IA deve separar o que é mock estático (ex: `const mockAlunos = [...]`) do que será substituído por dados reais depois (ex: `// TODO: Substituir por chamada à API`).**

## Instrução final para estudantes e IA
Antes de implementar qualquer tela, preencha este arquivo com o máximo de clareza possível. Se uma informação ainda não estiver decidida, registre como `[a definir]` em vez de inventar requisitos.