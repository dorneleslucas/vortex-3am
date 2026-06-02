# Banco de Dados - VORTEX

Este documento descreve o que o banco de dados do VORTEX deve armazenar com base nas telas em `views/`.

O sistema e uma plataforma para personal trainers gerenciarem alunos, treinos, biblioteca de exercicios, execucoes, progresso, planos de assinatura e administracao geral.

## Visao geral do banco

O banco deve permitir:

- cadastrar visitantes interessados em testar a plataforma;
- autenticar usuarios do sistema;
- separar usuarios administradores, personais e alunos;
- controlar personais cadastrados, aprovados, ativos ou pendentes;
- controlar planos como Starter, Pro e Studio;
- limitar a quantidade de alunos por plano;
- cadastrar alunos vinculados a um personal;
- registrar objetivos, restricoes e observacoes dos alunos;
- montar treinos personalizados;
- manter uma biblioteca de exercicios;
- registrar series, repeticoes, descanso, carga e duracao dos exercicios;
- acompanhar execucoes de treinos;
- gerar metricas do dashboard, como total de alunos, treinos ativos, execucoes do dia e alertas;
- registrar progresso semanal, conclusao de treinos e carga total;
- armazenar alertas solicitando ajustes;
- registrar logs administrativos e acessos recentes;
- salvar configuracoes globais do sistema, como limite padrao de alunos e dias de teste.

## Padroes recomendados

- Usar MySQL.
- Usar nomes de tabelas no plural e em `snake_case`.
- Toda tabela principal deve ter um campo `id` inteiro, chave primaria e auto incremento.
- Toda tabela operacional deve ter `created_at`, `updated_at` e, quando necessario, `deleted_at` para exclusao logica.
- Campos de status devem usar valores previsiveis, por exemplo: `pending`, `active`, `inactive`, `blocked`, `approved`, `rejected`, `completed`, `cancelled`.
- Senhas nunca devem ser salvas em texto puro. Usar hash seguro no campo `password_hash`.

## Tabelas

### user_types

Define os tipos de usuarios do sistema.

Campos sugeridos:

- `id`: identificador do tipo.
- `name`: nome do tipo de usuario. Exemplos: `ADMIN`, `PERSONAL`, `STUDENT`.
- `description`: descricao do que esse tipo pode acessar.
- `created_at`: data de criacao.
- `updated_at`: data da ultima atualizacao.

Relacionamentos:

- Um tipo de usuario pode estar associado a muitos registros em `users`.

### users

Armazena os dados basicos de acesso ao sistema.

Campos sugeridos:

- `id`: identificador do usuario.
- `user_type_id`: referencia para `user_types`.
- `name`: nome completo.
- `email`: e-mail usado para login.
- `password_hash`: senha criptografada.
- `phone`: telefone ou WhatsApp.
- `status`: situacao do usuario, como `pending`, `active`, `inactive` ou `blocked`.
- `last_login_at`: data do ultimo login.
- `created_at`: data de criacao.
- `updated_at`: data da ultima atualizacao.
- `deleted_at`: data de exclusao logica.

Relacionamentos:

- Um usuario pode ser administrador, personal ou aluno.
- Um usuario do tipo personal deve ter um perfil em `personal_profiles`.
- Um usuario do tipo aluno pode ter um perfil em `students`.

### personal_profiles

Guarda informacoes especificas dos personal trainers.

Campos sugeridos:

- `id`: identificador do perfil do personal.
- `user_id`: referencia para o usuario do personal.
- `plan_id`: plano atual do personal.
- `display_name`: nome exibido nas telas.
- `bio`: breve descricao profissional.
- `document`: CPF ou documento de cadastro, se necessario.
- `specialty`: especialidade principal, como hipertrofia, mobilidade, corrida ou futebol.
- `approval_status`: status do cadastro, como `pending`, `approved` ou `rejected`.
- `approved_at`: data de aprovacao.
- `approved_by`: administrador que aprovou o cadastro.
- `student_limit`: limite individual de alunos, quando diferente do plano.
- `trial_ends_at`: data de fim do periodo de teste.
- `created_at`: data de criacao.
- `updated_at`: data da ultima atualizacao.
- `deleted_at`: data de exclusao logica.

Relacionamentos:

- Cada personal pertence a um usuario.
- Cada personal pode ter muitos alunos.
- Cada personal pode criar muitos treinos.
- Cada personal pode estar ligado a um plano.

### plans

Representa os planos mostrados no painel administrativo, como Starter, Pro e Studio.

Campos sugeridos:

