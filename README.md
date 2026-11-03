# DESAFIO BACKEND

## Sobre o teste
O objetivo do teste é avaliar suas habilidades como programador **backend PHP**.

Você receberá uma aplicação que necessita de ajustes descritos pelo cliente e correções de bugs apontadas, deve resolvê-los com a maior qualidade e organização de código possível. Serão avaliados: domínio da linguagem, resolução de problemas, performance, segurança e organização.

Existe uma seção ao fim do _README_ chamada **"Suas Respostas, Dúvidas e Observações"** reservada para documentação do processo de desenvolvimento, mudanças na API e lógica também devem ser documentadas.

## Configuração do Ambiente

### Requisitos
- _PHP >= 8.0_ e [extensões](https://www.php.net/manual/pt_BR/extensions.php) (**não esquecer de instalar as seguintes extensões: _pdo_, _pdo_sqlite_ e _sqlite3_**);
- [_SQLite_](https://www.sqlite.org/index.html);
- [_Composer_](https://getcomposer.org/).

### Instalação
- Instalar dependências pelo _composer_ com `composer install` na raiz do projeto;
- Servir a pasta _public_ do projeto através de algum servidor.
  (_Sugestão [PHP Built in Server](https://www.php.net/manual/en/features.commandline.webserver.)_. Exemplo para servir a pasta public: `php -S localhost:8000 -t public`)

## Sobre a entrega
>[!CAUTION]
> A entrega deve ser realizada em um repositório **_PRIVADO_** do **GitHub**;
> 
> Você deve adicionar os usuários [`pedrosobucki`](https://github.com/pedrosobucki) e [`aloefflerj`](https://github.com/aloefflerj) como colaboradores do repositório com permissão de leitura para que seu teste possa ser avaliado.

- A primeira etapa é realizar um commit inicial com o código sem nenhuma modificação;
- As modificações devem estar separadas por commits coerentes com as funcionalidades e mudanças realizadas ao longo do processo, não um único commit com todas as modificaçẽs;
- As soluções elaboradas e implementadas por você devem ser apresentadas na seção **Suas Respostas, Dúvidas e Observações** ao fim do _README_.

## Sobre o Projeto
- O cliente XPTO Ltda. contratou seu serviço para realizar alguns ajustes em seu sistema de cadastro de produtos;
- O sistema permite o cadastro, edição e remoção de _produtos_ e _categorias de produtos_ para uma _empresa_;
- Para que sejam possíveis os cadastros, alterações e remoções é necessário um usuário administrador;
- O sistema possui categorias padrão que pertencem a todas as empresas, bem como categorias personalizadas dedicadas a uma dada empresa. As categorias padrão são: (`clothing`, `phone`, `computer` e `house`) e **devem** aparecer para todas as _empresas_;
- O sistema tem um relatório de dados dedicado ao cliente.

## Sobre a API
As rotas estão divididas em:
  - _CRUD_ de _categorias_;
  - _CRUD_ de _produtos_;
  - Rota de busca de um _relatório_ que retorna um _html_.

Deve ser utilizado o [Postman](https://www.postman.com/) para desenvolvimento e documentação, o arquivo para importação das rotas se encontra em `docs/postman-api.json`.

> [!WARNING]
> É importante que se adicione o _header_ `admin_user_id` com o id do usuário desejado ao acessar as rotas para simular o uso de um usuário no sistema.

A documentação da API se encontra na pasta `docs/api-docs.pdf`
  - A documentação assume que a url base é `localhost:8000` mas você pode usar qualquer outra url ao configurar o servidor;
  - O _header_ `admin_user_id` na documentação está indicado com valor `1` mas pode ser usado o id de qualquer outro usuário caso deseje (_pesquisando no banco de dados é possível ver os outros id's de usuários_).

## Sobre o Banco de Dados
- O banco de dados é um _sqlite_ simples e já vem com dados preenchidos por padrão no projeto;
- O banco tem um arquivo de backup em `db/db-backup.sqlite3` com o estado inicial do projeto caso precise ser "resetado".

### Migrations
- Funcionalidades que exijam modificações no banco de dados (seja nos dados ou estrutura) **devem estar contidas em _migrations_**, não enviadas diretamente com o banco;
- **Seu arquivo de banco** `db.sqlite3` **não será utilizado para avaliação** do teste, por isso é importante persistir mudanças necessárias em migrations;
- A biblioteca utilizada para migrations foi o [**_Phinx_**](https://book.cakephp.org/phinx/0/en/index.html);
- As migrations criadas **devem poder ser revertidas** (método `down()`);
- Para interagir com as migrations, você pode usar os seguintes comandos:
- - Criar nova migration: `composer create-migration`
- - Rodar migrations: `composer migrate`
- - Reverter migration: `composer rollback`

# Demandas
Abaixo, as solicitações do cliente:

## Alterações
Modificações requisitadas pelo cliente em funcionalidades já existentes

### Categorias
- [ ] A categoria está vindo errada na listagem de produtos para alguns casos (_exemplo: produto `blue trouser` está vindo na categoria `phone`_);
- [ ] Alguns produtos estão vindo com a categoria `null` ao serem pesquisados individualmente (_exemplo: produto `iphone 8`_);
- [ ] Cadastrei o produto `king size bed` em mais de uma categoria, mas ele aparece **apenas** na categoria `furniture` na busca individual do produto.

### Filtros e Ordenamento
Para a listagem de produtos:
- [ ] Gostaria de poder filtrar os produtos ativos ou inativos;
- [ ] Gostaria de poder filtrar os produtos por categoria;
- [ ] Gostaria de poder ordenar os produtos por data de cadastro.

### Relatório
- [ ] O relatório não está mostrando a coluna de logs corretamente, se possível, gostaria de trazer no seguinte formato:
  (Nome do usuário, Tipo de alteração e Data),
  (Nome do usuário, Tipo de alteração e Data),
  (Nome do usuário, Tipo de alteração e Data)
  Exemplo:
  (John Doe, Criação, 01/12/2023 12:50:30),
  (Jane Doe, Atualização, 11/12/2023 13:51:40),
  (Joe Doe, Remoção, 21/12/2023 14:52:50)

### Logs
- [ ] Gostaria de saber qual usuário mudou o preço do produto `iphone 8` por último.

### Correção de bug
- [ ] Ao rodar os teste unitários com `composer test` são apontados erros. Eles precisam ser resolvidos, com documentação sobre a causa e a solução.

## Features
Novas funcionalidades requisitadas pelo cliente

> [!WARNING]
> Preste atenção, funcionalidades que exijam mudanças no banco de dados devem conter tais modificações em uma ou mais **migrations**.

### Traduções
- [ ] Quero disponibilizar meu sistema para fora do país, crie uma funcionalidade de cadastro de traduções para as categorias que segue o seguinte contrato:
```
POST "$base_url/categories/:id"

{
  "translations": [
    {
      "lang_code": "en",
      "label": "home"
    },
    {
      "lang_code": "pt",
      "label": "casa"
    }
  ]
}
```
- [ ] Não deve ser possível cadastrar traduções repetidas, se uma única tradução repetida foi enviada, nenhuma deve persistir;
- [ ] Ao buscar por produtos/categorias, o parâmetro adicional opcional "_`lang`_" pode ser passado para determinar a linguagem em que a categoria deve ser retornada;
- [ ] Caso não haja categoria correspondente ou não seja especificado por parâmetro, retornar em inglês;
- [ ] Inclua a rota e as modificações na coleção do Postman no repositório.

### Estoque
Além das informações já disponíveis do produto, desejo acrescentar também uma contagem de estoque para cada, a qual deve seguir algumas regras:
- [ ] Posso cadastrar a quantidade do estoque assim que cadastro um produto, mas se não for informado assumo que o estoque é _0_;
- [ ] Posso atualizar o estoque de um produto;
- [ ] Ao buscar um produto, posso filtrar por uma quantidade mínima em estoque.

### Comentários
Quero que os usuários do sistema possam discutir sobre os produtos em uma área de comentários.

Para isso, novas rotas devem ser criadas:
- [ ] Criar um novo comentário no produto
- [ ] Responder um comentário já realizado (todo comentário pode ser diretamente respondido)
- [ ] Remover um comentário feito por mim
- [ ] Curtir um comentário
- [ ] Listar todos os comentários de um produto em um objeto com hierarquia de comentários

##
**Seu trabalho é atender às demandas solicitadas pelo cliente.**

Caso julgue necessário, podem ser adicionadas ou modificadas as rotas da api. Caso altere, por favor, explique o porquê e indique as alterações nesse `README`.

Sinta-se a vontade para refatorar o que achar pertinente, considerando questões como arquitetura, padrões de código, padrões restful, _segurança_ e quaisquer outras boas práticas. Levaremos em conta essas mudanças.

## Docker
Você deve servir a aplicação por meio de um ambiente docker

Para efetuar a criação do ambiente docker, partimos de algumas premissas:
- O ambiente tem o **objetivo de ser utilizado por outros devs**, então deve funcionar sem necessidade de alteração de arquivos para inserção de dados específicos de máquina (quando baixo o repositório quero subir o container sem que fazer edições no ambiente docker);
- Levando o ponto anterior em conta, é inteligente não deixar a criação do container para o final;
- A funcionalidade de **PHP** deve rodar na porta **8000** do host.

### Itens obrigatórios
- [ ] Criar um ambiente docker que sobe a aplicação **PHP** na porta **8000**;
- [ ] Possibilitar que as **_migrations_ possam ser executadas/criadas por docker** (especificar comando);
- [ ] Possibilitar que os **_testes unitários_ sejam executados por docker** (especificar comando).

### Desafios
- [ ] Substituir o banco serverless **SQLite** por um banco como **MySQL**/**PostgreSQL**/outro e servir por container;
- [ ] Escrever **novos testes unitários** para funcionalidades faltantes;
- [ ] Implementar um **Linter** e disponibilizar por docker (especificar comando);
- [ ] Implementar **análise estática** e disponibilizar por docker (especificar comando);
- [ ] Escrever um script "_`check_deploy.sh`_" que faz todas as validações implementadas como uma pipeline e determina se o código está pronto para produção.

## Suas Respostas, Dúvidas e Observações

As seções seguem a ordem do enunciado: primeiro o ambiente e os testes, depois as demandas do cliente, os problemas que encontrei durante o desenvolvimento e, por fim, a refatoração. Nas causas dos bugs, os métodos citados são os do código original; depois, o acesso ao banco foi movido para repositories.

### Ambiente

```bash
docker compose up -d --build                                       # sobe a aplicação em localhost:8000
docker compose exec app composer migrate                           # executa as migrations
docker compose exec app composer test                              # roda todos os testes (unitários e de feature)
docker compose exec app composer rollback-migration                # reverte a última migration
docker compose exec app composer create-migration NomeDaMigration  # cria uma migration
docker compose exec -e XDEBUG_TRIGGER=1 app vendor/bin/phpunit     # roda os testes com o Xdebug ativo
```
Caso `db/db.sqlite3` não exista, o container o cria a partir de `db/db-backup.sqlite3`. Depois de subir o container, rode as migrations para aplicar as mudanças de banco das novas funcionalidades. O comando de reverter migrations no `composer.json` se chama `rollback-migration`, e não `rollback` como indicado no enunciado.

#### Coleção do Postman
A coleção em `docs/postman-api.json` não funcionava ao ser importada: as variáveis usavam a sintaxe do Insomnia (`{{ _.base_url }}`) em vez da do Postman (`{{base_url}}`), e as URLs terminavam com `/`, o que não corresponde às rotas do Slim nem à documentação em `docs/api-docs.pdf`. Corrigi as duas coisas.
A coleção também foi atualizada com as novas funcionalidades: a rota de cadastro de traduções (`categories → insertTranslations`), os parâmetros de filtro, ordenação e idioma (incluídos desmarcados nas requisições de busca, com descrição) e o campo `stock` no cadastro e na edição de produtos.

### Testes
Além dos testes unitários existentes, adicionei testes de feature (`tests/Feature`), que fazem requisições à aplicação e validam a resposta.
- Cada teste roda em uma cópia temporária de `db/db-backup.sqlite3`, com as migrations aplicadas, então o banco real nunca é alterado e os testes usam a mesma estrutura de banco da aplicação. Para isso, o caminho do banco passou a aceitar a variável de ambiente `DB_PATH`, e a montagem da aplicação foi extraída de `public/index.php` para `src/Config/app.php`, para ser reutilizada pelos testes.
- Os dados de cada teste são criados por factories (`tests/Factories`), que definem valores padrão para cada tabela e permitem sobrescrever apenas os campos relevantes para o cenário, por exemplo `ProductFactory::create(['active' => 0])`.

### Alterações

#### Categorias

**Categoria errada na listagem** (ex.: `blue trouser` como `phone`)
- **Problema:** produtos apareciam com a categoria errada, e a listagem retornava apenas 6 dos 18 produtos.
- **Causa:** em `ProductService::getAll()`, o JOIN com `category` usava `pc.id` (o id da linha da tabela de ligação) em vez de `pc.cat_id`.
- **Solução:** corrigi o JOIN para `pc.cat_id`. Coberto por teste de feature.

**Produtos duplicados e categorias incompletas na listagem** *(encontrado ao corrigir o item anterior)*
- **Problema:** produtos com mais de uma categoria apareciam duplicados na listagem, cada linha com só uma das categorias.
- **Causa:** o JOIN com `category` gera uma linha por categoria do produto.
- **Solução:** a listagem passou a buscar os produtos e, em uma segunda query, as categorias de todos eles de uma vez, evitando o problema de N+1. O relatório, que usa a mesma busca, também foi corrigido. Coberto por testes de feature.
- **Mudança na API:** o campo `category` (texto) foi substituído por `categories` (lista):
  ```json
  "categories": [
      { "id": 4, "title": "furniture" },
      { "id": 6, "title": "house" }
  ]
  ```

**Categoria `null` e categorias faltando na busca individual** (ex.: `iphone 8` e `king size bed`)
- **Problema:** em `GET /products/{id}`, o `iphone 8` vinha com categoria `null` e o `king size bed` mostrava apenas `furniture`.
- **Causa:** `ProductController::getOne()` buscava só a primeira categoria do produto (`fetch()`) e depois a consultava em `CategoryService::getOne()`, que filtra pela empresa do usuário. Como as categorias padrão têm `company_id` nulo, eram descartadas (no SQL, `NULL = 1` não é verdadeiro).
- **Solução:** a busca individual passou a usar a mesma busca de categorias da listagem, que retorna todas as categorias do produto, inclusive as padrão. O método `getProductCategory()`, que ficou sem uso, foi removido. Coberto por testes de feature.
- **Mudança na API:** assim como na listagem, o campo `category` foi substituído por `categories`.

#### Filtros e ordenamento

`GET /products` passou a aceitar parâmetros opcionais e combináveis:

| Parâmetro | Valores | Efeito |
|---|---|---|
| `active` | `1`/`true` ou `0`/`false` | apenas produtos ativos ou inativos |
| `category_id` | id da categoria | apenas produtos da categoria |
| `sort` | `created_at` ou `-created_at` | ordena pela data de cadastro, do mais antigo ou do mais novo primeiro |

Exemplo: `GET /products?active=1&category_id=4&sort=-created_at`. Sem parâmetros, a listagem continua como antes.

- Os parâmetros são convertidos no controller em um DTO tipado (`ProductFilters`), para que service e repository não dependam do formato da requisição HTTP.
- A filtragem é feita na própria consulta SQL. O filtro por categoria usa uma subconsulta, para não duplicar produtos e manter todas as categorias de cada produto na resposta.
- A ordenação aceita apenas os valores acima; qualquer outro mantém a ordem padrão. Como o `ORDER BY` não aceita parâmetros do prepared statement, ele nunca é montado com texto vindo do usuário.
- Coberto por testes de feature.

#### Relatório

**Coluna de logs mostrando `Array`**
- **Problema:** a coluna "Logs de Alterações" mostrava `Array` e gerava warnings de `Array to string conversion`.
- **Causa:** a lista de logs do produto era inserida diretamente na célula da tabela, sem ser convertida em texto.
- **Solução:** os logs passaram a ser exibidos no formato pedido, `(Nome do usuário, Tipo de alteração, Data)`, um por linha e em ordem cronológica. As ações são traduzidas (`create` → Criação, `update` → Atualização, `delete` → Remoção) e a data é exibida em `dd/mm/aaaa hh:mm:ss`. Coberto por testes de feature.
- **Decisão:** logs de usuários inexistentes (como o usuário 6, presente nos dados) aparecem como `Usuário desconhecido`, em vez de sumirem do relatório.

#### Logs

**Quem alterou o preço do `iphone 8` por último?**

```sql
SELECT pl.id, pl.admin_user_id, au.name, pl.action, pl.timestamp
FROM product_log pl
LEFT JOIN admin_user au ON au.id = pl.admin_user_id
WHERE pl.product_id = 4
ORDER BY pl.timestamp, pl.id;
```

A última alteração no produto foi feita pela usuária **corin** (id 3), em 22/12/2023 às 18:12:10.

Porém, o log registra apenas o tipo da ação (`create`, `update`, `delete`), e não quais campos foram alterados. Por isso, não é possível afirmar que essa alteração foi no preço, apenas que ela foi a última a alterar o produto. Para responder com precisão, o log precisaria guardar os valores anteriores e novos de cada campo alterado.

Observação: o histórico do produto contém dois registros de criação no mesmo instante, um deles atribuído a um usuário inexistente (id 6), o que indica inconsistência nos dados de log.

#### Correção de bug

Ao rodar `composer test` foram apontados dois problemas:
- **Aviso de `Deprecated` do PHPUnit:** ...
- **Teste `Hydrate by fetch` falhando:** ... Troquei o cast para `(float)`.

### Features

#### Estoque

Migration `AddStockToProducts`: adiciona a coluna `stock` à tabela `product`, com valor padrão `0`, e o `down()` a remove.

| Regra do cliente | Como usar |
|---|---|
| Informar o estoque ao cadastrar; se não informado, é 0 | `POST /products` com `"stock": 15` (opcional) |
| Atualizar o estoque | `PUT /products/{id}` com `"stock": 20` |
| Filtrar por estoque mínimo | `GET /products?min_stock=5` (estoque maior ou igual a 5), combinável com os outros filtros |

`GET /products` e `GET /products/{id}` passaram a retornar o campo `stock`.

- **Decisão:** no `PUT`, o campo `stock` é opcional e, quando não enviado, o estoque atual é mantido (`COALESCE` no SQL). Assim, clientes que já editam produtos sem esse campo não zeram o estoque sem perceber.
- Coberto por testes de feature, um por regra, além de um que garante que o `PUT` sem `stock` mantém o valor atual.

#### Traduções

Migration `CreateCategoryTranslations`: cria a tabela `category_translation`, com uma linha por categoria e idioma, e o `down()` a remove. Uma tabela própria permite cadastrar qualquer idioma sem novas alterações no banco.

**Cadastro:** `POST /categories/{id}`, seguindo o contrato do enunciado.

| Situação | Resposta |
|---|---|
| Traduções salvas | `201 Created` |
| Algum idioma repetido, na própria requisição ou já cadastrado para a categoria | `409 Conflict`, e nenhuma tradução é salva |
| Categoria inexistente ou de outra empresa | `404 Not Found` |

- A regra de não repetir é garantida pelo banco, com um índice único em `(category_id, lang_code)`. As traduções são inseridas em uma transação: se qualquer uma for recusada, o `rollback` desfaz as demais.
- As categorias padrão também podem ser traduzidas, já que o próprio exemplo do enunciado (`home`/`casa`) corresponde à categoria padrão `house`.
- A rota segue o contrato definido pelo cliente (`POST /categories/:id`), embora uma rota como `POST /categories/:id/translations` fosse mais próxima do padrão REST.
- Coberto por testes de feature.

**Busca no idioma:** `GET /categories`, `GET /categories/{id}`, `GET /products` e `GET /products/{id}` aceitam o parâmetro opcional `lang` (por exemplo, `GET /products?lang=pt`). O campo `title` das categorias passa a vir na seguinte ordem de prioridade:

1. a tradução no idioma pedido;
2. se ela não existir, ou se `lang` não for informado, a tradução em inglês (`en`);
3. se também não houver tradução em inglês, o título original da categoria, já que os títulos cadastrados estão em inglês.

- A regra é resolvida na própria consulta: as traduções no idioma pedido e em inglês são unidas por `LEFT JOIN` e o `COALESCE` escolhe a primeira existente. Assim, as traduções chegam junto com as categorias, sem consultas extras por categoria (sem N+1).
- O nome do campo continua sendo `title`, então os clientes atuais da API não precisam de alteração.
- O relatório usa a regra padrão e exibe as categorias em inglês.
- Coberto por testes de feature, um para cada regra e um para cada rota que aceita o parâmetro.

#### Comentários

Migration `CreateProductComments`: cria as tabelas `product_comment` (comentários, com `parent_id` apontando para o comentário respondido) e `product_comment_like` (curtidas), e o `down()` as remove.

**Comentar e responder:** `POST /products/{id}/comments` com `{"content": "..."}`. Para responder um comentário, basta enviar também o `parent_id` do comentário respondido. Como toda resposta é um comentário com pai, qualquer comentário pode ser respondido, inclusive outra resposta.

| Situação | Resposta |
|---|---|
| Comentário criado | `201 Created`, com o `id` do comentário |
| Produto inexistente ou de outra empresa | `404 Not Found` |
| `parent_id` inexistente ou de outro produto | `404 Not Found` |

- Coberto por testes de feature.

**Listar em hierarquia:** `GET /products/{id}/comments` retorna os comentários do produto, cada um com as respostas aninhadas em `replies`, em qualquer profundidade:

```json
[
  {
    "id": 1, "parent_id": null, "user": "rivers", "content": "A bateria dura bem?", "created_at": "...",
    "replies": [
      { "id": 2, "parent_id": 1, "user": "kim", "content": "Dura o dia todo", "created_at": "...", "replies": [] }
    ]
  }
]
```

- Uma única consulta busca todos os comentários do produto, e o `CommentService` monta a hierarquia em memória: as respostas de um comentário são os comentários cujo `parent_id` é o dele. Assim não há consultas por comentário (sem N+1).
- Produto inexistente ou de outra empresa retorna `404 Not Found`.
- Coberto por testes de feature.

### Problemas encontrados

Bugs que não foram apontados pelo cliente, mas que encontrei durante o desenvolvimento.

**Categorias padrão não apareciam nas rotas de categoria**
- **Problema:** `GET /categories` não listava as categorias padrão (`clothing`, `phone`, `computer` e `house`), e `GET /categories/{id}` retornava vazio para elas, contrariando o requisito de que devem aparecer para todas as empresas.
- **Causa:** `CategoryService::getAll()` e `getOne()` filtravam apenas `company_id = <empresa do usuário>`, descartando as categorias padrão, que têm `company_id` nulo.
- **Solução:** as duas consultas passaram a aceitar também `company_id IS NULL`. Coberto por testes de feature, incluindo um que garante que categorias de outras empresas continuam ocultas.
- **Decisão:** a edição e a remoção continuam restritas às categorias da própria empresa, então as categorias padrão podem ser vistas, mas não alteradas ou removidas por uma empresa, já que são compartilhadas entre todas.

**Listagem de produtos vazia para usuários diferentes do usuário 1**
- **Problema:** `GET /products` retornava lista vazia para qualquer usuário além do 1, mesmo sendo da mesma empresa.
- **Causa:** `ProductService::getAll()` comparava o `company_id` do produto com o id do usuário (`WHERE company_id = {$adminUserId}`), o que só funcionava porque o usuário 1 pertence à empresa 1.
- **Solução:** a consulta passou a buscar a empresa do usuário em `admin_user`. O relatório, que usa a mesma busca, também foi corrigido. Coberto por testes de feature, incluindo um que garante que produtos de outras empresas continuam ocultos.

**SQL injection e erros com apóstrofo e `"active": false`**
- **Problema:** `GET /categories/4 OR 1=1` retornava todas as categorias. Pelo mesmo motivo, títulos com apóstrofo e registros com `"active": false` quebravam a query ao serem salvos.
- **Causa:** os valores eram interpolados diretamente no texto das queries.
- **Solução:** todas as queries passaram a usar prepared statements, durante a extração do acesso ao banco para repositories. A injeção é coberta por teste de feature.

**Relatório vulnerável a XSS**
- **Problema:** valores cadastrados pelos usuários, como títulos de produtos e categorias, eram inseridos no HTML do relatório sem tratamento. Um título como `<script>alert(1)</script>` seria executado no navegador de quem abrisse o relatório.
- **Causa:** a tabela concatenava os valores diretamente nas células.
- **Solução:** todas as células passaram a ser escapadas com `htmlspecialchars()` na `HtmlTable`, que concentra a geração do HTML. Coberto por teste de feature.

### Refatoração
Tratei o projeto como um sistema legado: em vez de reescrever tudo, refatorei de forma incremental apenas o que trazia ganho concreto, sempre protegido pelos testes de feature, que passaram sem nenhuma alteração após cada etapa.

#### Camada de repositories
O acesso ao banco estava misturado às regras de negócio nos services. Separei as responsabilidades em camadas:
- **Controller:** recebe a requisição e devolve a resposta.
- **Service:** orquestra as regras de negócio.
- **Repository:** concentra o acesso ao banco, sempre com prepared statements.

Repositories criados:
- `CategoryRepository`: tabela `category`.
- `ProductRepository`: tabelas `product` e `product_category`.
- `ProductLogRepository`: tabela `product_log`.
- `CompanyRepository`: tabela `company`. O `CompanyController` fazia as queries diretamente, sem passar por um service.
- `AdminUserRepository`: único ponto que resolve a empresa do usuário, antes repetido nos services.

**Interfaces apenas nos repositories:** os services recebem os repositories pelo construtor e dependem das suas interfaces (`src/Repository/Contracts`), não das implementações. Isso isola as regras de negócio do banco de dados: é possível trocar o banco (um dos desafios propostos) ou testar um service com um repository falso sem alterá-lo. Não criei interfaces para os services porque eles têm uma única implementação e não há cenário de substituição, então a abstração não traria benefício.

**Sem container de injeção de dependência:** as dependências são montadas manualmente nos controllers. Com poucos controllers, um container adicionaria mais infraestrutura do que resolveria.

#### Separação do relatório
O `ReportController` buscava os dados, montava as linhas do relatório e gerava o HTML. Separei essas responsabilidades seguindo o padrão Controller → Service que o projeto já usa: o `ReportService` monta os dados e a `HtmlTable` (`src/View`) gera o HTML, deixando o controller apenas com a requisição e a resposta.