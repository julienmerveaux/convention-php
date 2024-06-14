# convention-php

```shell
composer install
bin/console doctrine:database:create
bin/console doc:sc: up -f
bin/console doctrine:fixtures:load
npm install #(si vous utilisez des dépendances pour le front)
npm build #(si vous utilisez des dépendances pour le front)
symfony server:start 
```


If you are `bash: symfony: command not found` execute this in the container
````shell
wget https://get.symfony.com/cli/installer -O - | bash
export PATH="$HOME/.symfony5/bin:$PATH"
````