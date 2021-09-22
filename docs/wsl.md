Чтобы избежать проблем с резолвингом в docker + WSL,
нужно добавить в конфиг /etc/wsl.conf:
[network]
generateResolvConf = false

Так в /etc/resolv.conf не будет добавляться nameserver,
который недоступен для docker
