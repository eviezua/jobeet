<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240217183634 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE job_affiliate (job_id INT NOT NULL, affiliate_id INT NOT NULL, PRIMARY KEY(job_id, affiliate_id))');
        $this->addSql('CREATE INDEX IDX_9ECA4ADEBE04EA9 ON job_affiliate (job_id)');
        $this->addSql('CREATE INDEX IDX_9ECA4ADE9F12C49A ON job_affiliate (affiliate_id)');
        $this->addSql('ALTER TABLE job_affiliate ADD CONSTRAINT FK_9ECA4ADEBE04EA9 FOREIGN KEY (job_id) REFERENCES job (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE job_affiliate ADD CONSTRAINT FK_9ECA4ADE9F12C49A FOREIGN KEY (affiliate_id) REFERENCES affiliate (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE job_affiliate DROP CONSTRAINT FK_9ECA4ADEBE04EA9');
        $this->addSql('ALTER TABLE job_affiliate DROP CONSTRAINT FK_9ECA4ADE9F12C49A');
        $this->addSql('DROP TABLE job_affiliate');
    }
}
