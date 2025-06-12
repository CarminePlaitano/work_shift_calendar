<?php

return [
    'id' => 'work-shift-calendar-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [],
    'controllerNamespace' => 'carmineplaitano\work_shift_calendar\commands',
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/../../common/config/db.php'),
    ],
    'controllerMap' => [
        'migrate' => [
            'class' => yii\console\controllers\MigrateController::class,
            'migrationPaths' => [
                'carmineplaitano\work_shift_calendar\migrations',
            ],
        ],
    ],
];