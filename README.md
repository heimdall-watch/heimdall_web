`mkdir -p config/jwt`

`openssl genrsa -out config/jwt/private.pem -aes256 4096`

`openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem`

As root :
`chmod -R 777 config/jwt`

(TODO in docker start.sh?)