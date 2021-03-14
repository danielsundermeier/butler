# Butler

A CLI Tool to automate my tasks

## Laravel

### New

Creates a new Laravel project

```
butler laravel new name
```

- updated laravel installer
- creates valet link
- requires composer packages
- links storage
- installs breeze
- changes .htaccess
- creates database
- initializes git
- npm install
- migrates databases
- creates sublime project
- opens project in sublime

## Wiki

### Summary

creates a SUMMARY.md from markdon files for gitbook

## Package

automates composer package maintainance

### New

Creates /packages/vendor/package-name with boilerplate and installed via composer.

```
butler package:new vendor/package-name D15r\\PackageNamespace --laravel
```

### Publish

- Creates Git Repository and pushes all
- --release=0.1.0 creates new release

```
butler package:publish vendor/package-name --release="0.1.0"
```

### Remove

- deletes /packages/vendor/package-name
- removes from composer
- deletes local repository
- --require: requires package vom packagist

```
butler package:remove vendor/package-name --require
```

### Clone

Clones repository to /packages/vendor/package-name

```
butler package:clone vendor/package-name https://github.com/vendor/package-name
```

### Push

- commits and pushes all changes in /packages/vendor/package-name
- create new release
    + --major
    + --minor
    + --patch
    + --release="X.Y.Z"

```
butler package:push vendor/package-name "Commit message (optional)" --release="0.2.0"
```
