# OnlineShop

How to run app using Docker: \
1.Clone this repo \
2.Go inside <kbd>.docker</kbd> directory \
3.Run <kbd>docker compose up -d</kbd> to start containers \
4.Go inside <kbd>shop-php</kbd> container using <kbd>docker exec -it shop-php bash</kbd> or use terminal in Docker Desktop \
5.Run <kbd>composer install</kbd> to install necessary dependencies \
6.Run <kbd>php bin/console doctrine:migrations:migrate</kbd> to create database structure 

Now you can open app at <kbd>http://localhost:8080</kbd>
