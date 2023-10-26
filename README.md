# Drawing Competition

## Folder Structure

```
├── docker-compose.yml
├── Dockerfile
├── dump
│   └── myDb.sql
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

#### Connect to the container named redis

```bash
docker-compose exec redis sh
```

Combined
```bash
docker-compose down -v --rmi all;  docker-compose up --build --force-recreate --no-deps -d
```