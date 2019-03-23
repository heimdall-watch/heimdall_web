Création client oAuth :
php bin/console fos:oauth-server:create-client --redirect-uri="http://dev.heimdall.watch" --grant-type="password" --grant-type="refresh_token"