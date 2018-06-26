CREATE OR REPLACE FUNCTION pre.sp_reporte_partida_proveedor (
  presupuesto_id integer,
  partida_id integer,
  moneda_id integer,
  usuario_id integer,
  fechaini date,
  fechafin date
)
RETURNS TABLE (
  num_tramite varchar,
  proveedor varchar,
  descripcion text,
  partida varchar,
  codigo_partida varchar,
  comprometido numeric,
  ejecutado numeric,
  pagado numeric,
  moneda varchar,
  gestion integer,
  user_reporte text,
  fecha date
) AS
$body$
DECLARE
  v_resp INT;

BEGIN
    RETURN QUERY
    select DISTINCT ON (num_tramite)
    registros.num_tramite,
    registros.desc_proveedor,
	registros.descripcion,
    registros.nombre_partida,
    registros.partida_cod,
    registros.comprometido,
    registros.ejecutado,
    registros.pagado,
    registros.moneda,
    registros.gestion,
    registros.desc_persona,
    registros.fecha_soli as fecha
	from (select
    (select COALESCE(ps_comprometido,0) from pre.f_verificar_com_eje_pag(sd.id_partida_ejecucion, moneda_id)) as comprometido,
	(select COALESCE(ps_ejecutado,0) from pre.f_verificar_com_eje_pag(sd.id_partida_ejecucion, moneda_id)) as ejecutado,
	(select COALESCE(ps_pagado,0) from pre.f_verificar_com_eje_pag(sd.id_partida_ejecucion, moneda_id)) as pagado,
    s.num_tramite,
    coalesce(s.justificacion, '')  ||' - Desc: '|| coalesce(sd.descripcion, '') as descripcion,
    ges.gestion,
    par.nombre_partida,
    par.codigo as partida_cod,
    pro.desc_proveedor,
    s.fecha_soli,
    usu.desc_persona,
    m.codigo as moneda
    from
    adq.tsolicitud s
    inner join adq.tsolicitud_det sd on sd.id_solicitud = s.id_solicitud and s.estado_reg = 'activo' and  sd.estado_reg = 'activo'
    inner join param.tmoneda m on m.id_moneda = moneda_id
    inner join param.tcentro_costo tcco on tcco.id_centro_costo = sd.id_centro_costo
    inner join param.tgestion ges on ges.id_gestion = tcco.id_gestion
    inner join pre.tpartida par on par.id_partida = sd.id_partida
    inner join pre.vpresupuesto_cc vpres on vpres.id_centro_costo = sd.id_centro_costo
    inner join orga.vfuncionario soli on soli.id_funcionario = s.id_funcionario
    inner join segu.vusuario  usu on usu.id_usuario = usuario_id
    inner join param.vproveedor pro on pro.id_proveedor = s.id_proveedor
    where  s.estado not in ('borrador','vbgerencia','vbpresupuestos','anulado')
          AND ((presupuesto_id > 0 AND sd.id_centro_costo in (presupuesto_id))OR(presupuesto_id = 0))
          AND((partida_id > 0 AND sd.id_partida in (partida_id))OR(partida_id = 0))

     union

    select
    (select COALESCE(ps_comprometido,0) from pre.f_verificar_com_eje_pag(odi.id_partida_ejecucion_com, moneda_id)) as comprometido,
	(select COALESCE(ps_ejecutado,0) from pre.f_verificar_com_eje_pag(odi.id_partida_ejecucion_com, moneda_id)) as ejecutado,
	(select COALESCE(ps_pagado,0) from pre.f_verificar_com_eje_pag(odi.id_partida_ejecucion_com, moneda_id)) as pagado,
    op.num_tramite,
    coalesce(op.obs, '') ||' - Desc: '||coalesce(odi.descripcion, ''),
    ges.gestion,
    part.nombre_partida,
    part.codigo,
    pro.desc_proveedor,
    op.fecha,
    usua.desc_persona,
    m.codigo as moneda
    from
    tes.tobligacion_pago op
    inner join tes.tobligacion_det odi on odi.id_obligacion_pago = op.id_obligacion_pago and odi.estado_reg = 'activo' and  op.estado_reg = 'activo'
    inner join param.tmoneda m on m.id_moneda = moneda_id
    inner join param.tcentro_costo tco on tco.id_centro_costo = odi.id_centro_costo
    inner join param.tgestion ges on ges.id_gestion = tco.id_gestion
    inner join pre.tpartida part on part.id_partida = odi.id_partida
    inner join pre.vpresupuesto_cc vpresu on vpresu.id_centro_costo = odi.id_centro_costo
    inner join orga.vfuncionario solici on solici.id_funcionario = op.id_funcionario
    inner join segu.vusuario  usua on usua.id_usuario = usuario_id
    inner join param.vproveedor pro on pro.id_proveedor = op.id_proveedor
    where  op.estado not in ('borrador','vbgerencia','vbpresupuestos','anulado')
    AND ((presupuesto_id > 0 AND odi.id_centro_costo in (presupuesto_id))OR(presupuesto_id = 0))
    AND((partida_id > 0 AND odi.id_partida in (partida_id))OR(partida_id = 0))
    ) as registros
   order by num_tramite, fecha;
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
RETURNS NULL ON NULL INPUT
SECURITY INVOKER
COST 100 ROWS 1000;