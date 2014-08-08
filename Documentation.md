# Guia para a instalação da extensão Intelipost para Magento


Acesse:   https://www.intelipost.com.br
Criar usuário que será o Administrador da conta

Você será redirecionado automaticamente para: https://secure.intelipost.com.br
Configurar “Dados do Cliente”
Obter a API_KEY

Instalar Intelipost Magento Connect Extension
Usar arquivo “Intelipost-0.8.4.tgz”
Configurar Intelipost em seu Ambiente Magento Admin Panel
System
Configuration
Shipping Methods
Intelipost Shipping

 ![documentation](https://cloud.githubusercontent.com/assets/7913922/3859725/f59f522a-1f1b-11e4-8731-cf9359eb50fc.png)

Se a api_url e api_key estão OK, ao salvar, uma call en /quote de teste será executada.
Em /var/log/intelipost.log veremos os registros de request/response.
Em Shipping/Model/Config/Apikey.php call de low level integration.
Em Shipping/Model/Carrier/Intelipost.php temos um exemplo de high level integration.

Manual de Integração – versão preliminar (sempre em constante atualização)
https://docs.intelipost.com.br

Github (com o change log)
https://github.com/intelipost/magento

Magento Connect 2.0
http://www.magentocommerce.com/magento-connect/intelipost-api.html


Dúvidas?
Entre em contato com: support@intelipost.com.br ou diretamente com seu contato comercial. Mais informações sobre o produto em www.intelipost.com.br

