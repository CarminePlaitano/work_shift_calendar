<?php

namespace backend\modules\work_shift_calendar\controllers;

use backend\modules\work_shift_calendar\models\Employee;
use backend\modules\work_shift_calendar\models\Event;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class DefaultController extends Controller
{
    public function actionIndex(): string
    {
        return $this->render('index');
    }

    public function actionEvents(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return Event::find()->with('employees')->asArray()->all();
    }

    public function actionResources(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return Employee::find()->select(['id', "CONCAT(first_name, ' ', last_name) AS title"])->asArray()->all();
    }

    /**
     * @throws Exception
     * @throws BadRequestHttpException|InvalidConfigException
     */
    public function actionSaveEvent(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;
        if (!$request->isPost) {
            throw new BadRequestHttpException('Richiesta non valida.');
        }

        $model = new Event();
        $formData = $request->getBodyParams();

        $model->load($formData, '');

        if ($model->validate() && $model->save()) {
            return [
                'success' => true,
                'message' => 'Evento salvato con successo.',
                'data' => $model,
            ];
        }

        return [
            'success' => false,
            'errors' => $model->getErrors(),
        ];
    }


    /**
     * Updates an existing Event record.
     * @param integer $id
     * @return array
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     * @throws Exception|InvalidConfigException
     */
    public function actionUpdateEvent(int $id): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $model = Event::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Evento non trovato.');
        }

        if (!$request->isPost) {
            throw new BadRequestHttpException('Richiesta non valida.');
        }

        $formData = $request->getBodyParams();
        $model->load($formData, '');

        if ($model->validate() && $model->save()) {
            return [
                'success' => true,
                'message' => 'Evento aggiornato con successo.',
                'data' => $model,
            ];
        }

        return [
            'success' => false,
            'errors' => $model->getErrors(),
        ];
    }

    /**
     * Delete an existing Event record
     * @param integer $id
     * @return array
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     */
    public function actionDeleteEvent(int $id): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        if (!$request->isPost) {
            throw new BadRequestHttpException('Richiesta non valida.');
        }
        $model = Event::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Evento non trovato.');
        }
        if ($model->delete() !== false) {
            return ['success' => true];
        } else {
            return ['success' => false, 'errors' => $model->getErrors()];
        }
    }
}
