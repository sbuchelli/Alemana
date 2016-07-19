<?php

class FACORDENCOMPRController extends Controller {

    public $clie;

    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    public function accessRules() {
        return array(
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('create', 'update', 'index', 'view', 'admin',
                    'delete', 'ClienteByTienda', 'ValorTienda', 'search',
                    'Respaldo', 'Ajax', 'Consulta', 'ocactua', 'Guia',
                    'Reporte', 'Anular'),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionReporte($id) {
        $this->render('Reporte', array(
            'model' => $this->loadModel($id),
        ));
    }

    public function actionGuia($id) {


        $usuario = Yii::app()->user->name;
        $connection = Yii::app()->db;
        $sqlStatement = "call PED_MIGRA_OC_TO_GUIA (:id ,:usuario,@out) ;";
        $command = $connection->createCommand($sqlStatement);

        // $command = $connection->createCommand("CALL remove_places(:user_id,:placeID,:place_type,@out)");
        $command->bindParam(":id", $id, PDO::PARAM_INT);
        $command->bindParam(":usuario", $usuario, PDO::PARAM_INT);

        $command->execute();
        $valueOut = $connection->createCommand("select @out as result;")->queryScalar();

        if ($valueOut == 0) {
            Yii::app()->user->setFlash('error', 'Por lo menos debe ingresar un producto en la O/C para realizar la migracion Guia, por favor revisar');
        }
        Yii::app()->user->setFlash('error', 'Se genero la Guia : ' . $id . ' satisfactoriamente');
        $this->render('index', array(
            'model' => $this->loadModel($id),
        ));
    }

    public function actionConsulta() {
        $this->render('/tEMPFACDETALORDENCOMPR/Consulta');
    }

    public function actionSearch() {
        $this->render('/tEMPFACDETALORDENCOMPR/search');
    }

    public function actionAjax() {
        if ($_GET['type'] == 'id_sele') {
            $id = $_GET["id"];
            $connection = Yii::app()->db;
            $usuario = Yii::app()->user->name;
            $sqlStatement = "call PED_ANULA_OC ('" . $id . "' ,'" . $usuario . "') ;";
            $command = $connection->createCommand($sqlStatement);
            $command->execute();
            //$this->renderPartial('index');
        }

        if ($_GET['type'] == 'id_oc_tg') {
            $id = $_GET["id"];
            $connection = Yii::app()->db;
            $usuario = Yii::app()->user->name;
            $sqlStatement = "call PED_MIGRA_OC_TO_GUIA(:id ,:usuario,@out) ;";
            $command = $connection->createCommand($sqlStatement);

            $command->bindParam(":id", $id, PDO::PARAM_INT);
            $command->bindParam(":usuario", $usuario, PDO::PARAM_INT);

            $command->execute();
            $valueOut = $connection->createCommand("select @out as result;")->queryScalar();

            if ($valueOut == 0) {
                Yii::app()->user->setFlash('error', 'Hay O/C no procesadas por no tener productos asociados, por favor revisar');
            }
            //$this->renderPartial('index');
        }

        if ($_GET['type'] == 'produc_tiend') {
            $cliente = $_GET["clie"];
            $tienda = $_GET["tienda"];
            $row_num = $_GET['row_num'];
            $connection = Yii::app()->db;
            $sqlStatement = "SELECT DES_LARG,COD_PROD,NRO_UNID,GET_VALOR_PRODU(COD_PROD, '" . $tienda . "' ,'" . $cliente . "') VAL_PROD  FROM MAE_PRODU where DES_LARG LIKE '" . strtoupper($_GET['nombre_producto']) . "%'";
            $command = $connection->createCommand($sqlStatement);
            $reader = $command->query();
            $data = array();
            while ($row = $reader->read()) {
                $name = $row['DES_LARG'] . '|' . $row['COD_PROD'] . '|' . $row['NRO_UNID'] . '|' . $row['VAL_PROD'] . '|' . $row_num;
                array_push($data, $name);
            }
            echo json_encode($data);
        }
    }

    public function actionRespaldo() {
        $this->render('/tEMPFACDETALORDENCOMPR/Respaldo');
    }

    public function actionClienteByTienda() {
        $model = new FACORDENCOMPR;
        $COD = $_POST["FACORDENCOMPR"]["NUM_ORDE"];
        $clie = $_POST["FACORDENCOMPR"]["COD_CLIE"];
        $list = MAETIEND::model()->findAll("COD_CLIE = ?", array($_POST["FACORDENCOMPR"]["COD_CLIE"]));
        echo "<option value=\"\">Seleccionar Tienda</option>";
        foreach ($list as $data)
            echo "<option value=\"{$data->COD_TIEN}\">{$data->DES_TIEN}</option>";

        Yii::app()->session['CODIGO'] = $COD;
    }

    public function actionValorTienda() {
        $clie = $_POST["FACORDENCOMPR"]["COD_CLIE"];
        $tienda = $_POST["FACORDENCOMPR"]["COD_TIEN"];
//          echo "{$clie}." - ".{$tienda}";

        $connection = Yii::app()->db;
        $sqlStatement = "Select MC.NRO_RUC,MC.DES_CLIE,MT.DIR_TIEN from MAE_CLIEN MC, MAE_TIEND MT where  MC.COD_CLIE = MT.COD_CLIE and MT.COD_ESTA = 1 and MC.COD_ESTA = 1 and MC.COD_CLIE = $clie and MT.COD_TIEN = $tienda;";
        $command = $connection->createCommand($sqlStatement);
        $reader = $command->query();

        foreach ($reader as $row)
            echo $row['NRO_RUC'];
        echo "/";
        echo $row['DES_CLIE'];
        echo "/";
        echo $row['DIR_TIEN'];
    }

    public function actionView($id) {
        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }

    public function actionCreate() {
        $model = new FACORDENCOMPR;
        $modelOC = new TEMPFACDETALORDENCOMPR();

        if (isset($_POST['FACORDENCOMPR'])) {

            //variables de auditoria
            $connection = Yii::app()->db;
            $usuario = Yii::app()->user->name;
            $ip = getenv("REMOTE_ADDR");
            $pc = @gethostbyaddr($ip);
            $pcip = $pc . ' - ' . $ip;

            $model->attributes = $_POST['FACORDENCOMPR'];
            //date_format($model->FEC_INGR, 'Y-m-d'); 
            $model->FEC_INGR = substr($model->FEC_INGR, 6, 4) . '/' . substr($model->FEC_INGR, 3, 2) . '/' . substr($model->FEC_INGR, 0, 2); //'2016-06-09' ;
            $model->FEC_ENVI = substr($model->FEC_ENVI, 6, 4) . '/' . substr($model->FEC_ENVI, 3, 2) . '/' . substr($model->FEC_ENVI, 0, 2); //'2016-06-09' ;
            $model->COD_ORDE = $model->au();
            $model->USU_DIGI = $usuario;

            if (isset($_POST['COD_PROD'])) {

                $CODPRO = $_POST['COD_PROD'];
                $DESCRI = $_POST['DES_LARG'];
                $UND = $_POST['NRO_UNID'];
                $VALPRE = $_POST['VAL_PREC'];
                $VALMOTUND = $_POST['VAL_MONT_UNID'];

                $count = Yii::app()->db->createCommand()->select('count(*)')
                        ->from('FAC_ORDEN_COMPR')
                        ->where("NUM_ORDE = '" . $model->NUM_ORDE . "' and COD_CLIE = '" . $model->COD_CLIE . "' and COD_TIEN = '" . $model->COD_TIEN . "';")
                        ->queryScalar();

                $id = ($count);

                if ($id > 0) {
                    Yii::app()->user->setFlash('error', 'La O/C ya ha sido ingresada para la relación cliente/tienda, por favor revisar');
                } else {
                    if ($model->save()) {
                        for ($i = 0; $i < count($CODPRO); $i++) {
                            if ($CODPRO[$i] <> '') {
                                $sqlStatement = "call PED_CREAR_DETAL_OC('" . $i . "',
                     '" . $model->COD_ORDE . "',
                     '" . $model->COD_TIEN . "',
                     '" . $model->COD_CLIE . "',
                     '" . $CODPRO[$i] . "', 
                     '" . $UND[$i] . "',
                     '" . $VALPRE[$i] . "', 
                     '" . $VALMOTUND[$i] . "',
                     '" . $DESCRI[$i] . "',
                     '" . $usuario . "',
                     '" . $pcip . "')";
                                $command = $connection->createCommand($sqlStatement);
                                $command->execute();
                            }
                        }
                        $this->redirect(array('index'));
                    }
                }
            } else {
                Yii::app()->user->setFlash('error', 'Por lo menos debe ingresar un producto en la O/C');
            }
            Yii::app()->user->setFlash('success', 'Se genero la O/C satisfactoriamente.');
        }


        $this->render('create', array(
            'model' => $model,
            'modelOC' => $modelOC,
        ));
    }

    public function actionUpdate($id) {
        $modelOC = new TEMPFACDETALORDENCOMPR();
        $model = $this->loadModel($id);

        if (isset($_POST['FACORDENCOMPR'])) {

            //variables de auditoria
            $connection = Yii::app()->db;
            $usuario = Yii::app()->user->name;
            $ip = getenv("REMOTE_ADDR");
            $pc = @gethostbyaddr($ip);
            $pcip = $pc . ' - ' . $ip;

            $model->attributes = $_POST['FACORDENCOMPR'];
            //date_format($model->FEC_INGR, 'Y-m-d'); 
            if (strrpos($model->FEC_ENVI, "/") > 0) {
                $model->FEC_ENVI = substr($model->FEC_ENVI, 6, 4) . '/' . substr($model->FEC_ENVI, 3, 2) . '/' . substr($model->FEC_ENVI, 0, 2); //'2016-06-09' ;
            }
            $model->USU_DIGI = $usuario;

            if (isset($_POST['COD_PROD'])) {

                $CODPRO = $_POST['COD_PROD'];
                $DESCRI = $_POST['DES_LARG'];
                $UND = $_POST['NRO_UNID'];
                $VALPRE = $_POST['VAL_PREC'];
                $VALMOTUND = $_POST['VAL_MONT_UNID'];

                $count = Yii::app()->db->createCommand()->select('count(*)')
                        ->from('FAC_ORDEN_COMPR')
                        ->where("NUM_ORDE = '" . $model->NUM_ORDE . "' and COD_CLIE = '" . $model->COD_CLIE . "' and COD_TIEN = '" . $model->COD_TIEN . "';")
                        ->queryScalar();

                $id = ($count);


                if ($model->update()) {
                    for ($i = 0; $i < count($CODPRO); $i++) {
                        if ($CODPRO[$i] <> '') {
                            $sqlStatement = "call PED_ACTUA_DETAL_OC('" . $i . "',
                     '" . $model->COD_ORDE . "',
                     '" . $model->COD_TIEN . "',
                     '" . $model->COD_CLIE . "',
                     '" . $CODPRO[$i] . "', 
                     '" . $UND[$i] . "',
                     '" . $VALPRE[$i] . "', 
                     '" . $VALMOTUND[$i] . "',
                     '" . $DESCRI[$i] . "',
                     '" . $usuario . "',
                     '" . $pcip . "')";
                            $command = $connection->createCommand($sqlStatement);
                            $command->execute();
                        }
                    }
                    $this->redirect(array('view', 'id' => $model->COD_ORDE));
                }
            } else {
                Yii::app()->user->setFlash('error', 'Por lo menos debe ingresar un producto en la O/C');
            }
        }


        $this->render('update', array(
            'model' => $model,
            'modelOC' => $modelOC,
        ));
    }

