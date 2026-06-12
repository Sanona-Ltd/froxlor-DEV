#!/usr/bin/env bash
# Froxlor v3.x — Direktinstaller
# Verwendung: curl -sSL https://raw.githubusercontent.com/Sanona-Ltd/froxlor-DEV/main/install.sh | sudo bash

set -euo pipefail

FROXLOR_DIR="/var/www/froxlor"
REPO_URL="https://github.com/Sanona-Ltd/froxlor-DEV.git"
GREEN='\033[0;32m'
CYAN='\033[0;36m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

step() { echo -e "\n${CYAN}▶ $1${NC}"; }
ok()   { echo -e "${GREEN}✓ $1${NC}"; }
warn() { echo -e "${YELLOW}! $1${NC}"; }
fail() { echo -e "${RED}✗ $1${NC}"; exit 1; }

# Root-Check
if [ "$(id -u)" != "0" ]; then
    fail "Dieses Skript muss als root ausgeführt werden. Nutze: sudo bash install.sh"
fi

# Alten APT-Eintrag entfernen (von einem früheren Installationsversuch)
if [ -f /etc/apt/sources.list.d/froxlor.list ]; then
    rm -f /etc/apt/sources.list.d/froxlor.list
    rm -f /usr/share/keyrings/froxlor.gpg
    warn "Alter froxlor APT-Eintrag entfernt."
fi

echo ""
echo -e "${CYAN}╔══════════════════════════════════════════╗${NC}"
echo -e "${CYAN}║   Froxlor v3.x — Installations-Assistent ║${NC}"
echo -e "${CYAN}╚══════════════════════════════════════════╝${NC}"
echo ""

# --- PHP 8.4 über ondrej/php PPA ---
step "PHP 8.4 Repository hinzufügen..."
apt -y install software-properties-common > /dev/null 2>&1
add-apt-repository -y ppa:ondrej/php > /dev/null 2>&1
ok "PHP 8.4 PPA hinzugefügt"

# --- Node.js 22 über NodeSource ---
step "Node.js 22 Repository hinzufügen..."
curl -fsSL https://deb.nodesource.com/setup_22.x | bash - > /dev/null 2>&1
ok "Node.js 22 Repository hinzugefügt"

# --- apt update (einmalig nach beiden PPAs) ---
apt update -q 2>&1 | grep -v "^$" || true

# --- PHP + System-Abhängigkeiten ---
step "PHP 8.4 und Abhängigkeiten installieren..."
apt -y install \
    git curl \
    php8.4-cli php8.4-fpm \
    php8.4-mysql php8.4-pgsql php8.4-sqlite3 \
    php8.4-mbstring php8.4-xml \
    php8.4-curl php8.4-zip \
    php8.4-bcmath php8.4-tokenizer \
    php8.4-intl php8.4-readline \
    composer \
    > /dev/null 2>&1
ok "PHP 8.4 installiert"

# --- Node.js 22 ---
step "Node.js 22 installieren..."
apt -y install nodejs > /dev/null 2>&1
NODE_VER=$(node --version)
ok "Node.js ${NODE_VER} installiert"

# --- Froxlor herunterladen ---
step "Froxlor herunterladen..."
if [ -d "$FROXLOR_DIR/.git" ]; then
    warn "Vorhandenes Verzeichnis gefunden — führe git pull aus..."
    git -C "$FROXLOR_DIR" pull --quiet
else
    git clone --depth=1 "$REPO_URL" "$FROXLOR_DIR" --quiet
fi
ok "Quellcode heruntergeladen"

cd "$FROXLOR_DIR"

# --- Composer (als root, chown kommt danach) ---
step "PHP-Abhängigkeiten installieren (composer install)..."
COMPOSER_ALLOW_SUPERUSER=1 composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --quiet
ok "PHP-Abhängigkeiten installiert"

# --- Frontend-Assets (als root, chown kommt danach) ---
step "Frontend-Assets bauen (npm run build)..."
npm install --silent
npm run build --silent
ok "Frontend-Assets gebaut"

# --- .env einrichten ---
step ".env konfigurieren..."
if [ ! -f ".env" ]; then
    cp .env.example .env
fi
php artisan key:generate --force --quiet
ok ".env konfiguriert"

# --- Dateiberechtigungen (nach dem Build setzen) ---
step "Dateiberechtigungen setzen..."
chown -R www-data:www-data "$FROXLOR_DIR"
chmod -R 755 "$FROXLOR_DIR"
chmod -R 775 "$FROXLOR_DIR/storage"
chmod -R 775 "$FROXLOR_DIR/bootstrap/cache"
ok "Berechtigungen gesetzt"

# --- systemd Queue Worker ---
step "Queue Worker (systemd) einrichten..."
cp "$FROXLOR_DIR/debian/froxlor-queue.service" /lib/systemd/system/froxlor-queue.service
systemctl daemon-reload
systemctl enable froxlor-queue.service
ok "Queue Worker eingerichtet"

# --- Webserver-Beispielkonfigs ---
step "Webserver-Vorlagen installieren..."
mkdir -p /etc/froxlor
cp "$FROXLOR_DIR/debian/apache.conf.example" /etc/froxlor/
cp "$FROXLOR_DIR/debian/nginx.conf.example"  /etc/froxlor/
ok "Vorlagen unter /etc/froxlor/ verfügbar"

# --- Setup-Skript ---
step "froxlor-setup Skript installieren..."
install -m 755 "$FROXLOR_DIR/debian/froxlor-setup" /usr/bin/froxlor-setup
ok "froxlor-setup installiert"

# --- Cron Job ---
step "Cron Job einrichten..."
(crontab -u www-data -l 2>/dev/null | grep -v 'froxlor'; \
 echo "* * * * * cd $FROXLOR_DIR && php artisan schedule:run >> /dev/null 2>&1") \
 | crontab -u www-data -
ok "Cron Job eingerichtet"

# --- Abschluss ---
echo ""
echo -e "${GREEN}╔══════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║    Froxlor wurde erfolgreich installiert! ║${NC}"
echo -e "${GREEN}╚══════════════════════════════════════════╝${NC}"
echo ""
echo -e "  Nächste Schritte:"
echo ""
echo -e "  ${YELLOW}1.${NC} Datenbank & Admin konfigurieren:"
echo "     sudo froxlor-setup"
echo ""
echo -e "  ${YELLOW}2.${NC} Webserver einrichten:"
echo "     Apache: sudo cp /etc/froxlor/apache.conf.example /etc/apache2/sites-available/froxlor.conf"
echo "     Nginx:  sudo cp /etc/froxlor/nginx.conf.example /etc/nginx/sites-available/froxlor"
echo ""
