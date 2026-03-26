# Seguridad del Servidor contra DDoS y Ataques Comunes

## 1️⃣ Deshabilitar IPv6 de forma temporal

```bash
sudo sysctl -w net.ipv6.conf.all.disable_ipv6=1
sudo sysctl -w net.ipv6.conf.default.disable_ipv6=1
sudo sysctl -w net.ipv6.conf.lo.disable_ipv6=1
```

---

## 2️⃣ SYN cookies y protección del TCP/IP

Los ataques SYN flood consisten en que alguien envía muchísimas solicitudes de conexión a tu servidor, pero no termina la conexión. Esto puede saturar tu servidor y hacerlo caer.

- `net.ipv4.tcp_syncookies=1` → Activa SYN cookies.
- `net.ipv4.tcp_fin_timeout=15` → Reduce el tiempo que las conexiones “medio abiertas” se quedan ocupando memoria.
- `net.ipv4.tcp_max_syn_backlog=2048` → Aumenta la cantidad de conexiones pendientes.
- `net.ipv4.tcp_synack_retries=2` → Limita cuántas veces el servidor intenta completar una conexión que nunca respondió.

Ejecuta estos comandos:

```bash
sudo sysctl -w net.ipv4.tcp_syncookies=1
sudo sysctl -w net.ipv4.tcp_fin_timeout=15
sudo sysctl -w net.ipv4.tcp_max_syn_backlog=2048
sudo sysctl -w net.ipv4.tcp_synack_retries=2
```

Para mantenerlo tras reiniciar, añade al final de `/etc/sysctl.conf`:

```conf
net.ipv4.tcp_syncookies = 1
net.ipv4.tcp_fin_timeout = 15
net.ipv4.tcp_max_syn_backlog = 2048
net.ipv4.tcp_synack_retries = 2
```

---

## 3️⃣ Configurar iptables para limitar conexiones

### Limitar conexiones HTTP/HTTPS (Apache2)

Limita conexiones simultáneas desde una misma IP a 10:

```bash
sudo iptables -A INPUT -p tcp --dport 80 -m connlimit --connlimit-above 10 -j REJECT
sudo iptables -A INPUT -p tcp --dport 443 -m connlimit --connlimit-above 10 -j REJECT
```

### Limitar SSH

Limita intentos de conexión SSH a 5 por minuto por IP:

```bash
sudo iptables -A INPUT -p tcp --dport 22 -m conntrack --ctstate NEW -m recent --set
sudo iptables -A INPUT -p tcp --dport 22 -m conntrack --ctstate NEW -m recent --update --seconds 60 --hitcount 5 -j DROP
```

### Bloquear tráfico sospechoso

Limitar pings a 1 por segundo y bloquear IPs que hagan más de 20 pings en 10 segundos:

```bash
sudo iptables -A INPUT -p icmp --icmp-type echo-request -m limit --limit 1/s -j ACCEPT
sudo iptables -A INPUT -p icmp --icmp-type echo-request -m recent --set --name PINGERS --rsource
sudo iptables -A INPUT -p icmp --icmp-type echo-request -m recent --update --seconds 10 --hitcount 20 --name PINGERS --rsource -j DROP
```

Bloquear paquetes TCP inválidos:

```bash
sudo iptables -A INPUT -m conntrack --ctstate INVALID -j DROP
```

Guardar las reglas de iptables permanentemente:

```bash
sudo netfilter-persistent save
```

---

## 4️⃣ Instalar y configurar Fail2Ban (protege SSH)

Instalación:

```bash
sudo apt update
sudo apt install -y fail2ban
```

Configurar `/etc/fail2ban/jail.local`:

```conf
[DEFAULT]
bantime = 600
findtime = 600
maxretry = 10
banaction = iptables-multiport

[sshd]
enabled = true
port = ssh
logpath = /var/log/auth.log
```

Activar y reiniciar fail2ban:

```bash
sudo systemctl enable fail2ban
sudo systemctl restart fail2ban
```

---

## 5️⃣ Mod_evasive para Apache2

### 1️⃣ Instalar mod_evasive

```bash
sudo apt update
sudo apt install libapache2-mod-evasive -y
```

### 2️⃣ Crear carpeta de logs

```bash
sudo mkdir /var/log/mod_evasive
sudo chown www-data:www-data /var/log/mod_evasive
sudo chmod 750 /var/log/mod_evasive
```

### 3️⃣ Configuración básica

Editar `/etc/apache2/mods-available/mod-evasive.conf`:

```apache
<IfModule mod_evasive20.c>
    DOSHashTableSize    3097
    DOSPageCount        10       # Máximo de requests a la misma página por segundo
    DOSSiteCount        50       # Máximo de requests al sitio completo por segundo
    DOSPageInterval     1        # Intervalo de tiempo en segundos
    DOSSiteInterval     1        # Intervalo de tiempo en segundos
    DOSBlockingPeriod   600      # Bloquear IP durante 10 minutos
    DOSEmailNotify      tuemail@dominio.com  # Opcional, para recibir alertas
    DOSLogDir           "/var/log/mod_evasive"  # Carpeta de logs
</IfModule>
```

Activar módulo y reiniciar Apache:

```bash
sudo a2enmod evasive
sudo systemctl restart apache2
```

---

### 🔹 Referencias

- [Configuración mod_evasive](https://juantrucupei.wordpress.com/2016/09/07/instalacion-y-configuracion-de-modulo-mod_evasive-servidor-web-apache/)

