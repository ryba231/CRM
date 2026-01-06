<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260106130919 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE workspace (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE workspace_user (id INT AUTO_INCREMENT NOT NULL, workspace_id INT DEFAULT NULL, user_id INT DEFAULT NULL, role VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_C971A58B82D40A1F (workspace_id), INDEX IDX_C971A58BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE workspace_user ADD CONSTRAINT FK_C971A58B82D40A1F FOREIGN KEY (workspace_id) REFERENCES workspace (id)');
        $this->addSql('ALTER TABLE workspace_user ADD CONSTRAINT FK_C971A58BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE contact ADD workspace_id INT NOT NULL');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E63882D40A1F FOREIGN KEY (workspace_id) REFERENCES workspace (id)');
        $this->addSql('CREATE INDEX IDX_4C62E63882D40A1F ON contact (workspace_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E63882D40A1F');
        $this->addSql('ALTER TABLE workspace_user DROP FOREIGN KEY FK_C971A58B82D40A1F');
        $this->addSql('ALTER TABLE workspace_user DROP FOREIGN KEY FK_C971A58BA76ED395');
        $this->addSql('DROP TABLE workspace');
        $this->addSql('DROP TABLE workspace_user');
        $this->addSql('DROP INDEX IDX_4C62E63882D40A1F ON contact');
        $this->addSql('ALTER TABLE contact DROP workspace_id');
    }
}
