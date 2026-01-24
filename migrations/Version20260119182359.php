<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260119182359 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD anonymized_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD is_active TINYINT(1) NOT NULL');
        $this->addSql('INSERT INTO `user` (`id`, `email`, `first_name`, `last_name`, `roles`, `password`, `created_at`, `deleted_at`, `anonymized_at`, `is_active`) VALUES (1, \'system@internal\', \'system\', \'system\', NULL, \'$2y$13$zKLYF85ToqWY1kM3wetJouvEU0zaPsYRbybQQzdJTzt\', \'2026-01-06 14:10:54\', NULL, NULL, 0);');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP anonymized_at, DROP is_active');
    }
}