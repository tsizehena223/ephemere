<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231022190117 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article ADD situation_travail VARCHAR(255) DEFAULT NULL, ADD domaine VARCHAR(255) DEFAULT NULL, ADD file_name VARCHAR(255) DEFAULT NULL, ADD file_size VARCHAR(255) DEFAULT NULL, DROP content');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article ADD content LONGTEXT DEFAULT NULL, DROP situation_travail, DROP domaine, DROP file_name, DROP file_size');
    }
}
