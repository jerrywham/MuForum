MuForum
=======

Fork of µForum (http://uforum.byethost5.com/)

Nouveautés
---
* Refonte totale du code

* Pagination (topics, messages, membres)

* Nouveau captcha dérivé de celui de Lion wiki que je trouve vraiment bien fait (simple et efficace)

* Obfuscation du nom des dossiers

* Séparation des dossiers de téléchargement des dossiers de messages et de données

* Simplification du javascript

* Utilisation des sessions pour la propagation de messages et éviter de soumettre plusieurs fois le même formulaire

* Ajout d'un fil d'ariane

* Recherche d'un membre dans la liste des membres

* Plusieurs autres corrections mineures que je vous laisse découvrir

Je me suis basé sur le code de µForum 0.9.6. J'ai repris l'essentiel des fonctions natives que j'ai réorganisées et corrigées.
Les objets sont plus lisibles. Les appels de propriétés sont facilité (plus d'index obscurs dans les tableaux). Le code a été rafraichi (plus de variables globales).

TODO
---

J'ai dans l'idée de créer des sous-catégories et d'ajouter quelques fonctionnalités encore manquantes :

* liens vers nouveaux messages,

* vers les différentes pages d'une conversation,

* flux rss,

* Revoir le css pour l'alléger encore,

* voir les messages d'un membre

Pour reprendre un célèbre slogan : la route est longue mais la voie est libre...
