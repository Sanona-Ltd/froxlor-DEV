# Froxlor v3.x — Installation

## Systemvoraussetzungen

- Ubuntu 22.04 / 24.04 oder Debian 12
- PHP 8.4 (wird automatisch als Paketabhängigkeit installiert)
- MySQL 8.0+ / MariaDB 10.6+ / PostgreSQL 14+

---

## Installation

### 1) APT-Repository hinzufügen

```bash
apt -y install apt-transport-https lsb-release ca-certificates curl gnupg

curl -sSLo /usr/share/keyrings/froxlor.gpg \
  https://sanona-ltd.github.io/froxlor-DEV/froxlor.gpg

echo "deb [signed-by=/usr/share/keyrings/froxlor.gpg] \
  https://sanona-ltd.github.io/froxlor-DEV/apt stable main" \
  > /etc/apt/sources.list.d/froxlor.list
```

### 2) System aktualisieren

```bash
apt update && apt upgrade
```

### 3) Froxlor installieren

```bash
apt install froxlor
```

### 4) Setup abschliessen

Nach der Installation den interaktiven Assistenten starten:

```bash
froxlor-setup
```

Der Assistent fragt nach:
- Datenbank-Verbindungsdaten
- App-URL
- Administrator-Name, E-Mail und Passwort

Danach ist das Panel unter der konfigurierten URL erreichbar.

---

## Webserver konfigurieren

Die Beispiel-Konfigurationen befinden sich nach der Installation unter `/etc/froxlor/`.

### Apache

```bash
cp /etc/froxlor/apache.conf.example /etc/apache2/sites-available/froxlor.conf
# ServerName und SSL-Pfade anpassen:
nano /etc/apache2/sites-available/froxlor.conf
a2enmod rewrite proxy_fcgi ssl
a2ensite froxlor
systemctl reload apache2
```

### Nginx

```bash
cp /etc/froxlor/nginx.conf.example /etc/nginx/sites-available/froxlor
# server_name und SSL-Pfade anpassen:
nano /etc/nginx/sites-available/froxlor
ln -s /etc/nginx/sites-available/froxlor /etc/nginx/sites-enabled/froxlor
nginx -t && systemctl reload nginx
```

---

## Update

```bash
apt update && apt upgrade froxlor
```

---

## Deinstallation

```bash
apt remove froxlor
# Inklusive Konfiguration:
apt purge froxlor
```

> **Hinweis:** Die Datei `/var/www/froxlor/.env` und das `storage/`-Verzeichnis (inkl. Datenbank-Daten) werden bei einer Deinstallation nicht automatisch gelöscht.

---

## Für Entwickler — Manuelle Installation

Für die Entwicklung ohne APT-Paket, siehe [CONTRIBUTING.md](CONTRIBUTING.md) oder direkt:

```bash
git clone https://github.com/Sanona-Ltd/froxlor-DEV /var/www/froxlor
cd /var/www/froxlor
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
# .env konfigurieren, dann:
php artisan migrate
php artisan froxlor:create-admin
```
