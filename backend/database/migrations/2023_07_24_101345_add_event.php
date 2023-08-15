<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Enable event scheduler globally
        DB::unprepared('SET GLOBAL event_scheduler = ON;');

        // Create the event
        DB::unprepared('
            CREATE EVENT verificar_estado
            ON SCHEDULE
            EVERY 1 SECOND
            ON COMPLETION PRESERVE
            DO
            BEGIN
            DECLARE cycle_id INT;
                
                -- Update cycle status
                UPDATE cycle 
                SET status =  
                    CASE
                        WHEN start_date > CURDATE() THEN "Inactivo"
                        WHEN start_date <= CURDATE() AND end_date >= CURDATE() THEN "Activo"
                        WHEN end_date < CURDATE() THEN "Finalizado"
                        ELSE "Desconocido"
                    END;
            END;  

        ');

        DB::unprepared('
            CREATE EVENT inscription_status
            ON SCHEDULE
            EVERY 1 SECOND
            ON COMPLETION PRESERVE
            DO
            BEGIN
                DECLARE done INT DEFAULT FALSE;
                DECLARE cycle_id INT;
                DECLARE cur CURSOR FOR SELECT id FROM cycle WHERE status = "Finalizado" AND end_date < CURDATE();
                DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
                
                OPEN cur;
                ins_loop: LOOP
                    FETCH cur INTO cycle_id;
                    IF done THEN
                        LEAVE ins_loop;
                    END IF;
                    
                    UPDATE inscription 
                    SET status = "Finalizado"
                    WHERE inscription.cycle_id = cycle_id;
                END LOOP;
                CLOSE cur;
            END;
        ');

        DB::unprepared('
            CREATE EVENT inscription_status_active
            ON SCHEDULE
            EVERY 1 SECOND
            ON COMPLETION PRESERVE
            DO
            BEGIN
                DECLARE done INT DEFAULT FALSE;
                DECLARE cycle_id INT;
                DECLARE cur CURSOR FOR SELECT id FROM cycle WHERE status = "Activo" AND end_date >= CURDATE();
                DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
                
                OPEN cur;
                ins_loop: LOOP
                    FETCH cur INTO cycle_id;
                    IF done THEN
                        LEAVE ins_loop;
                    END IF;
                    
                    UPDATE inscription 
                    SET status = "Inscrito"
                    WHERE inscription.cycle_id = cycle_id;
                END LOOP;
                CLOSE cur;
            END;
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Disable event scheduler globally
        DB::unprepared('SET GLOBAL event_scheduler = OFF;');

        // Drop the event
        DB::unprepared('DROP EVENT IF EXISTS verificar_estado;');
        DB::unprepared('DROP EVENT IF EXISTS inscription_status;');
        DB::unprepared('DROP EVENT IF EXISTS inscription_status_active;');
    }
};
