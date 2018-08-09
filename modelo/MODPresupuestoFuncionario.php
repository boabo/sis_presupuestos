<?php
/**
*@package pXP
*@file gen-MODPresupuestoUsuario.php
*@author  (admin)
*@date 29-02-2016 03:25:38
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODPresupuestoFuncionario extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarPresupuestoFuncionario(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='pre.ft_presupuesto_funcionario_sel';
		$this->transaccion='PRE_PREFUN_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_presupuesto_funcionario','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('accion','varchar');
		$this->captura('id_funcionario','int4');
		$this->captura('id_presupuesto','int4');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_ai','int4');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('desc_funcionario','varchar');
		
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		//var_dump($this->consulta);exit;
		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarCentroCostoFuncionarios(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='pre.f_get_funcionarios_x_cc';
		$this->transaccion='PRE_FUNCCC_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		$this->setParametro('id_cc','id_cc','integer');
		$this->setParametro('gestion','gestion','integer');
		//Definicion de la lista del resultado del query
		$this->captura('id_funcionario','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarPresupuestoFuncionario(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='pre.ft_presupuesto_funcionario_ime';
		$this->transaccion='PRE_PREFUN_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('accion','accion','varchar');
		$this->setParametro('id_funcionario','id_funcionario','int4');
		$this->setParametro('id_presupuesto','id_presupuesto','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarPresupuestoFuncionario(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='pre.ft_presupuesto_funcionario_ime';
		$this->transaccion='PRE_PREFUN_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_presupuesto_funcionario','id_presupuesto_funcionario','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('accion','accion','varchar');
		$this->setParametro('id_funcionario','id_funcionario','int4');
		$this->setParametro('id_presupuesto','id_presupuesto','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarPresupuestoFuncionario(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='pre.ft_presupuesto_funcionario_ime';
		$this->transaccion='PRE_PREFUN_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_presupuesto_funcionario','id_presupuesto_funcionario','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
//BVP	
	function listarFuncionarioPresupuesto(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='pre.ft_presupuesto_funcionario_sel';
		$this->transaccion='PRE_PREFUN_MUL_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_presupuesto','int4');
		$this->captura('descripcion','varchar');
		$this->captura('estado_reg','varchar');
		$this->captura('accion','varchar');			
		$this->captura('fecha_reg','timestamp');
		$this->captura('fecha_mod','timestamp');		
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('id_funcionario','int4');
		$this->captura('id_gestion','int4');
		$this->captura('id_tipo_cc','int4');
		$this->captura('nro_tramite','varchar');
		$this->captura('gestion','int4');
		$this->captura('codigo_cc','text');
		$this->captura('id_presupuesto_funcionario','int4');
		$this->captura('desc_tcc','varchar');
				
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
    function listarPresupuestoFun()
    {
        $this->procedimiento ='pre.ft_presupuesto_funcionario_sel';
        $this->transaccion = 'PRE_FUN_MUL_SEL';
        $this->tipo_procedimiento='SEL';
        //$this->setCount(false);
        //$this->setParametro('id_gestion','id_gestion','int4');
		//$this->setParametro('id_funcionario','id_funcionario','int4');
		
        $this->captura('id_presupuesto','int4');
		$this->captura('descripcion','varchar');
		$this->captura('codigo_cc','text');       
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
		//var_dump($this->consulta); exit;
        //Devuelve la respuesta
        return $this->respuesta;
    }
	function insertarPresupuestoFun(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='pre.ft_presupuesto_funcionario_ime';
		$this->transaccion='PRE_PREFUNMUL_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion		
		$this->setParametro('accion','accion','varchar');
		$this->setParametro('id_funcionario','id_funcionario','int4');
		$this->setParametro('id_presupuesto','id_presupuesto','varchar');
		$this->setParametro('id_gestion','id_gestion','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
	function modificarPresupuestoFun(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='pre.ft_presupuesto_funcionario_ime';
		$this->transaccion='PRE_PREFUN_MUL_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_presupuesto_funcionario','id_presupuesto_funcionario','int4');		
		$this->setParametro('accion','accion','varchar');
		$this->setParametro('id_funcionario','id_funcionario','int4');
		$this->setParametro('id_presupuesto','id_presupuesto','varchar');
		$this->setParametro('id_gestion','id_gestion','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}	
			
}
?>