# wake-over-internet
Magic packet sender over internet (this works only over internet) from a docker container, triggerable with a webhook.
Example:
- http://www.yourdomain.com:1337/wol.php?wake=device-one
- http://www.yourdomain.com:1337/wol.php?wake=Office%20PC

Docker image: https://hub.docker.com/r/kianda/wake-over-internet/

#### Prerequisites
Wake over internet is not that easy to configure, you need to open ports on your router, configure the IP and MAC binding of your devices etc...

I will not teach you here how to do that so please before using this container ensure that your wake over internet is already working, you can try sending a magic packet from your smartphone with any 'wake on lan' app (just remember to be outside your wifi network)

Again, this is just a magic packet sender, all the network and devices configuration is on you.

#### How to build
Go to docker-build folder
- docker pull webdevops/php-apache-dev:ubuntu-18.04
- docker build --no-cache -t yourcustomimagename:yourtag .

#### How to build for hub.docker.com:
Go to docker-build folder
- docker pull webdevops/php-apache-dev:ubuntu-18.04
- docker build --no-cache -t yourdockerusername/yourcustomimagename:yourtag .
- docker login
- docker push yourdockerusername/yourcustomimagename:yourtag

#### How to setup a new config
You can build your own image with your own config or mount your config.json inside the container /app folder on the run

Example:
- docker run -d -v /your/path/to/config.json:/app/config.json -p 1337:80 --name wake-over-internet yourcustomimagename:yourtag

#### How to run
- docker run -d -p 1337:80 --name wake-over-internet yourcustomimagename:yourtag

1337 is the port on the host, this way the webhook will respond under yourdomain.com:1337

#### How to remove the container
- docker rm -f wake-over-internet

This will also delete the logs (if not mounted)

#### How to connect to the container shell
- docker exec -w /app -it wake-over-internet bash

#### How to read debug logs
Connect to the container and then:
- tail -f wol.log
