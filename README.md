MuForum
=======

Fork of µForum (http://uforum.byethost5.com/)

Version actuelle : 2.0

ATTENTION : version multihread. Les anciennes versions (< 2.0) ne sont plus compatibles avec cette version et les versions futures.

Installation
---
Afin d'augmenter la sécurité de l'application, il est recommandé de la configurer (mais cette étape est optionnelle).
Pour cela, ouvrez le fichier index.php avec un éditeur de texte (Notepad++, Sublime Text ou autre).

	Pour définir une constante, il suffit de changer la chaine de caractères située après la virgule. Par exemple, si je voulais définir la constante TEST avec la valeur Valeur, je devrais écrire : define('TEST','Valeur');

Les lignes à modifier sont entre la ligne 23 et la ligne 30. Vous devez déterminer les valeurs de cinq constantes. 

	- define('LANG','fr');
	- define('STYLE_COLOR','default');
	- define('PROJECT','µForum');
	- define('SECURITY_SALT','DSKQJfmi879fdiznKSDJ56SD8734QRer980ZOIDQ');
	//Pensez à changer également le nom du dossier __captcha__ s'il existe déjà
	- define('CAPTCHA','captcha');

Placez le fichier index.php à la racine de votre serveur web (ou dans un sous dossier) à l'aide d'un client FTP.
A l'aide d'un navigateur web (Firefox, Opera, Safari, Internet Explorer...) rendez-vous à l'adresse racine de votre site (par exemple http://www.votresite.com ou http://www.votresite.com/sousdossier/) et lancez le script.

C'est tout. Enjoy :p

Nouveautés
---
* Refonte totale du code
* Version multithread
* Pagination (topics, messages, membres)
* Nouveau captcha dérivé de celui de Lion wiki que je trouve vraiment bien fait (simple et efficace)
* Obfuscation du nom des dossiers
* Séparation des dossiers de téléchargement des dossiers de messages et de données
* Simplification du javascript
* Utilisation des sessions pour la propagation de messages et éviter de soumettre plusieurs fois le même formulaire
* Ajout d'un fil d'ariane
* Recherche d'un membre dans la liste des membres
* Système de bannissement afin d'éviter le craquage des mots de passe par brute force (Merci SebSauvage)
* Erreur 404 si recherche d'une page autre que l'accueil ou les topics si l'on n'est pas connecté et que le forum est privé
* Nouveau thème et allègement du css
* Possibilité d'en créer de nouveaux facilement
* Variables dans le css (de la forme +MAVARIABLE+) permettant de changer rapidement certaines valeurs (couleur, bordure, fond...)
* Compression du css à l'affichage
* Plusieurs autres corrections mineures que je vous laisse découvrir

Je me suis basé sur le code de µForum 0.9.6. J'ai repris l'essentiel des fonctions natives que j'ai réorganisées et corrigées.
Les objets sont plus lisibles. Les appels de propriétés sont facilité (plus d'index obscurs dans les tableaux). Le code a été rafraîchi (plus de variables globales).

MISES A JOUR
---

Lors des mises à jour, vous avez deux possibilités :
* soit utiliser les boutons d'import/export en tant qu'administrateur
* soit faire une sauvegarde manuelle des dossiers data et upload, avant la mise à jour.

ATTENTION : les versions antérieures à la version 2 ne sont pas compatibles avec les versions 2 et supérieures.

TODO
---

- [x] ~~liens vers nouveaux messages~~,
- [x] ~~liens vers les différentes pages d'une conversation~~,
- [ ] flux rss,
- [x] ~~Revoir le css pour l'alléger encore,~~
- [ ] voir les messages d'un membre
- [ ] voir les messages sans réponse, quel que soit la catégorie
- [x] ~~correction des messages lus/membres~~
- [ ] améliorer le suivi des messages lus/non lus en n'utilisant plus les cookies mais le moment de connexion et un temps déterminé
- [ ] renouvellement mot de passe si oubli ou si désir d'en changer
- [x] ~~sous-rubriques~~
- [ ] gestion mots de passe à vérifier
- [x] ~~email dans lien image (pour le tableau des membres)~~
- [ ] interdire de poster à moins d'une minute d'intervalle (modif oui, new non)
- [x] ~~création de templates~~
- [x] ~~liste déroulante vers les différentes catégories~~
- [ ] liste des fichiers présents pour gestion admin
- [x] ~~En mode libre, prévoir un mode lecture seule (le topics sont visibles mais il faut être inscrit pour répondre ou créer un nouveau sujet)~~
- [ ] lien pour signaler un post à l'administrateur/aux modérateurs
- [ ] bannissement de membres par l'administrateur/les modérateurs
- [ ] règles de fonctionnement : ajouter la possibilité de les rédiger dans la partie administration
- [x] ~~nombre de réponses par topic (nbMsg - 1)~~
- [ ] supprimer la possibilité d'importer des fichiers autres que les avatars afin de conserver l'espace sur le disque et éviter les pb de sécurité liés

Pour reprendre un célèbre slogan : la route est longue mais la voie est libre...
