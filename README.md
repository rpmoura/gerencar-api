# gerencicar-api

Este projeto foi construído utilizando o framework PHP Laravel, em sua versão 10.x.

### Como executar

- Certifique-se de possuir o Docker e Docker Compose instalados em sua máquina 
- (no projeto foram utilizadas as versões mais recentes disponível, 24.0.6 e 2.23.0, respectivamente)
- Clonar o repositório do Github. **(https://github.com/rpmoura/gerencicar-api)**
- Acessar a raíz do projeto (path/to/gerencicar-api)
- No terminal, executar o seguinte comando:
```sh
docker-compose up -d --build
```
Este comando irá subir todos os containers necessários para a execução aplicação.<br>

>Note que o container ```nginx``` poderá levar mais tempo até ficar apto para uso, <br/>
pois necessitam que container ```api``` satisfaça a condição de teste definida no *healthcheck*


Em sua primeira execução, o container ```api``` irá instalar todas as dependëncias do projeto (definidas em composer.json),
além de realizar a criação das tabelas na base de dados (migrations) e povoar a base (seeder),
além de definir e gerar a chave (APP_KEY) do ambiente (.env),
logo, a conclusão dessa operação poderá levar alguns minutos.


### Como funciona o healthcheck do container

O container ```api``` possui um ponto de entrada (docker-entrypoint.sh) que é chamado através do *command*, definido no docker-compose.yml.<br>
Este ponto de entrada realiza as seguintes operações (iremos considerar a primeira execução):<br>
- Passo 1: Faz a instalação das dependências do projeto ```composer install --optimize-autoloader```<br>
  Cria o arquivo de controle ```.composer_installed```,
  para sinalizar que este passo foi concluído.
- Passo 2: Faz a migração da base de dados junto com a inserção dos dados iniciais ```php artisan migrate --seed```<br>
  Cria o arquivo de controle ```.migrations_executed```, para sinalizar que este passo foi concluído.
- Passo 3: Faz a criação do arquivo de configuração do ambiente(.env) com base no arquivo de exemplo ```cp .env.example .env```<br>
  E gera a chave da aplicação ```php artisan key:generate```<br>
> Nas próximas execuções do container, o healthcheck irá verificar a existência dos arquivo de controle,
> incluindo o .env, para definir o *status* do container ```api``` e seguir com a disponibilização dos containers que o possuem como dependência.

### Coleção de rotas
- As rotas disponibilizadas neste projeto estão em uma *Postman Collection*.<br>
  Disponível no arquivo: ```gerencicar-api.postman_collection.json```, na raíz do projeto, sendo possível realizar a importação.<br>

### Comandos disponíveis
- Execução dos testes
```
docker exec -it gerencicar-api composer run test
```
- Geração do relatório de cobertura dos testes
```
docker exec -it gerencicar-api composer run coverage
```
> O relatório gerado poderá ser acessado através do navegador em: [http://localhost:8019/reports/](http://localhost:8019/reports/)
- Validação das regras PSR-12
```
docker exec -it gerencicar-api composer run format:test
```
- Formatar código de acordo com as regras PSR-12
```
docker exec -it gerencicar-api composer run format
```
Em algumas instalações/sistemas o comando poderá ser **docker-exec**
#### Alguns pontos que poderiam ser considerados no futuro
- Possibilidade de trabalhar com DTO, permitindo termos melhor controle dos dados entre as camadas da aplicação.
- Adicionar autenticação, podendo ser através do Laravel Sanctum ou JWT.
- Manter o histórico de associações entre usuários e veículos

