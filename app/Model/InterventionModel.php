<?php
/*
 * InterventionModel Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date May 21, 2021
 */
namespace App\Model;

use src\Model\Model;

/**
 * Description of InterventionTable
 *
 * @author SelMaK
 */
class InterventionModel extends Model{
    
    protected $model = 'pe1x_interventions'; // Générer un nom de table différent

    /**
     * @return mixed
     */
    public function allWithPaginator($value){

        return $this->query("SELECT
        i.id as idi, i.title as tinter, received, number, details, pmad, status_id, equipment_id, i.auser_id, i.client_id as iuser_id, i.created_at as icreated, closed,i.updated_at as iupdated,back_home,inter_type,i.auser_id i_auser_id,author_name,
        c.fullname as c_fullname,
        a.pseudo as apseudo,
        bgcolor, txtcolor, s.title as tstatus,
        e.name as tcomputer, e.auser_id e_auser_id
        FROM " .$this->model." as i
        LEFT JOIN pe1x_equipments as e ON i.equipment_id = e.id
        LEFT JOIN pe1x_clients as c ON i.client_id=c.id
        LEFT JOIN pe1x_ausers as a ON i.auser_id = a.id
        LEFT JOIN pe1x_status as s ON i.status_id=s.id
        ORDER BY i.created_at DESC "  .$value);

    }

    /**
    * All lines
    */
 
	public function all(){
		return $this->query("SELECT 
        i.id as idi, i.title as tinter, received, number, details, status_id, equipment_id, i.client_id as iuser_id, i.created_at as icreated, closed,i.updated_at as iupdated,back_home,inter_type,
        c.fullname as c_fullname,
        a.pseudo as apseudo,
        bgcolor, txtcolor, s.title as tstatus,
        e.name as tcomputer
        FROM " .$this->model." as i
        LEFT JOIN pe1x_equipments as e ON i.equipment_id = e.id
        LEFT JOIN pe1x_clients as c ON i.client_id=c.id
        LEFT JOIN pe1x_ausers as a ON i.auser_id = a.id
        LEFT JOIN pe1x_status as s ON i.status_id=s.id
        ORDER BY i.created_at DESC");
	}

    /**
     * All in Progress
     * @return mixed
     */
    public function allInProgress(){
        return $this->query("SELECT
        i.id as idi, i.title, i.created_at as icreated, i.updated_at as iupdated, status_id, closed, number,
        s.title as tstatus, bgcolor, txtcolor,
        fullname
        FROM " .$this->model." as i
        LEFT JOIN pe1x_status as s ON i.status_id = s.id
        LEFT JOIN pe1x_clients as c ON i.client_id = c.id
        WHERE closed IS NULL OR closed = 0
        ORDER BY i.created_at DESC");
    }

    /**
     * All in Progress
     * @return mixed
     */
    public function selectWithClientInProgress(int $limit){
        return $this->query("SELECT
        i.id as idi, i.title, i.created_at as icreated, i.updated_at as iupdated, status_id, closed, number,
        s.title as tstatus, bgcolor, txtcolor,
        fullname
        FROM " .$this->model." as i
        LEFT JOIN pe1x_status as s ON i.status_id = s.id
        LEFT JOIN pe1x_clients as c ON i.client_id = c.id
        WHERE closed IS NULL OR closed = 0
        ORDER BY i.created_at DESC LIMIT 0,".$limit);
    }

    /**
     * All in Progress
     * @return mixed
     */
    public function selectWithClientInProgressByAuser(int $limit, int $id){
        return $this->query("SELECT
        i.id as idi, i.title, i.created_at as icreated, i.updated_at as iupdated, status_id, closed, number,
        s.title as tstatus, bgcolor, txtcolor,
        fullname
        FROM " .$this->model." as i
        LEFT JOIN pe1x_status as s ON i.status_id = s.id
        LEFT JOIN pe1x_clients as c ON i.client_id = c.id
        WHERE auser_id = $id AND closed IS NULL OR closed = 0
        ORDER BY i.created_at DESC LIMIT 0,".$limit);
    }

    /**
     * All in Close
     * @return mixed
     */
    public function selectWithClientInCloseByAuser(int $limit, int $id){
        return $this->query("SELECT
        i.id as idi, i.title, i.created_at as icreated, i.updated_at as iupdated, status_id, closed, number,
        s.title as tstatus, bgcolor, txtcolor,
        fullname
        FROM " .$this->model." as i
        LEFT JOIN pe1x_status as s ON i.status_id = s.id
        LEFT JOIN pe1x_clients as c ON i.client_id = c.id
        WHERE auser_id = $id AND closed = 1
        ORDER BY i.created_at DESC LIMIT 0,".$limit);
    }

    /**
     * All in Close
     * @return mixed
     */
    public function allInClose(){
        return $this->query("SELECT
        i.id as idi, i.title, i.created_at as icreated, i.updated_at as iupdated, status_id, closed, number,
        s.title as tstatus, bgcolor, txtcolor,
        fullname
        FROM " .$this->model." as i
        LEFT JOIN pe1x_status as s ON i.status_id = s.id
        LEFT JOIN pe1x_clients as c ON i.client_id = c.id
        WHERE closed IS NOT NULL OR closed = 1
        ORDER BY i.created_at DESC");
    }

    /**
     * All in Close
     * @return mixed
     */
    public function selectWithClientInClose($limit){
        return $this->query("SELECT
        i.id as idi, i.title, i.created_at as icreated, i.updated_at as iupdated, status_id, closed, number,
        s.title as tstatus, bgcolor, txtcolor,
        fullname
        FROM " .$this->model." as i
        LEFT JOIN pe1x_status as s ON i.status_id = s.id
        LEFT JOIN pe1x_clients as c ON i.client_id = c.id
        WHERE closed IS NOT NULL OR closed = 1
        ORDER BY i.created_at DESC LIMIT 0,".$limit);
    }

    /**
    *
    */

    public function lastByUser($id, $idParent = false)
    {
        return $this->query("
        SELECT *
        FROM ".$this->model." as i
        LEFT JOIN pe1x_clients as c ON i.client_id=c.id
        LEFT JOIN pe1x_status as s ON i.status_id=s.id
        WHERE c.id = ?", [$id], false);
    }
    
    /**
    * Dernier ID enregistré
    */

    public function lastInter($id, $idParent = false)
    {
        return $this->query("
        SELECT *
        FROM ".$this->model." as i
        LEFT JOIN pe1x_clients as c ON i.client_id=c.id
        LEFT JOIN pe1x_status as s ON i.status_id=s.id
        WHERE c.id = ?
        ORDER BY i.id DESC LIMIT 1", [$id], true);
    }

    /**
     *
     * @param $number
     * @param bool $idParent
     * @return array
     */

    public function findByNumber($number, $idParent = false)
    {
        return $this->query("SELECT *
            FROM ".$this->model."
            WHERE number = ?", [$number], true);
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function findByKey($key)
    {
        return $this->query("SELECT *
            FROM ".$this->model."
            WHERE $key = ?", [$value], true);
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function findByKeyAndValue($key, $value)
    {
        return $this->query("SELECT *
            FROM ".$this->model."
            WHERE $key = ?", [$value], true);
    }

    /**
     * @param $foreign
     * @return mixed
     */
    public function byclient($foreign)
    {
        return $this->query("SELECT
        i.id as idi, i.title as ititle, i.created_at as idate, number, name, inworkshop,
        e.id, e.name ename,
        ce.id ce_id, ce.icon,
        s.title as stitle, bgcolor, txtcolor
        FROM ".$this->model." as i
        INNER JOIN pe1x_equipments as e ON i.equipment_id = e.id
        INNER JOIN pe1x_category_equipments as ce ON e.category_equipment_id = ce.id
        INNER JOIN pe1x_status as s ON i.status_id = s.id
        WHERE i.client_id = ?", [$foreign]);
    }

    /**
     * @param $foreign
     * @return mixed
     */
    public function allByUserWithEquipmentStatusAndCategory($foreign)
    {
        return $this->query("SELECT
        i.id as idi, i.title as ititle, i.created_at as idate, number, name, inworkshop,
        e.id, e.name ename,
        ce.id ce_id, ce.icon,
        s.title as stitle, bgcolor, txtcolor
        FROM ".$this->model." as i
        INNER JOIN pe1x_equipments as e ON i.equipment_id = e.id
        INNER JOIN pe1x_category_equipments as ce ON e.category_equipment_id = ce.id
        INNER JOIN pe1x_status as s ON i.status_id = s.id
        WHERE i.auser_id = ?", [$foreign]);
    }

    /**
     * @param $foreign
     * @return mixed
     */
    public function byclientList($foreign)
    {
        return $this->query("SELECT
        i.id as idi, i.title as ititle, i.created_at as idate, number,
        name, inworkshop,
        s.title as stitle, bgcolor, txtcolor,
        a.pseudo as apseudo, a.created_at as adate
        FROM ".$this->model." as i
        INNER JOIN pe1x_equipments as e ON i.equipment_id = e.id
        INNER JOIN pe1x_status as s ON i.status_id = s.id
        INNER JOIN pe1x_clients as c ON i.client_id = c.id
        WHERE i.equipment_id = ?", [$foreign]);
    }

    /**
     * Recherche une intervention
     * @param string $word
     * @return mixed
     */

    public function search(string $word)
    {
        return $this->query("SELECT *
            FROM " .$this->model . "
            WHERE number
            LIKE ?", [$word], true);
    }

    /**
     * Permet de rechercher le numéro d'intervention
     *
     * @param $number
     * @return mixed
     */
    public function scanNumber($number)
    {
        return $this->query("SELECT id FROM " . $this->model . " WHERE number = ?", [$number]);
    }

    /**
     * @return int
     */
    public function countIntervention(){
        $rows = $this->query('SELECT id FROM '. $this->model);
        $total = count($rows);
        return $total;
    }

}
