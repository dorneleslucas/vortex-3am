# API VORTEX

Esta pasta contem a API REST do sistema VORTEX, desenvolvido em PHP com MySQL.

O VORTEX e um sistema para personal trainers organizarem alunos e treinos. A API nao gera telas HTML; ela recebe requisicoes do front-end e devolve respostas em JSON.

## Entidade principal

A entidade principal do sistema e `treinos`.

Essa escolha faz sentido porque o objetivo central do VORTEX e permitir que o personal trainer cadastre, consulte, edite e exclua treinos de seus alunos. Usuarios, alunos e exercicios existem como apoio para que o treino tenha dono, aluno e conteudo.

## Banco de dados

O script do banco esta em:

`../data-base/dump.slq`

Nome do banco usado pela API:

`vortex_3am`

Tabelas principais:

- `usuarios`: guarda administrador e personal trainer.
- `alunos`: guarda os alunos atendidos pelo personal.
- `exercicios`: guarda a biblioteca simples de exercicios.
- `treinos`: entidade principal do sistema.
- `treino_exercicios`: relaciona exercicios com cada treino.

## Rotas da API

URL base local:

`http://localhost/vortex-3am/api`

### Teste da API

`GET /hello`

Retorna uma mensagem simples para verificar se a API esta funcionando.

### CRUD de treinos

`GET /treinos`

Lista todos os treinos cadastrados.

`GET /treinos/{id}`

Busca um treino especifico pelo ID.

`POST /treinos`

Cadastra um novo treino.

Exemplo de corpo JSON:

```json
{
  "alunoId": 1,
  "titulo": "Treino A - Hipertrofia",
  "objetivo": "Ganho de massa muscular",
  "observacoes": "Priorizar execucao controlada.",
  "dataInicio": "2026-06-01",
  "dataFim": "2026-07-01",
  "status": "ativo"
}
```

`PUT /treinos/{id}`

Atualiza um treino existente.

`DELETE /treinos/{id}`

Exclui um treino existente.

## Formato das respostas

As respostas seguem o formato JSON:

```json
{
  "code": 200,
  "type": "success",
  "status": "success",
  "message": "Lista de treinos",
  "data": []
}
```

## Arquivos principais

- `index.php`: registra as rotas da API.
- `source/Controller/Treinos.php`: controla as requisicoes do CRUD de treinos.
- `source/Models/Treino.php`: acessa a tabela `treinos` no banco.
- `source/Core/Connect.php`: cria a conexao PDO com o MySQL.
- `source/Config/Config.php`: define as configuracoes do projeto e do banco.
