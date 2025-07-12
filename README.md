# Polypass

Credentials keeper.

## Two-Factor Authentication Configuration

To disable two-factor authentication in local environment, add the following line to your `.env` file:

```
2FA_ENABLED=false
```

By default, 2FA is enabled (`2FA_ENABLED=true`). Set it to `false` to skip 2FA verification in local development.

## Installation

1. Clone this package to your server:
```bash
cd /path_to_project_root_folder
git clone https://github.com/polyspirit/polypass.git .
```
2. Do `composer install`.
3. Copy **.env.example**, rename it to **.env** and modify as you need.
4. Make alias for sail:
```bash
alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'
```
5. Up docker containers
```bash
sail up -d
```
6. Do migrations:
```bash
sail artisan migrate --seed
```
Seeder will create all roles, root group and superadmin user with:
- Login: admin@example.com
- Password: qwe123

You can change it later.

7. Go to your APP_URL (defined in .env file).
8. Use as you want!