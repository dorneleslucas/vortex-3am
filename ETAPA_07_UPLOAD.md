# SE2 — Etapa 07: upload de imagens e arquivos

## O que foi integrado

- Foto do perfil autenticado: `POST /users/photo`, upload multipart no campo `photo`, token validado pelo backend e identificação do usuário extraída do token.
- Galeria de produtos: upload de 1 a 10 imagens por requisição, listagem persistente e remoção individual, relacionada por `product_id` na tabela `product_images`.
- O backend usa `Source\Support\Upload::image()` para validar MIME real, conteúdo da imagem, limite de 5 MB e nome aleatório. Os arquivos ficam no sistema de arquivos; o banco guarda somente os metadados e o nome gerado.
- `ImagesService` estende `HttpClientBase`; o cliente remove `Content-Type` quando recebe `FormData`, deixando o navegador preencher o boundary.

## Arquivos principais

| Responsabilidade | Arquivo |
| --- | --- |
| Classe de upload compartilhada | `api/source/Support/Upload.php` |
| Foto do perfil no backend | `api/source/Controller/Users.php` |
| Upload/listagem/remoção de imagens de produto | `api/source/Controller/ProductsImages.php` |
| Consultas preparadas para metadados | `api/source/Models/ProductImage.php` |
| Rotas e operações de produto | `api/index.php`, `api/source/Controller/Products/Products.php` |
| Migração do banco | `data-base/migrations/2026-11-06-product-images.sql` |
| Serviço HTTP de imagens | `views/assets/_common/services/ImagesService.js` |
| Interface de perfil | `views/assets/app/scripts/profile-photo.js`, `views/admin.html`, `views/app.html` |
| Interface da galeria | `views/products-images.html`, `views/assets/app/scripts/product-gallery.js` |

## Rotas e contrato

Todas as rotas de imagens exigem token Bearer. Galeria de produto e operações de escrita de produto exigem administrador (`type_id = 1`). A rota da foto aceita os tipos de usuário autenticados `1`, `2` e `3`; o perfil alvo vem exclusivamente do token.

| Método e rota | Campos/dados | Resposta |
| --- | --- | --- |
| `POST /users/photo` | multipart: `photo` | `200`, `data.id`, `data.photo`, `data.url` |
| `POST /products/{product_id}/images` | multipart: `images[]` (ou `image`) e `alt_text` opcional; 1–10 imagens | `201`, lista com `id`, `product_id`, `image_path`, `alt_text`, `position`, `url` |
| `GET /products/{product_id}/images` | ID na rota | `200`, lista das imagens daquele produto; lista vazia é `data: []` |
| `DELETE /products/{product_id}/images/{image_id}` | IDs na rota | `200` se a imagem pertencer ao produto; `404` caso não pertença |

Imagens aceitas: JPEG, PNG e WebP, até 5 MB por arquivo. As URLs públicas seguem `/api/uploads/products/{nome-aleatorio}` e `/api/uploads/users/{nome-aleatorio}`. Nomes absolutos de diretórios do servidor não são retornados. Ao excluir logicamente um produto pela rota existente, os metadados e arquivos da galeria também são removidos; a FK ainda protege exclusões físicas no banco.

## Instalação e execução local

1. Inicie Apache e MySQL no WampServer e configure a conexão em `api/source/Config/Config.php`.
2. Importe o banco do projeto. Depois, execute `data-base/migrations/2026-11-06-product-images.sql` no schema `vortex_3am`. A migração cria `products` somente se ainda não existir e cria `product_images` com FK `ON DELETE CASCADE`.
3. No `php.ini` usado pelo Apache, configure `file_uploads=On`, `upload_max_filesize=5M` e `post_max_size=55M` (o POST comporta até dez imagens de 5 MB, mais multipart). Reinicie o Apache depois da alteração.
4. Entre como administrador em `http://localhost/vortex-3am/views/auth.html`. Na tela `views/admin.html`, use **Seu perfil** para enviar foto e **Administrar galeria de produtos** para abrir a galeria.
5. Informe um ID de produto existente. O ID fica lembrado no navegador; ao reabrir/atualizar a página, a galeria é buscada novamente na API.

## Evidências reproduzíveis

Com um produto existente e uma sessão administrativa ativa:

1. Abra `views/products-images.html`, informe o ID e carregue a galeria. Um produto sem imagens mostra o estado vazio.
2. Selecione duas imagens válidas e envie. A resposta `201`, as miniaturas e a listagem confirmam a gravação; em Network, veja `POST /products/{id}/images` como multipart.
3. Atualize a página. O ID lembrado é carregado novamente e as duas imagens devem continuar listadas, comprovando persistência em disco e banco.
4. Remova uma imagem. A resposta `200` remove somente a imagem escolhida; a outra permanece. Verifique `DELETE /products/{id}/images/{image_id}` em Network.
5. No perfil, envie uma foto válida e confirme a prévia, a resposta `200` e a atualização da foto exibida. Para erros, tente arquivo acima de 5 MB, formato não aceito, produto inexistente ou token expirado.

As etapas acima são o roteiro para capturar evidências no ambiente local. Este repositório não inclui capturas de tela de execução, pois elas dependem de banco e sessão administrativa locais.
