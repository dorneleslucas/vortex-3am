# Migrações de banco

Para a Etapa 07, execute `2026-11-06-product-images.sql` no schema `vortex_3am` depois de importar `data-base/dump.slq`. A migração adiciona a tabela filha `product_images` com FK para `products(id)`. O dump deste checkout não contém `products`; por isso a migração cria uma tabela compatível com `api/source/Models/Product/Product.php` somente quando ela não existe.

Os arquivos binários são gravados pelo backend em `api/uploads/products`. A tabela armazena o nome gerado, o `product_id`, texto alternativo e posição. A FK remove os metadados em exclusão física; a rota de exclusão lógica também remove os arquivos do disco.
