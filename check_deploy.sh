#!/usr/bin/env bash
#
# Pipeline de validação: só considera o código pronto para produção
# se todas as etapas passarem. Para na primeira que falhar.

set -euo pipefail

step() {
    echo
    echo "==> $1"
}

step "Validando o composer.json e o composer.lock"
composer validate --no-check-publish

step "Verificando o padrão de código (PHP_CodeSniffer)"
composer lint

step "Rodando a análise estática (PHPStan)"
composer analyse

step "Rodando os testes (PHPUnit)"
composer test

echo
echo "Tudo certo: o código está pronto para produção."