    public function actionDelete($id) {
        $this->loadModel($id)->delete();
        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    public function actionAnular($id) {
        $model = new FACORDENCOMPR('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_POST['FACORDENCOMPR']))
            $model->attributes = $_POST['FACORDENCOMPR'];

        $connection = Yii::app()->db;
        $usuario = Yii::app()->user->name;
        $sqlStatement = "call PED_ANULA_OC ('" . $id . "' ,'" . $usuario . "') ;";
        $command = $connection->createCommand($sqlStatement);
        $command->execute();

        $model->IND_ESTA = '9';
        $this->render('/fACORDENCOMPR/Anulado', array(
            'model' => $model,
        ));
    }

    public function actionIndex() {
        $model = new FACORDENCOMPR('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_POST['FACORDENCOMPR']))
            $model->attributes = $_POST['FACORDENCOMPR'];

        $this->render('index', array(
            'model' => $model,
        ));
    }

    public function actionAdmin() {
        $model = new FACORDENCOMPR('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['FACORDENCOMPR']))
            $model->attributes = $_GET['FACORDENCOMPR'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }

    public function actionOcactua() {
        $model = new FACORDENCOMPR('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['FACORDENCOMPR']))
            $model->attributes = $_GET['FACORDENCOMPR'];

        $this->render('ocactua', array(
            'model' => $model,
        ));
    }

    public function loadModel($id) {
        $model = FACORDENCOMPR::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'facordencompr-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}

?>