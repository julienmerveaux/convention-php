# convention-php

```shell
composer install
php bin/console doctrine:database:create
php bin/console doc:sc: up -f
php bin/console doctrine:fixtures:load
npm install #(si vous utilisez des dépendances pour le front)
npm build #(si vous utilisez des dépendances pour le front)
symfony server:start 
```


If you are `bash: symfony: command not found` execute this in the container
````shell
wget https://get.symfony.com/cli/installer -O - | bash
export PATH="$HOME/.symfony5/bin:$PATH"
symfony server:start 
````