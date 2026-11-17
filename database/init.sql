-- Base de datos de la tienda (PostgreSQL).
-- Docker crea la BD a partir de POSTGRES_DB (.env); este script crea las tablas y datos de ejemplo.

DROP TABLE IF EXISTS productos;
DROP TABLE IF EXISTS user_roles;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS categorias;

-- ===================== CATEGORÍAS =====================
CREATE TABLE categorias (
    id          UUID PRIMARY KEY,
    nombre      VARCHAR(255) NOT NULL UNIQUE,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    is_deleted  BOOLEAN   NOT NULL DEFAULT FALSE
);

INSERT INTO categorias (id, nombre) VALUES
    ('d69cf3db-b77d-4181-b3cd-5ca8107fb6a9', 'PERIFÉRICOS'),
    ('6dbcbf5e-8e1c-47cc-8578-7b0a33ebc154', 'MONITORES'),
    ('9def16db-362b-44c4-9fc9-77117758b5b0', 'PORTÁTILES');

-- ===================== PRODUCTOS =====================
CREATE TABLE productos (
    id           BIGSERIAL PRIMARY KEY,                -- autoincremento
    uuid         UUID NOT NULL UNIQUE,
    marca        VARCHAR(255) NOT NULL,
    modelo       VARCHAR(255) NOT NULL,
    descripcion  TEXT,
    precio       DOUBLE PRECISION NOT NULL DEFAULT 0.0,
    stock        INTEGER NOT NULL DEFAULT 0,
    imagen       TEXT DEFAULT 'https://placehold.co/150',
    categoria_id UUID NOT NULL REFERENCES categorias(id),
    created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    is_deleted   BOOLEAN   NOT NULL DEFAULT FALSE
);

INSERT INTO productos (uuid, marca, modelo, descripcion, precio, stock, categoria_id) VALUES
    ('19135792-b778-441f-8ef2-4f4c6c4a1b11', 'Logitech', 'MX Keys',      'Teclado inalámbrico retroiluminado', 109.99, 15, 'd69cf3db-b77d-4181-b3cd-5ca8107fb6a9'),
    ('662ed342-de99-45c6-8463-db3f0bd3e2c4', 'Logitech', 'MX Master 3S', 'Ratón ergonómico silencioso',         99.99, 20, 'd69cf3db-b77d-4181-b3cd-5ca8107fb6a9'),
    ('bf6b2c8a-41f4-4d57-a0bc-4c7a5c5a9b21', 'Samsung',  'Odyssey G5',   'Monitor curvo 27" 144 Hz',           279.00,  8, '6dbcbf5e-8e1c-47cc-8578-7b0a33ebc154'),
    ('a1f8e0b7-2b0e-4a1e-9c86-07b4c3c8d2f3', 'LG',       'UltraGear 24', 'Monitor gaming 24" 165 Hz',          189.90, 12, '6dbcbf5e-8e1c-47cc-8578-7b0a33ebc154'),
    ('5e2d1c4b-8f3a-4b7e-a9d0-1c2b3a4d5e6f', 'Lenovo',   'IdeaPad 5',    'Portátil 15" Ryzen 7, 16 GB RAM',    699.00,  5, '9def16db-362b-44c4-9fc9-77117758b5b0');

-- ===================== USUARIOS =====================
CREATE TABLE usuarios (
    id          BIGSERIAL PRIMARY KEY,
    username    VARCHAR(255) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,                -- hash bcrypt (password_hash)
    nombre      VARCHAR(255) NOT NULL,
    apellidos   VARCHAR(255) NOT NULL,
    email       VARCHAR(255) NOT NULL UNIQUE,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    is_deleted  BOOLEAN   NOT NULL DEFAULT FALSE
);

-- Contraseñas: admin -> admin1234 ; user -> user1234
INSERT INTO usuarios (username, password, nombre, apellidos, email) VALUES
    ('admin', '$2y$10$7kbW4ROn4TZ2/QGNZ.Z8EuKZBkLPURWhInjoqJhDfZO9ZiS2qOEcO', 'Admin', 'Tienda',  'admin@tienda.com'),
    ('user',  '$2y$10$B1sxYpqEFEEm2blCaZ5LGekKyCQrdYkiAjsrQPJ9xc9uqeAAeKBrC', 'Usuario', 'Prueba', 'user@tienda.com');

-- ===================== ROLES =====================
CREATE TABLE user_roles (
    user_id  BIGINT NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
    roles    VARCHAR(255) NOT NULL,
    PRIMARY KEY (user_id, roles)
);

INSERT INTO user_roles (user_id, roles) VALUES
    (1, 'ADMIN'),
    (1, 'USER'),
    (2, 'USER');
