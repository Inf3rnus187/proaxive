<?php
/*
 * OutlayModel Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date Jun 14, 2021
 */

namespace App\Model;

use src\Model\Model;

class OutlayModel extends Model
{
    protected $model = 'pe1x_outlay'; // Générer un nom de table différent

    /**
     * All lines
     */

    public function allWithStatusAndClient(){
        return $this->query("SELECT 
        o.id as o_id, number,amount,refund,payment,ref_propal,toclosed,seller,signature, o.created_at o_created_at,
        c.id as c_id, c.fullname as c_fullname,
        bgcolor, s.title as tstatus,
        u.id  u_id, u.fullname u_fullname
        FROM " .$this->model." as o
        LEFT JOIN pe1x_clients as c ON o.client_id=c.id
        LEFT JOIN pe1x_status as s ON o.status_id=s.id
        LEFT JOIN pe1x_ausers as u ON o.auser_id=u.id
        ORDER BY o.created_at DESC");
    }

    /**
     * Permet de récupérer tous les débours
     *
     * @return mixed
     */
    public function exportWithClientAndStatus(){

        return $this->queryAssoc("
            SELECT 
            o.id as o_id, number,amount,refund,payment,ref_propal,toclosed,seller,signature, o.created_at o_created_at,
            c.id as c_id, c.fullname as c_fullname,
            s.title as tstatus
            FROM " .$this->model." as o
            LEFT JOIN pe1x_clients as c ON o.client_id=c.id
            LEFT JOIN pe1x_status as s ON o.status_id=s.id
            ORDER BY o.id DESC", false);
    }

    /**
     * Permet de récupérer tous les débours d'un client
     *
     * @param $foreign
     * @return mixed
     */
    public function byclient($foreign)
    {
        return $this->query("SELECT 
        o.id as o_id, number,amount,refund,payment,ref_propal,toclosed,seller,signature, o.created_at o_created_at,
        c.id as c_id, c.fullname as c_fullname,
        bgcolor, s.title as tstatus
        FROM " .$this->model." as o
        LEFT JOIN pe1x_clients as c ON o.client_id=c.id
        LEFT JOIN pe1x_status as s ON o.status_id=s.id
        WHERE client_id = ?", [$foreign]);
    }
}