- `id`: identificador do plano.
- `name`: nome do plano. Exemplos: Starter, Pro, Studio.
- `description`: descricao curta do plano.
- `student_limit`: quantidade maxima de alunos permitida.
- `price`: valor mensal do plano.
- `billing_period`: periodo de cobranca, como `monthly` ou `yearly`.
- `is_active`: indica se o plano esta disponivel.
- `created_at`: data de criacao.
- `updated_at`: data da ultima atualizacao.
- `deleted_at`: data de exclusao logica.

Relacionamentos:

- Um plano pode estar associado a muitos personais.
- Um plano pode ter muitas assinaturas em `subscriptions`.

### subscriptions

Controla a assinatura atual e o historico financeiro do personal.

Campos sugeridos:

- `id`: identificador da assinatura.
- `personal_id`: referencia para `personal_profiles`.
- `plan_id`: referencia para `plans`.
- `status`: status da assinatura, como `trial`, `active`, `overdue`, `cancelled` ou `expired`.
- `starts_at`: inicio da assinatura.
- `ends_at`: fim da assinatura.
- `trial_ends_at`: fim do periodo de teste.
- `paid_at`: data do ultimo pagamento confirmado.
- `created_at`: data de criacao.
- `updated_at`: data da ultima atualizacao.

Relacionamentos:

- Uma assinatura pertence a um personal.
- Uma assinatura pertence a um plano.
- Essa tabela ajuda a calcular metricas como "Planos pagos".

### leads

Armazena os cadastros feitos na pagina publica em "Receba acesso antecipado".

Campos sugeridos:

- `id`: identificador do lead.
- `name`: nome do personal informado no formulario.
- `email`: e-mail profissional informado.
- `status`: andamento do contato, como `new`, `contacted`, `converted` ou `discarded`.
- `source`: origem do lead, por exemplo `home_signup`.
- `notes`: observacoes internas.
- `created_at`: data de envio do formulario.
- `updated_at`: data da ultima atualizacao.

Relacionamentos:

- Um lead pode virar um usuario e um perfil de personal depois do cadastro completo.

### students

Armazena os alunos acompanhados por cada personal.

Campos sugeridos:

- `id`: identificador do aluno.
- `personal_id`: referencia para o personal responsavel.
- `user_id`: referencia opcional para `users`, caso o aluno tenha login proprio.
- `name`: nome completo do aluno.
- `email`: e-mail do aluno.
- `phone`: telefone ou WhatsApp.
- `goal`: objetivo principal, como hipertrofia, futebol, mobilidade ou resistencia.
- `training_context`: contexto do treino, como academia, casa, crossfit ou esporte.
- `restrictions`: restricoes, lesoes ou cuidados importantes.
- `notes`: observacoes do personal.
- `status`: situacao do aluno, como `active`, `inactive` ou `archived`.
- `created_at`: data de criacao.
- `updated_at`: data da ultima atualizacao.
- `deleted_at`: data de exclusao logica.

Relacionamentos:

- Um aluno pertence a um personal.
- Um aluno pode receber muitos treinos.
- Um aluno pode ter muitas execucoes e registros de progresso.

### exercises

Biblioteca de exercicios usada no editor de treinos e na area "Biblioteca".

Campos sugeridos:

- `id`: identificador do exercicio.
- `personal_id`: referencia opcional para o personal dono do exercicio. Se vazio, pode ser exercicio global.
- `name`: nome do exercicio, como Agachamento livre ou Supino reto.
- `category`: categoria, como forca, condicionamento, pernas, mobilidade ou superior.
- `muscle_group`: grupo muscular principal.
- `equipment`: equipamento necessario.
- `description`: instrucoes de execucao.
- `is_global`: indica se o exercicio aparece para todos os personais.
- `created_at`: data de criacao.
- `updated_at`: data da ultima atualizacao.
- `deleted_at`: data de exclusao logica.

Relacionamentos:

- Um exercicio pode aparecer em muitos treinos.
- Um personal pode criar exercicios proprios.

### workouts

Representa um treino criado pelo personal.

Campos sugeridos:

- `id`: identificador do treino.
- `personal_id`: referencia para o personal que criou o treino.
- `student_id`: referencia opcional para o aluno dono do treino.
- `name`: nome do treino, como Treino de Marina, Forca superior ou treino A/B.
- `goal`: objetivo do treino.
- `context`: contexto do treino, como academia, casa, futebol ou crossfit.
- `status`: situacao do treino, como `draft`, `active`, `expired` ou `archived`.
- `starts_at`: data de inicio.
- `expires_at`: data de vencimento.
- `created_at`: data de criacao.
- `updated_at`: data da ultima atualizacao.
- `deleted_at`: data de exclusao logica.

