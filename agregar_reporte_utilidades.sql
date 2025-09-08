-- Agrega la opción "Reporte Utilidades" al menú para todos los perfiles que ya tienen acceso a la categoría Reporte

-- 1. Agregar la opción al menú (tabla opcion)
INSERT INTO `opcion` (`idopcion`, `descripcion`, `icono`, `url`, `idopcion_ref`, `estado`) VALUES
(15, 'Reporte Utilidades', 'fa-coins', 'vista/reporte_utilidad_producto.php', 12, 1);

-- 2. Dar acceso a todos los perfiles que ya tienen acceso a la categoría Reporte (idopcion_ref = 12)
INSERT INTO acceso (idperfil, idopcion, estado)
SELECT idperfil, 15, 1 FROM acceso WHERE idopcion=8 AND estado=1;
-- (Esto copia el acceso de "Reportes Ventas" a "Reporte Utilidades" para los mismos perfiles)
