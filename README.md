# Sistema de Gestión de Incidencias - UAGRM

Proyecto desarrollado para la materia de **Arquitectura de Software** (Ingeniería de Sistemas). Este sistema optimiza el reporte y mantenimiento de la infraestructura universitaria mediante una plataforma centralizada.

## Arquitectura del Sistema
El software está construido bajo el patrón de diseño **MVC (Modelo-Vista-Controlador)**, garantizando una separación clara de responsabilidades:
- **Modelos:** Gestión de datos y lógica de negocio con MySQLi.
- **Vistas:** Interfaces dinámicas desarrolladas en PHTML y CSS institucional.
- **Controladores:** Orquestación de peticiones y flujo de la aplicación.

## Características Implementadas
- **Reporte Público:** Los estudiantes pueden registrar daños subiendo evidencia fotográfica y seleccionando categorías técnicas.
- **Dashboard Administrativo:** Panel de control para el monitoreo en tiempo real de todos los predios.
- **Gestión Inteligente (CU3):** Asignación de técnicos basada en su especialidad y ubicación geográfica.
- **Seguridad y Perfiles:** Autenticación robusta con hasheo de contraseñas y control de acceso por roles.
- **Reportes Institucionales:** Generación automática de documentos PDF para auditoría interna.

## Stack Tecnológico
- **Lenguaje:** PHP.
- **Base de Datos:** MySQL.
- **Reportes:** Dompdf Library.
- **Frontend:** Vanilla JavaScript y CSS3.

---
© 2026 - Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones