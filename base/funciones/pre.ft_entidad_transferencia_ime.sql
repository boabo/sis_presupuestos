CREATE OR REPLACE FUNCTION pre.ft_entidad_transferencia_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/************************************************************************** SISTEMA:        Sistema de Presupuesto
 FUNCION:         pre.ft_entidad_transferencia_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'pre.tentidad_transferencia'
 AUTOR:          (franklin.espinoza)
 FECHA:            21-07-2017 12:57:45
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE

  v_nro_requerimiento integer;
  v_parametros record;
  v_id_requerimiento integer;
  v_resp varchar;
  v_nombre_funcion text;
  v_mensaje_error text;
  v_id_entidad_transferencia integer;
  v_id_gestion integer;
  v_datos record;
  v_valid boolean;
  v_record_d record;
  v_record_m RECORD;
  v_id_gestion_sig integer;
  v_gestion integer;
  --v_cont_m                    integer;
  --v_cont_d                    integer;
  v_record_aux record;

BEGIN

  v_nombre_funcion = 'pre.ft_entidad_transferencia_ime';
  v_parametros = pxp.f_get_record(p_tabla);

  /*********************************
     #TRANSACCION:  'PRE_ENT_TRAN_INS'
     #DESCRIPCION:    Insercion de registros
     #AUTOR:        franklin.espinoza
     #FECHA:        21-07-2017 12:57:45
    ***********************************/

  if(p_transaccion='PRE_ENT_TRAN_INS')then

    begin
      SELECT g.id_gestion
      INTO v_id_gestion
      FROM param.tgestion g
      WHERE g.gestion = EXTRACT(YEAR
      FROM current_date);

      --Sentencia de la insercion

      insert into pre.tentidad_transferencia(id_gestion, estado_reg, codigo,
        nombre, usuario_ai, fecha_reg, id_usuario_reg, id_usuario_ai,
        id_usuario_mod, fecha_mod)
      values (v_id_gestion, 'activo', v_parametros.codigo, v_parametros.nombre,
        v_parametros._nombre_usuario_ai, now(), p_id_usuario,
        v_parametros._id_usuario_ai, null, null) RETURNING
        id_entidad_transferencia
      into v_id_entidad_transferencia;

      --Definicion de la respuesta
      v_resp = pxp.f_agrega_clave(v_resp,'mensaje',
        'EntidadTranferencia almacenado(a) con exito (id_entidad_transferencia'
        ||v_id_entidad_transferencia||')');
      v_resp = pxp.f_agrega_clave(v_resp,'id_entidad_transferencia',
        v_id_entidad_transferencia::varchar);

      --Devuelve la respuesta
      return v_resp;

    end;

    /*********************************
     #TRANSACCION:  'PRE_ENT_TRAN_MOD'
     #DESCRIPCION:    Modificacion de registros
     #AUTOR:        franklin.espinoza
     #FECHA:        21-07-2017 12:57:45
    ***********************************/

    elsif(p_transaccion='PRE_ENT_TRAN_MOD')then

    begin
      --Sentencia de la modificacion

      update pre.tentidad_transferencia
      set id_gestion = v_parametros.id_gestion,
          codigo = v_parametros.codigo,
          nombre = v_parametros.nombre,
          id_usuario_mod = p_id_usuario,
          fecha_mod = now(),
          id_usuario_ai = v_parametros._id_usuario_ai,
          usuario_ai = v_parametros._nombre_usuario_ai
      where id_entidad_transferencia = v_parametros.id_entidad_transferencia;

      --Definicion de la respuesta
      v_resp = pxp.f_agrega_clave(v_resp,'mensaje',
        'EntidadTranferencia modificado(a)');
      v_resp = pxp.f_agrega_clave(v_resp,'id_entidad_transferencia',
        v_parametros.id_entidad_transferencia::varchar);

      --Devuelve la respuesta
      return v_resp;

    end;

    /*********************************
     #TRANSACCION:  'PRE_ENT_TRAN_ELI'
     #DESCRIPCION:    Eliminacion de registros
     #AUTOR:        franklin.espinoza
     #FECHA:        21-07-2017 12:57:45
    ***********************************/

    elsif(p_transaccion='PRE_ENT_TRAN_ELI')then

    begin
      --Sentencia de la eliminacion

      delete
      from pre.tentidad_transferencia
      where id_entidad_transferencia = v_parametros.id_entidad_transferencia;

      --Definicion de la respuesta
      v_resp = pxp.f_agrega_clave(v_resp,'mensaje',
        'EntidadTranferencia eliminado(a)');
      v_resp = pxp.f_agrega_clave(v_resp,'id_entidad_transferencia',
        v_parametros.id_entidad_transferencia::varchar);

      --Devuelve la respuesta
      return v_resp;

    end;

    /*********************************
     #TRANSACCION:  'PRE_ENT_TRAN_ELI'
     #DESCRIPCION:    Validacion de codigo, nombre
     #AUTOR:        franklin.espinoza
     #FECHA:        21-07-2017 12:57:45
    ***********************************/

    elsif(p_transaccion='PRE_ENT_TRAN_VAL')then

    begin
      --Sentencia de la eliminacion

      select count(tet.id_entidad_transferencia) AS contador,
             CASE
               WHEN lower(tet.codigo) = lower(v_parametros.codigo) AND lower(
                 tet.nombre) <> lower(v_parametros.nombre) THEN (
                 'Codigo es Duplicado, ' || v_parametros.codigo)::varchar
               WHEN lower(tet.nombre) = lower(v_parametros.nombre) AND lower(
                 tet.codigo) <> lower(v_parametros.codigo) THEN (
                 'Nombre es Duplicado, ' || v_parametros.nombre)::varchar
               WHEN lower(tet.codigo) = lower(v_parametros.codigo) AND lower(
                 tet.nombre) = lower(v_parametros.nombre) THEN (
                 'Codigo y Nombre son Duplicados, ' || v_parametros.codigo ||
                 ' , ' || v_parametros.nombre)::varchar
             END AS mensaje
      INTO v_datos
      from pre.tentidad_transferencia tet
      where lower(tet.codigo) = lower(v_parametros.codigo) OR
            lower(tet.nombre) = lower(v_parametros.nombre)
      GROUP BY tet.codigo,
               tet.nombre;

        IF(v_datos.contador>=1)THEN
          v_valid = true;
          ELSE
          v_valid = false;
        END IF;

        --Definicion de la respuesta
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Validacion Exitosa.');
        v_resp = pxp.f_agrega_clave(v_resp,'v_valid',v_valid::varchar);
        v_resp = pxp.f_agrega_clave(v_resp,'v_mensaje',v_datos.mensaje::varchar)
          ;

        --Devuelve la respuesta
        return v_resp;

      end;
    /*********************************
     #TRANSACCION:  'PRE_CLOENTRANS_REP'
     #DESCRIPCION:    Clonar registros registros
     #AUTOR:        maylee.perez
     #FECHA:        25-08-2017 19:34:27
    ***********************************/

    elsif(p_transaccion='PRE_CLOENTRANS_REP')then

