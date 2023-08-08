<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddTrigger extends Migration
{
    public function up()
    {
        // Desactivamos el chequeo de claves foráneas para poder crear los triggers
        Schema::disableForeignKeyConstraints();

        // Trigger update_ApprovalInscriptionStatus
        DB::unprepared('
            CREATE TRIGGER update_ApprovalInscriptionStatus 
            AFTER UPDATE ON calification
            FOR EACH ROW
            BEGIN 
                DECLARE total_average DECIMAL(10,2);
                DECLARE average_approval DECIMAL(10,2);

            SELECT ROUND(SUM((calification.score*evaluation.ponder)/100), 2) into total_average
            FROM inscription
            JOIN inscription_detail AS i ON inscription.id = i.inscription_id
            JOIN calification ON i.id = calification.inscription_detail_id
            JOIN evaluation ON calification.evaluation_id = evaluation.id
            JOIN cycle ON inscription.cycle_id = cycle.id
            WHERE calification.inscription_detail_id = NEW.inscription_detail_id
            AND calification.deleted_at IS NULL
            AND i.status NOT LIKE "Retirado"
            AND cycle.status = "Activo";  

            SELECT subject.average_approval into average_approval
            FROM inscription
            JOIN inscription_detail AS i ON inscription.id = i.inscription_id
            JOIN `group` ON i.group_id = `group`.id
            JOIN subject ON `group`.subject_id = subject.id
            JOIN cycle ON inscription.cycle_id = cycle.id
            WHERE i.id = NEW.inscription_detail_id
            AND i.deleted_at IS NULL;

            IF (total_average >= average_approval) THEN
                    UPDATE inscription_detail 
                    SET status = "Aprobado"
                    WHERE id = NEW.inscription_detail_id;
                ELSE
                    UPDATE inscription_detail 
                    SET status = "Reprobado"
                    WHERE id = NEW.inscription_detail_id;
                END IF;
            END
        ');

        // Trigger insert_ApprovalInscriptionStatus
        DB::unprepared('
            CREATE TRIGGER insert_ApprovalInscriptionStatus 
            AFTER INSERT ON calification
            FOR EACH ROW
            BEGIN 
                DECLARE total_average DECIMAL(10,2);
                DECLARE average_approval DECIMAL(10,2);

            SELECT ROUND(SUM((calification.score*evaluation.ponder)/100), 2) into total_average
            FROM inscription
            JOIN inscription_detail AS i ON inscription.id = i.inscription_id
            JOIN calification ON i.id = calification.inscription_detail_id
            JOIN evaluation ON calification.evaluation_id = evaluation.id
            JOIN cycle ON inscription.cycle_id = cycle.id
            WHERE calification.inscription_detail_id = NEW.inscription_detail_id
            AND calification.deleted_at IS NULL
            AND i.status NOT LIKE "Retirado"
            AND cycle.status = "Activo";  

            SELECT subject.average_approval into average_approval
            FROM inscription
            JOIN inscription_detail AS i ON inscription.id = i.inscription_id
            JOIN `group` ON i.group_id = `group`.id
            JOIN subject ON `group`.subject_id = subject.id
            JOIN cycle ON inscription.cycle_id = cycle.id
            WHERE i.id = NEW.inscription_detail_id
            AND i.deleted_at IS NULL;

            IF (total_average >= average_approval) THEN
                    UPDATE inscription_detail 
                    SET status = "Aprobado"
                    WHERE id = NEW.inscription_detail_id;
                ELSE
                    UPDATE inscription_detail 
                    SET status = "Reprobado"
                    WHERE id = NEW.inscription_detail_id;
                END IF;
            END 
        ');

        // Activamos el chequeo de claves foráneas después de crear los triggers
        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        DB::unprepared('DROP TRIGGER `update_ApprovalInscriptionStatus`');
        DB::unprepared('DROP TRIGGER `insert_ApprovalInscriptionStatus`');
    }
};
