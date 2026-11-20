<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261120085610 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company_profile ADD CONSTRAINT FK_A105B0D8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE job_offer ADD CONSTRAINT FK_288A3A4EB27D3955 FOREIGN KEY (companyprofile_id) REFERENCES company_profile (id)');
        $this->addSql('ALTER TABLE match_conversation ADD CONSTRAINT FK_BF0917622DE302F1 FOREIGN KEY (swipe_id) REFERENCES swipe (id)');
        $this->addSql('ALTER TABLE match_conversation ADD CONSTRAINT FK_BF091762E6087225 FOREIGN KEY (studentprofile_id) REFERENCES student_profile (id)');
        $this->addSql('ALTER TABLE match_conversation ADD CONSTRAINT FK_BF091762BD612208 FOREIGN KEY (joboffer_id) REFERENCES job_offer (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F5C7C2A24 FOREIGN KEY (matchconversation_id) REFERENCES match_conversation (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE student_profile CHANGE photo_url photo_url VARCHAR(255) DEFAULT NULL, CHANGE target_study_level target_study_level VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE student_profile ADD CONSTRAINT FK_6C611FF7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE swipe ADD CONSTRAINT FK_DB59E9A9E6087225 FOREIGN KEY (studentprofile_id) REFERENCES student_profile (id)');
        $this->addSql('ALTER TABLE swipe ADD CONSTRAINT FK_DB59E9A9BD612208 FOREIGN KEY (joboffer_id) REFERENCES job_offer (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company_profile DROP FOREIGN KEY FK_A105B0D8A76ED395');
        $this->addSql('ALTER TABLE job_offer DROP FOREIGN KEY FK_288A3A4EB27D3955');
        $this->addSql('ALTER TABLE match_conversation DROP FOREIGN KEY FK_BF0917622DE302F1');
        $this->addSql('ALTER TABLE match_conversation DROP FOREIGN KEY FK_BF091762E6087225');
        $this->addSql('ALTER TABLE match_conversation DROP FOREIGN KEY FK_BF091762BD612208');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F5C7C2A24');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FA76ED395');
        $this->addSql('ALTER TABLE student_profile DROP FOREIGN KEY FK_6C611FF7A76ED395');
        $this->addSql('ALTER TABLE student_profile CHANGE photo_url photo_url VARCHAR(255) NOT NULL, CHANGE target_study_level target_study_level VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE swipe DROP FOREIGN KEY FK_DB59E9A9E6087225');
        $this->addSql('ALTER TABLE swipe DROP FOREIGN KEY FK_DB59E9A9BD612208');
    }
}
