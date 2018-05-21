CREATE OR REPLACE FUNCTION pre.f_concepto_partida_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de presupuesto
 FUNCION: 		pre.f_concepto_partida_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'pre.tconcepto_partida'
 AUTOR: 		 (admin)
 FECHA:	        25-02-2013 22:09:52
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_concepto_partida	integer;
    v_nombre_conexion		varchar;
    v_concepto				record;
    v_id_gestion			integer;
    v_id_tipo_relacion_contable				integer[];
    v_registros				record;
    v_id_concepto_endesis	integer;
    v_id_gestion_actual		integer;
    v_id_gestion_siguiente	integer;
    v_rec					record;
    v_id_partida_nueva		integer;
    v_gestion_actual		integer;
    v_consulta				varchar;

BEGIN

    v_nombre_funcion = 'pre.f_concepto_partida_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'PRE_CONP_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin
 	#FECHA:		25-02-2013 22:09:52
	***********************************/

	if(p_transaccion='PRE_CONP_INS')then

        begin
           --verificamos si la partida no se encuentra registrada
            IF exists (select 1  from pre.tconcepto_partida cp
                           where cp.id_partida = v_parametros.id_partida and cp.estado_reg ='activo'
                                 and  cp.id_concepto_ingas = v_parametros.id_concepto_ingas  ) THEN



                   raise exception 'La partida ya se encuntra asociada';

            END IF;


        	--Sentencia de la insercion
        	insert into pre.tconcepto_partida(
			id_partida,
			id_concepto_ingas,
			estado_reg,
			id_usuario_reg,
			fecha_reg,
			fecha_mod,
			id_usuario_mod
          	) values(
			v_parametros.id_partida,
			v_parametros.id_concepto_ingas,
			'activo',
			p_id_usuario,
			now(),
			null,
			null

			)RETURNING id_concepto_partida into v_id_concepto_partida;

            /*if (pxp.f_get_variable_global('sincronizar') = 'true') then

                select * into v_nombre_conexion from migra.f_crear_conexion();
                select * into v_concepto from param.tconcepto_ingas
                where id_concepto_ingas = v_parametros.id_concepto_ingas;

                select id_gestion into v_id_gestion
                from pre.tpartida where id_partida = v_parametros.id_partida;

                select * FROM dblink(v_nombre_conexion,'
                        select migracion.f_mig_concepto_ingas__tpr_concepto_ingas(NULL' ||
                        ',''INS'',''' || v_concepto.desc_ingas ||
                        ''',' || v_parametros.id_partida || ',' || v_concepto.sw_tes ||
                        ', ' || p_id_usuario || ' ,'''|| v_concepto.tipo ||
                        ''','''|| v_concepto.activo_fijo ||
                        ''','''|| v_concepto.almacenable ||
                        ''','|| p_id_usuario ||
                    ')',true) AS (id_concepto_endesis integer) into v_id_concepto_endesis;
                    insert into migra.tconcepto_ids (id_concepto_ingas, id_concepto_ingas_pxp,
                								id_gestion, desc_ingas)
                                                values (v_id_concepto_endesis, v_parametros.id_concepto_ingas,
                                                v_id_gestion, v_concepto.desc_ingas);
            	select * into v_resp from migra.f_cerrar_conexion(v_nombre_conexion,'exito');

             end if;*/

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Concepto-Partida almacenado(a) con exito (id_concepto_partida'||v_id_concepto_partida||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_concepto_partida',v_id_concepto_partida::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'PRE_CONP_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin
 	#FECHA:		25-02-2013 22:09:52
	***********************************/

	elsif(p_transaccion='PRE_CONP_MOD')then

		begin
			--Sentencia de la modificacion
			update pre.tconcepto_partida set
			id_partida = v_parametros.id_partida,
			id_concepto_ingas = v_parametros.id_concepto_ingas,
			fecha_mod = now(),
			id_usuario_mod = p_id_usuario
			where id_concepto_partida=v_parametros.id_concepto_partida;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Concepto-Partida modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_concepto_partida',v_parametros.id_concepto_partida::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'PRE_CONP_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin
 	#FECHA:		25-02-2013 22:09:52
	***********************************/

	elsif(p_transaccion='PRE_CONP_ELI')then

		begin


            if (pxp.f_get_variable_global('sincronizar') = 'true') then
            	select cig.*,cp.id_partida,cp.id_concepto_partida into v_concepto
                from param.tconcepto_ingas cig
                inner join pre.tconcepto_partida cp on cp.id_concepto_ingas = cig.id_concepto_ingas
                where cp.id_concepto_partida = v_parametros.id_concepto_partida;



            --validar que no exista aprametrizaciones para esta partida
            	v_id_tipo_relacion_contable = NULL;
                select pxp.aggarray(trc.id_tipo_relacion_contable) into v_id_tipo_relacion_contable
                from conta.ttabla_relacion_contable tr
                inner join conta.ttipo_relacion_contable trc
                	on trc.id_tabla_relacion_contable = tr.id_tabla_relacion_contable
                where lower(tr.esquema) = 'param' and tr.tabla = 'tconcepto_ingas';

                if (v_id_tipo_relacion_contable is not null) then
                	if (exists (SELECT 1 from conta.trelacion_contable
                    			where id_tipo_relacion_contable =ANY(v_id_tipo_relacion_contable)
                                and id_partida = v_concepto.id_partida and id_tabla = v_concepto.id_concepto_ingas ))then
                		raise exception 'Elimine las relaciones contables asociadas a esta partida antes de eliminarla';
                    end if;
                end if;
                select * into v_nombre_conexion from migra.f_crear_conexion();

                select id_gestion into v_id_gestion
                from pre.tpartida where id_partida = v_concepto.id_partida;

                for v_registros in (select id_concepto_ingas
                                    from migra.tconcepto_ids ci
                                    where id_concepto_ingas_pxp = v_concepto.id_concepto_ingas AND
                                    id_gestion = v_id_gestion) loop
                    select * FROM dblink(v_nombre_conexion,'
                        select migracion.f_mig_concepto_ingas__tpr_concepto_ingas(' || v_registros.id_concepto_ingas ||
                        ',''DEL'',''' || v_concepto.desc_ingas ||
                        ''',' || v_concepto.id_partida || ',''' || v_concepto.sw_tes ||
                        ''', ' || p_id_usuario || ' ,'''|| v_concepto.tipo ||
                        ''','''|| v_concepto.activo_fijo ||
                        ''','''|| v_concepto.almacenable ||
                        ''','|| p_id_usuario ||
                    ')',true) AS (id_concepto_endesis integer) into v_id_concepto_endesis;

                    if (v_id_concepto_endesis = v_registros.id_concepto_ingas)then
                    	delete from migra.tconcepto_ids where id_concepto_ingas = v_id_concepto_endesis;
                    end if;

                end loop;
            	select * into v_resp from migra.f_cerrar_conexion(v_nombre_conexion,'exito');



            end if;
            --Sentencia de la eliminacion
			delete from pre.tconcepto_partida
            where id_concepto_partida=v_parametros.id_concepto_partida;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Concepto-Partida eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_concepto_partida',v_parametros.id_concepto_partida::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'PRE_REPPARCON_REP'
 	#DESCRIPCION:	Replicacion de concepto_partida a la siguiente gestion
 	#AUTOR:		admin
 	#FECHA:		25-02-2013 22:09:52
	***********************************/

	elsif(p_transaccion='PRE_REPPARCON_REP')then

		begin
			select g.id_gestion,g.gestion::integer into v_id_gestion_actual, v_gestion_actual
            from param.tgestion g
            inner join pre.tpartida par
            	on par.id_gestion = g.id_gestion
            inner join pre.tconcepto_partida cp
            	on cp.id_partida = par.id_partida
            where cp.id_concepto_partida =  v_parametros.id_concepto_partida;


            select g.id_gestion into v_id_gestion_siguiente
            from param.tgestion g
            where  g.gestion::integer = v_gestion_actual + 1;


             if v_id_gestion_siguiente is null then
               raise exception 'La gestión destino no existe (%)', (to_char(now(),'YYYY'))::INTEGER + 1;
             end if;

            if (pxp.f_get_variable_global('sincronizar') = 'true') then

                select * into v_nombre_conexion from migra.f_crear_conexion();
            end if;

            for v_rec in (
            	select
                	cp.id_concepto_ingas, cp.id_partida
                from pre.tconcepto_partida cp
                inner join pre.tpartida p on p.id_partida = cp.id_partida
                where p.id_gestion = v_id_gestion_actual) loop

            	v_id_partida_nueva = pre.f_get_partida_ids(v_rec.id_partida);
            	--Validar que no exista la relacion
                if (not exists (select 1
                		from pre.tconcepto_partida
                        where id_concepto_ingas = v_rec.id_concepto_ingas and id_partida = v_id_partida_nueva) )then
                    --Sentencia de la insercion
                    insert into pre.tconcepto_partida(
                    id_partida,
                    id_concepto_ingas,
                    estado_reg,
                    id_usuario_reg,
                    fecha_reg,
                    fecha_mod,
                    id_usuario_mod
                    ) values(
                    v_id_partida_nueva,
                    v_rec.id_concepto_ingas,
                    'activo',
                    p_id_usuario,
                    now(),
                    null,
                    null

                    )RETURNING id_concepto_partida into v_id_concepto_partida;

               /*     if (pxp.f_get_variable_global('sincronizar') = 'true') then

                        select * into v_concepto from param.tconcepto_ingas
                        where id_concepto_ingas = v_rec.id_concepto_ingas;



                        select * FROM dblink(v_nombre_conexion,'
                                select migracion.f_mig_concepto_ingas__tpr_concepto_ingas(NULL' ||
                                ',''INS'',''' || v_concepto.desc_ingas ||
                                ''',' || v_id_partida_nueva || ',' || v_concepto.sw_tes ||
                                ', ' || p_id_usuario || ' ,'''|| v_concepto.tipo ||
                                ''','''|| v_concepto.activo_fijo ||
                                ''','''|| v_concepto.almacenable ||
                                ''','|| p_id_usuario ||
                            ')',true) AS (id_concepto_endesis integer) into v_id_concepto_endesis;

                            v_consulta = 'UPDATE
                                  presto.tpr_concepto_ingas
                                SET
                                  id_grupo_ots = string_to_array('''||array_to_string(v_concepto.id_grupo_ots,',') || ''','','')::integer[],
                                  sw_autorizacion = string_to_array('''||array_to_string(v_concepto.sw_autorizacion,',') || ''','','')::varchar[]
                                WHERE
                                  id_concepto_ingas ='|| v_id_concepto_endesis;

                 			perform  dblink(v_nombre_conexion, v_consulta, true);

                            insert into migra.tconcepto_ids (id_concepto_ingas, id_concepto_ingas_pxp,
                                                        id_gestion, desc_ingas)
                                                        values (v_id_concepto_endesis, v_rec.id_concepto_ingas,
                                                        v_id_gestion_siguiente, v_concepto.desc_ingas);

                     end if;*/

                end if;
            end loop;
            if (pxp.f_get_variable_global('sincronizar') = 'true') then
            	select * into v_resp from migra.f_cerrar_conexion(v_nombre_conexion,'exito');
            end if;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Concepto-Partida Replicado');
            v_resp = pxp.f_agrega_clave(v_resp,'id_gestion',v_id_gestion_siguiente::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	else

    	raise exception 'Transaccion inexistente: %',p_transaccion;

	end if;

EXCEPTION

	WHEN OTHERS THEN
		v_resp='';
		v_resp = pxp.f_agrega_clave(v_resp,'mensaje',SQLERRM);
		v_resp = pxp.f_agrega_clave(v_resp,'codigo_error',SQLSTATE);
		v_resp = pxp.f_agrega_clave(v_resp,'procedimientos',v_nombre_funcion);
		raise exception '%',v_resp;

END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;