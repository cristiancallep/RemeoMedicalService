-- Script para limpiar y recargar los datos de prueba con el hash correcto

USE remeo_medical_service;

-- Limpiar datos existentes
DELETE FROM historial WHERE turno_id IN (SELECT id FROM turnos);
DELETE FROM turnos;
DELETE FROM usuarios;
DELETE FROM domicilios;
DELETE FROM reglas_laborales;

-- Cargar datos nuevos con el hash correcto para "demo1234"
INSERT INTO usuarios (nombre, rol, email, password_hash)
VALUES
    ('Coordinador Principal', 'coordinador', 'coordinador@remeo.local', '$2y$10$EHMhRd2QT3xBm.bFEYQmUOLYDRc7mz1nL.43IhLEIpuyGcKXWqwr2'),
    ('Administrador', 'administrador', 'admin@remeo.local', '$2y$10$EHMhRd2QT3xBm.bFEYQmUOLYDRc7mz1nL.43IhLEIpuyGcKXWqwr2'),
    ('Enfermero 001', 'enfermero', 'enfermero1@remeo.local', '$2y$10$EHMhRd2QT3xBm.bFEYQmUOLYDRc7mz1nL.43IhLEIpuyGcKXWqwr2');

INSERT INTO reglas_laborales (horas_maximas, descanso_minimo, activo)
VALUES (8, 12, 1);

INSERT INTO domicilios (direccion, ciudad, provincia, codigo_postal, observaciones)
VALUES
    ('Calle Falsa 123', 'Ciudad Ejemplo', 'Provincia Demo', '1000', 'Domicilio de prueba');

INSERT INTO turnos (fecha, hora_inicio, hora_fin, estado, enfermero_id, domicilio_id, coordinador_id)
VALUES
    ('2026-06-01', '08:00:00', '14:00:00', 'Pendiente', 3, 1, 1);

INSERT INTO historial (turno_id, usuario_id, cambio)
VALUES
    (1, 1, 'Turno creado y asignado a Enfermero 001');
