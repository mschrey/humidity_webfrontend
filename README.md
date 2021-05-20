# humidity_webfrontend

This is the Web Frontend of the Humidity Project, which can present the temperature and humidity data in a somewhat nice fashion. It works in combination with [humidity_server](https://github.com/mschrey/humidity_server) and [humidity_sensor](https://github.com/mschrey/humidity_sensor)

It requires Apache2, MySQL and PHP to be installed. Furthermore, it uses [JpGraph](https://jpgraph.net/) to generate nice-looking gauges. 

## Installation ##
This howto assumes Apache2, MySQL and PHP to be installed and working. 

Clone the repo, then untar the jpgraph-4.3.2.tar.gz archive using 
``tar -xzvf jpgraph-4.3.2.tar.gz``

Then create a symbolic link using
``ln -s ./jpgraph-4.3.2 jpgraph``

Adjust php variables $servername, $username, $password and $dbname ([humidity_inc.php](https://github.com/mschrey/humidity_webfrontend/blob/21160d7df1603d1075cd1e4227478f9c7bbd6efd/humidity_inc.php#L32), lines 32ff) to your needs. While the user and password specified here needs to exist within MySQL, it only needs SELECT permissions. (This is in contrast to "user" and "password" from [database.c](https://github.com/mschrey/humidity_server/blob/main/database.c), who needs INSERT permissions. 

Furthermore, you might want to adjust the mapping of Sensor node ID to location name in variable $id2room. 
