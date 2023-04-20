test-dev
========

Un stagiaire à créer le code contenu dans le fichier src/Controller/Home.php

Celui permet de récupérer des urls via un flux RSS ou un appel à l’API NewsApi. 
Celles ci sont filtrées (si contient une image) et dé doublonnées. 
Enfin, il faut récupérer une image sur chacune de ces pages.

Le lead dev n'est pas très satisfait du résultat, il va falloir améliorer le code.

Pratique : 
1. Revoir complètement la conception du code (découper le code afin de pouvoir ajouter de nouveaux flux simplement) 

Questions théoriques : 
1. Que mettriez-vous en place afin d'améliorer les temps de réponses du script
    =>  Mise en cache: Utilisez un système de mise en cache pour stocker les résultats des appels d'API et des flux RSS. Ainsi, les appels aux sources externes sont moins fréquents, ce qui réduit les temps de réponse.
    => Eventuellement des appels asynchrones si des appels a plusieurs API différentes pour ne pas couper l'exécution du code
    => la mise en place d'une pagination si le nombre de résultat affiché est trop nombreux 
    => et l'optimisation de la tailles des images récupérés
2. Comment aborderiez-vous le fait de rendre scalable le script (plusieurs milliers de sources et images)
