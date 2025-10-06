# 🎟️ Muni Tickets

Sistema de Mesa de Ayuda y Helpdesk desarrollado para la **Municipalidad de San Juan de Miraflores**, con el objetivo de gestionar incidencias, requerimientos y solicitudes internas de soporte técnico.

---

## 🚀 Tecnologías utilizadas
- **Laravel 10** – Framework principal del backend  
- **MySQL** – Base de datos relacional  
- **PHP 8.1+**  
- **Docker / Laragon** – Entorno de desarrollo local  
- **Git & GitHub** – Control de versiones  
- **Bootstrap / TailwindCSS (opcional)** – Diseño frontend  

---
2️⃣ Instalar dependencias
composer install
npm install && npm run dev

3️⃣ Configurar entorno

Copia el archivo .env.example y crea tu entorno local:

cp .env.example .env


Genera la key de la aplicación:

php artisan key:generate

4️⃣ Configurar base de datos

Edita el archivo .env con tus credenciales MySQL, por ejemplo:

DB_DATABASE=muni_tickets
DB_USERNAME=root
DB_PASSWORD=


Luego ejecuta las migraciones:

php artisan migrate

5️⃣ Iniciar servidor
php artisan serve


Accede desde tu navegador a:

http://127.0.0.1:8000
