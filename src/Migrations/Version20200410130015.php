<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200410130015 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE class_group_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE email_alert_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE lesson_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE student_presence_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE refresh_tokens_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE class_group (id INT NOT NULL, name VARCHAR(50) NOT NULL, university VARCHAR(50) NOT NULL, ufr VARCHAR(50) NOT NULL, formation VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE class_group_admin (class_group_id INT NOT NULL, admin_id INT NOT NULL, PRIMARY KEY(class_group_id, admin_id))');
        $this->addSql('CREATE INDEX IDX_E04CE9274A9A1217 ON class_group_admin (class_group_id)');
        $this->addSql('CREATE INDEX IDX_E04CE927642B8210 ON class_group_admin (admin_id)');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, last_login TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, firstname VARCHAR(100) NOT NULL, lastname VARCHAR(100) NOT NULL, devices TEXT DEFAULT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON "user" (username)');
        $this->addSql('COMMENT ON COLUMN "user".devices IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE admin (id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE email_alert (id INT NOT NULL, email VARCHAR(255) NOT NULL, periodicity INT NOT NULL, last_sent TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE email_alert_class_group (email_alert_id INT NOT NULL, class_group_id INT NOT NULL, PRIMARY KEY(email_alert_id, class_group_id))');
        $this->addSql('CREATE INDEX IDX_584EA7889865403B ON email_alert_class_group (email_alert_id)');
        $this->addSql('CREATE INDEX IDX_584EA7884A9A1217 ON email_alert_class_group (class_group_id)');
        $this->addSql('CREATE TABLE teacher (id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE teacher_class_group (teacher_id INT NOT NULL, class_group_id INT NOT NULL, PRIMARY KEY(teacher_id, class_group_id))');
        $this->addSql('CREATE INDEX IDX_5D4BD9B941807E1D ON teacher_class_group (teacher_id)');
        $this->addSql('CREATE INDEX IDX_5D4BD9B94A9A1217 ON teacher_class_group (class_group_id)');
        $this->addSql('CREATE TABLE lesson (id INT NOT NULL, class_group_id INT DEFAULT NULL, teacher_id INT DEFAULT NULL, date_start TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, date_end TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F87474F34A9A1217 ON lesson (class_group_id)');
        $this->addSql('CREATE INDEX IDX_F87474F341807E1D ON lesson (teacher_id)');
        $this->addSql('CREATE TABLE student_presence (id INT NOT NULL, lesson_id INT NOT NULL, student_id INT NOT NULL, present BOOLEAN NOT NULL, late TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, excuse_description VARCHAR(255) DEFAULT NULL, excuse_proof VARCHAR(255) DEFAULT NULL, excuse_validated BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DAFC8B3ECDF80196 ON student_presence (lesson_id)');
        $this->addSql('CREATE INDEX IDX_DAFC8B3ECB944F1A ON student_presence (student_id)');
        $this->addSql('CREATE TABLE student (id INT NOT NULL, class_group_id INT DEFAULT NULL, photo_description VARCHAR(255) DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B723AF334A9A1217 ON student (class_group_id)');
        $this->addSql('CREATE TABLE refresh_tokens (id INT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9BACE7E1C74F2195 ON refresh_tokens (refresh_token)');
        $this->addSql('ALTER TABLE class_group_admin ADD CONSTRAINT FK_E04CE9274A9A1217 FOREIGN KEY (class_group_id) REFERENCES class_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE class_group_admin ADD CONSTRAINT FK_E04CE927642B8210 FOREIGN KEY (admin_id) REFERENCES admin (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE admin ADD CONSTRAINT FK_880E0D76BF396750 FOREIGN KEY (id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE email_alert_class_group ADD CONSTRAINT FK_584EA7889865403B FOREIGN KEY (email_alert_id) REFERENCES email_alert (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE email_alert_class_group ADD CONSTRAINT FK_584EA7884A9A1217 FOREIGN KEY (class_group_id) REFERENCES class_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE teacher ADD CONSTRAINT FK_B0F6A6D5BF396750 FOREIGN KEY (id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE teacher_class_group ADD CONSTRAINT FK_5D4BD9B941807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE teacher_class_group ADD CONSTRAINT FK_5D4BD9B94A9A1217 FOREIGN KEY (class_group_id) REFERENCES class_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F34A9A1217 FOREIGN KEY (class_group_id) REFERENCES class_group (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F341807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE student_presence ADD CONSTRAINT FK_DAFC8B3ECDF80196 FOREIGN KEY (lesson_id) REFERENCES lesson (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE student_presence ADD CONSTRAINT FK_DAFC8B3ECB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF334A9A1217 FOREIGN KEY (class_group_id) REFERENCES class_group (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF33BF396750 FOREIGN KEY (id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE class_group_admin DROP CONSTRAINT FK_E04CE9274A9A1217');
        $this->addSql('ALTER TABLE email_alert_class_group DROP CONSTRAINT FK_584EA7884A9A1217');
        $this->addSql('ALTER TABLE teacher_class_group DROP CONSTRAINT FK_5D4BD9B94A9A1217');
        $this->addSql('ALTER TABLE lesson DROP CONSTRAINT FK_F87474F34A9A1217');
        $this->addSql('ALTER TABLE student DROP CONSTRAINT FK_B723AF334A9A1217');
        $this->addSql('ALTER TABLE admin DROP CONSTRAINT FK_880E0D76BF396750');
        $this->addSql('ALTER TABLE teacher DROP CONSTRAINT FK_B0F6A6D5BF396750');
        $this->addSql('ALTER TABLE student DROP CONSTRAINT FK_B723AF33BF396750');
        $this->addSql('ALTER TABLE class_group_admin DROP CONSTRAINT FK_E04CE927642B8210');
        $this->addSql('ALTER TABLE email_alert_class_group DROP CONSTRAINT FK_584EA7889865403B');
        $this->addSql('ALTER TABLE teacher_class_group DROP CONSTRAINT FK_5D4BD9B941807E1D');
        $this->addSql('ALTER TABLE lesson DROP CONSTRAINT FK_F87474F341807E1D');
        $this->addSql('ALTER TABLE student_presence DROP CONSTRAINT FK_DAFC8B3ECDF80196');
        $this->addSql('ALTER TABLE student_presence DROP CONSTRAINT FK_DAFC8B3ECB944F1A');
        $this->addSql('DROP SEQUENCE class_group_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE email_alert_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE lesson_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE student_presence_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE refresh_tokens_id_seq CASCADE');
        $this->addSql('DROP TABLE class_group');
        $this->addSql('DROP TABLE class_group_admin');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE admin');
        $this->addSql('DROP TABLE email_alert');
        $this->addSql('DROP TABLE email_alert_class_group');
        $this->addSql('DROP TABLE teacher');
        $this->addSql('DROP TABLE teacher_class_group');
        $this->addSql('DROP TABLE lesson');
        $this->addSql('DROP TABLE student_presence');
        $this->addSql('DROP TABLE student');
        $this->addSql('DROP TABLE refresh_tokens');
    }
}
