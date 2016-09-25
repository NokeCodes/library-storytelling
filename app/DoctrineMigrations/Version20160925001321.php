<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160925001321 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        
        $this->addSql('INSERT INTO craue_config_setting ( name ) VALUES ("LSUvsAUB_max_id")');
        $this->addSql('INSERT INTO craue_config_setting ( name ) VALUES ("RoanokeStory_max_id")');
    

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

        $this->addSql('DELETE FROM craue_config_setting WHERE name = "LSUvsAUB_max_id"');
        $this->addSql('DELETE FROM craue_config_setting WHERE name = "RoanokeStory_max_id"');
    }
}
