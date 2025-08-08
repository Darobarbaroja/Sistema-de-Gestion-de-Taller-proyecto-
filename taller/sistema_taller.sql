-- Crear base de datos
CREATE DATABASE IF NOT EXISTS sistema_taller;
USE sistema_taller;

-- Tabla Roles
CREATE TABLE roles (
    rol_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50) NOT NULL
);

-- Tabla Usuarios
CREATE TABLE usuarios (
    usuario_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50) NOT NULL UNIQUE,
    contrasena VARCHAR(100) NOT NULL,
    rol_id INT,
    FOREIGN KEY (rol_id) REFERENCES roles(rol_id)
);

-- Tabla Proveedores
CREATE TABLE proveedores (
    proveedor_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_proveedor VARCHAR(100) NOT NULL
);

-- Tabla Repuestos
CREATE TABLE repuestos (
    repuesto_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_repuesto VARCHAR(100) NOT NULL,
    precio DECIMAL(10,2) NOT NULL
);

-- Tabla intermedia Proveedores_Repuestos para la relación muchos a muchos
CREATE TABLE proveedores_repuestos (
    proveedor_id INT NOT NULL,
    repuesto_id INT NOT NULL,
    cantidad INT NOT NULL,
    PRIMARY KEY (proveedor_id, repuesto_id),
    FOREIGN KEY (proveedor_id) REFERENCES proveedores(proveedor_id),
    FOREIGN KEY (repuesto_id) REFERENCES repuestos(repuesto_id)
);

-- Tabla Máquinas
CREATE TABLE maquinas (
    maquina_id INT AUTO_INCREMENT PRIMARY KEY,
    numero_interno VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(255) NOT NULL
);

-- Tabla Reparaciones
CREATE TABLE reparaciones (
    reparacion_id INT AUTO_INCREMENT PRIMARY KEY,
    maquina_id INT,
    descripcion_reparacion TEXT NOT NULL,
    horas_trabajadas INT NOT NULL,
    fecha_reparacion DATE NOT NULL,
    FOREIGN KEY (maquina_id) REFERENCES maquinas(maquina_id)
);

-- Tabla intermedia Reparaciones_Operarios (relación entre reparaciones y operarios)
CREATE TABLE reparaciones_operarios (
    reparacion_id INT NOT NULL,
    usuario_id INT NOT NULL,
    PRIMARY KEY (reparacion_id, usuario_id),
    FOREIGN KEY (reparacion_id) REFERENCES reparaciones(reparacion_id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id)
);

-- Tabla intermedia Reparaciones_Repuestos (relación entre reparaciones y repuestos utilizados)
CREATE TABLE reparaciones_repuestos (
    reparacion_id INT NOT NULL,
    repuesto_id INT NOT NULL,
    cantidad INT NOT NULL,
    PRIMARY KEY (reparacion_id, repuesto_id),
    FOREIGN KEY (reparacion_id) REFERENCES reparaciones(reparacion_id),
    FOREIGN KEY (repuesto_id) REFERENCES repuestos(repuesto_id)
);

-- Tabla Historial de Reparaciones
CREATE TABLE historial_reparaciones (
    historial_id INT AUTO_INCREMENT PRIMARY KEY,
    reparacion_id INT,
    usuario_id INT,
    cambio_descripcion TEXT NOT NULL,
    fecha_cambio DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reparacion_id) REFERENCES reparaciones(reparacion_id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id)
);

-- Insertar Roles
INSERT INTO roles (nombre_rol) 
VALUES 
('Superadmin'), 
('EncargadoAdmin'),
('Mecánico');


-- Insertar Usuarios
INSERT INTO usuarios (nombre_usuario, contrasena, rol_id) 
VALUES 
('superAdmin', 'admin123', 1), 
('mecanico', 'mecanico123', 3),
('encargado', 'encargado123', 2);
-- Insertar los usuarios Sánchez, Tarsia y Pereyra
INSERT INTO usuarios (nombre_usuario, contrasena, rol_id)
VALUES 
('sanchez', 'sanchez123', 3),  -- Sánchez como mecánico
('tarsia', 'tarsia123', 3),    -- Tarsia como mecánico
('pereyra', 'pereyra123', 2);  -- Pereyra como encargado


-- Insertar Proveedores
INSERT INTO proveedores (nombre_proveedor) 
VALUES 
('Proveedor A'), 
('Proveedor B'), 
('Proveedor C');

-- Insertar Repuestos
INSERT INTO repuestos (nombre_repuesto, precio) 
VALUES 
('Filtro de aceite', 50.00), 
('Bujía', 20.00), 
('Neumático', 150.00);

-- Relación entre Proveedores y Repuestos (muchos a muchos)
INSERT INTO proveedores_repuestos (proveedor_id, repuesto_id, cantidad) 
VALUES 
(1, 1, 100), 
(2, 2, 200), 
(3, 3, 50);

-- Insertar Máquinas
INSERT INTO maquinas (numero_interno, descripcion) 
VALUES 
('AE123', 'Autoelevador grande'), 
('GC456', 'Grúa Container');

-- Insertar Reparaciones
INSERT INTO reparaciones (maquina_id, descripcion_reparacion, horas_trabajadas, fecha_reparacion) 
VALUES 
(1, 'Cambio de filtro de aceite', 5, '2024-08-01'), 
(2, 'Reparación de transmisión', 8, '2024-08-15');

-- Relación entre Reparaciones y Operarios
INSERT INTO reparaciones_operarios (reparacion_id, usuario_id) 
VALUES 
(1, 2), 
(2, 3);

-- Relación entre Reparaciones y Repuestos
INSERT INTO reparaciones_repuestos (reparacion_id, repuesto_id, cantidad) 
VALUES 
(1, 1, 2), 
(2, 2, 4);

-- Insertar Historial de Reparaciones
INSERT INTO historial_reparaciones (reparacion_id, usuario_id, cambio_descripcion) 
VALUES 
(1, 3, 'Actualización de horas trabajadas en la reparación 1.'),
(2, 1, 'Se corrigió la descripción de la reparación.');