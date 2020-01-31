CREATE OR REPLACE FUNCTION pre.f_verificar_presupuesto_individual_centro_costo (
  p_nro_tramite varchar,
  p_id_partida_ejecucion integer,
  p_id_presupuesto integer,
  p_id_partida integer,
  p_monto_total_mb numeric,
  p_monto_total numeric,
  p_sw_momento varchar
)
RETURNS varchar [] AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Presupuestos
 FUNCION: 		pre.f_verificar_presupuesto_individual_centro_costo
 DESCRIPCION:   funcion que verifica si el monto puede procesarce  
 AUTOR: 		breydi vasquez
 FECHA:	        07/01/2020
 COMENTARIOS:	
***************************************************************************/


DECLARE
  verificado numeric[];
  v_consulta varchar;
  v_conexion varchar;
  v_resp	varchar;
  v_sincronizar varchar;
 
  
  v_nombre_funcion  		varchar;
  
  v_total_formulado 		numeric;
  v_total_comprometido		numeric;
  v_total_ejecutado			numeric;
  v_total_pagado			numeric;
  v_total_revertido			numeric;
  v_saldo					numeric;
  
  v_total_formulado_mb 		numeric;
  v_total_comprometido_mb	numeric;
  v_total_ejecutado_mb		numeric;
  v_total_revertido_mb		numeric;
  v_saldo_mb				numeric;
  v_total_pagado_mb			numeric;
  
  
  v_respuesta						varchar[];
  v_pre_verificar_categoria			varchar;
  v_id_categoria_programatica		integer;
  v_pre_verificar_tipo_cc			varchar;
  v_pre_verificar_tipo_cc_control   varchar;
  v_id_tipo_cc_techo 				integer;
  v_control_partida					varchar;
  
