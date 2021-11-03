<?php


use Phinx\Seed\AbstractSeed;

class CategoryEquipmentSeeder extends AbstractSeed
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
                'title' => 'PC Portable',
                'slug' => 'laptop',
                'icon' => 'ri-macbook-line',
                'color' => 'dbdbdb'
            ],
            [
                'title' => 'PC de bureau',
                'slug' => 'desktop',
                'icon' => 'ri-computer-line',
                'color' => 'dbdbdb'
            ],
            [
                'title' => 'Imprimante',
                'slug' => 'printer',
                'icon' => 'ri-printer-line',
                'color' => 'dbdbdb'
            ],
            [
                'title' => 'Tablette/PC Tablette',
                'slug' => 'tablet',
                'icon' => 'ri-tablet-line',
                'color' => 'dbdbdb'
            ],
            [
                'title' => 'Smartphone',
                'slug' => 'smartphone',
                'icon' => 'ri-smartphone-line',
                'color' => 'dbdbdb'
            ],
            [
                'title' => 'Serveur',
                'slug' => 'server',
                'icon' => 'ri-server-line',
                'color' => 'dbdbdb'
            ]
        ];
        $this->insert('category_equipments', $data);
    }
}
