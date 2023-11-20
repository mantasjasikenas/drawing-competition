# Drawing Competition

## Folder Structure

```
├── docker-compose.yml
├── Dockerfile
├── dump
│   └── db.sql
├── sessions
└── www
    └── index.php
```

## Launch application

#### Build and run the containers

```bash
docker-compose up --build --force-recreate --no-deps -d
```

#### Stop the containers and remove volumes (--rmi all to remove images)

```bash
docker-compose down -v --rmi all
```

#### Connect to the container named db

```bash
docker-compose exec db sh
```

#### Launch the application on VM

```bash
docker logout
docker-compose up --build --force-recreate --no-deps -d

# stops and starts the containers
docker-compose stop
docker-compose start
```
