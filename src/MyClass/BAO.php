<?php
/*
 * BAO Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date October 15, 2021
 */
namespace src\MyClass;

use stdClass;

class BAO
{

    /**
     * Ouvre et lis le fichier Pax
     * @param $file_bao
     * @return false|resource
     */
    public function view($file_bao){
        return $this->open_bao_file($file_bao);
    }

    /**
     * Génère la valeur depuis les balise [][/]
     * @param $file_bao
     * @param $patern
     * @return array|mixed|string|string[]
     */
    public function parse_unique($file_bao, $patern){

        $pax = $this->open_bao_file($file_bao);
        preg_match('`\['.$patern.'](.+)\[/'.$patern.']`', $pax, $value);
        $replace_value = str_replace(' : ', '', $value);
        return $replace_value[1];
    }

    /**
     * @param string $file_bao
     * @return mixed
     */
    public function parser_array(string $file_bao){
        $pax = $this->open_bao_file($file_bao);
        preg_match('`\[INSTALL_DATE](.+)\[/INSTALL_DATE]`', $pax, $r_install_date);
        preg_match('`\[PC_NAME](.+)\[/PC_NAME]`', $pax, $r_pc_name);
        preg_match('`\[BIOS](.+)\[/BIOS]`', $pax, $r_bios);
        preg_match('`\[MODEL](.+)\[/MODEL]`', $pax, $r_manufacturer);
        preg_match('`\[RAM](.+)\[/RAM]`', $pax, $r_ram);
        preg_match('`\[CPU](.+)\[/CPU]`', $pax, $r_cpu);
        preg_match('`\[SOCKET](.+)\[/SOCKET]`', $pax, $r_socket);
        preg_match('`\[MB](.+)\[/MB]`', $pax, $r_mb);
        preg_match('`\[GC](.+)\[/GC]`', $pax, $r_cg);
        preg_match('`\[SN](.+)\[/SN]`', $pax, $r_sn);
        preg_match('/\[HDD0\](.*?)\[\/HDD0\]/ism', $pax, $r_hdd0);
        preg_match('/\[HDD1\](.*?)\[\/HDD1\]/ism', $pax, $r_hdd1);
        preg_match('/\[HDD2\](.*?)\[\/HDD2\]/ism', $pax, $r_hdd2);
        preg_match('/\[HDD3\](.*?)\[\/HDD3\]/ism', $pax, $r_hdd3);
        if(empty($r_hdd1)){
            $r_hdd1 = '##';
            $r_hdd2 = '##';
            $r_hdd3 = '##';
        } elseif(empty($r_hdd2)){
            $r_hdd2 = '##';
            $r_hdd3 = '##';
        } elseif(empty($r_hdd3)){
            $r_hdd3 = '##';
        }
        $array = [
            "r_install_date" => str_replace(' : ', '', $r_install_date[1]),
            "r_pc_name" => str_replace(' : ', '', $r_pc_name[1]),
            "r_bios" => str_replace(' : ', '', $r_bios[1]),
            "r_manufacturer" => str_replace(' : ', '', $r_manufacturer[1]),
            "r_ram" => str_replace(' : ', '', $r_ram[1]),
            "r_cpu" => str_replace(' : ', '', $r_cpu[1]),
            "r_socket" => str_replace(' : ', '', $r_socket[1]),
            "r_mb" => str_replace(' : ', '', $r_mb[1]),
            "r_cg" => str_replace(' : ', '', $r_cg[1]),
            "r_sn" => str_replace(' : ', '', $r_sn[1]),
            "r_hdd0" => preg_replace('~[[:cntrl:]]~', '', $r_hdd0[1]),
            "r_hdd1" => preg_replace('~[[:cntrl:]]~', '', $r_hdd1[1]),
            "r_hdd2" => preg_replace('~[[:cntrl:]]~', '', $r_hdd2[1]),
            "r_hdd3" => preg_replace('~[[:cntrl:]]~', '', $r_hdd3[1])

        ];
        $object = new stdClass();
        foreach ($array as $key => $value)
        {
            $object->$key = $value;
        }
        return $object;

    }

    /**
     * Vérifie si le fichier existe
     * @param int $id
     * @param string $filename
     * @param string $error
     * @return string|void
     */
    public function checkPath(int $id, string $filename, string $error){
        $path = ROOT . 'documents/bao/equipments/' . $id. '/' . $filename;
        if(file_exists($path)){
            return $path;
        } else {
            return false;
        }
    }


    /**
     * Open .bao
     * @param string $file_bao
     * @return false|resource
     */
    private function open_bao_file(string $file_bao)
    {
            $open_file = ROOT . 'documents/bao/equipments/' .$file_bao;
            $file = fopen($open_file, 'r');
            $contents = fread($file, filesize($open_file));
            fclose($file);
            return $contents;
    }

    /**
     * Redirection header
     * @param type $url
     */
    protected function redirect($url){
        header("Location: $url");
        exit();
    }
}