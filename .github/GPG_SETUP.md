# GPG-Schlüssel für das APT-Repository einrichten

Dieser Schritt muss **einmalig** vom Repository-Maintainer durchgeführt werden.

## 1. GPG-Schlüssel generieren (lokal)

```bash
gpg --batch --generate-key <<EOF
%no-protection
Key-Type: RSA
Key-Length: 4096
Subkey-Type: RSA
Subkey-Length: 4096
Name-Real: Froxlor by Sanona Ltd
Name-Email: packages@sanona.dev
Expire-Date: 0
EOF
```

## 2. Key-ID ermitteln

```bash
gpg --list-secret-keys --keyid-format LONG packages@sanona.dev
# Ausgabe z.B.:
# sec   rsa4096/ABCDEF1234567890 2024-01-01 [SC]
#                ^^^^^^^^^^^^^^^^ ← das ist die Key-ID
```

## 3. Privaten Schlüssel exportieren

```bash
gpg --armor --export-secret-keys ABCDEF1234567890
```

Den gesamten Ausgabe-Block (inkl. `-----BEGIN PGP PRIVATE KEY BLOCK-----`) kopieren.

## 4. GitHub Secrets setzen

Im GitHub Repository → **Settings → Secrets and variables → Actions → New repository secret**:

| Secret-Name       | Wert                                    |
|-------------------|-----------------------------------------|
| `GPG_PRIVATE_KEY` | Privater Schlüssel (kompletter Block)   |

## 5. GitHub Pages aktivieren

Im GitHub Repository → **Settings → Pages**:
- Source: **GitHub Actions**

## 6. Workflow starten

Entweder:
- Einen Push auf `main` machen, oder
- Im GitHub Repository → **Actions → Build & Publish APT Package → Run workflow**

Nach dem ersten erfolgreichen Durchlauf ist das APT-Repository unter  
`https://sanona-ltd.github.io/froxlor-DEV/` erreichbar.
