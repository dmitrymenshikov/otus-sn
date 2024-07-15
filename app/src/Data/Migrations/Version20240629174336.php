<?php

declare(strict_types=1);

namespace App\Data\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240629174336 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create cities and user table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            create table "cities"
            (
                uuid uuid         not null constraint city_pk primary key,
                name varchar(255) not null
            );
        ');

        $this->addSql('
            INSERT INTO "cities" (uuid, name) VALUES '. implode(',', $this->getCitiesList()) .';
        ');


        $this->addSql('
            create table "user"
            (
                uuid      uuid         not null constraint user_pk primary key,
                email     VARCHAR(255) DEFAULT NULL unique,
                password  VARCHAR(255) DEFAULT NULL,
                firstname varchar(255) not null,
                lastname  varchar(255) not null,
                birthday  date         not null,
                gender    integer      not null,
                city      uuid         not null,
                about     text,
                token     VARCHAR(255) DEFAULT NULL
            );
        ');
    }

    /**
     * @return string[]
     */
    private function getCitiesList(): array
    {
        $citiesCsv = fopen('/var/www/src/Data/Data/cities.csv', 'r');

        $cities = [];

        while ($row = fgetcsv($citiesCsv)) {
            $cities[] = $row;
        }

        fclose($citiesCsv);

        return array_map(function($city) {
            return '(gen_random_uuid(), \'' . $city[0] . '\')';
        }, $cities);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE "cities"');
        $this->addSql('DROP TABLE "user"');
    }
}