Relacionamentos:

- Um treino pertence a um personal.
- Um treino pode estar vinculado a um aluno.
- Um treino possui varios exercicios em `workout_exercises`.
- Um treino pode gerar varias execucoes em `workout_sessions`.

### workout_exercises

Tabela intermediaria que guarda os exercicios dentro de cada treino.

Campos sugeridos:

- `id`: identificador do item do treino.
- `workout_id`: referencia para `workouts`.
- `exercise_id`: referencia para `exercises`.
- `order_index`: ordem do exercicio no treino.
- `sets`: quantidade de series.
- `repetitions`: quantidade de repeticoes, quando aplicavel.
- `duration_seconds`: duracao do exercicio, quando for por tempo.
- `rest_seconds`: tempo de descanso.
- `load_kg`: carga planejada em quilos.
- `notes`: observacoes especificas para o aluno.
- `created_at`: data de criacao.
- `updated_at`: data da ultima atualizacao.

Relacionamentos:

- Um item pertence a um treino.
- Um item referencia um exercicio da biblioteca.

### workout_sessions

Registra cada execucao de treino feita ou planejada para um aluno.

Campos sugeridos:

- `id`: identificador da execucao.
- `workout_id`: referencia para o treino executado.
- `student_id`: referencia para o aluno.
- `scheduled_at`: data e horario planejados, como "Hoje, 18:30".
- `started_at`: inicio real da execucao.
- `finished_at`: fim real da execucao.
- `completion_percentage`: percentual de conclusao.
- `total_load_kg`: carga total registrada na execucao.
- `status`: status, como `scheduled`, `in_progress`, `completed`, `missed` ou `cancelled`.
- `student_feedback`: comentario do aluno.
- `personal_notes`: anotacoes do personal.
- `created_at`: data de criacao.
- `updated_at`: data da ultima atualizacao.

Relacionamentos:

- Uma execucao pertence a um treino.
- Uma execucao pertence a um aluno.
- Uma execucao pode ter detalhes por exercicio em `workout_session_exercises`.

### workout_session_exercises

Guarda o resultado real de cada exercicio executado em uma sessao de treino.

Campos sugeridos:

- `id`: identificador do registro.
- `workout_session_id`: referencia para `workout_sessions`.
- `workout_exercise_id`: referencia para o exercicio planejado no treino.
- `sets_done`: series realizadas.
- `repetitions_done`: repeticoes realizadas.
- `duration_done_seconds`: tempo realizado.
- `load_done_kg`: carga usada.
- `rest_done_seconds`: descanso real.
- `is_completed`: indica se o exercicio foi concluido.
- `notes`: observacoes sobre a execucao.
- `created_at`: data de criacao.
- `updated_at`: data da ultima atualizacao.

Relacionamentos:

- Um registro pertence a uma execucao de treino.
- Um registro aponta para um exercicio planejado.

### progress_records

Armazena indicadores de evolucao usados nos relatorios.

Campos sugeridos:

- `id`: identificador do registro.
- `student_id`: referencia para o aluno.
- `personal_id`: referencia para o personal.
- `reference_date`: data de referencia.
- `period_type`: tipo de periodo, como `daily`, `weekly` ou `monthly`.
- `completion_percentage`: percentual de treinos concluidos.
- `total_workouts`: quantidade de treinos planejados.
- `completed_workouts`: quantidade de treinos concluidos.
- `total_load_kg`: carga total no periodo.
- `notes`: resumo ou observacao do progresso.
- `created_at`: data de criacao.
- `updated_at`: data da ultima atualizacao.

Relacionamentos:

- Um aluno pode ter muitos registros de progresso.
- Esses dados alimentam relatorios como "Evolucao semanal".

### alerts

Registra alertas do painel, como alunos pedindo ajuste.

Campos sugeridos:

- `id`: identificador do alerta.
- `personal_id`: referencia para o personal.
- `student_id`: referencia opcional para o aluno relacionado.
- `workout_id`: referencia opcional para o treino relacionado.
- `type`: tipo do alerta, como `adjustment_request`, `expiring_workout`, `missed_session` ou `critical_log`.
- `title`: titulo curto do alerta.
- `message`: mensagem completa.
- `status`: status, como `open`, `read`, `resolved` ou `dismissed`.
- `created_at`: data de criacao.
- `resolved_at`: data de resolucao.

Relacionamentos:

