#!/usr/bin/env python3
"""Genera sql/champions_import.sql con i campioni dai dati statici di Riot (Data Dragon).

ATTENZIONE: script NON TESTATO. Controllare l'output prima di usarlo.
- Non va caricato sul server del corso: serve solo in locale.
- Data Dragon NON contiene i ruoli di corsia (Top, Jungle, Mid, Bot, Support): quelli
  vanno assegnati da admin_champions.php (tabella champion_roles).
- Opzione --images: scarica anche le immagini in img/champions/ (cosi' il sito non
  dipende da link esterni).

Uso:  python3 tools/import_datadragon.py [--images]
"""
import json
import os
import sys
import urllib.request

BASE = "https://ddragon.leagueoflegends.com"
HERE = os.path.dirname(os.path.abspath(__file__))
SITE = os.path.dirname(HERE)


def get_json(url):
    with urllib.request.urlopen(url, timeout=30) as resp:
        return json.loads(resp.read().decode("utf-8"))


def sql_str(value):
    return "'" + str(value).replace("\\", "\\\\").replace("'", "''") + "'"


def main():
    download = "--images" in sys.argv
    version = get_json(BASE + "/api/versions.json")[0]
    champions = get_json(f"{BASE}/cdn/{version}/data/it_IT/champion.json")["data"]

    lines = [f"-- Campioni da Data Dragon, versione {version}", "SET NAMES utf8mb4;"]
    for champ in sorted(champions.values(), key=lambda c: c["name"]):
        image_file = champ["image"]["full"]
        image_path = "img/champions/" + image_file
        lines.append(
            "INSERT INTO champions (name, title, image_path, description) VALUES ("
            + ", ".join(sql_str(v) for v in (champ["name"], champ["title"], image_path, champ["blurb"]))
            + ");"
        )
        if download:
            target = os.path.join(SITE, "img", "champions", image_file)
            if not os.path.exists(target):
                urllib.request.urlretrieve(f"{BASE}/cdn/{version}/img/champion/{image_file}", target)

    out = os.path.join(SITE, "sql", "champions_import.sql")
    with open(out, "w", encoding="utf-8") as f:
        f.write("\n".join(lines) + "\n")
    print(f"Scritto {out} ({len(champions)} campioni, versione {version})")


if __name__ == "__main__":
    main()
