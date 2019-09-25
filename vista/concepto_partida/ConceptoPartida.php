<?php
/**
*@package pXP
*@file gen-ConceptoPartida.php
*@author  (admin)
*@date 25-02-2013 22:09:52
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.ConceptoPartida=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
		
    	//llama al constructor de la clase padre
		Phx.vista.ConceptoPartida.superclass.constructor.call(this,config);
		this.init();
		var dataPadre = Phx.CP.getPagina(this.idContenedorPadre).getSelectedData()
        if(dataPadre){
            this.onEnablePanel(this, dataPadre);
        }
        else
        {
           this.bloquearMenus();
        }
        this.addButton('clonarConf',{ text: 'Clonar configuración', iconCls: 'blist',disabled: false, handler: this.clonarConf, tooltip: 'Clonar la configuración para la siguiente gestión'});
		
	},
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_concepto_partida'
			},
			type:'Field',
			form:true 
		},
		{
			config:{
				name: 'id_concepto_ingas',
				fieldLabel: 'id_concepto_ingas',
				inputType:'hidden'
			},
			type:'Field',
			form:true
		},
		{
			config: {
				name: 'id_partida',
				fieldLabel: 'Partida',
				typeAhead: false,
				forceSelection: true,
				allowBlank: false,
				emptyText: 'Partida...',
				store: new Ext.data.JsonStore({
					url: '../../sis_presupuestos/control/Partida/listarPartida',
					id: 'id_partida',
					root: 'datos',
					sortInfo: {
						field: 'codigo',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_partida', 'codigo', 'nombre_partida','descripcion','id_gestion','desc_gestion','tipo','sw_transaccional'],
					// turn on remote sorting
					remoteSort: true,
					baseParams: {par_filtro: 'par.nombre_partida#par.codigo',sw_transaccional:'movimiento'}
				}),
				valueField: 'id_partida',
				displayField: 'nombre_partida',
				gdisplayField: 'desc_partida',
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				pageSize: 20,
				queryDelay: 200,
				listWidth:420,
				minChars: 2,
                anchor: '80%',
				gwidth: 170,
                resizable: true,
				renderer: function(value, p, record) {
					return String.format('{0}', record.data['desc_partida']);
				},
				tpl: '<tpl for="."><div class="x-combo-list-item"><p><b>Codigo: <span style="color:green;">{codigo}</span></b></p><strong><b>Partida:</b> <span style="color:red;">{nombre_partida}</span></strong> <p><b>{tipo} - <span style="color:blue;">{desc_gestion}</span></b></p></div></tpl>'
			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {
				pfiltro: 'par.nombre_partida#par.codigo',
				type: 'string'
			},
			grid: false,
			form: true
		},
		{
			config:{
				name: 'codigo_partida',
				fieldLabel: 'Codigo',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
			type:'Field',
			filters:{pfiltro:'par.codigo',type:'string'},
			id_grupo:1,
			grid:true,
			form:false
		},
		{
			config:{
				name: 'nombre_partida',
				fieldLabel: 'Partida',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
			type:'Field',
			filters:{pfiltro:'par.nombre_partida',type:'string'},
			id_grupo:1,
			grid:true,
			form:false
		},
		{
			config:{
				name: 'desc_gestion',
				fieldLabel: 'Gestion',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
			type:'Field',
			filters:{pfiltro:'ges.gestion',type:'numeric'},
			id_grupo:1,
			grid:true,
			form:false
		},
		{
			config:{
				name: 'estado_reg',
				fieldLabel: 'Estado Reg.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:10
			},
			type:'TextField',
			filters:{pfiltro:'conp.estado_reg',type:'string'},
			id_grupo:1,
			grid:true,
			form:false
		},
		{
			config:{
				name: 'usr_reg',
				fieldLabel: 'Creado por',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
			type:'NumberField',
			filters:{pfiltro:'usu1.cuenta',type:'string'},
			id_grupo:1,
			grid:true,
			form:false
		},
		{
			config:{
				name: 'fecha_reg',
				fieldLabel: 'Fecha creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
						format: 'd/m/Y', 
						renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
			type:'DateField',
			filters:{pfiltro:'conp.fecha_reg',type:'date'},
			id_grupo:1,
			grid:true,
			form:false
		},
		{
			config:{
				name: 'fecha_mod',
				fieldLabel: 'Fecha Modif.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
						format: 'd/m/Y', 
						renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
			type:'DateField',
			filters:{pfiltro:'conp.fecha_mod',type:'date'},
			id_grupo:1,
			grid:true,
			form:false
		},
		{
			config:{
				name: 'usr_mod',
				fieldLabel: 'Modificado por',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
			type:'NumberField',
			filters:{pfiltro:'usu2.cuenta',type:'string'},
			id_grupo:1,
			grid:true,
			form:false
		}
	],
	
	title:'Concepto-Partida',
	ActSave:'../../sis_presupuestos/control/ConceptoPartida/insertarConceptoPartida',
	ActDel:'../../sis_presupuestos/control/ConceptoPartida/eliminarConceptoPartida',
	ActList:'../../sis_presupuestos/control/ConceptoPartida/listarConceptoPartida',
	id_store:'id_concepto_partida',
	fields: [
		{name:'id_concepto_partida', type: 'numeric'},
		{name:'id_partida', type: 'numeric'},
		{name:'id_concepto_ingas', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},'desc_gestion','desc_partida','codigo_partida','nombre_partida'
		
	],
	
	onReloadPage:function(m)
	{
		this.maestro=m;						
		this.store.baseParams={id_concepto_ingas:this.maestro.id_concepto_ingas};
		this.load({params:{start:0, limit:50}});
				
	},
	
	preparaMenu: function(n) {
		var tb = Phx.vista.ConceptoPartida.superclass.preparaMenu.call(this);
	   	if( this.getBoton('clonarConf')){
	   	   this.getBoton('clonarConf').setDisabled(false);
	   	}
  		return tb;
	},
	liberaMenu: function() {
		var tb = Phx.vista.ConceptoPartida.superclass.liberaMenu.call(this);
		if( this.getBoton('clonarConf')){
		   this.getBoton('clonarConf').setDisabled(false);
		   return tb;
		}
	},
	
	clonarConf:function(){
	     if(confirm('¿Está seguro de clonar? Se clonaran las partidas de todos los conceptos para la siguiente gestion')){
	        var rec = this.sm.getSelected();
		    Phx.CP.loadingShow();
            Ext.Ajax.request({
                url: '../../sis_presupuestos/control/ConceptoPartida/clonarConfig',
                params: { 
                	      id_concepto_partida: rec.data.id_concepto_partida
                	    },
                success: this.successSinc,
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });
	     }
	},
	
	successSinc:function(resp){
            Phx.CP.loadingHide();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            if(!reg.ROOT.error){
            	
            	alert(reg.ROOT.datos.observaciones)
            	this.reload();
             }else{
                alert('ocurrio un error durante el proceso')
            }
    },
	loadValoresIniciales:function()
	{
		
		console.log(this.maestro)
		Phx.vista.ConceptoPartida.superclass.loadValoresIniciales.call(this);
		this.getComponente('id_concepto_ingas').setValue(this.maestro.id_concepto_ingas);
		var tipo = this.maestro.movimiento=='recurso'?'recurso':'gasto';
		this.getComponente('id_partida').store.baseParams.tipo=tipo;		
	},
	sortInfo:{
		field: 'id_concepto_partida',
		direction: 'ASC'
	},
	bdel:true,
	bsave:false,
	bedit:false
	}
)
</script>
		
		