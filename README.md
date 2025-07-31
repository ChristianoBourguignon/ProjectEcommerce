# ProjectEcommerce - Sistema de E-commerce em PHP

## 📋 Descrição

ProjectEcommerce é um sistema completo de e-commerce desenvolvido em PHP puro, utilizando arquitetura MVC (Model-View-Controller) e PDO para conexão com banco de dados MySQL. O projeto inclui funcionalidades de autenticação, gerenciamento de produtos, carrinho de compras, pedidos e sistema de cupons.

## 🚀 Funcionalidades

### 🔐 Autenticação
- Registro de usuários com CPF
- Login com CPF
- Logout com limpeza de sessão
- Cookies para persistência de dados

### 📦 Gerenciamento de Produtos
- Listagem de produtos com estoque
- Cadastro de produtos com upload de imagens
- Exclusão de produtos
- Controle de estoque

### 🛒 Carrinho de Compras
- Adicionar produtos ao carrinho
- Gerenciar quantidades
- Cálculo automático de valores
- Persistência de dados no navegador

### 📋 Pedidos
- Finalização de compra
- Validação de estoque
- Cálculo de frete
- Histórico de pedidos
- Detalhes de pedidos

### 🎫 Sistema de Cupons
- Interface para gerenciamento de cupons
- Aplicação de descontos

## 🛠️ Tecnologias Utilizadas

- **PHP 8.0+**
- **MySQL 5.7+**
- **League Plates** (Template Engine)
- **PDO** (Database Access)
- **PHPUnit** (Testes)
- **PHPStan** (Análise Estática)

## 📁 Estrutura do Projeto

```
ProjectEcommerce/
├── App/
│   ├── controllers/          # Controladores da aplicação
│   ├── exceptions/           # Exceções customizadas
│   ├── models/              # Modelos e componentes
│   ├── routers/             # Sistema de roteamento
│   ├── static/              # Arquivos estáticos
│   │   ├── css/            # Estilos CSS
│   │   ├── js/             # JavaScript
│   │   ├── images/         # Imagens
│   │   └── uploads/        # Uploads de imagens
│   └── views/              # Views/Templates
├── Test/                    # Testes automatizados
├── logs/                    # Logs da aplicação
├── vendor/                  # Dependências Composer
├── composer.json            # Configuração do Composer
├── docker-compose.yml       # Configuração Docker
├── Dockerfile              # Dockerfile
├── index.php               # Ponto de entrada
└── README.md               # Este arquivo
```

## 🚀 Instalação e Configuração

### Pré-requisitos

- PHP 8.0 ou superior
- MySQL 5.7 ou superior
- Composer
- Docker (opcional)

### 1. Clone o Repositório

```bash
git clone https://github.com/seu-usuario/ProjectEcommerce.git
cd ProjectEcommerce
```

### 2. Instalação com Composer

```bash
# Instalar dependências
composer install

# Instalar dependências de desenvolvimento
composer install --dev
```

### 3. Configuração do Banco de Dados

#### Usando Docker

```bash
# Iniciar containers
docker-compose up -d

# Verificar status
docker-compose ps
```

### 4. Configuração do Servidor Web

#### Apache
Crie um arquivo `.htaccess` na raiz do projeto:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

### 5. Permissões de Arquivo

```bash
# Definir permissões para uploads
chmod 755 App/static/uploads/
chmod 755 logs/
```

## 🧪 Executando Testes

### Instalar PHPUnit

```bash
composer require --dev phpunit/phpunit
```

## 📊 Estrutura do Banco de Dados

O sistema cria automaticamente as seguintes tabelas:

### Tabela `users`
- `id_user` (INT, AUTO_INCREMENT, PRIMARY KEY)
- `name` (VARCHAR(150))
- `email` (VARCHAR(150))
- `cpf` (VARCHAR(11), UNIQUE)

### Tabela `products`
- `id_products` (INT, AUTO_INCREMENT, PRIMARY KEY)
- `name` (VARCHAR(150))
- `price` (DECIMAL(10,2))
- `image` (VARCHAR(150))
- `date_created` (DATETIME)

