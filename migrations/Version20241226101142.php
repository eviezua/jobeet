<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241226101142 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE affiliate ADD owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE affiliate ALTER state SET NOT NULL');
        $this->addSql('ALTER TABLE affiliate ADD CONSTRAINT FK_597AA5CF7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_597AA5CF7E3C61F9 ON affiliate (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE affiliate DROP CONSTRAINT FK_597AA5CF7E3C61F9');
        $this->addSql('DROP INDEX IDX_597AA5CF7E3C61F9');
        $this->addSql('ALTER TABLE affiliate DROP owner_id');
        $this->addSql('ALTER TABLE affiliate ALTER state DROP NOT NULL');
    }
}
