
var _xmlHttp = null; //l'objet xmlHttpRequest utilisé pour contacter le serveur
var _dataList = null;
var _inputField=null;

var _oldInputFieldValue=""; // valeur précédente du champ texte
var _currentInputFieldValue=""; // valeur actuelle du champ texte


window.onload = function(){
  // recherche l'element HTMl defini par ID loc-datalist et recupere un "pointeur" sur l'element
    _dataList = document.getElementById('loc-datalist');
    _inputField=document.getElementById('loc-input');
    mainLoop();
}

// tourne en permanence pour suggérer suite à un changement du champ texte
function mainLoop(){

  _currentInputFieldValue = _inputField.value;
  if(_oldInputFieldValue!=_currentInputFieldValue){
      // Equivalent à htmlentities() en PHP
      var valeur=encodeURIComponent(_currentInputFieldValue);

      // Appel Ajax
      callSuggestions(valeur) // appel distant

      // Astuce: Force la mise à jour du champ de saisie
      _inputField.blur();   //quitter le champ de saisie (clic ailleurs sur la page)
      _inputField.focus();  // retourner sur le champ de saisie
  }
  _oldInputFieldValue=_currentInputFieldValue;

  setTimeout("mainLoop()",200); // la fonction mainLoop() se redéclenchera dans 200 ms
  return true
}


function callSuggestions(valeur){
  // Annule une precedente requete si elle n'avait pas ete terminee
  if(_xmlHttp&&_xmlHttp.readyState!=0){
    _xmlHttp.abort()
  }

  // declare un nouvel objet
  _xmlHttp= new XMLHttpRequest();

  if(_xmlHttp){
    //appel à l'url distante
    console.log('appel='+valeur);
    _xmlHttp.open("GET","loc-json.php?search="+valeur,true);    //URL de l'API
    // gestionnaire d'evenement pour readystate
    // Fonction anonyme de callback quand readystate change de valeur
    _xmlHttp.onreadystatechange=function() {
      if(_xmlHttp.readyState===4&&_xmlHttp.status === 200) {
          // readystate=4 -> la requete est terminee
          // Status = 200 code HTTP 200 la requete a abouti

          // Decodage resultat json et stockage dans un tableau javascript
          var liste_villes = JSON.parse(_xmlHttp.responseText);
          metsEnPlace(liste_villes);
      }
    };
    // envoi de la requête
    _xmlHttp.send(null)
  }
}

function metsEnPlace(liste_villes) {

  // Supprimer les anciens elements dans la datalist
  // Modification du DOM (Document Objet Model) c'est la structure de donnees qui
  // Stocke la page web en fonctionnement modifiable en JS
  while(_dataList.childNodes.length>0) {
    _dataList.removeChild(_dataList.childNodes[0]);     // On supprime un element de la datalist
  }

  // Ajoute les elements dans datalist
  // Loop over the JSON array.
  // Fonction anonyme
  liste_villes.forEach(function (item) {
      // Create a new <option> element. Ajoute un element dans la datalist
      var option = document.createElement('option');
      // Set the value using the item in the JSON array.
      console.log(item);
      option.value = item;
      // Add the <option> element to the <datalist>.
      _dataList.appendChild(option);
  });

}
