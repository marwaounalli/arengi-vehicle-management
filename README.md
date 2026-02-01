# Arengi – Vehicle Management (Symfony)

Test technique Symfony : application de gestion de voitures avec :
- une liste publique
- un dashboard administrateur
- création / édition / suppression
- pagination
- gestion conditionnelle du champ PTRA (véhicules utilitaires)

Le projet est dockerisé pour faciliter l’installation et la démonstration.

---

## 1. Prérequis

- Docker
- Docker Compose
- Make (optionnel mais recommandé)

---

## 2. Installation du projet

### 2.1 Cloner le dépôt

```bash
git clone https://github.com/marwaounalli/arengi-vehicle-management.git
cd arengi-vehicle-management
```

### 2.2 Démarrer les containers Docker

Avec Makefile :
```bash
make start
```
Sans Makefile :
```bash
docker compose up -d
```

### 2.3 Installer les dépendances PHP

Avec Makefile :
```bash
make composer-install
```
Sans Makefile :
```bash
docker compose exec php composer install
```
## 3. Base de données

### 3.1 Initialiser la base de données
Cette commande :

* supprime la base existante

* la recrée

* applique les migrations

* charge les fixtures

* crée un utilisateur admin, les identifiants par défault sont : admin@test.com / admin

```bash
make database-init
```

Sans Makefile :
```bash
docker compose exec php php bin/console doctrine:database:drop --force --if-exists
docker compose exec php php bin/console doctrine:database:create --if-not-exists
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
docker compose exec php php bin/console doctrine:fixtures:load --no-interaction
docker compose exec php php bin/console app:create-admin  --email=admin@test.com --password=admin
```

### 3.2 Charger uniquement les fixtures
```bash
make fixtures
```
Sans Makefile :
```bash
docker compose exec php php bin/console doctrine:fixtures:load --no-interaction
```

### 3.3 Crée un admin

```bash
make admin
```
Sans Makefile :
```bash
docker compose exec php php bin/console app:create-admin  --email=admin@test.com --password=admin
```
## 4. Accès à l’application

Apres l'installation de projet, l’application est accessible en local :

* Liste publique des voitures
http://localhost/cars

* Page de connexion
http://localhost/login

* Dashboard administrateur
http://localhost/cars/dashboard

Le port peut varier selon la configuration Docker.
