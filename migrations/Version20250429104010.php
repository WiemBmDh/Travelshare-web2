<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250429104010 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE liste_favoris (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, excursion_id INT NOT NULL, INDEX IDX_38095C63A76ED395 (user_id), INDEX IDX_38095C634AB4296F (excursion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE liste_favoris ADD CONSTRAINT FK_38095C63A76ED395 FOREIGN KEY (user_id) REFERENCES users (user_id)');
        $this->addSql('ALTER TABLE liste_favoris ADD CONSTRAINT FK_38095C634AB4296F FOREIGN KEY (excursion_id) REFERENCES excursions (excursion_id)');
        $this->addSql('ALTER TABLE excursions DROP FOREIGN KEY fk_id_guide');
        $this->addSql('ALTER TABLE excursions CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE excursions ADD CONSTRAINT FK_B044FAB5D7ED1D4B FOREIGN KEY (guide_id) REFERENCES guides (guide_id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4D7795EFE7927C74 ON guides (email)');
        $this->addSql('ALTER TABLE reclamations DROP FOREIGN KEY FK_1CAD6B76A76ED395');
        $this->addSql('ALTER TABLE reclamations ADD CONSTRAINT FK_1CAD6B76A76ED395 FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reponses DROP FOREIGN KEY FK_1E512EC62D6BA2D9');
        $this->addSql('ALTER TABLE reponses ADD CONSTRAINT FK_1E512EC62D6BA2D9 FOREIGN KEY (reclamation_id) REFERENCES reclamations (reclamation_id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE liste_favoris DROP FOREIGN KEY FK_38095C63A76ED395');
        $this->addSql('ALTER TABLE liste_favoris DROP FOREIGN KEY FK_38095C634AB4296F');
        $this->addSql('DROP TABLE liste_favoris');
        $this->addSql('ALTER TABLE excursions DROP FOREIGN KEY FK_B044FAB5D7ED1D4B');
        $this->addSql('ALTER TABLE excursions CHANGE image image VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE excursions ADD CONSTRAINT fk_id_guide FOREIGN KEY (guide_id) REFERENCES guides (guide_id)');
        $this->addSql('DROP INDEX UNIQ_4D7795EFE7927C74 ON guides');
        $this->addSql('ALTER TABLE reclamations DROP FOREIGN KEY FK_1CAD6B76A76ED395');
        $this->addSql('ALTER TABLE reclamations ADD CONSTRAINT FK_1CAD6B76A76ED395 FOREIGN KEY (user_id) REFERENCES users (user_id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reponses DROP FOREIGN KEY FK_1E512EC62D6BA2D9');
        $this->addSql('ALTER TABLE reponses ADD CONSTRAINT FK_1E512EC62D6BA2D9 FOREIGN KEY (reclamation_id) REFERENCES reclamations (reclamation_id) ON UPDATE CASCADE ON DELETE CASCADE');
    }
}
