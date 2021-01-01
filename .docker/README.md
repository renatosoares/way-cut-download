criar banco, migrate e import data.
```sh
docker-compose exec robox-postal-code-postgresql psql --username=postgres --echo-all --command="CREATE DATABASE qualocep"
docker-compose exec robox-postal-code-postgresql psql --username=postgres --echo-all -d qualocep -f /tmp/qualocep.sql
```
