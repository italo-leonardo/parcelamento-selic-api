CREATE DATABASE api_parcelas;
USE api_parcelas;

-- Tabela de produtos
CREATE TABLE produtos (
  id VARCHAR(36) PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  tipo VARCHAR(100),
  valor DECIMAL(10,2) NOT NULL CHECK (valor >= 0)
);

-- Tabela de compras
CREATE TABLE compras (
  id VARCHAR(36) PRIMARY KEY,
  valorEntrada DECIMAL(10,2) NOT NULL,
  qtdParcelas INT NOT NULL,
  idProduto VARCHAR(36) NOT NULL,
  FOREIGN KEY (idProduto) REFERENCES produtos(id)
);

-- Tabela de parcelas
CREATE TABLE parcelas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  idCompra VARCHAR(36),
  numero INT,
  valorParcela DECIMAL(10,2),
  jurosAplicado DECIMAL(5,2),
  FOREIGN KEY (idCompra) REFERENCES compras(id)
);

-- Tabela de juros
CREATE TABLE juros (
  id INT PRIMARY KEY,
  taxa DECIMAL(5,4),
  ultimaAtualizacao DATE
);

INSERT INTO juros (id, taxa, ultimaAtualizacao) VALUES (1, 0.01, NOW());