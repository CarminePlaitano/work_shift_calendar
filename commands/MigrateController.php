<?php

namespace backend\modules\work_shift_calendar\commands;

use yii\console\Controller;

class MigrateController extends Controller
{
    public $migrationPath = __DIR__ . '/../migrations';

    public function actionUp($limit = 0)
    {
        $this->runSubcommand('up', [
            'migrationPath' => $this->migrationPath,
            'interactive'   => 0,
            'limit'         => $limit,
        ]);
    }
}