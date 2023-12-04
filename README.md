# Jump Park API

Bem-vindo ao projeto Jump Park API, uma aplicação Laravel para gerenciamento de ordens de serviço.

## Instalação

Clone o projeto:

```bash
git clone https://github.com/cleberamancio/jump_park.git
cd jump_park

#Execute o Docker Compose para iniciar o banco de dados:
docker-compose up -d

#Ataualize as dependencias do projeto.
composer install

#Gerar uma chave de aplicativo única para o seu projeto.
php artisan key:generate

#Definir um arquivo .env com as credenciais do Mysql no Docker
cp .env.example .env

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jump_park
DB_USERNAME=root
DB_PASSWORD=Abcd1234@

#Criar as tabelas necessárias.
php artisan migrate

#Executar o servidor interno do Laravel
php artisan serve

#Executar os testes unitários
php artisan test

```
Se estiver usando o MySQL no Docker, crie uma pasta chamada mysql na raiz do projeto.

## Documentação da API
-A documentação completa da API pode ser encontrada [aqui](https://jumppark.byte1.com.br/api/documentation).


### Recursos da API
- **Listagem com Filtro e Paginação** - *Realize uma requisição para obter uma lista com filtro e paginação.*
- **Criação de Ordem de Serviço** - *Crie uma nova ordem de serviço.*
- **Edição de Ordem de Serviço** - *Edite uma ordem de serviço existente.*
- **Remoção Lógica da Ordem de** *Serviço - Remova logicamente uma ordem de serviço.*
- **Cadastro de Novo Usuário** - *Registre um novo usuário e obtenha um token de acesso.*
- **Login do Usuário** - *Efetue login e obtenha um token de acesso.*
- **Cadastro de Novo Usuário** - *Registre um novo usuário e obtenha um token de acesso.*


### Licença
Este projeto é licenciado sob a [Licença MIT](https://github.com/cleberamancio/jump_park/blob/main/LICENSE).

