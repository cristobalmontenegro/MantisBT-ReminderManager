# MantisBT Reminder Manager (English / Español)

**Reminder Manager** is a powerful automation plugin for **MantisBT 2.22+** designed to streamline case follow-ups based on custom date fields.

**Reminder Manager** es un potente plugin de automatización para **MantisBT 2.22+** diseñado para optimizar el seguimiento de los casos basándose en campos personalizados de fecha.

---

## 🇺🇸 Features (English)

*   **Custom Date Field Triggers:** Link reminders to any existing date-type custom field.
*   **Fully Customizable Templates:** Define unique email subjects and bodies for each rule using dynamic placeholders:
    *   `[[id]]`, `[[summary]]`, `[[status]]`, `[[handler]]`, `[[reporter]]`, `[[link]]`.
    *   Support for other custom fields using `[[custom_field_X]]`.
*   **Flexible Recipient Selection:** Notify Reporter, Handler, and/or Monitors individually.
*   **Audit Logs:** Track every sent notification with a dedicated logging system within MantisBT.
*   **Security First:** Protected via security token for cron execution and strict input validation.

## 🇪🇸 Características (Español)

*   **Activación por Campos Personalizados:** Vincula recordatorios automáticos a cualquier campo de tipo fecha.
*   **Plantillas 100% Personalizables:** Define asuntos y cuerpos de correo únicos con placeholders dinámicos:
    *   `[[id]]`, `[[summary]]`, `[[status]]`, `[[handler]]`, `[[reporter]]`, `[[link]]`.
    *   Soporte para otros campos personalizados mediante `[[custom_field_X]]`.
*   **Selección Flexible de Destinatarios:** Notifica al Informador, Responsable y/o Monitores de forma independiente.
*   **Bitácora de Auditoría:** Seguimiento de cada notificación enviada con un sistema de logs integrado.
*   **Seguridad:** Protegido mediante un token de ejecución para el cron y validación estricta de datos.

---

## ⚙️ Installation / Instalación

1.  Download and move the `ReminderManager` folder to your MantisBT `plugins/` directory. / Descarga y mueve la carpeta `ReminderManager` al directorio `plugins/` de tu MantisBT.
2.  Go to **Manage > Manage Plugins** and click **Install**. / Ve a **Administrar > Administrar Complementos** y haz clic en **Instalar**.
3.  Configure your rules in the plugin's configuration page. / Configura tus reglas en la página de configuración del plugin.

## ⏲️ Automation / Automatización

To enable automated reminders, add the following entry to your server's **crontab**: / Para habilitar los recordatorios automáticos, añade la siguiente entrada al **crontab** de tu servidor:

```bash
*/15 * * * * /usr/bin/php /path/to/mantis/plugins/ReminderManager/scripts/process.php
```

---
*Developed by [Cristobal Montenegro](https://github.com/cristobalmontenegro)*
