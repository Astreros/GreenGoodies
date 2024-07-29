# GreenGoodies
Mission GreenGoodies OpenClassrooms

## Installation

### Composer
Installation des dépendances :
```bash
composer install
```

## Configuration

### Base de données 
Configurer l'accès à la base de données dans le fichier `.env`.

Exemple :
```dotenv
DATABASE_URL="mysql://root:Password123!@127.0.0.1:3306/greengoodies?serverVersion=10.4.28-MariaDB&charset=utf8mb4"
```

#### Créer la base de données
```bash
symfony console doctrine:database:create
```

#### Exécuter les migrations
```bash
symfony console doctrine:migrations:migrate -n
```

#### Charger les fixtures
```bash
symfony console doctrine:fixtures:load -n
```

### Lexik JWT
Création des clés privée et publique JWT. Configurer votre "passphrase" dans le fichier `.env`.
```dotenv
JWT_PASSPHRASE=PASSPHRASE!
```

#### Création de la clè privée
OpenSSL n'est pas nécessairement installé par défaut, l'utilisation de GitBash peut être une solution.
```bash
openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
```

#### Création de la publique
```bash
openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
```