<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181217183633 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_ED896F46B3E812C2');
        $this->addSql('DROP INDEX IDX_ED896F464584665A');
        $this->addSql('CREATE TEMPORARY TABLE __temp__order_detail AS SELECT id, product_id, store_order_id, quantity FROM order_detail');
        $this->addSql('DROP TABLE order_detail');
        $this->addSql('CREATE TABLE order_detail (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, product_id INTEGER DEFAULT NULL, store_order_id INTEGER DEFAULT NULL, quantity INTEGER NOT NULL, CONSTRAINT FK_ED896F464584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_ED896F46B3E812C2 FOREIGN KEY (store_order_id) REFERENCES store_order (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO order_detail (id, product_id, store_order_id, quantity) SELECT id, product_id, store_order_id, quantity FROM __temp__order_detail');
        $this->addSql('DROP TABLE __temp__order_detail');
        $this->addSql('CREATE INDEX IDX_ED896F46B3E812C2 ON order_detail (store_order_id)');
        $this->addSql('CREATE INDEX IDX_ED896F464584665A ON order_detail (product_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_ED896F464584665A');
        $this->addSql('DROP INDEX IDX_ED896F46B3E812C2');
        $this->addSql('CREATE TEMPORARY TABLE __temp__order_detail AS SELECT id, product_id, store_order_id, quantity FROM order_detail');
        $this->addSql('DROP TABLE order_detail');
        $this->addSql('CREATE TABLE order_detail (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, product_id INTEGER DEFAULT NULL, store_order_id INTEGER DEFAULT NULL, quantity INTEGER NOT NULL)');
        $this->addSql('INSERT INTO order_detail (id, product_id, store_order_id, quantity) SELECT id, product_id, store_order_id, quantity FROM __temp__order_detail');
        $this->addSql('DROP TABLE __temp__order_detail');
        $this->addSql('CREATE INDEX IDX_ED896F464584665A ON order_detail (product_id)');
        $this->addSql('CREATE INDEX IDX_ED896F46B3E812C2 ON order_detail (store_order_id)');
    }
}
