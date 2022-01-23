<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220107130031 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD subscribe_id INT NOT NULL AFTER api_token');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649C72A4771 FOREIGN KEY (subscribe_id) REFERENCES subscribe (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649C72A4771 ON user (subscribe_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649C72A4771');
        $this->addSql('DROP INDEX IDX_8D93D649C72A4771 ON user');
        $this->addSql('ALTER TABLE user DROP subscribe_id');
    }
}
