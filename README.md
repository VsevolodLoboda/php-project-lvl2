# Diff checker

### Hexlet tests and linter status:

[![Actions Status](https://github.com/VsevolodLoboda/php-project-lvl2/workflows/hexlet-check/badge.svg)](https://github.com/VsevolodLoboda/php-project-lvl2/actions)
[![self-check](https://github.com/VsevolodLoboda/php-project-lvl2/actions/workflows/self-check.yml/badge.svg?branch=main)](https://github.com/VsevolodLoboda/php-project-lvl2/actions/workflows/self-check.yml)
[![Maintainability](https://api.codeclimate.com/v1/badges/d0cc5a04c449a0f24cd6/maintainability)](https://codeclimate.com/github/VsevolodLoboda/php-project-lvl2/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/d0cc5a04c449a0f24cd6/test_coverage)](https://codeclimate.com/github/VsevolodLoboda/php-project-lvl2/test_coverage)

## Build

```sh
make install # Launches the installation of the necessary packages
```

## Usage

### Calculate difference between two files:

Calculates structure differences between two .json or [.yml, .yaml] files

```sh
php ./bin/gendiff <filePath1> <filePath2> --format=[stylish,plain,json]
```

## Examples
### Json files
```sh
php ./bin/gendiff ./tests/fixtures/file1.json ./tests/fixtures/file2.json
```
[![asciicast](https://asciinema.org/a/fdQs448H22P4RrWcFuYd2sexu.svg)](https://asciinema.org/a/fdQs448H22P4RrWcFuYd2sexu)

### Yaml files
```sh
php ./bin/gendiff ./tests/fixtures/file1.yaml ./tests/fixtures/file2.yaml
```
[![asciicast](https://asciinema.org/a/KcmyfztxEOeiwUp8sVXuQUqia.svg)](https://asciinema.org/a/KcmyfztxEOeiwUp8sVXuQUqia)

### Stylish format
```sh
php ./bin/gendiff ./tests/fixtures/file1.json ./tests/fixtures/file2.json --format=stylish
```
[![asciicast](https://asciinema.org/a/HoOlKE2seKfnQNYvoW4nKvDJN.svg)](https://asciinema.org/a/HoOlKE2seKfnQNYvoW4nKvDJN)

### Plain format
```sh
php ./bin/gendiff ./tests/fixtures/file1.json ./tests/fixtures/file2.json --format=plain
```
[![asciicast](https://asciinema.org/a/Nt7TgG2bM0D6lWdETUKcrVEtk.svg)](https://asciinema.org/a/HoOlKE2seKfnQNYvoW4nKvDJN)

### Json format
```sh
php ./bin/gendiff ./tests/fixtures/file1.json ./tests/fixtures/file2.json --format=json
```
[![asciicast](https://asciinema.org/a/6qbXlvhNVauHr122jmois1xbD.svg)](https://asciinema.org/a/HoOlKE2seKfnQNYvoW4nKvDJN)