BEGIN



            v_nombre_funcion = 'pre.f_verificar_presupuesto_individual_centro_costo';            
            v_control_partida = 'si'; --por defeto controlamos los monstos por partidas

     
           -- si tenemos partida ejecucion  obtener partida y presupuesto
           
        
            IF p_monto_total_mb >= 0 THEN
                   -- si el monto es positivo
                   
                        -------------------------
                        --  si es comprometer
                        -------------------------
                        
                        IF p_sw_momento  = 'comprometido' THEN 
                        
                               --  verificar que el monto formulado - comprometido sea suficiente
                               
                               IF p_id_partida is null or p_id_presupuesto is null THEN
                                  raise exception 'para verificar el comprometido tiene que indicar la partida y el presupeusto';
                               END IF;
                               
                               --  sumamos el formulado para esta partida y presupuesto
                               select 
                                  sum(pe.monto_mb)
                               into 
                                 v_total_formulado_mb
                               from pre.tpartida_ejecucion pe
                               inner join pre.tpresupuesto p on p.id_presupuesto = pe.id_presupuesto
                               inner join param.tcentro_costo cc on cc.id_centro_costo = p.id_centro_costo
                               inner join param.vtipo_cc_techo tcc on tcc.id_tipo_cc = cc.id_tipo_cc
                               where ( CASE WHEN v_control_partida = 'si'  THEN 
                                         pe.id_partida = p_id_partida
                                     ELSE
                                         0=0
                                     END)
                                     and pe.estado_reg = 'activo'
                                     and pe.tipo_movimiento = 'formulado'
                                     and  pe.id_presupuesto = p_id_presupuesto;
                                     
                              --sumamos el comprometido      
                              select 
                                  sum(pe.monto_mb)
                              into 
                                 v_total_comprometido_mb
                              from pre.tpartida_ejecucion pe
                              inner join pre.tpresupuesto p on p.id_presupuesto = pe.id_presupuesto
                              inner join param.tcentro_costo cc on cc.id_centro_costo = p.id_centro_costo
                              inner join param.vtipo_cc_techo tcc on tcc.id_tipo_cc = cc.id_tipo_cc
                              where ( CASE WHEN v_control_partida = 'si'  THEN 
                                         pe.id_partida = p_id_partida
                                     ELSE
                                         0=0
                                     END)
                                     and pe.estado_reg = 'activo'
                                     and pe.tipo_movimiento = 'comprometido'
                                     and pe.id_presupuesto = p_id_presupuesto;       
                           
                              v_saldo_mb = COALESCE(v_total_formulado_mb,0) - COALESCE(v_total_comprometido_mb,0);
                              
                              IF p_monto_total_mb <= v_saldo_mb THEN
                                v_respuesta[1] = 'true';
                              ELSE  
                                v_respuesta[1] = 'false';
                              END IF;
                              
                             v_respuesta[2] = v_saldo_mb::varchar;
                   end if;
                                                                     
             ELSE                 
                       
                          
                         IF p_sw_momento  = 'comprometido' THEN 
                            -- verificar que el monto sea menor que el comprometido - el ejecutado
                           
                                      IF p_nro_tramite is null  THEN
                                           raise exception 'para revertir el comprometido necesitamos el número de tramite';
                                      END IF;
                                    
                                     --recuperar la partida y el presupuesto
                                     IF p_id_partida is null or p_id_presupuesto is null THEN
                                      
                                           IF  p_id_partida_ejecucion is NULL  THEN
                                               raise exception 'si no especifica la partida y presupuesto es necesario al menos la partida ejecución';
                                           END IF;
                                         
                                          select
                                            pe.id_partida,
                                            pe.id_presupuesto
                                          into
                                            p_id_partida,
                                            p_id_presupuesto
                                          from pre.tpartida_ejecucion pe 
                                          
                                          where pe.id_partida_ejecucion = p_id_partida_ejecucion;
                                     END IF;
                          
                                    --listamos el monto comprometido 
                                    
                                     select
                                       sum(pe.monto_mb),
                                       sum(pe.monto)
                                     into
                                       v_total_comprometido_mb,
                                       v_total_comprometido
                                     from pre.tpartida_ejecucion pe
                                     where pe.estado_reg = 'activo'
                                           and pe.id_partida = p_id_partida
                                           and pe.id_presupuesto = p_id_presupuesto
                                           and pe.nro_tramite = p_nro_tramite
                                           and pe.tipo_movimiento = 'comprometido';       
                                           
                                           
                                  --listamso el monto ejectuado
                                  
                                   select
                                       sum(pe.monto_mb),
                                       sum(pe.monto)
                                     into
                                       v_total_ejecutado_mb,
                                       v_total_ejecutado
                                     from pre.tpartida_ejecucion pe
                                     where pe.estado_reg = 'activo'
                                           and pe.id_partida = p_id_partida
                                           and pe.id_presupuesto = p_id_presupuesto
                                           and pe.nro_tramite = p_nro_tramite
                                           and pe.tipo_movimiento = 'ejecutado';
                                  
                                  v_saldo =   COALESCE(v_total_comprometido,0) -  COALESCE(v_total_ejecutado,0);
                                  v_saldo_mb =   COALESCE(v_total_comprometido_mb,0) -  COALESCE(v_total_ejecutado_mb,0);       
                                           
                                  IF (p_monto_total_mb*-1) <= v_saldo_mb THEN
                                    v_respuesta[1] = 'true';
                                    v_respuesta[2] = v_saldo_mb::varchar;
                                  ELSE  
                                    v_respuesta[1] = 'false';
                                    v_respuesta[2] = v_saldo_mb::varchar;
                                  END IF; 
                                  
                                  IF (p_monto_total*-1) <= v_saldo THEN
                                    v_respuesta[3] = 'true';
                                    v_respuesta[4] = v_saldo::varchar;
                                  ELSE  
                                    v_respuesta[3] = 'false';
                                    v_respuesta[4] = v_saldo::varchar;
                                  END IF; 
                         end if;  
             
                  END IF;            
      

     return v_respuesta;


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
