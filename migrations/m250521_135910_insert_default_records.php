<?php

use yii\db\Migration;

class m250521_135910_insert_default_records extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%employee_type}}', [
            'name' => 'amministrazione',
            'label' => 'Dipendente Amministrativo'
        ]);

        $this->insert('{{%employee_type}}', [
            'name' => 'forestale',
            'label' => 'Dipendente Forestale'
        ]);

        $this->insert('{{%employee_type}}', [
            'name' => 'antincendio',
            'label' => 'Dipendente Forestale / Squadra Antincendio'
        ]);

        $firstNames = [
            'Luca', 'Marco', 'Giulia', 'Francesca', 'Davide',
            'Alessia', 'Matteo', 'Sara', 'Stefano', 'Martina',
            'Giorgio', 'Valentina', 'Fabio', 'Elena', 'Simone',
            'Ilaria', 'Andrea', 'Chiara', 'Riccardo', 'Laura'
        ];

        $lastNames = [
            'Rossi', 'Bianchi', 'Verdi', 'Russo', 'Ferrari',
            'Esposito', 'Romano', 'Colombo', 'Ricci', 'Marino',
            'Greco', 'Bruno', 'Gallo', 'Conti', 'De Luca',
            'Mancini', 'Costa', 'Barbieri', 'Moretti', 'Lombardi'
        ];

        for ($i = 0; $i < 20; $i++) {
            if ($i < 9) {
                $type = 1;
            } elseif ($i < 18) {
                $type = 2;
            } else {
                $type = 3;
            }

            $this->insert('{{%employee}}', [
                'first_name' => $firstNames[$i],
                'last_name' => $lastNames[$i],
                'type' => $type,
            ]);
        }

        $titles = [
            'Riunione di progetto', 'Formazione sicurezza',
            'Manutenzione mezzi',  'Sopralluogo cantiere',
            'Verifica materiali',  'Consegna attrezzature',
            'Incontro cliente',    'Pianificazione settimana',
            'Report mensile',       'Analisi rischi'
        ];

        for ($i = 0; $i < 10; $i++) {
            $startTimestamp = strtotime('+'. rand(0, 20) .' days +' . rand(8, 16) . ' hours');
            $endTimestamp   = $startTimestamp + rand(3600, 4 * 3600);

            $this->insert('{{%event}}', [
                'title'   => $titles[$i],
                'start'   => date('Y-m-d H:i:s', $startTimestamp),
                'end'     => date('Y-m-d H:i:s', $endTimestamp),
                'type'    => rand(1, 3),
                'color'   => '#779ECB',
                'display' => 'auto',
                'all_day' => false,
            ]);
        }

        for ($eventId = 1; $eventId <= 10; $eventId++) {
            $nResources = rand(1, 5);
            $employees = array_rand(range(1, 20), $nResources);

            if (!is_array($employees)) {
                $employees = [$employees];
            }

            foreach ($employees as $empIndex) {
                $empId = $empIndex + 1;
                $this->insert('{{%event_x_employee}}', [
                    'resource' => $empId,
                    'event'    => $eventId,
                ]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250521_135910_insert_default_records cannot be reverted.\n";

        return false;
    }
}
