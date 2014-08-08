Guia para a instalação da extensão Intelipost para Magento



1.	Acesse:   https://www.intelipost.com.br
1.1.	Criar usuário que será o Administrador da conta

2.	Você será redirecionado automaticamente para: https://secure.intelipost.com.br
2.1.	Configurar “Dados do Cliente”
2.2.	Obter a API_KEY

3.	Instalar Intelipost Magento Connect Extension
3.1.	Usar arquivo “Intelipost-0.8.4.tgz”

4.	Configurar Intelipost em seu Ambiente Magento Admin Panel
4.1.	System
4.2.	Configuration
4.3.	Shipping Methods
4.4.	Intelipost Shipping

 https://cloud.githubusercontent.com/assets/7913922/3859725/f59f522a-1f1b-11e4-8731-cf9359eb50fc.png

4.5.	Se a api_url e api_key estão OK, ao salvar, uma call en /quote de teste será executada.
4.6.	Em /var/log/intelipost.log veremos os registros de request/response.
4.7.	Em Shipping/Model/Config/Apikey.php call de low level integration.
4.8. Em Shipping/Model/Carrier/Intelipost.php temos um exemplo de high level integration.

Manual de Integração – versão preliminar (sempre em constante atualização)
➢	https://docs.intelipost.com.br

Github (com o change log)
➢	https://github.com/intelipost/magento

Magento Connect 2.0
➢	http://www.magentocommerce.com/magento-connect/intelipost-api.html


Dúvidas?
Entre em contato com: support@intelipost.com.br ou diretamente com seu contato comercial. Mais informações sobre o produto em www.intelipost.com.br

