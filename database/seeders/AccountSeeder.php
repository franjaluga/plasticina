<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Accounts\Account;

class AccountSeeder extends Seeder
{
    // Método estático para proveer las cuentas al momento de registrar un Owner
    public static function getTemplateAccounts(): array
    {
        return [
            // ACTIVO CORRIENTE (101)
            ['code' => '1010101', 'name' => 'CAJA', 'category' => 'activo'],
            ['code' => '1010102', 'name' => 'BANCOS', 'category' => 'activo'],
            ['code' => '1010103', 'name' => 'BANCOESTADO', 'category' => 'activo'],
            ['code' => '1010104', 'name' => 'DEPOSITOS EN TRANSITO', 'category' => 'activo'],
            ['code' => '1010210', 'name' => 'DEPOSITO A PLAZO BANCOS', 'category' => 'activo'],
            ['code' => '1010220', 'name' => 'FONDOS MUTUOS', 'category' => 'activo'],
            ['code' => '1010310', 'name' => 'UTILES DE OFICINA', 'category' => 'activo'],
            ['code' => '1010320', 'name' => 'MATERIALES DE OFICINA', 'category' => 'activo'],
            ['code' => '1010350', 'name' => 'ANTICIPOS A PROVEEDORES', 'category' => 'activo'],
            ['code' => '1010351', 'name' => 'ANTICIPOS PUBLICIDAD', 'category' => 'activo'],
            ['code' => '1010352', 'name' => 'ANTICIPOS DE HONORARIOS', 'category' => 'activo'],
            ['code' => '1010353', 'name' => 'ANTICIPOS DE SUELDOS', 'category' => 'activo'],
            ['code' => '1010370', 'name' => 'PRIMAS DE SEGURO', 'category' => 'activo'],
            ['code' => '1010380', 'name' => 'RENTAS PAGADAS POR ANTICIPADO', 'category' => 'activo'],
            ['code' => '1010401', 'name' => 'CLIENTES', 'category' => 'activo'],
            ['code' => '1010415', 'name' => 'HONORARIOS POR COBRAR', 'category' => 'activo'],
            ['code' => '1010420', 'name' => 'ESTIMACION DEUDORES INCOBRABLE', 'category' => 'activo'],
            ['code' => '1010430', 'name' => 'CHEQUES A FECHA', 'category' => 'activo'],
            ['code' => '1010431', 'name' => 'LETRAS POR COBRAR', 'category' => 'activo'],
            ['code' => '1010432', 'name' => 'PAGARES POR COBRAR', 'category' => 'activo'],
            ['code' => '1010440', 'name' => 'IMPUESTOS POR RECUPERAR', 'category' => 'activo'],
            ['code' => '1010441', 'name' => 'DEVOLUCIONES PRESCRITAS', 'category' => 'activo'],
            ['code' => '1010501', 'name' => 'CXC EMP. RELACIONADAS', 'category' => 'activo'],
            ['code' => '1010502', 'name' => 'OTROS DEUDORES', 'category' => 'activo'],
            ['code' => '1010610', 'name' => 'MATERIAS PRIMAS', 'category' => 'activo'],
            ['code' => '1010611', 'name' => 'MATERIALES DIRECTOS', 'category' => 'activo'],
            ['code' => '1010620', 'name' => 'PRODUCTOS EN PROCESO', 'category' => 'activo'],
            ['code' => '1010621', 'name' => 'PRODUCTOS TERMINADOS', 'category' => 'activo'],
            ['code' => '1010630', 'name' => 'MERCADERIAS NACIONALES', 'category' => 'activo'],
            ['code' => '1010640', 'name' => 'IMPORTACIONES EN TRANSITO', 'category' => 'activo'],
            ['code' => '1010690', 'name' => 'PROVISION MERCADERIA OBSOLETA', 'category' => 'activo'],
            ['code' => '1010701', 'name' => 'ACTIVOS BIOLOGICOS CORRIENTES', 'category' => 'activo'],
            ['code' => '1010801', 'name' => 'PPM', 'category' => 'activo'],
            ['code' => '1010802', 'name' => 'IVA CREDITO FISCAL', 'category' => 'activo'],
            ['code' => '1010803', 'name' => 'IVA REMANENTE CREDITO FISCAL', 'category' => 'activo'],
            ['code' => '1010804', 'name' => 'IMPUESTO ADICIONAL', 'category' => 'activo'],
            ['code' => '1010805', 'name' => 'IMPUESTO ESPECIFICO', 'category' => 'activo'],
            ['code' => '1010825', 'name' => 'RETENCION HONORARIO POR COBRAR', 'category' => 'activo'],
            ['code' => '1010910', 'name' => 'ACTIVO MANT. PARA VENTA', 'category' => 'activo'],
            ['code' => '1010920', 'name' => 'ACTIVO MANT. PARA PROPIETARIOS', 'category' => 'activo'],

            // ACTIVO NO CORRIENTE (102)
            ['code' => '1020101', 'name' => 'OTROS FINANCIEROS NO CORR.', 'category' => 'activo'],
            ['code' => '1020201', 'name' => 'GARANTIA ARRIENDO', 'category' => 'activo'],
            ['code' => '1020301', 'name' => 'DEUDORES A LARGO PLAZO', 'category' => 'activo'],
            ['code' => '1020410', 'name' => 'CXC A EMPRESA RELACIONADA L.P.', 'category' => 'activo'],
            ['code' => '1020420', 'name' => 'DXC A EMPRESA RELACIONADA L.P.', 'category' => 'activo'],
            ['code' => '1020430', 'name' => 'OTROS DXC A EMP RELACIONADA LP', 'category' => 'activo'],
            ['code' => '1020501', 'name' => 'INV. CONTAB. METODO PARTIC', 'category' => 'activo'],
            ['code' => '1020601', 'name' => 'DERECHOS DE AUTOR', 'category' => 'activo'],
            ['code' => '1020602', 'name' => 'MARCA REGISTRADA', 'category' => 'activo'],
            ['code' => '1020603', 'name' => 'PATENTES', 'category' => 'activo'],
            ['code' => '1020604', 'name' => 'SOFTWARE', 'category' => 'activo'],
            ['code' => '1020605', 'name' => 'FRANQUICIAS', 'category' => 'activo'],
            ['code' => '1020606', 'name' => 'GASTOS DE CONSTITUCION', 'category' => 'activo'],
            ['code' => '1020701', 'name' => 'PLUSVALIA', 'category' => 'activo'],
            ['code' => '1020810', 'name' => 'MAQUINARIAS Y EQUIPOS', 'category' => 'activo'],
            ['code' => '1020811', 'name' => 'DEP ACUM MAQUINARIAS Y EQUIPOS', 'category' => 'activo'],
            ['code' => '1020820', 'name' => 'MUEBLES Y UTILES', 'category' => 'activo'],
            ['code' => '1020821', 'name' => 'DEPR ACUM MUEBLES Y UTILES', 'category' => 'activo'],
            ['code' => '1020824', 'name' => 'EQUIPOS COMUNICACION', 'category' => 'activo'],
            ['code' => '1020825', 'name' => 'DEP ACUM EQUIPOS COMUNICACION', 'category' => 'activo'],
            ['code' => '1020830', 'name' => 'INSTALACIONES', 'category' => 'activo'],
            ['code' => '1020831', 'name' => 'DEPR ACUM INSTALACIONES', 'category' => 'activo'],
            ['code' => '1020901', 'name' => 'ACTIVOS BIOLOGICOS NO CORR.', 'category' => 'activo'],
            ['code' => '1021001', 'name' => 'PROPIEDAD DE INVERSION', 'category' => 'activo'],
            ['code' => '1021101', 'name' => 'ACTIVOS POR IMPUESTOS DIFERIDO', 'category' => 'activo'],

            // PASIVO CORRIENTE (201)
            ['code' => '2010101', 'name' => 'DEUDAS CON BANCOS CORTO PLAZO', 'category' => 'pasivo'],
            ['code' => '2010102', 'name' => 'LINEA DE CREDITO BANCOS', 'category' => 'pasivo'],
            ['code' => '2010120', 'name' => 'ACREEDORES POR LEASING', 'category' => 'pasivo'],
            ['code' => '2010130', 'name' => 'OBLIGACIONES POR FACTORING', 'category' => 'pasivo'],
            ['code' => '2010201', 'name' => 'PROVEEDORES', 'category' => 'pasivo'],
            ['code' => '2010210', 'name' => 'CUENTAS POR PAGAR', 'category' => 'pasivo'],
            ['code' => '2010211', 'name' => 'RENDICIONES POR PAGAR', 'category' => 'pasivo'],
            ['code' => '2010215', 'name' => 'SUELDOS POR PAGAR', 'category' => 'pasivo'],
            ['code' => '2010220', 'name' => 'HONORARIOS POR PAGAR', 'category' => 'pasivo'],
            ['code' => '2010225', 'name' => 'IMPOSICIONES POR PAGAR', 'category' => 'pasivo'],
            ['code' => '2010230', 'name' => 'IMPUESTOS MENSUALES', 'category' => 'pasivo'],
            ['code' => '2010231', 'name' => 'ANTICIP. CLIENTES', 'category' => 'pasivo'],
            ['code' => '2010299', 'name' => 'IMPTO RENTA POR PAGAR', 'category' => 'pasivo'],
            ['code' => '2010401', 'name' => 'CXP ENTIDAD RELACIONADA CORR.', 'category' => 'pasivo'],
            ['code' => '2010590', 'name' => 'PROVISIONES VARIAS', 'category' => 'pasivo'],
            ['code' => '2010910', 'name' => 'IMPUESTO UNICO TRABAJADORES', 'category' => 'pasivo'],
            ['code' => '2010911', 'name' => 'IVA RETENIDO TERC', 'category' => 'pasivo'],
            ['code' => '2010920', 'name' => 'IVA DEBITO FISCAL', 'category' => 'pasivo'],
            ['code' => '2010925', 'name' => 'OTROS IMPUESTOS', 'category' => 'pasivo'],
            ['code' => '2010930', 'name' => 'RETENCION IMPUESTO HONORARIOS', 'category' => 'pasivo'],
            ['code' => '2010940', 'name' => 'RETENCION INGRESO HONORARIO', 'category' => 'pasivo'],
            ['code' => '2010990', 'name' => 'PROVISION IMPUESTO A LA RENTA', 'category' => 'pasivo'],
            ['code' => '2011001', 'name' => 'PROVISION VACACIONES PERSONAL', 'category' => 'pasivo'],
            ['code' => '2011101', 'name' => 'INGRESOS PERCIBIDOS ADELANTADO', 'category' => 'pasivo'],
            ['code' => '2011120', 'name' => 'ARRIENDOS RECIBIDOS GARANTIA', 'category' => 'pasivo'],

            // PASIVO NO CORRIENTE (202)
            ['code' => '2020101', 'name' => 'DEUDAS CON BANCOS LARGO PLAZO', 'category' => 'pasivo'],
            ['code' => '2020201', 'name' => 'ACREEDORES LEASING A L.P.', 'category' => 'pasivo'],
            ['code' => '2020301', 'name' => 'CXP EMP. RELACIONADAS L.P.', 'category' => 'pasivo'],
            ['code' => '2020302', 'name' => 'CXP SOCIOS A L.P.', 'category' => 'pasivo'],
            ['code' => '2020601', 'name' => 'PROVISION INDEM. AÑOS SERVICIO', 'category' => 'pasivo'],

            // PATRIMONIO (3)
            ['code' => '30101', 'name' => 'CAPITAL', 'category' => 'patrimonio'],
            ['code' => '30102', 'name' => 'CAPITAL PREFERENTE', 'category' => 'patrimonio'],
            ['code' => '30103', 'name' => 'ACCIONISTAS', 'category' => 'patrimonio'],
            ['code' => '30201', 'name' => 'PERDIDAS ACUMULADAS', 'category' => 'patrimonio'],
            ['code' => '30202', 'name' => 'UTILIDADES ACUMULADAS', 'category' => 'patrimonio'],
            ['code' => '30203', 'name' => 'PERDIDAS Y GANANCIAS EJERCICIO', 'category' => 'patrimonio'],
            ['code' => '30301', 'name' => 'CTA CTE SOCIO 1', 'category' => 'patrimonio'],
            ['code' => '30302', 'name' => 'CTA CTE SOCIO 2', 'category' => 'patrimonio'],
            ['code' => '30303', 'name' => 'DIVIDENDOS PROVISORIOS', 'category' => 'patrimonio'],
            ['code' => '304', 'name' => 'ACCIONES PROPIAS EN CARTERA', 'category' => 'patrimonio'],
            ['code' => '305', 'name' => 'OTRAS PARTICIP. PATRIMONIO', 'category' => 'patrimonio'],
            ['code' => '30601', 'name' => 'REVALORIZACION CAPITAL PROPIO', 'category' => 'patrimonio'],
            ['code' => '307', 'name' => 'PATRIMONIO DUEÑOS CONTROLADORA', 'category' => 'patrimonio'],
            ['code' => '308', 'name' => 'PARTICIPACIONES NO CONTROLADOR', 'category' => 'patrimonio'],

            // INGRESOS (4)
            ['code' => '4010110', 'name' => 'VENTAS Y SERVICIOS AFECTOS', 'category' => 'ganancia'],
            ['code' => '4010120', 'name' => 'VENTAS Y SERVICIOS EXENTOS', 'category' => 'ganancia'],
            ['code' => '4010130', 'name' => 'EXPORTACIONES', 'category' => 'ganancia'],
            ['code' => '4010131', 'name' => 'ASESORIAS', 'category' => 'ganancia'],
            ['code' => '4010132', 'name' => 'CAPACITACIONES', 'category' => 'ganancia'],
            ['code' => '4010133', 'name' => 'PROGRAMACION', 'category' => 'ganancia'],
            ['code' => '4010134', 'name' => 'VTA. LIBROS', 'category' => 'ganancia'],
            ['code' => '4010601', 'name' => 'OTROS INGRESOS, POR FUNCION', 'category' => 'ganancia'],
            ['code' => '4010602', 'name' => 'OTROS INGRESOS', 'category' => 'ganancia'],
            ['code' => '4012110', 'name' => 'INTERESES GANADOS', 'category' => 'ganancia'],
            ['code' => '4012410', 'name' => 'DIFERENCIAS DE CAMBIO', 'category' => 'ganancia'],
            ['code' => '4012501', 'name' => 'REAJUSTE CREDITO IVA Y PPM', 'category' => 'ganancia'],
            ['code' => '4012502', 'name' => 'UNIDAD REAJUSTABLE', 'category' => 'ganancia'],
            ['code' => '4012570', 'name' => 'REAJUSTE ACTIVOS EN UF', 'category' => 'ganancia'],
            ['code' => '4012590', 'name' => 'REAJUSTE PASIVOS EN UF', 'category' => 'ganancia'],
            ['code' => '4019310', 'name' => 'GANANCIAS OPE. DISCONTINUADAS', 'category' => 'ganancia'],

            // COSTOS Y GASTOS / PÉRDIDA (5)
            ['code' => '4010210', 'name' => 'COSTO VENTAS AFECTAS', 'category' => 'perdida'],
            ['code' => '4010220', 'name' => 'COSTO VENTAS EXENTAS', 'category' => 'perdida'],
            ['code' => '4010230', 'name' => 'COSTO EXPORTACIONES', 'category' => 'perdida'],
            ['code' => '4010250', 'name' => 'COSTOS DE COMISION EN VENTAS', 'category' => 'perdida'],
            ['code' => '4010701', 'name' => 'MOVILIZACION', 'category' => 'perdida'],
            ['code' => '4010810', 'name' => 'GASTOS DE SUELDOS Y SALARIOS', 'category' => 'perdida'],
            ['code' => '4010812', 'name' => 'GASTOS DE FONASA E ISAPRE', 'category' => 'perdida'],
            ['code' => '4010813', 'name' => 'GASTOS DE AFC EMPLEADOR', 'category' => 'perdida'],
            ['code' => '4010814', 'name' => 'GASTOS DE SEGURO DE INVALIDEZ', 'category' => 'perdida'],
            ['code' => '4010815', 'name' => 'GASTOS DE SEGURO ACCIDENTE', 'category' => 'perdida'],
            ['code' => '4010816', 'name' => 'GASTOS DE MOVILIZACION', 'category' => 'perdida'],
            ['code' => '4010817', 'name' => 'GASTOS DE COLACION', 'category' => 'perdida'],
            ['code' => '4010818', 'name' => 'BONOS Y OTROS', 'category' => 'perdida'],
            ['code' => '4010822', 'name' => 'HONORARIOS', 'category' => 'perdida'],
            ['code' => '4010890', 'name' => 'GASTOS GENERALES', 'category' => 'perdida'],
            ['code' => '4010891', 'name' => 'ARRIENDOS', 'category' => 'perdida'],
            ['code' => '4010892', 'name' => 'ASESORIAS', 'category' => 'perdida'],
            ['code' => '4010893', 'name' => 'CHECKPOINT TRIBUTARIO', 'category' => 'perdida'],
            ['code' => '4010894', 'name' => 'SOFTWARE', 'category' => 'perdida'],
            ['code' => '4010895', 'name' => 'DEPRECIACIONES', 'category' => 'perdida'],
            ['code' => '4010896', 'name' => 'ZOOM VIDEOCONFERENCIAS', 'category' => 'perdida'],
            ['code' => '4010901', 'name' => 'GASTOS NOTARIALES', 'category' => 'perdida'],
            ['code' => '4010902', 'name' => 'CERTIFICADOS', 'category' => 'perdida'],
            ['code' => '4010903', 'name' => 'DOMINIOS', 'category' => 'perdida'],
            ['code' => '4010904', 'name' => 'HOST', 'category' => 'perdida'],
            ['code' => '4010905', 'name' => 'PATENTE MUNICIPAL', 'category' => 'perdida'],
            ['code' => '4010906', 'name' => 'COURIER', 'category' => 'perdida'],
            ['code' => '4010907', 'name' => 'COMISIONES', 'category' => 'perdida'],
            ['code' => '4010908', 'name' => 'CASTIGO INCOBRABLE', 'category' => 'perdida'],
            ['code' => '4010916', 'name' => 'OTROS GASTOS, POR FUNCION', 'category' => 'perdida'],
            ['code' => '4010917', 'name' => 'DETERIORO MERC. NAC.', 'category' => 'perdida'],
            ['code' => '4010999', 'name' => 'IMPTO RENTA', 'category' => 'perdida'],
            ['code' => '4011021', 'name' => 'IVA NO RECUPERABLE', 'category' => 'perdida'],
            ['code' => '4012210', 'name' => 'COSTOS FINANCIEROS', 'category' => 'perdida'],
            ['code' => '4019101', 'name' => 'IMPUESTO A LA RENTA', 'category' => 'perdida'],
            ['code' => '4019320', 'name' => 'PERDIDAS OPE. DISCONTINUADA', 'category' => 'perdida'],
        ];
    }

    public function run(): void
    {
        $accounts = self::getTemplateAccounts();

        foreach ($accounts as $acc) {
            Account::updateOrCreate(
                ['code' => $acc['code']],
                $acc
            );
        }
    }
}