<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210731113515 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE drug (id INT AUTO_INCREMENT NOT NULL, substance_id INT NOT NULL, manufacturer_id INT NOT NULL, name VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, INDEX IDX_43EB7A3EC707E018 (substance_id), INDEX IDX_43EB7A3EA23B42D (manufacturer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE drug ADD CONSTRAINT FK_43EB7A3EC707E018 FOREIGN KEY (substance_id) REFERENCES substance (id)');
        $this->addSql('ALTER TABLE drug ADD CONSTRAINT FK_43EB7A3EA23B42D FOREIGN KEY (manufacturer_id) REFERENCES manufacturer (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE drug');
    }
}
