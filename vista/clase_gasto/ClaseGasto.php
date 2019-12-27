<?php
/**
 * @package pXP
 * @file gen-ClaseGasto.php
 * @author  (admin)
 * @date 26-02-2016 01:22:22
 * @description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ClaseGasto = Ext.extend(Phx.gridInterfaz, {

            constructor: function (config) {
                this.maestro = config.maestro;
                //llama al constructor de la clase padre
                Phx.vista.ClaseGasto.superclass.constructor.call(this, config);
                this.init();
                this.load({params: {start: 0, limit: this.tam_pag}})
            },

            Atributos: [
                {
                    //configuracion del componente
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'id_clase_gasto'
                    },
                    type: 'Field',
                    form: true
                },
                {
                    config: {
                        name: 'codigo',
                        fieldLabel: 'Código',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 50,
                        maxLength: 5
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'clg.codigo', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    bottom_filter: true,
                    form: true
                },
                {
                    config: {
                        name: 'nombre',
                        fieldLabel: 'Nombre',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 200,
                        maxLength: 200,
                        style: 'text-transform:uppercase;',
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'clg.nombre', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    bottom_filter: true,
                    form: true
                },
                {
                    config: {
                        name: 'tipo_clase',
                        fieldLabel: 'Tipo',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        typeAhead: true,
                        triggerAction: 'all',
                        selectOnFocus: true,
                        mode: 'local',
                        emptyText: 'Tipo de Clase de Gasto',
                        store: new Ext.data.ArrayStore({
                            fields: ['ID', 'valor'],
                            data: [
                                ['con_imputacion', 'Con Imputación Presupuestaria'],
                                ['sin_imputacion', 'Sin Imputación Presupuestaria']
                            ]
                        }),
                        valueField: 'ID',
                        displayField: 'valor'
                    },
                    type: 'ComboBox',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'estado_reg',
                        fieldLabel: 'Estado Reg.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 10
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'clg.estado_reg', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'usr_reg',
                        fieldLabel: 'Creado por',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 4
                    },
                    type: 'Field',
                    filters: {pfiltro: 'usu1.cuenta', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'usuario_ai',
                        fieldLabel: 'Funcionaro AI',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 300
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'clg.usuario_ai', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'fecha_reg',
                        fieldLabel: 'Fecha creación',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer: function (value, p, record) {
                            return value ? value.dateFormat('d/m/Y H:i:s') : ''
                        }
                    },
                    type: 'DateField',
                    filters: {pfiltro: 'clg.fecha_reg', type: 'date'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'id_usuario_ai',
                        fieldLabel: 'Fecha creación',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 4
                    },
                    type: 'Field',
                    filters: {pfiltro: 'clg.id_usuario_ai', type: 'numeric'},
                    id_grupo: 1,
                    grid: false,
                    form: false
                },
                {
                    config: {
                        name: 'usr_mod',
                        fieldLabel: 'Modificado por',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 4
                    },
                    type: 'Field',
                    filters: {pfiltro: 'usu2.cuenta', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'fecha_mod',
                        fieldLabel: 'Fecha Modif.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer: function (value, p, record) {
                            return value ? value.dateFormat('d/m/Y H:i:s') : ''
                        }
                    },
                    type: 'DateField',
                    filters: {pfiltro: 'clg.fecha_mod', type: 'date'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                }
            ],
            tam_pag: 50,
            title: 'CLASE',
            ActSave: '../../sis_presupuestos/control/ClaseGasto/insertarClaseGasto',
            ActDel: '../../sis_presupuestos/control/ClaseGasto/eliminarClaseGasto',
            ActList: '../../sis_presupuestos/control/ClaseGasto/listarClaseGasto',
            id_store: 'id_clase_gasto',
            fields: [
                {name: 'id_clase_gasto', type: 'numeric'},
                {name: 'estado_reg', type: 'string'},
                {name: 'nombre', type: 'string'},
                {name: 'codigo', type: 'string'},
                {name: 'id_usuario_reg', type: 'numeric'},
                {name: 'usuario_ai', type: 'string'},
                {name: 'fecha_reg', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
                {name: 'id_usuario_ai', type: 'numeric'},
                {name: 'id_usuario_mod', type: 'numeric'},
                {name: 'fecha_mod', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
                {name: 'usr_reg', type: 'string'},
                {name: 'usr_mod', type: 'string'},
                {name: 'tipo_clase', type: 'string'}

            ],

            tabeast: [
                {
                    url: '../../../sis_presupuestos/vista/clase_gasto_partida/ClaseGastoPartida.php',
                    title: 'Partidas',
                    width: '60%',
                    cls: 'ClaseGastoPartida'
                },
                {
                    url: '../../../sis_presupuestos/vista/clase_gasto_cuenta/ClaseGastoCuenta.php',
                    title: 'Cuentas',
                    width: '60%',
                    cls: 'ClaseGastoCuenta'
                }
            ],
            sortInfo: {
                field: 'id_clase_gasto',
                direction: 'ASC'
            },
            bdel: true,
            bsave: true,

            disableTabPartida: function () {
                if (this.TabPanelEast && this.TabPanelEast.get(0)) {
                    this.TabPanelEast.get(0).disable();
                    this.TabPanelEast.get(1).enable();
                    this.TabPanelEast.setActiveTab(1)

                }
            },
            disableTabCuenta: function () {
                if (this.TabPanelEast && this.TabPanelEast.get(1)) {
                    this.TabPanelEast.get(1).disable();
                    this.TabPanelEast.get(0).enable();
                    this.TabPanelEast.setActiveTab(0)

                }
            },
            enableAllTab: function () {
                if (this.TabPanelEast && this.TabPanelEast.get(0) && this.TabPanelEast.get(1)) {
                    this.TabPanelEast.get(0).enable();
                    this.TabPanelEast.get(1).enable();
                }
            },

            preparaMenu: function (n) {
                Phx.vista.ClaseGasto.superclass.preparaMenu.call(this);
                var data = this.getSelectedData();
                var tb = this.tbar;
                console.log('llegaa m', data)
                if (data['tipo_clase'] == 'sin_imputacion') {
                    this.disableTabPartida();
                }
                else {
                    if (data['tipo_clase'] == 'con_imputacion') {
                        this.disableTabCuenta();
                    }
                    else {
                        this.enableAllTab();
                    }
                }
            }
        }
    )
</script>
		
