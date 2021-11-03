<?php
/*
 * OutlayEntity Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date Jun 14, 2021
 */

namespace App\Entity;

use src\Entity\Entity;

class OutlayEntity extends Entity
{

    /**
     * Permet de générer le lien d'édition
     *
     * @return string
     */
    public function getUrlAdmin(string $id = null)
    {
        if($id == null){
            $url = '/admin/outlay/' . $this->id . '/edit';
        } else {
            $url = '/admin/outlay/' . $id . '/edit';
        }
        return $url;
    }

    public function getsign()
    {
        if ($this->signature == 1) {
            return '<span class="badge badge-light-info">Signé</span>';
        } else {
            return '<span class="badge badge-light-danger">A signer</span>';
        }
    }

    public function getLabelRefund()
    {
        if ($this->refund == null) {
            return '<span class="badge badge-light-warning">En attente</span>';
        } else {
            return '<span class="badge badge-light-success">Ok le '.date($this->refund).'</span>';
        }
    }
}