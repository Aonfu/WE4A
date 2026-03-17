# installation et configuration du projet sur votre environnement de developement

## dependences requises :
- Git
- Docker Desktop
- assurez vous que la virtualisation est bien activée dans votre BIOS
### dependences supplémentaires pour Windows
- WSL2

## Sur Windows :
1. Exécutez la commande suivante dans PowerShell en tant qu'administrateur `wsl --install` et redémarrez votre ordinateur. Assurez vous que les fonctionnalités suivantes ont bien été installées(dans menu démarrer : fonctionnalités).
- Plateforme de virtualisation Windows (Virtual Machine Platform)
- Sous système linux pour Windows (Windows Subsystem for Linux) (WLS)
2. installer une distribution linux en utilisant la commande `wsl --install [Distro]` où `[Distro]` en est le nom (debian est recommandé). Pour voir toute les distributions disponibles entrez : `wsl --list --online`.
3. configurez la distribution linux que vous avez choisi avec un utilisateur et mettez la distribution à jour.

### Installation de Docker Desktop
1. (WINDOWS) Téléchargez et installez le logiciel depuis le site officiel [Docker website](https://www.docker.com/) ou utilisez winget (Windows Package Manager) avec PowerShell `winget install Docker.DockerDesktop`.
1. (AUTRE) Téléchargez et installez le logiciel depuis le site officiel [Docker website](https://www.docker.com/) ou utilisez votre gestionnaire de packet.

2. (WINDOWS) Sur l'écran d'acceuil dans les paramètres, assurez vous de selectionner `WSL 2` plutot que `Hyper-V`
3. quand vous démarrez Docker pour la première fois, acceptez les conditions d'utilisation. Vous n'avez pas besoin d'utiliser un compte (appuyez sur skip), travailler en local est suffisant.
4. Docker s'initialisera. Cela peut prendre un moment lors du premier lancement.

#### installation de PhpStorm
1. allez sur [Jetbrains PhpStorm website](https://www.jetbrains.com/fr-fr/phpstorm/) et télécharger l'installeur ou avec votre gestionnaire de packet.
2. Installez PhpStorm, puis connectez-vous à votre compte pour activer votre licence.

#### Installation de Git
Trivial

### Configuration du projet
1. Clonez le repo: `git clone https://github.com/TheRefraction/WE4_Project.git` 
et ouvrez le dans votre éditeur préféré
2. Copié le fichier de variable d'environnement `.env.example` sur un `.env` que vous créez et remplissez le avec vos propres valeurs
> [!CAUTION]
> ce fichier est strictement personnel et ne doit **JAMAIS** être publié sur GitHub
3. Démarrez les conteneurs en exécutant la commande suivante depuis le dossier du projet
`docker compose up --build -d`. Cela peut prendre un certain temps la première fois.
> [!TIP]
> pour les prochaines fois `--build` ne sera pas necessaire, il sert à utiliser le fichier Dockefile pour installer les dépendances php. Pour les prochains lancement préférez `docker compose up -d`
> [!TIP]
> Une fois la première commande lancée, vos conteneurs serons démarables depuis votre application Docker Desktop. Vous n'êtes donc pas obligé d'utiliser les commandes docker
4. Une fois fini ouvrez http://localhost:8080 pour voir le site
> [!TIP]
> le lien pour phpmyadmin et votre site web est aussi accessible et cliquable depuis votre application Docker

#### Configuration de PhpStorm (facultatif)
> [!IMPORTANT]
> Il faut avoir configurer le projet avec docker avant de configurer l'interpreteur
##### Configurez l'interpréteur PHP
1. allez dans `Settings > PHP`
2. Puis dans `CLI Interpreter`, cliquez sur `...`
3. Cliquez `+ > From Docker, ...`
4. Sélectionnez `Docker Compose`
5. mettez:
   - Server: Docker
   - Configuration files: `./compose.yml`
   - Service: `php`
6. Appliquez et Confirmez

##### Database Link Configuration
1. Dans le panneau de droite, cliquez sur l'icône `Database`
2. Cliquez sur l'icone `+` et selectionnez `MySQL`
3. mettez host à `localhost`, port à `3306`, et fournissez les données de login ainsi que le nom de la base de données
4. Installez les drivers si proposés
5. Testez la connexion et sauvegardez

adapté du guide de Corentin Hautefaye : https://github.com/TheRefraction/WE4_Project/blob/master/README.md
