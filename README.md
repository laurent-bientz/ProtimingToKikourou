# Protiming To Kikourou

*Hardcoded for personal use.*

`PHP-CLI` tool to export as `CSV` the results of a race on [protiming.fr](https://protiming.fr/results/runningR/5096/6) to be imported on [kikourou.net](http://www.kikourou.net/aide/resultats.gestion.php)

## Usage

### Install

```bash
composer install
```

### Run

```bash
php index.php --url=https://protiming.fr/results/runningR/5096/6
```

### CSV

CSV files are stored in `out` directory.