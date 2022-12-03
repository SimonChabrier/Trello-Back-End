<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221203204403 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task ADD task_column_id INT NOT NULL');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25C1A44384 FOREIGN KEY (task_column_id) REFERENCES `column` (id)');
        $this->addSql('CREATE INDEX IDX_527EDB25C1A44384 ON task (task_column_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25C1A44384');
        $this->addSql('DROP INDEX IDX_527EDB25C1A44384 ON task');
        $this->addSql('ALTER TABLE task DROP task_column_id');
    }
}
