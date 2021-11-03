<?php
/*
 * InterventionEntity Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date May 21, 2021
 */
namespace App\Entity;
use src\Entity\Entity;
use src\MyClass\Session;

/**
 * Description of InterventionEntity
 *
 * @author SelMaK
 */
class InterventionEntity extends Entity{


    /**
     * Retourne le lien d'édition d'une intervention
     * Dashboard
     * @return string
     */
    public function getUrlAdmin(string $id = null)
    {
        if($id == null){
            $url = '/admin/intervention/' . $this->id;
        } else {
            $url = '/admin/intervention/' . $id;
        }
        return $url;

    }

    /**
     * Retourne le lien de la vue d'intervention
     * Public
     * @return string
     */
    public function getUrl()
    {
        return '/i/'.$this->number_link;
    }
    
    /**
     * Genere le statut
     * @return
     */
    public function getStatus()
    {
        if ($this->closed == 1) {
            return '<span class="badge badge-light-success">Cloturé le '.$this->close_date.'</span>';
        } else {
            return '<span class="badge" style="background-color:#'.$this->bgcolor.'">'.$this->tstatus.'</span>';
        }
    }

    /**
     * Génère l'état de l'intervention  sans texte (circle) (cloture)
     * @return string
     */
    public function getEtat()
    {
        $session = Session::getSessionInstance()->read('auth');
        if ($this->closed == 1) {
            $html = '<div class="d-inline-block vertical-middle"><span class="label badge-light-success rounded-circle d-block" style="width:20px;height:20px;"></span></div>';
        } else {
            $html =  '<div class="d-inline-block vertical-middle"><span class="label badge-light-danger rounded-circle d-block" style="width:20px;height:20px;"></span></div>';
        }
        // Affiche l'icon lock si ne auser_id n'est pas égale à session ID
        if($this->auser_id != $session->id AND $session->id != 1){
            $html .= '<div class="d-inline-block mg-l-05 text-danger"><i class="ri-lock-fill"></i></div>';
        }
        return $html;
    }

    /**
     * Génère l'état de l'intervention avec texte (cloture)
     * @return string
     */
    public function getEtatTxt()
    {
        if ($this->closed == 1) {
            return '<span class="label badge-light-success">Cloturée</span>';
        } else {
            return '<span class="label badge-light-danger">Non cloturée</span>';
        }
    }
}