<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200429114521 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE student_presence DROP CONSTRAINT fk_dafc8b3e40c6888c');
        $this->addSql('DROP SEQUENCE roll_call_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE lesson_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE lesson (id INT NOT NULL, class_group_id INT DEFAULT NULL, teacher_id INT DEFAULT NULL, date_start TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, date_end TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F87474F34A9A1217 ON lesson (class_group_id)');
        $this->addSql('CREATE INDEX IDX_F87474F341807E1D ON lesson (teacher_id)');
        $this->addSql('CREATE TABLE class_group_admin (class_group_id INT NOT NULL, admin_id INT NOT NULL, PRIMARY KEY(class_group_id, admin_id))');
        $this->addSql('CREATE INDEX IDX_E04CE9274A9A1217 ON class_group_admin (class_group_id)');
        $this->addSql('CREATE INDEX IDX_E04CE927642B8210 ON class_group_admin (admin_id)');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F34A9A1217 FOREIGN KEY (class_group_id) REFERENCES class_group (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F341807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE class_group_admin ADD CONSTRAINT FK_E04CE9274A9A1217 FOREIGN KEY (class_group_id) REFERENCES class_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE class_group_admin ADD CONSTRAINT FK_E04CE927642B8210 FOREIGN KEY (admin_id) REFERENCES admin (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE roll_call');
        $this->addSql('DROP INDEX idx_dafc8b3e40c6888c');
        $this->addSql('ALTER TABLE student_presence ALTER late TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE student_presence ALTER late DROP DEFAULT');
        $this->addSql('ALTER TABLE student_presence RENAME COLUMN roll_call_id TO lesson_id');
        $this->addSql('ALTER TABLE student_presence RENAME COLUMN excuse TO excuse_description');
        $this->addSql('ALTER TABLE student_presence ADD CONSTRAINT FK_DAFC8B3ECDF80196 FOREIGN KEY (lesson_id) REFERENCES lesson (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_DAFC8B3ECDF80196 ON student_presence (lesson_id)');
        $this->addSql('ALTER TABLE student RENAME COLUMN photo TO photo_description');
        $this->addSql('ALTER TABLE class_group ADD university VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE class_group ADD ufr VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE class_group ADD formation VARCHAR(50) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE student_presence DROP CONSTRAINT FK_DAFC8B3ECDF80196');
        $this->addSql('DROP SEQUENCE lesson_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE roll_call_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE roll_call (id INT NOT NULL, class_group_id INT DEFAULT NULL, teacher_id INT DEFAULT NULL, date_start TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, date_end TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_553e0c624a9a1217 ON roll_call (class_group_id)');
        $this->addSql('CREATE INDEX idx_553e0c6241807e1d ON roll_call (teacher_id)');
        $this->addSql('ALTER TABLE roll_call ADD CONSTRAINT fk_553e0c624a9a1217 FOREIGN KEY (class_group_id) REFERENCES class_group (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE roll_call ADD CONSTRAINT fk_553e0c6241807e1d FOREIGN KEY (teacher_id) REFERENCES teacher (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE lesson');
        $this->addSql('DROP TABLE class_group_admin');
        $this->addSql('ALTER TABLE student RENAME COLUMN photo_description TO photo');
        $this->addSql('DROP INDEX IDX_DAFC8B3ECDF80196');
        $this->addSql('ALTER TABLE student_presence ALTER late TYPE INT');
        $this->addSql('ALTER TABLE student_presence ALTER late DROP DEFAULT');
        $this->addSql('ALTER TABLE student_presence RENAME COLUMN lesson_id TO roll_call_id');
        $this->addSql('ALTER TABLE student_presence RENAME COLUMN excuse_description TO excuse');
        $this->addSql('ALTER TABLE student_presence ADD CONSTRAINT fk_dafc8b3e40c6888c FOREIGN KEY (roll_call_id) REFERENCES roll_call (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_dafc8b3e40c6888c ON student_presence (roll_call_id)');
        $this->addSql('ALTER TABLE class_group DROP university');
        $this->addSql('ALTER TABLE class_group DROP ufr');
        $this->addSql('ALTER TABLE class_group DROP formation');
    }
}
