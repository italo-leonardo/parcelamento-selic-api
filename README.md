# 📊 Parcelamento SELIC API

API REST em **PHP puro + MySQL** para simular compras parceladas com aplicação de **juros baseados na taxa SELIC**, conforme projeto acadêmico da AVP2 - Backend.

---

## 📌 Funcionalidades

- Cadastro de produtos
- Simulação de compras com entrada e parcelamento
- Aplicação de juros compostos com base na taxa SELIC (real)
- Atualização da taxa SELIC via integração com a API do Banco Central
- Estatísticas financeiras gerais
- Listagem, consulta e exclusão de compras

---

## 🚀 Tecnologias Utilizadas

- PHP 8.x (puro, sem framework)
- MySQL
- XAMPP (Apache + phpMyAdmin)
- Postman (para testes)
- API pública do Banco Central (BCData/SGS)

---

## 🧱 Estrutura da API

| Método | Endpoint       | Descrição                                         |
|--------|----------------|--------------------------------------------------|
| POST   | `/produtos`    | Cadastra um produto                              |
| POST   | `/compras`     | Registra uma nova compra com parcelas            |
| PUT    | `/juros`       | Atualiza a taxa SELIC com base em um período     |
| GET    | `/compras`     | Retorna todas as compras                         |
| GET    | `/compras?id=` | Retorna uma compra específica                    |
| DELETE | `/compras?id=` | Exclui uma compra e suas parcelas                |
| GET    | `/estatistica` | Retorna estatísticas financeiras gerais          |

---

## 📂 Estrutura de Pastas

parcelamento-selic-api/
├── db/
│ └── conexao.php
├── endpoints/
│ ├── produtos.php
│ ├── compras.php
│ ├── compras_get.php
│ ├── compras_delete.php
│ ├── juros.php
│ └── estatistica.php
├── index.php
└── .htaccess


---

## 🛠️ Como Instalar e Rodar Localmente

### 1. Clone o repositório
```bash
git clone https://github.com/seu-usuario/parcelamento-selic-api.git

mv parcelamento-selic-api /opt/lampp/htdocs/parcelamento-selic-api