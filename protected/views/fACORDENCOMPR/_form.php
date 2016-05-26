<?php
/* @var $this FACORDENCOMPRController */
/* @var $model FACORDENCOMPR */
/* @var $form CActiveForm */
?>
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/styleV2.css">


<div class="container-fluid">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Registrar Nuevo O/C</h3>
        </div>

        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'facordencompr-form',
            // Please note: When you enable ajax validation, make sure the corresponding
            // controller action is handling ajax validation correctly.
            // There is a call to performAjaxValidation() commented in generated controller code.
            // See class documentation of CActiveForm for details on this.
            'enableAjaxValidation' => false,
        ));
        ?>
 <br>

    <div class="container-fluid">
        <p class="note">Los aspectos con <span class="required letra"> (*) </span> son requeridos.</p>
    </div>

            <?php // echo $form->errorSummary($model); ?>

                <!--<div class="col-sm-3 control-label">-->
                    <?php // echo $form->labelEx($model, 'COD_ORDE'); ?>
                    <?php // echo $form->textField($model, 'COD_ORDE', array('size' => 6, 'maxlength' => 6, 'class' => 'form-control', 'placeholder' => 'N° de Orden')); ?>
                    <?php // echo $form->error($model, 'COD_ORDE'); ?>
                <!--</div>-->
            
            
            
            
        <div class="fieldset">
            
            <div class="form-group ir">
                <div class="col-sm-3 control-label">
                    <?php echo $form->labelEx($model, 'NUM_ORDE'); ?>
                    <?php echo $form->textField($model, 'NUM_ORDE', array('maxlength' => 6, 'class' => 'form-control', 'placeholder' => 'N° de Orden')); ?>
                    <?php echo $form->error($model, 'NUM_ORDE'); ?>
                </div>

                <div class="col-sm-3 control-label">
                    <?php echo $form->labelEx($model, 'COD_CLIE'); ?>
                    <?php echo $form->dropDownList($model, 'COD_CLIE',$model->ListaCliente(), array('class' => 'form-control', 'empty' => 'Seleccionar Cliente')); ?>
                    <?php echo $form->error($model, 'COD_CLIE'); ?>
                </div>
                
                <div class="col-sm-3 control-label">
                    <?php echo $form->labelEx($model, 'COD_TIEN'); ?>
                    <?php echo $form->textField($model, 'COD_TIEN', array('maxlength' => 12, 'class' => 'form-control', 'placeholder' => 'Ingrese el Nombre del Analista')); ?>
                    <?php echo $form->error($model, 'COD_TIEN'); ?>
                </div>                
                
                 <div class="col-sm-3 control-label">
                    <?php echo $form->labelEx($model, 'TIP_MONE'); ?>
                    <?php echo $form->dropDownList($model, 'TIP_MONE',$model->Moneda(), array('class' => 'form-control', 'empty' => 'Seleccionar Moneda')); ?>
                    <?php echo $form->error($model, 'TIP_MONE'); ?>
                </div>               
            </div>
            
                        <div class="form-group ir">
                <div class="col-sm-3 control-label">
                    <?php echo $form->labelEx($model, 'FEC_INGR'); ?>
                                        <?php
                    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'model' => $model,
                        'attribute' => 'FEC_INGR',
                        'value' => $model->FEC_INGR,
                        'language' => 'es',
                        'htmlOptions' => array('class' => 'form-control', 'placeholder' => 'Ingrese la Fecha Ingreso'),
                        'options' => array(
                            'autoSize' => true,
                            'defaultDate' => $model->FEC_INGR,
                            'dateFormat' => 'dd-mm-yy',
                            'buttonImage' => Yii::app()->baseUrl . '/images/calendario.gif',
                            'buttonImageOnly' => true,
                            'buttonText' => 'FEC_INGR',
                            'selectOtherMonths' => true,
                            'showAnim' => 'slide',
                            'showOtherMonths' => true,
                            'changeMonth' => 'true',
                            'changeYear' => 'true',
                            'minDate' => 'date("Y-MM-DD")',
                            'maxDate' => "+20Y",
                        ),
                    ));
                    ?>
                    <?php echo $form->error($model, 'FEC_INGR'); ?>
                </div>

                <div class="col-sm-3 control-label">
                    <?php echo $form->labelEx($model, 'FEC_ENVI'); ?>
                                        <?php
                    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'model' => $model,
                        'attribute' => 'FEC_ENVI',
                        'value' => $model->FEC_ENVI,
                        'language' => 'es',
                        'htmlOptions' => array('class' => 'form-control', 'placeholder' => 'Ingrese la Fecha Envio'),
                        'options' => array(
                            'autoSize' => true,
                            'defaultDate' => $model->FEC_ENVI,
                            'dateFormat' => 'dd-mm-yy',
                            'buttonImage' => Yii::app()->baseUrl . '/images/calendario.gif',
                            'buttonImageOnly' => true,
                            'buttonText' => 'FEC_ENVI',
                            'selectOtherMonths' => true,
                            'showAnim' => 'slide',
                            'showOtherMonths' => true,
                            'changeMonth' => 'true',
                            'changeYear' => 'true',
                            'minDate' => 'date("Y-MM-DD")',
                            'maxDate' => "+20Y",
                        ),
                    ));
                    ?>
                    <?php echo $form->error($model, 'FEC_ENVI'); ?>
                </div>
                            
            </div>
            
        </div>

                <br><br><br><br><br><br><br>   <br>         
                <legend>Datos del Cliente</legend>
 


            
            
           

                
                
                <br><br><br><br><br>   <br><br><br><br><br>  
                
                
            
        <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
        </div>

<?php $this->endWidget(); ?>

    </div><!-- form -->