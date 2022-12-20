# aws_codedeploy_using_github

#!/bin/bash -xe

# Use this for your user data (script from top to bottom)
# install httpd (Linux 2 version)
yum update -y
yum install -y httpd
systemctl start httpd
systemctl enable httpd
mkdir /var/popsixlelogs
mkdir /var/popsixlelogs/shopsixle
mkdir /var/popsixlelogs/report
mkdir /var/www/shopsixle
mkdir /var/www/report
chown -R ec2-user:ec2-user /var/www/
echo "Welcome to shopsixle.com" >> /var/www/shopsixle/shopsixle.html
echo "Welcome to report.shopsixle.com" >> /var/www/report/report.html
aws s3 cp s3://ec2tocode/virtualhost.conf /etc/httpd/conf.d/
yum install -y amazon-linux-extras
amazon-linux-extras enable php8.0
yum install -y php
chkconfig httpd on
yum install -y ruby
cd /home/ec2-user
wget https://aws-codedeploy-us-east-1.s3.us-east-1.amazonaws.com/latest/install
chmod +x ./install
./install auto
