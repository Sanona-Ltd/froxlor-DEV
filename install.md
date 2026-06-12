# Froxlor v3.x — Installation

## Systemvoraussetzungen

- Ubuntu 22.04 / 24.04 oder Debian 12
- PHP 8.4 (wird automatisch installiert)
- MySQL 8.0+ / MariaDB 10.6+ / PostgreSQL 14+

---

## Methode A — Direktinstaller (sofort, kein APT-Repo nötig)

```bash
curl -sSL https://raw.githubusercontent.com/Sanona-Ltd/froxlor-DEV/main/install.sh | sudo bash
```

Danach:

```bash
sudo froxlor-setup
```

---

## Methode B — APT-Repository (empfohlen, sobald Workflow einmalig gelaufen ist)

> **Hinweis:** Das APT-Repository wird durch den GitHub Actions Workflow automatisch befüllt.
> Voraussetzung: GPG-Key als GitHub Secret hinterlegt + GitHub Pages aktiviert.
> Siehe [.github/GPG_SETUP.md](.github/GPG_SETUP.md)

### 1) APT-Repository hinzufügen

```bash
sudo apt -y install apt-transport-https lsb-release ca-certificates curl gnupg

sudo curl -sSLo /usr/share/keyrings/froxlor.gpg \
  https://sanona-ltd.github.io/froxlor-DEV/froxlor.gpg

echo "deb [signed-by=/usr/share/keyrings/froxlor.gpg] \
  https://sanona-ltd.github.io/froxlor-DEV/apt stable main" \
  | sudo tee /etc/apt/sources.list.d/froxlor.list
```

### 2) System aktualisieren

```bash
sudo apt update && sudo apt upgrade
```

### 3) Froxlor installieren

```bash
sudo apt install froxlor
```

### 4) Setup abschliessen

Nach der Installation den interaktiven Assistenten starten:

```bash
sudo froxlor-setup
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
sudo cp /etc/froxlor/apache.conf.example /etc/apache2/sites-available/froxlor.conf
# ServerName und SSL-Pfade anpassen:
sudo nano /etc/apache2/sites-available/froxlor.conf
sudo a2enmod rewrite proxy_fcgi ssl
sudo a2ensite froxlor
sudo systemctl reload apache2
```

### Nginx

```bash
sudo cp /etc/froxlor/nginx.conf.example /etc/nginx/sites-available/froxlor
# server_name und SSL-Pfade anpassen:
sudo nano /etc/nginx/sites-available/froxlor
sudo ln -s /etc/nginx/sites-available/froxlor /etc/nginx/sites-enabled/froxlor
sudo nginx -t && sudo systemctl reload nginx
```

---

## Update

```bash
sudo apt update && sudo apt upgrade froxlor
```

---

## Deinstallation

```bash
sudo apt remove froxlor
# Inklusive Konfiguration:
sudo apt purge froxlor
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
