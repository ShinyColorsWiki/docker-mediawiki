import json
import sys
import tempfile
import os
import tarfile
import zipfile
import time
import shutil
import multiprocessing
import subprocess
from urllib.request import urlopen, urlretrieve

if len(sys.argv) < 2:
    print("Please Provide REL Branch")
    exit(1)
MW_BRANCH = sys.argv[1]

with open(os.path.join(sys.path[0], "list.json"), "r") as l:
    extlist = json.load(l)

#TEMP = tempfile.TemporaryDirectory()
TEMP = "/tmp/extension_temp"

composer_home = "/tmp/composer"
dest = "/tmp/mediawiki"

def getWMFExtensionDLUrl(name):
    info_url = f"https://gerrit.wikimedia.org/r/projects/mediawiki%2Fextensions%2F{name}/branches/{MW_BRANCH}"
    resp = urlopen(info_url)
    data = json.loads(resp.read().decode("utf-8").strip()[4:])
    rev = data['revision']
    return f"https://extdist.wmflabs.org/dist/extensions/{name}-{MW_BRANCH}-{rev[0:7]}.tar.gz"

def downloadWithName(url, name, ext = "tar.gz"):
    path = os.path.join(TEMP.name, f"{name}.{ext}")
    r = urlretrieve(url, path)
    return (path, r[0])

def unTar(name, path, with_new_path = False):
    extract_path = os.path.join(TEMP.name, f"{name}-extract") if with_new_path else TEMP.name
    tar = tarfile.open(path)
    tar.extractall(extract_path)
    tar.close()
    return extract_path

def unZip(name, path, with_new_path = False):
    extract_path = os.path.join(TEMP.name, f"{name}-extract") if with_new_path else TEMP.name
    zip = zipfile.ZipFile(path)
    zip.extractall(extract_path)
    zip.close()
    return extract_path

def wmfDownloadHanlder(name):
    url = getWMFExtensionDLUrl(name)
    print(f"Downloading [{name}] from [{url}]...")
    t = downloadWithName(url, name)

    print(f"Extracting [{name}]...")
    extract_path = unTar(name, os.path.join(t[0], t[1]))
    
    movedir(os.path.join(extract_path, name), f"{dest}/extensions/{name}")

def nonWmfDownloadHandler(obj): 
    name = obj['name']
    url = obj['url']
    ext = url.split('.')[-1]

    inner_name = name
    if obj['inner_name'] is not None:
        if "$[version]" in obj['inner_name']:
            inner_name = obj['inner_name'].replace("$[version]", obj['version'])
        elif "$[n_version]" in obj['inner_name']:
            inner_name = obj['inner_name'].replace("$[n_version]", obj['version'][1:])
        else: 
            inner_name = obj['inner_name']

    if "$[version]" in url:
        url = url.replace("$[version]", obj['version'])

    print(f"Downloading [{name}] from [{url}]...")
    t = downloadWithName(url, name)

    print(f"Extracting [{name}]...")    
    if ext == "zip":
        extract_path = unZip(name, os.path.join(t[0], t[1]))
    else: 
        extract_path = unTar(name, os.path.join(t[0], t[1]))

    movedir(os.path.join(extract_path, inner_name), f"{dest}/extensions/{name}")
    composerDeps(f"{dest}/extensions/{name}")

def composerDeps(path): 
    if os.path.isfile(f"{path}/composer.json"):
        # www-data doesn't have a write access on `/var/www/.composer`. overriding.
        current_env = os.environ.copy()
        current_env['COMPOSER_HOME'] = composer_home

        print("Install Composer Dependencies...")
        subprocess.call(["composer", "update", "--no-dev", "--working-dir", f"'{path}'"],
            env=current_env)

def movedir(old, new): 
    print(f"Moving [{old}] to [{new}]...")
    shutil.move(old, new)
    

if __name__ == "__main__":
    wmf_exts = extlist['WikiMedia']
    non_wmfs = extlist['non-WMF']

    wmfPool = multiprocessing.Pool()
    wmfPool.map(wmfDownloadHanlder, wmf_exts)

    nonWmfPool = multiprocessing.Pool()
    nonWmfPool.map(nonWmfDownloadHandler, non_wmfs)

    wmfPool.close()
    nonWmfPool.close()

    wmfPool.join()
    nonWmfPool.join()

