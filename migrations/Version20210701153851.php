<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210701153851 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'ALTER TABLE partner CHANGE password password_hash VARCHAR(255) NOT NULL, CHANGE email_confirm email_confirmed TINYINT(1) DEFAULT NULL;'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            'ALTER TABLE partner CHANGE password_hash password VARCHAR(255) NOT NULL, CHANGE email_confirmed email_confirm TINYINT(1) DEFAULT NULL;'
        );
    }
}
