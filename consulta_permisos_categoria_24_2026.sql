SELECT 
    e.id AS empleado_id,
    e.nombre AS empleado_nombre,
    e.oni AS empleado_oni,
    tp.nombre AS tipo_permiso,
    COUNT(p.id) AS cantidad_permisos,
    SUM(CASE WHEN DATENAME(weekday, p.desde) IN ('Saturday', 'Sábado') 
              OR DATENAME(weekday, p.hasta) IN ('Saturday', 'Sábado') THEN 1 ELSE 0 END) AS cae_sabado,
    SUM(CASE WHEN DATENAME(weekday, p.desde) IN ('Sunday', 'Domingo') 
              OR DATENAME(weekday, p.hasta) IN ('Sunday', 'Domingo') THEN 1 ELSE 0 END) AS cae_domingo,
    SUM(CASE WHEN DATENAME(weekday, p.desde) IN ('Saturday', 'Sábado', 'Sunday', 'Domingo')
              OR DATENAME(weekday, p.hasta) IN ('Saturday', 'Sábado', 'Sunday', 'Domingo') THEN 1 ELSE 0 END) AS cae_fin_de_semana
FROM 
    empleados e
    INNER JOIN permisos p ON e.id = p.empleado_id
    INNER JOIN tipo_permisos tp ON p.tipo_permiso_id = tp.id
WHERE 
    e.categoria_id = 24
    AND YEAR(p.fecha_creacion) = 2026
GROUP BY 
    e.id,
    e.nombre,
    e.oni,
    tp.nombre
ORDER BY 
    e.nombre,
    tp.nombre;
