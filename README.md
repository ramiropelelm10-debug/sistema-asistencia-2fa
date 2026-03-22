# API de Servicios 2FA (Autenticación de Dos Factores)

Este proyecto es una API RESTful construida con Laravel para gestionar autenticación de dos factores (2FA) en aplicaciones. Incluye funcionalidades como envío de OTP por email, verificación de dispositivos confiables y gestión de servicios.

## Requisitos

- PHP 8.1 o superior
- Composer
- Node.js y npm (para assets frontend, opcional)
- Base de datos (MySQL, PostgreSQL, SQLite, etc.)

## Instalación

1. **Clona el repositorio:**
   ```bash
   git clone https://github.com/ramiropelelm10-debug/sistema-asistencia-2fa.git
   cd api-2fa-services
   ```

2. **Instala las dependencias de PHP:**
   ```bash
   composer install
   ```

3. **Instala las dependencias de Node.js (opcional, para frontend):**
   ```bash
   npm install
   npm run build
   ```

4. **Configura el archivo de entorno:**
   - Copia el archivo `.env.example` a `.env`:
     ```bash
     cp .env.example .env
     ```
   - Edita `.env` y configura:
     - Conexión a la base de datos (DB_HOST, DB_DATABASE, etc.)
     - Configuración de email (MAIL_MAILER, MAIL_HOST, etc.)
     - Clave de aplicación: `php artisan key:generate`

5. **Ejecuta las migraciones de la base de datos:**
   ```bash
   php artisan migrate
   ```

6. **Ejecuta el servidor:**
   ```bash
   php artisan serve
   ```
   La API estará disponible en `http://localhost:8000`.

## Uso

- Registra usuarios y servicios.
- Envía OTP por email para verificación.
- Verifica códigos OTP y administra dispositivos confiables.

Consulta la documentación de la API en las rutas definidas en `routes/api.php`.

## Contribución

1. Haz un fork del proyecto.
2. Crea una rama para tu feature: `git checkout -b feature/nueva-funcionalidad`.
3. Commit tus cambios: `git commit -m 'Agrega nueva funcionalidad'`.
4. Push a la rama: `git push origin feature/nueva-funcionalidad`.
5. Abre un Pull Request.

## Licencia

Este proyecto está bajo la Licencia MIT.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
