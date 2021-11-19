/**
 * Recherche un client
 * @param str
 */
function searchClient(str){
    if (str.length == 0){ //exit function if nothing has been typed in the textbox
        document.getElementById("txtName").innerHTML=""; //clear previous results
        return;
    }
    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    } else {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function(){
        if(xmlhttp.readyState == 4 && xmlhttp.status == 200){
            document.getElementById("txtName").innerHTML=xmlhttp.responseText;
        }
    }
    xmlhttp.open("GET","/admin/client/search/"+str,true);
    xmlhttp.send();
}

/**
 * Recherche les interventions par numéro
 * @param str
 */
function searchIntervention(str){
    if (str.length == 0){ //exit function if nothing has been typed in the textbox
        document.getElementById("txtName").innerHTML=""; //clear previous results
        return;
    }
    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    } else {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function(){
        if(xmlhttp.readyState == 4 && xmlhttp.status == 200){
            document.getElementById("txtName").innerHTML=xmlhttp.responseText;
        }
    }
    xmlhttp.open("GET","/admin/intervention/search/"+str,true);
    xmlhttp.send();
}

/**
 * Recherche les équipements par numéro
 * @param str
 */
function searchEquipment(str){
    if (str.length == 0){ //exit function if nothing has been typed in the textbox
        document.getElementById("txtName").innerHTML=""; //clear previous results
        return;
    }
    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    } else {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function(){
        if(xmlhttp.readyState == 4 && xmlhttp.status == 200){
            document.getElementById("txtName").innerHTML=xmlhttp.responseText;
        }
    }
    xmlhttp.open("GET","/admin/equipment/search/"+str,true);
    xmlhttp.send();
}