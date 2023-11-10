<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231110170927 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE affiliate_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE job_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE affiliate (id INT NOT NULL, url VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, active BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE affiliate_category (affiliate_id INT NOT NULL, category_id INT NOT NULL, PRIMARY KEY(affiliate_id, category_id))');
        $this->addSql('CREATE INDEX IDX_CEC6AF8A9F12C49A ON affiliate_category (affiliate_id)');
        $this->addSql('CREATE INDEX IDX_CEC6AF8A12469DE2 ON affiliate_category (category_id)');
        $this->addSql('CREATE TABLE category (id INT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE job (id INT NOT NULL, category_id INT NOT NULL, type VARCHAR(255) NOT NULL, company VARCHAR(255) NOT NULL, logo VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, position VARCHAR(255) NOT NULL, location VARCHAR(255) NOT NULL, description TEXT NOT NULL, how_to_apply TEXT NOT NULL, token VARCHAR(255) NOT NULL, public BOOLEAN NOT NULL, activated BOOLEAN NOT NULL, email VARCHAR(255) NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FBD8E0F812469DE2 ON job (category_id)');
        $this->addSql('ALTER TABLE affiliate_category ADD CONSTRAINT FK_CEC6AF8A9F12C49A FOREIGN KEY (affiliate_id) REFERENCES affiliate (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE affiliate_category ADD CONSTRAINT FK_CEC6AF8A12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F812469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE affiliate_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE job_id_seq CASCADE');
        $this->addSql('ALTER TABLE affiliate_category DROP CONSTRAINT FK_CEC6AF8A9F12C49A');
        $this->addSql('ALTER TABLE affiliate_category DROP CONSTRAINT FK_CEC6AF8A12469DE2');
        $this->addSql('ALTER TABLE job DROP CONSTRAINT FK_FBD8E0F812469DE2');
        $this->addSql('DROP TABLE affiliate');
        $this->addSql('DROP TABLE affiliate_category');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE job');
    }
}
