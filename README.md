# Polypass
This is laravel 9.11 based credentials keeper. You can install it and keep your credentials, passwords and remote data.

## Alpha version warning!
The project is still in development. 

**Be careful**, you assume all responsibility for data storage!

## How to install
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