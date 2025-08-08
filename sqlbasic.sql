-- Inserir usuário padrão
INSERT INTO users (name, email, cpf)
VALUES ('Usuario teste', 'usuario.teste@example.com', '12345678911');

-- Inserir produto com imagem específica
INSERT INTO products (name, price, image)
VALUES ('Produto Teste 1', 199.99, 'img_688a2af69c3e87.36082935.png'),
       ('Produto Teste 2', 99.90, 'img_688a2af69c3e87.36082935.png'),
       ('Produto Teste 3', 49.50, 'img_688a2af69c3e87.36082935.png');

-- Inserir estoque para os produtos criados
INSERT INTO stock (product_id, quantity)
VALUES (1, 20), -- Produto Teste 1
       (2, 15), -- Produto Teste 2
       (3, 30); -- Produto Teste 3

-- Inserir cupons
INSERT INTO cupons (code, min_cart_value, discount_percent, discount_value, active, expires_at)
VALUES ('DESCONTO10', 100.00, 10.00, NULL, 1, '2025-12-31 23:59:59'),
       ('FRETEGRATIS', 50.00, NULL, 20.00, 1, '2025-09-30 23:59:59'),
       ('PROMO25', 200.00, 25.00, NULL, 1, '2025-08-31 23:59:59');
