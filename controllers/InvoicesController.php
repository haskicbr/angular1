<?php

namespace app\controllers;

use app\models\Invoice;
use Yii;
use yii\helpers\json as JSON;
use yii\web\Controller;

class InvoicesController extends Controller
{

    const STATUS_OK = 'ok';
    const STATUS_ERROR = 'error';

    public $request;
    public $requestParams;
    public $response = [

    ];


    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    /**
     * @param \yii\base\Action $action
     * @return bool
     */
    public function beforeAction($action)
    {
        $this->request = Yii::$app->request;

        $this->requestParams = $this->request->bodyParams;


        $this->enableCsrfValidation = false;

        $this->response['status'] = self::STATUS_OK;

        return parent::beforeAction($action);
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $invoices = Invoice::find()->all();

        return JSON::encode($invoices);
    }

    /**
     * @return string
     */
    public function actionUpdate($id)
    {
        $invoice = Invoice::findOne(['id' => $id]);

        if (!empty($invoice)) {
            $invoice->attributes = $this->requestParams;
            $invoice->save();
        } else {
            $this->response['status'] = self::STATUS_ERROR;
            $this->response['message'] = 'empty invoice item';
        }

        return JSON::encode($this->response);
    }

    /**
     * @param $id
     * @return string
     */
    public function actionDelete()
    {

        $ids = (!empty($this->request->bodyParams['ids'])) ? $this->request->bodyParams['ids'] : false;

        if (!empty($ids)) {

            foreach ($ids as $id) {
                $invoice = Invoice::findOne(['id' => $id]);
                if (!empty($invoice)) {
                    $invoice->delete();
                }
            }
        } else {
            $this->response['status'] = self::STATUS_ERROR;
            $this->response['message'] = 'empty invoice item';
        }

        return JSON::encode($this->response);
    }


    /**
     * @return string
     */
    public function actionCreate()
    {
        $invoice = new Invoice();

        $invoice->attributes = $this->request->bodyParams;
        if ($invoice->validate()) {
            $invoice->save(false);
            $this->response['model'] = $invoice;
        } else {
            $this->response['status'] = self::STATUS_ERROR;
            $this->response['message'] = $invoice->getErrors();
        }

        return JSON::encode($this->response);
    }
}