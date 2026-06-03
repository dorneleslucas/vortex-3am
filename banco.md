# Banco de Dados - VORTEX

Resumo simples do banco de dados do VORTEX, uma plataforma para personal trainers gerenciarem alunos, treinos, exercicios, progresso, assinaturas e administracao.

## Padroes

- Banco: MySQL.
- Tabelas no plural e em `snake_case`.
- Toda tabela principal deve ter `id`.
- Tabelas operacionais devem ter `created_at` e `updated_at`.
- Usar `deleted_at` quando precisar de exclusao logica.
- Senhas devem ser salvas com hash em `password_hash`.

## Tabelas principais

### user_types

Tipos de usuario do sistema.

Campos:

- `id`
- `name`: `ADMIN`, `PERSONAL` ou `STUDENT`
- `description`
- `created_at`
- `updated_at`

### users

Usuarios que acessam o sistema.

Campos:

- `id`
- `user_type_id`
- `name`
- `email`
- `password_hash`
- `phone`
- `status`: `pending`, `active`, `inactive` ou `blocked`
- `last_login_at`
- `created_at`
- `updated_at`
- `deleted_at`

### personal_profiles

Dados especificos dos personais.

Campos:

- `id`
- `user_id`
- `plan_id`
- `display_name`
- `bio`
- `document`
- `specialty`
- `approval_status`: `pending`, `approved` ou `rejected`
- `approved_at`
- `approved_by`
- `student_limit`
- `trial_ends_at`
- `created_at`
- `updated_at`
- `deleted_at`

### plans

Planos da plataforma.

Campos:

- `id`
- `name`: Starter, Pro ou Studio
- `description`
- `student_limit`
- `price`
- `billing_period`: `monthly` ou `yearly`
- `is_active`
- `created_at`
- `updated_at`
- `deleted_at`

### subscriptions

Assinaturas dos personais.

Campos:

- `id`
- `personal_id`
- `plan_id`
- `status`: `trial`, `active`, `overdue`, `cancelled` ou `expired`
- `starts_at`
- `ends_at`
- `trial_ends_at`
- `paid_at`
- `created_at`
- `updated_at`

### leads

Interessados que preencheram o formulario publico.

Campos:

- `id`
- `name`
- `email`
- `status`: `new`, `contacted`, `converted` ou `discarded`
- `source`
- `notes`
- `created_at`
- `updated_at`

### students

Alunos vinculados a um personal.

Campos:

- `id`
- `personal_id`
- `user_id`
- `name`
- `email`
- `phone`
- `goal`
- `training_context`
- `restrictions`
- `notes`
- `status`: `active`, `inactive` ou `archived`
- `created_at`
- `updated_at`
- `deleted_at`

### exercises

Biblioteca de exercicios.

Campos:

- `id`
- `personal_id`
- `name`
- `category`
- `muscle_group`
- `equipment`
- `description`
- `is_global`
- `created_at`
- `updated_at`
- `deleted_at`

### workouts

Treinos criados pelo personal.

Campos:

- `id`
- `personal_id`
- `student_id`
- `name`
- `goal`
- `context`
- `status`: `draft`, `active`, `expired` ou `archived`
- `starts_at`
- `expires_at`
- `created_at`
- `updated_at`
- `deleted_at`

### workout_exercises

Exercicios dentro de um treino.

Campos:

- `id`
- `workout_id`
- `exercise_id`
- `order_index`
- `sets`
- `repetitions`
- `duration_seconds`
- `rest_seconds`
- `load_kg`
- `notes`
- `created_at`
- `updated_at`

### workout_sessions

Execucoes de treino feitas ou planejadas.

Campos:

- `id`
- `workout_id`
- `student_id`
- `scheduled_at`
- `started_at`
- `finished_at`
- `completion_percentage`
- `total_load_kg`
- `status`: `scheduled`, `in_progress`, `completed`, `missed` ou `cancelled`
- `student_feedback`
- `personal_notes`
- `created_at`
- `updated_at`

### workout_session_exercises

Resultado real de cada exercicio executado.

Campos:

- `id`
- `workout_session_id`
- `workout_exercise_id`
- `sets_done`
- `repetitions_done`
- `duration_done_seconds`
- `load_done_kg`
- `rest_done_seconds`
- `is_completed`
- `notes`
- `created_at`
- `updated_at`

### progress_records

Indicadores de evolucao dos alunos.

Campos:

- `id`
- `student_id`
- `personal_id`
- `reference_date`
- `period_type`: `daily`, `weekly` ou `monthly`
- `completion_percentage`
- `total_workouts`
- `completed_workouts`
- `total_load_kg`
- `notes`
- `created_at`
- `updated_at`

### alerts

Alertas exibidos no painel.

Campos:

- `id`
- `personal_id`
- `student_id`
- `workout_id`
- `type`
- `title`
- `message`
- `status`: `open`, `read`, `resolved` ou `dismissed`
- `created_at`
- `resolved_at`

### system_settings

Configuracoes globais do sistema.

Campos:

- `id`
- `setting_key`
- `setting_value`
- `value_type`: `number`, `string`, `boolean` ou `json`
- `description`
- `updated_by`
- `created_at`
- `updated_at`

### audit_logs

Logs administrativos e acessos importantes.

Campos:

- `id`
- `user_id`
- `action`
- `entity_type`
- `entity_id`
- `severity`: `info`, `warning` ou `critical`
- `ip_address`
- `user_agent`
- `metadata`
- `created_at`

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

## Dados iniciais

### Tipos de usuario

- `ADMIN`
- `PERSONAL`
- `STUDENT`

### Planos

- `Starter`: 10 alunos
- `Pro`: 50 alunos
- `Studio`: 200 alunos

### Configuracoes

- `default_student_limit`: `50`
- `trial_days`: `14`

### Exercicios globais

- Agachamento livre
- Supino reto
- Remada baixa
- Prancha frontal
- Levantamento terra
- Burpee
- Afundo alternado

## Metricas das telas

- Personais ativos: `personal_profiles` aprovados com usuario ativo.
- Cadastros pendentes: `personal_profiles.approval_status = pending`.
- Planos pagos: `subscriptions` ativas ou pagas.
- Logs criticos: `audit_logs.severity = critical`.
- Total de alunos: `students` ativos do personal logado.
- Treinos ativos: `workouts.status = active`.
- Execucoes hoje: `workout_sessions` do dia.
- Alertas: `alerts` abertos do personal logado.
- Evolucao semanal: `progress_records` ou `workout_sessions`.

## Prioridade de implementacao

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
