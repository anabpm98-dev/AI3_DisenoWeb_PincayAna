-- Ajuste de la tabla para la resolución
USE db_sistema_web;

ALTER TABLE solicitudes
    MODIFY estado ENUM('Pendiente', 'En proceso', 'Finalizada', 'Rechazada') NOT NULL DEFAULT 'Pendiente',
    ADD COLUMN comentario_resolucion VARCHAR(500) NULL AFTER fecha_creacion,
    ADD COLUMN fecha_resolucion TIMESTAMP NULL DEFAULT NULL AFTER comentario_resolucion;