- Um alerta pertence a um personal.
- Um alerta pode estar ligado a um aluno ou treino.
- Essa tabela ajuda a montar a metrica "Alertas".

### system_settings

Salva configuracoes globais exibidas na area administrativa.

Campos sugeridos:

- `id`: identificador da configuracao.
- `setting_key`: chave da configuracao, como `default_student_limit` ou `trial_days`.
- `setting_value`: valor salvo.
- `value_type`: tipo do valor, como `number`, `string`, `boolean` ou `json`.
- `description`: descricao do que a configuracao controla.
- `updated_by`: administrador que alterou a configuracao.
- `created_at`: data de criacao.
- `updated_at`: data da ultima atualizacao.

Relacionamentos:

- Uma configuracao pode ser alterada por um usuario administrador.
- Exemplos vindos da tela: limite padrao de alunos e dias de teste.

### audit_logs

Registra eventos importantes do sistema e acessos recentes.

Campos sugeridos:

- `id`: identificador do log.
- `user_id`: usuario que realizou a acao.
- `action`: acao executada, como `admin_login`, `plan_updated` ou `personal_approved`.
- `entity_type`: tipo de entidade afetada, como `user`, `plan`, `personal` ou `workout`.
- `entity_id`: id da entidade afetada.
- `severity`: gravidade, como `info`, `warning` ou `critical`.
- `ip_address`: IP de origem.
- `user_agent`: navegador ou cliente usado.
- `metadata`: dados extras em JSON.
- `created_at`: data do evento.

Relacionamentos:

- Um log pode estar associado a um usuario.
- Essa tabela alimenta a area "Logs" do painel administrativo e a metrica "Logs criticos".

## Relacionamentos principais

- `user_types` 1:N `users`
- `users` 1:1 `personal_profiles`
- `plans` 1:N `personal_profiles`
- `plans` 1:N `subscriptions`
- `personal_profiles` 1:N `students`
- `personal_profiles` 1:N `workouts`
- `personal_profiles` 1:N `exercises`
- `students` 1:N `workouts`
- `workouts` 1:N `workout_exercises`
- `exercises` 1:N `workout_exercises`
- `workouts` 1:N `workout_sessions`
- `students` 1:N `workout_sessions`
- `workout_sessions` 1:N `workout_session_exercises`
- `students` 1:N `progress_records`
- `personal_profiles` 1:N `alerts`
- `users` 1:N `audit_logs`

## Dados iniciais sugeridos

### Tipos de usuario

- `ADMIN`: acessa o painel administrativo.
- `PERSONAL`: acessa a area do personal.
- `STUDENT`: aluno acompanhado, caso exista login de aluno no futuro.

### Planos

- `Starter`: limite de 10 alunos.
- `Pro`: limite de 50 alunos.
- `Studio`: limite de 200 alunos.

### Configuracoes globais

- `default_student_limit`: limite padrao de alunos. Valor inicial: `50`.
- `trial_days`: dias de teste. Valor inicial: `14`.

### Exercicios globais

- Agachamento livre - Forca / Pernas.
- Supino reto - Forca / Superior.
- Remada baixa - Forca / Costas.
- Prancha frontal - Core / Mobilidade.
- Levantamento terra - Forca.
- Burpee - Condicionamento.
- Afundo alternado - Pernas.

## Metricas das telas e origem dos dados

- "Personais ativos": contar `personal_profiles` aprovados e usuarios ativos.
- "Cadastros pendentes": contar `personal_profiles` com `approval_status = pending`.
- "Planos pagos": calcular percentual de `subscriptions` ativas ou pagas.
- "Logs criticos": contar `audit_logs` com `severity = critical`.
- "Total de alunos": contar `students` ativos do personal logado.
- "Treinos ativos": contar `workouts` com `status = active`.
- "Execucoes hoje": contar `workout_sessions` do dia.
- "Alertas": contar `alerts` abertos do personal logado.
- "Evolucao semanal": usar `progress_records` ou calcular a partir de `workout_sessions`.

## Observacoes finais

As tabelas de `products`, `products_categories`, `faqs` e `faqs_categories` aparecem na API como exemplos didaticos, mas nao fazem parte do dominio principal mostrado nas views do VORTEX.

Para implementar o sistema real, a prioridade deve ser criar primeiro:

1. `user_types`
2. `users`
3. `plans`
4. `personal_profiles`
5. `students`
6. `exercises`
7. `workouts`
8. `workout_exercises`
9. `workout_sessions`
10. `leads`
11. `system_settings`
12. `audit_logs`