begin
  /*
            *    v_parametros.id_gestion_maestro: gestion de la que se quiere copiar (origen).
            *    v_parametros.id_gestion: gestion a la que se quiere copiar (destino)
            */
  select gestion + 1
  into v_gestion
  FROM param.tgestion
  WHERE id_gestion = v_parametros.id_gestion;

 select id_gestion
  into v_id_gestion
  FROM param.tgestion
  WHERE gestion = v_gestion;
  --raise exception 'gestiones %, %',v_gestion,v_id_gestion;

 FOR v_record_m IN (SELECT tet.codigo,tet.nombre, tet.id_entidad_transferencia
					  FROM pre.tentidad_transferencia tet
					  where tet.id_gestion = v_parametros.id_gestion)loop



  /*Preguntamos si existe un dato con el codigo y gestion duplkicados en la tabla entidad transferencia
  donde se si existe el valor nos devolvera true pero si no hay no devolvera nada, para eso uso el exist
   ya que se encarga de verificar si existe un dato porlo menos*/
   IF EXISTS ( select true from  pre.tentidad_transferencia where id_gestion=v_id_gestion and codigo = v_record_m.codigo ) then

    RAISE EXCEPTION 'ESTIMADO USUARIO: LAS ENTIDADES DE TRANSFERENCIA YA FUERON REGISTRADOS PARA LA GESTION % ANTERIORMENTE',v_gestion ;
    ELSE


     INSERT INTO pre.tentidad_transferencia(id_gestion, codigo, nombre,
          estado_reg, id_usuario_ai, usuario_ai, fecha_reg, id_usuario_reg,
          fecha_mod, id_usuario_mod)
        values (v_id_gestion, v_record_m.codigo, v_record_m.nombre, 'activo',
          v_parametros._id_usuario_ai, v_parametros._nombre_usuario_ai, now(),
          p_id_usuario, null, null)RETURNING id_entidad_transferencia into v_id_entidad_transferencia;

     insert into pre.tentidad_transferencia_ids(id_entidad_uno, id_entidad_dos)
     VALUES(v_record_m.id_entidad_transferencia, v_id_entidad_transferencia );

   end if;


  END LOOP;

  --Definicion de la respuesta
  v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se ha clonado exitosamente');

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