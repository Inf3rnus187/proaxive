<?php


use Phinx\Seed\AbstractSeed;

class StatusSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $data = [
            [
                'title' => 'En traitement',
                'not_delete' => true,
                'bgcolor' => 'rgba(0,207,232,0.12)',
                'txtcolor' => '#00CFE8'
            ],
            [
                'title' => 'En attente',
                'not_delete' => true,
                'bgcolor' => 'rgba(0,207,232,0.12)',
                'txtcolor' => '#00CFE8'
            ],
            [
                'title' => 'Livraison en cours',
                'not_delete' => true,
                'bgcolor' => 'rgba(0,207,232,0.12)',
                'txtcolor' => '#00CFE8'
            ],
            [
                'title' => 'En attente d\'expédition',
                'not_delete' => true,
                'bgcolor' => 'rgba(0,207,232,0.12)',
                'txtcolor' => '#00CFE8'
            ],
            [
                'title' => 'Transféré(e)',
                'not_delete' => true,
                'bgcolor' => 'rgba(255,159,67,0.12)',
                'txtcolor' => '#FF9F43'
            ],
            [
                'title' => 'Assemblage',
                'not_delete' => true,
                'bgcolor' => 'rgba(0,207,232,0.12)',
                'txtcolor' => '#00CFE8'
            ],
            [
                'title' => 'Annulé(e)',
                'not_delete' => true,
                'bgcolor' => 'rgba(234,84,85,0.12)',
                'txtcolor' => '#EA5455'
            ],
            [
                'title' => 'Terminé(e)',
                'not_delete' => true,
                'bgcolor' => 'rgba(46, 204, 113, 0.2)',
                'txtcolor' => '#2ecc71'
            ],
            [
                'title' => 'En attente de retour',
                'not_delete' => true,
                'bgcolor' => 'rgba(0,207,232,0.12)',
                'txtcolor' => '#00CFE8'
            ]
        ];
        $this->insert('status', $data);
    }
}
