# MantisBT Reminder Manager (English / Español)

**Reminder Manager** is a high-performance automation plugin for **MantisBT 2.22+** (optimized for 2.28+) designed to eliminate the manual overhead of following up on deadlines and scheduled activities within your bug tracker.

---

# 🇺🇸 English Documentation

## 📖 Overview
In professional issue tracking, many tasks are tied to specific dates (Audit deadlines, promised delivery dates, follow-up meetings). Standard MantisBT requires manual checks to see which dates are approaching. 

**Reminder Manager** bridges this gap by allowing administrators to create dynamic rules that monitor **Custom Date Fields**. When a scheduled date is reached, the plugin automatically triggers personalized email notifications to the relevant stakeholders and performs a cleanup action to finalize the workflow.

## 🚀 Key Features

*   **Dynamic Rule Engine:** Create multiple independent rules. Monitor one field for "Audits" and another for "Due Dates" simultaneously, each with its own logic.
*   **100% Customizable Templates:** Use a rich template editor for both **Email Subject** and **Email Body**.
*   **Smart Placeholders:** Inyect real-time case data into your emails:
    *   `[[id]]`: Issue ID.
    *   `[[summary]]`: Issue title/summary.
    *   `[[status]]`: Current status (e.g., Assigned, Resolved).
    *   `[[handler]]`: Name of the person in charge.
    *   `[[reporter]]`: Name of the person who reported the issue.
    *   `[[link]]`: Direct clickable URL to the MantisBT issue.
    *   `[[custom_field_X]]`: Dynamically pull values from *other* custom fields to provide full context.
*   **Granular Recipient Control:** Choose exactly who gets notified for each rule:
    *   The **Reporter** (to keep them informed).
    *   The **Handler** (to remind them of the task).
    *   **Monitors** (to keep supervisors in the loop).
*   **Automated Workflow Cleanup:** Once a notification is successfully sent, the plugin **clears the trigger date field**. This prevents notification loops and signals that the reminder has been processed.
*   **Audit Logging:** Every automated email is recorded in a dedicated log table within MantisBT, allowing administrators to verify when and to whom notifications were sent.
*   **Enterprise Security:** 
    *   Access restricted to users with direct administrative privileges.
    *   Cron execution protected via a unique **Security Token**.
    *   Native integration with MantisBT API (no direct SQL hacks).

## 🛠️ The Documentation & Lifecycle

### How it works:
1.  **Define a Rule:** Select a Custom Field (Type: Date) and set a preferred execution time (e.g., 09:00 AM).
2.  **Set the Trigger:** A user sets a date in that custom field within a MantisBT issue.
3.  **The Cron Job:** Your server's cron executes the processing script.
4.  **Notification:** If `current_time >= scheduled_time` and `current_date >= field_date`, the email is sent according to your template.
5.  **Completion:** The custom field value is deleted, and a log entry is created.

## ⚙️ Installation

1.  Download the repository and move the `ReminderManager` folder to your MantisBT `plugins/` directory.
2.  Navigate to **Manage > Manage Plugins**.
3.  Find **Reminder Manager** and click **Install**.
4.  Click on the plugin name to access the configuration dashboard.

## ⏲️ Automation (Cron Setup)

Add the following entry to your server's **crontab** (running every 15 minutes is recommended):

```bash
*/15 * * * * /usr/bin/php /path/to/mantis/plugins/ReminderManager/scripts/process.php
```

---

# 🇪🇸 Documentación en Español

## 📖 Descripción General
En la gestión profesional de incidencias, muchas tareas están ligadas a fechas específicas (vencimientos de auditoría, fechas de entrega, reuniones de seguimiento). El flujo estándar de MantisBT requiere revisiones manuales para saber qué fechas están próximas.

**Reminder Manager** soluciona esto permitiendo a los administradores crear reglas dinámicas que monitorean **Campos Personalizados de Fecha**. Cuando se alcanza la fecha programada, el plugin dispara automáticamente notificaciones por correo personalizadas y realiza una acción de limpieza para finalizar el flujo.

## 🚀 Características Principales

*   **Motor de Reglas Dinámico:** Crea múltiples reglas independientes. Monitorea un campo para "Auditorías" y otro para "Vencimientos" simultáneamente, cada uno con su propia lógica.
*   **Plantillas 100% Personalizables:** Editor completo para el **Asunto** y el **Cuerpo** del correo.
*   **Placeholders Inteligentes:** Inyecta datos del caso en tiempo real en tus correos:
    *   `[[id]]`: ID del caso.
    *   `[[summary]]`: Título/resumen del caso.
    *   `[[status]]`: Estado actual.
    *   `[[handler]]`: Nombre del responsable asignado.
    *   `[[reporter]]`: Nombre de quien reportó el caso.
    *   `[[link]]`: URL directa al caso en MantisBT.
    *   `[[custom_field_X]]`: Extrae valores de *otros* campos personalizados para dar contexto total.
*   **Control Granular de Destinatarios:** Elige exactamente a quién notificar en cada regla:
    *   Al **Informador** (para mantenerlo al tanto).
    *   Al **Responsable** (como recordatorio de tarea).
    *   A los **Monitores** (para supervisión).
*   **Limpieza Automática de Flujo:** Una vez enviado el correo con éxito, el plugin **borra el valor del campo fecha**. Esto evita bucles de notificación y señala que el recordatorio ya fue procesado.
*   **Bitácora de Auditoría (Logs):** Cada envío queda registrado en una tabla de logs dentro de MantisBT, permitiendo verificar cuándo y a quién se le notificó.
*   **Seguridad Empresarial:**
    *   Acceso restringido a administradores.
    *   Ejecución de Cron protegida por un **Token de Seguridad** único.
    *   Integración nativa con el API de MantisBT (sin manipulaciones directas de SQL).

## 🛠️ Ciclo de Vida del Recordatorio

### Cómo funciona:
1.  **Definir Regla:** Selecciona un Campo Personalizado (Tipo: Fecha) y una hora de ejecución (ej. 09:00 AM).
2.  **Activar:** Un usuario asigna una fecha en ese campo dentro de una incidencia de MantisBT.
3.  **El Cron:** El cron de tu servidor ejecuta el script de procesamiento.
4.  **Notificación:** Si `hora_actual >= hora_programada` y `fecha_actual >= fecha_campo`, se envía el correo según tu plantilla.
5.  **Finalización:** Se borra el valor del campo personalizado y se genera un log.

## ⚙️ Instalación

1.  Descarga el repositorio y mueve la carpeta `ReminderManager` al directorio `plugins/` de tu MantisBT.
2.  Ve a **Administrar > Administrar Complementos**.
3.  Busca **Reminder Manager** y haz clic en **Instalar**.
4.  Haz clic en el nombre del plugin para acceder al panel de configuración.

## ⏲️ Automatización (Configuración del Cron)

Añade la siguiente entrada al **crontab** de tu servidor (se recomienda cada 15 minutos):

```bash
*/15 * * * * /usr/bin/php /path/to/mantis/plugins/ReminderManager/scripts/process.php
```

---
*Developed by [Cristobal Montenegro](https://github.com/cristobalmontenegro)*
