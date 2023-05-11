Requirements
============

1. [symfony-cli](https://symfony.com/download)
2. PHP >= 8.1
3. pdo_sqlite
4. composer
5. yarn

Install
=======

1. clone the repository
2. Create a `.env.local` file with the following content
```dotenv
###> omdb/api ###
OMDB_API_KEY="XXXXXXX"
###< omdb/api ###
```
with `XXXXXXX` being the API Key from https://www.omdbapi.com/apikey.aspx
3. Run the following commands :

```bash
$ symfony composer install
$ yarn install
$ yarn dev
$ symfony console doctrine:migration:migrate -n
$ symfony console doctrine:fixture:load -n
$ symfony serve -d 
```
