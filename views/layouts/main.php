<?php

/* @var $this View */
/* @var $content string */

use yii\web\View;

?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?= Yii::$app->request->csrfToken ?>">
        <title>TEST</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@event-calendar/build@4.2.0/dist/event-calendar.min.css">
        <script src="https://cdn.jsdelivr.net/npm/@event-calendar/build@4.2.0/dist/event-calendar.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
        <script src="/js/moment.min.js"></script>
        <script src="/js/moment-timezone-with-data.min.js"></script>
        <?php $this->head() ?>
    </head>
    <body class="hold-transition sidebar-mini">
    <?php $this->beginBody() ?>

    <div class="wrapper p-2">
        <?= $this->render('content', ['content' => $content]) ?>
    </div>

    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>