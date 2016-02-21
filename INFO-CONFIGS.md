# INFOS CONFIGS - PROJET WORDPRESS

### Pour installer wordpress proprement

Pour installer proprement wordpress (permet l'accès écriture pour apache, création automatique du .htaccess par wordpress)  

```
$ cd /var/www/html/wordpress
$ sudo chown -R www-data wordpress
$ sudo chmod -R g+w wordpress
```

### Permalinks sous Apache / Ubuntu

Pour bénéficier les permalinks, importants pour le référencement :

activer le mod-rewrite d'Apache  
```
$ sudo a2enmod rewrite
```

Ouvrir le fichier de configuration d'Apache :
```
$ sudo gedit /etc/apache2/apache2.conf &
```

Modifier dans `<Directory /var/www/>` ou `<Directory /var/www/nom_du_repertoire_wordpress>` :

```
# ajouter juste "FollowSymLinks" aux "Options", si l'option n'est pas
# déjà sélectionnée
Options FollowSymLinks
AllowOverride FileInfo
```

Enfin redémarrer Apache pour prendre en compte les modifs
```
$ sudo /etc/init.d/apache2 restart
```


