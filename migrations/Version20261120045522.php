<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261120045522 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE company_profile (id INT AUTO_INCREMENT NOT NULL, company_name VARCHAR(255) NOT NULL, logo_url VARCHAR(255) NOT NULL, media_gallery JSON NOT NULL, employee_count VARCHAR(255) NOT NULL, sector VARCHAR(255) NOT NULL, short_presentation VARCHAR(255) NOT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_A105B0D8A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE job_offer (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, mission_description VARCHAR(255) NOT NULL, website_url VARCHAR(255) NOT NULL, tutor_name VARCHAR(255) NOT NULL, exact_adress VARCHAR(255) NOT NULL, required_study_level LONGTEXT NOT NULL, accepted_durations LONGTEXT NOT NULL, accepted_rhythm VARCHAR(255) NOT NULL, start_date DATETIME NOT NULL, perks JSON DEFAULT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, companyprofile_id INT NOT NULL, INDEX IDX_288A3A4EB27D3955 (companyprofile_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE match_conversation (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(255) NOT NULL, matched_at DATETIME NOT NULL, swipe_id INT NOT NULL, studentprofile_id INT NOT NULL, joboffer_id INT NOT NULL, UNIQUE INDEX UNIQ_BF0917622DE302F1 (swipe_id), INDEX IDX_BF091762E6087225 (studentprofile_id), INDEX IDX_BF091762BD612208 (joboffer_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, is_read TINYINT NOT NULL, send_at DATETIME NOT NULL, matchconversation_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_B6BD307F5C7C2A24 (matchconversation_id), INDEX IDX_B6BD307FA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE student_profile (id INT AUTO_INCREMENT NOT NULL, photo_url VARCHAR(255) NOT NULL, target_study_level VARCHAR(255) NOT NULL, obtained_diploma VARCHAR(100) NOT NULL, bio VARCHAR(255) NOT NULL, school_name VARCHAR(100) NOT NULL, school_status VARCHAR(255) NOT NULL, contract_duration VARCHAR(255) NOT NULL, alternation_rhythm VARCHAR(100) NOT NULL, desired_start_date DATETIME NOT NULL, cv_url VARCHAR(255) NOT NULL, target_sector VARCHAR(255) NOT NULL, is_public TINYINT NOT NULL, is_paused TINYINT NOT NULL, search_area_city VARCHAR(255) NOT NULL, search_area_department VARCHAR(255) NOT NULL, search_area_region VARCHAR(255) NOT NULL, search_area_radius_km INT NOT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_6C611FF7A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE swipe (id INT AUTO_INCREMENT NOT NULL, student_action VARCHAR(255) NOT NULL, company_action VARCHAR(255) NOT NULL, student_swiped_at DATETIME NOT NULL, company_swiped_at DATETIME NOT NULL, is_rewound TINYINT NOT NULL, studentprofile_id INT NOT NULL, joboffer_id INT NOT NULL, INDEX IDX_DB59E9A9E6087225 (studentprofile_id), INDEX IDX_DB59E9A9BD612208 (joboffer_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(50) NOT NULL, last_name VARCHAR(50) NOT NULL, role VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id))');
        $this->addSql('ALTER TABLE company_profile ADD CONSTRAINT FK_A105B0D8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE job_offer ADD CONSTRAINT FK_288A3A4EB27D3955 FOREIGN KEY (companyprofile_id) REFERENCES company_profile (id)');
        $this->addSql('ALTER TABLE match_conversation ADD CONSTRAINT FK_BF0917622DE302F1 FOREIGN KEY (swipe_id) REFERENCES swipe (id)');
        $this->addSql('ALTER TABLE match_conversation ADD CONSTRAINT FK_BF091762E6087225 FOREIGN KEY (studentprofile_id) REFERENCES student_profile (id)');
        $this->addSql('ALTER TABLE match_conversation ADD CONSTRAINT FK_BF091762BD612208 FOREIGN KEY (joboffer_id) REFERENCES job_offer (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F5C7C2A24 FOREIGN KEY (matchconversation_id) REFERENCES match_conversation (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
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
        $this->addSql('ALTER TABLE swipe DROP FOREIGN KEY FK_DB59E9A9E6087225');
        $this->addSql('ALTER TABLE swipe DROP FOREIGN KEY FK_DB59E9A9BD612208');
        $this->addSql('DROP TABLE company_profile');
        $this->addSql('DROP TABLE job_offer');
        $this->addSql('DROP TABLE match_conversation');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE student_profile');
        $this->addSql('DROP TABLE swipe');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
