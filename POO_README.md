# JavaScript POO — VORTEX

Implementação do CRUD orientado a objetos ligado à API PHP/MySQL. A branch de entrega é `javascript-poo`.

## Executar

1. Coloque o projeto em `C:\\wamp64\\www\\vortex-3am` no WampServer e inicie Apache e MySQL.
2. Importe `data-base/dump.slq` no MySQL; o schema esperado é `vortex_3am`.
3. Confira host, banco e credenciais em `api/source/Config/Config.php`.
4. Abra `http://localhost/vortex-3am/views/alunos.html` em um navegador. A página usa módulos ES, então acesse via Apache (não abra o arquivo com `file://`).
5. Para criar treinos, autentique como personal e mantenha `vortex_token` e `vortex_user` na sessão local; as escritas de treino passam pelo controle de permissão da API.

## Arquitetura

- `views/assets/_common/classes/EntityBase.js`: validação comum de IDs, textos e flags.
- `alunos.js`, `Treinos.js`, `Exercicios.js` e `Users.js`: entidades com campos privados, construtor, getters/setters e `toJSON()`. As entidades não fazem chamadas de rede.
- `views/assets/_common/classes/HttpClientBase.js`: encapsula URL, headers, token e verbos HTTP.
- `views/assets/_common/services/*Service.js`: serviços especializados, todos derivados de `HttpClientBase` e mapeados às rotas reais em `api/index.php`.
- `views/assets/app/scripts/gestao-poo.js`: coordena interface, validação por entidade, estados, toast e serviços. A página `views/alunos.html` oferece CRUD de alunos, treinos (entidade principal) e exercícios.
- `api/source/Controller` recebe os dados; `api/source/Models` executa SQL nas tabelas `alunos`, `treinos` e `exercicios` definidas em `data-base/dump.slq`.

## Rotas utilizadas

| Entidade | Listar | Consultar | Criar | Atualizar | Excluir |
| --- | --- | --- | --- | --- | --- |
| Alunos | `GET /alunos/list` | `GET /alunos/list/{id}` | `POST /alunos/` | `PUT /alunos/{id}` | `DELETE /alunos/{id}` |
| Treinos | `GET /treinos` | `GET /treinos/{id}` | `POST /treinos` | `PUT /treinos/{id}` | `DELETE /treinos/{id}` |
| Exercícios | `GET /exercicios/list` | `GET /exercicios/list/{id}` | `POST /exercicios/` | `PUT /exercicios/{id}` | `DELETE /exercicios/{id}` |

As operações POST usam `multipart/form-data` e PUT usa `application/x-www-form-urlencoded`, formatos que o roteador PHP deste projeto lê para popular os dados dos controllers. Os campos seguem o contrato dos controllers; para treino, datas usam `dataInicio` e `dataFim`. As respostas usam `{code,message,data}`. O formulário valida campos obrigatórios no navegador e nas entidades. Respostas 200/201 mostram toast de sucesso; problemas de validação/API mostram toast de erro. Listagens mostram estado de carregamento e estado vazio.

## Evidências reproduzíveis do CRUD

Com Apache/MySQL ativos e uma sessão válida, a própria tela registra os resultados das cinco etapas por entidade:

1. Abra uma aba, use **+ Novo** e salve: a resposta 201 aparece em toast e o novo registro entra na listagem.
2. Reabra o registro por **Editar**, altere um campo e salve: a resposta 200 e o valor atualizado aparecem na tabela.
3. Use **Editar** para confirmar a leitura do registro na linha; a tabela vem de `GET` da rota acima e inclui campos relacionados retornados pelo `JOIN` de treinos/alunos.
4. Use **Excluir** e confirme: a API responde 200 e a listagem é recarregada sem o registro.
5. Sem dados ou sem API, a tela apresenta estado vazio ou mensagem de erro em vez de falhar silenciosamente.

Rotas também podem ser inspecionadas no DevTools do navegador (Network): cada botão dispara a URL e verbo listados acima. Não há imagens de execução anexadas porque a captura depende de um banco local populado e de uma sessão pessoal ativa.

## Conceitos de POO

- **Encapsulamento:** propriedades como `#nome`, `#email` e `#aluno_id` são privadas; getters/setters validam limites e formato antes de aceitar valores.
- **Herança:** `AlunosService`, `TreinosService` e `ExerciciosService` herdam os métodos HTTP de `HttpClientBase`; as entidades reutilizam validações de `EntityBase`.
- **Composição:** `GestaoManager` compõe um serviço e uma entidade conforme a aba ativa. A camada visual coordena objetos, mas cada serviço trata apenas de seu endpoint.
- **Protótipos:** `prototypes.js` acrescenta `String.prototype.toTitleCase()` para rótulos e `Array.prototype.sortBy(chave, direção)` para ordenar qualquer coleção por campo. `Object.defineProperty` mantém os métodos não enumeráveis; ambos são usados na tela de gestão.

## Correspondência com o banco

- `alunos`: `personal_id`, `nome`, `email`, `telefone`, `objetivo`, `ativo`.
- `treinos`: `aluno_id`, `titulo`, `objetivo`, `observacoes`, `data_inicio`, `data_fim`, `status`.
- `exercicios`: `nome`, `categoria`, `descricao`, `ativo`.

O modelo `Treino` relaciona treino a aluno por `aluno_id`; o aluno aponta ao personal por `personal_id`. Exercícios têm CRUD próprio e ainda não são associados a `treino_exercicios` pela tela, pois os endpoints PHP registrados não oferecem CRUD para essa relação.
