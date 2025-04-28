<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250426193940 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reclamations DROP FOREIGN KEY FK_1CAD6B76A76ED395');
        $this->addSql('DROP INDEX fk_1cad6b76a76ed395 ON reclamations');
        $this->addSql('CREATE INDEX fk_user_id ON reclamations (user_id)');
        $this->addSql('ALTER TABLE reclamations ADD CONSTRAINT FK_1CAD6B76A76ED395 FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reponses DROP FOREIGN KEY FK_1E512EC62D6BA2D9');
        $this->addSql('DROP INDEX fk_1e512ec62d6ba2d9 ON reponses');
        $this->addSql('CREATE INDEX fk_reclamation_id ON reponses (reclamation_id)');
        $this->addSql('ALTER TABLE reponses ADD CONSTRAINT FK_1E512EC62D6BA2D9 FOREIGN KEY (reclamation_id) REFERENCES reclamations (reclamation_id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reclamations DROP FOREIGN KEY FK_1CAD6B76A76ED395');
        $this->addSql('DROP INDEX fk_user_id ON reclamations');
        $this->addSql('CREATE INDEX FK_1CAD6B76A76ED395 ON reclamations (user_id)');
        $this->addSql('ALTER TABLE reclamations ADD CONSTRAINT FK_1CAD6B76A76ED395 FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reponses DROP FOREIGN KEY FK_1E512EC62D6BA2D9');
        $this->addSql('DROP INDEX fk_reclamation_id ON reponses');
        $this->addSql('CREATE INDEX FK_1E512EC62D6BA2D9 ON reponses (reclamation_id)');
        $this->addSql('ALTER TABLE reponses ADD CONSTRAINT FK_1E512EC62D6BA2D9 FOREIGN KEY (reclamation_id) REFERENCES reclamations (reclamation_id) ON DELETE CASCADE');
    }
}
