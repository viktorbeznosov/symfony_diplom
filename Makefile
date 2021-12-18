php_cs_fixer = ./vendor/bin/php-cs-fixer
php_code_sniffer = ./vendor/bin/phpcbf
php_md = ./vendor/bin/phpmd

run_fixers: run_cs fixer_run md_run

run_cs:
	php -d memory_limit=1024M ./vendor/bin/phpcbf --standard=PSR2 --encoding=utf-8 \
	--ignore=/tests,/public,/vendor,/resources,/node_modules,storage,bootstrap,_ide_helper.php,models.php . -p

fixer_check:
	${php_cs_fixer} fix -v --dry-run --config=.php_cs.dist

fixer_run:
#	${docker_exec_php} ${php_cs_fixer} fix --config=.php_cs.dist
	${php_cs_fixer} fix --config=.php_cs.dist

md_run:
	${php_md} ./ text ./phpmd.xml --exclude tests,public,vendor,resources,node_modules,storage,bootstrap,_ide_helper.php,models.php

run_checks: run_fixers docker_php_test

