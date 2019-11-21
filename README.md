CrosHAL permet : 
. de vérifier la validité des métadonnées des notices saisies dans HAL avec celles présentes dans CrossRef, Pubmed et ISTEX,
. de compléter et corriger les auteurs, 
. de déposer le texte intégral des articles.

1. Sous cet espace GIT, il faut juste récupérer le dossier CrossHAL; le reste constitue une archive de la progression.
2. Le fichier par défaut est croshal.php.
3. Si le serveur hébergeant l'application n'a pas accès au CAS du CCSD, il faut renseigner le fichier _connexion.php avec le compte gestionnaire HAL.
4. Toujours en phase de développement, l'accès à l'application est restreint à quelques adresses IP; il suffit de supprimer les 6 lignes sous //Restriction IP pour lever cette limitation.
5. L'application est testée et développée sous PHP 7.1.9.