-- Galeria de imagens para a entidade existente `exercicios`.
-- Execute no banco vortex_3am depois de importar data-base/dump.slq.
CREATE TABLE IF NOT EXISTS `exercicio_images` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `exercicio_id` INT NOT NULL,
    `image_path` VARCHAR(255) NOT NULL,
    `alt_text` VARCHAR(255) DEFAULT NULL,
    `position` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_exercicio_images_exercicio_id` (`exercicio_id`),
    CONSTRAINT `fk_exercicio_images_exercicio`
        FOREIGN KEY (`exercicio_id`) REFERENCES `exercicios` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
