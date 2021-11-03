<?php
/*
 * ProaxiveUpdateModel Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date Jun 03, 2021
 */

namespace App\Model;


use src\Model\Model;

class ProaxiveUpdateModel extends Model
{

    /**
     * Permet de mettre à jour la table Interventions
     * Ajout du champ note_tech
     * @return mixed
     */
    public function addFieldNoteTechFromIntervention(){
        return $this->query("
            ALTER TABLE pe1x_interventions
            ADD note_tech LONGTEXT NULL");
    }
}