

1. Crear la Imagen Docker a partir del Dockerfile:

	$ sudo docker build --tag socendani/xampp186 .

2. Ejecutar

	$ sudo docker run -p 80:80 -p 4101:2222 -d -it socendani/xampp186

3. Probar web y ssh
	http://localhost/www/
	$ ssh root@localhost -p 4101      => password es root




docker run -d -it socendani/xampp186 /bin/bash


docker run  -p 80:80   -p 3306:3306  -p 4101:2222 -d -it socendani/xampp186

-v/MIREPO:/var/www/html \
https://downloadsapachefriends.global.ssl.fastly.net/xampp-files/1.8.2/xampp-linux-xampp-linux-x64-1.8.2-6-installer.run?from_af=true

https://www.apachefriends.org/xampp-files/5.6.28/xampp-win32-5.6.28-0-VC11-installer.exe

https://www.apachefriends.org/xampp-files/1.8.2/xampp-linux-x64-1.8.2-6-installer.run

https://sourceforge.net/projects/xampp/files/XAMPP%20Linux/1.8.2/xampp-linux-1.8.2-6-installer.run/download

