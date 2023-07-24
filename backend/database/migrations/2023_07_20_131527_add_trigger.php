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

        // Trigger update_CycleInscriptionStatus
        // DB::unprepared('
        //     CREATE TRIGGER update_CycleInscriptionStatus
        //     AFTER UPDATE ON inscription_detail
        //     FOR EACH ROW
        //     BEGIN
        //         DECLARE total_retirados INT;
        //         DECLARE total_inscription_details INT;

        //         -- Obtener el total de inscription_detail con estado "Retirado"
        //         SELECT COUNT(*) INTO total_retirados
        //         FROM inscription_detail
        //         WHERE inscription_id = NEW.inscription_id AND status = "Retirado";

        //         -- Obtener el total de inscription_detail asociados a la misma inscription
        //         SELECT COUNT(*) INTO total_inscription_details
        //         FROM inscription_detail
        //         WHERE inscription_id = NEW.inscription_id;

        //         IF (NEW.status = "Retirado") THEN
        //             -- Si todos los inscription_detail están "Retirado", cambiar estado de inscription
        //             IF total_retirados = total_inscription_details THEN
        //                 UPDATE inscription
        //                 SET status = "Retirado"
        //                 WHERE id = NEW.inscription_id;
        //             END IF;
        //         ELSEIF (NEW.status = "Inscrito") THEN
        //             -- Si no todos los inscription_detail están "Retirado", cambiar estado de inscription
        //             IF total_retirados != total_inscription_details THEN
        //                 UPDATE inscription
        //                 SET status = "Inscrito"
        //                 WHERE id = NEW.inscription_id;
        //             END IF;
        //         END IF;
        //     END
        // ');

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

        // Trigger delete_ApprovalInscriptionStatus
        DB::unprepared('
            CREATE TRIGGER delete_ApprovalInscriptionStatus 
            AFTER DELETE ON calification
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
            WHERE calification.inscription_detail_id = OLD.inscription_detail_id
            AND calification.deleted_at IS NULL
            AND i.status NOT LIKE "Retirado"
            AND cycle.status = "Activo";  

            SELECT subject.average_approval into average_approval
            FROM inscription
            JOIN inscription_detail AS i ON inscription.id = i.inscription_id
            JOIN `group` ON i.group_id = `group`.id
            JOIN subject ON `group`.subject_id = subject.id
            JOIN cycle ON inscription.cycle_id = cycle.id
            WHERE i.id = OLD.inscription_detail_id
            AND i.deleted_at IS NULL;

            IF (total_average >= average_approval) THEN
                    UPDATE inscription_detail 
                    SET status = "Aprobado"
                    WHERE id = OLD.inscription_detail_id;
                ELSE
                    UPDATE inscription_detail 
                    SET status = "Reprobado"
                    WHERE id = OLD.inscription_detail_id;
                END IF;
            END
        ');

        // Activamos el chequeo de claves foráneas después de crear los triggers
        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        DB::unprepared('DROP TRIGGER `update_CycleInscriptionStatus`');
        DB::unprepared('DROP TRIGGER `update_ApprovalInscriptionStatus`');
        DB::unprepared('DROP TRIGGER `insert_ApprovalInscriptionStatus`');
        DB::unprepared('DROP TRIGGER `delete_ApprovalInscriptionStatus`');
    }
};