<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OnlyOfficeTablesSeeder extends Seeder
{
    /**
     * Criar tabelas necessárias para o OnlyOffice DocumentServer
     *
     * @return void
     */
    public function run()
    {
        // Verificar se as tabelas já existem antes de criar
        if (!Schema::hasTable('doc_changes')) {
            DB::statement('CREATE TABLE IF NOT EXISTS "public"."doc_changes" (
                "tenant" varchar(255) NOT NULL,
                "id" varchar(255) NOT NULL,
                "change_id" int4 NOT NULL,
                "user_id" varchar(255) NOT NULL,
                "user_id_original" varchar(255) NOT NULL,
                "user_name" varchar(255) NOT NULL,
                "change_data" text NOT NULL,
                "change_date" timestamp without time zone NOT NULL,
                PRIMARY KEY ("tenant", "id", "change_id")
            )');
            
            $this->command->info('✅ Tabela doc_changes criada para OnlyOffice');
        } else {
            $this->command->info('ℹ️  Tabela doc_changes já existe');
        }

        if (!Schema::hasTable('task_result')) {
            DB::statement('CREATE TABLE IF NOT EXISTS "public"."task_result" (
                "tenant" varchar(255) NOT NULL,
                "id" varchar(255) NOT NULL,
                "status" int2 NOT NULL,
                "status_info" int4 NOT NULL,
                "created_at" timestamp without time zone DEFAULT NOW(),
                "last_open_date" timestamp without time zone NOT NULL,
                "user_index" int4 NOT NULL DEFAULT 1,
                "change_id" int4 NOT NULL DEFAULT 0,
                "callback" text NOT NULL,
                "baseurl" text NOT NULL,
                "password" text NULL,
                "additional" text NULL,
                PRIMARY KEY ("tenant", "id")
            )');
            
            $this->command->info('✅ Tabela task_result criada para OnlyOffice');
        } else {
            $this->command->info('ℹ️  Tabela task_result já existe');
        }

        // Criar função merge_db que o OnlyOffice precisa
        try {
            DB::statement('CREATE OR REPLACE FUNCTION merge_db(
                _tenant varchar(255), 
                _id varchar(255), 
                _status int2, 
                _status_info int4, 
                _last_open_date timestamp without time zone, 
                _user_index int4, 
                _change_id int4, 
                _callback text, 
                _baseurl text, 
                OUT isupdate char(5), 
                OUT userindex int4
            ) AS $$
            DECLARE
                t_var "public"."task_result"."user_index"%TYPE;
            BEGIN
                LOOP
                    -- first try to update the key
                    IF ((_callback <> \'\') IS TRUE) AND ((_baseurl <> \'\') IS TRUE) THEN
                        UPDATE "public"."task_result" 
                        SET last_open_date=_last_open_date, user_index=user_index+1, callback=_callback, baseurl=_baseurl 
                        WHERE tenant = _tenant AND id = _id 
                        RETURNING user_index into userindex;
                    ELSE
                        UPDATE "public"."task_result" 
                        SET last_open_date=_last_open_date, user_index=user_index+1 
                        WHERE tenant = _tenant AND id = _id 
                        RETURNING user_index into userindex;
                    END IF;
                    
                    IF found THEN
                        isupdate := \'true\';
                        RETURN;
                    END IF;
                    
                    -- not there, so try to insert the key
                    BEGIN
                        INSERT INTO "public"."task_result"(tenant, id, status, status_info, last_open_date, user_index, change_id, callback, baseurl) 
                        VALUES(_tenant, _id, _status, _status_info, _last_open_date, _user_index, _change_id, _callback, _baseurl) 
                        RETURNING user_index into userindex;
                        isupdate := \'false\';
                        RETURN;
                    EXCEPTION WHEN unique_violation THEN
                        -- do nothing, and loop to try the UPDATE again
                    END;
                END LOOP;
            END;
            $$ LANGUAGE plpgsql');

            $this->command->info('✅ Função merge_db criada para OnlyOffice');
        } catch (\Exception $e) {
            $this->command->warn('⚠️  Erro ao criar função merge_db: ' . $e->getMessage());
        }

        $this->command->info('🎯 OnlyOffice DocumentServer: Tabelas e funções inicializadas com sucesso!');
    }
}