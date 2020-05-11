
var _xmlHttp = null; //l'objet xmlHttpRequest utilisé pour contacter le serveur
var _dataList = null;
var _inputField=null;
var _xmlHttp2 = null; //l'objet xmlHttpRequest utilisé pour contacter le serveur
var _inputNombreResultat=null;

var _oldInputFieldValue=""; // valeur précédente du champ texte
var _currentInputFieldValue=""; // valeur actuelle du champ texte

// Fonction executee au moment du chargement de la page
window.onload = function(){
  // recherche l'element HTMl defini par ID loc-datalist et recupere un "pointeur" sur l'element
    _dataList = document.getElementById('loc-datalist');
    _inputField=document.getElementById('loc-input');
    _inputNombreResultat=document.getElementById('count-input');
    mainLoop();
}

// tourne en permanence pour suggérer suite à un changement du champ texte
function mainLoop(){

  // Verification si la donnée dans le champ input a été modifiée
  _currentInputFieldValue = _inputField.value;
  if(_oldInputFieldValue!=_currentInputFieldValue){
      // Equivalent à htmlentities() en PHP
      var valeur=encodeURIComponent(_currentInputFieldValue);

      callSuggestions(valeur) // appel AJAX distant recherche de suggestions
      countResultat(valeur) // appel AJAX distant comptage des suggestions

      // Astuce: Force la mise à jour du champ de saisie
      _inputField.blur();   //quitter le champ de saisie (clic ailleurs sur la page)
      _inputField.focus();  // retourner sur le champ de saisie
  }
  _oldInputFieldValue=_currentInputFieldValue;

  setTimeout("mainLoop()",200); // la fonction mainLoop() se redéclenchera dans 200 ms
  return true
}

// Recherche sur l'API des sugestions correspondant à la saisie dans le champ input
function callSuggestions(valeur){
  // Annule une precedente requete si elle n'avait pas ete terminee
  if(_xmlHttp&&_xmlHttp.readyState!=0){
    _xmlHttp.abort()
  }

  // declare un nouvel objet
  _xmlHttp= new XMLHttpRequest();

  if(_xmlHttp){
//    console.log('appel='+valeur);
    _xmlHttp.open("GET","api.php?search="+valeur+"&limit=30",true);    //URL de l'API
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

/*
La recherche sans les accents ne fonctionnne pas avec le fonctionnement de base du champ input.
Même si les données sont présentes dans le datalist, elles ne sont pas affichées, car le champ input recherche une
valeur exacte. Il est possible de mettre la donnée sans accent dans le contenu du tag ou dans l'attribut label.
Dans ce cas, la donnée affichée sera cette valeur et permet la saisie, mais au final c'est la donnée value qui sera envoyée au serveur.
 */
// Mise en place des données recues dans la page
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
//      console.log(item);
      option.value = item;
      // Add the <option> element to the <datalist>.
      _dataList.appendChild(option);
  });

}

// Compte le nombre de résultats corresondant à notre recherche
function countResultat(valeur){
  // Annule une precedente requete si elle n'avait pas ete terminee
  if(_xmlHttp2&&_xmlHttp2.readyState!=0){
    _xmlHttp2.abort()
  }

  // declare un nouvel objet
  _xmlHttp2= new XMLHttpRequest();

  if(_xmlHttp2){
    //appel à l'url distante
    //    console.log('appel='+valeur);
    _xmlHttp2.open("GET","api.php?search="+valeur+"&count=1",true);    //URL de l'API
    // gestionnaire d'evenement pour readystate
    // Fonction anonyme de callback quand readystate change de valeur
    _xmlHttp2.onreadystatechange=function() {
      if(_xmlHttp2.readyState===4 && _xmlHttp2.status === 200) {
          // readystate=4 -> la requete est terminee
          // Status = 200 code HTTP 200 la requete a abouti

          // Decodage resultat json et stockage dans un tableau javascript
          var nombre_resultat = JSON.parse(_xmlHttp2.responseText);
          _inputNombreResultat.value = nombre_resultat;

      }
    };
    // envoi de la requête
    _xmlHttp2.send(null)
  }
}
