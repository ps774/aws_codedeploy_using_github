#cloud-config
package_upgrade: true
packages:
- nfs-utils
- amazon-linux-extras
runcmd:
- amazon-linux-extras enable php8.0
packages:
- php8.0
- php{pear,cgi,common,curl,mbstring,gd,mysqlnd,gettext,bcmath,json,xml,fpm,intl,zip,imap}
runcmd:
- service httpd start
- chkconfig httpd on
