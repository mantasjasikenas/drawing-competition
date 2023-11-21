echo "Launching drawing competion container"
docker logout
git clone https://github.com/mantasjasikenas/drawing-competition.git
docker-compose up --build --force-recreate --no-deps -d