#!/bin/bash
# Este script executa a atualizção no servidor de produção, neste caso o servidor é deskfacil.com
# é preciso que a máquina local, tenha acesso ao servidor sem a necessidade de digitar senha
# também é importante que no servidor tenha o script autoHemoPull.sh, com o seguinte conteúdo
# #!/bin/bash
# cd /var/www/autohemo
# git pull
# uma vez que o diretório "/var/www/autohemo" é o diretório aonde está o projeto.
#

ssh root@deskfacil.com /root/autoHemoPull.sh

