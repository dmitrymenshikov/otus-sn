<?php

declare(strict_types=1);

namespace App\Data\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240719071327 extends AbstractMigration
{
    protected array $firstNames = [];
    protected array $lastNames = [];

    public function getDescription(): string
    {
        return 'Create 1m testing data.';
    }

    public function up(Schema $schema): void
    {
        $this->initFunctions();
        $this->prepareFullName();
        $this->createTempTables();
        $this->fillTempData();
        $this->generateData();
        $this->dropTempTables();
    }

    private function initFunctions(): void
    {
        $this->addSql('
            CREATE OR REPLACE FUNCTION RANDOM_NAME(g integer)
                RETURNS varchar
                LANGUAGE plpgsql
                AS
                $$
                DECLARE
                    random_name varchar;
                BEGIN
                    SELECT
                        value
                    INTO
                        random_name
                    FROM
                        names_tmp
                    WHERE
                        gender = g
                    ORDER BY
                        random()
                    LIMIT 1;
            
                    RETURN random_name;
                END;
                $$;
        ');

        $this->addSql('
            CREATE OR REPLACE FUNCTION RANDOM_LASTNAME(g integer)
                RETURNS varchar
                LANGUAGE plpgsql
                AS
                $$
                DECLARE
                    random_name varchar;
                BEGIN
                    SELECT
                        value
                    INTO
                        random_name
                    FROM
                        lastnames_tmp
                    WHERE
                        gender = g
                    ORDER BY
                        random()
                    LIMIT 1;
            
                    RETURN random_name;
                END;
                $$;
        ');

        $this->addSql('
            CREATE OR REPLACE FUNCTION RANDOM_CITY_UUID()
                RETURNS uuid
                LANGUAGE plpgsql
                AS
                $$
                DECLARE
                    random uuid;
                BEGIN
                    SELECT
                        uuid
                    INTO
                        random
                    FROM
                        cities
                    ORDER BY
                        random()
                    LIMIT 1;
            
                    RETURN random;
                END;
                $$;
        ');

        $this->addSql('
            CREATE OR REPLACE FUNCTION random_between(low INT ,high INT)
                RETURNS INT AS
            $$
            BEGIN
                RETURN floor(random()* (high-low + 1) + low);
            END;
            $$ language plpgsql STRICT;
        ');
    }

    private function createTempTables(): void
    {
        $this->addSql('DROP TABLE IF EXISTS names_tmp, lastnames_tmp;');
        $this->addSql('
            CREATE TEMP TABLE names_tmp (
                value varchar(255),
                gender integer
            );
        ');
        $this->addSql('
            CREATE TEMP TABLE lastnames_tmp (
               value varchar(255),
                gender integer
            ); 
        ');
    }

    private function fillTempData(): void
    {
        $this->addSql('
            INSERT INTO names_tmp VALUES ' . implode(',', $this->getNames()) . ';
        ');
        $this->addSql('
            INSERT INTO lastnames_tmp VALUES ' . implode(',', $this->getLastnames()) . ';
        ');
    }

    private function generateData(): void
    {
        $this->addSql('create index user_firstname_lastname_index on public."user" (firstname text_pattern_ops, lastname text_pattern_ops);');
        $this->addSql('
            WITH gender (value) as (
                SELECT random()::int as value
            )
            INSERT INTO "user" (uuid, email, gender, firstname, lastname, password, birthday, city)
            SELECT
                gen_random_uuid() as uuid,
                (
                    random()::varchar || (
                            CASE (random() * 2)::INT
                                WHEN 0 THEN \'gmail.com\'
                                WHEN 1 THEN \'mail.ru\'
                                WHEN 2 THEN \'yandex.ru\'
                            END
                        )
                    ) AS email,
                gend.value as gender,
                RANDOM_NAME(gend.value) as firstname,
                RANDOM_LASTNAME(gend.value) as lastname,
                \'123\' as password,
                to_timestamp(random_between(0, 1167591600))::date as birthday,
                RANDOM_CITY_UUID() as city
            FROM
                generate_series(1,1000000) g
            LEFT JOIN
                gender as gend
            ON
                gend.value IS NOT NULL
            ;
        ');
    }

    private function dropTempTables(): void
    {
        $this->addSql('DROP TABLE IF EXISTS names_tmp, lastnames_tmp;');
    }

    private function getLastnames(): array
    {
        return array_map(function($item) {
            return '(\'' . $item[0] . '\'::varchar,\'' . $item[1] . '\')';
        }, $this->lastNames);
    }

    private function getNames(): array
    {
        return array_map(function($item) {
            return '(\'' . $item[0] . '\'::varchar,\'' . $item[1] . '\')';
        }, $this->firstNames);
    }

    private function prepareFullName(): void
    {
        $namesCsv = fopen('/var/www/src/Data/Data/names.csv', 'r');

        while ($row = fgetcsv($namesCsv)) {

            $gender = $row[3] === 'М' ? 1 : 0;

            $this->firstNames[] = [mb_ucfirst(mb_strtolower($row[1])), $gender];
            $this->lastNames[] = [mb_ucfirst(mb_strtolower($row[0])), $gender];
        }

        fclose($namesCsv);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
