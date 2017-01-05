FROM ubuntu:14.04
MAINTAINER Dani Morte <socendani (at) gmail (dot) com>

# RUN apt-get update \
#  && apt-get upgrade -yq \
#  && apt-get install -yq \
#     wget git lib32stdc++6 \
#  && wget https://sourceforge.net/projects/xampp/files/XAMPP%20Linux/1.8.2/xampp-linux-x64-1.8.2-6-installer.run/download \
# && tar xfz download \
# && mv lampp /opt\
# && rm -rf download

RUN \
       apt-get update \
    && apt-get -y install curl net-tools \
    && curl -o xampp-linux-installer.run "https://sourceforge.net/projects/xampp/files/XAMPP%20Linux/1.8.2/xampp-linux-1.8.2-6-installer.run/download" \
    && chmod +x xampp-linux-installer.run \
    && bash -c './xampp-linux-installer.run' \
    && ln -sf /opt/lampp/lampp /usr/bin/lampp \
    && apt-get clean

# RUN chmod +x xampp-linux-x64-1.8.2-6-installer.run
# RUN bash -c './xampp-linux-x64-1.8.2-6-installer.run'
# RUN ln -sf /opt/lampp/lampp /usr/bin/lampp

# Enable XAMPP web interface(remove security checks)
RUN \
       bash -c 'head --lines=-7 /opt/lampp/etc/extra/httpd-xampp.conf | tee /opt/lampp/etc/extra/httpd-xampp.conf.new ' \
    && mv /opt/lampp/etc/extra/httpd-xampp.conf.new /opt/lampp/etc/extra/httpd-xampp.conf

# Create a /www folder and a symbolic link to it in /opt/lampp/htdocs. It'll be accessible via http://localhost:[port]/www/
# This is convenient because it doesn't interfere with xampp, phpmyadmin or other tools in /opt/lampp/htdocs
RUN \
    mkdir /www \
    && ln -s /www /opt/lampp/htdocs/

# SSH server
RUN \
    apt-get update \
    && apt-get install -y -q supervisor openssh-server \
    && mkdir -p /var/run/sshd \
    && apt-get clean

# Output supervisor config file to start openssh-server
RUN echo "[program:openssh-server]" >> /etc/supervisor/conf.d/supervisord-openssh-server.conf
RUN echo "command=/usr/sbin/sshd -D" >> /etc/supervisor/conf.d/supervisord-openssh-server.conf
RUN echo "numprocs=1" >> /etc/supervisor/conf.d/supervisord-openssh-server.conf
RUN echo "autostart=true" >> /etc/supervisor/conf.d/supervisord-openssh-server.conf
RUN echo "autorestart=true" >> /etc/supervisor/conf.d/supervisord-openssh-server.conf

# Allow root login via password
# root password is: root
RUN sed -ri 's/PermitRootLogin without-password/PermitRootLogin yes/g' /etc/ssh/sshd_config
RUN sed -ri 's/22/2222/g' /etc/ssh/sshd_config 

# Set root password
# password hash generated using this command: openssl passwd -1 -salt xampp root
RUN sed -ri 's/root\:\*/root\:\$1\$xampp\$5\/7SXMYAMmS68bAy94B5f\./g' /etc/shadow

VOLUME [ "/var/log/mysql/", "/var/log/apache2/", "/home/dani/Dropbox/htdocs2" ]

# EXPOSE 9001
EXPOSE 3306
EXPOSE 2222
EXPOSE 80

# write a startup script
RUN echo '/opt/lampp/lampp start' >> /startup.sh
RUN echo '/usr/bin/supervisord -n' >> /startup.sh

CMD ["sh", "/startup.sh"]

# RUN sed -i 's/*_64)/*_69)/g' /opt/lampp/lampp \
#  && echo "/opt/lampp/lampp startapache" > /opt/lampp/start \
#  && echo "/opt/lampp/lampp startmysql" >> /opt/lampp/start \
#  && echo "tail -f /opt/lampp/logs/error_log" >> /opt/lampp/start \
#  && chmod +x /opt/lampp/start

# CMD /opt/lampp/start
