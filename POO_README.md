# JavaScript POO — VORTEX

Implementação do CRUD orientado a objetos ligado à API PHP/MySQL. A branch de entrega é `javascript-poo`.

## Executar

1. Coloque o projeto em `C:\\wamp64\\www\\vortex-3am` no WampServer e inicie Apache e MySQL.
2. Importe `data-base/dump.slq` no MySQL; o schema esperado é `vortex_3am`.
3. Confira host, banco e credenciais em `api/source/Config/Config.php`.
4. Abra `http://localhost/vortex-3am/views/alunos.html` em um navegador. A página usa módulos ES, então acesse via Apache (não abra o arquivo com `file://`).
5. Para criar treinos, autentique como personal e mantenha `vortex_token` e `vortex_user` na sessão local; as escritas de treino passam pelo controle de permissão da API.

## Arquitetura

- `alunos.js`, `Treinos.js`, `Exercicios.js` e `Users.js`: entidades independentes, com campos privados, construtor, getters/setters, validações próprias e `toJSON()`. As entidades não herdam de uma classe-base nem fazem chamadas de rede.
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
- **Herança:** `AlunosService`, `TreinosService`, `ExerciciosService` e `ImagesService` herdam os métodos HTTP de `HttpClientBase`. As entidades são classes independentes.
- **Composição:** `GestaoManager` compõe um serviço e uma entidade conforme a aba ativa. A camada visual coordena objetos, mas cada serviço trata apenas de seu endpoint.
- **Protótipos:** `prototypes.js` acrescenta `String.prototype.toTitleCase()` para rótulos e `Array.prototype.sortBy(chave, direção)` para ordenar qualquer coleção por campo. `Object.defineProperty` mantém os métodos não enumeráveis; ambos são usados na tela de gestão.

## Correspondência com o banco

- `alunos`: `personal_id`, `nome`, `email`, `telefone`, `objetivo`, `ativo`.
- `treinos`: `aluno_id`, `titulo`, `objetivo`, `observacoes`, `data_inicio`, `data_fim`, `status`.
- `exercicios`: `nome`, `categoria`, `descricao`, `ativo`.

O modelo `Treino` relaciona treino a aluno por `aluno_id`; o aluno aponta ao personal por `personal_id`. Exercícios têm CRUD próprio e ainda não são associados a `treino_exercicios` pela tela, pois os endpoints PHP registrados não oferecem CRUD para essa relação.

## Etapa 07 — upload de imagens

### Banco e arquivos

Importe primeiro `data-base/dump.slq`, que já cria `exercicios`, e depois execute `data-base/migrations/2026-11-03-exercicio-images.sql` no banco `vortex_3am`. A tabela `exercicio_images` relaciona cada imagem a um exercício por `exercicio_id`, com FK e `ON DELETE CASCADE`. O banco guarda somente nome aleatório, texto alternativo e posição. Os arquivos ficam em `api/uploads/users` e `api/uploads/exercicios`. JPEG, PNG e WebP são permitidos, até 5 MB por imagem. Para até 10 imagens por requisição, configure no `php.ini` `upload_max_filesize=5M`, `post_max_size=52M` e `max_file_uploads=10` e reinicie o Apache.

A tabela `exercicios` já é criada pelo dump. Para demonstrar no ambiente local, cadastre um exercício pela tela de gestão ou insira um registro de teste:

```sql
INSERT INTO exercicios (nome, categoria, descricao) VALUES ('Agachamento de demonstração', 'Pernas', 'Exercício para teste da galeria');
```

Use o ID do exercício criado na página de galeria.

### Rotas e permissões

| Método e rota | Multipart / parâmetros | Permissão e resposta |
| --- | --- | --- |
| `POST /users/photo` | `photo` (arquivo) | Token de usuário autenticado (aluno, personal ou admin); retorna `photo` e `url`. O ID vem do token, não do formulário. |
| `GET /exercicios/{exercicio_id}/images` | ID do exercício na rota | Admin autenticado; retorna array de imagens e URLs públicas. |
| `POST /exercicios/{exercicio_id}/images` | `images[]` (1 a 10 arquivos), `alt_text` opcional | Admin autenticado; retorna IDs e caminhos. |
| `DELETE /exercicios/{exercicio_id}/images/{image_id}` | IDs do exercício e da imagem na rota | Admin autenticado; só remove imagem que pertença ao exercício indicado. |

As imagens são enviadas em `FormData`; `HttpClientBase` remove o `Content-Type` padrão para o navegador acrescentar o boundary multipart. `Users::uploadPhoto` identifica o usuário pelo JWT validado. `ExerciciosImages` valida o exercício e a autorização, usa `Source\\Support\\Upload::image()` e delega os metadados ao model `ExercicioImage`, com prepared statements. Se o banco falhar, os arquivos e registros parciais daquela requisição são removidos. Ao atualizar foto de perfil, o arquivo antigo gerenciado só é removido depois do UPDATE bem-sucedido. A FK remove os metadados quando um exercício é excluído; o controller remove os arquivos correspondentes.

### Interface e evidências

- O perfil em `views/app.html` (aluno) e `views/admin.html` (personal/admin) tem seletor, prévia, envio, atualização da imagem e estados de carregamento, sucesso e erro. A validação no navegador aceita JPEG, PNG e WebP até 5 MB; o servidor valida MIME real e conteúdo.
- `views/exercicios-images.html` é a interface administrativa da galeria de exercícios. Informe um ID de exercício, carregue a galeria, escolha várias imagens, veja prévias e remova imagens individualmente. A listagem vem do banco, portanto permanece após atualizar a página.
- `ImagesService` estende `HttpClientBase` e centraliza as chamadas. A tela usa `textContent` para textos e restringe URLs de imagem ao mesmo host e à pasta de uploads da aplicação.
- Para evidenciar: faça login como admin, crie um exercício, abra `views/exercicios-images.html`, envie pelo menos duas imagens, confirme os cartões e atualize a página. Depois remova uma delas e confirme que a outra continua. No perfil, troque a foto e atualize a página para confirmar a persistência. O DevTools/Network mostra as rotas e os payloads multipart.
