# Changement réalisé depuis la présentation

## 1) Page d'identification
On vérifie si l'utilisateur à son compte actif avant de
le connecter.

## 2) Communication entre les copropriétaires
- Possibilité d'éditer une conversation.
Tout ce qui a été demandé à été fait.

## 3) Gestion des charges
- Correction du bug lors de l'ajout de charge
- Tout les utilisateurs de la copropriété sont maintenant 
sélectionné par défaut.
- Si l'on modifie le nombre de personne lié à cette charge
cela supprime les anciens payement et en crée de nouveau.
tout ce qui a été demandé à été fait.
- Seul le manager et l'admin peuvent voir les charges expirées.

## 4) Gestion de la liste des contrats
- Ajout d'une page de détail.
- Il est aussi possible d'éditer un contrat.
 tout ce qui a été demandé à été fait.

## 5) Création d'un projet d'évolution
- On peut maintenant attacher plusieurs sondage à un projet.
- Ajout de la journalisation dans le projet avec ajout de pièce jointe.

## 6) Appel de fond
- Correction du bug d'affichage du sondage
- Il est maintenant possible de réaliser un appel de fond.

## 7) Gestion des versements
Aucun changement, tout ce qui a été demandé à été fait.

## 8) Gestion des réunions
- Il est maintenant possible d'organiser des réunions.

## 9) Page d'accueil
Aucun changement, tout ce qui a été demandé à été fait.

## 10) Amélioration des notifications par mail
- Les notifications sont envoyés par mail à 23h30 par 
un cron qui s'occupe d'éxécuter les commandes correspondantes.
Les valeurs sont stockés dans mon crontab.

## 11) Interface d'administration et gestion des rôles
- Un compte n'est pas utilisable tant que l'utilisateur n'a
pas initialisé son mot de passe. Il doit initialiser son compte
grâce au mail qu'il a reçu. S'il tente de se connecter son 
compte ne sera pas reconnu.
- Utilisation de UserCheckerInterface

## 12) Gestion de la qualité
- J'ai finalement réussi à utiliser OPcache pour plus de rapidité
- Je n'ai pas réussi à résoudre mon problème de test unitaire
alors je n'ai pas poursuivi dans la réalisation de ce type de test.

## Autres changements

- Ajout de mailcatcher à docker
- Possibilité d'ajouter une copropriété depuis l'admin
- Ajout de notification lors d'action dans le projet
- Uniformisation des dates et du formatage des nombres dans les paramètres twig
- Correction de bug dans la globalité du projet
- Possibilité de voir les notifications de la journée en
 cliquant sur la cloche
- La commande pour les charges prend en argument optionnel l'id 
de la propriété et envoie ensuite un mail au manager de cette copropriété
- En accédant à 'http://localhost:8000/account' l'utilisateur
peut modifier ses données