### Tabela `stock`
- `id_stock` (INT, AUTO_INCREMENT, PRIMARY KEY)
- `product_id` (INT, FOREIGN KEY)
- `quantity` (INT)
- `update_in` (DATETIME)

### Tabela `cupons`
- `id_cupom` (INT, AUTO_INCREMENT, PRIMARY KEY)
- `code` (VARCHAR(50), UNIQUE)
- `discount_percent` (DECIMAL(5,2))
- `discount_value` (DECIMAL(10,2))
- `active` (TINYINT(1))
- `expires_at` (DATETIME)
- `created_at` (TIMESTAMP)

### Tabela `orders`
- `id_orders` (INT, AUTO_INCREMENT, PRIMARY KEY)
- `user_id` (INT, FOREIGN KEY)
- `cupom_id` (INT, FOREIGN KEY)
- `total_price` (DECIMAL(10,2))
- `shipping_price` (DECIMAL(10,2))
- `status` (ENUM: 'PENDENTE', 'PAGO', 'CANCELADO')
- `order_date` (DATETIME)
- Campos de endereço (street, number, complement, etc.)

### Tabela `items_order`
- `id_orderitems` (INT, AUTO_INCREMENT, PRIMARY KEY)
- `order_id` (INT, FOREIGN KEY)
- `product_id` (INT, FOREIGN KEY)
- `quantity` (INT)
- `unit_price` (DECIMAL(10,2))

## 🔧 Configuração de Desenvolvimento

### Análise Estática com PHPStan

```bash
# Executar análise estática
./vendor/bin/phpstan analyse App/
```

### Logs

Os logs são salvos em `logs/` com formato `log-YYYY-MM-DD.log`.

### Variáveis de Ambiente

Crie um arquivo `.env` na raiz do projeto:

```env
# Configurações do Banco de Dados
DB_HOST=db
DB_PORT=3306
DB_NAME=project_php
DB_USER=admin
DB_PASS=admin
DB_ROOT_PASSWORD=root
COMPOSE_PROJECT_NAME=projectphp

```

## 🐳 Docker

### Usando Docker Compose

```bash
# Construir e iniciar containers
docker-compose up -d

# Ver logs
docker-compose logs -f

# Parar containers
docker-compose down

# Reconstruir containers
docker-compose up -d --build
```

### Dockerfile

O projeto inclui um Dockerfile para containerização:

```bash
# Construir imagem
docker build -t project-ecommerce .

# Executar container
docker run -p 8080:80 project-ecommerce
```

## 📝 API Endpoints

### Autenticação
- `POST /criar-conta` - Criar nova conta
- `POST /logar` - Fazer login
- `GET /deslogar` - Fazer logout

### Produtos
- `GET /produtos` - Listar produtos
- `POST /criar-produto` - Criar produto
- `POST /excluir-produto` - Excluir produto

### Pedidos
- `GET /pedidos` - Listar pedidos
- `GET /checkout` - Página de checkout
- `POST /finalizar-compra` - Finalizar compra
- `POST /get-produtos-pedido` - Obter produtos do pedido

### Cupons
- `GET /cupons` - Página de cupons

## 🚨 Tratamento de Erros

O sistema utiliza exceções customizadas para tratamento de erros:

- `exceptionCustom` - Exceção base
- `viewsControllerException` - Erros de views
- `invalidParametersAuthException` - Parâmetros de autenticação inválidos
- `invalidArgumentsForProductsException` - Argumentos de produtos inválidos
- `ordersFinishException` - Erros de finalização de pedidos

## 🔒 Segurança

- Sanitização de inputs com `filter_input()`
- Validação de tipos de arquivo para uploads
- Prepared statements para prevenir SQL Injection
- Validação de CPF
- Controle de sessão

## 📈 Monitoramento

### Logs
- Logs de erro em `logs/`
- Formato: `log-YYYY-MM-DD.log`

## 🤝 Contribuindo

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 👨‍💻 Autor

**Christiano Bourguignon**

## 📞 Suporte

Para suporte, abra uma issue no repositório.

---

**Nota**: Este é um projeto pessoal. Para uso em produção, considere implementar medidas de segurança adicionais e otimizações de performance. 